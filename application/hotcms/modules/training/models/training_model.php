<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Training_model extends HotCMS_Model {

  private $tables;
  // common properties between draft, live version and revisions
  public $comm_props = array('category_id', "target_id", 'title', 'slug', 'status', 'editor_id', 'featured', 'featured_image_id', 'description',
      'features', 'scheduled_publish_timestamp', 'scheduled_archive_timestamp', 'link');

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('training/training', TRUE);
    $this->tables = $this->config->item('tables', 'training');
  }

  /**
   * Check to see if a training slug already exists
   * @param  str   training slug
   * @param  int   exclude training id
   * @return bool
   */
  public function slug_exists($slug, $exclude_id = 0) {
    $query = $this->db->select('id')
            ->where('site_id', $this->site_id)
            ->where('slug', $slug);
    if ($exclude_id > 0) {
      $this->db->where('id != ', $exclude_id);
    }
    $query = $this->db->get($this->tables['training']);
    return $query->num_rows();
  }

  /**
   * Get a random slug for showcase purpose
   */
  public function get_random_slug() {
    $query = $this->db->select('slug')
            ->where('status', 1)
            ->where('site_id', $this->site_id)
            ->order_by('', 'random')
            ->limit(1)
            ->get($this->tables['training']);
    if ($query->num_rows > 0) {
      $result = $query->row()->slug;
    } else {
      $result = '';
    }
    return $result;
  }

  /**
   * Get a random slug for preview widget
   */
  public function get_preview_slug($type) {
    $this->db->select('slug')
            ->where('status', 1)
            ->where('site_id', $this->site_id);
    if ($type == 'featured') {
      $this->db->where('featured', 1);
      $this->db->order_by('', 'random');
      $this->db->limit(1);
    }
    if ($type == 'coming_soon') {
      $this->db->order_by('create_timestamp', 'DESC');
      //$this->db->order_by('', 'random')
      $this->db->limit(2);
    }
    if ($type == 'new') {
      //has quiz defined
      $this->db->order_by('create_timestamp', 'ASC');
      $this->db->limit(2);
    }
    $query = $this->db->get($this->tables['training']);
    if ($query->num_rows > 0) {
      $result = $query->result();
    } else {
      $result = '';
    }
    return $result;
  }

  /**
   * Given a slug or ID, retrieve a training from DB
   * @param  int  training ID,
   * @param  str  training slug
   * @param  bool  loads live/published training only
   * @param  bool  loads training for current domain/site only
   * @return mixed FALSE if the training does not exist
   */
  public function training_load($id = 0, $slug = '', $live_only = TRUE, $current_site_only = TRUE)
  {
    $id = (int) $id;
    $slug = trim($slug);
    if ($id == 0 && $slug == '') {
      return FALSE;
    }
    if ($id > 0 && !$current_site_only) {
      $this->db->select('t.*, s.domain, s.name AS site_name')
        ->join($this->tables['site'] . ' s', 's.id=t.site_id');
    }
    else {
      $this->db->select()->where('t.site_id', $this->site_id);
    }
    if ($id > 0) {
      $this->db->where('t.id', $id);
    }
    else {
      $this->db->where('t.slug', $slug);
    }
    if ($live_only) {
      $this->db->where('t.status', 1);
    }
    $query = $this->db->get($this->tables['training'] . ' t');
    return $query->row();
  }

  /**
   * Given a slug, retrieves a training ID
   * returns 0 if the training does not exist
   */
  public function get_training_id($slug)
  {
    $query = $this->db->select('id')
            ->where('site_id', $this->site_id)
            ->where('slug', $slug)
            ->get($this->tables['training']);
    if ($query->num_rows()) {
      return $query->row()->id;
    }
    else {
      return 0;
    }
  }

  /**
   * Lists all training categories
   * @return array of objects
   */
  public function training_category_list()
  {
    $query = $this->db->where('site_id', $this->site_id)
            ->order_by('parent_id', 'ASC')
            ->order_by('name', 'ASC')
            ->get($this->tables['category']);
    return $query->result();
  }
  
  /**
   * Load default category ID
   * @return category ID
   */
  public function load_default_category_id()
  {
    $query = $this->db->select()
            ->where('site_id', $this->site_id)
            ->where('parent_id !=',0)
            ->limit(1)
            ->get($this->tables['category']);
    return $query->row('id');
  }
  

  /**
   * Lists all training from DB
   * @param  array  filters
   * @param  bool  live/published only
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function training_list($filters = FALSE, $live_only = TRUE, $page_num = 1, $per_page = 0)
  {
    $per_page = (int)$per_page;
    $page_num = (int)$page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num - 1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    if ($per_page > 0) {
      $this->db->limit($per_page, $offset);
    }
    if ($live_only) {
      $this->db->where('status', 1);
      $table = $this->tables['training_live'] . ' t';
    }
    else {
      $table = $this->tables['training'] . ' t';
    }
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
        $this->db->like('t.title', $filters['keyword']);
      }
      
      $sortable_fields = array('status','title','create_timestamp','category_id', 'update_timestamp', 'editor_id');
      if (array_key_exists('sort_by', $filters) && $filters['sort_by'] > '' && in_array($filters['sort_by'], $sortable_fields)) {
        if (array_key_exists('sort_direction', $filters) && strtoupper($filters['sort_direction']) == 'DESC') {
          $sort_direction = 'DESC';
        }
        else {
          $sort_direction = 'ASC';
        }
        $this->db->order_by("t.".$filters['sort_by'], $sort_direction);
      }
      else {
        $this->db->order_by('t.title', 'ASC');
      }
      
    } else {
      $this->db->order_by('t.title', 'ASC');
    }
    $query = $this->db->select('t.*,u.username,c.name AS category_name')
    //$query = $this->db->select('t.*')
      ->join($this->tables['user'] . ' u', 'u.id=t.editor_id', 'left outer')
      ->join($this->tables['category'] . ' c', 'c.id=t.category_id', 'left outer')
      ->where('t.site_id', $this->site_id)
      ->order_by('t.update_timestamp', 'DESC')
      ->get($table);
    return $query->result();
  }

  /**
   * Counts all training
   * @param  array  filters
   * @param  bool  live/published only
   * @return int
   */
  public function training_count($filters = 0, $live_only = TRUE, $all_sites = FALSE) {
    if ($live_only) {
      $this->db->where('status', 1);
    }
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
          $this->db->like('title', $filters['keyword']);
      }
    }
    if($all_sites){
            $query = $this->db->get($this->tables['training']);
    }else{
    $query = $this->db->where('site_id', $this->site_id)
            ->get($this->tables['training']);
    }
    return $query->num_rows();
  }

  /**
   * Insert a new record
   * @return mixed  training ID if succeed or FALSE if failed
   */
  public function training_insert($attr) {
    $site_id = (int) ($this->site_id);
    if ($site_id < 1) {
      return FALSE;
    }
    if (array_key_exists('slug', $attr) && $attr["slug"] > '') {
      $slug = format_url($attr["slug"]);
    } elseif (array_key_exists('title', $attr) && $attr["title"] > '') {
      $slug = format_url($attr["title"]);
    }
    $ts = time();
    foreach ($this->comm_props as $prop) {
      if (array_key_exists($prop, $attr)) {
        $this->db->set($prop, $attr[$prop]);
      }
    }
    $this->db->set('site_id', $site_id);
    $this->db->set('slug', $slug);
    $this->db->set('author_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('create_timestamp', $ts);
    $this->db->set('update_timestamp', $ts);
    $inserted = $this->db->insert($this->tables['training']);
    if ($inserted) {
      $training_id = $this->db->insert_id();
      // create a draft immediately
      foreach ($this->comm_props as $prop) {
        if (array_key_exists($prop, $attr)) {
          $this->db->set($prop, $attr[$prop]);
        }
      }
      $this->db->set('training_id', $training_id);
      $this->db->set('slug', $slug);
      $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
      $this->db->set('update_timestamp', $ts);
      $inserted = $this->db->insert($this->tables['revision']);
      return $training_id;
    } else {
      return FALSE;
    }
  }

  /**
   * Inserts a new draft, mostly in a situation where draft is missing
   * @param  object  training
   * @return mixed
   */
  public function draft_insert($training) {
    if ($training->id <= 0) {
      return FALSE;
    }
    foreach ($this->comm_props as $prop) {
      $this->db->set($prop, $training->$prop);
    }
    $this->db->set('training_id', $training->id);
    $this->db->set('slug', $training->slug);
    $this->db->set('tags', serialize($training->tags));
    $this->db->set('asset', serialize($training->assets));
    $this->db->set('variant', serialize($training->variants));
    $this->db->set('resource', serialize($training->resources));
    $this->db->set('update_timestamp', time());
    $inserted = $this->db->insert($this->tables['revision']);
    if ($inserted) {
      $revision_id = $this->db->insert_id();
      return $inserted;
    } else {
      return FALSE;
    }
  }

  /**
   * Reverts a training draft from a revision
   * @param  object  training revision
   */
  public function draft_revert($rev)
  {
    if (empty($rev) || $rev->training_id < 1) {
      return FALSE;
    }
    // update tags
    $this->db->where('training_id', $rev->training_id)
      ->delete($this->tables['training_tags']);
    foreach ($rev->tags as $tag) {
      $this->db->set('training_id', $rev->training_id);
      $this->db->set('tag_id', $tag->tag_id);
      $this->db->insert($this->tables['training_tags']);
    }
    // update training assets
    $this->db->where('training_id', $rev->training_id)
      ->delete($this->tables['training_asset']);
    foreach ($rev->assets as $a) {
      $this->db->set('training_id', $rev->training_id);
      $this->db->set('asset_id', $a->asset_id);
      $this->db->insert($this->tables['training_asset']);
    }
    // update training resources
    $this->db->where('training_id', $rev->training_id)
      ->delete($this->tables['training_resource']);
    foreach ($rev->resources as $res) {
      $this->db->set('training_id', $rev->training_id);
      $this->db->set('asset_id', $res->asset_id);
      $this->db->insert($this->tables['training_resource']);
    }
    // update variants
    $this->db->where('training_id', $rev->training_id)
      ->delete($this->tables['variant']);
    foreach ($rev->tags as $tag) {
      $this->db->set('training_id', $rev->training_id);
      $this->db->set('tag_id', $tag->tag_id);
      $this->db->insert($this->tables['variant']);
    }
    //variant_detail
//a:2:{i:0;O:8:"stdClass":4:{s:2:"id";s:1:"1";s:11:"training_id";s:1:"3";s:8:"sequence";s:1:"0";s:7:"details";a:3:{i:0;O:8:"stdClass":6:{s:2:"id";s:1:"1";s:10:"variant_id";s:1:"1";s:8:"field_id";s:1:"2";s:5:"label";N;s:5:"value";s:4:"test";s:8:"sequence";s:1:"0";}i:1;O:8:"stdClass":6:{s:2:"id";s:1:"2";s:10:"variant_id";s:1:"1";s:8:"field_id";s:1:"1";s:5:"label";N;s:5:"value";s:4:"Xbox";s:8:"sequence";s:1:"0";}i:2;O:8:"stdClass":6:{s:2:"id";s:1:"3";s:10:"variant_id";s:1:"1";s:8:"field_id";s:1:"4";s:5:"label";N;s:5:"value";s:4:"good";s:8:"sequence";s:1:"0";}}}i:1;O:8:"stdClass":4:{s:2:"id";s:1:"2";s:11:"training_id";s:1:"3";s:8:"sequence";s:1:"0";s:7:"details";a:3:{i:0;O:8:"stdClass":6:{s:2:"id";s:1:"4";s:10:"variant_id";s:1:"2";s:8:"field_id";s:1:"2";s:5:"label";N;s:5:"value";s:5:"test2";s:8:"sequence";s:1:"0";}i:1;O:8:"stdClass":6:{s:2:"id";s:1:"5";s:10:"variant_id";s:1:"2";s:8:"field_id";s:1:"1";s:5:"label";N;s:5:"value";s:4:"Xbox";s:8:"sequence";s:1:"0";}i:2;O:8:"stdClass":6:{s:2:"id";s:1:"6";s:10:"variant_id";s:1:"2";s:8:"field_id";s:1:"4";s:5:"label";N;s:5:"value";s:5:"test3";s:8:"sequence";s:1:"0";}}}}
    // update attributes
    $this->db->set( 'revision_id', $rev->id );
    foreach ($this->comm_props as $prop) {
      $this->db->set($prop, $rev->$prop);
    }
    $this->db->set( 'update_timestamp', $rev->update_timestamp );
    $this->db->where( 'id', $rev->training_id );
    return $this->db->update( $this->tables['training'] );
  }

  /**
   * Update a training (in draft/revision, never in live)
   * @param  int  training revision ID
   * @param  array  training attributes
   */
  public function training_update($id, $attr) {
    $id = (int) $id;
    if (is_array($attr)) {
      //hack -> unchecked checkbox is not included in post array
      if(!array_key_exists('featured', $attr)) $attr['featured'] = 0;
      foreach ($this->comm_props as $prop) {
        if (array_key_exists($prop, $attr)) {
          $this->db->set($prop, $attr[$prop]);
        }
      }
    }
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    $result = $this->db->update($this->tables['training']);
    if ($result) {
      // update tags
      $this->db->where('training_id', $id)
              ->delete($this->tables['training_tags']);
      if (array_key_exists('tags', $attr) && is_array($attr['tags'])) {
        foreach ($attr['tags'] as $tid) {
          $this->db->set('training_id', $id);
          $this->db->set('tag_id', $tid);
          $this->db->insert($this->tables['training_tags']);
        }
      }
    }
    return $result;
  }

  /**
   * Lists all revisions
   * @param  id  training id
   * @return array of objects
   */
  public function revision_list($id) {
    $id = (int) $id;
    $query = $this->db->select('r.*, u.username')
      ->join($this->tables['user'] . ' u', 'u.id=r.editor_id')
      ->where('r.training_id', $id)
      ->order_by('r.update_timestamp DESC')
      ->get($this->tables['revision'] . ' r');
    return $query->result();
  }

  /**
   * Loads a revision from DB
   * @param  int  training id
   * @param  str  training URL
   * @param  int  training revision ID, if 0 loads the latest version
   * @return object with one row
   */
  public function revision_get($id = 0, $url = '', $rid = 0) {
    $id = (int)$id;
    $url = trim($url);
    $rid = (int)$rid;
    if ($id < 1 && $url == '') {
      return FALSE;
    }
    $this->db->select('r.*', FALSE);
    if ($id > 0) {
      $this->db->where('r.training_id', $id);
    } elseif ($url > '') {
      $this->db->join($this->tables['training'] . ' t', 't.id=r.training_id')
        ->where('t.site_id', $this->site_id)
        ->where('r.url', $url);
    }
    if ($rid > 0) {
      $this->db->where('id', $rid);
    }
    $this->db->order_by('update_timestamp', 'DESC')->limit(1);
    $query = $this->db->get($this->tables['revision'] . ' r');
    if ($query->num_rows() == 1) {
      $rev = $query->row();
      if ($rev->tags > '') {
        $rev->tags = unserialize($rev->tags);
      }
      else {
        $rev->tags = array();
      }
      if ($rev->assets > '') {
        $rev->assets = unserialize($rev->assets);
      }
      else {
        $rev->assets = array();
      }
      if ($rev->variants > '') {
        $rev->variants = unserialize($rev->variants);
      }
      else {
        $rev->variants = array();
      }
      if ($rev->resources > '') {
        $rev->resources = unserialize($rev->resources);
      }
      else {
        $rev->resources = array();
      }
      return $rev;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Inserts a new training revision
   * @param  object  training draft
   * @param  int  training status
   * @return mixed
   */
  public function revision_insert($training, $status = 0) {
    if ($training->id < 1) {
      return FALSE;
    }
    $this->db->set('training_id', $training->id);
    $this->db->set('status', $status);
    $this->db->set('update_timestamp', time());
    foreach ($this->comm_props as $prop) {
      $this->db->set($prop, $training->$prop);
    }
    $this->db->set('tags', serialize($training->tags));
    $this->db->set('assets', serialize($training->assets));
    $this->db->set('variants', serialize($training->variants));
    $this->db->set('resources', serialize($training->resources));
    $inserted = $this->db->insert($this->tables['revision']);
    if ($inserted) {
      $revision_id = $this->db->insert_id();
      // update the draft
      $this->db->set('revision_id', $revision_id);
      $this->db->where('id', $training->id);
      $this->db->update($this->tables['training']);
      return $revision_id;
    } else {
      return FALSE;
    }
  }

  /**
   * Lists all assets for a training item
   * @param  int  training id
   * @return array of objects
   */
  public function asset_list($training_id) {
    $training_id = (int)$training_id;
    $query = $this->db->select()
            ->where('training_id', $training_id)
            ->order_by('sequence')
            ->get($this->tables['training_asset']);
    return $query->result();
  }

  /**
   * Retrieves a training asset from DB
   * @param  int  training asset ID,
   * @return mixed FALSE if the training asset does not exist
   */
  public function asset_get($id) {
    $id = (int)$id;
    if ($id == 0) {
      return FALSE;
    }
    $this->db->select()->where('id', $id);
    $query = $this->db->get($this->tables['training_asset']);
    return $query->row();
  }

  /**
   * Inserts a new asset
   * @param  int  training id
   * @param  array  asset attributes
   * @return mixed
   */
  public function asset_insert($training_id, $asset_id, $attr) {
    $this->db->set('training_id', (int)$training_id);
    $this->db->set('asset_id', (int)$asset_id);
    //$this->db->set('title', $attr['title']);
    //$this->db->set('sequence', $attr['sequence']);
    return $this->db->insert($this->tables['training_asset']);
  }

  /**
   * Updates an asset
   * @param  int  training id
   * @param  int  asset id
   * @param  array  asset attributes
   * @return bool
   */
  public function asset_update($training_id, $asset_id, $attr) {
    if ($training_id <= 0 || $asset_id <= 0 || empty($attr)) {
      return FALSE;
    }
    // update attributes
    if (array_key_exists('asset_id', $attr)) {
      $this->db->set('asset_id', (int)($attr['asset_id']));
    }
    //$this->db->set('title', $attr['title']);
    //$this->db->set('sequence', $attr['sequence']);
    $this->db->where('training_id', (int)$training_id);
    $this->db->where('asset_id', (int)$asset_id);
    return $this->db->update($this->tables['training_asset']);
  }

  /**
   * Deletes an asset
   * @param  int  training id
   * @param  int  asset id
   * @return bool
   */
  public function asset_delete($training_id, $asset_id) {
    if ($asset_id <= 0 || $training_id <= 0) {
      return FALSE;
    }
    $this->db->where('training_id', (int)$training_id);
    $this->db->where('asset_id', (int)$asset_id);
    return $this->db->delete($this->tables['training_asset']);
  }

  /**
   * Deletes a training
   * @param  int  training ID
   * @return bool
   */
  public function delete($id) {
    $id = (int)$id;
    if ($id > 0) {
      // delete related relational data
      $this->db->where('training_id', $id);
      $this->db->delete($this->tables['training_asset']);
      $this->db->where('training_id', $id);
      $this->db->delete($this->tables['training_resource']);
      $this->db->where('training_id', $id);
      $this->db->delete($this->tables['training_tags']);
      $this->db->where('training_id', $id);
      $this->db->delete($this->tables['variant']);
      // delete revisions?
      //$this->db->where( 'training_id', $id );
      //$this->db->delete( $this->tables['revision'] );
      // delete training
      $this->db->where('id', $id);
      return $this->db->delete($this->tables['training']);
    }
    return FALSE;
  }

  /**
   * Publishes a training
   * @param  int  training id
   * @return bool
   */
  public function training_publish($id) {
    if ($id <= 0) {
      return FALSE;
    }
    $this->db->set('status', 1);
    $ts = time();
    $this->db->set('update_timestamp', $ts);
    $this->db->set('publish_timestamp', $ts);
    $this->db->where('id', $id);
    return $this->db->update($this->tables['training']);
  }

  /**
   * Archives a training and set it to hidden
   * @param  int  training ID
   * @return bool
   */
  public function training_archive($id) {
    if ($id <= 0) {
      return FALSE;
    }
    $this->db->set('status', 0);
    $this->db->set('archive_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['training']);
  }

  /**
   * Set up a schedule for a training item to go live or off-line on a future date
   * @param  int  training ID
   * @param  int  publish timestamp
   * @param  int  archive timestamp
   * @return bool
   */
  public function training_schedule($id, $scheduled_publish_timestamp, $scheduled_archive_timestamp) {
    if ($id <= 0) {
      return FALSE;
    }
    $this->db->set('scheduled_publish_timestamp', $scheduled_publish_timestamp);
    $this->db->set('scheduled_archive_timestamp', $scheduled_archive_timestamp);
    $this->db->where('id', $id);
    return $this->db->update($this->tables['training']);
  }

  /**
   * Run through all scheduled training, either publish or archive them
   * @return bool
   */
  public function training_schedule_run() {
    //TODO: log all cron job results
    // publish items
    $this->db->set('status', 1)
            ->set('publish_timestamp', time())
            ->where('site_id', $this->site_id)
            ->where('status', 0)
            ->where('scheduled_publish_timestamp >', 0)
            ->where('scheduled_publish_timestamp <', time());
    $this->db->update($this->tables['training']);
    // archive items
    $this->db->set('status', 0)
            ->set('archive_timestamp', time())
            ->where('site_id', $this->site_id)
            ->where('status', 1)
            ->where('scheduled_archive_timestamp >', 0)
            ->where('scheduled_archive_timestamp <', time());
    $this->db->update($this->tables['training']);
  }

  /**
   * Insert a new category + one default subcategory
   * @param int quiz type id
   *
   * @return bool
   */
  public function category_add() {

    $ts = time();
    $this->db->set('site_id', $this->site_id);
    $this->db->set('name', 'Category');
    $this->db->set('author_id', (int)($this->session->userdata('user_id')));
    $this->db->set('editor_id', (int)($this->session->userdata('user_id')));
    $this->db->set('create_timestamp', $ts);
    $this->db->set('update_timestamp', $ts);

    $this->db->insert($this->tables['category']);

    $category_id = $this->db->insert_id();

    $this->subcategory_add($category_id);

    if (is_numeric($category_id)) {
      return true;
    } else {
      return false;
    }
  }

  public function subcategory_add($cat_id) {
    $ts = time();

    //get last sequence for parent_id
    $query = $this->db->select('max(sequence) as sq')
            ->where('parent_id', $cat_id)
            ->get($this->tables['category']);

    if ($query->num_rows()) {
      $sequence = $query->row()->sq + 1;
    } else {
      $sequence = 1;
    }

    $this->db->set('name', 'Generic Subcategory');
    $this->db->set('template_id', 1);

    $this->db->set('site_id', $this->site_id);
    $this->db->set('parent_id', $cat_id);
    $this->db->set('sequence', $sequence);
    $this->db->set('author_id', (int)($this->session->userdata('user_id')));
    $this->db->set('editor_id', (int)($this->session->userdata('user_id')));
    $this->db->set('create_timestamp', $ts);
    $this->db->set('update_timestamp', $ts);

    return $this->db->insert($this->tables['category']);
  }

  /* category functions */

  /**
   * get all training categories
   * @param  bool
   * @return array of objects
   */
  public function category_get_all($main_cat = FALSE) {
    if ($main_cat)
      $this->db->where('parent_id', 0);
    $query = $this->db->select()
            ->where('site_id', $this->site_id)
            ->order_by('sequence, name')
            ->get($this->tables['category']);
    return $query->result();
  }

  public function category_get_all_childs($parent_id) {
    $query = $this->db->select()
            ->where('site_id', $this->site_id)
            ->where('parent_id', $parent_id)
            ->order_by('sequence, name')
            ->get($this->tables['category']);
    return $query->result();
  }

  /**
   * Delete quiz category
   * @param int category id
   * @return bool
   *
   * TODO delete all subcats
   */
  public function category_delete($cat_id) {
    $id = (int)$cat_id;
    if ($id > 0) {
      $this->db->where('id', $id);
      return $this->db->delete($this->tables['category']);
    }
    return FALSE;
  }

  /**
   * get all training subcategories
   * @param int category id
   * @return array of objects
   */
  public function subcategory_get_by_category($category_id) {
    $query = $this->db->select()
            ->where('site_id', $this->site_id)
            ->where('parent_id', $category_id)
            ->order_by('sequence')
            ->get($this->tables['category']);
    return $query->result();
  }

  /**
   * Delete quiz subcategories
   * @param int category id
   * @return bool
   */
  public function delete_subcategory_by_category_id($sid) {
    $id = (int)$sid;
    if ($id > 0) {
      $this->db->where('category_id', $id);
      return $this->db->delete($this->tables['subcategory']);
    }
    return FALSE;
  }

  /**
   * Retrieves a training category from DB
   * @param  int  category ID,
   * @return mixed FALSE if the category does not exist
   */
  public function category_get($id) {
    $id = (int)$id;
    if ($id == 0) {
      return FALSE;
    }
    $this->db->select()->where('id', $id);
    $query = $this->db->get($this->tables['category']);
    return $query->row();
  }

  /**
   * Updates a category
   * @param  int  quesiton id
   * @param  array  category attributes
   * @return bool
   */
  public function category_update($category_id, $attr) {
    if ($category_id <= 0 || empty($attr)) {
      return FALSE;
    }
    // update attributes
    $this->db->set('name', $attr['name']);
    $this->db->set('editor_id', (int)($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $category_id);
    return $this->db->update($this->tables['category']);
  }

  /* category functions ends */

  /* resource functions starts */

  /**
   * Lists all resources for a training item
   * @param  int  training id
   * @return array of objects
   */
  public function resource_list($training_id)
  {
    $training_id = (int)$training_id;
    $query = $this->db->select()
      //->join($this->tables['asset'] . ' a', 'a.id=r.asset_id')
      ->where('r.training_id', $training_id)
      ->order_by('r.sequence')
      ->get($this->tables['training_resource'] . ' r');
    return $query->result();
  }

  /**
   * Retrieves a training resource from DB
   * @param  int  training resource ID,
   * @return mixed FALSE if the training resource does not exist
   */
  public function resource_get($id)
  {
    $id = (int)$id;
    if ($id == 0) {
      return FALSE;
    }
    $this->db->select()->where('id', $id);
    $query = $this->db->get($this->tables['training_resource']);
    return $query->row();
  }

  /**
   * Inserts a new resource
   * @param  int  training id
   * @param  int  asset id
   * @param  array  resource attributes
   * @return mixed
   */
  public function resource_insert($training_id, $asset_id, $attr)
  {
    $this->db->set('training_id', (int)$training_id);
    $this->db->set('asset_id', (int)$asset_id);
    $result = $this->db->insert($this->tables['training_resource']);
    return $result;
  }

  /**
   * Updates a resource
   * @param  int  training id
   * @param  int  resource id
   * @param  array  resource attributes
   * @return bool
   */
  public function resource_update($training_id, $asset_id, $attr)
  {
    if ($training_id <= 0 || $asset_id <= 0 || empty($attr) || !array_key_exists('asset_id', $attr)) {
      return FALSE;
    }
    // update attributes
    $this->db->set('asset_id', (int)($attr['asset_id']));
    $this->db->where('training_id', (int)$training_id);
    $this->db->where('asset_id', (int)$asset_id);
    return $this->db->update($this->tables['training_resource']);
  }

  /**
   * Deletes a resource
   * @param  int  training id
   * @param  int  resource id
   * @return bool
   */
  public function resource_delete($training_id, $asset_id)
  {
    if ($asset_id <= 0 || $training_id <= 0) {
      return FALSE;
    }
    $this->db->where('training_id', (int)$training_id);
    $this->db->where('asset_id', (int)$asset_id);
    return $this->db->delete($this->tables['training_resource']);
  }

  /* resource functions ends */

  /* tag functions */

  /**
   * Insert a new record
   * @param int training category id
   */
  public function tag_type_add($category_id) {
    //get last sequence for $type_id
    $query = $this->db->select('max(sequence) as sq')
            ->where('category_id', $category_id)
            ->get($this->tables['tag_type']);


    if ($query->num_rows()) {
      $sequence = $query->row()->sq + 1;
    }
    $this->db->set('site_id', $this->site_id);
    $this->db->set('category_id', $category_id);
    $this->db->set('editor_id', (int)($this->session->userdata('user_id')));
    $this->db->set('author_id', (int)($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->set('create_timestamp', time());
    $this->db->set('type_name', 'Tag type name');
    $this->db->set('sequence', $sequence);
    return $this->db->insert($this->tables['tag_type']);
  }

  /**
   * Insert a new record
   * @param int training category id
   */
  public function tag_type_add_child($parent_tag_id) {
    //get last sequence for $type_id
    $query = $this->db->select('max(sequence) as sq')
            ->where('type_id', $parent_tag_id)
            ->get($this->tables['tag']);


    if ($query->num_rows()) {
      $sequence = $query->row()->sq + 1;
    }
    $this->db->set('type_id', $parent_tag_id);
    //$this->db->set('parent_id', $tag_id);
    //$this->db->set('editor_id', (int)($this->session->userdata('user_id')));
    //$this->db->set('author_id', (int)($this->session->userdata('user_id')));
    //$this->db->set('update_timestamp', time());
    //$this->db->set('create_timestamp', time());
    $this->db->set('name', 'Tag Type item name');
    $this->db->set('sequence', $sequence);
    return $this->db->insert($this->tables['tag']);
  }

  /**
   * get all type tags
   * @param int category id
   * @param bool list just main tags
   * @return array of objects
   */
  public function type_tag_get_by_category_id($category_id, $main = FALSE) {
    if ($main)
      $this->db->where('parent_id', 0);
    $query = $this->db->select()
            ->where('site_id', $this->site_id)
            ->where('category_id', $category_id)
            ->order_by('sequence')
            ->get($this->tables['tag_type']);
    return $query->result();
  }

  /**
   * Delete tag type
   * @param int category id
   * @return bool
   */
  public function tag_type_delete($tag_id) {
    $id = (int)$tag_id;
    if ($id > 0) {
      $this->db->where('id', $id);
      return $this->db->delete($this->tables['tag_type']);
    }
    return FALSE;
  }

  /**
   * Lists all tag types
   * @param int category id
   * @return array of objects
   */
  public function tag_type_list($category_id = 0) {
    $category_id = (int)$category_id;
    if ($category_id > 0) {
      $this->db->where('category_id', $category_id);
    }
    $query = $this->db->select()
            ->where('site_id', $this->site_id)
            ->order_by('type_name')
            ->get($this->tables['tag_type']);
    return $query->result();
  }

  /**
   * Retrieves a training tag type from DB
   * @param  int  training tag type ID,
   * @return mixed FALSE if the training tag does not exist
   */
  public function tag_type_get($id) {
    $id = (int)$id;
    if ($id == 0) {
      return FALSE;
    }
    $this->db->select()->where('id', $id);
    $query = $this->db->get($this->tables['tag_type']);
    return $query->row();
  }
    /**
   * Retrieves a training tags for parent tag
   * @param  int  training tag type ID,
   * @return mixed FALSE if the training tag does not exist
   */
  public function tag_type_get_childs($id) {
    $id = (int)$id;
    if ($id == 0) {
      return FALSE;
    }
    $this->db->select()->where('type_id', $id);
    $this->db->order_by('sequence');
    $query = $this->db->get($this->tables['tag']);
    //var_dump($this->db->last_query());
    return $query->result();
  }

  /**
   * get all training subcategories
   * @param int tag id
   * @return array of objects
   *
   * deprecated ??
   */
  public function tag_type_get_by_tag($tag_type_id) {
    $query = $this->db->select()
            ->where('site_id', $this->site_id)
            ->where('parent_id', $tag_type_id)
            ->order_by('sequence')
            ->get($this->tables['tag_type']);
    return $query->result();
  }
  public function tag_type_get_tag_detail($id) {
    $id = (int)$id;
    if ($id == 0) {
      return FALSE;
    }
    $this->db->select()->where('id', $id);
    $query = $this->db->get($this->tables['tag']);
    //var_dump($this->db->last_query());
    return $query->row();
  }

  /**
   * Update a training tag type
   * @param  int  training tag type ID
   * @param  array  training tag type attributes
   */
  public function tag_type_update($id, $attr) {
    $id = (int)$id;
    if (is_array($attr)) {
      if (array_key_exists('name', $attr)) {
        $this->db->set('type_name', $attr['name']);
      }
    }
    $this->db->where('id', $id);
    return $this->db->update($this->tables['tag_type']);
  }

  /**
   * Lists all tags under a training tag type
   * @param  int  training tag type id
   * @param  int  category id
   * @return array of objects
   */
  public function tag_list($type_id = 0, $category_id = 0) {
    $type_id = (int)$type_id;
    $category_id = (int)$category_id;
    if ($type_id > 0) {
      $this->db->where('type_id', $type_id)
              ->order_by('sequence');
    } elseif ($category_id > 0) {
      $this->db->where('tt.category_id', $category_id)
              ->join($this->tables['tag_type'] . ' tt', 'tt.id=t.type_id');
    }
    $query = $this->db->select('t.*')
            ->get($this->tables['tag'] . ' t');
    $result = $query->result();
    //echo $this->db->last_query();
    return $result;
  }

  /**
   * Retrieves a training tag from DB
   * @param  int  training tag ID,
   * @return mixed FALSE if the training tag does not exist
   */
  public function tag_get($id) {
    $id = (int)$id;
    if ($id == 0) {
      return FALSE;
    }
    $this->db->select()->where('id', $id);
    $query = $this->db->get($this->tables['tag']);
    return $query->row();
  }

  /**
   * Update a training tag
   * @param  int  training tag ID
   * @param  array  training tag attributes
   */
  public function tag_update($id, $attr) {
    $id = (int)$id;
    if (is_array($attr)) {
      if (array_key_exists('name', $attr)) {
        $this->db->set('name', $attr['name']);
      }
      if (array_key_exists('sequence', $attr)) {
        $this->db->set('sequence', $attr['sequence']);
      }
    }
    $this->db->where('id', $id);
    return $this->db->update($this->tables['tag']);
  }

  /**
   * Deletes a training tag
   * @param  int  training tag ID
   * @return bool
   */
  public function tag_delete($id) {
    $id = (int)$id;
    if ($id > 0) {
      $this->db->where('id', $id);
      return $this->db->delete($this->tables['tag']);
    }
    return FALSE;
  }

  /**
   * Lists all tags for a training object
   * @param  int  training id
   * @return array of objects
   */
  public function training_tag_list($training_id) {
    $training_id = (int) $training_id;
    if ($training_id == 0) {
      return FALSE;
    }
    $query = $this->db->select('tags.tag_id, t.name,type.type_name, type.id as type_id')
            ->join($this->tables['tag'] . ' t', 't.id=tags.tag_id')
            ->join($this->tables['tag_type'] . ' type', 't.type_id=type.id')
            ->where('tags.training_id', $training_id)
            ->order_by('type.sequence, t.sequence')
            ->get($this->tables['training_tags'] . ' tags');
    $rows = $query->result();
    $result = array();
    foreach ($rows as $row) {
      $result[$row->tag_id] = $row;
    }
    return $result;
  }

  /* variant functions */

  /**
   * Lists all variant fields under a category
   * @param  int  category id
   * @return array of objects
   */
  public function variant_field_list($category_id) {
    $category_id = (int) $category_id;
    $query = $this->db->select()
            ->where('category_id', $category_id)
            ->order_by('sequence')
            ->get($this->tables['variant_field']);
    $rows = $query->result();
    $result = array();
    foreach ($rows as $row) {
      $result[$row->id] = $row;
    }
    return $result;
  }

  /**
   * Retrieves a variant field from DB
   * @param  int  variant field ID,
   * @return mixed FALSE if the variant field does not exist
   */
  public function variant_field_get($id) {
    $id = (int) $id;
    if ($id == 0) {
      return FALSE;
    }
    $query = $this->db->select()->where('id', $id)
            ->get($this->tables['variant_field']);
    return $query->row();
  }

  /**
   * Inserts a new variant field
   * @param  int  category id
   * @param  array  variant field attributes
   * @return mixed
   */
  public function variant_field_insert($category_id, $attr) {
    //set the sequence = get number of variants in category +1

    $this->db->like('category_id', $category_id);
    $this->db->from($this->tables['variant_field']);
    $variant_count =  $this->db->count_all_results();
      
    $this->db->set('category_id', (int) $category_id);
    $this->db->set('label', trim($attr['field_name']));
    $this->db->set('field_type', trim($attr['field_type']));
    $this->db->set('sequence',$variant_count+1);
    $result = $this->db->insert($this->tables['variant_field']);
    if ($result) {
      $id = $this->db->insert_id();
      return $id;
    } else {
      return FALSE;
    }
  }

  /**
   * Updates a variant field
   * @param  int  field id
   * @param  array  variant field attributes
   * @return bool
   */
  public function variant_field_update($field_id, $attr) {
    if ($field_id <= 0 || empty($attr)) {
      return FALSE;
    }
    // update attributes
    if (array_key_exists('label', $attr)) {
      $this->db->set('label', $attr['label']);
    }
    if (array_key_exists('required', $attr)) {
      $this->db->set('required', $attr['required']);
    }
    if (array_key_exists('visible', $attr)) {
      $this->db->set('visible', $attr['visible']);
    }
    if (array_key_exists('options', $attr)) {
      $this->db->set('options', $attr['options']);
    }
    if (array_key_exists('rows', $attr)) {
      $this->db->set('rows', $attr['rows']);
    }
    if (array_key_exists('max_length', $attr)) {
      $this->db->set('max_length', $attr['max_length']);
    }
    if (array_key_exists('sequence', $attr)) {
      $this->db->set('sequence', $attr['sequence']);
    }
    $this->db->where('id', $field_id);
    return $this->db->update($this->tables['variant_field']);
  }

  /**
   * Deletes a variant field
   * @param  int  variant field id
   * @return bool
   */
  public function variant_field_delete($field_id) {
    if ($field_id <= 0) {
      return FALSE;
    }
    $this->db->where('id', $field_id);
    return $this->db->delete($this->tables['variant_field']);
  }

  /**
   * Lists all variants for a training object
   * @param  int  training id
   * @return array of objects
   */
  public function variant_list($training_id) {
    $training_id = (int) $training_id;
    $query = $this->db->select()
            ->where('training_id', $training_id)
            ->order_by('sequence')
            ->get($this->tables['variant']);
    $result = $query->result();
    foreach ($result as $r) {
      $query = $this->db->select('f.label, f.field_type, d.*')->where('variant_id', $r->id)
              ->join($this->tables['variant_field'] . ' f', 'f.id=d.field_id')
              ->order_by('f.sequence')
              ->get($this->tables['variant_detail'] . ' d');
      $r->details = $query->result();
    }
    return $result;
  }

  /**
   * Inserts a new variant
   * @param  int  training id
   * @param  array  variant field data
   * @return mixed
   */
  public function variant_insert($training_id, $data) {
    if ($training_id == 0 || empty($data)) {
      return FALSE;
    }
    $this->db->set('training_id', (int) $training_id);
    $result = $this->db->insert($this->tables['variant']);
    if ($result) {
      $variant_id = $this->db->insert_id();
      foreach ($data as $k => $v) {
        $this->db->set('variant_id', $variant_id);
        $this->db->set('field_id', $k);
        $this->db->set('value', $v);
        $this->db->insert($this->tables['variant_detail']);
      }
      return $variant_id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Inserts a new variant
   * @param  int  variant id
   * @param  array  variant field data
   * @return mixed
   */
  public function variant_update($variant_id, $data) {
    if ($variant_id == 0 || empty($data)) {
      return FALSE;
    }
    $this->db->where('variant_id', $variant_id);
    $result = $this->db->delete($this->tables['variant_detail']);
    if ($result) {
      foreach ($data as $k => $v) {
        $this->db->set('variant_id', $variant_id);
        $this->db->set('field_id', $k);
        $this->db->set('value', $v);
        $this->db->insert($this->tables['variant_detail']);
      }
      return $result;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Retrieves a variant from DB
   * @param  int  variant ID,
   * @return mixed FALSE if the variant does not exist
   */
  public function variant_get($id) {
    $id = (int) $id;
    if ($id == 0) {
      return FALSE;
    }
    $query = $this->db->select()->where('id', $id)
            ->get($this->tables['variant']);
    $result = $query->row();
    $query2 = $this->db->select('f.label, f.field_type, d.*')->where('variant_id', $id)
            ->join($this->tables['variant_field'] . ' f', 'f.id=d.field_id')
            ->order_by('f.sequence')
            ->get($this->tables['variant_detail'] . ' d');
//    $query = $this->db->select()->where('variant_id', $id)
//            ->get($this->tables['variant_detail']);
    $result->details = $query2->result();
    return $result;
  }

  /**
   * Deletes a training variant
   * @param  int  variant ID
   * @return bool
   */
  public function variant_delete($id) {
    $id = (int) $id;
    if ($id > 0) {
      $this->db->where('variant_id', $id);
      $this->db->delete($this->tables['variant_detail']);
      $this->db->where('id', $id);
      return $this->db->delete($this->tables['variant']);
    }
    return FALSE;
  }

  /**
   * Lists all news from DB
   * @param  int  category ID
   * @param  bool  live/published only
   * @param  int  user ID
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function list_all_training($category = 0, $live_only = TRUE, $has_quiz = FALSE, $user_id = 0, $page_num = 1, $per_page = 0)
  {
    $per_page = (int)$per_page;
    $page_num = (int)$page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num-1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    if ($category > 0) {
      $this->db->where('t.category_id', (int)$category);
    }
    if ($live_only) {
      $this->db->where('t.status', 1);
    }
    if ($user_id > 0) {
      $this->db->where('t.author_id', (int)$user_id);
    }
    if ($per_page > 0) {
      $this->db->limit($per_page, $offset);
    }

    $query = $this->db->select('t.slug')
      ->where('t.site_id', $this->site_id)
      ->order_by('t.update_timestamp', 'DESC')
      ->get($this->tables['training'] . ' t');
    return $query->result();
  }

  /**
   * Get all targets.
   * @param  bool  $all_sites determind site limitation
   * @return array found targets
   */
  function get_all_target_options($all_sites = FALSE) {
    if (!$all_sites) {
      $site_id = $this->session->userdata("siteID");
      if ($site_id > 1) {
        $this->db->where("site_id", $site_id);
      }
    }
    $result = $this->db->get($this->tables["target"])->result();
    if (empty($result)) {
      return array();
    }
    $targets = array("" => " -- select target -- ");
    foreach ($result as $row) {
      $targets[$row->id] = $row->name;
    }
    return $targets;
  }
  
  /**
   * Save sequence in database (duplicated for each module with sortable)
   * @TODO move it to main model, fix return value 
   * @param  string  table
   * @param  int  item id
   * @param  int  item sequence
   */
  
  public function save_image_assets_sequence($table, $id, $sequence)
  {
    $this->db->set( 'sequence', $sequence );
    $this->db->where( 'id', $id );
    $this->db->update( $table );
  }  
}

?>