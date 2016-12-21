<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_model extends HotCMS_Model {

   public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('site/site', TRUE);
    //$this->tables['site']  = $this->config->item('table', 'site');
    $this->tables = $this->config->item('tables', 'site');
    
    $this->default_roles = array( array( 'name' => 'admin', 
                                      'description' => 'manage users, modules, and contents', 
                                      'active' => '1',
                                      'system' => '2', 
                                    ),
                               array( 'name' => 'member', 
                                      'description' => 'can browse content and take quizzes', 
                                      'active' => '1',
                                      'system' => '3', 
                                    ),      
                               array( 'name' => 'visitor', 
                                      'description' => 'can browse content ', 
                                      'active' => '1',
                                      'system' => '4', 
                                    ),              
                             ); 
    
     $this->default_permissions = array(
      array('permission_key' => 'view_content','description' => 'View published website content','category' => 'core', "map_roles" => array("member")),
      array('permission_key' => 'admin_area','description' => 'Access admin area','category' => 'core'),
      array('permission_key' => 'manage_content','description' => 'Manage content','category' => 'page'),
      array('permission_key' => 'update_content','description' => 'Update content','category' => 'page'),
      array('permission_key' => 'create_content','description' => 'Create content','category' => 'page'),
      array('permission_key' => 'view_user','description' => 'View user profile','category' => 'user', "map_roles" => array("member")),
      array('permission_key' => 'manage_user','description' => 'Manage users','category' => 'user'),
      array('permission_key' => 'manage_role','description' => 'Manage roles','category' => 'user'),
      array('permission_key' => 'manage_member','description' => 'Manage members','category' => 'user'),
      array('permission_key' => 'manage_author','description' => 'Manage authors','category' => 'user'),
      array('permission_key' => 'manage_editor','description' => 'Manage editors','category' => 'user'),
      array('permission_key' => 'manage_publisher','description' => 'Manage publishers','category' => 'user'),
      array('permission_key' => 'manage_admin','description' => 'Manage administrators','category' => 'user'),
      array('permission_key' => 'view_news','description' => 'View news','category' => 'news', "map_roles" => array("member")),
      array('permission_key' => 'manage_news','description' => 'Manage and publish all news','category' => 'news'),
      array('permission_key' => 'edit_news','description' => 'Edit all users\' news','category' => 'news'),
      array('permission_key' => 'create_news','description' => 'Create and update own news','category' => 'news'),
      array('permission_key' => 'view_locations','description' => 'View locations list','category' => 'location'),
      array('permission_key' => 'view_training','description' => 'View training','category' => 'training'),
      array('permission_key' => 'manage_training','description' => 'Manage and publish all training','category' => 'training'),
      array('permission_key' => 'edit_training','description' => 'Edit all users\' training','category' => 'training'),
      array('permission_key' => 'create_training','description' => 'Create and update own training','category' => 'training', "map_roles" => array("member")),
      array('permission_key' => 'earn_draws','description' => 'Earn draws','category' => 'quiz', "map_roles" => array("member")),
      array('permission_key' => 'earn_points','description' => 'Earn points from quizzes','category' => 'quiz', "map_roles" => array("member")),
      array('permission_key' => 'view_quiz','description' => 'View quiz','category' => 'quiz', "map_roles" => array("member")),
      array('permission_key' => 'manage_quiz','description' => 'Manage and publish all quiz','category' => 'quiz'),
      array('permission_key' => 'view_product','description' => 'View product','category' => 'product', "map_roles" => array("member")),
      array('permission_key' => 'manage_product','description' => 'Manage all products','category' => 'product'),
      array('permission_key' => 'manage_retailer','description' => 'Manage retialers','category' => 'retailer'),
      array('permission_key' => 'manage_retailer_permission','description' => 'Manage retialer\'s permissions','category' => 'retailer')
    );    
     
    $this->default_modules = array(
      array('name' => 'Random Media','module_code' => 'randomizer','icon_name' => '','version' => NULL,'core_level' => '1','sequence' => '6','is_embed' => '1','active' => '1'),
      array('name' => 'Image','module_code' => 'image','icon_name' => 'picture','version' => NULL,'core_level' => '1','sequence' => '2','is_embed' => '1','active' => '1'),
      array('name' => 'Carousel','module_code' => 'carousel','icon_name' => '','version' => NULL,'core_level' => '1','sequence' => '4','is_embed' => '1','active' => '1'),
      array('name' => 'Member','module_code' => 'member','icon_name' => '','version' => NULL,'core_level' => '1','sequence' => '5','is_embed' => '1','active' => '1'),
      array('name' => 'News','module_code' => 'news','icon_name' => '','version' => NULL,'core_level' => '1','sequence' => '3','is_embed' => '1','active' => '1'),
      array('name' => 'Locations','module_code' => 'location','icon_name' => '','version' => NULL,'core_level' => '1','sequence' => '7','is_embed' => '1','active' => '1'),
      array('name' => 'Training','module_code' => 'training','icon_name' => '','version' => NULL,'core_level' => '1','sequence' => '3','is_embed' => '1','active' => '1'),
      array('name' => 'Quiz','module_code' => 'quiz','icon_name' => '','version' => NULL,'core_level' => '1','sequence' => '3','is_embed' => '1','active' => '1'),
      array('name' => 'Account','module_code' => 'account','icon_name' => '','version' => NULL,'core_level' => '0','sequence' => '0','is_embed' => '0','active' => '1'),
      array('name' => 'Product','module_code' => 'product','icon_name' => '','version' => NULL,'core_level' => '1','sequence' => '8','is_embed' => '1','active' => '1'),
      array('name' => 'User','module_code' => 'user','icon_name' => '','version' => NULL,'core_level' => '1','sequence' => '9','is_embed' => '1','active' => '1'),
      array('name' => 'Media Library','module_code' => 'asset','icon_name' => '','version' => NULL,'core_level' => '0','sequence' => '10','is_embed' => '0','active' => '1'),
      array('name' => 'Page publisher','module_code' => 'page','icon_name' => '','version' => '1','core_level' => '0','sequence' => '1','is_embed' => '0','active' => '1'),
      array('name' => 'Refer Colleague','module_code' => 'refer_colleague','icon_name' => '','version' => '1','core_level' => '0','sequence' => '12','is_embed' => '0'),
      array('name' => 'Badge','module_code' => 'badge','icon_name' => '','version' => '1','core_level' => '0','sequence' => '14','is_embed' => '0')
    );     
    
$this->default_page_layout = array(
  array('name' => 'Overview page','icon' => 'layout_full_width.jpg','description' => 'Overview page layout','zones' => 'upper_left,upper_right,middle_zone,lower_left,lower_right'),
  array('name' => 'Two columns','icon' => 'layout_right_sidebar.jpg','description' => 'Right column  70%, left 30%','zones' => 'left_zone,right_zone'),
  array('name' => 'Two columns','icon' => 'layout_left_sidebar.jpg','description' => 'Right column  30%, left 70%','zones' => 'left_zone,right_zone')
);
    
$this->module_widget_default = array(
  array('name' => 'Account widgets','widget_code' => 'account_forms_widget','sequence' => '1'),
  array('name' => 'Image','widget_code' => 'image_widget','sequence' => '2'),
  array('name' => 'News Item Detail','widget_code' => 'news_item_widget','sequence' => '3'),
  array('name' => 'News List','widget_code' => 'news_list_widget','sequence' => '4'),
  array('name' => 'News Preview','widget_code' => 'news_preview_widget','sequence' => '5'),
  array('name' => 'Product Item Detail','widget_code' => 'product_item_widget','sequence' => '6'),
  array('name' => 'Product List','widget_code' => 'product_list_widget','sequence' => '7'),
  array('name' => 'Quiz Item Detail','widget_code' => 'quiz_item_widget','sequence' => '8'),
  array('name' => 'Quiz List','widget_code' => 'quiz_list_widget','sequence' => '9'),
  array('name' => 'Quiz preview','widget_code' => 'quiz_preview_widget','sequence' => '10'),
  array('name' => 'Refer Colleague','widget_code' => 'refer_colleague_form_widget','sequence' => '11'),
  array('name' => 'Training Item Detail','widget_code' => 'training_item_widget','sequence' => '12'),
  array('name' => 'Training List','widget_code' => 'training_list_widget','sequence' => '13'), 
  array('name' => 'Training Preview','widget_code' => 'training_preview_widget','sequence' => '14'),
  array('name' => 'Training resources','widget_code' => 'training_resource_widget','sequence' => '15'),
  array('name' => 'User Activity','widget_code' => 'user_activity_widget','sequence' => '16'),  
  array('name' => 'User Leaderboard','widget_code' => 'user_leaderboard_widget','sequence' => '17'),
  array('name' => 'Badge list page','widget_code' => 'badge_list_widget','sequence' => '18'),
  array('name' => 'Refer Colleague History','widget_code' => 'refer_colleague_history_widget','sequence' => '19'),
);

$this->menu_group_default = array(
  array('menu_name' => 'Main Navigation','primary' => '1','sequence' => '2'),
  array('menu_name' => 'Footer Navigation','primary' => '0','sequence' => '4')
);

$this->default_asset_category = array(
  array('name' => 'Default','path' => 'image','system_generated' => '1','context' => ''),
  array('name' => 'Content','path' => 'content','system_generated' => '1','context' => 'image_widget'),
  array('name' => 'Training','path' => 'training','system_generated' => '1','context' => 'training_default'),
  array('name' => 'Training resource','path' => 'training_resource','system_generated' => '1','context' => 'training_resource'),
  array('name' => 'Quiz icons','path' => 'quiz_icons','system_generated' => '1','context' => 'quiz_icons'),
  array('name' => 'Training video','path' => 'training_video','system_generated' => '1','context' => 'training_video'),
  array('name' => 'News images','path' => 'news_images','system_generated' => '1','context' => 'news_images')
);
$this->default_quiz_type = array(
  array('name' => 'Novice','time_limit' => '5','expiry_period' => '0','tries_per_day' => '3', 'tries_per_week' => '8','points_pre_expiry' => '1000','points_post_expiry' => '1000', 'contest_entries_pre_expiry' => '100', 'contest_entries_post_expiry' => 100),
  array('name' => 'Expert','time_limit' => '5','expiry_period' => '0','tries_per_day' => '3', 'tries_per_week' => '10','points_pre_expiry' => '1000','points_post_expiry' => '1000', 'contest_entries_pre_expiry' => '100', 'contest_entries_post_expiry' => 100)
);
$this->default_pages = array(
  array('name' => 'Home','url' => 'home','author_id' => '46','module' => 'page', 'heading' => 'Home','meta_title' => 'home'),
  array('name' => 'Welcome','url' => 'welcome','author_id' => '46','module' => 'page', 'heading' => 'Welcome','meta_title' => 'Welcome'),
  array('name' => 'Labs','url' => 'labs','author_id' => '46','module' => 'page', 'heading' => 'Labs','meta_title' => 'Labs'),  
  array('name' => 'Browse Labs','url' => 'labs/browse-labs','author_id' => '46','module' => 'page', 'heading' => 'Browse Labs','meta_title' => 'Browse Labs'),  
  array('name' => 'Cheddar Available','url' => 'labs/cheddar-available','author_id' => '46','module' => 'page', 'heading' => 'Cheddar Available','meta_title' => 'Cheddar Available'),  
  array('name' => 'Completed Labs','url' => 'labs/completed-labs','author_id' => '46','module' => 'page', 'heading' => 'Completed Labs','meta_title' => 'Completed Labs'),
  array('name' => 'Promotions','url' => 'promotions','author_id' => '46','module' => 'page', 'heading' => 'Promotions','meta_title' => 'Promotions'),
  array('name' => 'Leaderboad','url' => 'leaderboard','author_id' => '46','module' => 'page', 'heading' => 'Promotions','meta_title' => 'Promotions'),
  array('name' => 'Lab Notes','url' => 'news','author_id' => '46','module' => 'page', 'heading' => 'Lab Notes','meta_title' => 'Lab Notes'),
  array('name' => 'Lab Notes Item','url' => 'news/*','url_parser' => 'news', 'author_id' => '46','module' => 'page', 'heading' => 'Lab Notes Item','meta_title' => 'Lab Notes Item'),
  array('name' => 'Training Detail Page','url' => 'labs/product/*','url_parser' => 'trainig','author_id' => '46','module' => 'page', 'heading' => 'Training Detail Page','meta_title' => 'Training Detail Page'),
  array('name' => 'Quiz Detail Page','url' => 'quiz/*','url_parser' => 'quiz','author_id' => '46','module' => 'page', 'heading' => 'Quiz Detail Page','meta_title' => 'Quiz Detail Page'),
  );
 }
  

 public function check_user($username, $email, $password){

    $this->db->select();

    $where = sprintf("(username = '%s' OR email = '%s') AND password = '%s'",$username, $email, $password);
    $this->db->where($where);
    $query =  $this->db->get('user');

    return $query->row();

 }

  /**
  * get_all_sites() - get all sites from DB order by firts name
  *
  * @param bool active 
  * 
  *  @return object with all sites
  *
  */
  public function get_all_sites($active = false) {

   if($active){
       $this->db->where('active', 1);
   }   
      
    $query = $this->db->order_by('name', 'ASC')->get($this->tables['site'] );
    return $query->result();
  }

  /**
  * getUserById() - get user for DB by id user
  *
  * Get user for DB by id user
  *
  *  @param id user
  *  @return object with one row
  *
  */
  public function get_site_by_id($id) {
    $this->db->select();

    $this->db->where($this->tables['site'] .'.id', $id);
    $site =  $this->db->get($this->tables['site'])->row();
    if (( !empty($site)) && is_object($site)) {
      $site->balance = self::get_point_balance($site->id);
    }
    return $site;
  }

  public function update($id) {
    self::_setElement();

    $ts = time();
    $this->db->set( 'update_timestamp', $ts );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['site']  );
  }
  
  public function update_balance($id) {
    self::_setElement();

    $ts = time();
    $this->db->set( 'update_timestamp', $ts );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['site']  );
  }  

 public function delete_balance_by_id($id) {
    //detele all roles for site
    $this->db->where( 'id', $id );
    if($this->db->delete( $this->tables['brand_points_deposit'])){
        return true;
    }else{
        return false;
    }
  }   
  
  public function delete_by_id($site_id) {
    //detele all roles for site
    $this->db->where( 'site_id', $site_id );
    $this->db->delete( $this->tables['role']  );      
       
    //detele all widgets
    $query = $this->db->select('id')->where('site_id', $site_id )->get($this->tables['module']);
    foreach($query->result() as $module){
      $this->db->where( 'module_id', $module->id );
      $this->db->delete( $this->tables['module_widget']  );             
    }
    
    //detele all modules for site
    $this->db->where( 'site_id', $site_id );
    $this->db->delete( $this->tables['module']  );              
      
    //detele all permissions for site
    $this->db->where( 'site_id', $site_id );
    $this->db->delete( $this->tables['permission']  );        
    
    //TODO delete all records in permission map
    
    //delete pages, pages drafts, pages sections, revisions
    $this->load->model('page/page_model');
    //get all pages id
    $this->db->where( 'site_id', $site_id );
    $query = $this->db->order_by('id', 'ASC')->get($this->tables['page'] );  
    $pages = $query->result();
    foreach($pages as $page){
        $this->page_model->delete_by_id($page->id);
        //$cur_page->delete();
//       
//        $this->db->where( 'id', $page->id );
//        $this->db->delete( $this->tables['page_draft'] );    
//        
//        $this->db->where( 'page_id', $page->id );
//        $this->db->delete( $this->tables['page_draft'] ); 
//      
//        $this->db->where( 'page_id', $page->id );
//        $this->db->delete( $this->tables['page_section'] );         
//        
//        $this->db->where( 'page_id', $page->id );
//        $this->db->delete( $this->tables['page_revision'] );         
    }
    //detele all page layouts for site
    $this->db->where( 'site_id', $site_id );
    $this->db->delete( $this->tables['page_layout']  );       
      
        
    //detele all menu groups
    $this->db->where( 'site_id', $site_id );
    $this->db->delete( $this->tables['menu_group']  );      
    
    //detele all asset categories
    $this->db->where( 'site_id', $site_id );
    $this->db->delete( $this->tables['asset_category']  );      
        
    // delete site
    $this->db->where( 'id', $site_id );
    $this->db->delete( $this->tables['site']  );
    
    return true;
  }

  private function _setElement() {
    // assign values
    $this->db->set( 'name', $this->input->post( 'name' ) );
    $this->db->set( 'domain', $this->input->post( 'url' ) );
    $this->db->set( 'path', $this->input->post( 'path' ) );
    $this->db->set( 'theme', $this->input->post( 'theme' ) );
    $this->db->set( 'primary', $this->input->post( 'primary' ) ? 1 : 0 );
    $this->db->set( 'active', $this->input->post( 'active' ) ? 1 : 0 );
    $this->db->set( 'hidden', $this->input->post( 'hidden' ) ? 1 : 0 );
  }
  

  
  public function insert($id) {
    $ts = time();
    $this->db->set( 'name', $this->input->post( 'name' ) );  
    $this->db->set( 'domain', $this->input->post( 'url' ) );
    $this->db->set( 'path', '' );
    $this->db->set( 'theme', '' );
    $this->db->set( 'site_image_id', '0' );
    $this->db->set( 'update_timestamp', $ts );
    $this->db->set( 'create_timestamp', $ts );
    $this->db->where( 'id', $id );
    $this->db->insert( $this->tables['site']  );
    return $this->db->insert_id();
    
  }

  /**
   * Add default roles for a site.
   * @param  int   $site_id row id in site table
   * @return array site role's row ids in database table
   */
  function add_default_roles($site_id = 1) {
    $site_role_ids = array();
    foreach($this->default_roles as $role_info){
      foreach($role_info as $k => $v){
        $this->db->set($k, $v);            
      }
      $this->db->set('site_id', $site_id);
      $this->db->set('update_date', 'CURRENT_TIMESTAMP', FALSE);
      $this->db->set('create_date', 'CURRENT_TIMESTAMP', FALSE);       
      $this->db->insert($this->tables['role']);
      $site_role_ids[$role_info["name"]] = $this->db->insert_id();
    }
    return $site_role_ids;
  }

  /**
   * Add default permissions to a site.
   * Map permissions with matching roles.
   * @param int   $site_id       row id in site table
   * @param array $site_role_ids row ids in role table
   */
  function add_default_permissions($site_id = 1, $site_role_ids = array()) {
   foreach($this->default_permissions as $permission_info){
       foreach($permission_info as $k => $v){
         if (strcasecmp($k, "map_roles") !== 0) {
          $this->db->set($k, $v);
         }
       }
       $this->db->set('site_id', $site_id);
       $this->db->insert($this->tables['permission']);
       if (isset($permission_info["map_roles"]) && is_array($permission_info["map_roles"]) && ( !empty($permission_info["map_roles"]))) {
         $permission_id = $this->db->insert_id();
         foreach ($site_role_ids as $role_name => $role_id) {
           if (in_array($role_name, $permission_info["map_roles"])) {
             $this->db->insert($this->tables["permission_map"], array("role_id" => $role_id, "permission_id" => $permission_id));
           }
         } 
       }
   }   
  }
  public function add_default_modules($site_id) {    
   foreach($this->default_modules as $module_info){
       foreach($module_info as $k => $v){
         $this->db->set( $k, $v );            
       }
       $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
       $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false );   
       $this->db->set( 'active_date', 'CURRENT_TIMESTAMP', false );       
       $this->db->set( 'site_id', $site_id );
       $this->db->insert( $this->tables['module']  );
   } 
  }
  public function add_default_layouts($site_id) {     
   foreach($this->default_page_layout as $page_layout_info){
       foreach($page_layout_info as $k => $v){
         $this->db->set( $k, $v );            
       }     
       $this->db->set( 'site_id', $site_id );
       $this->db->insert( $this->tables['page_layout']  );
   }    
  }    
  
  public function add_default_module_widgets($site_id) {     
       //load all modules for site to determine module id
       $this->load->model('module/model_module');
       $modules = $this->model_module->get_module_by_site_id($site_id);

   foreach($this->module_widget_default as $widget_default_info){
       foreach($widget_default_info as $k => $v){
           //get module id for widget
           if($k == 'widget_code'){
               
            foreach($modules as $module){
                $aModuleName = explode('_', $module->module_code);
                $widget_code = explode('_', $v);
               if($aModuleName[0] == $widget_code[0]){
                     $this->db->set('module_id', $module->id);
                }
            }
           }
         $this->db->set( $k, $v );            
       }     
       //$this->db->set( 'site_id', $site_id );
       $this->db->insert( $this->tables['module_widget']  );
   }    
  }   
  
  public function add_default_menu_group($site_id) {     
   foreach($this->menu_group_default as $menu_group_info){
       foreach($menu_group_info as $k => $v){
         $this->db->set( $k, $v );            
       }     
       $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
       $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false ); 
       $this->db->set( 'author_id', $this->session->userdata('id') );
       $this->db->set( 'site_id', $site_id );
       $this->db->insert( $this->tables['menu_group']  );
   }    
  }     
  public function add_default_asset_categories($site_id) {
      
      //TODO create upload folder & set permissions
       foreach($this->default_asset_category as $asset_category_info){
       foreach($asset_category_info as $k => $v){
         $this->db->set( $k, $v );            
       }
       $this->db->set( 'site_id', $site_id );
       $this->db->insert( $this->tables['asset_category']  );
   }   
  } 
  
  public function add_default_quiz_type($site_id) {
       foreach($this->default_quiz_type as $quiz_type){
       foreach($quiz_type as $k => $v){
         $this->db->set( $k, $v );            
       }
       $this->db->set( 'site_id', $site_id );
       $this->db->insert( $this->tables['quiz_type']  );
   }   
  }   
  
  public function add_default_pages($site_id) {
      $ts = time();
      //get two column layout for current site id
      $layout_id = self::get_default_page_layout_id($site_id);

       foreach($this->default_pages as $page){
       foreach($page as $k => $v){
         $this->db->set( $k, $v );            
       }
       //set default layout id
       $this->db->set( 'layout_id', $layout_id );
       $this->db->set( 'create_timestamp', $ts ); 
       $this->db->set( 'site_id', $site_id );
       $this->db->insert( $this->tables['page']);
       
       //get new page id to create draft
       $new_page_id = $this->db->insert_id();
       foreach($page as $k => $v){
           //skip module
           if($k == 'module') {continue;}   
         $this->db->set( $k, $v );            
       }   
       $this->db->set( 'layout_id', $layout_id );   
       $this->db->set( 'id', $new_page_id );
       $this->db->insert( $this->tables['page_draft']);
   }   
  } 
  
  /*
   * Get layout id for new site -> two column layout
   * @param int site_id
   * 
   * @return int layout_id
   */
  function get_default_page_layout_id($site_id){
        if($site_id > 0){
          $this->db->select('id');
          $this->db->where('site_id', $site_id);
          $this->db->where('description', 'Right column  70%, left 30%');
          $this->db->limit(1);
          $layout_row = $this->db->get($this->tables['page_layout'])->row();
          return $layout_row->id;
        }
  }
  
  /**
   * Update site image
   * @param  int  badge id
   * @param  int  asset id
   * @return object with one row
   */
  function update_site_image($s_id, $asset_id)
  {

    $query = $this->db->set('site_image_id',$asset_id)
      ->where('id', $s_id)
      ->update($this->tables['site']);
    return TRUE;
  }  

  /**
   * Get the brand point balance.
   * @param  int   $site_id         row id in site table
   * @param  int   $to_timestamp    latest time limit
   * @param  int   $from_timestamp  earliest time limt
   * @param  int   $total_deposit   total deposited points
   * @param  int   $total_withdraw  total withdrew points
   * @param  array $deposits        rows in brand_point_deposit table
   * @param  array $point_withdraws rows in quiz_history table
   * @return int   balance resulted in above parameters
   */
  function get_point_balance($site_id = 0, $to_timestamp = 0, $from_timestamp = 0, $total_deposit = 0, $total_withdraw = 0, $deposits = FALSE, $point_withdraws = FALSE) {
    if ($total_deposit <= 0) {
      try {
        $total_deposit = $this->get_total_deposited_points($site_id, $to_timestamp, $from_timestamp, $deposits);
        if (empty($total_deposit)) {
          $total_deposit = 0;
        }
      } catch(Exception $ex) {
        $total_deposit = 0;
      }
    }
    if ($total_withdraw <= 0) {
      try {
        $total_withdraw = $this->get_total_withdraw_points($site_id, $to_timestamp, $from_timestamp, $point_withdraws);
        if (empty($total_withdraw)) {
          $total_withdraw = 0;
        }
      } catch(Exception $ex) {
        $total_withdraw = 0;
      }
    }
    return $total_deposit - $total_withdraw;
  }

  /**
   * Get total points deposited for brand(s).
   * @param  int $site_id        determine limiting result to a site
   * @param  mix $to_timestamp   determine the latest deposit to be included
   * @param  mix $from_timestamp determine the earliest deposit to be included
   * @return int total points deposited within above parameters
   */
  function get_total_point_deposit($site_id = 0, $to_timestamp = "now", $from_timestamp = FALSE) {
    if (strcasecmp($to_timestamp, "now") === 0) {
      $to_timestamp = time();
    } elseif (is_array($to_timestamp)) {
      if (array_key_exists("from_timestamp", $to_timestamp)) {
        $from_timestamp = $to_timestamp["from_timestamp"];
      } elseif (array_key_exists("from", $to_timestamp)) {
        $from_timestamp = $to_timestamp["from"];
      } elseif (array_key_exists("to_timestamp", $to_timestamp)) {
        $to_timestamp = $to_timestamp["to_timestamp"];
      } elseif (array_key_exists("to", $to_timestamp)) {
        $to_timestamp = $to_timestamp["to"];
      }
    }
    if ($from_timestamp > 0) {
      $this->db->where("deposit_timestamp >=", $from_timestamp);
    }
    if ($to_timestamp > 0) {
      $this->db->where("deposit_timestamp <=", $to_timestamp);
    }
    if ($site_id > 0) {
      $this->db->where("site_id", $site_id);
    }
    return $this->db->select_sum("points")->get($this->tables["brand_points_deposit"])->row()->points;
  }

  /**
   * Get total point withdrawed from brand(s).
   * @param  int $site_id        determine limiting result to a site
   * @param  mix $to_timestamp   determine the latest withdraw to be included
   * @param  mix $from_timestamp determine the earliest withdraw to be included
   * @return int total points withdrawed within above parameters
   */
  function get_total_point_withdraw($site_id = 0, $to_timestamp = "now", $from_timestamp = FALSE) {
    if (strcasecmp($to_timestamp, "now") === 0) {
      $to_timestamp = time();
    } elseif (is_array($to_timestamp)) {
      if (array_key_exists("from_timestamp", $to_timestamp)) {
        $from_timestamp = $to_timestamp["from_timestamp"];
      } elseif (array_key_exists("from", $to_timestamp)) {
        $from_timestamp = $to_timestamp["from"];
      } elseif (array_key_exists("to_timestamp", $to_timestamp)) {
        $to_timestamp = $to_timestamp["to_timestamp"];
      } elseif (array_key_exists("to", $to_timestamp)) {
        $to_timestamp = $to_timestamp["to"];
      }
    }
    if ($from_timestamp > 0) {
      $this->db->where("qh.finish_timestamp >=", $from_timestamp);
    }
    if ($to_timestamp > 0) {
      $this->db->where("qh.finish_timestamp <=", $to_timestamp);
    }
    if ($site_id > 0) {
      $this->db->where("q.site_id", $site_id);
    }
    return $this->db->select_sum("qh.points_earned")->join($this->tables["quiz"]." q", "q.id = qh.quiz_id")->get_where($this->tables["quiz_history"]." qh", array("q.site_id" => $site_id))->row()->points_earned;
  }

  /**
   * Record point deposit for a site.
   * @param  int $site_id the site the points are deposited to
   * @return mix new row id in brand points deposit table if success insert new record, or FALSE otherwise
   */
  function deposit_points($site_id) {
    if ($site_id > 0) {
      $this->db->set("site_id", $site_id);
      $this->db->set("points", $this->input->post("points"));
      $this->db->set("method", $this->input->post("method"));
      $this->db->set("cost", $this->input->post("cost"));
      $this->db->set("balance_before", $this->input->post("balance_before"));
      $this->db->set("user_id", $this->session->userdata("user_id"));
      $this->db->set("deposit_timestamp", time());
      if ($this->db->insert($this->tables["brand_points_deposit"])) {
        return $this->db->insert_id();
      }
    }
    return FALSE;
  }

  /**
   * Get points deposit history
   * @param  int $site_id the site 
   * @return 
   */
  function get_points_deposit_history($site_id) {
      
   if($site_id > 0){
       $this->db->where('site_id', $site_id);
    $query = $this->db->order_by('id', 'DESC')->get($this->tables['brand_points_deposit'] );
    return $query->result();   
    
   }else{   
          return FALSE;
   }

  }

  /**
   * Get points ever deposited
   * @param  int   $site_id        row id in site table
   * @param  int   $to_timestamp   latest time limit
   * @param  int   $from_timestamp earliest time limt
   * @return array point-deposit objects
   */
  function get_point_deposit($site_id = 0, $to_timestamp = 0, $from_timestamp = 0) {
    if (is_array($to_timestamp)) {
      if (array_key_exists("from_timestamp", $to_timestamp)) {
        $from_timestamp = $to_timestamp["from_timestamp"];
      } elseif (array_key_exists("from", $to_timestamp)) {
        $from_timestamp = $to_timestamp["from"];
      } else {
        $from_timestamp = 0;
      }
      if (array_key_exists("to_timestamp", $to_timestamp)) {
        $to_timestamp = $to_timestamp["to_timestamp"];
      } elseif (array_key_exists("to", $to_timestamp)) {
        $to_timestamp = $to_timestamp["to"];
      } else {
        $to_timestamp = 0;
      }
    }
    if (((int) $from_timestamp) > 0) {
      $this->db->where("deposit_timestamp >=", $from_timestamp);
    }
    if (((int) $to_timestamp) > 0) {
      $this->db->where("deposit_timestamp <=", $to_timestamp);
    }
    if (((int) $site_id) > 0) {
      $this->db->where("site_id", $site_id);
    }
    return $this->db->order_by("deposit_timestamp", "desc")->get($this->tables["brand_points_deposit"])->result();
  }

  /**
   * Get total deposited points for brand(s).
   * @param  int   $site_id        row id in site table
   * @param  int   $to_timestamp   latest time limit
   * @param  int   $from_timestamp earliest time limt
   * @param  array $deposits       rows in brand_point_deposit table
   * @return int total points deposited within above parameters
   */
  function get_total_deposited_points($site_id = 0, $to_timestamp = 0, $from_timestamp = 0, $deposits = FALSE) {
    if ( !is_object_array($deposits)) {
      $deposits = $this->get_point_deposit($site_id, $to_timestamp, $from_timestamp);
    }
    $total_deposited_points = 0;
    if (is_object_array($deposits)) {
      foreach ($deposits as $deposit) {
        $total_deposited_points += $deposit->points;
      }
    }
    return $total_deposited_points;
  }

  /**
   * Get points ever withdrawed.
   * @param  int   $site_id        row id in site table
   * @param  int   $to_timestamp   latest time limit
   * @param  int   $from_timestamp earliest time limt
   * @return array point-withdraw objects
   */
  function get_point_withdraw($site_id = 0, $to_timestamp = 0, $from_timestamp = 0) {
    if (is_array($to_timestamp)) {
      if (array_key_exists("from_timestamp", $to_timestamp)) {
        $from_timestamp = $to_timestamp["from_timestamp"];
      } elseif (array_key_exists("from", $to_timestamp)) {
        $from_timestamp = $to_timestamp["from"];
      } else {
        $from_timestamp = 0;
      }
      if (array_key_exists("to_timestamp", $to_timestamp)) {
        $to_timestamp = $to_timestamp["to_timestamp"];
      } elseif (array_key_exists("to", $to_timestamp)) {
        $to_timestamp = $to_timestamp["to"];
      } else {
        $to_timestamp = 0;
      }
    }
    if (((int) $from_timestamp) > 0) {
      $this->db->where("qh.finish_timestamp >=", $from_timestamp);
    }
    if (((int) $to_timestamp) > 0) {
      $this->db->where("qh.finish_timestamp <=", $to_timestamp);
    }
    if (((int) $site_id) > 0) {
      $this->db->where("q.site_id", $site_id)->join($this->tables["quiz"]." q", "q.id = qh.quiz_id");
    }
    try {
      return $this->db->select("qh.id, qh.user_id, qh.points_earned")->distinct()->join($this->tables["user_role"]." ur", "ur.user_id = qh.user_id")->join($this->tables["role"]." r", "r.id = ur.role_id")->order_by("qh.create_timestamp", "desc")->get_where($this->tables["quiz_history"]." qh", array("r.system" => 3))->result();
    } catch (Exception $ex) {
      return array();
    }
  }

  /**
   * Get total withdrew points from brand(s).
   * @param  int   $site_id          row id in site table
   * @param  int   $to_timestamp     latest time limit
   * @param  int   $from_timestamp   earliest time limt
   * @param  array $point_widthdraws rows in quiz_history table
   * @return int total points withdrawed within above parameters
   */
  function get_total_withdraw_points($site_id = 0, $to_timestamp = 0, $from_timestamp = 0, $point_withdraws = FALSE) {
    if ( !is_object_array($point_withdraws)) {
      $point_withdraws = $this->get_point_withdraw($site_id, $to_timestamp, $from_timestamp);
    }
    $total_withdrew_points = 0;
    if (is_object_array($point_withdraws)) {
      foreach ($point_withdraws as $point_withdraw) {
        $total_withdrew_points += $point_withdraw->points_earned;
      }
    }
    return $total_withdrew_points;
  }

  /**
   * Get active labs of a site.
   * @param  int   $site_id row id in site table
   * @return array found active labs
   */
  function get_active_labs($site_id = 1) {
    if ($site_id > 1) {
      $this->db->where("site_id", $site_id);
    }
    try {
      return $this->db->get_where($this->tables["lab"], array("status" => 1))->result();
    } catch (Exception $ex) {
      return array();
    }
  }

  /**
   * Get active quizzes of a site.
   * @param  int   $site_id row id in site table
   * @return array found active quiz
   */
  function get_active_quizzes($site_id = 1) {
    if ($site_id > 1) {
      $this->db->where("site_id", $site_id);
    }
    try {
      return $this->db->get_where($this->tables["quiz"], array("status" => 1))->result();
    } catch (Exception $ex) {
      return array();
    }
  }

  /**
   * Get number of completed quizzes for a site.
   * @param  int   $site_id row id in site table
   * @return array completed quiz objects
   */
  function get_completed_quizzes($site_id = 1) {
    if ($site_id > 1) {
      $this->db->where("q.site_id", $site_id)->join($this->tables["quiz"]." q", "q.id = qh.quiz_id");
    }
    try {
      return $this->db->select("qh.*, t.title AS lab, qt.name AS quiz_type")->distinct()->join($this->tables["user_role"]." ur", "ur.user_id = qh.user_id")->join($this->tables["quiz"]." q", "q.id = qh.quiz_id")->join($this->tables["lab"]." t", "t.id = q.training_id")->join($this->tables["quiz_type"]." qt", "qt.id = q.quiz_type_id")->join($this->tables["role"]." r", "r.id = ur.role_id")->where("qh.finish_timestamp >", 0)->get_where($this->tables["quiz_history"]." qh", array("r.system" => 3))->result();
    } catch (Exception $ex) {
      return array();
    }
  }

  /**
   * Get number of taken quizzes for a site.
   * @param  int   $site_id row id in site table
   * @return array taken quiz objects
   */
  function get_taken_quizzes($site_id = 1) {
    if ($site_id > 1) {
      $this->db->where("q.site_id", $site_id)->join($this->tables["quiz"]." q", "q.id = qh.quiz_id");
    }
    try {
      return $this->db->join($this->tables["user_role"]." ur", "ur.user_id = qh.user_id")->join($this->tables["role"]." r", "r.id = ur.role_id")->get_where($this->tables["quiz_history"]." qh", array("r.system" => 3))->result();
    } catch (Exception $ex) {
      return array();
    }
  }

  /**
   * Get average quiz scores.
   * @param  int    $site_id           row id in site table
   * @param  array  $completed_quizzes rows with finish_timestamp in quiz_history table
   * @return double average correct percent
   */
  function get_quiz_average($site_id = 1, $completed_quizzes = FALSE) {
    if (is_object_array($completed_quizzes)) {
      $total_score = 0;
      foreach ($completed_quizzes as $quiz_result) {
        $total_score += $quiz_result->correct_percent;
      }
      return ($total_score / count($completed_quizzes));
    } else {
      if ($site_id > 1) {
         $this->db->where("q.site_id", $site_id)->join($this->tables["quiz"]," q", "q.id = qh.quiz_id");
      }
      try {
        return $this->db->select_avg("qh.correct_percent")->where("qh.finish_timestamp >", 0)->get($this->tables["quiz_history"]." qh")->row()->correct_percent;
      } catch (Exception $ex) {
        return 0;
      }
    }
  }

  /**
   * Get the withdrew poiints worth in dollar.
   * @param  int   $site_id   row id in site table
   * @param  int   $timestamp unix timestamp
   * @param  array $deposits  rows in brand_point_deposit_table
   * @return float dollar worth per point
   */
  function get_withdrew_points_worth($site_id = 1, $timestamp = 0, $deposits = FALSE) {
    if (($site_id < 2) OR ($timestamp < 1)) {
      return 0;
    }
    if ( !is_object_array($deposits)) {
      $deposits = $this->get_point_deposit($site_id);
    }
    if ( !is_object_array($deposits)) {
      return 0;
    }
    $total_withdrew = $this->get_total_withdraw_points($site_id, $timestamp);
    if ($total_withdrew <= 0) {
      return 0;
    }
    $withdrew_deposit = FALSE;
    $total_deposit = 0;
    foreach ($deposits as $deposit) {
      $total_deposit += $deposit->points;
      if ($total_deposit >= $total_withdrew) {
        $withdrew_deposit = $deposit;
        break;
      }
    }
    if ($withdrew_deposit === FALSE) {
      return 0;
    } else {
      return ($withdrew_deposit->cost / $withdrew_deposit->points);
    }
  }  
  
  /**
   * Get all modules for site ID
   * @param  int $site_id the site 
   * @return 
   */
  function get_site_modules($site_id) {
   if($site_id > 0){
    $this->db->where('site_id', $site_id);
    $query = $this->db->order_by('id', 'DESC')->get($this->tables['module'] );    
    return $query->result();   
    
   }else{   
          return FALSE;
   }
  }    
  
  /**
   * Active/deactive module for site
   * @param  int $module_id 
   * @return 
   */  
  
  function site_module_activation($module_id){
      $ts = date('Y-m-d H:i:s');
      //check old value
      $this->db->where('id', $module_id);
      $query = $this->db->get($this->tables['module']);   
      $active = $query->row()->active;
      if ($active == 1 ){
        $this->db->set( 'active', 0 );
        $this->db->set( 'update_date', $ts );
        $this->db->where( 'id', $module_id );
        $this->db->update( $this->tables['module']  );
        return 'deactivated';
      } else {
        $this->db->set( 'active', 1 );
        $this->db->set( 'update_date', $ts );
        $this->db->set( 'active_date', $ts );
        $this->db->where( 'id', $module_id );
        $this->db->update( $this->tables['module']  );          
        return 'activated';
      }
            //die(var_dump($module_id));
  }
}
?>
