<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Quiz Config
 *
 * Description:  Quiz management
 */

/**
 * Module title
 */
$config['module_title'] = 'Quiz';

/**
 * Module default URL
 */
$config['module_url'] = "quiz";

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
$config['js'] = "quiz.js";

/**
 * Tables
 */
$config['tables']['type'] = 'quiz_type';
$config['tables']['type_section'] = 'quiz_type_section';
$config['tables']['quiz'] = 'quiz';
$config['tables']['question'] = 'quiz_question';
$config['tables']['history'] = 'quiz_history';
$config['tables']['history_detail'] = 'quiz_history_detail';
$config['tables']['user'] = 'user';
$config['tables']['training'] = 'training';
$config['tables']['user_role'] = 'user_role';
$config['tables']['site'] = 'site';
$config['tables']['target'] = 'target';

/**
 * Admin menu/navigation
 */
$config['admin_menu'] = array(
  'quiz' => array('label' => 'Quiz', 'access' => 'manage_quiz')
);

/* End of file quiz.php */
