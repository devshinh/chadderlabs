<div class="container">
    <div class="span8">
        <div class="hero-unit">
            <div class="row-fluid">
                <div class="span6">
                    <h1><?php echo $user->screen_name ?></h1>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span3">
                    <?php if(!empty($user->avatar_id)){
                      $avatar = asset_load_item($user->avatar_id);
                      printf('<img height="125" width="125" src="/asset/upload/thumbnail_200x200/%s_thumb.%s" alt="user-avatar" title="user-avatar" />',$avatar->name,$avatar->extension);
                    }else{?>
                      <img height="125" width="125" src="/asset/upload/thumbnail_200x200/icon-user_thumb.jpg" alt="user-avatar" title="user-avatar" />
                    <?php }?>
                    <br />
                    <br />
                </div>
                <div class="span9">

                    <div class="row-fluid item-tag-wrapper">
                        <?php printf('<div class="span4 tag-type-wrapper">Current points</div><div class="span8 item-tag-name-wrapper">%s</div>', number_format($user_points['current'])); ?>
                    </div>
                    <div class="row-fluid item-tag-wrapper">
                        <?php printf('<div class="span4 tag-type-wrapper">Current contest entires</div><div class="span8 item-tag-name-wrapper">%s</div>', number_format($user_draws['current'])); ?>
                    </div>
                    <div class="row-fluid item-tag-wrapper">
                        <?php printf('<div class="span4 tag-type-wrapper">Quizzes Taken</div><div class="span8 item-tag-name-wrapper">%s</div>', number_format($quiz_number)); ?>
                    </div>                      
                    <div class="row-fluid item-tag-wrapper last">
                        <?php printf('<div class="span4 tag-type-wrapper">Date joined</div><div class="span8 item-tag-name-wrapper">%s</div>', date($this->config->item('timestamp_format_without_time'), $user->created_on)); ?>
                    </div>                   
                </div>        
            </div>
        </div>

        <?php print($activity_feed['content']) ?>
    </div>
    <div class="span4">
        <div class="hero-unit">
            <div class="row-fluid">
                <h2 class="pull-left">Badges</h2>
                <?php if(!empty($badges)){?>
                <div class="pull-right">
                    <a class="view-all-link" href="http://<?php echo $sMainDomain; ?>/badges/<?php echo strtolower($user->screen_name) ?>">
                        <span class="view-all-arrows">» </span>See All</a>
                </div>
                <?php } ?>
            </div>            
            <div class="row-fluid">
                <?php 
                if(!empty($badges)){
                    printf('<a href="http://%s/badges/%s">',$sMainDomain, strtolower($user->screen_name));
                    $i = 0;
                    foreach($badges as $badge){
                        if($i % 4 == 0){
                          printf('<div class="row-fluid badge-row">');
                        }
                        printf('<div class="badge_icon_wrapper span3"><img src="http://%s/asset/upload/%s.%s" alt="%s" /></div>',$sMainDomain, $badge->icon->file_name, $badge->icon->extension,$badge->icon->name);
                        $i++;
                        if($i % 4 == 0){
                          printf('</div>');
                        }
                    }
                    if ($i % 4 != 0) {print('</div>');}
            
                    print('</a>');
                }else{
                    printf('No badges yet.');
                }
                ?>
            </div>
        </div>           

        <div class="hero-unit" id="labs">
            <div class="row-fluid">
                <h2 class="pull-left">Labs</h2>
               <?php /** <div class="pull-right">
                    <a class="view-all-link" href="http://<?php echo $sMainDomain; ?>/training-labs">
                        <span class="view-all-arrows">» </span>See All</a>
                </div>          */?>
            </div>
                <?php 
                if(!empty($sites)){
//                  echo '<script type="text/javascript">jQuery(document).ready(function() { console.log("trainings: '.$trainings.'"); });</script>';
//                  echo '<script type="text/javascript">jQuery(document).ready(function() { console.log("targets: '.$targets.'"); });</script>';

                    foreach($sites as $site){
                        if ($site->id ==1) continue;
                        if(!empty($site->image)){
                            echo '<div class="row-fluid lab">'; 
                            printf('<a href="http://%s/welcome">%s<span>%s</span></a>',$site->domain, $site->image->thumb_html, $site->name);
                            echo '</div>';
                        }else{
                            echo '<div class="row-fluid lab">'; 
                            printf('<a href="http://%s/welcome"><span>%s</span></a>',$site->domain, $site->name);
                            echo '</div>';                            
                        }
                    }
                    
                }else{
                    printf('No sites yet.');
                }
                ?>
        </div>
        
       <?php print $retailers_info['content']; ?>           

        <?php //print($leaderboard_widget['content']) ?>

    </div>    

</div> <!-- /container -->
