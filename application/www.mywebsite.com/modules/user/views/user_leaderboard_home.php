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
<div class="hero-unit" id="leaderboard-widget">
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
    <div id="leaderboard-table">
      <div class="row-fluid tableHeader">
        <div class="span7">User</div>
        <?php if($type == 'points'){?>
        <div class="span5" style="text-align:right;">Points</div>
        <?php }else{?>
        <div class="span5" style="text-align:right;">Entries</div>
        <?php }?>
      </div>
      <?php
      foreach ($users as $u) {
        print ('<div class="row-fluid ">');
        printf('<div class="span7"><a href="/public-profile/%s">%s</a></div><div class="span5" style="text-align:right">%s</div>', strtolower($u->screen_name), $u->screen_name, number_format($u->points, 0));
        print ('</div>');
      }
      ?>
    </div>

  </div> <!--.container-fluid -->

</div>
