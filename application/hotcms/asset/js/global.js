jQuery.noConflict();

jQuery.fn.exists = function(){return this.length>0;}

jQuery(document).ready( function() {

  jQuery("#top_menu li").hover(function(){
      if(jQuery(this).children('ul').length !== 0){
        jQuery(this).children('ul').toggle();
      };
  });


 
//jQuery( "#top_menu" ).menu();


  // deprecated
 // jQuery(".tablesorter").tablesorter({
    // sort on the first column and third column, order asc
    //sortList: [[0,0],[1,0]]
 // });

  // new way of sorting, works with search and pagination
  jQuery("table.table_sorter th.sortable").click(function() {
    var selector = jQuery(this);
    var field_name = selector.attr("id").substring(9);
    var is_asc = selector.hasClass("headerSortUp");
    if (field_name > "") {
      jQuery("#search_form input[name=sort_by]").val(field_name);
      if (is_asc) {
        jQuery("#search_form input[name=sort_direction]").val('desc');
      }
      else {
        jQuery("#search_form input[name=sort_direction]").val('asc');
      }
      jQuery("#search_form").submit();
    }
  });
  
    jQuery('#per_page_select').change(function() {
        jQuery('#pagination_form').submit();
    });   
  

  
  jQuery('textarea.tinymce').tinymce({
    // Location of TinyMCE script
    script_url : '/hotcms/asset/js/tinymce/tinymce.min.js',

    // General options
    width: "770",
    height: "450",
    theme : "modern",
    
    plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor hotimg"
   ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons | hotimg",
    // Example content CSS (should be your site CSS)
    content_css : "/themes/cheddarLabs/css/cheddarFront.css",
    relative_urls: false,
    remove_script_host: false
  });
 
  if (jQuery("#cboSite_global").length > 0 && jQuery("#sites_form").length > 0){
    jQuery("#cboSite_global").change(function(){
      var site_id = jQuery(this).val();
      //alert(site_id);
      if (site_id > 0) {
        jQuery("#sites_form").submit();
      }
    });
  }

});

function confirmDelete(item_name) {
  if (item_name == undefined || item_name == '') {
    item_name = 'item';
  }
  var agree=confirm("Are you sure you wish to delete this " + item_name + "?");
  if (agree)
    return true;
  else
    return false;
}

function closeMessage() {
  jQuery('div[class*=message]').hide();
  jQuery('.underline').show();
}

/**
 * Asks the user if they want to leave the current editing page and discard all changes
 * note: this only provides minimum protection because there are so many ways to leave/close the page
 */
function confirm_discard() {
  if (jQuery("input[name='draft_updated']").val() == "1") {
    return confirm("The content has been changed. Are you sure you wish to leave without saving?");
  }
  return true;
}