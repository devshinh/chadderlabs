jQuery.noConflict();
jQuery( document ).ready( function () {
    
  // new way of sorting, works with search and pagination
  jQuery("div.sortable").click(function() {
    var selector = jQuery(this);
    var field_name = selector.attr("id").substring(9);
    //console.log(field_name);
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
    
    //      if( /Android|iPhone|BlackBerry/i.test(navigator.userAgent) ) {
  //alert("This site is not optimized for mobile devices. Please check back soon.");
  //}
  
//  if(jQuery("#slideshow-overview").length > 0){   
//    jQuery('#slideshow-overview').cycle2({ 
//      speed:  'slow',
//      timeout: 5000,
//      next:   '#next',
//      prev:   '#prev',
//      log:    false
//    });     
//  }  
      
  if(jQuery("#quiz_form").length > 0){
    jQuery("#quiz_form").validate({
      errorClass: 'error',
      
      highlight: function(element, errorClass) {
        jQuery(element).fadeOut(function() {
          jQuery(element).fadeIn();
        });
      }

    });
  }

  if (jQuery('img.reflection').length > 0){
    jQuery('img.reflection').each(function() {
      jQuery(this).reflect();
    });
  }
  if (jQuery('img.reflection_less').length > 0){
    jQuery('img.reflection_less').each(function() {
      jQuery(this).reflect({
        height:0.1, 
        opacity: 0.5
      });
    }); 
  }
  if (jQuery('.tabs-profile').length > 0){
    jQuery('.tabs-profile').tabs({ cookie: { expires: 1 } });
  }
  if (jQuery('.item-detail').length > 0){
    jQuery('.tabs-assets').tabs();
  } 
  if (jQuery('.items-list').length > 0){
    jQuery('.tabs-items').tabs();
  }   
  
  // framed box
  if ( jQuery( 'div.framedbox' ).length > 0 ){
    jQuery( 'div.framedbox' ).wrapInner('<span class="innerbox" />');
  }
  // captcha
  if ( jQuery( 'div#divCaptcha' ).exists() ){
    jQuery('div#divCaptcha').load('/hotajax/captcha/' + Math.random()*99999);
  }
  
    jQuery("#refer_colleague_form").validate({
        rules: {
            firstname: "required",
            lastname: "required",
            email: "required"
        },
        messages: {
            firstname: "The first name field is required.",
            lastname: "The last name field is required.",
            email: "The email field is required." }
    });
  
    if(jQuery('input[name=refer_colleague_hidden]').val() === 'show_response'){
       jQuery('#refColModal').modal('show');
    }  


});

/*
 * Custom jQuery methods
 */
jQuery.fn.exists = function() {
  return jQuery( this ).length > 0;
};

var aCache = [];
jQuery.preloadImage = function() {
  var oImage = document.createElement( 'img' );
  for (var i = arguments.length; i--;) {
    oImage.src = arguments[i];
    aCache.push( oImage );
  }
};

function goBack()
  {
  window.history.back()
  };

function confirmDelete(item_name) {
  if (item_name == undefined || item_name == '') {
    item_name = 'item';
  }
  var agree=confirm("Are you sure you wish to delete this " + item_name + "?");
  if (agree)
    return true;
  else
    return false;
};

function toggleVerticalMenu(parentId) {
  var verticalMenu = jQuery(".vertical-menu");
  if (verticalMenu.hasClass("hidden")) {
    verticalMenu.removeClass("hidden");
    jQuery("#main-nav-wrapper").addClass("shift-left");
    jQuery("#secondary-nav-wrapper").addClass("shift-left");
    jQuery("body > .container").addClass("shift-left");
    parentId = 0;
  } else if (parseInt(parentId) === -2) {
    verticalMenu.addClass("hidden");
    jQuery("#main-nav-wrapper").removeClass("shift-left");
    jQuery("#secondary-nav-wrapper").removeClass("shift-left");
    jQuery("body > .container").removeClass("shift-left");
    return;
  }
  verticalMenu.find("li").each(function() {
    if (parseInt(jQuery(this).data("parentid")) === parseInt(parentId)) {
      if (jQuery(this).hasClass("hidden")) {
        jQuery(this).removeClass("hidden");
      }
    } else if ((parseInt(jQuery(this).data("parentid")) > -1) && !jQuery(this).hasClass("hidden")) {
      jQuery(this).addClass("hidden");
    }
  });
}