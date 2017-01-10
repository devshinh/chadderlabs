<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends HotCMS_Controller {

  function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute a helper function from a module and output contents in JSON format
   * Useful when need to refresh content on a widget using Ajax
   * The URL pattern is /ajax/module/method/param1/param2/...
   * The helper function naming pattern is module_method_ajax($param1, $param2)
   * @param str $method
   * @param array $params
   */
  public function _remap($method, $params = array())
  {
    if ($method == '' || !is_array($params) || count($params) == 0) {
      exit;
    }
    $module_name = strtolower($method);
    $helper_filename = APPPATH . 'modules/' . $module_name . '/helpers/' . $module_name . '_helper.php';
    if (!file_exists($helper_filename)) {
      exit;
    }
    $this->load->helper($module_name . '/' . $module_name);
    $func_name = array_shift($params);
    $ajax_function = $module_name . '_' . $func_name . '_ajax'; // Ajax function name pattern used in module helpers
    if (function_exists($ajax_function)) {
      try {
        $json = call_user_func_array($ajax_function, $params);
        echo json_encode($json);
      }
      catch (Exception $e) {}
    }
  }

}
?>
