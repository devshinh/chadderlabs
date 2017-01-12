<?php
if ($environment == 'admin_panel') {
    if (!empty($css)) {
        echo '<link rel="stylesheet" type="text/css" media="all" href="modules/user/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
        echo '<script type="text/javascript" src="modules/user/js/' . $js . "\"></script>\n";
    }
}
$new_items = 10;
?>
<div class="activity_feed_wrapper">
    <div class="container-fluid">
        <div class="row-fluid title">
            <?php
            if (!empty($title)) {
                echo '<div class="box-title">' . $title . "</div>\n";
            }
            ?>
        </div>
        <div id="user_activities">
            <?php
                $aSite = explode('.',$_SERVER['HTTP_HOST']); 
                $sMainDomain = 'www.'.$aSite[1].'.'.$aSite[2];
            
            if (count($items) > 0) {
                foreach ($items as $item) {
                    if ($item->point_type == 'quiz' && $item->quiz_type_name && $item->training_slug) {
                        print('<div class="media activity_wrapper">');
                        $training_item_link = sprintf('<a href="http://%s/labs/product/%s">%s</a>', $item->training_domain, $item->training_slug, $item->training_title);
                        //$training_img = sprintf('<img src="%s" width="37" height="45"/>', $item->training_featured_image_full_path);
                        $training_img = sprintf('<img class="media-object" src="%s" width="37" height="45"/>', $item->training_featured_image_full_path);
                        $desc = sprintf('<span class="quiz_type">%s quiz:</span> %s took %s Quiz and earned %s points.<div class="time_ago">%s AGO</div>', $item->quiz_type_name, $item->screen_name, $training_item_link, $item->points, $item->time_ago);
                        printf('<div class="pull-left image_wrapper">%s</div><div class="media-body desc">%s</div>', $training_img, $desc);
                        print('</div>');
                    }
                    if ($item->point_type == 'EA') {
                        print('<div class="media activity_wrapper">');
                        //$training_item_link = sprintf('<a href="http://%s/labs/product/%s">%s</a>', $item->training->domain, $item->training->slug, $item->training->title);
                        $img = sprintf('<img class="media-object" src="/asset/upload/_EA_trans.jpg" alt="img" width="37" height="45"/>');
                        printf('<div class="pull-left image_wrapper">%s</div><div class="media-body desc">%s %s<div class="time_ago">%s AGO</div></div>',$img,  $item->screen_name, $item->description,$item->time_ago);
                        print('</div>');
                    }
                    if ($item->point_type == 'order') {
                        print('<div class="media activity_wrapper">');
                        $img = $item->product_featured_image_thumb_html;
                        $img_link = sprintf('<a class="pull-left image_wrapper" href="http://%s/store/product/%s">%s</a>',$sMainDomain ,$item->product_slug, $img);
                        printf('%s<div class="media-body desc">%s %s<div class="time_ago">%s AGO</div></div>', $img_link, $item->screen_name, $item->description,$item->time_ago);
                        print('</div>');
                    }
                    if ($item->point_type == 'badge' && $item->badge_icon) {
                            print('<div class="media activity_wrapper">');
                            //badge image are uploaded just to main site
                            $img = sprintf('<img class="media-object badge-img" class="badge-img" src="http://%s/asset/upload/%s.%s" alt="Badge img" width="37" height="37"/>',$sMainDomain, $item->badge_icon->file_name, $item->badge_icon->extension);
                            printf('<div class="pull-left image_wrapper">%s</div><div class="media-body desc">%s %s<div class="time_ago"> %s AGO</div></div>', $img, $item->screen_name, $item->description, $item->time_ago);
                            print('</div>');
                    }
                    
                    if ($item->point_type == 'quiz-draw' && $item->quiz_type_name && $item->training_slug) {
                        print('<div class="media activity_wrapper">');
                        $training_item_link = sprintf('<a href="http://%s/labs/product/%s">%s</a>', $item->training_domain, $item->training_slug, $item->training_title);
                        $training_img = sprintf('<img class="media-object" src="%s" width="37" height="45"/>', $item->training_featured_image_full_path);
                        $desc = sprintf('<span class="quiz_type">%s quiz:</span> %s took %s Quiz and earned %s contest draws.<div class="time_ago">%s AGO</div>', $item->quiz_type_name, $item->screen_name, $training_item_link, $item->draws, $item->time_ago);
                        printf('<div class="pull-left image_wrapper">%s</div><div class="media-body desc">%s</div>', $training_img, $desc);
                        print('</div>');
                    }    
                    if ($item->point_type == 'reffer_colleague') {
                        print('<div class="media activity_wrapper">');
                        if(!empty($item->avatar)){
                          $img = sprintf('<img class="media-object" src="/asset/upload/thumbnail_50x50/%s_thumb.%s" alt="img" width="37"  height="45"/>',$item->avatar->name,$item->avatar->extension);
                        }else{
                            $img = sprintf('<img class="media-object" src="/asset/upload/thumbnail_50x50/icon-user_thumb.jpg" alt="img" width="37"  height="45"/>');
                        }
                        printf('<div class="pull-left image_wrapper">%s</div><div class="media-body desc">%s %s<div class="time_ago">%s AGO</div></div>',$img,  $item->screen_name, $item->description,$item->time_ago);
                        print('</div>');
                    }      
                    if ($item->point_type == 'reffer_veri') {
                        print('<div class="media activity_wrapper">');
                        $img = sprintf('<img class="media-object" src="/asset/upload/_EA_trans.jpg" alt="img" width="37" height="45"/>');
                        //$desc = sprintf('earned 500 contest entries because their <a href="http://earetailprofessionals.cheddarlabs.com/overview/refer-a-colleague">referral</a> verified themselves!');
                        $desc = $item->description;
                        printf('<div class="pull-left image_wrapper">%s</div><div class="media-body desc">%s %s<div class="time_ago">%s AGO</div></div>',$img,  $item->screen_name, $desc,$item->time_ago);
                        print('</div>');
                    }        
                    if ($item->point_type == 'award') {
                        print('<div class="media activity_wrapper">');
                        //$training_item_link = sprintf('<a href="http://%s/labs/product/%s">%s</a>', $item->training->domain, $item->training->slug, $item->training->title);
                        $img = sprintf('<img class="media-object" src="/asset/upload/_EA_trans.jpg" alt="img" width="37" height="45"/>');
                        printf('<div class="pull-left image_wrapper">%s</div><div class="media-body desc">%s %s<div class="time_ago">%s AGO</div></div>',$img,  $item->screen_name, $item->description,$item->time_ago);
                        print('</div>');
                    }        
                    if ($item->point_type == 'draw_winner') {
                        print('<div class="media activity_wrapper">');
                        //$training_item_link = sprintf('<a href="http://%s/labs/product/%s">%s</a>', $item->training->domain, $item->training->slug, $item->training->title);
                        $img = sprintf('<img class="media-object" src="/asset/upload/_EA_trans.jpg" alt="img" width="37" height="45"/>');
                        printf('<div class="pull-left image_wrapper">%s</div><div class="media-body desc">%s %s<div class="time_ago">%s AGO</div></div>',$img,  $item->screen_name, $item->description,$item->time_ago);
                        print('</div>');
                    }       
                    if ($item->point_type == 'upload_avat') {
                        print('<div class="media activity_wrapper">');
                        if(!empty($item->avatar)){
                          $img = sprintf('<img class="media-object" class="avatar" src="/asset/upload/thumbnail_50x50/%s_thumb.%s" alt="img" width="37"  height="45"/>',$item->avatar->name,$item->avatar->extension);
                        }else{
                            $img = sprintf('<img class="media-object" class="avatar" src="/asset/upload/thumbnail_50x50/icon-user_thumb.jpg" alt="img" width="37"  height="45"/>');
                        }
                        printf('<div class="pull-left image_wrapper">%s</div><div class="media-body desc">%s %s<div class="time_ago">%s AGO</div></div>',$img,  $item->screen_name, $item->description,$item->time_ago);
                        print('</div>');
                    }                      
                }
               // $a_uri = explode('/', uri_string());
                //var_dump($a_uri);


?>

                <div class="row-fluid" id="loadMore" onClick="load_activity(<?php echo $limit + $new_items; ?>,<?php echo $restricted; ?>)">
                MORE
                </div>
            <?php

            } else {
                print('<div class="row-fluid">No activities were found.</div>');
            }
            ?>

        </div>
    </div>
