<html>
<body>
<table style="width:650px">
 <tr>
 <td style="padding:0 20px;">
	<p style="font-size: 16px;font-weight: bold">Hello <?php echo $first_name; ?>,</p>
	<p>Thank you for signing up for the cheddarLabs!</p>
  	<p>Please click on the link below to verify your email address and log into your account. If you cannot click on the link, please copy and paste it into
your browser.</p>
  <p><a style="text-decoration:none;color:#bd222a;" href="http://<?php echo $_SERVER['HTTP_HOST'].'/my-account/activate/'. $id .'/'. $activation;?>">Activate Your Account</a></p>
	<p style="text-decoration:none;color:#bd222a;"><a style="text-decoration:none;color:#bd222a;" href="http://<?php echo $_SERVER['HTTP_HOST'].'/my-account/activate/'. $id .'/'. $activation;?>">http://<?php echo $_SERVER['HTTP_HOST'].'/my-account/activate/'. $id .'/'. $activation;?></a></p>
	<p>Thanks!</p>
	</td>
	</tr>
	<tr>
	<td style="border-top: 3px solid #D4D5D7;text-align:right">
  <div id="footer">
      <p>Copyright &copy; <?php echo date('Y') ?>&nbsp;&nbsp;  <b>Cheddar Labs</b> by<b><a target="_blank" href="http://www.hottomali.com">Hot Tomali Communications</a></b></p>
  </div>	 
	 </td>
	</tr>
</table>
</body>
</html>