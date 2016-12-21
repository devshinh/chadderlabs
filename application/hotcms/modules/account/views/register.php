<?php if ( $message ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error ){ ?><div class="error alert alert-error"><?php echo $error;?></div><?php } ?>
	
<div id="registerPage" class="hero-unit">
  <h1 class="box-title">Register your Account</h1>
  <p>EA retail Professionals is FREE online training for retail sales professionals. Train on EA's products to earn points to claim EA merchandise.</p>  
  <form action="register" method="post" id="register_form">
    <div class="container-fluid">
      <div class="span6">
        <div class="row-fluid">
          <label for="first_name">First Name: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_input($first_name); ?>
        </div>
        <div class="row-fluid">
          <label for="last_name">Last Name: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_input($last_name); ?>
        </div>
        <div class="row-fluid">
          <label for="screen_name">Screen Name: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_input($screen_name); ?>
        </div>      
        <div class="row-fluid">
          <label for="email">Email Address: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_input($email); ?>
        </div>     
        <div class="row-fluid">
          <label for="email_confirm">Confirm Email Address: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_input($email_confirm); ?>
        </div>
        <div class="row-fluid">
          <label for="password">Password: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_input($password); ?>
        </div>
        <div class="row-fluid">
          <label for="password_confirm">Confirm Password: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_input($password_confirm); ?>
        </div>
        <div class="row-fluid">
          <label>Country: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_radio('country_code', 'US', $selected_country == 'US', 'id="country_us"'); ?>
          <label for="country_us" style="display:inline-block;margin-right:20px">USA</label>
          <?php echo form_radio('country_code', 'CA', $selected_country == 'CA', 'id="country_ca"'); ?>
          <label for="country_ca" style="display:inline-block">Canada</label>
        </div>
      </div> <!-- /span6 -->
      <div class="span6">
        <div class="row-fluid">
          <label>Retailer: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_dropdown('retailer', $retailers, $selected_retailer); ?>
        </div>
        <div class="row-fluid">
          <label>Location: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_dropdown('store', $stores, $selected_store); ?>
        </div>
        <div class="row-fluid">
          <label for="referral_code">Referral Code:</label>
        </div>
        <div class="row-fluid">
          <?php echo form_input($referral_code); ?>
        </div>
        <div class="row-fluid">
          <label>Employment: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_dropdown('employment', $employments, $selected_employment); ?>
        </div>
        <div class="row-fluid">
          <label>Job Title: <span class="red">*</span></label>
        </div>
        <div class="row-fluid">
          <?php echo form_dropdown('job_title', $job_titles, $selected_job_title); ?>
        </div>
        <div class="row-fluid">
          <label for="hire_date">Hire Date:</label>
        </div>
        <div class="row-fluid">
          <?php echo form_input($hire_date); ?>
        </div>
        <div class="row-fluid">
          <label>I would like to receive:</label>
        </div>
        <div class="row-fluid">
          <?php echo form_checkbox('newsletter_monthly', '1', $selected_newsletter_monthly == 1, 'id="newsletter_monthly"'); ?>
          <label for="newsletter_monthly" style="display:inline-block">Monthly Newsletters</label>
        </div>
        <div class="row-fluid">
          <?php echo form_checkbox('newsletter_newlab', '1', $selected_newsletter_newlab == 1, 'id="newsletter_newlab"'); ?>
          <label for="newsletter_newlab" style="display:inline-block">Alerts about new Labs</label>
        </div>
        <div class="row-fluid">
          <?php echo form_checkbox('newsletter_newswag', '1', $selected_newsletter_newswag == 1, 'id="newsletter_newswag"'); ?>
          <label for="newsletter_newswag" style="display:inline-block">Alerts about new SWAG</label>
        </div>
        <div class="row-fluid">
          <?php echo form_checkbox('newsletter_survey', '1', $selected_newsletter_survey == 1, 'id="newsletter_survey"'); ?>
          <label for="newsletter_survey" style="display:inline-block">Survey Invitations</label>
        </div>
      </div> <!-- /span6 -->
      <div class="row-fluid">
        <div class="span3">
          <input type="submit" name="submit" class="btn btn-primary btn-large" value="Register" />
          <?php echo form_hidden('ref', $_GET['ref']); ?>
        </div>
        <div class="span9 mandatory-fields-wrappper">
          All fields marked with <span class="red">*</span> are mandatory.
        </div>
      </div>
    </div> <!-- /container-fluid -->
  </form>
</div>

