<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hotajax extends HotCMS_Controller {

  function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->application_name = 'www.example.com';
  }

  /**
   * Default view
   *
   * @access public
   * @return void
   */
  public function index()
  {
    $this->data['message'] = '<!-- ajax -->';
    $this->load->view('hotajax', $this->data);
  }
  
  /**
   * Generate a captcha image
   *
   * @access public
   * @return void
   */
  public function captcha()
  {
    $this->load->library('captcha');
    
    $this->captcha_folder = 'asset/valid_captcha';
    $this->captcha_table = 'cms_dCaptcha';
    
    // clear outdated images & records
    $this->model->captcha_clear();
    
    // create a new image
    $vals = array(
      //'word'       => 'Random word',
      'img_path'   => './application/'.$this->application_name.'/'.$this->captcha_folder.'/',
      'img_url'    => (empty($_SERVER['HTTPS'])? 'http://':'https://').$_SERVER['HTTP_HOST'].'/'.$this->captcha_folder.'/',
      'font_path'  => './application/'.$this->application_name.'/asset/font/angelina.ttf',
      'img_width'  => '225',
      'img_height' => '60',
      'expiration' => '3600'
    );
    $cap = $this->captcha->create_captcha($vals);
    $this->data['captcha_image'] = $cap['image'];
    // insert a new record into DB
    $this->model->captcha_insert($cap['time'], $this->input->ip_address(), $cap['word']);
    // load view
    $this->load->view('ajax_captcha', $this->data);
  }
    
}
?>
