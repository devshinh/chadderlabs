<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_item_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('news/news', TRUE);
    $this->load->model('news/news_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    $data['has_permission'] = has_permission('manage_news');
    if (!$data['has_permission']) {
      return '<p>You do not have permission to manage news.</p>';
    }

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['title'] = trim($args['title']);
      return $settings;
    }

    // build the form
    $data['title'] = array(
      'name'  => 'title',
      'id'    => 'title',
      'type'  => 'text',
      'value' => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
    );

    // load widget view
    return $this->render('news_item_admin', $data);
  }

}
?>