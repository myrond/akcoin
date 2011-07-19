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
//
//    Improved Stats written by Tom Lightspeed (tomlightspeed@gmail.com + http://facebook.com/tomlightspeed)
//    Developed Socially for http://ozco.in
//    If you liked my work, want changes/etc please contact me or donate 16p56JHwLna29dFhTRcTAurj4Zc2eScxTD.
//    May the force be with you.

$pageTitle = "- Hash History";
include ("includes/header.php");

?>

<?php
$numberResults = 30;
?>
<table border=2>
<tr><th colspan="4" scope="col">Server Hash Rate Last <?php echo $numberResults;?> Days</th></tr>
<tr><th scope="col">Date</th><th scope="col">MH/s</th></tr>
<?php

// TOP 30 CURRENT HASHRATES  *************************************************************************************************************************

$result = mysql_query("SELECT currentdate,hashrate from serverhashhistory order by currentdate limit " . $numberResults);
$rank = 1;
$user_found = false;

while ($resultrow = mysql_fetch_object($result)) {
echo "<tr><td>" . $resultrow->currentdate . "</td><td>" . $resultrow->hashrate . "</td></tr>";
}

?>
</table>
<center><a href="/stats.php">View all stats</a></center>
</div>
<?
if ($cookieValid ) {
?>
<div id="rightcolumn">
<table border=2>
<tr><th colspan="4" scope="col"><?echo $userInfo->username; ?> Hash Rate Last <?php echo $numberResults;?> Days</th></tr>
<tr><th scope="col">Date</th><th scope="col">MH/s</th></tr>
<?
 if (!is_numeric($userId)) {
                $tempId = 0;
                return false;
                }

$result = mysql_query("SELECT currentdate,hashrate from userhashhistory where associatedUserId='$userId' order by currentdate limit " . $numberResults);

while ($resultrow = mysql_fetch_object($result)) {
echo "<tr><td>" . $resultrow->currentdate . "</td><td>" . $resultrow->hashrate . "</td></tr>";

}

?>


</table>
</div>
<?
}
else
{
?>
<div id="rightcolumn">
<table border=2>
<tr><th colspan="4" scope="col">Your stats COULD be here!</th></tr>
</table>
</div>

<?
}

	
include("includes/footer.php");

?>
