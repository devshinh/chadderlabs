<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Shopping Cart Module Config
*
* Author: jeffrey@hottomali.com
*
* Created:  04.07.2011
* Last updated:  04.01.2011
*
* Description:  Shopping Cart module.
*
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Shopping Cart';

	/**
	 * Module default URL
	 */
	$config['module_url']		   = "shop";

	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'Shopping Cart';

	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'Shopping Cart';

  /**
   * Module styles, separated by space
   */
  $config['css']       = "shop.css";

  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "shop.js";

  /**
   * Contact email address
   */
  $config['notice_email_from']       = "example@example.com";
  $config['notice_email_to']       = "example@example.com";

  /**
   * Shopping cart route and alias
   * The URL of the shopping cart, e.g. shopping/my-cart ($config['module_route']/$config['cart_alias'])
   */
	$config['module_route']		   = "shop";
	$config['cart_alias']		   = "cart";

  /**
   * Tables.
   **/
  $config['tables']['product']  = 'product';
  $config['tables']['category']  = 'product_category';
  $config['tables']['order']  = 'order';
  $config['tables']['order_item']  = 'order_item';
  //$config['tables']['transaction']  = 'transaction';
  //$config['tables']['secure_log']  = 'secure_log';
  $config['tables']['tax']  = 'tax';
