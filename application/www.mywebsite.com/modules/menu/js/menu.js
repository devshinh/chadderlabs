jQuery.noConflict();
jQuery( document ).ready( function () {

		jQuery('table.groupWrapper').Sortable(
			{
				accept: 'groupItem',
				helperclass: 'sortHelper',
				activeclass : 	'sortableactive',
				hoverclass : 	'sortablehover',
				handle: 'td.itemHeader',
				tolerance: 'pointer',
        axis: 'vertically',
				onChange : function(ser)
				{
         //alert('onChange');

				},
				onStart : function()
				{
         
					jQuery.iAutoscroller.start(this, document.getElementsByTagName('body'));
				},
				onStop : function()
				{
					jQuery.iAutoscroller.stop();
          
          
          var items = jQuery('.groupWrapper .groupItem');
          var sequence = 'menuItem=';
          jQuery.each(items, function(){
           
            sequence += jQuery(this).attr('id');
            sequence += '_';
           
          });
          sequence = sequence.substring(0, sequence.length - 1);

          //console.log(sequence);
         jQuery.ajax({
           url: "/hotcms/menu/ajax_sequence?"+sequence,
           type: "POST",
           context: document.body,
           success: function(){
             jQuery(this).addClass("done");
           }
         });        
          
          }
			}
		);

 
});
