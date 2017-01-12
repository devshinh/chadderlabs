jQuery( document ).ready( function() {

	jQuery("form:eq(1) input:visible:enabled:first").focus();

  jQuery('.questionnaire').hide();
  jQuery('.btn-continue').click(function(){
    if (jQuery("#quote_form").valid()){
      jQuery('.questionnaire').show();
      jQuery('.quote-info').hide();
    }
  });
  jQuery('.btn-back').click(function(){
    jQuery('.questionnaire').hide();
    jQuery('.quote-info').show();
  });

  jQuery("#quote_form").validate();

  jQuery("input[name=phone]").mask("(999) 999-9999");
  jQuery("input[name=postal]").mask("a9a 9a9");
  jQuery("input[name=year]").mask("9999");

  jQuery("div.hidden_field").hide();

  // a few special fields and dynamic effects
  jQuery("select[name=fld_13]").live("change", function(){
    if (jQuery(this).val() == 'Yes') {
      jQuery("input[name=fld_14]").parent("div.hidden_field").show();
      jQuery("input[name=fld_15]").parent("div.hidden_field").show();
      jQuery("input[name=fld_16]").parent("div.hidden_field").show();
    }
    else if (jQuery(this).val() == 'No') {
      jQuery("input[name=fld_14]").parent("div.hidden_field").hide();
      jQuery("input[name=fld_15]").parent("div.hidden_field").hide();
      jQuery("input[name=fld_16]").parent("div.hidden_field").hide();
    }
  });
  jQuery("select[name=fld_34]").live("change", function(){
    jQuery("input[name=fld_35]").parent("div.hidden_field").hide();
    jQuery("input[name=fld_36]").parent("div.hidden_field").hide();
    jQuery("input[name=fld_37]").parent("div.hidden_field").hide();
    jQuery("input[name=fld_38]").parent("div.hidden_field").hide();
    jQuery("input[name=fld_39]").parent("div.hidden_field").hide();
    jQuery("input[name=fld_40]").parent("div.hidden_field").hide();
    jQuery("input[name=fld_41]").parent("div.hidden_field").hide();
    jQuery("input[name=fld_42]").parent("div.hidden_field").hide();
    jQuery("input[name=fld_43]").parent("div.hidden_field").hide();
    jQuery("input[name=fld_44]").parent("div.hidden_field").hide();
    var sel = parseInt(jQuery(this).val());
    if (sel >= 1) {
      jQuery("input[name=fld_35]").parent("div.hidden_field").show();
      jQuery("input[name=fld_36]").parent("div.hidden_field").show();
    }
    if (sel >= 2) {
      jQuery("input[name=fld_37]").parent("div.hidden_field").show();
      jQuery("input[name=fld_38]").parent("div.hidden_field").show();
    }
    if (sel >= 3) {
      jQuery("input[name=fld_39]").parent("div.hidden_field").show();
      jQuery("input[name=fld_40]").parent("div.hidden_field").show();
    }
    if (sel >= 4) {
      jQuery("input[name=fld_41]").parent("div.hidden_field").show();
      jQuery("input[name=fld_42]").parent("div.hidden_field").show();
    }
    if (sel >= 5) {
      jQuery("input[name=fld_43]").parent("div.hidden_field").show();
      jQuery("input[name=fld_44]").parent("div.hidden_field").show();
    }
  });
});