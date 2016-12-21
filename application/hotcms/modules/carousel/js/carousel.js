jQuery(document).ready(function() {

	jQuery('#carousel').cycle({
		timeout : 8000,
		pager : '#carouselNav',
		pause : true
	});
	jQuery('#carouselNav').click(function() {
		jQuery('#carousel').cycle('pause');
	});

	jQuery('a#navRight').click(function() {
		var nextClick = jQuery('#carouselNav a.activeSlide').next('a');
		jQuery(nextClick).click();
		return false;
	});
	jQuery('a#navLeft').click(function() {
		var prevClick = jQuery('#carouselNav a.activeSlide').prev('a');
		jQuery(prevClick).click();
		return false;
	});

});

