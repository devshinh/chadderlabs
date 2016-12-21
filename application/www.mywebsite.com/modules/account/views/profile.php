<div class="container" id="profile_page">
    <div class="span8">
        <div class="hero-unit">
            <?php if (!empty($message)){?> 
            <div class="row-fluid">                
                <?php if (!empty($message)) { ?><div class="message"><?php echo $message; ?></div><?php } ?>
            </div>
            <?php }?>
            <div class="row-fluid">
                <div class="span6">
                    <h1>Profile</h1>
                </div>
                <div class="span6">
                    <div class="pull-right">
                        <a href="profile-update" class="btn btn-primary">Edit profile</a>
                    </div>
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
                    <!--<a href="#" class="view-all-link"><span class="view-all-arrows">&raquo; </span>Pick Avatar</a>-->
                        <a href="#avatar-upload-wrapper" class="view-all-link" data-toggle="modal"><span class="view-all-arrows">&raquo; </span>Upload Avatar</a>
                        
                            <div class="modal hide fade" id="avatar-upload-wrapper" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-header">
                                    
                                    <h3>Avatar upload</h3>
                                </div>
                                <div class="modal-body">
                                    <?php if ($user->verified) { ?>
                                    <form action="/avatar/upload" method="POST" enctype="multipart/form-data" id="avatar-upload-form">
                                        <div class="row-fluid">
                                            Upload your avatar. For best avatar results use jpg, jpeg, png or gif.
                                        </div>
                                        <div class="row-fluid">
                                        <label for="email">File: <span class="red">*</span></label>
                                        </div>
                                        <div class="row-fluid">
                                          <input type="file" name="avatar" id="avatar" size="20" required class="required avatarfile" />
                                        </div>
                                        <div class="row-fluid">
                                        <input class="btn btn-primary" type="submit" name="submit" value="Upload" />
                                        </div>                            
                                    </form>
                                    <?php }else{
                                       echo('<p>You need to verify your account in order to upload an avatar.</p>'
                                               . '<a data-toggle="modal" href="#verification-upload" class="view-all-link">
                                                  <span class="view-all-arrows">» </span>Upload verification file</a>');
                                    }?>
                                </div>
                                <div class="modal-footer">
                                <a href="/profile" class="btn btn-primary">Close</a>
                                </div>
                            </div>                        
                </div>
                <div class="span9">
                    <div class="row-fluid item-tag-wrapper">
                        <div class="tag-type-wrapper blue"><?php echo $user->first_name; ?> <?php echo $user->last_name; ?></div>
                    </div>
                    <div class="row-fluid item-tag-wrapper">
                        <?php printf('<div class="span4 tag-type-wrapper">SCREEN NAME</div><div class="span8 item-tag-name-wrapper">%s</div>', $user->screen_name); ?>
                    </div>            
                    <div class="row-fluid item-tag-wrapper">
                        <?php printf('<div class="span4 tag-type-wrapper">EMAIL</div><div class="span8 item-tag-name-wrapper">%s</div>', $user->email); ?>
                    </div>
                    <div class="row-fluid item-tag-wrapper">
                        <?php if(isset($user_points['ea']) && $user_points['ea'] > 0){
                            printf('<div class="span4 tag-type-wrapper">Current points</div><div class="span8 item-tag-name-wrapper">%s</div>', number_format($user_points['current']));
                            printf('<div class="row-fluid"><div class="span4"></div><div class="span8">*your %s EA points are set to expire on Sept 22, 2013</div></div>',number_format($user_points['ea']));
                        }else{
                          printf('<div class="span4 tag-type-wrapper">Current points</div><div class="span8 item-tag-name-wrapper">%s</div>', number_format($user_points['current']));
                        }?>
                    </div>
                    <div class="row-fluid item-tag-wrapper">
                        <?php printf('<div class="span4 tag-type-wrapper">Monthly contest entries</div><div class="span8 item-tag-name-wrapper">%s</div>', number_format($user_draws['current'])); ?>
                    </div>        
                    <div class="row-fluid item-tag-wrapper">
                        <?php printf('<div class="span4 tag-type-wrapper">Lifetime contest entries</div><div class="span8 item-tag-name-wrapper">%s</div>', number_format($user_draws['lifetime'])); ?>
                    </div>                      
                    <div class="row-fluid item-tag-wrapper">
                        <?php printf('<div class="span4 tag-type-wrapper">Quizzes Taken</div><div class="span8 item-tag-name-wrapper">%s</div>', number_format($quiz_number)); ?>
                    </div>                    
                    <div class="row-fluid item-tag-wrapper">
                        <?php printf('<div class="span4 tag-type-wrapper">Date joined</div><div class="span8 item-tag-name-wrapper">%s</div>', date($this->config->item('timestamp_format_without_time'), $user->created_on)); ?>
                    </div>                 
                    <div class="row-fluid item-tag-wrapper">
                        <?php
                        if ($user->verified) {
                            printf('<div class="span4 tag-type-wrapper">Status</div><div class="span8 item-tag-name-wrapper">Verified - %s</div>', date($this->config->item('timestamp_format_without_time'), $user->verified_date));
                        } else {
                            printf('<div class="span4 tag-type-wrapper">Status</div><div class="span8 item-tag-name-wrapper">Registered</div>');
                        }
                        ?>
                    </div>  
                    <div class="row-fluid item-tag-wrapper">
                        <div class="tag-type-wrapper"><a href="/profile/communication-preferences" class="view-all-link"><span class="view-all-arrows">&raquo; </span>Change Your communication preferences</a></div>
                    </div>                          
                    <div class="row-fluid item-tag-wrapper">
                        <?php
                            printf('<div class="tag-type-wrapper"><a href="/profile/refer-colleague-history" class="view-all-link"><span class="view-all-arrows">&raquo; </span>Refer A Colleague History</a></div>');
                        ?>                

                    </div>                     
                    <div class="row-fluid item-tag-wrapper last">
                        <?php
                        if (!empty($user_default_contact)) {
                            printf('<div class="span4 tag-type-wrapper">Default Mailing Address</div><div class="span8 item-tag-name-wrapper">%s, %s<br />%s, %s</div>', $user_default_contact->address_1, $user_default_contact->address_2, $user_default_contact->city, $user_default_contact->province);

                            printf('<div class="tag-type-wrapper link-profile-update"><a href="/profile-update#user-contact" class="view-all-link"><span class="view-all-arrows">&raquo; </span>Update your mailing address</a></div>');
                        } else {
                            printf('<div class="tag-type-wrapper"><a href="/profile-update?profile_edit=false#user-contact" class="view-all-link"><span class="view-all-arrows">&raquo; </span>Please add a mailing address</a></div>');
                        }
                        ?>                

                    </div>                
                </div>        
            </div>
        </div>

<?php print($activity_feed['content']) ?>
       <br /> 
<?php if(!empty($orders)){?>
        <div class="hero-unit">
            <div class="row-fluid">
                <div class="span6">
                    <h2>Orders</h2>
                </div>    
            </div>
            <div class="row-fluid orders-header">
                <div class="span1">
                    #
                </div>
                <div class="span3">
                    Date
                </div>                
                <div class="span2">
                    Order status
                </div>                     
                <div class="span3">
                    Points spent
                </div>  
                <div class="span3">
                    Fedex Tracking number
                </div>
            </div>
            
                <?php foreach($orders as $order){?>
            <div class="row-fluid orders-row">
                <div class="span1">
                    <a href="/profile/order_detail/<?php print($order->id)?>"><?php print($order->id);?></a>
                </div>                 
                <div class="span3">
                    <?php print(date('Y-m-d H:i',$order->create_timestamp));?>
                </div>                
                <div class="span2">
                    <?php print(ucfirst($order->order_status));?>
                </div> 
                <div class="span3">
                    <?php print($order->subtotal);?>
                </div>     
                <div class="span3">
                    <?php print($order->fedex_number);?>
                </div>                 
            </div>                    
                <?php }?>
                        
         </div>
<?php } ?>  
       
<?php if(!empty($certificates)){?>
        <div class="hero-unit">
            <div class="row-fluid">
                <div class="span6">
                    <h2>Certificates</h2>
                </div>    
            </div>
            <div class="row-fluid orders-header">
                <div class="span4">
                    Date Issued
                </div>                
                <div class="span4">
                    Name
                </div>                     
                <div class="span4">
                    File
                </div>  
            </div>
            
                <?php foreach($certificates as $c){?>
            <div class="row-fluid orders-row">               
                <div class="span4">
                    <?php print(date('Y-m-d H:i',$c->create_timestamp));?>
                </div>
                <div class="span4">
                    <?php print($c->certificate_name);?>
                </div>    
                <div class="span4">
                    <a href="/certificates/<?php print($c->certificate_filename);?>">Download</a>
                </div>                    
            </div>                    
                <?php }?>
                        
         </div>
<?php } ?>        
       
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
        
<?php print($refer_colleague_widget['content'])  ?>             

        <div class="hero-unit" id="labs">
            <div class="row-fluid">
                <h2 class="pull-left">Labs</h2>
                <div class="pull-right">
                    <a class="view-all-link" href="http://<?php echo $sMainDomain; ?>/training-labs">
                        <span class="view-all-arrows">» </span>See All</a>
                </div>          
            </div>
                <?php 
                if(!empty($sites)){
//                  echo '<script type="text/javascript">jQuery(document).ready(function() { console.log("trainings: '.$trainings.'"); });</script>';
//                  echo '<script type="text/javascript">jQuery(document).ready(function() { console.log("targets: '.$targets.'"); });</script>';

                    foreach($sites as $site){
                        if ($site->id ==1) continue;
                        if(!empty($site->image)){
                            echo '<div class="row-fluid lab">'; 
                            printf('<a href="http://%s">%s<span>%s</span></a>',$site->domain, $site->image->thumb_html, $site->name);
                            echo '</div>';
                        }else{
                            echo '<div class="row-fluid lab">'; 
                            printf('<a href="http://%s"><span>%s</span></a>',$site->domain, $site->name);
                            echo '</div>';                            
                        }
                    }
                    
                }else{
                    printf('No sites yet.');
                }
                ?>
        </div>       
        
       <?php print $retailers_info['content']; ?>        
        
       <?php print $retailers_reports; ?>    

<!--
        <div class="hero-unit" id="labs">
            <div class="row-fluid">
                <h2 class="pull-left">User's Labs</h2>
                <?php if(!empty($sites)){?>
                <div class="pull-right">
                    <a class="view-all-link" href="http://<?php echo $sMainDomain; ?>/training-labs">
                        <span class="view-all-arrows">» </span>See All</a>
                </div>
                <?php } ?>
            </div>

            
        </div>        
        -->
        <div class="hero-unit" id="verifications">
            <div class="row-fluid">
                <h2 class="pull-left">User Verification</h2>
                <div class="pull-right">
                       <a class="view-all-link" href='#verification-upload' data-toggle="modal">
                        <span class="view-all-arrows">» </span>Upload</a>
                </div> 
                <!--verification upload modal-->
                <div class="modal hide fade" id="verification-upload" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-header">

                        <h3>Verification upload</h3>
                    </div>
                    <div class="modal-body">
                        <form action="/verification/upload" method="POST" enctype="multipart/form-data" id="verification-upload-form">
                            <div class="row-fluid">
                                <div class="row-fluid">
                                   Upload your paystub and for best result use jpg, jpeg, png or gif. <br />
                                   For more information about Verification process visit: <a target="_blank" href="http://www.cheddarlabs.com/terms-of-use">http://www.cheddarlabs.com/terms-of-use</a>
                                </div>
                            </div>
                            <div class="row-fluid">
                            <label for="email">File: <span class="red">*</span></label>
                            </div>
                            <div class="row-fluid">
                              <input type="file" name="verification" id="verification" size="20" required class="required verificationfile" />
                            </div>
                            <div class="row-fluid">
                            <input class="btn btn-primary" type="submit" name="submit" value="Upload" />
                            </div>                            
                        </form>
                    </div>
                    <div class="modal-footer">
                    <a href="/profile" class="btn btn-primary">Close</a>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <?php
                if(!empty($verifications)){
                    foreach($verifications as $verification){
                      print('<div class="row-fluid">');
                      //printf('%s',$verification->status);
                      if($verification->asset_id > 0){
                        printf ('<div class="span4"><a href="#verification-modal-%s" data-id="%s" data-toggle="modal" class="verification-image">%s</a></div>',$verification->id, $verification->asset_id,$verification->retailer_name);
                      }else{
                        printf ('<div class="span4">%s</div>',$verification->retailer_name);  
                      }
                      printf('<div class="span3">%s</div>',  ucfirst($verification->status));
                      if($verification->status == 'pending'){
                          $delete_link = sprintf('<a onClick="confirmDelete()" href="/verification/delete/%s"><img style="max-width: 97%%;" height="22" width="22" src="/themes/cheddarLabs/images/icons/remove.jpg" alt="remove"/></a>',$verification->id);
                          printf('<div class="span4">%s</div><div class="span1">%s</div>',date($this->config->item('timestamp_format_without_time'),$verification->create_timestamp), $delete_link);
                      }else{
                          printf('<div class="span5">%s</div>',date($this->config->item('timestamp_format_without_time'),$verification->create_timestamp));
                      }
                      //var_dump($verification);  
                      print('</div>');
                    }
                }
                ?>
            </div> 
        </div>
        
<?php //print($leaderboard_widget['content'])  ?>

    </div>    

</div> <!-- /container -->

    <?php
    if(!empty($verifications)){
    foreach($verifications as $verification){
        if($verification->asset_id > 0){
        ?>
    <!-- //modal windows for verification image -->
    <div id="verification-modal-<?php echo $verification->id?>" class="modal hide fade in" style="display: none; ">
      <div class="modal-body">
          <!-- Carousel items -->
            <?php
            printf('<img src="http://%s/asset/upload/verifications/%s.%s" atl="verification img"/>',$sMainDomain, str_replace(' ','_',$verification->image->file_name) ,$verification->image->extension);
          ?>
        </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Close</a>
      </div>
    </div>
                <?php }
    }
    }?>
