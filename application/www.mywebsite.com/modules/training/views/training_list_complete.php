<div class="hero-unit">
  <div class="box-featured-title">Training Center</div>
<h1>Completed Labs</h1>
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
      <li><a href="#done">Completed Labs</a></li>
    </ul>
      -->
    <div id="done" class="">
            <?php
      if (!empty($complete_lab) && count($complete_lab) > 0) {
        $i = 0;

        foreach ($complete_lab as $item) {
            $img = sprintf('<div style="width: 180px; height: 100px; overflow: hidden"><a href="/labs/product/%s"><img src="%s" alt="%s" title="%s" width="198" height="249" /></a></div>', $item->slug, $item->featured_image->full_path, $item->featured_image->name, $item->featured_image->description);
            $item_link = sprintf('<div class="list-link"><a href="/labs/product/%s">%s</a></div>', $item->slug, $item->title);
            $item_wrapper = sprintf('<div class="span4">%s %s</div>', $img, $item_link);
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
      echo "<p>No labs completed.</p>";
      }
      ?>
    </div>    <!-- #coming-soon -->
  </div> <!-- tabs-items-->


</div>
</div>