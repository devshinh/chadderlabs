jQuery( document ).ready( function() {
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