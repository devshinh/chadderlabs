<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Newsletter extends HotCMS_Controller {

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
    $this->load->config('newsletter', TRUE);
    //$this->load->library('session');
    $this->load->model('model_newsletter');

    // prepare module information
    $this->aModuleInfo = array(
      'sName'            => 'newsletter',
      'sTitle'           => $this->config->item('module_title', 'newsletter'),
      'sURL'             => $this->config->item('module_url', 'newsletter'),
      'sMetaDescription' => $this->config->item('meta_description', 'newsletter'),
      'sMetaKeyword'     => $this->config->item('meta_keyword', 'newsletter'),
      'sStyleSheet'      => $this->config->item('css', 'newsletter'),
      'sJavaScript'      => $this->config->item('js', 'newsletter')
    );

    @include(APPPATH.'config/routes.php');
  }

  /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function index()
  {
    $this->data['sTitle'] = "Be Notified About Our Specials and New Releases";
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

    // Validation rules
    $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
    //$this->form_validation->set_rules('lastname',  'Last Name', 'trim|required|xss_clean');
    $this->form_validation->set_rules('email',     'Email', 'trim|required|filter_var|xss_clean');
    $this->form_validation->set_rules('terms',     'Terms',      'callback__validator_terms' );
    
    if ($this->form_validation->run())
    {
      $firstname = $this->input->post('firstname');
      $lastname = $this->input->post('lastname');
      $email = $this->input->post('email');
      $postal = $this->input->post('postal');
      $phone = $this->input->post('phone');
      $nonumber = $this->input->post('nonumber');
      $terms = $this->input->post('terms');

      if($result = $this->model_newsletter->register($firstname, $lastname, $email, $postal, $phone, $nonumber))
      {
      	$this->session->set_flashdata('postalcode', $postal);
        // load module view
        redirect('/sign-up-confirm');
      }
      else{
        $this->data['error'] = '<p>Failed to submit the sign up form.</p>';
      }
    }
    else {
      // Return the validation error
      if (validation_errors()>''){
        $this->data['error'] = validation_errors();
      }

      $this->data['firstname'] = array('name' => 'firstname',
                                'id'      => 'firstname',
                                'type'    => 'text',
                                'value'   => $this->form_validation->set_value('firstname'),
                               );
      $this->data['lastname'] = array('name' => 'lastname',
                                'id'      => 'lastname',
                                'type'    => 'text',
                                'value'   => $this->form_validation->set_value('lastname'),
                               );
      $this->data['email'] = array('name' => 'email',
                                'id'      => 'email',
                                'type'    => 'text',
                                'value'   => $this->form_validation->set_value('email'),
                               );
      $this->data['postal'] = array('name' => 'postal',
                                'id'      => 'postal',
                                'type'    => 'text',
                                'value'   => $this->form_validation->set_value('postal'),
                               );
      $this->data['phone'] = array('name' => 'phone',
                                'id'      => 'phone',
                                'type'    => 'text',
                                'value'   => $this->form_validation->set_value('phone'),
                               );
      $this->data['nonumber'] = array('name' => 'nonumber',
                                'id'      => 'nonumber',
                                'type'    => 'checkbox',
                                'value'   => $this->form_validation->set_value('nonumber'),
                               );
      $this->data['terms'] = array('name' => 'terms',
                                'id'      => 'terms',
                                'type'    => 'checkbox',
                                'value'   => $this->form_validation->set_value('terms'),
                               );
      // load module view
      self::loadModuleView( $this->aModuleInfo, $this->data );
    }
  }

  /**
   * Confirm sign up displaying method
   * @access public
   * @return void
   */
  public function confirm_signup()
  {
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
    $this->data['postalcode'] = $this->session->flashdata('postalcode');

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
    self::loadModuleView( $this->aModuleInfo, $this->data, 'signup_success' );
  }

  public function _validator_terms() {
    $isValid = true;
    // if terms not checked / accepted
    if ( !$this->input->post( 'terms' ) ) {
      // assign error message
      $this->form_validation->set_message( '_validator_terms', 'You must read and accept the privacy policy and terms.' );
      $isValid = false;
    }
    return $isValid;
  }

}
?>
