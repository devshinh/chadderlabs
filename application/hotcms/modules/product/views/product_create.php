<div>
    <form action="/hotcms/<?php echo $module_url ?>/create" method="post" accept-charset="UTF-8">
        <div class="row">       
            <?php echo form_error('name', '<div class="error">', '</div>'); ?>
            <?php echo form_label('<span class="red">*</span> ' . lang('hotcms_name') . ' ' . lang('hotcms__colon'), 'name'); ?>
            <?php echo form_input($name_input); ?>
        </div>
        <div class="row">         
            <?php echo form_error('description', '<div class="error">', '</div>'); ?>
            <?php echo form_label('<span class="red">*</span> ' . lang('hotcms_description') . ' ' . lang('hotcms__colon'), 'description'); ?>
            <?php echo form_textarea($description_input); ?>                     
        </div> 
        <div class="row">         
            <?php echo form_error('category', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_category') . ' ' . lang('hotcms__colon'), 'category'); ?>
            <?php echo form_dropdown('category', $categories, $this->input->post('category')); ?>                     
        </div>        
        <div class="row">       
            <?php echo form_error('price_input', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_price') . ' ' . lang('hotcms__colon'), 'price'); ?>
            <?php echo form_input($price_input); ?>
        </div>   
        <div class="row">       
            <?php echo form_error('stock_input', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_stock') . ' ' . lang('hotcms__colon'), 'stock'); ?>
            <?php echo form_input($stock_input); ?>
        </div>
        <div class="row">       
            <?php echo form_error('featured_image_id', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_featured_image_id') . ' ' . lang('hotcms__colon'), 'featured_image_id'); ?>
            <?php echo form_input($featured_image_id_input); ?>
        </div>        
        <div class="row">
            <?php echo form_label(lang('hotcms_active') . ' ' . lang('hotcms__colon'), 'active'); ?>
            <?php echo form_checkbox($active_input); ?> 
        </div>    
        <div class="submit">
            <input type="submit" class="input red_button"  value="<?php echo lang('hotcms_save') ?>" />
            <a href="/hotcms/<?php echo $module_url ?>/" class="red_button"><?php echo lang('hotcms_back') ?></a>
            <?php echo form_hidden('hdnMode', 'insert') ?>
        </div>
    </form>
</div>
