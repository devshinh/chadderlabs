<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="en" />
    <title>Hot CMS Version 3.0 [DEV]</title>
    <base href="<?php echo $this->config->item('base_url') ?>" />
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="asset/images/favicon/global.ico" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="Author" content="Hot Tomali Communications Inc." />
    <meta name="Description" content="" />
    <meta name="Keywords" content="" />
    <link rel="stylesheet" type="text/css" href="asset/css/stickyFooter.css?<?php echo time(); ?>" media="screen" />
    <link rel="stylesheet" type="text/css" href="asset/css/global.css?<?php echo time(); ?>" media="screen" />
    <link rel="stylesheet" type="text/css" href="asset/css/ui-lightness/jquery-ui-1.8.16.custom.css" media="screen" />
    <!-- <link rel="stylesheet" type="text/css" href="asset/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />-->
    <link rel="stylesheet" type="text/css" href="asset/css/hotcms.css?<?php echo time(); ?>" />
    <link rel="stylesheet" href="asset/css/font-awesome.min.css">
    <!-- link rel="stylesheet" type="text/css" href="asset/js/video-js/video-js.css" media="screen" / -->
    <?php
//load module css
    if (!empty($aModuleInfo['sStyleSheet'])) {
      foreach (explode(' ', $aModuleInfo['sStyleSheet']) as $file) {
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $file; ?>?<?php echo time(); ?>" media="screen" />
        <?php
      }
    }
    ?>
    <script type="text/javascript" src="asset/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="asset/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="asset/js/tinymce/jquery.tinymce.min.js"></script>
    <!--<script type="text/javascript" src="asset/js/tinymce/jquery.tinymce.js"></script>-->
    <script type="text/javascript" src="asset/js/jquery-ui-timepicker-addon.js"></script>
    <script type="text/javascript" src="asset/js/jquery.cycle.all.min.js"></script>
    <script type="text/javascript" src="asset/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="asset/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="asset/js/jstree/jquery.jstree.js"></script>
    <script type="text/javascript" src="asset/js/json2.js"></script>
    <script type="text/javascript" src="asset/js/jquery.tablesorter.min.js"></script>
    <script type="text/javascript" src="asset/js/global.js"></script>
    <!--<script type="text/javascript" src="http://cdn.sublimevideo.net/js/djr103cr-beta.js"></script>-->
<?php
//load module js
    if (!empty($java_script)) {
      foreach (explode(' ', $java_script) as $file) {
        ?>
        <script type="text/javascript" src="<?php echo $file; ?>?<?php echo time(); ?>"></script>
      <?php
      }
    }
//load module css
    if (!empty($css)) {
      foreach (explode(' ', $css) as $file) {
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $file; ?>" media="screen" />
      <?php
      }
    }
    ?>
  </head>
  <body id="admin">
    <div id="wrap">
      <div id="header">
        <div class="content">
          <div id="header_left">
            <div id="logoSmall">
              <a id="logo_link" href="/hotcms/">HotCMS</a>
              <?php
              if(!empty($aSite)){
                foreach ($aSite as $row){
                 if ($row->id == $this->session->userdata( 'siteID' ) && !empty($row->site_image)){ 
                  printf('<span id="brand_logo">%s</span>',$row->site_image->thumb_html);
                 }
                }
              }
