<html>
<body>
	<h1>Reset Password for <?php echo $identity;?></h1>
	<p>Please click on the link below to reset your password. If it's not clickable in your email client, please copy and paste it into your browser.</p>
	<p><a href="http://<?php echo $_SERVER['HTTP_HOST'].'/my-account/reset_password/'. $forgotten_password_code;?>">http://<?php echo $_SERVER['HTTP_HOST'].'/my-account/reset_password/'. $forgotten_password_code;?></a></p>
</body>
</html>