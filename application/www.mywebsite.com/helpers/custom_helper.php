<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * HotCMS Custom helper
 *
 * Some customized functions to be used cross the site
 */

/**
 * Convert a string into URL format
 * leaving only alphanumeric characters(all in lower case), forward slashes, and astrisks; replace all others with dashes
 * @param  string  a name/title
 * @return string
 */
if (!function_exists('format_url'))
{
  function format_url($name) {
    $url = strtolower(trim($name));
    // remove single qutation marks
    $url = str_replace(array("'"), "", $url);
    // replace all other characters with dashes
    $url = preg_replace("/[^A-Za-z0-9\/\*]/", "-", $url);
    // replace empty spaces and redundant dashes
    $url = str_replace(array(" ", "----", "---", "--"), "-", $url);
    // remove leading and trailing dashes
    $url = trim($url, "-");
    return $url;
  }
}

/**
 * Check if the current user has certain permission
 * @param  string  permission to be checked
 * @return bool
 */
if (!function_exists('has_permission'))
{
  function has_permission($permission) {
    $CI =& get_instance();
    //$permissions = $CI->session->userdata('permissions');
    $user_id = (int)($CI->session->userdata('user_id'));
    $permissions = $CI->permission->get_user_permissions($user_id);
    if (is_array($permissions)) {
      return (in_array($permission, $permissions) || in_array('super_admin', $permissions));
    }
    return FALSE;
  }
}

/**
 * Get current timestamp from database
 * @return int
 */
if (!function_exists('current_db_timestamp'))
{
  function current_db_timestamp()
  {
    $CI =& get_instance();
    $CI->load->model('model__global');
    return $CI->model__global->get_db_timestamp();
  }
}

/**
 * List all provinces
 * @param  string  country code
 * @return array of objects
 */
if (!function_exists('list_provinces'))
{
  function list_provinces($country_code = '')
  {
    $CI =& get_instance();
    $CI->load->model('model__global');
    return $CI->model__global->list_provinces($country_code);
  }
}

/**
 * List all provinces in an array
 * @param  string  country code
 * @return array
 */
if (!function_exists('list_province_array'))
{
  function list_province_array($country_code = '')
  {
    $result = array();
    $provinces = list_provinces($country_code);
    if ($country_code > '') {
      foreach ($provinces as $row) {
        $result[$row->province_code] = $row->province_name;
      }
    }
    else {
      foreach ($provinces as $row) {
        $result[$row->province_code] = $row->country_code . ' - ' . $row->province_name;
      }
    }
    return $result;
  }
}

/**
 * List all provinces in an array using Ajax
 * The URL to get here is /ajax/global/provinces/country_code
 * @param  string  country code
 * @return array
 */
if (!function_exists('global_provinces_ajax'))
{
  function global_provinces_ajax($country_code = '')
  {
    $json = array(
      'result' => FALSE,  // mandatory for all JSON output
      'messages' => '',   // mandatory for all JSON output
      'provinces' => '',  // dynamic output parameter, include when needed
    );
    $provinces = list_provinces($country_code);
    $json['result'] = TRUE;
    $json['provinces'] = array();
    if ($country_code > '') {
      foreach ($provinces as $row) {
        $json['provinces'][$row->province_code] = $row->province_name;
      }
    }
    else {
      foreach ($provinces as $row) {
        $json['provinces'][$row->province_code] = $row->country_code . ' - ' . $row->province_name;
      }
    }
    return $json;
  }
}

/**
 * List all countries
 * @return array of objects
 */
if (!function_exists('list_countries'))
{
  function list_countries()
  {
    $CI =& get_instance();
    $CI->load->model('model__global');
    return $CI->model__global->list_countries();
  }
}

/**
 * List all country in an array
 * @return array
 */
if (!function_exists('list_country_array'))
{
  function list_country_array()
  {
    $result = array();
    $countries = list_countries();
    foreach ($countries as $row) {
      $result[$row->country_code] = $row->country;
    }
    return $result;
  }
}

/**
 * Initialize and return default paginate configurations
 * @return array
 */
