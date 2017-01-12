<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sitemap {

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function generateXML() {
        //$this->CI->load->library('session');

        $aURL = explode('/', $this->CI->config->item('base_url'));
        $url = $aURL[0] . '//' . $aURL[2];

        $site_id = $this->CI->session->userdata('siteID');

        $query = $this->CI->db->where('id', $site_id)
                ->where('active', 1)
                ->get('site');
        $oSite = $query->row();
        if (!empty($oSite)) {
            $url = 'http://' . $oSite->domain;
            //var_dump($oSite);
            $query = $this->CI->db->where('site_id', $site_id)
                    ->where('status', 1)
                    ->order_by('update_timestamp', 'DESC')
                    ->get('page');

            $aPage = $query->result();

            $url_array = array();
            foreach ($aPage as $row) {
                if ($row->exclude_sitemap == 1) {
                    continue;
                }
                if (preg_match('[\*]', $row->url)) {
                    if ($row->url_parser > '') {
                        $helper_filename = APPPATH . 'modules/' . $row->url_parser . '/helpers/' . $row->url_parser . '_helper.php';
                        if (!file_exists($helper_filename)) {
                            continue;
                        }
                        $this->CI->load->helper($row->url_parser . '/' . $row->url_parser);
                        $sitemap_helper = $row->url_parser . '_sitemap';
                        if (function_exists($sitemap_helper)) {
                            try {
                                $sub_pages = $sitemap_helper($row->url);
                                if (is_array($sub_pages) && count($sub_pages) > 0) {
                                    foreach ($sub_pages as $sp) {
                                        $url_array[] = array(
                                            'loc' => $url . '/' . str_replace('*', $sp['slug'], $row->url),
                                            'priority' => '0.3'
                                        );
                                    }
                                }
                            } catch (Exception $e) {
                                
                            }
                        }
                    }
                } else {
                    $url_array[] = array(
                        'loc' => sprintf('%s/%s', $url, $row->url),
                        'priority' => '0.5'
                    );
                }
            }
//var_dump($url_array);
//die();
            // generate file
            $xml = str_replace('xml_header', '<?xml version="1.0" encoding="UTF-8" ?>', $this->CI->load->view('_sitemap', array('aPage' => $url_array, 'url' => $url), TRUE));
            $sitemap_name = 'sitemap_' . $site_id . '.xml';
            $fh = fopen('../www.mywebsite.com/' . $sitemap_name, 'w+');
            fwrite($fh, $xml);
            fclose($fh);
        }
    }

}

?>
