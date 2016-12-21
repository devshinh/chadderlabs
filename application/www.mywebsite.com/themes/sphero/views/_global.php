<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->session->userdata('langCode') ?>" lang="<?php echo $this->session->userdata('langCode') ?>">
  <!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
  <!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
  <!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
  <!--[if gt IE 8]> <html class="no-js" lang="en"> <![endif]-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php 
if ($oPage->url == 'home') {
  echo htmlspecialchars($sSiteName) . ' | ' . htmlspecialchars($oPage->meta_title);
} elseif ($oPage->url == null) {
    echo ('404 Page not Found | ' . htmlspecialchars($sSiteName));
}else {
  echo (!empty($oPage->meta_subtitle) ? $oPage->meta_subtitle : '') . htmlspecialchars($oPage->meta_title) . ' | ' . htmlspecialchars($sSiteName);
}
?></title>
    <meta http-equiv="Content-Language" content="en" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="description" content="<?php echo htmlspecialchars($oPage->meta_description) ?>" />
    <meta name="keywords" content="<?php echo htmlentities($oPage->meta_keyword) ?>" />
    <meta name="author" content="Hot Tomali Communications Inc." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . '/'; ?>" />
    <link rel="shortcut icon" href="/themes/<?php echo $sTheme; ?>/images/favicon-sphero.ico" type="image/x-icon" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600|Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" media="all" href="/themes/<?php echo $sTheme; ?>/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" media="all" href="/themes/<?php echo $sTheme; ?>/bootstrap/css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" media="all" href="/themes/<?php echo $sTheme; ?>/css/trainingFront.css?t=<?php echo time(); ?>" />
    <link rel="stylesheet" type="text/css" media="all" href="/themes/<?php echo $sTheme; ?>/css/jquery/ui-lightness/jquery-ui-1.8.16.custom.css" media="screen" />
    <link rel="stylesheet" type="text/css" media="all" href="/themes/<?php echo $sTheme; ?>/css/jquery/liquidcarousel.css" />
    <link rel="stylesheet" type="text/css" media="all" href="/themes/<?php echo $sTheme; ?>/css/jquery/jquery.countdown.css" />

    <?php
    if (!empty($oPage->style_sheets)) {
      foreach ($oPage->style_sheets as $file) {
        ?>
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo $file; ?>" />
        <?php
      }
    }
    ?>
    <?php
    if (!empty($oPage->style_sheet) && empty($oModule->style_sheet)) {
      foreach (explode(' ', $oPage->style_sheet) as $file) {
        ?>
        <link rel="stylesheet" type="text/css" media="all" href="/themes/<?php echo $sTheme; ?>/css/page/<?php echo $file; ?>?D" />
        <?php
      }
    }
    if (!empty($oModule->style_sheet)) {
      foreach (explode(' ', $oModule->style_sheet) as $file) {
        ?>
        <link rel="stylesheet" type="text/css" media="all" href="/modules/<?php echo $oModule->name; ?>/css/<?php echo $file; ?>?D" />
        <?php
      }
    }
    ?>

    <!--[if IE 8]><link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/ie8.css"><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/ie7.css"><![endif]-->
    <meta name="google-site-verification" content="" />
    <?php
