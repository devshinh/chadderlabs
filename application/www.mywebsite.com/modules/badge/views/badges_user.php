<div class="hero-unit" id="badge_list">
    <?php if(!empty($screen_name)){?>
    <h1><?php print($screen_name)?>'s badges</h1>
    <p>If you use your dedication, smarts, and swag grabbing capabilities to their fullest extent, then you can expect to see your badge collection grow. Outdo your buddies and get them all!</p>
    <?php } ?>
    <div class="container-fluid">
    <?php
    $displayed = array();
    foreach ($all_badges as $badge) {
        if (!empty($user_badges)) {
            foreach ($user_badges as $ub) {
                if ($ub->id == $badge->id) {
                    $img = sprintf('<div class="img" style="background-image: url(%s);"></div>',$badge->hover->full_path);
                    printf('<div class="cheddar_badge span3"><div class="badge_img hover">%s</div><div class="badge_name">%s</div></div>', $img, $badge->name);
                    $displayed[$badge->id] = $badge->id;
                }
            }
        }
        if (!in_array($badge->id, $displayed)) {
            $hide_badge_img = '<img src="/asset/upload/cheddarLabs-badges-w1_03.jpg" alt="badge" width="145" height="145"/>';
            printf('<div class="cheddar_badge span3"><div class="badge_img">%s</div><div class="badge_name">%s</div></div>', $hide_badge_img, $badge->name);
        }
    }
    ?>
    </div>
</div>
