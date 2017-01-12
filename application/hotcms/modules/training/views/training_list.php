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
    <ul>
      <li><a href="#all">All games </a></li>
      <li><a href="#new">New Games</a></li>
      <li><a href="#coming-soon">Coming Soon</a></li>
    </ul>
    <div id="all" class="hero-unit">
      <?php
      if (count($items_all) > 0) {
        $i = 0;

        foreach ($items_all as $item) {
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
      echo "<p>No training at this moment.</p>";
      }
      ?>
    </div>
    <div id="new" class="hero-unit">
      <?php
      if (count($items_new) > 0) {
        $i = 0;

        foreach ($items_new as $item) {
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
      echo "<p>No training at this moment.</p>";
      }
      ?>
    </div> <!-- #new -->
    <div id="coming-soon" class="hero-unit">
            <?php
      if (count($items_coming) > 0) {
        $i = 0;

        foreach ($items_coming as $item) {
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
      echo "<p>No training at this moment.</p>";
      }
      ?>
    </div>    <!-- #coming-soon -->
  </div> <!-- tabs-items-->


</div>
