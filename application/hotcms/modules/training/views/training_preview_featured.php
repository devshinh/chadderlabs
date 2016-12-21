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

  <div id="featured-item-preview" class="hero-unit" id="item-<?php print($item->id); ?>">
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
          <div class="item-subtitle">Quiz Completion:</div>
          <div class="row-fluid progress-bar">
            <div class="span8">
              <div class="progress">
                <div class="bar" style="width: <?php print($item->points_percent); ?>%;"></div>
              </div>
            </div>
            <div class="span4">
              <span class="blue"><?php print($item->points_percent); ?>%</span>
            </div>
          </div>
          <div class="item-subtitle">Points Achieved:</div>
          <div class="blue points"><b><?php print($item->user_points); ?> Points</b></div>
          <a class="btn btn-primary btn-large" href="/labs/product/<?php echo $item->slug; ?>">
            TRAIN NOW
          </a>
        </div>
      </div>
      <br />
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
    </div><!-- .container-fluid -->
  </div>
<?php } ?>