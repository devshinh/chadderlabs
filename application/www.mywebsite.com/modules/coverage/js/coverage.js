var obj;

jQuery( document ).ready( function() {
  jQuery('form:text:first').focus();
  
  jQuery("#coverage_map").iviewer({
    src: "wireless_coverage_map.gif", 
    zoom_min: 32,
    zoom_max: 300,
    initCallback: function ()
    {
      obj = this;
      jQuery("#mapProvince").change(function(){
        var x = 0;
        var y = 0;
        switch (jQuery("#mapProvince").val()){
        case 'AB': x = 10; y = 10; break;
        case 'BC': x = 20; y = 20; break;
        case 'MB': x = 30; y = 30; break;
        case 'NB': x = 40; y = 40; break;
        case 'NL': x = 50; y = 50; break;
        case 'NS': x = 60; y = 60; break;
        case 'NT': x = 70; y = 70; break;
        case 'NU': x = 80; y = 80; break;
        case 'ON': x = 90; y = 90; break;
        case 'PE': x = 2916; y = 661; break;
        case 'QC': x = 100; y = 100; break;
        case 'SK': x = 100; y = 100; break;
        case 'YT': x = 0; y = 0; break;
        }
        obj.set_zoom(x);
        // moveTo function is not working properly
        obj.moveTo(x, y);
        //obj.setCoords(x, y);
      }); 
    }
  });
  
});
