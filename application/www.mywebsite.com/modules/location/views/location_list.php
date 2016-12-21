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
  $i = 0;
  $a_uri = explode('/', $_SERVER['REQUEST_URI']);
  if (count($items) > 0) {
    echo '<ul>';
    foreach ($items as $item) {
      $i++;
      $last = "";

      if ($i == count($items))
        $last = "last";
      //$create_date = $item->create_timestamp > 0 ? date('Y-m-d H:i:s', $item->create_timestamp) : '&mdash;';
      $current = "";
      if (isset($a_uri[2]) && $a_uri[2] == $item->slug) {
        $current = "current";
      }
      echo '<li class="' . $last . ' ' . $current . '">';
      echo '<a href="location/' . $item->slug . '">' . $item->name . '</a>';
      printf('<div class="location-details hidden">%s<br/>%s<br/>%s %s<br/><br/><span class="darkBlue">P</span><span class="number"> %s</span><br/><span class="darkBlue">F</span> <span class="number"> %s</span><br/><a href="/location/%s">More info</a></div>',
              $item->address_1,$item->city,$item->province,$item->postal_code,$item->main_phone,$item->main_fax,$item->slug);
      echo "</li>\n";
    }
    echo "</ul>\n";
  } else {
    echo "<p>No active locations at this moment.</p>";
  }
  ?>
</div>
