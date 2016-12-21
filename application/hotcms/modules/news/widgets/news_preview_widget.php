<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class News_preview_widget extends Widget {

  public function run($args = array()) {

    $this->load->config('news/news', TRUE);
    $this->load->library('news/CmsNews');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'news');
    $data['css'] = $this->config->item('css', 'news');
    $module_title = 'Training Widgets';

    if (isset($args['preview_type'])) {
      $slug_array = CmsNews::random_slug($args['preview_type']);
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

        foreach ($slug_array as $i) {
          $item = new CmsNews($i->slug, TRUE);
          //if new preview -> load up just items with quiz
          if (isset($args['preview_type']) && $args['preview_type'] == 'new') {
            if ($item->has_quiz($item->id)) {
              $items[$item->id] = $item;
            }
          } elseif (isset($args['preview_type']) && $args['preview_type'] == 'coming_soon') {
            if (!$item->has_quiz($item->id)) {
              $items[$item->id] = $item;
            }
          } elseif (isset($args['preview_type']) && $args['preview_type'] == 'featured') {
            $items[$item->id] = $item;
          } elseif (isset($args['preview_type']) && $args['preview_type'] == 'carousel') {
            if ($item->has_quiz($item->id)) {
              $items[$item->id] = $item;
            }
          } elseif (isset($args['preview_type']) && $args['preview_type'] == 'latest') {
            $items[$item->id] = $item;
          }           
          
        }

        $data['items'] = $items;
        // load widget view
        if (isset($args['preview_type']) && $args['preview_type'] == 'featured') {
          return array(
            'content' => $this->render('news_preview_featured', $data),
            'meta_subtitle' => 'Training Center'
          );
        }
        if (isset($args['preview_type']) && $args['preview_type'] == 'coming_soon') {
          return array('content' => $this->render('news_preview_coming_soon', $data));
        }
        if (isset($args['preview_type']) && $args['preview_type'] == 'new') {
          return array('content' => $this->render('news_preview_new', $data));
        }
        if (isset($args['preview_type']) && $args['preview_type'] == 'carousel') {
          return array('content' => $this->render('news_preview_carousel', $data));
        }        
      } else {
        // item not found. set 404 status
        $this->output->set_status_header('404');
        return '<p>Training not found.</p>';
      }

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return '<p>Training not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
    //}
  }

}

?>