<div>
  <form action="/hotcms/<?php echo $module_url ?>/edit/<?php echo $current_item->slug ?>" method="post">
    <div class="row">       
      <?php echo form_error('name', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_name') . ' ' . lang('hotcms__colon'), 'name'); ?>
      <?php echo form_input($form['name_input']); ?>
    </div>
    <div class="row">       
      <?php echo form_error('email', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_email') . ' ' . lang('hotcms__colon'), 'email'); ?>
      <?php echo form_input($form['email_input']); ?>
    </div>
    <div class="row">       
      <?php echo form_error('phone', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_phone') . ' ' . lang('hotcms__colon'), 'phone'); ?>
      <?php echo form_input($form['phone_input']); ?>
    </div>     
    <div class="row">       
      <?php echo form_label(lang('hotcms_active') . ' ' . lang('hotcms__colon'), 'active'); ?>
      <?php echo form_checkbox($form['active_input']); ?> 
    </div>   
    <div class="row">       
      <?php if (!empty($locations_table)) { ?>
        <div class="table">   
          <table id="tableCurrent" class="tablesorter">
            <thead>
              <tr>
                <th><?php echo lang('hotcms_location_name') ?></th>
                <th><?php echo lang('hotcms_number_of_users') ?></th>
                <th><?php echo lang('hotcms_date_updated') ?></th>
                <th><?php echo lang('hotcms_date_created') ?></th>         
                <th class="action"><?php echo lang('hotcms_edit') ?></th>
                <th class="action"><?php echo lang('hotcms_delete') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($locations_table as $row) { ?>
                <tr id="trData_<?php echo $row->id ?>">
                  <td><?php echo $row->name ?></td>
                  <td><?php echo $row->users ?></td>
                  <td><?php echo (empty($row->update_date) ? '&mdash;' : date($this->config->item('timestamp_format'), $row->update_timestamp)); ?></td>
                  <td><?php echo date($this->config->item('timestamp_format'), $row->create_timestamp) ?></td>
                  <td>
                    <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id) ?>"><div class="btn-edit"></div></a>
                  </td>
                  <td class="last">
                    <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id) ?>"><div class="btn-delete"></div></a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } ?>
    </div>

    <div class="row">       
      <a href="/hotcms/<?php echo $module_url ?>/add_location/<?php echo $current_item->id ?>" class="red_button">Add new location</a>
    </div>       
    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
      <a href="/hotcms/<?php echo $module_url ?>/" class="red_button"><?php echo lang('hotcms_back') ?></a>

      <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url ?>/delete/<?php echo $current_item->id ?>" class="red_button"><?php echo lang('hotcms_delete') ?></a>

      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>
  </form>
</div>