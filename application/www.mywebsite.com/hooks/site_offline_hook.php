<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Check whether the site is offline or not.
 *
 */
class site_offline_hook {

  public function __construct()
  {
    log_message('debug', 'Accessing site_offline hook!');
  }

  public function is_offline()
  {
    if (file_exists(APPPATH . 'config/config.php'))
    {
      include(APPPATH . 'config/config.php');

      if (isset($config['is_offline']) && $config['is_offline'] === TRUE) {
        if (isset($config['theme']) && $config['theme'] > '') {
          //$splash_img = sprintf('<img src="/themes/%s/images/%s" />', $config['theme'], $config['offline_splash_image']);
          include(APPPATH . 'themes/' . $config['theme'] . '/views/maintenance.php');
        }
        else {
          $this->show_site_offline();
        }
        exit;
      }
    }
  }

  private function show_site_offline()
  {
    $info = '<p>Due to maintenance this site is temporarily offline.</p>';
    echo '<html><body style="text-align:center">' . $info . '</body></html>';
  }

}
/* Location: ./system/application/hooks/site_offline_hook.php */