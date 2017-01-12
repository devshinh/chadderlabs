jQuery( document ).ready( function() {
  if(jQuery(".tabs").length > 0){
    jQuery(".tabs").tabs();
    var cur_id = 0;
    var cur_page = self.document.location.hash.substring(1);
    switch (cur_page){
      case 'training-templates':
        cur_id = 1;
        break;
      case 'training-categories':
        cur_id = 2;
        break;
      case 'training-list':
      default:
        cur_id = 0;
    }
    jQuery("#training-tab").tabs('select', cur_id);
    setTimeout(function(){
      jQuery('html, body').animate({ scrollTop: 0 }, 0);
    }, 1);

  }


  //category tab
  jQuery('.edit_category_name').live("click", function(){
    var category_id = jQuery(this).attr("href");
    jQuery(this).closest(".category_header").load("training/ajax_category_edit_form/" + category_id + "/" + Math.random()*99999, function(){
      jQuery('.add_subcategory').each(function() {
        jQuery(this).addClass('disabled');
      });
      jQuery('.subcategory').each(function() {
        jQuery(this).addClass('disabled');
      });
      jQuery('.edit_category_name').each(function() {
        jQuery(this).addClass('disabled');
      });
      jQuery('.category_delete').each(function() {
        jQuery(this).addClass('disabled');
      });
      jQuery('.category_add').addClass('disabled');
    });
    return false;
  });

  jQuery('.cancel_category_link').live("click", function(){
    var category_id = jQuery(this).attr("href");
    jQuery(this).closest(".category_header").load("training/ajax_category_display/" + category_id + "/" + Math.random()*99999, function(){
      jQuery('.add_subcategory').each(function() {
        jQuery(this).removeClass('disabled');
      });
      jQuery('.subcategory').each(function() {
        jQuery(this).removeClass('disabled');
      });
      jQuery('.edit_category_name').each(function() {
        jQuery(this).removeClass('disabled');
      });
      jQuery('.category_delete').each(function() {
        jQuery(this).removeClass('disabled');
      });
      jQuery('.category_add').removeClass('disabled');
    });
    return false;
  });

  jQuery('.save_category_link').live("click", function(){
    var category_id = jQuery(this).attr("href");
    var category_div = jQuery(this).closest(".category_header")
    var category_name = jQuery("input[name='category_name_" + category_id + "']").val();

    var ajax_url = "training/ajax_save_category/" + category_id + "/" + Math.random()*99999;
    var postdata = {
      "name": category_name
    };


    if (category_id > 0 && category_name > '') {
      jQuery.post(ajax_url, postdata, function(data){
        try{
          var JSONobj = JSON.parse(data);
          if (JSONobj['result']) {
            category_div.load("training/ajax_category_display/" + category_id + "/" + Math.random()*99999, function(){
              jQuery('.add_subcategory').each(function() {
                jQuery(this).removeClass('disabled');
              });
              jQuery('.subcategory').each(function() {
                jQuery(this).removeClass('disabled');
              });
              jQuery('.edit_category_name').each(function() {
                jQuery(this).removeClass('disabled');
              });
              jQuery('.category_delete').each(function() {
                jQuery(this).removeClass('disabled');
              });
              jQuery('.category_add').removeClass('disabled');
            });
          //jQuery(this).before(JSONobj['question_form']);
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
    else {
      alert('Please enter all mandatory fields.');
    }
    return false;
  });
  //subcategories
  jQuery('.edit_subcategory').live("click", function(){
    var subcategory_id = jQuery(this).attr("href");

    jQuery('.add_subcategory').each(function() {
      jQuery(this).addClass('disabled');
    });
    jQuery('.subcategory').each(function() {
      jQuery(this).addClass('disabled');
    });
    jQuery('.edit_category_name').each(function() {
      jQuery(this).addClass('disabled');
    });
    jQuery('.category_delete').each(function() {
      jQuery(this).addClass('disabled');
    });
    jQuery('.category_add').addClass('disabled');

    jQuery(this).closest("tr").removeClass('disabled');
    jQuery(this).closest("tr").load("training/ajax_subcategory_edit_form/" + subcategory_id + "/" + Math.random()*99999, function(){
      //reorder_questions();
      });
    return false;
  });

  jQuery('.cancel_subcategory_link').live("click", function(){
    var category_id = jQuery(this).attr("href");
    jQuery(this).closest("tr").load("training/ajax_subcategory_display/" + category_id + "/" + Math.random()*99999, function(){
      jQuery('.add_subcategory').each(function() {
        jQuery(this).removeClass('disabled');
      });
      jQuery('.subcategory').each(function() {
        jQuery(this).removeClass('disabled');
      });
      jQuery('.edit_category_name').each(function() {
        jQuery(this).removeClass('disabled');
      });
      jQuery('.category_delete').each(function() {
        jQuery(this).removeClass('disabled');
      });
      jQuery('.category_add').removeClass('disabled');
    });
    return false;
  });

  jQuery('.save_subcategory_link').live("click", function(){
    var category_id = jQuery(this).attr("href");
    var category_div = jQuery(this).closest("tr")
    var category_name = jQuery("input[name='subcategory_name_" + category_id + "']").val();

    var ajax_url = "training/ajax_save_category/" + category_id + "/" + Math.random()*99999;
    var postdata = {
      "name": category_name
    };


    if (category_id > 0 && category_name > '') {
      jQuery.post(ajax_url, postdata, function(data){
        try{
          var JSONobj = JSON.parse(data);
          if (JSONobj['result']) {
            category_div.load("training/ajax_subcategory_display/" + category_id + "/" + Math.random()*99999, function(){
              //reorder_questions();
              jQuery('.add_subcategory').each(function() {
                jQuery(this).removeClass('disabled');
              });
            });
          //jQuery(this).before(JSONobj['question_form']);
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
    else {
      alert('Please enter all mandatory fields.');
    }
    return false;
  });

  //tempalte tab
  jQuery('.edit_type_tag_name').live("click", function(){
    var type_tag_id = jQuery(this).attr("href");
    jQuery(this).closest(".module_header").load("training/ajax_tag_type_name_edit_form/" + type_tag_id + "/" + Math.random()*99999, function(){
      //reorder_questions();
      });
    return false;
  });

  jQuery('.cancel_type_tag_link').live("click", function(){
    var type_tag_id = jQuery(this).attr("href");
    jQuery(this).closest(".module_header").load("training/ajax_tag_type_display/" + type_tag_id + "/" + Math.random()*99999, function(){
      //reorder_questions();
      });
    return false;
  });
  jQuery('.save_type_tag_link').live("click", function(){
    var type_tag_id = jQuery(this).attr("href");
    var type_tag_div = jQuery(this).closest(".module_header")
    var type_tag_name = jQuery("input[name='tag_type_name_" + type_tag_id + "']").val();

    var ajax_url = "training/ajax_save_tag_type/" + type_tag_id + "/" + Math.random()*99999;
    var postdata = {
      "name": type_tag_name
    };


    if (type_tag_id > 0 && type_tag_name > '') {
      jQuery.post(ajax_url, postdata, function(data){
        try{
          var JSONobj = JSON.parse(data);
          if (JSONobj['result']) {
            type_tag_div.load("training/ajax_tag_type_display/" + type_tag_id + "/" + Math.random()*99999, function(){
              //reorder_questions();
              });
          //jQuery(this).before(JSONobj['question_form']);
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
    else {
      alert('Please enter all mandatory fields.');
    }
    return false;
  });

  jQuery('.edit_tag').live("click", function(){
    var type_tag_id = jQuery(this).attr("href");
    jQuery(this).closest("tr").load("training/ajax_tag_name_edit_form/" + type_tag_id + "/" + Math.random()*99999, function(){
      });
    return false;
  });

  jQuery('.cancel_tag_link').live("click", function(){
    var type_tag_id = jQuery(this).attr("href");
    jQuery(this).closest("tr").load("training/ajax_tag_display/" + type_tag_id + "/" + Math.random()*99999, function(){
      });
    return false;
  });

  jQuery('.save_tag_link').live("click", function(){
    var tag_id = jQuery(this).attr("href");
    var tag_div = jQuery(this).closest("tr")
    var tag_name = jQuery("input[name='subtag_name_" + tag_id + "']").val();

    var ajax_url = "training/ajax_save_tag/" + tag_id + "/" + Math.random()*99999;
    var postdata = {
      "name": tag_name
    };


    if (tag_id > 0 && tag_name > '') {
      jQuery.post(ajax_url, postdata, function(data){
        try{
          var JSONobj = JSON.parse(data);
          if (JSONobj['result']) {
            tag_div.load("training/ajax_tag_display/" + tag_id + "/" + Math.random()*99999, function(){
              });
          //jQuery(this).before(JSONobj['question_form']);
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
    else {
      alert('Please enter all mandatory fields.');
    }
    return false;
  });

});
