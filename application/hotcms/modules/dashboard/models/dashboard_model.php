<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('dashboard/dashboard', TRUE);
    $this->tables = $this->config->item('tables', 'dashboard');
  }

  /**
   * Count all orders
   * @return int
   */
  public function order_count()
  {
    $count = $this->db->count_all_results($this->tables['order']);
    return $count;
  }

  /**
   * Sum all orders excluding taxes
   * @return numeric
   */
  public function order_sum()
  {
    $query = $this->db->select_sum('subtotal - refund', 'order_sum')
      ->where('order_status', 1)
      ->get($this->tables['order']);
    $row = $query->row();
    return $row->order_sum;
  }
  
  /**
   * Count all referrals
   * @return numeric
   */
  public function referral_count()
  {
    if($this->site_id != 1){
         $this->db->where('site_id', $this->site_id);
    }
    $query = $this->db->select('id')
      ->get($this->tables['referral']);
    $nums = $query->num_rows();
    return $nums;
  }  

  /*
  public function insert( $value, $userID = null ) {
    // fetch logtype id
    $oLogType = self::select_row( 'lLogType', 0, 'login' );
    $this->db->set( 'nLogTypeID', $oLogType->nLogTypeID );
    $this->db->set( 'nModuleID',  $this->input->post( 'hdnModuleID' ) );
    $this->db->set( 'nUserID',    $userID );
    $this->db->set( 'sValue',     $value );
    $this->db->insert( 'cms_dLog' );
  } */

}
?>