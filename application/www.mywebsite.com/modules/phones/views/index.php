<div class="clear"></div>

<div class="content">
  <h1>Which one's for you?</h1> 
  <div id="divPhones">
  <?php foreach ($aPhones as $phone) { 
    ?>
    <div class="divPhone <?php echo $phone->nSequence%2==1?'marginRight':''?>">
      <div class="divPhoneName">
      <?php if (!empty($phone->sShortName)){?>
        <h2><?php printf("%s", strtoupper($phone->sName)) ?>    
            <span><?php printf(" (%s)", strtoupper($phone->sShortName)) ?></span>
        </h2>
      <?php } else { ?>
        <h2><?php printf("%s", $phone->sName) ?></h2>
      <?php } ?>        
      </div>
      <hr />
      <div class="divPhoneInfoBox">
        <a href="/phones/<?php printf("%s", $phone->sSlug) ?>">
          <div class="divPhonePicture"><img src="/asset/upload/image/phone/<?php printf('%s-%s-100x190.%s', $phone->nImageID, $phone->sFileName, $phone->sExtension) ?>" alt="<?php printf('%s', $phone->sFileName)?>"/></div>
        </a>
        <div class="divPhoneInfo">
          <div class="divPhonePrice">
            $<?php printf("%s", $phone->nPrice) ?><span class="redColor star">*</span>
              <div class="divTaxes"><span class="redColor">*</span> Plus applicable taxes </div>
          </div>
          <div class="divFeatures">
            <h3>Features</h3>
            <div class="divFeaturesList">
              <?php printf("%s", $phone->sTopFeatures) ?>
            </div>
          </div> <!-- end of divFeatures-->
        </div><!-- end of divPhoneInfo-->
      </div><!-- end of divPhoneBox-->
      <div class="clear"></div>
      <a class="aPhoneButton" href="/phones/<?php printf("%s", $phone->sSlug) ?>">Learn More</a>
    </div> <!-- end of divPhone-->
  <?php } ?>
  </div> <!--end of div phones -->
  <div id="divPromos">
    <div id="flipBoxSmall"> 
      <?php foreach ($aStatements as $row) { ?>
        <?php $image = sprintf('%d-%s-300x260.%s',$row->nImageID, $row->sFileName, $row->sExtension) ?>
        <?php if(empty($row->sLink)) {?>
          <img src="/asset/upload/image/Statements/<?php echo $image?>" alt="<?php printf('%s', $row->sFileName)?>" />
        <?php } else {?>
          <a href="<?php printf('%s', $row->sLink)?>">
           <img src="/asset/upload/image/Statements/<?php echo $image?>" alt="<?php printf('%s', $row->sFileName)?>" />
          </a>
       <?php }?>
      <?php }?>
    </div>
    <div class="clear"></div>
    <div id="divPhoneAd">
    <?php foreach ($aPhoneAd as $row) {?> 
        <?php $image = sprintf('%d-%s-280x251.%s',$row->nImageID, $row->sFileName, $row->sExtension) ?>
        <?php if(empty($row->sLink)) {?>
          <img src="/asset/upload/image/PhoneAds/<?php echo $image?>" alt="<?php printf('%s', $row->sFileName)?>" />
        <?php } else {?>
          <a href="<?php printf('%s', $row->sLink)?>">
           <img src="/asset/upload/image/PhoneAds/<?php echo $image?>" alt="<?php printf('%s', $row->sFileName)?>" />
          </a>
       <?php }?>
    <?php } ?>
    </div>
    <div id="divSignUp">
     <a href="/sign-up"><img src="/asset/images/sign-up-image-link.jpg" alt="sign up image link"/></a>
    </div>
  
 </div> <!--end of div promos -->
</div>



<div class="clear"></div>
<div><p class="footnote">&copy; Copyright <?php echo date('Y');?> Nokia. All rights reserved.</p></div>
<div class="clear"></div>
