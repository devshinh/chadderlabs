<html>
<body>
<table style="width:650px">

 <td style="padding:0 20px;">
	<p style="font-size: 16px;font-weight: bold">Hi <?php echo $first_name; ?>,</p>
           <p>Someone has outbid you on <?php echo $product_name; ?>.</p>
           <p style="text-decoration:none;color:#bd222a;"><a style="text-decoration:none;color:#bd222a;" href="<?php echo $_SERVER['HTTP_HOST'] .'/'.$item_link; ?>"><?php echo $_SERVER['HTTP_HOST'] .'/'. $item_link; ?></a></p>
	<p>Thanks!</p>
	</td>
	</tr>
	<tr>
	<td style="border-top: 3px solid #D4D5D7;text-align:right">
  <div id="footer">
   <p>Copyright &copy; <?php echo date('Y') ?>&nbsp;&nbsp; <a _target="_blank" href="http://www.hottomali.com" title="Hot Tomali logo"><img style="width:45px;height:22px;vertical-align:middle" alt="HotTomali Logo" src="http://<?php echo $_SERVER['HTTP_HOST']?>/themes/auction/images/htc_logo.jpg" /></a></p>
  </div>	 
	 </td>
	</tr>
</table>
</body>
</html>
