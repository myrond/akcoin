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
$pageTitle = "- Admin Panel";
include ("includes/header.php");

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

$goodMessage = "";
$returnError = "";
//Scince this is the Admin panel we'll make sure the user is logged in and "isAdmin" enabled boolean; If this is not a logged in user that is enabled as admin, redirect to a 404 error page

if(!$cookieValid || $isAdmin != 1) {
	header('Location: /');
	exit;
}

if (isset($_POST["act"])) {
$act = mysql_real_escape_string($_POST["act"]);
}

if (isset($act) && isset($_POST["authPin"]))
{
	$inputAuthPin = hash("sha256", $_POST["authPin"].$salt);
	//Make sure an authPin is set and valid when $act is active
	if($act && $authPin == $inputAuthPin){
		//Update information if needed
		if($act == "UpdateMainPageSettings"){
			try {		
				$settings->setsetting("sitepayoutaddress", mysql_real_escape_string($_POST["paymentAddress"]));
				$settings->setsetting("sitepercent", mysql_real_escape_string($_POST["percentageFee"]));
				$settings->setsetting("websitename", mysql_real_escape_string($_POST["headerTitle"]));
				$settings->setsetting("pagetitle", mysql_real_escape_string($_POST["pageTitle"]));
				$settings->setsetting("slogan", mysql_real_escape_string($_POST["headerSlogan"]));
				$settings->setsetting("siterewardtype", mysql_real_escape_string($_POST["rewardType"]));
				$settings->loadsettings(); //refresh settings
				$goodMessage = "Successfully updated general settings";
			} catch (Exception $e) {
				$returnError = "Database Failed - General settings was not updated";
			}
		}
	} else if($act && $authPin != $inputAuthPin){
		$returnError = "Authorization Pin # - Invalid";
	}
}

//Display Error and Good Messages(If Any)
echo "<span class=\"goodMessage\">".antiXss($goodMessage)."</span><br/>";
echo "<span class=\"returnMessage\">".antiXss($returnError)."</span>";
?>
<div id="AdminContainer">
	<h1 style="text-decoration:underline;">Welcome back admin</h1><br/>
	<h3>General Settings</h3>
	<hr size="1" width="80%"></hr>
	<!--Begin main page edits-->
	<form action="/adminPanel.php" method="post">
		<input type="hidden" name="act" value="UpdateMainPageSettings">
		Page Title <input type="text" name="pageTitle" value="<?php echo antiXss($settings->getsetting("pagetitle"));?>"><br/>
		Header Title <input type="text" name="headerTitle" value="<?php echo antiXss($settings->getsetting("websitename"));?>"><br/>
		Header Slogan <input type="text" name="headerSlogan" value="<?php echo antiXss($settings->getsetting("slogan"));?>"><br/>
		Percentage Fee <input type="text" name="percentageFee" size="10" maxlength="10" value="<?php echo antiXss($settings->getsetting("sitepercent")); ?>">%<br/>
		Fee Address <input type="text" name="paymentAddress" size="60" value="<?php echo antiXss($settings->getsetting("sitepayoutaddress"));?>"><br/>
		Default Reward Type <select name="rewardType">
		<option value="0" <?php if ($settings->getsetting("siterewardtype") == 0) echo "selected"; ?>>Cheat Proof Score</option>
		<option value="1" <?php if ($settings->getsetting("siterewardtype") == 1) echo "selected"; ?>>Proportional</option>
		</select>
		<br/><br/>
		Authorization Pin <input type="password" size="4" maxlength="4" name="authPin"><br/>
		<input type="submit" value="Update Main Page Settings">
	</form>
	<br/><br/>
	<h3>Info</h3>
	<hr size="1" width="80%"></hr>

	<? 

	$sitewallet = mysql_query("SELECT sum(balance) FROM `accountBalance` WHERE `balance` > 0")or sqlerr(__FILE__, __LINE__);
	$sitewalletq = mysql_fetch_row($sitewallet);
	$usersbalance = $sitewalletq[0];
	$balance = $bitcoinController->query("getbalance");
	$total = $balance - $usersbalance;

	echo "Block Number: ".$bitcoinController->getblocknumber()."<br>";
	echo "Difficulty: ".$bitcoinController->query("getdifficulty")."<br>";
	echo "Wallet Balance: ".$balance."<br>";
	echo "UnPaid: ".$usersbalance."<br>";
	echo "Total Left: <font color=red>$total</font><br>";
	
?>
	<br><h3>News Control</h3>
	<hr size="1" width="80%"></hr>
	<a href=news.php style="color: blue">Edit News</a>
	<br/><br/>
	<h3>Users Control</h3>
	<hr size="1" width="80%"></hr>
	<a href=users.php style="color: blue">Show USers</a>
</div>

<?include ("includes/footer.php");?>
