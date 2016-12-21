<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Brand Config
 *
 * Description:  Brand management
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'Brand';

/**
 * Module default URL
 */
$config['module_url']		   = "brand";

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
$config['css']       = "brand.css";

/**
 * Module Javascripts, separated by space
 */
$config['js']       = "brand.js";

/**
 * Tables
 */
$config['tables']['asset'] = 'asset';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  //'brand' => array('label' => 'Brand', 'access' => 'manage_brand')
);

/* End of file brand.php */
