<?php if ($register['message']) { ?><div class="message"><?php echo $register['message']; ?></div><?php } ?>
<?php if ($register['error']) { ?><div class="error"><?php echo $register['error']; ?></div><?php } ?>
<div id="registerWidgetHome" class="hero-unit">
    <div class="box-title"><?php echo $title ?></div>
    <p><?php echo $welcome_text; ?></p>
    <?php if ($register['ref_site_name']) { echo '<p>Sign up for Cheddar Labs to start training on ' . $register['ref_site_name'] . ' and more!</p>'; } ?>
    <form action="signup" method="post" id="register_form_home">
  <?php
    if (is_array($_REQUEST) && array_key_exists('ref', $_REQUEST)) {
      $ref_id = $_REQUEST['ref'];
    }
    else {
      $ref_id = 1;
    }
    echo form_hidden(array('ref' => $ref_id, 'noerror' => '1', 'form' => 'register'));
  ?>
      <div class="container-fluid">
          <div class="row-fluid">
              <div class="row-fluid">
                  <label for="first_name">First Name: <span class="red">*</span></label>
                  <?php echo form_input($register['first_name']); ?>
              </div>  
              <div class="row-fluid">
                  <label for="last_name">Last Name: <span class="red">*</span></label>
                  <?php echo form_input($register['last_name']); ?>
              </div>          
              <div class="row-fluid">
                  <label for="email">Email Address: <span class="red">*</span></label>
                  <?php echo form_input($register['email']); ?>
              </div>     
              <div class="row-fluid">
                <label for="screen_name">Screen Name: <span class="red">*</span></label>
                <?php echo form_input($register['screen_name']); ?>
              </div>
          </div>
          <div class="row-fluid">
              All fields marked with <span class="red">*</span> are mandatory.
          </div>   
          <br />
          <div class="row-fluid">
              <input type="submit" name="submit" class="btn btn-primary btn-large" value="Sign Up" />
          </div>          
          <br /><p>Existing member? <a href="/login">Log In Here</a></p>
      </div> <!-- /container-fluid --> 
</form>
</div>

