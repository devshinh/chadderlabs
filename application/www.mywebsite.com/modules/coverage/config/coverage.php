<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Wireless Coverage Map Config
* 
* Author: jeffrey@hottomali.com
*          
* Created:  08.04.2010
* Last updated:  08.04.2010
* 
* Description:  Wireless coverage map.
* 
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Wireless Coverage Map';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "coverage";
	
	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'View our coverage map to find out where you can talk in Canada.';
	 
	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'prepaid phone coverage,phone coverage map,gsm phone coverage, coverage,canada';
	
  /**
   * Module styles, separated by space
   */
  $config['css']       = "jquery.iviewer.css coverage.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "jquery.mousewheel.min.js jquery.iviewer.js coverage.js";
  
  /**
   * Tables.
   **/
  $config['tables']['stores']  = 'cms_dStorecode';
  
/* End of file storelocator.php */
/* Location: ./system/application/config/storelocator.php */
