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
    <div class="row-fluid">
      <?php
      if (!empty($title)) {
        echo '<div class="box-title">' . $title . "</div>\n";
      }
      ?>
    </div>
    <div id="user_activities">
      <?php
      if (count($items) > 0) {
        foreach ($items as $item) {
          if ($item->point_type == 'quiz' && $item->quiz_history && $item->quiz && $item->training) {
            print('<div class="row-fluid activity_wrapper">');
            $training_item_link = sprintf('<a href="http://%s/labs/product/%s">%s</a>', $item->training->domain, $item->training->slug, $item->training->title);
            $training_img = sprintf('<img src="%s" width="37" height="45"/>', $item->training->featured_image->full_path);
            $desc = sprintf('<span class="quiz_type">%s quiz:</span> %s took %s Quiz<div class="time_ago">%s AGO</div>', $item->quiz->type->name, $item->screen_name, $training_item_link, $item->time_ago);
            printf('<div class="span2 image_wrapper">%s</div><div class="span7 desc">%s</div><div class="span3 points_earned_wrapper"><div class="header_text">Points earned</div>%s</div>',
              $training_img, $desc, $item->points);
            print('</div>');
          }
        }
      }
      else {
        print('<div class="row-fluid">No activities were found.</div>');
      }
      ?>
    </div>
  </div>
</div>

<script type="text/javascript">
function refresh_activity() {
  var ajax_url = "/ajax/user/activity/<?php echo $limit; ?>/" + Math.random()*99999;
  jQuery.getJSON(ajax_url, function(json) {
    if (json.result && json.activities.length > 0) {
      jQuery("#user_activities").empty();
      var row;
      for (var i in json.activities) {
        row = json.activities[i];
        if (typeof row.training_title === "undefined") {
          continue;
        }
        jQuery("#user_activities").append('<div class="row-fluid activity_wrapper"><div class="span2 image_wrapper">'
          + '<img src="' + row.featured_image_path + '" width="37" height="45" /></div>'
          + '<div class="span7 desc"><span class="quiz_type">' + row.quiz_type_name + ' quiz: </span>'
          + row.screen_name + ' took <a href="http://' + row.domain + '/labs/product/' + row.training_slug + '">' + row.training_title + '</a>'
          + ' Quiz <div class="time_ago">' + row.time_ago + ' AGO</div></div>'
          + '<div class="span3 points_earned_wrapper"><div class="header_text">Points earned</div>' + row.points + '</div></div>'
        );
      }
    }
  })
  .error(function(){
    //alert("Sorry but there was an error.");
  });
}
window.setInterval("refresh_activity()", 300000); // refreshes every 5 minutes
</script>
