<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_asset extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('asset/asset',TRUE);
    $this->tables = $this->config->item('table','asset');
  }

  /**
   * list all assets
    * list all items from DB
    * @param  array  filters, including search keyword, sorting field, and customized filter criteria
    * @param  int  page number
    * @param  int  per page
    * @return array of objects
   */
  //public function get_all_assets($type = 0, $category_id = 0, $page_num = 1, $per_page = 1000)
  public function get_all_assets($filters = FALSE, $page_num = 1, $per_page = 0)
  {
        $per_page = (int) $per_page;
        $page_num = (int) $page_num;
        if ($page_num < 1) {
            $page_num = 1;
        }
        $offset = ($page_num - 1) * $per_page;
        if ($offset < 0) {
            $offset = 0;
        }
        if ($per_page > 0) {
            $this->db->limit($per_page, $offset);
        }
        
        if (is_array($filters)) {
            if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
                $this->db->like('a.name', $filters['keyword']);
            }
            if (array_key_exists('category', $filters) && $filters['category'] > '') {
                if (is_array($filters['category'])) {
                    $this->db->where_in('c.name', $filters['category']);
                } else {
                    $this->db->where('c.name', $filters['category']);
                }
            }   
            if (array_key_exists('type', $filters) && $filters['type'] > '') {
                if (is_array($filters['type'])) {
                    $this->db->where_in('a.type', $filters['type']);
                } else {
                    $this->db->where('a.type', $filters['type']);
                }                
            }            
            $sortable_fields = array('name', 'type', 'description', 'create_date', 'update_date', 'asset_category_id');
            if (array_key_exists('sort_by', $filters) && $filters['sort_by'] > '' && in_array($filters['sort_by'], $sortable_fields)) {
                if (array_key_exists('sort_direction', $filters) && strtoupper($filters['sort_direction']) == 'DESC') {
                    $sort_direction = 'DESC';
                } else {
                    $sort_direction = 'ASC';
                }
                $this->db->order_by($filters['sort_by'], $sort_direction);
            } else {
                $this->db->order_by('name', 'ASC');
            }
        } else {
            $this->db->where('a.asset_category_id', $filters);
            $this->db->order_by('name', 'ASC');
        }
        $site_id = $this->session->userdata( 'siteID' );
        if ($site_id != 1){
            $this->db->where('a.site_id', $site_id);
        }
        
        $query = $this->db->select('a.*, c.name AS category_name, c.path')
                        ->join($this->tables['asset_category'] . ' c', 'c.id=a.asset_category_id')
                        ->get($this->tables['asset'] . ' a');

        $result = $query->result();
        //var_dump($this->db->last_query());
        return $result;        

  }

  /**
   * count all assets
   * @param  int  asset type
   * @param  int  category ID
   * @return objects
   */
  public function count_all_assets($filters = FALSE)
  {
        if (is_array($filters)) {
            if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
                $this->db->like('a.name', $filters['keyword']);
            }
        }
        if (array_key_exists('category', $filters) && $filters['category'] > '') {
            if (is_array($filters['category'])) {
                $this->db->where_in('c.name', $filters['category']);
            } else {
                $this->db->where('c.name', $filters['category']);
            }
        }
        if (array_key_exists('type', $filters) && $filters['type'] > '') {
            if (is_array($filters['type'])) {
                $this->db->where_in('a.type', $filters['type']);
            } else {
                $this->db->where('a.type', $filters['type']);
            }                
        }          
        $this->db->select('a.*, c.name AS category_name, c.path')->join($this->tables['asset_category'] . ' c', 'c.id=a.asset_category_id');
        if($this->session->userdata( 'siteID' ) != 1){
                $this->db->where('a.site_id', $this->session->userdata( 'siteID' ));
        }
        return $this->db->count_all_results($this->tables['asset'] . ' a');
  }
  
  public function get_all_image_assets() {
    $this->db->order_by('name', 'ASC');
    $this->db->where('type', 1);
    $query = $this->get($this->tables['asset']);
    return $query->result();
  }

  public function get_all_images() {
    $this->db->where($this->tables['asset'].'.type', 1);
    $query = $this->db->order_by('name', 'ASC')->get($this->tables['asset']);
    return $query->result();
  }

  public function get_all_images_by_catgegory_id($asset_category_id) {
    $this->db->where($this->tables['asset'].'.type', 1);
    $this->db->where('asset_category_id', $asset_category_id);
    $query = $this->db->order_by('name', 'ASC')->get($this->tables['asset']);
    return $query->result();
  }

  public function get_random_item_from_catgegory($asset_category_id = 0)
  {
    //$this->db->where('type', 1);
    if ($asset_category_id > 0) {
      $this->db->where('a.asset_category_id', $asset_category_id);
    }
    $query = $this->db->select('a.*, c.name AS category_name, c.path')
      ->join($this->tables['asset_category'] . ' c', 'c.id=a.asset_category_id')
      ->order_by('a.id', 'random')->limit(1)
      ->get($this->tables['asset'] . ' a');
    return $query->row();
  }

  /**
   * get_asset_by_id() - get asset by id
   * @param  int  asset id
   * @param  bool  if true, load published assets only
   * @return object with one row
   */
  public function get_asset_by_id($id, $live_only = TRUE)
  {
    //if ($live_only) {
    //  $this->db->where('status', 1);
    //}
    $query = $this->db->select('a.*, c.name AS category_name, c.path')
      ->join($this->tables['asset_category'] . ' c', 'c.id=a.asset_category_id')
      ->where('a.id', $id)->get($this->tables['asset'] . ' a');
    return $query->row();
  }

  /**
   * insert an asset record
   * @param int $type
   * @param int $asset_category_id
   * @param str $name
   * @param str $description
   * @return mixed
   */
  public function insert($type, $asset_category_id, $name, $description = '')
  {
    $this->db->set( 'site_id', $this->session->userdata( 'siteID' ) );
    $this->db->set( 'author_id', $this->session->userdata( 'user_id' ) );
    $this->db->set( 'type', $type );
    $this->db->set( 'asset_category_id', $asset_category_id);
    $this->db->set( 'name', $name );
    $this->db->set( 'description', $description );
    $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', FALSE );
    $result = $this->db->insert( $this->tables['asset'] );
    if ($result) {
      $asset_id = $this->db->insert_id();
      return $asset_id;
    }
    else {
      return FALSE;
    }
  }

  public function insert_asset($type, $asset_category_id, $filename, $extension, $name, $description = '')
  {
    $this->db->set( 'site_id', $this->session->userdata( 'siteID' ) );
    $this->db->set( 'author_id', $this->session->userdata( 'user_id' ) );
    $this->db->set( 'type', $type );
    $this->db->set( 'asset_category_id', $asset_category_id);
    $this->db->set( 'name', $name );
    $this->db->set( 'description', $description );
    $this->db->set( 'file_name', $filename );
    $this->db->set( 'extension', $extension );
    $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', FALSE );
    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', FALSE );
    $result = $this->db->insert( $this->tables['asset'] );
    if ($result) {
      $asset_id = $this->db->insert_id();
      return $asset_id;
    }
    else {
      return FALSE;
    }
  }

  public function insert_image_asset_with_info($name,$description,$asset_type,$asset_category_id,$filename,$extension,$width,$height)
  {
    $this->db->set( 'site_id', $this->session->userdata( 'siteID' ) );
    $this->db->set( 'author_id', $this->session->userdata( 'user_id' ));
    $this->db->set( 'name', $name);
    $this->db->set( 'description', $description);
    $this->db->set( 'type', $asset_type);
    $this->db->set( 'width', $width);
    $this->db->set( 'height', $height);
    $this->db->set( 'asset_category_id', $asset_category_id);
    $this->db->set( 'file_name', $filename );
    $this->db->set( 'extension', $extension );
    $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false );
    $result = $this->db->insert( $this->tables['asset'] );
    if ($result) {
      $asset_id = $this->db->insert_id();
//      $this->db->set('id', $asset_id);
//      $this->db->set('portrait', $portrait);
//      $this->db->insert( $this->tables['asset_image'] );
      return $asset_id;
    }
    else {
      return FALSE;
    }
  }

  public function image_asset_add_thumbnail($image_id,$width,$height,$keep_ratio,$folder)
  {
    $this->db->set('image_id',$image_id);
    $this->db->set('width',$width);
    $this->db->set('height',$height);
    $this->db->set('keep_ratio',$keep_ratio);
    $this->db->set('folder',$folder);
    $this->db->insert( $this->tables['asset_image_thumbnail'] );
  }

  public function image_asset_get_thumbnail($image_id,$width,$height)
  {
    $this->db->where('width', $width);
    $this->db->where('height', $height);
    $this->db->where('image_id',$image_id);
    $query = $this->db->get($this->tables['asset_image_thumbnail']);
    return $query->row();
  }

  /**
   * Delete all thumbnails of an asset
   * @param  int  asset ID
   * @return bool
   */
  public function asset_delete_thumbnails($id)
  {
    //TODO: delete the actual file(s) from the server
    $this->db->where('image_id', $id);
    return $this->db->delete($this->tables['asset_image_thumbnail']);
  }

  /**
   * add/update alternatives (webm, HD) to a video/audio asset, delete the old one if it exists
   * @param int $asset_id
   * @param str $filename
   * @param str $extension
   * @return bool
   */
  public function asset_add_alternative($asset_id, $filename, $extension, $format)
  {
    //TODO: delete the actual file(s) from the server
    $this->db->where('asset_id', $asset_id);
    $this->db->where('format', $format);
    $this->db->delete($this->tables['asset_alternative']);
    $this->db->set('asset_id', $asset_id);
    $this->db->set('file_name', $filename);
    $this->db->set('extension', $extension);
    $this->db->set('format', $format);
    return $this->db->insert($this->tables['asset_alternative']);
  }

  /**
   * list alternatives (webm, HD) for a video asset
   * @param int $asset_id
   * @return object
   */
  public function asset_list_alternatives($asset_id)
  {
    $this->db->where('asset_id', $asset_id)->order_by('format');
    $query = $this->db->get($this->tables['asset_alternative']);
    return $query->result();
  }

  /**
   * add/update poster image to an asset
   * @param int $asset_id
   * @param str $filename
   * @param str $extension
   * @return bool
   */
  public function asset_update_poster($asset_id, $filename)
  {
    $this->db->set('poster', $filename);
    $this->db->where('id', $asset_id);
    return $this->db->update($this->tables['asset']);
  }

  /**
   * Update asset attributes
   * @param  int  asset ID
   * @param  array  asset attributes
   * @return bool
   */
  public function update($id, $attr)
  {
    $fields = array('asset_category_id', 'name', 'description', 'file_name', 'extension', 'width', 'height', 'poster');
    foreach ($fields as $fld) {
      if (array_key_exists($fld, $attr)) {
        $this->db->set($fld, $attr[$fld]);
      }
    }
    $this->db->set('update_date', 'CURRENT_TIMESTAMP', FALSE);
    $this->db->where('id', $id);
    return $this->db->update($this->tables['asset']);
  }

  /**
   * Update asset name and description
   * @param  int  asset ID
   * @return bool
   */
  public function update_asset($id, $name, $description = '')
  {
    //$this->db->set( 'author_id', $this->session->userdata( 'user_id' ) );
    $this->db->set( 'name', $name );
    $this->db->set( 'description', $description );
    //$this->db->set( 'asset_category_id', $this->input->post( 'asset_categories' ) );
    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', FALSE );
    $this->db->where( 'id', $id );
    return $this->db->update( $this->tables['asset'] );
  }

  /**
   * Delete asset
   * @param  int  asset ID
   * @return bool
   */
  public function delete_by_id($id)
  {
    //TODO: delete the actual file(s) from the server
    $this->db->where( 'asset_id', $id );
    $this->db->delete( $this->tables['asset_alternative'] );
    $this->db->where( 'image_id', $id );
    $this->db->delete( $this->tables['asset_image_thumbnail'] );
    $this->db->where( 'id', $id );
    return $this->db->delete( $this->tables['asset'] );
  }

}
?>