<div>
  <?=form_open($module_url."/create")?>
    <?=form_hidden($form["draw_step_hidden"])?>
    <div class="<?=($form["draw_type_hide"] ? "hidden " : "")?>row">
     <?php echo form_label(lang( 'hotcms_type' ).' '.lang( 'hotcms__colon' ), 'draw_type');?>
     <?php echo form_dropdown("draw_type", $form['draw_types_array'], $form['draw_type_value']); ?>
    </div>
     <div class="<?=($form["draw_custom_hide"] ? "hidden" : "")?>">
      <div class="row"><h4>Custom Draw</h4></div>
      <div class="row">
       <?php echo form_label('Start date:', 'datepicker_begining');?>
       <?php echo form_input($form['start_input']); ?>
      </div>
      <div class="row">       
       <?php echo form_label('End date:', 'datepicker_closing');?>
          <?php echo form_input($form['end_input']); ?>   
      </div>
<!--      <div class="row"> 
          <label>Site filter:</label>

      </div>
                <div id="site_jstree">
              <ul>
                  <li>All Sites
                      <ul>
                        <?php 
                        foreach($sites as $site) {
                            printf('<li id="site_id_%s">%s</li>',$site->id, $site->name);
                        }?>
                      </ul>
                  </li>
               </ul>     
          </div>-->
    </div>
    <div class="<?=($form["draw_monthly_hide"] ? "hidden" : "")?>">
      <div class="row"><h4>Monthly Draw</h4></div>
      <div class="row">
       <?php echo form_label(lang( 'hotcms_year' ).' '.lang( 'hotcms__colon' ), 'draw_monthly_year');?>
       <?php echo form_dropdown("draw_monthly_year", $form['draw_monthly_years_array'], $form['draw_monthly_year_value']); ?>
      </div>
      <div class="row">       
       <?php echo form_label(lang( 'hotcms_month' ).' '.lang( 'hotcms__colon' ), 'draw_monthly_month');?>
       <?php echo form_dropdown("draw_monthly_month", $form['draw_monthly_months_array'], $form['draw_monthly_month_value']); ?>
      </div>
    </div>
    <div class="<?=($form["draw_name_hide"] ? "hidden" : "")?>">
      <div class="row"><h4><?php echo ucfirst($form["draw_type_value"])?> Draw</h4></div>
      <div class="row">       
       <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'draw_name');?>
       <?php echo form_input($form['draw_name_input']); ?>
      </div>
      <div class="row">       
       <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'draw_description');?>
       <?php echo form_textarea($form['draw_description_input']); ?>
      </div>
      <div class="row">       
       <?php echo form_label(lang( 'hotcms_draw_winner_numer' ).' '.lang( 'hotcms__colon' ), 'draw_winner_numer');?>
       <?php echo form_input($form['draw_winner_numer_input']); ?>
      </div>
      <div class="row">
        <?php echo form_label('Eligible draws: ' .$number_of_eligible_draws);?>
      </div>
    </div>
    <div class="submit">
      <input type="submit" name="button_next" class="red_button" value="<?=lang('hotcms_next')?>" />
      <input type="submit" name="button_back" class="red_button" value="<?=lang('hotcms_back')?>" />
    </div>
  </form>
</div>
<!--<script type="text/javascript">

    jQuery('#site_jstree').jstree({

      'plugins' : ['themes'],
      'themes' : {
        'icons' : false
      }
    });
 
</script>-->