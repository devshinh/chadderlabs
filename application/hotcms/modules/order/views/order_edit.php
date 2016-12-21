<div>
  <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $currentItem->id ?>" method="post">
  <div class="row">
      Order #:
   <?php print($currentItem->id); ?>
  </div>
  <div class="row">
      Order by user:
   <?php print($currentItem->user_id); ?>
  </div>
  <div class="row">
  <?php
    echo form_error('order_status_options', '<div class="error">', '</div>');
    echo form_label('Order Status ' . lang('hotcms__colon'), 'order_status_options');
    echo form_dropdown('order_status_options', $form['order_status_options'], $currentItem->order_status);
  ?>
  </div>      
<?php  if(!empty($form['fedex_number'])){?>
  <div class="row">
  <?php
    echo form_error('fedex_number', '<div class="error">', '</div>');
    echo form_label('Fedex number' . lang('hotcms__colon'), 'fedex_number');
    echo form_input($form['fedex_number']);
  ?>
  </div>        
      
<?php } ?>
  <div class="row">
  SHIPPING INFO:      
  </div>
  <div class="row">
  <?php
    echo form_error('shipping_firstname', '<div class="error">', '</div>');
    echo form_label('First '.lang( 'hotcms_name' ) . ' ' . lang( 'hotcms__colon' ), 'shipping_firstname');
    echo form_input($form['shipping_firstname']);
  ?>
  </div>      
  <div class="row">
  <?php
    echo form_error('shipping_lastname', '<div class="error">', '</div>');
    echo form_label('Last '.lang( 'hotcms_name' ) . ' ' . lang( 'hotcms__colon' ), 'shipping_lastname');
    echo form_input($form['shipping_lastname']);
  ?>
  </div>      
  <div class="row">
  <?php
    echo form_error('shipping_street1', '<div class="error">', '</div>');
    echo form_label('Street 1' . lang( 'hotcms__colon' ), 'shipping_street1');
    echo form_input($form['shipping_street1']);
  ?>
  </div>         
  <div class="row">
  <?php
    echo form_error('shipping_street2', '<div class="error">', '</div>');
    echo form_label('Street 2' . lang( 'hotcms__colon' ), 'shipping_street2');
    echo form_input($form['shipping_street2']);
  ?>
  </div> 
  <div class="row">
  <?php
    echo form_error('shipping_city', '<div class="error">', '</div>');
    echo form_label(lang( 'hotcms_city' ) .' '. lang( 'hotcms__colon' ), 'shipping_city');
    echo form_input($form['shipping_city']);
  ?>
  </div>       
  <div class="row">
  <?php
    echo form_error('shipping_province', '<div class="error">', '</div>');
    echo form_label(lang( 'hotcms_province' ) .' '. lang( 'hotcms__colon' ), 'shipping_province');
    echo form_input($form['shipping_province']);
  ?>
  </div>  
  <div class="row">
  <?php
    echo form_error('shipping_postal', '<div class="error">', '</div>');
    echo form_label(lang( 'hotcms_postal_code' ) .' '. lang( 'hotcms__colon' ), 'shipping_postal');
    echo form_input($form['shipping_postal']);
  ?>
  </div>     
  <div class="row">
  <?php
    echo form_error('shipping_phone', '<div class="error">', '</div>');
    echo form_label(lang( 'hotcms_phone' ) .' '. lang( 'hotcms__colon' ), 'shipping_phone');
    echo form_input($form['shipping_phone']);
  ?>
  </div>    
  <div class="row">
  <?php
    echo form_error('shipping_email', '<div class="error">', '</div>');
    echo form_label(lang( 'hotcms_email' ) .' '. lang( 'hotcms__colon' ), 'shipping_email');
    echo form_input($form['shipping_email']);
  ?>
  </div>  
  <div class="row">
  <?php
    echo form_error('shipping_instruction', '<div class="error">', '</div>');
    echo form_label('Shipping instructions '. lang( 'hotcms__colon' ), 'shipping_instruction');
    echo form_textarea($form['shipping_instruction']);
  ?>
  </div>            
      
  <div class="row">
      Order Items:
      <?php foreach($currentItem->items as $item){?>
      <div class="row">
          <?php printf('%sx %s (%s points)',$item->qty,$item->product_name,$item->price);?>
      </div>
      <?php };?>
  </div>
  
<div class="row">
    <a href="/hotcms/<?php echo $module_url?>/receipt/<?php echo $currentItem->id;?>" target='_blank' class="red_button">Print Receipt</a>
</div>

  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/index/<?php echo $index_page_num; ?>" class="red_button"><?php echo lang('hotcms_back') ?></a>
    <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
    <?php echo form_hidden('hdnMode', 'edit') ?>
  </div>
  </form>
</div>