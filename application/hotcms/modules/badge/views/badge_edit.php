<div>
  <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $currentItem->id ?>" method="post">
    <div class="row">
    <?php
      echo form_error('name', '<div class="error">','</div>');
      echo form_label(lang( 'hotcms_name' ) . ' ' . lang( 'hotcms__colon' ), 'name');
      echo form_input($form['name_input']);
    ?>
    </div>
    <div class="row">
    <?php
      echo form_error('description', '<div class="error">','</div>');
      echo form_label(lang( 'hotcms_description' ) . ' ' . lang( 'hotcms__colon' ), 'description');
      echo form_textarea($form['description_input']);
    ?>
    </div>
    <div class="row">
    <?php
      echo form_error('feed_description', '<div class="error">','</div>');
      echo form_label('Feed decription ' . lang( 'hotcms__colon' ), 'feed_description');
      echo form_textarea($form['feed_description_input']);
    ?>
    </div>      
    <div class="row">
    <?php
      echo form_error('award_type', '<div class="error">', '</div>');
      echo form_label('Award type' . ' ' . lang('hotcms__colon'), 'award_type');
      echo form_dropdown('award_type', $form['award_type_options'], $currentItem->award_type);
    ?>
    </div>      
      
    <div class="row">
    <?php
      echo form_error('award_amount', '<div class="error">','</div>');
      echo form_label('Award amount ' . lang( 'hotcms__colon' ), 'award_amount');
      echo form_input($form['award_amount_input']);
    ?>
    </div>        
      
    <div id="badge_icon_div" style="padding: 10px;">
      <label>Badge Icon</label>
      <div id="news_image">
        <?php
        if (!empty($currentItem->items['icon'])) {
          echo $currentItem->items['icon']->full_html;
        }
        ?>
      </div>
      
      
      <a href="<?php echo $currentItem->icon_image_id; ?>" class="red_button icon_image_link" data-id="<?php echo $currentItem->id ?>">Choose</a>
      <input type="hidden" name="icon_image_id" id="icon_image_id" value="<?php echo $currentItem->icon_image_id; ?>" />
    </div>    
      
      
    <div id="badge_hover_image_div" style="padding: 10px;">
      <label>Hover Image</label>
      <div id="hover_image">
        <?php
        if (!empty($currentItem->items['hover'])) {
          echo $currentItem->items['hover']->full_html;
        }
        ?>
      </div>
      
      
      <a href="<?php echo $currentItem->big_image_id; ?>" class="red_button hover_image_link" data-id="<?php echo $currentItem->id ?>">Choose</a>
      <input type="hidden" name="big_image_id" id="big_image_id" value="<?php echo $currentItem->big_image_id; ?>" />
    </div>        
      
  <div class="row">
  <?php
    echo form_error('status', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_status') . ' ' . lang('hotcms__colon'), 'status');
    echo form_dropdown('status', $form['badge_status_options'], $currentItem->status);
  ?>
  </div>      

    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
      <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>

  </form>
    
  <div id="icon-image-form" title="Badge Icon Image">
  </div>     

  <div id="hover-image-form" title="Badge Hover Image">
  </div>         
</div>