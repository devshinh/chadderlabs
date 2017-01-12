jQuery(document).ready(function() {

    if (jQuery("#cboSite").length > 0 && jQuery("#dashboard_form").length > 0) {
        jQuery("#cboSite").change(function() {
            var site_id = jQuery(this).val();
            //alert(site_id);
            if (site_id > 0) {
                jQuery("#dashboard_form").submit();
            }
        });
    }

    jQuery("#from_filter_range").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3,
        showOn: "button",
        buttonImage: "asset/images/icons/btn-calendar.png",
        buttonImageOnly: false,
        onClose: function(selectedDate) {
            jQuery("#to_filter_range").datepicker("option", "minDate", selectedDate);
        }
    });
    jQuery("#to_filter_range").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3,
        showOn: "button",
        buttonImage: "asset/images/icons/btn-calendar.png",
        buttonImageOnly: false,
        onClose: function(selectedDate) {
            jQuery("#from_filter_range").datepicker("option", "maxDate", selectedDate);
        }
    });
});
