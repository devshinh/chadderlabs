jQuery( document ).ready( function() {
  // tabs
  if (jQuery("#page-content").length > 0) {
    jQuery(".tabs").tabs({
      selected: 1
    });
  }
  else {
    jQuery(".tabs").tabs();
  }

  jQuery(".editable").hover(
    function () {
      jQuery(this).children(".section-buttons").slideDown("fast");
    },
    function () {
      jQuery(this).children(".section-buttons").slideUp("fast");
    }
    );

  //  jQuery('.draft').change(function(){
  //    jQuery("input[name='draft_updated']").val("1");
  //  });
  //  jQuery("select[name='status']").change(function(){
  //    jQuery("input[name='status_updated']").val("1");
  //  });
  //  jQuery(".schedule").change(function(){
  //    jQuery("input[name='schedule_updated']").val("1");
  //  });

  jQuery('.save_link').click(function(){
    save_draft(false);
    return false;
  });

  jQuery('.publish_link').click(function(){
    if (confirm('Are you sure you want to make these changes appear publicly?')) {
      jQuery('select[name=status]').val('1');
      //jQuery("input[name='status_updated']").val("1");
      save_draft(true);
    }
    return false;
  });

  jQuery('.archive_link').click(function(){
    if (confirm('Are you sure you want to archive this item?')) {
      jQuery('select[name=status]').val('2');
      //jQuery("input[name='status_updated']").val("1");
      save_draft(false);
    }
    return false;
  });
  
  // ---------- featured image related functions ----------

  jQuery("#news-image-form").dialog({
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
      jQuery("#news-image-form").empty();
    //      jQuery("#featured_image_form").each(function(){
    //        this.reset();
    //      });
    }
  }); 

  jQuery(".news_image_link").click(function() {
    var link = jQuery(this);
    var news_id = link.data('id');
    var asset_id = link.attr("href");
    var asset_id_input = jQuery("input[name='featured_image_id']");
    var asset_preview = jQuery("#news_image");
    var ajax_url = "/hotcms/news/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#news-image-form").html(json.content).dialog("open");
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
            jQuery.post("/hotcms/news/ajax_update_image/" + news_id + "/" + asset_id + "/" + Math.random()*99999,
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
            
            jQuery("#news-image-form").dialog("close");
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

  jQuery(".section-edit").click(function() {
    try {
      //var news_id = jQuery("input[name='news_id']").val();
      jQuery("#txtTinyMCE").val(jQuery("#sectionview_body .section-text").html());
      jQuery('#wrap').fadeTo(1, 0.5);
      jQuery('#footer').fadeTo(1, 0.5);
      jQuery(".dialog-wysiwyg").dialog('option', 'title', 'Text Editor');
      jQuery(".dialog-wysiwyg").dialog("open");
    }
    catch(err) {
      txt = "Sorry but there is an error on this page.\n\n";
      txt += err + "\n\n";
      txt += "If you keep getting this error, please contact our customer service.\n\n";
      txt += "Click OK to continue.\n\n";
      alert(txt);
    }
    return false;
  });
  
  jQuery("a.revert-revision").click(function() {
    try {
      if (confirm("Are you sure you wish to revert to this version?")) {
        var url_str = jQuery( this ).attr("href") + "/" + Math.random()*99999;
        var news_id = jQuery("input[name=news_id]").val();
        jQuery.get(url_str, function(data){
          try{
            var JSONobj = JSON.parse(data);
            if (JSONobj['result']) {
              window.location = "/hotcms/news/edit/" + news_id;
            }
            else if (JSONobj['messages'] > '') {
              alert(JSONobj['messages']);
            }
          }
          catch(e){
            alert("Error: "+e.description);
          }
        });
      }
    }
    catch(err) {
      txt = "Sorry but there is an error on this page.\n\n";
      txt += err + "\n\n";
      txt += "If you keep getting this error, please contact our customer service.\n\n";
      txt += "Click OK to continue.\n\n";
      alert(txt);
    }
    return false;
  });

  jQuery(".dialog-wysiwyg").dialog({
    autoOpen: false,
    width: 800,
    height: 680,
    modal: true,
    buttons: {
      "Update": {
        text: 'Update',
        className: 'arraw',
        click: function() {
          var news_id = jQuery("input[name='news_id']").val();
          var preview_selector = "#sectionview_body .section-text";
          //jQuery(text_selector).val(jQuery('#txtTinyMCE').val());
          jQuery.post("/hotcms/news/ajax_update_body/" + news_id + "/" + Math.random()*99999,
          {
            txtTinyMCE: jQuery('#txtTinyMCE').val()
            },
          function(data){
            try{
              var JSONobj = JSON.parse(data);
              if (JSONobj['result']) {
                //jQuery("input[name='draft_updated']").val("1");
                jQuery(preview_selector).load("/hotcms/news/ajax_display_body/" + news_id + "/" + Math.random()*99999);
              }
              if (JSONobj['messages'] > '') {
                alert(JSONobj['messages']);
              }
            }
            catch(e){
              alert("Error: "+e.description);
            }
          });
          jQuery('#wrap').fadeTo(1, 1);
          jQuery('#footer').fadeTo(1, 1);
          jQuery( this ).dialog( "close" );
        }
      },
      "Cancel": {
        text: 'Cancel',
        click: function() {
          jQuery( this ).dialog( "close" );
        }
      }
    },
    close: function() {
      jQuery('#wrap').fadeTo(1, 1);
      jQuery('#footer').fadeTo(1, 1);
    }
  });

  jQuery("#scheduled_publish_date, #scheduled_archive_date").datetimepicker({
    showOn: "button",
    buttonImage: "/hotcms/asset/images/icons/btn-calendar.png",
    buttonImageOnly: true,
    dateFormat: "yy-mm-dd"
  });

});

function save_draft(publish){
  var news_id = jQuery("input[name='news_id']").val();
  var ajax_url = "";
  ajax_url = "/hotcms/news/ajax_save/" + news_id;
  if (publish){
    ajax_url = ajax_url + "/publish";
  }
  ajax_url = ajax_url + "/" + Math.random()*99999;
  jQuery.post(ajax_url, jQuery("#news-form").serialize(),
    function(data){
      try{
        var JSONobj = JSON.parse(data);
        if (JSONobj['result']){
          //            jQuery("input[name='draft_updated']").val("0");
          //            jQuery("input[name='status_updated']").val("0");
          //            jQuery("input[name='schedule_updated']").val("0");
          if (jQuery('select[name=status]').val() == "2"){
            jQuery('.archive_link').hide();
          }
          else{
            jQuery('.archive_link').show();
          }
        }
        if (JSONobj['messages'] > '') {
          alert(JSONobj['messages']);
        }
      }
      catch(e){
        alert("Error: "+e.description);
      }
    });
//}
/*
  else if (jQuery("input[name='status_updated']").val() == "1"){
    if (jQuery('select[name=status]').val()=="1"){
      ajax_url = "/hotcms/news/ajax_publish/" + news_id + "/" + Math.random()*99999;
    }
    else if(jQuery('select[name=status]').val()=="2"){
      ajax_url = "/hotcms/news/ajax_archive/" + news_id + "/" + Math.random()*99999;
    }
    if (ajax_url > ""){
      jQuery.get( ajax_url,
        function(data){
          try{
            var JSONobj = JSON.parse(data);
            if (JSONobj['result']){
              jQuery("input[name='status_updated']").val("0");
              if (jQuery('select[name=status]').val() == "2"){
                jQuery('.archive_link').hide();
              }
              else{
                jQuery('.archive_link').show();
              }
            }
            if (JSONobj['messages'] > '') {
              alert(JSONobj['messages']);
            }
          }
          catch(e){
            alert("Error: "+e.description);
          }
      });
    }
  } */
}