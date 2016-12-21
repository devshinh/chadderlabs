<div>
  <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8">
     <div class="row">  
      <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
      <?php echo form_input($form['name_input']); ?>
     </div>
     <div class="row">
      <?php echo form_label(lang( 'hotcms_url' ).' '.lang( 'hotcms__colon' ), 'url');?>
      <?php echo form_input($form['url_input']); ?>         
     </div>     
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url; ?>/index/<?php echo $index_page_num; ?>" class="red_button"><?php echo lang( 'hotcms_back' ); ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
