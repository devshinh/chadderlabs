<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<form method="post" id="formFile" name="formFile" class="configform">
  <input name="asset_id" type="hidden" value="<?php if (!empty($asset_id)) { echo $asset_id; } ?>" />
  <input name="asset_title" type="hidden" value="" />
  <fieldset>
    <legend>Selected Audio</legend>
    <table width="700">
      <tr>
        <td><label for="title">Audio<span class="red">*</span>:</label></td>
        <td class="preview_area">
          <?php
            if (!empty($file)) {
              echo $file->thumb_html;
            }
          ?>
        </td>
        <td class="preview_title">
          <?php
            if (!empty($file)) {
              echo $file->name;
            }
          ?>
        </td>
      </tr>
    </table>
  </fieldset>
</form>
<table>
  <tr>
    <td width="25"></td>
    <td><label for="file_category_id">Select Category:</label></td>
    <td width="10"></td>
    <td><?php
      if (!empty($asset_category_id)) {
        echo form_dropdown("file_category_id", $asset_categories, $asset_category_id);
      }
      else {
        echo form_dropdown("file_category_id", $asset_categories);
      }
      echo form_hidden("asset_type", '4');
    ?></td>
    <td></td>
  </tr>
</table>
<div class="asset_files">
<?php
if (!empty($media_library_ui)) {
?>
  <fieldset>
    <legend>Audio</legend>
    <?php echo $media_library_ui; ?>
  </fieldset>
<?php
}
?>
</div>
<div class="asset_file_upload">
<?php
if (!empty($media_upload_ui)) {
?>
  <fieldset>
    <legend>Upload Audio to Group</legend>
    <?php echo $media_upload_ui; ?>
  </fieldset>
<?php
}
?>
</div>