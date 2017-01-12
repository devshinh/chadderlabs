<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dashboard Analysis Object
 *
 * @package		HotCMS
 * @author		Tao Long
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.2013.10.23
 */
class Analysis {
  /**
   * Eligible choices for an user to choose a filter.
   */
  private $eligibles = array('first_date', "filter_date", 'countries', 'retailers', 'states', 'cities', 'stores');
  /**
   * The date is either when the Cheddar Labs first go online or the subdomain first goes live online.
   * This should only be set by the system because it is a constant for the whole site.
   * @var string English formatted date
   */
  private $first_date = '';
  
  /**
   * The date an user filters.
   * This should only be set by the system because it reflects the present time which is staic.
   * @var string English formatted date
   */
  private $filter_date = '';
  
  /**
   * Eligible countries, in which either the Chaddar Labs or a subdomain are operating, to choose.
   * Default has only an option "all" => "All";
   * @var array whose key is country code and value is country name
   */
  private $countries = array();
  
  /**
   * Eligible retailers, which operates in the chosen country, to choose.
   * Default has only an option "all" => "All";
   * @var array whose key is retailer id and value is retailer name
   */
  private $retailers = array();
  
  /**
   * Eligible states, which has at least a store of the chosen retailer, to choose.
   * Default has only an option "all" => "All";
   * @var array whose key is state code and value is state name
   */
  private $states = array();
  
  /**
   * Eligible cities, which are in the chosen state, to choose.
   * Default has only an option "all" => "All";
   * @var array whose key and value both are city name
   */
  private $cities = array();
  
  /**
   * Eligible stores, which are in the chosen city, to choose.
   * Default has only an option "all" => "All";
   * @var array whose key and value both are city name
   */
  private $stores = array();
  
  /**
   * Chosen parameters to filter.
   * @var array list of filter names
   */
  private $filters = array('from_date', 'to_date', 'country', 'retailer', 'state', 'city', 'store');
  /**
   * The start date of range of store created dates to filtering.
   * Leave empty if user wants start from the first date this subdomain goes live.
   * @var string english formatted date
   */
  private $from_date = '';
  
  /**
   * The end date of range of store created dates to filtering.
   * Leave empty if user wants to be the date he/she filters.
   * @var string english formatted date
   */
  private $to_date = '';
  
  /**
   * A country to filtering.
   * Leave empty if user wants all countries.
   * @var string 2-letters country code
   */
  private $country = '';
  
  /**
   * A retailer to filtering.
   * Leave empty if user wants all retailes operating in the chosen country.
   * @var string id of retailer
   */
  private $retailer = '';
  
  /**
   * A state to filtering.
   * Leave empty if user wants all states the chosen retailer operates in.
   * @var string 2-letters state code
   */
  private $state = '';
  
  /**
   * A city to filtering.
   * Leave empty if user wants all cities the chosen state includes.
   * @var string name of city
   */
  private $city = '';
  
  /**
   * A store to filtering.
   * Leave empty if user wants all stores the chosen cities includes.
   * @var string id of store
   */
  private $store = '';

  /**
   * Collection of latest error messages.
   * @var array whose key is where error occur, and value is the error message
   */
  private $errors = array();

  /**
   * Results after filtering.
   */
  private $results = array('brand', 'total_retailers', 'active_locations', 'pending_locations', 'registered_members', 'active_labs', 'lab_hours', 'active_quizzes', 'quizzes_completed', 'training_hours', 'quiz_time', 'quiz_score', 'quiz_points', 'cheddar_points_awarded', 'points_redeemed', 'store_orders', 'contest_entries_awarded', 'referrals_send', 'referral_converson', 'badges_awarded', 'member_sessions', 'quizzes_per_month');
  /**
   * Name of the brand of current site
   * @var string name of brand/subdoamin/site_id
   */
  private $brand = '';
  
  /**
   * Total number of retailers are filtered by above parameters.
   * @var int total
   */
  private $total_retailers = 0;
  
  /**
   * Total number of active locations are filtered by above parameters.
   * @var int total
   */
  private $active_locations = 0;
  
  /**
   * Total number of pending locations are filtered by above parameters.
   * @var int total
   */
  private $pending_locations = 0;
  
  /**
   * Total number of regestered members are filtered by above parameters.
   * @var int total
   */
  private $registered_members = 0;
  
  /**
   * Total number of actived labs are filtered by above parameters.
   * @var int total
   */
  private $active_labs = 0;
  
  /**
   * Total number of hours each traing pageis are filtered by above parameters.
   * @var int total
   */
  private $lab_hours = 0;
  
  /**
   * Total number of actived quizzes are filtered by above parameters.
   * @var int total
   */
  private $active_quizzes = 0;
  
  /**
   * Total number of quizzes completed are filtered by above parameters.
   * @var int total
   */
  private $quizzes_completed = 0;
  
  /**
   * Total number of training hours are filtered by above parameters.
   * @var int total
   */
  private $training_hours = 0;
  
