<?php
  if (!empty($title)) {
    echo '<h3>' . $title . "</h3>\n";
  }
  if ($environment == 'admin_panel') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/carousel/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/carousel/js/' . $js . "\"></script>\n";
    }
  }
  if (is_array($items) && count($items)>0) {
?>
<div class="carousel">
  <div id="carouselBorder">
    <div id="carouselWrapper">
      <div id="carousel">
      <?php
      foreach ($items as $item) {
        if (empty($item->image)) {
          continue;
        }
        echo '<div class="image slide">';
        if ($item->link > '') {
          echo '<a href="' . $item->link . '" title="' . $item->link_title . '"' . ($item->link_target > 0 ? ' target="_blank"' : '') . ' ' . ($item->link_property > '' ? $item->link_property : '') . '>';
        }
        echo '<img src="' . $item->image->full_path . '" alt="' . $item->image->description . '" />';
        if ($item->link > '') {
          echo '</a>';
        }
        echo "</div>\n";
      }
      ?>
      </div>
    </div>
  </div>
  <div id="carouselNavWrap" class="stopped"><a id="navRight" class="carouselNavControl" href="#"></a><span id="carouselNav"></span><a id="navLeft" class="carouselNavControl" href="#"></a></div>
</div>
<?php
  }
?>
