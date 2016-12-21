<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Location Config
*
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Location';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "location";
	
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
  $config['css']       = "location_edit.css";
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "location.js";

  /**
   * Tables.
   **/
  $config['tables']['location']  = 'location';
  $config['tables']['location_user']  = 'user_location';
  $config['tables']['user']  = 'user';
  $config['tables']['user_profile']  = 'user_profile';
  $config['tables']['hours']  = 'operation_hours';
  
  /**
   * google maps API key
   */
  $config['google_maps_api_key']       = "AIzaSyCQhBQKr8v6c3OuWnjeNNUFMMYjUBRW9uY";