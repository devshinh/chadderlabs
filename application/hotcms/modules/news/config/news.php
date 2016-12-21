<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * News Config
 *
 * Description:  News management
 */

/**
 * Module title
 */
$config['module_title']       = 'News';

/**
 * Module default URL
 */
$config['module_url']		   = "news";

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
$config['tables']['category'] = 'news_category';
$config['tables']['news'] = 'news';
$config['tables']['draft'] = 'news_draft';
$config['tables']['revision'] = 'news_revision';
$config['tables']['user'] = 'user';
$config['tables']['news_item'] = 'news_item';
$config['tables']['training_item'] = 'training';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'news' => array('label' => 'News', 'access' => 'manage_news')
);

/**
 * Default category ID
 */
$config['default_category_id'] = 1;

/* End of file news.php */

  