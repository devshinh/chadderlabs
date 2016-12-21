<div class="module">
<h2><a class="red_button" href="<?="/hotcms/".$module_url."/create"?>"><?php printf('Create new draw') ?></a></h2>
<?php if (!empty( $draws )){ ?>
<div class="table">
  <!--<table id="tableCurrent" class="tablesorter">-->
  <table class="tablesorter">
    <thead>
      <tr>
        <th><?=lang("hotcms_name")?></th>
        <th><?=lang("hotcms_type")?></th>
        <th><?=lang("hotcms_draw_winners")?></th>
        <th><?=lang("hotcms_month")?></th>
        <th><?=lang("hotcms_year")?></th>
        <th><?=lang("hotcms_date_created")?></th>
        <th><?=lang("hotcms_open")?></th>
        <th><?php echo lang("hotcms_delete")?></th>
      </tr>
    </thead>
    <tbody id="ui-sortable">
  <?php foreach ($draws as $row) { ?>
      <tr id="<?=$row->id?>" class="groupItem">
        <td><?=$row->name?></td>
        <td><?=$row->type?></td>
        <td><?=$row->number_of_winners?></td>
        <td><?=date("F", mktime(0, 0, 0, $row->monthly_month, 13))?></td>
        <td><?=$row->monthly_year?></td>
        <td><?=date($this->config->item('timestamp_format'), $row->create_timestamp)?>
        </td>
        <td>
           <a class="red_button" href="<?php printf('/hotcms/%s/list_winners/%s', $module_url, $row->id)?>"><?=lang("hotcms_open")?></a>
        </td>
        <td class="last">
           <a onClick="return confirmDelete()" class="red_button" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id)?>"><?php echo lang("hotcms_delete")?></a>
        </td>        
      </tr>
  <?php } ?>
    </tbody>
  </table>
            <div id="pagination"><?php echo $pagination; ?></div>
            <div id="pagination_items">
                <form id="pagination_form" method="post" action="<?php echo $module_url?>/index">
                    <?=form_hidden($form['hidden']);?>        
                    <?php
                    $id = 'id="per_page_select"';
                    echo form_dropdown('per_page', $form['per_page_options'], $filters['per_page'], $id);
                    ?>
                </form>
            </div>    
</div>
<?php } ?>
</div>
