<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Load site configs from DB.
 *
 */
class site_config_hook {

  public function __construct()
  {
    log_message('debug', 'Accessing site_config hook!');
  }

  public function load_config()
  {
    global $CFG;
    var_dump($CFG);
    die('test');
    $this->CI =& get_instance();
    $this->load->model('model__global', 'model');
    $site = $this->model->get_site();
    if (empty($site)) {
      die('Sorry but the site configurations are missing.');
    }
    // load site-wide configurations
    $this->config->set_item('theme', $site->theme);
  }

}
/* Location: ./system/application/hooks/site_config_hook.php */