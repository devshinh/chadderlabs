<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Training Controller
 *
 * @package		HotCMS
 * @author		jeffrey@hottomali.com
 * @copyright	Copyright (c) 2012, HotTomali.
 * @since		Version 3.0
 */
class Training extends HotCMS_Controller {

  public function __construct() {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_training')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->config('training', TRUE);
    $this->load->library('CmsTraining');
    $this->load->model('training_model');
    $this->load->helper('training');

    $this->module_url = $this->config->item('module_url', 'training');
    $this->field_types = $this->config->item('field_types', 'training');
    $this->module_header = $this->lang->line('hotcms_training');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_training'));
    $this->front_theme = $this->config->item('theme');
    $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'training');
    $this->search_options = array('title' => 'Title');
  }

  /**
   * list all training
   * @param  int  page number
   */
  public function index($page_num = 1)
  {
    $data = array(
      'module_url' => $this->module_url,
      'module_header' => $this->module_header,
      'add_new_text' => $this->add_new_text,
      'java_script' => $this->java_script,
    );

    $category_id = $this->load_used_category_id();

    if (empty($category_id))
      $category_id = 8;

    $data['java_script'] .= ' modules/' . $this->module_url . '/js/training_setting.js';
    $right_data['css'] = 'modules/' . $this->module_url . '/css/training.css';

    $left_data['training_list'] = CmsTraining::list_training(0, FALSE);

    // paginate configuration
    $this->load->library('pagination');
    $default_sort_by = 'title'; // default field to sort by
    $default_per_page = 10;

    if ($this->input->post()) {
      $sort_direction = $this->input->post('sort_direction');
      if (!in_array($sort_direction, array('asc', 'desc'))) {
        $sort_direction = 'asc';
      }

      $filters = array(
        'sort_by' => $this->input->post('sort_by') > '' ? $this->input->post('sort_by') : $default_sort_by,
        'sort_direction' => $this->input->post('sort_direction'),              
        'per_page' => $this->input->post('per_page') > 0 ? $this->input->post('per_page') : $default_per_page,
        'keyword' => $this->input->post('keyword')
      );
      $this->session->set_userdata('training_filters', $filters);
      redirect('training');
    }        

    $filters = $this->session->userdata('training_filters');
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',              
        'per_page' => $default_per_page,
        'keyword' => '',
        'keyword_column' => 'title',            
      );
    }
    $data['filters'] = $filters;
    $data['form']['keyword_column_options'] = $this->search_options;
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'],'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);
    $data['form']['per_page_options'] = list_page_options();
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = CmsTraining::count_all($filters, FALSE);
    $items = CmsTraining::list_training($filters, FALSE, $page_num, $pagination_config['per_page']);

    // paginate
    $data['form']['per_page_options'] = list_page_options();
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();

    $data['messages'] = $this->load_messages();

    $data['items'] = $items;
    $data['items'] = $this->load->view('training_items', $data, true);

    $categories_main = $this->training_model->category_get_all(TRUE);

    /* data for tempalte setting tab */
    $categories_dropdown = array();
    foreach ($categories_main as $mc) {
      $sub_categories = $this->training_model->category_get_all_childs($mc->id);
      foreach ($sub_categories as $sc) {
        $categories_dropdown[$sc->id] = $mc->name . ' - ' . $sc->name;
      }
    }
    $main_tags = $this->training_model->type_tag_get_by_category_id($category_id, TRUE);
    $tags = $this->training_model->type_tag_get_by_category_id($category_id, FALSE);
    $sub_tags = array();
    foreach ($tags as $tag) {
      //$main_cat = $this->training_model->tag_type_get($tag->parent_id);
      $sub_tags[$tag->id] = $this->training_model->tag_type_get_childs($tag->id);
    }
    $data['type_tags_main'] = $main_tags;
    $data['type_tags_sub'] = $sub_tags;
    $data['variant_fields'] = $this->training_model->variant_field_list($category_id);
    $data['field_types'] = $this->field_types;
    $data['categories_dropdown'] = $categories_dropdown;
    $data['selected_category'] = $category_id;

    $right_data['templates'] = $this->load->view('training_templates', $data, true);

    /* data for categories tab */
    $main_categories = $this->training_model->category_get_all(true);
    $subcategories = array();
    foreach ($main_categories as $c) {
      $subcategories[$c->id] = $this->training_model->subcategory_get_by_category($c->id);
    }
    $data['categories'] = $main_categories;
    $data['subcategories'] = $subcategories;
    $right_data['categories'] = $this->load->view('training_categories', $data, true);

    self::loadBackendView($data, 'training/training_leftbar', $left_data, 'training/training', $right_data);
  }

  /**
   * Create a new item
   */
  public function create() {
    //$this->output->enable_profiler(TRUE);
    // check permission
    if (!(has_permission('manage_training') || has_permission('create_training'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $data = array(
      'module_url' => $this->module_url,
      'module_header' => $this->module_header,
      'add_new_text' => $this->add_new_text,
    );

    $this->form_validation->set_rules('category_id', 'lang:hotcms_category', 'required');
    $this->form_validation->set_rules('target_id', 'lang:hotcms_target', 'required');
    $this->form_validation->set_rules('title', 'lang:hotcms_title', 'trim|required');

    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $training = new CmsTraining;
      $created = $training->create($attr);
      if ($created) {
        $this->add_message('confirm', 'Training '.$this->lang->line('hotcms_created_item'));
        redirect($this->module_url . '/edit/' . $training->id);
        exit;
      }
      else {
        $this->add_message('error', $training->errors());
      }
    }
    elseif (validation_errors() > "") {
      $this->add_message('error', validation_errors());
    }

    $left_data['training_list'] = CmsTraining::list_training(0, FALSE);

    // generate form
    $right_data['form']['title'] = $this->_create_text_input('title', '', 100, 40, '');
    $categories = array('' => ' -- select category -- ');
    $categories_main = $this->training_model->category_get_all(TRUE);
    foreach ($categories_main as $mc) {
      $sub_categories = $this->training_model->category_get_all_childs($mc->id);
      foreach ($sub_categories as $sc){
        $categories[$sc->id] = $mc->name . ' - ' . $sc->name;
      }
    }
    $right_data['categories'] = $categories;
    $right_data["targets"] = $this->training_model->get_all_target_options();

//    $trainings = array('' => ' -- select training item -- ');
//    foreach (CmsTraining::list_training() as $v) {
//      $trainings[$v->id] = $v->title;
//    }
//    $right_data['trainings'] = $trainings;
    $this->load_messages();
    self::loadBackendView($data, 'training/training_leftbar', $left_data, 'training/training_create', $right_data);
  }

  /**
   * Edit an existing item
   * @param  int  id
   */
  public function   edit($id)
  {
    $data = array(
      'module_url' => $this->module_url,
      'module_header' => $this->module_header,
      'add_new_text' => $this->add_new_text,
    );

    $training = new CmsTraining($id);

    // check permission
    if (!((has_permission('create_training') && $training->author_id == $this->user_id)
            || has_permission('edit_training') || has_permission('manage_training'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    // assign validation rules
    $this->form_validation->set_rules('category_id', 'lang:hotcms_category', 'required');
    $this->form_validation->set_rules('target_id', 'lang:hotcms_target', 'required');
    //$this->form_validation->set_rules('training_id', 'lang:hotcms_training', 'required');
    $this->form_validation->set_rules('title', 'lang:hotcms_title', 'trim|required');

    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $saved = $training->save($attr);
    }
    elseif (validation_errors() > "") {
      $this->add_message('error', validation_errors());
    }

    //$data['message'] = self::setMessage(false);

    $left_data['training_list'] = CmsTraining::list_training(0, FALSE);

    //foreach ($training->questions as $q) {
    //  $q->admin_display = $this->render_question_admin_display($q);
      //$q->admin_form = $this->render_question_admin_form($q);
    //}
    $right_data['item'] = $training;
    $right_data['front_theme'] = $this->front_theme;
    //$right_data['schedule_array'] = array('0'=>'No Schedule', '1'=>'Scheduled');
    $right_data['java_script'] = 'modules/' . $this->module_url . '/js/training_edit.js';
    $right_data['css'] = 'modules/' . $this->module_url . '/css/training_edit.css';

    // generate form
    $right_data['status_array'] = array('0' => 'Draft', '1' => 'Published', '2' => 'Archived');
    $right_data['form'] = array();
    $right_data['form']['hidden_fields'] = array(
      'training_id' => $training->id,
      'featured_image_id' => $training->featured_image_id,
    );
    $right_data['form']['title'] = $this->_create_text_input('title', $training->title, 100, 40, '');
    $right_data['form']['link'] = $this->_create_text_input('link', $training->link, 100, 40, '');
    $categories = array('' => ' -- select category -- ');
    $categories_main = $this->training_model->category_get_all(TRUE);
    foreach ($categories_main as $mc) {
      $sub_categories = $this->training_model->category_get_all_childs($mc->id);
      foreach ($sub_categories as $sc){
        $categories[$sc->id] = $mc->name . ' - ' . $sc->name;
      }
    }
    $right_data['categories'] = $categories;
    $right_data["targets"] = $this->training_model->get_all_target_options();
    $right_data['form']['featured'] =  array(
      'name'        => 'featured',
      'id'          => 'featured',
      'value'       => '1',
      'checked'     => ($training->featured == 1),
    );

    //TODO: decide which asset category to use
    $category_context = 'training_default';
    $asset_categories = asset_list_categories(array('context' => $category_context));
    $options = array('' => ' -- select category -- ');
    foreach ($asset_categories as $c) {
      $options[$c->id] = $c->name;
    }
    $right_data['asset_categories'] = $options;

//    $asset_category_id = 1; // default category
//    $images = Asset_item::list_all_images($asset_category_id);
//    $right_data['asset_category_id'] = $asset_category_id;
//    $right_data['images'] = $images;
//    if (!empty($image)) {
//      $data['image'] = $image;
//    }

    $right_data['form']['featured_image'] = '';
    $right_data['tag_types'] = $this->training_model->tag_type_list($training->category_id);
    $right_data['tags'] = $this->training_model->tag_list(0, $training->category_id);
    $right_data['form']['description'] = array(
      'name'  => 'description',
      'id'    => 'description',
      'value' => $training->description,
      'rows'  => '8',
      'cols'  => '60',
      'class' => 'tinymce',
    );
    $right_data['form']['features'] = array(
      'name'  => 'features',
      'id'    => 'features',
      'value' => $training->features,
      'rows'  => '8',
      'cols'  => '60',
      'class' => 'tinymce',
    );
    $right_data['variant_fields'] = $this->training_model->variant_field_list($training->category_id);

    $this->load_messages();
    self::loadBackendView($data, 'training/training_leftbar', $left_data, 'training/training_edit', $right_data);
  }

  function preview($tid, $rid) {
    $this->load->library('training/CmsTraining');
    $training = new CmsTraining($tid);
    if ($rid > 0) {
      $item = $training->load_revision($rid);
    } else {
      $item = $training;
    }
    $site_id = $this->session->userdata("siteID");
    $this->load->model("site/site_model");
    $preview_site = $this->site_model->get_site_by_id($site_id);
    $this->load->model("page/page_model");
    $training_item_page = $this->page_model->get_training_item_page($site_id);
    $url = rtrim($training_item_page->url, "*") . $item->slug;
    set_cookie('preview_training', $tid . ':' . $rid . ':' . $item->slug, 300);
    redirect("http://" . $preview_site->domain . "/" . $url);
    exit;
  }
  /**
   * Save training into database using Ajax
   * @param  int  training ID
   */
  public function ajax_save($training_id) {
    $this->form_validation->set_rules('category_id', 'lang:hotcms_category', 'required');
    $this->form_validation->set_rules('target_id', 'lang:hotcms_target', 'required');
    $this->form_validation->set_rules('title', 'lang:hotcms_title', 'trim|required');
    $messages = '';
    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $training = new CmsTraining($training_id);
      $result = $training->save($attr);
      $messages = $training->messages() . $training->errors();
      if ($result) {
        // reload and insert a revision
        $training = new CmsTraining($training_id);
        $revision = new CmsTrainingRevision;
        $result = $revision->create($training);
        $messages .= $revision->messages() . $revision->errors();
      }
    }
    else {
      $result = FALSE;
      $error = validation_errors();
      $messages .= strip_tags($error) . "\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Revert a training to a previous version
   * @param int training id
   * @param int revision id
   * @return string
   */
  public function ajax_revert($training_id, $rid)
  {
    $training = new CmsTraining($training_id);
    $result = $training->revert_to_revision($rid);
    $messages = $training->messages() . $training->errors();
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Calling delete function from model class
   * @param  int  id of item
   * @return void
   */
  public function delete($id) {
    // check permission
    $training = new CmsTraining($id);
    if (!((has_permission('create_training') && $training->author_id == $this->user_id)
            || has_permission('edit_training') || has_permission('manage_training'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $training = new CmsTraining($id);
    $training->delete();
    redirect($this->module_url);
  }

  /**
   * Add new training category
   */
  public function category_add() {
    if ($this->training_model->category_add()) {
      $this->add_message('confirm', $this->lang->line('training_category') . '' . $this->lang->line('hotcms_created_item'));
    } else {
      $this->add_message('error', $this->lang->line('training_category') . '' . $this->lang->line('hotcms_created_item'));
    }
    //$cookie = $cookie = $this->set_tab_cookie(2);
    //$this->input->set_cookie($cookie);

    redirect($this->module_url . '#training-categories');
  }

  /**
   * Add new training category
   */
  public function subcategory_add($cat_id) {
    if ($this->training_model->subcategory_add($cat_id)) {
      $this->add_message('confirm', $this->lang->line('training_subcategory') . '' . $this->lang->line('hotcms_created_item'));
    } else {
      $this->add_message('error', $this->lang->line('training_subcategory') . '' . $this->lang->line('hotcms_created_item'));
    }
    //$cookie = $cookie = $this->set_tab_cookie(2);
    //$this->input->set_cookie($cookie);
    $this->add_used_category_id($cat_id);

    redirect($this->module_url . '#training-categories');
  }

  public function category_delete($cat_id) {
    $training = new CmsTraining();
    $category = $training->load_category($cat_id);
    //deleting subcategories
    if ($category->parent_id == 0) {
      $subcats = $this->training_model->subcategory_get_by_category($cat_id);
      foreach ($subcats as $s) {
        $this->training_model->category_delete($s->id);
      }
      if ($this->training_model->category_delete($cat_id)) {
        $this->add_message('confirm', $this->lang->line('training_category') . '' . $this->lang->line('hotcms_deleted_item'));
      } else {
        $this->add_message('error', $this->lang->line('training_category') . '' . $this->lang->line('hotcms_deleted_item'));
      }
    } else {
      if ($this->training_model->category_delete($cat_id)) {
        $this->add_message('confirm', $this->lang->line('training_subcategory') . '' . $this->lang->line('hotcms_deleted_item'));
      } else {
        $this->add_message('error', $this->lang->line('training_subcategory') . '' . $this->lang->line('hotcms_deleted_item'));
      }
    }
    //$cookie = $this->set_tab_cookie(2);
    //$this->input->set_cookie($cookie);
    $this->add_used_category_id($cat_id);
    redirect($this->module_url . '#training-categories');
  }

  /**
   * Generates a category editing form
   * @param  int  question id
   */
  public function ajax_category_edit_form($cat_id) {
    if ($cat_id > 0) {
      $training = new CmsTraining();
      $category = $training->load_category($cat_id);
      $form = $this->render_category_admin_form($category);
    } else {
      // form for adding new question
      $form = $this->render_question_admin_form();
    }
    echo $form;
  }

  //unused
  public function subcategory_delete($cat_id) {
    if ($this->training_model->category_delete($cat_id)) {
      $this->add_message('confirm', $this->lang->line('training_subcategory') . '' . $this->lang->line('hotcms_deleted_item'));
    } else {
      $this->add_message('error', $this->lang->line('training_subcategory') . '' . $this->lang->line('hotcms_deleted_item'));
    }
    //$cookie = $cookie = $this->set_tab_cookie(2);
    //$this->input->set_cookie($cookie);
    $this->add_used_category_id($cat_id);

    redirect($this->module_url . '#training-categories');
  }

  /**
   * Renders a category administration form
   * @param  object  category object
   * @return string
   */
  protected function render_category_admin_form($category) {
    $cid = $category->id;
    //$result = '<div class="question_num"></div>';
    //$result .= form_hidden('question_type_' . $qid, $question->question_type);
    $result = '';

    $result .= form_input(array(
        'name' => 'category_name_' . $cid,
        'id' => 'category_name_' . $cid,
        'value' => $category->name,
            ));
    $result .= '<div class="controls">';
    $result .= '<a href="' . $cid . '" class="red_button_smaller save_category_link" target="_blank">Save</a>';
    $result .= ' &nbsp; &nbsp; &nbsp; ';
    $result .= '<a href="' . $cid . '" class="red_button_smaller cancel_category_link" target="_blank">Cancel</a>';
    $result .= '</div>';
    return $result;
  }

  /**
   * Displays a category name as text
   * @param  int  question id
   */
  public function ajax_category_display($category_id) {

    if ($category_id > 0) {
      //$quiz_id = (int)($attr['quiz_id']);
      $training = new CmsTraining();
      $category = $training->load_category($category_id);
      $text = $this->render_category_name_admin_display($category);
      echo $text;
    }
  }

  /**
   * Renders a question as text for displaying
   * @param  object  question object
   * @return string
   */
  protected function render_category_name_admin_display($category) {
    $result = '';
    //$result .= form_hidden('question_type_' . $question->id, $question->question_type);

    $result .= '<div class="category_name">' . $category->name . '</div>';

    $result .= '<div class="category_actions">';
    $result .= '<a href="' . $category->id . '" class="edit_category_name">';
    $result .= '<div class="btn-edit"></div></a>';
    $result .= '<a class="category_delete" href="/hotcms/training/delete_category/' . $category->id . '" onclick="return confirmDelete()">';
    $result .= '<div class="btn-delete"></div> </a> </div> ';

    return $result;
  }

  /**
   * Updates a training category
   * @param  int  category id
   */
  public function ajax_save_category($category_id) {
    $messages = '';
    $attr = $this->input->post();
    if ($category_id > 0 && !empty($attr)) {
      //$quiz_id = (int)($attr['quiz_id']);
      $training = new CmsTraining();
      $result = $training->update_category($category_id, $attr);
      $messages = $training->messages() . $training->errors();
    } else {
      $result = FALSE;
      $messages = "Category not found.\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Generates a subcategory editing form
   * @param  int  question id
   */
  public function ajax_subcategory_edit_form($cat_id) {
    if ($cat_id > 0) {
      $training = new CmsTraining();
      $category = $training->load_category($cat_id);
      $form = $this->render_subcategory_admin_form($category);
    } else {
      // form for adding new question
      $form = $this->render_subquestion_admin_form();
    }
    echo $form;
  }

  /**
   * Renders a subcategory administration form
   * @param  object  question object
   * @return string
   */
  protected function render_subcategory_admin_form($category) {
    $id = $category->id;
    //$result = '<div class="question_num"></div>';
    //$result .= form_hidden('question_type_' . $qid, $question->question_type);
    $result = '<td>';

    $result .= form_input(array(
        'name' => 'subcategory_name_' . $id,
        'id' => 'subcategory_name_' . $id,
        'value' => $category->name,
            ));
    $result .= '</td>';
    $result .= '<td>' . $category->template_id . '</td>';
    $result .= '<td class="action"><a href="' . $id . '" class="red_button_smaller save_subcategory_link" target="_blank">Save</a></td>';
    $result .= '<td class="action last"><a href="' . $id . '" class="red_button_smaller cancel_subcategory_link" target="_blank">Cancel</a></td>';
    return $result;
  }

  /**
   * Displays a category name as text
   * @param  int  question id
   */
  public function ajax_subcategory_display($category_id) {

    if ($category_id > 0) {
      //$quiz_id = (int)($attr['quiz_id']);
      $training = new CmsTraining();
      $category = $training->load_category($category_id);
      $text = $this->render_subcategory_admin_display($category);
      echo $text;
    }
  }

  protected function render_subcategory_admin_display($category) {
    $result = '';
    //$result .= form_hidden('question_type_' . $question->id, $question->question_type);

    $result .= '<td>' . $category->name . '</td>';
    $result .= '<td>' . $category->template_id . '</td>';
    $result .= '<td class="action"><a class="edit_subcategory" href="' . $category->id . '"><div class="btn-edit"></div></a></td>';
    $result .= '<td class="action last"><a class="delete_subcategory" href="/hotcms/training/category_delete/' . $category->id . '" onclick="return confirmDelete()"><div class="btn-delete"></div></a></td>';
    return $result;
  }

  /**
   * Reload quiz index page when type is changed
   * @param  int  quiz type ID
   */
  public function template_reload() {

    $category_id = $this->input->post('training_category');

    //$cookie = $cookie = $this->set_tab_cookie(1);
    //$this->input->set_cookie($cookie);
    $this->add_used_category_id($category_id);
    redirect($this->module_url . '#training-templates');
  }

  /**
   * Add tag type to category
   * @param  int  caregory ID
   */
  public function tag_type_add($category_id) {

    $this->training_model->tag_type_add($category_id);

    $this->add_message('confirm', $this->lang->line('training_tag_type').''.$this->lang->line('hotcms_created_item'));
    $this->add_used_category_id($category_id);

    //$cookie = $this->set_tab_cookie(1);
    //$this->input->set_cookie($cookie);
    $this->add_used_category_id($category_id);
    redirect($this->module_url . '#training-templates');
  }

  /**
   * Add tag to tag type
   * @param  int  tag_type ID
   * @param  int  category ID
   */
  public function tag_type_add_child($tag_id, $cat_id) {

    $this->training_model->tag_type_add_child($tag_id);

    $this->add_message('confirm', $this->lang->line('training_tag_type_item').''.$this->lang->line('hotcms_created_item'));
    $this->add_used_category_id($cat_id);

    //$cookie = $this->set_tab_cookie(1);
    //$this->input->set_cookie($cookie);
    $this->add_used_category_id($cat_id);
    redirect($this->module_url . '#training-templates');
  }

  /**
   * Generates a category editing form
   * @param  int  question id
   */
  public function ajax_tag_type_name_edit_form($tag_type_id) {
    if ($tag_type_id > 0) {
      $training = new CmsTraining();
      $tag_type = $training->load_tag_type($tag_type_id);
      $form = $this->render_tag_type_name_admin_form($tag_type);
    } else {
      // form for adding new question
      $form = $this->render_tag_type_name_admin_form();
    }
    echo $form;
  }

  /**
   * Renders a tag name administration form
   * @param  object  question object
   * @return string
   */
  protected function render_tag_type_name_admin_form($tag_type) {
    $id = $tag_type->id;
    $result = '';
    $result .= form_input(array(
        'name' => 'tag_type_name_' . $id,
        'id' => 'tag_type_name_' . $id,
        'value' => $tag_type->type_name,
            ));
    $result .= '<div class="clear"></div>';
    $result .= '<div class="controls">';
    $result .= '<a href="' . $id . '" class="red_button_smaller save_type_tag_link" target="_blank">Save</a>';
    $result .= ' &nbsp; &nbsp; &nbsp; ';
    $result .= '<a href="' . $id . '" class="red_button_smaller cancel_type_tag_link" target="_blank">Cancel</a>';
    $result .= '</div>';
    return $result;
  }

  /**
   * Displays a category name as text
   * @param  int  question id
   */
  public function ajax_tag_type_display($tag_type_id) {

    if ($tag_type_id > 0) {
      //$quiz_id = (int)($attr['quiz_id']);
      $training = new CmsTraining();
      $tag_type = $training->load_tag_type($tag_type_id);

      $text = $this->render_tag_type_name_admin_display($tag_type);
      echo $text;
    }
  }

  /**
   * Renders a tag type name as text for displaying
   * @param  object  question object
   * @return string
   */
  protected function render_tag_type_name_admin_display($tag_type) {
    $result = '';
    //$result .= form_hidden('question_type_' . $question->id, $question->question_type);

    $result .= '<div class="type_tag_name">' . $tag_type->type_name . '</div>';

    $result .= '<div class="type_tag_actions">';
    $result .= '<a href="' . $tag_type->id . '" class="edit_type_tag_name">';
    $result .= '<div class="btn-edit"></div></a>';
    $result .= '<a href="/hotcms/training/delete_tag_type/' . $tag_type->id . '/' . $tag_type->category_id . '" onclick="return confirmDelete()">';
    $result .= '<div class="btn-delete"></div> </a> </div> ';

    return $result;
  }

  /**
   * Updates a tag type
   * @param  int  tag type id
   */
  public function ajax_save_tag_type($tag_type_id) {
    $messages = '';
    $attr = $this->input->post();
    if ($tag_type_id > 0 && !empty($attr)) {
      //$quiz_id = (int)($attr['quiz_id']);
      $training = new CmsTraining();
      $result = $training->update_tag_type($tag_type_id, $attr);
      $messages = $training->messages() . $training->errors();
    } else {
      $result = FALSE;
      $messages = "Tag name not found.\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  public function tag_type_delete($tag_type_id, $category_id) {

    $training = new CmsTraining();
    $tag_type = $training->load_tag_type($tag_type_id);
    //deleting all subtag types
    if ($tag_type->parent_id == 0) {
      $subtags = $this->training_model->tag_type_get_by_tag($tag_type_id);
      foreach ($subtags as $s) {
        $this->training_model->tag_type_delete($s->id);
      }
      if ($this->training_model->tag_type_delete($tag_type_id)) {
        $this->add_message('confirm', $this->lang->line('training_tag_type') . '' . $this->lang->line('hotcms_deleted_item'));
      } else {
        $this->add_message('error', $this->lang->line('training_tag_type') . '' . $this->lang->line('hotcms_deleted_item'));
      }
    } else {
      if ($this->training_model->tag_type_delete($tag_type_id)) {
        $this->add_message('confirm', $this->lang->line('training_tag_type_item') . '' . $this->lang->line('hotcms_deleted_item'));
      } else {
        $this->add_message('error', $this->lang->line('training_tag_type_item') . '' . $this->lang->line('hotcms_deleted_item'));
      }
    }
    //$cookie = $cookie = $this->set_tab_cookie(1);
    //$this->input->set_cookie($cookie);
    $this->add_used_category_id($cat_id);
    redirect($this->module_url . '#training-templates');
  }

  public function tag_delete($tag_id,$cat_id) {

    $training = new CmsTraining();
    $tag = $training->load_tag($tag);

      if ($this->training_model->tag_delete($tag_id)) {
        $this->add_message('confirm', $this->lang->line('training_tag_type_item') . '' . $this->lang->line('hotcms_deleted_item'));
      } else {
        $this->add_message('error', $this->lang->line('training_tag_type_item') . '' . $this->lang->line('hotcms_deleted_item'));
      }

    //$cookie = $cookie = $this->set_tab_cookie(1);
    //$this->input->set_cookie($cookie);
    $this->add_used_category_id($cat_id);
    redirect($this->module_url . '#training-templates');
  }

  /**
   * Generates a subtag editing form
   * @param  int  type_tag id
   */
  public function ajax_tag_name_edit_form($tag_id) {
    if ($tag_id > 0) {
      $training = new CmsTraining();
      $tag = $training->load_tag($tag_id);
      $form = $this->render_tag_name_admin_form($tag);
    } else {
      // form for adding new question
      $form = $this->render_tag_name_admin_form();
    }
    echo $form;
  }

  /**
   * Renders a subtag editing form
   * @param  object  tag_type object
   * @return string
   */
  protected function render_tag_name_admin_form($tag) {
    $id = $tag->id;
    //$result = '<div class="question_num"></div>';
    //$result .= form_hidden('question_type_' . $qid, $question->question_type);
    $result = '<td>';

    $result .= form_input(array(
        'name' => 'subtag_name_' . $id,
        'id' => 'subtag_name_' . $id,
        'value' => $tag->name,
        'class' => 'ajax_text_input'
            ));
    $result .= '</td>';
    $result .= '<td><a href="' . $id . '" class="red_button_smaller save_tag_link" target="_blank">Save</a></td>';
    $result .= '<td><a href="' . $id . '" class="red_button_smaller cancel_tag_link" target="_blank">Cancel</a></td>';

    return $result;
  }

  /**
   * Displays a category name as text
   * @param  int  question id
   */
  public function ajax_tag_display($tag_id) {

    if ($tag_id > 0) {
      //$quiz_id = (int)($attr['quiz_id']);
      $training = new CmsTraining();
      $tag_type = $training->load_tag($tag_id);

      $text = $this->render_tag_name_admin_display($tag_type);
      echo $text;
    }
  }

  /**
   * Renders a tag type name as text for displaying
   * @param  object  question object
   * @return string
   */
  protected function render_tag_name_admin_display($tag_type) {
    $result = '';
    //$result .= form_hidden('question_type_' . $question->id, $question->question_type);

    $result .= '<td>' . $tag_type->name . '</td>';
    $result .= '<td><a href="' . $tag_type->id . '" class="edit_tag">';
    $result .= '<div class="btn-edit"></div></a></td>';
    $result .= '<td class="last"><a href="/hotcms/training/tag_delete/' . $tag_type->id . '" onclick="return confirmDelete()">';
    $result .= '<div class="btn-delete"></div></a></td>';
    return $result;
  }
  /**
   * Updates a tag type
   * @param  int  tag type id
   */
  public function ajax_save_tag($type_id) {
    $messages = '';
    $attr = $this->input->post();
    if ($type_id > 0 && !empty($attr)) {
      $training = new CmsTraining();
      $result = $training->update_tag($type_id, $attr);
      $messages = $training->messages() . $training->errors();
    } else {
      $result = FALSE;
      $messages = "Tag name not found.\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Add selected category to session
   * @param  str  message type
   * @param  array or string  message(s)
   */
  private function add_used_category_id($cat_id) {
    $this->session->set_userdata('selectedTrainningCategory', $cat_id);
  }

  /**
   * Load selected category from session
   */
  protected function load_used_category_id() {
    $category_id = $this->session->userdata('selectedTrainningCategory');
    //remove from session
    $this->session->unset_userdata('selectedTrainningCategory');
    
    if (empty($category_id))
        $category_id = $this->training_model->load_default_category_id();
    
    return $category_id;
  }

  /* function to set active tab cookie
   * @param int tab index
   *
   * @return array cookie setting
   *

  private function set_tab_cookie($tab_id) {
    return $cookie = array(
        'name' => 'selectedTab',
        'value' => $tab_id,
        'expire' => '3600',
        'domain' => $this->config->item('domain'),
        'prefix' => '',
        'secure' => FALSE,
        'path' => '/hotcms/training/'
    );
  } */

  /**
   * Display Variant Type user interface
   * @param  string  field ID
   * @return string
   */
  public function ajax_variant_type_ui($field_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $field_id = (int) $field_id;
    if ($field_id > 0) {
      $field = $this->training_model->variant_field_get($field_id);
      if ($field) {
        $result = TRUE;
        $content .= field_config_ui($field);
      }
    } else {
      $messages = "Invalid field ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Display and process New Variant Type form
   * @param  string  category ID
   * @return string
   */
  public function ajax_variant_type_add($category_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $category_id = (int)$category_id;
    if ($category_id > 0) {
      $attr = $this->input->post();
      if (!empty($attr)) {
        $field_type = '';
        if (array_key_exists('field_type', $attr)) {
          $field_type = trim($attr['field_type']);
        }
        if (array_key_exists('field_name', $attr)) {
          $field_name = trim($attr['field_name']);
        }
        if ($field_type > '' && $field_name > '') {
          $result = $field_id = $this->training_model->variant_field_insert($category_id, $attr);
          if ($result) {
            $field = $this->training_model->variant_field_get($field_id);
            $content .= field_config_ui($field);
          }
        }
      }
      else {
        $content .= '<p class="validateTips">All form fields are required.</p>
	<form id="new_variant_type_form">
	<fieldset>
    <div class="row">
      <label for="field_name">Name: </label>
      <input type="text" name="field_name" id="field_name" class="text ui-widget-content ui-corner-all required" />
    </div>
    <div class="row">
      <label for="field_type">Field Type: </label>
      <select name="field_type" id="field_type" class="required">
        <option value=""> -- Select Field Type -- </option>';
        foreach($this->field_types as $k => $v ) {
          $content .= '<option value="' . $k . '">' . $v . '</option>';
        }
        $content .= '</select>
    </div>
	</fieldset>
	</form>';
        $result = TRUE;
      }
    }
    else {
      $messages = "Invalid category.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Display and process Edit Variant Type form
   * @param  string  field ID
   * @return string
   */
  public function ajax_variant_type_edit($field_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $field_id = (int) $field_id;
    if ($field_id == 0) {
      exit;
    }
    $attr = $this->input->post();
    if (!empty($attr)) {
        //var_dump($attr);
      $result = $this->training_model->variant_field_update($field_id, $attr);
      if ($result) {
        $field = $this->training_model->variant_field_get($field_id);
        $content .= field_config_ui($field);
      }
    } else {
      $field = $this->training_model->variant_field_get($field_id);
      $content .= field_config_form($field);
      $content .= '<div class="controls"><a class="red_button_smaller save_variant_type_link" onclick="return false" href="' . $field_id . '">Save</a> &nbsp; &nbsp; &nbsp;
        <a class="red_button_smaller cancel_variant_type_link" href="' . $field_id . '">Cancel</a></div>';
      $result = TRUE;
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Delete a Variant Type
   * @param  string  field ID
   * @return string
   */
  public function ajax_variant_type_delete($field_id)
  {
    $result = FALSE;
    $messages = '';
    $field_id = (int) $field_id;
    if ($field_id > 0) {
      $action = $this->input->get('action', TRUE);
      if ($action == "delete") {
        $result = $this->training_model->variant_field_delete($field_id);
        if (!$result) {
          $messgaes = "Sorry but there was an error when trying to delete this item.\n";
        }
      }
    } else {
      $messgaes = "Invalid field ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Display and process New Variant form
   * @param  string  training ID
   * @param  string  category ID
   * @return string
   */
  public function ajax_variant_add($training_id, $category_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $training_id = (int)$training_id;
    $category_id = (int)$category_id;
    if ($training_id > 0 && $category_id > 0) {
      //$training = new CmsTraining($training_id);
      $variant_fields = $this->training_model->variant_field_list($category_id);
      $attr = $this->input->post();
      if (!empty($attr)) {
        $data = array();
        foreach ($variant_fields as $fld) {
          if($fld->field_type == 'checkboxes') {
              $values = '';
                      foreach ($attr['checkboxes_variant_fld_'.$fld->id] as $value){
                         $values .= $value.'\n';
                      }         
              $data[$fld->id] = $values;
          }else{
            if (array_key_exists('fld_' . $fld->id, $attr)) {
              $data[$fld->id] = $attr['fld_' . $fld->id];
            }
          }
        }
        $result = $variant_id = $this->training_model->variant_insert($training_id, $data);
        if ($result) {
          $variant = $this->training_model->variant_get($variant_id);
          //$content .= '<tr class="variant_row">';
          foreach ($variant->details as $d) {
            $content .= '<td>';
            if($d->field_type == 'checkboxes'){
                foreach (explode('\n',$d->value) as $val){
                    $content .= $val.'<br/>';
                }  
            }else{
                $content .= $d->value;
            }
            $content .= '</td>';
          }
          $content .= '<td><a href="' . $variant->id . '" class="edit_variant_link"><div class="btn-edit"></div></a></td>';
          $content .= '<td><a href="' . $variant->id . '" class="delete_variant_link"><div class="btn-delete"></div></a></td>';
          //$content .= '</tr>';
        }
      }
      else {
        // build the form
        $content .= '<p class="validateTips"></p>
          <form id="variant_form">
          <fieldset>';
        foreach ($variant_fields as $fld) {
          $content .= field_form($fld);
        }
	      $content .= '</fieldset></form>';
        $result = TRUE;
      }
    }
    else {
      $messages = "Invalid training ID or category ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Display and process Edit Variant form
   * @param  string  variant ID
   * @param  string  category ID
   * @return string
   */
  public function ajax_variant_update($variant_id, $category_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $variant_id = (int)$variant_id;
    $category_id = (int)$category_id;
    if ($variant_id > 0 && $category_id > 0) {
      $variant_fields = $this->training_model->variant_field_list($category_id);
      $attr = $this->input->post();
      if (!empty($attr)) {
        $data = array();
        foreach ($variant_fields as $fld) {
          if($fld->field_type == 'checkboxes') {
                      $values = '';
                      foreach ($attr['checkboxes_variant_fld_'.$fld->id] as $value){
                         $values .= $value.'\n';
                      }         
              $data[$fld->id] = $values;
          }else{
            if (array_key_exists('fld_' . $fld->id, $attr)) {
              $data[$fld->id] = $attr['fld_' . $fld->id];
            }
          }
        }
        $result = $this->training_model->variant_update($variant_id, $data);
        if ($result) {
          $variant = $this->training_model->variant_get($variant_id);
          //$content .= '<tr class="variant_row">';
          foreach ($variant->details as $d) {
            $content .= '<td>';
            if ($d->field_type == 'image') {
              $d->image = asset_load_item((int)($d->value));
              $content .= $d->image->thumb_html;
            }elseif($d->field_type == 'checkboxes'){
                foreach (explode('\n',$d->value) as $val){
                    $content .= $val.'<br/>';
                }
            }else {
              $content .= $d->value;
            }
            $content .= '</td>';
          }
          $content .= '<td><a href="' . $variant->id . '" class="edit_variant_link"><div class="btn-edit"></div></a></td>';
          $content .= '<td><a href="' . $variant->id . '" class="delete_variant_link"><div class="btn-delete"></div></a></td>';
          //$content .= '</tr>';
        }
      }
      else {
        // build the form
        $variant = $this->training_model->variant_get($variant_id);
        $content .= '<p class="validateTips"></p>
          <form id="variant_form">
          <input name="variant_id" value="' . $variant_id . '" type="hidden" />
          <fieldset>';
        foreach ($variant_fields as $fld) {
          $value = '';
          $preview = '';
          foreach ($variant->details as $d) {
            if ($d->field_id == $fld->id) {
              $value = $d->value;
              if ($fld->field_type == 'image') {
                $d->image = asset_load_item((int)($d->value));
                $preview = $d->image->thumb_html;
              }
              break;
            }
          }
          $content .= field_form($fld, $value, $preview);
        }
	      $content .= '</fieldset></form>';
        $result = TRUE;
      }
    }
    else {
      $messages = "Invalid variant ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Removes a variant
   * @param  string  variant ID
   * @return string
   */
  public function ajax_variant_delete($variant_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $variant_id = (int)$variant_id;
    if ($variant_id > 0) {
      $result = $this->training_model->variant_delete($variant_id);
    }
    else {
      $messages = "Invalid variant ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Image selection form
   * @param  string  asset ID
   * @param  string  training ID
   * @return string
   */
  public function ajax_image_chooser($asset_id = 0, $training_id = 0)
  {
    $result = FALSE;
    $messages = '';
    $content = '';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('asset_id', $attr) && $attr['asset_id'] > 0 && $training_id > 0) {
      $result = $this->training_model->asset_update($field_id, $attr);
    }
    else {
      $result = TRUE;
    }

    $image = NULL;
    $asset_id = (int)$asset_id;
    if ($asset_id > 0) {
      $image = asset_load_item( $asset_id );
    }
    $data['asset_id'] = $asset_id;
    $data['image'] = $image;

    $asset_category_id = 1; // default image category
    $data['asset_category_id'] = $asset_category_id;
    //$images = array();
    //$images = Asset_item::list_all_images($asset_category_id);
    //$data['images'] = $images;
    //$asset_options = array('' => ' -- select image -- ');
    //foreach ($images as $img) {
    //  $asset_options[$img->id] = $img->name;
    //}
    //$data['asset_options'] = $asset_options;

    // build the config form
    $category_context = 'training_default';
    $asset_categories = asset_list_categories(array('context' => $category_context));
    $options = array('' => ' -- select category -- ');
    foreach ($asset_categories as $c) {
      $options[$c->id] = $c->name;
    }
    $data['asset_categories'] = $options;

    $args = array();
    $args['asset_category_id'] = $asset_category_id;
    $data['media_upload_ui'] = asset_upload_ui($args);
    $images = asset_images_ui($args + array('single_selection' => 'ON'));
    $data['media_library_ui'] = $images['formatted'];
    $content = $this->load->view('training_image_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Video selection form
   * @param  string  resource ID
   * @param  string  training ID
   * @return string
   */
  public function ajax_video_chooser($resource_id = 0, $training_id = 0)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $category_context = 'training_video';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('resource_id', $attr) && $attr['resource_id'] > 0 && $training_id > 0) {
      $result = $this->training_model->resource_update($training_id, $resource_id, $attr);
    }
    else {
      $result = TRUE;
    }

    $file = NULL;
    $resource_id = (int)$resource_id;
    if ($resource_id > 0) {
      $file = asset_load_item( $resource_id );
    }
    $data['resource_id'] = $resource_id;
    $data['file'] = $file;

    // build the config form
    $asset_categories = asset_list_categories(array('context' => $category_context));
    $options = array('' => ' -- select category -- ');
    foreach ($asset_categories as $c) {
      $options[$c->id] = $c->name;
    }
    $data['asset_categories'] = $options;

    $args = array();
    $content = $this->load->view('training_video_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Audio selection form
   * @param  string  resource ID
   * @param  string  training ID
   * @return string
   */
  public function ajax_audio_chooser($resource_id = 0, $training_id = 0)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $category_context = 'training_audio';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('resource_id', $attr) && $attr['resource_id'] > 0 && $training_id > 0) {
      $result = $this->training_model->resource_update($training_id, $resource_id, $attr);
    }
    else {
      $result = TRUE;
    }

    $file = NULL;
    $resource_id = (int)$resource_id;
    if ($resource_id > 0) {
      $file = asset_load_item( $resource_id );
    }
    $data['resource_id'] = $resource_id;
    $data['file'] = $file;

    // build the config form
    $asset_categories = asset_list_categories(array('context' => $category_context));
    $options = array('' => ' -- select category -- ');
    foreach ($asset_categories as $c) {
      $options[$c->id] = $c->name;
    }
    $data['asset_categories'] = $options;

    $args = array();
    $content = $this->load->view('training_audio_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Process New Asset form
   * @param  string  training ID
   * @param  string  asset ID
   * @return string
   */
  public function ajax_asset_add($training_id, $asset_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $training_id = (int)$training_id;
    $asset_id = (int)$asset_id;
    if ($training_id > 0 && $asset_id > 0) {
      $attr = $this->input->post();
      if (!empty($attr)) {
        $result = $this->training_model->asset_insert($training_id, $asset_id, $attr);
      }
    }
    else {
      $messages = "Invalid training ID or asset ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Process Update Asset form
   * @param  string  training ID
   * @param  string  asset ID
   * @return string
   */
  public function ajax_asset_update($training_id, $asset_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $training_id = (int)$training_id;
    $asset_id = (int)$asset_id;
    if ($training_id > 0 && $asset_id > 0) {
      $attr = $this->input->post();
      if (!empty($attr)) {
        $result = $this->training_model->asset_update($training_id, $asset_id, $attr);
      }
    }
    else {
      $messages = "Invalid training ID or asset ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Removes an asset
   * @param  string  training ID
   * @param  string  asset ID
   * @return string
   */
  public function ajax_asset_delete($training_id, $asset_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $training_id = (int)$training_id;
    $asset_id = (int)$asset_id;
    if ($training_id > 0 && $asset_id > 0) {
      $result = $this->training_model->asset_delete($training_id, $asset_id);
    }
    else {
      $messages = "Invalid training ID or asset ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * File selection form
   * @param  string  resource ID
   * @param  string  training ID
   * @return string
   */
  public function ajax_file_chooser($resource_id = 0, $training_id = 0)
  {
    $result = FALSE;
    $messages = '';
    $content = '';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('resource_id', $attr) && $attr['resource_id'] > 0 && $training_id > 0) {
      $result = $this->training_model->resource_update($training_id, $resource_id, $attr);
    }
    else {
      $result = TRUE;
    }

    $file = NULL;
    $resource_id = (int)$resource_id;
    if ($resource_id > 0) {
      $file = asset_load_item( $resource_id );
    }
    $data['resource_id'] = $resource_id;
    $data['file'] = $file;

    //$asset_category_id = 1; // default file category
    //$data['asset_category_id'] = $asset_category_id;
    //$files = array();
    //$files = Asset_item::list_all_items(2, $asset_category_id);
    //$data['files'] = $files;
    //$asset_options = array('' => ' -- select file -- ');
    //foreach ($files as $f) {
    //  $asset_options[$f->id] = $f->name;
    //}
    //$data['asset_options'] = $asset_options;

    // build the config form
    $category_context = 'training_resource';
    $asset_categories = asset_list_categories(array('context' => $category_context));
    $options = array('' => ' -- select category -- ');
    foreach ($asset_categories as $c) {
      $options[$c->id] = $c->name;
    }
    $data['asset_categories'] = $options;

    $args = array();
    //$args['asset_category_id'] = $asset_category_id;
    //$data['media_upload_ui'] = asset_upload_ui($args);
    //$files = asset_files_ui($args + array('single_selection' => 'ON'));
    //$data['media_library_ui'] = $files['formatted'];
    $content = $this->load->view('training_file_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

    /**
   * Process New Resource form
   * @param  string  training ID
   * @param  string  asset ID
   * @return string
   */
  public function ajax_resource_add($training_id, $asset_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $training_id = (int)$training_id;
    $asset_id = (int)$asset_id;
    if ($training_id > 0 && $asset_id > 0) {
      $attr = $this->input->post();
      if (!empty($attr)) {
        $result = $this->training_model->resource_insert($training_id, $asset_id, $attr);
      }
    }
    else {
      $messages = "Invalid training ID or asset ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Process Update Resource form
   * @param  string  training ID
   * @param  string  asset ID
   * @return string
   */
  public function ajax_resource_update($training_id, $asset_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $training_id = (int)$training_id;
    $asset_id = (int)$asset_id;
    if ($training_id > 0 && $asset_id > 0) {
      $attr = $this->input->post();
      if (!empty($attr)) {
        $result = $this->training_model->resource_update($training_id, $asset_id, $attr);
      }
    }
    else {
      $messages = "Invalid training ID or asset ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /**
   * Removes a resource
   * @param  string  training ID
   * @param  string  asset ID
   * @return string
   */
  public function ajax_resource_delete($training_id, $asset_id)
  {
    $result = FALSE;
    $messages = '';
    $content = '';
    $training_id = (int)$training_id;
    $asset_id = (int)$asset_id;
    if ($training_id > 0 && $asset_id > 0) {
      $result = $this->training_model->resource_delete($training_id, $asset_id);
    }
    else {
      $messages = "Invalid training ID or asset ID.\n";
    }
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  /*   * *
   * get list of training items for news
   * @param int news id
   * @return select box of users
   *
   * TODO - role by string not ID
   */

  public function get_news_items_select($news_id) {

    $this->load->module('location', 'location_module');
    $training_items = $this->training_model->training_list(0, FALSE);
    $select_options = array();
    foreach ($training_items as $item) {
      $select_options[$item->id] = $item->title;
    }
    $this->load->model('news_model');
    $selected_array = $this->news_model->get_items_for_news($news_id);

    $selected = array();
    foreach ($selected_array as $s) {
      $selected[$s->id] = $s->id;
    }
    $data['select'] = $select_options;
    $data['selected'] = $selected;
    $items_select = $this->load->view('training_select', $data, true);
    return $items_select;
  }
  
    /* function for call model fuction to store sequence in database */

    public function ajax_save_training_image_assets_sequence() {

        // load array
        $sequence = explode('_', $_GET['order']);
   
        // loop sequence...
        $count = 0;
        foreach ($sequence as $id) {
            if(!empty($id)){
              $this->training_model->save_image_assets_sequence('training_asset', $id, ++$count);
            }
        }
    }  

}

?>