// Google Analytics code
    if (!empty($aAnalytics['google'])) {
      ?>
      <?php //echo $aAnalytics['google']->sCode    ?>
      <?php
    }
    $user_id = $this->session->userdata('user_id');
    if(!empty($userpoints)) {
        $user_points = $userpoints;
    }else{
        $user_points = 0;
    }
    if(!empty($userdraws)) {
        $user_draws = $userdraws;
    }else{
        $user_draws = 0;
    }    
    //$user_info = $this->session->userdata('user_info');
    $this->load->model("account/account_model");
    $user_info = $this->account_model->get_info($this->session->userdata("user_id"));    
    ?>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery.validate.min.js"></script>
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery-ui-1.8.16.custom.min.js"></script>

   <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-36727939-1']);
      _gaq.push(['_trackPageview']);
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        //ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
<?php
    $bg_array = array(
       'BG-sphero-1-w1.jpg',
       'BG-sphero-2-w1.jpg',
       'BG-sphero-3-w1.jpg',
       'BG-sphero-4-w1.jpg'
    );

    shuffle($bg_array);
    if ($oPage->url == 'page-not-found' || $oPage->url == null) {?>
    <style type="text/css">
      html{
        background-color: #e8e8e8;
      }
    </style>        
    <?php }else{ ?>
    <style type="text/css">
      html{
        background: url('/themes/<?php echo $sTheme; ?>/images/background/<?php echo $bg_array[0]; ?>') no-repeat center 5% fixed #e8e8e8;
      }
    </style>
    <?php }?>
  </head>
  <!--add class out-of-points to disable lab -->
  
  <?php if(get_realtime_balance($sSiteID)< -25000 && (strpos($_SERVER['PATH_INFO'], '/labs/product') !== FALSE)){?>
  <body class="out-of-points">
  <?php }else{ ?>
      <body>    
  <?php } ?>
    <div class="navbar-fixed-top" >
      <div class="navbar navbar-inverse" id="navbarUser">
        <div class="navbar-inner">            
          <div class="container" >
          <div id="global-cheddar">
            <a href="http://<?php echo $sMainDomain;?>"><img src="/themes/cheddarLabs/images/logo-cheddar-labs-w1.png" alt="cheddarLab logo" title="cheddarLab Global menu"/></a>
          </div>        
              <div class="pull-right hidden-mobile">
              <?php if (isset($sGlobalMenu) && $sGlobalMenu > '') { ?>
                <?php print($sGlobalMenu); ?>              
              <?php } ?>    
                <ul class="nav" id="main-nav-user">
                  <?php if (!empty($user_id)) { ?>
                    <li>
                      <div id="user-info-wrapper">
                        <a <?php printf('href="http://%s/profile/">%s', $sMainDomain, $user_info->screen_name);?></a><br />
                        <?php echo number_format($user_draws,0) ?> draws | <?php echo number_format($user_points,0) ?> points
                      </div>
                      <div id="user-menu-wrapper">
                        <div class="btn-group" id="button-avatar">
                          <button class="btn dropdown-toggle" data-toggle="dropdown">
                            <?php if(!empty($user_info->avatar_id)){
                                $avatar = asset_load_item($user_info->avatar_id);                            
                                printf('<img height="15px" src="/asset/upload/thumbnail_30x30/%s_thumb.%s"/>',$avatar->name,$avatar->extension);
                            }else{ ?>
                              <img height="15px" src="/asset/upload/thumbnail_30x30/icon-user_thumb.jpg"/>
                            <?php } ?>
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" id="dropdown-menu-user">
                            <li><a href="http://<?php echo $sMainDomain?>/profile">Profile</a></li>
                            <li><a href="/logout">Log Out</a></li>
                          </ul>

                        </div>
                      </div>
                    </li>
                  <?php } else { ?>
                    <li><div id="user-info-wrapper" class="logout">
                        Welcome to Cheddar Labs
                        <div id="user-controls">
                          <a href="/login">Log In</a> or <a href="http://www.cheddarlabs.com/signup?ref=<?php echo $sSiteID?>">Sign Up</a>
                        </div>
                      </div></li>
                  <?php } ?>
                </ul>                 
            </div>
            <?php if (isset($sGlobalMenu) && $sGlobalMenu > '') { ?>
                <?php print($sMobileGlobal); ?>              
              <?php } ?>
          </div>
        </div>
      </div>
      <div class="navbar">
        <div class="navbar-inner">
          <div class="container">
            <span class="visible-mobile pull-left"><button id="main_menu_button" class="pull-right btn btn-clear" type="button" onclick="toggleVerticalMenu(-2)"><img src="/themes/<?php echo $sTheme; ?>/images/icons/btn-d3p-menu.png" alt="Menu"></button></span>
            <?php if (!empty($user_id)) { ?>
              <a class="pull-left" id="main-logo" href="/home"><img src="/themes/<?php echo $sTheme; ?>/images/SpheroLogo.png" alt="Sphero" title="Sphero" /></a>
            <?php } else { ?>
              <a class="pull-left" id="main-logo" href="/"><img src="/themes/<?php echo $sTheme; ?>/images/SpheroLogo.png" alt="Sphero" title="Sphero"  /></a>
            <?php } ?>
            <div class="pull-right hidden-mobile">
              <?php print $sMainMenu; ?>
            </div><!--/.nav-collapse -->
          </div>
        </div>
      </div>
    <div class="hidden-mobile navbar navbar-fixed-top<?php echo(empty($sSubMenu) ? ' empty' : ' ') ?>" id="secondary-nav-wrapper">
      <div class="container">
        <div class="pull-right">
          <?php if (!empty($sSubMenu)) print $sSubMenu; ?>
        </div>
      </div>
    </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
      <div class="content">

        <div class="wrapper">
          <div class="proper-content">
