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
<div class="title">Spend Points</div>
<h1><?php print($item->name); ?></h1>
<div class="item-detail" id="item-<?php print($item->id); ?>">
  <div class="left-col">
    <div id="item-asset">
      <?php
      if (is_array($assets) && count($assets) > 0) {
        echo "<ul class=\"asset-list\" id=\"slides\">\n";
        foreach ($assets as $asset) {
          echo '<li class="product-image">' . $asset->full_html . '</li>';
        }
        echo "</ul>\n";
      }
      ?>
    </div>
    <div id="social">
      <p>Tweet & Like</p>
    </div>
  </div>
  <div class="right-col">
    <div id="item-summary">
      <?php print(html_entity_decode($item->description)); ?>
    </div>
    <div id="item-price">
      <p>PRICE: <?php echo number_format($item->price, 0, '.', ','); ?> Points</p>
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
    <div id="purchase-form-container">
      <form method="post" id="purchase-form" <?php echo ($environment == 'admin_panel' ? 'onsubmit="return false;"' : ''); ?>>
        <?php echo form_hidden($hidden_fields); ?>
        <table id="table_form">
          <tr>
            <td><?php //echo form_input($quantity_field); ?></td>
            <th><label for="quantity">Quantity:</label> &nbsp; </th>
            <td><?php echo form_input($quantity_field); ?></td>
          </tr>
          <tr>
            <td colspan="3">
            <input type="<?php echo ($environment == 'admin_panel' ? 'button' : 'submit'); ?>" name="Submit" id="submit" class="triangle" value="Submit" />
            </td>
          </tr>
          <tr>
            <td colspan="3" id="purchase-message">
            </td>
        </table>
      </form>
    </div>
  <p><a class="triangle" href="<?php print('/products'); ?>">Go Back</a></p>
  </div>
</div><!-- end of item detail -->
