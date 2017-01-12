jQuery( document ).ready( function() {
  //if(jQuery(".tabs").length > 0){
  //  jQuery(".tabs").tabs();
  //}
  if(("#badge-modal").length > 0){
      jQuery('#badge-modal').modal('show');
  }
  //TODO: change validation message to "Answer required."
  if(("#quiz_form").length > 0){
    jQuery("#quiz_form").validate({
      errorClass: 'error',
      highlight: function(element, errorClass) {
        jQuery(element).fadeOut(function() {
          jQuery(element).fadeIn();
        });
      }
    });
  }

  if (typeof timelimit != 'undefined' && jQuery("#quiz_form").length > 0) {
    if (timelimit > 0) {
      jQuery('#quizCountdown').countdown({
        until: '+' + timelimit + 's',
        //format: 'YOWDHMS',
        //significant: 2,
        compact: true,
        description: '',
        onTick: function(periods){
          if (jQuery.countdown.periodsToSeconds(periods) == 10) {
            jQuery(this).addClass('red');
          }
        },
        onExpiry: function(){
          jQuery("#quiz_form").validate().cancelSubmit = true;
          jQuery("#quiz_form").submit();
        }
      });
      //setTimeout(function(){
      //  jQuery("#quiz_form").validate().cancelSubmit = true;
      //  jQuery("#quiz_form").submit();
      //}, timelimit * 1000);
    }
  }
});

