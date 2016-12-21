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
<div id="refer_history">
  <div class="container-fluid">
    <div class="row-fluid header">
      <?php
      if (!empty($title)) {
        echo '<div class="box-title pull-left">' . $title . '</div>';
        echo '<div class="pull-right"> <a href="#refColInfoModal" data-toggle="modal" class="view-all-link"><span class="view-all-arrows">»</span> INFO</a> </div>';
      }
      ?>
    </div> <!--.row-fluid -->
    <br />
    
    <div class="row-fluid">
        <div class="tableHeader">
            <div class="span3"><strong>First Name</strong></div>
            <div class="span3"><strong>Screen Name</strong></div>
            <div class="span3"><strong>Email</strong></div>
            <div class="span3" style="text-align:right;"><strong>Status</strong></div>
        </div>

    </div>   
    <?php
    $i=0;
    foreach ($history as $h) {
      //$i++;
      print ('<div class="row-fluid tableRow">');
      printf('<div class="span3">%s</div><div class="span3"><a href="/public-profile/%s">%s</a></div><div class="span3">%s</div><div class="span3" style="text-align:right">%s</div>',$h->first_name,  strtolower($h->screen_name),$h->screen_name,$h->email,$h->state);
      print ('</div>');
    }
    ?>
  </div> <!--.container-fluid -->
</div>
</div>
