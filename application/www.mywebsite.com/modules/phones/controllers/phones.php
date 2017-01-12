<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Phones extends HotCMS_Controller {

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
    $this->load->config('phones', TRUE);
    //$this->load->library('session');
    //$this->load->helper('language');
    //$this->lang->load('phones');
    $this->load->model('model_phones');
    
    // prepare module information
    $this->aModuleInfo = array(
      'sName'            => 'phones',
      'sTitle'           => $this->config->item('module_title', 'phones'),
      'sURL'             => $this->config->item('module_url', 'phones'),
      'sMetaDescription' => $this->config->item('meta_description', 'phones'),
      'sMetaKeyword'     => $this->config->item('meta_keyword', 'phones'),
      'sStyleSheet'      => $this->config->item('css', 'phones'),
      'sJavaScript'      => $this->config->item('js', 'phones')
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
    
    $this->data['aPhones'] = $this->model_phones->get_phones();

    $this->data['aStatements'] = $this->model_phones->get_statements();
    
    $this->data['aPhoneAd'] = $this->model_phones->get_phone_ad();
   
    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data );
    //$this->output->enable_profiler(TRUE);
  }
  
    /**
   * Displaying method for phone detail Page
   * @access public
   * @return void
   */
  public function detail($phoneSlug='')
  {
    //set the flash data error or notice messages if any
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
    
    $this->data['aPhone'] = $this->model_phones->get_phone($phoneSlug);
    
    if ($this->data['aPhone'][0]->nPhoneID < 1){
      redirect('page-not-found');
      return false;
    }
    
    // set the page sub-title for SEO
    $this->aModuleInfo['sSubTitle'] = $this->data['aPhone'][0]->sName;
    
    $this->data['aPhonePromo'] = $this->model_phones->get_phone_promo($this->data['aPhone'][0]->nPhoneID);
    
    $imageTypeID_phone_asset = 10;
    $moduleID = 31;
     
    foreach ($this->model_phones->get_phone_assets( $this->data['aPhone'][0]->nPhoneID ) as $row) {
      $row = $this->model->select_row( 'dImage', 0, array( $imageTypeID_phone_asset, $moduleID, $row->nAssetID ) );
      $this->data['aAssetImage'][]  = $row;
    }
     
    $this->aModuleInfo['sStyleSheet'] = $this->aModuleInfo['sStyleSheet'] .' phones-detail.css'; 
     
    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data, 'detail' );
    //$this->output->enable_profiler(TRUE);
  }
  
}
?>