<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * HotCMS Custom helper
 *
 * Some customized functions to be used cross the site
 */

/**
 * Convert a string into URL format
 * leaving only alphanumeric characters(all in lower case), forward slashes, and astrisks; replace all others with dashes
 * @param  string  a name/title
 * @return string
 */
if (!function_exists('format_url'))
{
  function format_url($name)
  {
    $url = strtolower(trim($name));
    // remove single qutation marks
    $url = str_replace(array("'"), "", $url);
    // replace all other characters with dashes
    $url = preg_replace("/[^A-Za-z0-9\/\*]/", "-", $url);
    // replace empty spaces and redundant dashes
    $url = str_replace(array(" ", "----", "---", "--"), "-", $url);
    // remove leading and trailing dashes
    $url = trim($url, "-");
    return $url;
  }
}

/**
 * Check if the current user has certain permission
 * @param  string  permission to be checked
 * @return bool
 */
if (!function_exists('has_permission'))
{
  function has_permission($permission)
  {
    $CI =& get_instance();
    //$permissions = $CI->session->userdata('permissions');
    $user_id = (int)($CI->session->userdata('user_id'));
    $permissions = $CI->permission->get_user_permissions($user_id);
    if (is_array($permissions)) {
      return (in_array($permission, $permissions) || in_array('super_admin', $permissions));
    }
    return FALSE;
  }
}

/**
 * List items per page options
 * @return array
 */
if (!function_exists('list_page_options'))
{
  function list_page_options()
  {
    return array('10' => '10 / page', '25' => '25 / page', '50' => '50 / page', '100' => '100 / page');
  }
}

/**
 * Get current timestamp from database
 * @return int
 */
if (!function_exists('current_db_timestamp'))
{
  function current_db_timestamp()
  {
    $CI =& get_instance();
    $CI->load->model('global_model');
    return $CI->global_model->get_db_timestamp();
  }
}

/**
 * List all provinces
 * @param  string  country code
 * @return array of objects
 */
if (!function_exists('list_provinces'))
{
  function list_provinces($country_code = '')
  {
    $CI =& get_instance();
    $CI->load->model('global_model');
    return $CI->global_model->list_provinces($country_code);
  }
}

/**
 * List all provinces in an array
 * @param  string  country code
 * @return array
 */
if (!function_exists('list_province_array'))
{
  function list_province_array($country_code = '')
  {
    $result = array();
    $provinces = list_provinces($country_code);
    if ($country_code > '') {
      foreach ($provinces as $row) {
        $result[$row->province_code] = $row->province_name;
      }
    }
    else {
      foreach ($provinces as $row) {
        $result[$row->province_code] = $row->country_code . ' - ' . $row->province_name;
      }
    }
    return $result;
  }
}

/**
 * List all provinces in an array using Ajax
 * The URL to get here is /ajax/global/provinces/country_code
 * @param  string  country code
 * @return array
 */
if (!function_exists('global_provinces_ajax'))
{
  function global_provinces_ajax($country_code = '')
  {
    $json = array(
      'result' => FALSE,  // mandatory for all JSON output
      'messages' => '',   // mandatory for all JSON output
      'provinces' => '',  // dynamic output parameter, include when needed
    );
    $provinces = list_provinces($country_code);
    $json['result'] = TRUE;
    $json['provinces'] = array();
    if ($country_code > '') {
      foreach ($provinces as $row) {
        $json['provinces'][$row->province_code] = $row->province_name;
      }
    }
    else {
      foreach ($provinces as $row) {
        $json['provinces'][$row->province_code] = $row->country_code . ' - ' . $row->province_name;
      }
    }
    return $json;
  }
}

/**
 * List all countries
 * @return array of objects
 */
if (!function_exists('list_countries'))
{
  function list_countries()
  {
    $CI =& get_instance();
    $CI->load->model('global_model');
    return $CI->global_model->list_countries();
  }
}

/**
 * List all country in an array
 * @return array
 */
