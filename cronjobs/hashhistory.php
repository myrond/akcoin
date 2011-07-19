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

lock("hashhistory.php");

//Hashrate by worker
$sql =  "SELECT IFNULL(sum(a.id),0) as hashrate, p.associatedUserId FROM pool_worker p LEFT JOIN ".
			"((select count(id) as id, username ". 
			"from shares ". 
			"where time > DATE_SUB(now(), INTERVAL 1440 MINUTE) ".
			"group by username) ".
		"UNION ". 
			"(select count(id) as id, username ". 
			"from shares_history ". 
			"where time > DATE_SUB(now(), INTERVAL 1440 MINUTE) ". 
			"group by username)) a ".
		"ON p.username=a.username ".
		"group by associatedUserId";
$result = mysql_query($sql);

while ($resultrow = mysql_fetch_object($result)) {
	$hashrate = $resultrow->hashrate;
	$hashrate = round((($hashrate*4294967296)/86400)/1000000, 0);
	if ($hashrate > 0)
	{
	mysql_query("replace into userhashhistory (associatedUserId,hashrate,currentdate) values ('".$resultrow->associatedUserId."','".$hashrate."',substring(now(),1,10))") or die("Failed to insert into userhashhistory: ".mysql_error());
	}
}

//Hashrate by server

$sql = "SELECT sum(a.id) as serverhashrate from ((select count(id) as id from shares where time > DATE_SUB(now(), INTERVAL 1440 MINUTE)) UNION (select count(id) as id from shares_history where time > DATE_SUB(now(), INTERVAL 1440 MINUTE)) ) a;";
//
$result = mysql_query($sql);
while ($resultrow = mysql_fetch_object($result)) {
        $hashrate = $resultrow->serverhashrate;
        $hashrate = round((($hashrate*4294967296)/86400)/1000000, 0);
        mysql_query("replace into serverhashhistory (hashrate,currentdate) values ('".$hashrate."',substring(now(),1,10))") or die("Failed to insert into serverhashhistory: ".mysql_error());
}

?>
