<div>
  <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8">
  <div class="row">
   <?php echo form_error('name', '<div class="error">','</div>');?>
   <?php echo form_label(lang('hotcms_organization')." ".lang('hotcms__colon'), 'name');?>
   <?php echo form_input($form['name_input']); ?>
  </div>
  <div class="row">
   <?php echo form_error('website', '<div class="error">','</div>');?>
   <?php echo form_label(lang('hotcms_website').' '.lang('hotcms__colon'), 'website');?>
   <?php echo form_input($form['website_input']); ?>
  </div>      
  <div class="row">
  <?php
    echo form_error('country_code', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_country') . ' ' . lang('hotcms__colon'), 'country_code');
    echo form_dropdown('country_code', $form['country_code_options'], $selected_country);
  ?>
  </div>
  <div class="row">
    <?php echo form_label(lang('hotcms_status') . ' ' . lang('hotcms__colon')); ?>
    <?php echo form_radio($form['status_pending']); ?>
    <label for="status_pending" style="display:inline-block;margin-left:5px">Pending</label>
    <?php echo form_radio($form['status_confirmed']); ?>
    <label for="status_confirmed" style="display:inline-block;margin-left:5px">Confirmed</label>
    <?php echo form_radio($form['status_closed']); ?>
    <label for="status_closed" style="display:inline-block;margin-left:5px">Closed</label>
  </div>
    <?php if (isset($categories) && count($categories) > 0) { ?>
    <div class="row">
      <?php echo form_label(lang( 'hotcms_categories' )." ".lang( 'hotcms__colon' ), 'categories');?>
      <?php echo form_error('categories', '<div class="error">','</div>');?>
      <?php
      foreach ($categories as $category){
        echo '<div class="checkbox">';
        echo form_checkbox($category);
        echo form_label($category["id"], $category["id"]);
        echo '</div>';
      }
      ?>
    </div>
    <?php }
    if (isset($types) && count($types) > 0) { ?>
    <div class="row">
      <?php echo form_label(lang( 'hotcms_types' )." ".lang( 'hotcms__colon' ), 'types');?>
      <?php echo form_error('types', '<div class="error">','</div>');?>
      <?php
      foreach ($types as $type){
        echo '<div class="checkbox">';
        echo form_checkbox($type);
        echo form_label($type["id"], $type["id"]);
        echo '</div>';
      }
      ?>
    </div>
    <?php } ?>      
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_next' ) ?>" />
    <a href="/hotcms/<?php echo $module_url; ?>/index/<?php echo $index_page_num; ?>" class="red_button"><?php echo lang( 'hotcms_back' ); ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
