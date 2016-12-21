jQuery.noConflict();
jQuery( document ).ready( function () {
 
  jQuery('#addAsset').click(function(){
   jQuery('#mediaLib').toggle('fast');
  });
//product asstets  
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
        var sequence = 'asset=';
        jQuery.each(items, function(){

          sequence += jQuery(this).attr('id');
          sequence += '_';

        });
        sequence = sequence.substring(0, sequence.length - 1);

        //console.log(sequence);
       jQuery.ajax({
         url: "/hotcms/product/ajax_assets_sequence?"+sequence,
         type: "POST",
         context: document.body,
         success: function(){
           jQuery(this).addClass("done");
         }
       });        

        }
    }
  );
//products  
  jQuery('table.groupWrapperProducts').Sortable(
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


        var items = jQuery('.groupWrapperProducts .groupItem');
        //console.log(items);
        var sequence = 'asset=';
        jQuery.each(items, function(){

          sequence += jQuery(this).attr('id');
          sequence += '_';

        });
        sequence = sequence.substring(0, sequence.length - 1);

        //console.log(sequence);
       jQuery.ajax({
         url: "/hotcms/product/ajax_sequence?"+sequence,
         type: "POST",
         context: document.body,
         success: function(){
           jQuery(this).addClass("done");
         }
       });        

        }
    }
  );
   if (jQuery( "#opening_time" ).length > 0 ) {
		jQuery( "#opening_time" ).datetimepicker({
     showOn: "button",
     buttonImage: "/hotcms/asset/images/icons/btn-calendar.png",
     buttonImageOnly: true,
     dateFormat: "yy-mm-dd"
    });
   }
   if (jQuery( "#closing_time" ).length > 0 ) {
		jQuery( "#closing_time" ).datetimepicker({
     showOn: "button",
     buttonImage: "/hotcms/asset/images/icons/btn-calendar.png",
     buttonImageOnly: true,
     dateFormat: "yy-mm-dd"
    });    
   }
   
});