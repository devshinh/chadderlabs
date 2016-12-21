jQuery( document ).ready( function() {  

  jQuery('input[name="linktype"]').click(function() {
  	//console.log(jQuery(this).val());
  	var val = jQuery(this).val();
  	var selected = '';
  	jQuery.each(jQuery('.panel_visible'), function(index,panel)
  	{
  		jQuery(panel).removeClass('panel_visible').addClass('panel_hidden');
  	});
  	if(val == "normal") {	
  		selected = 'content_page';
  	}
  	if(val == "external") {	
  		selected = 'link_external';
  	}  
  	if(selected != '')	  	
  	{
  		jQuery('.'+selected).removeClass('panel_hidden').addClass('panel_visible');	  	  	
  	}
  });

});