<?php
if (!empty( $currentMenuItems )){ ?>
<div id="menu_editor" class="demo jstree jstree-0 jstree-default jstree-focused">
	<ul>
		<?php 
			foreach($currentMenuItems as $menu_item) { 
		?>
			<?php
				$data['menu_item'] = $menu_item;
				$data['menu_group_id'] = $currentMenuGroupId;
				$this -> load -> view('menu/menu_item_sub_menu', $data);
			?>	
		<?php } ?>
	</ul>			
</div>

<script type="text/javascript">
jQuery(function () {
	    jQuery('#menu_editor')
			.bind("move_node.jstree", function (event, data) {
				jQuery('#menu_editor li a').unbind('click').click(function() {
					alert('You must save your changes before editting individual items');
					return false;
				});
				//console.log('moved');
				/*
		        data.rslt.o.each(function (i) {
		        	console.log('move _node');
		            console.log("id " + jQuery(this).attr("id").replace("node_",""));		            
		            console.log("ref" + data.rslt.cr === -1 ? 1 : data.rslt.np.attr("id").replace("node_",""));
		            console.log("position" + (data.rslt.cp + i));
		            console.log("title" + data.rslt.name);
		            console.log("copy" + data.rslt.cy ? 1 : 0);
		               });
*/		
				    })

        
        		.jstree({
          		"types": { "disabled": { "icon": { "image": "/some/path" } } },  
          		"plugins" : [ "themes", "html_data", "dnd", "json_data", "type"],
                "theme" : { "htc" : "default", "dots" : false, "icons" : false},
                "max_depth" : 3
		});
		
		/*
		jQuery('#checkjson').click(function() {
			//console.log(jQuery("#menu_editor").jstree("get_json"));						
		});
		*/

		
		jQuery('#save').click(function() {
			/*
			var menu_items = jQuery('#menu_editor li');
			var menu_array = new Array();
			menu_array = parseMenu(menu_array, menu_items, 0);
			console.log(menu_array)
			*/
			var menu_items = {menu_items: jQuery("#menu_editor").jstree("get_json") };
			var menu_data = JSON.stringify(menu_items);
			//console.log(menu_data);
			jQuery("#menu_data").val(menu_data);
			jQuery("#menu_group_edit").trigger("submit")
			
			/* // this wasn't working so now i am putting it into a hidden form variable and doing a normal post.
			jQuery.ajax({
				url : "/hotcms/menu/save_menu",
				type : "POST",
				data : menu_data,
				success : function(data) {
					console.log('menu saved');
					console.log(data);					
				}
			});
			*/
		});
		
		/* //this is the old menu ajax that updated he code as you manipulated the menu (non-tree view control) 
		var sequence = 'menuItem=';
		jQuery.each(items, function() {
			sequence += jQuery(this).attr('id');
			sequence += '_';
		
		});
		sequence = sequence.substring(0, sequence.length - 1);
		
		//console.log(sequence);
		jQuery.ajax({
			url : "/hotcms/menu/ajax_sequence?" + sequence,
			type : "POST",
			context : document.body,
			success : function() {
				jQuery(this).addClass("done");
			}
		*/ 
});
</script>
	

<!--
<div class="table">
  <table id="tableCurrent" class="groupWrapper">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' ) ?></th>
        <th><?php echo lang( 'hotcms_actions' ) ?></th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($currentMenuItems as $row){?>
      <tr class="groupItem" id="<?php echo $row->id ?>">
        <td class="itemHeader"><?php echo $row->title ?></td>
        <td>
           <a href="<?php printf('menu/edit_menu_item/%s/%s', $row->id, $group_id)?>"><img src="asset/images/actions/edit.png" alt="Edit" /></a>
           <a onClick="return confirmDelete()" href="<?php printf('menu/delete_menu_item/%s/%s',$row->id, $group_id)?>"><img src="asset/images/actions/delete.png" alt="Delete" /></a>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
</div>
<br />
<hr/>
<br />
-->
<?php /*
 <div class="groupWrapper">
 
  
   <?php foreach ($currentMenuItems as $row){?>
         <div class="groupItem" id="<?php echo $row->id?>">
          <div class="itemHeader"><?php echo $row->title ?></div>
         </div>
   <?php } ?>   
 
</div>      
<div style="clear:both"></div>

<?php */ } ?>


