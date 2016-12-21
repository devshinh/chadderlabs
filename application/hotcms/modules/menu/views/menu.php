<h2>Recent Activity</h2>
<div class="module">
	<?php if (!empty($group_list)){	?>
	<div class="table">
		<table id="tableCurrent" class="tablesorter">
			<thead>
				<tr>
					<th><?php echo lang( 'hotcms_name' ); ?></th>
					<th><?php echo lang( 'hotcms_date_updated' ); ?></th>
					<th><?php echo lang( 'hotcms_date_created' );	?></th>
					<th><?php echo lang( 'hotcms_actions' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($group_list as $row){
				?>
				<tr id="trData_<?php echo $row->id ?>">
					<td><?php echo $row->menu_name
					?></td>
					<td><?php if (!empty( $row->update_date )){ echo $row->update_date; }else{
					?>&mdash;<?php }?></td>
					<td><?php if (!empty( $row->create_date )){ echo $row->create_date; }else{
					?>&mdash;<?php }?></td>
					<td>
            <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id)?>"><div class="btn-edit"></div></a>
            <!-- a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id)?>"><div class="btn-delete"></div></a -->
          </td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
	<?php }?>
</div>