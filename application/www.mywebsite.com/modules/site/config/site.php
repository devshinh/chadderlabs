<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Site Config
* 
* Description:  Site management
* 
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Site';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "site";
	
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
  $config['css']       = "site.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "site.js";

  /**
   * Tables.
   **/
  $config['tables']['site'] = 'site';
  $config['tables']['role'] = 'role';
  $config['tables']['permission'] = 'permission';  
  $config['tables']['module'] = 'module';  
  $config['tables']['page_layout'] = 'page_layout';  
  $config['tables']['module_widget'] = 'module_widget';    
  $config['tables']['menu_group'] = 'menu_group';    
  $config['tables']['asset_category'] = 'asset_category';   
  $config['tables']['retailer_permission'] = 'retailer_permission';
  $config["tables"]["target"] = "target";
  $config['tables']['brand_points_deposit'] = 'brand_points_deposit';
  $config['tables']['quiz'] = 'quiz';
  $config['tables']['quiz_history'] = 'quiz_history';
  $config['tables']['quiz_type'] = 'quiz_type';
  $config['tables']['lab'] = 'training';
  $config["tables"]["user_role"] = "user_role";
  $config["tables"]["user_profile"] = "user_profile";
  
  /**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'site' => array('label' => 'Sites', 'access' => 'manage_sites'),
);
  
/* End of file storelocator.php */
/* Location: ./system/application/config/storelocator.php */
