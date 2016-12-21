<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Brand_list_widget extends Widget {

    public function run($args = array()) {
        $this->load->library('session');
        $this->load->config('brand/brand', TRUE);
        //$this->load->model('brand/brand_model');
        $this->load->model('retailer/retailer_model');
        
        $this->load->helper('asset/asset');
        
        $data = array();
        $data['js'] = $this->config->item('js', 'brand');
        $data['css'] = $this->config->item('css', 'brand');
        $data['environment'] = $this->config->item('environment');
        $module_title = 'Brand List';

        // check permissions
        // unregistered users can view brands, but should not be able to bid on brands
        $data['userid'] = (int) ($this->session->userdata("user_id"));
        if (!has_permission('view_content')) {
            //return array('content' => '<p>You do not have permission to access brands.</p>');
        }

        //if (is_array($args) && count($args) > 0 && array_key_exists('brand_id', $args)) {
        if (is_array($args)) {
            if (array_key_exists('title', $args)) {
                $data['title'] = $args['title'];
            }

            // load widget view
            //if (($this->ion_auth->logged_in())) {
            //    return array('content' => $this->render('widget_list_logged_user', $data));
            //} else {
                if($this->input->post() && $this->input->post('sort_by')){
                  $sorting = array('sort_by' => $this->input->post('sort_by'), 'sort_direction' => $this->input->post('sort_direction'));
                }else{
                    $sorting = array('sort_by' => "active_store_count", 'sort_direction' => 'desc');
                }
                $retailers = $this->retailer_model->retailer_load_active($sorting);
                //var_dump($retailers);
                foreach ($retailers as $retailer) {
                    //$count_stores = $this->retailer_model->count_stores($retailer->id);
                    //$retailer->stores = $count_stores->count;
                    $count_users = $this->retailer_model->count_users($retailer->id);
                    $retailer->users = $count_users->count;
                    if(!empty($retailer->logo_image_id)){
                      $retailer->logo = asset_load_item($retailer->logo_image_id);            
                    }                    
                }
                $data['retailers'] = $retailers;
                $data['sorting'] = $sorting;
                return array('content' => $this->render('widget_list_not_logged_user', $data));
            //}

            // if anything goes wrong, return 404
                  $this->output->set_status_header('404');
                  redirect('page-not-found');
            return array('content' => '<p>Product not found.</p>');
        }

        if ($data['environment'] == 'admin_panel') {
            return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
        }
    }

}

?>