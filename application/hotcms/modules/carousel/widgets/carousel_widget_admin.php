<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Carousel_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    //$this->load->library('form_validation');
    $this->load->config('carousel/carousel', TRUE);
    $this->load->model('carousel/carousel_model');

    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // Validation rules
    // TODO: add form validation
    //$this->form_validation->set_rules('group_id', 'Group', 'trim|required|xss_clean');

    if (array_key_exists('postback', $args)) {
      $settings = array();
      $group_id = (int)($args['group_id']);
      if ($group_id > 0) {
        // insert new item
        //$new_asset_id = (int)($args['new_asset_id']);
        //if ($new_asset_id > 0) {
        //  $item_id = $this->carousel_model->insert_item($group_id, $new_asset_id);
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
              $item = $this->carousel_model->get_item($id);
              asset_delete_item($item->asset_id);
              $this->carousel_model->delete_item($id);
            }
          }
        }
        // update exiting items
        if (is_array($ids) && count($ids) > 0) {
          foreach ($ids as $id) {
            if (is_array($deletes) && array_key_exists($id, $deletes)) {
              continue;
            }
            $this->carousel_model->update_item($id, $links[$id], $titles[$id], $sequences[$id]);
          }
        }
      }
      $settings['group_id'] = $group_id;
      $settings['title'] = trim($args['title']);
      return $settings;
    }

    $this->load->helper('asset/asset');
    $this->load->library('asset/asset_item');

    $group_id = 0;
    if (is_array($args) && array_key_exists('group_id', $args)) {
      $group_id = (int)($args['group_id']);
      $group = $this->carousel_model->get_carousel_group($group_id);
    }
    $data['group_id'] = $group_id;

    // build the form
    $data['title'] = array('name' => 'title',
                      'id'      => 'title',
                      'type'    => 'text',
                      'value'   => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
                     );
    $groups = $this->carousel_model->list_groups();
    $options = array('' => ' -- select group -- ');
    foreach ($groups as $g) {
      $options[$g->id] = $g->name;
    }
    $data['groups'] = $options;
    $data['items'] = array();
    // load media library management interface
    if ($group_id > 0 && !empty($group) && $group->asset_category_id > 0) {
      $args = array('asset_category_id' => $group->asset_category_id);
      $images = Asset_item::list_all_images($group->asset_category_id);
      $items = $this->carousel_model->list_items($group_id);
      $refresh = FALSE;
      foreach ($images as $image) {
        $presented = FALSE;
        foreach ($items as $item) {
          if ($item->asset_id == $image->id) {
            $presented = TRUE;
            break;
          }
        }
        if (!$presented) {
          $item_id = $this->carousel_model->insert_item($group_id, $image->id, $image->name);
          $refresh = TRUE;
        }
      }
      if ($refresh) {
        $items = $this->carousel_model->list_items($group_id);
      }
      foreach ($items as $item) {
        if ($item->asset_id > 0 && array_key_exists($item->asset_id, $images)) {
          $item->image = $images[$item->asset_id];
        }
        else {
          $item->image = NULL;
        }
      }
      $data['items'] = $items;
    }

    $data['media_upload_ui'] = asset_upload_ui( $args );
    // load widget view
    return $this->render('carousel_admin', $data);
  }

}
?>