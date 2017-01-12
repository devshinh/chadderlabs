<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_activity_widget extends Widget {

    protected function time_elapsed_string($ptime) {
        $etime = time() - $ptime;

        if ($etime < 1) {
            return '0 seconds';
        }

        $a = array(12 * 30 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str . ($r > 1 ? 's' : '');
            }
        }
    }

    public function run($args = array()) {
        $this->load->config('user/user', TRUE);
        $this->load->model('user/user_model');

        $this->load->model('product/product_model');
        $this->load->model('shop/shop_model');
        $this->load->model('user/user_model');
        
        $this->load->model('badge/badge_model');

        //$this->load->library('asset/asset_item');
        $this->load->library('training/CmsTraining');
        $this->load->library('quiz/CmsQuiz');
        $data = array();
        $data['environment'] = $this->config->item('environment');
        $data['js'] = $this->config->item('js', 'user');
        $data['css'] = $this->config->item('css', 'user');
        $module_title = 'User Activity';

        // check permission
        $data['userid'] = (int) ($this->session->userdata("user_id"));
        if (!has_permission('view_content')) {
            //return array('content' => '<p>You do not have permission to access this widget.</p>');
            return array('content' => '');
        }

        if (is_array($args) && array_key_exists('title', $args)) {
            $data['title'] = $args['title'];
        }
        if (is_array($args) && array_key_exists('user_id', $args)) {
            $data['user_id'] = $args['user_id'];
        }        
        if (is_array($args) && array_key_exists('limit', $args)) {
            $limit = (int) $args['limit'];
        } else {
            $limit = 10; // default value
        }
        $data['limit'] = $limit;
        
        $restricted = 0;
        if (is_array($args) && array_key_exists('site_restricted', $args)) {
          $restricted = (int)$args['site_restricted'];
        } 
        $data['restricted'] = $restricted;

        $data['error'] = $this->session->flashdata('error');

        //load user activity list

        $a_uri = explode('/', uri_string());
        /*
        if (uri_string() == 'profile') {
            $activity_list = $this->user_model->list_user_activity_profile($limit, $data['userid']);
        } elseif ($a_uri[0] == 'public-profile') {
            if (is_array($args) && array_key_exists('user_info', $args)) {
                $data['user_info'] = $args['user_info'];
            }

            $activity_list = $this->user_model->list_user_activity_profile($limit, $data['user_info']->user_id);
        } else {
            $activity_list = $this->user_model->list_user_activity($limit, $restricted);
        }
         */
        if(!empty($data['user_id'])){
          $activity_list = $this->user_model->list_user_activity_profile($limit, $data['user_id']);
        }else{
          $activity_list = $this->user_model->list_user_activity_profile($limit,46);
        }
        foreach ($activity_list as $item) {
            $item->time_ago = $this->time_elapsed_string($item->create_timestamp);
            // when it is a quiz we need more info: quiz type name, training item name and featured image for item
            if ($item->point_type == 'quiz') {
                $quiz = new CmsQuiz();
                $quiz_history = $quiz->quiz_history($item->ref_id);
                if (!empty($quiz_history)) {
                    $quiz = new CmsQuiz($quiz_history->quiz_id, FALSE, FALSE);
                    $item->quiz_type_name = new stdClass();
                    $item->quiz_type_name = $quiz->type->name;
                    $training = new CmsTraining($quiz->training_id, FALSE, FALSE);                 
                    $item->training_domain = new stdClass();
                    $item->training_slug = new stdClass();
                    $item->training_title = new stdClass();
                    $item->training_featured_image_full_path = new stdClass();
                    $item->training_site_id = new stdClass();
                    
                    $item->training_domain = $training->domain;                    
                    $item->training_slug = $training->slug;
                    $item->training_title = $training->title;     
                    $thumb = sprintf('http://%s%s/%s_thumb.%s', $training->domain, $training->featured_image->thumb,$training->featured_image->file_name,$training->featured_image->extension);
                    $item->training_featured_image_full_path = $thumb;
                    $item->training_site_id = $training->site_id; 
                }
            }
            // when it is a quiz we need more info: quiz type name, training item name and featured image for item
            elseif ($item->point_type == 'quiz-draw') {
                $quiz = new CmsQuiz();
                $quiz_history = $quiz->quiz_history($item->ref_id);
                if (!empty($quiz_history)) {
                    $quiz = new CmsQuiz($quiz_history->quiz_id, FALSE, FALSE);
                    $item->quiz_type_name = new stdClass();
                    $item->quiz_type_name = $quiz->type->name;
                    $training = new CmsTraining($quiz->training_id, FALSE, FALSE);                 
                    $item->training_domain = new stdClass();
                    $item->training_slug = new stdClass();
                    $item->training_title = new stdClass();
                    $item->training_featured_image_full_path = new stdClass();
                    
                    $item->training_domain = $training->domain;                    
                    $item->training_slug = $training->slug;
                    $item->training_title = $training->title;     
                   // $item->training_featured_image_full_path = $training->featured_image->full_path;
                    $thumb = sprintf('http://%s%s/%s_thumb.%s', $training->domain, $training->featured_image->thumb,$training->featured_image->file_name,$training->featured_image->extension);
                    $item->training_featured_image_full_path = $thumb;                    
                }
            }            
            elseif ($item->point_type == 'order') {
                $order = $this->shop_model->load_order($item->ref_id);
                foreach ($order->items as $order_item) {
                    $product_id = $order_item->product_id;
                }
                $product = $this->product_model->get_product($product_id);
                $item->product_featured_image_thumb_html = new stdClass();
               // $item->product_featured_image_thumb_html = $product->featured_image->thumb_html;
                $item->product_slug = new stdClass();
                $item->product_slug = $product->slug;
            }
            elseif ($item->point_type == 'reffer_colleague') {
                $avatar = $this->user_model->get_user_avatar($item->avatar_id);
                $item->avatar = $avatar;
                
            }  
            elseif ($item->point_type == 'reffer_veri') {
                $avatar = $this->user_model->get_user_avatar($item->avatar_id);
                $item->avatar = $avatar;
            }elseif ($item->point_type == 'badge') {
                $badge = $this->badge_model->badge_load($item->ref_id);
                if($badge->icon_image_id != 0){  
                  $item->badge_icon = asset_load_item($badge->icon_image_id);
                }    
            }
//            elseif ($item->point_type == 'draw_winner') {
//                $badge = $this->badge_model->badge_load($item->ref_id);
//                if($badge->icon_image_id != 0){  
//                  $item->badge_icon = asset_load_item($badge->icon_image_id);
//                }
//            }                
            if (uri_string() == 'profile' || $a_uri[0]=='public-profile') {
                    $item->screen_name = sprintf('%s', $item->screen_name);;
                } else {
                    $item->screen_name = sprintf('<a href="/public-profile/%s">%s</a>',  strtolower($item->screen_name),$item->screen_name);
                } 
        }
        // load widget view
        if (!empty($activity_list)) {
            $data['items'] = $activity_list;
            
            return array(
                'meta_subtitle' => 'User Activity',
                'content' => $this->render('user_activity_list_admin', $data),
            );
        }

        // if anything goes wrong, return 404
        $this->output->set_status_header('404');
        return array('content' => '<p>No results were found.</p>');

        if ($data['environment'] == 'admin_panel') {
            return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
        }
    }

}

?>
