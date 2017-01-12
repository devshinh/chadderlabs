<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Brand_store_detail_widget extends Widget {

    public function run($args = array()) {
        $this->load->library('session');
        $this->load->config('brand/brand', TRUE);
        //$this->load->model('brand/brand_model');
        $this->load->model('retailer/retailer_model');
        $this->load->model('contact/model_contact');

        $this->load->helper('asset/asset');
        $this->load->helper('account/account');
        
        $data = array();
        $data['js'] = $this->config->item('js', 'brand');
        $data['css'] = $this->config->item('css', 'brand');
        $data['environment'] = $this->config->item('environment');
        $module_title = 'Brand Detail Page';

        if (is_array($args)) {
            if (array_key_exists('title', $args)) {
                $data['title'] = $args['title'];
            }
            if (array_key_exists('slug', $args)) {
                $aSlug = explode('/', $args['slug']);
                $retailer_slug = $aSlug[0]; 
                $store_id = $aSlug[1];
            }   
            if (empty($retailer_slug)) {
                $retailer_slug = 'best-buy-usa';
            }
            if (empty($store_id)) {
                $store_id = '455';
            }                 

            $store_detail = $this->retailer_model->store_load($store_id, true);
            
            $retailer_detail = $this->retailer_model->get_retailer_by_slug($retailer_slug);
            if (!empty($retailer_detail->logo_image_id)) {
                $retailer_detail->logo = asset_load_item($retailer_detail->logo_image_id);
            }
            //load office contact
            $retailer_detail->head_office = $this->model_contact->get_contact_by_connection('organization',$retailer_detail->id);
            
            if (!empty($store_detail)) {
                $data['store_detail'] = $store_detail;
                $data['retailer_detail'] = $retailer_detail;
                return array('content' => $this->render('widget_store_detail_page', $data));
            } else {

                // if anything goes wrong, return 404
                $this->output->set_status_header('404');
                redirect('page-not-found');
                return array('content' => '<p>Brand not found.</p>');
            }
        }

        if ($data['environment'] == 'admin_panel') {
            return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
        }
    }

}

?>