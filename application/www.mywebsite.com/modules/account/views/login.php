<div class="span8">
<div class="hero-unit" id="loginPage">
<?php if ($sTitle > '') { ?><h1><?php echo $sTitle; ?></h1><?php } ?>

<?php if ($message > '') { ?><div class="message"><?php echo $message; ?></div><?php } ?>
<?php if ($error > '') { ?><div class="error alert alert-error"><?php echo $error; ?></div><?php } ?>

<p class="title">Log in to your online account. Or register <a href="http://<?php echo $sMainDomain; ?>/signup?ref=<?php echo $sSiteID; ?>">new account</a>.</p>

  <div class="input js_validation">
    <form method="post" id="login_form" action="login">
      <div class="row-fluid">
        <label for="email">Enter your email: <span class="red">*</span></label>
      </div>
      <div class="row-fluid">
        <?php echo form_input($username); ?>
      </div>      

      <div class="row-fluid">
        <label for="password">Password: <span class="red">*</span></label>
      </div>
      <div class="row-fluid">        
        <?php echo form_input($password); ?>
      </div>
      <!--
      <div class="row-fluid">   
        <div class="span1"><?php echo form_checkbox('remember', '1', FALSE); ?></div>
        <div class="span11"><label for="remember" class="remember-wrapper">Remember me</label></div>
      </div>    
      -->
      <div class="row-fluid">
        <div class="span2"><input class="btn btn-primary btn-large" type="submit" value="Login" /></div>
      </div>
      <div class="row-fluid">
        <div class="span10 forgot-pass-link-wrapper">
          <a class="view-all-link" href="/forgot-password" title="forgot password"><span class="view-all-arrows">» </span>Forgot password?</a>
        </div>
      </div>
    </form>
  </div>
</div>
</div>