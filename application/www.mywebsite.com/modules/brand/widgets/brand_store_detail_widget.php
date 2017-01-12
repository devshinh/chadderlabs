<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
                $country_code = $aSlug[1];
                $province_code = $aSlug[2];
                $store_slug = $aSlug[3];
                $store_id = $aSlug[4];
            }

            $store_detail = $this->retailer_model->store_load($store_id, true);
            
            $retailer_detail = $this->retailer_model->get_retailer_by_slug($retailer_slug);
            if (!empty($retailer_detail->logo_image_id)) {
                $retailer_detail->logo = asset_load_item($retailer_detail->logo_image_id);
            }
            //load office contact
            $retailer_detail->head_office = $this->model_contact->get_contact_by_connection('organization',$retailer_detail->id);
            
            if (!empty($store_detail)) {
                //SEO   
                //set meta description
                if (!empty($store_detail->store_num)){
                  $meta_description = sprintf('%s, %s, (Store number: %s) in %s, %s (%s), address, map, contact info, and directions', $retailer_detail->name, $store_detail->store_name, $store_detail->store_num, $store_detail->city, $store_detail->province_name, $store_detail->province);
                  $meta_keywords = sprintf('%s, %s, %s, Store number %s, address, map, directions, contact, locations, phone, email, website, city, state, province, country, employee, retail, training, LMS', $retailer_detail->name, $store_detail->store_name, $store_detail->city, $store_detail->store_num);
                  $meta_title =  sprintf('%s, %s, (Store number: %s) in %s, %s (%s) - Address, Map and Contact Information', $retailer_detail->name, $store_detail->store_name, $store_detail->store_num, $store_detail->city, $store_detail->province_name, $store_detail->province);
                }else{
                  $meta_description = sprintf('%s, %s in %s , address, map, contact info, and directions', $retailer_detail->name, $store_detail->store_name, $store_detail->city,$store_detail->province);
                  $meta_keywords = sprintf('%s, %s, %s, address, map, directions, contact, locations, phone, email, website, city, state, province, country, employee, retail, training, LMS', $retailer_detail->name, $store_detail->store_name, $store_detail->city);
                  $meta_title =  sprintf('%s, %s in %s, %s (%s) - Address, Map and Contact Information', $retailer_detail->name, $store_detail->store_name, $store_detail->city, $store_detail->province_name, $store_detail->province);
                }
                //set page title
                
                //set page keywords
                
                
                
                $data['store_detail'] = $store_detail;
                $data['retailer_detail'] = $retailer_detail;
                return array('content' => $this->render('widget_store_detail_page', $data),
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
