<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Widget Library
 *
 */
class Widget
{
  public $module_path;

  public function run($file) {
    $args = func_get_args();
    $module = '';

    // is module in filename?
    if (($pos = strrpos($file, '/')) !== FALSE) {
      $module = substr($file, 0, $pos);
      $file = substr($file, $pos + 1);
    }

    list($path, $file) = Modules::find($file, $module, 'widgets/');
    if ($path === FALSE) {
      $path = APPPATH.'widgets/';
    }

    Modules::load_file($file, $path);

    $file = ucfirst($file);
    $widget = new $file();

    $widget->module_path = $path;

    return call_user_func_array(array($widget, 'run'), array_slice($args, 1));
  }

  public function render($view, $data = array(), $as_string = TRUE) {
    extract($data);
    if ($as_string) {
      ob_start();
    }
    $path = str_replace('/widgets/', '/', $this->module_path);
    include $path . 'views/' . $view . EXT;
    if ($as_string) {
      $buffer = ob_get_clean();
      return $buffer;
    }
  }

  public function load($object) {
    $this->$object = load_class(ucfirst($object));
  }

  public function __get($var) {
    global $CI;
    return $CI->$var;
  }
}