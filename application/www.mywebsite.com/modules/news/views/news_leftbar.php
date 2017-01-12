<a class="red_button" href="<?php printf('/hotcms/%s/create', $module_url); ?>"><?php echo lang( 'hotcms_add_new' ) ?></a>
<?php
if (isset( $news_list ) && is_array($news_list)) { ?>
<div class="left_bar_list">
	<ul>
	<?php
  foreach ($news_list as $item) {
    $trunked = $item->title;
    if (strlen($trunked) > 30) {
      $trunked = substr($trunked, 0, 30) . '...';
    }
    echo '<li><a href="/hotcms/' . $module_url . '/edit/' . $item->id . '">' . $trunked . '</a></li>';
	} ?>
	</ul>
</div>
<?php } ?>

