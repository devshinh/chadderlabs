<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auction Module Config
*
* Author: jeffrey@hottomali.com
*
* Created:  12/12/2011
* Last updated:  03/20/2012
*
* Description:  auction module.
*
*/

  /**
   * Module title
   */
  $config['module_title']       = 'Auction';

	/**
	 * Module default URL
	 */
	$config['module_url']		   = "auction";

	/**
	 * Meta description in the HTML head section
	 */
	$config['meta_description']         = 'auction and bid';

	/**
	 * Meta keyword in the HTML head section
	 */
	$config['meta_keyword']             = 'auction, bid';

  /**
   * Module styles, separated by space
   */
  $config['css']       = "auction.css";

  /**
   * Module Javascripts, separated by space
   */
  $config['js']       = "auction.js";

  /**
   * Tables
   */
  $config['tables']['auction']  = 'auction';
  $config['tables']['auction_bid']  = 'auction_bid';
  $config['tables']['auction_category']  = 'auction_category';
  $config['tables']['auction_item']  = 'auction_item';
  $config['tables']['auction_item_asset']  = 'auction_item_asset';
  $config['tables']['asset']  = 'asset';
  $config['tables']['user']  = 'user';
  $config['tables']['user_profile']  = 'user_profile';

  /**
   * Simple email templates
   * TODO: move these into database
   */
  $config['outbid_notice'] = '<html>
<body>
	<p>Hi %s,</p>
	<p>Someone has outbid you on %s</p>
	<p><a href="http://auction.hottomali.com/%s">http://auction.hottomali.com/%3$s</a></p>
	<p>Hot Tomali & MWCT</p>
</body>
</html>';