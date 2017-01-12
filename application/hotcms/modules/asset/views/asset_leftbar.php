<a class="red_button" href="<?php printf('/hotcms/%s/create', $module_url); ?>"><?php echo lang( 'hotcms_add_new' ) ?></a>
<?php
if (isset( $categories ) && is_array($categories)) { ?>
<div class="left_bar_list">
	<ul>
	<?php
  foreach ($categories as $id => $name) {
//    $trunked = $item->title;
//    if (strlen($trunked) > 30) {
//      $trunked = substr($trunked, 0, 30) . '...';
//    }
    echo '<li><a href="/hotcms/' . $module_url . '/edit/' . $id . '">' . $name . '</a></li>';
	} ?>
	</ul>
</div>
<?php } ?>

