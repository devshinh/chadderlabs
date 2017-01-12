jQuery.noConflict();
jQuery( document ).ready( function() {

  jQuery(".tabs").tabs();

     
  jQuery(".ajax_submit_day_hour").click(function() {  
    
    var id = jQuery(this).attr('id');     

    // var ajax_url = "/hotcms/operation_hours/edit_hours/"+jQuery('#editFormDayHours_'+ id + ' .row_id').text()+"/"+jQuery('#editFormDayHours_'+ id + ' .connection_id').text()+"/"+jQuery('#editFormDayHours_'+ id + ' .module_back_url').text()+'/' + Math.random()*99999;
    var ajax_url = "/hotcms/operation_hours/edit_hours/" + Math.random()*99999;
    jQuery.post(ajax_url, jQuery('#editFormDayHours_'+ id).serialize(),
      function(data){
        try{
          var JSONobj = JSON.parse(data);

          if (JSONobj['messages'] > '') {
            alert(JSONobj['messages']);
          }
        }
        catch(e){
          alert("Error: "+e.description);
        }
      });

    return false;
  }); 
     
  jQuery('.hours_row:odd').css("background-color", "#CCD1CD");
     
  if (jQuery("#extra_fields_input").hasClass('hidden')){
    jQuery('.extra_fields').hide();
  }else{
    jQuery('.extra_fields').show();
  }
   
  jQuery("#extra_fields_input").click(function(){
    if(jQuery(this).hasClass('hidden')){
      jQuery(this).removeClass('hidden').addClass('show');
      jQuery('.extra_fields').css('display','block');
    }else{
      jQuery(this).removeClass('show').addClass('hidden');
      jQuery('.extra_fields').css('display','none');
    }
  });
   
  jQuery("#clear_button").click(function(){
    clear_form_elements(".operation_hours");
  });
   
  //set disabled inputs for "closed" days
  jQuery('.operation_hours .input .text').each(function(index) {
    if(jQuery(this).hasClass('disabled')){ 
      jQuery(this).attr('disabled','disabled');
    }
  });
  
  //enable/disable inputs when checkbox is clicked
  jQuery(".closebox").click(function(){     
    var arr = jQuery(this).attr('id').split('_');
    var id = arr[1];    
    if (jQuery(this).hasClass('disabled')) {
      jQuery(this).removeClass('disabled');
      jQuery('#from1_'+id).removeAttr('disabled').removeClass('disabled');
      jQuery('#from2_'+id).removeAttr('disabled').removeClass('disabled');
      jQuery('#to1_'+id).removeAttr('disabled').removeClass('disabled');
      jQuery('#to2_'+id).removeAttr('disabled').removeClass('disabled');
    }else{
      jQuery(this).addClass('disabled');
      jQuery('#from1_'+id).attr('disabled','disabled').addClass('disabled');
      jQuery('#from2_'+id).attr('disabled','disabled').addClass('disabled');
      jQuery('#to1_'+id).attr('disabled','disabled').addClass('disabled');
      jQuery('#to2_'+id).attr('disabled','disabled').addClass('disabled');
    }
  });
});

function clear_form_elements(ele) {
	 
  jQuery(ele).find(':input').each(function() {
    switch(this.type) {
      case 'password':
      case 'select-multiple':
      case 'select-one':
      case 'text':
      case 'textarea':
        jQuery(this).val('');
        break;
      case 'checkbox':
      case 'radio':
        this.checked = false;
    }
  });
}