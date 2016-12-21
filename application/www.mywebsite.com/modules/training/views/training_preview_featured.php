<?php
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/training/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/training/js/' . $js . "\"></script>\n";
  }
}
foreach ($items as $item) {
?>

  <div id="featured-item-preview" class="hero-unit">
    <div class="container-fluid">
      <div class="row-fluid">
        <?php
        if (!empty($title)) {
          echo '<div class="box-featured-title">' . $title . "</div>\n";
        }
        ?>
      </div>
      <div class="row-fluid">
        <div class="item-header"><?php echo $item->title; ?></div>
      </div>
      <div class="row-fluid featured-box">
        <div class="span4">
          <?php
          $img = sprintf('<img class="reflection_less" src="%s" alt="%s" title="%s" />', $item->featured_image->full_path, $item->featured_image->name, $item->featured_image->description);
          printf('<a href="/labs/product/%s" title="%s">%s</a>', $item->slug, $item->title, $img);
          ?>
        </div>
        <div class="span1"></div>
        <div class="span7">
          <?php if(strlen($item->description) > 450){
            $description = substr($item->description, 0, 450).'...';
          } else{
            $description = $item->description;
          }
?>
          <div class="item-description"><?php echo $description; ?></div>
          <div class="item-subtitle">Lab Completion:</div>
          
          <div class="row-fluid progress-bar">
            <div class="span8">
              <div class="progress">
                <div class="bar" style="width: <?php print($item->highest_percent_score); ?>%;"></div>
              </div>
            </div>
            <div class="span4">
              <span class="blue"><?php print($item->highest_percent_score); ?>%</span>
            </div>
          </div>  
          <?php if ($point_balance == 'ok') { ?>
          <?php if(has_permission('earn_points') && (($item->max_points - $item->user_points) >0 )){?>  
            <div class="row-fluid">
              <div class="item-subtitle">Points Available:
              <span class="blue points"><b><?php print($item->max_points - $item->user_points); ?> Points</b></span>
              </div>          
            </div>
          <?php } ?>
          <?php if(has_permission('earn_draws') && (($item->max_contest_entries - $item->user_contest_entries) > 0)){?>
        <div class="row-fluid">
          <div class="item-subtitle points">Contest Entries Available: <span class="blue"><?php print($item->max_contest_entries - $item->user_contest_entries); ?></span></div>
        </div>              
          <?php }?>
          <a class="btn btn-primary btn-large" href="/labs/product/<?php echo $item->slug; ?>">
            TRAIN NOW
          </a>
          <?php } ?>
        </div>
      </div>
      <br />
      <?php if ($point_balance == 'ok') { ?>
      <div class="row-fluid">
        <?php
        $i_pics = 0;
        shuffle($item->assets);
        foreach ($item->assets as $v) {

          if ($v->type == 1 && $i_pics <3) { // image
            printf('<div class="span4"><a href="/labs/product/%s"><img src="%s/%s.%s" alt="%s" title="%s"/></a></div>', $item->slug, $v->thumb, $v->file_name . '_thumb', $v->extension, $v->description, $v->name);
            $i_pics++;
          }
        }
        ?>
      </div><!-- .row-fluid -->
      <?php }else{?>
    <div id="no-cheddar-img"></div>
    <br />
    <div class="box-title">This lab is all outta Cheddar!</div>
    <p>
      Training and quizzes for <?php echo $site_name?> are currently unavailable due to insufficient Cheddar Points for the Training Lab.
    </p>          
      <?php }?>
    </div><!-- .container-fluid -->
  </div>
<?php } ?>