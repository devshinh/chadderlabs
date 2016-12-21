var nCAROUSEL_COUNT = 1;
var oIndex = { promos: null, flavours: null, products: null, recipes: null };
var oMode  = { promos: null, flavours: null, products: null, recipes: null };
var carouselTimerID = 0;
var fixSafari = true;

jQuery.noConflict();
jQuery( document ).ready( function () {

  // if working locally...
  if (jQuery( 'base' ).exists() && jQuery( 'base' ).attr( 'href' ).match( '127.0.0.1' )) {

    var sPath = jQuery( 'base' ).attr( 'href' ).replace( /\/application\/.+\//, '' );

    // prepend absolute links with (abbreviated) local path
    jQuery( 'a[href^=/]' ).each( function () { jQuery( this ).attr( 'href', sPath + jQuery( this ).attr( 'href' ) ); } );

    // prepend form actions with (abbreviated) local path
    jQuery( 'form' ).each( function () {
      if (jQuery( this ).attr( 'action' ).match( /^\// )) { jQuery( this ).attr( 'action', sPath + jQuery( this ).attr( 'action' ) ); }
    } );

  } else {

    // prepend form actions with (abbreviated) local path
    jQuery( 'form' ).each( function () {
      if (jQuery( this ).attr( 'action' ).match( /^\// )) { jQuery( this ).attr( 'action', jQuery( this ).attr( 'action' ) ); }
    } );

    // prepend absolute links with (abbreviated) local path
    jQuery( 'a[href^=/]' ).each( function () { jQuery( this ).attr( 'href', jQuery( this ).attr( 'href' ) ); } );
  }

  // initialize external links
  jQuery( 'a.external' ).click( function() {
    window.open( jQuery( this ).attr( 'href' ) );
    return false;
  } );

  // assign name (to avoid: id="txtFoo" name="txtFoo")
  jQuery( 'form :input[id]' ).each( function () {
    if (jQuery( this ).attr( 'name' ) == '' || jQuery( this ).attr( 'name' ) == null) {
      jQuery( this ).attr( 'name', jQuery( this ).attr( 'id' ) );
    }
  } );

  // remove textarea resize (for safari)
  jQuery( 'textarea' ).css( 'resize', 'none' );

  // if carousel exists... preload carousel images
  if (typeof( window['aCarousel'] ) != 'undefined' && aCarousel.length) {
    for (var i in aCarousel) { jQuery.preloadImage( 'asset/upload/image/' + aCarousel[i].src ); }
  }

  // if IE... fix positioning
  if (jQuery.browser.msie && jQuery.browser.version <= 6) {
    //jQuery( 'div#divFooter' ).css( { 'position' : 'absolute' } );
    //jQuery( window ).scroll( function() { jQuery( 'div#divFooter' ).css( 'top', (jQuery( this ).scrollTop() + jQuery( window ).height() - 39) + 'px' ); } );
  }
  
  // slide-down submenu handling
  var menuTimerId = 0;
  jQuery("div#divMenu ul li.main a.hasChildren").mouseover(function(){
    clearTimeout( menuTimerId );
    jQuery("div#divSubmenu ul").hide();
    jQuery("div#divSubmenu ul#listSubmenu_"+jQuery(this).attr("id")).show();
    jQuery("div#divSubmenu").slideDown("fast");
  }).mouseout(function(){
    menuTimerId = window.setTimeout(function(){
      jQuery( 'div#divSubmenu' ).slideUp("slow");
    }, 8000);
  });
  jQuery("div#divSubmenu").mouseover(function(){
    clearTimeout( menuTimerId );
  }).mouseout(function(){
    menuTimerId = window.setTimeout(function(){
      jQuery( 'div#divSubmenu' ).slideUp("slow");
    }, 8000);
  });
  
  // carousel
  if ( jQuery( 'div.carousel_outer' ).length>0 ){
    initCarousel( 'promos' );
    startCarousel( 'promos' );
    //jQuery( 'div.carousel_outer a.arrow' ).show();
    //jQuery( 'div.carousel_outer a.arrow' ).css('visibility','visible');
  }
  
  // framed box
  if ( jQuery( 'div.framedbox' ).length>0 ){
    jQuery( 'div.framedbox' ).wrapInner('<span class="innerbox" />');
  }
  // captcha
  if ( jQuery( 'div#divCaptcha' ).length>0 ){
    jQuery('div#divCaptcha').load('/hotajax/captcha/' + Math.random()*99999);
  }
});

/*
 * Custom jQuery methods
 */
jQuery.fn.exists = function() { return jQuery( this ).length > 0; }

var aCache = [];
jQuery.preloadImage = function() {
  var oImage = document.createElement( 'img' );
  for (var i = arguments.length; i--;) {
    oImage.src = arguments[i];
    aCache.push( oImage );
  }
}

function initCarousel( sMode ) {

  var aCarousel = eval( 'aCarousel_' + sMode );

  //console.log(aCarousel);
  // initialize indicator
  if (aCarousel.length >1) {
    for (var i = 0; i < aCarousel.length; i++) {
      jQuery( 'div.carousel_outer div.indicator' ).append( '<span class="' + sMode + '_' + (i + 1) + '">&bull;</span>' );
    } 
  }
  jQuery( 'div.carousel_outer div.indicator span' ).hide();

  jQuery( 'div.carousel_' + sMode ).css('visibility','visible');
  jQuery( 'div.carousel_outer div.indicator span[class^=' + sMode + ']' ).show();

  
  var scroll=0; 
  var length = 1;
  if (aCarousel.length >1) {  
    scroll=1;
    length = aCarousel.length; 
  }

  // initialize carousel
  jQuery( 'div.carousel_' + sMode + ' ul' ).jcarousel( {

    animation: 4000,
    auto: 2,
    scroll: scroll,
    buttonNextHTML: null,
    buttonPrevHTML: null,
    easing: 'easeInOutExpo', 
    wrap: 'both',
    size: length,

    initCallback: function ( carousel ) {

      //console.log( carousel );

      oMode[sMode] = carousel;

    },

    itemVisibleInCallback: {

      onBeforeAnimation: function ( carousel, item, i, state, evt ) {
        oIndex[sMode] = carousel.index( i, aCarousel.length );
        if (aCarousel[oIndex[sMode] - 1].url == ""){
          carousel.add( i, '<img width="598px" height="250px" class="mainPic" src="/asset/upload/image/speakoutPromotionCarousel/' + aCarousel[oIndex[sMode] - 1].src + '" alt="' + aCarousel[oIndex[sMode] - 1].title + '" />' );
        }else{
          carousel.add( i, '<a href="/' + aCarousel[oIndex[sMode] - 1].url + '"><img width="598px" height="250px" class="mainPic" src="/asset/upload/image/speakoutPromotionCarousel/' + aCarousel[oIndex[sMode] - 1].src + '" alt="' + aCarousel[oIndex[sMode] - 1].title + '" /></a>' );
        }
      },
      
      onAfterAnimation: function ( carousel, item, i, state, evt ) { carousel.index( i, aCarousel.length ); }

    },

    itemVisibleOutCallback: { onAfterAnimation: function ( carousel, item, i, state, evt ) { 
      //carousel.remove( i ); 
    } },

    itemFirstInCallback: { onAfterAnimation: function ( carousel, item, i, state ) { displayIndicator( aCarousel, sMode, item ); } }

  } );
}

function startCarousel( sMode ) {
  oMode[sMode].scroll(1,false);
  oMode[sMode].startAuto();
}

function switchMode( sMode ) {
  jQuery( 'div.search a' ).removeClass('active');
  jQuery( 'div.search a#img_'+sMode ).addClass('active');
  startCarousel( sMode );
  jQuery( 'div.carousel_inner' ).css('visibility','hidden');
  jQuery( 'div.carousel_outer div.indicator span' ).hide();
  jQuery( 'div.carousel_' + sMode ).css('visibility','visible');
  jQuery( 'div.carousel_outer div.indicator span[class^=' + sMode + ']' ).show();
}

function displayIndicator( aCarousel, sMode, item ) {
  
  var nModulo = jQuery( item ).attr( 'jcarouselindex' ) % aCarousel.length;
  var nItem   = nModulo > 0 ? nModulo : aCarousel.length;

  jQuery( 'div.carousel_outer div.indicator span[class^=' + sMode + ']' ).removeClass( 'active' );

  for (var i = nItem; i < nItem + 1; i++) {
    jQuery( 'div.carousel_outer div.indicator span.' + sMode + '_' + (i == aCarousel.length ? i : i % aCarousel.length) ).addClass( 'active' );
  }
}

function refresh_captcha() {
  // if stupid IE...
  //if (jQuery.browser.msie) {
    jQuery('div#divCaptcha').load('/hotajax/captcha/' + Math.random()*99999);
  //}else{
  //  jQuery('div#divCaptcha').load('/hotajax/captcha');
  //}
  return false;
}