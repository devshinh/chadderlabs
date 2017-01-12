<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_item_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('product/product', TRUE);
    $this->load->model('product/product_model');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'product');
    $data['css'] = $this->config->item('css', 'product');
    $module_title = 'Product Detail';

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_content')) {
      return array('content' => '<p>You do not have permission to access products.</p>');
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
    if ($data['environment'] == 'admin_panel') {
      $slug = $this->product_model->get_random_slug();
      $args['slug'] = $slug;
    }

    if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
      $item_slug = $args['slug'];
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      if ($item_slug > '') {
        $item = $this->product_model->get_product(0, $item_slug);
        if ($item) {
          $data['item'] = $item;
          // build the form
          $data['hidden_fields'] = array('product' => $item->id);
          $data['quantity_field'] = array(
            'name'  => 'quantity',
            'id'    => 'quantity',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('quantity'),
            'size'  => '3',
          );

          $assets = $this->product_model->list_product_assets( $item->id );
          $data['assets'] = $assets;
          // load widget view
          return array('content' => $this->render('detail', $data));
        }
      }

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      // and list all available items
      return array('content' => '<p>Product not found.</p>');
    }

    if ($data['environment'] == 'admin_panel') {
      return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
    }
  }

}
?>