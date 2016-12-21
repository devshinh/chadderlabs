<?php
  if (!empty($title)) {
    echo '<h3>' . $title . "</h3>\n";
  }
  if ($presence == 'backend') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/image/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/image/js/' . $js . "\"></script>\n";
    }
  }
  if (!empty($image)) {
    echo '<img src="' . $image->full_path . '" alt="' . $image->description . '" />';
  }
?>
