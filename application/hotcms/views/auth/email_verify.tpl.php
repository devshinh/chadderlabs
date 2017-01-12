<html>
<body>
	<h1>Email Verification</h1>
  <p>Please click on the link below to verify your email address. If you cannot click on the link, please copy and paste it into your browser.</p>
	<p><a href="http://<?php echo $_SERVER['HTTP_HOST'].'/my-account/verify-email/'. $id .'/'. $activation;?>">Verify Your Email</a></p>
	<p>http://<?php echo $_SERVER['HTTP_HOST'].'/my-account/verify-email/'. $id .'/'. $activation;?></p>
</body>
</html>