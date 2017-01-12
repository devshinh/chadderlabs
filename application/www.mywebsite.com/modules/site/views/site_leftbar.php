<?php
if (isset( $sites ) && is_array($sites)) { ?>
<div class="left_bar_list">
	<ul>
	<?php
  foreach ($sites as $row) {
    echo '<li><a href="/hotcms/' . $module_url . '/edit/' . $row->id . '">' . $row->name . '</a></li>';
	} ?>
	</ul>
</div>
<?php } ?>
<a class="red_button" href="<?php printf('/hotcms/%s', $module_url); ?>/create"><?php echo lang( 'hotcms_add_new' ) ?></a>
