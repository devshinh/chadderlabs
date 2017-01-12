<div>
  <form action="/hotcms/<?php echo $module_url; ?>/create" method="post" accept-charset="UTF-8">
    <?php
      //echo form_hidden('hdnMode', 'insert');
    ?>
    <div class="row">
      <?php echo form_error('category_id', '<div class="error">', '</div>');?>
      <?php echo form_label(lang( 'hotcms_category' ) . lang( 'hotcms__colon' ), 'category_id');?>
      <?php echo form_dropdown("category_id", $categories); ?>
    </div>
    <div class="row">
      <?php echo form_error('target_id', '<div class="error">', '</div>');?>
      <?php echo form_label(lang( 'hotcms_target' ) . lang( 'hotcms__colon' ), 'target_id');?>
      <?php echo form_dropdown("target_id", $targets); ?>
    </div>
    <div class="row">
      <?php echo form_error('title', '<div class="error">', '</div>');?>
      <?php echo form_label(lang( 'hotcms_title' ) . lang( 'hotcms__colon' ), 'title');?>
      <?php echo form_input($form['title']); ?>
    </div>
    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
      <a href="/hotcms/<?php echo $module_url; ?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    </div>
  </form>
</div>
