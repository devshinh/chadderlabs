
jQuery.noConflict();
jQuery( document ).ready( function () {
    
    //  if( /Android|iPhone|BlackBerry/i.test(navigator.userAgent) ) {
  //alert("This site is not optimized for mobile devices. Please check back soon.");

  //}
  
  if(jQuery("#slideshow-overview").length > 0){
    jQuery('#slideshow-overview').cycle({ 
      speed:  'slow',
      timeout: 5000,
      next:   '#next',
      prev:   '#prev'
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
    jQuery('.tabs-profile').tabs();
  }
  if (jQuery('.item-detail').length > 0){
    jQuery('.tabs-assets').tabs();
  } 
  if (jQuery('.items-list').length > 0){
    jQuery(this).scrollTop(0);
    jQuery('.tabs-items').tabs();
  }   
  
  // framed box
  if ( jQuery( 'div.framedbox' ).length>0 ){
    jQuery( 'div.framedbox' ).wrapInner('<span class="innerbox" />');
  }
  // captcha
  if ( jQuery( 'div#divCaptcha' ).length>0 ){
    jQuery('div#divCaptcha').load('/hotajax/captcha/' + Math.random()*99999);
  }
 
  if (jQuery('#login_form').length > 0){
    jQuery('#login_form').validate({
      rules: {
        username: {
          required: true,
          email: true
        },
        password: "required" 
      },
      messages: {
        username: "Please enter a valid email address",
        password: "Please enter a password.",
      }
    });
  }
  
  if (jQuery('#register_form').length > 0){
    jQuery('#register_form').validate({
      rules: {
        first_name: "required",
        last_name: "required",
        postal: "required",
        email: {
          required: true,
          email: true
        },
        email_confirm: {
          required: true,
          email: true,
          equalTo: "#email"
        },
        password2: {
          required: true,
          minlength: 4
        },    
        password_confirm: {
          required: true,
          equalTo: "#password2",
          minlength: 4
        }    
      },
      messages: {
        first_name: "The first name field is required.",
        last_name: "The last name field is required.",
        postal: "The postal code field is required.",
        email: "Please enter a valid email address.",
        email_confirm: { 
          required:"Please enter a valid email address.",
          equalTo: "Please enter the same email as above."
        },
        password: {
          required: "Please provide a password.",
          minlength: "Your password must be at least 4 characters long."
        },
        password_confirm: {
          required: "Please provide a password.",
          minlength: "Your password must be at least 4 characters long.",
          equalTo: "Please enter the same password as above."
        }    
      }

    
    });
  }
  if (jQuery('#forgot_password').length > 0){
    jQuery('#forgot_password').validate({
      rules: {
        email: {
          required: true,
          email: true
        }
      },
      messages: {
        email: "Please enter a valid email address"
      }
    });
  } 
 
  if (jQuery('#change_password').length > 0){
    jQuery('#change_password').validate({
      rules: {
        password: {
          required: true
        },
        new_password: {
          required: true,
          minlength: 4
        },    
        new_password_confirm: {
          required: true,
          minlength: 4,
          equalTo: "#new_password"
        }    
      },
      messages: {
        password: {
          required: "Please provide a password."
        },
        new_password: {
          required: "Please provide a password.",
          minlength: "Your password must be at least 4 characters long."
        },
        new_password_confirm: {
          required: "Please provide a password.",
          minlength: "Your password must be at least 4 characters long.",
          equalTo: "Please enter the same password as above."
        }    
      }

    
    });
  } 
});

/*
 * Custom jQuery methods
 */
jQuery.fn.exists = function() {
  return jQuery( this ).length > 0;
}

var aCache = [];
jQuery.preloadImage = function() {
  var oImage = document.createElement( 'img' );
  for (var i = arguments.length; i--;) {
    oImage.src = arguments[i];
    aCache.push( oImage );
  }
}

function toggleVerticalMenu(parentId) {
  var verticalMenu = jQuery(".vertical-menu");
  if (verticalMenu.hasClass("hidden")) {
    verticalMenu.removeClass("hidden");
    jQuery("body > .navbar-fixed-top").addClass("shift-left");
    jQuery("body > .container").addClass("shift-left");
    parentId = 0;
  } else if (parseInt(parentId) === -2) {
    verticalMenu.addClass("hidden");
    jQuery("body > .navbar-fixed-top").removeClass("shift-left");
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

function toggleGlobalMobile() {
  var globalMobile = jQuery(".global-mobile");
  if (globalMobile.hasClass("hidden")) {
    globalMobile.removeClass("hidden");
  } else {
    globalMobile.addClass("hidden");
  }
  jQuery(window).one("scroll", function() {
    if (!globalMobile.hasClass("hidden")) {
      globalMobile.addClass("hidden");
    }
  });
}
