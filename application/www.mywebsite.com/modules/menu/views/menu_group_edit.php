<div>
	<form id="menu_group_edit" action="/hotcms/<?php echo $module_url?>/edit/<?php echo $currentItem->id ?>" method="post">
		<div class="row">
			<?php echo form_error('menu_name', '<div class="error">', '</div>');?>
			<?php echo form_label(lang('hotcms_name') . ' ' . lang('hotcms__colon'), 'menu_name');?>
			<?php echo form_input($form['menu_name_input']);?>
		</div>
		<div class="module menuItems">
			<a id="" href="<?php printf('/hotcms/menu/add_menu_item/%s', $currentItem->id )?>" class="red_button_smaller">Add Menu Item</a>
			<?php
			if (!empty($currentMenuItems)) {
				$data['currentMenuGroupId'] = $currentItem->id;
				$data['currentMenuItems'] = $currentMenuItems;
				$this -> load -> view('menu_item', $data);
			}
			?>
		</div>
		<div class="submit">
			<input id="save" type="button" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>"></input>
			<!-- <input type="submit" class="button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" /> -->
			<input type="hidden" name="menu_data" id="menu_data"></input>
			<a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' )
			?></a>
			<a onclick="window.location.reload()" class="red_button"><?php echo lang( 'hotcms_reset' ); ?></a>
			<!-- <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $currentItem->id ?>" class="button"><?php echo lang( 'hotcms_delete' )
			?></a> -->
		</div>
	</form>
</div>
<!--
<div id="demo1" class="demo jstree jstree-0 jstree-default jstree-focused" style="height: 416px; ">
        <ul>
                <li id="phtml_1" class="jstree-open">
                        <ins class="jstree-icon">&nbsp;</ins><a href="#"><ins class="jstree-icon">&nbsp;</ins>Root node 1</a>
                        <ul style="">
                                <li id="phtml_3" class="jstree-leaf jstree-last">
                                        <ins class="jstree-icon">&nbsp;</ins><a href="#"><ins class="jstree-icon">&nbsp;</ins>Child node 2</a>
                                </li>
                        </ul>
                </li>
                <li id="phtml_4" class="jstree-leaf">
                        <ins class="jstree-icon">&nbsp;</ins><a href="#"><ins class="jstree-icon">&nbsp;</ins>Root node 2</a>
                </li>
                <li id="phtml_2" class="jstree-leaf jstree-last">
                        <ins class="jstree-icon">&nbsp;</ins><a href="#"><ins class="jstree-icon">&nbsp;</ins>Child node 1</a>
                </li>
        </ul>
</div>

<pre>
<?php var_dump($currentMenuItems); ?>
</pre>
-->