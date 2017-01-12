<?php
if ($environment == 'admin_panel') {
    if (!empty($css)) {
        echo '<link rel="stylesheet" type="text/css" media="all" href="modules/training/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
        echo '<script type="text/javascript" src="modules/training/js/' . $js . "\"></script>\n";
    }
}
// format publish date
if (isset($item->publish_timestamp) && $item->publish_timestamp > 0) {
    $date = date('Y-m-d H:i:s', $item->publish_timestamp);
} elseif (isset($item->scheduled_publish_timestamp) && $item->scheduled_publish_timestamp > 0) {
    $date = date('Y-m-d H:i:s', $item->scheduled_publish_timestamp);
} else {
    $date = '(unknown publish date)';
}
?>
<div class="item-detail" id="item-<?php print($item->id); ?>">
    <div class="container-fluid">
        <div class="hero-unit">
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
            <div class="row-fluid item-summary">
                <div class="span4">
<?php printf('<img class="reflection_less" src="%s" alt="%s" title="%s" />', $item->featured_image->full_path, $item->featured_image->name, $item->featured_image->description) ?>
                </div>
                <div class="span8">
                    <div class="row-fluid">
                        <div class="item-subtitle">Lab Completion:</div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="progress">
                                <div class="bar" style="width: <?php print($item->highest_percent_score); ?>%;"></div>
                            </div>
                        </div>
                        <div class="span6">
                            <span class="blue"><?php print($item->highest_percent_score); ?>%</span>
                        </div>
                    </div>  
<?php if ($point_balance == 'ok') { ?>  
                        <?php if (has_permission('earn_points') && ($item->max_points - $item->user_points > 0 )) { ?>
                            <div class="row-fluid">
                                <div class="item-subtitle points">Points Available: <span class="blue"><?php print($item->max_points - $item->user_points); ?></span></div>
                            </div>
    <?php } ?>
                        <?php if (has_permission('earn_draws') && ($item->max_contest_entries - $item->user_contest_entries > 0)) { ?>
                            <div class="row-fluid">
                                <div class="item-subtitle points">Contest Entries Available: <span class="blue"><?php print($item->max_contest_entries - $item->user_contest_entries); ?></span></div>
                            </div>        
    <?php } ?>
                        <?php
                        $last_tag_id = 0;
                        $next_tag = '';
                        $tag_line = '';
                        $close_row = false;
                        if (empty($item->tags))
                            printf('<div><div>');;
                        foreach ($item->tags as $tag) {
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
                        }
//        if (count($item->tags) == 0) {
//          if (!empty($item->link)) {
//            print('<div class="row-fluid item-tag-wrapper last">');
//            print('<div class="span4 tag-type-wrapper">LINK</div><div class="span8 item-tag-name-wrapper"><a href="#" target="_blank">official website</a></div></div>');
//          }
//          print('</div> </div>');
//        } else {
                        //has link
                        print('</div> </div>');
                        if (!empty($item->link)) {
                            $official_website = $item->link;
                            if (substr($official_website, 0, 4) != 'http') {
                                $official_website = 'http://' . $official_website;
                            }
                            print('<div class="row-fluid item-tag-wrapper last">');
                            printf('<div class="span4 tag-type-wrapper">LINK</div><div class="span8 item-tag-name-wrapper"><a href="%s" target="_blank">official website</a></div></div>', $official_website);
                        }
                    }
                    print('</div> </div>');
