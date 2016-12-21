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
    <div class="row-fluid header">
      <?php
      if (!empty($title)) {
        echo '<div class="box-title">' . $title . "</div>\n";
      }
      ?>
    </div> <!--.row-fluid -->
    <div id="leaderboard-table">
    <div class="row-fluid tableHeader">
        <div class="span2">RANK</div><div class="span5">USERNAME</div>
        <?php if($type == 'points'){?>
        <div class="span5" style="text-align:right;">POINTS
        <?php }else{?>
        <div class="span5" style="text-align:right;">Contest Entries
        <?php }?>
        
        </div>
    </div>   
    <?php
    $i=0;
    foreach ($users as $u) {
      $i++;
      print ('<div class="row-fluid">');
      printf('<div class="span2">%s</div><div class="span7"><a href="/public-profile/%s">%s</a></div><div class="span3" style="text-align:right">%s</div>',$i, strtolower($u->screen_name), $u->screen_name, number_format($u->points, 0));
      print ('</div>');
    }
    ?>
  </div> <!--.container-fluid -->
  </div>
</div>

