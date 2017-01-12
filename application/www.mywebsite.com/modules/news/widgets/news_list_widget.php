<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class News_list_widget extends Widget {

  public function run($args = array())
  {
    $this->load->library('session');
    $this->load->config('news/news', TRUE);
    $this->load->model('news/news_model');
    $this->load->library('news/CmsNews');
    $this->load->helper('asset/asset');
    $data = array();
    $data['js'] = $this->config->item('js', 'news');
    $data['css'] = $this->config->item('css', 'news');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'News Item List';

    // check permissions
    $data['userid'] = (int) ($this->session->userdata("user_id"));
    if (!has_permission('view_news')) {
      //return array('content' => '<p>You do not have permission to access news.</p>');
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
      $news = $this->news_model->list_all_news($category_id, TRUE);
      $items = array();
      foreach ($news as $item) {
        $new = new CmsNews($item->id);
        $items[$item->id] = $new;
      }
      $data['items'] = $items;

      // load widget view
      $content = $this->render('news_list', $data);
      return array(
          'content' => $content,
          'meta_subtitle' => ''
      );
      //}
      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return array('content' => '<p>News not found.</p>');
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}

?>