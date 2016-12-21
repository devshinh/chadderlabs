<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Quiz class
 *
 * Author: jeffrey@hottomali.com
 * Created on:  06.14.2012
 */
class CmsQuiz {

  /**
   * quiz attributes
   */
  public $id = 0;
  public $quiz_type_id = 0;
  public $training_id = 0;
  public $target_id = 0;
  public $name = '';
  public $slug = '';
  public $status = 0;
  public $editor_id = 0;
  public $update_timestamp;
  public $publish_timestamp;
  public $archive_timestamp;
  public $scheduled_publish_timestamp;
  public $scheduled_archive_timestamp;
  public $questions;
  public $domain = '';
  public $site_name = '';

  private $_type;      // quiz type object
  //private $_sections;  // quiz section objects
  private $_random_questions; // array of random question objects
  private $_max_points;
  private $_max_contest_entries;

  public $user_id = 0; // a user ID for points calculation
  private $_user_points; // total points a user has earned from this quiz; the above $user_id attribute must be assigned
  private $_points_percent; // percent of points a user has earned from this quiz; the above $user_id attribute must be assigned
  private $_user_contest_entries; 
  private $_contest_entries_percent; 
  
  private $_highest_percent_score;

  /**
   * CodeIgniter global, messages and errors
   * @var string
   */
  protected $ci;
  public $messages = array();
  public $errors = array();

  /**
   * __construct
   * @param  str  item ID or slug
   * @param  bool  only load live/published item
   * @param  bool  loads item for the current domain/site only
   * @param  bool  loads quiz that targets user
   * @return void
   */
  public function __construct($identifier = NULL, $live_only = FALSE, $current_site_only = TRUE, $targeting_user = TRUE)
  {
    $this->ci = & get_instance();
    $this->ci->load->model('quiz/quiz_model');

    if (!empty($identifier)) {
      if (is_numeric($identifier)) {
        $this->id = (int)$identifier;
      }
      else {
        $this->slug = trim($identifier);
      }
      $this->load($live_only, $current_site_only, $targeting_user);
    }
  }

