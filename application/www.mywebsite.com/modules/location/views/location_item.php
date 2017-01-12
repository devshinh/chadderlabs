<?php
if (!empty($name)) {
  echo '<h3>' . $name . "</h3>\n";
}
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/news/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/news/js/' . $js . "\"></script>\n";
  }
}
if (isset($item->publish_timestamp) && $item->publish_timestamp > 0) {
  $date = date('Y-m-d H:i:s', $item->publish_timestamp);
} elseif (isset($item->scheduled_publish_timestamp) && $item->scheduled_publish_timestamp > 0) {
  $date = date('Y-m-d H:i:s', $item->scheduled_publish_timestamp);
} else {
  $date = '(unknown publish date)';
}
?>
<script type="text/javascript">
  function initialize() {
    var latlng = new google.maps.LatLng( <?php print($item->latitude); ?>, <?php print($item->longitude); ?>);
    
          
    var myOptions = {
      center: latlng,
      zoom: 15,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      streetViewControl: false,
      zoomControl: true,
      zoomControlOptions: {
        position: google.maps.ControlPosition.RIGHT_TOP
      },
      panControlOptions: {
        position: google.maps.ControlPosition.RIGHT_TOP
      }
      

    };
    
     
    var map = new google.maps.Map(document.getElementById("map_canvas"),
    myOptions);
   
   

    var image = new google.maps.MarkerImage(
      'themes/bwinsurance/images/map-marker/icon-map-locator.png',
      new google.maps.Size(34,38),
      new google.maps.Point(0,0),
      new google.maps.Point(17,38)
    );

    var shadow = new google.maps.MarkerImage(
      'themes/bwinsurance/images/map-marker/icon-shadow-map-locator.png',
      new google.maps.Size(56,38),
      new google.maps.Point(0,0),
      new google.maps.Point(17,38)
    );

    var shape = {
      coord: [33,0,33,1,33,2,33,3,33,4,33,5,33,6,33,7,33,8,33,9,33,10,33,11,33,12,33,13,33,14,33,15,33,16,33,17,33,18,33,19,33,20,33,21,33,22,20,23,20,24,20,25,19,26,19,27,19,28,18,29,18,30,18,31,19,32,19,33,19,34,19,35,19,36,19,37,14,37,14,36,14,35,14,34,14,33,14,32,16,31,16,30,15,29,15,28,15,27,14,26,14,25,14,24,13,23,0,22,0,21,0,20,0,19,0,18,0,17,0,16,0,15,0,14,0,13,0,12,0,11,0,10,0,9,0,8,0,7,0,6,0,5,0,4,0,3,0,2,0,1,0,0,33,0],
      type: 'poly'
    };

    var marker = new google.maps.Marker({
      draggable: false,
      raiseOnDrag: false,
      icon: image,
      shadow: shadow,
      shape: shape,
      map: map,
      position: latlng,
      title: '<?php echo $item->page_location_title; ?>'
    });       

  }
</script>

<div class="location-detail" id="item-<?php print($item->id); ?>">
  <div id="map_wrapper">
    <div id="map_canvas" style="width:100%; height:100%"></div>
    <script type="text/javascript">
      initialize(); 
    </script>
  </div>
  <h1><?php echo $item->page_location_title; ?></h1>
  <div class="location-info">
    <div class="two-columns">
      <div id="location-description">
        <?php echo $item->page_location_description; ?>
      </div>
      <div class="col border"></div>
      <div id="location-address">
        <h2>Location</h2>
        <?php echo $item->address_1; ?><br />
        <?php if (!empty($item->address_2)) echo $item->address_2 . '<br />'; ?>
        <?php echo $item->city . ', ' . $item->province . ' ' . $item->postal_code ?>
      </div>      
    </div>
    <div class="three-columns">
      <div id="location-contact" class="col">
        <h2>Contact information</h2>
        <?php printf('<b>Phone: </b> %s<br />', $item->main_phone) ?>
        <?php if(!empty($item->toll_free_phone)) printf('<b>Toll Free: </b> %s<br />', $item->toll_free_phone) ?>
        <?php if(!empty($item->main_fax)) printf('<b>Fax: </b> %s<br />', $item->main_fax) ?>
        <?php printf('<b>Email: </b> <a class="orange" href="mailto:%s">%s</a>', $item->main_email, $item->main_email) ?>
      </div>
      <div class="col border"></div>
      <div id="location-hours" class="col">
        <h2>Hours of operation</h2>
        <?php
        foreach ($location_hours as $day_hours) {

          if ($day_hours->closed == 1) {
            printf('<b>%s</b>: Closed<br />', $day_hours->day);
          } else {
            printf('<b>%s</b>: %s - %s', $day_hours->day, $day_hours->from1, $day_hours->to1);
            if (!empty($day_hours->from2)) {
              printf(' | %s - %s', $day_hours->from2, $day_hours->to2);
            }
            print('<br />');
          }
        }
        ?>
      </div>
      <div class="col border"></div>
      <div id="location-services" class="col">
        <h2>Services at location</h2>
         <?php print($item->page_location_services); ?>
      </div>      
    </div>
    <div id="location-team">
      <h2>Meet the team</h2>
      <?php
      foreach ($location_users as $user) {?>
      <div class="location-user">
        <?php 
        if (!empty($location_user_avatar[$user->id]->name)){
        printf('<img alt="user-image" src="/asset/upload/image/avatars/%s.%s" title="%s"/>',$location_user_avatar[$user->id]->name,$location_user_avatar[$user->id]->extension, $user->first_name.'-'.$user->position);        
        }else{
          printf('<img alt="user-image" src="/asset/upload/image/avatars/thumbnail_90x90/icon-user_thumb.jpg" width="90" height="90" title="%s"/>', $user->first_name.'-'.$user->position);        
        }
?>
      </div>
        
      <?php } ?>


    </div>
  </div>

</div>
