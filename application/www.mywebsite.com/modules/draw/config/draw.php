<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Draw Config
 *
 * Description:  Draw management
 *
 */

/**
 * Module title
 */
$config['module_title']       = 'Draw';

/**
 * Module default URL
 */
$config['module_url']		   = "draw";

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
$config['css']       = "draw.css";

/**
 * Module Javascripts, separated by space
 */
$config['js']       = "draw.js";

/**
 * Tables
 */
$config['tables']['draw_winner'] = 'user_draw_winner';
$config['tables']['asset'] = 'asset';
$config['tables']['draws'] = 'user_draws';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'draw' => array('label' => 'Draw', 'access' => 'manage_draw')
);

/* End of file draw.php */
