<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*    Permission Class
*    COPYRIGHT (C) 2008-2009 Haloweb Ltd
*    http://www.haloweb.co.uk/blog/
*
*    Version:    0.9.1
*    Wiki:       http://codeigniter.com/wiki/Permission_Class/
*
*    Description:
*    The Permission class uses keys in a session to allow or disallow functions
*    or areas of a site. The keys are stored in a database and this class adds
*    and/or takes them away. The use of IF statements are required within
*    controllers and views, please see wiki for code.
*
*    Permission is hereby granted, free of charge, to any person obtaining a copy
*    of this software and associated documentation files (the "Software"), to deal
*    in the Software without restriction, including without limitation the rights
*    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
*    copies of the Software, and to permit persons to whom the Software is
*    furnished to do so, subject to the following conditions:
*
*    The above copyright notice and this permission notice shall be included in
*    all copies or substantial portions of the Software.
*
*    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
*    THE SOFTWARE.
**/

class Permission {

    // init vars
    var $CI;
    var $where = array();
    var $set = array();
    var $required = array();
    var $site_id = 0;

    public static $sites = array();

    function Permission()
    {
      // init vars
      $this->CI =& get_instance();
      // set group_id from session (if set)
      //$this->group_id = ($this->CI->session->userdata('group_id')) ? $this->CI->session->userdata('group_id') : 0;
      if (empty(self::$sites)) {
        $query = $this->CI->db->select('id, domain')->where('active', 1)->get('site');
        $rows = array();
        foreach ($query->result() as $row) {
          $rows[$row->domain] = $row->id;
        }
        self::$sites = $rows;
      }
      // get site ID by domain name
      if (array_key_exists($_SERVER['HTTP_HOST'], self::$sites)) {
        $this->site_id = self::$sites[$_SERVER['HTTP_HOST']];
        // the admin area has the same domain name as the main site, use session instead
        if ($this->site_id == 1 && substr($_SERVER['REQUEST_URI'], 0, 8) == '/hotcms/') {
          $this->site_id = (int)($this->CI->session->userdata('siteID'));
        }
      }
    }

    /**
     * get user roles
     * @param  int  $user_id
     * @param  int  $site_id
     * @return array
     */
    function get_user_roles($user_id, $site_id = 0)
    {
      $primary_site_id = 1; // hard coded for now: roles on primary site may have specail privileges
      //get user's retailer id
        $query = $this->CI->db->select('retailer_id')
          ->where('user_profile.user_id', $user_id)
          ->get('user_profile');
      $retailer_id = $query->row('retailer_id');
      
      $roles = array();
      if ($user_id > 0) {
        $query = $this->CI->db->select('role.id, role.name, role.system, role.site_id')->distinct()
          ->join('user_role', 'user_role.role_id=role.id')
          ->where('user_role.user_id', $user_id)
          ->where('role.active', 1)
          ->get('role');
        if ($query->num_rows()) {
          foreach ($query->result() as $row) {
            // only super admin and roles on the same domain count, unless site ID was not specified
            if (($row->site_id == $site_id && $site_id > 0 || $row->system == 1)) {
              $roles[$row->id] = $row;
            } elseif ($site_id === 0) {
              $roles[$row->id] = $row;
            }
            // get special roles based on member profile (retailer ID)
            // get site's id where is enabled user's retailer targeting
            //if ($row->system == 3) {
              // add related roles according to the retailer
//              $query2 = $this->CI->db->select('role.id, role.name, role.system, role.site_id')->distinct()
//                ->join('retailer_role', 'retailer_role.role_id=role.id')
//                ->join('user_profile', 'user_profile.retailer_id=retailer_role.retailer_id')
//                ->where('user_profile.user_id', $user_id)
//                ->where('role.site_id', $this->site_id)
//                ->where('role.active', 1)
//                ->get('role');
              $query2 = $this->CI->db->select('site_id')->distinct()
                   ->join('retailer_permission as rp', ' rp.permission_id = p.id')
                   ->where('rp.retailer_id ', $retailer_id)
                   ->get('permission as p');
              $all_roles = self::list_all_roles();

              // find member role for site_ids
//              if ($query2->num_rows()) {
//                foreach ($query2->result() as $site_id) {
                    //var_dump($site_id->site_id);
                    foreach($all_roles as $role){
                        
                        if($site_id == $role->site_id && $role->name == 'member'){
                        //var_dump($role);
                            $roles[$role->id] = $role;
                        }
                    }
              //    if (!array_key_exists($r->id, $roles)) {
              //      $roles[$r->id] = $r;
              //    }
//                }
//              }
              //var_dump($all_roles);
              //die();
            //}
          }
        }
      }
      // for visitors or users who are not associated with any roles
      if ($user_id == 0 || empty($roles)) {
        if ($site_id > 0) {
          $this->CI->db->where('role.site_id', $site_id);
        }
        else {
          $this->CI->db->where('role.site_id', $this->site_id);
        }
        $query = $this->CI->db->select('role.id, role.name, role.system, role.site_id')->distinct()
          ->where('system', 4)
          ->where('role.active', 1)
          ->get('role');
        if ($query->num_rows()) {
          foreach ($query->result() as $row) {
            $roles[$row->id] = $row;
          }
        }
      }
      return $roles;
    }

