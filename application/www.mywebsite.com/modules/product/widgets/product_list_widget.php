<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_list_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('product/product', TRUE);
    $this->load->model('product/product_model');
    $data = array();
    $data['js'] = $this->config->item('js', 'product');
    $data['css'] = $this->config->item('css', 'product');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Product List';

    // check permissions
    // unregistered users can view products, but should not be able to bid on products
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_content')) {
      return array('content' => '<p>You do not have permission to access products.</p>');
    }

    //if (is_array($args) && count($args) > 0 && array_key_exists('product_id', $args)) {
    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      if (array_key_exists('category_id', $args)) {
        $data['category_id'] = (int)($args['category_id']);
      }
      else {
        $data['category_id'] = 0;
      }
      if (array_key_exists('page_num', $args)) {
        $data['page_num'] = (int)($args['page_num']);
      }
      else {
        $data['page_num'] = 1;
      }
      if (array_key_exists('per_page', $args)) {
        $data['per_page'] = (int)($args['per_page']);
      }
      else {
        $data['per_page'] = 12; // by default. can be moved into config file
      }
      // max display in this block, display a More link instead of pagination
      // useful for creating a block that does not need a full list of products (Related Product, landing page etc)
      if (array_key_exists('max_display', $args)) {
        $data['max_display'] = (int)($args['max_display']);
        if ($data['per_page'] > $data['max_display']) {
          // retrieves one more item, and exclude it if it's on the list and also displayed as main content on item detail page
          $data['per_page'] = $data['max_display'] + 1;
        }
      }
      else {
        $data['max_display'] = 0; // no limit, show full list and pagination as normal
      }
      $data['items'] = $this->product_model->list_products($data['category_id'], TRUE, $data['page_num'], $data['per_page']);
      $data['item_count'] = $this->product_model->count_product($data['category_id'], TRUE);
      foreach ($data['items'] as $item) {
        $data['items_images'][$item->id] = $this->product_model->list_product_assets($item->featured_image_id);
      }

      // load widget view
      return array(
          'content' => $this->render('list', $data),
          'meta_subtitle' => 'Swag Shop'
          );

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return array('content' => '<p>Product not found.</p>');
    }

    if ($data['environment'] == 'admin_panel') {
      return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
    }
  }

}
?>