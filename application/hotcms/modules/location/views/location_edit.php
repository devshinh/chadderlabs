<script type="text/javascript">
  function load() {
    if (GBrowserIsCompatible()) {
      var map = new GMap2(document.getElementById("map"));
      map.addControl(new GSmallMapControl());
      map.addControl(new GMapTypeControl());
      var center = new GLatLng(49.26123,  	-123.11393);
      map.setCenter(center, 15);
      geocoder = new GClientGeocoder();
      var marker = new GMarker(center, {draggable: true});  
      map.addOverlay(marker);
      document.getElementById("lat").innerHTML = center.lat().toFixed(5);
      document.getElementById("lng").innerHTML = center.lng().toFixed(5);

      GEvent.addListener(marker, "dragend", function() {
        var point = marker.getPoint();
        map.panTo(point);
        document.getElementById("lat").innerHTML = point.lat().toFixed(5);
        document.getElementById("lng").innerHTML = point.lng().toFixed(5);

      });
      
 
      GEvent.addListener(map, "moveend", function() {
        map.clearOverlays();
        var center = map.getCenter();
        var marker = new GMarker(center, {draggable: true});
        map.addOverlay(marker);
        document.getElementById("lat").innerHTML = center.lat().toFixed(5);
        document.getElementById("lng").innerHTML = center.lng().toFixed(5);


        GEvent.addListener(marker, "dragend", function() {
          var point =marker.getPoint();
          map.panTo(point);
          document.getElementById("lat").innerHTML = point.lat().toFixed(5);
          document.getElementById("lng").innerHTML = point.lng().toFixed(5);

        });
 
      });

    }
  }
  
  function load_saved(lat, lng) {
    if (GBrowserIsCompatible()) {
      var map = new GMap2(document.getElementById("location_map"));
      map.addControl(new GSmallMapControl());
      map.addControl(new GMapTypeControl());
      var center = new GLatLng(lat, lng);
      map.setCenter(center, 15);
      var marker = new GMarker(center, {draggable: false});  
      map.addOverlay(marker);

    } 
  }

  function showAddress(address) {
    var map = new GMap2(document.getElementById("map"));
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    if (geocoder) {
      geocoder.getLatLng(
      address,
      function(point) {
        if (!point) {
          alert("Address "+ address + " not found");
        } else {
          document.getElementById("lat").value = point.lat().toFixed(5);
          document.getElementById("lng").value = point.lng().toFixed(5);
          map.clearOverlays()
          map.setCenter(point, 14);
          var marker = new GMarker(point, {draggable: true});  
          map.addOverlay(marker);

          GEvent.addListener(marker, "dragend", function() {
            var pt = marker.getPoint();
            map.panTo(pt);
            document.getElementById("lat").value = pt.lat().toFixed(5);
            document.getElementById("lng").value = pt.lng().toFixed(5);
          });


          GEvent.addListener(map, "moveend", function() {
            map.clearOverlays();
            var center = map.getCenter();
            var marker = new GMarker(center, {draggable: true});
            map.addOverlay(marker);
            document.getElementById("lat").value = center.lat().toFixed(5);
            document.getElementById("lng").value = center.lng().toFixed(5);

            GEvent.addListener(marker, "dragend", function() {
              var pt = marker.getPoint();
              map.panTo(pt);
              document.getElementById("lat").value = pt.lat().toFixed(5);
              document.getElementById("lng").value = pt.lng().toFixed(5);
            });
 
          });

        }
      }
    );
    }
  }
    
</script>

