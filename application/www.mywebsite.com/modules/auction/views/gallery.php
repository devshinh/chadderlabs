<?php
  if (!empty($title)) {
    echo '<h3>' . $title . "</h3>\n";
  }
  if ($environment == 'admin_panel') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/auction/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/auction/js/' . $js . "\"></script>\n";
    }
  }

  $image_count = 0;
	$image_row = 1;
	$useragent = $_SERVER['HTTP_USER_AGENT'];

	//foreach ($categories as $cat) {
?>
<div class="auction_items">
<?php
    foreach ($items as $item) {
      if(array_key_exists($item->id, $items_images)){
        $css_classes = '';
        $image_count++;
        if($image_count == 4) { $image_count = 1; $image_row++; echo '</div>'; }
        if($image_count == 1) { echo $image_row != 1 ? '<div class="galleryRowContainer">' : '<div class="galleryFirstRowContainer">';  }
        if($image_count == 3) { $css_classes = 'right_item'; }
        //$image_path = '/var/www/auction-live/auction/application/auction.hottomali.com/asset/upload/image/tmb_200/'.$items_images[$item->id]->file_name.'.'.$items_images[$item->id]->extension;
        //list($width, $height, $type, $attr)= getimagesize($image_path);
        $portrait = true; //($width <= $height);
        if($image_count == 1) { $css_classes = 'left_item_'.($portrait ? 'portrait': 'landscape'); }
        if($image_count == 2) { $css_classes = 'center_item_'.($portrait ? 'portrait': 'landscape'); }
        if($image_count == 3) { $css_classes = 'right_item_'.($portrait ? 'portrait': 'landscape'); }
        //if($image_row > 1) $css_classes .= ' needs_top_border';
        if (!empty($items_images[$item->id])){


          $front_image = sprintf('<img src="%s/asset/upload/image/auction_product/thumbnail_200x200/%s%s.%s" alt="%s"/>', $this->config->item( 'base_url' ), $items_images[$item->id]->file_name, '_thumb', $items_images[$item->id]->extension, $items_images[$item->id]->name);
        }else {
          $front_image = 'image place holder';
        }
        ?>
        <div style="float: left; height: 300px; overflow: hidden;">
          <?php if($image_row > 1) { ?> <div class="auction_item_container"></div> <?php } ?>
          <div class="auction_item <?php echo $css_classes; ?>">
            <div class="<?php echo ($portrait ? 'portrait': 'landscape'); ?>">
              <?php
              //try adding a span to divs to hack it into some vertical alignment "line-hight bug"
              if(strchr($useragent,"MSIE 7.0")) {
              ?>
              <span style="display: inline-block;"></span>
              <?php } ?>
              <a href="<?php printf('/gallery/%s', $item->slug); ?>" title="<?php print($item->name); ?>"><?php print($front_image); ?></a>
              <img class="plus" src="/themes/auction/images/plus_icon.jpg" />
            </div>
          </div>
        </div>
<?php
      }
    }
  //}
  if($image_count > 0) {
    while($image_count % 3 != 0) {
      $css_classes = '';
      $image_count++;
      if($image_count == 3) { $css_classes = 'right_item'; }
      $portrait = false;
      if($image_count == 1) { $css_classes = 'left_item_'.($portrait ? 'portrait': 'landscape'); }
      if($image_count == 2) { $css_classes = 'center_item_'.($portrait ? 'portrait': 'landscape'); }
      if($image_count == 3) { $css_classes = 'right_item_'.($portrait ? 'portrait': 'landscape'); }
?>
      <div style="float: left">
        <?php if($image_row > 1) { ?> <div class="auction_item_container"></div> <?php } ?>
        <div class="auction_item <?php echo $css_classes; ?>">
          <div class="empty_<?php echo ($portrait ? 'portrait': 'landscape'); ?>">
          </div>
        </div>
      </div>
<?php
    }
		echo "</div>";
	}
?>
</div><!-- end of auction_items -->
