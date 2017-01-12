jQuery.noConflict();
jQuery( document ).ready( function () {
 if(jQuery('.home_texts').length > 0){
   jQuery('.home_texts').hide();
   var randomNum = Math.ceil(Math.random()*3); // to add more, increase the number here
   
   jQuery('#home_text_'+randomNum).show();
 }
 jQuery('.location_items li').hover( function(){
   if (jQuery(this).children('.location-details').hasClass('hidden')){
     jQuery(this).children('.location-details').removeClass('hidden').addClass('show');
   }else{
     jQuery(this).children('.location-details').removeClass('show').addClass('hidden');
   }
 });
 //same height for homepage columns
if(jQuery('#upper_left').length > 0){
  var max_height = Math.max(jQuery('#upper_left p').height(),jQuery('#upper_middle p').height(),jQuery('#upper_right p').height()); 
  jQuery('#upper_left p').height(max_height);
  jQuery('#upper_middle p').height(max_height);
  jQuery('#upper_right p').height(max_height);
  jQuery('#upper_border_left').height(max_height+30);
  jQuery('#upper_border_right').height(max_height+30);
}
});   

