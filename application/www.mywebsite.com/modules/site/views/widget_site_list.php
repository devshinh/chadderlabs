<div id="site-list">
    <?php
    foreach ($available_points as $site_id => $points) {

        foreach ($sites as $site) {
            if ($site->id == $site_id) {
                if ($site->id == 1)
                    continue;
                ?>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="media bg-white">
                            <a class="pull-left" href="http://<?php echo $site->domain ?>"><img class="media-object" width="227" height="157" alt="brand image" src="<?php echo $site->image->full_path ?>"></a>
                            <div class="media-body">
                                <h2 class="media-heading"><a class="site-link" href="http://<?php echo $site->domain ?>"><?php echo $site->name ?></a></h2>
                                <p><strong>Cheddar Available:</strong></p>
                                <?php if (($available_points[$site->id]) > 0) { ?>
                                    <p>Points: <strong><span class="blue"><?php print number_format($available_points[$site->id], 0, ',', ','); ?></span></strong></p>
                                <?php } else { ?>
                                    <p>Points: <strong><span class="blue">0</span></strong></p>
                                <?php } ?>
                                <?php if (($available_ce[$site->id]) > 0) { ?>
                                    <p>Contest entries: <strong><span class="blue"><?php print number_format($available_ce[$site->id], 0, ',', ',') ?></span></strong></p>
            <?php } ?>                    
                                <a href="http://<?php echo $site->domain ?>" class="btn btn-primary">Go To Lab</a></div>
                        </div>                
                    </div><!--/span12-->
                </div>
            <?php
            }
        }
    }
    ?>
</div>
