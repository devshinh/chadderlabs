<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Site_list_widget extends Widget {

    public function run($args = array()) {
        $this->load->library('session');
        $this->load->config('site/site', TRUE);
        $this->load->model('site/site_model');

        
        $this->load->helper('asset/asset');
        
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

                $sites = $this->site_model->get_all_sites(true);
                foreach($sites as $site){
                    if($site->site_image_id != 0){  
                      $site->image = asset_load_item($site->site_image_id);
                    }
              
                }

                $data['sites'] = $sites;
                return array('content' => $this->render('widget_site_list', $data));
            //}

            // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            return array('content' => '<p>Not found.</p>');
        }

        if ($data['environment'] == 'admin_panel') {
            return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
        }
    }

}

?>