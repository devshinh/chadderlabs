<?php
if ($environment == 'admin_panel') {
    if (!empty($css)) {
        echo '<link rel="stylesheet" type="text/css" media="all" href="modules/training/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
        echo '<script type="text/javascript" src="modules/training/js/' . $js . "\"></script>\n";
    }
}
?>
<div class="hero-unit" id="new-item-preview">
    <div class="container-fluid">
        <div class="row-fluid">
            <?php
            if (!empty($title)) {
                echo '<div class="box-title">' . $title . "</div>\n";
            }
            ?>
        </div>
        <?php foreach ($items as $item) { ?>
            <div class="row-fluid item-preview">
                <div class="span4">
                    <?php
                    $img = sprintf('<img class="reflection_less" src="%sthumbnail_80x98/%s_thumb.%s" alt="%s" title="%s" />', $item->featured_image->folder_path, $item->featured_image->file_name, $item->featured_image->extension, $item->featured_image->name, $item->featured_image->description);
                    printf('<a href="/labs/product/%s" title="%s">%s</a>', $item->slug, $item->title, $img);
                    ?>
                </div>
                <div class="span1"></div>
                <div class="span7">
               <!--   <div class="item-date"><?php echo date($this->config->item('timestamp_format_without_time'), $item->create_timestamp); ?></div>-->
                    <div class="item-title"><?php echo $item->title; ?></div>
                    <p>Lab Completion: <span class="blue"><?php print($item->highest_percent_score); ?>%</span></p>
                    <?php if ($point_balance == 'ok') { ?>
                        <?php if (has_permission('earn_points') && (($item->max_points - $item->user_points) > 0 )) { ?>   
                            <p>Points Available: <span class="blue"><?php print($item->max_points - $item->user_points); ?></span></p>
                        <?php } ?>
                        <?php if (has_permission('earn_draws') && (($item->max_contest_entries - $item->user_contest_entries) > 0)) { ?>   
                            <p>Entries Available: <span class="blue"><?php print($item->max_contest_entries - $item->user_contest_entries); ?></span></p>
                        <?php } ?>         

                        <a class="btn btn-primary" href="/labs/product/<?php echo $item->slug; ?>">
                            TRAIN NOW
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="clearfix"></div>
        <?php } ?>
        <?php if ($point_balance == 'ok') { ?>
            <div class="pull-right height18" style="height:18px">
                <div class="span12 ">
                    <a href="/labs/browse-labs" class="view-all-link"><span class="view-all-arrows">&raquo; </span>View All</a>
                </div>
            </div>
        <?php } ?>  
    </div>
</div>


