<?php
  if (!empty($title)) {
    echo '<h3>' . $title . "</h3>\n";
  }
  if ($environment == 'admin_panel') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/news/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/news/js/' . $js . "\"></script>\n";
    }
  }

?>
<div class="location_items">
<?php
  $i= 0;
  if (count($items) > 0) {
    $i++;
    $last = "";
    if ($i = count($items)) $last="last";
    echo '<ul>';
    foreach ($items as $item) {
      //$create_date = $item->create_timestamp > 0 ? date('Y-m-d H:i:s', $item->create_timestamp) : '&mdash;';
      echo '<li class="'.$last.'">';
      echo '<a href="location/'. $item->slug .'">' . $item->name . '</a>';
      
      echo "</li>\n";
    }
    echo "</ul>\n";
  }
  else {
    echo "<p>No active locations at this moment.</p>";
  }
?>
</div>
