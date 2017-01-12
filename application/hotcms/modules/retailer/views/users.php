<div class="module">
<div>
    <div class="row">
      <h1>Users list for <?php if (strcasecmp($table, "retailer") === 0) { echo strtolower(lang("hotcms_organization")); } else { echo strtolower(lang("hotcms_".$table)); }?></h1>
    </div>
  <div class="row">
  <?php
    if (strcasecmp($table, "retailer") === 0) {
    echo form_label(lang("hotcms_organization") . " " . lang( 'hotcms_name' ) . lang( 'hotcms__colon' ));
      echo $retailer->name;
    } else {
    echo form_label(lang("hotcms_".$table) . " " . lang( 'hotcms_name' ) . lang( 'hotcms__colon' ));
      echo $store->store_name;
    }
  ?>
  </div>
  <div class="row">
  <?php
    if (strcasecmp($table, "retailer") === 0) {
      echo form_label(lang('hotcms_country') . ' ' . lang('hotcms__colon'));
      echo $retailer->country;
    } else {
      echo form_label(lang("hotcms_province")."/".lang("hotcms_state")." ".lang('hotcms__colon'));
      echo $store->province_name;
    }
  ?>
  </div>
</div>
    <div class="row">
        <div class="control_buttons">
          <?php if (strcasecmp($table, "retailer") === 0) { ?>
<a href="/hotcms/<?=$module_url?>/index/<?=$index_page_num?>" class="red_button"><?=lang("hotcms_back")." to ".lang("hotcms_organization")." List"?></a>
          <?php } else { ?>
<a href="/hotcms/<?=$module_url?>/store_edit/<?=$filters["row_id"]?>" class="red_button"><?=lang("hotcms_back")." to ".lang("hotcms_store")?></a>
          <?php } ?>
        </div>
      
        <div id="filters_div">
          <form id="search_form" method="post">
          <?php
            echo form_hidden($form['hidden']);
          ?>
          <div class="search_bar">
            <div class="col">
            <?php
              echo form_label(lang('hotcms_search') . lang('hotcms__colon'), 'keyword');
              echo form_input($form['keyword_input']);
            ?>
            </div>
              <!--
            <div class="col">
            <?php
              echo form_label(lang('hotcms_status') . lang('hotcms__colon'), 'status');
              echo form_dropdown('status', $form['status_options'], $filters['status']);
            ?>
            </div>
            <div class="col">
            <?php
              echo form_dropdown('per_page', $form['per_page_options'], $filters['per_page']);
            ?>
            </div>
              -->
            <div class="col">
              <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_search' ); ?>" />
            </div>
          </div>
          </form>
        </div>
    </div>
<div class="clear"></div>
    <?php if (!empty($users)) { ?>
        <div class="table">
            <table id="tableCurrent" class="tablesorter">
                <thead>
                    <tr>
                        <th><?php echo lang('hotcms_name_first') ?></th>
                        <th><?php echo lang('hotcms_name_last') ?></th>
                        <th><?php echo lang('hotcms_screen_name') ?></th>
                        <th><?php echo lang('hotcms_email_address') ?></th>
                        <th><?php echo lang('hotcms_status') ?></th>
                        <th><?php echo lang('hotcms_last_login') ?></th>
                        <th><?php echo lang('hotcms_date_created') ?></th>
                        <th class="action"><?php echo lang('hotcms_edit') ?></th>
                        <th class="action"><?php echo lang('hotcms_delete') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $row) { ?>
                        <tr id="trData_<?php echo $row->user_id ?>">
                            <td>
                                <?php echo $row->first_name ?>
                            </td>
                            <td>
                                <?php echo $row->last_name ?>
                            </td>
                            <td>
                                <?php echo $row->screen_name ?>
                            </td>                            
                            <td>
                                <?php if (!empty($row->email)) { ?><?php echo $row->email ?><a href="mailto:<?php echo $row->email ?>"><img class="link" src="asset/images/icon_link-out.png" alt="" /></a><?php } else { ?>&mdash;<?php } ?>
                            </td>
                            <td>
                                <?php
                                if ($row->active == 1) {
                                    printf('<a href="/hotcms/%s/deactivate/%d">%s</a>', $module_url, $row->user_id, lang('hotcms_active'));
                                } else {
                                    printf('<a href="/hotcms/%s/activate/%d/%s">%s</a>', $module_url, $row->user_id, $row->activation_code, lang('hotcms_inactive'));
                                }
                                ?>
                            </td>
                <td><?php echo $row->last_login > 0 ? date('Y-m-d H:i:s', $row->last_login) : '&mdash;'; ?></td>
                <td><?php echo $row->created_on > 0 ? date('Y-m-d H:i:s', $row->created_on) : '&mdash;'; ?></td>

                            <td>
                                <a href="<?php printf('/hotcms/user/edit/%s',  $row->user_id) ?>"><div class="btn-edit"></div></a>
                            </td>
                            <td class="last">
                                <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->user_id) ?>"><div class="btn-delete"></div></a>
                            </td>
                        </tr>
                <?php } ?>
                </tbody>
            </table>
            <div id="pagination"><?php echo $pagination; ?></div>
            <div id="pagination_items">
                <form id="pagination_form" method="post">
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
