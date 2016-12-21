<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if ($oPage->url=='home') { echo htmlspecialchars($sSiteName).' | '.htmlspecialchars($oPage->title); }else{ echo (!empty($oModule->sSubTitle) ? $oModule->sSubTitle . ' | ' : '') . htmlspecialchars($oPage->title) . ' | ' . htmlspecialchars($sSiteName);}?></title>
    <meta http-equiv="Content-Language" content="en" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="description" content="<?php echo htmlspecialchars($oPage->meta_description) ?>" />
    <meta name="keywords" content="<?php echo htmlentities($oPage->meta_keyword) ?>" />
    <meta name="author" content="Hot Tomali Communications Inc." />
    <base href="<?php echo (empty($_SERVER['HTTPS'])? 'http://':'https://') . $_SERVER['HTTP_HOST'] . '/'; ?>" />
    <link rel="shortcut icon" href="themes/<?php echo $sTheme; ?>/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/global.css?ver=1.0" />
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/sticky_footer.css?ver=1.0" />
<?php 
if (!empty( $oPage->style_sheets )){ foreach ($oPage->style_sheets as $file){ ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $file; ?>" />
<?php } } ?>
    <!--[if IE 8]><link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/ie8.css"><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/ie7.css"><![endif]-->
    <link rel="stylesheet" type="text/css" media="print" href="themes/<?php echo $sTheme; ?>/css/print.css" />
    <script type="text/javascript">
    var oGlobal = { page : { name : "<?php echo $oPage->name ?>" } };
    </script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery-1.4.4.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.cycle.all.min.js"></script>
    <!--[if lt IE 7]><script defer="defer" type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.png.js"></script><![endif]-->
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/global.js"></script>
<?php if (!empty( $oPage->javascripts )){ foreach ($oPage->javascripts as $file){ ?>
    <script type="text/javascript" src="<?php echo $file; ?>"></script>
<?php } } ?>
<?php
// Google Analytics code
if (function_exists('google_analytics')) {
	echo google_analytics();
} ?>
  </head>
  <body>
    <div id="head-wrapper">
      <div id="header">
        <div id="site-logo"><a href="/" title="<?php echo htmlspecialchars($sSiteName); ?>"><span><?php echo htmlspecialchars($sSiteName); ?></span></a></div>
        <div id="quick-links">
          <ul>
            <li><a id="ql-youtube" title="City of Vancouver on YouTube" class="hover-icon" href="http://www.youtube.com/user/VancouverCityHall" target="_blank"><span>YouTube</span></a></li>
            <li><a id="ql-facebook" title="City of Vancouver on Facebook" class="hover-icon" href="http://www.facebook.com/CityofVancouver" target="_blank"><span>Facebook</span></a></li>
            <li><a id="ql-twitter" title="City of Vancouver on Twitter" class="hover-icon" href="http://twitter.com/CityofVancouver" target="_blank"><span>Twitter</span></a></li>
            <li><a id="ql-cov" title="City of Vancouver" href="http://vancouver.ca" target="_blank"><img src="/themes/<?php echo $sTheme; ?>/images/logo-header-CoV.png" alt="City of Vancouver" /></a></li>
          </ul>
        </div>
        <div id="main-nav">
        <?php         
        if(is_array($main_menu_items) &&  count($main_menu_items > 0)) {
          echo '<ul>';
          foreach($main_menu_items as $menu_item) {
            echo '<li class="'.$menu_item->render_css_classes().'">';
            //echo '<div class="menuItemContainer">';					
            echo '<a class="' . $menu_item->render_css_classes() . '" id="nav-' . str_replace(' & ', '-', (strtolower($menu_item->display)))
              . '" href="' . $menu_item->link . '" title="' . $menu_item->display . '"' . ($menu_item->external==1 ? ' target="_blank"' : '') . '><span>';
            echo $menu_item->display;
            echo '</span></a>';
            //echo '</div>';					
            echo '</li>';
          }
          echo '</ul>';
        }
        ?>
        </div>
      </div>
    </div>
    <div id="content-wrapper">
      
