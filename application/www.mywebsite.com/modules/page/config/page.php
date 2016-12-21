<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Page Config
 * 
 * Description:  Page management
 * 
 */

/**
  * Module title
  */
$config['module_title'] = 'Page';

/**
  * Module default URL
  */
$config['module_url'] = "page";

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
$config['css'] = "page.css";

/**
  * Module Javascripts, separated by space
  */
$config['js'] = "page.js";

/**
  * Tables
  */
$config['tables']['page'] = 'page';
$config['tables']['page_section'] = 'page_section';
$config['tables']['draft'] = 'page_draft';
$config['tables']['draft_section'] = 'page_draft_section';
$config['tables']['revision'] = 'page_revision';
$config['tables']['revision_section'] = 'page_revision_section';
$config['tables']['page_layout'] = 'page_layout';
$config['tables']['user'] = 'user';

/**
  * Admin menu/navigation
  */
$config['admin_menu'] = array(
  'page' => array('label' => 'Page Publisher', 'access' => 'manage_content')
);

/**
  * Default text for a new text area
  */
$config['demo_text'] = "<p>This is the default content for this text block.<br />Click here to edit.</p>";

/* End of file page.php */
