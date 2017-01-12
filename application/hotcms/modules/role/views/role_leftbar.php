<?php
if (isset( $roles ) && is_array($roles)) { ?>
<div class="left_bar_list">
	<ul>
	<?php
  foreach ($roles as $role) {
    echo '<li><a href="/hotcms/' . $module_url . '/edit/' . $role->id . '/'. $site_id_for_roles .'">' . $role->name . ' detail</a></li>';
	} ?>
	</ul>
</div>
<?php } ?>
<a class="red_button" href="<?php printf('/hotcms/%s', $module_url); ?>/create"><?php echo lang( 'hotcms_add_new' ) ?></a>
