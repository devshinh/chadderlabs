<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Brand_detail_page_widget extends Widget {

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
                $data['slug'] = $args['slug'];
            }            

            $retailer_detail = $this->retailer_model->get_retailer_by_slug($data['slug']);
            if (!empty($retailer_detail->logo_image_id)) {
                $retailer_detail->logo = asset_load_item($retailer_detail->logo_image_id);
            }
            //load office contact
            $retailer_detail->head_office = $this->model_contact->get_contact_by_connection('organization',$retailer_detail->id);
            if(empty($retailer_detail)){
                $this->output->set_status_header('404');
                redirect('page-not-found');
            }            
            // load all states or provinces
            $states = account_provinces($retailer_detail->country_code);
            //filter states
            $retailers_states = $this->retailer_model->list_retailer_provinces($retailer_detail->id);

            foreach($states as $state){
                
                if(in_array($state->province_code, $retailers_states)){
                    $rs[]= $state;
                }
            }
            $data['states'] = $rs; 
            //set meta description
            if($retailer_detail->country_code == 'US'){
               $meta_description = sprintf('Full listing of %s locations and contact information in all States across USA', $retailer_detail->name);
            }elseif ($retailer_detail->country_code == 'UK'){
                $meta_description = sprintf('Full listing of %s locations and contact information in all Counties across UK', $retailer_detail->name);
            }else{
                $meta_description = sprintf('Full listing of %s locations and contact information in all Provinces across Canada', $retailer_detail->name);
            }
            //set page title
            $meta_title = sprintf('%s Locations in %s', $retailer_detail->name, $retailer_detail->country);
            //set page keywords
            $meta_keywords = sprintf('%s, address, map, directions, contact, locations, store, phone, email, website, city, state, province, country, training, retail, ratings"', $retailer_detail->name);            
            //load all cites where store is
           // $data['cities'] = $this->retailer_model->get_retailer_cities($retailer_detail->id);
            if (!empty($retailer_detail)) {
                $data['retailer_detail'] = $retailer_detail;
                return array(
                    'content' => $this->render('widget_brand_detail_page', $data),
                    'meta_subtitle' => $meta_title,
                    'meta_keyword' => $meta_keywords,
                    'meta_description' => $meta_description,
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