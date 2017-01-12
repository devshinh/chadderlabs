<div class="hero-unit" id="badge_list">
    <div class="container-fluid">
    <?php
    $displayed = array();
    foreach ($all_badges as $badge) {
        if (isset($user_badges)) {
            foreach ($user_badges as $ub) {
                if ($ub->id == $badge->id) {
                    $img = sprintf('<div class="img" style="background-image: url(%s);"></div>',$badge->hover->full_path);
                    printf('<div class="cheddar_badge span3"><div class="badge_img hover">%s</div><div class="badge_name">%s</div></div>', $img, $badge->name);
                    $displayed[$badge->id] = $badge->id;
                }
            }
        }
        if (!in_array($badge->id, $displayed)) {
            $hide_badge_img = '<img src="/asset/upload/badge-default.png" alt="badge" width="145" height="145"/>';
            printf('<div class="cheddar_badge span3"><div class="badge_img">%s</div><div class="badge_name">%s</div></div>', $hide_badge_img, $badge->name);
        }
    }
    ?>
    </div>
</div>
