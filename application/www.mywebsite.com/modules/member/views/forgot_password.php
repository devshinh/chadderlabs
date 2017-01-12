<h1><?php echo $sTitle;?></h1>

<?php if ( $message ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<p>Please enter your email address below, and we will send you an email to reset your password.</p>

<form action="forgot-password" method="post">
<table id="table_form">  
  <tr>
      <th><label for="email">Email: <span class="red">*</span></label></th>
      <td><?php echo form_input($email);?></td>
  </tr>
  </table>
  
  <p><input class="triangle" name="submit" id="submit" value="Submit" type="submit"></p>
  
</form>