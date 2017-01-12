<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Hot CMS Version 3.01 [beta]</title>
    <base href="<?php echo $this->config->item('base_url') ?>" />
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="asset/images/favicon/global.ico" />
    <meta http-equiv="Content-Language" content="en" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="Author" content="Hot Tomali Communications Inc." />
    <meta name="Description" content="" />
    <meta name="Keywords" content="" />

    <link rel="stylesheet" type="text/css" href="asset/css/stickyFooter.css?t=<?php echo time(); ?>" media="screen" />
    <link rel="stylesheet" type="text/css" href="asset/css/global.css?t=<?php echo time(); ?>" media="screen" />
    <!-- <link rel="stylesheet" type="text/css" href="asset/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />-->
    <link rel="stylesheet" type="text/css" href="asset/css/hotcms.css?t=<?php echo time(); ?>" />
    <?php
//load module css
    if (!empty($left_data['aModuleInfo']['sStyleSheet'])) {
      $aModuleInfo = $left_data['aModuleInfo'];
      foreach (explode(' ', $aModuleInfo['sStyleSheet']) as $file) {
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $file; ?>?t=<?php echo time(); ?>" media="screen" />
        <?php
      }
    }
    if (!empty($right_data['aModuleInfo']['sStyleSheet'])) {
      $aModuleInfo = $right_data['aModuleInfo'];
      foreach (explode(' ', $aModuleInfo['sStyleSheet']) as $file) {
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $file; ?>?t=<?php echo time(); ?>" media="screen" />
      <?php
      }
    }
    ?>         

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
    <script type="text/javascript" src="asset/js/jquery.tablesorter.min.js"></script>
    <script type="text/javascript" src="asset/js/json2.js"></script>
    <script type="text/javascript" src="asset/js/jquery-ui-timepicker-addon.js"></script>
    <script type="text/javascript" src="asset/js/ajaxupload.3.5.js"></script>

    <script type="text/javascript" src="asset/js/global.js?t=<?php echo time(); ?>"></script>
    <script type="text/javascript">
      var save_flag = false;
      window.onbeforeunload = PromptToSave;

      function PromptToSave() {
        if (save_flag) {
          return 'Warning: Your changes to sitemap will be lost.  Continue?';
        }
      }  
    </script>


    <?php
//load module js
    if (!empty($left_data['java_script'])) {
      $java_script = $left_data['java_script'];
      foreach (explode(' ', $java_script) as $file) {
        ?>
        <script type="text/javascript" src="<?php echo $file; ?>?t=<?php echo time(); ?>"></script>
        <?php
      }
    }
    if (!empty($right_data['java_script'])) {
      $java_script = $right_data['java_script'];
      foreach (explode(' ', $java_script) as $file) {
        ?>
        <script type="text/javascript" src="<?php echo $file; ?>?t=<?php echo time(); ?>"></script>
      <?php
      }
    }
    ?>     


    <?php
//load module css
    if (!empty($left_data['css'])) {
      $css = $left_data['css'];
      foreach (explode(' ', $css) as $file) {
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $file; ?>" media="screen" />
        <?php
      }
    }
    if (!empty($right_data['css'])) {
      $css = $right_data['css'];
      foreach (explode(' ', $css) as $file) {
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $file; ?>" media="screen" />
      <?php
      }
    }
    ?>       

    <link rel="stylesheet" type="text/css" href="asset/css/ui-lightness/jquery-ui-1.8.16.custom.css" media="screen" />
    
<!-- bugHerd javascript -->        
<script type='text/javascript'>
(function (d, t) {
  var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
  bh.type = 'text/javascript';
  bh.src = '//www.bugherd.com/sidebarv2.js?apikey=bwhpqaoh3x2rfh8enabh6a';
  s.parentNode.insertBefore(bh, s);
  })(document, 'script');
</script>        


  </head>
  <body>
    <div id="wrap">
      <div id="header">
        <div class="content">
          <div id="header_left">
            <div id="logoSmall">
              <a id="logo_link" href="/hotcms/">HotCMS</a>
            </div>

<!--<div id="htc_text">Hot Tomali Communications Inc. <span class="red">//</span> <a href="http://www.hottomali.com" target="_blank">hottomali.com</a></div>-->
          </div>
          <div id="loginInfo">
            <ul>
              <?php if ($this->session->userdata('user_id')) { ?>
                <?php
                $this->load->library('HotCMS_Model');
                $user = $this->hotcms_model->get_user_profile($this->session->userdata('user_id'));
                if (!empty($user->avatar_id)) {
                  $avatar_picture = $this->hotcms_model->get_user_avatar($user->avatar_id);
                }
                if (!empty($avatar_picture)) {
                  printf('<li><img src="%s%s/%s.%s" alt="%s" /></li>', $this->config->item('base_url_front'), '/asset/upload/image/avatars/thumbnail_30x30/', $avatar_picture->file_name . '_thumb', $avatar_picture->extension, $avatar_picture->file_name);
                }
                ?>

                <li><b><?php echo $user->first_name . ' ' . $user->last_name ?> </b>/ <em><?php echo $this->session->userdata('user_email') ?></em></li>
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
<?php if ($this->session->userdata('user_id')) { ?>
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
                <li><a href="/hotcms/media-library">Media Library</a></li>
                <li><a href="/hotcms/role">Roles</a></li>
                <li><a href="/hotcms/user">Address Book</a></li>
                <!--<li><a href="/hotcms/auction">Auction settings</a></li>
                <li><a href="/hotcms/product">Product</a></li>-->
                <li class=""><a href="/hotcms/retailer">Retailers</a></li>
                <li class="last"><a href="/hotcms/product">Products</a></li>
                <!--<li><a href="/hotcms/organization">Organization [beta]</a></li>    
                <li class="last"><a href="/hotcms/news">News</a></li>-->
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
<?php if ($this->session->userdata('user_id')) { ?>
            <!-- if you are logged in then do somethin here -->
<?php } else { ?>
            <!--
          <div id="logoBig">
           <a href="/hotcms/">
            <img src="asset/images/logo_htc.png" alt="HTC logo" width="215" height="115" />
           </a>      
          </div>
            -->
<?php } ?>
          <div id="container">
            <div id="leftContent">
              <div class="module_header"><?php echo $module_header ?></div>
              <div class="sub_menu">
<?php
$this->load->view($left_view, $left_data);
?>
              </div>
            </div>
            <div id="rightContent">
              <div id="messageContainer">
                <div class="message">
                    <?php if (!empty($message)) { ?>
                    <div class="message_close"><a onClick="closeMessage()">[close]</a></div>
                    <div class="message <?php echo $message['type'] ?>">
                      <?php echo $message['value'] ?>
                    </div>
              <?php } ?>
                  <div class="<?php echo!empty($message) ? ' hide' : '' ?>"><!----></div>
                </div>   
              </div>
<?php
$this->load->view($right_view, $right_data);
?>
            </div>
            <div class="clear"></div>
          </div> <!-- #container-->
        </div>
      </div> <!-- #main-->
    </div> <!-- #wrap-->
    <div id="footer">
      <div class="content">
        <p class="copyright">Copyright &copy; <?php echo date('Y'); ?> Hot Tomali Communications Inc. <?php echo lang('hotcms_allrightsreserved') ?></p>
        <div class="powered"><p>Powered by </p> <img src="asset/images/logo-hotcms-footer.png" atl="HotTomali CMS" /></div>
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
