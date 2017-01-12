<div>
 <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8">
  <div class="row">       
   <?php 
   echo form_error('name', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
   <?php echo form_input($form['name_input']); ?>
  </div>
  <div class="row">       
   <?php echo form_error('note', '<div class="error">','</div>');?>
   <?php echo form_label( 'Note '.lang( 'hotcms__colon' ), 'title');?>
   <?php echo form_input($form['note_input']); ?>
  </div> 
     
     <div class="row">       
         <?php printf('Active contest entries for this period: %s', $curent_draws_sum);?>
     </div>
     
     
  <div class="submit">
      <?php if($curent_draws_sum > 0){ ?>
       <input type="submit" class="red_button" value="PICK WINNER" />
      <?php }else{?>
       <input type="submit" class="red_button" value="PICK WINNER" disabled />
      <?php }?>
    <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
