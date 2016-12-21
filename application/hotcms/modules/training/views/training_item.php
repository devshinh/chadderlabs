<?php
if ($environment == 'admin_panel') {
  if (!empty($css)) {
    echo '<link rel="stylesheet" type="text/css" media="all" href="modules/training/css/' . $css . "\" />\n";
  }
  if (!empty($js)) {
    echo '<script type="text/javascript" src="modules/training/js/' . $js . "\"></script>\n";
  }
}
if (isset($item->publish_timestamp) && $item->publish_timestamp > 0) {
  $date = date('Y-m-d H:i:s', $item->publish_timestamp);
} elseif (isset($item->scheduled_publish_timestamp) && $item->scheduled_publish_timestamp > 0) {
  $date = date('Y-m-d H:i:s', $item->scheduled_publish_timestamp);
} else {
  $date = '(unknown publish date)';
}
//var_dump($item);
?>
<div class="item-detail" id="item-<?php print($item->id); ?>">
  <div class="container-fluid">
    <div class="row-fluid">
      <?php
      if (!empty($title)) {
        echo '<div class="box-featured-title">' . $title . "</div>\n";
      }
      ?>
    </div>  
    <div class="row-fluid">
      <h1 class="item-header"><?php echo $item->title; ?></h1>
    </div>      
    <div class="row-fluid">
      <div class="span4">
        <?php printf('<img class="reflection_less" src="%s" alt="%s" title="%s" />', $item->featured_image->full_path, $item->featured_image->name, $item->featured_image->description) ?>
      </div>
      <div class="span8">
        <div class="item-subtitle">Quiz Completion:</div>
        <div class="row-fluid">
          <div class="span6">
            <div class="progress">
              <div class="bar" style="width: 20%;"></div>
            </div>
          </div>
          <div class="span6">
            <span class="blue">20%</span>
          </div>
        </div> 
        <div class="row-fluid">        
          <div class="item-subtitle">Points Achieved: <span class="blue">0 Points</span></div>
        </div>     
        <br />
        <?php
        $last_tag_id = 0;
        $next_tag = '';
        $tag_line = '';
        $close_row = false;
        foreach ($item->tags as $tag) {/*
          if($last_tag_id != $tag->type_id) {
          print $tag_line;
          $tag_line = sprintf('<div class="row-fluid item-tag-wrapper"><div class="span4 tag-type-wrapper">%s</div><div class="span8 item-tag-name-wrapper">%s %s</div></div>',$tag->type_name, $tag->name, $next_tag);
          $last_tag_id = $tag->type_id;
          }else {

          $tag_line.= sprintf($tag_line, $tag->name);
          } */
          if ($last_tag_id != $tag->type_id) {
            if ($close_row) {
              printf('</div></div>');
              $close_row = false;
            };
            print('<div class="row-fluid item-tag-wrapper">');
            printf('<div class="span4 tag-type-wrapper">%s</div><div class="span8 item-tag-name-wrapper">%s', $tag->type_name, $tag->name);
            $last_tag_id = $tag->type_id;
          } else {
            print(', ' . $tag->name);
            $last_tag_id = $tag->type_id;
            $close_row = true;
          }
          $close_row = true;
          ?>
          <?php
        }
        if (count($item->tags) == 0) {
          if (!empty($item->link)) {
            print('<div class="row-fluid item-tag-wrapper">');
            print('<div class="span4 tag-type-wrapper">LINK</div><div class="span8 item-tag-name-wrapper"><a href="#" target="_blank">official website</a></div></div>');
          }
          print('</div> </div>');
        } else {
          //has link
          print('</div> </div>');
          if (!empty($item->link)) {
            print('<div class="row-fluid item-tag-wrapper">');
            print('<div class="span4 tag-type-wrapper">LINK</div><div class="span8 item-tag-name-wrapper"><a href="#" target="_blank">official website</a></div></div>');
          }
          print('</div> </div>');
        }
        ;
        ?>        
        <br />
        <div class="row-fluid">
          <div class="span12">
            <?php print $item->description; ?>
          </div>
        </div><!-- .row-fluid -->
        <div class="row-fluid hero-unit">
          <div class="tabs-assets">
            <ul>
              <li>
                <a href="#videos">Videos</a>
              </li>
              <li><a href="#pics">Screenshots</a></li>
            </ul>
            <div id="videos">
              Videos
            </div>
            <div id="pics">
              <div id="myCarousel" class="carousel slide"> 
                <div class="carousel-inner">
                  <?php
                  foreach ($item->assets as $v) {
                    $active = true;
                    if ($v->type == 1) { // image
                      printf('<div class="%s item"><img src="%s/%s.%s" alt="%s" title="%s"/></div>', $active, $v->thumb, $v->file_name . '_thumb', $v->extension, $v->description, $v->name);
                      if ($active)
                        $active = false;
                    }
                  }
                  ?>
                </div> 
                <a class="carousel-control left" href="#myCarousel" data-slide="prev">‹</a>  
                <a class="carousel-control right" href="#myCarousel" data-slide="next">›</a>                  
              </div>
            </div>
          </div>
        </div><!-- .row-fluid -->

        <div class="row-fluid hero-unit item-features">
          <div class="box-title">GAME FEATURES</div>
          <br />
          <?php print $item->features; ?>
        </div><!-- .row-fluid -->    

      </div>
      <?php
//echo '<pre>';
//var_dump($item);
      ?>
    </div>
