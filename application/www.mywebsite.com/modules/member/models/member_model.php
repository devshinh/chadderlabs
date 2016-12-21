<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Member Model
*
* Author: Jan Antl
*
* Created:  06.12.2011
* Last updated:  06.12.2011
*
* Description:  Member model.
*
*/

class Member_model extends HotCMS_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('member', TRUE);
    $this->tables = $this->config->item('tables', 'member');
  }

  /**
   * get user account
   */
  public function get_user($userid) {
    $query = $this->db->select('m.email,p.first_name,p.last_name,p.postal')
      ->join('member_profile p', 'p.user_id=m.id')
      ->where('m.id', $userid)
      ->get('member m');
    $user = $query->row();
    return $user;
  }

  /**
   * update user info
   */
  public function update_user($userid) {

    $data = array(
      'first_name' => $this->input->post('first_name'),
      'last_name' => $this->input->post('last_name'),
      'postal' => $this->input->post('postal')
    );

    $this->db->where('user_id', $userid);
    $this->db->update('member_profile', $data);

  }

 }
 ?>