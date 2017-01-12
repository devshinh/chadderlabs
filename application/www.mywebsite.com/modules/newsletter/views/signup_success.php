<h1>Thanks for signing up.</h1>
<p>Now you'll be the first to know about our upcoming deals and new phone releases.</p>
<br /><br />

<script type="text/javascript">
var aCarousel_promos    = [ <?php $index = 0; foreach ($oCarousel->aPromos as $row){ ?>
<?php $image = sprintf('%d-%s-598x250.%s',$row->nImageID, $row->sFileName, $row->sExtension) ?>
{ id: <?php echo $row->nCarouselContentID ?>, src: "<?php echo $image?>",title: "<?php echo  $row->sName  ?>", url: "<?php echo $row->sLink ?>" }<?php if (++$index < count( $oCarousel->aPromos )): ?>, <?php endif ?>

<?php } ?>
];
var aCarousel_statements    = [ <?php $index = 0; foreach ($oCarousel->aStatements as $row){ ?>
<?php $image = sprintf('%d-%s-930x100.%s',$row->nImageID, $row->sFileName, $row->sExtension) ?>
{ id: <?php echo $row->nStatementID ?>, src: "<?php echo $image?>",title: "<?php echo  $row->sName  ?>", url: "<?php echo $row->sLink ?>" }<?php if (++$index < count( $oCarousel->aStatements )): ?>, <?php endif ?>

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

<div class="clear"> </div>

<?php if ( $postalcode>'' ){ ?>
<script type="text/javascript">
_gaq.push(['_setCustomVar', 3, "eNewsletter_Subscriber", "<?php echo str_replace(' ','',$postalcode);?>", 3]);
_gaq.push(['_trackPageview']);
</script>
<?php } ?>
