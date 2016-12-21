<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auction extends HotCMS_Controller {

    public function __construct() {
        parent::__construct();
        // check permission
        if (!($this->ion_auth->logged_in())) {
            $this->session->set_userdata('redirect_to', $this->uri->uri_string());
            redirect($this->config->item('login_page'));
        }
        if (!has_permission('manage_auction')) {
            show_error($this->lang->line('hotcms_error_insufficient_privilege'));
        }

        $this->load->model('auction_model');

        $this->load->config('auction', TRUE);
        $this->module_url = $this->config->item('module_url', 'auction');
        $this->module_header = $this->lang->line('hotcms_auction');
        $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_auction'));

        $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'auction');
        $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'auction');
    }

    /**
     * Default displaying method
     * @access public
     * @return void
     */
    public function index() {
        $data['module_url'] = $this->module_url;
        $data['module_header'] = $this->module_header;
        $data['add_new_text'] = $this->add_new_text;

        $right_data['items_array'] = $this->auction_model->get_all_auctions();
        $right_data['add_new_text'] = $this->add_new_text;
        self::loadBackendView($data, 'auction/auction', NULL, 'auction/auction', $right_data);
    }

    /**
     * Set validation rules
     *
     */
    public function validate() {
        // assign validation rules
        $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
        $this->form_validation->set_rules('opening_time', strtolower(lang('hotcms_opening_time')), 'trim|required');
        $this->form_validation->set_rules('closing_time', strtolower(lang('hotcms_closing_time')), 'trim|required');
    }

    public function edit($id) {
        $data['module_url'] = $this->module_url;
        $data['module_header'] = "Edit " . $this->module_header;

        $right_data['java_script'] = 'modules/' . $this->module_url . '/js/auction_edit.js';
        $right_data['css'] = $this->css;
        $right_data['css'] .= '/hotcms/asset/css/ui-lightness/jquery-ui-1.8.16.custom.css';

        $this->validate();

        if ($this->form_validation->run()) {
            $this->auction_model->update($id);

            $data['current_item'] = $this->auction_model->get_auction_by_id($id);

            $right_data['form'] = self::set_edit_form($data['current_item']);

            $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_updated_item')));
            $data['message'] = self::setMessage(false);
            self::loadBackendView($data, 'auction/auction_submenu', NULL, 'auction/auction_edit', $right_data);
        } else {
            $right_data['current_item'] = $this->auction_model->get_auction_by_id($id);
            $right_data['form'] = self::set_edit_form($right_data['current_item']);

            $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
            $data['message'] = self::setMessage(false);
            self::loadBackendView($data, 'auction/auction_submenu', NULL, 'auction/auction_edit', $right_data);
        }
    }

    private function set_edit_form($current_item) {
        $data['name_input'] = $this->_create_text_input('name', $current_item->name, 100, 20, 'text');
        $data['opening_time_input'] = $this->_create_text_input('opening_time', $current_item->opening_time, 100, 20, 'text');
        $data['closing_time_input'] = $this->_create_text_input('closing_time', $current_item->closing_time, 100, 20, 'text');

        $data['active_input'] = $this->_create_checkbox_input('active', 'active', 'active', 'accept', $current_item->active == 1, 'margin:10px');

        return $data;
    }

    /**
     * Display an auction bids
     * @param int  Auction ID
     */
    public function bids($id, $page_num = 1) {
        $data['module_url'] = $this->module_url;
        $data['module_header'] = "Bids for " . $this->module_header;
        $data['add_new_text'] = $this->add_new_text;

        // paginate configuration
        $this->load->library('pagination');
        $pagination_config = pagination_configuration();
        $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/bids/' . $id;
        $pagination_config['per_page'] = 10;
        $pagination_config['uri_segment'] = 5;
        $pagination_config['total_rows'] = $this->auction_model->count_all_bids();

        $right_data['items_array'] = $this->auction_model->get_auction_bids($id, $page_num, $pagination_config['per_page']);


        // paginate
        $this->pagination->initialize($pagination_config);
        $right_data['pagination'] = $this->pagination->create_links();

        $right_data['css'] = $this->css;

        // $right_data['items_array'] = $this->auction_model->get_auction_bids($id);

        self::loadBackendView($data, 'auction/auction', NULL, 'auction/auction_bids', $right_data);
    }

    /**
     * Delete auction bid
     * @param int item ID
     * @param int auction ID
     */
    public function delete_bid($item_id, $auction_id) {

        //is it highest bid?
        if ($this->auction_model->highest_bid($item_id) == 1) {
            $this->auction_model->set_as_highest_bid($item_id);
        }
        $this->auction_model->delete_bid($item_id);

        $this->bids($auction_id);
    }

}

?>