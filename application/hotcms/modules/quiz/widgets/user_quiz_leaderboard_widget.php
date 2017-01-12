<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');


function points_sort($a, $b) {
  return ($a->points < $b->points);
  }
class User_quiz_leaderboard_widget extends Widget {

  public function run($args = array()) {
    $this->load->config('quiz/quiz', TRUE);
    $this->load->library('quiz/CmsQuiz');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'quiz');
    $data['css'] = $this->config->item('css', 'quiz');
    $module_title = 'Quiz Leaderboard';

    // check permission
    $data['userid'] = (int) ($this->session->userdata("user_id"));
    if (!has_permission('view_quiz')) {
      return '<p>You do not have permission to access quiz.</p>';
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
    //if ($data['environment'] == 'admin_panel') {
    $args['slug'] = CmsQuiz::random_slug();
    //}
    //load user list with quiz points
    $user_list = CmsQuiz::load_user_points_list();

    if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
      $slug = $args['slug'];
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      $data['error'] = $this->session->flashdata('error');

      if ($user_list > '') {
        //sort users by points
        usort($user_list, 'points_sort');
        $data['users'] = $user_list;
        // load a published item
        return array(
            'meta_subtitle' => 'Points Leaderboard',
            'content' => $this->render('user_leaderboard', $data),
        );
      }

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return '<p>No quiz results not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}

?>