<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Site_list_widget extends Widget {

    public function run($args = array()) {
        $this->load->library('session');
        $this->load->config('site/site', TRUE);
        $this->load->model('site/site_model');

        
        $this->load->helper('asset/asset');
        
        $this->load->library('quiz/CmsQuiz');
        $this->load->model('quiz/quiz_model');
        
        $data = array();
        $data['js'] = $this->config->item('js', 'site');
        $data['css'] = $this->config->item('css', 'site');
        $data['environment'] = $this->config->item('environment');
        $module_title = 'Brand List';

        // check permissions
        // unregistered users can view sites, but should not be able to bid on sites
        $data['userid'] = (int) ($this->session->userdata("user_id"));
        if (!has_permission('view_content')) {
            return array('content' => '<p>You do not have permission to access sites.</p>');
        }

        //if (is_array($args) && count($args) > 0 && array_key_exists('site_id', $args)) {
        if (is_array($args)) {
          if (array_key_exists('title', $args)) {
              $data['title'] = $args['title'];
          }         
          //site list for logged users 
            if($data['userid'] != 0){      
              $sites = $this->site_model->get_all_sites_for_account($data['userid']);
              foreach ($sites as $site) {
                  if ($site->site_image_id != 0) {  
                    $site->image = asset_load_item($site->site_image_id);
                  }
                  //get available points for logged user
                  //get all trainings for lab
                  $quizzes = $this->quiz_model->list_all_quizzes_for_site($site->id);
                  $lab_max_points = 0;
                  $lab_user_points = 0;
                  $lab_max_ce = 0;
                  $lab_user_ce = 0;

                  foreach($quizzes as $q){
                      $quiz = new CmsQuiz($q->id, TRUE, FALSE);
                      $quiz->user_id = $data['userid']; 

                      $lab_max_points += $quiz->max_points;
                      $lab_user_points += $quiz->user_points;
                      $lab_max_ce += $quiz->max_contest_entries;
                      $lab_user_ce += $quiz->user_contest_entries;                  

                      //$user_lab_points +=  $training->get_user_points();
                  }

                  $available_points[$site->id] = $lab_max_points - $lab_user_points;
                  $available_ce[$site->id] = $lab_max_ce - $lab_user_ce;
              }
                arsort($available_points);

                 $data['available_points']= $available_points;
                 $data['available_ce']= $available_ce;


              $data['sites'] = $sites;
              return array('content' => $this->render('widget_site_list', $data));
            }else{
                //display link for login page
                $login = '<div class="hero-unit">Please <a href="/login">login</a> to access Labs list.</div>';
                return array('content' => $login);
            }
        } else {
        // if anything goes wrong, return 404
                $this->output->set_status_header('404');
                redirect('page-not-found');
          return array('content' => '<p>Not found.</p>');
        }

        if ($data['environment'] == 'admin_panel') {
            return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
        }
    }

}

?>