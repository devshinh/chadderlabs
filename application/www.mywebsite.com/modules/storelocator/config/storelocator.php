<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Store Locator Config
* 
* Author: jeffrey@hottomali.com
*          
* Created:  07.22.2010
* Last updated:  10.06.2010
* 
* Description:  Geographical store locator using Google map APIs.
* 
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Store Locator';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "store-locator";
	
	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'Use our store locator map to find our stores.';
	 
	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'store locator';
	
  /**
   * Module styles, separated by space
   */
  $config['css']       = "storelocator.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "storelocator.js";
  
  /**
   * Tables.
   **/
  $config['tables']['stores']  = 'cms_dStorecode';
  
/* End of file storelocator.php */
/* Location: ./system/application/config/storelocator.php */
