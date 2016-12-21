<?php if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Page_model extends HotCMS_Model {

  private $tables;

  // common properties between draft, live version and revisions
	public $comm_props = array('author_id', 'editor_id', 'publisher_id', 'layout_id', 'name', 'url', 'url_parser', 'heading', 'meta_title', 'meta_keyword', 'meta_description', 'style_sheet', 'javascript');

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('page/page',TRUE);
    $this->tables = $this->config->item('tables', 'page');
    $this->demo_text = $this->config->item('demo_text', 'page');
  }

  /**
   * Check to see if a page URL already exists
   * @param  str   page URL
   * @param  int   exclude page id
   * @return bool
   */
  public function url_exists($url, $exclude_pid = 0)
  {
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('url', $url);
    if ($exclude_pid > 0) {
      $this->db->where('id != ', $exclude_pid);
    }
    $query = $this->db->get($this->tables['page']);
    $result = $query->num_rows() > 0;
    return $result;
  }

  /**
   * Given an URL or ID, retrieve a page from DB
   * @param  int  page ID,
   * @param  str  page URL
   * @param  bool  loads live/published page only
   * @return mixed FALSE if the page does not exist
   */
  public function load_page($id = 0, $url = '', $live_only = TRUE)
  {
    $id = (int)$id;
    $url = trim($url);
    if ($id == 0 && $url == '') {
      return FALSE;
    }
    // TODO: page caching
    // load the live/published version, for the front end website
    $this->db->select()->where('site_id', $this->site_id);
    if ($id > 0) {
      $this->db->where('id', $id);
    }
    else {
      $this->db->where('url', $url);
    }
    if ($live_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->get($this->tables['page']);
    $row_count = $query->num_rows();
    if ($row_count > 0) {
      return $query->row();
    }
    elseif ($url > '') {
      // if a page was not found, try the dynamic URLs
      $partial = $url;
      $ipos = strrpos($partial, '/');
      while ($ipos > 0 && $row_count == 0) {
        $partial = substr($url, 0, $ipos);
        $query = $this->db->select()->where('site_id', $this->site_id)
          ->where('url', $partial . '/*')
          ->where('status', 1)
          ->get($this->tables['page']);
        $row_count = $query->num_rows();
        if ($row_count > 0) {
          $page = $query->row();
          $page->widget_slug = substr($url, strlen($partial)+1);
          return $page;
        }
        $ipos = strrpos($partial, '/');
      }
    }
    return FALSE;
  }

  /**
   * Lists all sections in a page
   * @param  int  page ID,
   * @return array
   */
  public function list_page_sections($id)
  {
    $id = (int)$id;
    $sections = array();
    if ($id < 1) {
      return $sections;
    }
    $query = $this->db->select()
      ->where('page_id', $id)
      ->order_by('sequence')
      ->get($this->tables['page_section']);
    return $query->result();
  }

  /**
   * Given an URL, retrieves a page's ID
   * @return int page ID, or 0 if the page does not exist
   */
  public function get_page_id($url)
  {
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('url', $url)
      ->get($this->tables['page']);
    if ($query->num_rows()) {
      return $query->row()->id;
    }
    else {
      return 0;
    }
  }

  /**
   * Lists all pages from DB
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function list_all_pages($page_num = 1, $per_page = 100)
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
    $query = $this->db->where('site_id', $this->site_id)
      ->order_by('update_timestamp', 'DESC')
      ->limit($per_page, $offset)
      ->get($this->tables['page']);
    return $query->result();
  }

  /**
   * Counts all pages
   * @return int
   */
  public function count_all_pages()
  {
    $query = $this->db->where('site_id', $this->site_id)
      ->from($this->tables['page']);
    return $this->db->count_all_results();
  }

  /**
   * get section by id
   * @param id   section id
   * @return object with one row
   *
  public function get_section_by_id($id) {
    $id = (int)$id;
    $query = $this->db->select()
      ->where('id', $id)
      ->get($this->tables['page']);
    return $query->row();
  } */

  /**
   * Insert a new page record
   * @return mixed  page ID if succeed or FALSE if failed
   */
  public function insert_page($attr)
  {
    $site_id = (int)($this->site_id);
    if ($site_id < 1) {
      return FALSE;
    }
    $name = trim($attr["name"]);
    if (array_key_exists('url', $attr) && $attr["url"] > '') {
      $url = format_url($attr["url"]);
    }
    if ($url == '') {
      $url = format_url($name);
    }
    if (array_key_exists('heading', $attr)) {
      $heading = trim($attr["heading"]);
    }
    else {
      $heading = $name;
    }
    if (array_key_exists('meta_title', $attr)) {
      $meta_title = trim($attr["meta_title"]);
    }
    else {
      $meta_title = $name;
    }
    $ts = time();
    $this->db->set( 'site_id',  $site_id);
    $this->db->set( 'status', array_key_exists('status', $attr) && $attr['status'] ? $attr['status'] : 0 );
    $this->db->set( 'author_id', (int)($this->session->userdata( 'user_id' )) );
    $this->db->set( 'name', $name );
    $this->db->set( 'url', $url );
    $this->db->set( 'heading', $heading );
    $this->db->set( 'meta_title', $meta_title );
    $this->db->set( 'create_timestamp', $ts);
    $this->db->set( 'update_timestamp', $ts);
    $inserted = $this->db->insert( $this->tables['page'] );
    if ($inserted) {
      $page_id = $this->db->insert_id();
      // insert a dummy page section
      /*
      $this->db->set( 'page_id', $page_id );
      $this->db->set( 'section_type', 0 );
      $this->db->set( 'zone', 'upper_zone' );
      $this->db->set( 'sequence', 0 );
      $this->db->set( 'content', $this->demo_text );
      $this->db->insert( $this->tables['page_section'] );
      */
      // alwasy insert a draft for new records
      $this->db->set( 'id',  $page_id);
      $this->db->set( 'author_id', (int)($this->session->userdata( 'user_id' )) );
      $this->db->set( 'name', $name );
      $this->db->set( 'url', $url );
      $this->db->set( 'heading', $heading );
      $this->db->set( 'meta_title', $meta_title );
      $this->db->set( 'update_timestamp', $ts);
      $inserted = $this->db->insert( $this->tables['draft'] );
      return $page_id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Given an URL or ID, retrieves a page draft from DB
   * @param  int  page ID,
   * @param  str  page URL
   * @return mixed FALSE if the page does not exist
   */
  public function draft_get($id = 0, $url = '')
  {
    $id = (int)$id;
    $url = trim($url);
    if ($id == 0 && $url == '') {
      return FALSE;
    }
    if ($id > 0) {
      $this->db->select()->where('r.id', $id);
    }
    elseif ($url > '') {
      $this->db->select('r.*,p.module,p.status,p.create_timestamp,p.publish_timestamp,p.archive_timestamp,
        p.scheduled_publish_timestamp,p.scheduled_archive_timestamp', FALSE)
        ->join($this->tables['page'] . ' p', 'p.id=r.id')
        ->where('r.url', $url)
        ->where('p.site_id', $this->site_id);
    }
    $query = $this->db->get($this->tables['draft'] . ' r');
    if ($query->num_rows() == 1) {
      $row = $query->row();
      $query = $this->db->select()
        ->where('page_id', $row->id)
        ->order_by('sequence')
        ->get($this->tables['draft_section']);
      $row->sections = $query->result();
      return $row;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Inserts a new page draft
   * @param  object  page
   * @return mixed
   */
  public function draft_insert($page)
  {
    if ($page->id <= 0) {
      return FALSE;
    }
    $this->db->set('id', $page->id);
    $this->db->set('revision_id', $page->revision_id);
    $this->db->set('update_timestamp', time());
    foreach ($this->comm_props as $prop) {
      $this->db->set($prop, $page->$prop);
    }
    $inserted = $this->db->insert($this->tables['draft']);
    if ($inserted) {
      // insert sections
      foreach ($page->sections as $section) {
        $this->db->set( 'page_id', $page->id );
        $this->db->set( 'section_type', $section->section_type );
        $this->db->set( 'zone', $section->zone );
        $this->db->set( 'content', $section->content );
        $this->db->set( 'module_widget', $section->module_widget );
        $this->db->set( 'sequence', $section->sequence );
        $this->db->insert( $this->tables['draft_section'] );
      }
      return $inserted;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Update a page draft
   * @param  int  page ID
   * @param  array  page attributes
   * @return bool
   */
  public function draft_update($id, $attr)
  {
    $id = (int)$id;
    if (is_array($attr)) {
      foreach ($this->comm_props as $prop) {
        if (array_key_exists($prop, $attr)) {
          $this->db->set($prop, $attr[$prop]);
        }
      }
      // if getting published or scheduled to go live on a future date,
      // set the publisher ID as the current editing user ID
      if ((array_key_exists('scheduled_publish_date', $attr) && $attr['scheduled_publish_date'] > '') || (array_key_exists('status', $attr) && $attr['status'] == '1')) {
        $this->db->set('publisher_id', (int)($this->session->userdata('user_id')));
      }
    }
    // remove previous revision ID since content has been changed
    $this->db->set('revision_id', 0);
    $this->db->set('editor_id', (int)($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['draft']);
    // update sections
    /*
    if (array_key_exists('section', $attr) && array_key_exists('section_type', $attr) && array_key_exists('section_zone', $attr) && array_key_exists('section_sequence', $attr) &&
      is_array($attr['section']) && is_array($attr['section_type']) && is_array($attr['section_zone']) && is_array($attr['section_sequence'])) {
      $section_type_array = $attr['section_type'];
      $section_zone_array = $attr['section_zone'];
      $section_sequence_array = $attr['section_sequence'];
      foreach ($attr['section'] as $k=>$v) {
        //$this->db->set( 'revision_id', $rid );  // parent ID and section type will not change
        //$this->db->set( 'section_type', $section_type_array[$k] );
        $this->db->set( 'zone', $section_zone_array[$k] );
        $this->db->set( 'sequence', $section_sequence_array[$k] );
        if ($section_type_array[$k] == 1) {
          $this->db->set( 'module_widget', $v );
        }
        else {
          $this->db->set( 'content', $v );
        }
        $this->db->where( 'id', $k );
        $this->db->update( $this->tables['draft_section'] );
      }
    } */
  }

  /**
   * Reverts a page draft from a revision
   * @param  object  page revision
   */
  public function draft_revert($rev)
  {
    if (empty($rev) || $rev->page_id < 1) {
      return FALSE;
    }
    // update draft sections
    $this->db->where( 'page_id', $rev->page_id )
      ->delete( $this->tables['draft_section'] );
    foreach ($rev->sections as $section) {
      $this->db->set( 'page_id', $rev->page_id );
      $this->db->set( 'section_type', $section->section_type );
      $this->db->set( 'zone', $section->zone );
      $this->db->set( 'module_widget', $section->module_widget );
      $this->db->set( 'content', $section->content );
      $this->db->set( 'sequence', $section->sequence );
      $this->db->insert( $this->tables['draft_section'] );
    }
    // update draft attributes
    $this->db->set( 'revision_id', $rev->id );
    foreach ($this->comm_props as $prop) {
      $this->db->set($prop, $rev->$prop);
    }
    $this->db->set( 'update_timestamp', $rev->update_timestamp );
    $this->db->where( 'id', $rev->page_id );
    return $this->db->update( $this->tables['draft'] );
  }

  /**
   * Gets a section from a draft
   * @param  int  section id
   * @return object
   */
  public function draft_get_section($id)
  {
    $id = (int)$id;
    $query = $this->db->select()
      ->where('id', $id)
      ->get($this->tables['draft_section']);
    return $query->row();
  }

  /**
   * Mark a draft as changed, by removing its previous revision ID
   * @param  int  draft id
   * @return bool
   */
  private function _draft_mark_as_changed($id)
  {
    return $this->db->set('revision_id', 0)
      ->where('id', (int)$id)
      ->update($this->tables['draft']);
  }

  /**
   * Insert a new section to a draft
   * $param  int  section type, 0 = text, 1 = module/widget
   * $param  string  layout zone
   * $param  string  widget code
   * @return mixed  section ID if succeed or FALSE if failed
   */
  public function draft_insert_section($id, $section_type=0, $zone='', $module_widget='')
  {
    $id = (int)$id;
    $section_type = (int)$section_type;
    //$sections = self::_count_sections($id);
    $this->db->set( 'page_id', $id );
    $this->db->set( 'section_type', $section_type );
    $this->db->set( 'zone', $zone );
    $this->db->set( 'sequence', 0 );
    if ($section_type == 1) {
      $this->db->set( 'module_widget', $module_widget );
    }
    else {
      $this->db->set( 'content', $this->demo_text );
    }
    $inserted = $this->db->insert( $this->tables['draft_section'] );
    if ($inserted) {
      $section_id = $this->db->insert_id();
      // mark the draft as changed
      $this->_draft_mark_as_changed($id);
      return $section_id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Update a section in draft
   * @param  int  page ID
   * @param  int  section ID
   * @param  str  section content
   * @return bool
   */
  public function draft_update_section($pid, $sid, $content)
  {
    $result = FALSE;
    $pid = (int)$pid;
    $sid = (int)$sid;
    if ($pid > 0 && $sid > 0) {
      $this->db->set( 'content', $content );
      $this->db->where( 'id', $sid );
      $result = $this->db->update( $this->tables['draft_section'] );
      if ($result && $this->db->affected_rows() > 0) {
        // mark the draft as changed
        $this->_draft_mark_as_changed($pid);
      }
    }
    return $result;
  }

  /**
   * Rearrange a section in draft
   * @param  int  page ID
   * @param  int  section ID
   * @param  str  section zone
   * @param  int  section sequence
   * @return bool
   */
  public function draft_rearrange_section($pid, $sid, $zone, $sequence)
  {
    $result = FALSE;
    $pid = (int)$pid;
    $sid = (int)$sid;
    if ($pid > 0 && $sid > 0) {
      $this->db->set( 'zone', $zone );
      $this->db->set( 'sequence', $sequence );
      $this->db->where( 'id', $sid );
      $result = $this->db->update( $this->tables['draft_section'] );
      if ($result && $this->db->affected_rows() > 0) {
        // mark the draft as changed
        $this->_draft_mark_as_changed($pid);
      }
    }
    return $result;
  }

  /**
   * Delete a section from draft
   * @param  int  page ID
   * @param  int  section ID
   * @return bool
   */
  public function draft_delete_section($pid, $sid)
  {
    $pid = (int)$pid;
    $sid = (int)$sid;
    if ($pid > 0 && $sid > 0) {
      $result = $this->db->where('id', $sid)
        ->delete($this->tables['draft_section']);
      if ($result) {
        // mark the draft as changed
        $this->_draft_mark_as_changed($pid);
      }
      return $result;
    }
    return FALSE;
  }

  /**
   * Deletes a page
   * @param  int  page ID
   * @return bool
   */
  public function delete_by_id($id)
  {
    $id = (int)$id;
    if ($id > 0) {
      // delete revision sections
      $sql = sprintf("DELETE FROM %s WHERE id IN (SELECT id FROM %s WHERE page_id=%d)",
        $this->tables['revision_section'], $this->tables['revision'], $id);
      $this->db->query( $sql );
      // delete revisions
      $this->db->where( 'page_id', $id )
        ->delete( $this->tables['revision'] );
      // delete draft sections
      $this->db->where( 'page_id', $id )
        ->delete( $this->tables['draft_section'] );
      // delete draft
      $this->db->where( 'id', $id )
        ->delete( $this->tables['draft'] );
      // delete page sections
      $this->db->where( 'page_id', $id )
        ->delete( $this->tables['page_section'] );
      // finally delete the page
      return $this->db->where( 'id', $id )
        ->delete( $this->tables['page'] );
    }
    return FALSE;
  }

  /**
   * Lists all revisions of a page
   * @param  id  page id
   * @return array of objects
   */
  public function revision_list($id)
  {
    $id = (int)$id;
    $query = $this->db->select('r.*, u.username')
      ->join($this->tables['user'] . ' u', 'u.id=r.author_id')
      ->where('r.page_id', $id)
      ->order_by('r.update_timestamp DESC')
      ->get($this->tables['revision'] . ' r');
    return $query->result();
  }

  /**
   * Loads a revision from DB
   * @param  int  page id
   * @param  str  page URL
   * @param  int  page revision ID, if 0 loads the latest version
   * @return object with one row
   */
  public function revision_get($id = 0, $url = '', $rid = 0)
  {
    $id = (int)$id;
    $url = trim($url);
    $rid = (int)$rid;
    if ($id < 1 && $url == '') {
      return FALSE;
    }
    $this->db->select('r.*', FALSE);
    if ($id > 0) {
      $this->db->where('r.page_id', $id);
    }
    elseif ($url > '') {
      $this->db->join($this->tables['page'] . ' p', 'p.id=r.page_id')
        ->where('p.site_id', $this->site_id)
        ->where('r.url', $url);
    }
    if ($rid > 0) {
      $this->db->where('id', $rid);
    }
    $this->db->order_by('update_timestamp', 'DESC')->limit(1);
    $query = $this->db->get($this->tables['revision'] . ' r');
    if ($query->num_rows() == 1) {
      $rev = $query->row();
      $query = $this->db->select()
        ->where('revision_id', $rev->id)
        ->order_by('sequence')
        ->get($this->tables['revision_section']);
      $rev->sections = $query->result();
      return $rev;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Inserts a new page revision
   * @param  object  page draft
   * @param  int  page status
   * @return mixed
   */
  public function revision_insert($page, $status = 0)
  {
    if ($page->id < 1) {
      return FALSE;
    }
    $this->db->set('page_id', $page->id);
    $this->db->set('status', $status);
    $this->db->set('update_timestamp', time());
    foreach ($this->comm_props as $prop) {
      $this->db->set($prop, $page->$prop);
    }
    $inserted = $this->db->insert( $this->tables['revision'] );
    if ($inserted) {
      $revision_id = $this->db->insert_id();
      // insert page sections of this revision
      foreach ($page->sections as $section) {
        $this->db->set( 'revision_id', $revision_id );
        $this->db->set( 'section_type', $section->section_type );
        $this->db->set( 'zone', $section->zone );
        $this->db->set( 'content', $section->content );
        $this->db->set( 'module_widget', $section->module_widget );
        $this->db->set( 'sequence', $section->sequence );
        $this->db->insert( $this->tables['revision_section'] );
      }
      // update the draft
      $this->db->set( 'revision_id', $revision_id );
      $this->db->where( 'id', $page->id );
      $this->db->update( $this->tables['draft'] );
      return $revision_id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Update a page's permission and sitemap option
   * note: all other changes must be done in draft, never update a page directly
   * @param  int  page ID
   * @param  array  page attributes
   * @return bool
   */
  public function page_update($id, $attr)
  {
    $id = (int)$id;
    if (is_array($attr)) {
      if (array_key_exists('exclude_sitemap', $attr)) {
        $this->db->set('exclude_sitemap', $attr['exclude_sitemap']);
      }
    }
    $perm = '';
    if (array_key_exists('permissions', $attr)) {
      if (is_array($attr['permissions'])) {
        $permissions = array();
        foreach ($attr['permissions'] as $k => $v) {
          $permissions[] = $k;
        }
        $perm = serialize($permissions);
      }
    }
    $this->db->set('permissions', $perm);
    $this->db->set('editor_id', (int)($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['page']);
  }

  /**
   * Publishes a page from a draft
   * @param  object  page draft
   */
  public function page_publish($draft)
  {
    if (empty($draft) || $draft->id <= 0 || $draft->revision_id <= 0) {
      return FALSE;
    }
    // update page sections
    // delete sections
    $this->db->where('page_id', $draft->id)
      ->delete($this->tables['page_section']);
    // insert sections
    foreach ($draft->sections as $section) {
      $this->db->set('page_id', $draft->id);
      $this->db->set('section_type', $section->section_type);
      $this->db->set('zone', $section->zone);
      $this->db->set('module_widget', $section->module_widget);
      $this->db->set('content', $section->content);
      $this->db->set('sequence', $section->sequence);
      $this->db->insert($this->tables['page_section']);
    }
    // update page attributes
    foreach ($this->comm_props as $prop) {
      $this->db->set($prop, $draft->$prop);
    }
    $this->db->set('revision_id', $draft->revision_id);
    $this->db->set('status', 1);
    $ts = time();
    $this->db->set('update_timestamp', $ts);
    $this->db->set('publish_timestamp', $ts);
    $this->db->where('id', $draft->id);
    return $this->db->update($this->tables['page']);
  }

  /**
   * Archives a page and set it to hidden on the front-end
   * @param  int  page ID
   * @param  bool  change status back to draft instead of archived
   * @return bool
   */
  public function page_archive($id, $draft = FALSE)
  {
    if ($id <= 0) {
      return FALSE;
    }
    if ($draft) {
      $this->db->set('status', 0);
      $this->db->set('publish_timestamp', 0);
    }
    else {
      $this->db->set('status', 2);
      $this->db->set('archive_timestamp', time());
    }
    $this->db->where('id', $id);
    return $this->db->update($this->tables['page']);
  }

  /**
   * Set up a schedule for a page to go live or off-line on a future date
   * @param  int  page ID
   * @param  int  publish timestamp
   * @param  int  archive timestamp
   * @return bool
   */
  public function page_schedule($id, $scheduled_publish_timestamp, $scheduled_archive_timestamp)
  {
    if ($id <= 0) {
      return FALSE;
    }
    $this->db->set('scheduled_publish_timestamp', $scheduled_publish_timestamp);
    $this->db->set('scheduled_archive_timestamp', $scheduled_archive_timestamp);
    $this->db->where('id', $id);
    return $this->db->update($this->tables['page']);
  }

  /**
   * Run through all scheduled tasks, either publish or archive them
   * @return bool
   */
  public function page_schedule_run()
  {
    // publish
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('status', 0)
      ->where('scheduled_publish_timestamp >', 0)
      ->where('scheduled_publish_timestamp <', time())
      ->get($this->tables['page']);
    foreach ($query->result() as $row) {
      // publish the latest draft
      $draft = $this->draft_get($row->id);
      if ($draft) {
        $published = $this->page_publish($draft);
        //TODO: log executed schedule task results
        if ($published) {
          echo 'Page ' . $draft->name . ' was published successfully.<br />';
          // update related revision status
          $this->db->set('status', 1)
            ->where('id', $draft->revision_id)
            ->update($this->tables['revision']);
        }
        else {
          echo 'Page ' . $draft->name . ' failed to publish.<br />';
        }
      }
    }
    // archive
    $this->db->set('status', 2)
      ->set('archive_timestamp', time())
      ->where('site_id', $this->site_id)
      ->where('status', 1)
      ->where('scheduled_archive_timestamp >', 0)
      ->where('scheduled_archive_timestamp <', time());
    $this->db->update($this->tables['page']);
  }

  /**
   * List available page layouts
   * @return array of objects
   */
  public function list_layouts()
  {
    $query = $this->db->where('site_id', $this->site_id)
      ->order_by('id', 'ASC')
      ->get($this->tables['page_layout']);
    return $query->result();
  }

  /**
   * List zones in a layout
   * @param  int  layout ID
   * @return array of string
   */
  public function list_layout_zones($layout_id)
  {
    $layout_id = (int)$layout_id;
    $query = $this->db->select('zones')
      ->where('site_id', $this->site_id)
      ->where('id', $layout_id)
      ->get($this->tables['page_layout']);
    $layout = $query->row();
    if ($layout) {
      return explode(',', $layout->zones);
    }
    else {
      return array();
    }
  }

  /**
   * Get training item page for a site/subdoamin/brand.
   * @param  int    $site_id row id of site table
   * @return object training item page
   */
  function get_training_item_page($site_id) {
    if (((int) $site_id) < 2) {
      return;
    }
    return $this->db->select("p.*")->join("page_section s", "s.page_id = p.id")->where("s.module_widget", "training:training_item_widget")->get_where("page p", array("p.site_id" => $site_id))->row();
  }

  /**
   * Get news item page for a site/subdoamin/brand.
   * @param  int    $site_id row id of site table
   * @return object news item page
   */
  function get_news_item_page($site_id = 0) {
    if ($site_id > 0) {
      return $this->db->select("p.*")->join("page_section s", "s.page_id = p.id")->where("s.module_widget", "news:news_item_widget")->get_where("page p", array("p.site_id" => $site_id))->row();
    }
    return NULL;
  }
}
?>