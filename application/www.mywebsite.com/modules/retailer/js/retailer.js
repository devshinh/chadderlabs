jQuery( document ).ready( function() {
  
  jQuery("select[name=country_code]").change(function() {
    var selector = jQuery(this);
    var ajax_url = "/hotcms/ajax/global/provinces/" + selector.val() + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result) {
        jQuery('select[name=province] option').remove();
        jQuery('select[name=province]').append(jQuery("<option></option>").attr("value", '').text('Select'));
        jQuery.each(json.provinces, function(key, value) {
          jQuery('select[name=province]').append(jQuery("<option></option>").attr("value", key).text(value));
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });  
  
  jQuery("select.retailer_access_selector").change(function() {
    var selector = jQuery(this);
    var retailer_id = selector.attr("id");
    var ajax_url = "/hotcms/ajax/retailer/access/" + retailer_id + "/" + selector.val() + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result) {
        selector.after('&nbsp; <img src="/hotcms/asset/images/icons/accept.png" width="21" height="16" alt="updated" style="vertical-align:middle" />');
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });
  //submit items per page on change
  jQuery('#per_page_select').change(function(){
     jQuery('#pagination_form').submit();
  });
  
    //open modal window on filters
    if (jQuery('#filter_button').length > 0) {
        jQuery('#filter_button').click(function() {
                jQuery('#filters-modal' ).dialog({
                    modal: true,
                    width: 444,
                    resizable: false
                });         

        });    
    }  
    
    //expand/colapse filters on filter modal
    if (jQuery('.filter_form').length > 0) {
        jQuery('.filter_header').click(function() {
            var collapsed = jQuery(this).hasClass('closed');
            jQuery('.filter_header').each(function (){
                jQuery(this).removeClass('opened').addClass('closed');
                jQuery(this).find('.filter_header_arrow').hide();
                jQuery(this).next().hide();
            });
            
            if(collapsed){
                jQuery(this).removeClass('closed').addClass('opened');
                jQuery(this).find('.filter_header_arrow').show();
                jQuery(this).next().show();
            }
            else {
                jQuery(this).removeClass('opened').addClass('closed');
                jQuery(this).find('.filter_header_arrow').hide();
                jQuery(this).next().hide();
            }
        });
        
      jQuery('.filter_form').change(function() { 
          var data = jQuery(this).serialize();
          var tmp = data.split('&');
          var status = '', country = '';
          for (var i=0; i< tmp.length; i++){
              var keyValPair = tmp[i].split('=');
              //dataObj[keyValPair[0]] = keyValPair[1];
              if(keyValPair[0] == 'status%5B%5D'){
               
                  if (keyValPair[1] == 0) {
                      status += 'Pending, ';
                  }else if(keyValPair[1] == 1){
                      status += 'Confirmed, ';
                  }else if(keyValPair[1] == 2){
                      status += 'Closed, ';
                  }
              }
              if(keyValPair[0] == 'country_code%5B%5D'){
                  country += keyValPair[1]+', ';
              }              
          }
          var output = '';
           if (status.length > 0) {
               output += 'Status: '+status;
           } 
           if (country.length > 0) {
               output += 'County: '+country;
           }            
           if (status.length == 0 && country.length == 0) {
               output = 'None, '
           }
          output = output.substring(0, output.length -2);
          jQuery('.selected_filters i').html(output);
      });
        
    }
    //remove all filters 
    if (jQuery('#remove_all_filters').length > 0) {
        jQuery('#remove_all_filters').click(function() {
            var checkbox = jQuery('.filter_form').find('input');
            checkbox.removeAttr('checked');   
            jQuery('.selected_filters i').html('None');
        });
    }    
});
