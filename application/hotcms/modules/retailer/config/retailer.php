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
$config['module_title']       = 'Organization';

/**
 * Module default URL
 */
$config['module_url']		   = "organization";

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
$config['tables']['permission'] = 'permission';
$config['tables']['retailer_permission'] = 'retailer_permission';
$config['tables']['user'] = 'user';
$config['tables']['user_profile'] = 'user_profile';
$config['tables']['retailer_category'] = 'retailer_category';
$config['tables']['retailers_categories'] = 'retailer_in_category';
$config['tables']['organization_type'] = 'organization_type';
$config['tables']['organization_in_type'] = 'organization_in_type';
$config['tables']['target_organization'] = 'target_organization';
$config['tables']['target_store'] = 'target_store';
$config['tables']['target_category'] = 'target_organization_category';
$config['tables']['target_type'] = 'target_organization_type';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'organization' => array('label' => 'Organization', 'access' => 'manage_retailer'),
//  'retailer/access' => array('label' => 'Retailer Access', 'access' => 'manage_retailer_permission', 'submenu' => true),
  'organization/categories' => array('label' => 'Organization Categories', 'access' => 'manage_retailer', 'submenu' => true),
  "organization/types" => array("label" => "Organization Types", "access" => "manage_retailer", "submenu" => TRUE)
);

/* End of file retailer.php */
