<?php

class HotCMS_Model extends CI_Model {

  public $site;
  protected $site_id;
  protected static $sites = array();

  public function __construct()
  {
    parent::__construct();
    if (empty(self::$sites)) {
      $query = $this->db->select()->where('active', 1)->get('site');
      $rows = array();
      foreach ($query->result() as $row) {
        $rows[$row->domain] = $row;
//	echo $row->domain."<br/>";
      }
      self::$sites = $rows;
    }
//print_r($_SERVER['HTTP_HOST']);
    if (array_key_exists($_SERVER['HTTP_HOST'], self::$sites) || $_SERVER['HTTP_HOST'] == '66.148.113.94-') {

      $this->site = self::$sites[$_SERVER['HTTP_HOST']];
      $this->site_id = $this->site->id;
      $this->session->set_userdata('siteID', $this->site_id);
      $this->session->set_userdata('siteName', $this->site->name);
      $this->session->set_userdata('siteDomain', $this->site->domain);
      $this->session->set_userdata('sitePath', $this->site->path);
      $this->session->set_userdata('siteURL', $this->site->domain); // deprecated session variable
    }
    else {
      die('Error: site configurations are missing.');
    }
    //if (empty($this->site)) {
    //  $this->site = $this->get_site();
    //}
    //$this->site_id = $this->site->id;
  }

  /**
   * Get site information based on the present domain name
   * @param  str  domain name
   * @param  bool  load the primary site
   * @return object
   */
  public function get_site($domain = '', $primary = FALSE)
  {
    if ($primary) {
      $this->db->where('primary', 1);
    }
    else {
      if ($domain == '') {
        $domain = $_SERVER['HTTP_HOST'];
      }
      $this->db->where('domain', $domain);
    }
    $query = $this->db->where('active', 1)->get('site');
    return $query->row();
  }

  /**
   * list active pages
   */
  public function list_active_pages() {
    $sql = "SELECT * FROM page WHERE active=1 AND site_id = ? ORDER BY parent_id, sequence;";
    $query = $this->db->query($sql, array($this->site_id));
    $rows = $query->result();
    return $rows;
  }

  /**
   * get first child page's URL
   */
  public function get_first_child_url($page_id) {
    $sql = "SELECT url FROM page WHERE active=1 AND parent_id = ? ORDER BY sequence LIMIT 1;";
    $query = $this->db->query($sql, array($page_id));
    $oChild = $query->row();
    if ($oChild && !empty($oChild->url) && $oChild->url > '') {
      return $oChild->url;
    } else {
      return '';
    }
  }

  /**
   * list menu links
   * TODO: only show menu for ACTIVE pages, hide archived page menus, even if the menu itself is not hidden
   * @param str $menu_key  either the menu group ID or menu group name
   * @param bool $showHidden
   * @param str $menu_name
   * @return array
   */
  public function list_menu($menu_key = '', $showHidden = FALSE)
  {
    if (is_numeric($menu_key) && $menu_key > 0) {
      $this->db->where('m.menu_group_id', $menu_key);
    }
    elseif ($menu_key > '') {
      $this->db->where('g.menu_name', $menu_key);
      $this->db->where('g.site_id', $this->site_id);
    }
    else {
      $this->db->where('g.primary', 1);
    }
    if (!$showHidden) {
      $this->db->where('m.hidden', 0);
    }
    $query = $this->db->select()->from('menu m')
      ->join('menu_group g', 'g.id=m.menu_group_id')
      ->where('g.site_id', $this->site_id)
      ->order_by('m.parent_id, m.sequence')
      ->get();
    return $query->result();
//    $sql = "SELECT * FROM menu WHERE menu_group_id = ?";
//    $sql .= " AND path!='page-not-found'";
//    if (!$showHidden) {
//      $sql .= " AND hidden=0";
//    }
//    if ($menu_name > '') {
//      $sql .= " AND hidden=0";
//    }
//    $sql .= " ORDER BY parent_id, sequence";
//    $query = $this->db->query($sql, array($menuGroup));
//    $rows = $query->result();
//    return $rows;
  }

  /**
   * list the main menu of the current front-end website
   * if menu group = 0 retrieve the one that has primary = 1
   * @param int $menuGroup
   * @param bool $showHidden
   * @return array
   */
  public function list_main_menu($menuGroup = 0, $showHidden = FALSE)
  {
    if ($menuGroup > 0) {
      $this->db->where('m.menu_group_id', $menuGroup);
    }
    else {
      $this->db->where('g.primary', 1);
    }
    if (!$showHidden) {
      $this->db->where('m.hidden', 0);
    }
    $query = $this->db->select("m.*, g.id as group_id, g.menu_name")->from('menu m')
      ->join('menu_group g', 'g.id=m.menu_group_id')
      ->where('m.parent_id', '0')
      ->where('g.site_id', $this->site_id)
      ->order_by('m.parent_id, m.sequence')
      ->get();
    return $query->result();
//    $sql = "SELECT * FROM menu WHERE menu_group_id = ?";
//    $sql .= " AND path!='page-not-found'";
//    $sql .= " AND parent_id=0";
//    if (!$showHidden) {
//      $sql .= " AND hidden=0";
//    }
//    $sql .= " ORDER BY parent_id, sequence";
//    $query = $this->db->query($sql, array($menuGroup));
//    $rows = $query->result();
//    return $rows;
  }

