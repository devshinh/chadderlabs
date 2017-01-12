<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_list_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('news/news', TRUE);
    $this->load->model('news/news_model');
    $data = array();
    $data['js'] = $this->config->item('js', 'news');
    $data['css'] = $this->config->item('css', 'news');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'News Item List';

    // check permissions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_news')) {
      return '<p>You do not have permission to access news.</p>';
    }

    //if (is_array($args) && count($args) > 0 && array_key_exists('news_id', $args)) {
    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      //$category = $this->news_model->get_category();
      //if ($category && $category->id > 0) {
        //$data['category'] = $category;
        //$data['categories'] = $this->news_model->list_categories(TRUE);
        $category_id = 1; //$category->id;
        $data['items'] = $this->news_model->list_all_news($category_id, TRUE);
        // load widget view
        return $this->render('news_list', $data);
      //}

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return '<p>News not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>