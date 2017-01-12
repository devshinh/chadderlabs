<h2><a class="red_button" href="<?php printf('/hotcms/%s', $module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
<div class="left_bar_list">
<ul>
<?php foreach ($current as $row) { ?>

  <li><a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id) ?>"><?php echo $row->name ?></a></li>

<?php }?>
</ul>
</div>
