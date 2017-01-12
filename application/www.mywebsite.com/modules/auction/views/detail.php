<?php
if ($item->opening_time == '0000-00-00 00:00:00' || $item->opening_time == NULL) {
  $open_time = $auction->opening_time;
}
else {
  $open_time = $item->opening_time;
}
if ($item->closing_time == '0000-00-00 00:00:00' || $item->closing_time == NULL) {
  $close_time = $auction->closing_time;
}
else {
  $close_time = $item->closing_time;
}
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
?>
<div class="item-detail" id="item-<?php print($item->id); ?>">
 <div class="left-col">
  <div id="item-asset">

    <?php
    if (is_array($assets) && count($assets) > 0) {
      echo "<ul class=\"asset-list\" id=\"slides\">\n";
      foreach ($assets as $asset) {
        echo '<li class="product-image">' . $asset->html . '</li>';
      }
      echo "</ul>\n";
    }
    ?>
  </div>
  <div id="item-description">
    <p>Reserved Price: $<?php print($item->minimum_bid);
    ?><br />
    Minimum Bid Increment: $<?php print($item->minimum_increment); ?></p>
    <p>Opening time: <?php print($open_time); ?><br />
    Closing time: <?php print($close_time); ?></p>
  </div>
</div>
<div class="right-col">
  <h1><?php print($item->name); ?></h1>
  <div id="item-summary">
    <?php print(html_entity_decode($item->short_description)); ?>
  </div>
  <div id="item-bids">
    <h2>Latest Bids</h2>
    <?php
    $current_high = 0;
    $my_min_bid = $item->minimum_bid;
    $i=0;
    if (is_array($bids) && count($bids) > 0) {
      echo "<ul class=\"bid-list\">\n";
      foreach ($bids as $bid) {
        $i++;
        echo '<li>';
        $username = $bid->first_name .' '. ucfirst(substr($bid->last_name, 0, 1)).'.';
        if ($i== 1){
          printf('<b>%s &mdash; $%s</b>', $username, number_format($bid->amount,2,'.',','));
        }else{
          printf('%s &mdash; $%s', $username, number_format($bid->amount,2,'.',','));
        }
        if ($bid->highest) {
          $current_high = $bid->amount;
          $my_min_bid = $bid->amount + $item->minimum_increment;
          echo ($my_min_bid < 99999999.99) ? '<br /><i>(current high bid)</i><div style="height:15px"></div>' : '<br /><b>(maximum bid reached)</b>';
        }
        echo "</li>\n";
      }
      echo "</ul>\n";
    }
    else {
      echo '<p>No bids have been placed yet.<br />Be the first one to bid on this item!</p>';
    }
    ?>
  </div>
  <div id="bidding-form">
    <?php
    $open_err = '';
	  $err = '';
    if(strtotime($auction->closing_time)<time()){
      $err = '<p><b>Sorry, bidding for this item is closed.</b></p>';
    }
    if(strtotime($auction->opening_time)>time()){
      $open_err = '<p><b>This auction is not open yet.</b></p>';
    }
    //time checks -> item related
    if(!empty($item->closing_item) && strtotime($item->closing_time)<time()){
      $err = '<p><b>Sorry, bidding for this item is closed.</b></p>';
    }
    if(!empty($item->opening_item) && strtotime($item->opening_time)>time()){
      $open_err = '<p><b>This auction is not open yet. Please check back later.</b></p>';
    }
    if(!empty($open_err)){
      echo $open_err;
    }elseif (!empty($err)){
      echo ($err);
    }else{
      if (empty($userid)) {
        echo '<p><a href="/login" class="triangle">Log in to bid on this item</a></p>';
      }
      else {
        // display bidding form
      ?>
  <?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
  <?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>
  <?php if ($my_min_bid < 99999999.99) { ?>
        <form method="post" id="formBid" <?php echo ($environment == 'admin_panel' ? 'onsubmit="return false;"' : ''); ?>>
        <?php echo form_hidden($hidden_fields); ?>
        <table id="table_form">
          <tr>
          <?php if ($current_high > 0) { ?>
            <th><label>Current high bid:</label></th>
            <td>$<?php echo number_format($current_high,2,'.',','); ?></td>
          <?php
          }
          ?>
          </tr>
          <tr>
            <th><label for="bid">Your bid:</label></th>
            <td>$ <?php echo form_input($bid_field); ?></td>
          </tr>
          <tr>
            <td colspan="2">
            (enter $<?php echo number_format($my_min_bid,2,'.',','); ?> or more)
            </td>
          </tr>
          <tr>
            <td colspan="2">
            <input type="<?php echo ($environment == 'admin_panel' ? 'button' : 'submit'); ?>" name="Submit" id="submit" class="triangle" value="Place Bid" />
            </td>
          </tr>
        </table>
        </form>
  <?php } ?>
      <?php
      }
    }
    ?>
  </div>
<p><a class="triangle" href="<?php print('/gallery'); ?>">Go Back</a></p>
</div>
</div><!-- end of item detail -->
