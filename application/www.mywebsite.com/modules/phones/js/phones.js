jQuery( document ).ready( function() {

  jQuery('#divPhoneAssets').cycle({
		fx: 'scrollHorz',
		timeout: 0,
    prev:   '#prevAsset', 
    next:   '#nextAsset'
	});
	
	jQuery("#divPhoneTabs").tabs();

	jQuery('#flipBoxSmall').cycle({
		fx: 'slideY',
		timeout: 16000,
		sync: true

	});
	
	
});
