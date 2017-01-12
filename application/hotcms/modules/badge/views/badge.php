<div class="module">
<h2><a class="red_button" href="<?php printf('/hotcms/%s',$module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
<?php if (!empty( $badges )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' )?></th>
        <th><?php echo lang( 'hotcms_description' )?></th>
        <th><?php echo lang( 'hotcms_status' )?></th>
        <th><?php echo lang( 'hotcms_date_updated' )?></th>
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th class="action"><?php echo lang( 'hotcms_edit' )?></th>
        <th class="action"><?php echo lang( 'hotcms_delete' )?></th>
      </tr>
    </thead>
    <tbody id="ui-sortable">
  <?php foreach ($badges as $row) { ?>
      <tr id="<?php echo $row->id ?>" class="groupItem">
        <td><?php echo $row->name; ?></td>
        <td><?php echo $row->description; ?></td>
        <td><?php echo $row->status; ?></td>
        <td>
          <?php echo $row->update_timestamp > 0 ? date($this->config->item('timestamp_format'), $row->update_timestamp) : '&mdash;'; ?>
        </td>        
        <td>
          <?php echo $row->create_timestamp > 0 ? date($this->config->item('timestamp_format'), $row->create_timestamp) : '&mdash;'; ?>
        </td>
        <td>
           <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id)?>"><div class="btn-edit"></div></a>
        </td>
        <td class="last">
           <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id)?>"><div class="btn-delete"></div></a>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
            <div id="pagination"><?php echo $pagination; ?></div>
            <div id="pagination_items">
                <form id="pagination_form" method="post" action="<?php echo $module_url?>/index">
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
<?php } ?>
</div>
