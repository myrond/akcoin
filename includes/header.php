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


#enable caching for this script
$cachethis = 1;
//#alternative cache in memory using APC
//if($cachethis == 2 && $html=apc_fetch($_SERVER['REQUEST_URI'].'2')){
//echo $html;
//exit;
//}
#add page content to $html just dont echo anything

//printf($_SERVER["HTTP_ACCEPT_ENCODING"]);
?>

<?


//Set page starter variables//	
$cookieValid	= false;
$activeMiners = false;

include("requiredFunctions.php");	
include("universalChecklogin.php"); 

function html_compress($html){
if(!empty($GLOBALS['cachethis'])){
if($GLOBALS['cookieValid'] === false) {
if($GLOBALS['cachethis'] === 1) {
if ($_SERVER["REQUEST_METHOD"] === "GET") {
if (strpos($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip") !== false) {
// gzip browser detected
$filename = '/dev/shm/lua/'.md5($_SERVER['REQUEST_URI']).'.gz';
$gz = gzopen($filename, "w9");
gzwrite($gz, $html);
gzclose($gz);
}else
{
// NO gzip write a plain text file instead!
$filename = '/dev/shm/lua/'.md5($_SERVER['REQUEST_URI']);
$gz = fopen($filename, "w9");
fwrite($gz, $html);
fclose($gz);
}
}
}
}
}
return $html;
}

$html = "";
ob_start("html_compress");





if (!isset($pageTitle)) $pageTitle = outputPageTitle(); 
else $pageTitle = outputPageTitle(). " ". $pageTitle;
	
?>
<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo $pageTitle;?></title>
		<!--This is the main style sheet-->
<?
if ( isset($_SERVER["HTTPS"]))
{
?>
		<link rel="stylesheet" href="https://akpool.org/css/main-ssl.css" type="text/css" /> 
<?
}
else
{
?>
                <link rel="stylesheet" href="/css/main.css" type="text/css" />
<?
}
?>




<style type="text/css"> 
 
#tabs {
	float:left;
	width:100%;
	font-size:93%;
	line-height:normal;
	border-bottom:1px solid #666;
	margin-bottom:1em; /*margin between menu and rest of page*/
	overflow:hidden;
	}
 
#tabs ul {
	margin:0;
	padding:10px 10px 0 0px;
	list-style:none;
	
	}
 
#tabs li {
	display:inline;
	margin:0;
	padding:0;
	}
 
#tabs a {
	float:left;
	background:url("http://akpool.org/images/left.png") no-repeat left top;
	margin:0;
	padding:0 0 0 6px;
	text-decoration:none;
	}
 
#tabs a span {
	float:left;
	display:block;
	background:url("http://akpool.org/images/right.png") no-repeat right top;
	padding:6px 15px 4px 6px;
	margin-right:2px;
	color:#FFF;
	}
 
/* Commented Backslash Hack hides rule from IE5-Mac \*/
#tabs a span {float:none;}
 
/* End IE5-Mac hack */
#tabs a:hover span {
	}
 
#tabs a:hover {
	background-position:0% -42px;
	}
 
#tabs a:hover span {
	background-position:100% -42px;
	}
 
</style> 
		<?php
			//If user isn't logged in load the login.js
			if(!$cookieValid){
		?>
			<script src="/js/login.js"></script>
		<?php
			}
		?>
	</head>
	<body>
		<div id="topbar">	
			<div id="logodiv">
					<img src="images/logo.png">
			</div>
			<div id="toprightdiv">
						<a href="http://www.mtgox.com" target="_blank">MtGox (USD):</a><?php print number_format($settings->getsetting('mtgoxlast'),1); ?>  
						<a href="/hashhistory.php">Hashes:</a><?php print round($settings->getsetting('currenthashrate')/1000,1); ?> GH/s 
						<a href="/workerhistory.php">Workers:</a><?php print $settings->getsetting('currentworkers'); ?> 
						<a href="/sharehistory.php">Shares:</a><?php print $settings->getsetting('currentroundshares'); ?>
</div>
		<?php include ("menu.php"); ?>		
		</div>
<div class="maincontainer">
		<?php include ("leftsidebar.php"); ?>		
<div id="middlecolumn">
