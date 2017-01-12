jQuery.noConflict();
jQuery( document ).ready( function () {
  jQuery(function() {
    var zIndexNumber = 1000;
    jQuery('div').each(function() {
      jQuery(this).css('zIndex', zIndexNumber);
      zIndexNumber -= 10;
    });
  });
});
