<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Creates a new category and returns the new ID.
 */
if (!function_exists('asset_insert_category'))
{
  function asset_insert_category( $args=array() )
  {
    $CI =& get_instance();
    $CI->load->model('asset/model_asset');

    if (is_array($args) && array_key_exists('name', $args) && array_key_exists('path', $args)) {
      $category_id = $CI->model_asset->insert_category($args['name'], $args['path']);
      return $category_id;
    }
  }
}

/**
 *
 */
if (!function_exists('asset_list_categories'))
{
  function asset_list_categories( $args=array() )
  {

    $CI =& get_instance();
    $CI->load->model('asset/model_asset_category');

    if (is_array($args) && array_key_exists('context', $args)) {
      $categories = $CI->model_asset_category->get_system_generated_categories($args['context']);
      return $categories;
    }
  }
}

/**
 * Returns upload form fields
 */
if (!function_exists('asset_upload_fields'))
{
  function asset_upload_fields( $args=array() )
  {
    $data['hidden_fields'] = array();
    $data['hidden_fields']['asset_type'] = array_key_exists('asset_type', $args) ? $args['asset_type'] : '1';
    $data['hidden_fields']['asset_category_id'] = array_key_exists('asset_category_id', $args) ? $args['asset_category_id'] : '';
    $data['hidden_fields']['asset_description'] = '';
    //$data['asset_type'] = array('1'  => 'image');
    $data['asset_file_input'] = array(
      'name'        => 'asset_file',
      'id'          => 'asset_file',
      'value'       => array_key_exists('asset_file', $args) ? set_value( 'asset_file', $args['asset_file'] ) : NULL,
      'maxlength'   => 100,
      'size'        => 20,
    );
    $data['asset_name_input'] = array(
      'name'        => 'asset_name',
      'id'          => 'asset_name',
      'value'       => array_key_exists('asset_name', $args) ? set_value( 'asset_name', $args['asset_name'] ) : NULL,
      'maxlength'   => 100,
      'size'        => 20,
      'class'       => 'text'
    );
    /*
    $data['asset_description_input'] = array(
      'name'        => 'asset_description',
      'id'          => 'asset_description',
      'value'       => array_key_exists('asset_name', $args) ? set_value( 'asset_description', $args['asset_description'] ) : NULL,
      'rows'        => '5',
      'cols'        => '20',
      'class'       => 'textarea'
    ); */
    return $data;
  }
}

/**
 * Displays an image upload user interface.
 */
if (!function_exists('asset_upload_ui'))
{
  function asset_upload_ui( $args=array() )
  {
    $CI =& get_instance();
    $CI->load->library('asset/asset_item');
    $data['fields'] = asset_upload_fields( $args );
    if (is_array($args) && array_key_exists('asset_type', $args) && array_key_exists('asset_category_id', $args)) {
      $asset_type = (int)($args['asset_type']);
      $asset_category_id = (int)($args['asset_category_id']);
      if ($asset_type > 0 && $asset_category_id > 0) {
        $data['asset_type'] = $asset_type;
        $data['asset_category_id'] = $asset_category_id;
        return $CI->load->view('asset/asset_widget_upload', $data, TRUE);
      }
    }
  }
}

/**
 * Displays an image listing user interface.
 */
if (!function_exists('asset_images_ui'))
{
  function asset_images_ui( $args=array() )
  {
    $CI =& get_instance();
    if (is_array($args) && array_key_exists('asset_category_id', $args)) {
      $asset_category_id = (int)($args['asset_category_id']);
      if ($asset_category_id > 0) {
        $data['asset_category_id'] = $asset_category_id;
        $CI->load->library('asset/asset_item');
        $data['images'] = Asset_item::list_all_images($asset_category_id);
        if (array_key_exists('single_selection', $args)) {
          $data['single_selection'] = $args['single_selection'];
        }
        $formatted = $CI->load->view('asset/asset_widget_images', $data, TRUE);
        $raw = array();
        foreach ($data['images'] as $k => $v) {
          $raw[$v->id] = $v->name;
        }
        return array('raw' => $raw, 'formatted' => $formatted);
      }
    }
  }
}

/**
 * Displays an image management user interface.
 */
