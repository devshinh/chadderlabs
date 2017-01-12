<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Role Config
 *
 * Description:  Role management
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'Role';

/**
 * Module default URL
 */
$config['module_url']		   = "role";

/**
 * Meta description in the HTML head section
 */
$config['meta_description']         = '';

/**
 * Meta keyword in the HTML head section
 */
$config['meta_keyword']             = '';

/**
 * Module styles, separated by space
 */
$config['css']       = "";

/**
 * Module Javascripts, separated by space
 */
$config['js']       = "";

/**
 * Tables
 */
$config['tables']['role'] = 'role';
$config['tables']['permission'] = 'permission';
$config['tables']['permission_map'] = 'permission_map';
$config['tables']['user_role'] = 'user_role';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'role' => array('label' => 'Roles', 'access' => 'manage_role')
);

/* End of file role.php */
