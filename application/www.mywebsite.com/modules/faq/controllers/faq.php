<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Faq extends HotCMS_Controller {

  // module information
  protected $aModuleInfo;
    
  /**
   * Constructor method
   * @access public
   * @return void
   */
  public function __construct()
  {
    // call the parent's constructor method
    parent::__construct();
    // Load the required classes
    $this->load->config('faq', TRUE);
    $this->load->model('model_faq');
    
    // prepare module information
    $this->aModuleInfo = array(
      'sName'            => 'faq',
      'sTitle'           => $this->config->item('module_title', 'faq'),
      'sURL'             => $this->config->item('module_url', 'faq'),
      'sMetaDescription' => $this->config->item('meta_description', 'faq'),
      'sMetaKeyword'     => $this->config->item('meta_keyword', 'faq'),
      'sStyleSheet'      => $this->config->item('css', 'faq'),
      'sJavaScript'      => $this->config->item('js', 'faq')
    );
  }

  /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function index()
  {
    $this->load->helper('faq');
    if (function_exists('faq_list')) {
      print faq_list();
    }
    /*
    //set the flash data error or notice messages if any
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
    
    $this->data['faqs'] = $this->model_faq->get_faqs();
    $this->data['faq_groups'] = $this->model_faq->get_faq_groups();
    
    // set the page sub-title for SEO
    $this->aModuleInfo['sSubTitle'] = 'FAQ';
    $this->aModuleInfo['sTitle'] = 'Support';
    
    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data );
    //$this->output->enable_profiler(TRUE);
    */
  } 
}
?>