  /**
   * Average time spent on a quiz are filtered by above parameters.
   * @var string seconds
   */
  private $quiz_time = '';
  
  /**
   * Average percentage of quiz score are filtered by above parameters.
   * @var string percentage
   */
  private $quiz_score = '';
  
  /**
   * Average points of quiz earned are filtered by above parameters.
   * @var string percentage
   */
  private $quiz_points = '';
  
  /**
   * Total number of cheddar points awarded are filtered by above parameters.
   * @var int total
   */
  private $cheddar_points_awarded = 0;
  
  /**
   * Total number of cheddar points redeemed are filtered by above parameters.
   * @var int total
   */
  private $points_redeemed = 0;
  
  
  /**
   * Total number of store orders awarded are filtered by above parameters.
   * @var int total
   */
  private $store_orders = 0;
  /**
   * Total number of contest entries awarded are filtered by above parameters.
   * @var int total
   */
  private $contest_entries_awarded = 0;
  
  /**
   * Total number of referrals send are filtered by above parameters.
   * @var int total
   */
  private $referrals_send = 0;
  
  /**
   * Percentage of referral conversion are filtered by above parameters.
   * @var float percentage of referral conversion
   */
  private $referral_converson = 0.0;

  /**
   * Total number of badges awared are filtered by above parameters.
   * @var int number of badges
   */
  private $badges_awarded = 0;
  
  /**
   * Total number of member logged in are filtered by above parameters.
   * @var int number of sessions
   */
  private $member_sessions = 0;


  /**
   * A list of monthly number of taken quizzes.
   * @var string json encoded
   */
  private $quizzes_per_month = "";
  
  /**
   * Private properties to help class functions.
   */
  /**
   * To access other classes.
   * @var object an instance of CodeIgniter
   */
  private $ci = NULL;

  /**
   * When site id is not 1, it means user is in a subdomain.
   * @var int site id
   */
  private $site_id = 0;
  
  /**
   * Class contructor sets eligible filtering options, chosen filters, and filtered results.
   * @param array $new_filters preset filter
   */
  public function __construct($new_filters = array()) {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->set_all($new_filters);
  }
  
