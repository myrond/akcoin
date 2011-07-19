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

lock("hashrate.php");

//Hashrate by worker
$sql =  "SELECT IFNULL(sum(a.id),0) as id, p.username FROM pool_worker p LEFT JOIN ".
			"((select count(id) as id, username ". 
			"from shares ". 
			"where time > DATE_SUB(now(), INTERVAL 10 MINUTE) ".
			"group by username) ".
		"UNION ". 
			"(select count(id) as id, username ". 
			"from shares_history ". 
			"where time > DATE_SUB(now(), INTERVAL 10 MINUTE) ". 
			"group by username)) a ".
		"ON p.username=a.username ".
		"group by username";
$result = mysql_query($sql);
while ($resultrow = mysql_fetch_object($result)) {
	$hashrate = $resultrow->id;
	$hashrate = round((($hashrate*4294967296)/600)/1000000, 0);
	mysql_query("update pool_worker set hashrate=".$hashrate." where username='".$resultrow->username."'");
}


//This collects all stale records in the last 24 hours (stale_records_24_hours) and records them
$sql = "SELECT IFNULL(sum(a.id),0) as id, p.username FROM pool_worker p LEFT JOIN ((select count(id) as id, username from shares where our_result='N' and reason='stale' AND time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username) UNION (select count(id) as id, username from shares_history where our_result='N' and reason='stale' AND time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username)) a ON p.username=a.username group by username";
$result = mysql_query($sql);
while ($resultrow = mysql_fetch_object($result)) {
	$stalerecords = $resultrow->id;
	mysql_query("update pool_worker set stale_records_24_hours=".$stalerecords." where username='".$resultrow->username."'");
}

//This collect all accepted records in the last 24 hours (accepted_records_24_hours) and records them
$sql = "SELECT IFNULL(sum(a.id),0) as id, p.username FROM pool_worker p LEFT JOIN ((select count(id) as id, username from shares where our_result='Y' AND time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username) UNION (select count(id) as id, username from shares_history where our_result='Y' AND time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username)) a ON p.username=a.username group by username";
$result = mysql_query($sql);
while ($resultrow = mysql_fetch_object($result)) {
        $acceptedrecords = $resultrow->id;
        mysql_query("update pool_worker set accepted_records_24_hours=".$acceptedrecords." where username='".$resultrow->username."'");
}



//This collects all records accepted on current block and applies them individually to each worker
$sql = "SELECT IFNULL(sum(a.id),0) as id, p.username FROM pool_worker p LEFT JOIN ((select count(id) as id, username from shares where our_result='Y' group by username) UNION (select count(id) as id, username from shares_history where our_result='Y' AND counted='0' group by username)) a ON p.username=a.username group by username";
$result = mysql_query($sql);
while ($resultrow = mysql_fetch_object($result)) {
        $currentblockaccepted = $resultrow->id;
        mysql_query("update pool_worker set accepted_current_block=".$currentblockaccepted." where username='".$resultrow->username."'");
}



//Total Hashrate (more exact than adding)
$sql =  "SELECT sum(a.id) as id FROM ".
			"((select count(id) as id from shares where time > DATE_SUB(now(), INTERVAL 10 MINUTE)) ".
		"UNION ". 
			"(select count(id) as id from shares_history where time > DATE_SUB(now(), INTERVAL 10 MINUTE)) ". 
			") a ";
$result = mysql_query($sql);
if ($resultrow = mysql_fetch_object($result)) {
	$hashrate = $resultrow->id;
	$hashrate = round((($hashrate*4294967296)/600)/1000000, 0);	
	mysql_query("update settings set value='".$hashrate."' where setting='currenthashrate'");
}

//Hashrate by user
$sql = "select u.id, IFNULL(sum(p.hashrate),0) as hashrate ".
		"FROM webUsers u LEFT JOIN pool_worker p ". 
		"ON p.associatedUserId = u.id ".
		"GROUP BY id";
$result = mysql_query($sql);
while ($resultrow = mysql_fetch_object($result)) {
	mysql_query("update webUsers set hashrate=".$resultrow->hashrate." where id=".$resultrow->id);
}

$currentTime = time();
mysql_query("update settings set value='".$currentTime."' where setting='statstime'");
	
?>
