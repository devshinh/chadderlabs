<div class="clear"></div>
<div class="content">
  <div id="imageBg">
    <img src="/asset/upload/image/Promotion/<?php  printf('%d-%s-920x400.%s', $promotion[0]->nImageID, $promotion[0]->sFileName, $promotion[0]->sExtension) ?>" alt="<?php printf('%s', $promotion[0]->sFileName)?>" />
  </div>
  
  <div id="bodyBg">
    <?php printf('%s',$promotion[0]->sContent) ?>
    <br />
  </div>
  <div id="footBg"></div>
</div>

<div class="clear"></div>
