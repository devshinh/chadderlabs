<div>
  <div class="row">
      <h4>Confirm Winner</h4>
  </div>
  <form action="/hotcms/<?php echo $module_url?>/edit_winner/<?php echo $currentItem->id ?>" method="post">
    <div class="row">
       Draw's name: <?php echo $currentItem->name; ?>
    </div>
    <div class="row">        
       User ID:<?php echo $currentItem->user_id; ?>
    </div>
    <div class="row">        
       User's Screen Name: <?php echo $currentItem->user_info->screen_name; ?>
    </div>

    <div class="row">
    <?php
      echo form_error('feed_description', '<div class="error">','</div>');
      echo form_label('Feed decription ' . lang( 'hotcms__colon' ), 'feed_description');
      $tooltip = 'title="Text will display in activity feed. Format: {screen_name} {text}"';
      echo form_textarea($form['feed_description_input'],'feed_description',$tooltip);
    ?>
    </div>   
      <div class="row"><small>Text will display in activity feed. Format: {screen_name} {text}.</small></div>
     <div class="row">
      <?php echo form_label('Verified '.lang( 'hotcms__colon' ), 'hidden_site');?>
      <?php echo form_checkbox($form['verified_input']); ?> 
     </div>        
      
    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
      <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>

   <?php echo form_close()?>
        
</div>