<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->session->userdata('langCode') ?>" lang="<?php echo $this->session->userdata('langCode') ?>">

  <!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
  <!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
  <!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
  <!--[if gt IE 8]> <html class="no-js" lang="en"> <![endif]-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php
//  echo '<pre>';
// var_dump($oPage);
if ($oPage->url == 'home') {
  echo htmlspecialchars($oPage->meta_title) . ' | ' . htmlspecialchars($sSiteName);
} else {
  echo (!empty($oPage->meta_subtitle) ? $oPage->meta_subtitle : htmlspecialchars($oPage->meta_title)) . ' | ' . htmlspecialchars($sSiteName);
}
?></title>
    <meta name="description" content="<?php echo htmlspecialchars($oPage->meta_description) ?>" />
    <meta name="keywords" content="<?php echo htmlentities($oPage->meta_keyword) ?>" />
    <meta name="author" content="Hot Tomali Communications Inc." />
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="themes/bwinsurance/images/bw-favicon.ico" />
    <meta name="viewport" content="width=device-width,initial-scale=1">
      <base href="<?php echo (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . '/'; ?>" />
      <link rel="shortcut icon" href="/favicon.ico" />

      <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/global.css?t=<?php echo time(); ?>" />
      <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,600,700|Merriweather:300' rel='stylesheet' type='text/css'>
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
            <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/page/<?php echo $file; ?>?t=<?php echo time(); ?>" />
            <?php
          }
        }
        ?>
        <!--[if IE 7]>
          <link type="text/css" rel="stylesheet" media="screen" href="themes/<?php echo $sTheme; ?>/css/ie7.css/" />
          <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/ie7.js"></script>
        <![endif]-->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <script type="text/javascript">
          var oGlobal = { page : { name : "<?php echo $oPage->name ?>" } };
        </script>

        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
        <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery.validate.min.js"></script>
        <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery.maskedinput-1.3.min.js"></script>
        <!--[if lt IE 7]><script defer="defer" type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.png.js"></script><![endif]-->
        <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/global.js?t=<?php echo time(); ?>"></script>
        <?php
        if (!empty($oPage->javascripts)) {
          foreach ($oPage->javascripts as $file) {
            ?>
            <script type="text/javascript" src="<?php echo $file; ?>"></script>
            <?php
          }
        }
        ?>
        <?php
        if (!empty($oPage->javascript) && empty($oModule->javascript)) {
          foreach (explode(' ', $oPage->javascript) as $file) {
            ?>
            <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/page/<?php echo $file; ?>"></script>
            <?php
          }
        }
        ?>