if (!function_exists('list_country_array'))
{
  function list_country_array()
  {
    $result = array();
    $countries = list_countries();
    foreach ($countries as $row) {
      $result[$row->country_code] = $row->country;
    }
    return $result;
  }
}

/**
 * List all time zones
 * @return array
 */
if (!function_exists('list_timezone'))
{
  function list_timezone()
  {
//    static $timezones = array(
//      //'GMT' => 'Greenwich Mean Time 	GMT',
//      'UTC' => 'Universal Coordinated Time GMT',
//      'ECT' => 'European Central Time GMT+1',
//      'EET' => 'Eastern European Time GMT+2',
//      'ART' => 'Egypt Standard Time GMT+2',
//      'EAT' => 'Eastern African Time GMT+3',
//      'MET' => 'Middle East Time GMT+3:30',
//      'NET' => 'Near East Time GMT+4',
//      'PLT' => 'Pakistan Lahore Time GMT+5',
//      'IST' => 'India Standard Time GMT+5:30',
//      'BST' => 'Bangladesh Standard Time GMT+6',
//      'VST' => 'Vietnam Standard Time GMT+7',
//      'CTT' => 'China Taiwan Time GMT+8',
//      'JST' => 'Japan Standard Time GMT+9',
//      'ACT' => 'Australia Central Time GMT+9:30',
//      'AET' => 'Australia Eastern Time GMT+10',
//      'SST' => 'Solomon Standard Time GMT+11',
//      'NST' => 'New Zealand Standard Time GMT+12',
//      'MIT' => 'Midway Islands Time	GMT-11',
//      'HST' => 'Hawaii Standard Time GMT-10',
//      'AST' => 'Alaska Standard Time GMT-9',
//      'PST' => 'Pacific Standard Time GMT-8',
//      'PNT' => 'Phoenix Standard Time GMT-7',
//      'MST' => 'Mountain Standard Time GMT-7',
//      'CST' => 'Central Standard Time GMT-6',
//      'EST' => 'Eastern Standard Time GMT-5',
//      'IET' => 'Indiana Eastern Standard Time GMT-5',
//      'PRT' => 'Puerto Rico and US Virgin Islands Time GMT-4',
//      'CNT' => 'Canada Newfoundland Time GMT-3:30',
//      'AGT' => 'Argentina Standard Time GMT-3',
//      'BET' => 'Brazil Eastern Time GMT-3',
//      'CAT' => 'Central African Time GMT-1',
//    );
    static $timezones = array(
      "Pacific/Midway" => "(GMT-11:00) Midway Island, Samoa",
      "America/Adak" => "(GMT-10:00) Hawaii-Aleutian",
      "Etc/GMT+10" => "(GMT-10:00) Hawaii",
      "Pacific/Marquesas" => "(GMT-09:30) Marquesas Islands",
      "Pacific/Gambier" => "(GMT-09:00) Gambier Islands",
      "America/Anchorage" => "(GMT-09:00) Alaska",
      "America/Ensenada" => "(GMT-08:00) Tijuana, Baja California",
      "Etc/GMT+8" => "(GMT-08:00) Pitcairn Islands",
      "America/Los_Angeles" => "(GMT-08:00) Pacific Time (US & Canada)",
      "America/Denver" => "(GMT-07:00) Mountain Time (US & Canada)",
      "America/Chihuahua" => "(GMT-07:00) Chihuahua, La Paz, Mazatlan",
      "America/Dawson_Creek" => "(GMT-07:00) Arizona",
      "America/Belize" => "(GMT-06:00) Saskatchewan, Central America",
      "America/Cancun" => "(GMT-06:00) Guadalajara, Mexico City, Monterrey",
      "Chile/EasterIsland" => "(GMT-06:00) Easter Island",
      "America/Chicago" => "(GMT-06:00) Central Time (US & Canada)",
      "America/New_York" => "(GMT-05:00) Eastern Time (US & Canada)",
      "America/Havana" => "(GMT-05:00) Cuba",
      "America/Bogota" => "(GMT-05:00) Bogota, Lima, Quito, Rio Branco",
      "America/Caracas" => "(GMT-04:30) Caracas",
      "America/Santiago" => "(GMT-04:00) Santiago",
      "America/La_Paz" => "(GMT-04:00) La Paz",
      "Atlantic/Stanley" => "(GMT-04:00) Faukland Islands",
      "America/Campo_Grande" => "(GMT-04:00) Brazil",
      "America/Goose_Bay" => "(GMT-04:00) Atlantic Time (Goose Bay)",
      "America/Glace_Bay" => "(GMT-04:00) Atlantic Time (Canada)",
      "America/St_Johns" => "(GMT-03:30) Newfoundland",
      "America/Araguaina" => "(GMT-03:00) UTC-3",
      "America/Montevideo" => "(GMT-03:00) Montevideo",
      "America/Miquelon" => "(GMT-03:00) Miquelon, St. Pierre",
      "America/Godthab" => "(GMT-03:00) Greenland",
      "America/Argentina/Buenos_Aires" => "(GMT-03:00) Buenos Aires",
      "America/Sao_Paulo" => "(GMT-03:00) Brasilia",
      "America/Noronha" => "(GMT-02:00) Mid-Atlantic",
      "Atlantic/Cape_Verde" => "(GMT-01:00) Cape Verde Is.",
      "Atlantic/Azores" => "(GMT-01:00) Azores",
      "Europe/Belfast" => "(GMT) Greenwich Mean Time : Belfast",
      "Europe/Dublin" => "(GMT) Greenwich Mean Time : Dublin",
      "Europe/Lisbon" => "(GMT) Greenwich Mean Time : Lisbon",
      "Europe/London" => "(GMT) Greenwich Mean Time : London",
      "Africa/Abidjan" => "(GMT) Monrovia, Reykjavik",
      "Europe/Amsterdam" => "(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna",
      "Europe/Belgrade" => "(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague",
      "Europe/Brussels" => "(GMT+01:00) Brussels, Copenhagen, Madrid, Paris",
      "Africa/Algiers" => "(GMT+01:00) West Central Africa",
      "Africa/Windhoek" => "(GMT+01:00) Windhoek",
      "Asia/Beirut" => "(GMT+02:00) Beirut",
      "Africa/Cairo" => "(GMT+02:00) Cairo",
      "Asia/Gaza" => "(GMT+02:00) Gaza",
      "Africa/Blantyre" => "(GMT+02:00) Harare, Pretoria",
      "Asia/Jerusalem" => "(GMT+02:00) Jerusalem",
      "Europe/Minsk" => "(GMT+02:00) Minsk",
      "Asia/Damascus" => "(GMT+02:00) Syria",
      "Europe/Moscow" => "(GMT+03:00) Moscow, St. Petersburg, Volgograd",
      "Africa/Addis_Ababa" => "(GMT+03:00) Nairobi",
      "Asia/Tehran" => "(GMT+03:30) Tehran",
      "Asia/Dubai" => "(GMT+04:00) Abu Dhabi, Muscat",
      "Asia/Yerevan" => "(GMT+04:00) Yerevan",
      "Asia/Kabul" => "(GMT+04:30) Kabul",
      "Asia/Yekaterinburg" => "(GMT+05:00) Ekaterinburg",
      "Asia/Tashkent" => "(GMT+05:00) Tashkent",
      "Asia/Kolkata" => "(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi",
      "Asia/Katmandu" => "(GMT+05:45) Kathmandu",
      "Asia/Dhaka" => "(GMT+06:00) Astana, Dhaka",
      "Asia/Novosibirsk" => "(GMT+06:00) Novosibirsk",
      "Asia/Rangoon" => "(GMT+06:30) Yangon (Rangoon)",
      "Asia/Bangkok" => "(GMT+07:00) Bangkok, Hanoi, Jakarta",
      "Asia/Krasnoyarsk" => "(GMT+07:00) Krasnoyarsk",
      "Asia/Hong_Kong" => "(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi",
      "Asia/Irkutsk" => "(GMT+08:00) Irkutsk, Ulaan Bataar",
      "Australia/Perth" => "(GMT+08:00) Perth",
      "Australia/Eucla" => "(GMT+08:45) Eucla",
      "Asia/Tokyo" => "(GMT+09:00) Osaka, Sapporo, Tokyo",
      "Asia/Seoul" => "(GMT+09:00) Seoul",
      "Asia/Yakutsk" => "(GMT+09:00) Yakutsk",
      "Australia/Adelaide" => "(GMT+09:30) Adelaide",
      "Australia/Darwin" => "(GMT+09:30) Darwin",
      "Australia/Brisbane" => "(GMT+10:00) Brisbane",
      "Australia/Hobart" => "(GMT+10:00) Hobart",
      "Asia/Vladivostok" => "(GMT+10:00) Vladivostok",
      "Australia/Lord_Howe" => "(GMT+10:30) Lord Howe Island",
      "Etc/GMT-11" => "(GMT+11:00) Solomon Is., New Caledonia",
      "Asia/Magadan" => "(GMT+11:00) Magadan",
      "Pacific/Norfolk" => "(GMT+11:30) Norfolk Island",
      "Asia/Anadyr" => "(GMT+12:00) Anadyr, Kamchatka",
      "Pacific/Auckland" => "(GMT+12:00) Auckland, Wellington",
      "Etc/GMT-12" => "(GMT+12:00) Fiji, Kamchatka, Marshall Is.",
      "Pacific/Chatham" => "(GMT+12:45) Chatham Islands",
      "Pacific/Tongatapu" => "(GMT+13:00) Nuku'alofa",
      "Pacific/Kiritimati" => "(GMT+14:00) Kiritimati"
    );
    return $timezones;
  }
}

