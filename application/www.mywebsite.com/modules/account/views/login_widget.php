<div id="messageContainer">
  <?php if ($login['message'] > '') { ?>
    <div class="message">  
      <div class="message_close">
        <a onclick="closeMessage()">[close]</a>
      </div>
      <?php echo $message; ?>
    </div><?php } ?>
  <?php if ($error > '') { ?><div class="message error">
      <div class="message_close">
        <a onclick="closeMessage()">[close]</a>
      </div>
      <?php echo $error; ?></div><?php } ?>
</div>
<div id="loginWidget" class="hero-unit">
  <h1><?php echo $title ?></h1>
  <p><?php echo $welcome_text; ?></p>
  <div class="input js_validation" id="loginForm">
    <form method="post" id="login_form" action="login">
      <div class="row-fluid">
        <label for="email">Enter your email: <span class="red">*</span></label>
      </div>
      <div class="row-fluid">
        <?php echo form_input($login['username']); ?>
      </div>      

      <div class="row-fluid">
        <label for="password">Password: <span class="red">*</span></label>
      </div>
      <div class="row-fluid">        
        <?php echo form_input($login['password']); ?>
      </div>
        <!--
      <div class="row-fluid">   
        <div class="span1"><?php echo form_checkbox('remember', '1', FALSE); ?></div>
        <div class="span11"><label for="remember" class="remember-wrapper">Remember me</label></div>
      </div>
        -->
        <div class="row-fluid">    
            <i>All fields marked with <span class="red">*</span> are mandatory.</i>
        </div>
        <div class="row-fluid" style="margin-top: 5px;">
        <div class="span4"><input class="btn btn-primary btn-large" type="submit" value="Login" /></div>
        <div class="span8 forgot-pass-link-wrapper">
          <a class="view-all-link" href="/forgot-password" title="forgot password"><span class="view-all-arrows">» </span>Forgot password?</a>
        </div>
      </div>
    </form>
  </div>
</div>