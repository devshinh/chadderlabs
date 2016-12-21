<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Organization Config
* 
* Description:  Organization management
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
  $config['css']       = "organization.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "organization.js";

  /**
   * Tables.
   **/
  $config['tables']['organization'] = 'organization';
  $config['tables']['location'] = 'location';
  $config['tables']['location_user'] = 'user_location';
  
/* End of file storelocator.php */
/* Location: ./system/application/config/storelocator.php */
