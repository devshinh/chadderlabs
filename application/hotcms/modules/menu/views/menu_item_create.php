<div class="">
 <h2>Selected menu group: <?php echo $menu_group->menu_name?></h2>
</div>
<div>
 <form action="/hotcms/<?php echo $module_url?>/add_menu_item/<?php echo $menu_group->id?>" method="post" accept-charset="UTF-8">
  <div class="row">
   <?php echo form_error('title', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'title');?>
   <?php echo form_input($item_title_input); ?>
  </div>
  <div class="row">
   <?php echo form_error('pages_array', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_page' ).' '.lang( 'hotcms__colon' ), 'pages_array');?>
    <?php $id = 'id="pages_list"'?>
   <?php echo form_dropdown('pages_array', $pages_array, 1, $id); ?>
  </div>
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
