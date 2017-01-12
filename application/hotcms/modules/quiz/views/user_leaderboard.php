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
      <?php
      if (!empty($title)) {
        echo '<div class="box-title">' . $title . "</div>\n";
      }
      ?>
    </div> <!--.row-fluid -->
    <br />
      <?php
      $i=0;
      foreach ($users as $u){
        $i++;
        print ('<div class="row-fluid">');
        printf('<div class="span1">%s</div><div class="span8">%s %s</div><div class="span3">%s points</div>',$i, $u->first_name, $u->last_name, $u->points_earned);
        print ('</div>');
      }?>


  </div> <!--.container-fluid -->

</div>
