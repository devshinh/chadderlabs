<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Menu Config
 *
 * Description:  Menu management
 */

/**
 * Module title
 */
$config['module_title']       = 'Menu';

/**
 * module default URL
 */
$config['module_url']		   = "menu";

/**
 * Meta description in the HTML head section
 */
$config['meta_description']         = '';

/**
 * Meta keyword in the HTML head section
 */
$config['meta_keyword']             = '';

/**
 * Role styles, separated by space
 */
$config['css']       = "menu.css";

/**
 * Role Javascripts, separated by space
 */
$config['js']       = "menu.js";

/**
 * Tables
 */
$config['tables']['menu_group'] = 'menu_group';
$config['tables']['menu_item'] = 'menu';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'menu' => array('label' => 'Menus', 'access' => 'manage_content')
);

/* End of file menu.php */
/* Location: ./system/application/config/menu.php */