if (!function_exists('pagination_configuration'))
{
  function pagination_configuration() {
    $config = array();
    // default paginate configurations
    $config['base_url'] = '';   // mandatory
    $config['total_rows'] = 0;  // mandatory
    $config['uri_segment'] = 4; // mandatory
    $config['per_page'] = 10;
    $config['num_links'] = 9;
    $config['use_page_numbers'] = TRUE;
    $config['first_link'] = '&laquo;';
    $config['first_tag_open'] = '<span class="first_link">';
    $config['first_tag_close'] = '</span>';
    $config['last_link'] = '&raquo;';
    $config['last_tag_open'] = '<span class="last_link">';
    $config['last_tag_close'] = '</span>';
    $config['next_link'] = '&gt;';
    $config['next_tag_open'] = '<span class="next_link">';
    $config['next_tag_close'] = '</span>';
    $config['prev_link'] = '&lt;';
    $config['prev_tag_open'] = '<span class="prev_link">';
    $config['prev_tag_close'] = '</span>';
    $config['cur_tag_open'] = '<span class="current">';
    $config['cur_tag_close'] = '</span>';
    $config['num_tag_open'] = '<span class="page_link">';
    $config['num_tag_close'] = '</span>';
    return $config;
  }
}

/**
 * Generate google analytics code
 */
if (!function_exists('google_analytics'))
{
  function google_analytics()
  {
    $CI =& get_instance();
    $site_ga = $CI->config->item('site_ga');
    if (!empty($site_ga)) {
      return sprintf("  <script type='text/javascript'>
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '%s']);
    _gaq.push(['_trackPageview']);
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>
  ", $site_ga);
    }
  }
}

/**
 * Format a string as phone number. eg. (604) 123-4567
 */
if (!function_exists('format_phone_number'))
{
  function format_phone_number($number)
  {
    return preg_replace("/^[\+]?[1]?[- ]?[\(]?([1-9][0-9][0-9])[\)]?[- ]?([1-9][0-9]{2})[- ]?([0-9]{4})$/", "(\\1) \\2-\\3", $number);
  }
}

/**
 * Extract numbers from a string, keep only numbers
 * useful to get phone numbers 6041234567 from (604) 123-4567
 * @param string
 * @return string
 */
if (!function_exists('extract_number'))
{
  function extract_number($string)
  {
    $number = '';
    for ($i=0; $i<strlen($string); $i++) {
      if (in_array(substr($string, $i, 1), array('0','1','2','3','4','5','6','7','8','9'))) {
        $number .= substr($string, $i, 1);
      }
    }
    return $number;
  }
}


/**
 * List canadian provinces
 * @return array
 */
if (!function_exists('canadian_provinces'))
{
  function canadian_provinces() {
    $provinceCode = array(
      'AB' => 'Alberta',
      'BC' => 'British Columbia',
      'MB' => 'Manitoba',
      'NB' => 'New Brunswick',
      'NL' => 'Newfoundland and Labrador',
      'NS' => 'Nova Scotia',
      'ON' => 'Ontario',
      'PE' => 'Prince Edward Island',
      'SK' => 'Saskatchewan',
      'NT' => 'Northwest Territories',
      'NU' => 'Nunavut',
      'QC' => 'Quebec',
      'YT' => 'Yukon'
    );
    return $provinceCode;
  }
}

/**
 * Build page URL
 */
if (!function_exists('build_page_url'))
{
  function build_page_url( &$aURLFull, $row, $aPage) {
    // if valid parent id...
    if (!empty( $row->nParentID ) && !empty( $aPage[$row->nParentID] )) {
      // assign parent page
      $row = $aPage[$row->nParentID];
      // assign url
      $aURLFull[$row->nPageID] = $row->sURL;
      // if parent id is not zero...
      if (!empty( $row->nParentID )) {
        build_page_url( $aURLFull, $row, $aPage );
      } else { return; }
    } else { return; }
  }
}

/**
 * Set full URL
 */
if (!function_exists('set_full_url'))
{
  function set_full_url( &$row, $aPage ) {
    // initialize array
    $aURLFull = array();
    $aURLFull[$row->nPageID] = $row->sURL;
    // recurse menu
    build_page_url( $aURLFull, $row, $aPage );
    /// assign values
    $row->sURLFull    = implode( '/', array_reverse( $aURLFull ) );
    $row->aBreadCrumb = array_reverse( array_keys( $aURLFull ) );
  }
}

/**
 * Generate site menu and sitemap
 * @param object  the current page object
 */
