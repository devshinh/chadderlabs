/*
 * jquery.png.js / Paul Kevin Koehler / 2009-07-16
 *
 * Correctly handle PNG transparency in Win IE 5.5 & 6.
 * Based on pngfix.js: http://homepage.ntlworld.com/bobosola
 *
 * Use in <head> with defer keyword wrapped in conditional comments:
 * <!--[if lt IE 7]><script defer type="text/javascript" src="js/jquery.png.js"></script><![endif]-->
 *
 */

jQuery( document ).ready( function () {

  if (document.body.filters) {

    // loop images...
    jQuery( '*[src$=png]' ).each( function () {

      // set css values
      jQuery( this ).css( { 'width'  : jQuery( this ).attr( 'width' ),
                            'height' : jQuery( this ).attr( 'height' ),
                            'filter' : 'progid:DXImageTransform.Microsoft.AlphaImageLoader( src="' + jQuery( 'base' ).attr( 'href' ) + jQuery( this ).attr( 'src' ) + '", sizingMethod="crop" )' } );

      // reset src
      jQuery( this ).attr( 'src', 'asset/images/transparent.gif' );

    } );

    // loop background images...
    jQuery( '*' ).each( function () {

      // if png background image...
      if (jQuery( this ).css( 'backgroundImage' ).match( 'png' )) {

        // assign path
        var sSRC = jQuery( this ).css( 'backgroundImage' ).replace( /(^url\("|"\)$)/g, '' );

        // set css values
        jQuery( this ).css( { 'background' : '', 'filter' : 'progid:DXImageTransform.Microsoft.AlphaImageLoader( src="' + sSRC + '", sizingMethod="crop" )' } );
      }

    } );
  }

} );
