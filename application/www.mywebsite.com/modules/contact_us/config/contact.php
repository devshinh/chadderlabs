<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Contact Us Config
* 
* Author: jeffrey@hottomali.com
*          
* Created:  09.08.2010
* Last updated:  09.08.2010
* 
* Description:  Contact Us module.
* 
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Contact Us';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "contact-us";
	
	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'Contact Us';
	 
	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'Contact Us';
	
  /**
   * Module styles, separated by space
   */
  $config['css']       = "contact.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "contact.js";
  
  /**
   * Contact email address
   */
  $config['notice_email_from']     = "example@mywebsite.com";
  $config['notice_email_to']       = "jeffrey@hottomali.com";
  
  /**
   * Tables.
   **/
  $config['tables']['contact']  = 'contact_request';
