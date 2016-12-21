<div>
  <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8">
  <div class="row">
    <?php echo form_error('name', '<div class="error">','</div>');?>
    <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
    <?php echo form_input($name_input); ?>
  </div>
  <?php /*
  <div class="row">
    <?php echo form_error('url', '<div class="error">','</div>');?>
    <?php echo form_label(lang( 'hotcms_url' ).' '.lang( 'hotcms__colon' ), 'url');?>
    <?php echo form_input($url_input); ?>
  </div>
  <div class="row">
    <?php echo form_label(lang( 'hotcms_active' ).' '.lang( 'hotcms__colon' ), 'active');?>
    <?php echo form_checkbox($active_input); ?>
  </div>
  */?>
  <div class="link">
    <p>Page Type:</p>
    <?php echo form_error('linktype', '<div class="error">','</div>');?>
    <?php foreach($page_type_options as $option) { ?>
      <input id="linktype_<?php echo $option['key'] ?>" name="linktype" type="radio" value="<?php echo $option['key'] ?>" <?php echo $linktype == $option['key'] ? 'checked="checked"' : ''; ?> />
      <?php echo form_label($option['value'], 'linktype_' . $option['key']);?><br />
    <?php } ?>
  </div>
  <div class="row link_external <?php echo $linktype!='external' ? 'panel_hidden' : 'panel_visible'; ?>">
      <?php echo form_error('link_url','<div class="error">','</div>'); ?>
      <?php echo form_label('Redirect URL '.' '.lang( 'hotcms__colon' ), 'link_url');?>
      <?php echo form_input($link_external); ?>
  </div>
  <div class="content_page <?php echo $linktype!='normal' ? 'panel_hidden' : 'panel_visible'; ?>">
    <?php /*
    <div class="row">
      <?php echo form_error('title', '<div class="error">','</div>');?>
      <?php echo form_label('Meta '.lang( 'hotcms_title' ).' '.lang( 'hotcms__colon' ), 'title');?>
      <?php echo form_input($title_input); ?>
    </div>
    <div class="row">
      <?php echo form_error('heading', '<div class="error">','</div>');?>
      <?php echo form_label('Heading '.lang( 'hotcms__colon' ), 'heading');?>
      <?php echo form_input($heading_input); ?>
    </div>
    */ ?>
    <!--
    <div class="row">
      <?php //echo form_error('meta_description', '<div class="error">','</div>');?>
      <?php //echo form_label(lang( 'hotcms_meta_description' ).' '.lang( 'hotcms__colon' ), 'meta_description');?>
      <?php //echo form_input($meta_description_input); ?>
    </div>
    <div class="row">
      <?php //echo form_error('meta_keyword', '<div class="error">','</div>');?>
      <?php //echo form_label(lang( 'hotcms_meta_keywords' ).' '.lang( 'hotcms__colon' ), 'meta_keyword');?>
      <?php //echo form_input($meta_keyword_input); ?>
    </div>
    <div class="row">
      <?php //echo form_error('style_sheet', '<div class="error">','</div>');?>
      <?php //echo form_label(lang( 'hotcms_stylesheet' ).lang( 'hotcms__colon' ), 'style_sheet');?>
      <?php //echo form_input($style_sheet_input); ?>
    </div>
    <div class="row">
      <?php //echo form_error('javascript', '<div class="error">','</div>');?>
      <?php //echo form_label(lang( 'hotcms_javascript' ).lang( 'hotcms__colon' ), 'javascript');?>
      <?php //echo form_input($javascript_input); ?>
    </div>
    -->

  </div>
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
