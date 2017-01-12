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

?>
<div class="quiz_items">
<?php
  if (count($items) > 0) {
    echo '<ul>';
    foreach ($items as $item) {
      for($i=0;$i<count($item);$i++){
      $create_date = $item[$i]->create_timestamp > 0 ? date('Y-m-d H:i:s', $item[$i]->create_timestamp) : '&mdash;';
      echo '<li>';
      echo '<a href="quiz/' . $item[$i]->slug . '"><b>' . $item[$i]->name . "</b></a> <i>" . $create_date . '</i><br />';
      echo '<br /><a href="quiz/' . $item[$i]->slug . '">Details ...</a>';
      echo "</li>\n";
      }
    }
    echo "</ul>\n";
  }
  else {
    echo "<p>No quiz at this moment.</p>";
  }
?>
</div>