if (!function_exists('populate_menus'))
{
  function populate_menus( $oPage = NULL )
  {
    $CI =& get_instance();
    $site_domain = $CI->config->item('site_domain');
    $CI->load->model('model__global', 'model');

    $aPage = array();
    $aData = array();
    foreach ($CI->model->select_result( 'dPage' ) as $row) {
      // filter asset links
      $row->sContent = str_replace( '../application/' . $site_domain . '/', '', $row->sContent );
      // assign row
      $aPage[$row->nPageID] = $row;
    }

    // fetch menu info
    $aData['aMenu'] = $CI->model->select_result( 'dPage', 5 );

    // assign full url
    foreach ($aData['aMenu'] as $row) { set_full_url( $row, $aPage ); }

    // assign branch
    if ($oPage) {
      foreach ($aData['aMenu'] as $row) {
        // if same top level as current page
        if (is_array($row->aBreadCrumb) && is_array($oPage->aBreadCrumb)
          && count($row->aBreadCrumb) && count($oPage->aBreadCrumb)
          && $row->aBreadCrumb[0] == $oPage->aBreadCrumb[0]) {
          $aData['aBranch'][] = $row;
        }
      }
    }

    // sort website hierarchy
    $CI->hierarchy->sort( $aData['aMenu'], 'nPageID' );

    // output menu in a pre-formated string
    $MAX_TOP_LEVEL = 8;
    $count         = 0;
    $countTopLevel = 1;
    $levelPrev     = 0;
    $aSubMenus = $aData['aMenu'];
    $sMenuAll = $sMenuTop = '<ul>' . "\n";
    $sMenuSubs = '';
    $aXML = array();
    foreach ($aData['aMenu'] as $item) {
      // count top level items
      if ($item->nLevel == 0) { $countTopLevel++; }
      // assign values
      if (!empty( $item->bHasChildren )){
      	$hasChildren = true;
      }else{
      	$hasChildren = false;
      }
      //$hasChildren     = !empty( $item->bHasChildren )                         ? true : false;
      $hasContent      = !empty( $item->sContent ) || !empty( $oModule->view ) ? true : false;
      if ($oPage) {
        $isCurrent = $item->sURL == $oPage->sURL ? true : false;
        $isCurrentBranch = in_array( $item->nPageID, $oPage->aBreadCrumb ) ? true : false;
      }
      else {
        $isCurrent = FALSE;
        $isCurrentBranch = FALSE;
      }
      if ($item->nLevel == 0) {
        if ($item->sURLFull == 'home') {
          $item->sURLFull = '';
        }
        $sMenuTop .= '<li class="' . ($item->bIsMainNav ? 'main' : 'subNav') . (($item->nLevel == 0 && empty( $item->bIsMainNav )) || !empty( $item->bIsHidden ) ? ' hidden' : '')
          . ($item->nLevel == 0 && $countTopLevel == //$MAX_TOP_LEVEL ? ' last' : '') . ($isCurrentBranch ? ' current' : '') .'">';
        	$MAX_TOP_LEVEL ? ' lasttext' : '') . ($isCurrentBranch ? ' current' : '') .'">';
        $sMenuTop .= '<a id="'.str_replace('-','_',$item->sURL).'" class="'. ($hasChildren ? 'hasChildren' : '') . ($isCurrentBranch ? ' current' : '') . (preg_match( '/^https?:\/\//', $item->sURLRedirect ) ? ' external' : '') . '" href="' . (preg_match( '/^https?:\/\//', $item->sURLRedirect ) == 0 ? '/' : '') . (!empty( $item->sURLRedirect ) ? $item->sURLRedirect : $item->sURLFull) . '">' . htmlentities( $item->sName ) . '</a>';
        $sMenuAll .= '<li class="' . ($item->bIsMainNav ? 'main' : 'subNav') . (($item->nLevel == 0 && empty( $item->bIsMainNav )) ? ' hidden' : '') . ($item->nLevel == 0 && $countTopLevel == $MAX_TOP_LEVEL ? ' last' : '') . ($isCurrentBranch ? ' current' : '') .'">';
        $sMenuAll .= '<a class="'. ($hasChildren ? 'hasChildren' : '') . ($isCurrentBranch ? ' current' : '') . (preg_match( '/^https?:\/\//', $item->sURLRedirect ) ? ' external' : '') . '" href="' . (preg_match( '/^https?:\/\//', $item->sURLRedirect ) == 0 ? '/' : '') . (!empty( $item->sURLRedirect ) ? $item->sURLRedirect : $item->sURLFull) . '">' . htmlentities( $item->sName ) . '</a>';
        $aXML[] = (preg_match( '/^https?:\/\//', $item->sURLRedirect ) == 0 ? '/' : '') . (!empty( $item->sURLRedirect ) ? $item->sURLRedirect : $item->sURLFull);
        if (!empty( $item->bHasChildren )) {
          $sMenuSubs .= '<ul id="listSubmenu_'.str_replace('-','_',$item->sURL).'" style="display:none;">' . "\n";
          $sMenuAll .= '<ul>' . "\n";
          foreach ($aSubMenus as $sub) {
            if ($oPage) {
              $isCurrent = $sub->nPageID == $oPage->nPageID ? true : false;
              $isCurrentBranch = in_array( $sub->nPageID, $oPage->aBreadCrumb ) ? true : false;
            }
            else {
              $isCurrent = FALSE;
              $isCurrentBranch = FALSE;
            }
            if ($sub->nParentID == $item->nPageID){
              $sMenuSubs .= '<li class="subNav' . ($isCurrentBranch ? ' current' : '') .'"><a id="'.str_replace('-','_',$sub->sURL).'" class="'. ($isCurrentBranch ? 'current' : '') . (preg_match( '/^https?:\/\//', $sub->sURLRedirect ) ? ' external' : '') . '" href="' . (preg_match( '/^https?:\/\//', $sub->sURLRedirect ) == 0 ? '/' : '') . (!empty( $sub->sURLRedirect ) ? $sub->sURLRedirect : $sub->sURLFull) . '">' . htmlentities( $sub->sName ) . '</a></li>' . "\n";
              $sMenuAll .= '<li class="subNav' . ($isCurrentBranch ? ' current' : '') .'"><a class="'. ($isCurrentBranch ? 'current' : '') . (preg_match( '/^https?:\/\//', $sub->sURLRedirect ) ? ' external' : '') . '" href="' . (preg_match( '/^https?:\/\//', $sub->sURLRedirect ) == 0 ? '/' : '') . (!empty( $sub->sURLRedirect ) ? $sub->sURLRedirect : $sub->sURLFull) . '">' . htmlentities( $sub->sName ) . '</a></li>' . "\n";
              $aXML[] = (preg_match( '/^https?:\/\//', $sub->sURLRedirect ) == 0 ? '/' : '') . (!empty( $sub->sURLRedirect ) ? $sub->sURLRedirect : $sub->sURLFull);
            }
          }
          $sMenuSubs .= "</ul>\n";
          $sMenuAll .= "</ul>\n";
        }
        $sMenuTop .= '</li>' . "\n";
        $sMenuAll .= '</li>' . "\n";
      }
      $levelPrev = $item->nLevel;
      $count++;
    }
    // hard code footer menus into XML
    $aXML[] = "/disclaimer";
    $aXML[] = "/privacy-policy";
    $aXML[] = "/contact-us";

    $sMenuAll .= "</ul>\n";
    $sMenuTop .= "</ul>\n";
    $aData['sMenuTop'] = $sMenuTop;
    $aData['sMenuSubs'] = $sMenuSubs;
    $aData['sMenuAll'] = $sMenuAll;
    $aData['sMenuXml'] = $aXML;
    return $aData;
  }
}

