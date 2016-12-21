<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_asset_category extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('asset/asset',TRUE);
    $this->tables = $this->config->item('table','asset');
  }

  public function get_all_categories()
  {
    $this->db->where('site_id', $this->site_id);
    $this->db->order_by('id', 'ASC');
    $query = $this->db->get($this->tables['asset_category']);
    return $query->result();
  }

  public function get_category_by_id($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get($this->tables['asset_category']);
    return $query->row();
  }

  public function get_system_generated_category($name, $context)
  {
    $asset_category_id = false;
    //$this->db->where('system_generated',1);
    $this->db->where('name',$name);
    $this->db->where('context',$context);
    $this->db->where('site_id', $this->site_id);
    $query = $this->db->get($this->tables['asset_category']);
    if ($query->num_rows() == 0) {
      $this->db->set('name',$name);
      $this->db->set('path',$name);
      $this->db->set('context',$context);
      $this->db->set('system_generated',1);
      $this->db->insert( $this->tables['asset_category'] );
      $asset_category_id = $this->db->insert_id();
    }
    else {
      $asset_category_id = $query->row()->id;
    }
    //log_message('info',$this->db->last_query());
    return $asset_category_id;
  }

  public function get_system_generated_categories($context)
  {
    //$this->db->where('system_generated', 1);
    $this->db->where('site_id', $this->site_id);
    $this->db->where('context', $context);
    $query = $this->db->get($this->tables['asset_category']);
    return $query->result();
  }

}
?>