<html>
<body>
	<h1>New Password for <?php echo $identity;?></h1>
	
	<p>Your password has been reset to: <?php echo $new_password;?></p>
	<p>Click on the link below and use this password to log into your account. If the link is not clickable, please copy and paste it into your browser.</p>
	<p><a href="http://<?php echo $_SERVER['HTTP_HOST'];?>/login">http://<?php echo $_SERVER['HTTP_HOST'];?>/login</a></p>
</body>
</html>