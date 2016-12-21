<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quiz_list_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    //$this->load->library('form_validation');
    $this->load->config('quiz/quiz', TRUE);
    $this->load->model('quiz/quiz_model');
    $this->load->library('quiz/CmsQuiz');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permissions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('manage_quiz')) {
      return '<p>You do not have permission to manage quiz.</p>';
    }

    // Validation rules
    // TODO: add form validation
    //$this->form_validation->set_rules('quiz_id', 'Quiz', 'trim|required|xss_clean');

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      /*
      $quiz_id = (int)($args['quiz_id']);
      if ($quiz_id > 0) {
        // insert new item
        //$new_asset_id = (int)($args['new_asset_id']);
        //if ($new_asset_id > 0) {
        //  $item_id = $this->quiz_model->insert_item($quiz_id, $new_asset_id);
        //}
        $deletes = $args['delete'];
        $ids = $args['id'];
        $links = $args['link'];
        $titles = $args['link_title'];
        $sequences = $args['sequence'];
        // delete items
        if (is_array($deletes) && count($deletes) > 0) {
          $this->load->helper('asset/asset');
          foreach ($deletes as $id) {
            if ($id > 0) {
              $item = $this->quiz_model->get_item($id);
              asset_delete_item($item->asset_id);
              $this->quiz_model->delete_item($id);
            }
          }
        }
        // update exiting items
        if (is_array($ids) && count($ids) > 0) {
          foreach ($ids as $id) {
            if (is_array($deletes) && array_key_exists($id, $deletes)) {
              continue;
            }
            $this->quiz_model->update_item($id, $links[$id], $titles[$id], $sequences[$id]);
          }
        }
        $settings['quiz_id'] = $quiz_id;
      } */
      $settings['title'] = trim($args['title']);
      
      if(array_key_exists('types',$args)){
        foreach($args['types'] as $type_id){
          $settings['types'][$type_id]=$type_id;
        }
        //$settings['types'] = trim($args['types%5B%5D']);
      }
      return $settings;
    }
    
    //load quiz types
      $quiz_type = new CmsQuizType;
      $quiz_types = CmsQuizType::list_type();
      foreach ($quiz_types as $type) {
        $type->sections = $quiz_type->type_list_section($type->id);
      }      

    //load activated types
    $active_types = array();
    if(array_key_exists('types',$args)){
      $active_types = $args['types'];
    }
    //foreach ($this->permission->list_role_permissions($id) as $active_role_permission) {
    //  $active_role_permissions[$active_role_permission->id] = $active_role_permission->description;
    //}
    $data['quiz_types'] = $quiz_types;
    
    foreach ($data['quiz_types'] as $type) {
      if (count($active_types)) {
        $checked = array_key_exists($type->id, $active_types);
      }else{
        $checked = FALSE;
      }
      $data['form']['quiz_types'][$type->id] = $this->_create_checkbox_input('types[]', $type->name, $type->id, $checked , 'margin-right:10px');      
    }
      

    // build the form
    $data['title'] = array(
      'name'  => 'title',
      'id'    => 'title',
      'type'  => 'text',
      'value' => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
    );

    // load widget view
    return $this->render('quiz_list_admin', $data);
  }

   /* function for models to generate varible for CI function form_input - checkbox*/
  protected function _create_checkbox_input($name, $id, $default_value = 'accept', $checked = false, $style = "", $css_class = ""){

      return array(
       'name'        => $name,
       'id'          => $id,
       'value'       => $default_value,
       'checked'     => $checked,
       'class'       => $css_class,
       'style'       => $style
     );
  } 

}
?>