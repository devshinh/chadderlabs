<?php
  if (!empty($title)) {
    echo '<h3>' . $title . "</h3>\n";
  }
  if ($environment == 'admin_panel') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/randomizer/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/randomizer/js/' . $js . "\"></script>\n";
    }
  }
  if (!empty($image)) { ?>
<!--<p class="random-info"<?php echo ($image->full_path > '' ? ' style="background:url(\'' . $image->full_path . '\') no-repeat scroll left top transparent"' : ''); ?>><span><?php echo $image->description; ?></span></p>-->
<img src="<?php echo $image->full_path ?>" title="<?php echo $image->description ?>" alt="<?php echo $image->name ?>" width="1020" height="540"/>
<?php
  }
?>
