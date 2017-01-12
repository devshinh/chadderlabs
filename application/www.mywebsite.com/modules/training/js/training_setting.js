jQuery( document ).ready( function() {

  jQuery( "#variant-type-form" ).dialog({
    autoOpen: false,
    height: 360,
    width: 480,
    modal: true,
    buttons: {
      "Create Variant Type": function() {
        jQuery("#new_variant_type_form").submit();
      },
      Cancel: function() {
        jQuery( this ).dialog( "close" );
      }
    },
    close: function() {
      jQuery("#new_variant_type_form").each(function(){
        //this.removeClass('error');
        this.reset();
      });
    }
  });

  jQuery(".add_variant_type_link").click(function() {
    var category_id = jQuery("select[name='training_category']").val();
    var ajax_url = "/hotcms/training/ajax_variant_type_add/" + category_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        jQuery("#variant-type-form").html(json.content).dialog("open");
        jQuery("#new_variant_type_form").bind("submit", function(){
          if ( jQuery("#new_variant_type_form").valid() ) {
            var category_id = jQuery("select[name='training_category']").val();
            var link = jQuery("a.add_variant_type_link").parent(".row");
            var ajax_url = "/hotcms/training/ajax_variant_type_add/" + category_id + "/" + Math.random()*99999;
            jQuery.post(ajax_url, jQuery("#new_variant_type_form").serialize(), function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result'] && JSONobj['content'] > '') {
                  link.before('<div class="variant_type row editable" id="field_id_' + JSONobj['result'] + '">' + JSONobj['content'] + '</div>');
                }
                if (JSONobj['messages'] > '') {
                  alert(JSONobj['messages']);
                }
              }
              catch(e){
                alert("Error: "+e.description);
              }
            });
            jQuery( "#variant-type-form" ).dialog( "close" );
          }
          return false;
        });
      }
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery('.edit_variant_type_link').live("click", function(){
    var link = jQuery(this);
    var parent_div = link.parents(".variant_type");
    var field_id = link.attr("href");
    var ajax_url = "/hotcms/training/ajax_variant_type_edit/" + field_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        parent_div.html(json.content);
        parent_div.children("form.field_config_form").validate();
      }
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery('.cancel_variant_type_link').live("click", function(){
    var link = jQuery(this);
    var field_id = link.attr("href");
    var ajax_url = "/hotcms/training/ajax_variant_type_ui/" + field_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, function(json) {
      if (json.result && json.content > '') {
        link.parents(".variant_type").html(json.content);
      }
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  jQuery('.save_variant_type_link').live("click", function(){
    var field_id = jQuery(this).attr("href");
    var parent_div = jQuery(this).parents(".variant_type");
    var config_form = parent_div.children("form.field_config_form");
    var ajax_url = "/hotcms/training/ajax_variant_type_edit/" + field_id + "/" + Math.random()*99999;
    if (config_form.valid()) {
      jQuery.post(ajax_url, config_form.serialize(), function(data){
        try{
          var JSONobj = JSON.parse(data);
          if (JSONobj['result'] && JSONobj['content'] > '') {
            parent_div.html(JSONobj['content']);
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
    return false;
  });

  jQuery('.delete_variant_type_link').live("click", function(){
    if (!confirmDelete('variant type')) {
      return false;
    }
    var link = jQuery(this);
    var field_id = link.attr("href");
    var ajax_url = "/hotcms/training/ajax_variant_type_delete/" + field_id + "/" + Math.random()*99999;
    jQuery.getJSON(ajax_url, {action: "delete"}, function(json) {
      if (json.result) {
        link.parents(".variant_type").remove();
      }
      else if (json.messages > '') {
        alert(json.messages);
      }
    })
    .error(function(){ alert("Sorry but there was an error."); });
    return false;
  });

  // drag 'n' drop
  var sort_item_height = 30;
  jQuery(".droppable-zone").sortable({
    connectWith: ".droppable-zone",
    handle: ".variant_type_name",
    cancel: ".section-text",
    cursor: "move",
    placeholder: "drop-placeholder",
    tolerance: "pointer",
    forcePlaceholderSize: true,
    helper: function( event ) {
      return jQuery( "<div class='drag-helper' style='width:300px;height:30px;'>Drag&drop to a target area.</div>" );
    },
    start: function (event,ui) {
      sort_item_height = ui.item.height();
      jQuery(".droppable-zone").css("min-height", sort_item_height);
    },
    stop: function (event,ui) {
      jQuery(".droppable-zone").css("min-height", "0");
    },
    update: function( event, ui ) {
      if (ui.item.attr("id") != undefined) {
        var field_id = ui.item.attr("id").substring(9);
        // rearrange all item within the same zone
        jQuery(".editable", this).each(function(e){
          field_id = jQuery(this).attr("id").substring(9);
          //jQuery("input[name='section_sequence["+section_id+"]']").val(jQuery(this).index());
          jQuery.post("/hotcms/training/ajax_variant_type_edit/" + field_id + "/" + Math.random()*99999,
            {sequence: jQuery(this).index()+1},
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
        });
      }
    }
  });

});
