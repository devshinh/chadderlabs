<?php if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class News_model extends HotCMS_Model {

  private $tables;

  // common properties between news draft, live version and revisions
	public $comm_props = array('author_id', 'editor_id', 'publisher_id', 'slug', 'title', 'snippet', 'body','featured_image_id');

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('news/news',TRUE);
    $this->tables = $this->config->item('tables', 'news');
    $this->default_category_id = $this->config->item('default_category_id', 'news');
  }

  /**
   * Check to see if a news slug already exists
   * @param  str   news slug
   * @param  int   exclude news id
   * @return bool
   */
  public function slug_exists($slug, $exclude_id = 0)
  {
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('slug', $slug);
    if ($exclude_id > 0) {
      $this->db->where('id != ', $exclude_id);
    }
    $query = $this->db->get($this->tables['news']);
    return $query->num_rows();
  }

  /**
   * Get a random slug for showcase purpose
   */
  public function get_random_slug()
  {
    $query = $this->db->select('slug')
      ->where('status', 1)
      ->where('site_id', $this->site_id)
      ->order_by('', 'random')
      ->limit(1)
      ->get($this->tables['news']);
    if ($query->num_rows > 0) {
      $result = $query->row()->slug;
    }
    else {
      $result = '';
    }
    return $result;
  }

  /**
   * Given a slug or ID, retrieve a news from DB
   * @param  int  news ID,
   * @param  str  news slug
   * @param  bool  loads live/published news only
   * @return mixed FALSE if the news does not exist
   */
  public function load_news($id = 0, $slug = '', $live_only = TRUE)
  {
    $id = (int)$id;
    $slug = trim($slug);
    if ($id == 0 && $slug == '') {
      return FALSE;
    }
    // TODO: caching
    // load the live/published version, for the front end website
    $this->db->select()->where('site_id', $this->site_id);
    if ($id > 0) {
      $this->db->where('id', $id);
    }
    else {
      $this->db->where('slug', $slug);
    }
    if ($live_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->get($this->tables['news']);
    return $query->row();
  }

  /**
   * Given a slug, retrieves a news ID
   * returns 0 if the news does not exist
   */
  public function get_news_id($slug)
  {
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('slug', $slug)
      ->get($this->tables['news']);
    if ($query->num_rows()) {
      return $query->row()->id;
    }
    else {
      return 0;
    }
  }

  /**
   * Lists all news categories
   * @return array of objects
   */
  public function list_categories()
  {
    $query = $this->db->where('site_id', $this->site_id)
      ->order_by('sequence', 'ASC')
      ->get($this->tables['category']);
    return $query->result();
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
  public function list_all_news($category = 0, $live_only = TRUE, $user_id = 0, $page_num = 1, $per_page = 0)
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
      $this->db->where('n.category_id', (int)$category);
    }
    if ($live_only) {
      $this->db->where('n.status', 1);
    }
    if ($user_id > 0) {
      $this->db->where('n.author_id', (int)$user_id);
    }
    if ($per_page > 0) {
      $this->db->limit($per_page, $offset);
    }
    $query = $this->db->select('n.*,u.username')
      ->join($this->tables['user'] . ' u', 'u.id=n.author_id')
      ->where('n.site_id', $this->site_id)
      ->order_by('n.update_timestamp, n.create_timestamp', 'DESC')
      ->get($this->tables['news'] . ' n');
    return $query->result();
  }

  /**
   * Counts all news
   * @param  int  category ID
   * @param  bool  live/published only
   * @return int
   */
  public function count_all_news($category = 0, $live_only = TRUE)
  {
    if ($category > 0) {
      $this->db->where('category_id', (int)$category);
    }
    if ($live_only) {
      $this->db->where('status', 1);
    }
    $this->db->where('site_id', $this->site_id)
      ->from($this->tables['news']);
    return $this->db->count_all_results();
  }

  /**
   * Insert a new record
   * @return mixed  news ID if succeed or FALSE if failed
   */
  public function insert_news($attr)
  {
    $site_id = (int)($this->site_id);
    if ($site_id < 1) {
      return FALSE;
    }
    $title = trim($attr["title"]);
    if (array_key_exists('slug', $attr) && $attr["slug"] > '') {
      $slug = format_url($attr["slug"]);
    }
    if ($slug == '') {
      $slug = format_url($title);
    }
    $ts = time();
    $this->db->set( 'site_id',  $site_id);
    $this->db->set( 'category_id', (int)($this->default_category_id));
    $this->db->set( 'status', array_key_exists('status', $attr) ? $attr['status'] : 0 );
    $this->db->set( 'author_id', (int)($this->session->userdata( 'user_id' )) );
    $this->db->set( 'enable_comments', array_key_exists('enable_comments', $attr) ? $attr['enable_comments'] : 0 );
    $this->db->set( 'slug', $slug );
    $this->db->set( 'title', $title );
    $this->db->set( 'snippet', array_key_exists('snippet', $attr) ? $attr['snippet'] : '' );
    $this->db->set( 'body', array_key_exists('body', $attr) ? $attr['body'] : '' );
    $this->db->set( 'featured_image_id', array_key_exists('featured_image_id', $attr) ? $attr['featured_image_id'] : 0 );
    $this->db->set( 'create_timestamp', $ts);
    $this->db->set( 'update_timestamp', $ts);
    $inserted = $this->db->insert( $this->tables['news'] );
    if ($inserted) {
      $news_id = $this->db->insert_id();
      // alwasy insert a draft for each new record
      $this->db->set( 'id',  $news_id);
      $this->db->set( 'author_id', (int)($this->session->userdata( 'user_id' )) );
      $this->db->set( 'slug', $slug );
      $this->db->set( 'title', $title );
      $this->db->set( 'snippet', array_key_exists('snippet', $attr) ? $attr['snippet'] : '' );
      $this->db->set( 'body', array_key_exists('body', $attr) ? $attr['body'] : '' );
      $this->db->set( 'featured_image_id', array_key_exists('featured_image_id', $attr) ? $attr['featured_image_id'] : 0 );
      $this->db->set( 'update_timestamp', $ts);
      $inserted = $this->db->insert( $this->tables['draft'] );
      return $news_id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Given a slug or ID, retrieves a news draft from DB
   * @param  int  news ID,
   * @param  str  news slug
   * @return mixed FALSE if the news does not exist
   */
  public function draft_get($id = 0, $slug = '')
  {
    $id = (int)$id;
    $slug = trim($slug);
    if ($id == 0 && $slug == '') {
      return FALSE;
    }
    if ($id > 0) {
      $this->db->select()->where('d.id', $id);
    }
    elseif ($slug > '') {
      $this->db->select('d.*,p.status,p.enable_comments,p.create_timestamp,p.publish_timestamp,p.archive_timestamp,
        p.scheduled_publish_timestamp,p.scheduled_archive_timestamp', FALSE)
        ->join($this->tables['news'] . ' p', 'p.id=d.id')
        ->where('d.slug', $slug)
        ->where('p.site_id', $this->site_id);
    }
    $query = $this->db->get($this->tables['draft'] . ' d');
    return $query->row();
  }

  /**
   * Inserts a new draft
   * Normally a draft is always created with a new item,
   * only use this function when a news was imported or for some reason the draft was missing
   *
   * @param  object  news
   * @return mixed
   */
  public function draft_insert($news)
  {
    if ($news->id <= 0) {
      return FALSE;
    }
    $this->db->set('id', $news->id);
    $this->db->set('revision_id', $news->revision_id);
    $this->db->set('update_timestamp', time());
    foreach ($this->comm_props as $att) {
      $this->db->set($att, $news->$att);
    }
    return $this->db->insert($this->tables['draft']);
  }

  /**
   * Update a draft
   * @param  int  news ID
   * @param  array  news attributes
   */
  public function draft_update($id, $attr)
  {
    $id = (int)$id;
    if (is_array($attr)) {
      if (array_key_exists('slug', $attr)) {
        $this->db->set('slug', $attr['slug']);
      }
      if (array_key_exists('title', $attr)) {
        $this->db->set('title', $attr['title']);
      }
      if (array_key_exists('snippet', $attr)) {
        $this->db->set('snippet', $attr['snippet']);
      }
      if (array_key_exists('body', $attr)) {
        $this->db->set('body', $attr['body']);
      }
      if (array_key_exists('featured_image_id', $attr)) {
        $this->db->set('featured_image_id', $attr['featured_image_id']);
      }
      // if getting published or scheduled to be published on a future date,
      // set the publisher ID as the current editor user ID
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
  }

  /**
   * Reverts a draft from a revision
   * @param  object  revision row
   * @return bool
   */
  public function draft_revert($rev)
  {
    if (empty($rev) || $rev->news_id < 1) {
      return FALSE;
    }
    // update draft attributes
    $this->db->set('revision_id', $rev->id);
    foreach ($this->comm_props as $prop) {
      $this->db->set($prop, $rev->$prop);
    }
    $this->db->set('update_timestamp', $rev->create_timestamp);
    $this->db->where('id', $rev->news_id);
    return $this->db->update($this->tables['draft']);
  }

  /**
   * Deletes a news
   * @param  int  news ID
   * @return bool
   */
  public function delete_by_id($id)
  {
    $id = (int)$id;
    if ($id > 0) {
      // delete news revisions
      $this->db->where( 'news_id', $id );
      $this->db->delete( $this->tables['revision'] );
      // delete draft
      $this->db->where( 'id', $id );
      $this->db->delete( $this->tables['draft'] );
      // delete news
      $this->db->where( 'id', $id );
      return $this->db->delete( $this->tables['news'] );
    }
    return FALSE;
  }

  /**
   * Lists all revisions of a news
   * @param  id  news id
   * @return array of objects
   */
  public function revision_list($id)
  {
    $id = (int)$id;
    $query = $this->db->select('r.*, u.username')
      ->join($this->tables['user'] . ' u', 'u.id=r.author_id')
      ->where('r.news_id', $id)
      ->order_by('r.update_timestamp DESC')
      ->get($this->tables['revision'] . ' r');
    return $query->result();
  }

  /**
   * Loads a revision from DB
   * @param  int  news id
   * @param  str  news slug
   * @param  int  news revision ID, if 0 loads the latest version
   * @return object with one row
   */
  public function revision_get($id = 0, $slug = '', $rid = 0)
  {
    $id = (int)$id;
    $slug = trim($slug);
    $rid = (int)$rid;
    if ($id < 1 && $slug == '') {
      return FALSE;
    }
    $this->db->select('r.*', FALSE);
    if ($id > 0) {
      $this->db->where('r.news_id', $id);
    }
    elseif ($slug > '') {
      $this->db->join($this->tables['news'] . ' p', 'p.id=r.news_id')
        ->where('p.site_id', $this->site_id)
        ->where('r.slug', $slug);
    }
    if ($rid > 0) {
      $this->db->where('r.id', $rid);
    }
    $this->db->order_by('r.create_timestamp', 'DESC')->limit(1);
    $query = $this->db->get($this->tables['revision'] . ' r');
    if ($query->num_rows() == 1) {
      return $query->row();
    }
    else {
      return FALSE;
    }
  }

  /**
   * Inserts a new revision
   * @param  object  news or news draft
   * @param  int  news status
   * @return mixed
   */
  public function revision_insert($news, $status = 0)
  {
    if ($news->id < 1) {
      return FALSE;
    }
    $this->db->set('news_id', $news->id);
    $this->db->set('status', $status);
    $this->db->set('update_timestamp', time());
    $this->db->set('publish_timestamp', $news->publish_timestamp);
    foreach ($this->comm_props as $att) {
      $this->db->set($att, $news->$att);
    }
    $inserted = $this->db->insert($this->tables['revision']);
    if ($inserted) {
      $revision_id = $this->db->insert_id();
      // update draft with the new revision ID
      $this->db->set('revision_id', $revision_id);
      $this->db->where('id', $news->id);
      $this->db->update($this->tables['draft']);
      return $revision_id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Publishes news from draft
   * @param  object  news draft
   * @return bool
   */
  public function news_publish($draft)
  {
    if (empty($draft) || $draft->id <= 0 || $draft->revision_id <= 0) {
      return FALSE;
    }
    // update news attributes
    foreach ($this->comm_props as $prop) {
      $this->db->set($prop, $draft->$prop);
    }
    $this->db->set('revision_id', $draft->revision_id);
    $this->db->set('status', 1);
    $ts = time();
    $this->db->set('update_timestamp', $ts);
    $this->db->set('publish_timestamp', $ts);
    $this->db->where('id', $draft->id);
    return $this->db->update($this->tables['news']);
  }

  /**
   * Archives a news and set it to hidden
   * @param  int  news ID
   * @param  bool  change status back to draft instead of archived
   * @return bool
   */
  public function news_archive($id, $draft = FALSE)
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
    return $this->db->update($this->tables['news']);
  }

  /**
   * Set up a schedule for a news item to go live or off-line on a future date
   * @param  int  news ID
   * @param  int  publish timestamp
   * @param  int  archive timestamp
   * @return bool
   */
  public function news_schedule($id, $scheduled_publish_timestamp, $scheduled_archive_timestamp)
  {
    if ($id <= 0) {
      return FALSE;
    }
    $this->db->set('scheduled_publish_timestamp', $scheduled_publish_timestamp);
    $this->db->set('scheduled_archive_timestamp', $scheduled_archive_timestamp);
    $this->db->where('id', $id);
    return $this->db->update($this->tables['news']);
  }

  /**
   * Run through all scheduled news, either publish or archive them
   * @return bool
   */
  public function news_schedule_run()
  {
    // publish items
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('status', 0)
      ->where('scheduled_publish_timestamp >', 0)
      ->where('scheduled_publish_timestamp <', time())
      ->get($this->tables['news']);
    foreach ($query->result() as $row) {
      // publish the latest draft
      $draft = $this->draft_get($row->id);
      if ($draft) {
        $published = $this->news_publish($draft);
        //TODO: log executed schedule task results
        if ($published) {
          echo 'News ' . $draft->title . ' was published successfully.<br />';
          // update related revision status
          $this->db->set('status', 1)
            ->where('id', $draft->revision_id)
            ->update($this->tables['revision']);
        }
        else {
          echo 'News ' . $draft->title . ' failed to publish.<br />';
        }
      }
    }
    // archive items
    $this->db->set('status', 2)
      ->set('archive_timestamp', time())
      ->where('site_id', $this->site_id)
      ->where('status', 1)
      ->where('scheduled_archive_timestamp >', 0)
      ->where('scheduled_archive_timestamp <', time());
    $this->db->update($this->tables['news']);
  }

  /**
   * get_items_for_news - get all item for news
   *
   *
   *  @param id new
   *  @return object with one row
   *
   */
  public function get_items_for_news($id) {
    $this->db->select()->from($this->tables['news_item']);
    $this->db->join($this->tables['training_item'] . ' t', 't.id = '.$this->tables['news_item'].'.training_item_id');
    $this->db->where('news_id', $id);
    $query = $this->db->get();

    return $query->result();
  }

  public function delete_all_items_for_news($news_id){
    $this->db->where('news_id', $news_id);
    $this->db->delete($this->tables['news_item']);
  }
  public function delete_item_for_news($item_id, $news_id){
    $this->db->where('news_id', $news_id);
    $this->db->where('training_item_id', $item_id);
    $this->db->delete($this->tables['news_item']);
  }

  public function add_item($news_id, $item_id){
    //add new item
    $this->db->set('news_id', $news_id);
    $this->db->set('training_item_id', $item_id);
    $this->db->insert($this->tables['news_item']);

    //update timestamp
    $this->db->set('update_timestamp', time(), false);
    $this->db->where('id', $news_id);
    $this->db->update($this->tables['news']);
  }
  
  
  /**
   * Insert a new record
   * @return mixed  news ID if succeed or FALSE if failed
   */
  public function insert_news_ea($attr)
  {
    $ts = time();
    $this->db->set( 'site_id', 2);
    $this->db->set( 'category_id', 1);
    $this->db->set( 'status', 1 );
    $this->db->set( 'author_id', 1 );
    $this->db->set( 'editor_id', 1 );
    $this->db->set( 'enable_comments', 0 );
    $this->db->set( 'slug', format_url($attr['title']) );
    $this->db->set( 'title', $attr['title'] );
    $this->db->set( 'snippet', $attr['sumary'] );
    $this->db->set( 'body', $attr['text'] );
    $this->db->set( 'featured_image_id', 0 );
    $this->db->set( 'create_timestamp', $attr['created']);
    $this->db->set( 'update_timestamp', $ts);
    $inserted = $this->db->insert( $this->tables['news'] );
    if ($inserted) {
      $news_id = $this->db->insert_id();
      // alwasy insert a draft for each new record
      $this->db->set( 'id',  $news_id);
      $this->db->set( 'author_id', 1 );
      $this->db->set( 'slug', format_url($attr['title']) );
      $this->db->set( 'title', $attr['title'] );
      $this->db->set( 'snippet', $attr['sumary'] );
      $this->db->set( 'body', $attr['text'] );
      $this->db->set( 'featured_image_id',  0 );
      $this->db->set( 'update_timestamp', $ts);
      $inserted = $this->db->insert( $this->tables['draft'] );
      return $news_id;
    }
    else {
      return FALSE;
    }
  }  
   public function load_ea_news(){

//  &lt;/p&gt;
       
       //update news set snippet = replace(snippet, "&lt;/p&gt;", "") where site_id= 2
       
       //&lt;p&gt;
       //update news set snippet = replace(snippet, "&lt;p&gt;", "") where site_id= 2
    $query = $this->db->select('*')
      ->order_by('id', 'ASC')
      ->get('eanews');
    return $query->result();
  }
       
   
  
}
?>