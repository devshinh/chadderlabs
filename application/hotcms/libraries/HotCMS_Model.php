<?php

class HotCMS_Model extends CI_Model {

  protected $site_id;

	public function __construct()
  {
    parent::__construct();
    $this->site_id = (int)($this->session->userdata('siteID'));
  }

  public function get_user_profile($user_id)
  {
    $this->db->select();
    $this->db->join('user_profile','user_profile.user_id = user.id');
    $this->db->where('user.id', $user_id);
    $this->db->order_by('user_profile.last_name');
    $query = $this->db->get('user');
    return $query->row();
  }

  /**
   * get_user_avatar()
   *
   * @param id asset
   * @return object with avatar image
   */
  public function get_user_avatar($id)
  {
    $this->db->select();
    $this->db->where('asset.id', $id);
    $query = $this->db->get('asset');
    return $query->row();
  }
  
  /**
   * get_array_of_super_admins()
   *
   * @param id asset
   * @return array with user_id
   */
  public function _get_super_admin_users()
  {
    //get array of superadmins
    $this->db->distinct('user_id');
    $this->db->where('role_id',1);
    $super_admins = $this->db->get($this->tables["user_role"]);
    foreach($super_admins->result() as $super_admin){
      $admins[$super_admin->user_id] = $super_admin->user_id;
    }
    return $admins;
  }  

}
?>
