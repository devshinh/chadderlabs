<div>

    <div class="tabs">
        <ul>
            <li><a href="#site-general" id="general"><i class="icon-file-alt"></i><span>General Settings</span></a></li>
            <li><a href="#site-balance" id="settings"><i class="icon-cog"></i></span><span>Point balance</span></a></li>
            <li><a href="#site-modules" id="content"><i class=""></i><span>Modules</span></a></li>
            <li><a href="#site-widgets" id="content"><i class=""></i><span>Widgets</span></a></li>
        </ul>
        <div id="site-general">    

            <form action="/hotcms/<?php echo $module_url ?>/edit/<?php echo $currentItem->id ?>" method="post">
                <div class="input">
                    <div class="row">  
                        <?php echo form_label(lang('hotcms_name') . ' ' . lang('hotcms__colon'), 'name'); ?>
                        <?php echo form_input($form['name_input']); ?>
                    </div>
                    <div class="row">
                        <?php echo form_label(lang('hotcms_url') . ' ' . lang('hotcms__colon'), 'url'); ?>
                        <?php echo form_input($form['url_input']); ?>         
                    </div>
                    <div class="row">
                        <?php echo form_label('Upload path ' . lang('hotcms__colon'), 'path'); ?>
                        <?php echo form_input($form['path_input']); ?>         
                    </div>   
                    <div class="row">
                        <?php echo form_label('Theme ' . lang('hotcms__colon'), 'theme'); ?>
                        <?php echo form_input($form['theme_input']); ?>         
                    </div>
                    <div class="row">
                        <?php echo form_label(lang('hotcms_primary_site') . ' ' . lang('hotcms__colon'), 'primary'); ?>
                        <?php echo form_checkbox($form['primary_input']); ?> 
                    </div>
                    <div class="row">
                        <?php echo form_label(lang('hotcms_active') . ' ' . lang('hotcms__colon'), 'active'); ?>
                        <?php echo form_checkbox($form['active_input']); ?> 
                    </div>
                    <div class="row">
                        <?php echo form_label(lang('hotcms_hidden') . ' ' . lang('hotcms__colon'), 'hidden_site'); ?>
                        <?php echo form_checkbox($form['hidden_site_input']); ?> 
                    </div>         

                    <div id="site_image_div">
                        <label>Site Image</label>
                        <div id="site_image">
                            <?php
                            if (!empty($siteItems->items['image'])) {
                                echo $siteItems->items['image']->full_html;
                            }
                            ?>
                        </div>


                        <a href="<?php echo $currentItem->site_image_id; ?>" class="red_button site_image_link" data-id="<?php echo $currentItem->id ?>">Choose</a>
                        <input type="hidden" name="site_image_id" id="site_image_id" value="<?php echo $currentItem->site_image_id; ?>" />
                    </div>  

                </div>
                <div class="submit">
                    <input type="submit" class="red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
                    <a href="/hotcms/<?php echo $module_url ?>/" class="red_button"><?php echo lang('hotcms_back') ?></a>

                    <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url ?>/delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang('hotcms_delete') ?></a>

                    <?php echo form_hidden('hdnMode', 'edit_settings') ?>
                </div>
            </form>

            <div id="site-image-form" title="Site Image">
            </div>    
        </div>
        <div id="site-balance">
            <form action="/hotcms/<?php echo $module_url ?>/edit/<?php echo $currentItem->id ?>" method="post">
                <div class="row">
                    <?php echo form_label(lang("hotcms_deposit") . " " . lang('hotcms_point') . lang('hotcms__colon'), 'points'); ?>
                    <?php echo form_input($form['point_input']); ?>         
                </div>
                <div class="row">
                    <?php echo form_label(lang("hotcms_deposit") . " " . lang('hotcms_method') . lang('hotcms__colon'), 'method'); ?>
                    <?php echo form_input($form['method_input']); ?>         
                </div>
                <div class="row">
                    <?php echo form_label(lang("hotcms_deposit") . " " . lang('hotcms_cost') . lang('hotcms__colon'), 'cost'); ?>
                    <?php echo form_input($form['cost_input']); ?>         
                </div>
                <div class="row">
                    <?php echo form_label(lang("hotcms_realtime") . " " . lang("hotcms_current") . " " . lang('hotcms_balance') . lang('hotcms__colon')); ?>
                    <?php echo form_hidden("balance_before", $currentItem->balance); ?>
                    <?php
                    echo "<span";
                    if ($currentItem->balance < 0) {
                        echo ' class="red"';
                    }
                    echo ">" . $currentItem->balance . "</span>";
                    ?>         
                </div>  
                <div class="submit">
                    <input type="submit" class="red_button" value="ADD POINTS" />
                    <a href="/hotcms/<?php echo $module_url ?>/" class="red_button"><?php echo lang('hotcms_back') ?></a>

                    <?php echo form_hidden('hdnMode', 'edit_balance') ?>
                </div>                
            </form>
            <?php if (!empty($deposit_history)) { ?>
                <div class="table">
                    <table id="tableCurrent" class="tablesorter">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Points</th>
                                <th>Balance before deposit</th>
                                <th>Method</th>
                                <th>Cost</th>
                                <th><?php echo lang('hotcms_date_created') ?></th>
                                <th class="action"><?php echo lang('hotcms_delete') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deposit_history as $row) { ?>
                                <tr id="trData_<?php echo $row->id ?>">
                                    <td>
                                        <?php echo $row->id ?>
                                    </td>
                                    <td>
                                        <?php echo $row->points ?>
                                    </td>        
                                    <td>
                                        <?php echo $row->balance_before ?>
                                    </td>          
                                    <td>
                                        <?php echo $row->method ?>
                                    </td>        
                                    <td>
                                        <?php echo $row->cost ?>
                                    </td>  
                                    <td>
                                        <?php
                                        if (!empty($row->deposit_timestamp)) {
                                            echo date($this->config->item('timestamp_format'), $row->deposit_timestamp);
                                        } else {
                                            ?>&mdash;<?php } ?>
                                    </td>          
                                    <td class="last">
                                        <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete_balance/%s/%s', $module_url, $row->id, $currentItem->id) ?>"><div class="btn-delete"></div></a>
                                    </td>                                    

                                </tr>
    <?php } ?>
                        </tbody>
                    </table>
                </div>
