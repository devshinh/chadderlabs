<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Promotion Module Model
* 
* Author: jan@hottomali.com
*          
* Created:  08.26.2010
* Last updated:  08.26.2010
* 
* Description:  Promotion module.
* 
*/

class Model_promotions extends HotCMS_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('promotions', TRUE);
    $this->tables = $this->config->item('tables', 'promotions');
  }
  
   public function get_main_promotion(){

    $this->db->select();
    $this->db->join('cms_dImage', 'cms_dImage.nReferenceID = cms_dPromotion.nPromotionID');
    $this->db->where('cms_dImage.nImageTypeID', 6);
    $this->db->where('dActive >', '');
    $this->db->order_by('cms_dPromotion.nSequence');
    $query =  $this->db->get($this->tables['promotion'],1);
    return $query->result();
  } 
  
   public function get_small_promotions(){
    $this->db->select();
    $this->db->join('cms_dImage', 'cms_dImage.nReferenceID = cms_dPromotion.nPromotionID');
    $this->db->where('cms_dImage.nImageTypeID', 7);
    $this->db->where('dActive >', '');
    //$this->db->where('cms_dPromotion.nSequence >', 1);
    $this->db->order_by('cms_dPromotion.nSequence');
    $query =  $this->db->get($this->tables['promotion']);

    return $query->result();
  } 
  
   public function get_promotion($slug){
    $this->db->select();
    $this->db->join('cms_dImage', 'cms_dImage.nReferenceID = cms_dPromotion.nPromotionID');
    $this->db->where('cms_dImage.nImageTypeID', 6);
    $this->db->where('dActive >', '');
    $this->db->where('cms_dPromotion.sSlug', $slug);
    $this->db->order_by('cms_dPromotion.nSequence');
    $query =  $this->db->get($this->tables['promotion']);

    return $query->result();
  }   
  
}
?>