jQuery.noConflict();
jQuery(document).ready(function(){

  jQuery('#purchase-form').submit(function(){
    var ajax_url = "/shop/ajax_add/" + Math.random()*99999;
    jQuery.post(ajax_url, jQuery("#purchase-form").serialize(), function(data){
      try{
        var JSONobj = JSON.parse(data);
        if (JSONobj['messages'] > '') {
          jQuery('#purchase-message').html(JSONobj['messages']);
        }
      }
      catch(e){
        alert("Error: "+e.description);
      }
    });
    return false;
  });

  //jQuery('#addAsset').click(function(){
  //  jQuery('#mediaLib').toggle('fast');
  //});
  if(("#purchase-form").length > 0){
    jQuery("#purchase-form").validate({
      rules: {
        quantity: "required" 
      },
      messages: {
        quantity: "Please enter a number"
      }    
    });
  }   

});