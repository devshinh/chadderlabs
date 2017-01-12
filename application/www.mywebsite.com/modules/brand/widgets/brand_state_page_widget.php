<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Brand_state_page_widget extends Widget {

    public function run($args = array()) {
        $this->load->library('session');
        $this->load->config('brand/brand', TRUE);
        //$this->load->model('brand/brand_model');
        $this->load->model('retailer/retailer_model');
        $this->load->model('contact/model_contact');

        $this->load->helper('asset/asset');

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
                $country_code = $aSlug[1];
                $state_code = $aSlug[2];
                
            }            
            //retailer details
            $retailer_detail = $this->retailer_model->get_retailer_by_slug($retailer_slug);
            if (!empty($retailer_detail->logo_image_id)) {
                $retailer_detail->logo = asset_load_item($retailer_detail->logo_image_id);
            }
            //load office contact
            $retailer_detail->head_office = $this->model_contact->get_contact_by_connection('organization',$retailer_detail->id);            
                if($this->input->post()){
                  $sorting = array('sort_by' => $this->input->post('sort_by'), 'sort_direction' => $this->input->post('sort_direction'));
                }else{
                  $sorting = array('sort_by' => "city", 'sort_direction' => 'asc');
                }            
            //get state/province details
            $state_detail = $this->retailer_model->get_state_details($state_code);
            //get store list
            $stores_list = $this->retailer_model->get_stores_state_list($retailer_detail->id, $state_code, $sorting);
            if (!empty($stores_list)) {
                //SEO   
                //set meta description
                $meta_description = sprintf('%s locations in %s, %s - Directory of addresses, maps and contact information', $retailer_detail->name, $state_detail->province_name, $retailer_detail->country);
                //set page title
                $meta_title =  sprintf('%s Locations in %s, %s', $retailer_detail->name, $state_detail->province_name, $retailer_detail->country);
                //set page keywords
                $meta_keywords = sprintf('%s, %s, %s, addresses, map, directions, contact, locations, stores, phone, email, website, city, state, province, country, retail training, ratings', $retailer_detail->name, $state_detail->province_name, $retailer_detail->country);
                
                
                $data['retailer_detail'] = $retailer_detail;
                $data['state_detail'] = $state_detail;
                $data['stores_list'] = $stores_list;
                $data['sorting'] = $sorting;
                return array('content' => $this->render('widget_brand_state_page', $data),
                    'meta_subtitle' => $meta_title,
                    'meta_keyword' => $meta_keywords,
                    'meta_description' => $meta_description                    
                    );
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