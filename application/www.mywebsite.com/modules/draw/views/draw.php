<div class="module">
<h2><a class="red_button" href="<?php printf('/hotcms/%s',$module_url) ?>/create"><?php printf('Pick winner') ?></a></h2>
<?php if (!empty( $draws )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' )?></th>
        <th><?php echo 'note'?></th>
        <th><?php echo 'User ID'?></th>
        <th><?php echo lang( 'hotcms_screen_name' )?></th>
        <th><?php echo lang( 'hotcms_date_created' )?></th>

        <th class="action"><?php echo lang( 'hotcms_delete' )?></th>
      </tr>
    </thead>
    <tbody id="ui-sortable">
  <?php foreach ($draws as $row) { ?>
      <tr id="<?php echo $row->id ?>" class="groupItem">
        <td><?php echo $row->name; ?></td>
        <td><?php echo $row->note; ?></td>
        <td><?php echo $row->user_id; ?></td>
        <td><?php echo $row->user_info->screen_name; ?></td>
 
        <td>
          <?php echo $row->create_timestamp > 0 ? date($this->config->item('timestamp_format'), $row->create_timestamp) : '&mdash;'; ?>
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
