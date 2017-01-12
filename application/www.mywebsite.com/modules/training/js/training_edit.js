jQuery( document ).ready( function() {

  var training_id = jQuery("input[name='training_id']").val();

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

  jQuery( "#featured-image-form" ).dialog({
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

  jQuery(".featured_image_link").click(function() {
    var asset_id = jQuery( this ).attr("href");
    var ajax_url = "/hotcms/training/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#featured-image-form").html(json.content).dialog("open");
        jQuery("#formImage").bind("submit", function(){
          var asset_id = jQuery("input[name='asset_id']").val();
          if (asset_id == '') {
            alert("Please select an image.");
          }
          else {
            jQuery("input[name='featured_image_id']").val(asset_id);
            jQuery("#featured_image").html(jQuery("#preview_image").html());
            jQuery("#featured-image-form").dialog("close");
          }
          return false;
        });
      }
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  // ---------- asset related functions ----------

  jQuery( "#asset-form" ).dialog({
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
    }
  });

  jQuery(".add_asset_image_link").click(function() {
    var asset_id = 0;
    var ajax_url = "/hotcms/training/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#asset-form").html(json.content).dialog("open");
        jQuery("#formImage").bind("submit", function(){
          var asset_id = jQuery("input[name='asset_id']").val();
          if (asset_id == '') {
            alert("Please select an image.");
          }
          else {
            var ajax_url = "/hotcms/training/ajax_asset_add/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formImage").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery("#preview_image").html() + '</td><td>' + jQuery("td#image_title").html() + '</td>';
                  row = row + '<td><a href="' + asset_id + '" class="edit_asset_link">edit</a></td>';
                  row = row +  '<td><a href="' + asset_id + '" class="delete_asset_link">delete</a></td>';
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
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery(".edit_asset_link").live("click", function() {
    var asset_id = jQuery( this ).attr("href");
    var tr = jQuery( this ).parents('tr.asset_row');
    var ajax_url = "/hotcms/training/ajax_image_chooser/" + asset_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#asset-form").html(json.content).dialog("open");
        jQuery("input[name='asset_id']").val(asset_id);
        jQuery("#formImage").bind("submit", function(){
          var new_id = jQuery("input[name='asset_id']").val();
          if (new_id == '') {
            alert("Please select an image.");
          }
          else {
            var ajax_url = "/hotcms/training/ajax_asset_update/" + training_id + "/" + asset_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formImage").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery("#preview_image").html() + '</td><td>' + jQuery("td#image_title").html() + '</td><td>';
                  row = row + '<td><a href="' + new_id + '" class="edit_asset_link">edit</a></td>';
                  row = row + '<td><a href="' + new_id + '" class="delete_asset_link">delete</a></td>';
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
          return false;
        });
      }
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery(".delete_asset_link").live("click", function() {
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
      })
      .error(function(){ alert("Sorry but there was an error."); });
    }
    return false;
  });

  // ---------- variant related functions ----------

  jQuery( "#variant-form" ).dialog({
    autoOpen: false,
    height: 360,
    width: 480,
    modal: true,
    buttons: {
      "Create Variant": function() {
        jQuery("#variant_form").submit();
      },
      Cancel: function() {
        jQuery( this ).dialog( "close" );
      }
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
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery(".edit_variant_link").live("click", function() {
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
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery(".delete_variant_link").live("click", function() {
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
      })
      .error(function(){ alert("Sorry but there was an error."); });
    }
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
        jQuery("#formImage").submit();
      },
      Cancel: function() {
        jQuery( this ).dialog( "close" );
      }
    }
  });

  jQuery(".add_resource_link").click(function() {
    var resource_id = 0;
    var ajax_url = "/hotcms/training/ajax_file_chooser/" + resource_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#resource-form").html(json.content).dialog("open");
        jQuery("#formImage").bind("submit", function(){
          var resource_id = jQuery("input[name='resource_id']").val();
          if (resource_id == '') {
            alert("Please select an image.");
          }
          else {
            var ajax_url = "/hotcms/training/ajax_resource_add/" + training_id + "/" + resource_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formImage").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery("#preview_image").html() + '</td><td>' + jQuery("td#image_title").html() + '</td>';
                  row = row + '<td><a href="' + resource_id + '" class="edit_resource_link">edit</a></td>';
                  row = row +  '<td><a href="' + resource_id + '" class="delete_resource_link">delete</a></td>';
                  jQuery("#image_table tr:last").after('<tr class="resource_row">' + row + '</tr>');
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
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery(".edit_resource_link").live("click", function() {
    var resource_id = jQuery( this ).attr("href");
    var tr = jQuery( this ).parents('tr.resource_row');
    var ajax_url = "/hotcms/training/ajax_image_chooser/" + resource_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#resource-form").html(json.content).dialog("open");
        jQuery("input[name='resource_id']").val(resource_id);
        jQuery("#formImage").bind("submit", function(){
          var new_id = jQuery("input[name='resource_id']").val();
          if (new_id == '') {
            alert("Please select an image.");
          }
          else {
            var ajax_url = "/hotcms/training/ajax_resource_update/" + training_id + "/" + resource_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#formImage").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  var row = '<td>' + jQuery("#preview_image").html() + '</td><td>' + jQuery("td#image_title").html() + '</td><td>';
                  row = row + '<td><a href="' + new_id + '" class="edit_resource_link">edit</a></td>';
                  row = row + '<td><a href="' + new_id + '" class="delete_resource_link">delete</a></td>';
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
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery(".delete_resource_link").live("click", function() {
    if (confirmDelete('resource')) {
      var tr = jQuery( this ).parents('tr.resource_row');
      var resource_id = jQuery( this ).attr("href");
      var ajax_url = "/hotcms/training/ajax_resource_delete/" + training_id + "/"  + resource_id + "/" + Math.random()*99999;
      jQuery.getJSON(ajax_url, function(json) {
        if (json.result) {
          tr.remove();
        }
        if (json.messages > '') {
          alert(json.messages);
        }
      })
      .error(function(){ alert("Sorry but there was an error."); });
    }
    return false;
  });

  // ---------- revision related functions ----------

  jQuery("a.revert-revision").click(function() {
    try {
      if (confirm("Are you sure you wish to revert to this version?")) {
        var url_str = jQuery( this ).attr("href") + "/" + Math.random()*99999;
        var page_id = jQuery("input[name=page_id]").val();
        jQuery.get(url_str, function(data){
          try{
            var JSONobj = JSON.parse(data);
            if (JSONobj['result']) {
              window.location = "/hotcms/page/edit/" + page_id;
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