<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class News_preview_widget_admin extends Widget {

  public function run($args = array()) {
    $this->load->config('news/news', TRUE);
    $this->load->model('news/news_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permission
    $data['userid'] = (int) ($this->session->userdata("user_id"));
    $data['has_permission'] = has_permission('manage_news');
    if (!$data['has_permission']) {
      return '<p>You do not have permission to manage news.</p>';
    }

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['title'] = trim($args['title']);
      $settings['preview_type'] = trim($args['preview_type']);
      return $settings;
    }

    // build the form
    $data['title'] = array(
        'name' => 'title',
        'id' => 'title',
        'type' => 'text',
        'value' => array_key_exists('title', $args) ? set_value('title', $args['title']) : NULL,
    );
    $saved_type = '';
    if (isset($args['preview_type']))
      $saved_type = $args['preview_type'];
    $data['preview_type_archive'] = array(
        'name' => 'preview_type',
        'id' => 'preview_type_archive',
        'value' => 'archive',
        'checked' => $saved_type == 'archive' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );
    $data['preview_type_homepage'] = array(
        'name' => 'preview_type',
        'id' => 'preview_type_homepage',
        'value' => 'homepage',
        'checked' => $saved_type == 'homepage' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );
    $data['preview_type_training_item'] = array(
        'name' => 'preview_type',
        'id' => 'preview_type_training_item',
        'value' => 'training_item',
        'checked' => $saved_type == 'training_item' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );

    // load widget view
    return $this->render('news_preview_admin', $data);
  }

}

?>