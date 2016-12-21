<?php if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/news/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/news/js/' . $js . "\"></script>\n";
  }
}
?>
<div id="news-item">
<div class="hero-unit no-bottom-margin">
<?php
if (!empty($title)) {
  echo '<div class="row-fluid">';
  echo '<div class="box-featured-title pull-left">' . $title . '</div>';
  echo '<div class="pull-right"><a href="/news/" class="btn">BACK</a></div>';
  echo '</div>';
}else{
  echo '<div class="row-fluid">';
  echo '<div class="pull-right"><a href="/news/" class="btn">BACK</a></div>';
  echo '</div>';
}
if (!empty($item)) {

    $create_date = $item->create_timestamp > 0 ? date($this->config->item('timestamp_format_without_time'), $item->create_timestamp) : '&mdash;';
    echo '<div class="news-preview-wrapper">';
    echo '<div class="news-preview-texts">';
    printf('<div class="row-fluid news-title"><h1><a href="news/%s">%s</a></h1></div>',$item->slug,$item->title);
    printf('<div class="row-fluid news-posted">Posted on %s</div>',$create_date);
    echo "</div>\n";
    echo "</div>\n";
}
?>
</div>
    <?php
    if (!empty($item->featured_image)){
    echo '<div class="news-image">';

    $img_full = '<img src="'.$item->featured_image->full_path.'" alt="'.$item->featured_image->name.'" title="'.$item->featured_image->description.'" style="width:auto;"/>';
    $img_link= sprintf('<a href="news/%s">%s</a>', $item->slug, $img_full);
    printf('%s<div class="news-image-arrow"></div>',$img_full);  
    echo '</div>';
    }?>

<div class="hero-unit">
<?php
if (!empty($item)) {

    $create_date = $item->create_timestamp > 0 ? date($this->config->item('timestamp_format_without_time'), $item->create_timestamp) : '&mdash;';
    echo '<div class="news-preview-wrapper">';
    echo '<div class="news-preview-texts">';
    printf('<div class="row-fluid news-snippet">%s</div>',$item->body);
    echo "</div>\n";
    echo "</div>\n";
}
else {
  echo "<p>No news at this moment.</p>";
}
?>
</div>
 </div>
