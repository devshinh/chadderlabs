<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Contact Config
*  
* 
*/

  /**
   * Module title
   */
  $config['module_title']  = 'Contact';
  
	/**
	 * Module default URL
	 */
	$config['module_url']		   = "contact";
	
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
  $config['css']       = 'contact.css';
  
  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = 'contact.js';

  /**
   * Tables.
   **/
  $config['tables']['contact']  = 'contact';
  
  
  $config['columns'] = array('phone', 'fax', 'ext', 'cell','email','twitter','website','address_1','address_2','city','province','postal_code','name','default');
      
/* End of file contact.php */

