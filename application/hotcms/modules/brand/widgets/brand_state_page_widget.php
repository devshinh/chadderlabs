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
        $module_title = 'Brand State Page';

        if (is_array($args)) {
            if (array_key_exists('title', $args)) {
                $data['title'] = $args['title'];
            }
            if (array_key_exists('slug', $args)) {
                $aSlug = explode('/', $args['slug']);
                $retailer_slug = $aSlug[0];
                $state_code = $aSlug[1];
            }
            if (empty($retailer_slug)) {
                $retailer_slug = 'best-buy-usa';
            }
            if (empty($state_code)) {
                $state_code = 'al';
            }
            //retailer details

            $retailer_detail = $this->retailer_model->get_retailer_by_slug($retailer_slug);

            if (!empty($retailer_detail->logo_image_id)) {
                $retailer_detail->logo = asset_load_item($retailer_detail->logo_image_id);
            }

            //load office contact
            $retailer_detail->head_office = $this->model_contact->get_contact_by_connection('organization', $retailer_detail->id);

            if ($this->input->post()) {
                $sorting = array('sort_by' => $this->input->post('sort_by'), 'sort_direction' => $this->input->post('sort_direction'));
            } else {
                $sorting = array('sort_by' => "store_name", 'sort_direction' => 'asc');
            }
            $stores_list = $this->retailer_model->get_stores_state_list($retailer_detail->id, $state_code, $sorting);
            if (!empty($stores_list)) {
                $data['retailer_detail'] = $retailer_detail;
                $data['stores_list'] = $stores_list;
                $data['sorting'] = $sorting;
                return array('content' => $this->render('widget_brand_state_page', $data));
            } else {

                // if anything goes wrong, return 404
                $this->output->set_status_header('404');
                //redirect('page-not-found');
                return array('content' => '<p>Brand not found.</p>');
            }
        }

        if ($data['environment'] == 'admin_panel') {
            return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
        }
    }

}

?>