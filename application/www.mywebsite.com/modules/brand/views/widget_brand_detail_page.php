<style>
    html, body, #map-canvas {
        height: 450px;
        margin: 0px 0px 20px 0;
        padding: 0px
    }
</style>
<?php 
$map_sources = !empty($retailer_detail->head_office[0]->address_1) || !empty($retailer_detail->head_office[0]->city)|| !empty($retailer_detail->head_office[0]->province) || !empty($retailer_detail->head_office[0]->postal_code);
if($map_sources){
?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">
    // Enable the visual refresh
    google.maps.visualRefresh = true;

    var geocoder;
    var map;
    var query = '<?php print($retailer_detail->head_office[0]->address_1 . ' ' . $retailer_detail->head_office[0]->city . ', ' . $retailer_detail->head_office[0]->province . ' ' . $retailer_detail->head_office[0]->postal_code); ?>';
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
        <div class="pull-left">
            <?php print $retailer_detail->logo->full_html ?>
        </div>
        <div class="media-body">
            <h1 style="line-height:85px"><?php print $retailer_detail->name ?></h1>
        </div>
    </div>
    <?php if($map_sources){?>
    <div class="row-fluid" style="text-transform:uppercase;">
        <strong>Head Office Address</strong>
    </div>
    <div class="row-fluid">
        <?php print($retailer_detail->head_office[0]->address_1 . '<br />' . $retailer_detail->head_office[0]->city . ', ' . $retailer_detail->head_office[0]->province) . ' ' . $retailer_detail->head_office[0]->postal_code; ?>
    </div>
    <?php } ?>
    <?php if(!empty($retailer_detail->head_office[0]->phone)){?>
    <div class="row-fluid">
        <strong>TEL:</strong> <?php print($retailer_detail->head_office[0]->phone); ?>
    </div>    
    <?php } ?>
    <?php if(!empty($retailer_detail->head_office[0]->fax)){?>
    <div class="row-fluid">
        <strong>FAX:</strong> <?php print($retailer_detail->head_office[0]->fax); ?>
    </div> 
    <?php } ?>
    <?php if(!empty($retailer_detail->head_office[0]->email)){?>
    <div class="row-fluid">
        <strong>EMAIL:</strong> <a href="mailto:<?php print($retailer_detail->head_office[0]->email); ?>"><?php print($retailer_detail->head_office[0]->email); ?></a>        
    </div>     
    <?php } ?>
    <div class="row-fluid" style="margin: 5px 0;">
        <p>If you don't see your store or organization in our list of retailers, it's not a problem. Just add your new location when you register a user <a href="http://www.cheddarlabs.com/signup">account</a> and you can start training and earn some Cheddar!</p>
    </div>   
</div>
<?php if($map_sources){?>
<div id="map-canvas"></div>
<?php }else{
    print '<br />';
}
?>
<div class="hero-unit">
    <h2>All <?php print $retailer_detail->name ?> locations</h2>
    <hr />
    <p>Find a <?php print $retailer_detail->name ?> location by state:</p>
    <ul style="list-style-type: none;margin-left: 0;">
    <?php
    foreach ($states as $s){?>
          <li style="float: left;width: 33%;"><a href="/retailer-state/<?php print $retailer_detail->slug ?>/<?php print(strtolower($retailer_detail->country_code)) ?>/<?php print url_title(strtolower($s->province_code)) ?>"><?php print ($s->province_name) ?></a></li>
    <?php } ?>    
    </ul>
    <div class="clearfix"></div>
<!--    <hr />
    <p>Find a <?php print $retailer_detail->name ?> location in major cities:</p>    
    <ul style="list-style-type: none;margin-left: 0;">
    <?php
    foreach ($cities as $c){?>
         <li style="float: left;width: 33%;"><a href="/retailer-state/<?php print $retailer_detail->slug ?>/<?php print url_title(strtolower($c->city),'-') ?>"><?php print ($c->city) ?></a></li>
    <?php } ?>    
    </ul>-->
    <div class="clearfix"></div>
</div>