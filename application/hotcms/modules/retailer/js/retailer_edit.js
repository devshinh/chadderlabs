jQuery( document ).ready( function() {
   jQuery(".tabs").tabs();
   
   jQuery(".ajax_submit_contact").click(function() {  
    
     var id = jQuery(this).attr('id');     

    var ajax_url = "/hotcms/contact/edit_contact/"+jQuery('#editFormContact_'+ id + ' .contact_id').text()+"/"+jQuery('#editFormContact_'+ id + ' .connection_id').text()+"/"+jQuery('#editFormContact_'+ id + ' .module_back_url').text()+'/' + Math.random()*99999;
    jQuery.post(ajax_url, jQuery('#editFormContact_'+ id).serialize(),
      function(data){
        try{
          var JSONobj = JSON.parse(data);

          if (JSONobj['messages'] > '') {
            alert(JSONobj['messages']);
          }
        }
        catch(e){
          alert("Error: "+e.description);
        }
    });

     return false;
     });   
   
  // ---------- featured image related functions ----------

  jQuery("#logo-image-form").dialog({
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
      jQuery("#logo-image-form").empty();
    }
  }); 

  jQuery(".logo_image_link").click(function() {
    var link = jQuery(this);
    var retailer_id = link.data('id');
    var asset_id = link.attr("href");
    var asset_id_input = jQuery("input[name='logo_image_id']");
    var asset_preview = jQuery("#logo_image");
    var ajax_url = "/hotcms/organization/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#logo-image-form").html(json.content).dialog("open");
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
            jQuery.post("/hotcms/organization/ajax_update_image/" + retailer_id + "/" + asset_id + "/" + Math.random()*99999,
            {
              //asset: jQuery('#txtTinyMCE').val()
              },
            function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  //jQuery("input[name='draft_updated']").val("1");
                  //jQuery(preview_selector).load("/hotcms/news/ajax_display_body/" + retailer_id + "/" + Math.random()*99999);
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });      
            
            jQuery("#logo-image-form").dialog("close");
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
