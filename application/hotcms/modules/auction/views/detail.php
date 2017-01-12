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
    <h2>Notes from artist</h2>
    <?php print($item->description); ?>
    <br />
    <p>Reserved Price: $<?php print($item->minimum_bid); ?><br />
    Minimum Bid Increment: $<?php print($item->minimum_increment); ?></p>
    <p>Opening time: <?php print(($item->opening_time=='' || $item->closing_time == NULL ?$auction->opening_time:$item->opening_time)); ?><br />
    Closing time: <?php print(($item->closing_time=='' || $item->closing_time == NULL?$auction->closing_time:$item->closing_time)); ?></p>
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
    if (is_array($bids) && count($bids) > 0) {
      echo "<ul class=\"bid-list\">\n";
      foreach ($bids as $bid) {
        echo '<li>';
        $username = $bid->first_name .' '. ucfirst(substr($bid->last_name, 0, 1)).'.';
        printf('%s - $%s', $username, $bid->amount);
        if ($bid->highest) {
          $current_high = $bid->amount;
          $my_min_bid = $bid->amount + $item->minimum_increment;
          echo ($my_min_bid < 99999999.99) ? '<br /><b>(current high bid)</b>' : '<br /><b>(maximum bid reached)</b>';
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
    //time checks -> auctin related
    $open_err = '';
	  $err = '';
    if(strtotime($auction->closing_time)<time()){
      $err = '<p><b>Sorry, bidding for this item is closed.</b></p>';
    }
    if(strtotime($auction->opening_time)>time()){
      $open_err = '<p><b>This auction is not open yet.</b></p>';
    }
    //time checks -> item related
    if(!empty($item->closing_item) && strtotime($$item->closing_time)<time()){
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
        echo '<p><a href="/login">Log in to bid on this item.</a></p>';
      }
      else {
        // display bidding form
      ?>
  <?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
  <?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>
  <?php if ($my_min_bid < 99999999.99) { ?>
        <form method="post" id="formBid" <?php echo ($environment == 'admin_panel' ? 'onsubmit="return false;"' : ''); ?>>
        <?php echo form_hidden($hidden_fields); ?>
        <table>
          <tr>
          <?php if ($current_high > 0) { ?>
            <th><label>Current high bid:</label></th>
            <td>$<?php echo $current_high; ?></td>
          <?php
          }
          ?>
          </tr>
          <tr>
            <th><label for="bid">Your bid:</label></th>
            <td>$ <?php echo form_input($bid_field); ?><br /> (enter $<?php echo $my_min_bid; ?> or more)</td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="<?php echo ($environment == 'admin_panel' ? 'button' : 'submit'); ?>" name="Submit" class="triangle" value="Place Bid" />
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
<p><a href="<?php print('/gallery'); ?>">Go Back</a></p>
</div>
</div><!-- end of item detail -->
