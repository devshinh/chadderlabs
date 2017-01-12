<?php
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/training/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/training/js/' . $js . "\"></script>\n";
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
    </div>
    <div class="row-fluid item-preview">
      <?php
      $i=0;
      foreach ($items as $item) {
        $i++;
        if ($i < 4) {
          ?>
          <div class="span4">
            <?php
            if (!empty($item->featured_image)) {
              $img = sprintf('<img class="reflection_less" src="%sthumbnail_80x98/%s_thumb.%s" alt="%s" title="%s" />', $item->featured_image->folder_path, $item->featured_image->file_name, $item->featured_image->extension, $item->featured_image->name, $item->featured_image->description);
              printf('<a href="/labs/product/%s" title="%s">%s</a>', $item->slug, $item->title, $img);
            }
            ?>
          </div>
          <?php
        }
      }
      ?>
    </div>
    <div class="pull-right height18">
      <div class="span12 ">
        <a href="/labs/coming-soon#coming-soon" class="view-all-link"><span class="view-all-arrows">&raquo; </span>View All</a>
      </div>
    </div>
  </div>
</div>
