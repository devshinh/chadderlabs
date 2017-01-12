<h1><?php echo $sTitle;?></h1>

<?php if ( $message ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<p>Please enter your email address below, and we will send you an email to reset your password.</p>

<form action="forgot-password" method="post">

  <p class="em">Email Address:
  <?php echo form_input($email);?>
  </p>
  
  <p><input class="triangle" name="submit" id="submit" value="Submit" type="submit"></p>
  
</form>