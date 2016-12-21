<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Badge Config
 *
 * Description:  Badge management
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'Badge';

/**
 * Module default URL
 */
$config['module_url']		   = "badge";

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
$config['css']       = "badge.css";

/**
 * Module Javascripts, separated by space
 */
$config['js']       = "badge.js";

/**
 * Tables
 */
$config['tables']['badge'] = 'badge';
$config['tables']['asset'] = 'asset';
$config['tables']['points'] = 'user_points';
$config['tables']['draws'] = 'user_draws';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'badge' => array('label' => 'Badge', 'access' => 'manage_badge')
);

/* End of file badge.php */
