<?php
  if (!empty($title)) {
    echo '<h3>' . $title . "</h3>\n";
  }
  if ($environment == 'admin_panel') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/quiz/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/quiz/js/' . $js . "\"></script>\n";
    }
  }
  if (isset($item->publish_timestamp) && $item->publish_timestamp > 0) {
    $date = date('Y-m-d H:i:s', $item->publish_timestamp);
  }
  elseif (isset($item->scheduled_publish_timestamp) && $item->scheduled_publish_timestamp > 0) {
    $date = date('Y-m-d H:i:s', $item->scheduled_publish_timestamp);
  }
  else {
    $date = '(unknown publish date)';
  }
?>
<div class="item-detail" id="item-<?php print($item->id); ?>">
  <h2><?php echo $item->name; ?></h2>
  <p><?php echo $item->content; ?></p>
</div>
