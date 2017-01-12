<div class="clear"></div>

<div class="content">
  <div id="mainPromotion">
    <a href="/promotions/<?php printf('%s', strtolower(url_title($main_promotion[0]->sName))) ?>">
     <img src="/asset/upload/image/Promotion/<?php  printf('%d-%s-920x400.%s', $main_promotion[0]->nImageID, $main_promotion[0]->sFileName, $main_promotion[0]->sExtension) ?>" alt="<?php printf('%s', $main_promotion[0]->sFileName)?>" />
    </a>
  </div>
  <div class="clear"></div>
  <?php 
  $i = 0;
  foreach($small_promotions as $promo) {
    $i++;
    if ($i>1){
    ?>
    <div class="smallPromotion <?php echo $promo->nSequence%2==0?'marginRight':''?>">
      <a href="/promotions/<?php printf('%s', strtolower(url_title($promo->sName,'dash'))) ?>">
        <img src="/asset/upload/image/Promotion/<?php  printf('%d-%s-441x252.%s', $promo->nImageID, $promo->sFileName, $promo->sExtension) ?>" alt="<?php printf('%s', $main_promotion[0]->sFileName)?>" />
      </a>
    </div>
    <?php }?>   
  <?php }?>    
</div>

<div class="clear"></div>
<br />
