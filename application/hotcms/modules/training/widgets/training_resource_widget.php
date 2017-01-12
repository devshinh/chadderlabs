<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Training_resource_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('training/training', TRUE);
    $this->load->library('training/CmsTraining');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'training');
    $data['css'] = $this->config->item('css', 'training');
    $module_title = 'Training Detail';

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_training')) {
      return '<p>You do not have permission to access training.</p>';
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
    if ($data['environment'] == 'admin_panel') {
      $args['slug'] = CmsTraining::random_slug();
    }

    if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
      $slug = $args['slug'];
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      $data['error'] = $this->session->flashdata('error');

      if ($slug > '') {
        // is this a preview?
        $preview = get_cookie('preview_training');
        if ($preview > '' && strpos($preview, ':') > 0) {
          $ids = explode(':', $preview);
          $training_id = $ids[0];
          $rev_id = $ids[1];
          $rev_slug = $ids[2];
          if ($rev_slug == $slug) {
            if ($rev_id > 0) {
              // preview a revision
              $training = new CmsTraining($training_id);
              $item = $training->get_revision($rev_id);
              if ($item) {
                $item->title .= ' [revision: ' . date('Y-m-d H:i:s', $item->create_timestamp) . ']';
              }
            }
            else {
              // preview a training
              $training = new CmsTraining($training_id);
              $item = $training->type;
              if ($item) {
                $item->title .= ' [training preview]';
              }
            }
          }
          else {
            $item = new CmsTraining($slug, TRUE);
          }
        }
        else {
          // load a published item
          $item = new CmsTraining($slug, TRUE);
        }
        if ($item) {
          $data['item'] = $item;
          if ($item->id == 0) {
            // item not found. set 404 status
            $this->output->set_status_header('404');
            return '<p>Training not found.</p>';
          }
          return $this->render('training_resource', $data);
          // load widget view
          //return array(
          //  'meta_title' => $item->title,
          //  'content' => $this->render('training_item', $data),
          //);
        }
      }

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return '<p>Training not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>