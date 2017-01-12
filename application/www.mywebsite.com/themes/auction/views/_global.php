<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->session->userdata( 'langCode' ) ?>" lang="<?php echo $this->session->userdata( 'langCode' ) ?>">
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
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/jquery/jquery.jcarousel.css?<?php echo time(); ?>" />
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/global.css?<?php echo time(); ?>" />
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/sticky_footer.css?<?php echo time(); ?>" />
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/jquery/redmond/jquery-ui-1.8.9.custom.css?<?php echo time(); ?>" />
<?php 
if (!empty( $oPage->style_sheets )){ foreach ($oPage->style_sheets as $file){ ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $file; ?>?<?php echo time(); ?>" />
<?php } } ?>
<?php if (!empty( $oPage->style_sheet ) && empty( $oModule->style_sheet ) ){ foreach (explode( ' ', $oPage->style_sheet ) as $file){ ?>
    <link rel="stylesheet" type="text/css" media="all" href="themes/<?php echo $sTheme; ?>/css/page/<?php echo $file; ?>?<?php echo time(); ?>" />
<?php } } ?>
    <script type="text/javascript">
    var oGlobal = { 
     page : { name : "<?php echo $oPage->name ?>" }
    };
    </script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery-1.4.4.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.validate.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.bxGallery.1.1.min.js"></script>
    <script type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery-ui-1.8.9.custom.min.js"></script>
    <!--[if lt IE 7]><script defer="defer" type="text/javascript" src="themes/<?php echo $sTheme; ?>/js/jquery/jquery.png.js"></script><![endif]-->
    <script type="text/javascript" src="/themes/<?php echo $sTheme; ?>/js/global.js"></script>
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
     <script type="text/javascript">
jQuery(document).ready(function(){
 if(jQuery('#slides').length>0){  
	jQuery('#slides').bxGallery({
		maxheight: 350,
    maxwidth: 650,
	 	thumbwidth: 75,
	 	thumbplacement: 'bottom',
	 	thumbcontainer: 90
	 });
  }
});

      </script>
  </head>
  <body>
  <?php $aUrl = explode('/',$this->uri->uri_string()); ?>
   <div id="wrap"> 
    <div id="user-menu-wrapper">
     <div id="user-menu">
      <p>All proceeds will go directly to the Maasai Wilderness Conservation Trust. <a href="/about-mwct-hot-tomali">Find out more about MWCT.</a></p>
      <ul>
       <?php 
       $userid = $this->session->userdata('user_id');
       $username = $this->session->userdata('username');
       if(empty($userid)){
       ?>
         <li class=<?php echo ($aUrl[0]=='register' ? 'current':'');?>><a href="/register">SIGN UP</a></li>
         <li class=<?php echo ($aUrl[0]=='login' ? 'current':'');?>><a href="/login">LOG IN</a></li>
       <?php }else{ ?>
         <li class="<?php echo ($aUrl[0]=='my-account' ? 'current':'');?>"><a href="/my-account/profile">PROFILE <span id="username"><?php echo $username ?></span></a></li>
         <li><a href="/logout">LOG OUT</a></li>
       <?php }?>
      </ul>
     </div>
    </div>
    <div id="head-wrapper">
      <div id="header">        
       <a href="/"><img alt="Art for Africa Auction" src="/themes/<?php echo $sTheme; ?>/images/logo.jpg" /></a>
      </div>
    </div>
    <div id="main-nav-wrapper">
      <div id="main-nav">
        <?php         
        	//echo $sMainMenu;
			//var_dump($main_menu_items);
			if(count($main_menu_items > 0)) {
				echo '<ul>';
				foreach($main_menu_items as $menu_item)
				{
					echo '<li class="'.$menu_item->render_css_classes().'">';
					echo '<div class="menuItemContainer">';					
					echo '<a class="'.$menu_item->render_css_classes().'" href="'.$menu_item->link.'" title="'. $menu_item->display. '">';
					echo $menu_item->display;
					echo '</a>';
					echo '</div>';					
					echo '</li>';
					 										
				}
				echo '</ul>';
			}
        ?>
      </div>
    </div>
    <div id="content-wrapper">
      
<?php if($oPage->url=='home') { ?>

      <div id="content-wrapper2">
        <div id="content-wrapper3">
          <div id="content">
            <h2>
              Page heading
            </h2>
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
          <div id="content">
            <?php echo $oModule->view ?>
          </div>
        </div>
      </div>
<?php
}
?>
</div> <!-- #content-wrapper -->
</div> <!-- #wrap -->
<div id="footer-wrapper">
  <div id="footer">
   <p>Copyright &copy; <?php echo date('Y') ?>&nbsp;&nbsp; <a _target="_blank" href="http://www.hottomali.com"><img style="width:45px;height:22px;vertical-align:middle" alt="HotTomali Logo" src="/themes/<?php echo $sTheme; ?>/images/htc_logo.jpg" /></a></p>
  </div>
</div><!-- #footer-wrapper -->
    
    
    <div style="top: 400px; left: 600px; display:none" id="loading"><img width="16" height="16" align="middle" src="/themes/<?php echo $sTheme; ?>/images/loading.gif" alt="loading" /> loading... please wait...</div>
  </body>
</html>