/**
 * Initialize and return default paginate configurations
 * @return array
 */
if (!function_exists('pagination_configuration'))
{
  function pagination_configuration()
  {
    $config = array();
    // default paginate configurations
    $config['base_url'] = '';   // mandatory
    $config['total_rows'] = 0;  // mandatory
    $config['uri_segment'] = 4; // mandatory
    $config['per_page'] = 10;
    $config['num_links'] = 9;
    $config['use_page_numbers'] = TRUE;
    $config['first_link'] = '&laquo;';
    $config['first_tag_open'] = '<span class="first_link">';
    $config['first_tag_close'] = '</span>';
    $config['last_link'] = '&raquo;';
    $config['last_tag_open'] = '<span class="last_link">';
    $config['last_tag_close'] = '</span>';
    $config['next_link'] = '&gt;';
    $config['next_tag_open'] = '<span class="next_link">';
    $config['next_tag_close'] = '</span>';
    $config['prev_link'] = '&lt;';
    $config['prev_tag_open'] = '<span class="prev_link">';
    $config['prev_tag_close'] = '</span>';
    $config['cur_tag_open'] = '<span class="current">';
    $config['cur_tag_close'] = '</span>';
    $config['num_tag_open'] = '<span class="page_link">';
    $config['num_tag_close'] = '</span>';
    return $config;
  }
}

/**
 * strip the extension from a file name
 *
 * @param  string $name filename
 * @return string filename without extension
 */
if (!function_exists('strip_extension'))
{
  function strip_extension($name)
  {
    $ext = strrchr($name, '.');
    if ($ext !== FALSE) {
      $name = substr($name, 0, -strlen($ext));
    }
    return $name;
  }
}

/**
 * get the extension from a file name
 *
 * @param    string $name filename
 * @return   string filename without extension
 */
if (!function_exists('get_extension'))
{
  function get_extension($name)
  {
    return substr(strrchr($name, '.'), 1);
  }
}

/**
  * compare site objects for sorting
  * @param object $a
  * @param object $b
  * @return bool
  */
if (!function_exists('compare_sites'))
{
  function compare_sites($a, $b)
  {
    if ($a->primary != $b->primary) {
      return $b->primary - $a->primary;
    }
    else {
      return strcmp($a->name, $b->name);
    }
  }
}

/* End of file custom_helper.php */
/* Location: ./application/helpers/custom_helper.php */