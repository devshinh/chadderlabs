<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Global_model extends HotCMS_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->tables = array(
      'country' => 'country',
      'module' => 'module',
      'module_widget' => 'module_widget',
      'province' => 'province',
    );
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

}
