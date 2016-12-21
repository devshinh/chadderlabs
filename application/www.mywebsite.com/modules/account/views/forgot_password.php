<div class="hero-unit">
<h1><?php echo $sTitle; ?></h1>

<?php if ($message) { ?><div class="message"><?php echo $message; ?></div><?php } ?>
<?php if ($error) { ?><div class="error"><?php echo $error; ?></div><?php } ?>

<p>Please enter your email address below, and we will send you an email to reset your password.</p>

<form action="forgot-password" method="post">
  <div class="row-fluid">
    <div class="span2">Email Address:</div>
    <div class="span10"><?php echo form_input($email); ?></div>
  </div>
  <br />
  <div class="row-fluid">
    <input class="btn btn-primary btn-large" name="submit" id="submit" value="Submit" type="submit">
  </div>
</form>
</div>
