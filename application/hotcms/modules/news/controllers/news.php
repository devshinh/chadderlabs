<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * News Controller
 *
 * @package		HotCMS
 * @author		jeffrey@hottomali.com
 * @copyright	Copyright (c) 2012, HotTomali.
 * @since		Version 3.0
 */
class News extends HotCMS_Controller {

  private $default_category_id;

  public function __construct() {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_news')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->config('news', TRUE);
    $this->load->library('CmsNews');
    $this->load->library('asset/asset_item');
    $this->load->helper('asset/asset');

    $this->module_url = $this->config->item('module_url', 'news');
    $this->module_header = $this->lang->line('hotcms_news');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_news'));
    $this->front_theme = $this->config->item('theme');
    $this->default_category_id = $this->config->item('default_category_id', 'news');
  }

  /**
   * list all news
   * @param  int  page number
   */
  public function index($page_num = 1) {
    $data = array(
        'module_url' => $this->module_url,
        'module_header' => $this->module_header,
        'add_new_text' => $this->add_new_text,
    );

    // check permission
    if (has_permission('manage_news') || has_permission('edit_news')) {
      $limit_by_author = 0;
    } else {
      $limit_by_author = $this->user_id;
    }
    //$left_data['categories'] = CmsNews::list_category();
    $left_data['news_list'] = CmsNews::list_news($this->default_category_id, FALSE, $limit_by_author);

    // paginate configuration
    $this->load->library('pagination');
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = 10;

    $pagination_config['total_rows'] = CmsNews::count_all();
    $right_data['items'] = CmsNews::list_news($this->default_category_id, FALSE, $limit_by_author, $page_num, $pagination_config['per_page']);

    // paginate
    $this->pagination->initialize($pagination_config);
    $right_data['pagination'] = $this->pagination->create_links();
    self::loadBackendView($data, 'news/news_leftbar', $left_data, 'news/news', $right_data);
  }

