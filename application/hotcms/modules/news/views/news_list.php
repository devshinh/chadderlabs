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
<div class="news_items">
<?php
  if (count($items) > 0) {
    echo '<ul>';
    foreach ($items as $item) {
      $create_date = $item->create_timestamp > 0 ? date('Y-m-d H:i:s', $item->create_timestamp) : '&mdash;';
      echo '<li>';
      echo '<a href="news/' . $item->slug . '"><b>' . $item->title . "</b></a> <i>" . $create_date . '</i><br />';
      echo $item->snippet;
      echo '<br /><a href="news/' . $item->slug . '">Read more...</a>';
      echo "</li>\n";
    }
    echo "</ul>\n";
  }
  else {
    echo "<p>No news at this moment.</p>";
  }
?>
</div>
