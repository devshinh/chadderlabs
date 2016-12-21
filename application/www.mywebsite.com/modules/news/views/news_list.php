<?php if (!empty($title)) { ?>
<div class="hero-unit">
  <h1><?=$title?></h1>
</div>
<?php
}
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/news/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/news/js/' . $js . "\"></script>\n";
  }
}
?>
<div class="news_items">
<?php
  if (count($items) > 0) {
    
    foreach ($items as $item) {
      $create_date = $item->create_timestamp > 0 ? date($this->config->item('timestamp_format_without_time'), $item->create_timestamp) : '&mdash;';
      echo '<div class="news-preview-wrapper">';
      if (!empty($item->featured_image)){
      echo '<div class="news-image">';
      
      $img_full = '<img src="'.$item->featured_image->full_path.'" alt="'.$item->featured_image->name.'" title="'.$item->featured_image->description.'" style="width: 100% !important; max-height: auto !important;" />';
      $img_link= sprintf('<a href="news/%s">%s</a>', $item->slug, $img_full);
      echo '<div style="width: 100%; height: auto; max-height: 200px overflow: hidden">'.$img_link.'<div class="news-image-arrow"></div></div>';      
      echo '</div>';
      }
      echo '<div class="news-preview-texts hero-unit">';
      printf('<div class="row-fluid news-posted">Posted on %s</div>',$create_date);
      printf('<div class="row-fluid news-title"><a href="news/%s">%s</a></div>',$item->slug,$item->title);
      printf('<div class="row-fluid news-snippet"><p>%s</p></div>',$item->snippet);
      printf('<div class="row-fluid"><a class="view-all-link" href="news/%s"><span class="view-all-arrows">» </span>Read more</a></div>',$item->slug);
      echo "</div>\n";
      echo "</div>\n";
    }
  }
  else {
    echo "<p>No news at this moment.</p>";
  }
?>
</div>
