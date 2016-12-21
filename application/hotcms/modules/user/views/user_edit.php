<div>
  <div class="tabs">
    <ul>
      <li><a href="#user-info" id="general"><span id="g"></span><span>Info</span></a></li>
      <li><a href="#user-contact" id="settings"><span id="s"></span><span>Contact</span></a></li>
      <li><a href="#user-activity" id="activity_feed"><span id=""></span><span>Activity</span></a></li>
    </ul>
    <div id="user-info">
      <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $user_id ?>" method="post" enctype="multipart/form-data">
      <div class="input">
        <?php if (isset($message) && is_array($message)) { ?>
          <div class="message <?php echo $message['type']; ?>">
            <div class="message_close"><a onClick="closeMessage()">[close]</a></div>
        <?php echo $message['value']; ?>
          </div>
          <div class="<?php echo !empty($message) ? ' hide' : '' ?>"><!----></div>
        <?php } ?>
        <div class="row">
          <?php
          echo form_error('salutation', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_salutation' ).' '.lang( 'hotcms__colon' ), 'salutation');?>
          <?php echo form_dropdown('salutation',$form['salutation'], $current_item->salutation); ?>
        </div>
        <div class="row">
          <?php echo form_error('first_name', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_name_first' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'first_name');?>
          <?php echo form_input($form['first_name_input']); ?>
        </div>
        <?php /* div class="row">
          <?php echo form_label(lang( 'hotcms_name_middle' ).' '.lang( 'hotcms__colon' ), 'middle_name');?>
          <?php echo form_input($form['middle_name_input']); ?>
        </div */ ?>
        <div class="row">
          <?php echo form_label(lang( 'hotcms_name_last' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'last_name');?>
          <?php echo form_input($form['last_name_input']); ?>
        </div>
        <?php /* div class="row">
          <?php echo form_label(lang( 'hotcms_name_user' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'username');?>
          <?php echo form_input($form['username_input']); ?>
        </div */ ?>
        <div class="row">
          <?php echo form_label(lang( 'hotcms_screen_name' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'screen_name');?>
          <?php echo form_input($form['screen_name_input']); ?>
        </div>          
        <?php /* div class="row">
          <label for="old_password">Old password :</label>
          <input autocomplete="off" type="password" class="text" size="20" maxlength="50" id="old_password" value="" name="old_password">
        </div */ ?>
        <div class="row">
          <?php echo form_label(lang( 'hotcms_password_new' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'password');?>
          <?php echo form_password($form['password_input']); ?>
        </div>
        <div class="row">
          <?php echo form_label(lang( 'hotcms_password_retype' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'password_retype');?>
          <?php echo form_password($form['password_retype_input']); ?>
        </div>
        <div class="row">
          <?php echo form_label(lang( 'hotcms_email_address' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'email');?>
          <?php echo form_input($form['email_input']); ?>
        </div>
        <?php /* div class="row">
          <?php echo form_label(lang( 'hotcms_user_position' ).' '.lang( 'hotcms__colon' ), 'position');?>
          <?php echo form_input($form['position_input']); ?>
        </div */ ?>
        <div class="row">
          <label>Country:</label>
          <?php 
          $id = 'id="country_code"';
          echo form_dropdown('country_code', $countries, $selected_country,$id); ?>
        </div>          
        <div class="row">
          <?php echo form_label(lang( 'hotcms_province' ).''.lang( 'hotcms__colon' ), 'province');?>
          <?php echo form_dropdown('province_code', $provinces, $selected_province); ?>
        </div>          
        <div class="row">
          <label><?=lang("hotcms_organization")?>:</label>
          <?php echo form_dropdown('retailer_id', $retailers, $selected_retailer); ?>
        </div>
        <div class="row">
          <label>Location:</label>
          <?php echo form_dropdown('store_id', $stores, $selected_store); ?>
        </div>
        <div class="row">
          <label for="referral_code">Referral Code:</label>
          <?php echo form_input($form['referral_code']); ?>
        </div>
        <div class="row">
          <label>Employment:</label>
          <?php echo form_dropdown('employment', $employments, $selected_employment); ?>
        </div>
        <div class="row">
          <label>Job Title:</label>
          <?php echo form_dropdown('job_title', $job_titles, $selected_job_title); ?>
        </div>
        <div class="row">
          <label for="hire_date">Hire Date:</label>
          <?php echo form_input($form['hire_date']); ?>
        </div>
        <div class="row">
          <label>Newsletters:</label>
        </div>
        <div class="row">
          <?php echo form_checkbox('newsletter_monthly', '1', $selected_newsletter_monthly == 1, 'id="newsletter_monthly"'); ?>
          <label for="newsletter_monthly" style="display:inline-block; margin-left: 5px;">Monthly Newsletters</label>
        </div>
        <div class="row">
          <?php echo form_checkbox('newsletter_newlab', '1', $selected_newsletter_newlab == 1, 'id="newsletter_newlab"'); ?>
          <label for="newsletter_newlab" style="display:inline-block; margin-left: 5px;">Alerts about new Labs</label>
        </div>
        <div class="row">
          <?php echo form_checkbox('newsletter_newswag', '1', $selected_newsletter_newswag == 1, 'id="newsletter_newswag"'); ?>
          <label for="newsletter_newswag" style="display:inline-block; margin-left: 5px;">Alerts about new SWAG</label>
        </div>
        <div class="row">
          <?php echo form_checkbox('newsletter_survey', '1', $selected_newsletter_survey == 1, 'id="newsletter_survey"'); ?>
          <label for="newsletter_survey" style="display:inline-block; margin-left: 5px;">Survey Invitations</label>
        </div>

        <?php if (isset($roles) && count($roles) > 0) { ?>
        <div class="row">
          <?php echo form_label(lang( 'hotcms_role' ).' '.lang( 'hotcms__colon' ), 'roles');?>
          <?php echo form_error('roles', '<div class="error">','</div>');?>
          <?php
          foreach ($roles as $role){
            echo '<div class="checkbox">';
            echo form_checkbox($role);
            echo form_label($role["id"], $role["id"]);
            echo '</div>';
          }
          ?>
        </div>
        <?php } ?>
        <div class="row">
          <?php 
          if (!empty($avatar_picture)){
            printf('<img src="%s%s/%s.%s" alt="%s" />', $this->config->item( 'base_url_front' ) ,'/asset/upload/thumbnail_50x50/',$avatar_picture->file_name.'_thumb', $avatar_picture->extension, $avatar_picture->file_name ); 
          }?>
        </div>
        
        <div class="row">         
          <?php echo form_error('asset_file', '<div class="error">', '</div>'); ?>
          <?php echo form_label(lang('hotcms_user_avatar') . ' ' . lang('hotcms__colon'), 'asset_file'); ?>
          <?php echo form_upload($form['asset_file_input']); ?>                     
        </div>        

        <div class="row">
          <?php echo form_label(lang( 'hotcms_active' ) . ' ' . lang( 'hotcms__colon' ), 'active');?>
          <?php echo form_checkbox($form['active_input']); ?>
        </div>
        <div class="row">
          <?php echo form_label(lang( 'hotcms_verified' ) . ' ' . lang( 'hotcms__colon' ), 'verified');?>
          <?php echo form_checkbox($form['verified_input']); ?>
          <?php if ($current_item->verified == 1 && $current_item->verified_date > 0) { echo '<i>&nbsp; (on ' . date('Y-m-d H:i', $current_item->verified_date) . ')</i>'; } ?>
          <?php  if(!empty($ref_user)){?>
            <div class="row">
                <?php printf('User was referred by user %s.',$ref_user->email);?>
            </div>
          <?php }?>
          <?php  if(!empty($ref_award)){?>
            <div class="row">
                <?php printf('User %s was awared.',$ref_user->email);?>
            </div>
          <?php }?>            
        </div>
        <div class="row">
          <?php echo form_label('Created on' . lang( 'hotcms__colon' ));?>
          <?php if ($current_item->created_on > 0) { echo ' &nbsp; ' . date('Y-m-d H:i', $current_item->created_on); } ?>
        </div>
        <div class="row">
          <?php echo form_label('Last Login' . lang( 'hotcms__colon' ));?>
          <?php if ($current_item->last_login > 0) { echo ' &nbsp; ' . date('Y-m-d H:i', $current_item->last_login); } ?>
        </div>
      </div>
      <div class="submit">
        <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
        <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
        <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $current_item->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
        <input type="hidden" id="hdnPassword" value="<?php echo set_value( 'hdnPassword', $this->input->post( 'hdnPassword' ) ) ?>" />
      </div>
      </form>
    </div> <!-- #user_info -->
    <div id="user-contact">
      <a id="add_new_contact" class="red_button">Add new contact</a>
      <form id="add_new_contact_form" style="display:none;" action="/add_new_contact/<?php echo $user_id ?>" method="post">
        <?php echo form_label(lang( 'hotcms_contact_type' ).' '.lang( 'hotcms__colon' ), 'contact_name');?>
        <?php echo form_input('contact_name', $this->input->post( 'contact_name' ),100,20,'text'); ?>
        <input type="submit" class="red_button_smaller" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
        <a id="hide_contact_name_form" class="red_button_smaller"><?php echo lang( 'hotcms_back' ) ?></a>
      </form>
      <?php
      if ($form_contacts){
        foreach($form_contacts as $form_contact){
          echo $form_contact;
        }
      }
      ?>
    </div> <!-- #user_contact -->
    <div id="user-activity">
        <b>Users stats</b>
        <div class="row">
            Current points: <?php print($user_points['current']);?>
        </div>
        <div class="row">
            Lifetime points: <?php print($user_points['lifetime']);?>
        </div>        
        <div class="row">
            Number of quizzes: <?php print($quiz_number);?>
        </div>        
        
        <?php print($activity_feed['content']);?>
    </div> <!-- #user-activity -->    
    <div class="clear"></div>
  </div> <!-- .tabs -->
</div>
