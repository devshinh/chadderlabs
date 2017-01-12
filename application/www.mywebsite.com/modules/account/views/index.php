<h1><?php echo $sTitle;?></h1>
 
<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<table cellpadding="0">
	<tr>     
		<td valign="top">
    
<p class="title">Register your Account</p>
<p>Complete the form below to manage your account.</p>
<form action="register" method="post">
<table>  
  <tbody><tr>
    <th><label for="first_name">First Name:</label></th>
    <td><input name="first_name" value="" id="first_name" type="text"></td>
  </tr>
  <tr>
    <th><label for="last_name">Last Name:</label></th>
    <td><input name="last_name" value="" id="last_name" type="text"></td>
  </tr>
  <tr>
    <th><label for="postal">Postal Code:</label></th>
    <td><input name="postal" value="" id="postal" type="text"></td>
  </tr>
  <tr>
    <th><label for="email">Email Address:</label></th>
    <td><input name="email" value="" id="email" type="text"></td>
  </tr>
  <tr>
    <th><label for="email_confirm">Confirm Email Address:</label></th>
    <td><input name="email_confirm" value="" id="email_confirm" type="text"></td>
  </tr>
  <tr>
    <th><label for="password">Password:</label></th>
    <td><input name="password" value="" id="password" type="password"></td>
  </tr>
  <tr>
    <th><label for="password_confirm">Confirm Password:</label></th>
    <td><input name="password_confirm" value="" id="password_confirm" type="password"></td>
  </tr>
  
  <tr>
    <td></td>
    <td><div id="divCaptcha"></div></td>
  </tr>
  <tr>
    <th><p class="em">Type the letters above:</p></th>
    <td><input name="captcha" id="captcha" value="" type="text"></td>
  </tr>
  <tr>
    <td colspan="2"><input class="check" id="newsletter" name="newsletter" value="1" type="checkbox"> 
    <label for="newsletter">I would like to be notified of News and Special Offers.</label></td>
  </tr>
  <tr>
    <td colspan="2"><input class="check" id="terms" name="terms" value="1" type="checkbox"> 
    <label for="terms">I have read and agree with the <a href="/privacy-policy" title="privacy policy" target="_blank">privacy policy</a> and <a href="/terms" title="website terms and conditions of use" target="_blank">website terms and conditions of use</a>.</label></td>
  </tr>
  
  <tr>
    <td colspan="2">
      <input name="submit" class="triangle" value="Register" type="submit">
    </td>
  </tr>
</tbody></table>
</form>
    </td>
    <td valign="top">
<p class="title">Log in to your On-line Account</p>
<form action="login" method="post">

<table>  
  <tbody><tr>
    <th><label for="email">Email:</label></th>
    <td><input name="email" value="" id="email" type="text"></td>
  </tr>
  
  <tr>
    <th><label for="password">Password:</label></th>
    <td><input name="password" value="" id="password" type="password"></td>
  </tr>
  
  <tr>
    <td colspan="2">
      <input class="triangle" name="submit" id="submit" value="Log in" type="submit">
    </td>
  </tr>
</tbody></table>

</form>    

<p class="footnote">Forgot password? <a href="/forgot-password" title="forgot password">Click here.</a></p>

    </td>
	</tr>
</table>

<br /><br />