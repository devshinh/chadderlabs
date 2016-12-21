<?php
if (isset( $authorized_roles ) && is_array($authorized_roles)) { ?>
<div class="left_bar_list">
	<ul>
	<?php
  foreach ($authorized_roles as $role) {
    echo '<li><a href="/hotcms/' . $module_url . '/index/' . $role->id . '">' . $role->name . '</a></li>';
	} ?>
	</ul>
</div>
<?php } ?>
<a class="red_button" href="<?php printf('/hotcms/%s', $module_url); ?>/create"><?php echo lang( 'hotcms_add_new' ) ?></a>
