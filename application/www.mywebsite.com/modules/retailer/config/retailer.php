<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Retailer Config
 *
 * Description:  Retailer management
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'Retailer';

/**
 * Module default URL
 */
$config['module_url']		   = "retailer";

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
$config['tables']['retailer'] = 'retailer';
$config['tables']['store'] = 'retailer_store';
$config['tables']['retailer_role'] = 'retailer_role';
$config['tables']['province'] = 'province';
$config['tables']['country'] = 'country';
$config['tables']['user_profile'] = 'user_profile';
$config['tables']['role'] = 'role';
$config['tables']['organization_type'] = 'organization_type';
$config['tables']['organization_in_type'] = 'organization_in_type';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'retailer' => array('label' => 'Retailers', 'access' => 'manage_retailer'),
  'retailer/access' => array('label' => 'Retailer Access', 'access' => 'manage_retailer_permission')
);

/* End of file retailer.php */
