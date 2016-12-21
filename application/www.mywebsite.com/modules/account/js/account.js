jQuery(document).ready(function() {

    if (jQuery("select[name=retailer]").val() === '99999') {
        jQuery('#custom_retailer_wrapper').show();
    } else {
        jQuery('#custom_retailer_wrapper').hide();
    }

    if (jQuery("select[name=store]").val() === '99999') {
        jQuery('#custom_location_wrapper').show();
    } else {
        jQuery('#custom_location_wrapper').hide();
    }

//    if (jQuery("select[name=employment]").val() === 'Other') {
//        jQuery('#custom_employment_wrapper').show();
//    } else {
//        jQuery('#custom_employment_wrapper').hide();
//    }
//    if (jQuery("select[name=job_title]").val() === 'Other') {
//        jQuery('#custom_job_title_wrapper').show();
//    } else {
//        jQuery('#custom_job_title_wrapper').hide();
//    }    

    //initial state for selects

    if(jQuery("select[name=province]").val()===''){
        jQuery("select[name=province]").prop('disabled', true);
    }
    if(jQuery("select[name=retailer]").val()===''){
        jQuery("select[name=retailer]").prop('disabled', true);
    }
    if(jQuery("select[name=location]").val()===''){
        jQuery("select[name=location]").prop('disabled', true);
    }    
    if(jQuery("select[name=store]").val()===''){
        jQuery("select[name=store]").prop('disabled', true);
    }        
//    if(jQuery("select[name=employment]").val()===''){
//        jQuery("select[name=employment]").prop('disabled', true);
//    }
//    if(jQuery("select[name=job_title]").val()===''){       
//        jQuery("select[name=job_title]").prop('disabled', true);
//    }    
//jQuery("input").prop('disabled', false);    
    
    jQuery("#country_code").change(function() {
        var country_code = jQuery('#country_code option:selected');
        if (country_code.attr('value') !== '') {
            jQuery("select[name=province]").prop('disabled', false);
            //reset locations
            jQuery('select[name=store] option').remove();
            //jQuery('select[name=store]').append(jQuery("<option></option>").attr("value", '99999').text('Please select your retailer first.'));     
            var ajax_url = "/ajax/account/retailers/"+country_code.attr('value')+"/" + Math.random() * 99999;
            jQuery.getJSON(ajax_url, function(json) {
                if (json.result) {
                    jQuery('select[name=retailer] option').remove();
                    jQuery('select[name=retailer]').append(jQuery("<option></option>").attr("value", "").text('')); 
                    //jQuery('select[name=retailer]').append(jQuery("<option></option>").attr("value", "").text('Please select your retailer')); 
                    //console.log(json.retailers);
                    jQuery.each(json.retailers, function(key, value) {
                        //console.log(value['name']);
                        jQuery('select[name=retailer]')
                                .append(jQuery("<option></option>")
                                .attr("value", value['id'])
                                .text(value['name']));
                    });
                }
            })
                    .error(function() {
                alert("Sorry but there was an error.");
            });
            //display provinces/states
            var ajax_url2 = "/ajax/account/provinces/"+country_code.attr('value')+"/" + Math.random() * 99999;
            jQuery.getJSON(ajax_url2, function(json) {
                if (json.result) {
                    jQuery('select[name=province] option').remove();
                if(country_code.attr('value') === 'US'){
                        jQuery('select[name=province]')
                                .append(jQuery("<option></option>")
                                .attr("value", "")
                                .text('Please select your state'));
                }else if(country_code.attr('value') === 'CA'){
                        jQuery('select[name=province]')
                                .append(jQuery("<option></option>")
                                .attr("value", "")
                                .text('Please select your province'));
                }                    
                    jQuery.each(json.provinces, function(key, value) {
                        jQuery('select[name=province]')
                                .append(jQuery("<option></option>")
                                .attr("value", key)
                                .text(value));
                    });
                }
            })
                    .error(function() {
                alert("Sorry but there was an error.");
            });
        }else{
            jQuery("select[name=province]").prop('disabled', true);
        }
    });    

    jQuery("select[name=province]").change(function() {

        if (jQuery("select[name=retailer]").val() === 99999) {
            jQuery('#custom_retailer_wrapper').show();

        } else {
            retailer_id = jQuery("select[name=retailer]").val();
            jQuery('#custom_retailer_wrapper').hide();
        }


        if (jQuery(this).val() > "") {
            jQuery("select[name=retailer]").prop('disabled', false);
            var ajax_url = "/ajax/account/stores/" + retailer_id + "/" + jQuery(this).val() + "/" + Math.random() * 99999;
            jQuery.getJSON(ajax_url, function(json) {
                if (json.result) {
                    jQuery('select[name=store] option').remove();                  
                    jQuery.each(json.stores, function(key, value) {
                        jQuery('select[name=store]').append(jQuery("<option></option>").attr("value", "").text('')); 
                        jQuery('select[name=store]')
                                .append(jQuery("<option></option>")
                                .attr("value", value['id'])
                                .text(value['name']));
                    });
                    if (getPropertyCount(json.stores) === 0) {
                        jQuery('#custom_location_wrapper').show();
                    } else {
                        jQuery('#custom_location_wrapper').hide();
                    }
                }
            })
                    .error(function() {
                alert("Sorry but there was an error.");
            });
        }else{
            jQuery("select[name=retailer]").prop('disabled', true);
        }

    });

    jQuery("select[name=retailer]").change(function() {
        if (jQuery("select[name=province]").val() === "") {
            var province_code = 99999;
        } else {
            province_code = jQuery("select[name=province]").val();
        }

        if (jQuery(this).val() > "") {
            jQuery("select[name=store]").prop('disabled', false);
            var ajax_url = "/ajax/account/stores/" + jQuery(this).val() + "/" + province_code + "/" + Math.random() * 99999;
            jQuery.getJSON(ajax_url, function(json) {
                if (json.result) {
                    jQuery('select[name=store] option').remove();
                    jQuery('select[name=store]').append(jQuery("<option></option>").attr("value", "").text('')); 
                    jQuery.each(json.stores, function(key, value) {
                        jQuery('select[name=store]')
                                .append(jQuery("<option></option>")
                                .attr("value", value['id'])
                                .text(value['name']));
                    });
//                    if (getPropertyCount(json.stores) === 0) {
//                        jQuery('#custom_location_wrapper').show();
//                    } else {
//                        jQuery('#custom_location_wrapper').hide();
//                    }
                }
            })
                    .error(function() {
                alert("Sorry but there was an error.");
            });
        }else {
           jQuery("select[name=store]").prop('disabled', true);
        }

        if (jQuery(this).val() === '99999') {
            jQuery('#custom_retailer_wrapper').show();
        } else {
            jQuery('#custom_retailer_wrapper').hide();
        }

        if (jQuery('select[name=store]').val() === '99999') {
            jQuery('#custom_location_wrapper').show();
        } else {
            jQuery('#custom_location_wrapper').hide();
        }

    });

    jQuery("select[name=store]").change(function() {       
        if (jQuery(this).val() === '99999') {
            jQuery('#custom_location_wrapper').show();
        } else {
            jQuery('#custom_location_wrapper').hide();
        }
        
//        if(jQuery("select[name=location]").val() !== '99999' ){ 
//            //enable employment & job title
//            jQuery("select[name=employment]").prop('disabled', false);
//            jQuery("select[name=job_title]").prop('disabled', false);    
//        }

    });
    
//    jQuery("select[name=employment]").change(function() {
//        if (jQuery(this).val() === 'Other') {
//            jQuery('#custom_employment_wrapper').show();
//        } else {
//            jQuery('#custom_employment_wrapper').hide();
//        }
//    });
    
//    jQuery("select[name=job_title]").change(function() {
//        if (jQuery(this).val() === 'Other') {
//            jQuery('#custom_job_title_wrapper').show();
//        } else {
//            jQuery('#custom_job_title_wrapper').hide();
//        }
//    });    

    if (jQuery('#hire_date').length > 0) {
        jQuery("#hire_date").datepicker({
            //    showOn: "button",
            //    buttonImage: "/hotcms/asset/images/icons/btn-calendar.png",
            //    buttonImageOnly: false,
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true
        });
    }

    if (jQuery('#login_form').length > 0) {
        jQuery('#login_form').validate({
            rules: {
                username: {
                    required: true,
                    email: true
                },
                password: "required"
            },
            messages: {
                username: "Please enter a valid email address",
                password: "Please enter a password."
            },
            highlight: function(label) {
                jQuery(label).addClass('error');
            },

            success: function(label) {
                label.remove();
                        //.text('OK!').addClass('valid');
                        //.closest('.control-group').removeClass('error').addClass('success');
            }
        });
    }

    if (jQuery('#register_form_home').length > 0) {
        jQuery('#register_form_home').validate({
            rules: {
                first_name: "required",
                last_name: "required",
                screen_name: "required",
                email: {
                    required: true,
                    email: true
                }

            },
            messages: {
                first_name: "The First Name field is required.",
                last_name: "The Last Name field is required.",
                screen_name: "The Screen Name field is required.",
                email: "Please enter a valid Email Address."
            },
            highlight: function(label) {
                jQuery(label).addClass('error');
            }
        });
    }
    if (jQuery('#forgot_password').length > 0) {
        jQuery('#forgot_password').validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: "Please enter a valid email address"
            }
        });
    }


    if (jQuery('#change_password').length > 0) {
        jQuery('#change_password').validate({
            rules: {
                password: {
                    required: true
                },
                new_password: {
                    required: true,
                    minlength: 4
                },
                new_password_confirm: {
                    required: true,
                    minlength: 4,
                    equalTo: "#new_password"
                }
            },
            messages: {
                password: {
                    required: "Please provide a password."
                },
                new_password: {
                    required: "Please provide a password.",
                    minlength: "Your password must be at least 4 characters long."
                },
                new_password_confirm: {
                    required: "Please provide a password.",
                    minlength: "Your password must be at least 4 characters long.",
                    equalTo: "Please enter the same password as above."
                }
            }


        });
    }

    if (jQuery('.table_form').length > 0) {
        // jQuery('.table_form').validate();
    }
    if (jQuery('#avatar-upload-form').length > 0) {
        jQuery('#avatar-upload-form').validate();
        
        jQuery("input.avatarfile").each(function(){        
            jQuery(this).rules("add", {            
                required:true,            
                accept: "jpg|jpeg|png|gif",
                messages: {
                   accept: "File not compatible, please choose .jpg, .jpeg, .png or .gif."
                }                
            }            
        );      
            
        });
    }    
    
    if (jQuery('#verification-upload-form').length > 0) {
        jQuery('#verification-upload-form').validate();
        
        jQuery("input.verificationfile").each(function(){        
            jQuery(this).rules("add", {            
                required:true,            
                accept: "jpg|jpeg|png|gif",
                messages: {
                   accept: "File not compatible, please choose .jpg, .jpeg, .png or .gif."
                }                
            }            
        );      
            
        });
    }        
    
    if (jQuery('#register_form').length > 0) {
        jQuery('#register_form').validate({
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

    if (jQuery('#register_brand_form').length > 0) {
        jQuery('#register_brand_form').validate({
            rules: {
                first_name: "required",
                last_name: "required",
                email: {
                    required: true,
                    email: true
                },
                email_confirm: {
                    required: true,
                    email: true,
                    equalTo: '#email'
                },           
                phone: "required",
                company: "required"

            },
            messages: {
                first_name: "The First Name field is required.",
                last_name: "The Last Name field is required.",
                email: {
                    required: "Please enter a valid Email Address.",
                    email: "Please enter a valid Email Address."
                },
                email_confirm: {
                    required: "Please enter a valid Email Address.",
                    email: "Please enter a valid Email Address.",
                    equalTo: "Please enter the same email as above"
                },
                phone: "The Phone field is required.",
                company: "The Phone field is required."
            },
            highlight: function(label) {
                jQuery(label).addClass('error');
            }
        });
    }

//The First Name field is required.
//
//The Last Name field is required.
//
//The Screen Name field is required.
//
//The Email Address field is required.
//
//The Confirm Email Address field is required.
//
//The Password field is required.
//
//The Confirm Password field is required.
//
//The Retailer field is required.
//
//The Location field is required.
//
//The Employment field is required.
//
//The Job Title field is required.
//
//The Country field is required.
//
//The Province field is required.

    
});
function getPropertyCount(obj) {
    var count = 0,
            key;

    for (key in obj) {
        if (obj.hasOwnProperty(key)) {
            count++;
        }
    }

    return count;
}
