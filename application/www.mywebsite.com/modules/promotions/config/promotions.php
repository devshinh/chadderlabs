<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Promotion Module Config
* 
* Author: jan@hottomali.com
*          
* Created:  08.26.2010
* Last updated:  08.26.2010
* 
* Description:  Promotion module.
* 
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Promotions';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "promotions";
	
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
  $config['css']       = "promotions.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "promotions.js";
  
  /**
   * Tables.
   **/
  $config['tables']['promotion']  = 'cms_dPromotion';
  
/* End of file promotion.php */
/* Location: ./system/application/config/promotion.php */
