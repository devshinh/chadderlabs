<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Quote_model extends HotCMS_Model {

  private $tables;

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('quote/quote', TRUE);
    $this->tables = $this->config->item('tables', 'quote');
  }

  /**
   * Check to see if a quote slug already exists
   * @param  str   quote slug
   * @param  int   exclude quote id
   * @return bool
   */
  public function slug_exists($slug, $exclude_id = 0) {
    $query = $this->db->select('id')
            ->where('site_id', $this->site_id)
            ->where('slug', $slug);
    if ($exclude_id > 0) {
      $this->db->where('id != ', $exclude_id);
    }
    $query = $this->db->get($this->tables['quote']);
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
            ->get($this->tables['quote']);
    if ($query->num_rows > 0) {
      $result = $query->row()->slug;
    } else {
      $result = '';
    }
    return $result;
  }

  /**
   * Given a slug or ID, retrieve a quote from DB
   * @param  int  quote ID,
   * @param  str  quote slug
   * @param  bool  loads live/published quote only
   * @return mixed FALSE if the quote does not exist
   */
  public function quote_load($id = 0, $slug = '', $live_only = TRUE) {
    $id = (int) $id;
    $slug = trim($slug);
    if ($id == 0 && $slug == '') {
      return FALSE;
    }
    // load the live/published version, for the front end website
    $this->db->select()->where('site_id', $this->site_id);
    if ($id > 0) {
      $this->db->where('id', $id);
    } else {
      $this->db->where('slug', $slug);
    }
    if ($live_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->get($this->tables['quote']);
    $form = $query->row();
    $form->questions = $this->question_list($form->id);
    return $form;
  }

  /**
   * Given a slug, retrieves a quote ID
   * returns 0 if the quote does not exist
   */
  public function get_quote_id($slug) {
    $query = $this->db->select('id')
            ->where('site_id', $this->site_id)
            ->where('slug', $slug)
            ->get($this->tables['quote']);
    if ($query->num_rows()) {
      return $query->row()->id;
    } else {
      return 0;
    }
  }

  /**
   * Lists all quote from DB
   * @param  bool  live/published only
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function quote_list($live_only = TRUE, $page_num = 1, $per_page = 100) {
    $per_page = (int) $per_page;
    $page_num = (int) $page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num - 1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    if ($live_only) {
      $this->db->where('status', 1);
    }
    if ($per_page > 0 && $offset > 0) {
      $this->db->limit($per_page, $offset);
    }
    $query = $this->db->select()
            ->where('site_id', $this->site_id)
            ->order_by('sequence', 'ASC')
            ->get($this->tables['quote']);
    return $query->result();
  }

  /**
   * Counts all quote
   * @param  bool  live/published only
   * @return int
   */
  public function quote_count($live_only = TRUE) {
    if ($live_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->where('site_id', $this->site_id)
            ->get($this->tables['quote']);
    return $query->num_rows();
  }

  /**
   * Lists all questions in a quote form
   * @param  id  quote id
   * @return array of objects
   */
  public function question_list($id) {
    $id = (int) $id;
    $query = $this->db->select()
            ->where('form_id', $id)
            ->order_by('sequence', 'ASC')
            ->get($this->tables['question']);
    return $query->result();
  }

  /**
   * Insert a new record
   * @return mixed  quote ID if succeed or FALSE if failed
   */
  public function quote_insert($attr) {
    $site_id = (int) ($this->site_id);
    if ($site_id < 1) {
      return FALSE;
    }
    if (array_key_exists('slug', $attr) && $attr["slug"] > '') {
      $slug = format_url($attr["slug"]);
    }
    $ts = time();
    $this->db->set('site_id', $site_id);
    $this->db->set('slug', $slug);
    $this->db->set('status', array_key_exists('status', $attr) ? $attr['status'] : 0);
    $this->db->set('author_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('create_timestamp', $ts);
    $this->db->set('update_timestamp', $ts);
    $inserted = $this->db->insert($this->tables['quote']);
    if ($inserted) {
      $quote_id = $this->db->insert_id();
      return $quote_id;
    } else {
      return FALSE;
    }
  }

  /**
   * Update a quote
   * @param  int  quote ID
   * @param  array  quote attributes
   */
  public function quote_update($id, $attr) {
    $id = (int) $id;
    if (is_array($attr)) {
      if (array_key_exists('slug', $attr)) {
        $this->db->set('slug', $attr['slug']);
      }
      if (array_key_exists('status', $attr)) {
        $this->db->set('status', $attr['status']);
      }
    }
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['quote']);
  }

  /**
   * Retrieves a quote question from DB
   * @param  int  quote question ID,
   * @return mixed FALSE if the quote question does not exist
   */
  public function question_get($id) {
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
   * @param  int  quote id
   * @param  array  question attributes
   * @return mixed
   */
  public function question_insert($id, $attr) {
    $this->db->set('form_id', (int) $id);
    $this->db->set('section_id', (int) ($attr['section_id']));
    $this->db->set('question_type', (int) ($attr['question_type']));
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $result = $this->db->insert($this->tables['question']);
    if ($result) {
      $question_id = $this->db->insert_id();
      return $question_id;
    } else {
      return FALSE;
    }
  }

  /**
   * Updates a question
   * @param  int  quesiton id
   * @param  array  question attributes
   * @return bool
   */
  public function question_update($quesiton_id, $attr) {
    if ($quesiton_id <= 0 || empty($attr)) {
      return FALSE;
    }
    // update attributes
    $this->db->set('question', $attr['question']);
    $this->db->set('correct_answer', $attr['correct_answer']);
    $this->db->set('required', $attr['required']);
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
  public function question_delete($quesiton_id) {
    if ($quesiton_id <= 0) {
      return FALSE;
    }
    $this->db->where('id', $quesiton_id);
    return $this->db->delete($this->tables['question']);
  }

  /**
   * Deletes a quote
   * @param  int  quote ID
   * @return bool
   */
  public function delete($id) {
    $id = (int) $id;
    if ($id > 0) {
      // delete quote questions
      $this->db->where('quote_id', $id);
      $this->db->delete($this->tables['question']);
      // delete history?
      //$this->db->where( 'quote_id', $id );
      //$this->db->delete( $this->tables['history'] );
      // delete quote
      $this->db->where('id', $id);
      return $this->db->delete($this->tables['quote']);
    }
    return FALSE;
  }

  /**
   * Process quote request
   * @param  object  quote object
   * @param  array  form postback
   * @return bool
   */
  public function process_request($quote, $attr) {
    if (empty($attr) || !is_array($attr) || empty($quote) || $quote->id <= 0) {
      return FALSE;
    }
    // insert general information
    $site_id = (int) ($this->site_id);
    $ts = time();
    $this->db->set('site_id', $site_id);
    $this->db->set('form_id', $quote->id);
    $this->db->set('title', array_key_exists('title', $attr) ? $attr['title'] : '');
    $this->db->set('first_name', array_key_exists('first_name', $attr) ? $attr['first_name'] : '');
    $this->db->set('last_name', array_key_exists('last_name', $attr) ? $attr['last_name'] : '');
    $this->db->set('date_of_birth', array_key_exists('date_of_birth', $attr) ? $attr['date_of_birth'] : 0);
    $this->db->set('address_1', array_key_exists('address_1', $attr) ? $attr['address_1'] : '');
    $this->db->set('address_2', array_key_exists('address_2', $attr) ? $attr['address_2'] : '');
    $this->db->set('city', array_key_exists('city', $attr) ? $attr['city'] : '');
    $this->db->set('postal', array_key_exists('postal', $attr) ? $attr['postal'] : '');
    $this->db->set('phone', array_key_exists('phone', $attr) ? $attr['phone'] : '');
    $this->db->set('email', array_key_exists('email', $attr) ? $attr['email'] : '');
    $this->db->set('near_location_id', array_key_exists('near_location_id', $attr) ? $attr['near_location_id'] : 0);
    $this->db->set('create_timestamp', time());
    $inserted = $this->db->insert($this->tables['request']);
    if ($inserted) {
      $quote_request_id = $this->db->insert_id();
      // loop through questions
      foreach ($quote->questions as $q) {
        $field_name = 'fld_' . $q->id;
        $this->db->set('quote_request_id', $quote_request_id);
        $this->db->set('quote_question_id', $q->id);
        $this->db->set('question', $q->label);
        $this->db->set('answer', array_key_exists($field_name, $attr) ? $attr[$field_name] : '');
        $this->db->set('sequence', $q->sequence);
        $this->db->insert($this->tables['detail']);
      }
    }
    return $inserted;
  }

  /**
   * Email a quote request to the site owner
   * @param  string  email address
   * @param  object  quote object
   * @param  array  form postback
   * @return bool
   */
  public function email_request($email_address, $quote, $attr,  $insurance_type) {
    if (empty($attr) || !is_array($attr) || empty($quote) || $quote->id <= 0 || $email_address == '') {
      return FALSE;
    }

    // general information
    $subject = 'B&W Insurance Quote Submission for '. $insurance_type .' insurance';
    $message = $subject . "\n";
    $message .= 'Sent at ' . date('Y-m-d H:i') . "\n";
    $message .= array_key_exists('title', $attr) ? 'Title: ' . $attr['title'] . "\n" : '';
    $message .= array_key_exists('firstname', $attr) ? 'First Name: ' . $attr['firstname'] . "\n" : '';
    $message .= array_key_exists('lastname', $attr) ? 'Last Name: ' . $attr['lastname'] . "\n" : '';
    $message .= array_key_exists('dateofbirth', $attr) ? 'Date of Birth: ' . $attr['dateofbirth'] . "\n" : '';
    $message .= array_key_exists('address1', $attr) ? 'Address Line 1: ' . $attr['address1'] . "\n" : '';
    $message .= array_key_exists('address2', $attr) ? 'Address Line 2: ' . $attr['address2'] . "\n" : '';
    $message .= array_key_exists('city', $attr) ? 'City: ' . $attr['city'] . "\n" : '';
    $message .= array_key_exists('postal', $attr) ? 'Postal Code: ' . $attr['postal'] . "\n" : '';
    $message .= array_key_exists('phone', $attr) ? 'Phone: ' . $attr['phone'] . "\n" : '';
    $message .= array_key_exists('email', $attr) ? 'Email: ' . $attr['email'] . "\n" : '';
    $message .= array_key_exists('near_location_name', $attr) ? 'Nearest Location: ' . $attr['near_location_name'] . "\n" : '';
    $message .= "\n\n";
    
    // loop through questions
    foreach ($quote->questions as $q) {
      $field_name = 'fld_' . $q->id;
      $message .= $q->label . ': ' . (array_key_exists($field_name, $attr) ? $attr[$field_name] : '') . "\n";
    }
    
    $headers = 'From: noreply@bwinsurance.org' . "\r\n" .
            'Reply-To: noreply@bwinsurance.org' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
    return mail($email_address, $subject, $message, $headers);
  }

}

?>