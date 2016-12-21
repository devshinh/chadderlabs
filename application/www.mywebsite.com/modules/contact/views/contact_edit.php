<div id="editContact_<?php echo $form_contact['contact_id'] ?>">
   <div class="message" style="display: none">      
                  <div class="message_close"><a onClick="closeMessage()">[close]</a></div>
                  <div class="message"></div>
     <div class="<?php echo !empty( $message ) ? ' hide' : '' ?>"><!----></div>
   </div>   
     <form action="/hotcms/contact/edit_contact/<?php echo $form_contact['contact_id'] ?>/<?php echo $connection_id ?>/<?php echo $module_back_url?>" method="post" id="editFormContact_<?php echo $form_contact['contact_id'] ?>">     
      <div class="contact_id" style="display:none"><?php echo $form_contact['contact_id'] ?></div>
      <div class="connection_id" style="display:none"><?php echo $connection_id ?></div>
      <div class="module_back_url" style="display:none"><?php echo $module_back_url ?></div>
      <h2><?php echo ucfirst($form_contact['contact_name']).' Contact';?></h2>
      <div class="row headers">
       <div class="input">Mailing Address</div> 
       <div class="input">Phone Numbers</div>
       <div class="input">Electronic Contact</div>
      </div>
      <div class="row">
       <div class="input"> 
        <?php echo form_label(lang( 'hotcms_address' ).' 1'.lang( 'hotcms__colon' ), 'address_1');?>
        <?php echo form_input($form_contact['address_1']); ?> 
       </div>
       <div class="input"> 
        <?php echo form_label(lang( 'hotcms_phone' ).' '.lang( 'hotcms__colon' ), 'phone');?>
        <?php echo form_input($form_contact['phone']); ?> 
       </div>
       <div class="input"> 
        <?php echo form_error('email', '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_email_address' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'email');?>
        <?php echo form_input($form_contact['email']); ?>  
       </div>
      </div> 
      <div class="row">
        <div class="input">
         <?php echo form_label(lang( 'hotcms_address' ).' 2'.lang( 'hotcms__colon' ), 'address_2');?>
         <?php echo form_input($form_contact['address_2']); ?> 
        </div>
        <div class="input">
          <?php echo form_label(lang( 'hotcms_phone_cell' ).' '.lang( 'hotcms__colon' ), 'cell');?>
          <?php echo form_input($form_contact['cell']); ?> 
        </div>  
        <div class="input">
          <?php echo form_label(lang( 'hotcms_twitter' ).' '.lang( 'hotcms__colon' ), 'twitter');?>
          <?php echo form_input($form_contact['twitter']); ?> 
        </div>         
      </div>  
      <div class="row">
       <div class="input">
        <?php echo form_label(lang( 'hotcms_city' ).' '.lang( 'hotcms__colon' ), 'city');?>
        <?php echo form_input($form_contact['city']); ?> 
       </div>
       <div class="input">
         <?php echo form_label(lang( 'hotcms_fax' ).' '.lang( 'hotcms__colon' ), 'fax');?>
         <?php echo form_input($form_contact['fax']); ?> 
       </div>  
       <div class="input">
        <?php echo form_label(lang( 'hotcms_website' ).' '.lang( 'hotcms__colon' ), 'website');?>
        <?php echo form_input($form_contact['website']); ?> 
       </div>         
      </div>     
      <div class="row">
        <?php echo form_label(lang( 'hotcms_province' ).' '.lang( 'hotcms__colon' ), 'pronvice');?>
        <?php echo form_input($form_contact['province']); ?> 
      </div>      
      <div class="row">
        <?php echo form_label(lang( 'hotcms_postal_code' ).' '.lang( 'hotcms__colon' ), 'postal_code');?>
        <?php echo form_input($form_contact['postal_code']); ?> 
      </div>       
                                      
       <div class="submit">
        <input type="submit" class="red_button_smaller ajax_submit_contact" value="<?php echo lang( 'hotcms_save_changes' ) ?>" id="<?php echo $form_contact['contact_id'] ?>" />
        <a href="/hotcms/<?php echo $module_url?>/" class="red_button_smaller"><?php echo lang( 'hotcms_back' ) ?></a>
        <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete_contact/<?php echo $form_contact['contact_id'] ?>/<?php echo $connection_id ?>" class="red_button_smaller"><?php echo lang( 'hotcms_delete' ) ?></a>
      </div>
      
     </form>

</div>