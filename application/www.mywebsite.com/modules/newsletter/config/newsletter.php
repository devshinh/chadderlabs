<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  E-Newsletter Config
* 
* Author: jeffrey@hottomali.com
*          
* Created:  09.08.2010
* Last updated:  09.08.2010
* 
* Description:  E-newsletter sign up module.
* 
*/

  /**
   * Module title
   */
  $config['module_title']       = 'E-Newsletter Sign Up';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "sign-up";
	
	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'E-Newsletter Sign Up';
	 
	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'E-Newsletter Sign Up';
	
  /**
   * Module styles, separated by space
   */
  $config['css']       = "newsletter.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "newsletter.js";
  
  /**
   * Tables.
   **/
  $config['tables']['recipient']  = 'cms_dNewsletterRecipient';
