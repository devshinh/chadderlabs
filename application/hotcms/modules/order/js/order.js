jQuery( document ).ready( function() {
    jQuery('#per_page_select').change(function() {
        jQuery('#pagination_form').submit();
    }); 
});
