<style>
    html, body, #map-canvas {
        height: 450px;
        margin: 0px 0px 20px 0;
        padding: 0px
    }
    #state-list {
        list-style-type: none;
        margin-left: 0;
    }
    #state-list li{  
      float: left;
      width: 33%;
      
    }
</style>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">


    var geocoder;
    var map;
    var query = '<?php print($store_detail->street_1 . ' ' . $store_detail->city . ', ' . $store_detail->province) . ' ' . $store_detail->postal_code; ?>';
    function initialize() {
        geocoder = new google.maps.Geocoder();
        var mapOptions = {
            zoom: 17
        }
        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        codeAddress();
    }

    function codeAddress() {
        var address = query;
        geocoder.geocode({'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);




</script>



<div class="hero-unit" id="retailerdetail" style="margin-bottom: 0">   
    <div class="media">
        <div class="pull-right">
            <?php print $retailer_detail->logo->full_html ?>
        </div>
        <div class="media-body">
            <h1><?php print $retailer_detail->name ?> - <?php print $store_detail->store_name ?> (STORE <?php print $store_detail->store_num ?>)</h1>
        </div>
    </div>
    <div class="row-fluid" style="text-transform:uppercase">
        <strong>Store Details</strong>
    </div>
    <div class="row-fluid">
        <?php print($store_detail->street_1 . ' ' . $store_detail->city . ', ' . $store_detail->province) . ' ' . $store_detail->postal_code; ?>
    </div>
    <div class="row-fluid">
        <strong>TEL:</strong> <?php print($retailer_detail->head_office[0]->phone); ?>
    </div>    
    <div class="row-fluid">
        <strong>FAX:</strong> <?php print($retailer_detail->head_office[0]->fax); ?>
    </div> 
    <div class="row-fluid">
        <strong>EMAIL:</strong> <?php print($retailer_detail->head_office[0]->email); ?>
    </div>      
    <hr />
    <div class="row-fluid" style="text-transform:uppercase">
        <strong>HQ Details</strong>
    </div>
    <div class="row-fluid">
        <?php print($retailer_detail->head_office[0]->address_1 . ' ' . $retailer_detail->head_office[0]->city . ', ' . $retailer_detail->head_office[0]->province) . ' ' . $retailer_detail->head_office[0]->postal_code; ?>
    </div>
    <div class="row-fluid">
        <strong>TEL:</strong> <?php print($retailer_detail->head_office[0]->phone); ?>
    </div>    
    <div class="row-fluid">
        <strong>FAX:</strong> <?php print($retailer_detail->head_office[0]->fax); ?>
    </div> 
    <div class="row-fluid">
        <strong>EMAIL:</strong> <?php print($retailer_detail->head_office[0]->email); ?>
    </div>     
</div>
<div id="map-canvas"></div>