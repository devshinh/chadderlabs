<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  CmsPage
 *
 * Author: jeffrey@hottomali.com
 *
 * Created on:  02.08.2012
 *
 * Description:  Page class handles page rendering, publishing, and revisions etc.
 */
class CmsPage extends CmsPageBasic {

  /**
   * page attributes
   */
  public $status;
  public $module;
  public $permissions;
  public $exclude_sitemap;

	public $create_timestamp;
	public $update_timestamp;
	public $publish_timestamp;
	public $archive_timestamp;
	public $scheduled_publish_timestamp;
	public $scheduled_archive_timestamp;

  public $menu;

  /**
   * private attributes
   */
	//private $autosave = FALSE; // switches on/off the draft autosave feature
	private $_draft;     // page draft object. to access this attribute, call $page->draft
	private $_revisions; // array of revision objects. to access this attribute, call $page->revisions
	private $_allowed_roles; // array of revision objects. to access this attribute, call $page->allowed_roles

  /**
   * __construct
   * @param  str  page ID or URL
   * @param  bool  only load live/published item
	 * @return void
   */
  public function __construct($identifier = NULL, $live_only = FALSE)
  {
    parent::__construct();
    if (!empty($identifier)) {
      if (is_numeric($identifier)) {
        $this->id = (int) $identifier;
      }
      else {
        $this->url = trim($identifier);
      }
      $this->load($live_only);
    }
  }

  /**
   * Retrieves the latest draft, for editing or previewing
   * @return mixed
   */
  protected function get_draft()
  {
    if ($this->id == 0) {
      return FALSE;
    }
    if (!isset($this->_draft)) {
      $this->_draft = new CmsPageDraft($this->id);
    }
    // if for some reason (e.g. was imported) the draft didn't exist, create one immediately
    if (empty($this->_draft) || $this->_draft->id == 0) {
      $this->ci->page_model->draft_insert($this);
      // and load the new one
      $this->_draft = new CmsPageDraft($this->id);
    }
    if (!empty($this->_draft)) {
      // inherited properties
      //$this->_draft->status = $this->status;
      $this->_draft->permissions = $this->permissions;
      $this->_draft->allowed_roles = $this->allowed_roles;
      $this->_draft->exclude_sitemap = $this->exclude_sitemap;
      $this->_draft->create_timestamp = $this->create_timestamp;
      $this->_draft->publish_timestamp = $this->publish_timestamp;
      $this->_draft->archive_timestamp = $this->archive_timestamp;
      $this->_draft->scheduled_publish_timestamp = $this->scheduled_publish_timestamp;
      $this->_draft->scheduled_archive_timestamp = $this->scheduled_archive_timestamp;
    }
    return $this->_draft;
  }

  /**
   * Retrieves page revisions
   * @return mixed
   */
  protected function get_revisions()
  {
    if ($this->id == 0) {
      return FALSE;
    }
    if (!isset($this->_revisions)) {
      $this->_revisions = $this->ci->page_model->revision_list($this->id);
    }
    return $this->_revisions;
  }

  /**
   * Retrieves roles that have permissions to access this page
   * @return array
   */
  protected function get_allowed_roles()
  {
    if ($this->id == 0) {
      return FALSE;
    }
    if (!isset($this->_allowed_roles)) {
      if ($this->permissions > '') {
        $this->_allowed_roles = unserialize($this->permissions);
      }
      else {
        $this->_allowed_roles = array();
      }
    }
    return $this->_allowed_roles;
  }

