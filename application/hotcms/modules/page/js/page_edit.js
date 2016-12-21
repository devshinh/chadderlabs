//TODO: basic client side form validation using jQuery - config_widget
jQuery( document ).ready( function() {
  // page publisher functions
  if (jQuery("#page-content").length > 0) {
    jQuery(".tabs").tabs({selected: 2});
  }
  else {
    jQuery(".tabs").tabs();
  }
  
  //function for hide/show widget panel
  if (jQuery('#widget-panel').length > 0) {
    jQuery('#widget-panel .widget_expander').click( function(){
       if(jQuery('#widget-panel').hasClass('expanded')){
           jQuery(this).removeClass('expanded').addClass('collapsed');
           jQuery('#widget-panel').removeClass('expanded').addClass('collapsed');
           jQuery('#widget-panel .widget_expander i').removeClass('icon-angle-up').addClass('icon-angle-down');
           jQuery('#new-widgets').hide();
           jQuery('#widget-help-text').hide();
       } else {
           jQuery(this).removeClass('collapsed').addClass('expanded');
           jQuery('#widget-panel').removeClass('collapsed').addClass('expanded');
           jQuery('#widget-panel .widget_expander i').removeClass('icon-angle-down').addClass('icon-angle-up');
           jQuery('#new-widgets').show();         
           jQuery('#widget-help-text').show();
       }
    });
  }

  //jQuery(".editable, .cloneable").hover(
  jQuery(".editable").hover(
    function () {
      jQuery(this).children(".section-buttons").slideDown("fast");
    },
    function () {
      jQuery(this).children(".section-buttons").slideUp("fast");
    }
  );

  jQuery(".section-edit").click(function() {
    try {
      var section_id = jQuery( this ).parents(".editable").attr("id").substring(12);
      jQuery("input[name='editing_section']").val(section_id);
      jQuery("#txtTinyMCE").val(jQuery("#sectionview_" + section_id + " .section-text").html());
      //tinyMCE.get( 'txtTinyMCE' ).setContent( jQuery("input[name='section_"+section_id+"']").val() );
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

  jQuery(".section-config").click(function() {
    try {
      var page_id = jQuery("input[name='page_id']").val();
      var section_id = jQuery( this ).parents(".editable").attr("id").substring(12);
      var widget_name = jQuery( this ).siblings(".section-move").text();
      jQuery("input[name='editing_section']").val(section_id);
      jQuery("#widget-config").load("/hotcms/page/ajax_config_section/" + page_id + "/" + section_id + "/" + Math.random()*99999);
      jQuery('#wrap').fadeTo(1, 0.5);
      jQuery('#footer').fadeTo(1, 0.5);
      jQuery(".dialog-config").dialog('option', 'title', widget_name);
      jQuery(".dialog-config").dialog("open");
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

  jQuery(".section-delete").click(function() {
    try {
      if (confirmDelete('section')) {
        var page_id = jQuery("input[name='page_id']").val();
        var section_id = jQuery( this ).parents(".editable").attr("id").substring(12);
        jQuery.get("/hotcms/page/ajax_delete_section/" + page_id + "/"  + section_id + "/" + Math.random()*99999, function(data){
          try{
            var JSONobj = JSON.parse(data);
            if (JSONobj['result']) {
              jQuery("input[name='draft_updated']").val("1");
              jQuery("#sectionview_"+section_id).remove();
              //jQuery("input[name='section["+section_id+"]']").remove();
              //jQuery("input[name='section_type["+section_id+"]']").remove();
              //jQuery("input[name='section_zone["+section_id+"]']").remove();
              //jQuery("input[name='section_sequence["+section_id+"]']").remove();
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
          var page_id = jQuery("input[name='page_id']").val();
          var section_id = jQuery("input[name='editing_section']").val();
          var preview_selector = "#sectionview_" + section_id + " .section-text";
          //jQuery(text_selector).val(jQuery('#txtTinyMCE').val());
          jQuery.post("/hotcms/page/ajax_update_section/" + page_id + "/" + section_id + "/" + Math.random()*99999,
            {txtTinyMCE: jQuery('#txtTinyMCE').val()},
            function(data){
              try{
                var JSONobj = JSON.parse(data);
                if (JSONobj['result']) {
                  jQuery("input[name='draft_updated']").val("1");
                  //jQuery(preview_selector).html(jQuery('#txtTinyMCE').val());
                  jQuery(preview_selector).load("/hotcms/page/ajax_display_section/" + section_id + "/" + Math.random()*99999);
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

  jQuery(".dialog-config").dialog({
    autoOpen: false,
    width: 800,
    height: 800,
    modal: true,
    buttons: {
      "Close": {
        text: 'Close',
        click: function() {
          jQuery( this ).dialog( "close" );
        }
      }
    },
    close: function() {
      if (jQuery("input[name='section_updated']").val() == '1') {
        var section_id = jQuery("input[name='editing_section']").val();
        jQuery("#sectionview_"+section_id).children(".section-text").load("/hotcms/page/ajax_display_section/" + section_id + "/" + Math.random()*99999);
        jQuery("input[name='section_updated']").val("0");
      }
      jQuery("#widget-config").html("");
      jQuery('#wrap').fadeTo(1, 1);
      jQuery('#footer').fadeTo(1, 1);
    }
  });

  /*
  jQuery("select[name=widget]").change(function(){
    jQuery("input#insert-type-1").prop('checked', true);
  }); */

  // drag 'n' drop
  var sort_item_height = 30;
  jQuery(".droppable-zone").sortable({
    connectWith: ".droppable-zone",
    handle: ".section-move",
    cancel: ".section-text",
    cursor: "move",
    placeholder: "drop-placeholder",
    tolerance: "pointer",
    forcePlaceholderSize: true,
    helper: function( event ) {
      return jQuery( "<div class='drag-helper' style='width:170px;height:30px;'>Drag&drop to a target area.</div>" );
    },
    start: function (event,ui) {
      sort_item_height = ui.item.height();
      jQuery(".droppable-zone").css("min-height", sort_item_height);
    },
    stop: function (event,ui) {
      jQuery(".droppable-zone").css("min-height", "0");
      var zone_obj = jQuery(this);
      var layout_zone = jQuery(this).attr("id");
      if (ui.item.hasClass("cloneable")) {
        // TODO: resort all sections within this zone
        ui.item.removeClass("cloneable").addClass("editable");
        try {
          var section_type = '0';
          var module_widget = "";
          if (ui.item.hasClass("section-widget")) {
            section_type = '1';
            module_widget = ui.item.children(".widget-code").text();
          }
          var page_id = jQuery("input[name=page_id]").val();
          jQuery.get("/hotcms/page/ajax_add_section/" + page_id + "/" + section_type + "/" + layout_zone + "/" + module_widget + "/" + Math.random()*99999, function(data){
            try{
              var JSONobj = JSON.parse(data);
              if (JSONobj['result']) {
                var section_id = JSONobj['section_id'];
                var section_content = "";
                ui.item.attr('id', 'sectionview_'+section_id);
                ui.item.children(".section-buttons").remove();
                if (section_type == '0') {
                  section_content = jQuery("input[name=demo_text]").val();
                  ui.item.children(".section-text").html(section_content);
                  jQuery("#sectionview_text .section-buttons").clone(true).prependTo(ui.item);
                }
                else {
                  section_content = module_widget;
                  jQuery("#sectionview_widget .section-buttons").clone(true).prependTo(ui.item);
                  ui.item.children(".section-buttons").children(".section-move").html('<span class="move-icon"></span>' + ui.item.children(".section-text").text() );
                  //ui.item.children(".section-text").html('<p>This is an empty ' + ui.item.children(".section-text").text() + ' widget.<br />Click here to edit.</p>' );
                  ui.item.children(".section-text").load("/hotcms/page/ajax_display_section/" + section_id + "/" + Math.random()*99999);
                }
                //jQuery('<input>').attr({type: 'hidden', name: 'section['+section_id+']', value: section_content}).appendTo('#page-form');
                //jQuery('<input>').attr({type: 'hidden', name: 'section_type['+section_id+']', value: section_type}).appendTo('#page-form');
                //jQuery('<input>').attr({type: 'hidden', name: 'section_zone['+section_id+']', value: section_zone}).appendTo('#page-form');
                //jQuery('<input>').attr({type: 'hidden', name: 'section_sequence['+section_id+']', value: ui.item.index()}).appendTo('#page-form');
                ui.item.hover(
                  function () {
                    jQuery(this).children(".section-buttons").show();
                  },
                  function () {
                    jQuery(this).children(".section-buttons").hide();
                  }
                );
                // rearrange all sections within the same zone
                zone_obj.children(".editable").each(function(e){
                  section_id = jQuery(this).attr("id").substring(12);
                  //jQuery("input[name='section_sequence["+section_id+"]']").val(jQuery(this).index());
                  jQuery.post("/hotcms/page/ajax_rearrange_section/" + page_id + "/" + section_id + "/" + Math.random()*99999,
                    {zone: layout_zone, sequence: jQuery(this).index()+1},
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
                jQuery("input[name='draft_updated']").val("1");
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
        catch(e) {
          jQuery('#wrap').fadeTo(1, 1);
          jQuery('#footer').fadeTo(1, 1);
          alert('An error has occurred: ' + e.message);
          jQuery( this ).dialog( "close" );
        }
      }
    },
    update: function( event, ui ) {
      if (ui.item.attr("id") != undefined) {
        var page_id = jQuery("input[name=page_id]").val();
        var section_id = ui.item.attr("id").substring(12);
        //jQuery("input[name='section_zone["+section_id+"]']").val(jQuery(this).attr("id"));
        var layout_zone = jQuery(this).attr("id");
        // rearrange all sections within the same zone
        jQuery(".editable", this).each(function(e){
          section_id = jQuery(this).attr("id").substring(12);
          //jQuery("input[name='section_sequence["+section_id+"]']").val(jQuery(this).index());
          jQuery.post("/hotcms/page/ajax_rearrange_section/" + page_id + "/" + section_id + "/" + Math.random()*99999,
            {zone: layout_zone, sequence: jQuery(this).index()+1},
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
        jQuery("input[name='draft_updated']").val("1");
      }
    }
  });

  jQuery(".cloneable").draggable({
    connectToSortable: ".droppable-zone",
    revert: "invalid",
    helper: function( event ) {
		  return jQuery( "<div class='drag-helper' style='width:170px;height:30px;'>Drag&drop to a target area.</div>" );
    }
  });

/*
  jQuery('#media-library-upload #upload_cancel').click(function() {
   	//console.log('cancel');
		jQuery('#dialog-upload').css('display','none').css('z-index',-1);
		jQuery(".dialog-wysiwyg").dialog("open");
  });

	jQuery('#media-library-upload').submit(function(e) {
    //e.preventDefault();
    var error_flag = false;
      if(jQuery('#media-library-upload #asset_name').val() == '')
      {
    jQuery('#media-library-upload #asset_name_error').html('Image name is required').show();
        error_flag = true;
    }
    else {
    jQuery('#media-library-upload #asset_name_error').hide();
    }
      if(jQuery('#media-library-upload #asset_description').val() == '')
      {
    jQuery('#media-library-upload #asset_description_error').html('Image description is required').show();
        error_flag = true;
    } else {
      jQuery('#media-library-upload #asset_description_error').hide();
    }
    //console.log(error_flag);
    return !error_flag;
      //e.preventDefault();
      /*
      jQuery.ajaxFileUpload({
       url         :'/hotcms/media-library/ajax_upload/',
       secureuri      :false,
       fileElementId  :'asset_file',
       dataType    : 'json',
       data        : {
          'name'           : jQuery('#asset_name').val()
       },
       success  : function (data, status)
       {
          if(data.status != 'error')
          {
            console.log('success');
          }
          alert(data.msg);
       }
    });
    return false;
    * /
  });
*/
	/*
	jQuery('.divTinyMCE .upload #btnSelect_tinymce').click(function(){
		document.getElementById("upload_target").onload = uploadDone;
		//tinymce.get('txtTinyMCE').hide();
		jQuery(".dialog-wysiwyg").dialog("close");
		jQuery('#media-library-upload #asset_name').val('');
		jQuery('#media-library-upload #asset_description').val('');
		jQuery('#media-library-upload #asset_file').val('');
		jQuery('#dialog-upload, #dialog-upload-contents').css('display','block').css('z-index',1200);
		jQuery('#dialog-upload .error').css('display','none');
//		var uploader = new qq.FileUploaderBasic({
//		    // pass the dom node (ex. $(selector)[0] for jQuery users)
//		    element: document.getElementById('test_upload'),
//		    // path to server-side upload script
//		    action: '/hotcms/media-library/ajax_upload',
//		    debug: true,
//		    showMessage: function(message){ alert(message); }
//		});
//		alert('test');
	});
	*/

  jQuery('input.draft').change(function(){
    jQuery("input[name='draft_updated']").val("1");
  });
  jQuery('input.page_menu').change(function(){
    jQuery("input[name='menu_updated']").val("1");
  });
  jQuery("select[name='status']").change(function(){
    jQuery("input[name='status_updated']").val("1");
  });

  if (jQuery("#menu_visible").is(":checked")){
    jQuery("#menu_title").removeAttr("readonly").css("background-color", "#fff");
  }
  else{
    jQuery("#menu_title").attr("readonly", true).css("background-color", "#ccc");
  }
  jQuery("#menu_visible").change(function(){
    if (jQuery(this).is(":checked")){
      jQuery("#menu_title").removeAttr("readonly").css("background-color", "#fff");
    }
    else{
      jQuery("#menu_title").attr("readonly", true).css("background-color", "#ccc");
    }
  });

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
    if (confirm('Are you sure you want to archive this page?')) {
      jQuery('select[name=status]').val('2');
      //jQuery("input[name='status_updated']").val("1");
      save_draft(false);
    }
    return false;
  });

  jQuery("#minmaxWrapper").click(function (){
    if (jQuery('#templateHeaderWrapper').hasClass('minimazed')){
      jQuery('#templateHeaderWrapper').removeClass('minimazed').addClass('maximazed');
      jQuery('#minmaxWrapper').html('- Minimaze');
      jQuery('#templateWrapper').show('fast');
    }
    else if (jQuery('#templateHeaderWrapper').hasClass('maximazed')){
      jQuery('#templateHeaderWrapper').removeClass('maximazed').addClass('minimazed');
      jQuery('#templateWrapper').hide('fast');
      jQuery('#minmaxWrapper').html('+ Maximize');
    }
  });

});

function save_draft(publish){
  var page_id = jQuery("input[name='page_id']").val();
  var ajax_url = "/hotcms/page/ajax_save/" + page_id;
  //if (jQuery("input[name='draft_updated']").val() == "1" || jQuery("input[name='menu_updated']").val() == "1"){
  if (publish){
    ajax_url = ajax_url + "/publish";
  }
  ajax_url = ajax_url + "/" + Math.random()*99999;
  jQuery.post(ajax_url, jQuery("#page-form").serialize(),
    function(data){
      try{
        var JSONobj = JSON.parse(data);
        if (JSONobj['result']){
          //jQuery("input[name='menu_updated']").val("0");
          //jQuery("input[name='draft_updated']").val("0");
          //jQuery("input[name='status_updated']").val("0");
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
  /* else if (jQuery("input[name='status_updated']").val() == "1"){
    if (jQuery('select[name=status]').val()=="1"){
      ajax_url = "/hotcms/page/publish/" + page_id + "/" + Math.random()*99999;
    }
    else if(jQuery('select[name=status]').val()=="2"){
      ajax_url = "/hotcms/page/archive/" + page_id + "/" + Math.random()*99999;
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

function config_widget(obj) {
  var page_id = jQuery("input[name='page_id']").val();
  var section_id = jQuery("input[name='editing_section']").val();
  var dataString = jQuery(obj).serialize();
  jQuery.ajax({
    type: "POST",
    url: "/hotcms/page/ajax_config_section/" + page_id + "/" + section_id + "/" + Math.random()*99999,
    data: dataString,
    success: function() {
      jQuery("input[name='draft_updated']").val("1");
      jQuery("input[name='section_updated']").val("1");
      jQuery("#widget-config").load("/hotcms/page/ajax_config_section/" + page_id + "/" + section_id + "/" + Math.random()*99999);
      alert('Settings updated.');
    }
  });
  return false;
}

/*
function uploadDone()
{
	var ret = frames['upload_target'].document.getElementsByTagName("body")[0].innerHTML;
	var data = eval("("+ret+")"); //Parse JSON
	//console.log(data);
	if(data.status == "success") {
		tinyMCE.execCommand('mceInsertContent',false,'<img src="'+ data.msg +'"/>');
		jQuery('#dialog-upload').css('display','none').css('z-index',-1);
		jQuery(".dialog-wysiwyg").dialog("open");
	} else {
		jQuery('#media-library-upload #asset_upload_error').html(data.msg).show();
	}
}
*/