if (!function_exists('asset_management_ui'))
{
  function asset_management_ui( $args=array() )
  {
    $CI =& get_instance();
    $data['fields'] = asset_upload_fields( $args );
    if (is_array($args) && array_key_exists('asset_category_id', $args)) {
      $asset_category_id = (int)($args['asset_category_id']);
      if ($asset_category_id > 0) {
        $data['asset_category_id'] = $asset_category_id;
        $CI->load->library('asset/asset_item');
        $data['images'] = Asset_item::list_all_images($asset_category_id);
        return $CI->load->view('asset/asset_widget_images', $data, TRUE);
      }
    }
    return $CI->load->view('asset/asset_management', $data, TRUE);
  }
}

/**
 * Load an asset item.
 * @param int asset ID
 * @return mixed
 */
if (!function_exists('asset_load_item'))
{
  function asset_load_item( $id )
  {
    $id = (int)$id;
    if ($id > 0) {
      $CI =& get_instance();
      $CI->load->model('asset/model_asset');
      $CI->load->library('asset/asset_item');
      $asset = new Asset_item($id);
      return $asset;
    }
    return FALSE;
  }
}

/**
 * Delete an asset item.
 */
if (!function_exists('asset_delete_item'))
{
  function asset_delete_item( $id )
  {
    $id = (int)$id;
    if ($id > 0) {
      $CI =& get_instance();
      $CI->load->model('asset/model_asset');
      $CI->model_asset->delete_by_id($id);
    }
  }
}

/**
 * Displays an image list.
 */
if (!function_exists('asset_tinymce_images_ui'))
{
  function asset_tinymce_images_ui( $args=array() )
  {
    $CI =& get_instance();
    if (is_array($args) && array_key_exists('asset_category_id', $args)) {
      $asset_category_id = (int)($args['asset_category_id']);
      if ($asset_category_id > 0) {
        $data['asset_category_id'] = $asset_category_id;
        $CI->load->library('asset/asset_item');
        $data['images'] = Asset_item::list_all_images($asset_category_id);
        if (array_key_exists('single_selection', $args)) {
          $data['single_selection'] = $args['single_selection'];
        }
        return $CI->load->view('asset/asset_tinymce_images', $data, TRUE);
      }
    }
  }
}

/**
 * Displays a file upload user interface.
 *
if (!function_exists('asset_file_upload_ui'))
{
  function asset_file_upload_ui( $args=array() )
  {
    $CI =& get_instance();
    $CI->load->library('asset/asset_item');
    $data['fields'] = asset_upload_fields( $args );
    if (is_array($args) && array_key_exists('asset_type', $args) && array_key_exists('asset_category_id', $args)) {
      $asset_type = (int)($args['asset_type']);
      $asset_category_id = (int)($args['asset_category_id']);
      if ($asset_type > 0 && $asset_category_id > 0) {
        $data['asset_type'] = $asset_type;
        $data['asset_category_id'] = $asset_category_id;
        return $CI->load->view('asset/asset_widget_upload', $data, TRUE);
      }
    }
  }
} */

/**
 * Displays a file listing user interface.
 */
if (!function_exists('asset_files_ui'))
{
  function asset_files_ui( $args=array() )
  {
    $CI =& get_instance();
    if (is_array($args) && array_key_exists('asset_category_id', $args)) {
      $asset_category_id = (int)($args['asset_category_id']);
      if ($asset_category_id > 0) {
        $data['asset_category_id'] = $asset_category_id;
        $CI->load->library('asset/asset_item');
        if (array_key_exists('asset_type', $args)) {
          $asset_type = (int)($args['asset_type']);
        }
        else {
          $asset_type = 0;
        }
        $data['files'] = Asset_item::list_all_files($asset_category_id);
        if (array_key_exists('single_selection', $args)) {
          $data['single_selection'] = $args['single_selection'];
        }
        $formatted = $CI->load->view('asset/asset_widget_files', $data, TRUE);
        //$raw = $data['files'];
        $raw = array();
        foreach ($data['files'] as $k => $v) {
          $raw[$v->id] = $v->name;
        }
        return array('raw' => $raw, 'formatted' => $formatted);
      }
    }
  }
}