    /**
     * get user sites/domains (that they have permission in the admin panel)
     * @param  int  $user_id
     * @return array
     */
    function get_admin_sites($user_id)
    {
      $sites = array();
      $super_admin = FALSE;
      if ($user_id > 0) {
        $roles = self::get_user_roles($user_id);
        foreach ($roles as $r) {
          if ($r->system == 1) {
            $super_admin = TRUE;
            break;
          }
        }
        if ($super_admin) {
          // super admin has access to all sites, always
          $query = $this->CI->db->select('id, name, domain, primary, path, site_image_id')
            ->order_by('primary DESC, name ASC')
            ->get('site');
          foreach ($query->result() as $row) {
            $sites[$row->id] = $row;
          }
        }
        else {
          $role_ids = array_keys($roles);
          $query = $this->CI->db->select('site.id, site.name, site.domain, site.primary, site.path,site.site_image_id')->distinct()
            ->join('permission', 'permission.site_id=site.id')
            ->join('permission_map', 'permission_map.permission_id=permission.id')
            ->where('permission.permission_key', 'admin_area')
            ->where_in('permission_map.role_id', $role_ids)
            ->order_by('site.primary DESC, site.name ASC')
            ->get('site');
          if ($query->num_rows()) {
            foreach ($query->result() as $row) {
              $sites[$row->id] = $row;
            }
          }
        }
      }
      return $sites;
    }

    /**
     * get user permissions
     * @param  int  $user_id
     * @param  int  $site_id
     * @return array
     */
    function get_user_permissions($user_id, $site_id = 0)
    {
      if ($site_id == 0) {
        $site_id = $this->site_id;
      }
      $permissions = array();
      //get user's retailer id
        $query = $this->CI->db->select('retailer_id')
          ->where('user_profile.user_id', $user_id)
          ->get('user_profile');
      $retailer_id = $query->row('retailer_id');
      $roles = self::get_user_roles($user_id, $site_id);
      $is_visitor = FALSE;
      $is_admin = FALSE;
      // special permissions
      foreach ($roles as $r) {
        if ($r->system == 1) {
          $permissions[] = 'super_admin';
        }
        elseif ($r->system == 2) {
          $is_admin = TRUE;
          $site_id = $r->site_id;
        }        
        elseif ($r->system == 4) {
          $is_visitor = TRUE;
        }
        //elseif ($r->system == 3 && $r->site_id == 1) {
        //  $is_visitor = TRUE;
        //}
        $is_visitor=FALSE;
      }
      //die(var_dump($roles));
      if ($user_id > 0 && !$is_visitor && !in_array('super_admin', $permissions)) {
        $role_ids = array_keys($roles);
        $query = $this->CI->db->select('permission_key')->distinct()
          ->join('permission_map', 'permission_map.permission_id = permission.id')
          ->where('permission.site_id', $site_id)
          ->where_in('permission_map.role_id', $role_ids)
          ->get('permission');
        if ($query->num_rows()) {
          foreach ($query->result() as $row) {
            $permissions[] = $row->permission_key;
          }
        }
        //add retailer permissions
        $query = $this->CI->db->select('permission_key')->distinct()
          ->join('retailer_permission', 'retailer_permission.permission_id = permission.id')
          ->where('retailer_permission.retailer_id', $retailer_id)
          ->get('permission');
       // die(var_dump($this->CI->last_query()));
        if ($query->num_rows()) {
          foreach ($query->result() as $row) {
            $permissions[] .= $row->permission_key;
          }
        }        
      }
      // if not logged in, get permissions for a visitor
      if ($user_id == 0 || $is_visitor) {
        $query = $this->CI->db->select('permission_key')->distinct()
          ->join('permission_map', 'permission_map.permission_id = permission.id')
          ->join('role', 'role.id = permission_map.role_id AND role.site_id=permission.site_id')
          ->where('role.system', '4')
          ->where('role.site_id', $site_id)
          ->get('permission');
        if ($query->num_rows()) {
          foreach ($query->result() as $row) {
            $permissions[] = $row->permission_key;
          }
        }
      }
      if ($user_id > 0 && $is_admin) {
        $role_ids = array_keys($roles);
        $query = $this->CI->db->select('permission_key')->distinct()
          ->join('permission_map', 'permission_map.permission_id = permission.id')
          ->where('permission.site_id', $site_id)
          ->where_in('permission_map.role_id', $role_ids)
          ->get('permission');
        //var_dump($this->CI->db->last_query());
        //die();
        if ($query->num_rows()) {
          foreach ($query->result() as $row) {
            $permissions[] = $row->permission_key;
          }
        }
      
      }      
      //die(var_dump($permissions));
      return $permissions;
    }

