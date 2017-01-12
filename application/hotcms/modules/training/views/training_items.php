<div class="module">
<?php if (!empty( $items )) { ?>
<div class="row">
        <div class="control_buttons">
            <a class="red_button" href="<?php printf('/hotcms/%s', $module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a>
        </div>
        <div id="filters_div">
            <form id="search_form" method="post">
                <?php
                echo form_hidden($form['hidden']);
                ?>
                <div class="search_bar">

                    <?php
                    echo form_label(lang('hotcms_search') . lang('hotcms__colon'), 'keyword');
                    echo form_input($form['keyword_input']);
                    ?>


                    <input type="submit" class="red_button" value="<?php echo lang('hotcms_search'); ?>" />

                </div>
            </form>
        </div>
    </div>    
<div class="table">
  <table id="tableCurrent" class="table_sorter">
    <thead>
      <tr>
        <th id="sortable_title" class="sortable 
            <?php if ($filters['sort_by'] == 'title') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang( 'hotcms_title' )?></th>
        <th id="sortable_category_id" class="sortable 
            <?php if ($filters['sort_by'] == 'category_id') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang( 'hotcms_category' )?></th>
        <th id="sortable_create_timestamp" class="sortable 
            <?php if ($filters['sort_by'] == 'create_timestamp') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang( 'hotcms_date_created' )?></th>
        <th id="sortable_update_timestamp" class="sortable 
            <?php if ($filters['sort_by'] == 'update_timestamp') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang( 'hotcms_date_updated' )?></th>
        <th id="sortable_editor_id" class="sortable 
            <?php if ($filters['sort_by'] == 'editor_id') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang( 'hotcms_author' )?></th>
        <th id="sortable_status" class="sortable 
            <?php if ($filters['sort_by'] == 'status') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang( 'hotcms_status' )?></th>
        <th><?php echo lang( 'hotcms_edit' )?></th>
        <th><?php echo lang( 'hotcms_delete' )?></th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($items as $row) { ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td class="title"><?php echo $row->title ?></td>
        <td class="title"><?php echo $row->category_name ?></td>
        <td><?php echo $row->create_timestamp > 0 ? date($this->config->item('timestamp_format'), $row->create_timestamp) : '&mdash;'; ?></td>
        <td><?php echo $row->update_timestamp > 0 ? date($this->config->item('timestamp_format'), $row->update_timestamp) : '&mdash;'; ?></td>
        <td><?php echo $row->username ?></td>
        <td class="<?php if ($row->status == 1) { echo lang( 'hotcms_yes' ); } else { echo lang( 'hotcms_no' ); } ?>">
        <?php
        switch ($row->status) {
          case 0: echo lang( 'hotcms_inactive' );
            break;
          case 1: echo lang( 'hotcms_active' );
            break;
        }
        ?>
        </td>
        <td>
          <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id)?>"><div class="btn-edit"></div></a>
        </td>
        <td  class="last">
          <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id)?>"><div class="btn-delete"></div></a>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
  <div id="pagination"><?php echo $pagination; ?></div>
  <div id="pagination_items">
      <form id="pagination_form" method="post" action="<?php echo $module_url ?>/index">
          <?php
          echo form_hidden($form['hidden']);
          ?>        
          <?php
          $id = 'id="per_page_select"';
          echo form_dropdown('per_page', $form['per_page_options'], $filters['per_page'], $id);
          ?>
      </form>
  </div>
</div>
<?php }
  else {
    echo 'No items were found.';
  }
?>
</div>
