<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Product Config
 *
 * Description:  Product management
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'Product';

/**
 * Module default URL
 */
$config['module_url']		   = "product";

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
$config['css']       = "product.css";

/**
 * Module Javascripts, separated by space
 */
$config['js']       = "product.js";

/**
 * Tables
 */
$config['tables']['product'] = 'product';
$config['tables']['product_category'] = 'product_category';
$config['tables']['product_asset'] = 'product_asset';
$config['tables']['asset'] = 'asset';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'product' => array('label' => 'Shop', 'access' => 'manage_product')
);

/* End of file product.php */
