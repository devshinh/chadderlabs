<hr />
<h2>Contact Us</h2>
<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<form method="post" id="formContact">
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
    <th><label for="company">Your Postal Code<span class="red">*</span>:</label></th>
    <td><?php echo form_input($postal);?></td>
  </tr>
  <tr>
    <th colspan="2" style="text-align:left"><label for="website">My enquiry concerns:</label></th>
  </tr>
  <tr>
    <td colspan="2">
      <?php echo form_checkbox($concerns, 'Product', in_array('Product', $concerns_default)); ?>
      <label>Product</label> &nbsp; &nbsp;
      <?php echo form_checkbox($concerns, 'Service', in_array('Service', $concerns_default)); ?>
      <label>Service</label> &nbsp; &nbsp;
      <?php echo form_checkbox($concerns, 'Feedback', in_array('Feedback', $concerns_default)); ?>
      <label>Feedback</label> &nbsp; &nbsp;
      <?php echo form_checkbox($concerns, 'Other', in_array('Other', $concerns_default)); ?>
      <label>Other</label>
    </td>
  </tr>
  <tr>
    <th><label for="company">Comments:</label></th>
    <td><?php echo form_textarea($comment);?></td>
  </tr>
  <tr>
    <td colspan="2"><?php echo form_checkbox($terms); ?>
    <label for="terms">I would like to be notified of News and Special Offers. I have read and agree with the <a href="/privacy-policy" title="privacy policy" target="_blank">privacy policy</a> and <a href="/terms" title="website terms and conditions of use" target="_blank">website terms and conditions of use</a>.</label></td>
  </tr>
  
  <tr>
    <td colspan="2"><p class="footnote"> &nbsp;<span class="red">*</span> indicates mandatory fields</p></td>
  </tr>

  <tr>
    <td colspan="2"><input type="submit" class="triangle" name="Submit" value="Submit" /></td>
  </tr>
</table>
</form>
<br />
<br />



