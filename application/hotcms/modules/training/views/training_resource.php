<?php
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/training/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/training/js/' . $js . "\"></script>\n";
  }
}
if (count($item->resources)!=0){
?>
<div class="hero-unit" id="training-resource">
  <div class="container-fluid">
    <div class="row-fluid">
      <?php
      if (!empty($title)) {
        echo '<div class="box-title">' . $title . "</div>\n";
      }
      ?>
    </div>


      <?php
      foreach ($item->resources as $resource) {
        print('<div class="row-fluid resouce-links">');
        if($resource->extension=='pdf'){
          $pdf_icon = sprintf('<img src="/themes/cheddarLabs/images/pdf-icon.jpg" alf="pdf-icon" title="pdf-%s" width="33" height="35"/>', $resource->name);
          $pdf_icon_link = sprintf('<a href="%s" target="_blank">%s</a>', $resource->full_path, $pdf_icon);
          printf('<div class="span2">%s</div><div class="span10"><a class="resource-link" href="%s" target="_blank">%s</a></div>',$pdf_icon_link,$resource->full_path, $resource->name);
        }
        else{
          printf('<div class="span12"><a class="resource-link" href="%s" target="_blank">%s</a></div>',$resource->full_path, $resource->name);
        }
        print('<div class="clearfix"></div></div>');
      }
      ?>
  </div><!--container-fluid-->
</div>
<?php }?>


