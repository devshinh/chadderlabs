<div class="">
 <h2>Selected menu: <?php echo $menu_group_item->menu_name?></h2>
</div>
<div>
  <form action="<?php printf('/hotcms/%s/edit_menu_item/%d/%d', $module_url, $current_item->id, $group_id); ?>" method="post">
    <div class="input">
     <div class="row">
      <?php echo form_error('name', '<div class="error">','</div>');?>
      <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
      <?php echo form_input($form['title_input']); ?>
     </div>
     <div class="row">
      <?php echo form_error('pages_array', '<div class="error">','</div>');?>
      <?php echo form_label(lang( 'hotcms_page' ).' '.lang( 'hotcms__colon' ), 'pages_array');?>
      <?php $id = 'id="pages_list"'?>
      <?php echo form_dropdown('pages_array', $form['pages_array'], $current_item->page_id, $id); ?>
     </div>
     <div class="row">
      <?php echo form_error('menu_enabled', '<div class="error">','</div>');?>
      <?php echo form_label(lang( 'hotcms_active' ).' '.lang( 'hotcms__colon' ), 'menu_enabled');?>
      <?php echo form_checkbox('menu_enabled',$form['menu_enabled'],$current_item->hidden == 0); ?>
     </div>
    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="<?php printf('/hotcms/%s/edit/%d/', $module_url, $group_id); ?>" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>

      <?php if($menu_deletable) { ?>
      	<a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete_menu_item/%d/%d', $module_url, $current_item->id, $group_id); ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
      <?php } else { ?>
      	<a onClick="alert('A menu with children cannot be deleted'); return false;" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
      <?php } ?>
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>
  </form>
  </div>
</div>
