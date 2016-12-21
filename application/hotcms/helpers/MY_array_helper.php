<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Merge multiple arrays of objects with unqiue values.
 */
if ( !function_exists("array_objects_merge")) {
  function array_objects_merge() {
    $objects_arrays = func_get_args();
    return array_map("unserialize", array_unique(array_map("serialize", call_user_func_array("array_merge", $objects_arrays))));
  }
}

if ( !function_exists("object_array_sort")) {
  /**
   * Sort object array by named property.
   * @param  array  $object_array         to sort
   * @param  string $shared_property_name objects' shared property-name to sort by
   * @return array  sorted array
   */
  function object_array_sort($object_array, $shared_property_name = "id") {
    /**
     * Compare two objects by shared property.
     * @param  object $a                    first object to compare
     * @param  object $b                    second object to compare
     * @param  string $shared_property_name name of the shared property to compare with
     * @return int    compared result
     */
    function cmp($a, $b, $shared_property_name) {
      return strcmp($a->{$shared_property_name}, $b->{$shared_property_name});
    }
    return usort($object_array, create_function('$a, $b', 'return cmp($a, $b, '.$shared_property_name));
  }
}

if ( !function_exists("array_2d_sort")) {
  /**
   * Sort two dimentions array by named key.
   * @param  array  $array_2d         to sort
   * @param  string $shared_key_name  arrays' shared key-name to sort by
   * @return array  sorted array
   */
  function array_2d_sort($array_2d, $shared_key_name = "id") {
    /**
     * Compare two arrays by shared key.
     * @param  object $a               first array to compare
     * @param  object $b               second array to compare
     * @param  string $shared_key_name name of the shared property to compare with
     * @return int    compared result
     */
    function cmp($a, $b, $shared_key_name) {
      return strcmp($a->{$shared_key_name}, $b->{$shared_key_name});
    }
    return usort($array_2d, create_function('$a, $b', 'return cmp($a, $b, '.$shared_key_name));
  }
}

/**
 * Determine the parameter is empty, not aarray, or has not object
 */
if ( !function_exists("is_object_array")) {
  function is_object_array($object_array) {
    if ( !is_array($object_array)) {
      return FALSE;
    }
    if (empty($object_array)) {
      return FALSE;
    }
    $is_object = array_map("is_object", $object_array);
    if (empty($is_object)) {
      return FALSE;
    }
    return TRUE;
  }
}