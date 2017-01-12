<div class="clear"></div>

<div class="content">
  <div id="divLeftColumn">
  <?php foreach ($aPhone as $phone) { ?>
    <?php if (!empty($aAssetImage)) {?>
      <?php if (count($aAssetImage)>1) {?>
        <div class="nav">
          <a id="prevAsset">Prev</a>
        </div>
      <?php } ?>
        <div id="divPhoneAssets">
          <?php foreach ($aAssetImage as $assetImage){ ?>    
            <div class="divPhonePictures">
              <img src="/asset/upload/image/phone/asset/<?php printf('%s-%s-280x380.%s', $assetImage->nImageID, $assetImage->sFileName, $assetImage->sExtension) ?>" alt="<?php printf('%s', $assetImage->sFileName)?>"/>
            </div>  
          <?php } ?>      
        </div>
      <?php if (count($aAssetImage)>1) {?>
        <div class="nav right">
          <a id="nextAsset">Next</a>
        </div>
      <?php } ?>
      <?php } else {?>
        <div id="divPhoneAssetSingle">
          <img src="/asset/upload/image/phone/<?php printf('%s-%s-100x190.%s', $phone->nImageID, $phone->sFileName, $phone->sExtension) ?>" alt="<?php printf('%s', $phone->sFileName)?>"/>
        </div>        
      <?php } ?>
    <div class="clear"></div>
    <div id="divPhoneLinks">
      <a class="aDocumentButton" target="_blank" href="/asset/upload/phone-user-guide/<?php printf("%s", $phone->sUserGuide) ?>.pdf">Download User Guide</a>
      <a class="aPrintButton" href="#" onclick="window.print();_gaq.push(['_trackEvent', 'phones', 'pageprint', '<?php printf("%s", $phone->sName) ?>']);return false;">Print</a>
      <a class="aFAQ" href="/support/faq">
        <img alt="faq-image-link" src="/asset/images/faq-image-link.jpg">
      </a>
    </div>
  <?php } ?>
  </div> <!-- end of leftColumn -->
  <div id="divRightColumn">
    <?php foreach ($aPhone as $phone) { ?>
    <div id="divPhoneTexts">
      <h1><?php printf("%s", $phone->sName) ?>
      <?php //if (!empty($phone->sShortName)){?>
        <span><?php //printf(" (%s)", $phone->sShortName) ?>
        <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2F<?php echo $_SERVER['HTTP_HOST'];?>%2Fphones%2F<?php echo $phone->sSlug;?>&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:95px; height:25px;" allowTransparency="true"></iframe>
        </span>
      <?php //}?>
       </h1>
      <div id="divPhoneDescription">
        <?php printf("%s", $phone->sDescription) ?>
      </div>
    </div> <!-- end of divPhoneTexts -->
      <div id="divPriceAndStoreLocator">
        <div id="divPhonePrice">
          $<?php printf("%s", $phone->nPrice) ?><span class="redColor star">*</span>
            <div class="divTaxes"><span class="redColor">*</span> Plus applicable taxes </div>
        </div>   
        <div class="clear"></div>        
      </div> <!-- end of divPriceAndStoreLocator -->
    
      <?php if (!empty($aPhonePromo)){ ?>
      <div id="divPhonePromo">
      <?php if (empty($phone->sPhonePromoLink)){?>
        <img src="/asset/upload/image/phone/<?php printf('%s-%s-540x73.%s', $aPhonePromo[0]->nImageID, $aPhonePromo[0]->sFileName, $aPhonePromo[0]->sExtension) ?>" alt="<?php printf('%s', $aPhonePromo[0]->sFileName)?>"/>
      <?php } else { ?>
        <a href="<?php printf('%s', $phone->sPhonePromoLink)?>">
          <img src="/asset/upload/image/phone/<?php printf('%s-%s-540x73.%s', $aPhonePromo[0]->nImageID, $aPhonePromo[0]->sFileName, $aPhonePromo[0]->sExtension) ?>" alt="<?php printf('%s', $aPhonePromo[0]->sFileName)?>"/>
        </a>
      <?php } ?>  
      </div>
      <?php } ?>
      <div id="divPhoneTabs">
        <ul>
          <li><a href="#tabs-1">Features</a></li>
          <li><a href="#tabs-2">Specifications</a></li>
        </ul>      
      
        <div id="tabs-1">
          <?php printf("%s", $phone->sFeatures) ?>
        </div>
        <div id="tabs-2">
          <?php printf("%s", $phone->sSpecification) ?>
          
        </div> 
        
      </div>
      <div class="clear"></div>
    <?php } ?>
  </div> <!-- end of RightColumn -->
</div>
<div class="clear"></div>
<hr/>
<div id="divRates">
  <div id="divRatesIcon">
    <img src="/asset/images/phones/wallet-graphic.jpg" alt="Graphic wallet" />
  </div>
<div class="clear"></div>  
</div>

<div class="clear"></div>
<div><p class="footnote">&copy; Copyright <?php echo date('Y');?> Nokia. All rights reserved.</p></div>
<div class="clear"></div>
