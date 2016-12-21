<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Quiz_preview_widget extends Widget {

  public function run($args = array())
  {
    $this->load->config('quiz/quiz', TRUE);
    $this->load->library('quiz/CmsQuiz');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'quiz');
    $data['css'] = $this->config->item('css', 'quiz');
    $module_title = 'Quiz Product Preview';

    // check permission
    $data['userid'] = (int) ($this->session->userdata("user_id"));
    if (!has_permission('view_quiz')) {
      return array('content' => '<p>Login to see widget content</p>');
      //return '<p>You do not have permission to access quiz.</p>';
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
    if ($data['environment'] == 'admin_panel') {
      $args['slug'] = CmsQuiz::random_slug();
    }

    if (is_array($args) && count($args) > 0 && array_key_exists('title', $args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      $data['error'] = $this->session->flashdata('error');
      //load quiz slugs
      $product_slug = $args['slug'];

      $slug_array = CmsQuiz::quiz_slugs_by_product($product_slug);

      if (is_array($slug_array) && count($slug_array) > 0) {
        $items = array();
        foreach ($slug_array as $row) {
          $item = new CmsQuiz($row->slug, TRUE);
          $item->user_id = $data['userid'];
          $items[$item->id] = $item;
        }
        $data['items'] = $items;

        // load widget view
        return array('content' => $this->render('quiz_preview', $data));
      }
      else {
        // item not found. set 404 status
        //$this->output->set_status_header('404');
        return array('content' => '');
      }

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return array('content' => '<p>No found quiz.</p>');
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}

?>