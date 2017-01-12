<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_list_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    //$this->load->library('form_validation');
    $this->load->config('news/news', TRUE);
    $this->load->model('news/news_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permissions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('manage_news')) {
      return '<p>You do not have permission to manage news.</p>';
    }

    // Validation rules
    // TODO: add form validation
    //$this->form_validation->set_rules('news_id', 'News', 'trim|required|xss_clean');

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      /*
      $news_id = (int)($args['news_id']);
      if ($news_id > 0) {
        // insert new item
        //$new_asset_id = (int)($args['new_asset_id']);
        //if ($new_asset_id > 0) {
        //  $item_id = $this->news_model->insert_item($news_id, $new_asset_id);
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
              $item = $this->news_model->get_item($id);
              asset_delete_item($item->asset_id);
              $this->news_model->delete_item($id);
            }
          }
        }
        // update exiting items
        if (is_array($ids) && count($ids) > 0) {
          foreach ($ids as $id) {
            if (is_array($deletes) && array_key_exists($id, $deletes)) {
              continue;
            }
            $this->news_model->update_item($id, $links[$id], $titles[$id], $sequences[$id]);
          }
        }
        $settings['news_id'] = $news_id;
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
    return $this->render('news_list_admin', $data);
  }

}
?>