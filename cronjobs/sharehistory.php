<?php
//    Copyright (C) 2011  Mike Allison
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

$includeDirectory = "/var/www/includes/";

include($includeDirectory."requiredFunctions.php");

//Verify source of cron job request
if (isset($cronRemoteIP) && $_SERVER['REMOTE_ADDR'] !== $cronRemoteIP) {
 die(header("Location: /"));
}

lock("sharehistory.php");


//global stale count
$sql = "select count(*) as stales from shares_history WHERE time > DATE_SUB(now(), INTERVAL 1440 MINUTE) and our_result='N' and reason='stale'";
$result = mysql_query($sql);
$num_rows = mysql_num_rows($result);
if ($num_rows > 0)
{
while ($row = mysql_fetch_object($result)) {
	mysql_query("replace into serverstalesharehistory (stales,currentdate) values ('".$row->stales."',substring(now(),1,10))") or die ("Failed to insert into serverstalehistory: ".mysql_error());
	}

}

//global share count
$sql = "select count(*) as shares from shares_history WHERE time > DATE_SUB(now(), INTERVAL 1440 MINUTE) and our_result='Y'";
$result = mysql_query($sql);
$num_rows = mysql_num_rows($result);
if ($num_rows > 0)
{
while ($row = mysql_fetch_object($result)) {
        mysql_query("replace into serveracceptedsharehistory (shares,currentdate) values ('".$row->shares."',substring(now(),1,10))") or die ("Failed to insert into serveracceptedsharehistory: ".mysql_error());
        }
}

//pushpool requires 8 additional zero bits, before it submits to upstream.  Therefore, you will see (Y, NULL) for most shares, (Y, N) for uncommon shares that are just a bit closer to the target, and (Y, Y) for valid, full-target mainnet block hash accepted by upstream.  Only the latter (Y, Y) pays you 50 BTC, and generates a block w/ transactions.

//global rare shares
$sql = "select count(*) as rare from shares_history WHERE time > DATE_SUB(now(), INTERVAL 1440 MINUTE) and our_result='Y' and upstream_result='N'";
$result = mysql_query($sql);
$num_rows = mysql_num_rows($result);
if ($num_rows > 0)
{
while ($row = mysql_fetch_object($result)) {
        mysql_query("update serveracceptedsharehistory set rare='".$row->rare."' where currentdate=substring(now(),1,10)") or die ("Failed to insert rare records into serverrareacceptedsharehistory: ".mysql_error());
        }
}






//CREATE TABLE `serverstalesharehistory` (
//  `id` int(11) NOT NULL AUTO_INCREMENT,
//  `stales` int(11) DEFAULT NULL,
//  `currentdate` varchar(11) NOT NULL,
//  PRIMARY KEY (`id`),
//  UNIQUE KEY (`currentdate`)
//) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


//share count all, first get a list of all user's submitting shares in the last 24 hours
$sql = "SELECT p.associateduserid as associateduserid FROM pool_worker p RIGHT JOIN (SELECT count(id) AS id, username FROM shares WHERE time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username UNION SELECT count(id) AS id, username FROM shares_history WHERE time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username) a ON p.username=a.username group by p.associateduserid";
$result = mysql_query($sql);
$num_rows = mysql_num_rows($result);
if ($num_rows > 0)
{
while ($row = mysql_fetch_object($result)) {
	//Now for each $row->associateduserid collect accepted shares
	$result2=mysql_query("select count(*) as accepted from shares_history where time > DATE_SUB(now(), INTERVAL 1440 MINUTE) and userId='".$row->associateduserid."' and our_result='Y' limit 1");
	$row2=mysql_fetch_object($result2);
	mysql_query("replace into useracceptedsharehistory (associatedUserId,shares,currentdate) values ('".$row->associateduserid."','".$row2->accepted."',substring(now(),1,10))") or die ("Failed to insert into useracceptedsharehistory: ".mysql_error());	

	$result2=mysql_query("select count(*) as notaccepted from shares_history where time > DATE_SUB(now(), INTERVAL 1440 MINUTE) and userId='".$row->associateduserid."' and our_result='N' limit 1");
	$row2=mysql_fetch_object($result2);
        mysql_query("replace into userstalesharehistory (associatedUserId,stales,currentdate) values ('".$row->associateduserid."','".$row2->notaccepted."',substring(now(),1,10))") or die ("Failed to insert into userstalesharehistory: ".mysql_error());
        }

}


?>
