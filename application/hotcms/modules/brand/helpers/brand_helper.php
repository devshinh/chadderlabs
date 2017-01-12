<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Brand helper
 */
/**
 * Sitemap
 * @return array
 */
if (!function_exists('brand_sitemap')) {

    function brand_sitemap($url = '') {
        $CI = & get_instance();
        $CI->load->model('retailer/retailer_model');
        $link_array = array();

        //string(7) "store/*"
        //string(16) "retailer-state/*"
        if ($url == 'retailer/*') {
            //brand detail pages
            $rows = $CI->retailer_model->retailer_list(FALSE, TRUE);
            foreach ($rows as $row) {
                if ($row->status == 1) {
                    $link_array[] = array(
                        'slug' => $row->slug,
                        'title' => $row->name,
                    );
                }
            }
        }
        if ($url == 'retailer-state/*') {
            //brand detail pages
            $rows = $CI->retailer_model->retailer_list(FALSE, TRUE);
            foreach ($rows as $row) {
                if ($row->status == 1) {
                    $provinces = $CI->retailer_model->list_retailer_provinces($row->id);
                    foreach($provinces as $p){
                        $link_array[] = array(
                            'slug' => $row->slug.'/'.strtolower($p),
                            'title' => $row->name,
                        );
                    }
                }
            }
        }    
        if ($url == 'store/*') {
            //brand detail pages
            $rows = $CI->retailer_model->get_active_stores();
            foreach ($rows as $store) {
                if ($store->status == 1) {

                        $link_array[] = array(
                            'slug' => strtolower($store->ret_slug).'/'.strtolower($store->country_code).'/'.strtolower($store->province).'/'.strtolower($store->slug).'/'.strtolower($store->id),
                            'title' => $store->store_name,
                        );

                }
            }
        }           
        return $link_array;
    }

}