<?php } ?>    
        </div>   
        <div id="site-modules">

            <?php if (!empty($site_modules)) { ?>
                <div class="table">
                    <table id="tableCurrent" class="tablesorter">
                        <thead>
                            <tr>
                                <th style="width:30px;">ID</th>
                                <th>Name</th>
                                <th style="width:30px;">Active</th>
                                <th><?php echo lang('hotcms_date_updated') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($site_modules as $row) { ?>
                                <tr id="trData_<?php echo $row->id ?>">
                                    <td style="width:30px;">
                                        <?php echo $row->id ?>
                                    </td>
                                    <td>
                                        <?php echo $row->name ?>
                                    </td>        
                                    <td style="width:30px;">
                                           <?php
                                             echo '<div class="checkbox">';
                                             $checked = ($row->active==1?TRUE:FALSE);
                                             echo form_checkbox($row->id,'accept',$checked);
                                             //echo form_label($permission["id"], $permission["value"]);
                                             echo '</div>';
                                           
                                           ?>
                                                      
                                    </td>          
                                    <td class="last">
                                        <?php
                                        if (!empty($row->update_date)) {
                                            echo $row->update_date;
                                        } else { echo '&mdash;';} ?>
                                    </td>                                           

                                </tr>
    <?php } ?>
                        </tbody>
                    </table>
                </div>
<?php } ?>               
        </div>
        <div id="site-widgets">
            
        </div>        
    </div>
