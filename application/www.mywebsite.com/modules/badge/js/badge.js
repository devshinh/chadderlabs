jQuery(document).ready(function() {
    //submit items per page on change
    jQuery('#per_page_select').change(function() {
        jQuery('#pagination_form').submit();
    });

    jQuery('#ui-sortable').sortable({
        update: function(event, ui) {
            var newOrder = '';
            jQuery("#ui-sortable").children().each(function(i) {
                var tr = jQuery(this);
                newOrder += "" + tr.attr("id") + '_';
            });
            var ajax_url = "badge/ajax_save_badge_sequence/" + Math.random() * 99999;
            jQuery.get(ajax_url, {order: newOrder});
        }
    });
});