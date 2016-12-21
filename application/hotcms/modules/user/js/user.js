jQuery.noConflict();
jQuery( document ).ready( function() {

   jQuery(".tabs").tabs();


   jQuery(".ajax_submit_contact").click(function() {  
    
     var id = jQuery(this).attr('id');     

    var ajax_url = "/hotcms/contact/edit_contact/"+jQuery('#editFormContact_'+ id + ' .contact_id').text()+"/"+jQuery('#editFormContact_'+ id + ' .connection_id').text()+"/"+jQuery('#editFormContact_'+ id + ' .module_back_url').text()+'/' + Math.random()*99999;
    jQuery.post(ajax_url, jQuery('#editFormContact_'+ id).serialize(),
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
     
     jQuery("#add_new_contact").click(function() {
       jQuery("#add_new_contact_form").toggle();
     
     });
     jQuery("#hide_contact_name_form").click(function() {
      jQuery("#add_new_contact_form").hide();
     });
     
    jQuery("#country_code").change(function() {
        var country_code = jQuery('#country_code option:selected');
        if (country_code.attr('value') !== '') {
            //reset locations
            jQuery('select[name=retailer_id] option').remove();
            jQuery('select[name=store_id] option').remove();
            var ajax_url = "/hotcms/ajax/account/retailers/"+country_code.attr('value')+"/" + Math.random() * 99999;
            jQuery.getJSON(ajax_url, function(json) {
                if (json.result) {
                    jQuery('select[name=retailer_id] option').remove();
                    jQuery('select[name=retailer_id]').append(jQuery("<option></option>").attr("value", "").text('')); 
                    jQuery.each(json.retailers, function(key, value) {
                        jQuery('select[name=retailer_id]').append(jQuery("<option></option>").attr("value", value['id']).text(value['name']));
                    });
                }
            })
                    .error(function() {
                alert("Sorry but there was an error.");
            });
            
            
            //display provinces/states
            var ajax_url2 = "/hotcms/ajax/account/provinces/"+country_code.attr('value')+"/" + Math.random() * 99999;
            jQuery.getJSON(ajax_url2, function(json) {
                if (json.result) {
                    jQuery('select[name=province_code] option').remove();
                if(country_code.attr('value') === 'US'){
                        jQuery('select[name=province_code]')
                                .append(jQuery("<option></option>")
                                .attr("value", "")
                                .text('Please select state'));
                }else if(country_code.attr('value') === 'CA'){
                        jQuery('select[name=province_code]')
                                .append(jQuery("<option></option>")
                                .attr("value", "")
                                .text('Please select province'));
                }                    
                    jQuery.each(json.provinces, function(key, value) {
                        jQuery('select[name=province_code]')
                                .append(jQuery("<option></option>")
                                .attr("value", key)
                                .text(value));
                    });
                }
            })
                    .error(function() {
                alert("Sorry but there was an error.");
            });
        }else{
            jQuery("select[name=province]").prop('disabled', true);
        }
    });         
     
     
    jQuery("select[name=retailer_id]").change(function() {
        province_code = jQuery("select[name=province_code]").val();
        retailer_id = jQuery("select[name=province_code]").val();

        if (jQuery(this).val() > "") {
            jQuery("select[name=store]").prop('disabled', false);
            var ajax_url = "/hotcms/ajax/account/stores/" + jQuery(this).val() + "/" + province_code + "/" + Math.random() * 99999;
            jQuery.getJSON(ajax_url, function(json) {
                if (json.result) {
                    jQuery('select[name=store_id] option').remove();
                    jQuery('select[name=store_id]').append(jQuery("<option></option>").attr("value", "").text('')); 
                    jQuery.each(json.stores, function(key, value) {
                        jQuery('select[name=store_id]')
                                .append(jQuery("<option></option>")
                                .attr("value", value['id'])
                                .text(value['name']));
                    });
                }
            })
                    .error(function() {
                alert("Sorry but there was an error.");
            });
        }else {
           jQuery("select[name=store]").prop('disabled', true);
        }

    });     
    
    jQuery("select[name=province_code]").change(function() {
        province_code = jQuery("select[name=province_code]").val();
        retailer_id = jQuery("select[name=retailer_id]").val();
        
        if (jQuery(this).val() > "") {
            var ajax_url = "/hotcms/ajax/account/stores/" + retailer_id + "/" + province_code + "/" + Math.random() * 99999;
            jQuery.getJSON(ajax_url, function(json) {
                if (json.result) {
                    jQuery('select[name=store_id] option').remove();
                    jQuery('select[name=store_id]').append(jQuery("<option></option>").attr("value", "").text('')); 
                    jQuery.each(json.stores, function(key, value) {
                        jQuery('select[name=store_id]')
                                .append(jQuery("<option></option>")
                                .attr("value", value['id'])
                                .text(value['name']));
                    });
                }
            })
                    .error(function() {
                alert("Sorry but there was an error.");
            });
        }else {
           jQuery("select[name=store]").prop('disabled', true);
        }

    });      
     
});