//        }
                    ?>
                    <?php if ($point_balance == 'ok') { ?>
                        <div class="row-fluid">
                            <div class="span12">
    <?php print $item->description; ?>
                            </div>
                        </div><!-- .row-fluid -->
                    </div> <!-- .hero-unit -->
    <?php
    //get count of assets
    $screenshots = 0;
    $videos = 0;
    $audios = 0;
    foreach ($item->assets as $v) {
        if ($v->type == 1) {
            $screenshots++;
        }
        if ($v->type == 4) {
            $audios++;
        }
        if ($v->type == 3) {
            $videos++;
        }
    }
    if ($screenshots > 0 || $videos > 0 || $audios > 0) {
        ?>
                        <div class="hero-unit">
                            <div class="tabs-assets">
                                <ul>
        <?php if ($videos > 0) { ?>
                                        <li>
                                            <a href="#videos">Videos (<?php print $videos ?>)</a>
                                        </li>
            <?php
        }
        if ($screenshots > 0) {
            ?>
                                        <li><a href="#pics">Images (<?php print $screenshots ?>)</a></li>
                                    <?php } ?>
                                    <?php if ($audios > 0) { ?>
                                        <li>
                                            <a href="#audios">Audio (<?php print $audios ?>)</a>
                                        </li>
            <?php }
        ?>
                                </ul>
                                    <?php if ($videos > 0) { ?>
                                    <div id="videos">
                                        <div id="<?php echo ($videos > 3 ? 'videoCarousel' : '') ?>" class="<?php echo ($videos > 3 ? 'carousel slide' : '') ?>">
                                            <!-- Carousel items -->
                                            <div class="carousel-inner">
            <?php
            $i_pics = 0;
            $active = true;
            foreach ($item->assets as $v) {
                if ($v->type == 3) { // video
                    $img = sprintf('<div class="span4"><a class="sublime" data-toggle="modal" data-id="%s" href="%s">%s<div class="screenshot-title">%s</div></a>%s</div>', $i_pics, $v->full_path, $v->thumb_html, $v->name, $v->lightbox_html);
                    if ($i_pics % 3 == 0) {
                        printf('<div class="item %s">%s', $active ? 'active' : '', $img);
                    } else {
                        print $img;
                    }
                    $i_pics++;
                    if ($i_pics % 3 == 0)
                        print('</div>');
                    if ($active)
                        $active = false;
                }
            }
            if ($i_pics % 3 != 0)
                print('</div>');
            ?>
                                            </div>
                                            <!-- Carousel nav -->
            <?php if ($videos > 3) { ?>
                                                <a class="carousel-control left" href="#videoCarousel" data-slide="prev">‹</a>
                                                <a class="carousel-control right" href="#videoCarousel" data-slide="next">›</a>
            <?php } ?>
                                        </div>
                                    </div>
            <?php
        }
        if ($screenshots > 0) {
            ?>
                                    <div id="pics">
                                        <div id="<?php echo ($screenshots > 3 ? 'myCarousel' : '') ?>" class="<?php echo ($screenshots > 3 ? 'carousel slide' : '') ?>">
                                            <!-- Carousel items -->
                                            <div class="carousel-inner">
            <?php
            $i_pics = 0;
            $active = true;
            //print ('<div class="item active"><div class="row-fluid">');
            foreach ($item->assets as $v) {

                if ($v->type == 1) { // image
                    $img = sprintf('<div class="span4"><a class="screenshot-image" data-toggle="modal" data-id="%s"><img src="%s/%s.%s" alt="%s" title="%s"/></a></div>', $i_pics, $v->thumb, $v->file_name . '_thumb', $v->extension, $v->description, $v->name);
                    if (($v->height > $v->width) && $v->height > 400) {
                        $screenshots_full[$v->id] = sprintf('<img src="" data-lazy-load="%s" alt="%s" title="%s" height="400px" width="%spx" />', $v->full_path, $v->name, $v->name, (400 / ( $v->height / $v->width)));
                    } else {
                        $screenshots_full[$v->id] = sprintf('<img src="" data-lazy-load="%s" alt="%s" title="%s" height="%s" width="%s" />', $v->full_path, $v->name, $v->name, $v->height, $v->width);
                    }
                    if ($i_pics % 3 == 0) {
                        printf('<div class="item %s">%s', $active ? 'active' : '', $img);
                    } else {
                        print $img;
                    }
                    $i_pics++;
                    if ($i_pics % 3 == 0)
                        print('</div>');
                    if ($active)
                        $active = false;
                }
            }
            if ($i_pics % 3 != 0)
                print('</div>');
            ?>

                                            </div>
                                            <!-- Carousel nav -->
            <?php if ($screenshots > 3) { ?>
                                                <a class="carousel-control left" href="#myCarousel" data-slide="prev">‹</a>
                                                <a class="carousel-control right" href="#myCarousel" data-slide="next">›</a>
            <?php } ?>
                                        </div>
                                    </div>
        <?php } ?>

                                <?php if ($audios > 0) { ?>
                                    <div id="audios">
                                        <div id="<?php echo ($audios > 3 ? 'audioCarousel' : '') ?>" class="<?php echo ($audios > 3 ? 'carousel slide' : '') ?>">
                                            <!-- Carousel items -->
                                            <div class="carousel-inner">
            <?php
            $i_pics = 0;
            $active = true;
            foreach ($item->assets as $v) {
                if ($v->type == 4) { // audio
                    $img = sprintf('<div class="span4"><a class="sublime" data-toggle="modal" data-id="%s" href="%s%s">%s<div class="screenshot-title">%s</div></a></div>', $i_pics, $v->full_path, $v->extension, $v->thumb_html, $v->name);
                    if ($i_pics % 3 == 0) {
                        printf('<div class="item %s">%s', $active ? 'active' : '', $img);
                    } else {
                        print $img;
                    }
                    $i_pics++;
                    if ($i_pics % 3 == 0)
                        print('</div>');
                    if ($active)
                        $active = false;
                }
            }
            if ($i_pics % 3 != 0)
                print('</div>');
            ?>
                                            </div>
                                            <!-- Carousel nav -->
            <?php if ($audios > 3) { ?>
                                                <a class="carousel-control left" href="#audioCarousel" data-slide="prev">‹</a>
                                                <a class="carousel-control right" href="#audioCarousel" data-slide="next">›</a>
            <?php } ?>
                                        </div>
                                    </div>
            <?php }
        ?>
                            </div><!-- .row-fluid -->
                        </div><!-- .hero-unit -->
    <?php } ?>
                    <?php if (!empty($item->features)) { ?>
                        <div class="hero-unit item-features">
                        <?php if ($this->session->userdata('siteID') == 2) { ?>
                                <div class="box-title">GAME FEATURES</div>
                            <?php } else { ?>
                                <div class="box-title">FEATURES</div>
                            <?php } ?>
                            <?php print $item->features; ?>
                        </div><!-- .row-fluid -->
                        <?php } ?>
                    <?php if (!empty($item->variants)) { ?>
                        <div class="hero-unit item-variants">
                            <!--@TODO DYNAMIC TEXT HERE --> 
                            <div class="box-title">VARIANTS</div>
        <?php
        //var_dump($item);
        $i_pics = 0;
        foreach ($item->variants as $variant) {

            printf('<div class="row-fluid item-variant-wrapper %s">', (count($item->variants) == $i_pics + 1) ? 'last' : '');
            foreach ($variant->details as $variant_detail) {
                if ($variant_detail->field_type == 'image') {
                    $img = sprintf('<img class="reflection_less" src="%s/%s.%s" alt="%s" title="%s"/>', $variant_detail->image->thumb, $variant_detail->image->file_name . '_thumb', $variant_detail->image->extension, $variant_detail->image->name, $variant_detail->image->description);
                    printf('<div class="span2"><a class="variant-image" data-toggle="modal" data-id="%s">%s</a></div>', $i_pics, $img);
                    $variant_images_full[$variant_detail->image->id] = sprintf('<img src="" data-lazy-load="%s" alt="%s" title="%s" height="%s" width="%s"/>', $variant_detail->image->full_path, $variant_detail->image->name, $variant_detail->image->name, $variant_detail->image->height, $variant_detail->image->width);
                    $i_pics++;
                }
            }
            print ('<div class="span10">');
            $i = 0;
            $used = 0;
            $class = '';
            foreach ($variant->details as $variant_detail) {
                if ($variant_detail->field_type != 'image' && $variant_detail->value != '')
                    $used++;
            }
            foreach ($variant->details as $variant_detail) {
                if ($i == $used) {
                    $class = 'last';
                }
                $i++;
                if ($variant_detail->field_type != 'image' && $variant_detail->value != '') {
                    printf('<div class="row-fluid item-variant-detail-wrapper %s">', $class);
                    printf('<div class="span4">%s:</div>', $variant_detail->label);

                    if ($variant_detail->field_type == 'checkboxes') {
                        printf('<div class="span8">');
                        foreach (explode('\n', $variant_detail->value) as $val) {
                            if (!empty($val)) {
                                printf($val . '</br>');
                            }
                        }
                        printf('</div>');
                    } else {

                        printf('<div class="span8">%s</div>', $variant_detail->value);
                    }
                    print('</div>');
                }
            }
            print('</div>'); // end of span10
            print('</div>'); // end of item-variant-wrapper
        }
        ?>
                        </div>
                    <?php } ?>
                </div> <!-- span8 -->
                <?php
                //echo '<pre>';
                //var_dump($item);
                ?>
            </div><!-- .row-fluid -->

            <!-- //modal windows for variants images -->
            <div id="variant-images" class="modal hide fade in" style="display: none; ">
                <div class="modal-body">
                    <a id="prev" class="carousel-control left nav" href="#">‹</a>
                    <div id="slideshow-variant-images" class="carousel-inner">
                        <!-- Carousel items -->
                        <?php
                        foreach ($variant_images_full as $img) {
                            echo $img;
                        }
                        ?>
                    </div>
                    <div id="title-variant"></div>
                    <div id="slideshow-variant-summary"></div>
                    <a id="next" class="carousel-control right nav" href="#">›</a>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn" data-dismiss="modal">Close</a>
                </div>
            </div>

            <!-- //modal windows for screenshots -->
            <div id="screenshots-images" class="modal hide fade in" style="display: none; ">
                <div class="modal-body">
                    <?php if ($screenshots > 2){?>
                    <a id="prev-screenshot" class="carousel-control left nav" href="#">‹</a>
                    <?php }?>
                    <div id="slideshow-screenshots" class="carousel-inner">
                        <!-- Carousel items -->
                        <?php
                        foreach ($screenshots_full as $img) {
                            echo $img;
                        }
                        ?>
                    </div>
                    <div id="title-screenshot"></div>
                    <div id="slideshow-screenshot-summary"></div>
                    <?php if ($screenshots > 2){?>
                    <a id="next-screenshot" class="carousel-control right nav" href="#">›</a>
                    <?php }?>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn" data-dismiss="modal">Close</a>
                </div>
            </div>
<?php } else { ?>
            <div id="no-cheddar-img"></div>
            <br />
            <div class="box-title">This lab is all outta Cheddar!</div>
            <p>
                Training and quizzes for <?php echo $site_name ?> are currently unavailable due to insufficient Cheddar Points for the Training Lab.
            </p>
        </div>
    </div>
    </div>
<?php } ?>
