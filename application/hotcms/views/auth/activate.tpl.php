<html>
<body>
	<p>Hello <?php echo $first_name; ?>,</p>
	<br />
	<p>Thank you for signing up for the MWCT Art Auction for Africa!</p><br />
  	<p>Please click on the link below to verify your email address and log into your account. If you cannot click on the link, please copy and paste it into
your browser.</p>
	<p><a href="http://<?php echo $_SERVER['HTTP_HOST'].'/my-account/activate/'. $id .'/'. $activation;?>">Activate Your Account</a></p>
	<p>http://<?php echo $_SERVER['HTTP_HOST'].'/my-account/activate/'. $id .'/'. $activation;?></p>
	<br />
	<p>
		Thanks!<br/>
		Hot Tomali & MWCT
	</p>
</body>
</html>