  public function list_sub_menu($parent_menu_id = 0, $showHidden = FALSE) {
    $sql = "SELECT * FROM menu WHERE";
    $sql .= " path!='page-not-found'";
    $sql .= " AND parent_id = ?";
    if (!$showHidden) {
      $sql .= " AND hidden=0";
    }
    $sql .= " ORDER BY parent_id, sequence";
    $query = $this->db->query($sql, array($parent_menu_id));
    $rows = $query->result();
    return $rows;
  }

  public function get_menu_item_id($page_id = 0)
  {
    $sql = "SELECT id FROM menu WHERE";
    $sql .= " path!='page-not-found'";
    //main menu
    //$sql .= " AND menu_group_id = 2";
    $sql .= " AND page_id = ?";
    $query = $this->db->query($sql, array($page_id));
    $menu_item_id = $query->row()->id;
    return $menu_item_id;
  }
  /**
   * get single page info
   */
  public function get_page_info($id) {
    $sql = "SELECT * FROM page WHERE id = ?";
    $query = $this->db->query($sql, $id);
    $row = $query->row();
    return $row;
  }

  /**
   * strip the extension from a filename
   * @param    string $name filename
   * @return   string filename without extension
   */
  function strip_extension($name) {
    $ext = strrchr($name, '.');
    if ($ext !== false) {
      $name = substr($name, 0, -strlen($ext));
    }
    return $name;
  }

  /**
   * Get a list of modules that an admin has permission to access
   * @param int $user_id ID of an admin user
   * @return array
   */
  public function list_available_modules($user_id = 0) {
    if ($user_id == 0) {
      $user_id = (int) $this->session->userdata('userID');
    }
    $site_id = (int) $this->session->userdata('siteID');
    // TODO: need to ensure there is a valid site ID uppon user logging in
    if ($site_id == 0) {
      $site_id = 1;
    }
    $query = $this->db->select('m.*,ml.*')
            ->distinct()
            ->join('cms_dLang_Module ml', 'ml.nModuleID=m.nModuleID')
            ->join('cms_jSiteModule AS sm', 'sm.nModuleID=m.nModuleID')
            ->join('cms_dModulePermission mp', 'mp.nModuleID=m.nModuleID')
            ->join('cms_jModuleAccess AS ma', 'ma.nPermissionID=mp.nPermissionID')
            ->join('cms_jUserRole AS ur', 'ur.nRoleID=ma.nRoleID')
            ->where('ur.nUserID', $user_id)
            ->where('ml.sLangCode', 'en')
            ->where('sm.nSiteID', $site_id)
            ->order_by('m.nSequence')
            ->get('cms_dModule m');
    $rows = $query->result();
    // var_dump($this->db->last_query());
    $result = array();
    foreach ($rows as $row) {
      $result[$row->nModuleID] = $row;
    }
    return $result;
  }

  /**
   * Get a list of module categories that an admin has permission to access
   * for HotCMS 2.5 ONLY
   * @param int $user_id ID of an admin user
   * @return array
   */
  public function list_available_module_categories($user_id = 0) {
    if ($user_id == 0) {
      $user_id = (int) $this->session->userdata('userID');
    }
    $query = $this->db->select('m1.*,lm.*')
            ->distinct()
            ->join('cms_dLang_Module lm', 'lm.nModuleID=m1.nModuleID')
            ->join('cms_dModule AS m2', 'm1.nModuleID=m2.nParentID')
            ->join('cms_dModulePermission mp', 'mp.nModuleID=m2.nModuleID')
            ->join('cms_jModuleAccess AS ma', 'ma.nPermissionID=mp.nPermissionID')
            ->join('cms_jUserRole AS ur', 'ur.nRoleID=ma.nRoleID')
            ->where('ur.nUserID', $user_id)
            ->where('lm.sLangCode', 'en')
            ->order_by('m1.nSequence')
            ->get('cms_dModule m1');
    $rows = $query->result();
    $result = array();
    foreach ($rows as $row) {
      $result[$row->nModuleID] = $row;
    }
    return $result;
  }

  /**
   * Check if an admin user has permission to manage a module
   * @param int @user_id
   * @param string $permission module permission name
   * @return bool
   */
  public function check_module_permission($permission, $user_id = 0) {
    $result = FALSE;
    if ($user_id == 0) {
      $user_id = (int) $this->session->userdata('userID');
    }
    if ($user_id > 0 && $permission > '') {
      $permissions = self::list_module_permissions($user_id);
      //var_dump($user_id, $permissions);
      if (in_array($permission, $permissions)) {
        $result = TRUE;
      }
    }
    return $result;
  }

