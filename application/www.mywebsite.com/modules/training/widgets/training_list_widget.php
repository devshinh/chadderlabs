<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Training_list_widget extends Widget {

  public function run($args = array())
  {
    $this->load->library('session');
    $this->load->config('training/training', TRUE);
    $this->load->library('training/CmsTraining');
    $this->load->model('training/training_model');
    $data = array();
    $data['js'] = $this->config->item('js', 'training');
    $data['css'] = $this->config->item('css', 'training');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Training Item List';

    // check permissions
    $data['userid'] = (int) ($this->session->userdata("user_id"));
    if (!has_permission('view_training')) {
      return '<p>You do not have permission to access training.</p>';
    }

    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      $category_id = 0; //$category->id; 0 = all categories
      $all_slugs = $this->training_model->list_all_training($category_id, TRUE);

      foreach ($all_slugs as $slug) {

        $item = new CmsTraining($slug->slug, TRUE);
        $items[$item->id] = $item;
      }
      $data['items_all'] = $items;

      // cheddar available -> all trainings where user doesn't have 100%
      foreach ($all_slugs as $slug) {

        $item = new CmsTraining($slug->slug, TRUE);
        $item->user_id = $data['userid']; // set user ID for points calculation
        //var_dump($item->points_percent);
            if($item->get_has_quiz()){
                if ($item->highest_percent_score == 100) {
                    $complete_lab[$item->id] = $item;
                }
                else {
                    $uncomplete_lab[$item->id] = $item;          
                }
            }
        }
      $data['uncomplete_lab'] = isset($uncomplete_lab)?$uncomplete_lab:'';
      $data['complete_lab'] = isset($complete_lab)?$complete_lab:'';
      // load widget view
        // load widget view
        if (isset($args['list_type']) && $args['list_type'] == 'all_labs') {
          return array(
            'content' => $this->render('training_list_all', $data)
          );
        }
        if (isset($args['list_type']) && $args['list_type'] == 'uncomplete_labs') {
          return array('content' => $this->render('training_list_uncomplete', $data));
        }
        if (isset($args['list_type']) && $args['list_type'] == 'complete_lab') {
          //just product with quiz
          return array('content' => $this->render('training_list_complete', $data));
        }      
      return array('content' => $this->render('training_list', $data));

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return '<p>Training not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}

?>
