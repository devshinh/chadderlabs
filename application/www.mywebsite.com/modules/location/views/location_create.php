<div>
 <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8">
  <div class="row">       
   <?php echo form_error('name', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_location_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
   <?php echo form_input($name_input); ?>
  </div>
   <!--
  <div class="row">       
   <?php echo form_error('website', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_website' ).' '.lang( 'hotcms__colon' ), 'website');?>
   <?php echo form_input($website_input); ?>
  </div>
   -->
  <div class="row">       
   <?php echo form_error('main_email', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_email' ).' '.lang( 'hotcms__colon' ), 'main_email');?>
   <?php echo form_input($main_email_input); ?>
  </div>  
  <div class="row">       
   <?php echo form_error('main_phone', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_main_phone' ).' '.lang( 'hotcms__colon' ), 'main_phone');?>
   <?php echo form_input($main_phone_input); ?>
  </div>    
  <div class="row">       
   <?php echo form_error('toll_free_phone', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_phone_toll_free' ).' '.lang( 'hotcms__colon' ), 'toll_free_phone');?>
   <?php echo form_input($toll_free_phone_input); ?>
  </div>    
  <div class="row">       
   <?php echo form_error('main_fax', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_fax' ).' '.lang( 'hotcms__colon' ), 'main_fax');?>
   <?php echo form_input($main_fax_input); ?>
  </div>     
  <div class="row">
   <?php echo form_error('address_1', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_address' ).' 1'.lang( 'hotcms__colon' ), 'address_1');?>
   <?php echo form_input($address_1_input); ?>                     
  </div>    
  <div class="row">
   <?php echo form_error('address_2', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_address' ).' 2'.lang( 'hotcms__colon' ), 'address_2');?>
   <?php echo form_input($address_2_input); ?>                     
  </div>  
  <div class="row">
   <?php echo form_error('city', '<div class="error">','</div>');?> 
   <?php echo form_label(lang( 'hotcms_city' ).' '.lang( 'hotcms__colon' ), 'city');?>
   <?php echo form_input($city_input); ?>                     
  </div>    
  <div class="row">         
   <?php echo form_error('province', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_province' ).' '.lang( 'hotcms__colon' ), 'province');?>
   <?php echo form_input($province_input); ?>                     
  </div>  
  <div class="row">      
   <?php echo form_error('postal_code', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_postal_code' ).' '.lang( 'hotcms__colon' ), 'postal_code');?>
   <?php echo form_input($postal_code_input); ?>                     
  </div>   
  <div class="submit">
    <input type="submit" class="input red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
