<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * News class
 *
 * Author: jeffrey@hottomali.com
 *
 * Created on:  05.03.2012
 *
 * Description:  News class handles news composing, publishing, and revisions etc.
 */
class CmsNews {

  /**
   * news attributes
   */
  public $id = 0;
  public $revision_id = 0;
  public $category_id;
  public $status;
  public $enable_comments;
  public $author_id = 0;
  public $editor_id = 0;
  public $publisher_id = 0;
  public $title = '';
  public $slug = '';
  public $snippet;
  public $body;
  public $featured_image_id = 0;
  public $featured_image; // image object    
  public $create_timestamp;
  public $update_timestamp;
  public $publish_timestamp;
  public $archive_timestamp;
  public $scheduled_publish_timestamp;
  public $scheduled_archive_timestamp;

  /**
   * CodeIgniter global, messages and errors
   * @var string
   */
  protected $ci;
  public $messages = array();
  public $errors = array();
  //private $autosave = FALSE; // Switches on/off the autosave draft feature
  private $_draft;     // news draft object
  private $_revisions; // array of revision objects

  /**
   * __construct
   * @param  str  item ID or slug
   * @param  bool  only load live/published item
   * @return void
   */

  public function __construct($identifier = NULL, $live_only = FALSE) {
    $this->ci = & get_instance();
    $this->ci->load->model('news/news_model');
    $this->ci->load->helper('cookie');

    if (!empty($identifier)) {
      if (is_numeric($identifier)) {
        $this->id = (int) $identifier;
      } else {
        $this->slug = trim($identifier);
      }
      $this->load($live_only);
    }
  }

  /**
   * Acts as a simple way to call model methods without loads of alias
   */
  public function __call($method, $arguments) {
    if (!method_exists($this->ci->news_model, $method)) {
      throw new Exception('Undefined method News::' . $method . '()');
    }
    return call_user_func_array(array($this->ci->news_model, $method), $arguments);
  }

  /**
   * Property getter
   */
  public function __get($property) {
    $method = 'get_' . strtolower($property);
    if (method_exists($this, $method)) {
      return $this->$method();
    }
  }

  /**
   * Retrieves the latest draft, for editing or previewing
   * @return mixed
   */
  protected function get_draft() {
    if ($this->id == 0) {
      return FALSE;
    }
    if (!isset($this->_draft)) {
      $this->_draft = new CmsNewsDraft($this->id);
    }
    // if for some reason (e.g. was imported) the draft didn't exist, creates one immediately
    if (empty($this->_draft) || $this->_draft->id == 0) {
      $this->ci->news_model->draft_insert($this);
      // and load the new one
      $this->_draft = new CmsNewsDraft($this->id);
    }
    if (!empty($this->_draft)) {
      // inherited properties
      $this->_draft->status = $this->status;
      $this->_draft->enable_comments = $this->enable_comments;
      $this->_draft->create_timestamp = $this->create_timestamp;
      $this->_draft->publish_timestamp = $this->publish_timestamp;
      $this->_draft->archive_timestamp = $this->archive_timestamp;
      $this->_draft->scheduled_publish_timestamp = $this->scheduled_publish_timestamp;
      $this->_draft->scheduled_archive_timestamp = $this->scheduled_archive_timestamp;
    }
    return $this->_draft;
  }

  /**
   * Retrieves the revisions
   * @return mixed
   */
  protected function get_revisions() {
    if ($this->id == 0) {
      return FALSE;
    }
    if (!isset($this->_revisions)) {
      $this->_revisions = $this->ci->news_model->revision_list($this->id);
    }
    return $this->_revisions;
  }

