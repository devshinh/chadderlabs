<?php $moduleConfig = $this->config->item('contact');?>
<?php echo validation_errors(); ?>
<script type="text/javascript"> 
 jQuery(document).ready(function(){
    jQuery("#addContact").validate();
    
  });
</script>
<div id="">
  <form action="/index.php/<?php echo $moduleConfig['module_url']?>/addContactTo<?php echo ucwords($con_name); ?>/<?php echo $con_id; ?>" method="post" accept-charset="UTF-8" id="addContact">
    <div class="input">
      <div class="panel panel_0">
        <div class="row">
         <label for="txtName"><span class="red">*</span> <?php echo lang( 'hotcms_name' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
         <input class="text required" id="txtName" value="<?php echo set_value( 'txtName', $this->input->post( 'txtName' ) ) ?>" maxlength="256" />
        </div>
        <div class="row">
         <label for="txtMainPhone"><span class="red">*</span> Main Phone<?php echo lang( 'hotcms__colon' ) ?></label>
         <input class="text" id="txtMainPhone" value="<?php echo set_value( 'txtMainPhone', $this->input->post( 'txtMainPhone' ) ) ?>" maxlength="256" />
        </div> 
        <div class="row">
         <label for="txtTollFreePhone">Phone (Toll Free)<?php echo lang( 'hotcms__colon' ) ?></label>
         <input class="text" id="txtTollFreePhone" value="<?php echo set_value( 'txtTollFreePhone', $this->input->post( 'txtTollFreePhone' ) ) ?>" maxlength="256" />
        </div>          
        <div class="row">
         <label for="txtEmail"><span class="red">*</span> Main Email<?php echo lang( 'hotcms__colon' ) ?></label>
         <input class="text" id="txtEmail" value="<?php echo set_value( 'txtEmail', $this->input->post( 'txtEmail' ) ) ?>" maxlength="256" />
        </div>           
        <div class="row">
         <label for="txtMainFax"><span class="red">*</span> Main Fax<?php echo lang( 'hotcms__colon' ) ?></label>
         <input class="text" id="txtMainFax" value="<?php echo set_value( 'txtMainFax', $this->input->post( 'txtMainFax' ) ) ?>" maxlength="256" />
        </div>         
        <div class="row">
          <label for="txtAddress_0"><span class="red">*</span> <?php echo lang( 'hotcms_address' ) ?> 1<?php echo lang( 'hotcms__colon' ) ?></label>
          <input class="text" id="txtAddress_0" value="<?php echo set_value( 'txtAddress_0', $this->input->post( 'txtAddress_0' ) ) ?>" maxlength="256" />
        </div>
        <div class="row">
          <label for="txtAddress_1"><?php echo lang( 'hotcms_address' ) ?> 2<?php echo lang( 'hotcms__colon' ) ?></label>
          <input class="text" id="txtAddress_1" value="<?php echo set_value( 'txtAddress_1', $this->input->post( 'txtAddress_1' ) ) ?>" maxlength="256" />
        </div>
        <div class="row">
          <label for="txtCity"><span class="red">*</span> <?php echo lang( 'hotcms_city' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
          <input class="text" id="txtCity" value="<?php echo set_value( 'txtCity', $this->input->post( 'txtCity' ) ) ?>" maxlength="256" />
        </div>
        <div class="row">
         <label for="txtStateProvince"><span class="red">*</span> <?php echo lang( 'hotcms_province' ) ?>/<?php echo lang( 'hotcms_state' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
         <input class="text" id="txtStateProvince" value="<?php echo set_value( 'txtStateProvince', $this->input->post( 'txtStateProvince' ) ) ?>" maxlength="30" />
        </div>
        <div class="row">
          <label for="txtPostalCode"><span class="red">*</span> <?php echo lang( 'hotcms_postal_zip_code' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
          <input class="text" id="txtPostalCode" value="<?php echo set_value( 'txtPostalCode', $this->input->post( 'txtPostalCode' ) ) ?>" maxlength="7" />
        </div>

      </div>
     
     <div class="submit">
       <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />

       <input type="hidden" id="hdnMode" value="insert" />
       <input type="hidden" id="hdnIDCurr" value="<?php echo set_value( 'hdnIDCurr', $this->input->post( 'hdnIDCurr' ) ) ?>" />
       <input type="hidden" id="hdnIDPrev" />      
     </div>
    </div>
  </form>
</div>