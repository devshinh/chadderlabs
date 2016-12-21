<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Verification Config
 *
 * Description:  Verification management
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'Verification';

/**
 * Module default URL
 */
$config['module_url']		   = "verification";

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
$config['css']       = "verification.css";

/**
 * Module Javascripts, separated by space
 */
$config['js']       = "verification.js";

/**
 * Tables
 */
$config['tables']['verification'] = 'user_verification';
$config['tables']['profile'] = 'user_profile';
$config['tables']['user'] = 'user';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'verification' => array('label' => 'Verification', 'access' => 'manage_verification')
);

/* End of file verification.php */
