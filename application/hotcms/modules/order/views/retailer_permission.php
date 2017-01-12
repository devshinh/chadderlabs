<div class="module">
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
    <div class="col">
    <?php
      echo form_label(lang('hotcms_country') . lang('hotcms__colon'), 'country_code');
      echo form_dropdown('country_code', $form['country_code_options'], $filters['country']);
    ?>
    </div>
    <div class="col">
    <?php
      echo form_dropdown('per_page', $form['per_page_options'], $filters['per_page']);
    ?>
    </div>
    <div class="col">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_search' ); ?>" />
    </div>
  </div>
  </form>
</div>
<?php if (!empty( $retailers )){ ?>
<div class="table">
  <table id="tableCurrent" class="table_sorter">
    <thead>
      <tr>
        <th id="sortable_name" class="sortable<?php if ($filters['sort_by'] == 'name') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_retailer')?></th>
        <th id="sortable_country" class="sortable<?php if ($filters['sort_by'] == 'country') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_country')?></th>
        <th id="sortable_stores" class="sortable<?php if ($filters['sort_by'] == 'stores') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_number_of_locations')?></th>
        <th id="sortable_users" class="sortable<?php if ($filters['sort_by'] == 'users') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_number_of_users')?></th>
        <th class="action"><?php echo lang('hotcms_status')?></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($retailers as $row) {
      if ($row->role_id > 0 && in_array($row->role_id, $retailer_roles)) {
        $row->access_id = array_search($row->role_id, $retailer_roles);
      }
      else {
        $row->access_id = 'N';
      }
    ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td><?php echo $row->name; ?></td>
        <td><?php echo $row->country; ?></td>
        <td><?php echo $row->stores; ?></td>
        <td><?php echo $row->users; ?></td>
        <td class="last">
        <?php
          echo form_dropdown('access', $form['access_options'], $row->access_id,  'id="' . $row->id . '" class="retailer_access_selector"');
        ?>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <div id="pagination"><?php echo $pagination; ?></div>
</div>
<?php
  }
  else {
    echo '<p>' . lang('hotcms__message__no_results') . '</p>';
  }
?>
</div>
