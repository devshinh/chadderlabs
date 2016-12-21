<div class="module">
<h2><a class="red_button" href="<?php printf('/hotcms/%s',$module_url) ?>"><?php printf('Back') ?></a></h2>
<?php if (!empty( $draws )){ ?>
<div class="row">
    <label>Draw Name:</label> <b><?php echo $draw_details->name; ?></b>
</div>
<div class="row">
    <label>Created by:</label> 
    <b><?php echo $draw_details->draw_creator->first_name;?> <?php echo $draw_details->draw_creator->last_name;?></b> (<?php echo date($this->config->item('timestamp_format'),$draw_details->create_timestamp); ?>)
</div>
<div class="row">
    <label>Last edited by:</label> 
    <b><?php echo $draw_details->draw_editor->first_name;?> <?php echo $draw_details->draw_editor->last_name;?></b> (<?php echo date($this->config->item('timestamp_format'),$draw_details->update_timestamp); ?>)
</div>
<?php if ($draw_details->type == 'custom') {?>
<div class="row">
    <label>Draw start time:</label> 
    <b><?php echo date($this->config->item('timestamp_format'),$draw_details->start); ?></b>
</div>
<div class="row">
    <label>Draw end time:</label> 
    <b><?php echo date($this->config->item('timestamp_format'),$draw_details->end); ?></b>
</div>
<?php }?>
<?php echo form_open($module_url."/edit_draw/".$draw_details->id);?>
<div class="row">
    <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'draw_description');?>
    <?php echo form_textarea($form['draw_description_input']); ?>
</div>
    <div class="">
      <input type="submit" name="button_next" class="red_button" value="<?php echo lang('hotcms_save')?>" />
    </div>
</form>

<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>       
        <th><?php echo 'User ID'?></th>
        <th><?php echo lang( 'hotcms_screen_name' )?></th>
        <th><?php echo lang( 'hotcms_email' )?></th>
        <th><?php echo lang( 'hotcms_verified' )?></th>
        <th><?php echo 'Confirmed Winner'?></th>
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th><?php echo lang( 'hotcms_edit' )?></th>
        <th class="action"><?php echo lang( 'hotcms_delete' )?></th>
      </tr>
    </thead>
    <tbody id="ui-sortable">
  <?php foreach ($draws as $row) { ?>
      <tr id="<?php echo $row->id ?>" class="groupItem">
        <td><a href="/hotcms/user/edit/<?php echo $row->user_id; ?>"><?php echo $row->user_id; ?></a></td>
        <td><a target="_blank" href="http://www.cheddarlabs.com/public-profile/<?php echo $row->screen_name; ?>"><?php echo $row->screen_name; ?></a></td>
        <td><?php echo $row->email; ?></td>
        <td> <?php if ($row->account_verified == 0){ ?>NO<?php }else{ ?>YES<?php } ?></td>
        <td class="<?php if (empty( $row->verified )){ ?>no<?php } else { ?>yes<?php } ?>">
          <?php if (empty( $row->verified )){ ?><?php }else{ ?>YES<?php } ?>
        </td>        
 
        <td>
          <?php echo $row->create_timestamp > 0 ? date($this->config->item('timestamp_format'), $row->create_timestamp) : '&mdash;'; ?>
        </td>
        <td>
           <a href="<?php printf('/hotcms/%s/edit_winner/%s', $module_url, $row->id)?>"><div class="btn-edit"></div></a>
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
