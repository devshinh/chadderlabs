<?php
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/news/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/news/js/' . $js . "\"></script>\n";
  }
}
?>
<div class="hero-unit news_items" id="new-item-preview">
  <div class="container-fluid">
    <div class="row-fluid">
      <?php
      if (!empty($title)) {
        echo '<div class="box-title">' . $title . "</div>\n";
      }
      ?>
    </div>
    <?php foreach ($items as $item) {

      $create_date = $item->create_timestamp > 0 ? date($this->config->item('timestamp_format_without_time'), $item->create_timestamp) : '&mdash;';
      printf('<div class="row-fluid news-posted">Posted on %s</div>',$create_date);
      printf('<div class="row-fluid news-title"><a href="news/%s">%s</a></div>',$item->slug,$item->title);
      printf('<div class="row-fluid news-snippet"><p>%s</p></div>',$item->snippet);



     } ?>
    <div class="pull-right height18" style="height:18px">
      <div class="span12 ">
        <a href="/news" class="view-all-link"><span class="view-all-arrows">&raquo; </span>View All</a>
      </div>
    </div>
  </div>
</div>


