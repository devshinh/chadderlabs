<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('product/product',TRUE);
    $this->tables = $this->config->item('tables', 'product');
  }

  /**
   * Given a slug, retrieves a product ID
   * returns 0 if the product does not exist
   */
  public function get_product_id($slug)
  {
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('slug', $slug)
      ->get($this->tables['product']);
    if ($query->num_rows()) {
      return $query->row()->id;
    }
    else {
      return 0;
    }
  }

  /**
   * Lists all product categories
   * @return array of objects
   */
  public function product_category_list()
  {
    $query = $this->db->where('site_id', $this->site_id)
      ->order_by('name', 'ASC')
      ->get($this->tables['category']);
    return $query->result();
  }

  /**
   * Lists all products from DB
   * @param  int  category ID
   * @param  bool  live/published only
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function list_products($category_id = 0, $live_only = TRUE, $page_num = 1, $per_page = 0)
  {
    $per_page = (int)$per_page;
    $page_num = (int)$page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num - 1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    if ($category_id > 0) {
      $this->db->where('category_id', (int)$category_id);
    }
    if ($live_only) {
      $this->db->where('p.active', 1);
    }
    if ($per_page > 0) {
      $this->db->limit($per_page, $offset);
    }
    $query = $this->db->select('p.*, c.name AS category_name')
      ->join($this->tables['product_category'] . ' c', 'c.id=p.category_id')
      ->where('p.site_id', $this->site_id)
      ->order_by('p.sequence', 'ASC')
      ->get($this->tables['product'] . ' p');
    return $query->result();
  }

  /**
   * Counts all product
   * @param  int  category ID
   * @param  bool  live/published only
   * @return int
   */
  public function count_product($category_id = 0, $live_only = TRUE)
  {
    if ($category_id > 0) {
      $this->db->where('category_id', (int)$category_id);
    }
    if ($live_only) {
      $this->db->where('active', 1);
    }
    $query = $this->db->where('site_id', $this->site_id)
      ->get($this->tables['product']);
    return $query->num_rows();
  }

  /**
   * Given a slug or ID, retrieve a product from DB
   * @param  int  product ID,
   * @param  str  product slug
   * @param  bool  loads live/published product only
   * @return mixed FALSE if the product does not exist
   */
  public function get_product($id = 0, $slug = '', $live_only = TRUE)
  {
    $id = (int)$id;
    $slug = trim($slug);
    if ($id == 0 && $slug == '') {
      return FALSE;
    }
    $this->db->select()->where('site_id', $this->site_id);
    if ($id > 0) {
      $this->db->where('id', $id);
    }
    else {
      $this->db->where('slug', $slug);
    }
    if ($live_only) {
      $this->db->where('active', 1);
    }
    $query = $this->db->get($this->tables['product']);
    return $query->row();
  }

  /**
   * Get product assets from DB by id produt
   * @param  int  product id
   * @return object with all assets for product
   */
  public function list_product_assets($id)
  {
    $this->load->helper('asset/asset');
    $this->db->select('asset_id');
    //$this->db->join($this->tables['asset'] . ' a', 'a.id = pa.asset_id');
    $this->db->where('pa.product_id', $id);
    $query = $this->db->get($this->tables['product_asset'] . ' pa');
    $rows = $query->result();
    $assets = array();
    foreach ($rows as $row) {
      $assets[] = asset_load_item($row->asset_id);
    }
    return $assets;
  }

  /**
   * add_image_asset() - add image for product
   * @param asset id
   * @param product id
   * @return true if image added
   */
  public function add_image_asset($a_id, $p_id)
  {
    $this->db->set( 'asset_id', $a_id);
    $this->db->set( 'product_id', $p_id);
    $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false );
    $this->db->insert( $this->tables['product_asset'] );
  }

  /**
   * Check to see if a slug already exists
   * @param  str   product slug
   * @param  int   exclude primary key
   * @return bool
   */
  public function slug_exists($slug, $exclude_id = 0)
  {
    $query = $this->db->select('id')
      ->where('slug', $slug);
    if ($exclude_id > 0) {
      $this->db->where('id != ', $exclude_id);
    }
    $query = $this->db->get($this->tables['product']);
    return $query->num_rows();
  }

  /**
   * Get a random slug for showcase purpose
   */
  public function get_random_slug()
  {
    $query = $this->db->select('slug')
            ->where('active', 1)
            ->where('site_id', $this->site_id)
            ->order_by('', 'random')
            ->limit(1)
            ->get($this->tables['product']);
    if ($query->num_rows > 0) {
      $result = $query->row()->slug;
    }
    else {
      $result = '';
    }
    return $result;
  }

  public function insert()
  {
    $time = now();
    self::_setElement();
    $this->db->set('create_timestamp', $time);
    $this->db->set('update_timestamp', $time);
    return $this->db->insert($this->tables['product']);
  }

  public function update($id)
  {
    self::_setElement();
    $this->db->set('update_timestamp', now());
    $this->db->where('id', $id);
    $this->db->update($this->tables['product']);
  }

  public function delete_by_id($id)
  {
    $this->db->where('id', $id);
    $this->db->delete($this->tables['product']);
    return TRUE;
  }

  private function _setElement()
  {
    // assign values
    $this->db->set( 'name', $this->input->post( 'name' ) );
    $this->db->set( 'slug', url_title($this->input->post( 'name' ),'-',TRUE ));
    //$this->db->set( 'short_description', $this->input->post( 'short_description' ) );
    $this->db->set( 'description', html_entity_decode($this->input->post( 'description' )) );
    $this->db->set( 'category_id', $this->input->post( 'category' ) );
    $this->db->set( 'price', $this->input->post( 'price' ) );
    $this->db->set( 'stock', $this->input->post( 'stock' ) );
    $this->db->set( 'featured_image_id', $this->input->post( 'featured_image_id' ) );
    //$this->db->set( 'opening_time', $this->input->post( 'opening_time' ) );
    //$this->db->set( 'closing_time', $this->input->post( 'closing_time' ) );
    //$this->db->set( 'auction_id', 1 ); ///TODO - get auction id properly
    $this->db->set( 'active', $this->input->post( 'active' ) ? 1 : 0 );
  }

  public function delete_asset($id)
  {
    // delete user data
    $this->db->where( 'a_id', $id );
    $this->db->delete( $this->tables['product_asset'] );
  }

  public function save_asset_sequence($table, $id, $sequence)
  {
    $this->db->set( 'sequence', $sequence );
    $this->db->where( 'a_id', $id );
    $this->db->update( $this->tables['product_asset'] );
  }

  public function save_product_sequence($table, $id, $sequence)
  {
    $this->db->set( 'sequence', $sequence );
    $this->db->where( 'id', $id );
    $this->db->update( $table );
  }

  /**
   *
   * returns 0 if the product does not exist
   */
  public function get_product_by_id($id)
  {
    $query = $this->db->select()
      ->where('site_id', $this->site_id)
      ->where('id', $id)
      ->get($this->tables['product']);
    if ($query->num_rows()) {
      return $query->row();
    }
    else {
      return 0;
    }
  }
}
?>
