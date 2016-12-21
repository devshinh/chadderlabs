<div>
    <div class="tabs">
        <ul>
            <li><a href="#retailer-info" id="general"><span id="g"></span><span>General</span></a></li>
            <li><a href="#retailer-hq" id="settings"><span id="s"></span><span>HQ</span></a></li>
        </ul>  
        <div id="retailer-info">
            <form action="/hotcms/<?php echo $module_url ?>/edit/<?php echo $currentItem->id ?>" method="post">
                <div class="row">
                    <?php
                    echo form_error('name', '<div class="error">', '</div>');
                    echo form_label(lang('hotcms_organization') . " " . lang('hotcms__colon'), 'name');
                    echo form_input($form['name_input']);
                    ?>
                </div>
                <div class="row">
                    <?php echo form_error('website', '<div class="error">', '</div>'); ?>
                    <?php echo form_label(lang('hotcms_website') . ' ' . lang('hotcms__colon'), 'website'); ?>
                    <?php echo form_input($form['website_input']); ?>
                </div>          
                <div class="row">
                    <?php
                    echo form_error('country_code', '<div class="error">', '</div>');
                    echo form_label(lang('hotcms_country') . ' ' . lang('hotcms__colon'), 'country_code');
                    echo form_dropdown('country_code', $form['country_code_options'], $selected_country);
                    ?>
                </div>
                <div class="row">
                    <?php echo form_label(lang('hotcms_status') . ' ' . lang('hotcms__colon')); ?>
                    <?php echo form_radio($form['status_pending']); ?>
                    <label for="status_pending" style="display:inline-block;margin-left:5px">Pending</label>
                    <?php echo form_radio($form['status_confirmed']); ?>
                    <label for="status_confirmed" style="display:inline-block;margin-left:5px">Confirmed</label>
                    <?php echo form_radio($form['status_closed']); ?>
                    <label for="status_closed" style="display:inline-block;margin-left:5px">Closed</label>
                </div>

                <?php if (isset($categories) && count($categories) > 0) { ?>
                    <div class="row">
                        <?php echo form_label(lang('hotcms_category') . ' ' . lang('hotcms__colon'), 'categories'); ?>
                        <?php echo form_error('categories', '<div class="error">', '</div>'); ?>
                        <?php
                        foreach ($categories as $category) {
                            echo '<div class="checkbox">';
                            echo form_checkbox($category);
                            echo form_label($category["id"], $category["id"]);
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <?php
                }
                if (isset($types) && count($types) > 0) {
                    ?>
                    <div class="row">
                        <?php echo form_label(lang('hotcms_types') . " " . lang('hotcms__colon'), 'types'); ?>
                        <?php echo form_error('types', '<div class="error">', '</div>'); ?>
                        <?php
                        foreach ($types as $type) {
                            echo '<div class="checkbox">';
                            echo form_checkbox($type);
                            echo form_label($type["id"], $type["id"]);
                            echo '</div>';
                        }
                        ?>
                    </div>
                <?php } ?>      

                <div class="row" id="retailer_logo_div">
                    <label><?= lang("hotcms_organization") ?>'s logo</label>
                    <div id="logo_image">
                        <?php
                        if (!empty($currentItem->logo)) {
                            echo $currentItem->logo->full_html;
                        }
                        ?>
                    </div>


                    <a href="<?php echo $currentItem->logo_image_id; ?>" class="red_button logo_image_link" data-id="<?php echo $currentItem->id ?>">Choose</a>
                    <input type="hidden" name="logo_image_id" id="logo_image_id" value="<?php echo $currentItem->logo_image_id; ?>" />
                </div>       

                <div class="submit">
                    <input type="submit" class="red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
                    <a href="/hotcms/<?php echo $module_url ?>/index/<?php echo $index_page_num; ?>" class="red_button"><?php echo lang('hotcms_back') ?></a>
                    <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url ?>/delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang('hotcms_delete') ?></a>
                    <?php echo form_hidden('hdnMode', 'edit') ?>
                </div>
            </form>
            <?php /**
              <div class="row">
              <label for="retailer_list">Duplicated: </label>
              <select name="retailer_list">
              <option value="0" selected>--select <?=lang("hotcms_organization")?>--</option>
              <?php
              foreach ($retailers as $r ){
              printf('<option value="%s">%s</option>',$r->id, $r->name);
              }
              ?>
              </select>
              </div>
             */ ?>
            <div id="logo-image-form" title="Retailer Logo Image">
            </div>  

        </div>
        <div id="retailer-hq">
            <?php
            if ($form_locations) {
                foreach ($form_locations as $form_location) {
                    echo $form_location;
                }
            }
            ?>            

        </div>        
    </div>