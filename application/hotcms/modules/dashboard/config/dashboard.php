<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Dashboard Config
*
*
*/

  /**
   * Module title
   */
  $config['module_title']  = 'Dashboard';

	/**
	 * Module default URL
	 */
	$config['module_url']		   = "dashboard";

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
  $config['css']       = 'dashboard.css';

  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = 'dashboard.js https://www.google.com/jsapi';

  /**
   * Tables.
   */
  $config['tables']['order'] = 'order';
  $config['tables']['order_item'] = 'order_item';
  $config['tables']['referral'] = 'refer_colleague';

  
/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'dashboard' => array('label' => 'Dashboard', 'access' => 'manage_content')
);  


/**
 * The first date Cheddar Labs goes live online.
 */
$config['first_date'] = "11/01/2012";

/* End of file dashboard.php */

