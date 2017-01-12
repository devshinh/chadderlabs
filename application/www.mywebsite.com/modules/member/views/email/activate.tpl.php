<html>
<body>
<table style="width:650px">
 <tr style="border-bottom: 3px solid #D4D5D7;">
  <td>
    <img src="http://<?php echo $_SERVER['HTTP_HOST']?>/themes/auction/images/logo.jpg" alt="auction-logo" align="left"/>
  </td>
 </tr>
 <tr>
  <td>
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
	</td>
	</tr>
	<tr style="border-top: 3px solid #D4D5D7;">
	 <td>
  <div id="footer">
   <p>Copyright &copy; <?php echo date('Y') ?>&nbsp;&nbsp; <a _target="_blank" href="http://www.hottomali.com"><img style="width:45px;height:22px;vertical-align:middle" alt="HotTomali Logo" src="http://<?php echo $_SERVER['HTTP_HOST']?>/themes/<?php echo $sTheme; ?>/images/htc_logo.jpg" /></a></p>
  </div>	 
	 </td>
	</tr>
</table>
</body>
</html>