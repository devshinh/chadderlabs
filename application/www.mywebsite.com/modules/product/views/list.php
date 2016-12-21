<div class="row-fluid">
    <?php
    if (!empty($title)) {
        echo '<h1 class="pull-left">' . $title . "</h1>\n";
    }
    if ($environment == 'admin_panel') {
        if (!empty($css)) {
            echo '<link rel="stylesheet" type="text/css" media="all" href="modules/product/css/' . $css . "\" />\n";
        }
        if (!empty($js)) {
            echo '<script type="text/javascript" src="modules/product/js/' . $js . "\"></script>\n";
        }
    }
    $user_id = $this->session->userdata('user_id');
    ?>
    <?php if (!empty($user_id)) { ?>
        <div class="pull-right">
            <a href="/shop/cart" class="view-all-link">
                <span class="view-all-arrows">» </span>Shopping Cart
            </a>    
        </div>
    <?php } ?>
</div>
<div id="product-list">

    <?php
    if (count($items) > 0) {
        $i = 0;

        foreach ($items as $item) {

            //if (!empty($items_images[$item->id])) {
            if (!empty($item->featured_image_id)) {

                $img = $items_images[$item->id][0];
                $front_image = sprintf('<img src="%s" alt="%s" title="%s" width="198" height="249" />', $img->full_path, $img->name, $img->description);
            } else {
                $front_image = 'Image Place Holder';
            }

            $img_link = sprintf('<div style="width: 180px; height: 100px; overflow: hidden"><a href="store/product/%s" title="%s">%s</a></div>', $item->slug, $item->name, $front_image);
            $item_name = sprintf('<div class="item-name">%s</div>', $item->name);
            if ($item->stock > 0) {
                $item_price = sprintf('<div class="item-price">Price: %s Pts</div>', number_format($item->price, 0));
            } else {
                $item_price = sprintf('<div class="item-price"><strong><span class="red">SOLD OUT</span></strong></div>');
            }
            //$item_progress = sprintf('<div class="item-progress">progress</div>');
            $item_progress = '<div class="row-fluid">
            <div class="span9">
              <div class="progress">
                <div class="bar" style="width: 100%;"></div>
              </div>
            </div>
            <div class="span3">
              <span class="blue">100%</span>
            </div>
          </div>';
            $item_progress = '';

            $item_link = sprintf('<a class="view-all-link"href="store/product/%s" title="Redeem Now"><span class="view-all-arrows">» </span>Redeem Now</a>', $item->slug);
            $item_wrapper = sprintf('<div class="span4 product_item">%s %s %s %s %s</div>', $img_link, $item_name, $item_price, $item_progress, $item_link);


            if ($i % 3 == 0) {
                printf('<div class="row-fluid">%s', $item_wrapper);
            } else {
                print $item_wrapper;
            }
            $i++;
            if ($i % 3 == 0)
                print('</div>');
        }
        if ($i % 3 != 0)
            print('</div>');
    } else {
        echo "<p>No training at this moment.</p>";
    }
    ?>      
</div>
