<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Phone Module Config
* 
* Author: jan@hottomali.com
*          
* Created:  09.01.2010
* Last updated:  09.01.2010
* 
* Description:  Phone module.
* 
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Phones';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "phones";
	
	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'Wireless Phones';
	 
	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'Wireless, Phones';
	
  /**
   * Module styles, separated by space
   */
  $config['css']       = "phones.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "phones.js";
  
  /**
   * Tables.
   **/
  $config['tables']['phone']  = 'cms_dPhone';
  $config['tables']['phoneAsset']  = 'cms_dPhoneAsset';
  $config['tables']['statement']  = 'cms_dStatement';
  $config['tables']['phoneAd']  = 'cms_dAd';
  $config['tables']['images']  = 'cms_dImage';
  
/* End of file promotion.php */
/* Location: ./system/application/config/promotion.php */
