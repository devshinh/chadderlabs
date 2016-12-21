<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Randomizer_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('randomizer/randomizer', TRUE);
    $data = array();
    $module_titile = $this->config->item('module_title', 'randomizer');
    $data['environment'] = $this->config->item('environment');

    if (is_array($args) && array_key_exists('asset_category_id', $args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      $asset_category_id = (int)($args['asset_category_id']);
      $this->load->library('asset/asset_item');
      $image = Asset_image_item::get_random_image_from_category($asset_category_id);
      if (!empty($image)) {
        $data['image'] = $image;
        return $this->render('randomizer', $data);
      }
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_titile . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>