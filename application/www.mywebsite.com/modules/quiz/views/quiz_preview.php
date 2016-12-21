<?php
if ($environment == 'admin_panel') {
    if (!empty($css)) {
        echo '<link rel="stylesheet" type="text/css" media="all" href="modules/quiz/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
        echo '<script type="text/javascript" src="modules/quiz/js/' . $js . "\"></script>\n";
    }
}
?>
<div class="hero-unit" id="quiz-preview">
    <div class="container-fluid">
        <div class="row-fluid">
            <?php
            if (!empty($title)) {
                echo '<div class="box-title">' . $title . "</div>\n";
            }
            ?>
        </div>
        <?php
        $i = 0;
        foreach ($items as $item) {
            $i++;
            ?>
            <div class="row-fluid">
                <div class="span2">
                    <?php
                    if (!empty($item->type->icon_image)) {
                        $icon = $item->type->icon_image;
                        printf('<img src="%s" alt="%s" title="%s" width="30" height="30" />', $icon->full_path, $icon->name, $icon->description);
                    }
                    ?>
                </div>
                <div class="span10">
                    <div class="item-title"><?php echo $item->name; ?></div>
                    <p>Quiz Completion:</p>
                    <div class="row-fluid">
                        <div class="span9">
                            <div class="progress">
                                <div class="bar" style="width: <?php print($item->highest_percent_score); ?>%;"></div>
                            </div>
                        </div>
                        <div class="span3">
                            <span class="blue"><?php print($item->highest_percent_score); ?>%</span>
                        </div>
                    </div> 
                    <?php if ($point_balance == 'ok') { ?>
                        <?php if (has_permission('earn_points')) { ?>
                            <?php if (($item->max_points - $item->user_points) > 0) { ?>
                                <div class="row-fluid">
                                    <p>Points Available: <span class="blue"><?php echo number_format(($item->max_points > $item->user_points ? $item->max_points - $item->user_points : 0), 0, '.', ','); ?></span></p>
                                </div>
                            <?php
                            }
                        }
                        if (has_permission('earn_draws') && ($item->max_contest_entries - $item->user_contest_entries > 0)) {
                            ?>         
                            <p>Contest Entries Available: <span class="blue"><?php echo number_format(($item->max_contest_entries > $item->user_contest_entries ? $item->max_contest_entries - $item->user_contest_entries : 0), 0, '.', ','); ?></span></p>
        <?php } ?>
        <?php if (has_permission('earn_points') || has_permission('earn_draws')) { ?>
                            <a class="btn btn-primary" href="/quiz/<?php echo $item->slug; ?>">
                                TAKE QUIZ
                            </a>
        <?php } ?>
    <?php } ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <?php
            if (count($items) > 1 && count($items) != $i) {
                echo '<hr />';
            }
        }
        ?>
    </div>

</div>
