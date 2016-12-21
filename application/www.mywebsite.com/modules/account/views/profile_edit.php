<style>
    /*users*/
    #user-contact input.text {
        width: 130px;
    }
    #user-contact .input {
        width: 280px;
        float: left;
    }
    #user-contact .headers 
    {
        font-size: 14px;
        font-weight: bold;
    }
    #user-contact h2 {
        font-size: 16px;
    }
    .row {
        clear: both;
        margin: 8px 0;
        overflow: auto;
    }
    .row.default label {
        width: auto;
        margin-right: 0;
        float: left;
    }
    .row.default input {
        float: left;
        margin-top: 5px;
        margin-left: 5px;
    }
    .row label {
        display: block;
        float: left;
        margin-right: 10px;
        width: 210px;
    }
</style>
<div id="edit-profile" class="hero-unit">
    <h1><?php echo $title; ?></h1>
    <?php if (!empty($message)) { ?><div class="message"><?php echo $message; ?></div><?php } ?>
    <?php if (!empty($error)) { ?><div class="error"><?php echo $error; ?></div><?php } ?>
    <div class="tabs-profile">
        <ul>
            <li><a href="#user-info" id="general"><span id=""></span><span>Account Info</span></a></li>
            <li><a href="#user-contact" id="contact"><span id=""></span><span>Shipping</span></a></li>
        </ul>
        <div class="mandatory-fields-wrappper">
            All fields marked with <span class="red">*</span> are mandatory.
        </div>

        <div id="user-info">
            <form action="profile-update" method="post" id="user_info_form">
                <div class="container-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <h2>About You</h2>
                        </div>                         
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
                            <label for="email">Email Address:</label>
                        </div>
                        <div class="row-fluid">
                            <?php echo $user->email; ?>
                        </div>
                        <div class="row-fluid" style="margin:5px 0;">
                            <a href="my-account/change-password" class="view-all-link"><span class="view-all-arrows">» </span>Change password</a>
                        </div>
