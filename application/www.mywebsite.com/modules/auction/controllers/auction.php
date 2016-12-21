<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auction extends HotCMS_Controller {

  // module information
  protected $aModuleInfo;

  /**
   * Constructor method
   * @access public
   * @return void
   */
  public function __construct()
  {
    // call the parent's constructor method
    parent::__construct();
    // Load the required classes
    $this->load->config('auction', TRUE);
    //$this->load->model('auction_model');

    // prepare module information
    $this->aModuleInfo = array(
      'name'            => 'auction',
      'title'           => $this->config->item('module_title', 'auction'),
      'url'             => $this->config->item('module_url', 'auction'),
      'meta_description' => $this->config->item('meta_description', 'auction'),
      'meta_keyword'     => $this->config->item('meta_keyword', 'auction'),
      'style_sheet'      => $this->config->item('css', 'auction'),
      'javascript'      => $this->config->item('js', 'auction')
    );
  }

  /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function index($auction_slug = '', $category_slug = '', $item_slug = '')
  {
    die('TESTING');
    $this->load->helper('auction');
    $section_html = '';
    if (function_exists('auction_item_detail') && $item_slug > '') {
      $section_html = auction_item_detail($item_slug, $auction_slug, $category_slug);
    }
    elseif (function_exists('auction_item_list')) {
      $section_html = auction_item_list($auction_slug, $category_slug);
    }
    $this->data['html_content'] = $section_html;
    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data, 'wrapper' );
    //$this->load->view('wrapper', $this->data);
  }
}
?>