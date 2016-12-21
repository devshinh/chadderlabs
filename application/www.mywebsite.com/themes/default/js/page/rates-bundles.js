jQuery(document).ready(function(){
  var cur_id = 0;
  var cur_page = self.document.location.hash.substring(1);
  switch (cur_page){
    case 'features': 
      cur_id = 1; 
      jQuery('a.menu_features').addClass("current");
      break;
    case 'text': 
      cur_id = 2; 
      jQuery('a.menu_text').addClass("current");
      break;
    case 'browsing': 
      cur_id = 3; 
      jQuery('a.menu_browsing').addClass("current");
      break;
    case 'long-distance': 
      cur_id = 4; 
      jQuery('a.menu_long_distance').addClass("current");
      break;
    case 'bundles': 
      cur_id = 5; 
      jQuery('a.menu_bundles').addClass("current");
      break;
    default:
      cur_id = 0;
      jQuery('a.menu_talk_rates').addClass("current");
  }
  jQuery('#tabs ul li:eq(5)').addClass("last");
  jQuery("#tabs").tabs({ selected: cur_id,
    show: function(event, ui) {
      reset_current_menu();
      var currentselected = jQuery(this).tabs('option', 'selected');
      switch (currentselected){
      case 0:
        jQuery('a.menu_talk_rates').addClass("current");
        break;
      case 1:
        jQuery('a.menu_features').addClass("current");
        break;
      case 2:
        jQuery('a.menu_text').addClass("current");
        break;
      case 3:
        jQuery('a.menu_browsing').addClass("current");
        break;
      case 4:
        jQuery('a.menu_long_distance').addClass("current");
        break;
      case 5:
        jQuery('a.menu_bundles').addClass("current");
        break;
      }
    }
  });
  
  // menu actions
  var rates_table = jQuery('#tabs').tabs();
  jQuery('a.menu_talk_rates').click(function() {
    reset_current_menu();
    jQuery(this).addClass("current");
    rates_table.tabs('select', 0);
    return false;
  });
  jQuery('a.menu_features').click(function() {
    reset_current_menu();
    jQuery(this).addClass("current");
    rates_table.tabs('select', 1);
    return false;
  });
  jQuery('a.menu_text').click(function() {
    reset_current_menu();
    jQuery(this).addClass("current");
    rates_table.tabs('select', 2);
    return false;
  });
  jQuery('a.menu_browsing').click(function() {
    reset_current_menu();
    jQuery(this).addClass("current");
    rates_table.tabs('select', 3);
    return false;
  });
  jQuery('a.menu_long_distance').click(function() {
    reset_current_menu();
    jQuery(this).addClass("current");
    rates_table.tabs('select', 4);
    return false;
  });
  jQuery('a.menu_bundles').click(function() {
    reset_current_menu();
    jQuery(this).addClass("current");
    rates_table.tabs('select', 5);
    return false;
  });
  
  // table style
  jQuery('.rates_table tr:even').css('backgroundColor', '#e6e9ee');
  
	jQuery('#flipBoxSmall').cycle({
		fx: 'slideY',
		timeout: 16000,
		sync: true
	});  
  
});

function reset_current_menu(){
  jQuery('a.menu_talk_rates').removeClass("current");
  jQuery('a.menu_features').removeClass("current");
  jQuery('a.menu_text').removeClass("current");
  jQuery('a.menu_browsing').removeClass("current");
  jQuery('a.menu_long_distance').removeClass("current");
  jQuery('a.menu_bundles').removeClass("current");
}