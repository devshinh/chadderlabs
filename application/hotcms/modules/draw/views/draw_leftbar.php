<?php
if (isset( $badges ) && is_array($badges)) { ?>
<div class="left_bar_list">
	<ul>
	<?php
  foreach ($badges as $badge) {
    echo '<li><a href="/hotcms/' . $module_url . '/edit/' . $badge->id . '/">' . $badge->name . ' detail</a></li>';
	} ?>
	</ul>
</div>
<?php } ?>

