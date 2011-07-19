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

//Check that script is run locally

//Verify source of cron job request
if (isset($cronRemoteIP) && $_SERVER['REMOTE_ADDR'] !== $cronRemoteIP) {
 die(header("Location: /"));
}

//if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != "127.0.0.1") {
//	echo "cronjobs can only be run locally.";
//	exit;
//}

//Verify source of cron job request
if (isset($cronRemoteIP) && $_SERVER['REMOTE_ADDR'] !== $cronRemoteIP) {
 die(header("Location: /"));
}

$includeDirectory = "/var/www/includes/";

include($includeDirectory."requiredFunctions.php");


$maxblock=0;
//fetch last block
//lets just update the projected round as soon as confirms are greater than 7
$sql = "select max(blockNumber) as blockNumber from networkBlocks where confirms > 7";
$result = mysql_query($sql);
while ($row = mysql_fetch_object($result)) {
        $maxblock = "$row->blockNumber";
}

	
//Update current round score
try {
	$sql ="SELECT DISTINCT s1.userId, sum(exp(s1.score-s2.score)) AS score FROM shares_history s1, shares_history s2 WHERE s2.id = s1.id -1 AND s1.counted = '0' and s1.our_result='Y' and s1.blocknumber>'$maxblock' group by s1.userId";

	$result = mysql_query($sql);
	$totalscorethisround = 0;
	while ($row = mysql_fetch_object($result)) {
		mysql_query("UPDATE webUsers SET score = $row->score WHERE id = $row->userId");
		$totalscorethisround += $row->score;
	}
	mysql_query("UPDATE settings SET value = '$totalscorethisround' WHERE setting='score'");
} catch (Exception $ex)  {}

?>
