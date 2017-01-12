jQuery( document ).ready( function() {
  //console.log(jQuery.cookie('selectedTab'));
  if(jQuery(".tabs").length > 0){
    jQuery(".tabs").tabs({
     selected : (jQuery.cookie('selectedTab') || 0)
    });
  }
  
  jQuery('#quiz-setting-form').validate();

  
  if(jQuery("#new_quiz_form #name").length > 0){
     
    jQuery("#quiz_type").change(function(){
      setName();      
    });
    
    jQuery("#training").change(function(){
      setName();
      setTarget(jQuery(this).val());
    });    
  } 
  
  // ---------- icon image related functions ----------

  jQuery( "#icon-image-form" ).dialog({
    autoOpen: false,
    height: 600,
    width: 800,
    modal: true,
    buttons: {
      "Select Image": function() {
        jQuery("#formImage").submit();
      },
      Cancel: function() {
        jQuery( this ).dialog( "close" );
      }
    },
    close: function() {
//      jQuery("#featured_image_form").each(function(){
//        this.reset();
//      });
    }
  });
  
  jQuery(".icon_image_link").click(function() {
    var link = jQuery(this);
    var asset_id = link.attr("href");
    var type_id = link.attr("id");
    var asset_id_input = jQuery("input[name='icon_image_id_"+ type_id+"']");
    var asset_preview = jQuery("#featured_image");
    var ajax_url = "/hotcms/quiz/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#icon-image-form").html(json.content).dialog("open");
        jQuery.cookie("asset_id", asset_id);
        jQuery.cookie("asset_preview", asset_preview);
        jQuery("#formImage").bind("submit", function(){
          //var asset_id = jQuery("input[name='asset_id']").val();
          var asset_id = jQuery.cookie("asset_id");
          if (asset_id > '') {
            asset_id_input.val(asset_id);
            link.attr("href", asset_id);
            asset_preview.html(jQuery.cookie("asset_preview"));
            jQuery("#icon-image-form").dialog("close");
          }
          else {
            alert("Please select an image.");
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });  
  
});
// Just get the values you want and update the target
function setName(){
  var str = '';
  jQuery("select#quiz_type option:selected").each(function () {
    //console.log(jQuery(this).val());
    if(jQuery(this).val() !== ""){
      str += jQuery(this).text();
    }
  });
    
  jQuery("select#trainning option:selected").each(function () {
    if(jQuery(this).val() !== ""){
      str +=' '+jQuery(this).text();
    }
  });
    
  jQuery('#name').val(str);
}
// Get selected training's target
function setTarget(training) {
  if (jQuery("#follow").is(":checked")) {
    console.log(training);
    var trainingsJSON = jQuery("input[name=targetOfTrainings]").val();
    console.log(trainingsJSON);
    var trainingsObj = jQuery.parseJSON(trainingsJSON);
    console.log(trainingsObj);
    jQuery.each(trainingsObj, function(key, value){
      if (key == training) {
        console.log(value);
        jQuery("#target").val(value);
        return false;
      }
    });
  } else {
    console.log("target not follow training");
  }
}