<!--                        <div class="row-fluid">
                            <label>I would like to receive:</label>
                        </div>                        
                        <div id="newsletters">
                            <div class="row-fluid">
                                <?php echo form_checkbox('newsletter_monthly', '1', $user->newsletter_monthly == 1, 'id="newsletter_monthly"'); ?>
                                <label for="newsletter_monthly" style="display:inline-block">Monthly Newsletters</label>
                            </div>
                            <div class="row-fluid">
                                <?php echo form_checkbox('newsletter_newlab', '1', $user->newsletter_newlab == 1, 'id="newsletter_newlab"'); ?>
                                <label for="newsletter_newlab" style="display:inline-block">Alerts about new Labs</label>
                            </div>
                            <div class="row-fluid">
                                <?php echo form_checkbox('newsletter_newswag', '1', $user->newsletter_newswag == 1, 'id="newsletter_newswag"'); ?>
                                <label for="newsletter_newswag" style="display:inline-block">Alerts about new SWAG</label>
                            </div>
                            <div class="row-fluid">
                                <?php echo form_checkbox('newsletter_survey', '1', $user->newsletter_survey == 1, 'id="newsletter_survey"'); ?>
                                <label for="newsletter_survey" style="display:inline-block">Survey Invitations</label>
                            </div>
                        </div>                        -->
                    </div> <!-- /span6 -->
                    <div class="span6">
                        <div class="row-fluid">
                            <h2>Where you work</h2>
                        </div>                         
                        <div class="row-fluid">
                            <label>Country: <span class="red">*</span></label>
                        </div>                        
                        <div class="row-fluid">
                            <?php
                            $id = 'id="country_code"';
                            echo form_dropdown('country_code', $country_options, $selected_country, $id);
                            ?>                                         
                        </div>
                        
                        <div class="row-fluid">
                            <label>Province: <span class="red">*</span></label>
                        </div>                    
                        <div class="row-fluid">
                            <?php echo form_dropdown('province', $provinces, $user->province_code); ?>
                        </div>                        
                        <div class="row-fluid">
                            <label>Retailer: <span class="red">*</span></label>
                        </div>
                        <div class="row-fluid">
                            <?php echo form_dropdown('retailer', $retailers, $user->retailer_id); ?>
                        </div>
                        <div class="row-fluid">
                            <label>Location: <span class="red">*</span></label>
                        </div>
                        <div class="row-fluid">
                            <?php echo form_dropdown('store', $stores, $user->store_id); ?>
                        </div>
                        <div class="row-fluid">
                            <label>Employment: <span class="red">*</span></label>
                        </div>
                        <div class="row-fluid">
                            <?php echo form_dropdown('employment', $employments, $user->employment); ?>
                        </div>
                        <div class="row-fluid">
                            <label>Job Title: <span class="red">*</span></label>
                        </div>
                        <div class="row-fluid">
                            <?php echo form_dropdown('job_title', $job_titles, $user->job_title); ?>
                        </div>
                        <div class="row-fluid">
                            <label for="hire_date">Hire Date:</label>
                        </div>
                        <div class="row-fluid">
                            <?php echo form_input($hire_date); ?>
                        </div>

                    </div> <!-- /span6 -->
                    <div class="row-fluid">
                        <div class="span12">
                            <input type="hidden" name="profile_edit" value="true"/>
                            <?php echo form_hidden('user_verified', $user->verified);?>
                            <div class="pull-left" style="margin-right: 20px;">
                                <a href ="/profile"  class="view-all-link"><span class="view-all-arrows">» </span>Cancel</a>
                            </div>
                            <div class="pull-left">
                                <input type="submit" name="submit" class="btn btn-primary" value="Save info" />
                            </div>
                        </div>
                    </div>
                </div> <!-- /container-fluid -->
            </form>
        </div>

        <div id="user-contact">
            <div class="add-new-contact-wrapper">
                <a id="add_new_contact" class="btn btn-primary">Add new</a>
            </div>
            <form id="add_new_contact_form" style="display:none;" action="/profile/add_new_contact/<?php echo $user->user_id ?>" method="post">
                <?php echo form_label(lang('hotcms_contact_type') . ' ' . lang('hotcms__colon').' <span class="red">*</span>', 'contact_name'); ?>
                <?php echo form_input('contact_name', $this->input->post('contact_name'), 100, 20, 'text'); ?>
                <div class="pull-right">
                  <input type="submit" class="btn btn-primary" value="<?php echo lang('hotcms_save_changes') ?>" />
                  <a id="hide_contact_name_form"  class="view-all-link"><span class="view-all-arrows">» </span><?php echo lang('hotcms_back') ?></a>
                </div>
            </form>
            <?php foreach ($contacts as $contact) { ?>
                <form action="my-account/update-contact/<?php echo $contact->id ?>" method="post"  id="table_form_<?php echo$contact->id ?>">
                    <h3><?php printf('%s', ucfirst($contact->name)) ?></h3>
                    <div class="row">
                        <div class="input"> 
                            <?php echo form_label('Address line 1: <span class="red">*</span>', 'address_1'); ?>
                            <?php echo form_input('address_1', $contact->address_1, 'class="required"'); ?> 
                        </div>
                        <div class="input"> 
                            <?php echo form_label('Phone: <span class="red">*</span>', 'phone'); ?>
                            <?php echo form_input('phone', $contact->phone, 'class="required phonenumber"'); ?> 
                        </div>
                    </div> 
                    <div class="row">
                        <div class="input">
                            <?php echo form_label('Address line 2:', 'address_2'); ?>
                            <?php echo form_input('address_2', $contact->address_2); ?> 
                        </div>
                        <div class="input">
                            <?php echo form_label('Cell Phone:', 'cell'); ?>
                            <?php echo form_input('cell', $contact->cell, 'class="phonenumber"'); ?> 
                        </div>         
                    </div>  
                    <div class="row">
                        <div class="input">
                            <?php echo form_label('City: <span class="red">*</span>', 'city'); ?>
                            <?php echo form_input('city', $contact->city, 'class="required"'); ?>      
                        </div>
                        <div class="input">
                            <?php echo form_label('Fax :', 'fax'); ?>
                            <?php echo form_input('fax', $contact->fax, 'class="phonenumber"'); ?>      
                        </div>       
                    </div>     
                    <div class="row">
                        <div class="input">
                            <?php echo form_label('Province: <span class="red">*</span>', 'province'); ?>
                            <?php echo form_input('province', $contact->province, 'class="required"'); ?>      
                        </div>
                        <div class="input"> 
                            <?php echo form_label('Email: <span class="red">*</span>', 'email'); ?>
                            <?php echo form_input('email', $contact->email,'class="required email"'); ?> 
                        </div>                        
                    </div>      
                    <div class="row">
                        <div class="input">
                            <?php echo form_label('Postal code: <span class="red">*</span>', 'postal_code'); ?>
                            <?php echo form_input('postal_code', $contact->postal_code, 'class="required"'); ?>          
                        </div>
                        <div class="input">
                            <?php echo form_label('Website :', 'website'); ?>
                            <?php echo form_input('website', $contact->website); ?>      
                        </div>                            
                    </div> 
                    <div class="row">
                        <div class="input">
    
                        </div>
                        <div class="input">
                            <?php echo form_label('Twitter :', 'twitter'); ?>
                            <?php echo form_input('twitter', $contact->twitter); ?>   
                        </div>                            
                    </div> 
                    <div class="row default">
                            <?php echo form_label('Make this my default mailing address :', 'default_address'); ?>
                            <?php echo form_checkbox('default_address', 1,($contact->default?TRUE:FALSE)); ?>   
                    </div>
                    <div class="row">
                        <div class="pull-left">
                            <a href="/profile" class="view-all-link"><span class="view-all-arrows">&raquo; </span>Back</a>
                        </div>
                        <div class="pull-right">
                            <a href="/profile/delete_contact/<?php echo $contact->id ?>" class="btn btn-primary">Delete contact</a>
                            <input type="submit" name="submit" class="btn btn-primary" value="Save Contact" />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            
            <script type="text/javascript">
            
            jQuery('#table_form_<?php echo $contact->id ?>').validate();
           
            </script>
            <?php } ?>
        </div>
    </div>
</div>
