<div>
  <form action="/hotcms/<?php echo $module_url?>/sitemap_edit/<?php echo $menu_item_record->id; ?>" method="post" accept-charset="UTF-8">
    <div class="row">       
      <?php echo form_error('name', '<div class="error">','</div>');?>
      <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
      <?php echo form_input($name_input); ?>
    </div>
    <div class="row">       
      <?php echo form_error('url', '<div class="error">','</div>');?>
      <?php echo form_label(lang( 'hotcms_url' ).' '.lang( 'hotcms__colon' ), 'url');?>
      <?php echo form_input($url_input); ?>
    </div>

    <div class="row">         
      <?php echo form_error('menu_enabled', '<div class="error">','</div>');?>
      <?php echo form_label(lang( 'hotcms_active' ).' '.lang( 'hotcms__colon' ), 'menu_enabled');?>
      <?php echo form_checkbox('menu_enabled','menu_enabled',$menu_enabled); ?>                     
    </div>                  
    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="<?php printf('/hotcms/%s', $module_url); ?>" class="red_button"><?php echo lang( 'hotcms_cancel' ) ?></a>
      
      <?php if($menu_deletable) { ?>
      	<a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/sitemap_delete/%d', $module_url, $menu_item_record->id); ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
      <?php } else { ?>
      	<a onClick="alert('A menu with children cannot be deleted'); return false;" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>      	
      <?php } ?>
      <?php echo form_hidden('hdnMode', 'edit') ?>
      <?php echo form_hidden('linktype',$linktype); ?>   
    </div>
  </form>
</div>  
