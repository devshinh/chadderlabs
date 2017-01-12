<div id="">
  <form action="contact/add_contact_from_shipping" method="POST" id="new_contact_from_shipping">
    <div class="input">
      <div class="panel panel_0">
        <div class="row-fluid">
         <label for="contact_name"><span class="red">*</span> <?php echo lang( 'hotcms_name' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
         <?php echo form_input($contact_name);?>
        </div>
        <div class="row-fluid">
          <label for="address_1"><span class="red">*</span> <?php echo lang( 'hotcms_address' ) ?> 1<?php echo lang( 'hotcms__colon' ) ?></label>
          <?php echo form_input($address_1);?>
        </div>
        <div class="row-fluid">
          <label for="address_2"><?php echo lang( 'hotcms_address' ) ?> 2<?php echo lang( 'hotcms__colon' ) ?></label>
          <?php echo form_input($address_2);?>
        </div>
        <div class="row-fluid">
          <label for="city"><span class="red">*</span> <?php echo lang( 'hotcms_city' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
          <?php echo form_input($city);?>
        </div>
        <div class="row-fluid">
          <label for="province"><span class="red">*</span> <?php echo lang( 'hotcms_province' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
          <?php echo form_input($province);?>
        </div>
        <div class="row-fluid">
          <label for="postal"><span class="red">*</span> <?php echo lang( 'hotcms_postal_code' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
          <?php echo form_input($postal);?>
        </div>

      </div>
     
     <div class="submit">
       <input type="submit" class="btn btn-primary btn-large" value="<?php echo lang( 'hotcms_save' ) ?>" />
       <?php echo form_input($user_id);?>
     </div>
    </div>
  </form>
</div>