  /**
   * Retrieves a page
   * @param  bool  if true, load published pages only
   * @return void
   */
  public function load($live_only = TRUE)
  {
    // TODO: page caching
    if ($this->id < 1 && $this->url == '') {
      return FALSE;
    }
    $row = $this->ci->page_model->load_page($this->id, $this->url, $live_only);
    if ($row) {
      $this->id = $row->id;
      $this->revision_id = $row->revision_id;
      $this->status = $row->status;
      $this->module = $row->module;
      $this->permissions = $row->permissions;
      $this->exclude_sitemap = $row->exclude_sitemap;
      if (isset($row->widget_slug)) {
        $this->widget_slug = $row->widget_slug;
      }
      foreach ($this->ci->page_model->comm_props as $prop) {
        $this->$prop = $row->$prop;
      }
      $this->create_timestamp = $row->create_timestamp;
      $this->update_timestamp = $row->update_timestamp;
      $this->publish_timestamp = $row->publish_timestamp;
      $this->archive_timestamp = $row->archive_timestamp;
      $this->scheduled_publish_timestamp = $row->scheduled_publish_timestamp;
      $this->scheduled_archive_timestamp = $row->scheduled_archive_timestamp;
      $this->sections = $this->ci->page_model->list_page_sections($row->id);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Retrieves a revision
   * @param  int  page revision ID, if 0 or not set then load the latest version
   * @return object
   */
  public function load_revision($revision_id = 0)
  {
    $revision_id = (int)$revision_id;
    if ($this->id < 1 && $this->url == '') {
      return FALSE;
    }
    $revision = new CmsPageRevision($this->id, $revision_id);
    if (!empty($revision)) {
      // inherited properties
      $revision->permissions = $this->permissions;
      $revision->allowed_roles = $this->allowed_roles;
      $revision->exclude_sitemap = $this->exclude_sitemap;
      $revision->create_timestamp = $this->create_timestamp;
      $revision->archive_timestamp = $this->archive_timestamp;
      $revision->scheduled_publish_timestamp = $this->scheduled_publish_timestamp;
      $revision->scheduled_archive_timestamp = $this->scheduled_archive_timestamp;
    }
    return $revision;
  }

  /**
   * Renders content of a page/revision, including css and js
   * @param  string  theme name
   * @param  array   parameters including front-end post-backs
   * @return void
   *
  public function render_content($theme = '', $params = array())
  {
    $this->content = '';
    $this->style_sheets = array();
    $this->javascripts = array();
    if ($this->style_sheet > "") {
      $this->style_sheets = explode(',', trim($this->style_sheet));
    }
    if ($this->javascript > "") {
      $this->javascripts = explode(',', trim($this->javascript));
    }
    foreach ($this->style_sheets as $k => $v) {
      $this->style_sheets[] = 'themes/' . $theme . '/css/page/' . $v;
    }
    foreach ($this->javascripts as $k => $v) {
      $this->javascripts[] = 'themes/' . $theme . '/js/page/' . $v;
    }
    foreach ($this->zones as $zone) {
      $this->content .= '<div id="' . $zone . '" class="content-zone">';
      foreach ($this->sections as $section) {
        if ($section->zone != $zone) {
          continue;
        }
        if ($section->section_type == 0) {
          $this->content .= '<div class="section-text ' . $section->style_class . '">' . $section->content . "</div>";
          // add page related CSS and JS
          if (!empty($section->section_css)) {
            $section_styles = explode(' ', trim($section->section_css));
            foreach ($section_styles as $ss) {
              $this->style_sheets[] = '/asset/css/' . $ss;
            }
          }
          if (!empty($section->section_js)) {
            $section_js = explode(' ', trim($section->section_js));
            foreach ($section_js as $sj) {
              $this->javascripts[] = '/asset/js/' . $sj;
            }
          }
        }
        elseif ($section->section_type == 1 && $section->module_widget > '') {
          $args = array();
          if ($section->content > '') {
            $args = unserialize($section->content);
          }
          if ($this->widget_slug > '') {
            $args['slug'] = $this->widget_slug;
          }
          if (is_array($params) && array_key_exists('frontpostback', $params)) {
            $args['postback'] = $params;
          }
          $widget_output = '';
          $method_parts = explode(":", $section->module_widget);
          if ($method_parts[0] > '' && $method_parts[1] > '') {
            try {
              $widget_output = widget::run($method_parts[0] . '/' . $method_parts[1], $args);
            } catch (Exception $e) {
              $widget_output = '';
            }
          }
          if (!empty($widget_output)) {
            if (is_array($widget_output)) {
              if (array_key_exists('content', $widget_output)) {
                $this->content .= '<div class="section-widget ' . $section->style_class . '">' . $widget_output['content'] . "</div>";
              }
              if (array_key_exists('meta_title', $widget_output) && $this->meta_title == '') {
                $this->meta_title = $widget_output['meta_title'];
              }
              if (array_key_exists('meta_keyword', $widget_output) && $this->meta_keyword == '') {
                $this->meta_keyword = $widget_output['meta_keyword'];
              }
              if (array_key_exists('meta_description', $widget_output) && $this->meta_description == '') {
                $this->meta_description = $widget_output['meta_description'];
              }
            }
            else {
              $this->content .= '<div class="section-widget ' . $section->style_class . '">' . $widget_output . "</div>";
            }
            // add module related CSS and JS
            $module_css = '/modules/' . $method_parts[0] . '/css/' . $method_parts[0] . '.css';
            $filename = FCPATH . APPPATH . $module_css;
            if (!in_array($module_css, $this->style_sheets) && file_exists($filename)) {
              $this->style_sheets[] = $module_css;
            }
            $module_js = '/modules/' . $method_parts[0] . '/js/' . $method_parts[0] . '.js';
            $filename = FCPATH . APPPATH . $module_js;
            if (!in_array($module_js, $this->javascripts) && file_exists($filename)) {
              $this->javascripts[] = $module_js;
            }
          }
        }
      }
      $this->content .= "</div>\n";
    }
  } */

  /**
   * Creates a new page
   * @param  array of page attributes
   * @return bool
   */
  public function create($attr = array())
  {
    $name = trim($attr['name']);
    if (array_key_exists('url', $attr) && $attr["url"] > '') {
      $attr["url"] = format_url($attr["url"]);
    }
    else {
      $attr["url"] = format_url($name);
    }
    // check to see if there are duplicated urls
    if ($this->ci->page_model->url_exists($attr["url"])) {
      $this->set_error('URL already exists.');
      return FALSE;
    }
    $page_id = $this->ci->page_model->insert_page($attr);
    if ($page_id) {
      $this->id = $page_id;
      $this->revision_id = 0;
      return $page_id;
    }
    else {
      $this->set_error('Failed to create a new page.');
      return FALSE;
    }
  }

  /**
	 * Saving a page performs several tasks:
   * 1 - saves a draft into database;
   * 2 - if any content changes, creates a new revision;
   * 3 - updates Publish/Archive status of the page;
   * 4 - updates scheduler, permissions, and sitemap option.
   * @param  array of page attributes
   * @param  string  additional operation
	 * @return bool
	 */
	public function save($attr = array(), $sidekick = '')
  {
    if ($this->id <= 0 || empty($attr)) {
      return FALSE;
    }
    $result = TRUE;
    // check to see if there are duplicated URLs
    $name = trim($attr['name']);
    if (array_key_exists('url', $attr) && $attr["url"] > '') {
      $attr["url"] = format_url($attr["url"]);
    }
    else {
      $attr["url"] = format_url($name);
    }
    if ($this->ci->page_model->url_exists($attr["url"], $this->id)) {
      $this->set_error('A page already exists with this title.');
      return FALSE;
    }
    // update scheduler time
    $schedule_changed = FALSE;
    if (array_key_exists('scheduled_publish_date', $attr)) {
      if ($attr['scheduled_publish_date'] > '') {
        $date_array = date_parse($attr['scheduled_publish_date']);
        date_default_timezone_set($attr['scheduled_publish_timezone']);
        $new_publish_ts = mktime($attr['scheduled_publish_hour'], $attr['scheduled_publish_minute'], 0, $date_array['month'], $date_array['day'], $date_array['year']);
      }
      else {
        $new_publish_ts = 0;
      }
      $new_publish_ts = $attr['scheduled_publish_date'] > '' ? strtotime($attr['scheduled_publish_date']) : 0;
      $schedule_changed = $new_publish_ts != $this->scheduled_publish_timestamp;
      $this->scheduled_publish_timestamp = $new_publish_ts;
    }
    if (array_key_exists('scheduled_archive_date', $attr)) {
      if ($attr['scheduled_archive_date'] > '') {
        $date_array = date_parse($attr['scheduled_archive_date']);
        date_default_timezone_set($attr['scheduled_archive_timezone']);
        $new_archive_ts = mktime($attr['scheduled_archive_hour'], $attr['scheduled_archive_minute'], 0, $date_array['month'], $date_array['day'], $date_array['year']);
      }
      else {
        $new_archive_ts = 0;
      }
      $new_archive_ts = $attr['scheduled_archive_date'] > '' ? strtotime($attr['scheduled_archive_date']) : 0;
      $schedule_changed = $schedule_changed || $new_archive_ts != $this->scheduled_archive_timestamp;
      $this->scheduled_archive_timestamp = $new_archive_ts;
    }
    if ($schedule_changed) {
      $updated = $this->ci->page_model->page_schedule($this->id, $this->scheduled_publish_timestamp, $this->scheduled_archive_timestamp);
      if ($updated) {
        $this->set_message('Page schedule has been updated successfully.');
      }
      $result = $result && $updated;
    }
    // find out if the status (0 = draft, 1 = live, 2 = archive) has been changed
    if (array_key_exists('status', $attr)) {
      $new_status = (int)($attr["status"]);
      $status_changed = ($new_status != $this->status) || ($sidekick == 'publish');
    }
    else {
      $new_status = 0;  // draft by default
      $status_changed = FALSE;
    }
    // find out if the draft has been changed
    $draft = $this->draft;
    $draft_changed = $draft->revision_id == 0 || $draft->layout_id != $attr["layout_id"] || $draft->name != $name || $draft->url != $attr["url"]
      || $draft->url_parser != $attr["url_parser"] || $draft->heading != $attr["heading"] || $draft->meta_title != $attr["meta_title"]
      || $draft->meta_description != $attr["meta_description"] || $draft->meta_keyword != $attr["meta_keyword"]
      || $draft->style_sheet != $attr["style_sheet"] || $draft->javascript != $attr["javascript"];
    // update draft if any changes
    if ($draft_changed) {
      $updated = $draft->update($attr);
      // if content changed, create a new revision
      $new_revision_id = $this->ci->page_model->revision_insert($draft, $new_status);
      if ($new_revision_id > 0) {
        $this->revision_id = $new_revision_id;
        $draft->revision_id = $new_revision_id;
        $this->set_message('Content changes were saved successfully.');
      }
      else {
        $this->set_error('There was an error when trying to save the new contents.');
      }
      $result = $result && $updated && ($new_revision_id > 0);
    }
    // change status (0 = draft, 1 = live, 2 = archive)
    if ($status_changed) {
      switch ($new_status) {
        case 0:
          $updated = $this->ci->page_model->page_archive($this->id, TRUE);
          if ($updated) {
            $this->set_message('Page was changed back to draft status.');
          }
          else {
            $this->set_error('There was an error when trying to change the page status.');
          }
          break;
        case 1:
          $updated = $this->ci->page_model->page_publish($draft);
          if ($updated) {
            $this->set_message('Page was published successfully.');
          }
          else {
            $this->set_error('There was an error when trying to publish this page.');
          }
          break;
        case 2:
          $updated = $this->ci->page_model->page_archive($this->id);
          if ($updated) {
            $this->set_message('Page was archived successfully.');
          }
          else {
            $this->set_error('There was an error when trying to archive this page.');
          }
          break;
      }
      $result = $result && $updated;
    }
    return $result;
	}

  /**
   * Lists all pages
   * @param  int  page number
   * @param  int  per page
   * @return mixed
   */
  public static function list_pages($page_num = 1, $per_page = 100)
  {
    $CI = & get_instance();
    return $CI->page_model->list_all_pages($page_num, $per_page);
  }

  /**
   * Counts all records
   * @return int
   */
  public static function count_all()
  {
    $CI = & get_instance();
    return $CI->page_model->count_all_pages();
  }

  /**
   * Deletes a page
   * @return bool
   */
  public function delete()
  {
    return $this->ci->page_model->delete_by_id($this->id);
  }

  //--------------------------------------------------------------------------------
  // Section Related Functions
  // note:
  // section updates always happen in draft, never in the (live) page itself
  // all changes have to get "published" to the live version
  //--------------------------------------------------------------------------------

  /**
   * Loads a section in a draft
   * @return mixed
   */
  public function load_section($id)
  {
    return $this->ci->page_model->draft_get_section($id);
  }

  /**
   * Adds a section to a draft
   * $param  int  section type, 0 = text, 1 = module/widget
   * $param  string  layout zone
   * $param  string  widget code
   * @return bool
   */
  public function add_section($type, $zone, $widget)
  {
    // TODO: re-sort all sections within the affected zone
    return $this->ci->page_model->draft_insert_section($this->id, $type, $zone, $widget);
  }

  /**
   * Deletes a section from a draft
   * $param  int  section ID
   * @return bool
   */
  public function delete_section($id)
  {
    return $this->ci->page_model->draft_delete_section($this->id, $id);
  }

  /**
   * Updates a section in a draft
   * $param  int  section ID
   * $param  string  new content
   * @return bool
   */
  public function update_section($id, $content)
  {
    return $this->ci->page_model->draft_update_section($this->id, $id, $content);
  }

  /**
   * Rearrange a section in a draft
   * $param  int  section ID
   * $param  string  layout zone
   * $param  int  sequence
   * @return bool
   */
  public function rearrange_section($id, $zone, $sequence)
  {
    $updated = $this->ci->page_model->draft_rearrange_section($this->id, $id, $zone, $sequence);
    if (!$updated) {
      $this->set_error('There was an error when trying to rearrange sections.');
    }
    return $updated;
  }

  /**
   * Reverts a page to a different stable revision
   * note:
   *   never revert revisions directly into a page object
   *   always revert into draft first and then make changes or publish
   * @param  int  revision ID, if 0 reverts to the last revision
   * @return bool
   */
  public function revert_to_revision($rid = 0)
  {
    if ($this->id < 1) {
      return FALSE;
    }
    $rid = (int)$rid;
    // load the last stable revision
    $rev = $this->ci->page_model->revision_get($this->id, NULL, $rid);
    if ($rev) {
      $reverted = $this->ci->page_model->draft_revert($rev);
      if ($reverted) {
        $this->set_message('Page was reverted successfully.');
        return $reverted;
      }
    }
    $this->set_error('There was an error when trying to revert this page.');
    return FALSE;
  }

  /**
   * Retrieves the menu object for a page
   */
  public function load_menu()
  {
    if ($this->id > 0) {
      $this->ci->load->model('model_menu_item');
      $row = $this->ci->model_menu_item->get_menu_item_by_page_id($this->id);
      if ($row) {
        $this->menu = $row;
      }
    }
  }

  /**
   * Updates a page menu
   * @return mixed
   */
  public function update_menu($title, $enabled)
  {
    if ($this->menu && $this->menu->id > 0) {
      $this->ci->load->model('model_menu_item');
      $updated = $this->ci->model_menu_item->update_menu_item($this->menu->id, $title, $this->id, $enabled);
      if ($updated) {
        $this->set_message('Menu updated successfully.');
        return $updated;
      }
    }else{
        //no menu item created
        $this->ci->load->model('model_menu_item');
        var_dump($title);
        var_dump($this->id);
        $created = $this->ci->model_menu_item->create_menu_item(0, $title, $this->id, 'page');
        var_dump($created);
        if ($created) {
          $this->set_message('Menu item created successfully.');
        return $created;
      }
        
    }
    $this->set_error('There was an error when trying to update the menu.');
    return FALSE;
  }

}


/**
 * Page Draft Class
 */
class CmsPageDraft extends CmsPageBasic {

  /**
   * page draft attributes
   */
	public $update_timestamp;

  /**
   * __construct
   * @param  str  page ID or URL
   * @return void
   */
  public function __construct($id)
  {
    parent::__construct();
    $id = (int)$id;
    if ($id > 0) {
      $this->load($id);
    }
  }

  /**
   * Retrieves a page draft for editing or previewing
   * @return void
   */
  public function load($id)
  {
    if ($id > 0) {
      $row = $this->ci->page_model->draft_get($id);
      if ($row) {
        $this->id = $row->id;
        $this->revision_id = $row->revision_id;
        $this->update_timestamp = $row->update_timestamp;
        foreach ($this->ci->page_model->comm_props as $prop) {
          $this->$prop = $row->$prop;
        }
        $this->sections = $row->sections;
      }
    }
  }

  /**
   * Updates a page draft, without creating a new revision
   * @param  array of page attributes
   * @return bool
   */
  public function update($attr = array())
  {
    if ($this->id <= 0 || empty($attr)) {
      return FALSE;
    }
    $name = trim($attr['name']);
    if (array_key_exists('url', $attr) && $attr["url"] > '') {
      $attr["url"] = format_url($attr["url"]);
    }
    else {
      $attr["url"] = format_url($name);
    }
    // check to see if there are duplicated URLs
    if ($this->ci->page_model->url_exists($attr["url"], $this->id)) {
      $this->set_error('URL already exists.');
      return FALSE;
    }
    if (array_key_exists('exclude_sitemap', $attr) || array_key_exists('permissions', $attr)) {
      $this->ci->page_model->page_update($this->id, $attr);
    }
    // now update draft
    return $this->ci->page_model->draft_update($this->id, $attr);
  }

}


/**
 * Page Revision Class
 */
class CmsPageRevision extends CmsPageBasic {

  /**
   * page revision attributes
   */
	public $update_timestamp;
	public $publish_timestamp;

  /**
   * __construct
   * @param  int  page ID
   * @param  int  revision ID
   * @return void
   */
  public function __construct($id, $rid = 0)
  {
    parent::__construct();
    $id = (int)$id;
    if ($id > 0) {
      $this->load($id, (int)$rid);
    }
  }

  /**
   * Retrieves a page revision for viewing
   * @param  int  page ID
   * @param  int  revision ID
   * @return void
   */
  public function load($id, $rid = 0)
  {
    if ($id > 0) {
      $row = $this->ci->page_model->revision_get($id, NULL, $rid);
      if ($row) {
        $this->id = $row->page_id;
        $this->revision_id = $row->id;
        $this->status = $row->status;
        $this->update_timestamp = $row->update_timestamp;
        $this->publish_timestamp = $row->publish_timestamp;
        foreach ($this->ci->page_model->comm_props as $prop) {
          $this->$prop = $row->$prop;
        }
        $this->sections = $row->sections;
      }
    }
  }

}


/**
 * Basic Page Class
 */
class CmsPageBasic {

  /**
   * page attributes
   */
	public $id = 0;
	public $revision_id = 0;
  public $author_id;
	public $editor_id;
	public $publisher_id;
  public $layout_id;
  public $name;
  public $url;
  public $url_parser;
  public $heading;
  public $meta_title;
  public $meta_keyword;
  public $meta_description;
  public $style_sheet;
  public $javascript;

  /**
   * sections and content
   */
  public $content;
  public $sections = array();
  public $style_sheets = array();
  public $javascripts = array();

  public $widget_slug;

  /**
   * private attributes
   */
	private $_layouts;   // array of available page layouts. to access this attribute, call $page->layouts
	private $_zones;     // array of zones within the currently selected layout. to access this attribute, call $page->zones

  /**
   * CodeIgniter global, messages and errors
   */
  protected $ci;
  public $messages = array();
  public $errors = array();

  /**
   * __construct
   * @return void
   */
  public function __construct()
  {
    $this->ci = & get_instance();
    $this->ci->load->model('page/page_model');
  }

  /**
   * Acts as a simple way to call model methods without loads of alias
   */
  public function __call($method, $arguments)
  {
    if (!method_exists($this->ci->page_model, $method)) {
      throw new Exception('Undefined method Page::' . $method . '()');
    }
    return call_user_func_array(array($this->ci->page_model, $method), $arguments);
  }

	/**
	 * Property getter
	 */
  public function __get($property)
  {
    $method = 'get_' . strtolower($property);
    if (method_exists($this, $method)) {
      return $this->$method();
    }
  }

  /**
   * Retrieves available page layouts
   * @return mixed
   */
  protected function get_layouts()
  {
    if (!isset($this->_layouts)) {
      $this->_layouts = $this->ci->page_model->list_layouts();
    }
    return $this->_layouts;
  }

  /**
   * Retrieves zones within the selected page layout
   * @return mixed
   */
  protected function get_zones()
  {
    if ($this->id == 0 || $this->layout_id == 0) {
      return array();
    }
    if (!isset($this->_zones)) {
      $this->_zones = $this->ci->page_model->list_layout_zones($this->layout_id);
    }
    return $this->_zones;
  }

  /**
   * Renders content of a page/draft/revision, including css and js
   * @param  string  theme name
   * @param  array   parameters including front-end post-backs
   * @return void
   */
  public function render_content($theme = '', $params = array())
  {
    $this->content = '';
    $this->style_sheets = array();
    $this->javascripts = array();
    if ($this->style_sheet > "") {
      $this->style_sheets = explode(',', trim($this->style_sheet));
    }
    if ($this->javascript > "") {
      $this->javascripts = explode(',', trim($this->javascript));
    }
    foreach ($this->style_sheets as $k => $v) {
      $this->style_sheets[] = 'themes/' . $theme . '/css/page/' . $v;
    }
    foreach ($this->javascripts as $k => $v) {
      $this->javascripts[] = 'themes/' . $theme . '/js/page/' . $v;
    }
    foreach ($this->zones as $zone) {
      $this->content .= '<div id="' . $zone . '" class="content-zone">';
      foreach ($this->sections as $section) {
        if ($section->zone != $zone) {
          continue;
        }
        if ($section->section_type == 0) {
          $this->content .= '<div class="section-text ' . $section->style_class . '">' . $section->content . "</div>";
          // add page related CSS and JS
          if (!empty($section->section_css)) {
            $section_styles = explode(' ', trim($section->section_css));
            foreach ($section_styles as $ss) {
              $this->style_sheets[] = '/asset/css/' . $ss;
            }
          }
          if (!empty($section->section_js)) {
            $section_js = explode(' ', trim($section->section_js));
            foreach ($section_js as $sj) {
              $this->javascripts[] = '/asset/js/' . $sj;
            }
          }
        }
        elseif ($section->section_type == 1 && $section->module_widget > '') {
          $args = array();
          if ($section->content > '') {
            $args = unserialize($section->content);
          }
          if ($this->widget_slug > '') {
            $args['slug'] = $this->widget_slug;
          }
          if (is_array($params) && array_key_exists('frontpostback', $params)) {
            $args['postback'] = $params;
          }
          $widget_output = '';
          $method_parts = explode(":", $section->module_widget);
          if ($method_parts[0] > '' && $method_parts[1] > '') {
            try {
              $widget_output = widget::run($method_parts[0] . '/' . $method_parts[1], $args);
            }
            catch (Exception $e) {
              $widget_output = '';
            }
          }
          if (!empty($widget_output)) {
            if (is_array($widget_output)) {
              if (array_key_exists('content', $widget_output)) {
                $this->content .= '<div class="section-widget ' . $section->style_class . '">' . $widget_output['content'] . "</div>";
              }
              if (array_key_exists('meta_subtitle', $widget_output) && $this->meta_subtitle == '') {
                $this->meta_subtitle = $widget_output['meta_subtitle'];
              }
              if (array_key_exists('meta_keyword', $widget_output) && $this->meta_keyword == '') {
                $this->meta_keyword = $widget_output['meta_keyword'];
              }
              if (array_key_exists('meta_description', $widget_output) && $this->meta_description == '') {
                $this->meta_description = $widget_output['meta_description'];
              }
            }
            else {
              $this->content .= '<div class="section-widget ' . $section->style_class . '">' . $widget_output . "</div>";
            }
            $section->content_widget = $widget_output['content'];
            // add module related CSS and JS
            $module_css = '/modules/' . $method_parts[0] . '/css/' . $method_parts[0] . '.css';
            $filename = FCPATH . APPPATH . $module_css;
            if (!in_array($module_css, $this->style_sheets) && file_exists($filename)) {
              $this->style_sheets[] = $module_css;
            }
            $module_js = '/modules/' . $method_parts[0] . '/js/' . $method_parts[0] . '.js';
            $filename = FCPATH . APPPATH . $module_js;
            if (!in_array($module_js, $this->javascripts) && file_exists($filename)) {
              $this->javascripts[] = $module_js;
            }
          }
        }
      }
      $this->content .= "</div>\n";
    }
  }

  /**
   * Set a message
   * @return void
   */
  public function set_message($message)
  {
    if (!in_array($message, $this->messages)) {
      $this->messages[] = $message;
    }
    return $message;
  }

  /**
   * Get the messages
   * @return string
   */
  public function messages()
  {
    $_output = '';
    foreach ($this->messages as $message) {
      $_output .= $message . "\n";
    }
    return $_output;
  }

  /**
   * Set an error message
   * @return void
   */
  public function set_error($error)
  {
    if (!in_array($error, $this->errors)) {
      $this->errors[] = $error;
    }
    return $error;
  }

  /**
   * Get the error message
   * @return string
   */
  public function errors()
  {
    $_output = '';
    foreach ($this->errors as $error) {
      $_output .= $error . "\n";
    }

    return $_output;
  }

}