  /**
   * Acts as a simple way to call model methods without loads of alias
   */
  public function __call($method, $arguments)
  {
    if (!method_exists($this->ci->quiz_model, $method)) {
      throw new Exception('Undefined method CmsQuiz::' . $method . '()');
    }
    return call_user_func_array(array($this->ci->quiz_model, $method), $arguments);
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
   * Retrieves quiz type object
   * @return mixed
   */
  protected function get_type()
  {
    if ($this->id == 0 || $this->quiz_type_id == 0) {
      return FALSE;
    }
    if (!isset($this->_type)) {
      $this->_type = new CmsQuizType($this->quiz_type_id);
    }
    return $this->_type;
  }

  /**
   * Retrieves the maxiumn points a user can get from this quiz
   * it's either the Points Pre Expiry, or Points Post Expiry after the set Expiry Period
   * @return int
   */
  protected function get_max_points()
  {
    if ($this->id == 0 || $this->quiz_type_id == 0 || $this->status == 0) {
      return 0;
    }
    if (!isset($this->_max_points)) {
      if (time() > $this->publish_timestamp + $this->type->expiry_period * 86400) {
        $this->_max_points = $this->type->points_post_expiry;
      }
      else {
        $this->_max_points = $this->type->points_pre_expiry;
      }
    }
    return $this->_max_points;
  }

  /**
   * Retrieves the total points a user has earned from this quiz
   * @return int
   */
  protected function get_user_points()
  {
    if ($this->id == 0 || $this->quiz_type_id == 0 || $this->user_id == 0) {
      return 0;
    }
    if (!isset($this->_user_points)) {
      $this->_user_points = $this->ci->quiz_model->get_user_point_by_quiz_id($this->id, $this->user_id);
    }
    return $this->_user_points;
  }

  /**
   * Retrieves the percent of points a user has earned from this quiz
   * @return int
   */
  protected function get_points_percent()
  {
    if ($this->id == 0 || $this->quiz_type_id == '' || $this->user_id == 0) {
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
   * Retrieves the maximun contest entries a user can get from this quiz
   * it's either the Contest Entries Pre Expiry, or Contest Entries Post Expiry after the set Expiry Period
   * @return int
   */
  protected function get_max_contest_entries()
  {

    if ($this->id == 0 || $this->quiz_type_id == 0 || $this->status == 0) {
      return 0;
    }

    if (!isset($this->_max_contest_entries)) {

      if (time() > $this->publish_timestamp + $this->type->expiry_period * 86400) {
        $this->_max_contest_entries = $this->type->contest_entries_post_expiry;
      }
      else {
        $this->_max_contest_entries = $this->type->contest_entries_pre_expiry;
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
    if ($this->id == 0 || $this->quiz_type_id == 0 || $this->user_id == 0) {
      return 0;
    }
    if (!isset($this->_user_contest_entries)) {
      $this->_user_contest_entries = $this->ci->quiz_model->get_user_contest_entries_by_quiz_id($this->id, $this->user_id);
    }
    return $this->_user_contest_entries;
  }

  /**
   * Retrieves the percent of contest entries a user has earned from this quiz
   * @return int
   */
  protected function get_contest_entries_percent()
  {
    if ($this->id == 0 || $this->quiz_type_id == '' || $this->user_id == 0) {
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
   * Retrieves the highest percent score a user has earned from this quiz
   * @return int
   */
  protected function get_highest_percent_score()
  {
    if ($this->id == 0 || $this->quiz_type_id == '' || $this->user_id == 0) {
      return 0;
    }
    if (!isset($this->_highest_percent_score)) {
      $this->_highest_percent_score = 0;
      $this->_highest_percent_score = $this->ci->quiz_model->get_user_highest_percent_by_quiz_id($this->id, $this->user_id);
    }
    if($this->_highest_percent_score == NULL) $this->_highest_percent_score = 0;
    return $this->_highest_percent_score;
  }  
  
  /**
   * Retrieves random questions from a quiz pool
   * considering the quiz settings, and "must show" questions
   * @return array
   */
  protected function get_random_questions()
  {
    if ($this->id == 0 || $this->quiz_type_id == 0) {
      return FALSE;
    }
    if (!isset($this->_type)) {
      $this->_type = new CmsQuizType($this->quiz_type_id);
    }
    if (!isset($this->_random_questions)) {
      $this->_random_questions = array();  
      $questions = $this->questions;
      shuffle($questions);
      foreach ($this->_type->sections as $section) {
        $question_array = array();  
        $type_counter = 0;  // max: $section->questions_per_quiz;
        // add "must show" questions
        foreach ($questions as $q) {
          if ($q->question_type == $section->section_type && $q->must_show == 1) {
            $question_array[$q->id] = $q;
            $type_counter++;
          }
          if ($type_counter >= $section->questions_per_quiz) {
            shuffle($question_array);
            $this->_random_questions = array_merge($this->_random_questions, $question_array);              
            break;
          }
        }   
        // if already full then stop and process the next section
        if ($type_counter >= $section->questions_per_quiz) {
          continue;
        }
        // add other random questions till it reaches the max number of questions per quiz
        foreach ($questions as $q) {
          if ($q->question_type == $section->section_type && !array_key_exists($q->id, $question_array)) {
            $question_array[$q->id] = $q;
            $type_counter++;
          }
          if ($type_counter >= $section->questions_per_quiz) {               
            break;
          }
        }
        shuffle($question_array);
        $this->_random_questions = array_merge($this->_random_questions, $question_array);
      }
    }
    return $this->_random_questions;
  }

  /**
   * Retrieves a quiz item from database
   * @param  bool  if true, load published items only
   * @param  bool  if true, load items under the current site/domain only
   * @param  bool  loads quiz that targets user
   * @return void
   */
  public function load($live_only = TRUE, $current_site_only = TRUE, $targeting_user = TRUE)
  {
    if ($this->id < 1 && $this->slug == '') {
      return FALSE;
    }
    $row = $this->ci->quiz_model->quiz_load($this->id, $this->slug, $live_only, $current_site_only, $targeting_user);
    if ($row) {
      if (!$current_site_only && $this->id > 0) {
        $this->domain = $row->domain;
        $this->site_name = $row->site_name;
      }
      $this->id = $row->id;
      $this->slug = $row->slug;
      $this->name = $row->name;
      $this->quiz_type_id = $row->quiz_type_id;
      $this->training_id = $row->training_id;
      $this->target_id = $row->target_id;
      $this->status = $row->status;
      $this->editor_id = $row->editor_id;
      $this->update_timestamp = $row->update_timestamp;
      $this->publish_timestamp = $row->publish_timestamp;
      $this->archive_timestamp = $row->archive_timestamp;
      $this->scheduled_publish_timestamp = $row->scheduled_publish_timestamp;
      $this->scheduled_archive_timestamp = $row->scheduled_archive_timestamp;
      $this->questions = $this->ci->quiz_model->question_list($this->id);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Creates a new item
   * @param  array of quiz attributes
   * @return mixed
   */
  public function create($attr = array())
  {
    if (array_key_exists('name', $attr) && $attr["name"] > '') {
      $attr["slug"] = format_url($attr["name"]);
    }
    // check to see if there are duplicated slugs
    if ($this->ci->quiz_model->slug_exists($attr["slug"])) {
      $this->set_error('A quiz already exists with this name.');
      return FALSE;
    }
    $quiz_id = $this->ci->quiz_model->quiz_insert($attr);
    if ($quiz_id) {
      $this->id = $quiz_id;
      $this->slug = $attr["slug"];
      return $quiz_id;
    }
    else {
      $this->set_error('Failed to create a quiz.');
      return FALSE;
    }
  }

  /**
   * Saves a quiz item into database
   * @param  array of quiz attributes
   * @return bool
   */
  public function save($attr = array())
  {
    if ($this->id <= 0 || empty($attr)) {
      return FALSE;
    }
    // check to see if there are duplicated slugs
    if (array_key_exists('name', $attr) && $attr["name"] > '') {
      $attr["slug"] = format_url($attr["name"]);
    }
    if ($this->ci->quiz_model->slug_exists($attr["slug"], $this->id)) {
      $this->set_error('A quiz already exists with this title.');
      return FALSE;
    }
    $result = $this->ci->quiz_model->quiz_update($this->id, $attr);
    if ($result) {
      $this->set_message('Quiz updated successfully.');
    }
    return $result;
  }

  /**
   * Adds a new question to quiz
   * @param  array of question attributes
   * @return mixed
   */
  public function add_question($attr = array())
  {
    $question_id = $this->ci->quiz_model->question_insert($this->id, $attr);
    if ($question_id) {
      return $question_id;
    }
    else {
      $this->set_error('Failed to add a quiz question.');
      return FALSE;
    }
  }

  /**
   * Updates a question
   * @param  int  question id
   * @param  array of question attributes
   * @return mixed
   */
  public function update_question($question_id, $attr = array())
  {
    $updated = $this->ci->quiz_model->question_update($question_id, $attr);
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
   * Deletes a question
   * @param  int  question id
   * @return mixed
   */
  public function delete_question($question_id)
  {
    $updated = $this->ci->quiz_model->question_delete($question_id);
    if ($updated) {
      //$this->set_message('Quiz question has been deleted successfully.');
      return TRUE;
    }
    else {
      $this->set_error('Failed to delete the question.');
      return FALSE;
    }
  }

  /**
   * Loads a question from a quiz
   * @param  int  question id
   * @return mixed
   */
  public function load_question($question_id)
  {
    return $this->ci->quiz_model->question_get($question_id);
  }

  /**
   * Deletes an item
   * @return bool
   */
  public function delete()
  {
    return $this->ci->quiz_model->delete($this->id);
  }

  /**
   * Create a quiz history record, save quiz questions, and the starting timestamp
   * @param  int  user ID
   * @return object  quiz history
   */
  public function quiz_start($user_id)
  {
    // check to see if the user is refreshing the page after the quiz started
    // in this case load the current quiz
    $time_spam = $this->type->time_limit * 60;
    $ongoing = $this->ci->quiz_model->history_list($this->id, $user_id, FALSE, FALSE, $time_spam); // quiz history that are not finished and not timed out
    if ($ongoing && count($ongoing) > 0) {
      $row = array_pop($ongoing);
      $history_id = $row->id;
    }
    else {
      // otherwise creates a new quiz history to begin with
      $history_id = $this->ci->quiz_model->history_insert($this->id, $this->random_questions);
    }
    return $this->quiz_history($history_id);
  }

  /**
   * Save quiz answers/results into database,
   * calculate points if the user is eligible, or grant a draw entry, depending on the settings
   * @param int quiz history ID
   * @param int user ID
   * @param array user submitted answers
   * @return bool
   */
  public function quiz_finish($quiz_history_id, $user_id, $answers)
  {
    $history = $this->quiz_history($quiz_history_id);
    if (!$history || $history->id == 0 || $history->quiz_id != $this->id || $history->user_id != $user_id) {
      $this->set_error('Quiz history not found.');
      return FALSE;
    }
    if ($history->finish_timestamp > 0) {
      $this->set_error('Your answers have already been submitted.');
      return FALSE;
    }
    // check how many attempts this user had made before
    //$under_limits = $this->quiz_history_check_attempts($history->user_id);
    //if (!$under_limits) {
    //  return FALSE;
    //}
    // check if it was finished within the time limit
    $finish_timestamp = current_db_timestamp();
    $time_spent = $finish_timestamp - $history->create_timestamp;
    $timed_out = $time_spent > $this->type->time_limit * 60 + 3; // add 3 seconds for possible network latency
    //if ($timed_out) {
    //  $this->set_error('Sorry but you have gone over the ' . $this->type->time_limit . ' minute time limit.');
    //  return FALSE;
    //}
    // loop through the questions and check/save the user answers
    $correct_answers = 0;
    foreach ($history->questions as $question) {
      $form_field_name = 'quiz-' . $question->id;
      if (!array_key_exists($form_field_name, $answers)) {
        continue;
      }
      $user_answer = 0;
      switch ($question->question_type) {
        case 1: // true/false
          if ($answers[$form_field_name] == 'true') {
            $user_answer = 1;
          }
          elseif ($answers[$form_field_name] == 'false') {
            $user_answer = 2;
          }
          break;
        case 2: // multiple choice
          $user_answer = $answers[$form_field_name];
          break;
      }
      if ($question->correct_answer == $user_answer) {
        $correct_answers++;
      }
      $this->ci->quiz_model->history_detail_update($question->id, $user_answer);
    }
    $correct_percent = round($correct_answers / count($history->questions), 2) * 100;
    // TODO: determine if the user should receive anything after timed out
    // for now, earn nothing if timed out
    if ($timed_out) {
      $points_before_deduction = 0;
      $points_earned = 0;
      $draw_entry = 0;
    }//else{
    // calculate points if the user is qualified
    else {
      $points_before_deduction = round(($this->max_points * $correct_answers / count($history->questions)), 0);
      $previous_points = $this->ci->quiz_model->get_user_point_by_quiz_id($history->quiz_id, $history->user_id);
      $points_earned = $points_before_deduction - $previous_points;
      if ($points_earned > 0) {
        $this->ci->load->helper('account/account');
        $this->ci->load->config('quiz/quiz', TRUE);
        $this->tables = $this->ci->config->item('tables', 'quiz');
        $description = 'earned ' . number_format($points_earned, 0) . ' points by completing a quiz.';
        $result = account_add_points($history->user_id, $points_earned, 'quiz', $this->tables['history'], $history->id, $description);
        if (!$result) {
          $this->set_error('Sorry but there was an error when trying to grant points.');
        }
        //$draw_entry = 0;
      }
      else {
        $points_earned = 0; // should never be negative
      }    
      //calculate & add contest entries (ce)
        if($this->max_contest_entries > 0){
        $ce_before_deduction = round(($this->max_contest_entries * $correct_answers / count($history->questions)), 0);
        $ce_previous = $this->ci->quiz_model->get_user_contest_entries_by_quiz_id($history->quiz_id, $history->user_id);
        $ce_earned = $ce_before_deduction - $ce_previous;
        if ($ce_earned > 0) {
          $this->ci->load->helper('account/account');
          $this->ci->load->config('quiz/quiz', TRUE);
          $this->tables = $this->ci->config->item('tables', 'quiz');
          $description = 'earned ' . number_format($ce_earned, 0) . ' contest entries by completing a quiz.';
          $result = account_add_contest_entries($history->user_id, $ce_earned, 'quiz-draw', $this->tables['history'], $history->id, $description);
          if (!$result) {
            $this->set_error('Sorry but there was an error when trying to grant contest entires.');
          }
          $draw_entry = $ce_earned;
          //$points_before_deduction = 0;
          //$points_earned = 0;
        }
        else {
          $draw_entry = 0; // should never be negative
          $points_before_deduction = 0;
          $points_earned = 0;        
        }  
      }
        
    }
    /*
    elseif (has_permission('earn_points')) {
      //calculate & add points
      $points_before_deduction = round(($this->max_points * $correct_answers / count($history->questions)), 0);
      $previous_points = $this->ci->quiz_model->get_user_point_by_quiz_id($history->quiz_id, $history->user_id);
      $points_earned = $points_before_deduction - $previous_points;
      if ($points_earned > 0) {
        $this->ci->load->helper('account/account');
        $this->ci->load->config('quiz/quiz', TRUE);
        $this->tables = $this->ci->config->item('tables', 'quiz');
        $description = 'earned ' . number_format($points_earned, 0) . ' points by completing a quiz.';
        $result = account_add_points($history->user_id, $points_earned, 'quiz', $this->tables['history'], $history->id, $description);
        if (!$result) {
          $this->set_error('Sorry but there was an error when trying to grant points.');
        }
        $draw_entry = 0;
      }
      else {
        $points_earned = 0; // should never be negative
        $draw_entry = 0;
      }
    }elseif (has_permission('earn_draws')) {  
      //calculate & add contest entries (ce)
      $ce_before_deduction = round(($this->max_contest_entries * $correct_answers / count($history->questions)), 0);
      $ce_previous = $this->ci->quiz_model->get_user_contest_entries_by_quiz_id($history->quiz_id, $history->user_id);
      $ce_earned = $ce_before_deduction - $ce_previous;
      if ($ce_earned > 0) {
        $this->ci->load->helper('account/account');
        $this->ci->load->config('quiz/quiz', TRUE);
        $this->tables = $this->ci->config->item('tables', 'quiz');
        $description = 'earned ' . number_format($ce_earned, 0) . ' contest entries by completing a quiz.';
        $result = account_add_contest_entries($history->user_id, $ce_earned, 'quiz-draw', $this->tables['history'], $history->id, $description);
        if (!$result) {
          $this->set_error('Sorry but there was an error when trying to grant points.');
        }
        $draw_entry = $ce_earned;
        $points_before_deduction = 0;
        $points_earned = 0;
      }
      else {
        $draw_entry = 0; // should never be negative
        $points_before_deduction = 0;
        $points_earned = 0;        
      }
    }
     
     */
    $history_data = array(
      'finish_timestamp' => $finish_timestamp,
      'time_spent' => $time_spent,
      'timed_out' => ($timed_out ? 1 : 0),
      'correct_answers' => $correct_answers,
      'correct_percent' => $correct_percent,
      'points_before_deduction' => $points_before_deduction,
      'points_earned' => $points_earned,
      'draw_entry' => $draw_entry,
    );
    $result = $this->ci->quiz_model->history_update($history->id, $history_data);
    if (!$result) {
      $this->set_error('Sorry but there was an error when trying to save the quiz results.');
    }
    return $result;
  }

  /**
   * Retrieves a quiz history and its details from database
   * @param int quiz history ID
   * @return object
   */
  public function quiz_history($quiz_history_id)
  {
    if ($quiz_history_id < 1) {
      return FALSE;
    }
    return $this->ci->quiz_model->history_get($quiz_history_id);
  }

  /**
   * Checks a user's previous attempts before displaying/submitting a quiz
   * @param int user ID
   * @return bool
   */
  public function quiz_history_check_attempts($user_id)
  {
    // check user attempts against the daily limit
    if ($this->type->tries_per_day > 0) {
      $attempted = $this->ci->quiz_model->user_attempts($this->id, $user_id, 1);
      if ($attempted >= $this->type->tries_per_day) {
        $this->set_error("Unfortunately, you can only take each quiz a maximum of " . $this->type->tries_per_day . ' times per day. Come back tomorrow to do this quiz again, or alternatively <a href="/labs">try a different quiz</a>.');
        return FALSE;
      }
    }
    // check user attempts against the weekly limit
    if ($this->type->tries_per_week > 0) {
      $attempted = $this->ci->quiz_model->user_attempts($this->id, $user_id, 7);
      if ($attempted >= $this->type->tries_per_week) {
        $this->set_error("Unfortunately, you can only take each quiz a maximum of " . $this->type->tries_per_week . ' times per day. Come back tomorrow to do this quiz again, or alternatively <a href="/labs">try a different quiz</a>.');
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Lists all quiz
   * @param  array  filters, including search keyword, sorting field, and other filter criteria
   * @param  int  page number
   * @param  int  per page
   * @return mixed
   */
  public static function list_quiz($filters = FALSE, $page_num = 1, $per_page = 0)
  {
    $CI = & get_instance();
    return $CI->quiz_model->quiz_list($filters, $page_num, $per_page);
  }

  /**
   * Counts all records
   * @param  array  filters, including search keyword, sorting field, and other filter criteria
   * @return int
   */
  public static function count_all($filters = FALSE)
  {
    $CI = & get_instance();
    return $CI->quiz_model->quiz_count($filters);
  }

  /**
   * Get a random slug for demonstration purpose
   */
  public static function random_slug()
  {
    $CI = & get_instance();
    return $CI->quiz_model->get_random_slug();
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

  /**
   * Get a quiz slug from a training item/slug
   *
   * @param str product slug
   */
  public static function quiz_slugs_by_product($product_slug)
  {
    $CI = & get_instance();
    return $CI->quiz_model->get_slugs_by_product($product_slug);
  }

  /**
   * Get user list with points for quiz
   * @return object of users
   * TODO: move this function to user module
   */
  public function load_user_points_list()
  {
    $CI = & get_instance();
    $CI->load->model('user/user_model');
    $users = $CI->user_model->lists_users_by_role(6);
    // no longer need to calculate
    // points are in the user profile and synced everytime they changes
//    foreach ($users as $u) {
//      //select all quiz ids for user
//      $quizzes_id = $CI->quiz_model->get_user_quizzes($u->user_id);
//      $user_points = 0;
//      foreach($quizzes_id as $qid){
//        $user_points += $CI->quiz_model->get_user_point_by_quiz_id((int)$qid->qid,$u->user_id);
//      }
//      $u->points = $user_points;
//    }
    return $users;
  }

  /**
   * Get user list with points for quiz
   * @return object of users
   *
  public function load_user_points($username) {
    $CI = & get_instance();
    $CI->load->model('user/user_model');
    $user = $CI->user_model->get_user_by_username($username);

      //select all quiz ids for user
      $quizzes_id = $CI->quiz_model->get_user_quizzes($user->user_id);
      $user_points = 0;
      foreach ($quizzes_id as $qid) {
        $user_points += $CI->quiz_model->get_user_point_by_quiz_id((int) $qid->qid, $user->user_id);
      }

    return $user_points;
  } */
  
}

/**
 * Quiz Type class
 */
class CmsQuizType {

  /**
   * quiz type attributes
   */
  public $id = 0;
  public $name = '';
  public $time_limit = 0;
  public $expiry_period = 0;
  public $tries_per_day = 0;
  public $tries_per_week = 0;
  public $points_pre_expiry = 0;
  public $points_post_expiry = 0;
  public $contest_entries_pre_expiry = 0;
  public $contest_entries_post_expiry = 0;  
  public $sequence;
  public $editor_id;
  public $update_timestamp;
  public $icon_image_id = 0;
  public $icon_image; // image object
  public $sections;

  /**
   * CodeIgniter global, messages and errors
   * @var string
   */
  protected $ci;
  public $messages = array();
  public $errors = array();

  /**
   * __construct
   * @param  int  unique ID
   * @return void
   */
  public function __construct($id = NULL)
  {
    $this->ci = & get_instance();
    $this->ci->load->model('quiz/quiz_model');
    $id = (int) $id;
    if ($id > 0) {
      $this->load($id);
    }
  }

  /**
   * Acts as a simple way to call model methods without loads of alias
   */
  public function __call($method, $arguments)
  {
    if (!method_exists($this->ci->quiz_model, $method)) {
      throw new Exception('Undefined method CmsQuizType::' . $method . '()');
    }
    return call_user_func_array(array($this->ci->quiz_model, $method), $arguments);
  }

  /**
   * Retrieves a quiz type from database
   * @param  int  unique ID
   * @return void
   */
  public function load($id)
  {
    if ($id > 0) {
      $row = $this->ci->quiz_model->type_get($id);
      if ($row) {
        $this->id = $row->id;
        $this->name = $row->name;
        $this->time_limit = $row->time_limit;
        $this->expiry_period = $row->expiry_period;
        $this->tries_per_day = $row->tries_per_day;
        $this->tries_per_week = $row->tries_per_week;
        $this->points_pre_expiry = $row->points_pre_expiry;
        $this->points_post_expiry = $row->points_post_expiry;
        $this->contest_entries_pre_expiry = $row->contest_entries_pre_expiry;
        $this->contest_entries_post_expiry = $row->contest_entries_post_expiry;        
        $this->sequence = $row->sequence;
        $this->editor_id = $row->editor_id;
        $this->update_timestamp = $row->update_timestamp;
        $this->sections = $this->ci->quiz_model->type_list_section($id);
        $this->icon_image_id = $row->icon_image_id;

        if ($this->icon_image_id > 0) {
          $this->ci->load->helper('asset/asset');
          $this->icon_image = asset_load_item($this->icon_image_id);
        }
      }
    }
  }

  /**
   * Updates a quiz type
   * @param  array of quiz type attributes
   * @return bool
   */
  public function update($attr = array())
  {
    if ($this->id <= 0 || empty($attr)) {
      return FALSE;
    }
    return $this->ci->quiz_model->type_update($this->id, $attr);
  }

  /**
   * Adds a section to quiz type
   * @param  int  section type
   * @param  int  question pool
   * @param  int  questions per quiz
   * @return bool
   */
  public function add_section($section_type, $question_pool, $questions_per_quiz)
  {
    if ($this->id < 1) {
      return FALSE;
    }
    return $this->ci->quiz_model->type_add_section($this->id, $section_type, $question_pool, $questions_per_quiz);
  }

  /**
   * Updates a section in quiz type
   * @param  int  section id
   * @param  array  attributes
   * @return bool
   */
  public function update_section($section_id, $attr)
  {
    if ($section_id <= 0 || !is_array($attr)) {
      return FALSE;
    }
    return $this->ci->quiz_model->type_update_section($section_id, $attr);
  }

  /**
   * Removes a  quiz type
   * @param  int  quiz type ID
   * @return bool
   */
  public function delete_section($id, $section_id)
  {
    if ($this->id < 1 || $section_id < 1) {
      return FALSE;
    }
    return $this->ci->quiz_model->type_delete_section($this->id, $section_id);
  }

  /**
   * Lists quiz types
   * @return mixed
   */
  public static function list_type()
  {
    $CI = & get_instance();
    return $CI->quiz_model->quiz_type_list();
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
