<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sitemap extends HotCMS_Controller {

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
    //$this->load->model('model_sitemap');
    @include(APPPATH.'config/routes.php');
  }

  /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function index()
  {
    //set the flash data error or notice messages if any
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
    
    // prepare meta information
    $this->aModuleInfo = array(
      'sName'            => '',
      'sURL'             => 'sitemap',
      'sTitle'           => 'Sitemap',
      'sMetaDescription' => 'Sitemap',
      'sMetaKeyword'     => 'Sitemap',
    );
    
    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data, 'sitemap' );
    //$this->output->enable_profiler(TRUE);
  }

  /**
   * Display page not found with a site map
   * @access public
   * @return void
   */
  public function page_not_found()
  {
    //set the flash data error or notice messages if any
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

    // prepare meta information
    $this->aModuleInfo = array(
      'sName'            => '',
      'sURL'             => 'page-not-found',
      'sTitle'           => 'Whoops!',
      'sMetaDescription' => 'Page Not Found',
      'sMetaKeyword'     => 'Page Not Found',
      'sContent'         => 'You&rsquo;ve landed on a page that does not exist. Click on one of the links below to get back on track and find more of the goods.',
    );
    
    $this->output->set_status_header('404');
    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data, 'sitemap' );
  }
  
  // wget http://domain.com/sitemap/xml
  // generate a new sitemap.xml.gz and submit it to www.google.com//webmasters/sitemaps/ping?sitemap=http://domain.com/sitemap.xml.gz  
  function xml()
  {
    $this->load->plugin('google_sitemap'); //Load Plugin
    $sitemap = new google_sitemap; //Create a new Sitemap Object
    if (is_null($this->aData['sMenuXml'])){
      self::setInfo( );
    }
    foreach ($this->aData['sMenuXml'] as $link){
      if (substr($link, 0, 4)=='http'){
        $item = new google_sitemap_item( $link, date("Y-m-d"), 'weekly', '0.8' ); //Create a new Item
      }else{
        $item = new google_sitemap_item( "http://".$_SERVER['HTTP_HOST'] . $link, date("Y-m-d"), 'weekly', '0.8' ); //Create a new Item
      }
      $sitemap->add_item($item); //Append the item to the sitemap object
    }
    $sitemap->build("./sitemap.xml"); //Build it...
            
    // compress it to gz
    $data = implode("", file("./sitemap.xml"));
    $gzdata = gzencode($data, 9);
    $fp = fopen("./sitemap.xml.gz", "w");
    fwrite($fp, $gzdata);
    fclose($fp);
    chmod("./sitemap.xml.gz", 0755);

    // Ping google
    $this->_pingGoogleSitemaps(base_url()."/sitemap.xml.gz");
  }

  function _pingGoogleSitemaps( $url_xml )
  {
    $status = 0;
    $google = 'www.google.com';
    if( $fp=@fsockopen($google, 80) )
    {
      $req =  'GET /webmasters/sitemaps/ping?sitemap=' .
               urlencode( $url_xml ) . " HTTP/1.1\r\n" .
               "Host: $google\r\n" .
               "User-Agent: Mozilla/5.0 (compatible; " .
               PHP_OS . ") PHP/" . PHP_VERSION . "\r\n" .
               "Connection: Close\r\n\r\n";
      fwrite( $fp, $req );
      while( !feof($fp) )
      {
        if( @preg_match('~^HTTP/\d\.\d (\d+)~i', fgets($fp, 128), $m) )
        {
          $status = intval( $m[1] );
          break;
        }
      }
      fclose( $fp );
    }
    return( $status );
  }
    
}
?>