<?php if($oPage->url=='homepage') { ?>

      <div id="content-wrapper2">
        <div id="content-wrapper3">
          <div id="page-content">
            <div id="upper_zone" class="content-zone">
		<div id="carousel">
			<div id="homePageCarouselBorder">
				<div id="homepageCarouselWrapper">
					<div id="homepageCarousel">
						<div class="image slide">
							<a href="/pedestrians/"> <img src="/themes/practiceroadsafety/images/img-pedestrian-carousel.jpg" alt="Pedestrians" /> </a>
						</div>
						<div class="image slide">
							<a href="/cyclists/"> <img src="/themes/practiceroadsafety/images/img-cyclist-carousel.jpg" alt="Cyclists" /> </a>
						</div>
						<div class="image slide">
							<a href="/vehicles"> <img src="/themes/practiceroadsafety/images/img-vehicle-carousel.jpg" alt="Vehicles" /> </a>
						</div>
					</div>
				</div>
			</div>
			<div id="carouselNavWrap" class="stopped">
				<a id="navRight" class="carouselNavControl" href="#">&nbsp;</a>
				<span id="carouselNav">&nbsp;</span>
				<a id="navLeft" class="carouselNavControl" href="#">&nbsp;</a>
			</div>
		</div>
            </div>
            <div id="leftbar_zone1" class="content-zone">
              <h2>Did You Know?</h2>
              <span id="home-stats"><p>Every day on average, about 230 crashes occurred at an intersection in B.C.</p></span>
            </div>
            <div id="middle_zone1" class="content-zone">
              <h2>Tips for Pedestrians</h2>
              <div class="text-box">
                <ul>
                  <li>Don&rsquo;t jaywalk.</li>
                  <li>Always cross at intersections.</li>
                  <li>Make eye contact with drivers and wait for cars to stop before crossing the street.</li>
                </ul>
              </div>
              <a href="pedestrians" class="home-link pedestrians"><img src="/themes/practiceroadsafety/images/img-pedestrian-icon.png" />Learn More</a>
            </div>
            <div id="middle_zone2" class="content-zone">
              <h2>Tips for Cyclists</h2>
              <div class="text-box">
                <ul>
                  <li>Obey all traffic signals, traffic signs and speed limits. </li>
                  <li>Stop at stop signs and red lights.</li>
                  <li>Signal before turning.</li>
                  <li>Yield to pedestrians.</li>
                </ul>
              </div>
              <a href="cyclists" class="home-link cyclists"><img src="/themes/practiceroadsafety/images/img-cyclist-icon.png" />Learn More</a>
            </div>
            <div id="middle_zone3" class="content-zone">
              <h2>Tips for Drivers</h2>
              <div class="text-box">
                <ul>
                  <li>Approach intersections with caution. Be aware of and watch for pedestrians, cyclists, and other vehicles even if you have a green light.</li>
                  <li>Obey all posted speed limits.</li>
                </ul>
              </div>
              <a href="drivers" class="home-link drivers"><img src="/themes/practiceroadsafety/images/img-driver-icon.png" />Learn More</a>
            </div>
            <div class="clear"></div>
            <div id="leftbar_zone2" class="content-zone">
              <h2>Quick Survey</h2>
              <div class="text-box2">
                <p>Take part in our two minute survey. We want your feedback!</p>
              </div>
              <a href="survey" class="home-link survey"><img src="/themes/practiceroadsafety/images/img-survey-icon.png" />Take Survey</a>
            </div>
            <div id="middle_zone4" class="content-zone">
              <h2>City of Vancouver Road Safety Campaign</h2>
              <div class="text-box2">
                <p>Lorem ipsum dolor sit amet consect etuer adipi scing elit sed diam nonummy nibh euismod tinunt ut laoreet dolore magna aliquam erat volut.
                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
              </div>
              <a href="about" class="home-link">Learn More</a>
            </div>
          </div>
        </div>
      </div>

<div class="clear"> </div>

<?php 
}

// page without module
elseif (empty( $oModule->view )) {
?>
      <div id="content-wrapper2">
        <div id="content-wrapper3">
          <div id="page-content">        
          <?php
            if (!empty( $oPage->content ) || !empty( $oInclude )) {
              echo $oPage->content;
              //if (isset($oInclude) && !empty( $oInclude )){
              //  echo $oInclude->view;
              //}
            }
          ?>
          </div>
        </div>
      </div>
<?php
}

// page with module
else {
?>  
      <div id="content-wrapper2">
        <div id="content-wrapper3">
          <div id="page-content">
            <?php echo $oModule->view ?>
          </div>
        </div>
      </div>
<?php
}
?>
    </div> <!-- #content-wrapper -->

    <div id="footer-wrapper">
      <div id="footer">
        <p>&copy; <?php echo date('Y'); ?> 
          <a title="City of Vancouver" id="fl-cov" href="http://app.fluidsurveys.com/s/roadsafety" target="_blank">City of Vancouver</a> &nbsp;|&nbsp; 
          <a title="Disclaimer" href="http://vancouver.ca/statements/copyright.htm" target="_blank">Disclaimer</a> &nbsp;|&nbsp;
          <a title="Privacy Policy" href="http://vancouver.ca/statements/privacy.htm" target="_blank">Privacy Policy</a> &nbsp;|&nbsp;
          <a title="Contact Us" href="mailto:info@vancouver.ca?subject=City of Vancouver People are Fragile road safety program feedback">Contact Us</a>
        </p>
      </div>
    </div><!-- #footer-wrapper -->

  </body>
</html>