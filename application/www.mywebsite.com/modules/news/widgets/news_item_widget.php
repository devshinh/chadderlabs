<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_item_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('news/news', TRUE);
    $this->load->library('news/CmsNews');
    $this->load->helper('asset/asset');    
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'news');
    $data['css'] = $this->config->item('css', 'news');
    $module_title = 'News Detail';

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_news')) {
      //return array('content' => '<p>You do not have permission to access news.</p>');
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
    if ($data['environment'] == 'admin_panel') {
      $args['slug'] = CmsNews::random_slug();
    }

    if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
      $slug = $args['slug'];
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      $data['error'] = $this->session->flashdata('error');
      $data['message'] = $this->session->userdata('bid_confirm');
      $this->session->set_userdata('bid_confirm', '');

      if ($slug > '') {
        // is this a preview?
        $preview = get_cookie('preview_news');
        if ($preview > '' && strpos($preview, ':') > 0) {
          $ids = explode(':', $preview);
          $news_id = $ids[0];
          $rev_id = $ids[1];
          $rev_slug = $ids[2];
          if ($rev_slug == $slug) {
            if ($rev_id > 0) {
              // preview a revision
              $news = new CmsNews($news_id);
              $item = $news->get_revision($rev_id);
              if ($item) {
                $item->title .= ' [revision: ' . date('Y-m-d H:i:s', $item->create_timestamp) . ']';
              }
            }
            else {
              // preview a draft
              $news = new CmsNews($news_id);
              $item = $news->draft;
              if ($item) {
                $item->title .= ' [draft preview]';
              }
            }
          }
          else {
            $item = new CmsNews($slug, TRUE);
          }
        }
        else {
          // load a published item
          $item = new CmsNews($slug, TRUE);
        }
        if ($item) {
          $data['item'] = $item;
          if ($item->id == 0) {
            // item not found. set 404 status
                  $this->output->set_status_header('404');
                  redirect('page-not-found');
            return '<p>News not found.</p>';
          }
          // load widget view
          if(strlen($item->title) > 100){
            $subtitle = substr($item->title, 0, 100).'...';
          }else{
            $subtitle = $item->title;
          }
          
          return array(
            'meta_subtitle' => $subtitle,
            'content' => $this->render('news_item', $data),
          );
        }
      }

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return '<p>News not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>