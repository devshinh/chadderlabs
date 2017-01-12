<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Hot CMS Version 3.0</title>
    <base href="<?php echo $this->config->item( 'base_url' ) ?>" />
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="asset/images/favicon/global.ico" />
    <meta http-equiv="Content-Language" content="en" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="Author" content="Hot Tomali Communications Inc." />
    <meta name="Description" content="" />
    <meta name="Keywords" content="" />
    
    <link rel="stylesheet" type="text/css" href="asset/css/stickyFooter.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="asset/css/global.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="asset/css/ui-lightness/jquery-ui-1.8.16.custom.css" media="screen" />
    <!-- <link rel="stylesheet" type="text/css" href="asset/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />-->
    <link rel="stylesheet" type="text/css" href="asset/css/hotcms.css" />
<?php 
//load module css
if (!empty( $aModuleInfo['sStyleSheet'] )){ 
   foreach (explode( ' ', $aModuleInfo['sStyleSheet'] ) as $file){ 
?>
   <link rel="stylesheet" type="text/css" href="<?php echo $file; ?>" media="screen" />
<?php } 
  } ?>

    <script type="text/javascript" src="asset/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="asset/js/tinymce/jquery.tinymce.js"></script>
    <script type="text/javascript" src="asset/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="asset/js/jquery.cycle.all.min.js"></script>
    <!-- script type="text/javascript" src="asset/js/tiny_mce/tiny_mce_gzip.js"></script>
    <script type="text/javascript" src="asset/js/tiny_mce/tiny_mce_gzip_init.js"></script>
    <script type="text/javascript" src="asset/js/tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript" src="asset/js/tiny_mce/tiny_mce_init.js"></script>
    <script type="text/javascript" src="asset/js/ajaxupload.3.5.js"></script -->
    <!-- <script type="text/javascript" src="asset/js/jquery.validate.min.js"></script> -->
    <script type="text/javascript" src="asset/js/interface_1.2/interface.js"></script>
    <script type="text/javascript" src="asset/js/global.js"></script>
    <script type="text/javascript" src="asset/js/jstree/jquery.jstree.js"></script>
    <script type="text/javascript" src="asset/js/json2.js"></script>
    
    
<?php 
//load module js
if (!empty( $java_script )){
   foreach (explode( ' ', $java_script ) as $file){ 
?>
    <script type="text/javascript" src="<?php echo $file; ?>"></script>
<?php } 
  } ?>    
    
    
<?php 
//load module css
if (!empty( $css )){ 
   foreach (explode( ' ', $css ) as $file){ 
?>
    <link rel="stylesheet" type="text/css" href="<?php echo $file; ?>" media="screen" />
<?php } 
  } ?>       
    
    
  </head>
  <body>
   <div id="wrap">
    <div id="header">
     <div class="content">
      <div id="header_left">
      <div id="logoSmall">
       <a id="logo_link" href="/hotcms/">HotCMS</a>
       <div id="cms_version">CMS V3.0</div>
      </div>
       
       <!--<div id="htc_text">Hot Tomali Communications Inc. <span class="red">//</span> <a href="http://www.hottomali.com" target="_blank">hottomali.com</a></div>-->
      </div>
      <div id="loginInfo">
      <ul>
        <?php if ($this->session->userdata( 'user_id' )){ ?>
         <li><b><?php echo $this->session->userdata( 'username' ) ?> </b>/ <em><?php echo $this->session->userdata( 'user_email' ) ?></em></li>
         <li class="separator">/</li>
         <li><a id="aLogout" href="/hotcms/dashboard/logout">Logout</a></li>
        <?php } ?>        
      </ul>
      </div>
     </div>
    </div> <!-- #header-->
    <div id="main">
    <div id="top_menu_bg">
      <div id="top_menu">
      <?php if ($this->session->userdata( 'user_id' )){ ?>
       <ul class="top_menu">
<!--        <li class="first"><a href="/hotcms/site">Sites</a></li>
        <li><a href="/hotcms/module">Modules</a></li>
        <li><a href="/hotcms/role">Roles</a></li>
        <li><a href="/hotcms/user">Users</a></li>
        <li><a href="/hotcms/organization">Organization</a></li>

        <li class="last"><a href="/hotcms/product">Product</a></li>
        <li><a href="/hotcms/menu">Menu</a></li>
        <li><a href="/hotcms/member">Member</a></li>
-->
        <li class="first"><a href="/hotcms/page">Page Publisher</a></li>
        <li class="last"><a href="/hotcms/media-library">Media Library</a></li>
       </ul>
      <?php } else { ?>
      <!--
      <div id="logoBig">
       <a href="/hotcms/">
        <img src="asset/images/logo_htc.png" alt="HTC logo" width="215" height="115" />
       </a>      
      </div>-->
      <?php } ?>
    </div>
    </div>
     <div class="content">
      <?php if ($this->session->userdata( 'user_id' )){ ?>
        <!-- if you are logged in then do somethin here -->
      <?php } else { ?>
      <!--<div id="logoBig">
       <a href="/hotcms/">
        <img src="asset/images/logo_htc.png" alt="HTC logo" width="215" height="115" />
       </a>      
      </div>-->
      <?php } ?>
      <div id="container">
          <div id="messageContainer">
             <div class="message">
              <?php if (!empty( $message )){ ?>
                            <div class="message_close"><a onClick="closeMessage()">[close]</a></div>
                            <div class="message <?php echo $message['type'] ?>">
                              <?php echo $message['value'] ?>
                            </div>
              <?php } ?>
               <div class="<?php echo !empty( $message ) ? ' hide' : '' ?>"><!----></div>
             </div>   
          </div>       
        <div id="leftContent">
         <div class="module_header"><?php echo $module_header ?></div>
          <div class="sub_menu" style="padding:10px 9px;">
          <?php
           echo $moduleView;
          ?>
          </div>
        </div>
        <div class="clear"></div>
      </div> <!-- #container-->       
           
      <div class="clear"></div>
     </div>
    </div> <!-- #main-->
   </div> <!-- #wrap-->
   <div id="footer">
    <div class="content">
      <p>Copyright &copy; <?php echo date('Y');?> Hot Tomali Communications Inc. <?php echo lang('hotcms_allrightsreserved')?></p>
    </div>
   </div>
  </body>
</html>
