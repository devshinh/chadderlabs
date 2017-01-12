<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Quiz_list_widget extends Widget {

  public function run($args = array()) {
    $this->load->library('session');
    $this->load->config('quiz/quiz', TRUE);
    $this->load->model('quiz/quiz_model');
    $data = array();
    $data['js'] = $this->config->item('js', 'quiz');
    $data['css'] = $this->config->item('css', 'quiz');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Quiz Item List';

    // check permissions
    $data['userid'] = (int) ($this->session->userdata("user_id"));
    if (!has_permission('view_quiz')) {
      return '<p>You do not have permission to access quiz.</p>';
    }

    //if (is_array($args) && count($args) > 0 && array_key_exists('quiz_id', $args)) {
    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      $data['items'] = array();
      if (array_key_exists('types', $args)) {
        foreach ($args['types'] as $type_id) {
          $types[$type_id] = $type_id;
        }


        foreach ($types as $type) {
          $category_id = $type; //$category->id;
          $data['items'][$category_id] = $this->quiz_model->list_all_quiz($category_id, TRUE);
        }
      }
      // load widget view
      return $this->render('quiz_list', $data);
    } else {

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return '<p>Quiz not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}

?>