<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Quote Config
 *
 * Description:  Quote management
 */

  /**
   * Module title
   */
  $config['module_title']       = 'Quote';

	/**
	 * Module default URL
	 */
	$config['module_url']		   = "quote";

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
  $config['tables']['quote'] = 'quote_form';
  $config['tables']['question'] = 'quote_question';
  $config['tables']['request'] = 'quote_request';
  $config['tables']['detail'] = 'quote_request_detail';

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
  $config['lines_per_column']       = 12;

/* End of file quote.php */
