<div class="hero-unit">
  <div class="box-featured-title">Training Center</div>
<h1>Cheddar Available</h1>
<?php
if (!empty($title)) {
  echo '<h3>' . $title . "</h3>\n";
}
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/training/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/training/js/' . $js . "\"></script>\n";
  }
}
?>
<div class="items-list">

  <div class="tabs-items">
      <!--
    <ul>
      <li><a href="#available">Cheddar Available</a></li>
    </ul>
      -->
    <div id="available" class="">
      <?php
      if (count($uncomplete_lab) > 0) {
        $i = 0;

        foreach ($uncomplete_lab as $item) {
            $item_progress = '';
            
                $item_progress = sprintf('<div class="row-fluid progress-wrapper">
                  <div class="span9 pull-left">
                    <div class="progress">
                      <div class="bar" style="width: %s%%;"></div>
                    </div>
                  </div>
                  <div class="span3 pull-right">
                    <span class="blue">%s %%</span>
                  </div>
                </div><div class="clearfix"></div>',$item->highest_percent_score,$item->highest_percent_score);             

           
            $img = sprintf('<div style="width: 180px; height: 100px; overflow: hidden"><a href="/labs/product/%s"><img src="%s" alt="%s" title="%s" width="198" height="249" /></a></div>', $item->slug, $item->featured_image->full_path, $item->featured_image->name, $item->featured_image->description);
            $item_link = sprintf('<div class="list-link"><a href="/labs/product/%s">%s</a></div>', $item->slug, $item->title);
            $item_wrapper = sprintf('<div class="span4">%s %s %s</div>', $img, $item_progress, $item_link);
            if ($i % 3 == 0) {
              printf('<div class="row-fluid">%s', $item_wrapper);
            } else {
              print $item_wrapper;
            }
            $i++;
            if ($i % 3 == 0)
              print('</div>');

        }
        if ($i % 3 != 0)
          print('</div>');


      } else {
      echo "<p>No training at this moment.</p>";
      }
      ?>
    </div> <!-- #new -->
  </div> <!-- tabs-items-->


</div>
</div>