  /**
   * Class getter for getting a private property.
   * Trigger system errror for getting undefined property.
   * @param  string $name of the property to get
   * @return mix    the value of named property if exists, otherwise NULL
   */
  public function __get($name) {
    if (in_array($name, $this->eligibles) OR in_array($name, $this->filters) OR in_array($name, $this->results) OR (strcasecmp($name, "errors") === 0)) {
      return $this->$name;
    }
    
    $trace = debug_backtrace();
    trigger_error('Undefined property via __get(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);
    return NULL;
  }
  
  /**
   * Class setter for setting a private property.
   * Trigger system errror for setting undefined property.
   * @param  string $name of the property to set
   * @param  mix    $value to be set to the named property
   * @return mix    nothing if success, otherwise NULL
   */
  public function __set($name, $value) {
    if (in_array($name, $this->eligibles) OR in_array($name, $this->filters) OR in_array($name, $this->results)) {
      $this->update(array("new_".$name => $value));
      return;
    }
    
    $trace = debug_backtrace();
    trigger_error('Undefined property via __get(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);
    return NULL;
  }

  /**
   * Updates eligible filtering options, chosen filters, and filtered results.
   * @param  mix    $new_from_date
   * @param  string $new_to_date
   * @param  string $new_country
   * @param  string $new_retailer
   * @param  string $new_state
   * @param  string $new_city
   */
  public function set_all($new_from_date = '', $new_to_date = '', $new_country = '', $new_retailer = '', $new_state = '', $new_city = '', $new_store = '', $new_first_date = '', $new_site_id = '') {
    if (( !empty($new_from_date)) && is_array($new_from_date)) {
      $temp = $new_from_date;
      $new_from_date = '';
      foreach(array_merge($this->filters, array('first_date', 'site_id')) as $filter_name) {
        if (array_key_exists($filter_name, $temp)) {
          ${'new_' . $filter_name} = $temp[$filter_name];
        }
        if (array_key_exists('new_' . $filter_name, $temp)) {
          ${'new_' . $filter_name} = $temp[('new_' + $filter_name)];
        }
      }
    }
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->errors = array();
    $this->ci->load->config("dashboard");
    $this->site_id = $new_site_id;
    $this->set_first_date($new_first_date);
    $this->set_from_date($new_from_date);
    $this->set_filter_date();
    $this->set_to_date($new_to_date);
    $this->set_countries();
    $this->set_country($new_country);
    $this->set_retailers();
    $this->set_retailer($new_retailer);
    $this->set_states();
    $this->set_state($new_state);
    $this->set_cities();
    $this->set_city($new_city);
    $this->set_stores();
    $this->set_store($new_store);
    $this->set_results();
  }
  
  /**
   * Set the first eligible date of the range of dates to be filtered.
   * @param string $first_date
   */
  public function set_first_date($first_date) {
    if (( !empty($this->errors['eligibles'])) && array_key_exists('first_date', $this->errors['eligibles'])) {
      unset($this->errors['eligibles']['first_date']);
    }
    if (strtotime($first_date) != FALSE) {
      if (strtotime($first_date) < strtotime("now")) {
        $this->first_date = $first_date;
      } else {
        $this->errors['eligibles']['first_date'] = "The first eligible date must before the current date.";
      }
    } else {
      $this->errors['eligibles']['first_date'] ="The first eligible date must be a valid date format.";
    }
  }

  /**
   * Set the date user filters.
   */
  public function set_filter_date() {
    if (( !empty($this->errors['eligibles'])) && array_key_exists('filter_date', $this->errors['eligibles'])) {
      unset($this->errors['eligibles']['filter_date']);
    }
    $this->filter_date = date("m/d/Y", time()+86400);
  }

  /**
   * Public function to validate and set from_date.
   * @param string  $new_from_date
   */
  public function set_from_date($new_from_date = '') {
    if (( !empty($this->errors['filters'])) && array_key_exists('from_date', $this->errors['filters'])) {
      unset($this->errors['filters']['from_date']);
    }
    if (is_string($new_from_date)) {
      $new_from_date = trim($new_from_date);
      if (empty($new_from_date)) {
        $this->from_date = '';
      } elseif (strtotime($new_from_date) !== FALSE) {
        if (( !empty($this->first_date)) && (strtotime($new_from_date) < strtotime($this->first_date))) {
          $this->errors['filters']['from_date'] = "The From Date can't be before the first date this website goes online.";
        } elseif (( !empty($this->to_date)) && (strtotime($new_from_date) > strtotime($this->to_date))) {
          $this->errors['filters']['from_date'] = "The From Date can't be after the To Date of the same range.";
        } elseif (( !empty($this->filter_date)) && (strtotime($new_from_date) > strtotime($this->filter_date))) {
          $this->errors['filters']['from_date'] = "The From Date can't be after the time you just submit filter..";
        } else {
          $this->from_date = $new_from_date;
        }
      } else {
        $this->errors['filters']['from_date'] = 'Wrong format for the From Date';
      }
    } else {
      $this->errors['filters']['from_date'] = 'Wrong data-type for the From Date';
    }
  }

  /**
   * Public function to validate and set to_date.
   * @param string  $new_to_date
   * @param boolean $wait_to_set, wait for setting other before setting eligibles
   */
  public function set_to_date($new_to_date = '', $wait_to_set = TRUE) {
    if (( !empty($this->errors['filters'])) && array_key_exists('to_date', $this->errors['filters'])) {
      unset($this->errors['filters']['to_date']);
    }
    if (is_string($new_to_date)) {
      $new_to_date = trim($new_to_date);
      if (empty($new_to_date)) {
        $this->to_date = '';
      } elseif (strtotime($new_to_date) !== FALSE) {
        if (( !empty($this->filter_date)) && (strtotime($new_to_date) > strtotime($this->filter_date))) {
          $this->errors['filters']['to_date'] = "The To Date can't be after the time you just update filter.";
        } elseif (( !empty($this->from_date)) && (strtotime($new_to_date) < strtotime($this->from_date))) {
          $this->errors['filters']['to_date'] = "The To Date can't be after the From Date of the same range.";
        } elseif (( !empty($this->first_date)) && (strtotime($new_to_date) < strtotime($this->first_date))) {
          $this->errors['filters']['to_date'] = "The from date can't be before the first date this website goes online.";
        } else {
          $this->to_date = $new_to_date;
        }
      } else {
        $this->errors['filters']['to_date'] = 'Wrong format for the To Date';
      }
    } else {
      $this->errors['filters']['to_date'] = 'Wrong data-type for the To Date';
    }
    if ( !$wait_to_set && !isset($this->errors['filters']['to_date'])) {
      $this->set_eligibles(FALSE);
    }
  }

  /**
   * Set eligible countries to choose for filter.
   */
  public function set_countries() {
    if (( !empty($this->errors['eligibles'])) && array_key_exists('countries', $this->errors['eligibles'])) {
      unset($this->errors['eligibles']['countries']);
    }
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select('c.*');
    $this->ci->db->distinct();
    $this->ci->db->from('country c');
    $this->ci->db->join('retailer_store s', 's.country_code = c.country_code');
//    if (empty($this->from_date)) {
//      $this->ci->db->where("s.create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("s.create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("s.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("s.create_timestamp <=", strtotime($this->to_date));
    }
    $this->ci->db->order_by("c.country", "asc");
    $query = $this->ci->db->get();
    $this->countries = array();
    if ($query->num_rows() == 0) {
      $this->errors['eligibles']['countries'] = 'No country is eligible to filter.';
    } else {
      $rows = $query->result_array();
      foreach ($rows as $row) {
        $this->countries[$row['country_code']] = $row['country'];
      }
    }
  }

  /**
   * Public function to validate set country.
   * @param string  $new_country
   */
  public function set_country($new_country = '') {
    if (( !empty($this->errors['filters'])) && array_key_exists('country', $this->errors['filters'])) {
      unset($this->errors['filters']['country']);
    }
    if (is_string($new_country)) {
      $new_country = trim($new_country);
      if (empty($new_country) OR (strcasecmp($new_country, "all") === 0)) {
        $this->country = '';
      } elseif( !empty ($this->countries)) {
        if (in_array($new_country, array_keys($this->countries))) {
          $this->country = $new_country;
        } else {
          $this->errors['filters']['country'] = "The chosen country is not an eligible filtering option.";
        }
      } else {
        $this->country = $new_country;
      }
    } else {
      $this->errors['filters']['country'] = 'Wrong data-type for the Country';
    }
  }
  
  /**
   * Set eligible retailers to choose for filter.
   */
  public function set_retailers() {
    if (( !empty($this->errors['eligibles'])) && array_key_exists('retailers', $this->errors['eligibles'])) {
      unset($this->errors['eligibles']['retailers']);
    }
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select('r.*');
    $this->ci->db->distinct();
    $this->ci->db->from('retailer r');
//    if (empty($this->from_date)) {
//      $this->ci->db->where("r.create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("r.create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("r.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("r.create_timestamp <=", strtotime($this->to_date));
    }
    if ( !empty ($this->country)) {
      $this->ci->db->join("country c", "c.country_code = r.country_code");
      $this->ci->db->where("c.country_code", $this->country);
    } elseif ( !empty ($this->countries)){
      $this->ci->db->join("country c", "c.country_code = r.country_code");
      $this->ci->db->where_in("c.country_code", array_keys($this->countries));
    }
    $this->ci->db->order_by("name", "asc");
    $query = $this->ci->db->get();
    $this->retailers = array();
    if ($query->num_rows() == 0) {
      $this->errors['eligibles']['retailers'] = 'No retailer is eligible to filter.';
    } else {
      $rows = $query->result_array();
      foreach ($rows as $row) {
        $this->retailers[$row['id']] = $row['name'];
      }
    }
  }

