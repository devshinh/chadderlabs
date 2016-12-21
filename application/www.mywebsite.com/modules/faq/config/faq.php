<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  FAQ Module Config
* 
* Author: jan@hottomali.com
*          
* Created:  08.26.2010
* Last updated:  11.29.2011
* 
* Description:  FAQ module.
* 
*/

  /**
   * Module title
   */
  $config['module_title']       = 'FAQ';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "faq";
	
	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'Got questions? We\'ve got answers. View our FAQ for information about our services.';
	 
	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'faq,frequently asked questions';
	
  /**
   * Module styles, separated by space
   */
  $config['css']       = "faq.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "faq.js";
  
  /**
   * Tables.
   */
  $config['tables']['faq']  = 'faq';
  $config['tables']['faq_group']  = 'faq_group';

