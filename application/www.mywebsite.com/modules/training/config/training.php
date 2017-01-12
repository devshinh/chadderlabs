<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Training Config
 *
 * Description:  Training management
 */

/**
 * Module title
 */
$config['module_title'] = 'Training';

/**
 * Module default URL
 */
$config['module_url'] = "training";

/**
 * Meta description in the HTML head section
 */
$config['meta_description'] = '';

/**
 * Meta keyword in the HTML head section
 */
$config['meta_keyword'] = '';

/**
 * Module styles, separated by space
 */
$config['css'] = "";

/**
 * Module Javascripts, separated by space
 */
$config['js'] = "training.js";

/**
 * Tables
 */
$config['tables']['category'] = 'training_category';
$config['tables']['subcategory'] = 'training_subcategory';
$config['tables']['revision'] = 'training_revision';
$config['tables']['tag'] = 'training_tag';
$config['tables']['tag_type'] = 'training_tag_type';
$config['tables']['training'] = 'training';
$config['tables']['training_live'] = 'training_live';
$config['tables']['training_asset'] = 'training_asset';
$config['tables']['training_resource'] = 'training_resource';
$config['tables']['training_tags'] = 'training_tags';
$config['tables']['variant'] = 'training_variant';
$config['tables']['variant_detail'] = 'training_variant_detail';
$config['tables']['variant_field'] = 'training_variant_field';
$config['tables']['user'] = 'user';
$config['tables']['asset'] = 'asset';
$config['tables']['site'] = 'site';
$config['tables']['page'] = 'page';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'training' => array('label' => 'Training', 'access' => 'manage_training')
);

/**
 * Module Javascripts, separated by space
 */
$config['field_types'] = array(
  'input' => 'Custom Input',
  'textarea' => 'Text Area',
  'dropdown' => 'Drop-Down',
  'checkbox' => 'Checkbox',
  'checkboxes' => 'Multiple Checkboxes',
  'radios' => 'Radio Buttons',
  'date' => 'Date',
  'image' => 'Image',
);

/* End of file training.php */
