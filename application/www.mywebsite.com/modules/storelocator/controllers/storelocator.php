<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Storelocator extends HotCMS_Controller {

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
    $this->load->database();
    $this->load->config('storelocator', TRUE);
    //$this->load->library('session');
    $this->load->helper('language');
    $this->lang->load('storelocator');
    $this->load->model('model_storelocator');
    
    // prepare module information
    $this->aModuleInfo = array(
      'sName'            => 'storelocator',
      'sTitle'           => $this->config->item('module_title', 'storelocator'),
      'sURL'             => $this->config->item('module_url', 'storelocator'),
      'sMetaDescription' => $this->config->item('meta_description', 'storelocator'),
      'sMetaKeyword'     => $this->config->item('meta_keyword', 'storelocator'),
      'sStyleSheet'      => $this->config->item('css', 'storelocator'),
      'sJavaScript'      => $this->config->item('js', 'storelocator')
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
    
    $this->data['stores'] = $this->model_storelocator->get_stores();
    $this->data['provinces'] = $this->model_storelocator->get_provinces();
    $this->data['cities'] = $this->model_storelocator->get_cities();
    
    $this->data['provinceCode'] = array(
      'AB' => 'Alberta',
      'BC' => 'British Columbia',
      'MB' => 'Manitoba',
      'NB' => 'New Brunswick',
      'NL' => 'Newfoundland and Labrador',
      'NS' => 'Nova Scotia',
      'NT' => 'Northwest Territories',
      'NU' => 'Nunavut',
      'ON' => 'Ontario',
      'PE' => 'Prince Edward Island',
      'QC' => 'Quebec',
      'SK' => 'Saskatchewan',
      'YT' => 'Yukon'
    );
    
    // load promo image
    $this->load->model( 'model_ad' );
    $adID = 2;
    if (!empty($adID)){
      $this->data['oAd'] = $this->model_ad->select_row( 'dAd', 1, array( $adID ) );
    }
    // load promo carousel
    $this->data['oCarousel']->aPromos = array();
    $this->load->model( 'model_carousel_content' );
    $moduleID = $this->model->select_row( 'dModule', 4, 'carousel_content' )->nModuleID;
    foreach ($this->model_carousel_content->select_result( 'dCarouselContent', 1, array( $this->session->userdata( 'siteID' ), $moduleID, 'main' )) as $row) {
      $this->data['oCarousel']->aPromos[] = $row; 
    }
    // load text carousel
    $this->data['oCarousel']->aStatements = array();   
    $this->load->model( 'model_statement' );
    $moduleID = 21; //$this->model->select_row( 'dModule', 4, 'statement' )->nModuleID;
    $imageTypeID = 4;
    foreach ($this->model_statement->select_result( 'dStatement', 1, array( $this->session->userdata( 'siteID' ), $moduleID, 'product', $imageTypeID )) as $row) { 
      $this->data['oCarousel']->aStatements[] = $row; 
    }    

    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data );
    //$this->output->enable_profiler(TRUE);
  }
  
}
?>