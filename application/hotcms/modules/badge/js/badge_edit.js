jQuery( document ).ready( function() {

  // ---------- featured image related functions ----------

  jQuery("#icon-image-form").dialog({
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
      jQuery("#icon-image-form").empty();
    }
  }); 

  jQuery(".icon_image_link").click(function() {
    var link = jQuery(this);
    var news_id = link.data('id');
    var asset_id = link.attr("href");
    var asset_id_input = jQuery("input[name='icon_image_id']");
    var asset_preview = jQuery("#icon_image");
    var ajax_url = "/hotcms/badge/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#icon-image-form").html(json.content).dialog("open");
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
            jQuery.post("/hotcms/badge/ajax_update_image/" + news_id + "/" + asset_id + "/" + Math.random()*99999,
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
            
            jQuery("#icon-image-form").dialog("close");
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
  

  jQuery("#hover-image-form").dialog({
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
      jQuery("#hover-image-form").empty();
    }
  }); 

  jQuery(".hover_image_link").click(function() {
    var link = jQuery(this);
    var news_id = link.data('id');
    var asset_id = link.attr("href");
    var asset_id_input = jQuery("input[name='icon_image_id']");
    var asset_preview = jQuery("#icon_image");
    var ajax_url = "/hotcms/badge/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#hover-image-form").html(json.content).dialog("open");
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
            jQuery.post("/hotcms/badge/ajax_update_big_image/" + news_id + "/" + asset_id + "/" + Math.random()*99999,
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
            
            jQuery("#hover-image-form").dialog("close");
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
  
});
