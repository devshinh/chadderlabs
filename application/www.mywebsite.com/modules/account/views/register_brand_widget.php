<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="hero-unit container-fluid">
<?php 
if ($register['message']) { ?><div class="message"><?php echo $register['message']; ?></div><?php } ?>
<?php if ($register['error']) { ?><div class="error"><?php echo $register['error']; ?></div><?php } ?>
<div class="box-title"><h1><?php echo $title ?></h1></div>
<p><?php echo $welcome_text; ?></p>
<form action="brand-sign-up" method="post" id="register_brand_form">
    <?php
    echo form_hidden(array('form' => 'register_brand'));
    ?>
        <div class="row-fluid mandatory-fields-wrappper">
                <i>All fields marked with <span class="red">*</span> are mandatory.</i>
        </div>          
        <div class="row-fluid">
            <div class="span12">
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
                    <label for="email_confirm">Confirm Email Address: <span class="red">*</span></label>
<?php 
      $extra = 'autocomplete="off"';
      echo form_input($register['email_confirm'],'',$extra); ?>
                </div>
                <div class="row-fluid">
                    <label for="phone">Phone: <span class="red">*</span></label>
<?php echo form_input($register['phone']); ?>
                </div>   
                <div class="row-fluid">
                    <label for="company">Company: <span class="red">*</span></label>
<?php echo form_input($register['company']); ?>
                </div>     
                <div class="row-fluid">
                    <label for="company">Comments:</label>
<?php echo form_textarea($register['comments']); ?>
                </div>  
                
            </div>
        </div> 
        <div class="row-fluid">
            <div class="pull-left span6" style="margin-top: 10px;">
             <div class="g-recaptcha" data-sitekey="6LfAhwITAAAAAAlvhu0WC6HI8ZTBwDd9o6mBZeL9" style="width:100%;"></div>  
            </div>
            <div class="span6" style="margin-top: 42px;margin-left: 0;">
                <input type="submit" name="submit" class="btn btn-primary btn-large pull-right" value="Submit" />
            </div>
        </div>
</form>

</div>