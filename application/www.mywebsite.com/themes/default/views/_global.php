<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->session->userdata( 'langCode' ) ?>" lang="<?php echo $this->session->userdata( 'langCode' ) ?>">
 
 <!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]> <html class="no-js" lang="en"> <![endif]-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if ($oPage->url=='home') { echo htmlspecialchars($sSiteName).' | '.htmlspecialchars($oPage->title); }else{ echo (!empty($oModule->sSubTitle) ? $oModule->sSubTitle . ' | ' : '') . htmlspecialchars($oPage->title) . ' | ' . htmlspecialchars($sSiteName);}?></title>
    <meta http-equiv="Content-Language" content="en" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="description" content="<?php echo htmlspecialchars($oPage->meta_description) ?>" />
    <meta name="keywords" content="<?php echo htmlentities($oPage->meta_keyword) ?>" />
    <meta name="author" content="Hot Tomali Communications Inc." />
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <base href="<?php echo (empty($_SERVER['HTTPS'])? 'http://':'https://') . $_SERVER['HTTP_HOST'] . '/'; ?>" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/jquery/jquery.jcarousel.css" />
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/global.css?ver=1.0" />
<?php
if (!empty( $oPage->style_sheets )){ foreach ($oPage->style_sheets as $file){ ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $file; ?>" />
<?php } } ?>
<?php if (!empty( $oPage->style_sheet ) && empty( $oModule->style_sheet ) ){ foreach (explode( ' ', $oPage->style_sheet ) as $file){ ?>
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/page/<?php echo $file; ?>?D" />
<?php } } ?>
    <script type="text/javascript">
    var oGlobal = { page : { name : "<?php echo $oPage->name ?>" } };
    </script>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/boiler/libs/modernizr-2.0.6.min.js"></script>
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/jquery/jquery.validate.min.js"></script>
   <!--
   <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/boiler/script.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery-1.4.4.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.jcarousel.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.typewriter.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.tools.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.selectboxes.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery-ui-1.8.9.custom.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.cycle.all.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.maskedinput-1.2.2.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/superfish/hoverIntent.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/superfish/supersubs.js"></script>-->
    <!--[if lt IE 7]><script defer="defer" type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.png.js"></script><![endif]-->
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/global.js"></script>
<?php if (!empty( $oPage->javascripts )){ foreach ($oPage->javascripts as $file){ ?>
    <script type="text/javascript" src="<?php echo $file; ?>"></script>
<?php } } ?>
<?php if (!empty( $oPage->javascript ) && empty( $oModule->javascript )){ foreach (explode( ' ', $oPage->javascript ) as $file){ ?>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/page/<?php echo $file; ?>"></script>
<?php } } ?>
    <!--[if IE 8]><link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/ie8.css"><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/ie7.css"><![endif]-->
    <link rel="stylesheet" type="text/css" media="print" href="themes/<?php echo $sTheme; ?>/css/print.css" />
    <meta name="google-site-verification" content="" />
<?php
// Google Analytics code
if (!empty( $aAnalytics['google'] )) { ?>
	<?php //echo $aAnalytics['google']->sCode ?>
<?php } ?>
  </head>
  <body>

    <div id="head-wrapper">
      <div id="header">
        <div id="website-logo"><span><?php echo htmlspecialchars($sSiteName); ?></span></div>
        <div id="main-nav">
          <?php echo $sMainMenu; ?>
        </div>
      </div>
    </div>
    <div id="content-wrapper">
      
<?php if($oPage->url=='homexx') { ?>

      <div id="content-wrapper2">
        <div id="content-wrapper3">
          <div id="hero"><img src="/asset/upload/image/home-hero.jpg" alt="hero image" width="940" /></div>
          <div id="content">
            <h1>
              Page heading
            </h1>
            <p>
              Lorem ipsum dolor sit amet consect etuer adipi scing elit sed diam nonummy nibh euismod tinunt ut laoreet dolore magna aliquam erat volut. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.
            </p>
            <p>
              Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
            </p>
            <p>
              Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.
            </p>
          </div>
          <div id="side-column">
            <h3>
              Side Heading
            </h3>
            <p>
              Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan.
            </p>
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
          <div id="divPageHeader">
            <div id="divBreadcrumb">
              <?php echo set_breadcrumb(); ?>
            </div>
            <?php if ($this->session->userdata('user_id')>'0') echo '<a href="/logout" id="logout" class="triangle" title="logout"> Logout</a>';?>
          </div>
          <div class="content">        
          <?php
            if (!empty( $oPage->content ) || !empty( $oInclude )) {
              echo $oPage->content;
              //if (isset($oInclude) && !empty( $oInclude )){
              //  echo $oInclude->view;
              //}
            }
          ?>
          </div> <!--end of content -->                
        </div>
      </div>
<?php
}

// page with module
else {
?>  
      <div id="content-wrapper2">
        <div id="content-wrapper3">
          <div id="divPageHeader">
            <div id="divBreadcrumb">
              <?php echo set_breadcrumb(); ?>
            </div>
            <?php if ($this->session->userdata('user_id')>'0') echo '<a href="/logout" id="logout" class="triangle" title="logout"><img src="/themes/'. $sTheme.'/images/icon_user.png" alt="logout" /> Logout</a>';?>
          </div>
          <div id="content">
            <?php echo $oModule->view ?>
          </div>
        </div>
      </div>
<?php
}
?>
        
      <div id="footer-wrapper">
        <div id="footer">
          Copyright &copy; <?php echo date('Y') . ' ' . htmlspecialchars($sSiteName) ; ?>
        </div>
      </div>
    </div>
    
    <div style="top: 400px; left: 600px; display:none" id="loading"><img width="16" height="16" align="middle" src="/themes/<?php echo $sTheme; ?>/images/loading.gif" alt="loading" /> loading... please wait...</div>
      
  </body>
</html>