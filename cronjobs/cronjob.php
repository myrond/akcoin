<?php
//    Copyright (C) 2011  Mike Allison <dj.mikeallison@gmail.com>
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.

// 	  BTC Donations: 163Pv9cUDJTNUbadV4HMRQSSj3ipwLURRc
ini_set('display_errors', '1');  
//Set page starter variables//
$includeDirectory = "/var/www/includes/";

//Include site functions
include($includeDirectory."requiredFunctions.php");

lock("cronjob.php");

//Verify source of cron job request
if (isset($cronRemoteIP) && $_SERVER['REMOTE_ADDR'] !== $cronRemoteIP) {
 die(header("Location: /"));
}

include($includeDirectory.'stats.php');
include($includeDirectory.'mtgox.php');

//Update MtGox last price, bypass if failed
try {
        $mtgox = new mtgox("", "");
        $ticker = $mtgox->ticker();
        if (intval($ticker['last']) > 0) $settings->setsetting('mtgoxlast', $ticker['last']);
} catch (Exception $e) { }

//Open a bitcoind connection
$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

//Get current block number & difficulty
$currentBlockNumber = $bitcoinController->getblocknumber();
$difficulty = $bitcoinController->query("getdifficulty");

//Get site percentage
$sitePercent = 0;
$sitePercentQ = mysql_query("SELECT value FROM settings WHERE setting='sitepercent'");
if ($sitePercentR = mysql_fetch_object($sitePercentQ)) $sitePercent = $sitePercentR->value;				

//Setup score variables
$feeVariance = .001;
$fee = 1;
if ($sitePercent > 0)
        $fee = $sitePercent / 100;
else
        $fee = (-$feeVariance)/(1-$feeVariance);
$difficultyRatio = 1.0/$difficulty;
$logRatio = log(1.0-$difficultyRatio+$difficultyRatio/$feeVariance);
$bonusCoins = 50;
$los = log(1/(exp($logRatio)-1));


//Setup score variables
$c = .001;
$f=1;
if ($sitePercent > 0)
	$f = $sitePercent / 100;
else
	$f = (-$c)/(1-$c);
$p = 1.0/$difficulty;
$r = log(1.0-$p+$p/$c);
$B = 50;
$los = log(1/(exp($r)-1));

//Is this block number in the database already
$inDatabaseQ = mysql_query("SELECT `id` FROM `networkBlocks` WHERE `blockNumber` = '$currentBlockNumber' LIMIT 0,1");
$inDatabase = mysql_num_rows($inDatabaseQ);

