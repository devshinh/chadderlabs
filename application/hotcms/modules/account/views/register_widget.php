<?php if ($register['message']) { ?><div class="message"><?php echo $register['message']; ?></div><?php } ?>
<?php if ($register['error']) { ?><div class="error"><?php echo $register['error']; ?></div><?php } ?>
<div class="hero-unit">
  <div class="box-title"><?php echo $title ?></div>
  <p><?php echo $welcome_text; ?></p>
  <?php if ($register['ref_site_name']) { echo '<p>Sign up for Cheddar Labs to start training on ' . $register['ref_site_name'] . ' and more!</p>'; } ?>
  <form action="signup" method="post" id="register_form">
  <?php
    if (is_array($_REQUEST) && array_key_exists('ref', $_REQUEST)) {
      $ref_id = $_REQUEST['ref'];
    }
    else {
      $ref_id = 1;
    }
    echo form_hidden('ref', $ref_id);
  ?>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span6">
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
            <label for="email">Confirm Email Address: <span class="red">*</span></label>
            <?php echo form_input($register['email_confirm']); ?>
          </div>
          <div class="row-fluid">
            <label for="email">Password: <span class="red">*</span></label>
            <?php echo form_input($register['password']); ?>
          </div>
          <div class="row-fluid">
            <label for="email">Confirm Password: <span class="red">*</span></label>
            <?php echo form_input($register['password_confirm']); ?>
          </div>
          <div class="row-fluid">
            <label>Country: <span class="red">*</span></label>
          </div>
          <div class="row-fluid">
            <?php echo form_radio('country_code', 'US', $register['selected_country'] == 'US', 'id="country_us"'); ?>
            <label for="country_us" style="display:inline-block;margin-right:20px">USA</label>
            <?php echo form_radio('country_code', 'CA', $register['selected_country'] == 'CA', 'id="country_ca"'); ?>
            <label for="country_ca" style="display:inline-block">Canada</label>
          </div>
          <div class="row-fluid">
            <label>Province: <span class="red">*</span></label>
          </div>                    
          <div class="row-fluid">
            <select name="province">
              <option value="" selected="selected"> - Province - </option>
              <option value="AB">Alberta</option>
              <option value="BC">British Columbia</option>
              <option value="MB">Manitoba</option>
              <option value="NB">New Brunswick</option>
              <option value="NL">Newfoundland and Labrador</option>
              <option value="NS">Nova Scotia</option>
              <option value="NT">Northwest Territories</option>
              <option value="NU">Nunavut</option>
              <option value="ON">Ontario</option>
              <option value="PE">Prince Edward Island</option>
              <option value="QC">Quebec</option>
              <option value="SK">Saskatchewan</option>
              <option value="YT">Yukon</option>
              <option value=""> - States - </option>
              <option value="AL">Alabama</option> 
              <option value="AK">Alaska</option> 
              <option value="AZ">Arizona</option> 
              <option value="AR">Arkansas</option> 
              <option value="CA">California</option> 
              <option value="CO">Colorado</option> 
              <option value="CT">Connecticut</option> 
              <option value="DE">Delaware</option> 
              <option value="DC">District Of Columbia</option> 
              <option value="FL">Florida</option> 
              <option value="GA">Georgia</option> 
              <option value="HI">Hawaii</option> 
              <option value="ID">Idaho</option> 
              <option value="IL">Illinois</option> 
              <option value="IN">Indiana</option> 
              <option value="IA">Iowa</option> 
              <option value="KS">Kansas</option> 
              <option value="KY">Kentucky</option> 
              <option value="LA">Louisiana</option> 
              <option value="ME">Maine</option> 
              <option value="MD">Maryland</option> 
              <option value="MA">Massachusetts</option> 
              <option value="MI">Michigan</option> 
              <option value="MN">Minnesota</option> 
              <option value="MS">Mississippi</option> 
              <option value="MO">Missouri</option> 
              <option value="MT">Montana</option> 
              <option value="NE">Nebraska</option> 
              <option value="NV">Nevada</option> 
              <option value="NH">New Hampshire</option> 
              <option value="NJ">New Jersey</option> 
              <option value="NM">New Mexico</option> 
              <option value="NY">New York</option> 
              <option value="NC">North Carolina</option> 
              <option value="ND">North Dakota</option> 
              <option value="OH">Ohio</option> 
              <option value="OK">Oklahoma</option> 
              <option value="OR">Oregon</option> 
              <option value="PA">Pennsylvania</option> 
              <option value="RI">Rhode Island</option> 
              <option value="SC">South Carolina</option> 
              <option value="SD">South Dakota</option> 
              <option value="TN">Tennessee</option> 
              <option value="TX">Texas</option> 
              <option value="UT">Utah</option> 
              <option value="VT">Vermont</option> 
              <option value="VA">Virginia</option> 
              <option value="WA">Washington</option> 
              <option value="WV">West Virginia</option> 
              <option value="WI">Wisconsin</option> 
              <option value="WY">Wyoming</option>
            </select>
          </div>
        </div>

        <div class="span6">
          <div class="row-fluid">
            <label>Retailer: <span class="red">*</span></label>
            <?php echo form_dropdown('retailer', $register['retailers'], $register['selected_retailer']); ?>
          </div>
          <div class="row-fluid">
            <label>Location: <span class="red">*</span></label>
            <?php echo form_dropdown('store', $register['stores'], $register['selected_store']); ?>
          </div>
          <div class="row-fluid">
            <label for="referral_code">Referral Code:</label>
            <?php echo form_input($register['referral_code']); ?>
          </div>
          <div class="row-fluid">
            <label>Employment: <span class="red">*</span></label>
            <?php echo form_dropdown('employment', $register['employments'], $register['selected_employment']); ?>
          </div>
          <div class="row-fluid">
            <label>Job Title: <span class="red">*</span></label>
            <?php echo form_dropdown('job_title', $register['job_titles'], $register['selected_job_title']); ?>
          </div>
          <div class="row-fluid">
            <label for="hire_date">Hire Date:</label>
            <?php echo form_input($register['hire_date']); ?>
          </div>
          <div class="row-fluid">
            <label>I would like to receive:</label>
          </div>
          <div class="row-fluid">
            <?php echo form_checkbox('newsletter_monthly', '1', $register['selected_newsletter_monthly'] == 1, 'id="newsletter_monthly"'); ?>
            <label for="newsletter_monthly" style="display:inline-block">Monthly Newsletters</label>
          </div>
          <div class="row-fluid">
            <?php echo form_checkbox('newsletter_newlab', '1', $register['selected_newsletter_newlab'] == 1, 'id="newsletter_newlab"'); ?>
            <label for="newsletter_newlab" style="display:inline-block">Alerts about new Labs</label>
          </div>
          <div class="row-fluid">
            <?php echo form_checkbox('newsletter_newswag', '1', $register['selected_newsletter_newswag'] == 1, 'id="newsletter_newswag"'); ?>
            <label for="newsletter_newswag" style="display:inline-block">Alerts about new SWAG</label>
          </div>
          <div class="row-fluid">
            <?php echo form_checkbox('newsletter_survey', '1', $register['selected_newsletter_survey'] == 1, 'id="newsletter_survey"'); ?>
            <label for="newsletter_survey" style="display:inline-block">Survey Invitations</label>
          </div>
        </div> 
      </div>
      <div class="row-fluid">
        <div class="span3">
          <input type="submit" name="submit" class="btn btn-primary btn-large" value="Register" />
        </div>
        <div class="span9 mandatory-fields-wrappper">
          All fields marked with <span class="red">*</span> are mandatory.
        </div>
      </div>
    </div> <!-- /container-fluid -->
  </form>
</div>