  /**
   * Public function to validate set retailer.
   * @param string  $new_retailer
   * @param boolean $wait_to_set, wait for setting other before setting eligibles
   */
  public function set_retailer($new_retailer = '', $wait_to_set = TRUE) {
    if (( !empty($this->errors['filters'])) && array_key_exists('retailer', $this->errors['filters'])) {
      unset($this->errors['filters']['retailer']);
    }
    if (is_string($new_retailer) OR is_numeric($new_retailer)) {
      $new_retailer = trim($new_retailer);
      if (empty($new_retailer) OR (strcasecmp($new_retailer, "all") === 0)) {
        $this->retailer = '';
      } elseif ( !empty($this->retailers)) {
        if (in_array($new_retailer, $this->retailers)) {
          $this->retailer = $new_retailer;
        } else {
          $this->errors['filters']['retailer'] = "The chosen retailer is not an eligible filtering option.";
        }
      } else {
        $this->retailer = $new_retailer;
      }
    } else {
      $this->errors['filters']['retailer'] = 'Wrong data-type for the Country';
    }
    if ( !$wait_to_set && !isset($this->errors['filters']['retailer'])) {
      $this->set_eligibles(FALSE);
    }
  }
  
  /**
   * Set eligible provinces/states to choose for filter.
   */
  public function set_states() {
    if (( !empty($this->errors['eligibles'])) && array_key_exists('states', $this->errors['eligibles'])) {
      unset($this->errors['eligibles']['states']);
    }
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->distinct();
    $this->ci->db->from("province p");
    $this->ci->db->join("retailer_store s", "s.province = p.province_code");
    if ( !empty($this->retailer)) {
      $this->ci->db->where("s.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("s.retailer_id", array_keys($this->retailers));
    }
    if ( !empty ($this->country)) {
      $this->ci->db->join("country c", "c.country_code = p.country_code");
      $this->ci->db->where("c.country_code", $this->country);
    } elseif ( !empty ($this->countries)){
      $this->ci->db->join("country c", "c.country_code = p.country_code");
      $this->ci->db->where_in("c.country_code", array_keys($this->countries));
    }
//    if (empty($this->from_date)) {
//      $this->ci->db->where("s.create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("s.create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("s.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("s.create_timestamp <=", strtotime($this->to_date));
    }
    $this->ci->db->order_by("province_name", "asc");
    $query = $this->ci->db->get();
    $this->states = array();
    if ($query->num_rows() == 0) {
      $this->errors['eligibles']['states'] = 'No province/state is eligible to filter.';
    } else {
      $rows = $query->result_array();
      foreach ($rows as $row) {
        $this->states[$row['province_code']] = $row['province_name'];
      }
    }
  }

