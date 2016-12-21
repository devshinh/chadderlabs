<h1>Only at Example.com</h1>

<form name="mapsearch">
  <div id="divSelectProvince">
    <select id="mapProvince" name="mapProvince" id="mapProvince">
      <option value="" selected="selected"> - <?php echo lang( 'select_province' ); ?> - </option>
  <?php
    foreach($provinces as $row) { 
      echo '<option value="'.$row->sProvince.'">'.$provinceCode[$row->sProvince].'</option>' . "\n";
    }
  ?>
    </select>
  </div>

  <div id="divSelectCity"> 
    <select name="mapCity" id="mapCity" onchange="centerMap();">
      <option value="" selected="selected"> - <?php echo lang( 'select_city' ); ?> - </option>
  <?php
    foreach($cities as $row) { 
      echo '<option value="'.$row->sCity.', '.$row->sProvince.'" class="'.$row->sProvince.'">'.$row->sCity.'</option>' . "\n";
    }
  ?>
    </select>
  </div>
</form>

<br />      
<div class="clear">&nbsp;</div>

<div class="storelocator">
    
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var map;
var geocoder;
var markerIcon = 'http://www.slurpee.ca/selogo.png';

jQuery(function(){
  applyCascadingDropdowns();
  
  var defaultLatLng = new google.maps.LatLng(49.241975, -123.077774);
  var myOptions = {
    zoom: 12,
    center: defaultLatLng,
    streetViewControl: true,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  geocoder = new google.maps.Geocoder();
  
  <?php foreach($stores as $row) { 
    $addr = $row->sUnit . ' ' . $row->sAddress . ', ' . $row->sCity . ', ' . $row->sProvince . ', Canada';
    $addr2 = '<span class=\'mapinfo\'><b>(#' . $row->nStoreID . ')</b><br />' . $row->sAddress . 
      '<br />' . $row->sUnit . ' ' . $row->sCity . ', ' . $row->sProvince . ' &nbsp; &nbsp; ' . $row->sPostalcode .
      '<br />Phone: ' . $row->sPhone;
    if ($row->sGasoline > '' || $row->sHotfoods == 'Y' || $row->sChicken == 'Y' || $row->sPizzahut == 'Y' || $row->sOutlet == 'Y'){
      $addr2 .= '<br /><b>' . lang( 'features' ) . ':</b>';
      if ($row->sGasoline > ''){
        $addr2 .= '<br />' . lang( 'gasoline' ) . ' (' . $row->sGasoline . ')';
      } 
      if ($row->sHotfoods == 'Y'){
        $addr2 .= '<br />' . lang( 'pizza_wings' );
      } 
      if ($row->sChicken == 'Y'){
        $addr2 .= '<br />' . lang( 'classic_chicken' );
      } 
      if ($row->sPizzahut == 'Y'){
        $addr2 .= '<br />' . lang( 'pizza_hut' );
      } 
      if ($row->sOutlet == 'Y'){
        $addr2 .= '<br />' . lang( 'canada_post' );
      } 
    }
    if ($row->sLimitedhours > ''){
      $addr2 .= '<br /><br /><i>' . $row->sLimitedhours . '</i>';
    }
    $addr2 .= '</span>';
    if ($row->sLat > '' && $row->sLong > ''){
  ?>
  addMarker("<?php echo $row->sLat; ?>", "<?php echo $row->sLong; ?>", "<?php echo $addr; ?>", "<?php echo $addr2; ?>");
  <?php
    }
    else{
  ?>
  codeAddress("<?php echo $addr; ?>", "<?php echo $addr2; ?>");
  <?php
    }
  } 
  ?>
  var panoramaOptions = {
    enableCloseButton: true,
    visible: false
  };
  var panorama = new google.maps.StreetViewPanorama(map.getDiv(), panoramaOptions);
  map.setStreetView(panorama); 
})
</script>

  <div id="map_canvas"></div>

</div>

<div class="clear">&nbsp;</div>


<script type="text/javascript">
var aCarousel_promos    = [ <?php $index = 0; foreach ($oCarousel->aPromos as $row){ ?>
<?php $image = sprintf('%d-%s-598x250.%s',$row->nImageID, $row->sFileName, $row->sExtension) ?>
{ id: <?php echo $row->nCarouselContentID ?>, src: "<?php echo $image?>",title: "<?php echo  $row->sName  ?>", url: "<?php echo $row->sLink ?>" }<?php if (++$index < count( $oCarousel->aPromos )): ?>, <?php endif ?> 

<?php } ?>        
];  
</script>

<div class="mainPromo">

  <div id="divHomePromoCarousel">
    <div class="carousel_outer">
      
      <div class="carousel_inner carousel_promos">
        <ul><li><!-- --></li></ul>
      </div>  
      
      <div class="indicator"><!-- --></div>
    </div><!-- end of carousel_outer -->
  </div>
  <div id="divHomePhonePromo">
  <?php $imageName = sprintf('%d-%s-280x251.%s', $oAd->nImageID, $oAd->sFileName, $oAd->sExtension) ?>
  <?php if(empty($oAd->sLink)) {?>
    <img src="/asset/upload/image/PhoneAds/<?php echo $imageName?>" alt="<?php echo $imageName?>" />
  <?php }else{ ?>
    <a href="<?php echo $oAd->sLink?>" target="_blank">
      <img src="/asset/upload/image/PhoneAds/<?php echo $imageName?>" alt="<?php echo $imageName?>" />
    </a>
  <?php }?>
  </div>
  
</div><!-- end of mainPromo -->  

<div class="clear"> </div>

<div id="divHomeTextCarousel">

  <div id="flipBox">
  <?php foreach ($oCarousel->aStatements as $row){ ?>
    <?php $image = sprintf('%d-%s-940x93.%s',$row->nImageID, $row->sFileName, $row->sExtension) ?>
    <?php if(empty($row->sLink)) {?>
      <img src="/asset/upload/image/Statements/<?php echo $image?>" alt="<?php printf('%s', $row->sFileName)?>" />
    <?php } else {?>
      <a href="<?php printf('%s', $row->sLink)?>">
       <img src="/asset/upload/image/Statements/<?php echo $image?>" alt="<?php printf('%s', $row->sFileName)?>" />
      </a>
    <?php }?>
  <?php } ?>
  </div> <!-- end of flipBox -->
  
</div> <!-- end of divHomeTextCarousel -->