<!--[if IE 8]><link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/ie8.css"><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/ie7.css"><![endif]-->
        <link rel="stylesheet" type="text/css" media="print" href="themes/<?php echo $sTheme; ?>/css/print.css" />
        <meta name="google-site-verification" content="x--WDmQ3Hj9fk6vXV4BAZvcf41br7H-aRzjDszvfYLs" />

        <script type="text/javascript">

          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-35894023-1']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

        </script>
        </head>
        <body>
          <div id="fb-root"></div>
          <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=168497083271505";
            fjs.parentNode.insertBefore(js, fjs);
          }(document, 'script', 'facebook-jssdk'));</script>            

          <div id="head-wrapper">
            <div id="header">
              <div id="website-logo-wrapper">
                <a href="/">
                  <div id="website-logo"><span><?php echo htmlspecialchars($sSiteName); ?></span></div>
                </a>
              </div>
              <div id="location-nav">
                <div id="header_text">Our locations:</div>
                <?php echo widget::run('location/location_list_widget', array()); ?>
              </div>
            </div>
          </div>

          <div id="main-menu-wrapper">
            <div id="call-text">To make a claim call: <span>1-877-559-7556</span></div>
            <div id="main-nav">
              <?php echo $sMainMenuInternal; ?>
            </div>
          </div>
          <div id="internal-content-wrapper">
            <?php if ($oPage->url == 'page-not-found') { ?>
              <div id="upper_zone" class="content-zone">
                <div class="section-widget "><img src="/asset/upload/image/content/img-marine.jpg" alt=""></div><div class="section-text ">
                  <div class="overlay fakePage">
                    <?php echo $oPage->content ?>
                    <div id="page-not-found-list">
                      <div id="left_column">
                        <?php echo $sMainMenuInternal; ?>
                      </div>
                      <div id="right_column">
                        <?php echo $sFooterMenu ?>                      
                      </div>
                  </div>
                </div>
              </div>
                </div>
              </div>
            <?php } elseif ($oPage->url == 'site-map') { ?>
              <div id="upper_zone" class="content-zone">
                <div class="section-widget "><img src="/asset/upload/image/content/img-marine.jpg" alt=""></div><div class="section-text ">
                  <div class="overlay fakePage">
                    <?php echo $oPage->content ?>
                    <div id="page-not-found-list">
                      <div id="left_column">
                        <?php echo $sMainMenuInternal; ?>
                      </div>
                      <div id="right_column">
                        <?php echo $sFooterMenu ?>                      
                      </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
          } else {
            if (!empty($oPage->content) || !empty($oInclude)) {
              echo $oPage->content;
            }
          }
          ?>
        </div>

        <div id="footer-wrapper-border">
          <div id="footer-wrapper-bg">
            <div id="footer">
              <div class="leftColumn">
                <h3>Professionally accredited by</h3>
                <div class="accreditedCompany">
                  <a href="http://www.ibabc.org/" target="_blank">
                    <img width="130" height="90" class="accreditedCompanyImg" src="/themes/bwinsurance/images/accredited-association/logo-accredited-IBABC.jpg" alt="The Insurance Brokers assn of BC" title="The Insurance Brokers assn of BC"/>
                  </a>
                </div>                   
                <div class="accreditedCompany">
                  <a href="http://www.cfib-fcei.ca/english/index.html" target="_blank">
                    <img width="130" height="90" class="accreditedCompanyImg" src="/themes/bwinsurance/images/accredited-association/logo-accredited-CFIB.jpg" alt="Canadian Federation of Independant Business" title="Canadian Federation of Independant Business"/>
                  </a>
                </div>   
                <div class="accreditedCompany">
                  <a href="http://www.bctrucking.com/" target="_blank">
                    <img width="130" height="90" class="accreditedCompanyImg" src="/themes/bwinsurance/images/accredited-association/logo-accredited-BCTA.jpg" alt="The BC Truckers Association" title="The BC Truckers Association"/>
                  </a>
                </div>      
                <div class="accreditedCompany last">
                  <a href="http://www.langleychamber.com/" target="_blank">
                    <img width="130" height="90" class="accreditedCompanyImg" src="/themes/bwinsurance/images/accredited-association/logo-accredited-GLCOC.jpg" alt="Langley Chamber of Commerce" title="Langley Chamber of Commerce"/>
                  </a>
                </div>                
                <div class="accreditedCompany">
                  <a href="http://abbotsfordchamber.com/" target="_blank">
                    <img width="130" height="90" class="accreditedCompanyImg" src="/themes/bwinsurance/images/accredited-association/logo-accredited-ACOC.jpg" alt="Abbotsford Chamber of Commerce" title="Abbotsford chamber of commerce"/>
                  </a>
                </div>              
                <div class="accreditedCompany">
                  <a href="http://www.businessinsurrey.com/" target="_blank">
                    <img width="130" height="90" class="accreditedCompanyImg" src="/themes/bwinsurance/images/accredited-association/logo-accredited-SBOT.jpg" alt="Surrey Board of Trade" title="Surrey Board of Trade"/>
                  </a>
                </div>                
                <div class="accreditedCompany">
                  <a href="http://www.childrenswish.ca/" target="_blank">
                    <img width="130" height="90" class="accreditedCompanyImg" src="/themes/bwinsurance/images/accredited-association/logo-accredited-CWF.jpg" alt="The Childrens Wish Foundation of Canada" title="The Childrens Wish Foundation of Canada"/>
                  </a>
                </div>                  


              </div>
              <div class="rightColumn">

                <h3>
                  <span id="connectText">Connect with us</span>
                  <span id="socialIcons">
                    <a href="https://www.facebook.com/bwinsurance" target="_blank"><img class="socialIcon" id="fb" src="/themes/bwinsurance/images/social_icons/icon-fb.png" alt="Facebook icon" height="26" width="25"/></a>
                    <!--<a href="/" target="_blank"><img class="socialIcon" id="twitter" src="/themes/bwinsurance/images/social_icons/icon-twitter.png" alt="Twitter icon" height="26" width="25"/></a>-->
                  </span>
                </h3>
                <div id="facebook">
                  Find B&mp;W insurance agencies on Facebook<br/>
                  <div class="fb-like" data-href="https://www.facebook.com/bwinsurance" data-send="false" data-layout="button_count" data-width="290" data-show-faces="false" data-font="verdana"></div>                      
                </div>
              </div>
              <div class="clear"></div>
              <h3>Insurance services</h3>
              <div class="verticalFooterMenu">
                <?php echo $sFooterInsuranceMenu ?>
              </div>
              <h3>Site information</h3>
              <div class="verticalFooterMenu">
                <?php echo $sFooterMenu ?>
              </div>
              <div class="clear"></div>
              <div id="copyright">
                &copy; <?php echo date('Y') ?> B&amp;W Insurance Agencies. All rights reserved.
              </div>
            </div>
          </div>
        </div>
        <script type='text/javascript'>
        (function (d, t) {
          var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
          bh.type = 'text/javascript';
          bh.src = '//www.bugherd.com/sidebarv2.js?apikey=3rtvnbaukvzxazlvhnyxba';
          s.parentNode.insertBefore(bh, s);
        })(document, 'script');
        </script>           
      </body>
      </html>
