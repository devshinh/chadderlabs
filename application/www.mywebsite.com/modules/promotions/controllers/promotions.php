<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Promotions extends HotCMS_Controller {

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
    $this->load->config('promotions', TRUE);
    
    //$this->load->library('session');
    //$this->load->helper('language');
    //$this->lang->load('promotions');
    $this->load->model('model_promotions');
    
    // prepare module information
    $this->aModuleInfo = array(
      'sName'            => 'promotions',
      'sTitle'           => $this->config->item('module_title', 'promotions'),
      'sURL'             => $this->config->item('module_url', 'promotions'),
      'sMetaDescription' => $this->config->item('meta_description', 'promotions'),
      'sMetaKeyword'     => $this->config->item('meta_keyword', 'promotions'),
      'sStyleSheet'      => $this->config->item('css', 'promotions'),
      'sJavaScript'      => $this->config->item('js', 'promotions')
    );

    //TODO: check if this module is enabled and the permissions are correct
    //if (!isEnabled() || !hasPermission()) {
    //  redirect( 'page-not-found' );
    //}
    
    @include(APPPATH.'config/routes.php');
  }

  /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function index()
  {
    //set the flash data error or notice messages if any
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

    $this->data['main_promotion'] = $this->model_promotions->get_main_promotion();

    $this->data['small_promotions'] = $this->model_promotions->get_small_promotions();

    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data );
    //$this->output->enable_profiler(TRUE);
  }
  
    /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function detail($promoSlug='')
  {
    //set the flash data error or notice messages if any
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
    
    $this->data['promotion'] = $this->model_promotions->get_promotion($promoSlug);

    if (count($this->data['promotion']) < 1){
      redirect('page-not-found');
      return false;
    }
    
    // set the page sub-title for SEO
    $this->aModuleInfo['sSubTitle'] = $this->data['promotion'][0]->sName;
    
    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data, 'detail' );
    //$this->output->enable_profiler(TRUE);
  }
  
}
?>