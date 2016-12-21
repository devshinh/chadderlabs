<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asset_item {

	public $id;
	public $type;
	public $asset_category_id;
	public $name;
	public $description;
  public $create_date;
  public $update_date;
  public $category_name;
	public $file_name;
	public $extension;
	public $width;
	public $height;
	public $poster;
	public $folder_path;
	//public $file_path;
	public $full_path;
	public $full_html;
	public $thumb_html;
	public $thumb = '';

	public function __construct($identifier = NULL, $live_only = TRUE)
  {
    $this->ci =& get_instance();
    $this->ci->load->model('asset/model_asset');
    $this->ci->load->model('asset/model_asset_category');
    if (is_numeric($identifier)) {
      $this->id = $identifier;
      $row = $this->load($live_only);
      $this->init($row);
    }
    elseif (is_object($identifier)) {
      $this->init($identifier);
    }
  }

  /**
   * Retrieves an asset record from database
   * @param  bool  if true, load published assets only
   * @return void
   */
  public function load($live_only = TRUE)
  {
    if ($this->id < 1) {
      return FALSE;
    }
    $row = $this->ci->model_asset->get_asset_by_id($this->id, $live_only);
    return $row;
  }

  /**
   * Initialize an asset object from a database record
   * @param  object database row
   * @return bool
   */
  public function init($row)
  {
    if ($row) {
      $this->id = $row->id;
      $this->type = $row->type;
      $this->asset_category_id = $row->asset_category_id;
      $this->name = $row->name;
      $this->description = $row->description;
      $this->create_date = $row->create_date;
      $this->update_date = $row->update_date;
      $this->file_name = $row->file_name;
      $this->extension = $row->extension;
      $this->width = $row->width;
      $this->height = $row->height;
      $this->poster = $row->poster;
      //$category = $this->ci->model_asset_category->get_category_by_id($this->asset_category_id);
      $this->category_name = $row->category_name;

      $category_folder = '';
      //if ($row->path > '') {
      //  $category_folder = $row->path . '/';
      //}
      $public_path = $this->ci->config->item('public_path', 'asset'); // e.g. asset
      $site_path = $this->ci->session->userdata( 'sitePath' ); // e.g. upload
      $public_path = '/' . $public_path . '/' . $site_path . '/';
      $this->folder_path = $public_path;
      $this->full_path = $public_path . $category_folder . $this->file_name . '.' . $this->extension;
      $site_url = $this->ci->session->userdata('siteURL');
      if (substr($site_url, 0, 4) != 'http') {
        $site_url = 'http://' . $site_url;
      }

      switch ($this->type) {
        case 1:   // image
          $this->full_html = sprintf('<img src="%s%s" alt="%s" />',
            $site_url, $this->full_path, $this->description);
          $thumbnail_record = $this->ci->model_asset->image_asset_get_thumbnail($this->id, 50, 50);
          if ($thumbnail_record) {
            $this->thumbnail = $thumbnail_record->folder;
            $this->thumb_html = sprintf('<img src="%s%s/%s.%s" alt="%s" class="asset_selector" />',
              $site_url, $this->thumbnail, $this->file_name.'_thumb', $this->extension, $this->description);
          }
          else {
            $this->thumb_html = $this->full_html;
          }
          $thumbnail_medium = $this->ci->model_asset->image_asset_get_thumbnail($this->id, 200, 200);
          if ($thumbnail_medium) {
            $this->thumb = $thumbnail_medium->folder;
          }
          else {
            $this->thumb = $this->full_html;
          }
          break;
        case 3:   // video
          //$this->thumb_html = sprintf('<a href="%s%s" title="%s" target="_blank" class="asset_selector">%s.%s</a>',
          //  $site_url, $this->full_path, $this->name, $this->file_name, $this->extension);
          $video_formats = array('mp4' => 'mp4', 'mp4_sd' => 'mp4_sd', 'mp4_hd' => 'mp4_hd', 'webm_sd' => 'webm_sd', 'webm_hd' => 'webm_hd');
          $poster = '';
          $source = '';
          $quality = '';
          if ($this->poster > '') {
            $poster = 'poster="' . $site_url . $public_path . $category_folder . $this->poster . '"';
          }
          if (array_key_exists($this->extension, $video_formats)) {
            $source = sprintf('<source src="%s%s" data-quality="hd" />', $site_url, $this->full_path);
          }
          $alternatives = $this->ci->model_asset->asset_list_alternatives($this->id);
          foreach ($alternatives as $alt) {
            if (array_key_exists($alt->format, $video_formats)) {
              $fmt = $alt->format;
              $this->$fmt = $alt->file_name . '.' . $alt->extension;
              if (substr($alt->format, -3) == '_hd') {
                $quality = 'data-quality="hd"';
              }
              else {
                $quality = '';
              }
              $source .= sprintf('<source src="%s%s.%s" %s />', $site_url, $public_path . $category_folder . $alt->file_name, $alt->extension, $quality);
            }
          }
          if ($source > '') {
            $this->full_html = sprintf('<video id="video_%d" class="sublime" controls preload="auto"
              width="%d" height="%d" %s data-setup="{}">%s</video>', $this->id, $this->width, $this->height, $poster, $source);
            $this->thumb_html = sprintf('<img src="%s%s%s%s" alt="%s" class="asset_selector" width="100" />',
              $site_url, $public_path, $category_folder, $this->poster, $this->name);
            //$this->thumb_html = sprintf('<video id="video_%d" class="video-js vjs-default-skin" controls preload="auto"
            //  width="240" height="180" %s data-setup="{}">%s</video>', $this->id, $poster, $source);
            $this->lightbox_html = sprintf('<video id="video_%d" style="display:none" class="sublime lightbox" controls preload="auto"
              width="%d" height="%d" %s data-setup="{}">%s</video>', $this->id, $this->width, $this->height, $poster, $source);
          }
          else {
            $this->thumb_html = $this->full_html = 'Video format not supported.';
          }
          break;
        case 4:   // audio
          $audio_formats = array('mp3' => 'mp3');
          $poster = '';
          $source = '';
          if ($this->poster > '') {
            $poster = 'poster="' . $site_url . $public_path . $category_folder . $this->poster . '"';
          }
          if (array_key_exists($this->extension, $audio_formats)) {
            $source = sprintf('<source src="%s%s" type="audio/%s" />', $site_url, $this->full_path, $audio_formats[$this->extension]);
          }
          $alternatives = $this->ci->model_asset->asset_list_alternatives($this->id);
          foreach ($alternatives as $alt) {
            if (array_key_exists($alt->extension, $audio_formats)) {
              $source .= sprintf('<source src="%s%s.%s" type="audio/%s" />', $site_url, $public_path . $category_folder . $alt->file_name, $alt->extension, $audio_formats[$alt->extension]);
            }
          }
          if ($source > '') {
            $this->full_html = sprintf('<audio id="audio_%d" class="audio-asset default-skin" controls preload="auto"
              width="640" height="480" %s >%s</audio>', $this->id, $poster, $source);
            $this->thumb_html = sprintf('<a href="%s%s" title="%s" target="_blank" class="asset_selector">%s.%s</a>',
              $site_url, $this->full_path, $this->name, $this->file_name, $this->extension);
            //$this->thumb_html = sprintf('<audio id="audio_%d" class="audio-asset default-skin" controls preload="auto"
            //  width="240" height="180" %s >%s</audio>', $this->id, $poster, $source);
          }
          else {
            $this->thumb_html = $this->full_html = 'Unknown audio format.';
          }
          break;
        default:  // 2 - document
          switch ($this->extension) {
            case 'doc':
            default:
              $doc_icon = 'pdf-icon.jpg';
          }
          $this->full_html = sprintf('<a href="%s%s" title="%s" target="_blank" class="asset_selector">%s.%s</a>',
            $site_url, $this->full_path, $this->name, $this->file_name, $this->extension);
          $this->thumb_html = sprintf('<a href="%s%s" title="%s" target="_blank" class="asset_selector"><img src="/asset/images/%s" /></a>',
            $site_url, $this->full_path, $this->name, $doc_icon);
      }
      return TRUE;
    }
    return FALSE;
  }

  public static function list_all_items($filters = FALSE, $page_num = 1, $per_page = 1000)
  {
    $CI =& get_instance();
    $rows = $CI->model_asset->get_all_assets($filters, $page_num, $per_page);
    $assets = array();
    foreach ($rows as $row) {
      $asset = new self($row);
      $assets[$row->id] = $asset;
    }
    return $assets;
  }

  public static function list_all_images($category_id = 0, $page_num = 1, $per_page = 1000)
  { 
    return self::list_all_items($category_id, $page_num, $per_page);
  }

  public static function count_all_items($filters = FALSE)
  {
    $CI =& get_instance();
    return $CI->model_asset->count_all_assets($filters);
  }
  
  public static function list_all_files($category_id = 0, $page_num = 1, $per_page = 1000)
  { 
    return self::list_all_items($category_id, $page_num, $per_page);
  }  

  /**
   * Get a random item from given category
   * @param  object database row
   * @return bool
   */
  public static function get_random_item_from_category($category_id = 0)
  {
    $image = NULL;
    $CI =& get_instance();
    $row = $CI->model_asset->get_random_item_from_catgegory($category_id);
    if ($row) {
      $asset = new self($row->id);
    }
    return $asset;
  }

}