<?php
if ($oPage->url == 'page-not-found' || $oPage->url == null) {
    $texts = array(
        array('lyrics'=>"See it's all about the <i>cheddar</i><br />nobody do it better",'artist'=>'Notorious B.I.G.','song' =>'Going Back to Cali'),
        array('lyrics'=>"Touch my <i>cheddar</i><br />feel my Beretta",'artist'=>'Notorious B.I.G.','song' =>'Warning'),
        array('lyrics'=>"Sooner then later, I know the <i>cheddar</i> gon' come<br />for now I write the world letters to better the young",'artist'=>'Common','song' =>'Forever Begins'),
        array('lyrics'=>"When you very large<br /> never spend <i>cheddar</i> you charge",'artist'=>'Mase','song' =>'Love U So'),
        array('lyrics'=>"My uptown Nikes hold caps and <i>cheddar</i><br />my waistline hold a 4-pound Baretta",'artist'=>'Outkast','song' =>'Burst'),
        array('lyrics'=>"Ad Rock a.k.a sharp <i>cheddar</i><br /> my rhymes are better",'artist'=>'Beastie Boys','song' =>'Triple Trouble'),
        array('lyrics'=>'Horse & Carriage to spend like Mason Betha<br /> chasing this <i>cheddar</i>','artist'=>'Jay-Z','song' =>'Diamond is Forever'),
        array('lyrics'=>"Jack cheddar from the make believe<br />break the trees on they eighth CD",'artist'=>'Joey BADA$$','song' =>'Unorthodox'),
        array('lyrics'=>'Now we stack <i>cheddar</i> galore<br /> when we shop and buy at the store','artist'=>'Busta Rhymes','song' =>'Show Me What You Got'),
        array('lyrics'=>"Big Pun the caputera with <i>cheddar</i><br />never settle for second best cause I'm primera",'artist'=>'Big Pun','song' =>'Top of the World (Remix)'),
        array('lyrics'=>"What's up? You all fed up cause I got a little cheddar<br />and my record's movin' out the store? ",'artist'=>'Jay-Z','song' =>"Heart Of The City (Ain't No Love)"),
        array('lyrics'=>"I'm growing my <i>cheddar</i>, my champagne warm<br />I had to struggle to make it, I die for my charm",'artist'=>'Rick Ross','song' =>"B!@#$, Don't Kill My Vibe (Remix)"),
        array('lyrics'=>"<i>Cheddar</i> on Federer, my champagne warm<br />ball 'till I fall, et cetera, et cetera",'artist'=>'Big Sean','song' =>"24k of Gold"),        
        array('lyrics'=>"And learn how to earn better<br />I burn <i>cheddar</i>",'artist'=>'Jay-Z','song' =>"All I Need"),  
        array('lyrics'=>"They told me cheese at the camps and they made me <i>cheddar</i><br />and the green only made me better",'artist'=>'A$AP Rocky','song' =>"BET Cypher 2012: Man With the Iron Fists"),  
        array('lyrics'=>"Lactose tolerant, addicted to <i>cheddar</i><br />and I spent it on a jacket man I don't know no better",'artist'=>'Childish Gambino','song' =>"Hero"),          
        array('lyrics'=>"Go getter, with no <i>cheddar</i><br />just a white tee and a swap meet sweater",'artist'=>'Kendrick Lamar','song' =>"Determined"),                  
        array('lyrics'=>"Don’t end up a dead man for the <i>cheddar</i><br />the way to play is Joey in Def Jam Vendetta",'artist'=>'Joe Budden','song' =>"Pump It Up (Remix)"),         
        array('lyrics'=>"Whatever rappers getting <i>cheddar</i><br />I'm the king, and they Coretta",'artist'=>'Lil Dicky','song' =>"Sky Hooks"),         
        array('lyrics'=>"If you really wanna get it, forget it, genetically better<br />then every pathetic competitor nettin' <i>cheddar</i>",'artist'=>'Lil Dicky','song' =>"The Cypher"),
        array('lyrics'=>"Get up like <i>cheddar</i> and get it together<br />get it now or never so you can hold it forever",'artist'=>'Hodgy Beats','song' =>"Black Magic"),
    );
        
    shuffle($texts);

    ?>
               <!-- two 100% content boxes -->
              <div class="container-fluid" id="mainContent">
                <div class="row-fluid">
                  <div class="span12">
                      <p>The requested URL was not found on our server. Go to the <a href="http://www.cheddarlabs.com">CHEDDAR LABS HOMEPAGE</a></p>
                      <div id="bubble404-wrapper">
                        <div id="bubble404">

                        </div>
                          
                            <div id="lyrics-wrapper">
                              <?php print $texts[0]['lyrics']; ?>
                             </div>
                      </div>
                          <div id="artist-wrapper">
                              <div style="padding-bottom:0px;"><b><?php print $texts[0]['artist']; ?></b></div>
                              <i><?php print $texts[0]['song']; ?></i>
                          </div>                      
                      <div class="clearfix"></div>
                      
                      <p>Sorry but the page you are looking for has not been found. Try checking the URL for errors, then hit the refresh button on your browser. Or just hit the refresh button for another Cheddar Lyric!</p>
                      <p><strong>Alternatively you can use the options below to navigate the site.</strong></p>
                      
                      <div id="menu404-wrapper">
                        <?php print $s404Menu; ?>
                        <?php print $s404MenuFooter; ?>
                      </div>
                  </div><!--/span-->
                </div><!--/row-->
              </div><!--/.fluid-container-->
<?php }else{         
                          
           if (empty($oPage->layout_id)) { ?>
              <!--two column layout-->
              <div class="container-fluid" id="mainContent">
                <div class="row-fluid">
                  <div class="span8">
                    <?php
                    $module_view_used = false;
                    //left_zone in template
                    if (empty($oPage->sections)) {
                      if (isset($oModule))
                        print($oModule->view);
                      $module_view_used = true;
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'left_zone') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            //var_dump($section->content_widget);
                            if (isset($section->content_widget))
                              print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->
                  <div class="span4">
                    <?php
                    //right_zone in template
                    if (empty($oPage->sections)) {
                      if (!$module_view_used && !empty($oModule))
                        print($oModule->view);
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'right_zone') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->
                </div><!--/row-->
              </div>
            <?php } elseif ($oPage->layout_id == 15) { ?>
              <div class="container-fluid" id="mainContent">
                <div class="row-fluid">
                  <div class="span8">
                    <?php
                    $module_view_used = false;
                    //left_zone in template
                    if (empty($oPage->sections)) {
                      if (isset($oModule))
                        print($oModule->view);
                      $module_view_used = true;
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'upper_left') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            //var_dump($section->content_widget);
                            if (isset($section->content_widget))
                              print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->

                  <div class="span4">
                    <?php
                    foreach ($oPage->sections as $section) {
                      if ($section->zone == 'upper_right') {
                        if (empty($section->module_widget)) {
                          print $section->content;
                        } else {
                          print $section->content_widget;
                        }
                      };
                    }
                    ?>
                  </div>
                </div> <!--/.row fluid-->
                <div class="row-fluid">
                  <div class="span12">
                    <?php
                    foreach ($oPage->sections as $section) {
                      if ($section->zone == 'middle_zone') {
                        if (empty($section->module_widget)) {
                          print $section->content;
                        } else {
                          print $section->content_widget;
                        }
                      };
                    }
                    ?>
                  </div><!--/span-->
                </div> <!--/.row fluid-->
                <div class="row-fluid">
                  <div class="span8">
                    <?php
                    $module_view_used = false;
                    //left_zone in template
                    if (empty($oPage->sections)) {
                      if (isset($oModule))
                        print($oModule->view);
                      $module_view_used = true;
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'lower_left') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            //var_dump($section->content_widget);
                            if (isset($section->content_widget))
                              print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->

                  <div class="span4">
                    <?php
                    foreach ($oPage->sections as $section) {
                      if ($section->zone == 'lower_right') {
                        if (empty($section->module_widget)) {
                          print $section->content;
                        } else {
                          print $section->content_widget;
                        }
                      };
                    }
                    ?>
                  </div>
                </div> <!--/.row fluid-->
              </div><!--/.fluid-container-->
            <?php } elseif ($oPage->layout_id == 16) { ?>
              <!--two column layout left side bigger-->
              <div class="container-fluid" id="mainContent">
                <div class="row-fluid">
                  <div class="span8">
                    <?php
                    $module_view_used = false;
                    //left_zone in template
                    if (empty($oPage->sections)) {
                      if (isset($oModule))
                        print($oModule->view);
                      $module_view_used = true;
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'left_zone') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            //var_dump($section->content_widget);
                            if (isset($section->content_widget))
                              print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->
                  <div class="span4">
                    <?php
                    //right_zone in template
                    if (empty($oPage->sections)) {
                      if (!$module_view_used && !empty($oModule))
                        print($oModule->view);
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'right_zone') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->
                </div><!--/row-->
              </div><!--/.fluid-container-->
            <?php } elseif ($oPage->layout_id == 17) { ?>
              <!--two column layout left side smaller-->
              <div class="container-fluid" id="mainContent">
                <div class="row-fluid">
                  <div class="span4">
                    <?php
                    $module_view_used = false;
                    //left_zone in template
                    if (empty($oPage->sections)) {
                      if (isset($oModule))
                        print($oModule->view);
                      $module_view_used = true;
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'left_zone') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            //var_dump($section->content_widget);
                            if (isset($section->content_widget))
                              print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->
                  <div class="span8">
                    <?php
                    //right_zone in template
                    if (empty($oPage->sections)) {
                      if (!$module_view_used && !empty($oModule))
                        print($oModule->view);
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'right_zone') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->
                </div><!--/row-->
              </div><!--/.fluid-container-->
            <?php }elseif($oPage->layout_id == 18){ ?>
              <!--two column layout left side bigger
              and full width content bellow
              -->
              <div class="container-fluid" id="mainContent">
                <div class="row-fluid">
                  <div class="span8">
                    <div class="hero-unit">
                      <?php
                      $module_view_used = false;
                      //left_zone in template
                      if (empty($oPage->sections)) {
                        if (isset($oModule))
                          print($oModule->view);
                        $module_view_used = true;
                      } else {
                        foreach ($oPage->sections as $section) {
                          if ($section->zone == 'left_zone') {
                            if (empty($section->module_widget)) {
                              print $section->content;
                            } else {
                              //var_dump($section->content_widget);
                              if (isset($section->content_widget))
                                print $section->content_widget;
                            }
                          };
                        }
                      }
                      ?>
                    </div>
                  </div><!--/span-->
                  <div class="span4">
                    <?php
                    //right_zone in template
                    if (empty($oPage->sections)) {
                      if (!$module_view_used && !empty($oModule))
                        print($oModule->view);
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'right_zone') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->
                </div><!--/row-->
                <div class="row-fluid">
                  <div class="span12">
                    <?php
                    //right_zone in template
                    if (empty($oPage->sections)) {
                      if (!$module_view_used && !empty($oModule))
                        print($oModule->view);
                    } else {
                      foreach ($oPage->sections as $section) {
                        if ($section->zone == 'bottom_zone') {
                          if (empty($section->module_widget)) {
                            print $section->content;
                          } else {
                            print $section->content_widget;
                          }
                        };
                      }
                    }
                    ?>
                  </div><!--/span-->
                </div><!--/row-->
              </div><!--/.fluid-container-->
  <?php } ?>
<?php } ?>


          </div><!-- /.proper-content -->

          <div class="push"></div>

        </div><!-- /.wrapper -->
        <div class="footer-wrapper">
          <footer>
            <div id="footerBorder"></div>
            <div class="navbar" id="footerMenu">

              <div class="container">
                <div class="pull-right">
                  <div id="socialButtons">
                      <a target="_blank" href="http://www.facebook.com/cheddarlabs" id="fbButton" class="socialButton"></a>
                      <a target="_blank" href="http://www.twitter.com/cheddarlabs" id="twitterButton" class="socialButton"></a>
                  </div>
                  <div id="footerLogo"></div>
                </div>
                <div class="nav">
                  <?php print $sFooterMenu; ?>
                  <p class="navbar-text pull-left">
                    &copy;<?php echo date('Y')?> <a target="_blank" href="http://www.hottomali.com" style="text-transform: none;color:#4D4D4D; font-size:10px">Hot Tomali Communications</a>, Inc. Trademarks belong to their respective owners. All rights reserved.
                  </p>
                </div>
              </div>
            </div>
          </footer>
        </div> <!-- /.footer-wrapper -->
      </div><!-- /.content -->
    </div>   <!-- /.container -->
    <div class="vertical-menu visible-mobile hidden">
      <div class="menu-header">
          <?php if (empty($user_id)) { ?>
        Welcome to CheddarLabs
        <a href="/login">Log In</a> or <a href="/signup">Sign Up</a>
          <?php } else { ?>
        <div class="media">
          <div class="pull-left">
            <?php if(isset($avatar)){                     
                printf('<img class="media-object" height="15px" src="/asset/upload/thumbnail_30x30/%s_thumb.%s"/>',$avatar->name,$avatar->extension);
            } else { ?>
              <img class="media-object" height="15px" src="/asset/upload/thumbnail_30x30/icon-user_thumb.jpg"/>
            <?php } ?>
          </div>
          <div class="media-body">
            <a class="screenname" href="/profile"><?php print $user_info->screen_name ?></a><br />
            <span class="points"><?php echo number_format($user_points,0) ?> points</span>
            <br />
            <a <?php printf('href="http://%s/profile">%s', $sMainDomain, $user_info->screen_name);?></a>
          </div>
        </div>
          <?php } ?>
      </div>
      <div class="menu-items">
        <ul class="unstyled">
          <?=$sMobileMenu?>
          <?php if (empty($user_id)) { ?>
          <li data-parentid="-1"><a href="/login">Log In</a></li>
          <li data-parentid="-1"><a href="/signup">Sign Up</a></li>
          <?php } else { ?>
          <li data-parentid="-1"><a href="http://<?php echo $sMainDomain?>/profile">Profile</a></li>
          <li data-parentid="-1"><a href="/logout">Log Out</a></li>
          <?php } ?>
        </ul>
      </div>
    </div>
    <?php if (isset($sGlobalMenu) && $sGlobalMenu > '') { ?>
      <?php print($sGlobalMobile); ?>              
    <?php } ?>
 
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/bootstrap/js/bootstrap.min.js"></script>   
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/reflection.js"></script>
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/global.js?t=<?php echo time(); ?>"></script>
    <script type="text/javascript" src="http://cdn.sublimevideo.net/js/djr103cr-beta.js"></script>
    <!--<script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery.cycle2.min.js"> </script>-->
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery.plugin.min.js"> </script>
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery.countdown.min.js"> </script>
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery.maskedinput.min.js"> </script>
    <?php
    if (!empty($oPage->javascripts)) {
      foreach ($oPage->javascripts as $file) {
        ?>
        <script type="text/javascript" src="<?php echo $file; ?>?t=<?php echo time(); ?>"></script>
        <?php
      }
    }
    if (!empty($oModule->javascript)) {
      foreach (explode(' ', $oModule->javascript) as $file) {
        ?>
        <script type="text/javascript" src="/modules/<?php echo $oModule->name; ?>/js/<?php echo $file; ?>?t=<?php echo time(); ?>"></script>
        <?php
      }
    }
    ?>
    <script type="text/javascript">
      var stayAlive = 0;
      var stayFocus = 0;
      var startFocus = 0;
      var lastInteraction = 0;
      var lastAlive = 0;

      function keepAlive() {
        var now = new Date();
        var diffInSeconds = Math.ceil((now.getTime() - lastAlive.getTime()) / 1000);
        jQuery.ajax({
          type: "GET",
          url: "/ajax/account/session/<?=$oPage->keys?>/"+diffInSeconds,
          success: function(data) {
            lastAlive = now;
            console.log("alive "+data);
          }
        });
      }
      function checkFocus() {
        if (startFocus instanceof Date) {
          var curr_time = new Date();                                                                               
          if((curr_time.getTime() - lastInteraction.getTime()) > (5 * 60 * 1000)) {
              //No interaction in this tab for last 5 minutes. Probably idle.                                               
              windowUnfocused();
          }
        }
      }
      function windowFocused(eo) {
        lastAlive = lastInteraction = new Date();
        if (startFocus <= 0) {
          startFocus = new Date();
          if (stayAlive <= 0) {
            stayAlive = window.setInterval(keepAlive, 15 * 1000);
          }
          if (stayFocus <= 0) {
            stayFocus = window.setInterval(checkFocus, 10 * 60 * 1000);
          }
        }
      }
      function windowUnfocused(eo) {
          if (startFocus > 0) {
            startFocus = 0;
            if (stayFocus > 0) {
              window.clearInterval(stayFocus);
              stayFocus = 0;
            }
            if (stayAlive > 0) {
              keepAlive();
              window.clearInterval(stayAlive);
              stayAlive = 0;
            }
          }
      }

      jQuery(window).load(function() {
        lastAlive = new Date();
        stayAlive = window.setInterval(keepAlive, 15 * 1000);
        stayFocus = window.setInterval(checkFocus, 10 * 60 * 1000);
        jQuery(window).on("blur focus", function(eo) {
          var prevType = jQuery(this).data("prevType");
          jQuery(this).data("prevType", eo.type);
          if (prevType != eo.type) {
            switch(eo.type) {
              case "blur":
                windowUnfocused();
                break;
              case "focus":
                windowFocused();
                break;
            }
          }
        });
        jQuery(window).on('beforeunload', windowUnfocused);
        });
    </script>
    <!-- bugHerd javascript -->
    <script type='text/javascript'>
      (function (d, t) {
        var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
        bh.type = 'text/javascript';
        bh.src = '//www.bugherd.com/sidebarv2.js?apikey=bwhpqaoh3x2rfh8enabh6a';
        s.parentNode.insertBefore(bh, s);
      })(document, 'script');
    </script>

  </body>
</html>
