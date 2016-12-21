<?php
  if (!empty($title)) {
    echo '<h3>' . $title . "</h3>\n";
  }
  if ($environment == 'admin_panel') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/image/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/image/js/' . $js . "\"></script>\n";
    }
  }
  if (!empty($image)) {
    if (!empty($link)) {
      echo '<a href="' . $link . '" title="' . $link_title . '" target="_blank">';
    }
    echo '<img src="' . $image->full_path . '" alt="' . $image->description . '" />';
    if (!empty($link)) {
      echo '</a>';
    }
  }
?>
