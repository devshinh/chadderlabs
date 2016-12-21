<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Account Config
 *
 * Author: jeffrey@hottomali.com
 *
 * Created:  04/18/2010
 * Last updated:  04/19/2010
 *
 * Description:  dealing with common tasks such as login, logout, my profile, change password, etc.
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'My Account';

/**
 * module default URL
 */
$config['module_url']		   = "account";

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
$config['js']       = "account.js";

/**
 * Tables.
 */
$config['tables']['user'] = 'user';
$config['tables']['user_profile'] = 'user_profile';
$config['tables']['user_role'] = 'user_role';
$config['tables']['role'] = 'role';
$config['tables']['points'] = 'user_points';
$config['tables']['retailer'] = 'retailer';
$config['tables']['store'] = 'retailer_store';
$config['tables']['province'] = 'province';
$config['tables']['country'] = 'country';
$config['tables']['contact'] = 'contact';
$config['tables']['user_points'] = 'user_points';
$config['tables']['user_order'] = 'order';
$config['tables']['draws'] = 'user_draws';
$config['tables']['retailer_role'] = 'retailer_role';
$config['tables']['log_login'] = 'log_login';
$config['tables']['draw_history'] = "user_draw_history";
$config['tables']['user_session_log'] = "user_session_log";
$config['tables']['quiz'] = 'quiz';
$config['tables']['quiz_history'] = 'quiz_history';