</div>

<script type="text/javascript">
    function load_activity(limit,restricted) {        
        var pathname = window.location.pathname;
        var pathname_exploded = pathname.split('/');
//        console.log(pathname_exploded[1]);
        var ajax_url = '';
         if (pathname === '/profile') { 
                 ajax_url = "/ajax/user/activityprofile/"+ limit +"/<?php echo $this->session->userdata('user_id');?>/" + Math.random() * 99999;
         }else if(pathname_exploded[1] === 'public-profile'){
             ajax_url = "/ajax/user/activityprofilepublic/"+ limit +"/"+pathname_exploded[2]+"/" + Math.random() * 99999;
         }else{
            ajax_url = "/ajax/user/activity/"+ limit +"/" +restricted +"/" + Math.random() * 99999;
         }
//         console.log(ajax_url);
        jQuery('#loadMore').html('LOADING ...');
        jQuery.getJSON(ajax_url, function(json) {
            if (json.result && json.activities.length > 0) {
                jQuery("#user_activities").empty();
                var row;
                for (var i in json.activities) {
                    row = json.activities[i];
                    var avatar_img ='';
                    if(typeof row.avatar === 'undefined'){
                        avatar_img =  '<img class="avatar" src="/asset/upload/thumbnail_50x50/icon-user_thumb.jpg" alt="avatar img" width="37" height="45"/>';
                    }else{
                        //console.log(row.avatar.extension);
//                        //$item->avatar->name,$item->avatar->extension
                        avatar_img =  '<img class="avatar" src="/asset/upload/thumbnail_50x50/'+row.avatar.file_name+'_thumb.'+row.avatar.extension+'" alt="avatar img" width="37" height="45"/>';
                    }
                    if (pathname === '/profile' || pathname_exploded[1] === 'public-profile') { 
                        //remove link from scren_name
                        row.screen_name = row.screenname;
                    }
                    //console.log(row.point_type);
                    if (row.point_type === "badge") {
                        jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper"><img width="37" height="37" alt="Badge img" src="'+ row.badge_filename+'" class="badge-img"></div><div class="media-body desc">'+ row.screen_name + ' '+ row.description +'<div class="time_ago"> '+ row.time_ago + ' AGO</div></div></div>');
                    }else if(row.point_type === "reffer_colleague"){
                         jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper">'+avatar_img+'</div><div class="media-body desc">'+ row.screen_name + ' '+ row.description +'<div class="time_ago"> '+ row.time_ago + ' AGO</div></div></div>');
                    }else if(row.point_type === "quiz-draw"){
                          var training_item_link = '<a href="http://'+row.training_domain+'/labs/product/'+row.training_slug+'">'+row.training_title+'</a>';
                          var training_img = '<img src="'+ row.training_featured_image_full_path+'" width="37" height="45"/>';
                          var desc = '<span class="quiz_type">'+row.quiz_type_name+' quiz:</span> '+row.screen_name+' took '+training_item_link+' Quiz and earned '+row.draws+' contest draws.<div class="time_ago">'+row.time_ago+' AGO</div>';
                          jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper">'+training_img+'</div><div class="media-body desc">'+desc+'</div></div></div>');
                    }else if(row.point_type === "quiz"){
                          var training_item_link = '<a href="http://'+row.training_domain+'/labs/product/'+row.training_slug+'">'+row.training_title+'</a>';
                          var training_img = '<img src="'+ row.training_featured_image_full_path+'" width="37" height="45"/>';
                          var desc = '<span class="quiz_type">'+row.quiz_type_name+' quiz:</span> '+row.screen_name+' took '+training_item_link+' Quiz and earned '+row.points+' points.<div class="time_ago">'+row.time_ago+' AGO</div>';
                          jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper">'+training_img+'</div><div class="media-body desc">'+desc+'</div></div></div>');
                    }else if(row.point_type === "order"){
                          var main_domain = '<?php echo $sMainDomain;?>';
                          var product_img_link = '<a href="http://'+main_domain+'/labs/product/'+row.product_slug+'">'+row.product_featured_image_thumb_html+'</a>';
                          var desc = row.screen_name+' '+row.description+'<div class="time_ago">'+row.time_ago+' AGO</div>';
                          jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper">'+product_img_link+'</div><div class="media-body desc">'+desc+'</div></div></div>');
                    }else if(row.point_type === "EA"){
                          var img = '<img src="/asset/upload/_EA_trans.jpg" alt="img" width="37" height="45"/>';
                          var desc = row.screen_name+' '+row.description+'<div class="time_ago">'+row.time_ago+' AGO</div>';
                          jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper">'+img+'</div><div class="media-body desc">'+desc+'</div></div></div>');
                    }else if(row.point_type === "award"){
                          var img = '<img src="/asset/upload/_EA_trans.jpg" alt="img" width="37" height="45"/>';
                          var desc = row.screen_name+' '+row.description+'<div class="time_ago">'+row.time_ago+' AGO</div>';
                          jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper">'+img+'</div><div class="media-body desc">'+desc+'</div></div></div>');                          
                    }else if(row.point_type === "reffer_veri"){
                          var img = '<img src="/asset/upload/_EA_trans.jpg" alt="img" width="37" height="45"/>';
                          //var desc = row.screen_name+' earned 500 contest entries because their <a href="http://earetailprofessionals.cheddarlabs.com/overview/refer-a-colleague">referral</a> verified themselves!<div class="time_ago">'+row.time_ago+' AGO</div>';
                          var desc = row.screen_name+' '+row.description+'<div class="time_ago">'+row.time_ago+' AGO</div>';
                          jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper">'+img+'</div><div class="media-body desc">'+desc+'</div></div></div>');                                                    
                    }else if(row.point_type === "draw_winner"){
                          var img = '<img src="/asset/upload/_EA_trans.jpg" alt="img" width="37" height="45"/>';
                          var desc = row.screen_name+' '+row.description+'<div class="time_ago">'+row.time_ago+' AGO</div>';
                          jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper">'+img+'</div><div class="media-body desc">'+desc+'</div></div></div>');                          
                    }else if(row.point_type === "upload_avat"){
                          var img = avatar_img;
                          var desc = row.screen_name+' '+row.description+'<div class="time_ago">'+row.time_ago+' AGO</div>';
                          jQuery("#user_activities").append('<div class="media activity_wrapper"><div class="pull-left image_wrapper">'+img+'</div><div class="media-body desc">'+desc+'</div></div></div>');
                    }else{
                        continue;
                    }
                    
       
                }

                var new_items = parseInt(<?php echo $new_items; ?>);
                var new_limit = parseInt(json.limit);
                if(json.activities.length === new_limit){
                  jQuery("#user_activities").append('<div class="row-fluid" id="loadMore" onClick="load_activity('+ (new_limit + new_items) +')">MORE</div>');
                }else{
                    jQuery("#user_activities").append('<div id="noMore" class="row-fluid">ALL ACTIVITY VISIBLE</div>');
                }
            }
        })
                .error(function() {
            alert("Sorry but there was an error.");
        });        
    }    
    
    
    function refresh_activity() {
        var ajax_url = "/ajax/user/activity/<?php echo $limit; ?>/" + Math.random() * 99999;
        jQuery.getJSON(ajax_url, function(json) {
            if (json.result && json.activities.length > 0) {
                jQuery("#user_activities").empty();
                var row;
                for (var i in json.activities) {
                    row = json.activities[i];
                    if (typeof row.training_title === "undefined") {
                        continue;
                    }
                    jQuery("#user_activities").append('<div class="row-fluid activity_wrapper"><div class="span2 image_wrapper">'
                            + '<img src="' + row.featured_image_path + '" width="37" height="45" /></div>'
                            + '<div class="span7 desc"><span class="quiz_type">' + row.quiz_type_name + ' quiz: </span>'
                            + row.screen_name + ' took <a href="http://' + row.domain + '/labs/product/' + row.training_slug + '">' + row.training_title + '</a>'
                            + ' Quiz <div class="time_ago">' + row.time_ago + ' AGO</div></div>'
                            + '<div class="span3 points_earned_wrapper"><div class="header_text">Points earned</div>' + row.points + '</div></div>'
                            );
                }
            }
        })
                .error(function() {
            //alert("Sorry but there was an error.");
        });
    }
//window.setInterval("refresh_activity()", 300000); // refreshes every 5 minutes
//window.setInterval("refresh_activity()", 3000000000); 
</script>
