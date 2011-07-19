<div id="ddtoptabs">
<ul>
<li style="margin-left: 1px"><a href="/" title="Home"><span>Home</span></a></li>
	<?php
		if(!$cookieValid){
		//Display this menu if the user isn't logged in
	?>
<li><a href="/register.php" title="Register"><span>Register</span></a>
	<?php
	} else if($cookieValid){
	?>
<li><a href="/accountdetails.php" title="Account Details"><span>Account Details</span></a>
	<?php
	//If this user is an admin show the adminPanel.php link
	if($isAdmin){
	?>
<li><a href="/adminPanel.php" title="(Admin Panel)"><span>(Admin Panel)</span></a>
	<?php	
		}
	}
	?>
<li><a href="/stats.php" title="Stats"><span>Stats</span></a>
<li><a href="/blocks.php" title="Blocks"><span>Blocks</span></a>
<li><a href="/gettingstarted.php" title="Getting Started"><span>Getting Started</span></a>
<li><a href="/about.php" title="About"><span>About</span></a>
</div>	
<div id="ddtoptabsline"><div class="leftarrow"></div>&nbsp;<div class="rightarrow"></div></div> 
</div> 
