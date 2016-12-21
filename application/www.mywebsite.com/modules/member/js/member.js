jQuery( document ).ready( function () {
  jQuery( 'form :text:first' ).focus();
  //applyCascadingDropdowns();
  jQuery("input#activation_code").mask("9999999");
  jQuery("input#pin").mask("9999");
  jQuery("input#pin_confirm").mask("9999");
  jQuery("input#captcha").mask("aaaaaa");
  jQuery("input#phone").mask("(999) 999-9999");
  jQuery("input#postal").mask("a9a 9a9");
  jQuery("input#voucher_number").mask("9999-9999-9999");
  jQuery("input#validation_code").mask("******");
});

//Binds dropdowns
function applyCascadingDropdowns() {
  applyCascadingDropdown("province", "city");
}

//Applies cascading behavior for the specified dropdowns
function applyCascadingDropdown(sourceId, targetId) {
  var source = document.getElementById(sourceId);
  var target = document.getElementById(targetId);
  if (source && target) {
    source.onchange = function() {
      displayOptionItemsByClass(target, source.value);
    }
    //displayOptionItemsByClass(target, source.value);
  }
}

//Displays a subset of a dropdown's options
function displayOptionItemsByClass(selectElement, className) {
  if (!selectElement.backup) {
    selectElement.backup = selectElement.cloneNode(true);
  }
  var options = selectElement.getElementsByTagName("option");
  for(var i=0, length=options.length; i<length; i++) {
    selectElement.removeChild(options[0]);
  }
  var options = selectElement.backup.getElementsByTagName("option");
  if (jQuery("select#province").val() > ''){
    selectElement.appendChild(options[0].cloneNode(true));
  }
  for(var i=0, length=options.length; i<length; i++) {
    if (options[i].className==className)
      selectElement.appendChild(options[i].cloneNode(true));
  }
}

//execute when the page is ready
//window.onload=applyCascadingDropdowns;