<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quote_list_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('quote/quote', TRUE);
    $this->load->model('quote/quote_model');
    $data = array();
    $data['js'] = $this->config->item('js', 'quote');
    $data['css'] = $this->config->item('css', 'quote');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Quote Form List';

    // check permissions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_quote')) {
      return '<p>You do not have permission to access quote.</p>';
    }

    //if (is_array($args) && count($args) > 0 && array_key_exists('quote_id', $args)) {
    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      //$category = $this->quote_model->get_category();
      //if ($category && $category->id > 0) {
        //$data['category'] = $category;
        //$data['categories'] = $this->quote_model->list_categories(TRUE);
        $category_id = 1; //$category->id;
        $data['items'] = $this->quote_model->list_all_quote($category_id, TRUE);
        // load widget view
        return $this->render('quote_list', $data);
      //}

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return '<p>Quote not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>
