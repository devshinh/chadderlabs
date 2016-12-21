<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Page Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */

class Page extends HotCMS_Controller {

  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_content')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->config('page', TRUE);
    $this->load->library('CmsPage');
    $this->load->model('menu/model_menu_item');
    $this->load->helper('menu/menu_item');

    $this->module_url = $this->config->item('module_url', 'page');
    $this->module_header = $this->lang->line( 'hotcms_page_publisher' );
    $this->add_new_text = $this->lang->line( 'hotcms_add_new' ).' '.strtolower($this->lang->line( 'hotcms_page' ));

    $this->java_script = 'modules/'.$this->module_url.'/js/'.$this->config->item('js', 'page');
    $this->css = 'modules/'.$this->module_url.'/css/'.$this->config->item('css', 'page');
    $this->demo_text = $this->config->item('demo_text', 'page');
    $this->front_theme = $this->config->item('theme');
    $this->widget_library = NULL;
  }

  /**
   * list all pages
   * @param  int  page number
   */
  public function index($page_num = 1)
  {
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;
    $right_data['css'] = $this->css;

    // paginate configuration
    $this->load->library('pagination');
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = 10;

    $data += $this->get_view_data_for_sitemap();

    //if (isset($data['aCurrent'])) {
    //  $right_data['aCurrent'] = $data['aCurrent'];
    //}
    //else {
      $pagination_config['total_rows'] = CmsPage::count_all();
      $right_data['aCurrent'] = CmsPage::list_pages($page_num, $pagination_config['per_page']);
    //}

    // paginate
    $this->pagination->initialize($pagination_config);
    $right_data['pagination'] = $this->pagination->create_links();
    self::loadBackendView($data, 'menu/menu_item_page_publisher', NULL, 'page/page_home', $right_data);
  }

  private function get_view_data_for_sitemap()
  {
    /* load tree view on left */
    $menu_group_id = $this->model_menu_item->get_primary_menu_group();
    $menu_item_records = $this->model_menu_item->get_all_root_menu_items_by_menu_id($menu_group_id);
    $menu_items = Menu_item::build_menu_item_array($menu_item_records, $this->model_menu_item);
    $left_data['aCurrent'] = CmsPage::list_pages();
    $left_data['currentMenuItems'] = $menu_items;
    $left_data['currentMenuGroupId'] = $menu_group_id;
    return $left_data;
  }

 /**
  * Set validation rules
  *
  private function validation_rules() {
    // assign validation rules
    $this->form_validation->set_rules( 'name', 'lang:hotcms_name', 'trim|required' );
    $this->form_validation->set_rules( 'url', 'lang:hotcms_url', 'trim|required' );
  }

 /**
  * Validates a page name to make sure no redundant
  *
  public function _validate_name($name) {
    $url = format_url($name);
//$this->firephp->fb($url);
    if ($this->page_model->url_exists($url)) {
      $this->form_validation->set_message('_validate_name', 'The page name/URL already exists.');
      return FALSE;
    }
    else {
      return TRUE;
    }
  } */

  /*
  private function radiobutton_selected()
  {
    die('test');
    if ($str == 'test')
    {
      $this->form_validation->set_message('username_check', 'The %s field can not be the word "test"');
      return FALSE;
    }
    else
    {
      return TRUE;
    }
  }
  */

 /**
  * Create a new page
  */
  public function create()
  {
    //$this->output->enable_profiler(TRUE);
    // check permission
    if (!(has_permission('manage_content') || has_permission('create_content'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $aData['module_url'] = $this->module_url;
    $aData['module_header'] = $this->module_header;
    $aData['add_new_text'] = $this->add_new_text;
    //$aData['aModuleInfo'] = $this->aModuleInfo;
    $linktype = $this->input->post('linktype');

    $options = array();
    //$this->_create_checkbox_input('linktype', 'linktype', $option['key'], false);
    $options[] = array('key' => 'normal','value' => 'Normal Page','selected' => $linktype=='normal');
    //$options[] = array('key' => 'internal','value' => 'Internal Redirect','selected' => $linktype=='internal');
    $options[] = array('key' => 'external','value' => 'Redirect','selected' => $linktype=='external');

    $this->form_validation->set_rules( 'linktype', 'Page Type', 'trim|required');
    $this->form_validation->set_rules( 'name', 'lang:hotcms_name', 'trim|required|unique_page' );

    //if($linktype == 'external') {
      //$this->form_validation->set_rules( 'link_url', 'External URL',  'trim|required' );
    //}
    if ($this->form_validation->run() === TRUE) {
      switch($linktype)
      {
        case "normal":
          $this->create_new_page();
          break;
        case "external":
          $this->create_new_link();
          break;
      }
    }
    elseif (validation_errors() > "") {
      $this->add_message('error', validation_errors());
    }

    // general page options
    $right_data['linktype'] = $linktype;
    $right_data['page_type_options'] = $options;
    $right_data['name_input'] = $this->_create_text_input('name', $this->input->post( 'name' ),100,20,'draft');
    //$right_data['title_input'] = $this->_create_text_input('meta_title', $this->input->post( 'meta_title' ),100,20,'draft');
    $right_data['url_input'] = $this->_create_text_input('url', $this->input->post( 'url' ),100,20,'draft');

    // external link specific
    $right_data['link_external'] = $this->_create_text_input('link_url', $this->input->post( 'link_url'),100,20,'text');

    $this->load_messages();

    $left_data = $this->get_view_data_for_sitemap();
    $left_data += $aData;
    $aData['leftbar'] = $this->load->view('menu/menu_item_page_publisher', $left_data, TRUE);
    $aData['main_area'] = $this->load->view('page/page_create', $right_data, TRUE);
    $this->sitemap->generateXML();

    self::loadBackendView($aData,'menu/menu_item_page_publisher',$left_data,'page/page_create',$right_data);


  }

  private function create_new_link()
  {
    //$site_id = $this->session->userdata('siteID');
    $attr = $this->input->post();
    $this->model_menu_item->create_menu_item_external_link(0, $attr["name"], $attr["link_url"], (int)($attr["active"]), 'system');
    redirect('/page/');
  }

  private function create_new_page()
  {
    $attr = $this->input->post();
    $page = new CmsPage;
    $page->create($attr);

    if ($page->id > 0) {
      $this->create_menu($page->id, $attr);
      $this->add_message('confirm', $this->lang->line( 'hotcms_created_item' ));
      /*
      $site_id = $this->session->userdata('siteID');
      $hidden = !isset($attr["active"]);
      $this->model_menu_item->create_menu_item_from_content(0,$attr["name"],$attr["url"],$page_id, $hidden);
      */
      redirect('/page/edit/' . $page->id);
      exit;
    }
  }

 /**
  * Edit an existing page
  * @param  int  page id
  * @param  str  if $sidekick == reset, discard all changes in draft and revert to the latest revision
  * TODO: prevent widget forms from interfering with page edit form
  */
  public function edit($id, $sidekick = NULL)
  {
    $aData['module_url'] = $this->module_url;
    $aData['module_header'] = $this->module_header;
    $aData['add_new_text'] = $this->add_new_text;

    $linktype = $this->input->post('linktype');
    $options = array();
    $options[] = array('key' => 'normal','value' => 'Normal','selected' => $linktype=='normal');
    $options[] = array('key' => 'internal','value' => 'Internal Redirect','selected' => $linktype=='internal');
    $options[] = array('key' => 'external','value' => 'External Link to','selected' => $linktype=='external');

    $this->form_validation->set_rules( 'name', 'lang:hotcms_name', 'trim|required' );
    $this->form_validation->set_rules( 'url', 'lang:hotcms_url', 'trim|required|unique_page[' . $id . ']' );
    $this->form_validation->set_rules( 'layout_id', 'template', 'required' );

    $page = new CmsPage($id);
    $draft = $page->draft;

    // check permission
    if (!((has_permission('create_content') && $page->author_id == $this->user_id)
      || has_permission('update_content') || has_permission('manage_content'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    if ($sidekick == 'reset') {
//      $page->revert_to_revision();
      redirect('/page/edit/' . $page->id);
      exit;
    }

    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $saved = $page->save($attr);
      $menu_saved = $this->update_menu($id, $attr);
      // TODO: handle multiple messages
      /*
      if($menu_saved)
      {
        $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => 'Page was updated.' ) );
      } else {
        $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => 'Page was updated. But there was a problem updating the menu.' ) );
      }
      */
    }
    else {
      $this->add_message('error', validation_errors());
    }

    //$aData['message'] = self::setMessage(false);

    if (!is_array($this->widget_library)) {
      $this->widget_library = $this->list_widgets();
    }

    //$left_data = $this->get_view_data_for_sitemap();
    $aData += $this->get_view_data_for_sitemap();
    // render widgets
    if (is_array($draft->sections) && count($draft->sections) > 0) {
      foreach ($draft->sections as $section) {
        if ($section->section_type == 1) {
          $section->widget_name = $this->widget_library[$section->module_widget]->widget_name;
          $section->content = $this->render_widget_section($section, TRUE);
        }
        else {
          $section->widget_name = 'Text';
        }
      }
    }
    $right_data['currentItem'] = $page;
    $right_data['front_theme'] = $this->front_theme;
    $right_data['layouts'] = $draft->layouts;
    $right_data['zones'] = $draft->zones;
    $right_data['java_script'] = 'modules/'.$this->module_url.'/js/page_edit.js';
    $right_data['java_script'] .= ' '.'modules/'.$this->module_url.'/js/fileuploader.js';
    $right_data['css'] = 'modules/'.$this->module_url.'/css/page_edit.css'.' '.'modules/'.$this->module_url.'/css/fileuploader.css';
    $right_data['asset_data']['asset_type'] = array('1'  => 'image');
    $right_data['asset_data']['asset_file_input'] = $this->_create_text_input('asset_file', $this->input->post( 'asset_file' ),100,20,'');
    $right_data['asset_data']['asset_name_input'] = $this->_create_text_input('asset_name', $this->input->post( 'name' ),100,20,'text');
    //$right_data['asset_data']['asset_description_input'] = $this->_create_text_input('asset_name', $this->input->post( 'name' ),100,20,'text');
    $right_data['asset_data']['asset_description_input'] = array(
      'name'        => 'asset_description',
      'id'          => 'asset_description',
      'value'       => set_value( 'asset_description', $this->input->post( 'asset_description' ) ),
      'rows'        => '5',
      'cols'        => '20',
      'class'       => 'textarea'
    );

    // general page form and options
    $right_data['form'] = self::_edit_form($draft);
    $right_data['linktype'] = $linktype;
    $right_data['page_type_options'] = $options;
    $right_data['widget_array'] = $this->widget_library;
    $right_data['status_array'] = array('0'=>'Draft', '1'=>'Published', '2'=>'Archived');
    $right_data['module_array'] = $this->list_modules(TRUE);
    $right_data['hour_array'] = array('00'=>'00','01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08',
      '09'=>'09','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20',
      '21'=>'21','22'=>'22','23'=>'23');
    $right_data['minute_array'] = array('00' => '00','30' => '30');
    $right_data['timezone_array'] = list_timezone();

    // menu form
    $page->load_menu();
    $right_data['menu_form']['menu_title'] = $this->_create_text_input('menu_title', ($page->menu ? $page->menu->title : ''), 100, 20, 'page_menu');
    $right_data['menu_form']['menu_visible'] = $this->_create_checkbox_input('menu_visible','menu_visible','1', ($page->menu ? $page->menu->hidden==0 : FALSE), '', 'page_menu');

    //$aData['leftbar'] = $this->load->view('menu/menu_item_page_publisher', $aData, TRUE);
    //$aData['main_area'] = $this->load->view('page/page_edit', $right_data, TRUE);
    //$this->load->view('global', $aData);
    $this->load_messages();
    self::loadBackendView($aData, 'menu/menu_item_page_publisher', $aData, 'page/page_edit', $right_data);

    //self::loadBackendView($aData,'menu/menu_item_page_publisher',$left_data,'page/page_edit',$right_data);
    //$moduleView = $this->load->view('page_edit', $aData, true);
    //self::loadView($moduleView);
  }

 /**
  * Saves a page using Ajax
  * @param  int  page ID
  * @param  str  addtional measure to be taken besides saving, such as publish or archive
  */
  public function ajax_save($page_id, $sidekick='')
  {
    // assign validation rules
    $this->form_validation->set_rules( 'name', 'lang:hotcms_name', 'trim|required' );
    $this->form_validation->set_rules( 'url', 'lang:hotcms_url', 'trim|required|unique_page[' . $page_id . ']' );
    $this->form_validation->set_rules( 'layout_id', 'template', 'required' );

    $updated = TRUE;
    $messages = '';
    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $page = new CmsPage($page_id);
      $updated = $updated && $page->save($attr, $sidekick);
      if ($attr['menu_updated'] == "1") {
        $page->load_menu();
        if (array_key_exists('menu_visible', $attr)) {
          $visible = $attr['menu_visible'];
        }
        else {
          $visible = 0;
        }
        $updated = $updated && $page->update_menu($attr['menu_title'], $visible);
      }
      $messages = $page->messages() . $page->errors();
    }
    else {
      $error = validation_errors();
      $messages .= strip_tags($error) . "\n";
    }
    $json = array('result' => $updated, 'messages' => $messages);
    if($sidekick == 'publish'){
      $this->sitemap->generateXML();
    }
    echo json_encode($json);
  }

  /**
   * Publishes a page in an Ajax call
   * @param int page ID
   */
  public function ajax_publish($page_id)
  {
    $page = new CmsPage($page_id);
    $result = $page->publish();
    $messages = $page->messages() . $page->errors();
    $json = array('result' => $result, 'messages' => $messages);
    $this->sitemap->generateXML();
    echo json_encode($json);
  }

  /**
   * Archives a page in an Ajax call
   * @param int page ID
   *
  public function archive($page_id)
  {
    $page = new CmsPage($page_id);
    $updated = $page->archive();
    $messages = $page->messages() . $page->errors();
    $json = array('result' => $updated, 'messages' => $messages);
    echo json_encode($json);
  } */

  public function sitemap_edit($id, $sidekick = NULL)
  {
    //$aData['message'] = self::setMessage(false);
    //var_dump($this->session);
    $left_data = $this->get_view_data_for_sitemap();
    $menu_item_record = $this->model_menu_item->get_menu_item_by_id($id);
    $menu_enabled = ($menu_item_record->hidden == 0);
    $menu_deletable = !$this->model_menu_item->has_children($id);

    $title = $menu_item_record->title;
    $url = $menu_item_record->path;

    if ($this->input->post()) {
      $title = $this->input->post('name');
      $url = $this->input->post('url');
      $menu_enabled = $this->input->post('menu_enabled') != NULL;
    }

    if ($menu_item_record->page_id != NULL) {
      redirect('page/edit/' . $menu_item_record->page_id . '/' . $sidekick);
    }

    //$aData['form'] = self::_edit_form_menu_item($aData['current_item']);
    $this->load->vars(array('module_url' => $this->module_url));
    $this->load->vars(array('module_header' => 'Edit Link'));

    $this->form_validation->set_rules( 'name', 'lang:hotcms_name', 'trim|required' );
    $this->form_validation->set_rules( 'url', 'lang:hotcms_url', 'trim|required|unique_page[' . $menu_item_record->page_id . ']' );

    if ($this->form_validation->run()) {
      //$site_id = $this->session->userdata('siteID');
      $this->model_menu_item->update_menu_item_external_link($id,$title,$url,$menu_enabled);
      redirect('/page');
    }
    else {
      //$this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => validation_errors() ) );
      //$aData['message'] = self::setMessage(false);
      $this->add_message('error', validation_errors());
    }

    $right_data['menu_item_record'] = $menu_item_record;
    $right_data['name_input'] = $this->_create_text_input('name', $title,100,20,'text');
    $right_data['url_input'] = $this->_create_text_input('url', $url,100,20,'text');
    $right_data['menu_enabled'] = $menu_enabled;
    $right_data['menu_deletable'] = $menu_deletable;
    $right_data['linktype'] = 'external';

    $this->load_messages();
    self::loadBackendView($aData,'menu/menu_item_page_publisher',$left_data,'menu/menu_link_item_edit_publisher',$right_data);
  }

  public function sitemap_delete($id)
  {
    if($this->model_menu_item->has_children($id)) {
      $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => 'Cannot delete menu unless menu has no children' ) );
    }
    else {
      $this->model_menu_item->delete_by_id($id);
      $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => lang( 'hotcms_updated_item' ) ) );
      redirect('/page');
    }
    $this->sitemap_edit($id);
  }

  public function save_content_layout($id)
  {
    $menu_data_string = $this->input->post('menu_data');
    $menu_data = json_decode($menu_data_string);
    $menu_items = Menu_item::parse_menu($menu_data->menu_items);
    $this->model_menu_item->update_menu($id, $menu_items);
    redirect('/page/');
  }

  /**
   * Previews a page revision/draft
   * @param  int  page id
   * @param  int  revision id. if rid = 0 loads the latest draft
   */
  public function preview($pid, $rid)
  {
    //TODO: save all changes in all tabs before previewing
    if ($rid == 0) {
      $page = new CmsPage($pid);
      $url = $page->draft->url;
    }
    else {
      $page = new CmsPage($pid, 'revision', $rid);
      $url = $page->load_revision($rid)->url;
    }
    $this->load->library('training/CmsTraining');
    set_cookie('previewing', $pid . ':' . $rid . ':' . $url, 300);
    //$page->load_revision($pid, $rid);
    //$page->render_content();
    //echo "<pre>";
    //echo $page->url;
    //echo "</pre>";
    $site_id = $this->session->userdata("siteID");
    $this->load->model("site/site_model");
    $preview_site = $this->site_model->get_site_by_id($site_id);
//    redirect('http://' . $_SERVER['HTTP_HOST'] . '/' . $url);
    redirect('http://' . $preview_site->domain . '/' . $url);
    exit;
  }

  /**
   * Inserts a section to a page
   * @param int page id
   * @param int section type
   * @param str section zone
   * @param str section widget code
   * @param str module name/path
   */
  public function ajax_add_section($page_id, $type = 0, $zone = '', $widget = '')
  {
    $page = new CmsPage($page_id);
    $section_id = $page->add_section($type, $zone, $widget);
    //$section_id = $draft->insert_section($id, $type, $zone, $widget);
    $result = ($section_id > 0);
    $messages = $page->messages() . $page->errors();
    $json = array('result' => $result, 'section_id' => $section_id, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Deletes a section from a page
   * @param  int  page id
   * @param  int  section id
   */
  public function ajax_delete_section($page_id, $section_id)
  {
    $page_id = (int)$page_id;
    $section_id = (int)$section_id;
    $page = new CmsPage($page_id);
    $result = $page->delete_section($section_id);
    $messages = $page->messages() . $page->errors();
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Updates a text section on a page
   * @param  int  page id
   * @param  int  section id
   */
  public function ajax_update_section($page_id, $section_id)
  {
    $page_id = (int)$page_id;
    $section_id = (int)$section_id;
    $updated = FALSE;
    $messages = '';
    if ($page_id > 0 && $section_id > 0) {
      $page = new CmsPage($page_id);
      try {
        if ($this->input->post() !== FALSE) {
          $content = $this->input->post('txtTinyMCE');
          $updated = $page->update_section($section_id, $content);
          $messages = $page->messages() . $page->errors();
        }
      }
      catch (Exception $e) {
        $messages = 'There was an error when trying to update section: ' . $e->getMessage();
      }
    }
    else {
      $messages = 'Section not found.';
    }
    $json = array('result' => $updated, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Rearranges a section on a page
   * @param  int  page id
   * @param  int  section id
   */
  public function ajax_rearrange_section($page_id, $section_id)
  {
    $page_id = (int)$page_id;
    $section_id = (int)$section_id;
    $updated = FALSE;
    $messages = '';
    if ($page_id > 0 && $section_id > 0) {
      $page = new CmsPage($page_id);
      try {
        if ($this->input->post() !== FALSE) {
          $zone = $this->input->post('zone');
          $sequence = $this->input->post('sequence');
          $updated = $page->rearrange_section($section_id, $zone, $sequence);
          $messages = $page->messages() . $page->errors();
        }
      }
      catch (Exception $e) {
        $messages = 'There was an error when trying to rearrange sections: ' . $e->getMessage();
      }
    }
    else {
      $messages = 'Section not found.';
    }
    $json = array('result' => $updated, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Configure a widget section on a page
   * TODO: output in JSON format
   * @param  int  page id
   * @param  int  section id
   */
  public function ajax_config_section($page_id, $section_id)
  {
    $page_id = (int)$page_id;
    $section_id = (int)$section_id;
    if ($page_id > 0 && $section_id > 0) {
      $page = new CmsPage($page_id);
      $section = $page->load_section($section_id);
      $code = explode(":", $section->module_widget);
      if ($code[0] == '' || $code[1] == '') {
        echo 'error';
        exit;
      }
      $admin_widget = $code[0] . '/' . $code[1] . '_admin';
      //$this->load->helper($code[0] . '/' . $code[0]);
      //if (function_exists($config_function)) {
        $configs = NULL;
        try {
          // process post
          if ($this->input->post() !== FALSE) {
            $args = $this->input->post();
            $args['page_section_id'] = $section_id;
            $args['postback'] = 'TRUE';
            //$configs = $config_function($args);
            $configs = widget::run($admin_widget, $args);
            // save config into section
            $conf_str = serialize($configs);
            $page->update_section($section_id, $conf_str);
          }
          else {
            if ($section->content > '') {
              $configs = unserialize($section->content);
              if (!is_array($configs)) {
                $configs = array();
              }
            }
            else {
              $configs = array();
            }
          }
          $configs['page_section_id'] = $section_id;
          //$widget_output = $config_function($configs);
          $widget_output = widget::run($admin_widget, $configs);
          echo $widget_output;
          exit;
        }
        catch (Exception $e) {
          echo 'error';
          exit;
        }
        //$section_content = '<div class="section-widget ' . $section->style_class . '">' . $widget_output . "</div>";
      //}
    }
    echo 'error';
    exit;
  }

  /**
   * Displays a section on a page
   * @param int section id
   */
  public function ajax_display_section($section_id)
  {
    $section_id = (int)$section_id;
    if ($section_id > 0) {
      $page = new CmsPage;
      $section = $page->load_section($section_id);
      if ($section->section_type == 1) {
        $str = $this->render_widget_section($section, TRUE);
      }
      else {
        $str = $section->content;
      }
      echo $str;
    }
    exit;
  }

  /**
   * Renders a widget section
   * @param  object section
   * @param  bool   whether to display widget errors
   */
  private function render_widget_section($section, $show_errors = FALSE)
  {
    $section_content = '';
    $args = NULL;
    if ($section->content > '') {
      $args = unserialize($section->content);
    }
    if (!is_array($args)) {
      $args = array();
    }
    $method_parts = explode(":", $section->module_widget);
    if ($method_parts[0] == '' || $method_parts[1] == '') {
      if ($show_errors) {
        $section_content = '<p>Widget not defined</p>';
      }
      return $section_content;
    }

    try {
      $widget_output = widget::run($method_parts[0] . '/' . $method_parts[1], $args);
      if (is_array($widget_output) && array_key_exists('content', $widget_output)) {
        $widget_output = $widget_output['content'];
      }
    }
    catch (Exception $e) {
      if ($show_errors) {
        $widget_output = 'Widget disabled: ' . $e->getMessage() . "\n";
      }
      else {
        $widget_output = '<p>Widget preview disabled.</p>';
      }
    }
    $section_content = '<div class="section-widget ' . $section->style_class . '">' . $widget_output . "</div>";
    return $section_content;

    /*
    $this->load->helper($method_parts[0] . '/' . $method_parts[0]);
    if (function_exists($method_parts[1])) {
      try {
        $widget_output = $method_parts[1]($args);
      }
      catch (Exception $e) {
        if ($show_errors) {
          $widget_output = 'Widget disabled: ' . $e->getMessage() . "\n";
        }
        else {
          $widget_output = '<p>Widget preview disabled.</p>';
        }
      }
      $section_content = '<div class="section-widget ' . $section->style_class . '">' . $widget_output . "</div>";
    }
    else {
      if ($show_errors) {
        $section_content = '<p>Widget function is missing.</p>';
      }
    } */
    return $section_content;
  }

  /**
   * Revert a page to a previous version
   * @param int page id
   * @param int revision id
   */
  public function ajax_revert($pid, $rid)
  {
    $page = new CmsPage($pid);
    $result = $page->revert_to_revision($rid);
    $messages = $page->messages() . $page->errors();
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  private function create_menu($page_id, $attr)
  {
    if (array_key_exists('url', $attr) && $attr["url"] > '') {
      $url = format_url($attr["url"]);
    }
    if ($url == '') {
      $url = format_url($attr["name"]);
    }
    $hidden = !isset($attr["active"]);
    $this->model_menu_item->create_menu_item_from_content(0, $attr["name"], $url, $page_id, $hidden);
  }

  private function update_menu($page_id, $attr)
  {
    $hidden = !isset($attr["active"]);
    //var_dump($hidden);
    //will return false if menu could not be updated
    return $this->model_menu_item->update_menu_item_from_content(0, $attr["name"], $attr["url"], $page_id, $hidden);
  }

  private function _edit_form($currentItem)
  {
    $aData = array();
    $aData['hidden_fields'] = array();
    $aData['hidden_fields']['page_id'] = $currentItem->id;
    $aData['hidden_fields']['revision_id'] = $currentItem->revision_id;
    $aData['hidden_fields']['editing_section'] = '0';
    $aData['hidden_fields']['draft_updated'] = '0';
    $aData['hidden_fields']['menu_updated'] = '0';
    $aData['hidden_fields']['status_updated'] = '0';
    $aData['hidden_fields']['section_updated'] = '0';
    $aData['hidden_fields']['demo_text'] = $this->demo_text;
    /*
    foreach ($currentItem->sections as $section) {
      $aData['hidden_fields']['section_type['.$section->id.']'] = $section->section_type;
      $aData['hidden_fields']['section_zone['.$section->id.']'] = $section->zone;
      $aData['hidden_fields']['section_sequence['.$section->id.']'] = $section->sequence;
      if ($section->section_type == 0) {
        $aData['hidden_fields']['section['.$section->id.']'] = $section->content;
      }
      else {
        $aData['hidden_fields']['section['.$section->id.']'] = $section->module_widget; // . '|' . $section->content;
      }
    } */
    $aData['name_input'] = $this->_create_text_input('name', $currentItem->name, 100, 40, 'draft');
    $aData['url_input'] = $this->_create_text_input('url', $currentItem->url, 100, 40, 'draft');
    $aData['heading_input'] = $this->_create_text_input('heading', $currentItem->heading, 100, 40,'draft');
    $aData['meta_title_input'] = $this->_create_text_input('meta_title', $currentItem->meta_title, 100, 40, 'draft');
    $aData['meta_description_input'] = $this->_create_text_input('meta_description', $currentItem->meta_description,100,20,'draft');
    $aData['meta_keyword_input'] = $this->_create_text_input('meta_keyword', $currentItem->meta_keyword, 100, 20, 'draft');
    $aData['style_sheet_input'] = $this->_create_text_input('style_sheet', $currentItem->style_sheet, 100, 20, 'draft');
    $aData['javascript_input'] = $this->_create_text_input('javascript', $currentItem->javascript,100, 20, 'draft');
    $aData['scheduled_publish_date_input'] = $this->_create_text_input('scheduled_publish_date', $currentItem->scheduled_publish_timestamp > 0 ? date('Y-m-d', $currentItem->scheduled_publish_timestamp) : '', 100, 20, 'schedule');
    $aData['scheduled_archive_date_input'] = $this->_create_text_input('scheduled_archive_date', $currentItem->scheduled_archive_timestamp > 0 ? date('Y-m-d', $currentItem->scheduled_archive_timestamp) : '', 100, 20, 'schedule');

    $aData['exclude_sitemap_input'] = $this->_create_checkbox_input('exclude_sitemap','exclude_sitemap',1, $currentItem->exclude_sitemap==1, '','draft');
    //$aData['active_input'] = $this->_create_checkbox_input('active','active','accept', $currentItem->status==1, '');

    $aData['roles'] = $this->permission->list_roles();
    $aData['allowed_roles'] = $currentItem->allowed_roles;
    foreach ($aData['roles'] as $k => $v) {
      $aData['permissions'][$k] = $this->_create_checkbox_input('permissions['.$k.']', 'permissions_'.$k, '1', in_array($k, $aData['allowed_roles']), '', 'draft');
    }

    return $aData;
  }

 /**
  * Calling delete function from model class
  * @param  int  id of item
  * @return void
  */
  public function delete($id)
  {
    // check permission
    if (!((has_permission('create_content') && $page->author_id == $this->user_id)
      || has_permission('update_content') || has_permission('manage_content'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    if ($this->model_menu_item->delete_by_page_id($id)) {
      $page = new CmsPage($id);
      $page->delete();
      redirect('/page/');
    }
    else {
      //TODO: output error message
      $this->index();
    }
  }

 /**
  * load all available assets
  * @param
  */
  public function load_assets()
  {
    $this->load->model('asset/model_asset');
    return $this->model_asset->get_all_assets();
    //die(var_dump($assets));
  }

 /**
  * list all available modules
  * @param  bool  includes an empty row
  * @return array
  */
  public function list_modules($empty_row = FALSE)
  {
    $this->load->model('module/model_module');
    $modules = $this->model_module->get_all_modules();
    $result = array();
    if ($empty_row) {
      $result[] = ' -- select -- ';
    }
    foreach ($modules as $m) {
      if ($m->active == 1) {
        $result[$m->module_code] = $m->name;
      }
    }
    return $result;
  }

 /**
  * List all available module widgets
  * // TODO: move this function to module libarry
  * // TODO: add more info to the array, e.g. autodetected module css and js
  * @return array
  */
  public function list_widgets()
  {
    $this->load->model('module/model_module');
    $widgets = $this->model_module->list_widgets(TRUE);
    $result = array();
    foreach ($widgets as $w) {
      $result[$w->module_code . ':' . $w->widget_code] = $w;
    }
    return $result;
  }

}
?>