/**
 * Display page not found with a site map
 * @access public
 * @param  string  page URL
 * @return void
 */
if (!function_exists('hotcms_404'))
{
  function hotcms_404( $page = '' )
  {
		$heading = "404 Page Not Found";
		$message[] = "You&rsquo;ve landed on a page that does not exist. Click on one of the links below to get back on track.";
    $menus = populate_menus();
		$message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';

    if ($page > '') {
      log_message('error', '404 Page Not Found --> ' . $page);
    }
		set_status_header(404);

		ob_start();
    // TODO: use a 404 page template in theme folder
		//include(APPPATH.'errors/hotcms_404.php');
		include(APPPATH.'errors/error_404.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
    exit;
  }
}

/**
  * compare site objects for sorting
  * @param object $a
  * @param object $b
  * @return bool
  */
if (!function_exists('compare_sites'))
{
  function compare_sites($a, $b)
  {
    if ($a->primary != $b->primary) {
      return $b->primary - $a->primary;
    }
    else {
      return strcmp($a->name, $b->name);
    }
  }
}

/**
 * strip the extension from a file name
 *
 * @param  string $name filename
 * @return string filename without extension
 */
if (!function_exists('strip_extension'))
{
  function strip_extension($name)
  {
    $ext = strrchr($name, '.');
    if ($ext !== FALSE) {
      $name = substr($name, 0, -strlen($ext));
    }
    return $name;
  }
}

/**
 * get the extension from a file name
 *
 * @param    string $name filename
 * @return   string filename without extension
 */
if (!function_exists('get_extension'))
{
  function get_extension($name)
  {
    return substr(strrchr($name, '.'), 1);
  }
}

/* End of file custom_helper.php */
/* Location: ./application/helpers/custom_helper.php */