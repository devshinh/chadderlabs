jQuery( document ).ready( function () {
	jQuery("form:eq(1) input:visible:enabled:first").focus();
  
   if(jQuery("input#phone").length > 0){
     jQuery("input#phone").mask("(999) 999-9999");
   }
  
  if(jQuery("#add_new_contact").length > 0){
    jQuery('#add_new_contact').click(function (){ 
        if(jQuery('#new_contact_form_wrapper').hasClass('hidden')){
            jQuery('#new_contact_form_wrapper').show().removeClass('hidden');
            jQuery('#add_new_contact').text('Hide address form');
        }else{
            jQuery('#new_contact_form_wrapper').hide().addClass('hidden');
            jQuery('#add_new_contact').text('Add new address');
        }
    }); 
  }  
  
  if(jQuery("#new_contact_from_shipping").length > 0){
    jQuery("#new_contact_from_shipping").validate({
        rules:{
            contact_name:"required",
            address_1:"required",
            city:"required",
            province:"required",
            postal:"required"
        },
        messages:{
            contact_name:"Please enter address name.",
            address_1:"Please enter address.",
            city:"Please enter yourcity.",
            province:"Please enter your province.",
            postal:"Please enter your postal code."            
        }
    });
  }
  
  /*
  jQuery(".cart-item select").change(function(){
    if (jQuery(this).val() == '0'){
      var where_to= confirm('You selected 0 and the item is going to be removed from your cart. Are you sure?');
      if (where_to== true) {
        jQuery(this).parents('form').submit();
      }
    }
    else {
      jQuery(this).parents('form').submit();
    }
  }); */
  
  /*
  jQuery("#gateway").load(function(){
    //alert(jQuery(this).attr("src").substring(0, 28));
    //if (jQuery(this).attr("src").substring(0, 28) != 'https://staging.eigendev.com') {
    //  window.location = jQuery(this).attr("src");
    //}
    var newContent = jQuery(this).contents().find("#divMain");
    var oldContent = jQuery(this).contents().find("#ContentDiv");
    alert(typeof newContent);
    alert(typeof oldContent);
    alert('testing');
    var i = 0;
    for(prop in newContent){
      if (prop == 'length' && newContent[prop] > 0) {
        jQuery('#divMain').replaceWith(newContent);
        break;
      }
      if (i < 10) {
        alert(prop + ': ' +newContent[prop]);
      }else{break;}
      i++;
    }
//console.log(newContent)
    alert('done testing');

    //alert(newContent.html());
    //if (newContent) {
    //  jQuery('#divMain').replaceWith(newContent);
    //}
  });
  */
});