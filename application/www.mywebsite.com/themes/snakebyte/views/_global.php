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
} else {
  echo (!empty($oPage->meta_subtitle) ? $oPage->meta_subtitle : '') . htmlspecialchars($oPage->meta_title) . ' | ' . htmlspecialchars($sSiteName);
}
?></title>
    <meta http-equiv="Content-Language" content="en" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="description" content="<?php echo htmlspecialchars($oPage->meta_description) ?>" />
    <meta name="keywords" content="<?php echo htmlentities($oPage->meta_keyword) ?>" />
    <meta name="author" content="Hot Tomali Communications Inc." />
    <meta name="viewport" content="width=1000px, initial-scale=0.35">
    <base href="<?php echo (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . '/'; ?>" />
<!--    <link rel="shortcut icon" href="/themes/<?php echo $sTheme; ?>/images/favicon.ico" />-->
    <link rel="shortcut icon" href="http://www.snakebyte-unu.com/fileadmin/unu_tmpl/favicon.ico" type="image/x-ico; charset=binary">
    <link rel="icon" href="http://www.snakebyte-unu.com/fileadmin/unu_tmpl/favicon.ico" type="image/x-ico; charset=binary">    
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600|Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" media="all" href="/themes/<?php echo $sTheme; ?>/bootstrap/css/bootstrap.css" />
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
    $bg_array = array(

    );
    shuffle($bg_array);
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
    $username = $this->session->userdata('username');
    if(!empty($userpoints)) {
        $user_points = $userpoints;
    }else{
        $user_points = 0;
    }
    $user_info = $this->session->userdata('user_info');
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
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
  </head>
  <body>
    <div class="navbar-fixed-top" >
      <div class="navbar navbar-inverse" id="navbarUser">
        <div class="navbar-inner">            
          <div class="container-fluid" >
          <div id="global-cheddar">
            <a href="http://<?php echo $sMainDomain;?>"><img src="/themes/cheddarLabs/images/logo-cheddar-labs-w1.png" alt="cheddarLab logo" title="cheddarLab Global menu"/></a>
          </div>        
              <div class="pull-right">
              <?php if (isset($sGlobalMenu) && $sGlobalMenu > '') { ?>
                <?php print($sGlobalMenu); ?>              
              <?php } ?>    
                <ul class="nav" id="main-nav-user">
                  <?php if (!empty($user_id)) { ?>
                    <li>
                      <div id="user-info-wrapper">
                        <a href="/profile"><?php print $username ?></a><br />
                        <?php echo number_format($user_info->draws,0) ?> draws | <?php echo number_format($user_points,0) ?> points
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
                            <li><a href="/profile">Profile</a></li>
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
          </div>
        </div>
      </div>
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <div class="container-fluid">
            <?php if (!empty($user_id)) { ?>
              <a id="main-logo" href="/home"><img src="/themes/<?php echo $sTheme; ?>/images/logo-unu.png" alt="UNU" title="UNU" /></a>
            <?php } else { ?>
              <a id="main-logo" href="/"><img src="/themes/<?php echo $sTheme; ?>/images/logo-unu.png" alt="UNU" title="UNU"  /></a>
            <?php } ?>
            <div class="pull-right">
              <?php print $sMainMenu; ?>
            </div><!--/.nav-collapse -->
          </div>
        </div>
      </div>
    <div class="navbar navbar-fixed-top <?php echo(empty($sSubMenu) ? 'empty' : '') ?>" id="secondary-nav-wrapper">
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
            <?php if (empty($oPage->layout_id)) { ?>
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
            <?php } elseif ($oPage->layout_id == 31) { ?>
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
            <?php } elseif ($oPage->layout_id == 32) { ?>
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
            <?php } elseif ($oPage->layout_id == 33) { ?>
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
    
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/bootstrap/js/bootstrap.min.js"></script>   
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/reflection.js"></script>
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/global.js?t=<?php echo time(); ?>"></script>
    <script type="text/javascript" src="http://cdn.sublimevideo.net/js/djr103cr-beta.js"></script>
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery.cycle.all.latest.min.js"> </script>
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
