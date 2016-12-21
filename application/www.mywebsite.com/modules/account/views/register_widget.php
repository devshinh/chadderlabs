<?php if ($register['message']) { ?><div class="message"><?php echo $register['message']; ?></div><?php } ?>
<?php if ($register['error']) { ?><div class="error"><?php echo $register['error']; ?></div><?php } ?>
<div class="box-title"><h1><?php echo $title ?></h1></div>
<p><?php echo $welcome_text; ?></p>
<?php if ($register['ref_site_name']) {
    echo '<p>Sign up for Cheddar Labs to start training on ' . $register['ref_site_name'] . ' and more!</p>';
} ?>
<form action="signup" method="post" id="register_form">
    <?php
    if (is_array($_REQUEST) && array_key_exists('ref', $_REQUEST)) {
        $ref_id = $_REQUEST['ref'];
    } else {
        $ref_id = 1;
    }
    echo form_hidden(array('ref' => $ref_id, 'form' => 'register'));
    ?>
    <div class="container-fluid">
        <div class="row-fluid mandatory-fields-wrappper">
                <i>All fields marked with <span class="red">*</span> are mandatory.</i>
        </div>          
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <h2>About you</h2>
                </div>
                <div class="row-fluid">
                    <label for="first_name">First Name: <span class="red">*</span></label>
<?php echo form_input($register['first_name']); ?>
                </div>  
                <div class="row-fluid">
                    <label for="last_name">Last Name: <span class="red">*</span></label>
<?php echo form_input($register['last_name']); ?>
                </div>          
                <div class="row-fluid">
                    <label for="email">Screen Name: <span class="red">*</span></label>
<?php echo form_input($register['screen_name']); ?>
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
                    <label for="password2">Password: <span class="red">*</span></label>
<?php echo form_input($register['password'],'',$extra); ?>
                </div>
                <div class="row-fluid">
                    <label for="password_confirm">Confirm Password: <span class="red">*</span></label>
<?php echo form_input($register['password_confirm'],'',$extra); ?>
                </div>
            </div>

            <div class="span6">
                <div class="row-fluid">
                    <h2>Where you work</h2>
                </div>            
                <div class="row-fluid">
                    <label>Country: <span class="red">*</span></label>
                </div>
                <div class="row-fluid">
<?php
//echo form_dropdown('country', $register['country_options'], $register['selected_country']);
$id = 'id="country_code"';
echo form_dropdown('country_code', $register['country_options'], $register['selected_country'],$id);
?>                    
                </div>
                <div class="row-fluid">
                    <label>State/Province: <span class="red">*</span></label>
                </div>                    
                <div class="row-fluid">
<?php 
$id = 'id="province"';
echo form_dropdown('province', $register['province_options'], $register['selected_province'],$id); ?>
                </div>

                <div class="row-fluid">
                    <label>Retailer: <span class="red">*</span></label>
<?php 
$id = 'id="retailer"';
echo form_dropdown('retailer', $register['retailers'], $register['selected_retailer'],$id); ?>
                </div>
                <div id="custom_retailer_wrapper" style="display:none;">
                    <div class="row-fluid">
                        <label>Retailer's Name: <span class="red">*</span></label>
                    </div>
                    <div class="row-fluid">
<?php echo form_input($register['retailer_name']); ?>
                    </div>

                </div>            
                <div class="row-fluid">
                    <label>Location: <span class="red">*</span></label>
<?php 
$id = 'id="store"';
echo form_dropdown('store', $register['stores'], $register['selected_store'],$id); ?>
                </div>
                <div id="custom_location_wrapper" style="display:none;">
                    <div class="row-fluid">
                        <label>Location's Name: <span class="red">*</span></label>
                    </div>
                    <div class="row-fluid">
<?php echo form_input($register['retailer_location_name']); ?>
                    </div>

                </div>             
                <div class="row-fluid">
                    <label>Employment: <span class="red">*</span></label>
<?php 
$id = 'id="employment"';
echo form_dropdown('employment', $register['employments'], $register['selected_employment'],$id); ?>
                </div>
                <div id="custom_employment_wrapper" style="display:none;">
                    <div class="row-fluid">
                        <label>Employment Type: <span class="red">*</span></label>
                    </div>
                    <div class="row-fluid">
<?php echo form_input($register['employment_type']); ?>
                    </div>

                </div>             
                <div class="row-fluid">
                    <label>Job Title: <span class="red">*</span></label>
<?php
$id = 'id="job_title"';
echo form_dropdown('job_title', $register['job_titles'], $register['selected_job_title'],$id); ?>
                </div>
                <div id="custom_job_title_wrapper" style="display:none;">
                    <div class="row-fluid">
                        <label>Job Title Name: <span class="red">*</span></label>
                    </div>
                    <div class="row-fluid">
<?php echo form_input($register['job_title_name']); ?>
                    </div>

                </div>            
                <div class="row-fluid">
                    <label for="hire_date">Hire Date:</label>
<?php echo form_input($register['hire_date']); ?>
                </div>
                <div class="row-fluid">
                    <label for="referral_code">Promo Code:</label>
<?php echo form_input($register['referral_code']); ?>
                </div>                
            </div> 
        </div>
        <div class="row-fluid">
            <label>I would like to receive:</label>
        </div>
        <div class="row-fluid">
            <div class="span3">
<?php echo form_checkbox('newsletter_monthly', '1', $register['selected_newsletter_monthly'] == 1, 'id="newsletter_monthly"'); ?>
                <label for="newsletter_monthly" style="display:inline-block">Monthly Newsletters</label>
            </div>
            <div class="span3">
<?php echo form_checkbox('newsletter_newlab', '1', $register['selected_newsletter_newlab'] == 1, 'id="newsletter_newlab"'); ?>
                <label for="newsletter_newlab" style="display:inline-block">Alerts about new Labs</label>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span3">
<?php echo form_checkbox('newsletter_newswag', '1', $register['selected_newsletter_newswag'] == 1, 'id="newsletter_newswag"'); ?>
                <label for="newsletter_newswag" style="display:inline-block">Alerts about new SWAG</label>
            </div>
            <div class="span3">
<?php echo form_checkbox('newsletter_survey', '1', $register['selected_newsletter_survey'] == 1, 'id="newsletter_survey"'); ?>
                <label for="newsletter_survey" style="display:inline-block">Survey Invitations</label>
            </div>
        </div>        
      
        <div class="row-fluid">
            <div class="span12">
                <input type="submit" name="submit" class="btn btn-primary btn-large pull-right" value="Register" />
            </div>
        </div>
    </div> <!-- /container-fluid -->
</form>

