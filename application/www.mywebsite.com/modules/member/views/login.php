<?php if ($sTitle>''){ ?><h1><?php echo $sTitle;?></h1><?php } ?>

<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>
	
<p class="title">Log in to your online Account</p>
  
<form action="login" method="post">
<table>  
  <tr>
    <th><label for="email">Username:</label></th>
    <td><?php echo form_input($username);?></td>
  </tr>
  
  <tr>
    <th><label for="password">Password:</label></th>
    <td><?php echo form_input($password);?></td>
  </tr>
  
  <!-- tr>
	  <td><label for="remember">Remember me:</label></td>
	  <td><?php echo form_checkbox('remember', '1', FALSE);?></td>
	</tr -->
  
  <tr>
    <td colspan="2">
      <input class="triangle" name="submit" id="submit" value="Log in" type="submit">
    </td>
  </tr>
</table>
</form>

<p class="footnote">Forgot password? <a href="/forgot-password" title="forgot password">Click here.</a></p>

<br /><br />
