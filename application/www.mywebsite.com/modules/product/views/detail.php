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
$user_id = $this->session->userdata('user_id');
?>
<div class="hero-unit">
    <div class="container-fluid" id="product-detail">
        <div class="row">
            <div class="pull-left">
                <div class="box-featured-title"><a style="color:#997119;" href="/shop">Store</a></div>
                <div class="item-header"><?php print($item->name); ?></div>
            </div>
            <?php if (!empty($user_id)) { ?>
                <div class="pull-right">
                    <a href="/shop/cart" class="view-all-link">
                        <span class="view-all-arrows">» </span>Shopping Cart
                    </a>    
                </div>
            <?php } ?>
        </div>
        <div class="row-fluid">
            <div id="item-<?php print($item->id); ?>">
                <div class="span4">
                    <div id="item-asset">
                        <?php
                        if (is_array($assets) && count($assets) > 0) {
                            //echo "<ul class=\"asset-list\" id=\"slides\">\n";
                            //foreach ($assets as $asset) {
                            //echo '<li class="product-image">' . $asset->full_html . '</li>';
                            //}
                            //echo "</ul>\n";
                            echo $assets[0]->full_html;
                        }
                        ?>
                    </div>
                    <div id="social" style="margin-top: 20px">
                        <p>  
                        <div class="fb-like" data-href="<?php echo (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . '/store/product/' . strtolower(url_title($item->name)); ?>" data-send="false" data-width="100" data-show-faces="false" data-font="arial"></div>  
                        </p>
                        <p> 
                            <a href="https://twitter.com/share" class="twitter-share-button" data-via="cheddarlabs" data-related="cheddarlabs" data-hashtags="cheddar">Tweet</a>
                            <script>!function(d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0];
                                    if (!d.getElementById(id)) {
                                        js = d.createElement(s);
                                        js.id = id;
                                        js.src = "//platform.twitter.com/widgets.js";
                                        fjs.parentNode.insertBefore(js, fjs);
                                    }
                                }(document, "script", "twitter-wjs");</script>
                        </p>
                    </div>
                </div>
                <div class="span8">
                    <div id="item-summary" class="row-fluid">
                        <p><?php print(html_entity_decode($item->description)); ?></p>
                    </div>
                    <div id="item-price" class="row-fluid">
                        PRICE: <?php echo number_format($item->price, 0, '.', ','); ?> Points
                    </div>
                    <div id="messageContainer">
                        <?php
                        if (isset($messages) && is_array($messages)) {
                            foreach ($messages as $msg) {
                                if (is_array($msg) && $msg['message'] > '') {
                                    echo '<div class="message ' . $msg['type'] . '">';
                                    echo '<div class="message_close"><a onClick="closeMessage()">[close]</a></div>';
                                    echo $msg['message'] . '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                    <br />
                    <div id="purchase-form-container">
                        <form method="post" id="purchase-form" <?php echo ($environment == 'admin_panel' ? 'onsubmit="return false;"' : ''); ?>>
                            <?php echo form_hidden($hidden_fields); ?>
                            <div id="table_form">
                                <div class="row-fluid">
                                    <div class="span3">
                                        <label for="quantity">Quantity:</label>
                                    </div>
                                    <div class="span4">
                                        <?php
                                        if ($item->stock > 0) {
                                            echo form_input($quantity_field);
                                        } else {
                                            echo '<strong><span class="red">SOLD OUT</span></strong>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <br />
                                <div class="row-fluid">
                      <!--<i>As of 12am September 30th EA points can no longer be used.</i>-->
                                    <br />
                                    <br />
                                    <?php if ($item->stock > 0) { ?>              
                                        <input type="<?php echo ($environment == 'admin_panel' ? 'button' : 'submit'); ?>" name="Submit" id="submit" class="btn btn-primary btn-large" value="Add to cart" />
                                    <?php } else { ?>
                                        <input disabled type="<?php echo ($environment == 'admin_panel' ? 'button' : 'submit'); ?>" name="Submit" id="submit" class="btn btn-primary btn-large" value="Add to cart" />
                                    <?php } ?>
                                </div>
                                <div class="row-fluid" id="purchase-message">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- end of item detail -->
        </div>
    </div>
</div>
