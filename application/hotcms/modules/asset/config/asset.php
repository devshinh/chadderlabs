<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Asset Config
 *
 * Description: configurations for media library
 */

/**
 * Module title
 */
$config['module_title']       = 'Media Library';

/**
 * Module default URL
 */
$config['module_url']		   = "media-library";

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
$config['css']       = "media-library.css";

/**
 * Module Javascripts, separated by space
 */
$config['js']       = "media-library.js";

/**
 * Tables
 */
$config['table']['asset'] = 'asset';
$config['table']['asset_image_thumbnail'] = 'asset_image_thumbnail';
$config['table']['asset_alternative'] = 'asset_alternative';
$config['table']['asset_category'] = 'asset_category';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'media-library' => array('label' => 'Media Library', 'access' => 'manage_content')
);

//$config['thumbnails'][] = array();
$config['thumbnails'][] = array('height' => 30, 'width' => 30, 'keep_ratio' => true, 'crop' => false);
$config['thumbnails'][] = array('height' => 50, 'width' => 50, 'keep_ratio' => true, 'crop' => false);
$config['thumbnails'][] = array('height' => 200, 'width' => 200, 'keep_ratio' => true, 'crop' => false); //training main image
$config['thumbnails'][] = array('height' => 350, 'width' => 350, 'keep_ratio' => true, 'crop' => false); //training main image
$config['thumbnails'][] = array('height' => 98, 'width' => 80, 'keep_ratio' => true, 'crop' => false);  //training preview widgets + homepage carousel
$config['thumbnails'][] = array('height' => 100, 'width' => 180, 'keep_ratio' => false, 'crop' => true, 'x_axis' => 0 ,'y_axis'=>20);  //training list page

$config['upload']['allowed_types'] = 'gif|jpg|png';
$config['upload']['overwrite']     = true;
$config['application_path']       = "www.mywebsite.com";
$config['public_path']       = "asset";
$config['category_default'] = 1; //this has to match default category in asset_category.//must exist
$config['category_content'] = 2; //this has to match main content category in asset_category.//must exist

/* End of file storelocator.php */
/* Location: ./system/application/config/storelocator.php */
