<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Target Config
* 
* Description:  Config Target Module
* 
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Target';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "target";
	
	/**
	 * Meta description in the HTML head section
	 */
//	$config['meta_description']         = '';
	 
	/**
	 * Meta keyword in the HTML head section
	 */
//	$config['meta_keyword']             = '';
	
  /**
   * Module styles, separated by space
   */
  $config['css']       = "target.css";
  
  /**
   * Module Javascripts, separated by space
   */
//  $config['js']       = "";

  /**
   * Tables.
   **/
  $config['tables']['target'] = 'target';
  $config['tables']['target_organization'] = 'target_organization';
  $config['tables']['target_organization_category'] = 'target_organization_category';
  $config['tables']['target_organization_type'] = 'target_organization_type';
  $config['tables']['target_store'] = 'target_store';
  $config['tables']['target_job_title'] = 'target_job_title';
  $config['tables']['store'] = 'retailer_store';
  $config['tables']['organization'] = 'retailer';
  $config['tables']['organization_type'] = 'organization_type';
  $config['tables']['organization_category'] = 'retailer_category';
  $config['tables']['organization_in_type'] = 'organization_in_type';
  $config['tables']['organization_in_category'] = 'retailer_in_category';
  $config['tables']['job_title'] = 'user_job_title';
  $config['tables']['user_profile'] = 'user_profile';
  $config['tables']['user'] = 'user';
  $config['tables']['user_role'] = 'user_role';
  $config['tables']['role'] = 'role';
  $config["tables"]["state"] = "province";
  $config["tables"]["site"] = "site";
  $config['tables']['quiz'] = 'quiz';
  $config['tables']['lab'] = 'training';
  
  /**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'target' => array('label' => 'Target', 'access' => 'manage_targets'),
);
  
/* End of file storelocator.php */
/* Location: ./system/application/config/storelocator.php */
