<div>
  <form action="/hotcms/<?php echo $module_url; ?>/create" method="post" accept-charset="UTF-8">
    <?php
      echo form_hidden('hdnMode', 'insert');
    ?>
  <div class="row">
    <?php echo form_error('title', '<div class="error">','</div>');?>
    <?php echo form_label(lang( 'hotcms_title' ).' '.lang( 'hotcms__colon' ), 'title');?>
    <?php echo form_input($form['title_input']); ?>
  </div>
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url; ?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
  </div>
  </form>
</div>
