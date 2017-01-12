<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Reffer colleague controller
 *
 * @package		HotCMS
 * @author		jan@hottomali.com
 * @copyright	Copyright (c) 2013, HotTomali.
 * @since		Version 3.0
 */

class News extends HotCMS_Controller {

  private $default_category_id;

  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect('login');
    }

    $this->load->config('refer_colleague', TRUE);


    $this->module_url = $this->config->item('module_url', 'refer_colleague');
    $this->module_header = 'Refer colleague';
    //$this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_news'));
    $this->front_theme = $this->config->item('theme');
  }

}
?>
