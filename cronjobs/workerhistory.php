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

lock("workerhistory");


//Worker count (global)
$sql = "SELECT p.username as username, p.associateduserid FROM pool_worker p RIGHT JOIN (SELECT count(id) AS id, username FROM shares WHERE time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username UNION SELECT count(id) AS id, username FROM shares_history WHERE time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username) a ON p.username=a.username group by username";
$result = mysql_query($sql);
$num_rows = mysql_num_rows($result);
if ($num_rows > 0)
{
mysql_query("replace into serverworkerhistory (workerrate,currentdate) values ('".$num_rows."',substring(now(),1,10))") or die("failed to insert into serverworkerrate: ".mysql_error());
}


//Worker count (individual)
$sql = "SELECT p.username as username, p.associateduserid as associatedUserId FROM pool_worker p RIGHT JOIN (SELECT count(id) AS id, username FROM shares WHERE time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username UNION SELECT count(id) AS id, username FROM shares_history WHERE time > DATE_SUB(now(), INTERVAL 1440 MINUTE) group by username) a ON p.username=a.username group by username";
$result = mysql_query($sql);
$num_rows = mysql_num_rows($result);
if ($num_rows > 0)
{
while ($row = mysql_fetch_object($result)) {
       mysql_query("replace into userworkerhistory (associatedUserId,username,currentdate) values ('".$row->associatedUserId."','".$row->username."',substring(now(),1,10))") or die("Failed to insert into userworkerhistory: ".mysql_error());
}
}

?>
