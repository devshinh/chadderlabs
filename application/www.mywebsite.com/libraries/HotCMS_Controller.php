<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class HotCMS_Controller extends CI_Controller {

  protected $aData = array();
  protected $aModule = array();
  protected $aPage = array();
  protected $aURL = array();
  protected $aError = array();
  protected $oModule = null;
  protected $isSorted = true;
  protected $user_id = 0;
 // protected $user_points = 0;
  protected $user_roles = array();
  //protected $user_permissions = array();
  protected $environment;

  public function __construct()
  {
    parent::__construct();
    //$this->load->library('permission');
    $this->lang->load('hotcms');
    //$this->CI =& get_instance();

    // load firephp debug library
    $this->load->config('fireignition');
    if ($this->config->item('fireignition_enabled')) {
      if (floor(phpversion()) < 5) {
        log_message('error', 'PHP 5 is required to run fireignition');
      } else {
        $this->load->library('FirePHP');
      }
    }
    else {
      $this->load->library('FirePHP_Fake');
      $this->firephp = & $this->firephp_fake;
    }

    $this->user_id = (int)($this->session->userdata('user_id'));
    $this->user_roles = $this->permission->get_user_roles($this->user_id);
    //$this->user_permissions = $this->permission->get_user_permissions($this->user_id);
    $this->environment = $this->config->item('environment');

    //load menu item
    $this->load->library('MenuItem');

    // load global model
    $this->load->model('model__global', 'model');
    if($this->user_id > 0){
        $this->aData['userpoints'] = $this->account_model->get_user_points($this->user_id);
        $this->aData['userdraws'] = $this->account_model->get_user_draws($this->user_id);
    }
    // set defaults
    //if (!$this->session->userdata( 'langCode' )) { $this->session->set_userdata( 'langCode', $this->config->item( 'language' ) ); }
    // determine which site this is and load site configurations from database
    $site = $this->model->site;
    if (empty($site)) {
      die('Sorry but the site configurations are missing.');
    }
    // load site-wide configurations
    $this->config->set_item('theme', $site->theme); // overwrite the default one
    $this->config->set_item('site_name', $site->name);
    $this->config->set_item('site_domain', $site->domain);
    $this->config->set_item('base_url', 'http://' . $site->domain); // overwrite the default one
    $this->aData['sTheme'] = $site->theme;
    $this->aData['sSiteName'] = $site->name;
    $this->aData['sSiteDomain'] = $site->domain;
    $this->aData['sSiteID'] = $site->id;
    $aSite = explode('.',$site->domain); 
    $this->aData['sMainDomain'] = 'www.'.$aSite[1].'.'.$aSite[2];
    $this->aData['sDomain'] = $aSite[1].'.'.$aSite[2];
  }
/*
  protected function setInfo($aModuleSegment = null, $oModuleData = null, $hasView = true) {
    // initialize variables
    $module = '';
    $url = '';

    // if this page has an associated module...
    if (!empty($aModuleSegment)) {
      // assign module identifier
      $module = trim(array_pop($aModuleSegment), '_');
      // assign uri string / page url
      $url = implode('/', $aModuleSegment);
    } else {
      // assign uri value
      //$url = $this->uri->uri_string() != '' ? trim( $this->uri->uri_string(), '/' ) : $this->router->default_controller;
      $url = $this->uri->uri_string() != '' ? trim($this->uri->uri_string(), '/') : 'home';
    }

    // loop url... to determine root
    $pageID = 0;
    $urlRoot = $url;
    if ($urlRoot == "" || $urlRoot == "home") {
      $pageID = $this->aURL[$urlRoot];
    } else {
      while ($urlRoot != '') {
        if (!empty($this->aURL[$urlRoot])) {
          $pageID = $this->aURL[$urlRoot];
          break;
        } else {
          $urlRoot = substr($urlRoot, 0, -1);
        }
      }
    }

    // if valid page...
    if (!empty($pageID)) {

      // assign page
      $page = $this->aPage[$pageID];

      // set page/image info
      self::setPageInfo($page);

      // if flag, remove page content
      if (!empty($oModuleData->hasNoContent)) {
        $this->aData['oPage']->content = null;
      }

      // if module exists yet not assigned to this page...
//      if (!empty( $module ) && $this->aModule[$module]->nModuleID != $page->nModuleID) { $module = ''; }
      // if associated module...
      if (empty($module) && !empty($page->nModuleID)) {
        // fetch module
        $oModule = $this->model->select_row('dModule', 1, array($page->nModuleID));
        // if valid module...
        if (!empty($oModule)) {
          redirect($url . '/_' . $oModule->name);
        }
      }

      // set additional info
      self::loadMenu();
      //self::setAnalyticsInfo();
      // if homepage... set info
      if ($page->url == $this->router->default_controller) {
        self::setHomepageInfo();
      }

      self::loadView($module, $oModuleData, $hasView);
      //die('ready');
    } else {
      redirect('page-not-found');
    }
  }
*/
  /**
   * Load module view
   * @param array $aModule
   * @param array $aModuleData
   * @param str $sMethodName
   * @param bool $hasView
   */
  protected function loadModuleView($aModule = NULL, $aModuleData = NULL, $sMethodName = 'index', $hasView = TRUE)
  {
    if (!empty($aModule)) {
      $oModule = self::array2Object($aModule);

      // simulate a page object for the menus, meta tags, etc.
      $aPage = $aModule;
      $aPage['aBreadCrumb'] = array();
      // override attributes for each method
      foreach ($aPage as $k => $v) {
        if (array_key_exists($k, $aModuleData) && $aModuleData[$k] > '') {
          $aPage[$k] = $aModuleData[$k];
        }
      }
      if (!array_key_exists('id', $aPage)) {
        $aPage['id'] = 0;
      }
      $this->aData['oPage'] = self::array2Object($aPage);
      $this->aData['oPage']->aBreadCrumb = $aPage['aBreadCrumb'];     
      //self::setImageInfo( $aPage['aBreadCrumb'], $oModule );
      // set additional info
      self::loadMenu();
      //self::setAnalyticsInfo();
      // load messages
      $messages = (array)$this->session->userdata('messages');
      if (count($messages) > 0) {
        $aModuleData['messages'] = $messages;
        //remove messages from session
        $this->session->unset_userdata('messages');
      }
      self::loadView($oModule, $aModuleData, $hasView, $sMethodName);
    }
    else {
      redirect('page-not-found');
    }
  }

  protected function setFullURL(&$row) {

    // initialize array
    $aURLFull = array();
    $aURLFull[$row->id] = $row->url;

    // recurse menu
    self::buildPageURL($aURLFull, $row);

    /// assign values
    $row->sURLFull = implode('/', array_reverse($aURLFull));
    $row->aBreadCrumb = array_reverse(array_keys($aURLFull));
  }

  protected function buildPageURL(&$aURLFull, $row) {

    // if valid parent id...
    if (!empty($row->parent_id) && !empty($this->aPage[$row->parent_id])) {

      // assign parent page
      $row = $this->aPage[$row->parent_id];

      // assign url
      $aURLFull[$row->id] = $row->url;

      // if parent id is not zero...
      if (!empty($row->parent_id)) {
        self::buildPageURL($aURLFull, $row);
      } else {
        return;
      }
    } else {
      return;
    }
  }

  /* TODO: put GA in a separate module, not in library
    protected function setAnalyticsInfo() {
    // load model
    $this->load->model( 'model_analytics_google' );

    // if analytics module(s) exist(s)
    foreach ($this->model->select_result( 'dModule', 2, array( $this->config->item('site_id'), '%analytics_%' ) ) as $row) {

    // fetch analytics code
    $aBind = array( $this->config->item('site_id'), str_replace( 'analytics_', '', $row->sName ) );
    foreach ($this->model_analytics_google->select_result( 'dAnalytics', 0, $aBind ) as $row) { $this->aData['aAnalytics'][$row->sValue] = $row; }
    }
    } */

  /**
   * set image information
   * @param array $aBreadCrumb
   * @param object $row
   * @return object
   */
  protected function setImageInfo(&$aBreadCrumb, $row) {
    // fetch image info
    $row->oImage = $this->model->select_row('dImage', 0, array($this->imageTypeID, $this->moduleID, array_pop($aBreadCrumb)));

    // if no image data...
    if (empty($row->oImage) && count($aBreadCrumb)) {
      self::setImageInfo($aBreadCrumb, $row);
    } else {
      return;
    }
  }

  /**
   * load menus
   */
  protected function loadMenu()
  {
    // fetch main menu info
    $aMainMenu = $this->model->list_main_menu();
    $this->aData['sMainMenu'] = self::renderMenu($aMainMenu, 'main-nav');
    $this->aData['sMobileMenu'] = self::renderMenu($aMainMenu, 'mobile-nav');
    $aFooterMenu = $this->model->list_menu('Footer Navigation');
    $this->aData['sFooterMenu'] = self::renderMenu($aFooterMenu, '');
    $this->aData['s404Menu'] = self::render404Menu($aMainMenu,'menu404');
    $this->aData['s404MenuFooter'] = self::render404Menu($aFooterMenu,'menu404foot');

    //for submenu load just pages with same parent ID
    $a_uri = explode('/', $_SERVER['REQUEST_URI']);
    foreach ($aMainMenu as $item) {
      if ($item->module == "page" || $item->module == "menu") {
        $page = $this->model->get_page_info($item->page_id);
        $link = $page->url;
      } else {
        $link = $item->path;
      }
      if ($link == $a_uri[1]) {
        $currentPageID = $item->page_id;
      }
    }
    if (!empty($currentPageID)) {
      //get main menu item id for page
      $parent_menu_id = $this->model->get_menu_item_id($currentPageID);
      $aSubMenu = $this->model->list_sub_menu($parent_menu_id);
      $this->aData['sSubMenu'] = self::renderMenu($aSubMenu, 'sub-nav');
    }

    // create a global menu that links to the primary website
    $sGlobalMenu = '';
    $sGlobalMobile = '';
    $sMobileGlobal = '';
    $primary_site = $this->model->get_site('', TRUE);
    if ($primary_site) {
      $primary_domain = $primary_site->domain;
      $sGlobalMenu .= '<li><a href="http://' . $primary_domain . '/overview" title="overview">OVERVIEW</a></li>';
      $sGlobalMenu .= '<li><a href="http://' . $primary_domain . '/retailers" title="retailers">RETAILERS</a></li>';
      if($this->ion_auth->logged_in()){
        $sGlobalMenu .= '<li><a href="http://' . $primary_domain . '/training-labs" title="More Training">TRAINING</a></li>';
        $sMobileGlobal .= '<li class="hidden-xs-portrait"><a href="http://' . $primary_domain . '/training-labs" title="More Training">TRAINING</a></li>';
      }
      $sGlobalMenu .= '<li><a href="http://' . $primary_domain . '/about-us" title="Contact">CONTACT</a></li>';
      $sGlobalMenu .= '<li><a href="http://' . $primary_domain . '/shop" title="Shop">SWAG</a></li>';
      $sMobileGlobal .= '<li class="hidden-xs-portrait"><a href="http://' . $primary_domain . '/shop" title="Shop">SWAG</a></li>';
      //TODO: list retailers as submenu when we have more than one retailers, and move this menu into database
      $sGlobalMobile .= '<li><a class="btn btn-link" href="http://' . $primary_domain . '/overview" title="overview">OVERVIEW</a></li>';
      $sGlobalMobile .= '<li><a class="btn btn-link" href="http://' . $primary_domain . '/retailers" title="retailers">RETAILERS</a></li>';
      if($this->ion_auth->logged_in()){
        $sGlobalMobile .= '<li class="visible-xs-portrait"><a class="btn btn-link" href="http://' . $primary_domain . '/training-labs" title="More Training">TRAINING</a></li>';
      }
      $sGlobalMobile .= '<li><a class="btn btn-link" href="http://' . $primary_domain . '/about-us" title="Contact">CONTACT</a></li>';
      $sGlobalMobile .= '<li class="visible-xs-portrait"><a class="btn btn-link" href="http://' . $primary_domain . '/shop" title="Shop">SWAG</a></li>';
      if($this->ion_auth->logged_in()){
        $sGlobalMobile .= '<li><a class="btn btn-link" href="http://' . $primary_domain . '/profile" title="Profile">PROFILE</a></li>';
        $sGlobalMobile .= '<li><a class="btn btn-link" href="http://' . $primary_domain . '/logout" title="Sign Out">SIGN OUT</a></li>';
      }
      $sGlobalMobile .= '<li><a class="btn btn-link" href="http://' . $primary_domain . '/faq" title="FAQ">FAQ</a>';
      $sGlobalMobile .= '<li><a class="btn btn-link" href="http://' . $primary_domain . '/terms-of-use" title="Terms of Use">TERMS OF USE</a></li>';
      $sGlobalMobile .= '<li><a class="btn btn-link" href="http://' . $primary_domain . '/privacy-policy" title="Privacy Policy">PRIVACY POLICY</a></li>';
      $sGlobalMobile .= '<li><button class="btn btn-link" type="button" onclick="toggleGlobalMobile()">CLOSE MENU</button></li>';
    }
    if ($sGlobalMenu > '') {
      $sGlobalMenu = '<ul class="nav" id="global_menu">' . $sGlobalMenu . "</ul>\n";
      $sMobileGlobal .= '<li><button id="global_menu_button" class="btn btn-link" type="button" onclick="toggleGlobalMobile()"><img src="/themes/cheddarLabs/images/icons/btn-cheddarlabs-more.png" alt="Menu" /></button></li>';
      $sMobileGlobal = '<div class="pull-right visible-mobile"><ul class="nav" id="global_menu">' . $sMobileGlobal . "</ul></div>\n";
      $sGlobalMobile = '<ul class="unstyled global-mobile visible-mobile hidden">' . $sGlobalMobile . "</ul>\n";
    }
    $this->aData['sGlobalMenu'] = $sGlobalMenu;
    $this->aData['sMobileGlobal'] = $sMobileGlobal;
    $this->aData['sGlobalMobile'] = $sGlobalMobile;
  }

  /**
   * parse menu into array of MenuItems
   */
  private function parseMenu($aMenu = array(), $parentID = 0) {
    //load menu item library
    $parsed_menu = array();
    $item_count = count($aMenu);
    if ($item_count == 0)
      return $parsed_menu;
    $a_uri = explode('/', $_SERVER['REQUEST_URI']);
    $i = 0;
    foreach ($aMenu as $item) {

      if ($item->parent_id != $parentID) {
        continue;
      }
      $i++;
      $menu_item = new MenuItem();

      if ($item->module == "page") {
        $page = $this->model->get_page_info($item->page_id);
        $link = $page->url;
      } else {
        $link = $item->path;
      }

      // TODO: find out class for each item, such as: current, first, last
      $menu_item->index = $i;
      $menu_item->first = ($item->sequence == '0');
      $menu_item->last = ($i == $item_count);
      $menu_item->current = ($a_uri[1] == $link) || ((empty($a_uri[1])));
      $menu_item->external = $item->external;
      $menu_item->link = $link;
      $menu_item->display = $item->title;
      $menu_item->sub_menu = self::parseMenu($aMenu, $item->id);
      $parsed_menu[] = $menu_item;
    }
    return $parsed_menu;
  }

  /**
   * render menu array into HTML
   * @param array $aMenu
   * @return string
   */
  //private function renderMenu($aMenu = array(), $parentID = 0) {
  private function renderMenu($aMenu = array(), $menuID = '', $parent_item = 0, $parent_group = 0) {
    $sMenu = '';
    if (count($aMenu) == 0) {
      return $sMenu;
    }
    $item_count = count($aMenu);
    $i = 0;
    $class = '';
    $a_uri = explode('/', $_SERVER['REQUEST_URI']);
    if ((strcasecmp($menuID, "mobile-nav") === 0) && ($parent_item > 0)) {
      $sMenu = '<li class="hidden" data-parentid="' . $parent_item . '"><button class="pull-right btn btn-link" type="button" onclick="toggleVerticalMenu(' . $parent_group . ')"><i style="padding-right:30px;text-shadow:none;">Back</i> ›</button></li>';
    }
    foreach ($aMenu as $item) {
        if($item->path=='training-labs' && !$this->ion_auth->logged_in()){
            continue;
        }
        
      $class = '';
      $i++;

      if ($item->module == "page" || $item->module == "menu") {
        $page = $this->model->get_page_info($item->page_id);
        $link = $page->url;
      } else {
        $link = $item->path;
      }
      $parent_link = '';

      if (strcasecmp($menuID, "mobile-nav") !== 0) {
        if ($i == 1) {
          if (strlen($class) > 0) {
            $class .= " first";
          } else {
            $class = "first";
          }
        }
        if ($i == $item_count) {
          if (strlen($class) > 0) {
            $class .= " last";
          } else {
            $class = "last";
          }
        }
      } else {
        if (strlen($class) > 0) {
          $class .= " hidden";
        } else {
          $class = "hidden";
        }
      }

      if (isset($a_uri[1]) && ($a_uri[1] == $link)) {
        if (strlen($class) > 0) {
          $class .= " active";
        } else {
          $class = "active";
        }
      }
      if (($menuID == 'sub-nav') && (isset($a_uri[1]) && (isset($a_uri[2])) && ($a_uri[1].'/'.$a_uri[2] == $link))) {
        if (strlen($class) > 0) {
          $class .= " active";
        } else {
          $class = "active";
        }
      }
      if (strlen(strip_tags($item->title))>14){
        if (strlen($class) > 0) {
          $class .= " twoLines";
        } else {
          $class = "twoLines";
        }          
      }

      if (strcasecmp($menuID, "mobile-nav") === 0) {
        $subMenu = $this->model->list_sub_menu($item->id);
        $subButton = '<span class="pull-left space-holder"></span>';
        if ( !empty($subMenu)) {
          $sMenu .= self::renderMenu($subMenu, 'mobile-nav', $item->id);
          $subButton = '<button class="pull-left btn btn-link" type="button" onclick="toggleVerticalMenu(' . $item->id . ')">‹</button>';
        }
        $subMenu = '';
        $sMenu .= '<li class="' . $class . '" data-parentid="' . $parent_item . '">' . $subButton . '<a href="' . $parent_link . '' . $link . '" title="' . strip_tags($item->title) . '"' . ($item->external == 1 ? ' target="_blank"' : '') . '>';
      } else {
        $sMenu .= '<li class="' . $class . '"> <a href="' . $parent_link . '' . $link . '" class="' . $class . '" title="' . strip_tags($item->title) . '"' . ($item->external == 1 ? ' target="_blank"' : '') . '>';
      }
      $sMenu .= $item->title;
      $sMenu .= '</a>';
      $sMenu .= "</li>\n";
    }
    if ((strcasecmp($menuID, "mobile-nav") !== 0) && ($sMenu > '')) {
      $sMenu = "<ul class='nav' id='" . $menuID . "'>\n" . $sMenu . "</ul>\n";
    }
    return $sMenu;
  }
  
  private function render404Menu($aMenu = array(), $menuID = '', $parent_item = 0, $parent_group = 0) {

    $sMenu = '';
    if (count($aMenu) == 0) {
      return $sMenu;
    }
    $item_count = count($aMenu);
    $i = 0;
    $class = '';
    $a_uri = explode('/', $_SERVER['REQUEST_URI']);
 
    foreach ($aMenu as $item) {
        if($item->path=='training-labs' && !$this->ion_auth->logged_in()){
            continue;
        }
      $class = '';
      $i++;

      if ($item->module == "page" || $item->module == "menu") {
        $page = $this->model->get_page_info($item->page_id);
        $link = $page->url;
      } else {
        $link = $item->path;
      }
      $parent_link = '';

      
        $subMenu = $this->model->list_sub_menu($item->id);

        $subMenu = '';
        $sMenu .= '<li class="' . $class . '"> <a href="' . $parent_link . '' . $link . '" class="' . $class . '" title="' . strip_tags($item->title) . '"' . ($item->external == 1 ? ' target="_blank"' : '') . '>';

       
      $sMenu .= $item->title;
      $sMenu .= '</a>';
      $sMenu .= "</li>\n";
    }
    $sMenu = "<ul class='nav' id='" . $menuID . "'>\n" . $sMenu . "</ul>\n";
    
    return $sMenu;      
      
  }
  /*
    protected function setMenuInfo() {
    // fetch menu info
    $this->aData['aMenu'] = $this->model->list_active_pages();

    // assign full url
    foreach ($this->aData['aMenu'] as $row) { self::setFullURL( $row ); }

    // assign branch
    foreach ($this->aData['aMenu'] as $row) {
    // if same top level as current page
    if (is_array($row->aBreadCrumb) && is_array($this->aData['oPage']->aBreadCrumb)
    && count($row->aBreadCrumb) && count($this->aData['oPage']->aBreadCrumb)
    && $row->aBreadCrumb[0] == $this->aData['oPage']->aBreadCrumb[0]) {
    $this->aData['aBranch'][] = $row;
    }
    }

    // sort website hierarchy
    $this->hierarchy->sort( $this->aData['aMenu'], 'id' );

    // output menu in a pre-formated string
    $MAX_TOP_LEVEL = 8;
    $count         = 0;
    $countTopLevel = 1;
    $levelPrev     = 0;
    $aSubMenus = $this->aData['aMenu'];
    $sMenuAll = $sMenuTop = '<ul>' . "\n";
    //hack to add shopping cart icon as last item in list
    $sMenuTop .= '<li class="main last';
    $sMenuTop .= $this->uri->uri_string() == "/buy-online/my-cart" ? ' current' : '';
    $sMenuTop .= '"><a href="/buy-online/my-cart" id="buy-online-icon">Shopping Cart</a></li>';
    $sMenuSubs = '';
    $aXML = array();
    foreach ($this->aData['aMenu'] as $item) {

    // count top level items
    if ($item->nLevel == 0) { $countTopLevel++; }
    // assign values
    if (!empty( $item->bHasChildren ) || $item->sURL =="promotions" || $item->sURL =="phones"){
    $hasChildren = true;
    }else{
    $hasChildren = false;
    }
    //$hasChildren     = !empty( $item->bHasChildren )                         ? true : false;
    $hasContent      = !empty( $item->sContent ) || !empty( $oModule->view ) ? true : false;
    $isCurrent       = $item->id == $this->aData['oPage']->id                     ? true : false;
    $isCurrentBranch = in_array( $item->nPageID, $this->aData['oPage']->aBreadCrumb )       ? true : false;
    if ($item->nLevel == 0) {
    $sMenuTop .= '<li class="' . ($item->bIsMainNav ? 'main' : 'subNav') . (($item->nLevel == 0 && empty( $item->bIsMainNav )) || !empty( $item->bIsHidden ) ? ' hidden' : '') . ($item->nLevel == 0 && $countTopLevel == //$MAX_TOP_LEVEL ? ' last' : '') . ($isCurrentBranch ? ' current' : '') .'">';
    $MAX_TOP_LEVEL ? ' lasttext' : '') . ($isCurrentBranch ? ' current' : '') .'">';
    $sMenuTop .= '<a id="'.str_replace('-','_',$item->sURL).'" class="'. ($hasChildren ? 'hasChildren' : '') . ($isCurrentBranch ? ' current' : '') . (preg_match( '/^https?:\/\//', $item->sURLRedirect ) ? ' external' : '') . '" href="' . (preg_match( '/^https?:\/\//', $item->sURLRedirect ) == 0 ? '/' : '') . (!empty( $item->sURLRedirect ) ? $item->sURLRedirect : $item->sURLFull) . '">' . htmlentities( $item->sName ) . '</a>';
    $sMenuAll .= '<li class="' . ($item->bIsMainNav ? 'main' : 'subNav') . (($item->nLevel == 0 && empty( $item->bIsMainNav )) ? ' hidden' : '') . ($item->nLevel == 0 && $countTopLevel == $MAX_TOP_LEVEL ? ' last' : '') . ($isCurrentBranch ? ' current' : '') .'">';
    $sMenuAll .= '<a class="'. ($hasChildren ? 'hasChildren' : '') . ($isCurrentBranch ? ' current' : '') . (preg_match( '/^https?:\/\//', $item->sURLRedirect ) ? ' external' : '') . '" href="' . (preg_match( '/^https?:\/\//', $item->sURLRedirect ) == 0 ? '/' : '') . (!empty( $item->sURLRedirect ) ? $item->sURLRedirect : $item->sURLFull) . '">' . htmlentities( $item->sName ) . '</a>';
    $aXML[] = (preg_match( '/^https?:\/\//', $item->sURLRedirect ) == 0 ? '/' : '') . (!empty( $item->sURLRedirect ) ? $item->sURLRedirect : $item->sURLFull);
    if (!empty( $item->bHasChildren )){
    $sMenuSubs .= '<ul id="listSubmenu_'.str_replace('-','_',$item->sURL).'" style="display:none;">' . "\n";
    $sMenuAll .= '<ul>' . "\n";

    foreach ($aSubMenus as $sub){
    $isCurrent       = $sub->nPageID == $this->aData['oPage']->nPageID                     ? true : false;
    $isCurrentBranch = in_array( $sub->nPageID, $this->aData['oPage']->aBreadCrumb )       ? true : false;
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
    //echo !empty( $item->bHasChildren ) ? '<ul class="subNav">' : '</li>';
    //echo "\n";
    //if ($count == count( $aMenu ) - 1) { for ($i = 0; $i < $item->nLevel; $i++) { echo "</ul></li>\n"; } }
    }
    $levelPrev = $item->nLevel;
    $count++;
    }
    $sMenuAll .= "</ul>\n";
    $sMenuTop .= "</ul>\n";
    $this->aData['sMenuTop'] = $sMenuTop;
    $this->aData['sMenuSubs'] = $sMenuSubs;
    $this->aData['sMenuAll'] = $sMenuAll;
    $this->aData['sMenuXml'] = $aXML;
    }
   */

  protected function setPageInfo($row) {
    // set page/image info
    $aBreadCrumb = $row->aBreadCrumb;
    //self::setImageInfo( $aBreadCrumb, $row );
    // if no content...
    if (empty($row->content)) {
      // fetch first child page's URL
      $childUrl = $this->model->get_first_child_url($row->id);
      // if child...
      if ($childUrl > '') {
        // redirect to first child
        redirect($row->sURLFull . '/' . $childUrl);
      } else {
        $this->aData['oPage'] = $row;
      }
    } else {
      $this->aData['oPage'] = $row;
    }
  }

  protected function initModule($oClass, $model = null) {

    // assign module name
    $module = str_replace('module_', '', strtolower(get_class($oClass)));

    // if not logged in...
    if ($module != 'dashboard' && !$this->session->userdata('userID')) {
      redirect();
    }

    // load model
    $this->load->model('../modules/' . (!empty($model) ? $model : $module) . '/models/model_' . (!empty($model) ? $model : $module), 'model');

    // fetch module info
    $this->oModule = $this->model->select_row('dModule', 5, array($this->session->userdata('langCode'), $module));
    //$this->oModule = $this->model->getModuleInfoByName($module);
  }

  protected function loadView($module = NULL, $oData = NULL, $hasView = TRUE, $sMethodName = '')
  {
    if ($this->session->userdata("restricted") > 0) {
      $this->session->unset_userdata("restricted");
    } else {
      $site_id = $this->session->userdata("siteID");
      if ($site_id > 1) {
        if ($this->ion_auth->logged_in()) {
          $this->load->model("site/site_model");
          $allowed_labs = $this->site_model->get_all_sites_for_account();
          if (empty($allowed_labs) OR ( !is_array($allowed_labs)) OR ( !array_key_exists($site_id, $allowed_labs))) {
            $this->session->set_userdata("restricted", 1);
            redirect("restricted");
          }
        }
      }
    }
    // debugging feature
    //$this->output->enable_profiler( true );
    $this->aData["oPage"]->keys = $this->account_model->process_period();
    if (!empty($module)) {
      // assign module data
      if (is_string($module)) {
        $this->aData['oModule']->name = $module;
      }
      else {
        $this->aData['oModule'] = $module;
        if(empty($module->url)) {
            $this->aData['oModule']->name = $module->name;
        
        }elseif ($module->url == 'page-not-found') {
            $this->load->library('page/CmsPage');
            $page = new CmsPage('page-not-found');
            $item = $page->load_revision($page->revision_id);
            $item->render_content($this->aData['sTheme'], '');
            $this->aData['oPage'] = $item;
        }else{
            $this->aData['oModule']->name = $module->name;
        }
 
        
      }
      // assign data
      if (is_array($oData)) {
        $aData = array_merge($this->aData, $oData);
      }
      else {
        $this->aData['oModule']->data = !empty($oData) ? $oData : NULL;
        $aData = $this->aData;
      }
      // if module has view, load it.
      if (!empty($hasView) && $module->url != 'page-not-found') {
        //$this->aData['oModule']->view = $this->load->view( $this->aData['oModule']->name . ($sMethodName>''?'/':'') . $sMethodName, $aData, true );
        $aData['oModule']->view = $this->load->view($this->aData['oModule']->name . ($sMethodName > '' ? '/' . $sMethodName : ''), $aData, true);
      }
      $this->load->view('_global', $aData);
    }
    else {

      $url = trim($this->uri->uri_string(), '/');
      if ($url == '') {
        $url = '/';
      }
      if ($url == 'page-not-found') {
        $this->load->library('page/CmsPage');
        $page = new CmsPage('page-not-found');
        $item = $page->load_revision($page->revision_id);
        $item->render_content($this->aData['sTheme'], '');
        $this->aData['oPage'] = $item;
      }
      $this->load->view('_global', $this->aData);
    }
  }

  protected function processSubmit($hasNoValidation = false, $aError = null)
  {
    // assign mode
    $mode = $this->input->post('hdnMode');

    // if insert/update/delete
    if ((($mode == 'insert' || $mode == 'update') && ($this->form_validation->run() || $hasNoValidation) && empty($aError)) || $mode == 'delete') {

      // if insert/update/delete...
      if ($mode == 'insert') {
        $this->model->insert();
      }
      if ($mode == 'update') {
        $this->model->update();
      }
      if ($mode == 'delete') {
        $this->model->delete();
      }

      // assign confirm message
      $message = '<p>' . str_replace('%s', strtolower($this->oModule->sItemSingular), $this->lang->line('hotcms__message__confirm__' . $mode)) . '</p>';
      $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => $message));

      // regenerate sitemap
      //$this->sitemap->generateXML();

      redirect($this->oModule->name);
    }

    /// assign error/focus values
    $error = validation_errors();
    $focus = validation_focus();

    // loop/append local errors...
    if (!empty($aError)) {
      foreach ($aError as $value) {
        $error .= '<p>' . $value . '</p>';
      }
    }

    // if error... assign error message
    if (!empty($error)) {
      $this->aData['aMessage'] = array('type' => 'error', 'value' => $error, 'focus' => $focus);
    }
  }

  // deprecated. use functions add_message() and load_messages() instead.
  protected function setMessage($isAlert = true) {

    // if message...
    if ($this->session->userdata('messageType') && $this->session->userdata('messageValue')) {

      // assign message
      $this->aData['aMessage'] = array('type' => $this->session->userdata('messageType'),
          'value' => $this->session->userdata('messageValue'));

      // remove message
      $this->session->unset_userdata(array('messageType' => '', 'messageValue' => ''));
    }

    // if no current items...
    if (empty($this->aData['aMessage']) && empty($this->aData['aCurrent']) && $isAlert) {

      // assign values
      $aPlaceholder = array('%sp', '%s');
      $aValue = array(strtolower($this->oModule->sItemPlural), strtolower($this->oModule->sItemSingular));

      // assign alert message
      $this->aData['aMessage'] = array('type' => 'alert',
          'value' => '<p>' . str_replace($aPlaceholder, $aValue, $this->lang->line('hotcms__message__alert')) . '</p>');
    }
  }

  /**
   * Add messages to session
   * @param  str  message type
   * @param  array or string  message(s)
   */
  protected function add_message($type, $message = array())
  {
    $messages = $this->session->userdata('messages');
    if (!is_array($messages)) {
      $messages = array();
    }
		if (is_string($message)) {
      $messages[] = array('type' => $type, 'message' => $message);
		}
    else {
      foreach ($message as $msg) {
        $messages[] = array('type' => $type, 'message' => $msg);
      }
    }
    $this->session->set_userdata( 'messages', $messages );
  }

  /**
   * Load messages from session
   */
  protected function load_messages()
  {
    $messages = (array)$this->session->userdata('messages');
    if (count($messages) > 0) {
      $data = array('messages' => $messages);
      $this->load->vars($data);
    }
    //remove messages from session
    $this->session->unset_userdata('messages');
    return $messages;
  }

  protected function getErrors($table) {
    /// assign error/focus values
    $error = validation_errors();
    $focus = validation_focus();

    $this->aData['aCurrent'] = $this->model->select_result($table, 0, $this->session->userdata('siteID'));

    // loop/append local errors...
    if (!empty($aError)) {
      foreach ($aError as $value) {
        $error .= '<p>' . $value . '</p>';
      }
    }
    // if error... assign error message
    if (!empty($error)) {
      $this->aData['aMessage'] = array('type' => 'error', 'value' => $error, 'focus' => $focus);
    }
  }

  protected function processImage($imageTypeID, $module, $prefix = '', $field_0 = 'hdnIDCurr', $field_1 = 'hdnImageID', $field_2 = 'chkDelete_image', $isArchive = false, $isRestore = false) {

    // define constant
    define('PATH_UPLOAD_IMAGE', '../' . $this->session->userdata('siteURL') . '/asset/upload/image/');

    // if valid prefix... append underscore
    if (!empty($prefix)) {
      $prefix .= '_';
    }

    // if file upload...
    if (!empty($_FILES['fileImage']['name'])) {

      // initialize value
      $imageID = 0;

      // if new image...
      if ($this->input->post($prefix . $field_1) == 0) {

        // assign id
        $imageID = $this->model->select_row('GLOBAL', 0, 'cms_dImage')->Auto_increment;
      } else {

        // assign value
        $imageID = $this->input->post($prefix . $field_1);

        // delete previous image
        $aImagePrev = array_merge(glob(PATH_UPLOAD_IMAGE . $imageID . '.*'), glob(PATH_UPLOAD_IMAGE . $imageID . '_tmb.*'));
        array_map('unlink', $aImagePrev);
      }

      // assign config values
      $config['allowed_types'] = 'gif|jpg|png';
      $config['file_name'] = $imageID;
      $config['overwrite'] = true;
      $config['upload_path'] = PATH_UPLOAD_IMAGE;

      // initialize upload object
      $this->upload->initialize($config);

      // if upload successful...
      if ($this->upload->do_upload('fileImage')) {

        // assign image data
        $aImage = $this->upload->data();
        $aImage['file_ext'] = str_replace('.', '', $aImage['file_ext']);

        // make thumbnail
        $config['source_image'] = PATH_UPLOAD_IMAGE . $imageID . '.' . $aImage['file_ext'];
        $config['create_thumb'] = true;
        $config['thumb_marker'] = '_tmb';
        $config['width'] = 75;
        $config['height'] = 50;

        // load library/make thumb
        $this->image_lib->initialize($config);
        $this->image_lib->resize();

        // fetch module id
        $moduleID = $this->model->select_row('dModule', 11, $module)->nModuleID;

        // if new image...
        if ($this->input->post($prefix . $field_1) == 0) {

          // assign values
          $this->db->set('nImageTypeID', $imageTypeID);
          $this->db->set('nModuleID', $moduleID);
          $this->db->set('nReferenceID', $this->input->post($prefix . $field_0));
          $this->db->set('sExtension', $aImage['file_ext']);
          $this->db->set('sMIMEType', $aImage['file_type']);
          $this->db->set('nWidth', $aImage['image_width']);
          $this->db->set('nHeight', $aImage['image_height']);
          $this->db->set('nSize', $aImage['file_size']);
          $this->db->set('sAlt', $this->input->post($prefix . 'txtImage_alt') ? $this->input->post($prefix . 'txtImage_alt') : '' );
          $this->db->insert('cms_dImage');
        } else {

          // delete extraneous files
          $aExtension = explode('|', $config['allowed_types']);
          foreach ($aExtension as $extension) {
            if ($extension != $aImage['file_ext']) {
              unlink(PATH_UPLOAD_IMAGE . $this->input->post($prefix . $field_1) . '.' . $extension);
            }
          }

          $this->db->set('sExtension', $aImage['file_ext']);
          $this->db->set('sMIMEType', $aImage['file_type']);
          $this->db->set('nWidth', $aImage['image_width']);
          $this->db->set('nHeight', $aImage['image_height']);
          $this->db->set('nSize', $aImage['file_size']);
          $this->db->set('sAlt', $this->input->post($prefix . 'txtImage_alt') ? $this->input->post($prefix . 'txtImage_alt') : '' );
          $this->db->set('dUpdated', 'CURRENT_TIMESTAMP', false);
          $this->db->where('nImageID', $imageID);
          $this->db->update('cms_dImage');
        }
      } else {
        $error = '<p>' . $this->upload->display_errors() . '</p>';
      }

      // if archiving image...
      if (!empty($isArchive)) {

        // insert data
        $this->db->set('nImageID', $imageID);
        $this->db->set('nImageTypeID', $imageTypeID);
        $this->db->set('nModuleID', $moduleID);
        $this->db->set('nReferenceID', $this->input->post($prefix . $field_0));
        $this->db->set('sExtension', $aImage['file_ext']);
        $this->db->set('sMIMEType', $aImage['file_type']);
        $this->db->set('nWidth', $aImage['image_width']);
        $this->db->set('nHeight', $aImage['image_height']);
        $this->db->set('nSize', $aImage['file_size']);
        $this->db->set('sAlt', $this->input->post($prefix . 'txtImage_alt') ? $this->input->post($prefix . 'txtImage_alt') : '' );
        $this->db->insert('cms_dImageArchive');

        // fetch date created
        $createDate = preg_replace('/( |\-|\:)/', '', $this->model->select_row('dImageArchive', 2)->dCreated);

        // copy images to archive directory
        copy(PATH_UPLOAD_IMAGE . $imageID . '.' . $aImage['file_ext'], PATH_UPLOAD_IMAGE . 'archive/' . $imageID . '_' . $createDate . '.' . $aImage['file_ext']);
        copy(PATH_UPLOAD_IMAGE . $imageID . '_tmb.' . $aImage['file_ext'], PATH_UPLOAD_IMAGE . 'archive/' . $imageID . '_' . $createDate . '_tmb.' . $aImage['file_ext']);
      }
    }

    // if deleting image...
    if ($this->input->post($prefix . $field_2) == 'true') {

      // assign value
      $imageID = $this->input->post($prefix . $field_1);

      // delete previous image
      $aImagePrev = array_merge(glob(PATH_UPLOAD_IMAGE . $imageID . '.*'), glob(PATH_UPLOAD_IMAGE . $imageID . '_tmb.*'));
      array_map('unlink', $aImagePrev);

      // delete from database
      $this->db->where('nImageID', $imageID);
      $this->db->delete('cms_dImage');
    }

    // if restoring image...
    if ($this->input->post($prefix . 'hdnImage_created')) {

      // update data
      $this->db->set('sExtension', $this->input->post($prefix . 'hdnImage_extension'));
      $this->db->set('sMIMEType', $this->input->post($prefix . 'hdnImage_mimetype'));
      $this->db->set('nWidth', $this->input->post($prefix . 'hdnImage_width'));
      $this->db->set('nHeight', $this->input->post($prefix . 'hdnImage_height'));
      $this->db->set('nSize', $this->input->post($prefix . 'hdnImage_size'));
      $this->db->set('sAlt', $this->input->post($prefix . 'hdnImage_alt'));
      $this->db->set('dCreated', $this->input->post($prefix . 'hdnImage_created'));
      $this->db->set('dUpdated', 'CURRENT_TIMESTAMP', false);
      $this->db->where('nImageID', $this->input->post($prefix . 'hdnImageID'));
      $this->db->update('cms_dImage');

      // assign values
      $imageID = $this->input->post($prefix . 'hdnImageID');
      $extension = $this->input->post($prefix . 'hdnImage_extension');
      $createDate = preg_replace('/( |\-|\:)/', '', $this->input->post($prefix . 'hdnImage_created'));

      // copy images to archive directory
      copy(PATH_UPLOAD_IMAGE . 'archive/' . $imageID . '_' . $createDate . '.' . $extension, PATH_UPLOAD_IMAGE . $imageID . '.' . $extension);
      copy(PATH_UPLOAD_IMAGE . 'archive/' . $imageID . '_' . $createDate . '_tmb.' . $extension, PATH_UPLOAD_IMAGE . $imageID . '_tmb.' . $extension);
    }
  }

  protected function getDatabaseDate($time = null) {
    return mdate($this->aData['GLOBAL']['MYSQL_FORMAT_DATE'], gmt_to_local(!empty($time) ? $time : now(), $this->aData['GLOBAL']['TIME_ZONE'], $this->aData['GLOBAL']['DAYLIGHT_TIME']));
  }

  protected function getURL($value) {
    return preg_replace('/ +/', '-', preg_replace('/[^a-z0-9\- ]/', '', strtolower(self::swapAccent($value))));
  }

  protected function array2Object($aData) {
    return is_array($aData) ? (object) array_map(array('HotCMS_Controller', 'array2Object'), $aData) : $aData;
  }

  protected function swapAccent($string) {

    $string = utf8_decode($string);

    // swap accented characters
    $string = preg_replace('/[������]/', 'A', $string);
    $string = preg_replace('/[����]/', 'E', $string);
    $string = preg_replace('/[����]/', 'I', $string);
    $string = preg_replace('/[�����]/', 'O', $string);
    $string = preg_replace('/[����]/', 'U', $string);
    $string = preg_replace('/[ݟ]/', 'Y', $string);
    $string = preg_replace('/�/', 'C', $string);
    $string = preg_replace('/�/', 'N', $string);

    $string = preg_replace('/[������]/', 'a', $string);
    $string = preg_replace('/[����]/', 'e', $string);
    $string = preg_replace('/[����]/', 'i', $string);
    $string = preg_replace('/[�����]/', 'o', $string);
    $string = preg_replace('/[����]/', 'u', $string);
    $string = preg_replace('/[��]/', 'y', $string);
    $string = preg_replace('/�/', 'c', $string);
    $string = preg_replace('/�/', 'n', $string);

    return $string;
  }

  /*   * * core validators ** */

  public function _validator_content($content) {

    // if invalid format...
    if (empty($content)) {

      // assign error message
      $this->form_validation->set_message('_validator_content', $this->lang->line('hotcms__validator_content'));
      return false;
    } else {
      return true;
    }
  }

  public function _validator_date($date) {

    // if invalid format...
    if (!empty($date) && preg_match('/^2[0-9]{3}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/', $date) == 0) {

      // assign error message
      $this->form_validation->set_message('_validator_date', $this->lang->line('hotcms__validator_date'));
      return false;
    } else {
      return true;
    }
  }

  public function _validator_password() {

    $isValid = true;

    // if embedded user name...
    if (stristr($this->input->post('txtPassword_0'), $this->input->post('txtUser')) !== false ||
            stristr($this->input->post('txtPassword_1'), $this->input->post('txtUser')) !== false) {

      // assign error message
      $this->form_validation->set_message('_validator_password', $this->lang->line('hotcms__validator_password_0'));
      $isValid = false;
    }

    // if password do not match...
    if (!empty($isValid) && $this->input->post('txtPassword_0') != $this->input->post('txtPassword_1')) {

      // assign error message
      $this->form_validation->set_message('_validator_password', $this->lang->line('hotcms__validator_password_1'));
      $isValid = false;
    }

    return $isValid;
  }

  public function _validator_url($url) {

    // fetch duplicate url(s)
    $aURL = $this->model->select_result('dPage', 4, array($this->input->post('hdnIDCurr'), $this->input->post('hdnParentID'), $url));

    // if duplicate(s)
    if (!empty($aURL)) {

      // assign error message
      $this->form_validation->set_message('_validator_url', $this->lang->line('hotcms__validator_url'));
      return false;
    } else {
      return true;
    }
  }

  public function _validator_user($user) {

    // fetch duplicate user name
    $aUser = $this->model->select_result('dUser', 3, array($this->input->post('hdnIDCurr'), $user));

    // if duplicate(s)
    if (!empty($aUser)) {

      // assign error message
      $this->form_validation->set_message('_validator_user', $this->lang->line('hotcms__validator_user'));
      return false;
    } else {
      return true;
    }
  }

}

?>