<div>
  <div class="row"><a id="back_button" style="float:right" class="red_button_smaller" href="/hotcms/location">Back</a></div>
  <div class="tabs">
    <ul>
      <li><a href="#location-info" id="general"><span id="g"></span><span>Info</span></a></li>
      <li><a href="#location-map" id="map-tab"><span id="m"></span><span>Map</span></a></li>
      <li><a href="#location-users" id="user"><span id="u"></span><span>Users</span></a></li>
      <li><a href="#location-hours" id="hours"><span id="h"></span><span>Hours</span></a></li>
    </ul>
    <div id="location-info">
      <form action="/hotcms/<?php echo $module_url ?>/edit/<?php echo $aCurrentItem->id ?>" method="post">
        <div class="input_area">
          <div class="row">       
            <?php echo form_error('name', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_location_name') . ' ' . lang('hotcms__colon'), 'name'); ?>
            <?php echo form_input($form['name_input']); ?>
          </div>
          <div class="row">       
            <?php echo form_error('main_email', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_email') . ' ' . lang('hotcms__colon'), 'main_email'); ?>
            <?php echo form_input($form['main_email_input']); ?>
          </div>  
          <div class="row">       
            <?php echo form_error('main_phone', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_main_phone') . ' ' . lang('hotcms__colon'), 'main_phone'); ?>
            <?php echo form_input($form['main_phone_input']); ?>
          </div>    
          <div class="row">       
            <?php echo form_error('toll_free_phone', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_phone_toll_free') . ' ' . lang('hotcms__colon'), 'toll_free_phone'); ?>
            <?php echo form_input($form['toll_free_phone_input']); ?>
          </div>    
          <div class="row">       
            <?php echo form_error('main_fax', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_fax') . ' ' . lang('hotcms__colon'), 'main_fax'); ?>
            <?php echo form_input($form['main_fax_input']); ?>
          </div>     
          <div class="row">
            <?php echo form_error('address_1', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_address') . ' 1' . lang('hotcms__colon'), 'address_1'); ?>
            <?php echo form_input($form['address_1_input']); ?>                     
          </div>    
          <div class="row">
            <?php echo form_error('address_2', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_address') . ' 2' . lang('hotcms__colon'), 'address_2'); ?>
            <?php echo form_input($form['address_2_input']); ?>                     
          </div>  
          <div class="row">
            <?php echo form_error('city', '<div class="error">', '</div>'); ?> 
            <?php echo form_label(lang('hotcms_city') . ' ' . lang('hotcms__colon'), 'city'); ?>
            <?php echo form_input($form['city_input']); ?>                     
          </div>    
          <div class="row">         
            <?php echo form_error('province', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_province') . ' ' . lang('hotcms__colon'), 'province'); ?>
            <?php echo form_input($form['province_input']); ?>                     
          </div>  
          <div class="row">      
            <?php echo form_error('postal_code', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_postal_code') . ' ' . lang('hotcms__colon'), 'postal_code'); ?>
            <?php echo form_input($form['postal_code_input']); ?>                     
          </div>   
          <div class="row">      
            <?php echo form_error('page_location_title', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_page_location_title') . ' ' . lang('hotcms__colon'), 'page_location_title'); ?>
            <?php echo form_input($form['page_location_title']); ?>                     
          </div> 
          <div class="row">      
            <?php echo form_error('page_location_description', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_page_location_description') . ' ' . lang('hotcms__colon'), 'page_location_description'); ?>
            <?php echo form_textarea($form['page_location_description']); ?>                     
          </div> 
          <div class="row">      
            <?php echo form_error('page_location_services', '<div class="error">', '</div>'); ?>
            <?php echo form_label(lang('hotcms_page_location_services') . ' ' . lang('hotcms__colon'), 'page_location_services'); ?>
            <?php echo form_textarea($form['page_location_services']); ?>                     
          </div>           
           <input type="hidden" value="1" name="currentTabIndex">
          <div class="submit">
            <input type="submit" class="input red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
            <a href="/hotcms/<?php echo $module_url ?>/" class="red_button"><?php echo lang('hotcms_back') ?></a>

            <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url ?>/delete/<?php echo $aCurrentItem->id ?>" class="red_button"><?php echo lang('hotcms_delete') ?></a>

            <?php echo form_hidden('hdnMode', 'edit') ?>
          </div>
        </div>
        <div class="map_area">
          <div align="center" id="location_map" style="width: 500px; height: 400px"><br/></div>      
          <script type="text/javascript">
            load_saved('<?php echo $aCurrentItem->latitude ?>','<?php echo $aCurrentItem->longitude ?>');
          </script>

        </div>
        <div class="clear"></div>
      </form>
    </div><!-- info tab -->   
    <div id="location-map">
      <div class="row">
        <form action="#" onsubmit="showAddress(this.address_for_map.value); return false">
          <?php
          $address = sprintf('%s %s %s', $aCurrentItem->address_1, $aCurrentItem->address_2, $aCurrentItem->city);
          //$js = 'onclick="showAddress('+$address+')" onchange="showAddress('+$address+')"';
          //echo form_input(array('name'=>'address_for_map','id'=>'address_for_map','value'=> $address));
          ?>
          <input type="text" id="address_for_map" value="<?php echo $address; ?>" name="address_for_map" onchange="showAddress('<?php echo $address ?>')" />            
          <input style="margin: 0 10px" type="submit" class="red_button_smaller" value="Search for coordinates" />

        </form>
      </div>
      <form action="/hotcms/<?php echo $module_url ?>/save_coordinates/<?php echo $aCurrentItem->id ?>" method="post">
        <div class="row">
          <label for="lat">Latitude :</label>
          <input type="text" id="lat" name="lat" />
        </div>  
        <div class="row">
          <label for="lng">Longitude :</label>
          <input type="text" id="lng" name="lng"/>
        </div>
        <div class="row">      
          <p>
          <div align="center" id="map" style="width: 600px; height: 400px"><br/></div>
          </p>
        </div>
        <script type="text/javascript">
          load();
<?php if ($address != "  ") { ?>
    showAddress('<?php echo $address ?>');
<?php } ?>
        </script>
        <div class="submit">
          <input type="submit" class="input red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
          <a href="/hotcms/<?php echo $module_url ?>/" class="red_button"><?php echo lang('hotcms_back') ?></a>
        </div>
      </form>
    </div><!-- map tab -->   

    <div id="location-users">
      <div class="row">  
        <div class="table">   
          <table id="tableCurrent" class="tablesorter">
            <thead>
              <tr>
                <th><?php echo lang('hotcms_name') ?></th>
                <th><?php echo lang('hotcms_email') ?></th>    
                <th class="action"><?php echo lang('hotcms_edit') ?></th>
                <th class="action"><?php echo lang('hotcms_delete') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!empty($users) > 0) {
                foreach ($users as $row) {
                  ?>
                  <tr id="trData_<?php echo $row->user_id ?>">
                    <td>
                      <?php echo $row->first_name . ' ' . $row->last_name; ?>
                    </td>
                    <td>
                      <?php echo $row->email; ?>
                    </td>
                    <td>
                      <a href="<?php printf('/hotcms/%s/edit/%s', 'user', $row->user_id) ?>"><div class="btn-edit"></div></a>
                    </td>
                    <td class="last">
                      <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete_user/%s/%s', $module_url, $row->user_id, $aCurrentItem->id) ?>"><div class="btn-delete"></div></a>
                    </td>              
                  </tr>
                  <?php
                }
              }else{ 
                print ($users_msg);
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
      <form action="/hotcms/<?php echo $module_url ?>/edit_users/<?php echo $aCurrentItem->id ?>#location-users" method="post">
        <div class="row">  
          <?php echo $user_select; ?>
        </div>        
        <input type="hidden" value="3" name="currentTabIndex" id="currentTabIndex">
        <div class="submit">
          <input type="submit" class="input red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
          <a href="/hotcms/<?php echo $module_url ?>/" class="red_button"><?php echo lang('hotcms_back') ?></a>
        </div>    
      </form>
    </div> <!-- location-users -->   
    <div id="location-hours">
        <form method="post" id="editFormDayHours_<?php echo $form_day_hours['row_id'] ?>" class="operation_hours">        
        <?php
        if (isset($form_hours)) {
          foreach ($form_hours as $day_hour) {
            echo $day_hour;
          }
        }
        ?>
      <div class="row padded">
        <label for="extra_fields_input" class="extra_fields_input">I'd like to enter two sets of hours for a single day.</label>
        <input type="checkbox" class="checkbox <?php echo $show_extra_fields ? 'show' : 'hidden' ?>" id="extra_fields_input" <?php echo $show_extra_fields ? 'checked="checked"' : ''; ?> name="extra_fields_input" />
      </div>          
        <div class="row" style="padding: 3px 12px;">
          <div class="submit">
            <a class="red_button_smaller" id="clear_button">Clear</a>
            <input type="submit" class="red_button_smaller ajax_submit_day_hour" value="<?php echo lang('hotcms_save_changes') ?>" id="<?php echo $form_day_hours['row_id'] ?>" />
          </div>
        </div>
      </form>
      <div class="clear"></div>
    </div> <!-- location-hours -->   
  </div> <!-- .tabs -->      
</div>  