  /**
   * Retrieves a news item from database
   * @param  bool  if true, load published items only
   * @return void
   */
  public function load($live_only = TRUE) {
    if ($this->id < 1 && $this->slug == '') {
      return FALSE;
    }
    $row = $this->ci->news_model->load_news($this->id, $this->slug, $live_only);
    if ($row) {
      $this->id = $row->id;
      $this->slug = $row->slug;
      $this->status = $row->status;
      $this->enable_comments = $row->enable_comments;
      $this->revision_id = $row->revision_id;
      foreach ($this->ci->news_model->comm_props as $prop) {
        $this->$prop = $row->$prop;
      }
      $this->create_timestamp = $row->create_timestamp;
      $this->update_timestamp = $row->update_timestamp;
      $this->publish_timestamp = $row->publish_timestamp;
      $this->archive_timestamp = $row->archive_timestamp;
      $this->scheduled_publish_timestamp = $row->scheduled_publish_timestamp;
      $this->scheduled_archive_timestamp = $row->scheduled_archive_timestamp;
      
      if ($this->featured_image_id > 0) {
        $this->featured_image = asset_load_item( $this->featured_image_id );
      }      
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Retrieves a stable revision
   * @param  int  revision ID, if 0 loads live version, if -1 or not set then load the latest version
   * @return object
   */
  public function get_revision($revision_id) {
    $revision_id = (int) $revision_id;
    if ($this->id < 1 && $this->slug == '' || $revision_id < 1) {
      return FALSE;
    }
    $revision = $this->ci->news_model->revision_get($this->id, $this->slug, $revision_id);
    if (!empty($revision)) {
      $revision->revision_id = $revision_id;
      // inherited properties
      $revision->enable_comments = $this->enable_comments;
      $revision->create_timestamp = $this->create_timestamp;
      $revision->archive_timestamp = $this->archive_timestamp;
      $revision->scheduled_publish_timestamp = $this->scheduled_publish_timestamp;
      $revision->scheduled_archive_timestamp = $this->scheduled_archive_timestamp;
    }
    return $revision;
  }

  /**
   * Creates a new item
   * @param  array of news attributes
   * @return void
   */
  public function create($attr = array()) {
    $title = trim($attr['title']);
    if (array_key_exists('slug', $attr) && $attr["slug"] > '') {
      $attr["slug"] = format_url($attr["slug"]);
    } else {
      $attr["slug"] = format_url($title);
    }
    // check to see if there are duplicated slugs
    if ($this->ci->news_model->slug_exists($attr["slug"])) {
      $this->set_error('A post already exists with this title.');
      return FALSE;
    }
    $news_id = $this->ci->news_model->insert_news($attr);
    if ($news_id) {
      $this->id = $news_id;
      $this->revision_id = 0;
      return $news_id;
    } else {
      $this->set_error('Failed to create a news.');
      return FALSE;
    }
  }

  /**
   * Saving a news item performs several tasks:
   * 1 - saves a draft into database;
   * 2 - if any content changes, creates a new revision;
   * 3 - updates Publish/Archive status of the news;
   * 4 - updates scheduler.
   * @param  array of news attributes
   * @return bool
   */
  public function save($attr = array(), $sidekick='') {
    if ($this->id <= 0 || empty($attr)) {
      return FALSE;
    }
    $result = TRUE;
    // check to see if there are duplicated slugs
    $title = trim($attr['title']);
    if (array_key_exists('slug', $attr) && $attr["slug"] > '') {
      $attr["slug"] = format_url($attr["slug"]);
    } else {
      $attr["slug"] = format_url($title);
    }
    if ($this->ci->news_model->slug_exists($attr["slug"], $this->id)) {
      $this->set_error('A news already exists with this title.');
      return FALSE;
    }
    $draft = $this->draft;
    // update scheduler time
    $schedule_changed = FALSE;
    if (array_key_exists('scheduled_publish_date', $attr)) {
      $new_publish_ts = $attr['scheduled_publish_date'] > '' ? strtotime($attr['scheduled_publish_date']) : 0;
      $schedule_changed = $new_publish_ts != $this->scheduled_publish_timestamp;
      $this->scheduled_publish_timestamp = $new_publish_ts;
    }
    if (array_key_exists('scheduled_archive_date', $attr)) {
      $new_archive_ts = $attr['scheduled_archive_date'] > '' ? strtotime($attr['scheduled_archive_date']) : 0;
      $schedule_changed = $schedule_changed || $new_archive_ts != $this->scheduled_archive_timestamp;
      $this->scheduled_archive_timestamp = $new_archive_ts;
    }
    if ($schedule_changed) {
      $updated = $this->ci->news_model->news_schedule($this->id, $this->scheduled_publish_timestamp, $this->scheduled_archive_timestamp);
      if ($updated) {
        $this->set_message('News schedule has been updated successfully.');
      }
      $result = $result && $updated;
    }
    // find out if the status (0 = draft, 1 = live, 2 = archive) has been changed
    if (array_key_exists('status', $attr)) {
      $new_status = (int) ($attr["status"]);

      $status_changed = ($new_status != $this->status) || ($sidekick == 'publish');

//      if($attr['featured_image_id'] != $this->featured_image_id){
//        $status_changed = true;
//      }
//      if($attr['snippet'] != $this->snippet){
//        $status_changed = true;
//      }      

    } else {
      $new_status = 0;  // draft by default
      $status_changed = FALSE;
    }    
    // find out if the draft has been changed
    $draft_changed = $draft->revision_id == 0 || $draft->title != $title || $draft->slug != $attr["slug"] || $draft->snippet != $attr["snippet"] || $draft->featured_image_id != $attr["featured_image_id"];
    // update draft
    if ($draft_changed) {
      $updated = $draft->update($attr);
      // if content changed, create a new revision
      $new_revision_id = $this->ci->news_model->revision_insert($draft, $new_status);
      if ($new_revision_id > 0) {
        $this->revision_id = $new_revision_id;
        $draft->revision_id = $new_revision_id;
        $this->set_message('Content changes were saved successfully.');
      } else {
        $this->set_error('There was an error when trying to save the new contents.');
      }
      $result = $result && $updated && ($new_revision_id > 0);
    }
    // change status (0 = draft, 1 = live, 2 = archive)
    if ($status_changed) {
      switch ($new_status) {
        case 0:
          $updated = $this->ci->news_model->news_archive($this->id, TRUE);
          if ($updated) {
            $this->set_message('News was changed back to draft status.');
          } else {
            $this->set_error('There was an error when trying to change news status.');
          }
          break;
        case 1:
          $updated = $this->ci->news_model->news_publish($draft);
          if ($updated) {
            $this->set_message('News was published successfully.');
          } else {
            $this->set_error('There was an error when trying to publish news.');
          }
          break;
        case 2:
          $updated = $this->ci->news_model->news_archive($this->id);
          if ($updated) {
            $this->set_message('News was archived successfully.');
          } else {
            $this->set_error('There was an error when trying to archive this news.');
          }
          break;
      }
      $result = $result && $updated;
    }
    return $result;
  }

  /**
   * Lists all news
   * @param  int  category ID
   * @param  bool  live/published only
   * @param  int  user ID
   * @param  int  page number
   * @param  int  per page
   * @return mixed
   */
  public static function list_news($category = 0, $live_only = TRUE, $user_id = 0, $page_num = 1, $per_page = 0) {
    $CI = & get_instance();
    return $CI->news_model->list_all_news($category, $live_only, $user_id, $page_num, $per_page);
  }

  /**
   * Lists categories
   * @return mixed
   */
  public static function list_category() {
    $CI = & get_instance();
    return $CI->news_model->list_categories();
  }

  /**
   * Counts all records
   * @return int
   */
  public static function count_all() {
    $CI = & get_instance();
    return $CI->news_model->count_all_news();
  }

  /**
   * Get a random slug for demonstration purpose
   */
  public static function random_slug() {
    $CI = & get_instance();
    return $CI->news_model->get_random_slug();
  }

  /**
   * Deletes an item
   * @return mixed
   */
  public function delete() {
    return $this->ci->news_model->delete_by_id($this->id);
  }

  /**
   * Set a message
   * @return void
   */
  public function set_message($message) {
    if (!in_array($message, $this->messages)) {
      $this->messages[] = $message;
    }
    return $message;
  }

  /**
   * Get the messages
   * @return string
   */
  public function messages() {
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
  public function set_error($error) {
    if (!in_array($error, $this->errors)) {
      $this->errors[] = $error;
    }
    return $error;
  }

  /**
   * Get the error message
   * @return string
   */
  public function errors() {
    $_output = '';
    foreach ($this->errors as $error) {
      $_output .= $error . "\n";
    }

    return $_output;
  }

}

/**
 * News Draft class
 */
class CmsNewsDraft {

  /**
   * news draft attributes
   */
  public $id = 0;
  public $revision_id = 0;
  public $author_id = 0;
  public $editor_id = 0;
  public $publisher_id = 0;
  public $title = '';
  public $slug = '';
  public $snippet;
  public $body;
  public $update_timestamp;
  public $featured_image_id = 0;
  public $featured_image; // image object       

  /**
   * CodeIgniter global, messages and errors
   * @var string
   */
  protected $ci;
  public $messages = array();
  public $errors = array();

  /**
   * Switches on/off autosave draft feature
   */
  //private $autosave = FALSE;

  /**
   * __construct
   * @param  int  unique ID
   * @return void
   */
  public function __construct($id) {
    $this->ci = & get_instance();
    $this->ci->load->model('news/news_model');
    $id = (int) $id;
    if ($id > 0) {
      $this->load($id);
    }
  }

  /**
   * Acts as a simple way to call model methods without loads of alias
   */
  public function __call($method, $arguments) {
    if (!method_exists($this->ci->news_model, $method)) {
      throw new Exception('Undefined method NewsDraft::' . $method . '()');
    }
    return call_user_func_array(array($this->ci->news_model, $method), $arguments);
  }

  /**
   * Retrieves the most recently updated draft, for editing or previewing
   * @param  int  unique ID
   * @return void
   */
  public function load($id) {
    if ($id > 0) {
      $row = $this->ci->news_model->draft_get($id);
      if ($row) {
        $this->id = $row->id;
        $this->revision_id = $row->revision_id;
        $this->update_timestamp = $row->update_timestamp;
        foreach ($this->ci->news_model->comm_props as $prop) {
          $this->$prop = $row->$prop;
        }
      }
    }
  }

  /**
   * Updates a draft, without creating a new revision
   * @param  array of news attributes
   * @return bool
   */
  public function update($attr = array()) {
    if ($this->id <= 0 || empty($attr)) {
      return FALSE;
    }
    $title = trim($attr['title']);
    if (array_key_exists('slug', $attr) && $attr["slug"] > '') {
      $attr["slug"] = format_url($attr["slug"]);
    } else {
      $attr["slug"] = format_url($title);
    }
    // check to see if there are duplicated slugs
    if ($this->ci->news_model->slug_exists($attr["slug"], $this->id)) {
      $this->set_error('A news already exists with this title.');
      return FALSE;
    }
    // now update
    return $this->ci->news_model->draft_update($this->id, $attr);
  }

  /**
   * Updates a draft body
   * @param  str  body text
   * @return void
   */
  public function update_body($body) {
    if ($this->id < 1) {
      return FALSE;
    }
    $attr = array('body' => $body);
    return $this->ci->news_model->draft_update($this->id, $attr);
  }

  /**
   * Updates a featured image id
   * @param  int  news_id
   * @param  int  asset id
   * @return void
   */
  public function update_image($asset_id) {
    if ($this->id < 1) {
      return FALSE;
    }
    $attr = array('featured_image_id' => $asset_id);
    return $this->ci->news_model->draft_update($this->id, $attr);
  }

  /**
   * Reverts a draft to a different stable revision
   * @param  int  revision ID, if 0 reverts to the last revision
   * @return bool
   */
  public function revert_to_revision($rid = 0) {
    if ($this->id < 1) {
      return FALSE;
    }
    $rid = (int) $rid;
    // load the last stable revision
    $rev = $this->ci->news_model->revision_get($this->id, NULL, $rid);
    if ($rev) {
      $reverted = $this->ci->news_model->draft_revert($rev);
      if ($reverted) {
        $this->set_message('News reverted successfully.');
        return $reverted;
      }
    }
    $this->set_error('There was an error when trying to revert this news.');
    return FALSE;
  }

  /**
   * Set a message
   * @return void
   */
  public function set_message($message) {
    if (!in_array($message, $this->messages)) {
      $this->messages[] = $message;
    }
    return $message;
  }

  /**
   * Get the messages
   * @return string
   */
  public function messages() {
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
  public function set_error($error) {
    if (!in_array($error, $this->errors)) {
      $this->errors[] = $error;
    }
    return $error;
  }

  /**
   * Get the error message
   * @return string
   */
  public function errors() {
    $_output = '';
    foreach ($this->errors as $error) {
      $_output .= $error . "\n";
    }

    return $_output;
  }

}