  /**
   * Create a new item
   */
  public function create() {
    //$this->output->enable_profiler(TRUE);
    // check permission
    if (!(has_permission('manage_news') || has_permission('create_news'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    if (has_permission('manage_news') || has_permission('edit_news')) {
      $limit_by_author = 0;
    } else {
      $limit_by_author = $this->user_id;
    }

    $data = array(
        'module_url' => $this->module_url,
        'module_header' => $this->module_header,
        'add_new_text' => $this->add_new_text,
    );

    $this->form_validation->set_rules('title', 'lang:hotcms_title', 'trim|required|unique_news');

    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $news = new CmsNews;
      $created = $news->create($attr);

      if ($created) {
        $this->add_message('confirm', 'News item '.$this->lang->line('hotcms_created_item'));
        redirect($this->module_url . '/edit/' . $news->id);
        exit;
      } else {
        $this->add_message('error', $news->errors());
      }
    } else {
      $this->add_message('error', validation_errors());
    }

    $left_data['news_list'] = CmsNews::list_news($this->default_category_id, FALSE, $limit_by_author);

    // generate form
    $right_data['form']['title_input'] = $this->_create_text_input('title', '', 100, 40, 'draft');
    $this->load_messages();
    self::loadBackendView($data, 'news/news_leftbar', $left_data, 'news/news_create', $right_data);
  }

  /**
   * Edit an existing item
   * @param  int  id
   */
  public function edit($id) {
    $data = array(
        'module_url' => $this->module_url,
        'module_header' => $this->module_header,
        'add_new_text' => $this->add_new_text,
    );

    $news = new CmsNews($id);

    // check permission
    if (!((has_permission('create_news') && $news->author_id == $this->user_id)
            || has_permission('edit_news') || has_permission('manage_news'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    if (has_permission('manage_news') || has_permission('edit_news')) {
      $limit_by_author = 0;
    } else {
      $limit_by_author = $this->user_id;
    }

    // assign validation rules
    $this->form_validation->set_rules('title', 'lang:hotcms_title', 'trim|required|unique_news[' . $id . ']');

    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $saved = $news->save($attr);
    } else {
      $this->add_message('error', validation_errors());
    }

    $data['message'] = self::setMessage(false);

    $left_data['news_list'] = CmsNews::list_news($this->default_category_id, FALSE, $limit_by_author);

    $right_data = array();
    //$news->list_revisions();
    $right_data['currentItem'] = $news;
    $right_data['front_theme'] = $this->front_theme;
    $right_data['status_array'] = array('0' => 'Draft', '1' => 'Published', '2' => 'Archived');
    $right_data['schedule_array'] = array('0' => 'No Schedule', '1' => 'Scheduled');
    $right_data['form'] = self::_edit_form($news);
    $right_data['java_script'] = 'modules/' . $this->module_url . '/js/news_edit.js';
    $right_data['css'] = 'modules/' . $this->module_url . '/css/news_edit.css';

    //load all utraining products
    $right_data['items_select'] = modules::run('training/controller/get_news_items_select', $id);
    //load all connected items
    $items = $this->news_model->get_items_for_news($id);
    if (empty($items)) {
      $right_data['items_msg'] = 'No item associated with this news.';
    } else {
      $right_data['items_msg'] = '<a onClick="return confirmDelete()" href="/hotcms/news/delete_items/' . $id . '">Delete all items</a>';
      $right_data['items'] = $items;
    }

    $data['messages'] = $this->load_messages();
    self::loadBackendView($data, 'news/news_leftbar', $left_data, 'news/news_edit', $right_data);
  }

  /**
   * Save news into database using Ajax
   * @param  int  news ID
  * @param  str  addtional measure to be taken besides saving, such as publish or archive
   */
  public function ajax_save($news_id, $sidekick='') {
    // assign validation rules
    $this->form_validation->set_rules('title', 'lang:hotcms_title', 'trim|required|unique_news[' . $news_id . ']');

    $updated = TRUE;
    $messages = '';
    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $news = new CmsNews($news_id);
      $updated = $updated && $news->save($attr, $sidekick);
      $messages = $news->messages() . $news->errors();
    } else {
      $result = FALSE;
      $error = validation_errors();
      $messages .= strip_tags($error) . "\n";
    }
    $json = array('result' => $updated, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Previews a news revision/draft
   * @param  int  news id
   * @param  int  revision id. if rid = 0 loads the latest draft
   */
  public function preview($id, $rid) {
    //TODO: save all changes in all tabs before previewing
    $news = new CmsNews($id);
    if ($rid == 0) {
      $slug = $news->draft->slug;
    } else {
      $slug = $news->get_revision($rid)->slug;
    }
    $site_id = $this->session->userdata("siteID");
    $this->load->model("site/site_model");
    $preview_site = $this->site_model->get_site_by_id($site_id);
    $this->load->model("page/page_model");
    $news_item_page = $this->page_model->get_news_item_page($site_id);
    $url = rtrim($news_item_page->url, "*") . $slug;
    set_cookie('preview_news', $id . ':' . $rid . ':' . $slug, 300);
    redirect("http://" . $preview_site->domain . "/" . $url);
    exit;
  }

  /**
   * Updates a text section
   * @param  int  news id
   */
  public function ajax_update_body($id) {
    $id = (int) $id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      $news = new CmsNews($id);
      $draft = $news->draft;
      if ($this->input->post() !== FALSE || is_null($this->input->post()) ) {
        $content = $this->input->post('txtTinyMCE');
        try {
          $result = $draft->update_body($content);
          $messages = $draft->messages() . $draft->errors();
        } catch (Exception $e) {
          $messages = 'There was an error when trying to update body: ' . $e->getMessage();
        }
      }
    } else {
      $messages = 'News not found.';
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Updates a text section
   * @param  int  news id
   */
  public function ajax_update_image($news_id, $asset_id) {
    $id = (int) $news_id;
    $asset_id = (int) $asset_id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      $news = new CmsNews($id);
      $draft = $news->draft;
      try {
        $result = $draft->update_image($asset_id);
        $messages = $draft->messages() . $draft->errors();
      } catch (Exception $e) {
        $messages = 'There was an error when trying to update image: ' . $e->getMessage();
      }
    } else {
      $messages = 'News not found.';
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Displays news draft body
   * @param int news id
   */
  public function ajax_display_body($id) {
    $id = (int) $id;
    if ($id > 0) {
      $news = new CmsNews($id);
      $draft = $news->draft;
      echo $draft->body;
    }
    exit;
  }

  /**
   * Revert a news draft to a previous version
   * note: never directly revert to live version, always to draft then (maybe) update and publish
   * @param int news id
   * @param int revision id
   */
  public function ajax_revert($id, $rid) {
    $news = new CmsNews($id);
    $draft = $news->draft;
    $result = $draft->revert_to_revision($rid);
    $messages = $draft->messages() . $draft->errors();
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  private function _edit_form($currentItem) {
    $draft = $currentItem->draft;
    $data = array();
    $data['hidden_fields'] = array();
    $data['featured_image_id'] = $currentItem->featured_image_id;
    $data['hidden_fields']['news_id'] = $currentItem->id;
    $data['hidden_fields']['revision_id'] = $currentItem->revision_id;

    $data['title_input'] = $this->_create_text_input('title', $draft->title, 100, 40, 'draft');
    //$data['meta_title_input'] = $this->_create_text_input('meta_title', $currentItem->meta_title, 100, 40,'draft');
    //$data['meta_keyword_input'] = $this->_create_text_input('meta_keyword', $currentItem->meta_keyword, 100, 20, 'draft');
    //$data['meta_description_input'] = $this->_create_text_input('meta_description', $currentItem->meta_description,100,20,'draft');
    $data['snippet_input'] = array(
        'name' => 'snippet',
        'id' => 'snippet',
        'value' => $draft->snippet,
        'rows' => '4',
        'cols' => '80',
        'class' => 'draft',
    );
    $data['body_input'] = array(
        'name' => 'body',
        'id' => 'body',
        'value' => $draft->body,
        'rows' => '8',
        'cols' => '80',
    );
    //$data['scheduled_input'] = $this->_create_checkbox_input('scheduled', 'scheduled', '1', $currentItem->scheduled==1, 'schedule');
    $data['scheduled_publish_date_input'] = $this->_create_text_input('scheduled_publish_date', $currentItem->scheduled_publish_timestamp > 0 ? date('Y-m-d H:i:s', $currentItem->scheduled_publish_timestamp) : '', 100, 20, 'schedule');
    $data['scheduled_archive_date_input'] = $this->_create_text_input('scheduled_archive_date', $currentItem->scheduled_archive_timestamp > 0 ? date('Y-m-d H:i:s', $currentItem->scheduled_archive_timestamp) : '', 100, 20, 'schedule');
    //$data['enable_comments_input'] = $this->_create_checkbox_input('enable_comments', 'enable_comments', '1', $currentItem->enable_comments==1, '');

    return $data;
  }

  /**
   * Calling delete function from model class
   * @param  int  id of item
   * @return void
   */
  public function delete($id) {
    // check permission
    if (!((has_permission('create_news') && $news->author_id == $this->user_id)
            || has_permission('edit_news') || has_permission('manage_news'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $news = new CmsNews($id);
    $news->delete();
    redirect($this->module_url);
  }

  /**
   * load all available assets
   * @return array
   */
  public function load_assets() {
    $this->load->model('asset/model_asset');
    return $this->model_asset->get_all_assets();
  }

  /**
   * Delete all users for location
   * @param  int  news id
   */
  public function delete_items($news_id) {
    $this->news_model->delete_all_items_for_news($news_id);
    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_item');

    $this->edit($news_id);
  }

  /**
   * Delete all items for news
   * @param  int  news id
   */
  public function delete_item($item_id, $news_id) {
    $this->news_model->delete_item_for_news($item_id, $news_id);
    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_item');

    $this->edit($news_id);
  }

  /**
   * Edit items for news (add/remove)
   * @param  int  news id
   */
  public function edit_items($news_id) {
    $this->news_model->delete_all_items_for_news($news_id);
    $items = $this->input->post('training_items');
    if (!empty($items)) {
      foreach ($items as $item_id) {
        $this->news_model->add_item($news_id, $item_id);
      }
      $message = array();
      $message['type'] = 'confirm';
      if (sizeof($items) == 1) {
        $message['value'] = $this->lang->line('hotcms_user_added');
      } else {
        $message['value'] = $this->lang->line('hotcms_users_added');
      }
    } else {
      $message['type'] = 'confirm';
      $message['value'] = $this->lang->line('hotcms_users_removed');
    }

    redirect('/news/edit/' . $news_id . '#page-items');
    //$this->edit($news_id);
  }

  /**
   * Image selection form
   * @param  string  asset ID
   * @param  string  training ID
   * @return string
   */
  public function ajax_image_chooser($asset_id = 0, $training_id = 0) {
    $result = FALSE;
    $messages = '';
    $content = '';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('asset_id', $attr) && $attr['asset_id'] > 0 && $training_id > 0) {
      $result = $this->training_model->asset_update($field_id, $attr);
    } else {
      $result = TRUE;
    }

    $image = NULL;
    $asset_id = (int) $asset_id;
    if ($asset_id > 0) {
      $image = asset_load_item($asset_id);
    }
    $data['asset_id'] = $asset_id;
    $data['image'] = $image;

    $asset_category_id = 1; // default image category
    $data['asset_category_id'] = $asset_category_id;
    // build the config form
    $category_context = 'news_images';
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
    $content = $this->load->view('news_image_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

  public function eaImport(){

      //      $attr = $this->input->post();

      $eanews = $this->news_model->load_ea_news();

      foreach($eanews as $eanew){
          $attr = '';

          $time = mktime(0, 0, 0, substr($eanew->Date, 5,2), substr($eanew->Date, 8,2), substr($eanew->Date, 0,4));

          //var_dump(html_entity_decode($eanew->Text));
          //die();

          $attr = array ('title' => $eanew->Title, 'sumary' =>  $eanew->Summary, 'created' =>  $time,'text' =>  html_entity_decode($eanew->Text));

          $news_id = $this->news_model->insert_news_ea($attr);

          echo $news_id.'<br />';
      }



      die('done');
  }

}

?>
