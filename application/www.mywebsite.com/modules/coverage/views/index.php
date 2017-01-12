<h1>Excluding libraries, where can you talk in Canada?</h1>

<div class="leftColumn">
<p>View wireless coverage maps to your right.</p>
</div>
<!-- form name="mapsearch">
  <div id="divSelectProvince">
    <select id="mapProvince" name="mapProvince" id="mapProvince">
      <option value="" selected="selected"> - <?php echo lang( 'select_province' ); ?> - </option>
  <?php
    foreach($provinceCode as $k => $v) { 
      echo '<option value="'.$k.'">'.$v.'</option>' . "\n";
    }
  ?>
    </select>
  </div>
</form -->

<br />      

<div class="wrapper">
  <div class="framedbox">
    <div id="coverage_map"></div>
  </div>
  <!-- p class="footnote"><img src="/modules/coverage/img/coverage_key1.jpg" alt="Current GSM/GPRS/EDGE" border="0"> GSM Coverage*</p>
  <p><img src="/modules/coverage/img/coverage_key2.jpg" alt="Current GSM/GPRS/EDGE/3.5G HSPA" border="0"> Current GSM/GPRS/EDGE/3.5G HSPA</p -->
  <p class="footnote">* Map depicts an approximation of outdoor coverage. Map may include areas served by unaffiliated carriers and may depict their licensed area rather than an approximation of the coverage there. 
  Actual coverage area may differ substantially from map graphics and coverage may be affected by such things as terrain, weather, foliage, buildings and other construction, signal strength, customer equipment, and other factors. 
  Charges will be based on the location of the site receiving and transmitting the call, not the location of the subscriber. 
  Future coverage, if depicted above, is based on current planning assumptions, but is subject to change.</p>
</div>

<div class="clear">&nbsp;</div>
<hr />
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
    <a href="<?php echo $oAd->sLink?>">
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
<div class="clear"> </div>
