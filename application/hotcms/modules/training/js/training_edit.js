jQuery( document ).ready( function() {

  var training_id = jQuery("input[name='training_id']").val();

  jQuery(".tabs-assets").tabs();
  jQuery(".tabs").tabs();

  jQuery('.save_link').click(function(){
    save_draft(false);
    return false;
  });

  jQuery('.publish_link').click(function(){
    if (confirm('Are you sure you want to make these changes appear publicly?')) {
      jQuery('select[name=status]').val('1');
      save_draft(true);
    }
    return false;
  });

  jQuery('.archive_link').click(function(){
    if (confirm('Are you sure you want to archive this item?')) {
      jQuery('select[name=status]').val('2');
      save_draft(false);
    }
    return false;
  });

  // ---------- featured image related functions ----------

  jQuery("#featured-image-form").dialog({
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
      jQuery("#featured-image-form").empty();
//      jQuery("#featured_image_form").each(function(){
//        this.reset();
//      });
    }
  });

  jQuery(".featured_image_link").click(function() {
    var link = jQuery(this);
    var asset_id = link.attr("href");
    var asset_id_input = jQuery("input[name='featured_image_id']");
    var asset_preview = jQuery("#featured_image");
    var ajax_url = "/hotcms/training/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#featured-image-form").html(json.content).dialog("open");
        jQuery.cookie("asset_id", asset_id);
        jQuery.cookie("asset_preview", asset_preview);
        jQuery("#formImage").bind("submit", function(){
          //var asset_id = jQuery("input[name='asset_id']").val();
          var asset_id = jQuery.cookie("asset_id");
          if (asset_id > '') {
            asset_id_input.val(asset_id);
            link.attr("href", asset_id);
            asset_preview.html(jQuery.cookie("asset_preview"));
            jQuery("#featured-image-form").dialog("close");
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

  // ---------- asset related functions ----------

  jQuery( "#asset-form" ).dialog({
    autoOpen: false,
    height: 660,
    width: 880,
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
      jQuery("#asset-form").empty();
    }
  });

  jQuery(".add_asset_image_link").click(function() {
    var asset_id = 0;
    var ajax_url = "/hotcms/training/ajax_image_chooser/" + asset_id + "/0/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#asset-form").html(json.content).dialog("open");
        jQuery.cookie("asset_id", null);
        jQuery.cookie("asset_preview", null);
        jQuery.cookie("asset_title", null);
        jQuery("#formImage").bind("submit", function(){
          //var asset_id = jQuery("input[name='asset_id']").val();
          var asset_id = jQuery.cookie("asset_id");
          if (asset_id == '') {
            alert("Please select an image.");
          }
          else {
            var ajax_url = "/hotcms/training/ajax_asset_add/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formImage").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery.cookie("asset_preview") + '</td><td>' + jQuery.cookie("asset_title") + '</td>';
                  row = row + '<td><a href="' + asset_id + '" class="edit_asset_link"><div class="btn-edit"></div></a></td>';
                  row = row +  '<td><a href="' + asset_id + '" class="delete_asset_link"><div class="btn-delete"></div></a></td>';
                  jQuery("#image_table tr:last").after('<tr class="asset_row">' + row + '</tr>');
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#asset-form" ).dialog( "close" );
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery(".add_asset_video_link").click(function() {
    var asset_id = 0;
    var ajax_url = "/hotcms/training/ajax_video_chooser/" + asset_id + "/0/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#asset-form").html(json.content)
        .dialog("option", "buttons", {
          "Select Video": function(){ jQuery("#formFile").submit(); },
          Cancel: function(){ jQuery(this).dialog("close"); }
        })
        .dialog("open");
        jQuery.cookie("asset_id", null);
        jQuery.cookie("asset_preview", null);
        jQuery.cookie("asset_title", null);
        jQuery("#formFile").bind("submit", function(){
          //var asset_id = jQuery("input[name='asset_id']").val();
          var asset_id = jQuery.cookie("asset_id");
          if (asset_id == '') {
            alert("Please select a video.");
          }
          else {
            var ajax_url = "/hotcms/training/ajax_asset_add/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formFile").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery.cookie("asset_preview") + '</td><td>' + jQuery.cookie("asset_title") + '</td>';
                  row = row + '<td><a href="' + asset_id + '" class="edit_video_link"><div class="btn-edit"></div></a></td>';
                  row = row +  '<td><a href="' + asset_id + '" class="delete_asset_link"><div class="btn-delete"></div></a></td>';
                  jQuery("#video_table tr:last").after('<tr class="asset_row">' + row + '</tr>');
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#asset-form" ).dialog( "close" );
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery(".add_asset_audio_link").click(function() {
    var asset_id = 0;
    var ajax_url = "/hotcms/training/ajax_audio_chooser/" + asset_id + "/0/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#asset-form").html(json.content)
        .dialog("option", "buttons", {
          "Select Audio": function(){ jQuery("#formFile").submit(); },
          Cancel: function(){ jQuery(this).dialog("close"); }
        })
        .dialog("open");
        jQuery.cookie("asset_id", null);
        jQuery.cookie("asset_preview", null);
        jQuery.cookie("asset_title", null);
        jQuery("#formFile").bind("submit", function(){
          //var asset_id = jQuery("input[name='asset_id']").val();
          var asset_id = jQuery.cookie("asset_id");
          if (asset_id == '') {
            alert("Please select an asset.");
          }
          else {
            var ajax_url = "/hotcms/training/ajax_asset_add/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formFile").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery.cookie("asset_preview") + '</td><td>' + jQuery.cookie("asset_title") + '</td>';
                  row = row + '<td><a href="' + asset_id + '" class="edit_audio_link"><div class="btn-edit"></div></a></td>';
                  row = row +  '<td><a href="' + asset_id + '" class="delete_asset_link"><div class="btn-delete"></div></a></td>';
                  jQuery("#audio_table tr:last").after('<tr class="asset_row">' + row + '</tr>');
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#asset-form" ).dialog( "close" );
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery("body").undelegate(".edit_asset_link", "click")
  .delegate(".edit_asset_link", "click", function() {
    var asset_id = jQuery( this ).attr("href");
    var tr = jQuery( this ).parents('tr.asset_row');
    var ajax_url = "/hotcms/training/ajax_image_chooser/" + asset_id + "/0/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#asset-form").html(json.content).dialog("open");
        jQuery("input[name='asset_id']").val(asset_id);
        jQuery.cookie("asset_id", asset_id);
        jQuery.cookie("asset_preview", jQuery("td:nth-child(1)", tr).html());
        jQuery.cookie("asset_title", jQuery("td:nth-child(2)", tr).html());
        jQuery("#formImage").bind("submit", function(){
          //var new_id = jQuery("input[name='asset_id']").val();
          var new_id = jQuery.cookie("asset_id");
          if (new_id > '') {
            var ajax_url = "/hotcms/training/ajax_asset_update/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formImage").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery.cookie("asset_preview") + '</td><td>' + jQuery.cookie("asset_title") + '</td><td>';
                  row = row + '<td><a href="' + new_id + '" class="edit_asset_link"><div class="btn-edit"></div></a></td>';
                  row = row + '<td><a href="' + new_id + '" class="delete_asset_link"><div class="btn-edit"></div></a></td>';
                  tr.html(row);
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#asset-form" ).dialog( "close" );
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

  jQuery("body").undelegate(".edit_video_link", "click")
  .delegate(".edit_video_link", "click", function() {
    var asset_id = jQuery( this ).attr("href");
    var tr = jQuery( this ).parents('tr.asset_row');
    var ajax_url = "/hotcms/training/ajax_video_chooser/" + asset_id + "/0/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#asset-form").html(json.content)
        .dialog("option", "buttons", {
          "Select Video": function(){ jQuery("#formFile").submit(); },
          Cancel: function(){ jQuery(this).dialog("close"); }
        })
        .dialog("open");
        jQuery("input[name='asset_id']").val(asset_id);
        jQuery.cookie("asset_id", asset_id);
        jQuery.cookie("asset_preview", jQuery("td:nth-child(1)", tr).html());
        jQuery.cookie("asset_title", jQuery("td:nth-child(2)", tr).html());
        jQuery("#formFile").bind("submit", function(){
          //var new_id = jQuery("input[name='asset_id']").val();
          var new_id = jQuery.cookie("asset_id");
          if (new_id > '') {
            var ajax_url = "/hotcms/training/ajax_asset_update/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formFile").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery.cookie("asset_preview") + '</td><td>' + jQuery.cookie("asset_title") + '</td><td>';
                  row = row + '<td><a href="' + new_id + '" class="edit_asset_link"><div class="btn-edit"></div></a></td>';
                  row = row + '<td><a href="' + new_id + '" class="delete_asset_link"><div class="btn-delete"></div></a></td>';
                  tr.html(row);
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#asset-form" ).dialog( "close" );
          }
          else {
            alert("Please select an asset.");
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery("body").undelegate(".edit_audio_link", "click")
  .delegate(".edit_audio_link", "click", function() {
    var asset_id = jQuery( this ).attr("href");
    var tr = jQuery( this ).parents('tr.asset_row');
    var ajax_url = "/hotcms/training/ajax_audio_chooser/" + asset_id + "/0/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#asset-form").html(json.content)
        .dialog("option", "buttons", {
          "Select Audio": function(){ jQuery("#formFile").submit(); },
          Cancel: function(){ jQuery(this).dialog("close"); }
        })
        .dialog("open");
        jQuery("input[name='asset_id']").val(asset_id);
        jQuery.cookie("asset_id", asset_id);
        jQuery.cookie("asset_preview", jQuery("td:nth-child(1)", tr).html());
        jQuery.cookie("asset_title", jQuery("td:nth-child(2)", tr).html());
        jQuery("#formFile").bind("submit", function(){
          //var new_id = jQuery("input[name='asset_id']").val();
          var new_id = jQuery.cookie("asset_id");
          if (new_id > '') {
            var ajax_url = "/hotcms/training/ajax_asset_update/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formFile").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery.cookie("asset_preview") + '</td><td>' + jQuery.cookie("asset_title") + '</td><td>';
                  row = row + '<td><a href="' + new_id + '" class="edit_asset_link"><div class="btn-edit"></div></a></td>';
                  row = row + '<td><a href="' + new_id + '" class="delete_asset_link"><div class="btn-delete"></div></a></td>';
                  tr.html(row);
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#asset-form" ).dialog( "close" );
          }
          else {
            alert("Please select an asset.");
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery("body").undelegate(".delete_asset_link", "click")
  .delegate(".delete_asset_link", "click", function() {
    if (confirmDelete('asset')) {
      var tr = jQuery( this ).parents('tr.asset_row');
      var asset_id = jQuery( this ).attr("href");
      var ajax_url = "/hotcms/training/ajax_asset_delete/" + training_id + "/"  + asset_id + "/" + Math.random()*99999;
      jQuery.getJSON(ajax_url, function(json) {
        if (json.result) {
          tr.remove();
        }
        if (json.messages > '') {
          alert(json.messages);
        }
      }).error(function(){ alert("Sorry but there was an error."); });
    }
    return false;
  });

  // ---------- variant related functions ----------

  jQuery( "#variant-form" ).dialog({
    autoOpen: false,
    height: 640,
    width: 640,
    modal: true,
    buttons: {
      "Submit": function() {
        jQuery("#variant_form").submit();
      },
      Cancel: function() {
        jQuery( this ).dialog( "close" );
      }
    },
    close: function() {
      jQuery("#variant-form").empty();
    }
  });

  jQuery(".add_variant_link").click(function() {
    var category_id = jQuery("select[name='category_id']").val();
    var ajax_url = "/hotcms/training/ajax_variant_add/" + training_id + "/" + category_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#variant-form").html(json.content).dialog("open");
        jQuery("#variant_form").bind("submit", function(){
          if ( jQuery("#variant_form").valid() ) {
            var category_id = jQuery("select[name='category_id']").val();
            var ajax_url = "/hotcms/training/ajax_variant_add/" + training_id + "/" + category_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#variant_form").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result'] && JSONobj['content'] > '') {
                  jQuery("#variant_table tr:last").after('<tr class="variant_row">' + JSONobj['content'] + '</tr>');
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#variant-form" ).dialog( "close" );
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery("body").undelegate(".edit_variant_link", "click")
  .delegate(".edit_variant_link", "click", function() {
    var category_id = jQuery("select[name='category_id']").val();
    var variant_id = jQuery( this ).attr("href");
    var tr = jQuery( this ).parents('tr.variant_row');
    var ajax_url = "/hotcms/training/ajax_variant_update/" + variant_id + "/" + category_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#variant-form").html(json.content).dialog("open");
        jQuery("#variant_form").bind("submit", function(){
          if ( jQuery("#variant_form").valid() ) {
            var category_id = jQuery("select[name='category_id']").val();
            var ajax_url = "/hotcms/training/ajax_variant_update/" + variant_id + "/" + category_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#variant_form").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result'] && JSONobj['content'] > '') {
                  //jQuery("#variant_table tr:last").after(JSONobj['content']);
                  tr.html(JSONobj['content']);
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#variant-form" ).dialog( "close" );
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery("body").undelegate(".delete_variant_link", "click")
  .delegate(".delete_variant_link", "click", function() {
    if (confirmDelete('variant')) {
      var tr = jQuery( this ).parents('tr.variant_row');
      var variant_id = jQuery( this ).attr("href");
      var ajax_url = "/hotcms/training/ajax_variant_delete/" + variant_id + "/" + Math.random()*99999;
      jQuery.getJSON(ajax_url, function(json) {
        if (json.result) {
          tr.remove();
        }
        if (json.messages > '') {
          alert(json.messages);
        }
      }).error(function(){ alert("Sorry but there was an error."); });
    }
    return false;
  });

  jQuery( "#variant-image-form" ).dialog({
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
      jQuery("#variant-image-form").empty();
    }
  });

  jQuery("body").undelegate(".variant_image_link", "click")
  .delegate(".variant_image_link", "click", function() {
    var asset_id = jQuery( this ).attr("href");
    var prev_area = jQuery( this ).siblings(".image_preview");
    var asset_field = jQuery( this ).siblings("input");
    var ajax_url = "/hotcms/training/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#variant-image-form").html(json.content).dialog("open");
        jQuery("#formImage").bind("submit", function(){
          var asset_id = jQuery("input[name='asset_id']").val();
          if (asset_id == '') {
            alert("Please select an image.");
          }
          else {
            asset_field.val(asset_id);
            prev_area.html(jQuery(".preview_area").html());
            jQuery("#variant-image-form").dialog("close");
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  // ---------- resource related functions ----------

  jQuery( "#resource-form" ).dialog({
    autoOpen: false,
    height: 600,
    width: 800,
    modal: true,
    buttons: {
      "Select File": function() {
        jQuery("#formFile").submit();
      },
      Cancel: function() {
        jQuery( this ).dialog( "close" );
      }
    },
    close: function() {
      jQuery("#resource-form").empty();
    }
  });

  jQuery(".add_resource_link").click(function() {
    var asset_id = 0;
    var ajax_url = "/hotcms/training/ajax_file_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#resource-form").html(json.content).dialog("open");      
        jQuery("#formFile").bind("submit", function(){
          var asset_id = jQuery("input[name='asset_id']").val();
          jQuery.cookie("resource_asset_id", asset_id);
          jQuery.cookie("resource_preview", jQuery(".preview_area").html());
          jQuery.cookie("resource_title", jQuery(".preview_title").html());
          if (asset_id == '') {
            alert("Please select a file.");
          }
          else {
            var ajax_url = "/hotcms/training/ajax_resource_add/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formFile").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery.cookie("resource_preview") + '</td><td>' + jQuery.cookie("resource_title") + '</td>';
                  row = row + '<td><a href="' + asset_id + '" class="edit_resource_link"><div class="btn-edit"></div></a></td>';
                  row = row +  '<td><a href="' + asset_id + '" class="delete_resource_link"><div class="btn-delete"></div></a></td>';
                  jQuery("#resource_table tr:last").after('<tr class="resource_row">' + row + '</tr>');
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#resource-form" ).dialog( "close" );
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery("body").undelegate(".edit_resource_link", "click")
  .delegate(".edit_resource_link", "click", function() {
    var asset_id = jQuery( this ).attr("href");
    var tr = jQuery( this ).parents('tr.resource_row');
    var ajax_url = "/hotcms/training/ajax_file_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#resource-form").html(json.content).dialog("open");
        jQuery("input[name='asset_id']").val(asset_id);
        jQuery("#formFile").bind("submit", function(){
          var new_id = jQuery("input[name='asset_id']").val();
          if (new_id == '') {
            alert("Please select a file.");
          }
          else {
            var ajax_url = "/hotcms/training/ajax_resource_update/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formFile").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery(".preview_area").html() + '</td><td>' + jQuery("td.preview_title").html() + '</td><td>';
                  row = row + '<td><a href="' + new_id + '" class="edit_resource_link"><div class="btn-edit"></div></a></td>';
                  row = row + '<td><a href="' + new_id + '" class="delete_resource_link"><div class="btn-delete"></div></a></td>';
                  tr.html(row);
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#resource-form" ).dialog( "close" );
          }
          return false;
        });
      }
    }).error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery("body").undelegate(".delete_resource_link", "click")
  .delegate(".delete_resource_link", "click", function() {
    if (confirmDelete('resource')) {
      var tr = jQuery( this ).parents('tr.resource_row');
      var asset_id = jQuery( this ).attr("href");
      var ajax_url = "/hotcms/training/ajax_resource_delete/" + training_id + "/"  + asset_id + "/" + Math.random()*99999;
      jQuery.getJSON(ajax_url, function(json) {
        if (json.result) {
          tr.remove();
        }
        if (json.messages > '') {
          alert(json.messages);
        }
      }).error(function(){ alert("Sorry but there was an error."); });
    }
    return false;
  });

  jQuery("body").undelegate("select[name=file_category_id]", "change")
  .delegate("select[name=file_category_id]", "change", function(){
    var category_id = jQuery(this).val();
    var asset_type = jQuery("input[name=asset_type]").val();
    var title1 = "";
    if (asset_type == 1) {
      title1 = "Group Images";
    }
    else if (asset_type == 2) {
      title1 = "Files";
    }
    else if (asset_type == 3) {
      title1 = "Videos";
    }
    else if (asset_type == 4) {
      title1 = "Audio";
    }
    if (category_id > 0) {
      var ajax_url = "/hotcms/media-library/ajax_assets/" + category_id + "/" + asset_type + "/" + Math.random()*99999;
      jQuery.getJSON(ajax_url, function(json) {
        if (json.formatted > '') {
          //jQuery(".asset_files").html("<fieldset><legend>Resources</legend>" + json.formatted + "</fieldset>");
          jQuery(".asset_files").html("<fieldset><legend>" + title1 + "</legend>" + json.formatted + "</fieldset>");
        }
        if (json.messages > '') {
          alert(json.messages);
        }
      }).error(function(){ alert("Sorry but there was an error."); });
      jQuery.get("/hotcms/media-library/ajax_asset_upload/" + category_id + "/" + asset_type + "/" + Math.random()*99999, null, function(data){
        jQuery(".asset_file_upload").html("<fieldset><legend>Upload File to Group</legend>" + data + "</fieldset>");
      });
    }
  });

  // ---------- revision related functions ----------

  jQuery("a.revert-revision").click(function() {
    if (confirm("Are you sure you wish to revert to this version?")) {
      var ajax_url = jQuery( this ).attr("href") + "/" + Math.random()*99999;
      jQuery.getJSON(ajax_url, function(json) {
        if (json.result) {
          window.location = "/hotcms/training/edit/" + training_id;
        }
        else if (json.messages > '') {
          alert(json.messages);
        }
      }).error(function(){ alert("Sorry but there was an error."); });
    }
    return false;
  });
  
  //drag and drop for image assets
    jQuery('#ui-sortable').sortable({
        update: function(event, ui) {
            var newOrder = '';
            jQuery("#ui-sortable").children().each(function(i) {
                var tr = jQuery(this);
                newOrder += "" + tr.attr("id") + '_';
            });
            var ajax_url = "training/ajax_save_training_image_assets_sequence/" + Math.random() * 99999;
            jQuery.get(ajax_url, {order: newOrder});
        }
    });  

});

function save_draft(publish){
  var training_id = jQuery("input[name='training_id']").val();
  var ajax_url = "/hotcms/training/ajax_save/" + training_id;
  if (publish){
    ajax_url = ajax_url + "/publish";
  }
  ajax_url = ajax_url + "/" + Math.random()*99999;
  jQuery.post(ajax_url, jQuery("#training-form").serialize(),
    function(data){
      try{
        var JSONobj = JSON.parse(data);
        if (JSONobj['result']){
          if (jQuery('select[name=status]').val() == "2"){
            jQuery('.archive_link').hide();
          }
          else{
            jQuery('.archive_link').show();
          }
          //TODO: update history list
          //JSONobj['content']
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