    /**
     * list all permissions on a site
     * @param  string  category name
     * @param  int  $site_id
     * @return array
     */
    function list_permissions($category = '', $site_id = 0)
    {
      if ($site_id == 0) {
        $site_id = $this->site_id;
      }
      $permissions = array();
      if ($category > '') {
        $this->CI->db->where('category', $category);
      }
      $query = $this->CI->db->select()
        ->where('site_id', $site_id)
        ->order_by('category, description')
        ->get('permission');
      if ($query->num_rows()) {
        foreach ($query->result() as $row) {
          $permissions[$row->id] = $row;
        }
      }
      return $permissions;
    }

    /**
     * list role permissions
     * @param  int  $role_id
     * @param  int  $site_id
     * @return array
     */
    function list_role_permissions($role_id, $site_id = 0)
    {
      if ($site_id == 0) {
        $site_id = $this->site_id;
      }
      $query = $this->CI->db->select('p.id,p.description,p.permission_key,p.category')->distinct()
        ->join('permission_map m', 'm.permission_id = p.id')
        ->where('m.role_id', $role_id)
        ->where('p.site_id', $site_id)
        ->get('permission p');
      return $query->result();
    }

    /**
     * list roles for site_id
     * @return array
     */
    function list_roles($site_id = 0)
    {
      if ($site_id == 0) {
        $site_id = $this->site_id;
      }
      $roles = array();
      $query = $this->CI->db->select('id, name')
        ->where('active', 1)
        ->where('site_id', $site_id)
        ->get('role');
      if ($query->num_rows()) {
        foreach ($query->result_array() as $row) {
          $roles[$row['id']] = $row['name'];
        }
      }
      return $roles;
    }
    
    /**
     * list all active roles
     * @return array
     */
    function list_all_roles()
    {
        
      $roles = array();
      $query = $this->CI->db->select('id, name, site_id, system')
        ->where('active', 1)
        ->get('role');
      if ($query->num_rows()) {
        foreach ($query->result() as $row) {
          $all_roles[$row->id] = $row;
        }
      }
      return $all_roles;
    }    

    /**
     * update permissions for a role(user group)
     * @param  int  role ID
     * @param  array  permissions
     * @return bool
     */
    function update_permissions($role_id, $permissions)
    {
      if (empty($permissions)) {
        $permissions = array();
      }
      // delete all permissions on this group_id first
      $this->CI->db->where('role_id', $role_id);
      $this->CI->db->delete('permission_map');
      foreach ($permissions as $k => $v) {
        $this->CI->db->set('role_id', $role_id);
        $this->CI->db->set('permission_id', $k);
        $this->CI->db->insert('permission_map');
      }
      return TRUE;
    }

    /* get permissions from for this group
    function get_group_permissions($group_id)
    {
        // grab keys
        $this->CI->db->select('key');
        $this->CI->db->join('permission', 'permission.id = permission_map.permission_id');

        // get groups
        $this->CI->db->where('role_id', $group_id);

        // set permissions array and return
        if ($query->num_rows())
        {
            foreach ($query->result_array() as $row)
            {
                $permissions[] = $row['key'];
            }

            return $permissions;
        }
        else
        {
            return false;
        }
    }

    // get all permissions, or permissions from a group for the purposes of listing them in a form
    function get_permissions($group_id = '')
    {
        // select
        $this->CI->db->select('DISTINCT(category)');

        // if group_id is set get on that group_id
        if ($group_id)
        {
            $this->CI->db->where_in('key', $this->get_user_permissions($group_id));
        }

        // order
        $this->CI->db->order_by('category');

        // return
        $query = $this->CI->db->get('permissions');

        if ($query->num_rows())
        {
            $result = $query->result_array();

            foreach($result as $row)
            {
                if ($cat_perms = $this->get_perms_from_cat($row['category']))
                {
                    $permissions[$row['category']] = $cat_perms;
                }
                else
                {
                    $permissions[$row['category']] = 'N/A';
                }
            }
            return $permissions;
        }
        else
        {
            return false;
        }
    }

    // get the map of keys from a group ID
    function get_permission_map($group_id)
    {
        // grab keys
        $this->CI->db->select('permission_id');
        // where
        $this->CI->db->where('group_id', $group_id);
        // return
        $query = $this->CI->db->get('permission_map');

        if ($query->num_rows())
        {
            return $query->result_array();
        }
        else
        {
            return false;
        }
    }

    // a group to the permission groups table
    function add_group($group_name = '')
    {
      if ($group_name)
      {
        $this->CI->db->set('group_name', $group_name);
        $this->CI->db->insert('permission_groups');
        return $this->CI->db->insert_id();
      }
      else
      {
        return false;
      }
    } */

}

?>
