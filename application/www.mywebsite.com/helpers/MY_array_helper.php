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
   * @param  string $direction            order of sorting
   * @return array  sorted array
   */
  function object_array_sort($object_array, $shared_property_name = "id", $direction = "asc") {
    usort($object_array, array(new Sorter($shared_property_name, $direction), "sort_obj"));
    return $object_array;
  }
}

if ( !function_exists("array_2d_sort")) {
  /**
   * Sort two dimentions array by named key.
   * @param  array  $array_2d        to sort
   * @param  string $shared_key_name arrays' shared key-name to sort by
   * @param  string $direction       order of sorting
   * @return array  sorted array
   */
  function array_2d_sort($array_2d, $shared_key_name = "id", $direction = "asc") {
    usort($array_2d, array(new Sorter($shared_key_name, $direction), "sort_array"));
    return $array_2d;
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

class Sorter {
  private $key;
  private $direction;

  function __construct($key, $direction = "asc") {
    $this->key = $key;
    $this->direction = $direction;
  }

  function sort_obj($a, $b) {
    if (strcasecmp($this->direction, "asc") === 0) {
      return strnatcasecmp($a->{$this->key}, $b->{$this->key});
    } else {
      return strnatcasecmp($b->{$this->key}, $a->{$this->key});
    }
  }

  function sort_array($a, $b) {
    if (strcasecmp($this->direction, "asc") === 0) {
      return strnatcasecmp($a[$this->key], $b[$this->key]);
    } else {
      return strnatcasecmp($b[$this->key], $a[$this->key]);
    }
  }
}