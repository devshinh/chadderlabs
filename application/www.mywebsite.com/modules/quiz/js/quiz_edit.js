jQuery( document ).ready( function() {

  jQuery('.save_link').click(function(){
    var quiz_id = jQuery("input[name='quiz_id']").val();
    var ajax_url = "";
    ajax_url = "/hotcms/quiz/ajax_save/" + quiz_id + "/" + Math.random()*99999;
    jQuery.post(ajax_url, jQuery("#quiz-form").serialize(), function(data){
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

  jQuery('.add_question_link').click(function(){
    var quiz_id = jQuery("input[name='quiz_id']").val();
    var link = jQuery(this);
    var ajax_url = jQuery(this).attr("href") + "/" + Math.random()*99999;
    jQuery.post(ajax_url, {"quiz_id": quiz_id}, function(data){
      try{
        var JSONobj = JSON.parse(data);
        if (JSONobj['result'] && JSONobj['question_form'] > '') {
          link.before('<div class="question">' + JSONobj['question_form'] + '</div>');
          reorder_questions();
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

  jQuery('.edit_question_link').live("click", function(){
    var question_id = jQuery(this).attr("href");
    jQuery(this).parents(".question").load("quiz/ajax_question_edit_form/" + question_id + "/" + Math.random()*99999, function(){
      reorder_questions();
    });
    return false;
  });

  jQuery('.cancel_question_link').live("click", function(){
    var question_id = jQuery(this).attr("href");
    jQuery(this).parents(".question").load("quiz/ajax_question_display/" + question_id + "/" + Math.random()*99999, function(){
      reorder_questions();
    });
    return false;
  });

  jQuery('.save_question_link').live("click", function(){
    var question_id = jQuery(this).attr("href");
    var question_div = jQuery(this).parents(".question");
    var question = jQuery("textarea[name='question_" + question_id + "']").val();
    var question_type = jQuery("input[name='question_type_" + question_id + "']").val();
    var correct_answer = jQuery("input[name='correct_answer_" + question_id + "']:checked").val();
    var required = 0;
    if (jQuery("input[name='question_required_" + question_id + "']").is(":checked")){
      required = 1;
    }
    var ajax_url = "quiz/ajax_save_question/" + question_id + "/" + Math.random()*99999;
    var postdata;
    if (question_type == 1) {
      postdata = {"question_type": question_type, "question": question, "correct_answer": correct_answer, "required": required};
    }
    else if (question_type == 2) {
      var option_1 = jQuery("textarea[name='option_1_" + question_id + "']").val();
      var option_2 = jQuery("textarea[name='option_2_" + question_id + "']").val();
      var option_3 = jQuery("textarea[name='option_3_" + question_id + "']").val();
      var option_4 = jQuery("textarea[name='option_4_" + question_id + "']").val();
      postdata = {"question_type": question_type, "question": question, "correct_answer": correct_answer, "required": required,
        "option_1": option_1, "option_2": option_2, "option_3": option_3, "option_4": option_4};
    }
    if (question_id > 0 && question > '' && correct_answer > 0) {
      jQuery.post(ajax_url, postdata, function(data){
        try{
          var JSONobj = JSON.parse(data);
          if (JSONobj['result']) {
            question_div.load("quiz/ajax_question_display/" + question_id + "/" + Math.random()*99999, function(){
              reorder_questions();
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
            reorder_questions();
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

  reorder_questions();

});

// re-assign numbers to the questions
function reorder_questions(){
  jQuery(".quiz_section").each(function(i){
    jQuery(".question", this).each(function(j){
      jQuery(this).children(".question_num").text("Question " + (j + 1) + ")");
    });
  });
}