if(!$inDatabase){
	//Add this block into the `networkBlocks` log
	$currentTime = time();
	mysql_query("INSERT INTO `networkBlocks` (`blockNumber`, `timestamp`) VALUES ('$currentBlockNumber', '$currentTime')");

    $sql = "" .
        "UPDATE webUsers wu " .
        "SET    wu.stale_share_count = wu.stale_share_count + " .
        "       ( " .
        "              SELECT COUNT(s.id) " .
        "              FROM   shares s " .
        "                     JOIN pool_worker pw " .
        "                     ON     s.username   = pw.username " .
        "              WHERE  our_result          = 'N' " .
        "              AND    pw.associatedUserId = wu.id " .
        "       ) ";
    mysql_query($sql);

    $sql = "" .
        "UPDATE webUsers wu " .
        "SET    wu.share_count = wu.share_count + " .
        "       ( " .
        "              SELECT COUNT(s.id) " .
        "              FROM   shares s " .
        "                     JOIN pool_worker pw " .
        "                     ON     s.username   = pw.username " .
        "              WHERE  our_result          = 'Y' " .
        "              AND    pw.associatedUserId = wu.id " .
        "       ) ";
    mysql_query($sql);

        //Don't delete shares until a new block is started
        //Go through every share and add it to the shares_history database

    mysql_query("BEGIN");


        //Get last Id
        $lastId = 0;
        $lastShareId = 0;
        $lastShareQ = mysql_query("SELECT id FROM shares ORDER BY id DESC LIMIT 1");
        if ($lastShareR = mysql_fetch_object($lastShareQ)) {
                $lastShareId = $lastShareR->id;
        }


	//Don't delete shares until a new block is started
	//Go through every share and add it to the shares_history database


    $result = mysql_query("SELECT id FROM shares ORDER BY id DESC LIMIT 1");
    $top_id = mysql_fetch_object($result);

        //Save winning share (if there is one)
        $winningShareQ = mysql_query("SELECT DISTINCT username FROM shares where upstream_result = 'Y'");
        while ($winningShareR = mysql_fetch_object($winningShareQ)) {
                mysql_query("INSERT INTO winning_shares (blockNumber, username) VALUES ($currentBlockNumber,'$winningShareR->username')");
        }

        //Select all shares
        $lastScore = 0;
        $shareInputQ = "";
//        $getAllShares = mysql_query("SELECT id, username, our_result, time, reason FROM `shares` ORDER BY `id` ASC");
        $getAllShares = mysql_query("SELECT s.id, s.username, our_result, time, reason,pw.associatedUserId,upstream_result FROM `shares` s left join pool_worker pw on s.username = pw.username ORDER BY `id` ASC");
        while($share = mysql_fetch_object($getAllShares)) {
                if ($i == 0)
                        $shareInputQ = "INSERT INTO shares_history (blockNumber, username, our_result, time, score, counted, reason,userId, upstream_result) VALUES ";
                $i++;
                if ($i > 1)
                        $shareInputQ .= ",";
                $score = $lastScore + $logRatio;
                $shareInputQ .="('$currentBlockNumber','$share->username','$share->our_result','$share->time',$score,'0','$share->reason','$share->associatedUserId','$share->upstream_result')";
                $lastId = $share->id;
                $lastScore = $score;
                if ($i > 20) {
                        //Add to `shares_history`
                        $shareHistoryQ = mysql_query($shareInputQ);

                        //Move all old shares from `shares` and move them to `shares_history`
                        if($shareHistoryQ){
                                //Delete all from shares whoms "id" is less then $lastId to prevent new "hard-earned" shares to be deleted
                                mysql_query("DELETE FROM shares WHERE id <= $lastId");
                        }
                        $i = 0;
                }
        }



//    $sql = "INSERT INTO shares_history
//            (`blockNumber`, `rem_host`, `username`, `our_result`, `upstream_result`, `reason`, `solution`, `time`, `userId`)
//            SELECT $currentBlockNumber, s.rem_host, s.username, s.our_result, s.upstream_result, s.reason, s.solution, s.time, pw.associatedUserId
//            FROM shares s
//            LEFT JOIN pool_worker pw ON s.username = pw.username";

        //Add to `shares_history`
        if ($shareHistoryQ != "")
                $shareHistoryQ = mysql_query($shareInputQ);

        //Move all old shares from `shares` and move them to `shares_history`
        if($shareHistoryQ){
                //Delete all from shares whoms "id" is less then $lastId to prevent new "hard-earned" shares to be deleted
                mysql_query("DELETE FROM shares WHERE id <= $lastId") or die("Failed to delete shares that have been moved: ".mysql_error());
        }


//    mysql_query($sql) or die("Failed to move shares into shares_history: ".mysql_error());

//    mysql_query("DELETE FROM shares WHERE id <= ".$top_id->id) or die("Failed to delete shares that have been moved: ".mysql_error());

    mysql_query("COMMIT");
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//try {
//Get list of transactions
$transactions = $bitcoinController->query("listtransactions", "*", "200");

//Go through all the transactions check if there is 50BTC inside
$numAccounts = count($transactions);

for($i = 0; $i < $numAccounts; $i++){
	//Check for 50BTC inside only if they are in the receive category
        if($transactions[$i]["amount"] >= 50 && ($transactions[$i]["category"] == "generate" || $transactions[$i]["category"] == "immature")) {
	
		//At this point we may or may not have found a block,
		//Check to see if this account addres is already added to `networkBlocks`
		$accountExistsQ = mysql_query("SELECT id FROM networkBlocks WHERE accountAddress = '".$transactions[$i]['txid']."' ORDER BY blockNumber DESC LIMIT 0,1")or die(mysql_error());
		$accountExists = mysql_num_rows($accountExistsQ);

		//If the account dosen't exist that means we found a block, now add it to the database so we can track the confirms
		if(!$accountExists){					
			//Update site balance for tx fee
			$poolReward = $transactions[$i]["amount"] - $B;
			mysql_query("UPDATE settings SET value = value +".$poolReward." WHERE setting='sitebalance'");
									
			//Get last confirmed block
			$lastSuccessfullBlockQ = mysql_query("SELECT n.id FROM shares_history s, networkBlocks n WHERE n.blockNumber = s.blockNumber AND s.upstream_result='Y' ORDER BY s.id DESC LIMIT 1 ");
			$lastSuccessfullBlockR = mysql_fetch_object($lastSuccessfullBlockQ);
			$lastEmptyBlock = $lastSuccessfullBlockR->id;			

			$insertBlockSuccess = mysql_query("UPDATE networkBlocks SET confirms = '1', accountAddress = '".$transactions[$i]["txid"]."' WHERE id = ".$lastEmptyBlock)or die(mysql_error());
            echo("We have found a block!");
		}
	}
}


//Go through all the transctionss from bitcoind and update their confirms
$blockExistsQ = mysql_query("SELECT id,accountAddress FROM networkBlocks WHERE confirms >= 1 and confirms <= 121 ORDER BY blockNumber DESC LIMIT 1")or die(mysql_error());
$blockExists = mysql_num_rows($blockExistsQ);

while ($blockExistsR = mysql_fetch_object($blockExistsQ)) {

	$transactions1 = $bitcoinController->query("gettransaction" ,"$blockExistsR->accountAddress");

	//This is a winning account
	$winningId	= $blockExistsR->id;
	$confirms = $transactions1['confirmations'];
	//Update X amount of confirms
	mysql_query("UPDATE networkBlocks SET confirms = '".$confirms."' WHERE id = ".$winningId);
}

//Go through all of `shares_history` that are uncounted shares; Check if there are enough confirmed blocks to award user their BTC
	//Get uncounted shares
	$overallReward = 0;

	$blocksQ = mysql_query("SELECT nb.blockNumber FROM networkBlocks nb WHERE nb.confirms > 119 AND (SELECT sh.id FROM shares_history sh WHERE sh.blockNumber = nb.blockNumber AND counted = '0' LIMIT 1) ORDER BY nb.blockNumber ASC");
	while ($blocks = mysql_fetch_object($blocksQ)) {
		$block = $blocks->blockNumber;

		$totalRoundSharesQ = mysql_query("SELECT count(id) as id FROM shares_history WHERE counted = '0' AND blockNumber <= ".$block);
		if ($totalRoundSharesR = mysql_fetch_object($totalRoundSharesQ)) {
			$totalRoundShares = $totalRoundSharesR->id;
            $sql = "SELECT DISTINCT sh.userId, count(sh.id) as id, wu.donate_percent
                    FROM shares_history sh
                    JOIN webUsers wu ON sh.userId = wu.id
                    WHERE sh.counted = '0' AND sh.blockNumber <= $block GROUP BY sh.userId";
			$userListCountQ = mysql_query($sql) or die(mysql_error());
			while ($userListCountR = mysql_fetch_object($userListCountQ)) {
				mysql_query("BEGIN");
				$userId = $userListCountR->userId;
				$uncountedShares = $userListCountR->id;
                $donatePercent = $userListCountR->donate_percent;

				$shareRatio = $uncountedShares/$totalRoundShares;
				$predonateAmount = 50 * $shareRatio;
                $totalReward = (1-($sitePercent/100)) * $predonateAmount;

				if ($predonateAmount > 0.00000001)	{
				
					//Take out donation
					$totalReward = $totalReward - ($totalReward * ($donatePercent/100));
					
					//Round Down to 8 digits
					$totalReward = $totalReward * 100000000;
					$totalReward = floor($totalReward);
					$totalReward = $totalReward/100000000;
					
					//Get total site reward
					$donateAmount = $predonateAmount - $totalReward;
							
					$overallReward += $totalReward;
					
					echo("PAID: ($userId, $totalReward, $block, $uncountedShares, $totalRoundShares, $sitePercent, $donatePercent)\n");

                    // Log what happened
                    $sql = "INSERT INTO accountHistory (userId, balanceDelta, blockNumber, userShares, totalShares, sitePercent, donatePercent) VALUES " .
                            "($userId, $totalReward, $block, $uncountedShares, $totalRoundShares, $sitePercent, $donatePercent)";
                    mysql_query($sql) or die(mysql_error());

					//Update balance
					$updateOk = mysql_query("UPDATE accountBalance SET balance = balance + ".$totalReward." WHERE userId = ".$userId) or die(mysql_error());
					if (!$updateOk)
						mysql_query("INSERT INTO accountBalance (userId, balance) VALUES (".$userId.",'".$totalReward."')") or die(mysql_error());
				}
				mysql_query("UPDATE shares_history SET counted = '1' WHERE userId='".$userId."' AND blockNumber <= ".$block." AND counted = '0'");
				mysql_query("COMMIT");
			}
		}
		$poolReward = $B -$overallReward;
		mysql_query("UPDATE settings SET value = value +".$poolReward." WHERE setting='sitebalance'");

//        mysql_query("DELETE FROM shares_history WHERE blockNumber <= $block AND counted = '1' AND (upstream_result != 'Y' OR upstream_result is null) AND time < DATE_SUB(now(), INTERVAL 1440 MINUTE)");
	}
?>
