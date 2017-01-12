<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Carousel_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('carousel/carousel', TRUE);
    $this->load->model('carousel/carousel_model');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $module_titile = $this->config->item('module_title', 'carousel');

    if (is_array($args) && array_key_exists('group_id', $args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      $group_id = (int)($args['group_id']);
      $group = $this->carousel_model->get_carousel_group($group_id);
      $items = $this->carousel_model->list_items($group_id);
      if ($group->asset_category_id > 0 && count($items) > 0) {
        $this->load->library('asset/asset_item');
        $images = Asset_image_item::get_all_images_by_categoryid($group->asset_category_id);
        foreach ($items as $item) {
          $item->image = NULL;
          foreach ($images as $image) {
            if ($item->asset_id == $image->id) {
              $item->image = $image;
            }
          }
        }
        $data['items'] = $items;
        $data['js'] = $this->config->item('js', 'carousel');
        $data['css'] = $this->config->item('css', 'carousel');
        // load widget view
        return $this->render('carousel', $data);
      }
    }
    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_titile . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>