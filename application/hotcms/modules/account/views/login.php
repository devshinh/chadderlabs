<style>
 #rightContent {
  border: 0;
  margin:0;
 }
</style>
<div id="messageContainer">
  <?php if ($message > '') { ?>
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
<div id="leftContent" class="login">
  <div class="module_header">Login Page</div>
  <div class="sub_menu" style="padding:10px 9px;">
    <h2 class="welcome"> Please enter your username and your password below. </h2>
    <div class="input js_validation" id="loginForm">
    <form method="post" id="login-form">
      <div class="row">
        <label for="email">Username:</label>
        <?php echo form_input($username); ?>
      </div>

      <div class="row">
        <label for="password">Password:</label>
        <?php echo form_input($password); ?>
      </div>
      <div class="row">
        <input class="submit red_button" type="submit" value="<?php echo lang('hotcms_login') ?>" />
      </div>
    </form>
    <p class="footnote">Forgot password? <a href="/forgot-password" title="forgot password">Click here.</a></p>
    </div>
  </div>
</div>
