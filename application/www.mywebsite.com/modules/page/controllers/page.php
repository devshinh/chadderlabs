<?php
class Page extends HotCMS_Controller {

  function _remap()
  {
    $this->load->library('CmsPage');
    $this->load->helper('cookie');

    $url = trim($this->uri->uri_string(), '/');
    if ($url == '') {
      $url = 'home';
    }
    // set permissions for visitors
//    if ($this->user_id == 0) {
//      $permissions = $this->session->userdata('permissions');
//      if (empty($permissions)) {
//        $permissions = $this->permission->get_user_permissions($this->user_id);
//        $this->session->set_userdata('permissions', $permissions);
//      }
//    }

    // check permission
    // the login_page will always bypass all permission checks and available to all visitors
    if (!has_permission('view_content') && !in_array($url, $this->config->item('public_pages'))) {
      if ($this->ion_auth->logged_in()) {
        show_error($this->lang->line('hotcms_error_insufficient_privilege'));
      }
      else {
        //store URI to session for later redirect
        $this->session->set_userdata('redirect_to', $this->uri->uri_string());
        redirect($this->config->item('login_page'));
      }
    }

    // load page object, $item
    $preview = get_cookie('previewing');
    if ($preview > '' && strpos($preview, ':') > 0) {
      $ids = explode(':', $preview);
      $page_id = $ids[0];
      $rev_id = $ids[1];
      $rev_url = $ids[2];
      if ($rev_url == $url) {
        if ($rev_id > 0) {
          // preview a revision
          $page = new CmsPage($page_id);
          $item = $page->load_revision($rev_id);
          if ($item) {
            $item->title .= ' [revision:' . date('Y-m-d H:i:s', $item->update_timestamp) . ']';
          }
        }
        else {
          // preview a draft
          $page = new CmsPage($page_id);
          $item = $page->draft;
          if ($item) {
            $item->title .= ' [draft preview]';
          }
        }
      }
      else {
        $item = $page = new CmsPage($url, TRUE);
      }
    } else {
      // load a published page
      $item = $page = new CmsPage($url, TRUE);
    }
    
    // load view of page object $item
    if ($item->id == 0 || $item->name == NULL || is_numeric($url)) {
      // if page not found
      //$item->url = 'page-not-found';
      //      $this->output->set_status_header('404');redirect('page-not-found');
      //return $this->load->view('application/www.mywebsite.com/themes/bwinsurance/views/maintenance');
      //redirect('page-not-found');
      //$item->load(FALSE);

      // if valid page, render it
      $item = $page = new CmsPage('page-not-found');
      //$item = $page->load_revision($page->revision_id);
      $item->render_content($this->aData['sTheme'], '');
      $this->aData['oPage'] = $item;
      // set additional info
      $this->loadMenu();
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return $this->load->view('_global', $this->aData);
      //return self::loadView( NULL, NULL, FALSE );
    }

    if ($item->id > 0) {
      // check individual page permission
      $allowed_roles = $page->allowed_roles;
      if (!empty($allowed_roles)) {
        $roles_found = array_intersect($allowed_roles, $this->user_roles);
        if (empty($roles_found)) {
          if ($this->user_id > 0) {
            show_error('You do not have access to this page.');
          }
          else {
            $this->session->set_userdata('redirect_to', $url);
            redirect($this->config->item('login_page'));
          }
        }
      }
      $params = array();
      // process post
      if ($this->input->post() !== FALSE) {
        $params = $this->input->post();
        $params['frontpostback'] = 'TRUE';
      }
      // if valid page, render it
      $item->render_content($this->aData['sTheme'], $params);
      $this->aData['oPage'] = $item;
      // set additional info
      $this->loadMenu();
      // if homepage... set info
      //if ($page->url == $this->router->default_controller) { self::setHomepageInfo(); }
      self::loadView( NULL, NULL, FALSE );
    }
    else {
      // if a 404 page was not created in the page publisher, show the system default 404 page
      show_404($url);
      exit;
    }
  }

}
?>