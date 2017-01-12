<div class="module">
    <div class="row">
        <div class="control_buttons">
            <a class="red_button" href="<?php printf('/hotcms/%s', $module_url) ?>/type_create"><?php printf('%s', $add_new_text) ?></a>
        </div>
    </div>
    
<?php if (!empty( $organization_types )){ ?>
<div class="table">
  <table id="tableCurrent" class="table_sorter">
    <thead>
      <tr>
        <th id="sortable_name"><?php echo lang("hotcms_organization")." ".lang('hotcms_type')?></th>
        <th class="action"><?php echo lang( 'hotcms_edit' )?></th>
        <th class="action"><?php echo lang( 'hotcms_delete' )?></th>      
      </tr>
    </thead>
    <tbody>
    <?php foreach ($organization_types as $row) { ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td><?php echo $row->name; ?></td>
        <td>
           <a href="<?php printf('/hotcms/%s/type_edit/%s', $module_url, $row->id)?>"><div class="btn-edit"></div></a>
        </td>
        <td class="last">
           <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/type_delete/%s', $module_url, $row->id)?>"><div class="btn-delete"></div></a>
        </td>        </tr>
    <?php } ?>
    </tbody>
  </table>
  
</div>
<?php
  }
  else {
    echo '<p>' . lang('hotcms__message__no_results') . '</p>';
  }
?>
</div>
