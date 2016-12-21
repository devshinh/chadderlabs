<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Carousel Module Config
*
* Author: jeffrey@hottomali.com
*
* Created:  01.31.2012
* Last updated:  01.31.2012
*
* Description:  This module displays a carousel.
*
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Carousel';

	/**
	 * Module default URL
	 */
	$config['module_url']		   = "carousel";

	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'Carousel';

	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'Carousel';

  /**
   * Module styles, separated by space
   */
  $config['css']       = "carousel.css";

  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "carousel.js";

  /**
   * Tables.
   */
  $config['tables']['carousel']  = 'carousel';
  $config['tables']['carousel_group']  = 'carousel_group';

  /**
   * An image folder under /asset/upload/
   */
  $config['image-folder']       = "carousel";