?>
            </div>
            <!--<div id="htc_text">Hot Tomali Communications Inc. <span class="red">//</span> <a href="http://www.hottomali.com" target="_blank">hottomali.com</a></div>-->
          </div>
          <div id="loginInfo">
            <ul>
              <?php if ($this->session->userdata('user_id')) { ?>
                <li>
                <?php echo form_open('dashboard', array('id' => 'sites_form'), $hidden_cur_url); ?>                    
                  <select id="cboSite_global" name="cboSite_global">
                  <?php foreach ($aSite as $row){ ?>
                    <option value="<?php echo $row->id ?>" <?php if ($row->id == $this->session->userdata( 'siteID' )){ ?> selected="selected"<?php } ?>>
                      <?php echo $row->name ?> &mdash; <?php echo $row->domain; ?>
                    </option>
                  <?php } ?>
                  </select>                    
                <?php 
                echo form_close(); 
                ?>
                    
                </li>
                <!--<li><em><?php echo $this->session->userdata('siteName') ?></em></li>
                <li class="separator">/</li>-->
                <li>                
                <?php
                if (!empty($avatar_picture)) {
                  printf('<li style="margin:3px 5px;"><img src="%s%s/%s.%s" alt="%s" /></li>', $this->config->item('base_url_front'), '/asset/upload/thumbnail_30x30', $avatar_picture->file_name . '_thumb', $avatar_picture->extension, $avatar_picture->file_name);
                }
                ?><b><?php echo $user->first_name . ' ' . $user->last_name ?> </b>&lt;<em><?php echo $this->session->userdata('user_email') ?></em>&gt;</li>
                
                <li><a id="aLogout" class="red_button_smaller" href="/hotcms/logout">Logout</a></li>
<?php } ?>
            </ul>
          </div>
        </div>
      </div> <!-- #header-->
      <div id="main">
        <div id="top_menu_bg">
          <div id="top_menu">
            <?php if (isset($admin_menu)) { echo $admin_menu; } ?>
          </div>
        </div>
        <div class="clear"></div>
      <div class="content">
        <?php if ($this->session->userdata('user_id')) { ?>
          <!-- if you are logged in then do somethin here -->
<?php } else { ?>
          <!--<div id="logoBig">
           <a href="/hotcms/">
            <img src="asset/images/logo_htc.png" alt="HTC logo" width="215" height="115" />
           </a>
          </div>-->
          <?php } ?>
        <div id="container">
<?php 
if (uri_string()!='hotcms/dashboard/analysis'){
if (isset($leftbar) && !empty($leftbar)) { ?>
            <div id="leftContent">
              <div class="module_header"><?php echo $module_header ?></div>
              <div class="sub_menu">
            <?php echo (isset($leftbar) ? $leftbar : ''); ?>
              </div>
            </div>
<?php }
}
?>
          <div id="rightContent">
            <div id="messageContainer">
              <!-- div class="message">
              </div -->
              <?php
              if (isset($messages) && is_array($messages)) {
                foreach ($messages as $msg) {
                  if (is_array($msg) && $msg['message'] > '') {
                    echo '<div class="message ' . $msg['type'] . '">';
                    echo '<div class="message_close"><a onClick="closeMessage()">[close]</a></div>';
                    echo  $msg['message'] . '</div>';
                  }
                }
              }
              ?>

            </div>
            <?php
            echo (isset($main_area) ? $main_area : '');
            echo (isset($moduleView) ? $moduleView : ''); // legacy code, to be removed
            ?>
          </div>
          <div class="clear"></div>
        </div> <!-- #container-->
        <div class="clear"></div>
      </div> <!-- .content -->
    </div> <!-- #main-->
    </div> <!-- #wrap-->
    <div id="footer">
      <div class="content">
        <p class="copyright">Copyright &copy; <?php echo date('Y'); ?> Hot Tomali Communications Inc. <?php echo lang('hotcms_allrightsreserved') ?></p>
        <div class="powered"><p>Powered by </p> <img src="asset/images/logo-hotcms-footer.png" atl="HotTomali CMS" /></div>
      </div>
    </div>
<!-- bugHerd javascript -->
<script type='text/javascript'>
(function (d, t) {
  var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
  bh.type = 'text/javascript';
  bh.src = '//www.bugherd.com/sidebarv2.js?apikey=qtbaagk1a1kojmgabmq36w';
  s.parentNode.insertBefore(bh, s);
  })(document, 'script');
  //_V_.options.flash.swf = "/asset/js/video-js/video-js.swf";
</script>
  </body>
</html>