  /**
   * list permissions for an admin user
   * @param int @user_id
   * @return array
   */
  public function list_module_permissions($user_id = 0) {
    $result = array();
    if ($user_id == 0) {
      $user_id = (int) $this->session->userdata('userID');
    }
    if ($user_id > 0) {
      $query = $this->db->select('mp.nPermissionID,mp.sPermission')
              ->join('cms_jModuleAccess AS ma', 'ma.nRoleID = ur.nRoleID')
              ->join('cms_dModulePermission mp', 'mp.nPermissionID = ma.nPermissionID')
              ->where('ur.nUserID', (int) $user_id)
              ->get('cms_jUserRole ur');
      $rows = $query->result();
      foreach ($rows as $row) {
        $result[$row->nPermissionID] = $row->sPermission;
      }
    }
    return $result;
  }

  protected function do_post_request($url, $postdata, $files = null) {
    $data = "";
    $boundary = "---------------------" . substr(md5(rand(0, 32000)), 0, 10);
    //Collect Postdata
    foreach ($postdata as $key => $val) {
      $data .= "--$boundary\n";
      $data .= "Content-Disposition: form-data; name=\"" . $key . "\"\n\n" . $val . "\n";
    }
    $data .= "--$boundary\n";
    //Collect Filedata
    /*
      foreach($files as $key => $file)
      {
      $fileContents = file_get_contents($file['tmp_name']);
      $data .= "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$file['name']}\"\n";
      $data .= "Content-Type: image/jpeg\n";
      $data .= "Content-Transfer-Encoding: binary\n\n";
      $data .= $fileContents."\n";
      $data .= "--$boundary--\n";
      } */
    $params = array('http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: multipart/form-data; boundary=' . $boundary,
            'content' => $data
            ));
    $ctx = stream_context_create($params);
    $fp = fopen($url, 'rb', false, $ctx);
    if (!$fp) {
      throw new Exception("Problem with $url, $php_errormsg");
    }
    $response = @stream_get_contents($fp);
    if ($response === false) {
      throw new Exception("Problem reading data from $url, $php_errormsg");
    }
    return $response;
  }

  protected function serverside_https_request($url) {
    $var = parse_url($url);
    if (( $io = fsockopen('ssl://' . $var['host'], 443, $number, $message, 5) ) !== false) {
      $path = ( substr($var['path'], 0, 1) != '/' ? '/' : '' ) . $var['path'];
      $info = (!empty($var['query']) ? '?' . $var['query'] : '' );
      $data = "GET " . $path . $info . " HTTP/1.0\r\n";
      $data .= "Host: " . $var['host'] . "\r\n";
      $data .= "Connection: Close\r\n\r\n";
      fputs($io, $data);
      $data = '';
      $head = '';
      $stop = 0;
      while (!feof($io)) {
        $data .= fgets($io, 128);
        if ($stop == 0 && strpos($data, "\r\n\r\n") !== false) {
          $stop += 1;
          $head = $data;
          $data = '';
        }
      }
      fclose($io);
      // return the result (the header would be in => $head)
      return $data;
    } else {
      // show the error, the error type would be in => $number)
      // echo 'socket error:  (message => ' . $message . ')';
      return FALSE;
    }
  }

  protected function _get_soap_client($transactionId = '') {
    $ns = 'http://www.my_soap_server.com';
    $wsdl = $_SERVER['DOCUMENT_ROOT'] . "/application/www.mywebsite.com/libraries/MySoapWebService.wsdl";
    // sandbox configuration
    $options = array('location' => 'https://www.my_soap_server.com:staging/webservices/MySoapWebService',
        'uri' => $ns,
        'soap_version' => "SOAP_1_1",
        'trace' => 1,
        "exceptions" => 0,
        'login' => "soapuser",
        'password' => "soappw",
        'connection_timeout' => 120);
    // live server configuration
    /*
      $options = array('location' => 'https://www.my_soap_server.com:live/webservices/MySoapWebService',
      'uri'          => $ns,
      'soap_version' => "SOAP_1_1",
      'trace'        => 1,
      "exceptions"   => 0,
      'login'        => "soapuser",
      'password'     => "soappw",
      'connection_timeout' => 120 );
     */

    $client = new SoapClient($wsdl, $options);

    //Create Soap Header.
    if ($transactionId == '') {
      $transactionId = self::get_random_string(20, TRUE);
    }
    $headers = array();
    $headers[] = new SOAPHeader($ns, 'Product', 'MYPRODUCT');
    $headers[] = new SOAPHeader($ns, 'Source', 'WEB');
    $headers[] = new SOAPHeader($ns, 'TransactionId', $transactionId);
    $client->__setSoapHeaders($headers);
    return $client;
  }

  protected function get_random_string($length = 20, $uppercase = FALSE, $noconfusion = FALSE) {
    if ($noconfusion) {
      // remove 0 and o, 1 and i, l so there is no confusion
      $characters = '23456789abcdefghjkmnpqrstuvwxyz';
    } else {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    }
    $ranmax = strlen($characters) - 1;
    $string = '';
    for ($p = 0; $p < $length; $p++) {
      $string .= $characters[mt_rand(0, $ranmax)];
    }
    if ($uppercase) {
      $string = strtoupper($string);
    }
    return $string;
  }

}

?>
