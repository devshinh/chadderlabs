jQuery(document).ready(function() {
     jQuery(this).scrollTop(0);
    if (jQuery('#add_new_contact').length > 0) {
        jQuery('#add_new_contact_form').validate({
            rules: {
                contact_name: {
                    required: true
                }
            }
        });
        jQuery('#add_new_contact').click(function() {
            jQuery('#add_new_contact_form').show();
            jQuery('#add_new_contact').hide();
        });
        jQuery('#hide_contact_name_form').click(function() {
            jQuery('#add_new_contact_form').hide();
            jQuery('#add_new_contact').show();
        });
    }
   if(jQuery("input.phonenumber").length > 0){
     jQuery("input.phonenumber").mask("(999) 999-9999");
   }
   //alert for user when changing retailer
   jQuery("select[name=retailer]").change(function(){
       //user verified

       if(jQuery("input[name=user_verified]").val() === '1'){
         alert("Caution - your account will become unverified if you switch retailers and save these changes in your profile.\n You will have to re-verify with a recent paystub from your new retailer.\n Thanks. The Cheddar Labs Team.");
       }
   });
   
    if (jQuery('#user_info_form').length > 0) {
        jQuery('#user_info_form').validate({
            rules: {
                first_name: "required",
                last_name: "required",
                screen_name: "required",
                email: {
                    required: true,
                    email: true
                },
                email_confirm: {
                    required: true,
                    email: true,
                    equalTo: '#email'
                },           
                password2: {
                    required: true,
                    minlength: 5
                },
                password_confirm: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password2"
                },       
                country_code: "required",
                province: "required",
                retailer: "required",
                store: "required",
                employment: "required",
                job_title: "required"

            },
            messages: {
                first_name: "The First Name field is required.",
                last_name: "The Last Name field is required.",
                screen_name: "The Screen Name field is required.",
                email: {
                    required: "Please enter a valid Email Address.",
                    email: "Please enter a valid Email Address."
                },
                email_confirm: {
                    required: "Please enter a valid Email Address.",
                    email: "Please enter a valid Email Address."},
                    equalTo: "Please enter the same email as above"
                },
                password2: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long"
                },
                password_confirm: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long",
                    equalTo: "Please enter the same password as above"
                },                
          
            highlight: function(label) {
                jQuery(label).addClass('error');
            }
        });
    }   
});