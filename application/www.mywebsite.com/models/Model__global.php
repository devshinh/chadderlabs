<?php if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Model__global extends HotCMS_Model {

  private $tables = array();

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    // TODO: move table name to config file
    $this->tables['variable'] = 'variable';
    $this->tables['captcha'] = 'captcha';
    $this->tables['recipient'] = 'newsletter_recipient';
    $this->tables['module'] = 'module';
    $this->tables['province'] = 'province';
    $this->tables['country'] = 'country';
  }

  /**
   * get database timestamp
   */
  public function get_db_timestamp()
  {
    $sql = "SELECT UNIX_TIMESTAMP() AS ts";
    $query = $this->db->query($sql);
    $row = $query->row();
    return $row->ts;
  }

  /**
   * List provinces
   * @param str  $country_code
   * @retun array
   */
  public function list_provinces($country_code = '')
  {
    if ($country_code > '') {
      $this->db->where('country_code', $country_code);
    }
    $query = $this->db->select('country_code, province_code, province_name')
      ->order_by('country_code, province_name')
      ->get($this->tables['province']);
    return $query->result();
  }

  /**
   * List countries
   * @retun array
   */
  public function list_countries()
  {
    $query = $this->db->select('country_code, country')
      ->order_by('country_code')
      ->get($this->tables['country']);
    return $query->result();
  }

  /**
   * check if a captcha exists
   */
  public function captcha_exists($word, $ip) {
    $exp = time()-600;
    $sql = "SELECT COUNT(*) AS count FROM ".$this->tables['captcha']." WHERE word = ? AND ip_address = ? AND captcha_time > ?";
    $binds = array($word, $ip, $exp);
    $query = $this->db->query($sql, $binds);
    $row = $query->row();
    if ($row->count > 0){
      return TRUE;
    }else{
      return FALSE;
    }
  }

  /**
   * remove outdated captcha
   */
  public function captcha_clear() {
    $expiration = time()-300;
    $this->db->query("DELETE FROM ".$this->tables['captcha']." WHERE captcha_time < ".$expiration);
  }

  /**
   * insert a new captcha
   */
  public function captcha_insert($time, $ip, $word) {
    $data = array(
      'captcha_id'   => '',
      'captcha_time' => $time,
      'ip_address'   => $ip,
      'word'         => $word
    );
    $query = $this->db->insert_string($this->tables['captcha'], $data);
    $this->db->query($query);
  }

  /**
   * sign up for the newsletter
   */
  public function register_newsletter($firstname, $lastname, $email, $postal, $phone, $nonumber, $signupfrom='newsletter') {
    $query = $this->db->select('sEmail')
      ->where('sEmail', $email)
      ->get($this->tables['recipient']);
    $emails = $query->num_rows();
    if ($emails == 0){
      $data = array(
        'sFirstName' => $firstname,
        'sLastName' => $lastname,
        'sEmail'    => $email,
        'sPostalcode'  => $postal,
        'sPhone'  => $phone,
        'bNoNumber'   => $nonumber,
        'sSignupFrom'   => $signupfrom,
      );
      $result = $this->db->insert($this->tables['recipient'], $data);
      return $result;
    }
    else{
      // already signed up, fine
      return true;
    }
  }

  /**
   * Read/write variables
   */
  public function variable($name, $value = '') {
    if (empty($name)) {
      return FALSE;
    }
    $query = $this->db->select('value')
      ->where('name', $name)
      ->get($this->tables['variable']);
    $num_rows = $query->num_rows();
    if ($value > '') {
      // write a variable to database
      $data = array(
        'name' => $name,
        'value' => $value,
      );
      if ($query->num_rows() > 0) {
			  $this->db->where('name', $name);
			  $result = $this->db->update($this->tables['variable'], $data);
        return $result;
      }
      else {
        $result = $this->db->insert($this->tables['variable'], $data);
        return $result;
      }
    }
    else {
      // read a variable from database
      if ($query->num_rows() > 0) {
        $row = $query->row();
        return $row->value;
      }
      else {
        return FALSE;
      }
    }
  }

  public function get_site_id_by_url($url)
  {
    $query = $this->db->select('id')
      ->where('url', $url)
      ->get('site');
    $site_id = $query->row();

    return $site_id;
  }

  /**
   * List active modules
   */
  public function list_modules($active_only = TRUE)
  {
    if ($active_only) {
      $this->db->where('active', 1);
    }
    $query = $this->db->where('site_id', $this->site_id)
      ->order_by('sequence')
      ->get($this->tables['module']);
    return $query->result();
  }
  
  /**
  public function get_all_admins()
  {

    $query = $this->db->where('role_id', 2)
      
      ->get('user_role');
    return $query->result();
  }    
  
  public function add_member_role($user_id){
        $this->db->set('user_id', $user_id);
        $this->db->set('role_id', 6);
        $this->db->insert('user_role');
        
        return true;
  }
  **/
}
?>