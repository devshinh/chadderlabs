<div>
   <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $currentItem->id ?>" method="post">
    <div class="row">       
     <?php echo form_error('name', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_name' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'name');?>
     <?php echo form_input($form['name_input']); ?>
    </div>
    <div class="row">      
     <?php echo form_error('description', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'description_code');?>
     <?php echo form_textarea($form['description_input']); ?>                     
    </div>   
    <!--
    <div class="row" style="display: none">
     <?php //echo form_error('asset_type', '<div class="error">','</div>');?>
     <?php //echo form_label(lang( 'hotcms_asset_type' ).' '.lang( 'hotcms__colon' ), 'asset_type');?>
     <?php //echo form_dropdown('asset_type', $form['asset_type'], $currentItem->type); ?>                     
    </div>
    -->
    <?php if ($currentItem->type == 1) {?>
    <div class="row">          
     <label>Image :</label>
     <img alt="image" src=<?php printf('"http://%s%s/%s%s.%s"', $this->session->userdata( 'siteURL' ), $currentThumb->folder, $currentItem->file_name,'_thumb', $currentItem->extension)?> />
    </div>      
    <?php } else if ($currentItem->type == 3) {?>
    <div class="row">          
     <label>Document :</label>
     <a href=<?php printf('"http://%s/asset/upload/document/%s.%s"', $this->session->userdata( 'siteURL' ), $currentItem->file_name, $currentItem->extension)?> target="_blank"> link</a>
    </div>        
    <?php } ?>
    <div class="submit">
      <input type="submit" class="button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="/hotcms/<?php echo $module_url?>/" class="button"><?php echo lang( 'hotcms_back' ) ?></a>
      
      <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $currentItem->id ?>" class="button"><?php echo lang( 'hotcms_delete' ) ?></a>
      
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>
  </form>
</div>