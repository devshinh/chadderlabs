<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Randomizer Module Config
*
* Author: jeffrey@hottomali.com
*
* Created:  01.25.2012
* Last updated:  01.25.2012
*
* Description:  This module displays random image or text.
*
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Random Facts';

	/**
	 * Module default URL
	 */
	$config['module_url']		   = "randomizer";

	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'Random facts.';

	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'Random facts';

  /**
   * Module styles, separated by space
   */
  $config['css']       = "";

  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "";

  /**
   * Tables.
   */
  $config['tables']['random_item']  = 'random_item';
  $config['tables']['random_group']  = 'random_group';

  /**
   * A default image folder under /asset/upload/image/
   */
  $config['default-folder']       = "randomizer";
