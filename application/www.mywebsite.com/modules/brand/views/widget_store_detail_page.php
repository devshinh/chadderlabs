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
<?php 
$map_sources = (!empty($store_detail->street_1)||!empty($store_detail->city)||!empty($store_detail->province)||!empty($store_detail->postal_code)); 
if($map_sources){
?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">

    // Enable the visual refresh
    google.maps.visualRefresh = true;

    var geocoder;
    var map;
    var query = '<?php print($store_detail->street_1 . ' ' . $store_detail->city . ', ' . $store_detail->province . ' ' . $store_detail->postal_code); ?>';
    function initialize() {
        geocoder = new google.maps.Geocoder();
        var mapOptions = {
            zoom: 17,
            panControl: true,
            zoomControl: false,
            scaleControl: true,
            streetViewControl: false,         
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
<?php } ?>


<div class="hero-unit" id="retailerdetail" style="margin-bottom: 0">   
    <div class="media">
        <div class="pull-right">
            <?php print $retailer_detail->logo->full_html ?>
        </div>
        <div class="media-body">
            <?php if(!empty($store_detail->store_num)){?>
              <h1><?php print $retailer_detail->name ?> - <?php print $store_detail->store_name ?> (STORE <?php print $store_detail->store_num ?>)</h1>
            <?php }else{ ?>
              <h1><?php print $retailer_detail->name ?> - <?php print $store_detail->store_name ?></h1>
            <?php } ?>
        </div>
    </div>
    <div class="row-fluid" style="text-transform:uppercase">
        <strong>Location Address</strong>
    </div>
    <div class="row-fluid">
        <?php print($store_detail->street_1 . '<br /> ' . $store_detail->city . ', ' . $store_detail->province_name) . ' ' . $store_detail->postal_code .' <br />'.$store_detail->country; ?>
    </div>
    <?php if (!empty($store_detail->phone)){?>
    <div class="row-fluid">
        <strong>TEL:</strong> <?php print($store_detail->phone); ?>
    </div>         
    <?php } ?>
    <?php if (!empty($store_detail->fax)){?>
    <div class="row-fluid">
        <strong>FAX:</strong> <?php print($store_detail->fax); ?>
    </div>         
    <?php } ?>    
    <?php if (!empty($store_detail->email)){?>
    <div class="row-fluid">
        <strong>EMAIL:</strong> <a href="mailto:<?php print($store_detail->email); ?>"><?php print($store_detail->email); ?></a>
    </div>         
    <?php } ?>     
</div>
<?php if($map_sources){?>
<div id="map-canvas"></div>
<?php }else{
    print '<br />';
}
?>