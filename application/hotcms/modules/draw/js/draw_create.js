/**
 * Created on November 11, 2013 By Tao Long
 * Last modified on November 11, 2013 By Tao Long
 */
jQuery(document).ready(function() {

  jQuery( "#datepicker_begining" ).datetimepicker({
     showOn: "button",
     buttonImage: "/hotcms/asset/images/icons/btn-calendar.png",
     buttonImageOnly: true,
     dateFormat: "yy-mm-dd"
    });
    jQuery( "#datepicker_closing" ).datetimepicker({
     showOn: "button",
     buttonImage: "/hotcms/asset/images/icons/btn-calendar.png",
     buttonImageOnly: true,
     dateFormat: "yy-mm-dd"
    });  
});