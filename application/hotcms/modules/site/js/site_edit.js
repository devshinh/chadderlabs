jQuery( document ).ready( function() {
   
  //tabs
  if (jQuery(".tabs").length > 0) {
    jQuery(".tabs").tabs({selected: 0});
  }
  else {
    jQuery(".tabs").tabs();
  }    

  // ---------- featured image related functions ----------

  jQuery("#site-image-form").dialog({
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
      jQuery("#site-image-form").empty();
    }
  }); 
  
  jQuery(".site_image_link").click(function() {
    var link = jQuery(this);
    var site_id = link.data('id');
    var asset_id = link.attr("href");
    var asset_id_input = jQuery("input[name='site_image_id']");
    var asset_preview = jQuery("#site_image");
    var ajax_url = "/hotcms/site/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#site-image-form").html(json.content).dialog("open");
        jQuery.cookie("asset_id", asset_id);
        jQuery.cookie("asset_preview", asset_preview);
        jQuery("#formImage").bind("submit", function(){
          var asset_id = jQuery("input[name='asset_id']").val();
          //var asset_id = jQuery.cookie("asset_id");
          if (asset_id > '') {
            asset_id_input.val(asset_id);
            link.attr("href", asset_id);
            asset_preview.html(jQuery.cookie("asset_preview"));
            //save image id to db
            jQuery.post("/hotcms/site/ajax_update_image/" + site_id + "/" + asset_id + "/" + Math.random()*99999,
            {
              //asset: jQuery('#txtTinyMCE').val()
              },
            function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  //jQuery("input[name='draft_updated']").val("1");
                  //jQuery(preview_selector).load("/hotcms/news/ajax_display_body/" + news_id + "/" + Math.random()*99999);
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });      
            
            jQuery("#site-image-form").dialog("close");
          }
          else {
            alert("Please select an image.");
          }
          return false;
        });
      }
    }).error(function(){
      alert("Sorry but there was an error.");
    });
    return false;
  });
  
  jQuery("#site-modules .checkbox input").click(function() {  

    var module_id = jQuery(this).attr('name');

    jQuery.post("/hotcms/site/site_module_activation_ajax/" + module_id + "/" + Math.random()*99999,
 
    function(data){
     jQuery('#messageContainer').html('<div class="message confirm"><div class="message_close"><a onClick="closeMessage()">[close]</a></div>Site module was updated.</div>')                 
    },"json");      
      
  });
  
});
