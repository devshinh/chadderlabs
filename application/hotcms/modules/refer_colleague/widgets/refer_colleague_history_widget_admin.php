<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Refer_colleague_history_widget_admin extends Widget {

  public function run( $args=array() )
  {
    //  die('reffer');
    $this->load->config('refer_colleague/refer_colleague', TRUE);
    $this->load->model('refer_colleague/refer_colleague_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    //$data['has_permission'] = has_permission('manage_quote');
    //if (!$data['has_permission']) {
    //  return '<p>You do not have permission to manage quotes.</p>';
    //}

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['title'] = trim($args['title']);
      $settings['form_id'] = (int)($args['form_id']);
      return $settings;
    }

    $form_id = 0;
    if (is_array($args) && array_key_exists('form_id', $args)) {
      $form_id = (int)($args['form_id']);
    }
    /*
    $data['form_id'] = $form_id;
    $quote_forms = $this->quote_model->quote_list();
    $form_array = array('' => ' -- select quote form -- ');
    foreach ($quote_forms as $row) {
      $form_array[$row->id] = $row->name;
    }
    $data['form_array'] = $form_array;
*/
    // build the form
    $data['title'] = array(
      'name'  => 'title',
      'id'    => 'title',
      'type'  => 'text',
      'value' => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
    );

    // load widget view
    return $this->render('refer_colleague_form_admin', $data);
  }

}
?>