<html>
<body>
<table style="width:650px">
 <tr>
 <td style="padding:0 20px;">
	<p style="font-size: 16px;font-weight: bold">Reset Password for <?php echo $identity;?></p>
	<p>Your password has been reset to: <?php echo $new_password;?></p>
	<p>Click on the link below and use this password to log into your account. If the link is not clickable, please copy and paste it into your browser.</p>
	<p style="text-decoration:none;color:#bd222a;"><a style="text-decoration:none;color:#bd222a;" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/login">http://<?php echo $_SERVER['HTTP_HOST'];?>/login</a></p>
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