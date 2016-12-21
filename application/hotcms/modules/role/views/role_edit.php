<div>
  <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $currentItem->id ?>/<?php echo $role_site_id ?>" method="post">
    <div class="row">
    <?php
      echo form_error('name', '<div class="error">','</div>');
      echo form_label(lang( 'hotcms_name' ) . ' ' . lang( 'hotcms__colon' ), 'name');
      echo form_input($form['name_input']);
    ?>
    </div>
    <div class="row">
    <?php
      echo form_error('description', '<div class="error">','</div>');
      echo form_label(lang( 'hotcms_description' ) . ' ' . lang( 'hotcms__colon' ), 'title');
      echo form_textarea($form['description_input']);
    ?>
    </div>
    <div class="row">
      <?php echo form_label(lang( 'hotcms_active' ) . ' ' . lang( 'hotcms__colon' ), 'active'); ?>
      <?php echo form_checkbox($form['active_input']); ?>
    </div>

    <div class="row">
      <h2><?php echo lang( 'hotcms_permission' ) . ' ' . lang( 'hotcms__colon' ); ?></h2>
    </div>
    <div class="row">
    <?php
    if ($currentItem->system == 1) {
      echo 'Super Admin has all permissions.';
    }
    else {
    ?>
      <table class="tablesorter" id="tableCurrent" cellpadding="5">
        <thead>
          <tr>
            <th width="80">Module</th>
            <th>Permission</th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $module = '';
          foreach ($form['permissions'] as $k => $v) {
            echo '<tr id="trData_' . $k . '"><td>';
            echo $permissions[$k]->category;
            echo '</td><td class="flexlabel">';
            echo form_checkbox($v);
            echo form_label($permissions[$k]->description, $v["id"]);
            echo '</td><td>';
            echo '</td><td>';
            echo form_error('permissions[' . $k . ']', '<div class="error">', '</div>');
            echo '</td></tr>';
          }
          ?>
        </tbody>
      </table>
    <?php } ?>
    </div>

    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
      <?php if ($currentItem->system == 0) { ?>
      <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
      <?php } ?>
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>

    <div class="row">
      <h2><?php echo lang( 'hotcms_user' ) . ' ' . lang( 'hotcms__colon' ); ?></h2>
    </div>
    <div class="table">
      <table id="tableCurrent" class="tablesorter">
        <thead>
          <tr>
            <th><?php echo lang( 'hotcms_name_first' )?></th>
            <th><?php echo lang( 'hotcms_name_last' )?></th>
            <th><?php echo lang( 'hotcms_name_user' )?></th>
            <th><?php echo lang( 'hotcms_email_address' )?></th>
            <th><?php echo lang( 'hotcms_status' )?></th>
            <th><?php echo lang( 'hotcms_last_login' )?></th>
            <th><?php echo lang( 'hotcms_date_created' )?></th>
            <th><?php echo lang( 'hotcms_edit' )?></th>
            <th><?php echo lang( 'hotcms_delete' )?></th>
          </tr>
        </thead>
        <tbody>
        <?php
        if (count($role_users) > 0) {
          foreach ($role_users as $row) { ?>
          <tr id="trUser_<?php echo $row->user_id ?>">
            <td><?php echo $row->first_name ?></td>
            <td><?php echo $row->last_name ?></td>
            <td>
            <?php if (!empty( $row->username )){ echo $row->username; } else { ?>&mdash;<?php } ?></td>
            <td>
            <?php if (!empty( $row->email )){ echo $row->email ?><a href="mailto:<?php echo $row->email ?>"><img class="link" src="asset/images/icon_link-out.png" alt="" /></a><?php }else{ ?>&mdash;<?php } ?>
            </td>
            <td>
              <?php if ($row->active == 1){
                printf('<a href="/hotcms/user/deactivate/%d">%s</a>', $row->user_id, lang( 'hotcms_active' ));
              }
              else {
                printf('<a href="/hotcms/user/activate/%d/%s">%s</a>', $row->user_id, $row->activation_code, lang( 'hotcms_inactive' ));
              } ?>
            </td>
            <td>
            <?php echo empty( $row->last_login ) ? '&mdash;' : date('Y-m-d H:i:s', $row->last_login); ?>
            </td>
            <td>
            <?php echo empty( $row->created_on ) ? '&mdash;' : date('Y-m-d H:i:s', $row->created_on); ?>
            </td>
            <td>
              <a href="<?php printf('/hotcms/user/edit/%s', $row->user_id)?>"><div class="btn-edit"></div></a>
            </td>
            <td class="last">
              <a onClick="return confirmDelete()" href="<?php printf('/hotcms/user/delete/%s', $row->user_id)?>"><div class="btn-delete"></div></a>
            </td>
          </tr>
        <?php
          }
        }
        else {
          echo '<tr><td colspan="9">There is no users with this role.</td></tr>';
        }
        ?>
        </tbody>
      </table>
      <a class="red_button" href="user/create">Add New User</a>
    </div>

  </form>
</div>