<?php
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/quiz/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/quiz/js/' . $js . "\"></script>\n";
  }
}
?>
<div class="featured-item-preview hero-unit">
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span8">
      <?php
      if (!empty($title)) {
        echo '<div class="box-title">' . $title . "</div>\n";
      }
      ?>
      </div>
      <div class="pull-right">
        <a href="/leaderboard" class="view-all-link"><span class="view-all-arrows">&raquo; </span>See All</a>
      </div>
    </div>
    <div id="leaderboard">
        <div class="row-fluid tableHeader">
        <div class="span7">User</div><div class="span3">Points</div>
        </div>


      <?php
      foreach ($users as $u){
        print ('<div class="row-fluid ">');
        printf('<div class="span7">%s %s</div><div class="span3">%s</div>', $u->first_name, $u->last_name, $u->points_earned);
        print ('</div>');
      }?>
    </div>

  </div> <!--.container-fluid -->

</div>
