<h1><?php echo $sTitle;?></h1>

<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<form action="/sign-up" method="post" id="formSignup">
<table>  
  <tr>
    <th><label for="firstname">First Name<span class="red">*</span>:</label></th>
    <td><?php echo form_input($firstname);?></td>
  </tr>
  <tr>
    <th><label for="firstname">Last Name:</label></th>
    <td><?php echo form_input($lastname);?></td>
  </tr>
  <tr>
    <th><label for="email">Email Address<span class="red">*</span>:</label></th>
    <td><?php echo form_input($email);?></td>
  </tr>
  <tr>
    <th><label for="company">Your Postal Code:</label></th>
    <td><?php echo form_input($postal);?></td>
  </tr>
  <tr>
    <th><label for="website">Wireless Number:</label></th>
    <td><?php echo form_input($phone);?><br />
      <input type="checkbox" class="check" id="nonumber" name="nonumber" value="1" />
      <label for="nonumber">I do not have an active wireless number</label>
    </td>
  </tr>
  <tr>
    <td colspan="2"><input type="checkbox" class="check" id="terms" name="terms" value="1" /> 
    <label for="terms">I have read and agree with the <a href="/privacy-policy" title="privacy policy" target="_blank">privacy policy</a> and <a href="/terms" title="website terms and conditions of use" target="_blank">website terms and conditions of use</a>. <span class="red">*</span></label></td>
  </tr>
  
  <tr>
    <td colspan="2"><p class="footnote"> &nbsp;<span class="red">*</span> indicates mandatory fields</p></td>
  </tr>
  
  <tr>
    <td colspan="2"><input class="triangle" name="submit" value="Submit" type="submit" /></td>
  </tr>
</table>
</form>
<br /><br />



