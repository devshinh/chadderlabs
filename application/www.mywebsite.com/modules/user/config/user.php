<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  User Config
 *
 * Description:  User management module dealing with common tasks such as login, logout, change password etc.
 */

/**
 * Module title
 */
$config['module_title']       = 'Address Book';

/**
 * Module default URL
 */
$config['module_url']		   = "user";

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
$config['js']       = "user.js";

/**
 * Tables
 */
$config['tables']['user'] = 'user';
$config['tables']['user_profile'] = 'user_profile';
$config['tables']['user_role'] = 'user_role';
$config['tables']['asset'] = 'asset';
$config['tables']['role'] = 'role';
$config['tables']['points'] = 'user_points';
$config['tables']['retailer'] = 'retailer';
$config['tables']['store'] = 'retailer_store';
$config['tables']['province'] = 'province';
$config['tables']['country'] = 'country';
$config['tables']['site'] = 'site';
$config['tables']['draws'] = 'user_draws';

/**
  * Admin menu/navigation
  */
$config['admin_menu'] = array(
  'user' => array('label' => 'Users', 'access' => 'manage_user')
);

/* End of file user.php */
