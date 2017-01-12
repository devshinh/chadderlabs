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

  $winning_text = (strtotime($auction->closing_time)>time()) ? "Winning" : "Won";
?>
<div>
<?php if(count($items) > 0) { ?>
<h2>Your Bids:</h2>
<ul id="UserBidList">
<?php
  foreach($items as $bid) {
?>
  <li>
    <div class="itemname left"><a href="/gallery/<?php echo $bid->slug ?>"><?php echo $bid->name ?></a><?php echo $bid->highest == 1 ? " ({$winning_text})": "" ?></div>
    <div class="datefield left"><?php echo $bid->create_date ?></div>
    <div class="amount left">$<?php echo number_format($bid->amount, 2, '.', ',') ?></div>
</li>
<?php
  }
?>
</ul>
<?php } else { ?>
	<h2>You have no bids.</h2>
<?php } ?>
</div>
