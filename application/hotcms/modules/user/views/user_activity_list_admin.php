<?php
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/user/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/user/js/' . $js . "\"></script>\n";
  }
}
?>
<div class="hero-unit">
  <div class="container-fluid">
    <div class="row">
      <?php
      if (!empty($title)) {
        echo '<div class="box-title"><b>' . $title . "</b></div>\n";
      }
      ?>
    </div>
    <div id="user_activities">
      <?php
      $i=0;
      if (count($items) > 0) {
        foreach ($items as $item) {
            $i++;
            $even = ($i % 2 == 1 )?'light':'dark';
            printf('<div class="row activity_wrapper %s">',$even);
            if($item->point_type == 'quiz-draw'){
                printf('<div class="description">%s - %s [%s]</div>',$item->description,   $item->training_title, $item->quiz_type_name);
            }else{
              printf('<div class="description">%s</div>',$item->description);
            }
            printf('<div class="time_ago">%s ago - (%s) </div>',$item->time_ago, date('Y-m-d H:i:s ', $item->create_timestamp));
            //printf('<div class="timestamp">%s</div>',date('Y-m-d H:i:s ', $item->create_timestamp));
            print('</div>');
        }
      }
      else {
        print('<div class="row-fluid">No activities were found.</div>');
      }
      ?>
    </div>
  </div>
</div>
