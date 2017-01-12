<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Training class
 *
 * Author: jeffrey@hottomali.com
 * Created on:  08.03.2012
 */
class CmsTraining extends CmsTrainingBase {

  /**
   * training attributes
   */
  public $id = 0;
  public $category_id = 0;
  public $revision_id = 0;
  public $target_id = 0;
  public $title = '';
  public $slug = '';
  public $status = 0;
  public $featured = 0;
  public $featured_image_id = 0;
  public $description;
  public $features;
  public $link;
  public $editor_id = 0;
  public $update_timestamp;
  public $publish_timestamp;
  public $archive_timestamp;
  public $scheduled_publish_timestamp;
  public $scheduled_archive_timestamp;
  public $tags = array();
  public $assets = array();
  public $variants = array();
  public $resources = array();
  public $featured_image; // image object
  public $domain = '';
  public $site_name = '';

  private $_draft;     // draft object. to access this attribute, use $training->draft
  private $_revisions; // array of revision objects. to access this attribute, use $training->revisions
  private $_has_quiz; // check if there are active quizzes under this training item. to access this attribute, use $training->has_quiz
  private $_quizzes; // array of quiz objects. to access this attribute, use $training->quizzes
  private $_max_points; // the maximum points from all quizzes for this training item
  private $_max_contest_entries; // the maximum draws from all quizzes for this training item

  public $user_id = 0; // a user ID for points calculation
  private $_user_points; // total points a user has earned from all quizzes for this training item; the above $user_id attribute must be assigned
  private $_points_percent; // percent of points a user has earned from all quizzes for this training item; the above $user_id attribute must be assigned
  private $_user_contest_entries;
  private $_contest_entries_percent;
  
  private $_highest_percent_score; //average from all highest score
  /**
   * __construct
   * @param  str  item ID or slug
   * @param  bool  if true, only load live/published item
   * @param  bool  if true, loads item for the current domain/site only
   * @param  bool  loads training that targets user
   * @return void
   */
  public function __construct($identifier = NULL, $live_only = FALSE, $current_site_only = TRUE, $targeting_user = TRUE)
  {
    parent::__construct();
    if (!empty($identifier)) {
      if (is_numeric($identifier)) {
        $this->id = (int) $identifier;
      }
      else {
        $this->slug = trim($identifier);
      }
      $this->load($live_only, $current_site_only, $targeting_user);
    }
  }

