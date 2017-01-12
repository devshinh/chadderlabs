jQuery( document ).ready( function () {
  jQuery( 'form :text:first' ).focus();
  //initialize();
});

function codeAddress(address, info) {
  if (geocoder) {
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        //map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map, 
            icon: markerIcon,
            position: results[0].geometry.location,
            title: address
        });
        var infowindow = new google.maps.InfoWindow({
            content: info
        });
        google.maps.event.addListener(marker, 'click', function() {
          infowindow.open(map, marker);
        });
        /*
        google.maps.event.addListener(marker, 'mouseover', function() {
          infowindow.open(map, marker);
        });
        google.maps.event.addListener(marker, 'mouseout', function() {
          infowindow.close();
        }); */
      //} else {
      //  alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
}

function addMarker(lat, long, address, info) {
  var loc = new google.maps.LatLng(lat,long);
  var marker = new google.maps.Marker({
      map: map, 
      icon: markerIcon,
      position: loc,
      title: address
  });
  var infowindow = new google.maps.InfoWindow({
      content: info
  });
  /*
  google.maps.event.addListener(marker, 'mouseover', function() {
    infowindow.open(map, marker);
  });
  google.maps.event.addListener(marker, 'mouseout', function() {
    infowindow.close();
  });
  */
  google.maps.event.addListener(marker, 'click', function() {
    infowindow.open(map, marker);
  });
}

function centerMap() {
  if (jQuery("select#mapCity").val() > ''){
    var addr = jQuery("select#mapCity").val();
    if (geocoder) {
      geocoder.geocode( { 'address': addr}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
        //} else {
        //  alert("Geocode was not successful for the following reason: " + status);
        }
      });
    }
  }
}

//Binds dropdowns
function applyCascadingDropdowns() {
  applyCascadingDropdown("mapProvince", "mapCity");
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
  if (jQuery("select#mapProvince").val() > ''){
    selectElement.appendChild(options[0].cloneNode(true));
  }
  for(var i=0, length=options.length; i<length; i++) {
    if (options[i].className==className)
      selectElement.appendChild(options[i].cloneNode(true));
  }
}

//execute when the page is ready
//window.onload=applyCascadingDropdowns;