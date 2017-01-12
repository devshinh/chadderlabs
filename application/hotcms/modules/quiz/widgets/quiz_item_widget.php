<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Quiz_item_widget extends Widget {

  public function run($args = array())
  {
    $this->load->config('quiz/quiz', TRUE);
    $this->load->library('quiz/CmsQuiz');
    $this->load->library('training/CmsTraining');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'quiz');
    $data['css'] = $this->config->item('css', 'quiz');
    $module_title = 'Quiz Detail';

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_quiz')) {
      return array(
        'content' => '<div class="hero-unit"><p>You have to have user account to take a quiz.<br /> Please <a href="/login">login</a> or <a href="/register">register</a>.</p></div>',
        'meta_subtitle' => 'Quiz Detail'
      );
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
    if ($data['environment'] == 'admin_panel') {
      $args['slug'] = CmsQuiz::random_slug();
    }

    if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
      $slug = $args['slug'];
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      $data['error'] = $this->session->flashdata('error');

      if ($slug > '') {
        // is this a preview?
        $preview = get_cookie('preview_quiz');
        if ($preview > '' && strpos($preview, ':') > 0) {
          $ids = explode(':', $preview);
          $quiz_id = $ids[0];
          $rev_id = $ids[1];
          $rev_slug = $ids[2];
          if ($rev_slug == $slug) {
            if ($rev_id > 0) {
              // preview a revision
              $quiz = new CmsQuiz($quiz_id);
              $item = $quiz->get_revision($rev_id);
              if ($item) {
                $item->title .= ' [revision: ' . date('Y-m-d H:i:s', $item->create_timestamp) . ']';
              }
            }
            else {
              // preview a quiz
              $quiz = new CmsQuiz($quiz_id);
              $item = $quiz->type;
              if ($item) {
                $item->title .= ' [quiz preview]';
              }
            }
          }
          else {
            $item = new CmsQuiz($slug, TRUE);
          }
        }
        else {
          // load a published item
          $item = new CmsQuiz($slug, TRUE);
        }
        if (!$item || $item->id == 0) {
          // item not found. set 404 status
          $this->output->set_status_header('404');
          return '<div class="hero-unit"><p>Quiz not found.</p></div>';
        }

        $data['item'] = $item;
        if ($this->input->post() == FALSE) {
          // check how many attempts this user had made before
          $under_limits = $item->quiz_history_check_attempts($data['userid']);
          if (!$under_limits) {
            $content = '<p>' . $item->errors() . '</p>';
          }
          else {
            // display the quiz questions, but only on the front end
            if ($this->input->get('start') == 'yes' && $data['environment'] != 'admin_panel') {
              // initialize a quiz history, and display randomly generated questions
              $quiz_history = $item->quiz_start($data['userid']);
              if (!$quiz_history || $quiz_history->id < 1) {
                // item not found. set 404 status
                $this->output->set_status_header('404');
                return '<div class="hero-unit"><p>Failed to initialize a quiz.</p></div>';
              }
              // load widget view
              $data['history'] = $quiz_history;
              $content = '<div id="quiz_form_wrapper"><form method="post" id="quiz_form">';
              $content .= $this->render('quiz_questions', $data);
              $content .= "</form></div>\n";
            }
            else {
              // display rules and a welcome screen
              $content = $this->render('quiz_welcome', $data);
            }
          }
        }
        else {
          // results submited
          $quiz_history_id = (int)($this->input->post('qhid'));
          if ($quiz_history_id == 0) {
            // item not found. set 404 status
            $this->output->set_status_header('404');
            return '<div class="hero-unit"><p>Invalid quiz history ID.</p></div>';
          }
          $training = new CmsTraining($item->training_id, TRUE);
          if (!$training || $training->id == 0) {
            // item not found. set 404 status
            $this->output->set_status_header('404');
            return '<div class="hero-unit"><p>Training subject not found.</p></div>';
          }
          $data['training'] = $training;
          $user_answers = $this->input->post();
          $result = $item->quiz_finish($quiz_history_id, $data['userid'], $user_answers);
          if (!$result) {
            $data['error'] = $item->errors();
          }
          $quiz_history = $item->quiz_history($quiz_history_id);
          if (!$quiz_history || $quiz_history->id == 0) {
            // item not found. set 404 status
            $this->output->set_status_header('404');
            return '<div class="hero-unit"><p>Quiz history not found.</p></div>';
          }
          $data['history'] = $quiz_history;
          // load widget view
          $content = '<div id="quiz_results">';
          $content .= $this->render('quiz_results', $data);
          $content .= "</div>\n";
        }

        $content = '<div class="hero-unit">' . $content . '</div>';
        return array(
          'meta_subtitle' => $item->name,
          'content' => $content
        );
      }

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return '<div class="hero-unit"><p>Quiz not found.</p></div>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<div class="hero-unit"><p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p></div>';
    }
  }

}

?>