  /**
   * Retrieves a training item from database
   * @param  bool  if true, load published items only
   * @param  bool  if true, loads item for the current domain/site only
   * @param  bool  loads training that targets user
   * @return void
   */
  public function load($live_only = TRUE, $current_site_only = TRUE, $targeting_user = TRUE)
  {
    if ($this->id < 1 && $this->slug == '') {
      return FALSE;
    }
    $row = $this->ci->training_model->training_load($this->id, $this->slug, $live_only, $current_site_only, $targeting_user);
    if ($row) {
      if (!$current_site_only && $this->id > 0) {
        $this->domain = $row->domain;
        $this->site_name = $row->site_name;
      }
      $this->id = $row->id;
      foreach ($this->ci->training_model->comm_props as $prop) {
        $this->$prop = $row->$prop;
      }
      $this->revision_id = $row->revision_id;
      $this->target_id = $row->target_id;
      $this->create_timestamp = $row->create_timestamp;
      $this->update_timestamp = $row->update_timestamp;
      $this->publish_timestamp = $row->publish_timestamp;
      $this->archive_timestamp = $row->archive_timestamp;
      $this->link = $row->link;
      $this->tags = $this->ci->training_model->training_tag_list($this->id);
      $this->variants = $this->ci->training_model->variant_list($this->id);
      foreach ($this->variants as $v) {
        foreach ($v->details as $vd) {
          if ($vd->field_type == 'image') {
            $vd->image = asset_load_item((int) ($vd->value));
          }
        }
      }
      $assets = $this->ci->training_model->asset_list($this->id);
      foreach ($assets as $a) {
        $this->assets[$a->id] = asset_load_item($a->asset_id);
      }
      $resources = $this->ci->training_model->resource_list($this->id);
      foreach ($resources as $a) {
        $this->resources[$a->id] = asset_load_item($a->asset_id);
      }
      if ($this->featured_image_id > 0) {
        $this->featured_image = asset_load_item($this->featured_image_id);
      }
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Retrieves a revision
   * @param  int  revision ID, if 0 or not set then load the latest version
   * @return object
   */
  public function load_revision($revision_id = 0)
  {
    $revision_id = (int) $revision_id;
    if ($this->id < 1 && $this->url == '') {
      return FALSE;
    }
    $revision = new CmsTrainingRevision($this->id, $revision_id);
    if (!empty($revision)) {
      // inherited properties
      $revision->author_id = $this->author_id;
      $revision->create_timestamp = $this->create_timestamp;
    }
    return $revision;
  }

  /**
   * Retrieves the latest revision as a draft for editing or previewing
   * @return mixed
   */
  protected function get_draft()
  {
    if ($this->id == 0) {
      return FALSE;
    }
    if (!isset($this->_draft)) {
      $this->_draft = new CmsTrainingRevision($this->id);
    }
    // if for some reason (e.g. was imported) the draft didn't exist
    if (empty($this->_draft) || $this->_draft->id == 0) {
      $this->_draft = $this;
    } else {
      // inherited properties
      $this->_draft->author_id = $this->author_id;
      $this->_draft->create_timestamp = $this->create_timestamp;
      $this->_draft->archive_timestamp = $this->archive_timestamp;
      $this->_draft->scheduled_publish_timestamp = $this->scheduled_publish_timestamp;
      $this->_draft->scheduled_archive_timestamp = $this->scheduled_archive_timestamp;
    }
    return $this->_draft;
  }

  /**
   * Retrieves revisions
   * @return mixed
   */
  protected function get_revisions()
  {
    if ($this->id == 0) {
      return FALSE;
    }
    if (!isset($this->_revisions)) {
      $this->_revisions = $this->ci->training_model->revision_list($this->id);
    }
    return $this->_revisions;
  }

  /**
   * Checks if there are quizzes for this training item
   * @return bool
   */
  public function get_has_quiz()
  {
    if ($this->id == 0 || $this->slug == '') {
      return FALSE;
    }
    if (!isset($this->_has_quiz)) {
      $this->_has_quiz = FALSE;
      $this->ci->load->library('quiz/CmsQuiz');
      $slug_array = CmsQuiz::quiz_slugs_by_product($this->slug);
      if (is_array($slug_array) && count($slug_array) > 0) {
        $this->_has_quiz = TRUE;
      }
    }
    return $this->_has_quiz;
  }

  /**
   * Retrieves a list quiz objects for this training object
   * @return mixed
   */
  protected function get_quizzes()
  {
    if ($this->id == 0 || $this->slug == '') {
      return FALSE;
    }
    if (!isset($this->_quizzes)) {
      $this->_quizzes = array();
      $this->ci->load->library('quiz/CmsQuiz');
      $slug_array = CmsQuiz::quiz_slugs_by_product($this->slug);
      if (is_array($slug_array) && count($slug_array) > 0) {
        foreach ($slug_array as $row) {
          $quiz = new CmsQuiz($row->slug, TRUE);
          $this->_quizzes[$quiz->id] = $quiz;
        }
      }
    }
    return $this->_quizzes;
  }

  /**
   * Retrieves the maximum points a user could get from all quizzes for this training object
   * @return int
   */
  protected function get_max_points()
  {    
    if ($this->id == 0 || $this->slug == '') {
      return 0;
    }
    if (!isset($this->_max_points)) {
      $this->_max_points = 0;
      if (is_array($this->quizzes) && count($this->quizzes) > 0) {
        foreach ($this->quizzes as $quiz) {
          $this->_max_points += $quiz->max_points;
        }
      }
    }
    return $this->_max_points;
  }

  /**
   * Retrieves the total points a user has earned from all quizzes for this training object
   * @return int
   */
  protected function get_user_points()
  {
    if ($this->id == 0 || $this->slug == '' || $this->user_id == 0) {
      return 0;
    }
    if (!isset($this->_user_points)) {
      $this->_user_points = 0;
      if (is_array($this->quizzes) && count($this->quizzes) > 0) {
        foreach ($this->quizzes as $quiz) {
          $quiz->user_id = $this->user_id;
          $this->_user_points += $quiz->user_points;
        }
      }
    }
    return $this->_user_points;
  }

  /**
   * Retrieves the percent of total points a user has earned from all quizzes for this training object
   * @return int
   */
  protected function get_points_percent()
  {
    if ($this->id == 0 || $this->slug == '' || $this->user_id == 0) {
      return 0;
    }
    if (!isset($this->_points_percent)) {
      $this->_points_percent = 0;
      if ($this->user_points > 0 && $this->max_points > 0 && $this->user_points >= $this->max_points) {
        $this->_points_percent = 100; // in case the max points decreased after expiry date and became less than the points earned
      }
      elseif ($this->user_points > 0 && $this->max_points > 0) {
        $this->_points_percent = round($this->user_points * 100 / $this->max_points, 0);
      }
    }
    return $this->_points_percent;
  }
  
  /**
   * Retrieves the maximum contest entries a user can get from this quiz
   * @return int
   */
  protected function get_max_contest_entries()
  {
    if ($this->id == 0 || $this->slug == '') {
      return 0;
    }
    if (!isset($this->_max_contest_entries)) {
      $this->_max_contest_entries = 0;
      if (is_array($this->quizzes) && count($this->quizzes) > 0) {
        foreach ($this->quizzes as $quiz) {
          $this->_max_contest_entries += $quiz->max_contest_entries;
        }
      }
    }
    return $this->_max_contest_entries;      
  }
  
  /**
   * Retrieves the total contest entries a user has earned from this quiz
   * @return int
   */
  protected function get_user_contest_entries()
  {
    if ($this->id == 0 || $this->slug == '' || $this->user_id == 0) {
      return 0;
    }
    if (!isset($this->_user_contest_entries)) {
      $this->_user_contest_entries = 0;
      if (is_array($this->quizzes) && count($this->quizzes) > 0) {
        foreach ($this->quizzes as $quiz) {
          $quiz->user_id = $this->user_id;
          $this->_user_contest_entries += $quiz->user_contest_entries;
        }
      }
    }
    return $this->_user_contest_entries;
  }

  /**
   * Retrieves the percent of contest entries a user has earned from this quiz
   * @return int
   */
  protected function get_contest_entries_percent()
  {
    if ($this->id == 0 || $this->slug == '' || $this->user_id == 0) {
      return 0;
    }
    if (!isset($this->_contest_entries_percent)) {
      $this->_contest_entries_percent = 0;
      if ($this->user_contest_entries > 0 && $this->max_contest_entries > 0 && $this->user_contest_entries >= $this->max_contest_entries) {
        $this->_contest_entries_percent = 100; // in case the max points decreased after expiry date and became less than the points earned
      }
      elseif ($this->user_contest_entries > 0 && $this->max_contest_entries > 0) {
        $this->_contest_entries_percent = round($this->user_contest_entries * 100 / $this->max_contest_entries, 0);
      }
    }
    return $this->_contest_entries_percent;
  }

    /**
   * Retrieves the highest percent for all on trainning
   * @return int
   */
  protected function get_highest_percent_score()
  {
    if ($this->id == 0 || $this->slug == '' || $this->user_id == 0) {
      return 0;
    }
    $precent_total = 0;
    if (!isset($this->highest_percent_score)) {
      $this->_highest_percent_score = 0;
      if (is_array($this->quizzes) && count($this->quizzes) > 0) {
        foreach ($this->quizzes as $quiz) {
          $quiz->user_id = $this->user_id;
          $precent_total += $quiz->highest_percent_score;
        }
      }else{
          return $this->_highest_percent_score = 0;
      }
    }
    
     $this->_highest_percent_score = round($precent_total/count($this->quizzes),0);
     
    return $this->_highest_percent_score;
  }
  
  /**
   * Reverts a training to a different stable revision
   * @param  int  revision ID, if 0 reverts to the latest revision
   * @return bool
   */
  public function revert_to_revision($rid = 0)
  {
    if ($this->id < 1) {
      return FALSE;
    }
    $rid = (int) $rid;
    // load the last stable revision
    $rev = $this->ci->training_model->revision_get($this->id, NULL, $rid);
    if ($rev) {
      $reverted = $this->ci->training_model->draft_revert($rev);
      if ($reverted) {
        $this->set_message('Training was reverted successfully.');
        return $reverted;
      }
    }
    $this->set_error('There was an error when trying to revert this training.');
    return FALSE;
  }

  /**
   * Creates a new item
   * @param  array of training attributes
   * @return mixed
   */
  public function create($attr = array())
  {
    if (array_key_exists('title', $attr) && $attr["title"] > '') {
      $attr["slug"] = format_url($attr["title"]);
    }
    // check to see if there are duplicated slugs
    if ($this->ci->training_model->slug_exists($attr["slug"])) {
      $this->set_error('A training already exists with this title.');
      return FALSE;
    }
    $training_id = $this->ci->training_model->training_insert($attr);
    if ($training_id) {
      $this->id = $training_id;
      $this->slug = $attr["slug"];
      return $training_id;
    }
    else {
      $this->set_error('Failed to create a training.');
      return FALSE;
    }
  }

  /**
   * Saves a training item into database
   * @param  array of training attributes
   * @return bool
   */
  public function save($attr = array())
  {
    if ($this->id <= 0 || empty($attr)) {
      return FALSE;
    }
    // check to see if there are duplicated slugs
    if (array_key_exists('title', $attr) && $attr["title"] > '') {
      $attr["slug"] = format_url($attr["title"]);
    }
    if ($this->ci->training_model->slug_exists($attr["slug"], $this->id)) {
      $this->set_error('A training already exists with this title.');
      return FALSE;
    }
    $result = $this->ci->training_model->training_update($this->id, $attr);
    if ($result) {
      $this->set_message('Training updated successfully.');
    }
    return $result;
  }

  /**
   * Adds a new question to training
   * @param  array of question attributes
   * @return mixed
   */
  public function add_asset($attr = array())
  {
    $asset_id = $this->ci->training_model->asset_insert($this->id, $attr);
    if ($asset_id) {
      return $asset_id;
    }
    else {
      $this->set_error('Failed to add a training asset.');
      return FALSE;
    }
  }

  /**
   * Updates an asset
   * @param  int  asset id
   * @param  array of asset attributes
   * @return mixed
   */
  public function update_asset($asset_id, $attr = array())
  {
    $updated = $this->ci->training_model->asset_update($asset_id, $attr);
    if ($updated) {
      //$this->set_message('Training asset updated successfully.');
      return TRUE;
    }
    else {
      $this->set_error('Failed to update a training asset.');
      return FALSE;
    }
  }

  /**
   * Deletes an asset
   * @param  int  asset id
   * @return mixed
   */
  public function delete_asset($asset_id)
  {
    $updated = $this->ci->training_model->asset_delete($asset_id);
    if ($updated) {
      //$this->set_message('Training asset has been deleted successfully.');
      return TRUE;
    }
    else {
      $this->set_error('Failed to delete the asset.');
      return FALSE;
    }
  }

  /**
   * Loads an asset from a training
   * @param  int  asset id
   * @return mixed
   */
  public function load_asset($asset_id)
  {
    return $this->ci->training_model->asset_get($asset_id);
  }

  /**
   * Lists all training
   * @param  int  category ID
   * @param  bool  live/published only
   * @param  int  page number
   * @param  int  per page
   * @return mixed
   */
  public static function list_training($category_id = 1, $live_only = TRUE, $page_num = 1, $per_page = 0)
  {
    $CI = & get_instance();
    return $CI->training_model->training_list($category_id, $live_only, $page_num, $per_page);
  }

  /**
   * Counts all records
   * @param  int  category ID
   * @param  bool  live/published only
   * @return int
   */
  public static function count_all($category_id = 1, $live_only = TRUE)
  {
    $CI = & get_instance();
    return $CI->training_model->training_count($category_id, $live_only);
  }

  /**
   * Get a random slug for demonstration purpose
   */
  public static function random_slug()
  {
    $CI = & get_instance();
    return $CI->training_model->get_random_slug();
  }

  /**
   * Get a random slug for demonstration purpose
   */
  public static function random_preview_slug($type)
  {
    $CI = & get_instance();
    return $CI->training_model->get_preview_slug($type);
  }

  /**
   * Deletes an item
   * @return bool
   */
  public function delete()
  {
    return $this->ci->training_model->delete($this->id);
  }

  /**
   * Loads a category from a training
   * @param  int  category id
   * @return mixed
   */
  public function load_category($category_id)
  {
    return $this->ci->training_model->category_get($category_id);
  }

  /**
   * Loads a tag_type from a training
   * @param  int  category id
   * @return mixed
   */
  public function load_tag_type($tag_type_id)
  {
    return $this->ci->training_model->tag_type_get($tag_type_id);
  }

  /**
   * Loads a tag from a tag_type
   * @param  int  category id
   * @return mixed
   */
  public function load_tag($tag_id)
  {
    return $this->ci->training_model->tag_get($tag_id);
  }

  /**
   * Loads a subcategory from a training
   * @param  int  category id
   * @return mixed
   */
  public function load_subcategory($subcategory_id)
  {
    return $this->ci->training_model->subcategory_get($subcategory_id);
  }

  /**
   * Updates a training category
   * @param  int  category id
   * @param  array of category attributes
   * @return mixed
   */
  public function update_category($category_id, $attr = array())
  {
    $updated = $this->ci->training_model->category_update($category_id, $attr);
    if ($updated) {
      //$this->set_message('Quiz question updated successfully.');
      return TRUE;
    }
    else {
      $this->set_error('Failed to update a quiz question.');
      return FALSE;
    }
  }

  /**
   * Updates a training category
   * @param  int  category id
   * @param  array of category attributes
   * @return mixed
   */
  public function update_tag_type($tag_type_id, $attr = array())
  {
    $updated = $this->ci->training_model->tag_type_update($tag_type_id, $attr);
    if ($updated) {
      $this->set_message('Tag name updated successfully.');
      return TRUE;
    }
    else {
      $this->set_error('Failed to update a quiz question.');
      return FALSE;
    }
  }

  /**
   * Updates a training category
   * @param  int  category id
   * @param  array of category attributes
   * @return mixed
   */
  public function update_tag($tag_id, $attr = array())
  {
    $updated = $this->ci->training_model->tag_update($tag_id, $attr);
    if ($updated) {
      $this->set_message('Tag name updated successfully.');
      return TRUE;
    }
    else {
      $this->set_error('Failed to update a quiz question.');
      return FALSE;
    }
  }

}

/**
 * Training Revision Class
 */
class CmsTrainingRevision extends CmsTrainingBase {

  /**
   * training revision attributes
   */
  public $id = 0;
  public $category_id = 0;
  public $revision_id = 0;
  public $title = '';
  public $slug = '';
  public $status = 0;
  public $featured = 0;
  public $featured_image_id = 0;
  public $description;
  public $features;
  public $editor_id = 0;
  public $update_timestamp;
  public $publish_timestamp;
  public $archive_timestamp;
  public $scheduled_publish_timestamp;
  public $scheduled_archive_timestamp;
  public $tags = array();
  public $assets = array();
  public $variants = array();
  public $resources = array();

  /**
   * __construct
   * @param  int  training ID
   * @param  int  revision ID
   * @return void
   */
  public function __construct($id = 0, $rid = 0)
  {
    parent::__construct();
    $id = (int) $id;
    if ($id > 0) {
      $this->load($id, (int) $rid);
    }
  }

  /**
   * Retrieves a revision for viewing
   * @param  int  training ID
   * @param  int  revision ID
   * @return void
   */
  public function load($id, $rid = 0)
  {
    if ($id > 0) {
      $row = $this->ci->training_model->revision_get($id, NULL, $rid);
      if ($row) {
        foreach ($this->ci->training_model->comm_props as $prop) {
          $this->$prop = $row->$prop;
        }
        $this->id = $row->training_id;
        $this->revision_id = $row->id;
        $this->update_timestamp = $row->update_timestamp;
        $this->publish_timestamp = $row->publish_timestamp;
        $this->tags = unserialize($row->tags);
        $assets = unserialize($row->assets);
        foreach ($assets as $a) {
          $this->assets[$a->id] = asset_load_item($a->asset_id);
        }
        $this->variants = unserialize($row->variants);
        foreach ($this->variants as $i => $v) {
          foreach ($v->details as $k => $vd) {
            if ($vd->field_type == 'image') {
              $this->variants[$i][$k]->image = asset_load_item((int) ($vd->value));
            }
          }
        }
        $resources = unserialize($row->resources);
        foreach ($resources as $r) {
          $this->resources[$r->id] = asset_load_item($r->asset_id);
        }
        if ($this->featured_image_id > 0) {
          $this->featured_image = asset_load_item($this->featured_image_id);
        }
      }
    }
    return FALSE;
  }

  /**
   * Creates a new revision
   * @param  object  training object
   * @return mixed
   */
  public function create($training)
  {
    $revision_id = $this->ci->training_model->revision_insert($training);
    if ($revision_id) {
      return $revision_id;
    }
    else {
      $this->set_error('Failed to create a training revision.');
      return FALSE;
    }
  }

}

/**
 * Training Tag Class
 */
class CmsTrainingTag extends CmsTrainingBase {

  /**
   * training revision attributes
   */
  public $update_timestamp;
  public $publish_timestamp;

  /**
   * __construct
   * @param  int  training ID
   * @param  int  revision ID
   * @return void
   */
  public function __construct($id = 0)
  {
    parent::__construct();
    $id = (int) $id;
    if ($id > 0) {
      $this->load($id);
    }
  }

  /**
   * Retrieves a revision for viewing
   * @param  int  tag ID
   * @return void
   */
  public function load($id)
  {
    if ($id > 0) {
      $row = $this->ci->training_model->tag_get($id);
      if ($row) {
        $this->id = $row->id;
        $this->type_id = $row->type_id;
        $this->name = $row->name;
        $this->sequence = $row->sequence;
      }
    }
  }

}

/**
 * Training Variant Class
 */
class CmsTrainingVariant extends CmsTrainingBase {

  /**
   * training revision attributes
   */
  public $update_timestamp;
  public $publish_timestamp;

  /**
   * __construct
   * @param  int  variant ID
   * @return void
   */
  public function __construct($id = 0)
  {
    parent::__construct();
    $id = (int) $id;
    if ($id > 0) {
      $this->load($id);
    }
  }

  /**
   * Retrieves a training variant
   * @param  int  variant ID
   * @return void
   */
  public function load($id)
  {
    if ($id > 0) {
      $row = $this->ci->training_model->variant_get($id);
      if ($row) {
        $this->id = $row->id;
        $this->type_id = $row->type_id;
        $this->name = $row->name;
        $this->sequence = $row->sequence;
      }
    }
  }

}

/**
 * Basic Training Class
 */
class CmsTrainingBase {

  /**
   * training attributes
   */
  public $id = 0;
  public $category_id = 0;
  public $title = '';
  public $slug = '';
  public $status = 0;
  public $featured = 0;
  public $featured_image_id = 0;
  public $description;
  public $features;
  public $link;
  public $editor_id = 0;
  public $update_timestamp;
  public $publish_timestamp;
  public $archive_timestamp;
  public $scheduled_publish_timestamp;
  public $scheduled_archive_timestamp;
  public $tags = array();
  public $assets = array();
  public $variants = array();
  public $resources = array();

  /**
   * CodeIgniter global, messages and errors
   * @var string
   */
  protected $ci;
  public $messages = array();
  public $errors = array();
  private $_draft;     // draft object. to access this attribute, use $training->draft
  private $_revisions; // array of revision objects. to access this attribute, use $training->revisions

  /**
   * __construct
   * @param  str  item ID or slug
   * @param  bool  only load live/published item
   * @return void
   */

  public function __construct()
  {
    $this->ci = & get_instance();
    $this->ci->load->model('training/training_model');
    $this->ci->load->library('asset/asset_item');
    $this->ci->load->helper('asset/asset');
  }

  /**
   * Acts as a simple way to call model methods without loads of alias
   */
  public function __call($method, $arguments)
  {
    if (!method_exists($this->ci->training_model, $method)) {
      throw new Exception('Undefined method CmsTraining::' . $method . '()');
    }
    return call_user_func_array(array($this->ci->training_model, $method), $arguments);
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