jQuery.noConflict();
jQuery(document).ready(function() {

    jQuery('#purchase-form').submit(function() {
        var ajax_url = "/shop/ajax_add/" + Math.random() * 99999;
        jQuery.post(ajax_url, jQuery("#purchase-form").serialize(), function(data) {
            try {
                var JSONobj = JSON.parse(data);
                if (JSONobj['messages'] > '') {
                    jQuery('#purchase-message').html(JSONobj['messages']);
                }
            }
            catch (e) {
                alert("Error: " + e.description);
            }
        });
        return false;
    });

    jQuery('#addAsset').click(function() {
        jQuery('#mediaLib').toggle('fast');
    });

    if (jQuery('#ui-sortable').exists()) {


        jQuery("#ui-sortable td").each(function() {
            //fix for tables to keep the  width
            jQuery(this).css("width", jQuery(this).width());
        });

        jQuery('#ui-sortable').sortable({
            update: function(event, ui) {
                //var newProductOrder = jQuery(this).sortable('toArray').toString();
                var newProductOrder = '';
                jQuery("#ui-sortable").children().each(function(i) {
                var tr = jQuery(this);
                //newProductOrder += ""+tr.attr("id") + '=' + i + '&';
                newProductOrder += ""+tr.attr("id") + '_';
                });
                //console.log(newProductOrder);
                var ajax_url = "product/ajax_save_product_sequence/" + Math.random()*99999;
                jQuery.get(ajax_url, {order: newProductOrder});
            }
        });
    }
});