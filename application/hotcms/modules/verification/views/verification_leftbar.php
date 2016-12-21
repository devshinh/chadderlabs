<?php
if (isset( $verifications ) && is_array($verifications)) { ?>
<div class="left_bar_list">
	<ul>
	<?php
  foreach ($verifications as $verification) {
    echo '<li><a href="/hotcms/' . $module_url . '/edit/' . $verification->id . '/">' . $verification->id . ' detail</a></li>';
	} ?>
	</ul>
</div>
<?php } ?>
<a class="red_button" href="<?php printf('/hotcms/%s', $module_url); ?>/create"><?php echo lang( 'hotcms_add_new' ) ?></a>
