<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Quiz_model extends HotCMS_Model {

  private $tables;

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('quiz/quiz', TRUE);
    $this->tables = $this->config->item('tables', 'quiz');
  }

  /**
   * Check to see if a quiz slug already exists
   * @param  str   quiz slug
   * @param  int   exclude quiz id
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
    $query = $this->db->get($this->tables['quiz']);
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
      ->get($this->tables['quiz']);
    if ($query->num_rows > 0) {
      $result = $query->row()->slug;
    }
    else {
      $result = '';
    }
    return $result;
  }

  /**
   * Get active quiz slugs for a training item
   * @param product slug
   * @return array of slugs
   */
  public function get_slugs_by_product($training_slug)
  {
    $query = $this->db->select('q.slug')
      ->join($this->tables['training'] . ' t', 't.id=q.training_id')
      ->where('q.status', 1)
      ->where('q.site_id', $this->site_id)
      ->where('t.slug', $training_slug)
      ->order_by('q.name')
      ->get($this->tables['quiz'] . ' q');
    return $query->result();
  }

  /**
   * Given a slug or ID, retrieve a quiz from DB
   * @param  int  quiz ID,
   * @param  str  quiz slug
   * @param  bool  loads live/published quiz only
   * @param  bool  loads quiz for current domain/site only
   * @param  bool  loads quiz that targets user
   * @return mixed FALSE if the quiz does not exist
   */
  function quiz_load($id = 0, $slug = '', $live_only = TRUE, $current_site_only = TRUE, $targeting_user = TRUE) {
    if ($targeting_user && !$this->account_model->is_super_admin($this->session->userdata("user_id"))) {
      $target_ids = $this->session->userdata("targets");
      if (empty($target_ids)) {
        return FALSE;
      } else {
        $target_ids = explode(",", $target_ids);
        $this->db->where_in("q.target_id", $target_ids);
      }
    }
    $id = (int)$id;
    $slug = trim($slug);
    if ($id == 0 && $slug == '') {
      return FALSE;
    }
    if ($id > 0 && !$current_site_only) {
      $this->db->select('q.*, s.domain, s.name AS site_name')
        ->join($this->tables['site'] . ' s', 's.id=q.site_id');
    }
    else {
      $this->db->select()->where('q.site_id', $this->site_id);
    }
    if ($id > 0) {
      $this->db->where('q.id', $id);
    }
    else {
      $this->db->where('q.slug', $slug);
    }
    if ($live_only) {
      $this->db->where('q.status', 1);
    }
    return $this->db->get($this->tables['quiz'] . ' q')->row();
  }

  /**
   * Given a slug, retrieves a quiz ID
   * returns 0 if the quiz does not exist
   */
  public function get_quiz_id($slug)
  {
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('slug', $slug)
      ->get($this->tables['quiz']);
    if ($query->num_rows()) {
      return $query->row()->id;
    }
    else {
      return 0;
    }
  }

  /**
   * Lists all quiz categories
   * @return array of objects
   */
  public function quiz_type_list()
  {
    $query = $this->db->where('site_id', $this->site_id)
      ->order_by('sequence', 'ASC')
      ->get($this->tables['type']);
    return $query->result();
  }

  /**
   * Check number of quizzes for type id
   * @return int number of quizzes
   */
  public function check_quiz_per_type($type_id)
  {
    $query = $this->db->where('quiz_type_id', $type_id)
      ->get($this->tables['quiz']);
    return $query->num_rows();
  }

  /**
   * delete all section of quiz type
   * @return bool
   */
  public function delete_sections_by_type_id($type_id)
  {
    $this->db->where('quiz_type_id', $type_id);
    $this->db->delete($this->tables['type_section']);
    return true;
  }

  /**
   * delete  quiz type
   * @return bool
   */
  public function delete_quiz_type($type_id)
  {
    $this->db->where('id', $type_id);
    $this->db->delete($this->tables['type']);
    return true;
  }

  /**
   * Lists all quizzes from DB
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function quiz_list($filters = FALSE, $page_num = 1, $per_page = 0)
  {
    $per_page = (int) $per_page;
    $page_num = (int) $page_num;
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
    //if ($type_id > 0) {
    //  $this->db->where('n.quiz_type_id', (int)$type_id);
    //}
    //if ($live_only) {
    //  $this->db->where('n.status', 1);
    //}
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
        $this->db->like('n.name', $filters['keyword']);
      }
      if (array_key_exists('type_id', $filters) && $filters['type_id'] > '') {
        if (is_array($filters['type_id'])) {
          $this->db->where_in('n.quiz_type_id', $filters['type_id']);
        }
        else {
          $this->db->where('n.quiz_type_id', $filters['type_id']);
        }
      }
      if (array_key_exists('status', $filters) && $filters['status'] > '') {
        if (is_array($filters['status'])) {
          $this->db->where_in('n.status', $filters['status']);
        }
        else {
          $this->db->where('n.status', $filters['status']);
        }
      }
      $sortable_fields = array('name', 'status', 'create_timestamp');
      if (array_key_exists('sort_by', $filters) && $filters['sort_by'] > '' && in_array($filters['sort_by'], $sortable_fields)) {
        if (array_key_exists('sort_direction', $filters) && strtoupper($filters['sort_direction']) == 'DESC') {
          $sort_direction = 'DESC';
        }
        else {
          $sort_direction = 'ASC';
        }
        $this->db->order_by($filters['sort_by'], $sort_direction);
      }
      else {
        $this->db->order_by('name', 'ASC');
      }
    }
    else {
      $this->db->order_by('name', 'ASC');
    }
    $query = $this->db->select('n.*,u.username,t.title,tp.name AS typename')
      ->join($this->tables['user'] . ' u', 'u.id=n.author_id')
      ->join($this->tables['training'] . ' t', 't.id=n.training_id')
      ->join($this->tables['type'] . ' tp', 'tp.id=n.quiz_type_id')
      ->where('n.site_id', $this->site_id)
      ->order_by('n.update_timestamp', 'DESC')
      ->get($this->tables['quiz'] . ' n');
    return $query->result();
  }

  /**
   * Counts all quizzes
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @return int
   */
  public function quiz_count($filters = FALSE)
  {
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
        $this->db->like('name', $filters['keyword']);
      }
      if (array_key_exists('quiz_type_id', $filters) && $filters['quiz_type_id'] > '') {
        if (is_array($filters['quiz_type_id'])) {
          $this->db->where_in('quiz_type_id', $filters['quiz_type_id']);
        }
        else {
          $this->db->where('quiz_type_id', $filters['quiz_type_id']);
        }
      }
      if (array_key_exists('status', $filters) && $filters['status'] > '') {
        if (is_array($filters['status'])) {
          $this->db->where_in('status', $filters['status']);
        }
        else {
          $this->db->where('status', $filters['status']);
        }
      }
    }
    $this->db->where('site_id', $this->site_id);
    return $this->db->count_all_results($this->tables['quiz']);
  }

  /**
   * Lists all questions in a quiz
   * @param  id  quiz id
   * @return array of objects
   */
  public function question_list($id)
  {
    $id = (int) $id;
    $query = $this->db->select()
      ->where('quiz_id', $id)
      ->order_by('id', 'ASC')
      ->get($this->tables['question']);
    return $query->result();
  }

  /**
   * Insert a new record
   * @return mixed  quiz ID if succeed or FALSE if failed
   */
  public function quiz_insert($attr)
  {
    $site_id = (int) ($this->site_id);
    if ($site_id < 1) {
      return FALSE;
    }
    if (array_key_exists('slug', $attr) && $attr["slug"] > '') {
      $slug = format_url($attr["slug"]);
    }
    elseif (array_key_exists('name', $attr) && $attr["name"] > '') {
      $slug = format_url($attr["name"]);
    }
    $ts = time();
    $this->db->set('site_id', $site_id);
    $this->db->set('quiz_type_id', array_key_exists('quiz_type_id', $attr) ? $attr['quiz_type_id'] : 0);
    $this->db->set('training_id', array_key_exists('training_id', $attr) ? $attr['training_id'] : 0);
    $this->db->set('name', array_key_exists('name', $attr) ? $attr['name'] : '');
    $this->db->set('slug', $slug);
    $this->db->set('author_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('create_timestamp', $ts);
    $this->db->set('update_timestamp', $ts);
    $inserted = $this->db->insert($this->tables['quiz']);
    if ($inserted) {
      $quiz_id = $this->db->insert_id();
      return $quiz_id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Update a quiz
   * @param  int  quiz ID
   * @param  array  quiz attributes
   */
  public function quiz_update($id, $attr)
  {
    $id = (int) $id;
    $old = $this->quiz_load($id, '', FALSE);
    if (!$old) {
      return FALSE;
    }
    if (is_array($attr)) {
      if (array_key_exists('quiz_type_id', $attr)) {
        $this->db->set('quiz_type_id', $attr['quiz_type_id']);
      }
      if (array_key_exists('training_id', $attr)) {
        $this->db->set('training_id', $attr['training_id']);
      }
      if (array_key_exists('slug', $attr)) {
        $this->db->set('slug', $attr['slug']);
      }
      if (array_key_exists('status', $attr)) {
        $this->db->set('status', $attr['status']);
        if ($old->status == 0 && $attr['status'] == 1) {
          $this->db->set('publish_timestamp', time());
        }
        elseif ($old->status == 1 && $attr['status'] == 0) {
          $this->db->set('archive_timestamp', time());
        }
      }
    }
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['quiz']);
  }

  /**
   * Retrieves a quiz question from DB
   * @param  int  quiz question ID,
   * @return mixed FALSE if the quiz question does not exist
   */
  public function question_get($id)
  {
    $id = (int) $id;
    if ($id == 0) {
      return FALSE;
    }
    $this->db->select()->where('id', $id);
    $query = $this->db->get($this->tables['question']);
    return $query->row();
  }

  /**
   * Inserts a new question
   * @param  int  quiz id
   * @param  array  question attributes
   * @return mixed
   */
  public function question_insert($id, $attr)
  {
    $this->db->set('quiz_id', (int) $id);
    $this->db->set('section_id', (int) ($attr['section_id']));
    $this->db->set('question_type', (int) ($attr['question_type']));
    if ($attr['question_type'] == '1') {
      // true/false questions always have the same two options
      $this->db->set('option_1', 'True');
      $this->db->set('option_2', 'False');
    }
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $result = $this->db->insert($this->tables['question']);
    if ($result) {
      $question_id = $this->db->insert_id();
      return $question_id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Updates a question
   * @param  int  quesiton id
   * @param  array  question attributes
   * @return bool
   */
  public function question_update($quesiton_id, $attr)
  {
    if ($quesiton_id <= 0 || empty($attr)) {
      return FALSE;
    }
    // update attributes
    $this->db->set('question', $attr['question']);
    $this->db->set('correct_answer', $attr['correct_answer']);
    $this->db->set('must_show', $attr['must_show']);
    //$this->db->set('required', $attr['required']);
    if ($attr['question_type'] == '2') {
      // multiple choice options
      $this->db->set('option_1', $attr['option_1']);
      $this->db->set('option_2', $attr['option_2']);
      $this->db->set('option_3', $attr['option_3']);
      $this->db->set('option_4', $attr['option_4']);
    }
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $quesiton_id);
    return $this->db->update($this->tables['question']);
  }

  /**
   * Deletes a question
   * @param  int  quesiton id
   * @return bool
   */
  public function question_delete($quesiton_id)
  {
    if ($quesiton_id <= 0) {
      return FALSE;
    }
    $this->db->where('id', $quesiton_id);
    return $this->db->delete($this->tables['question']);
  }

  /**
   * Retrieves a quiz type from DB
   * @param  int  quiz type ID,
   * @return mixed FALSE if the quiz type does not exist
   */
  public function type_get($id)
  {
    $id = (int) $id;
    if ($id == 0) {
      return FALSE;
    }
    $this->db->select()->where('id', $id);
    $query = $this->db->get($this->tables['type']);
    return $query->row();
  }

  /**
   * Update a quiz type
   * @param  int  quiz type ID
   * @param  array  quiz type attributes
   */
  public function type_update($id, $attr)
  {
    $id = (int) $id;
    if (is_array($attr)) {
      if (array_key_exists('name_' . $id, $attr)) {
        $this->db->set('name', $attr['name_' . $id]);
      }
      if (array_key_exists('time_limit_' . $id, $attr)) {
        $this->db->set('time_limit', $attr['time_limit_' . $id]);
      }
      if (array_key_exists('tries_per_day_' . $id, $attr)) {
        $this->db->set('tries_per_day', $attr['tries_per_day_' . $id]);
      }
      if (array_key_exists('tries_per_week_' . $id, $attr)) {
        $this->db->set('tries_per_week', $attr['tries_per_week_' . $id]);
      }
      if (array_key_exists('expiry_period_' . $id, $attr)) {
        $this->db->set('expiry_period', $attr['expiry_period_' . $id]);
      }
      if (array_key_exists('points_pre_expiry_' . $id, $attr)) {
        $this->db->set('points_pre_expiry', $attr['points_pre_expiry_' . $id]);
      }
      if (array_key_exists('points_post_expiry_' . $id, $attr)) {
        $this->db->set('points_post_expiry', $attr['points_post_expiry_' . $id]);
      }
      if (array_key_exists('contest_entries_pre_expiry_' . $id, $attr)) {
        $this->db->set('contest_entries_pre_expiry', $attr['contest_entries_pre_expiry_' . $id]);
      }
      if (array_key_exists('contest_entries_post_expiry_' . $id, $attr)) {
        $this->db->set('contest_entries_post_expiry', $attr['contest_entries_post_expiry_' . $id]);
      }      
      if (array_key_exists('icon_image_id_' . $id, $attr)) {
        $this->db->set('icon_image_id', $attr['icon_image_id_' . $id]);
      }
    }
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['type']);
  }

  /**
   * Deletes a quiz
   * @param  int  quiz ID
   * @return bool
   */
  public function delete($id)
  {
    $id = (int) $id;
    if ($id > 0) {
      // delete quiz questions
      $this->db->where('quiz_id', $id);
      $this->db->delete($this->tables['question']);
      // delete history?
      //$this->db->where( 'quiz_id', $id );
      //$this->db->delete( $this->tables['history'] );
      // delete quiz
      $this->db->where('id', $id);
      return $this->db->delete($this->tables['quiz']);
    }
    return FALSE;
  }

  /**
   * Lists all sections in a quiz type
   * @param  int  quiz type id
   * @return array of objects
   */
  public function type_list_section($id)
  {
    $id = (int) $id;
    $query = $this->db->select()
      ->where('quiz_type_id', $id)
      ->order_by('sequence')
      ->get($this->tables['type_section']);
    return $query->result();
  }

  /**
   * Updates a section in a quiz type
   * @param  int  quiz type section id
   * @param  array  attributes
   * @return bool
   */
  public function type_update_section($id, $attr)
  {
    $id = (int) $id;
    if (is_array($attr)) {
      if (array_key_exists('section_type_' . $id, $attr)) {
        $this->db->set('section_type', $attr['section_type_' . $id]);
      }
      if (array_key_exists('question_pool_' . $id, $attr)) {
        $this->db->set('question_pool', $attr['question_pool_' . $id]);
      }
      if (array_key_exists('questions_per_quiz_' . $id, $attr)) {
        $this->db->set('questions_per_quiz', $attr['questions_per_quiz_' . $id]);
      }
    }
    $this->db->where('id', $id);
    return $this->db->update($this->tables['type_section']);
  }

  /**
   * Insert a new record
   * @param int quiz type id
   */
  public function add_section($type_id)
  {
    //get last sequence for $type_id
    $query = $this->db->select('max(sequence) as sq')
      ->where('quiz_type_id', $type_id)
      ->get($this->tables['type_section']);
    if ($query->num_rows()) {
      $sequence = $query->row()->sq + 1;
    }
    $this->db->set('quiz_type_id', $type_id);
    $this->db->set('section_type', 1);
    $this->db->set('question_pool', 1);
    $this->db->set('questions_per_quiz', 1);
    $this->db->set('sequence', $sequence);
    return $this->db->insert($this->tables['type_section']);
  }

  /**
   * Delete quiz section record
   * @param int section id
   * @param int type id
   */
  public function delete_section($section_id, $type_id)
  {
    //get last sequence for $type_id
    $query = $this->db->select('max(sequence) as sq')
      ->where('quiz_type_id', $type_id)
      ->get($this->tables['type_section']);
    $max_sequence = $query->row()->sq;
    $query = $this->db->select('sequence as sq')
      ->where('id', $section_id)
      ->get($this->tables['type_section']);
    $sequence = $query->row()->sq;
    //deleting last section for type
    if ($max_sequence == $sequence) {
      $this->db->where('id', $section_id);
      $this->db->delete($this->tables['type_section']);
      return true;
    }
    else {
      //have to reorder section
      $this->db->where('id', $section_id);
      $this->db->delete($this->tables['type_section']);
      $query = $this->db->select('id')
        ->where('quiz_type_id', $type_id)
        ->order_by('sequence', 'ASC')
        ->get($this->tables['type_section']);
      $i = 1;
      foreach ($query->result() as $row) {
        $this->db->set('sequence', $i);
        $this->db->where('id', $row->id);
        $this->db->update($this->tables['type_section']);
        $i++;
      }
      return true;
    }
  }

  /**
   * Add new quiz type
   * @param  int  quiz id
   * @return bool
   */
  public function add_quiz_type()
  {
    $this->db->set('name', 'New Quiz Type Name');
    $this->db->set('time_limit', 0);
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    //$this->db->set('create_timestamp', time());
    $this->db->insert($this->tables['type']);
    return $this->db->insert_id();
  }

  /**
   * Publishes a quiz
   * @param  int  quiz id
   * @return bool
   */
  public function quiz_publish($id)
  {
    if ($id <= 0) {
      return FALSE;
    }
    $this->db->set('status', 1);
    $ts = time();
    $this->db->set('update_timestamp', $ts);
    $this->db->set('publish_timestamp', $ts);
    $this->db->where('id', $id);
    return $this->db->update($this->tables['quiz']);
  }

  /**
   * Archives a quiz and set it to hidden
   * @param  int  quiz ID
   * @return bool
   */
  public function quiz_archive($id)
  {
    if ($id <= 0) {
      return FALSE;
    }
    $this->db->set('status', 0);
    $this->db->set('archive_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['quiz']);
  }

  /**
   * Set up a schedule for a quiz item to go live or off-line on a future date
   * @param  int  quiz ID
   * @param  int  publish timestamp
   * @param  int  archive timestamp
   * @return bool
   */
  public function quiz_schedule($id, $scheduled_publish_timestamp, $scheduled_archive_timestamp)
  {
    if ($id <= 0) {
      return FALSE;
    }
    $this->db->set('scheduled_publish_timestamp', $scheduled_publish_timestamp);
    $this->db->set('scheduled_archive_timestamp', $scheduled_archive_timestamp);
    $this->db->where('id', $id);
    return $this->db->update($this->tables['quiz']);
  }

  /**
   * Run through all scheduled quiz, either publish or archive them
   * @return bool
   */
  public function quiz_schedule_run()
  {
    // publish items
    $this->db->set('status', 1)
      ->set('publish_timestamp', time())
      ->where('site_id', $this->site_id)
      ->where('status', 0)
      ->where('scheduled_publish_timestamp >', 0)
      ->where('scheduled_publish_timestamp <', time());
    $this->db->update($this->tables['quiz']);
    // archive items
    $this->db->set('status', 0)
      ->set('archive_timestamp', time())
      ->where('site_id', $this->site_id)
      ->where('status', 1)
      ->where('scheduled_archive_timestamp >', 0)
      ->where('scheduled_archive_timestamp <', time());
    $this->db->update($this->tables['quiz']);
  }

  /**
   * Lists all quizes from DB
   * @param  int   quiz type id
   * @param  bool  live/published only
   * @param  int  user ID
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function list_all_quiz($quiz_type_id = 0, $live_only = TRUE, $user_id = 0, $page_num = 1, $per_page = 100)
  {
    $per_page = (int) $per_page;
    $page_num = (int) $page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num - 1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    if ($quiz_type_id > 0) {
      $this->db->where('n.quiz_type_id', (int) $quiz_type_id);
    }
    if ($live_only) {
      $this->db->where('n.status', 1);
    }
    if ($user_id > 0) {
      $this->db->where('n.author_id', (int) $user_id);
    }
    if ($per_page > 0) {
      $this->db->limit($per_page, $offset);
    }
    $query = $this->db->select('n.*,u.username')
      ->join($this->tables['user'] . ' u', 'u.id=n.author_id')
      ->where('n.site_id', $this->site_id)
      ->order_by('n.update_timestamp', 'DESC')
      ->get($this->tables['quiz'] . ' n');
    return $query->result();
  }

  /**
   * Insert a new record to quiz history when a quiz is started
   * @param int quiz id
   * @param array random questions
   * @return int quiz history ID
   */
  public function history_insert($qid, $questions)
  {
    $user_id = (int) ($this->session->userdata('user_id'));
    $this->db->set('quiz_id', (int) ($qid));
    $this->db->set('user_id', $user_id);
    $this->db->set('create_timestamp', time());
    $result = $this->db->insert($this->tables['history']);
    // save to points history table
    if ($result) {
      $history_id = $this->db->insert_id();
      foreach ($questions as $q) {
        $this->db->set('quiz_history_id', $history_id);
        $this->db->set('question_id', $q->id);
        $this->db->set('correct_answer', $q->correct_answer);
        $options = array();
        for ($i = 1; $i <= 5; $i++) {
          $j = 'option_' . $i;
          if (!empty($q->$j)) {
            $options[$i] = $q->$j;
          }
        }
        $option_keys = array_keys($options);
        //if ($q->question_type == 2) {
        // shuffle the answer options for multi-choice quesitons
        // but exclude options like "All of the above" or "None of the above"
        //shuffle($option_keys);
        //}
        $ascii = 97; // starts from option_a
        while (count($option_keys) > 0 && $ascii <= 101) {
          $option_key = array_shift($option_keys);
          $field_name = 'option_' . chr($ascii);
          $this->db->set($field_name, $option_key);
          $ascii++;
        }
        $this->db->insert($this->tables['history_detail']);
      }
      return $history_id;
    }
    return $result;
  }

  /**
   * Retrieves a quiz history from DB
   * @param  int  quiz history ID,
   * @return mixed FALSE if the quiz question does not exist
   */
  public function history_get($id)
  {
    $id = (int) $id;
    if ($id == 0) {
      return FALSE;
    }
    $query = $this->db->select('*,UNIX_TIMESTAMP() AS cur_time')->where('id', $id)->get($this->tables['history']);
    $history = $query->row();
    if ($history) {
      // get the time passed in case a user refreshes the question page
      $history->time_passed = $history->cur_time - $history->create_timestamp;
      $query2 = $this->db->select('hd.*, q.section_id, q.question_type, q.required, q.question, q.option_1, q.option_2, q.option_3, q.option_4, q.option_5')
        ->join($this->tables['question'] . ' q', 'q.id=hd.question_id')
        ->where('hd.quiz_history_id', $id)->order_by('hd.id')
        ->get($this->tables['history_detail'] . ' hd');
      $history->questions = $query2->result();
    }
    return $history;
  }

  /**
   * Retrieves a list of quiz history records from DB
   * @param  int  quiz ID
   * @param  int  user ID
   * @param  bool  finished or not
   * @param  bool  timed out or not
   * @param  int  time spam in seconds
   * @return mixed
   */
  public function history_list($quiz_id, $user_id, $finished = NULL, $timed_out = NULL, $timespam = 0)
  {
    $quiz_id = (int) $quiz_id;
    $user_id = (int) $user_id;
    if ($quiz_id == 0 || $user_id == 0) {
      return FALSE;
    }
    if ($finished === TRUE) {
      $this->db->where('finish_timestamp > 0');
    }
    elseif ($finished === FALSE) {
      $this->db->where('finish_timestamp = 0');
    }
    if ($timed_out === TRUE) {
      $this->db->where('timed_out = 1');
    }
    elseif ($timed_out === FALSE) {
      $this->db->where('timed_out = 0');
    }
    if ($timespam > 0) {
      $this->db->where('UNIX_TIMESTAMP() - create_timestamp < ' . $timespam);
    }
    $query = $this->db->where('quiz_id', $quiz_id)
      ->where('user_id', $user_id)
      ->order_by('id')
      ->get($this->tables['history']);
    return $query->result();
  }

  /**
   * Save a user's answers and scores/points when a quiz is ended
   * @param int quiz history id
   * @param array data
   * @return bool
   */
  public function history_update($id, $data)
  {
    if ($id > 0 && is_array($data) && !empty($data)) {
      $result = $this->db->where('id', $id)->update($this->tables['history'], $data);
      return $result;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Save a user's single answer into quiz history detail table
   * @param int history detail id
   * @param int user_answer
   * @return bool
   */
  public function history_detail_update($id, $user_answer)
  {
    $this->db->set('user_answer', $user_answer);
    $this->db->where('id', $id);
    $result = $this->db->update($this->tables['history_detail']);
    return $result;
  }

  /**
   * return the amount of points a user has earned on a quiz
   * @param int quiz id
   * @param int user id
   * @retun int points
   */
  public function get_user_point_by_quiz_id($qid, $uid = 0)
  {
    if ($uid == 0) {
      $uid = (int) ($this->session->userdata('user_id'));
    }
    $query = $this->db->select_sum('points_earned')
      ->where('quiz_id', $qid)
      ->where('user_id', $uid)
      ->get($this->tables['history']);
    return $query->row()->points_earned;
  }
  
  /**
   * return the amount of contest entires a user has earned on a quiz
   * @param int quiz id
   * @param int user id
   * @retun int contest entries
   */
  public function get_user_contest_entries_by_quiz_id($qid, $uid = 0)
  {
    if ($uid == 0) {
      $uid = (int) ($this->session->userdata('user_id'));
    }
    $query = $this->db->select_sum('draw_entry')
      ->where('quiz_id', $qid)
      ->where('user_id', $uid)
      ->get($this->tables['history']);
    return $query->row()->draw_entry;
  }  

  /**
   * retrieves a list of user's quizzes
   *
   * @param int user_id
   *
   * @retun array of ids
   *
  public function get_user_quizzes($user_id)
  {
    $query = $this->db->distinct()
      ->select('quiz_id AS qid')
      ->where('user_id', $user_id)
      ->get($this->tables['history']);
    return $query->result();
  } */

  /**
   * Get the number of attempts a user made in the past few days
   * count in all finished quizzes but exclude ongoing ones
   * @param int quiz_id
   * @param int user_id
   * @param int days
   * @retun int
   */
  public function user_attempts($quiz_id, $user_id, $days)
  {
    $query = $this->db->select('t.time_limit')
      ->join($this->tables['quiz'] . ' q', 'q.quiz_type_id=t.id')
      ->where('q.id', $quiz_id)
      ->get($this->tables['type'] . ' t');
    $max_minutes = $query->row()->time_limit;
    $expiry_seconds = $max_minutes * 60;
    $time_span = (int)$days * 86400;
    $query = $this->db->select('id')
      ->where('quiz_id', $quiz_id)
      ->where('user_id', $user_id)
      ->where('create_timestamp > UNIX_TIMESTAMP() - ' . $time_span . ' AND (finish_timestamp > 0 OR (finish_timestamp = 0 AND create_timestamp < UNIX_TIMESTAMP() - ' . $expiry_seconds . '))')
      ->from($this->tables['history']);
    return $query->count_all_results();
  }

  /**
   * Counts all quiz results
   * @return int
   */
  public function quiz_result_count()
  {
    $count = $this->db->count_all_results($this->tables['history']);
    return $count;
  }

  /**
   * Average quiz score
   * @return int
   */
  public function quiz_result_score_avg()
  {
    $this->db->select_avg('correct_percent');
    $query = $this->db->get($this->tables['history']);
    return $query->row()->correct_percent;
  }

  /**
   * Sum quiz hours
   * @return int total time in seconds
   */
  public function quiz_result_time_sum()
  {
    $query = $this->db->select_sum('time_spent')
      ->get($this->tables['history']);
    return $query->row()->time_spent;
  }
  
  /**
   * return the highest amount of percent a user has earned on a quiz
   * @param int quiz id
   * @param int user id
   * @retun int points
   */
  public function get_user_highest_percent_by_quiz_id($qid, $uid = 0)
  {
    if ($uid == 0) {
      $uid = (int) ($this->session->userdata('user_id'));
    }
    $query = $this->db->select_max('correct_percent')
      ->where('quiz_id', $qid)
      ->where('user_id', $uid)
      ->get($this->tables['history']);
    return $query->row()->correct_percent;
  }  
  
  /**
   * return the number of finished quizzes for user
   * @param int user id
   * @retun int number of quizzes
   */
  public function get_number_of_user_quizzes($uid = 0)
  {
    if ($uid == 0) {
      $uid = (int) ($this->session->userdata('user_id'));
    }
    $query = $this->db->select('COUNT(id) as count')
      ->where('user_id', $uid)
      ->where('finish_timestamp >', 0)
      ->get($this->tables['history']);
    return $query->row();
  }    
  
  /**
   * return the number of finished quizzes for user
   * @param int user id
   * @retun int number of quizzes
   */
  public function get_quizzes_history_for_user($uid = 0)
  {
    if ($uid == 0) {
      $uid = (int) ($this->session->userdata('user_id'));
    }
    $query = $this->db->select('')
      ->where('user_id', $uid)
      ->where('finish_timestamp >', 0)
      ->get($this->tables['history']);
    return $query->result();
  }   

  /**
   * Get training-ids by quiz' target-id(s)
   * @param  array $target_ids   row ids in target table
   * @return array $training_ids row ids in training table
   */
  function get_trainings_by_targets($target_ids = array()) {
    $this->session->unset_userdata("trainings");
    if (empty($target_ids)) {
      return array();
    } elseif (is_array($target_ids) && (count($target_ids) == 1)) {
      $temp = array_shift(array_values($target_ids));
      if (empty($temp)) {
        return array();
      }
    }
    if ( !is_array($target_ids)) {
      $target_ids = array($target_ids);
    }
    $results = $this->db->select("training_id")->distinct()->where_in("target_id", $target_ids)->get($this->tables['quiz'])->result();
    $training_ids = array();
    foreach ($results as $training_id) {
      $training_ids[] = $training_id->training_id;
    }
    if ( !empty($training_ids)) {
      $this->session->set_userdata("trainings", implode(",", $training_ids));
    }
    return $training_ids;
  }
  
  /**
   * Lists all quizzes for site_id
   * @param  int  category ID
   * @param  bool  live/published only
   * @param  int  user ID
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function list_all_quizzes_for_site($site_id)
  {
    $query = $this->db->select('q.id, q.slug')
      ->where('q.site_id', $site_id)
      ->get($this->tables['quiz'] . ' q');
    return $query->result();
  }    
}

?>
