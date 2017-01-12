<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Member Config
 *
 * Author: jeffrey@hottomali.com
 *
 * Created:  07/22/2010
 * Last updated:  07/26/2010
 *
 * Description:  Member module dealing with common member tasks such as login, logout, change password etc.
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'Member';

/**
 * module default URL
 */
$config['module_url']		   = "member";

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
$config['css']       = "member.css";

/**
 * Module Javascripts, separated by space
 */
$config['js']       = "member.js";

/**
 * Tables.
 */
$config['tables']['user'] = 'user';
$config['tables']['user_profile'] = 'user_profile';
$config['tables']['user_role'] = 'user_role';
