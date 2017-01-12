<li id="menu_item_<?php echo $menu_item->id ?>" class="jstree-left <?php echo isset($menu_item->sub_menu) && count($menu_item->sub_menu) > 0 ? "jstree-open" : "jstree-leaf" ?><?php echo !$menu_item->enabled ? ' menu-disabled': ''; ?>"
	<ins class="jstree-icon">&nbsp;</ins><a href="page/sitemap_edit/<?php echo $menu_item->id; ?>/reset" onclick="return confirm_discard();"><ins class="jstree-icon">&nbsp;</ins><?php echo $menu_item->title; ?></a>
	<?php if(isset($menu_item->sub_menu) && count($menu_item->sub_menu) > 0) { ?>
		<ul style="">
		<?php 
			foreach($menu_item->sub_menu as $sub_menu_item) {	
		?>
			<?php
				$data['menu_item'] = $sub_menu_item;
				$data['menu_group_id'] = $menu_group_id;
				$this -> load -> view('menu/menu_item_sub_page_publisher', $data);
			?>	
		<?php } ?>			
		</ul>															
	<?php } ?>	
</li>
