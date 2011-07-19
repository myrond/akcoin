<?php 
include("includes/requiredFunctions.php");

setcookie($cookieName,"", time() - 3600, $cookiePath, $cookieDomain);
?>
<html>
  <head>
	<title><?php echo antiXss(outputPageTitle());?> </title>
	<link rel="stylesheet" href="/css/dynamicdrive.css" type="text/css" />
	<meta http-equiv="refresh" content="0;url=/">
  </head>
  <body>
	<div id="pagecontent">
		<h1>You have been logged out<br/>
		<a href="/">Click here if you continue to see this message</a></h1>
	</div>
  </body>
</html>
