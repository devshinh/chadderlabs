<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * User helper
 */

/**
 * List user activities through Ajax for profile pages
 * The URL to get here is /ajax/user/activityprofile/5
 * @param  int  $limit max number of results to be displayed
 * 
 * @return string
 * 
 */
if (!function_exists('user_activityprofile_ajax')) {

    function user_activityprofile_ajax($limit = 5, $user_id = 0) {

        $CI = & get_instance();
        $CI->load->config('user/user', TRUE);
        $CI->load->model('user/user_model');

        $json = array(
            'result' => FALSE, // mandatory for all JSON output
            'messages' => '', // mandatory for all JSON output
            'content' => '', // optional output parameter, include when needed
            'activities' => '', // dynamic output parameter, include when needed
        );
        // check permission
        if (!has_permission('view_content')) {
            $json['messages'] = '<p>You do not have permission to access this content.</p>';
            return $json;
        }
        //load user activity list

        $userid = $CI->session->userdata('user_id');
        $activity_list = $CI->user_model->list_user_activity_profile($limit, $userid);

        $json['result'] = TRUE;
        $json['activities'] = load_activity_items($activity_list);
        //$json['content'] = $CI->load->view('user/user_activity', $data, TRUE);
        $json['limit'] = $limit;
        return $json;


    }
}
/**
 * List user activities through Ajax
 * The URL to get here is /ajax/user/activity/5
 * @param  int  $limit max number of results to be displayed
 * @return string
 * 
 * TODO add new fields (same like in widget function 
 */

if (!function_exists('user_activity_ajax')) {

    function user_activity_ajax($limit = 5, $restricted = 0) {

        $CI = & get_instance();
        $CI->load->config('user/user', TRUE);
        $CI->load->model('user/user_model');

        $json = array(
            'result' => FALSE, // mandatory for all JSON output
            'messages' => '', // mandatory for all JSON output
            'content' => '', // optional output parameter, include when needed
            'activities' => '', // dynamic output parameter, include when needed
        );
        // check permission
        if (!has_permission('view_content')) {
            $json['messages'] = '<p>You do not have permission to access this content.</p>';
            return $json;
        }
        //load user activity list

        $a_uri = explode('/', uri_string());
        if (uri_string() == 'profile') {
            $user_id = $this->session->userdata('user_id');
            $activity_list = $this->CI->user_model->list_user_activity_profile($limit, $user_id);
        } elseif ($a_uri[0] == 'public-profile') {
            if (is_array($args) && array_key_exists('user_info', $args)) {
                $data['user_info'] = $args['user_info'];
            }

            $activity_list = $CI->user_model->list_user_activity_profile($limit, $data['user_info']->user_id);
        } else {
            $activity_list = $CI->user_model->list_user_activity($limit, $restricted);
        }
        
        $json['result'] = TRUE;
        $json['activities'] = load_activity_items($activity_list);
        //$json['content'] = $CI->load->view('user/user_activity', $data, TRUE);
        $json['limit'] = $limit;
        return $json;
    }

}

/**
 * List user activities through Ajax
 * The URL to get here is /ajax/user/activity/5
 * @param  int  $limit max number of results to be displayed
 * @return string
 * 
 * TODO add new fields (same like in widget function 
 */
if (!function_exists('user_activityprofilepublic_ajax')) {

    function user_activityprofilepublic_ajax($limit = 5, $screen_name = '') {

        $CI = & get_instance();
        $CI->load->config('user/user', TRUE);
        $CI->load->model('user/user_model');

        $json = array(
            'result' => FALSE, // mandatory for all JSON output
            'messages' => '', // mandatory for all JSON output
            'content' => '', // optional output parameter, include when needed
            'activities' => '', // dynamic output parameter, include when needed
        );
        // check permission
        if (!has_permission('view_content')) {
            $json['messages'] = '<p>You do not have permission to access this content.</p>';
            return $json;
        }
        //load user activity list
        //load user id by screenname
        $user_id = $CI->user_model->get_user_by_screename($screen_name);
        $activity_list = $CI->user_model->list_user_activity_profile($limit, $user_id);
        
        $json['result'] = TRUE;
        $json['activities'] = load_activity_items($activity_list);
        //$json['content'] = $CI->load->view('user/user_activity', $data, TRUE);
        $json['limit'] = $limit;
        return $json;
    }

}

if (!function_exists('load_activity_items')) {
    
   function load_activity_items($activity_list){
       
        $CI = & get_instance();
        //$CI->load->config('user/user', TRUE);
        $CI->load->model('user/user_model');
        $CI->load->model('shop/shop_model');
        $CI->load->model('product/product_model');
        $CI->load->model('badge/badge_model');
        $CI->load->helper('asset/asset');
        //$CI->load->library('asset/asset_item');
        $CI->load->library('training/CmsTraining');
        $CI->load->library('quiz/CmsQuiz');
        
        foreach ($activity_list as $item) {
            $item->time_ago = time_elapsed_string($item->create_timestamp);
            // when it is a quiz we need more info: quiz type name, training item name and featured image for item
            if ($item->point_type == 'quiz') {
                $quiz = new CmsQuiz();
                $quiz_history = $quiz->quiz_history($item->ref_id);
                if ($quiz_history) {
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
                    $thumb = sprintf('http://%s%s/%s_thumb.%s', $training->domain, $training->featured_image->thumb,$training->featured_image->file_name,$training->featured_image->extension);                    
                    $item->training_featured_image_full_path = $thumb;
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
                    $thumb = sprintf('http://%s%s/%s_thumb.%s', $training->domain, $training->featured_image->thumb,$training->featured_image->file_name,$training->featured_image->extension);                    
                    $item->training_featured_image_full_path = $thumb;
                }
            }            
            elseif ($item->point_type == 'order') {
                $order = $CI->shop_model->load_order($item->ref_id);
                foreach ($order->items as $order_item) {
                    $product_id = $order_item->product_id;
                }
                $product = $CI->product_model->get_product($product_id);
                $item->product_featured_image_thumb_html = new stdClass();
                $item->product_featured_image_thumb_html = $product->featured_image->thumb_html;
                $item->product_slug = new stdClass();
                $item->product_slug = $product->slug;
            }
            elseif ($item->point_type == 'reffer_colleague') {
                $avatar = $CI->user_model->get_user_avatar($item->avatar_id);
                if(!empty($avatar)){
                  $item->avatar = $avatar;
                }
                
            }  
            elseif ($item->point_type == 'ref-colleag') {
                $avatar = $this->user_model->get_user_avatar($item->avatar_id);
                $item->avatar = $avatar;
            }elseif ($item->point_type == 'badge') {
                $badge = $CI->badge_model->badge_load($item->ref_id);
                if($badge->icon_image_id != 0){  
                  $badge_icon = asset_load_item($badge->icon_image_id);
                  $item->badge_filename = new stdClass();
                  $item->badge_filename = 'asset/upload/'.$badge_icon->file_name.'.'.$badge_icon->extension;                  
                }
            }               

            $item->screenname = new stdClass();
            $item->screenname = $item->screen_name;            
            $item->screen_name = sprintf('<a href="/public-profile/%s">%s</a>',  strtolower($item->screen_name),$item->screen_name);
        }
        return $activity_list;
 }
}

if (!function_exists('time_elapsed_string')) {

    function time_elapsed_string($ptime) {
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
}