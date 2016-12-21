<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends HotCMS_Controller {

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
    $this->load->config('contact', TRUE);
    $this->load->model('model_contact');
    
    // prepare module information
    $this->aModuleInfo = array(
      'name'            => 'contact',
      'title'           => $this->config->item('module_title', 'contact'),
      'url'             => $this->config->item('module_url', 'contact'),
      'meta_description' => $this->config->item('meta_description', 'contact'),
      'meta_keyword'     => $this->config->item('meta_keyword', 'contact'),
      'style_sheet'      => $this->config->item('css', 'contact'),
      'javascript'      => $this->config->item('js', 'contact')
    );
  }

  /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function index()
  {
    $this->load->helper('contact');
    if (function_exists('contact_us_form')) {
      print contact_us_form();
    }
    
    /*
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
    
    // Validation rules
    $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
    //$this->form_validation->set_rules('lastname',  'Last Name', 'trim|required|xss_clean');
    $this->form_validation->set_rules('email',     'Email Address', 'trim|required|valid_email|xss_clean');
    $this->form_validation->set_rules('postal',     'Postal Code', 'trim|required|xss_clean');
    //$this->form_validation->set_rules('terms',     'Terms',      'callback__validator_terms' );
    
    if ($this->form_validation->run())
    {
      $firstname = $this->input->post('firstname');
      $lastname = $this->input->post('lastname');
      $email = $this->input->post('email');
      $postal = $this->input->post('postal');
      $concerns = $this->input->post('concerns');
      $comment = $this->input->post('comment');
      $terms = $this->input->post('terms');
      
      $result = $this->model_contact->notice_email($firstname, $lastname, $email, $postal, $concerns, $comment);
      $this->session->set_flashdata('postal',$postal);
      $this->session->set_flashdata('concerns',$concerns);
	  
      if($result) {
        if ($email>'' && $terms==1){
          $result = $this->model->register_newsletter($firstname, $lastname, $email, $postal, $phone, $nonumber, 'contact');
        }
      }
      else{
      	$this->session->set_flashdata('<p>Failed to submit the contact form.</p>');
      }
      redirect('/contact-us-confirm', 'refresh');
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
      $this->data['concerns'] = array('name' => 'concerns',
                                'id'      => 'concerns',
                                'type'    => 'checkbox',
                                'value'   => $this->form_validation->set_value('concerns'),
                               );
      $this->data['comment'] = array('name' => 'comment',
                                'id'      => 'comment',
                                'type'    => 'textarea',
                                'value'   => $this->form_validation->set_value('comment'),
                                'cols'    => '40',
                                'rows'    => '5',
                               );
      $this->data['terms'] = array('name' => 'terms',
                                'id'      => 'terms',
                                'type'    => 'checkbox',
                                'value'   => $this->form_validation->set_value('terms'),
                               );
      // load module view
      //self::loadModuleView( $this->aModuleInfo, $this->data );
      $this->load->view('index', $this->data);
    }
   */
  }
    
  /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function confirm()
  {
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
    $this->data['postal'] = $this->session->flashdata('postal');
    $concerns = $this->session->flashdata('concerns');
    $this->data['concerns'] = is_array($concerns) && count($concerns) ? implode(', ', $concerns) : "";
    // load module view
    //self::loadModuleView( $this->aModuleInfo, $this->data, 'submit_success' );
    $this->load->view('submit_success', $this->data);
  }
}
?>