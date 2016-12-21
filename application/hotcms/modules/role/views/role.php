<div class="module">
<h2><a class="red_button" href="<?php printf('/hotcms/%s',$module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
<form method="post">
  <select id="site_select" name="site_select">
  <?php foreach ($sites as $row){ ?>
    <option value="<?php echo $row->id ?>" <?php if ($row->id == $site_id_for_roles){ ?> selected="selected"<?php } ?>>
      <?php echo $row->name ?> &mdash; <?php echo $row->domain; ?>
    </option>
  <?php } ?>
  </select>
    <input class="red_button" type="submit" value="Load roles"/>
</form>
<br />
<?php if (!empty( $roles )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' )?></th>
        <th><?php echo lang( 'hotcms_description' )?></th>
        <th><?php echo lang( 'hotcms_status' )?></th>
        <th><?php echo lang( 'hotcms_number_of_users' )?></th>
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th class="action"><?php echo lang( 'hotcms_edit' )?></th>
        <th class="action"><?php echo lang( 'hotcms_delete' )?></th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($roles as $row) { ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td><?php echo $row->name; ?></td>
        <td><?php echo $row->description; ?></td>
        <td class="<?php if (empty( $row->active )){ echo lang( 'hotcms_no' ); } else { echo lang( 'hotcms_yes' ); } ?>">
          <?php if (empty( $row->active )){
               echo lang( 'hotcms_inactive' );
             }
             else {
               echo lang( 'hotcms_active' );
             } ?>
        </td>
        <td><?php echo $row->users; ?></td>
        <td>
          <?php if (!empty( $row->create_date )){ echo $row->create_date; }else{ ?>&mdash;<?php } ?>
        </td>
        <td>
           <a href="<?php printf('/hotcms/%s/edit/%s/%s', $module_url, $row->id,$site_id_for_roles)?>"><div class="btn-edit"></div></a>
        </td>
        <td class="last">
        <?php if ($row->system == 0) { ?>
           <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id)?>"><div class="btn-delete"></div></a>
        <?php } ?>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
</div>
<?php } ?>
</div>
