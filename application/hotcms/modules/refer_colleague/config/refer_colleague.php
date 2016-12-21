<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Refer Colleague Config
 *
 * Description:  Refer Colleague management
 */

  /**
   * Module title
   */
  $config['module_title']       = 'Refer Colleague';

	/**
	 * Module default URL
	 */
	$config['module_url']		   = "refer-colleague";

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
   * Tables.
   */
  $config['tables']['refer_col'] = 'refer_colleague';
  $config['tables']['draws'] = 'user_draws';  
  $config['tables']['feed'] = 'user_points';
  $config['tables']['user'] = 'user';
  $config['tables']['profile'] = 'user_profile';
  

  /**
   * Text to show in a dropdown first (empty) line
   */
  $config['dropdown_hint']       = "Select One";

  /**
   * Lines per column, if the fields are grouped in two columns
   * Each input/dropdown field is counted as 1 line,
   * label on top of the field is counted as 2 lines,
   * textarea field is counted using its actual rows
   */
  $config['lines_per_column']       = 10;
  
/* End of file quote.php */
