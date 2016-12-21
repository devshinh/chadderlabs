<?php if ($sTitle>''){ ?><h1><?php echo $sTitle;?></h1><?php } ?>

<?php if ( $message ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error ){ ?><div class="error"><?php echo $error;?></div><?php } ?>
	
<p class="title">Register your Account</p>
<p>Complete the form below to manage your account and phone(s) online.</p>
<form action="register" method="post">
<table id="register_form">  
  <tr>
    <th><label for="first_name">First Name:</label></th>
    <td><?php echo form_input($first_name);?></td>
  </tr>
  <tr>
    <th><label for="last_name">Last Name:</label></th>
    <td><?php echo form_input($last_name);?></td>
  </tr>
  <tr>
    <th><label for="postal">Postal Code:</label></th>
    <td><?php echo form_input($postal);?></td>
  </tr>
  <tr>
    <th><label for="email">Email Address:</label></th>
    <td><?php echo form_input($email);?></td>
  </tr>
  <tr>
    <th><label for="email_confirm">Confirm Email Address:</label></th>
    <td><?php echo form_input($email_confirm);?></td>
  </tr>
  <tr>
    <th><label for="password">Password:</label></th>
    <td><?php echo form_input($password);?></td>
  </tr>
  <tr>
    <th><label for="password_confirm">Confirm Password:</label></th>
    <td><?php echo form_input($password_confirm);?></td>
  </tr>
  <!--
  <tr>
    <td></td>
    <td><div id="divCaptcha"></div></td>
  </tr>
  <tr>
    <th><p class="em">Type the letters above:</p></th>
    <td><input type="text" name="captcha" id="captcha" value="" /></td>
  <tr>
    <td colspan="2"><input type="checkbox" class="check" id="newsletter" name="newsletter" value="1" /> 
    <label for="newsletter">I would like to be notified of News and Special Offers.</label></td>
  </tr>
  <tr>
    <td colspan="2"><input type="checkbox" class="check" id="terms" name="terms" value="1" /> 
    <label for="terms">I have read and agree with the <a href="/privacy-policy" title="privacy policy" target="_blank">privacy policy</a> and <a href="/terms" title="website terms and conditions of use" target="_blank">website terms and conditions of use</a>.</label></td>
  </tr>
   <tr>
    <td colspan="2" align="right"><span class="formnote">*</span> <span class="footnote">indicates mandatory fields</span></td>
  </tr -->
  
  <tr>
    <td colspan="2">
      <input type="submit" name="submit" class="triangle" value="Register" />
    </td>
  </tr>
</table>
</form>
<br /><br />
