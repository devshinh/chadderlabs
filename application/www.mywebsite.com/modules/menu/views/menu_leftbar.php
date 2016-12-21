<?php
  if (isset($group_list) && is_array($group_list)) {
?>
<div class="left_bar_list">
  <a class="red_button" href="<?php printf('/hotcms/%s/create', $module_url); ?>"><?php echo $add_new_text ?></a>
	<ul>
	<?php
  foreach ($group_list as $item) {
    $trunked = $item->menu_name;
    if (strlen($trunked) > 30) {
      $trunked = substr($trunked, 0, 30) . '...';
    }
    echo '<li><a href="/hotcms/' . $module_url . '/edit/' . $item->id . '">' . $trunked . '</a></li>';
	} ?>
	</ul>
</div>
<?php } ?>

