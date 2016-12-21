jQuery( document ).ready( function() {

  jQuery('.save_link').click(function(){
    var type_id = jQuery(this).attr("href");
    var ajax_url = "/hotcms/quiz/ajax_setting_update/" + type_id + "/" + Math.random()*99999;
    var name = jQuery("input[name='name_" + type_id + "']").val();
    var time_limit = jQuery("input[name='time_limit_" + type_id + "']").val();
    var expiry_period = jQuery("input[name='expiry_period_" + type_id + "']").val();
    var tries_per_day = jQuery("input[name='tries_per_day_" + type_id + "']").val();
    var tries_per_week = jQuery("input[name='tries_per_week_" + type_id + "']").val();
    var points_pre_expiry = jQuery("input[name='points_pre_expiry_" + type_id + "']").val();
    var points_post_expiry = jQuery("input[name='points_post_expiry_" + type_id + "']").val();
    var postdata = {"name": name, "time_limit": time_limit, "expiry_period": expiry_period, "tries_per_day": tries_per_day,
      "tries_per_week": tries_per_week, "points_pre_expiry": points_pre_expiry, "points_post_expiry": points_post_expiry};
    jQuery.post(ajax_url, postdata, function(data){
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

  jQuery('.add_section_link').click(function(){
    var type_id = jQuery(this).attr("href");
    var link = jQuery(this);
    var ajax_url = "/hotcms/quiz/ajax_setting_add_section/" + type_id + "/" + Math.random()*99999;
    jQuery.post(ajax_url, {"type_id": type_id}, function(data){
      try{
        var JSONobj = JSON.parse(data);
        if (JSONobj['result'] && JSONobj['section_form'] > '') {
          link.before('<div class="quiz_section">' + JSONobj['section_form'] + '</div>');
          reorder_sections();
        }
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

  jQuery('.save_section_link').live("click", function(){
    var section_id = jQuery(this).attr("href");
    //var btn_name = jQuery(this).attr("name");
    //var type_id = btn_name.substring(5, btn_name.length - 2);
    var ajax_url = "/hotcms/quiz/ajax_setting_section_update/" + section_id + "/" + Math.random()*99999;

    //var question_id = jQuery(this).attr("href");
    //var question_div = jQuery(this).parents(".question");
    var section_type = jQuery("select[name='section_type_" + section_id + "']").val();
    var question_pool = jQuery("input[name='question_pool_" + section_id + "']").val();
    var questions_per_quiz = jQuery("input[name='questions_per_quiz_" + section_id + "']").val();
    var postdata = {"section_id": section_id, "section_type": section_type,
      "question_pool": question_pool, "questions_per_quiz": questions_per_quiz};
    if (section_id > 0 && section_type > '') {
      jQuery.post(ajax_url, postdata, function(data){
        try{
          var JSONobj = JSON.parse(data);
          //if (JSONobj['result']) {
            //question_div.load("quiz/ajax_question_display/" + question_id + "/" + Math.random()*99999, function(){
            //  reorder_sections();
            //});
          //}
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

  jQuery('.delete_question_link').live("click", function(){
    var question_div = jQuery(this).parents(".question");
    var question_id = jQuery(this).attr("href");
    var ajax_url = "quiz/ajax_delete_question/" + question_id + "/" + Math.random()*99999;
    if (question_id > 0 && confirm('Are you sure you want to delete this question?')) {
      jQuery.post(ajax_url, function(data){
        try{
          var JSONobj = JSON.parse(data);
          if (JSONobj['result'] && question_div) {
            question_div.remove();
            reorder_sections();
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

  reorder_sections();

});

// re-assign numbers to the questions
function reorder_sections(){
  jQuery(".quiz_section").each(function(i){
    jQuery(".question", this).each(function(j){
      jQuery(this).children(".question_num").text("Question " + (j + 1) + ")");
    });
  });
}