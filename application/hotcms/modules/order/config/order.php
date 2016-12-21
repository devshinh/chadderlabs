<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Order Config
 *
 * Description:  Order management
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'Order';

/**
 * Module default URL
 */
$config['module_url']		   = "order";

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
$config['css']       = "";

/**
 * Module Javascripts, separated by space
 */
$config['js']       = "";

/**
 * Tables
 */
$config['tables']['order'] = 'order';
$config['tables']['order_item'] = 'order_item';
$config['tables']['order_status'] = 'order_status';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'order' => array('label' => 'Orders', 'access' => 'manage_order')
);

/* End of file order.php */