  /**
   * Public function to validate set state.
   * @param string  $new_state
   */
  public function set_state($new_state = '') {
    if (( !empty($this->errors['filters'])) && array_key_exists('state', $this->errors['filters'])) {
      unset($this->errors['filters']['state']);
    }
    if (is_string($new_state)) {
      $new_state = trim($new_state);
      if (empty($new_state) OR (strcasecmp($new_state, "all") === 0)) {
        $this->state = '';
      } elseif ( !empty ($this->states)) {
        if (in_array($new_state, array_keys($this->states))) {
          $this->state = $new_state;
        } else {
          $this->errors['filters']['state'] = "The chosen state is not an eligible filter option.";
        }
      } else {
        $this->state = $new_state;
      }
    } else {
      $this->errors['filters']['state'] = 'Wrong data-type for the Country';
    }
  }
  
  /**
   * Set eligible cities to choose for filter.
   */
  public function set_cities() {
    if (( !empty($this->errors['eligibles'])) && array_key_exists('cities', $this->errors['eligibles'])) {
      unset($this->errors['eligibles']['cities']);
    }
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select("city");
    $this->ci->db->distinct();
    if ( !empty($this->state)) {
      $this->ci->db->where('province', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('province', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("country_code", array_keys($this->countries));
    }
//    if (empty($this->from_date)) {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("create_timestamp <=", strtotime($this->to_date));
    }
    $this->ci->db->order_by("city", "asc");
    $query = $this->ci->db->get("retailer_store");
    $this->cities = array();
    if ($query->num_rows() == 0) {
      $this->errors['eligibles']['cities'] = 'No city is eligible to filter.';
    } else {
      $rows = $query->result_array();
      foreach ($rows as $row) {
        $city_underscore = str_replace(' ', '_', trim($row['city']));
        $this->cities[$city_underscore] = $row['city'];
      }
    }
  }

  /**
   * Public function to validate set city.
   * @param string  $new_city
   * @param boolean $wait_to_set, wait for setting other before setting eligibles
   */
  public function set_city($new_city = '', $wait_to_set = TRUE) {
    if (( !empty($this->errors['filters'])) && array_key_exists('city', $this->errors['filters'])) {
      unset($this->errors['filters']['city']);
    }
    if (is_string($new_city)) {
      $new_city = trim($new_city);
      if (empty($new_city) OR (strcasecmp($new_city, "all") === 0)) {
        $this->city = '';
      } elseif ( !empty($this->cities)) {
        $new_city = str_replace('_', ' ', $new_city); // The value attribute of <option> can not has spaces.
        if (in_array($new_city, $this->cities)) {
          $this->city = $new_city;
        } else {
          $this->errors['filters']['city'] = "The chosen city is not an eligible filter option.";
        }
      } else {
        $this->city = $new_city;
      }
    } else {
      $this->errors['filters']['city'] = 'Wrong data-type for the Country';
    }
    if ( !$wait_to_set && !isset($this->errors['filters']['city'])) {
      $this->set_eligibles(FALSE);
    }
  }
  
  /**
   * Set eligible stores to choose for filter.
   */
  public function set_stores() {
    if (( !empty($this->errors['eligibles'])) && array_key_exists('stores', $this->errors['eligibles'])) {
      unset($this->errors['eligibles']['stores']);
    }
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->distinct();
    if ( !empty($this->city)) {
      $this->ci->db->where('city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('province', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('province', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("country_code", array_keys($this->countries));
    }
//    if (empty($this->from_date)) {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("create_timestamp <=", strtotime($this->to_date));
    }
    $this->ci->db->order_by("store_name", "asc");
    $query = $this->ci->db->get("retailer_store");
    $this->stores = array();
    if ($query->num_rows() == 0) {
      $this->errors['eligibles']['stores'] = 'No store is eligible to filter.';
    } else {
      $rows = $query->result_array();
      foreach ($rows as $row) {
        $this->stores[$row['id']] = $row['store_name'];
      }
    }
  }

  /**
   * Public function to validate set store
   * @param string  $new_store
   */
  public function set_store($new_store = '') {
    if (( !empty($this->errors['filters'])) && array_key_exists('store', $this->errors['filters'])) {
      unset($this->errors['filters']['store']);
    }
    if (is_string($new_store) OR is_numeric($new_store)) {
      $new_store = trim($new_store);
      if (empty($new_store) OR (strcasecmp($new_store, "all") === 0)) {
        $this->store = '';
      } elseif ( !empty ($this->states)) {
        if (in_array($new_store, array_keys($this->stores))) {
          $this->store = $new_store;
        } else {
          $this->errors['filters']['store'] = "The chosen store is not an eligible filter option.";
        }
      } else {
        $this->store = $new_store;
      }
    } else {
      $this->errors['filters']['store'] = 'Wrong data-type for the Country';
    }
  }

  /**
   * Set all filtered results at once.
   */
  public function set_results() {
    $this->set_brand();
    $this->set_total_retailers();
    $this->set_active_locations();
    $this->set_pending_locations();
    $this->set_registered_members();
    $this->set_active_labs();
    $this->set_lab_hours();
    $this->set_active_quizzes();
    $this->set_training_hours();
    $this->set_quizzes_completed();
    $this->set_quiz_time();
    $this->set_quiz_score();
    $this->set_quiz_points();
    $this->set_cheddar_points_awarded();
    $this->set_points_redeemed();
    $this->set_store_orders();
    $this->set_contest_entries_awarded();
    $this->set_referrals_send();
    $this->set_referral_conversion();
    $this->set_badges_awarded();
    $this->set_member_sessions();
    $this->set_quizzes_per_month();
    if (array_key_exists('results', $this->errors) && empty($this->errors['results'])) {
      unset($this->errors['results']);
    }
  }

  /**
   * Set the name of the current subdomain
   */
  public function set_brand() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    if ($this->site_id > 1) {
      $query = $this->ci->db->get_where('site', array('id' => $this->site_id));
      $this->brand = $query->row()->name;
      if (empty($this->brand)) {
        $this->brand = '';
      }
    } else {
      $this->brand = $this->ci->db->count_all("site");
    }
  }
  
