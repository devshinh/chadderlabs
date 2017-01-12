<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Training_list_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    //$this->load->library('form_validation');
    $this->load->config('training/training', TRUE);
    $this->load->model('training/training_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permissions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('manage_training')) {
      return '<p>You do not have permission to manage training.</p>';
    }

    // Validation rules
    // TODO: add form validation
    //$this->form_validation->set_rules('training_id', 'Training', 'trim|required|xss_clean');

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      /*
      $training_id = (int)($args['training_id']);
      if ($training_id > 0) {
        // insert new item
        //$new_asset_id = (int)($args['new_asset_id']);
        //if ($new_asset_id > 0) {
        //  $item_id = $this->training_model->insert_item($training_id, $new_asset_id);
        //}
        $deletes = $args['delete'];
        $ids = $args['id'];
        $links = $args['link'];
        $titles = $args['link_title'];
        $sequences = $args['sequence'];
        // delete items
        if (is_array($deletes) && count($deletes) > 0) {
          $this->load->helper('asset/asset');
          foreach ($deletes as $id) {
            if ($id > 0) {
              $item = $this->training_model->get_item($id);
              asset_delete_item($item->asset_id);
              $this->training_model->delete_item($id);
            }
          }
        }
        // update exiting items
        if (is_array($ids) && count($ids) > 0) {
          foreach ($ids as $id) {
            if (is_array($deletes) && array_key_exists($id, $deletes)) {
              continue;
            }
            $this->training_model->update_item($id, $links[$id], $titles[$id], $sequences[$id]);
          }
        }
        $settings['training_id'] = $training_id;
      } */
      $settings['title'] = trim($args['title']);
      return $settings;
    }

    // build the form
    $data['title'] = array(
      'name'  => 'title',
      'id'    => 'title',
      'type'  => 'text',
      'value' => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
    );

    // load widget view
    return $this->render('training_list_admin', $data);
  }

}
?>