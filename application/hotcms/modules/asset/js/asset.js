jQuery( document ).ready( function() {
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
          var category = '', type = '';
          for (var i=0; i< tmp.length; i++){
              var keyValPair = tmp[i].split('=');
              if(keyValPair[0] == 'status%5B%5D'){
               
                  if (keyValPair[1] == 0) {
                      status += 'Pending, ';
                  }else if(keyValPair[1] == 1){
                      status += 'Confirmed, ';
                  }else if(keyValPair[1] == 2){
                      status += 'Closed, ';
                  }
              }
              if(keyValPair[0] == 'category%5B%5D'){
                  category += keyValPair[1].replace(/\+/g, ' ')+', ';
              } 
              if(keyValPair[0] == 'type%5B%5D'){
               
                  if (keyValPair[1] == 1) {
                      type += 'Image, ';
                  }else if(keyValPair[1] == 2){
                      type += 'Document, ';
                  }else if(keyValPair[1] == 3){
                      type += 'Video, ';
                  }else if(keyValPair[1] == 4){
                      type += 'Audio, ';
                  }
              }              
          }
          var output = '';
           if (type.length > 0) {
               output += '<b>Type:</b> '+type;
           } 
           if (category.length > 0) {
               output += '<b>Category:</b> '+category;
           }            
           if (type.length == 0 && category.length == 0) {
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
