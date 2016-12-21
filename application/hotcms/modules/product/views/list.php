<?php
  if (!empty($title)) {
    echo '<h3>' . $title . "</h3>\n";
  }
  if ($environment == 'admin_panel') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/product/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/product/js/' . $js . "\"></script>\n";
    }
  }
?>
<div class="product_items">
<?php
  $image_count = 0;
	$image_row = 1;
	$useragent = $_SERVER['HTTP_USER_AGENT'];
  foreach ($items as $item) {
    $portrait = true; //($width <= $height);
    $css_classes = '';
    if (array_key_exists($item->id, $items_images)) {
      $image_count++;
      if ($image_count == 4) { $image_count = 1; $image_row++; echo '</div>'; }
      if ($image_count == 1) { echo $image_row != 1 ? '<div class="galleryRowContainer">' : '<div class="galleryFirstRowContainer">';  }
      if ($image_count == 3) { $css_classes = 'right_item'; }
      //$image_path = '/var/www/product-live/product/application/product.hottomali.com/asset/upload/image/tmb_200/'.$items_images[$item->id]->file_name.'.'.$items_images[$item->id]->extension;
      //list($width, $height, $type, $attr)= getimagesize($image_path);
      if ($image_count == 1) { $css_classes = 'left_item_'.($portrait ? 'portrait': 'landscape'); }
      if ($image_count == 2) { $css_classes = 'center_item_'.($portrait ? 'portrait': 'landscape'); }
      if ($image_count == 3) { $css_classes = 'right_item_'.($portrait ? 'portrait': 'landscape'); }
      //if($image_row > 1) $css_classes .= ' needs_top_border';
      if (!empty($items_images[$item->id])) {
        $front_image = $items_images[$item->id][0]->thumb_html;
      }
      else {
        $front_image = 'Image Place Holder';
      }
    }
?>
  <div class="product-list">
    <?php if ($image_row > 1) { ?> <div class="product_item_container"></div> <?php } ?>
    <div class="product-item <?php echo $css_classes; ?>">
      <div class="<?php echo ($portrait ? 'portrait': 'landscape'); ?>">
        <?php
        //try adding a span to divs to hack it into some vertical alignment "line-hight bug"
        if (strchr($useragent, "MSIE 7.0")) {
        ?><span style="display: inline-block;"></span>
        <?php } ?>
        <a href="<?php printf('/product/%s', $item->slug); ?>" title="<?php print($item->name); ?>"><?php print($front_image); ?></a><br />
        <span class="item-name"><?php echo $item->name; ?></span><br />
        <span class="item-price">Price: <?php echo number_format($item->price, 0); ?> Pts</span><br />
        <span class="item-progress">progress</span><br />
        <a href="<?php printf('/product/%s', $item->slug); ?>" title="Redeem Now">Redeem Now</a>
      </div>
    </div>
  </div>
<?php
  }
  if ($image_count > 0) {
    while($image_count % 3 != 0) {
      $css_classes = '';
      $image_count++;
      if ($image_count == 3) { $css_classes = 'right_item'; }
      $portrait = false;
      if ($image_count == 1) { $css_classes = 'left_item_'.($portrait ? 'portrait': 'landscape'); }
      if ($image_count == 2) { $css_classes = 'center_item_'.($portrait ? 'portrait': 'landscape'); }
      if ($image_count == 3) { $css_classes = 'right_item_'.($portrait ? 'portrait': 'landscape'); }
?>
      <div style="float: left">
        <?php if ($image_row > 1) { ?> <div class="product_item_container"></div> <?php } ?>
        <div class="product_item <?php echo $css_classes; ?>">
          <div class="empty_<?php echo ($portrait ? 'portrait': 'landscape'); ?>">
          </div>
        </div>
      </div>
<?php
    }
		echo "</div>";
	}
  // display a More link leading to the full list page
  if ($max_display > 0) {
    echo '<div class="more-link"><a href="/products">More</a></div>';
  }
?>
</div><!-- end of product_items -->
