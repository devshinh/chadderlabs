<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class News_preview_widget extends Widget {

  public function run($args = array()) {

    $this->load->config('news/news', TRUE);
    $this->load->library('news/CmsNews');
    $this->load->library('training/CmsTraining');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'news');
    $data['css'] = $this->config->item('css', 'news');
    $module_title = 'News Widgets';
    if (isset($args['preview_type'])) {
      $training_item_id = 0;
      if (strcasecmp($args['preview_type'], 'training_item') === 0) {
        $this->load->model("training/training_model");
        $page_url = $this->uri->uri_string();
        $last_slash = strrpos($page_url, "/") + 1;
        $genetic_url = substr($page_url, 0, $last_slash)."*";
        $site_product_training_page_url = $this->training_model->get_site_training_url($this->session->userdata("siteID"));

        if(strcasecmp($genetic_url, $site_product_training_page_url) === 0){
          $training_slug = substr($page_url, $last_slash);
          $training_item = new CmsTraining($training_slug, TRUE);
        }
        if ( !empty($training_item)){
          $training_item_id = $training_item->id;
        }
      }
      $slug_array = CmsNews::random_preview_slug($args['preview_type'], $training_item_id);
    } else {
      $args['slug'] = CmsNews::random_slug();
    }

    if (is_array($args) && count($args) > 0 && array_key_exists('title', $args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      $data['error'] = $this->session->flashdata('error');
      if (is_array($slug_array) && count($slug_array) > 0) {
        $items = array();
        //load up items for each widget type
        foreach ($slug_array as $i) {
          $item = new CmsNews($i->slug, TRUE);
          if (isset($args['preview_type']) && $args['preview_type'] == 'training_item') {
              $items[$item->id] = $item;
          } elseif (isset($args['preview_type']) && $args['preview_type'] == 'homepage') {
              $items[$item->id] = $item;
          } elseif (isset($args['preview_type']) && $args['preview_type'] == 'featured') {
            $items[$item->id] = $item;
          } elseif (isset($args['preview_type']) && $args['preview_type'] == 'carousel') {
            if ($item->has_quiz($item->id)) {
              $items[$item->id] = $item;
            }
          } elseif (isset($args['preview_type']) && $args['preview_type'] == 'latest') {
                //remove actual post $training_slug
              $items[$item->id] = $item;
          }          
        }

        $data['items'] = $items;
        // load widget view
        if (isset($args['preview_type']) && $args['preview_type'] == 'training_item') {

          return array(
              'content' => $this->render('news_preview_training_item', $data),
              'meta_subtitle' => 'News'
          );
        }
        if (isset($args['preview_type']) && $args['preview_type'] == 'homepage') {
          return array('content' => $this->render('news_preview_homepage', $data));
        }
        if (isset($args['preview_type']) && $args['preview_type'] == 'new') {
          return array('content' => $this->render('news_preview_new', $data));
        }
        if (isset($args['preview_type']) && $args['preview_type'] == 'carousel') {
          return array('content' => $this->render('news_preview_carousel', $data));
        }
        if (isset($args['preview_type']) && $args['preview_type'] == 'latest') {
          return array('content' => $this->render('news_preview_latest', $data));
        }        
      }
      else {        
        return array('content' => '');
         //return array('content' => '<p>News item not found.</p>');
      }

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return array('content' => '<p>Training not found.</p>');
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
    //}
  }

}

?>