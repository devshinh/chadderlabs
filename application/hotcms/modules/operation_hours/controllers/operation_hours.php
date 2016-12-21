<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * operation hours Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */
class Operation_hours extends HotCMS_Controller {

  public function __construct() {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    //if (!has_permission('manage_auction')) {
    //  show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    //}

    $this->load->model('operation_hours_model');

    $this->load->config('operation_hours', TRUE);
    $this->module_url = $this->config->item('module_url', 'operation_hours');
    $this->module_header = $this->lang->line('hotcms_operation_hours');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . " " . $this->lang->line('hotcms_operation_hours');

    $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'operation_hours');
    $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'operation_hours');
  }

/**
* Set validation rules
*
*/

 public function validate($item_id) {
    //$this->form_validation->set_rules( 'email_'.$item_id, strtolower(lang( 'hotcms_email_address' )), 'trim|valid_email|required' );
 }

 /* function returning view for hours of operations */
 public function get_edit_form($con_table, $id){

    $data['module_back_url'] = $con_table;
    $data['module_url'] = $con_table;
    $data['connection_id'] = $id;
    $hours = $this->operation_hours_model->get_hours_by_connection($con_table,$id);
    $hour_forms = array();
    $show_extra_fields = false;
    foreach ($hours as $hour){
       $data['form_day_hours'] = self::set_edit_form_day_hours($hour);
       if(!empty($data['form_day_hours']['from2']['value']) || !empty($data['form_day_hours']['to2']['value'])){
         $show_extra_fields = true;
       }
       $data['show_extra_fields'] = $show_extra_fields;
       $hour_forms[$hour->id] = $this->load->view('operation_hours_edit',$data ,true);
    }

   if(!empty($hours)){
    return $hour_forms;
   }else{
    return false;
   }
 }

  /* function returning ONE day for item */
  private function set_edit_form_day_hours($current_item)
 {

    $data = array();
    $data['day'] = $current_item->day;
    $data['row_id'] = $current_item->id;
    $id = $current_item->id;
    $disabled = '';
    if ($current_item->closed==1) $disabled = 'disabled';
    $data['from1'] = $this->_create_text_input('from1_'.$id, $current_item->from1 ,100,5,'text '.$disabled);
    $data['to1'] = $this->_create_text_input('to1_'.$id, $current_item->to1 ,100,5,'text '.$disabled);
    $data['from2'] = $this->_create_text_input('from2_'.$id, $current_item->from2 ,100,5,'text '.$disabled);
    $data['to2'] = $this->_create_text_input('to2_'.$id, $current_item->to2 ,100,5,'text '.$disabled);
    $data['closed'] = $this->_create_checkbox_input('closed_'.$id,'closed_'.$id,'accept', ($current_item->closed==1?true:false), '', 'checkbox closebox '.$disabled);
    $data['closed_hidden'] = $this->_create_hidden_input('closed_'.$id,'0');
    return $data;
  }

  /*
   * update database fields
   *
   * @return json message
   *
   */
  public function edit_hours() {
     $data['module_url'] = $this->module_url;
     $data['module_header'] = "Edit ". $this->module_header;

     foreach ($this->input->post() as $k => $v){
       $key_array = explode('_',$k);
       if($key_array[0] == 'closed' & !empty($v[0])){
         $this->operation_hours_model->update_attribute($key_array[0],$key_array[1],$v[0]);
       }else {
         $this->operation_hours_model->update_attribute($key_array[0],$key_array[1],$v);
       }
     }

     //$this->validate($item_id);

     //if ($this->form_validation->run()) {
     //  $this->operation_hours_model->update($item_id);

       $messages = 'Items saved.';
       $json = array('result' => 'updated', 'messages' => $messages);
       echo json_encode($json);
    // }else {
    //   $json = array('result' => 'valid-error', 'messages' => strip_tags(validation_errors()));
    //   echo json_encode($json);
    // }
 }

}

?>
