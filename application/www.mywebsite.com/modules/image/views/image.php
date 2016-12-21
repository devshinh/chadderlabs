<div class="image-holder">
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
        if ($link_blank == 1) {
            $blank = 'target="_blank"';
        }else {
            $blank = '';
        }
        printf('<a href="%s" title="%s" %s>', $link, $link_title, $blank);
    }
    echo '<img src="' . $image->full_path . '" alt="' . $image->description . '" />';
    if (!empty($link)) {
      echo '</a>';
    }
  }
?>
</div>
