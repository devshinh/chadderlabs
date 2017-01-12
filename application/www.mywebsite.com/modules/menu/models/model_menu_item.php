<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_menu_item extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('menu/menu', TRUE);
    $this->tables = $this->config->item('tables','menu');
  }

  /**
  * get_all_groups() - get all roles from DB
  *
  *
  *  @return object with all menu groups
  *
  */
  public function get_all_menu_items_by_group_id($id) {
    $this->db->where('menu_group_id', $id);
    $query = $this->db->order_by('sequence', 'ASC')->get($this->tables['menu_item']);
    return $query->result();
  }

  public function get_all_root_menu_items_by_menu_id($gid, $root_menu_id = 0)
  {
    if ($gid == 0) {
      $gid = $this->get_primary_menu_group();
    }
    $this->db->where('menu_group_id', $gid);
	  $this->db->where('parent_id', $root_menu_id);
    $query = $this->db->order_by('sequence', 'ASC')->get($this->tables['menu_item']);
    return $query->result();
  }

 /**
  * get_menu_item_by_id()
  * @param  int  menu id
  * @return menu item
  */
  public function get_menu_item_by_id($id) {
    $this->db->select();
    $this->db->where('id', $id);
    $query = $this->db->get($this->tables['menu_item']);
    return $query->row();
  }

 /**
  * get_menu_item_by_page_id()
  * @param  int  page id
  * @return menu item
  */
  public function get_menu_item_by_page_id($page_id) {
    $this->db->select();
    $this->db->where('module', 'page');
    $this->db->where('page_id', $page_id);
    $query = $this->db->get($this->tables['menu_item']);
    return $query->row();
  }

 /**
  * get_menu_item_by_page_id_menu_id()
  * @param  int  page id
  * @return menu item
  */
  public function get_menu_item_by_page_id_menu_id($page_id, $group_id) {
    $this->db->select();
    $this->db->where('menu_group_id', $group_id);
    $this->db->where('page_id', $page_id);
    $query = $this->db->get($this->tables['menu_item']);
    return $query->row();
  }

  public function insert($id) {

    self::_setElement();

    $this->db->set( 'menu_group_id', $id );
    $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false );
    $this->db->insert( $this->tables['menu_item'] );

  }

  public function update($id) {
    self::_setElement();

    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id );
    $this->db->update($this->tables['menu_item']);
  }

  public function delete_by_page_id($page_id)
  {
    $this->db->where( 'page_id', $page_id);
    $query = $this->db->get($this->tables['menu_item']);
    $deleted = false;
    $num_rows = $query->num_rows();
    if($num_rows == 1)
    {
      $row = $query->row();
      $id = $row->id;
      if(!$this->has_children($id))
      {
        $this->delete_by_id($id);
        $deleted = true;
      } else {
        //echo 'has children';
      }
    } else {
      if($num_rows > 1) {
        //echo 'multiple menu with same page';
      } else {
        //echo 'no records found';
      }
    }
    return $deleted;
  }

  public function delete_by_id($id)
  {
    $this->db->where( 'id', $id );
    $this->db->delete( $this->tables['menu_item'] );
  }

  public function update_menu($group_id, $menu_items)
  {
  	$result = $this->get_all_menu_items_by_group_id($group_id);
	  $menu_ids = $this->_update_complete_menu($group_id,$menu_items);
  }

  public function has_children($id)
  {
    $this->db->where($this->tables['menu_item'].'.parent_id', $id);
    $query = $this->db->get($this->tables['menu_item']);
	  return $query->num_rows() > 0;
  }

  public function create_menu_item($gid, $title, $page_id)
  {
    if ($gid == 0) {
      $gid = $this->get_primary_menu_group();
    }
    $this->db->set('menu_group_id',$gid);
    $this->db->set('parent_id', 0);
    $this->db->set( 'title', $title);
    $this->db->set('module', 'menu');
    $this->db->set( 'page_id',$page_id);
    $this->db->set('hidden', 1);
    $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false );
    $this->db->insert( $this->tables['menu_item'] );
  }

  private function get_last_sequence_in_menu_root($gid)
  {
    $sql = 'select MAX(sequence) as last from '.$this->tables['menu_item'].' where menu_group_id = ?';
    $query = $this->db->query($sql,array($gid));
    $max_sequence = $query->row();
    return $max_sequence->last;
  }

  public function create_menu_item_from_content($gid, $title, $path, $page_id, $hidden)
  {
    if ($gid == 0) {
      $gid = $this->get_primary_menu_group();
    }
    $sequence = $this->get_last_sequence_in_menu_root($gid);
    if (empty($sequence)) {
      $sequence = 1;
    }
    else {
      $sequence = $sequence + 1;
    }
    $this->db->set('menu_group_id',$gid);
    $this->db->set('parent_id', 0);
    $this->db->set('title', $title);
    $this->db->set('path',$path);
    $this->db->set('module', 'page');
    $this->db->set('page_id',$page_id);
    $this->db->set('hidden', 1);
    $this->db->set('sequence',$sequence);
    $this->db->set('create_date', 'CURRENT_TIMESTAMP', false);
    $this->db->insert($this->tables['menu_item']);
  }

  public function create_menu_item_external_link($gid, $title, $path, $enabled, $module)
  {
    if ($gid == 0) {
      $gid = $this->get_primary_menu_group();
    }
    $sequence = $this->get_last_sequence_in_menu_root($gid);
    $this->db->set('menu_group_id',$gid);
    $this->db->set('parent_id', 0);
    $this->db->set('title', $title);
    $this->db->set('path',$path);
    $this->db->set('module', $module);
    $this->db->set('page_id', NULL);
    $this->db->set('hidden', $enabled ? '0': '1');
    $this->db->set('sequence',$sequence);
    $this->db->set('create_date', 'CURRENT_TIMESTAMP', false );
    return $this->db->insert( $this->tables['menu_item'] );
  }

  public function update_menu_item_external_link($id,$title,$path,$enabled)
  {
    $this->db->set('title', $title);
    $this->db->set('path',$path);
    $this->db->set('hidden', $enabled ? 0 : 1);
    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id);
    return $this->db->update( $this->tables['menu_item'] );
  }

  public function update_menu_item_from_content($gid, $title, $path, $page_id, $hidden)
  {
    if ($gid == 0) {
      $gid = $this->get_primary_menu_group();
    }
    $this->db->where( 'page_id', $page_id );
    $this->db->where('menu_group_id', $gid);
    $query =  $this->db->get($this->tables['menu_item']);
    $num_rows = $query->num_rows();
    $updated = false;
    if($num_rows == 1)
    {
      $row = $query->row();
      $id = $row->id;
      //var_dump($max_sequence);
      $this->db->set('title', $title);
      $this->db->set('path',$path);
      $this->db->set('hidden', $hidden ? 1 : 0);
      $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
      $this->db->where( 'id', $id);
      $this->db->update( $this->tables['menu_item'] );
      $updated = true;
    } else {
      if($num_rows > 1) {
        //echo 'multiple menu with same page';
      } else {
        //echo 'no records found';
      }
    }
    return $updated;
  }

  public function update_menu_item($id, $title, $page_id, $enabled)
  {
    //$this->db->set( 'title', $this->input->post( 'title' ) );
    //$this->db->set( 'page_id',$this->input->post( 'pages_array' ));

    $this->db->set('title', $title);
    $this->db->set('page_id',$page_id);
	  $this->db->set('hidden',$enabled ? '0': '1');
    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['menu_item'] );
    return $this->db->affected_rows();
  }

  private function _update_complete_menu($group_id,$menu_items,$menu_ids = array(),$parent_id = 0)
  {
  	$sequence = 0;
    //echo "<br>menu id's";
    //var_dump($menu_items);
    //echo "<br>";
    foreach($menu_items as $menu_item)
    {
      $id = $menu_item->id;
      $menu_ids[] = $id;
        $this->db->set('update_date', 'CURRENT_TIMESTAMP', false );
      $this->db->set('parent_id', $parent_id);
      $this->db->set('sequence', $sequence);
        $this->db->where( 'id', $id );
      $this->db->update($this->tables['menu_item']);
      //echo "<br>";
      //echo $this->db->last_query();
      //echo "<br>";
      if(isset($menu_item->sub_menu) && count($menu_item->sub_menu) > 0)
      {
        $menu_ids = $this->_update_complete_menu($group_id, $menu_item->sub_menu, $menu_ids, $id);
      }
      $sequence++;
    }
    return $menu_ids;
  }

  private function _setElement() {

    // assign values
    $this->db->set( 'title', $this->input->post( 'title' ) );
    $this->db->set( 'page_id',$this->input->post( 'pages_array' ));
 ///HC
    $this->db->set( 'module','page');
  }

  public function save_sequence($table, $id, $sequence){
    $this->db->set( 'sequence', $sequence );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['menu_item'] );
  }

  /**
   * Get the default primary menu group ID
   * @return int
   */
  public function get_primary_menu_group()
  {
    $this->db->where('site_id', $this->site_id);
    $this->db->where('primary', 1);
    $query = $this->db->get($this->tables['menu_group']);
    $row = $query->row();
    if ($row) {
      $result = $row->id;
    }
    else {
      $result = 0;
    }
    return $result;
  }
}
?>