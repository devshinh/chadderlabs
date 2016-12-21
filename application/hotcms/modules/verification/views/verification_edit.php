<div>
  <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $currentItem->id ?>" method="post">
    <div class="row">
        <label>User's ID:</label>
        <input id="user_id" name="user_id" disabled value="<?php echo $currentItem->user->user_id;?>" />
    </div>      
    <div class="row">
        <label>User's Email</label>
        <input disabled value="<?php echo $currentItem->user->email;?>" />
    </div>      
    <div class="row">
        <label>User's First Name</label>
        <input disabled value="<?php echo $currentItem->user->first_name;?>" />
    </div>         
    <div class="row">
        <label>User's Last Name</label>
        <input disabled value="<?php echo $currentItem->user->last_name;?>" />
    </div>   
    <div class="row">
        <label>User's Screen Name</label>
        <input disabled value="<?php echo $currentItem->user->screen_name;?>" />
    </div>    
    <div class="row">
        <label>User's Status</label>
        <?php 
        switch ($currentItem->user->verified) {
            case 1:
            $status = sprintf('Verified - %s', date($this->config->item('timestamp_format'), $currentItem->user->verified_date));
                break;
            case 0:
            $status = 'Unverified.';
                break;            
            default:
                break;
        }
        ?>
        <input disabled value="<?php echo $status;?>" />
    </div>     
    <div class="row">
        <label>User's Retailer Name</label>
        <input disabled value="<?php echo $currentItem->user->name;?>" />
    </div>         
      <div class="row">
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
  <?php
    echo form_error('status', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_status') . ' ' . lang('hotcms__colon'), 'status');
    echo form_dropdown('status', $form['verification_status_options'], $currentItem->status);
  ?>
  </div>          
    <div class="row">
        <label>Date uploaded</label>
        <input disabled value="<?php echo date($this->config->item('timestamp_format'), $currentItem->create_timestamp);?>" />
    </div>     
      <?php if($currentItem->asset_id > 0) {?>
    <div class="row">
        <label>Verification Image</label>
        <?php printf('<img src="/asset/upload/verifications/%s.%s" alt="verification image"/>', str_replace(' ','_',$currentItem->image->file_name),$currentItem->image->extension); ?>
    </div>      
      <?php } ?>
   

    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
      <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>

  </form>        
</div>
