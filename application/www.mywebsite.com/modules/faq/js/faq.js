jQuery(document).ready(function() {


    jQuery('div.question').click(function() {
                
        if (jQuery(this).hasClass('closed')) {
          var qu = jQuery(this).attr("id");
          var an = "#" + qu.replace("q","a");
          var ar = "#" + qu.replace("q","ar");
          jQuery(ar).attr('src','/modules/faq/img/answer-arrow.jpg');
          jQuery(an).show();
          jQuery(this).removeClass('closed').addClass('opened');
          jQuery(an).removeClass('closed').addClass('opened');
        } else {
          var qu = jQuery(this).attr("id");
          var an = "#" + qu.replace("q","a");
          var ar = "#" + qu.replace("q","ar");
          jQuery(ar).attr('src','/modules/faq/img/question-arrow.jpg')
          jQuery(an).hide();
          jQuery(this).removeClass('opened').addClass('closed');
          jQuery(an).removeClass('opened').addClass('closed');          
        }

    });

});

