</div>
<!--
<div id="right column">
<div class="titlebar"><span style="color: #da6e7b">Right Column</span></div>
</div>
-->
<div class="clearfix"></div> 
			<div id="contentfooter">akpool.org
			</div>
</div>
<div id="footerarea"> 
<div class="maincontainer" style="background-color: transparent; border-width: 0"> 
 
<div id="footermenu"> 
<ul> 
<li><a href="/" title="akpool home">Home</a></li> 
<?
$current_path = $_SERVER['SCRIPT_NAME'];
//if ( isset($_SERVER["HTTPS"]))
//{

//<li><font color="green">SSL Protected</font></li> 
//}
//else
//{
?>
<li><a href="/logout.php" title="logout">Logout</a></li>
<li><a href="https://akpool.org<?echo $current_path?>" title="SSL">SSL</a></li>
<?
//}
?>


</ul> 
</div> 

	</body>
</html>
<?php


//if(!empty($GLOBALS['cachethis'])){
//$html = ob_get_contents();
//html_compress("$html");
//}
while(@ob_end_flush());
?>