  /**
   * Set the number of total retilers base on current filters and eligibles.
   */
  public function set_total_retailers() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select("retailer_id");
    $this->ci->db->distinct();
    if ( !empty($this->store)) {
      $this->ci->db->where('id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('province', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('province', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("country_code", array_keys($this->countries));
    }
//    if (empty($this->from_date)) {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("create_timestamp <=", strtotime($this->to_date));
    }
    $query = $this->ci->db->get_where("retailer_store", array("status" => 1));
    $this->total_retailers = $query->num_rows();
  }

  /**
   * Set the number of total active locations base on current filters and eligibles.
   */
  public function set_active_locations() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    if ( !empty($this->store)) {
      $this->ci->db->where('id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('province', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('province', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("country_code", array_keys($this->countries));
    }
//    if (empty($this->from_date)) {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("create_timestamp <=", strtotime($this->to_date));
    }
    $query = $this->ci->db->get_where("retailer_store", array("status" => 1));
    $this->active_locations = $query->num_rows();
  }

  /**
   * Set the number of total pending locations base on current filters and eligibles.
   */
  public function set_pending_locations() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    if ( !empty($this->store)) {
      $this->ci->db->where('id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('province', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('province', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("country_code", array_keys($this->countries));
    }
//    if (empty($this->from_date)) {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("create_timestamp <=", strtotime($this->to_date));
    }
    $query = $this->ci->db->get_where("retailer_store", array("status" => 0));
    $this->pending_locations = $query->num_rows();
  }

  /**
   * Set the number of total registered members base on current filters and eligibles.
   */
  public function set_registered_members() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select("u.id");
    $this->ci->db->distinct();
    $this->ci->db->from("user u");
    $this->ci->db->join("user_profile p", "p.user_id = u.id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = u.id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
//    if (empty($this->from_date)) {
//      $this->ci->db->where("u.created_on >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("u.created_on >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("u.created_on <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("u.created_on <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("u.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->registered_members = $query->num_rows();
  }
  
  /**
   * Set the total number of active labs base on current filters and eligibles.
   */
  public function set_active_labs() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select('t.*');
    $this->ci->db->distinct();
    $this->ci->db->from("training t");
    $this->ci->db->join("quiz q", "q.training_id = t.id");
    $this->ci->db->join("quiz_history h", "h.quiz_id = q.id");
    $this->ci->db->join("user_profile p", "p.user_id = h.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->where("t.status", 1);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
//    if (empty($this->from_date)) {
//      $this->ci->db->where("t.create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("t.create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("t.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("t.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("t.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->active_labs = $query->num_rows();
  }
  
  public function set_lab_hours() {
    // To-do: pull data from google analystic
    $this->lab_hours = -1;
  }
  
  /**
   * Set the total number of active quizzes base on current filters and eligibles.
   */
  public function set_active_quizzes() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select("q.*");
    $this->ci->db->distinct();
    $this->ci->db->from("quiz q");
    $this->ci->db->join("quiz_history h", "h.quiz_id = q.id");
    $this->ci->db->join("user_profile p", "p.user_id = h.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->where("q.status", 1);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
//    if (empty($this->from_date)) {
//      $this->ci->db->where("t.create_timestamp >=", strtotime($this->first_date));
//    } else {
//      $this->ci->db->where("t.create_timestamp >=", strtotime($this->from_date));
//    }
    if (empty($this->to_date)) {
      $this->ci->db->where("q.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("q.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("q.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->active_quizzes = $query->num_rows();
  }
  
  /**
   * Set the total number of completed quizzes base on current filters and eligibles. 
   */
  public function set_quizzes_completed() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select('h.*');
    $this->ci->db->distinct();
    $this->ci->db->from("quiz_history h");
    $this->ci->db->join("user_profile p", "p.user_id = h.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = h.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->join("quiz q", "q.id = h.quiz_id");
      $this->ci->db->where("q.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->quizzes_completed = $query->num_rows();
    if (empty($this->from_date)) {
      $this->quizzes_completed += 210000;
    }
  }
  
  /**
   * Set the total number of training hours base on current filters and eligibles. 
   */
  public function set_training_hours() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select_sum('h.time_spent', "time_spent");
    $this->ci->db->from("quiz_history h");
    $this->ci->db->join("user_profile p", "p.user_id = h.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = h.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->join("quiz q", "q.id = h.quiz_id");
      $this->ci->db->where("q.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->training_hours = $query->row()->time_spent / 3600;
  }
  
  /**
   * Set the average time spent on a quiz base on current filters and eligibles. 
   */
  public function set_quiz_time() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select_avg('h.time_spent', "time_spent");
    $this->ci->db->from("quiz_history h");
    $this->ci->db->join("user_profile p", "p.user_id = h.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = h.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->join("quiz q", "q.id = h.quiz_id");
      $this->ci->db->where("q.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->quiz_time = $query->row()->time_spent;
  }
  
  /**
   * Set the average percentage of quiz score base on current filters and eligibles. 
   */
  public function set_quiz_score() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select_avg('h.correct_percent', "correct_percent");
    $this->ci->db->from("quiz_history h");
    $this->ci->db->join("user_profile p", "p.user_id = h.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->join("quiz q", "q.id = h.quiz_id");
      $this->ci->db->where("q.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->quiz_score = $query->row()->correct_percent;
  }
  
  /**
   * Set the average of quiz points eanred base on current filters and eligibles. 
   */
  public function set_quiz_points() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select_avg('h.points_earned', "points_earned");
    $this->ci->db->from("quiz_history h");
    $this->ci->db->join("user_profile p", "p.user_id = h.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = h.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->join("quiz q", "q.id = h.quiz_id");
      $this->ci->db->where("q.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->quiz_points = $query->row()->points_earned;
  }
  
  /**
   * Set the total number of points awarded base on current filters and eligibles. 
   */
  public function set_cheddar_points_awarded() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select_sum("up.points", "points");
    $this->ci->db->from("user_points up");
    $this->ci->db->join("user_profile p", "p.user_id = up.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = up.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("up.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("up.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("up.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("up.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("up.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->cheddar_points_awarded = $query->row()->points;
  }
  
  /**
   * Set the total number of points redeemed base on current filters and eligibles. 
   */
  public function set_points_redeemed() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select_sum("up.points", "points_redeemed_in_store");
    $this->ci->db->from("user_points up");
    $this->ci->db->join("user_profile p", "p.user_id = up.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = up.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    $this->ci->db->where("up.ref_table", "order");
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("up.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("up.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("up.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("up.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("up.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->points_redeemed = $query->row()->points_redeemed_in_store;
  }
  
  /**
   * Set the total number of contest entries awarded base on current filters and eligibles. 
   */
  public function set_contest_entries_awarded() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select_sum("d.draws", "draws");
    $this->ci->db->from("user_draws d");
    $this->ci->db->join("user_profile p", "p.user_id = d.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = d.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("d.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("d.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("d.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("d.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("d.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->contest_entries_awarded = $query->row()->draws;
  }
  
  /**
   * Set the total number of store orders base on current filters and eligibles. 
   */
  public function set_store_orders() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select('o.*');
    $this->ci->db->distinct();
    $this->ci->db->from("order o");
    $this->ci->db->join("user_profile p", "p.user_id = o.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = o.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("o.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("o.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("o.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("o.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->join("user u", "u.id = o.user_id");
      $this->ci->db->where("u.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->store_orders = $query->num_rows();
  }
  
  /**
   * Set the total number of referrals send base on current filters and eligibles. 
   */
  public function set_referrals_send() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select('r.email');
    $this->ci->db->distinct();
    $this->ci->db->from("refer_colleague r");
    $this->ci->db->join("user_profile p", "p.user_id = r.referal_user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("r.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("r.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("r.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("r.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("r.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->referrals_send = $query->num_rows();
  }
  
  /**
   * Set the percentage of referrals convered base on current filters and eligibles. 
   */
  public function set_referral_conversion() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select('COUNT(DISTINCT r.email) AS referred, COUNT(DISTINCT u.email) AS success', FALSE);
    $this->ci->db->from("refer_colleague r");
    $this->ci->db->join("user u", "u.email = r.email", "left outer");
    $this->ci->db->join("user_profile p", "p.user_id = r.referal_user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("r.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("r.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("r.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("r.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("r.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $row = $query->row();
    if ($row->referred > 0) {
      $this->referral_converson = $row->success / $row->referred * 100;
    } else {
      $this->referral_converson = 0;
    }
  }
  
  /**
   * Set total number of badges awared base on current filters and eligibles
   */ 
  public function set_badges_awarded() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->flush_cache();
    $this->ci->db->start_cache();
    $this->ci->db->select("dORp.id");
    $this->ci->db->distinct();
    $this->ci->db->join("user_profile p", "p.user_id = dORp.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = dORp.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("dORp.ref_table", "badge");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("dORp.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("dORp.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("dORp.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("dORp.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("dORp.site_id", $this->site_id);
    }
    $this->ci->db->stop_cache();
    $this->ci->db->from("user_draws dORp");
    $query = $this->ci->db->get();
    $this->badges_awarded = $query->num_rows();
    $this->ci->db->from("user_points dORp");
    $query = $this->ci->db->get();
    $this->badges_awarded += $query->num_rows();
    $this->ci->db->flush_cache();
  }
  
  /**
   * Set total number of member logged in base on current filter and eligibles.
   */
  public function set_member_sessions() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select("l.id");
    $this->ci->db->distinct();
    $this->ci->db->from("log_login l");
    $this->ci->db->join("user_profile p", "p.user_id = l.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    $this->ci->db->join("user_role ur", "ur.user_id = l.user_id");
    $this->ci->db->join("role r", "r.id = ur.role_id");
    $this->ci->db->where("p.registered", 1);
    $this->ci->db->where("r.system", 3);
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("l.login_timestamp >=", date("Y-m-d H:i:s",strtotime($this->first_date)));
    } else {
      $this->ci->db->where("l.login_timestamp >=", date("Y-m-d H:i:s",strtotime($this->from_date)));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("l.login_timestamp <=", date("Y-m-d H:i:s",strtotime($this->filter_date)));
    } else {
      $this->ci->db->where("l.login_timestamp <=", date("Y-m-d H:i:s",strtotime($this->to_date)));
    }
    if ($this->site_id > 1) {
      $this->ci->db->where("r.site_id", $this->site_id);
    }
    $query = $this->ci->db->get();
    $this->member_sessions = $query->num_rows();
  }
  
  /**
   * Set the monthly number of quizzes taken base on current filters and eligibles. 
   */
  public function set_quizzes_per_month() {
    if (empty($this->ci)) {
      $this->ci =& get_instance();
    }
    $this->ci->db->select("MONTH(FROM_UNIXTIME(h.create_timestamp)) as month, YEAR(FROM_UNIXTIME(h.create_timestamp)) as year, count(*) as quizzes", FALSE);
    $this->ci->db->from("quiz_history h");
    $this->ci->db->join("user_profile p", "p.user_id = h.user_id");
    $this->ci->db->join("retailer_store s", "s.id = p.store_id");
    if ( !empty($this->store)) {
      $this->ci->db->where('p.store_id', str_replace('_', ' ', $this->store));
    } elseif ( !empty($this->stores)) {
      $this->ci->db->where_in('p.store_id',  array_keys($this->stores));
    }
    if ( !empty($this->city)) {
      $this->ci->db->where('s.city', str_replace('_', ' ', $this->city));
    } elseif ( !empty($this->cities)) {
      $this->ci->db->where_in('s.city', $this->cities);
    }
    if ( !empty($this->state)) {
      $this->ci->db->where('p.province_code', $this->state);
    } elseif ( !empty($this->states)) {
      $this->ci->db->where_in('p.province_code', array_keys($this->states));
    }
    if ( !empty($this->retailer)) {
      $this->ci->db->where("p.retailer_id", $this->retailer);
    } elseif ( !empty ($this->retailers)){
      $this->ci->db->where_in("p.retailer_id", array_keys($this->retailers));
    }
    if ( !empty($this->country)) {
      $this->ci->db->where("p.country_code", $this->country);
    } elseif ( !empty($this->countries)){
      $this->ci->db->where_in("p.country_code", array_keys($this->countries));
    }
    if (empty($this->from_date)) {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->first_date));
    } else {
      $this->ci->db->where("h.create_timestamp >=", strtotime($this->from_date));
    }
    if (empty($this->to_date)) {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->filter_date));
    } else {
      $this->ci->db->where("h.create_timestamp <=", strtotime($this->to_date));
    }
    if ($this->site_id > 1) {
      $this->ci->db->join("quiz q", "q.id = h.quiz_id");
      $this->ci->db->where("q.site_id", $this->site_id);
    }
    $this->ci->db->group_by(array("month", "year"));
    $query = $this->ci->db->get();
    $rows = $query->result_array();
    $this->quizzes_per_month = array('cols' => array(array('id' => '', 'label' => 'Month', 'pattern' => '', 'type' => 'string'), array('id' => '', 'label' => 'Quizzes', 'pattern' => '', 'type' => 'number')), 'rows' => array());
    foreach($rows as $row) {
      $this->quizzes_per_month['rows'][] = array('c' => array(array('v' => date('M y', strtotime($row['year'].'-'.$row['month'].'-01')), 'f' => NULL), array('v' => $row['quizzes'], 'f' => NULL)));
    }
    $this->quizzes_per_month = json_encode($this->quizzes_per_month);
  }
}

?>
