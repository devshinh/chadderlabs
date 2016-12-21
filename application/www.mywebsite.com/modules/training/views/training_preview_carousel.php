<div class="games-carousel hero-unit">
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span8">
      <?php
      if (!empty($title)) {
        echo '<div class="box-title">' . $title . "</div>\n";
      }
      ?>
      </div>
      <div class="pull-right">
        <a href="/labs/browse-games" class="view-all-link"><span class="view-all-arrows">&raquo; </span>See All</a>
      </div>
    </div>
    <div class="row-fluid">

      <?php /*
        <div class="nav"><a id="prev-games" href="#" class="btn btn-primary">Prev</a> <a id="next-games" href="#" class="btn btn-primary">Next</a></div>
        <div id="slideshow-games">
        <ul>
        <?php
        foreach ($items as $item) {
        if (!empty($item->featured_image)) {
        $img = sprintf('<img class="reflection_less" src="%s/thumbnail_80x98/%s_thumb.%s" alt="%s" title="%s" />', $item->featured_image->folder_path, $item->featured_image->file_name, $item->featured_image->extension, $item->featured_image->name, $item->featured_image->description);
        printf('<li><a class="carousel-game-link" href="/labs/product/%s" title="%s">%s</a></li>', $item->slug, $item->title, $img);
        }
        }
        ?>
        </ul>
        </div>

       */ ?>
      <div id="itemSlider">

          <ul>
            <?php
            shuffle($items);
            foreach ($items as $item) {
              if (!empty($item->featured_image)) {
                $img = sprintf('<img class="reflection_less" src="$sthumbnail_80x98/%s_thumb.%s" alt="%s" title="%s" />', $item->featured_image->folder_path, $item->featured_image->file_name, $item->featured_image->extension, $item->featured_image->name, $item->featured_image->description);
                printf('<li><a class="carousel-game-link" href="/labs/product/%s" title="%s">%s</a></li>', $item->slug, $item->title, $img);
              }
            }
            ?>
          </ul>

      </div>
    </div>
  </div>
</div>
