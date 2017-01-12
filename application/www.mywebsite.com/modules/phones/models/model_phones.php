<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Phone Module Model
* 
* Author: jan@hottomali.com
*          
* Created:  09.01.2010
* Last updated:  09.01.2010
* 
* Description:  Phone model.
* 
*/

class Model_phones extends HotCMS_Model {
  
  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('phones', TRUE);
    $this->tables = $this->config->item('tables', 'phones');
  }
  
   public function get_phones(){
    $this->db->select();
    $this->db->join('cms_dImage', 'cms_dImage.nReferenceID = cms_dPhone.nPhoneID');
    $this->db->where('cms_dImage.nImageTypeID', 8);
    $this->db->order_by('cms_dPhone.nSequence');
    $query =  $this->db->get($this->tables['phone']);

    return $query->result();
  }   
  
   public function get_statements(){
    $this->db->select();
    $this->db->join('cms_dImage', 'cms_dImage.nReferenceID = cms_dStatement.nStatementID');
    $this->db->where('cms_dImage.nImageTypeID', 5);
    $this->db->order_by('','random');
    $query =  $this->db->get($this->tables['statement']);

    return $query->result();
  }    
  
   public function get_phone_ad(){
    $this->db->select();
    $this->db->join('cms_dImage', 'cms_dImage.nReferenceID = cms_dAd.nAdID');
    $this->db->where('cms_dImage.nImageTypeID', 3);
    $this->db->where('cms_dAd.dActive IS NOT NULL');
    $this->db->order_by('cms_dAd.nAdID','random');
    $query =  $this->db->get($this->tables['phoneAd']);

    return $query->result();
  }      
  
   public function get_phone($slug){
    $this->db->select();
    $this->db->join('cms_dImage', 'cms_dImage.nReferenceID = cms_dPhone.nPhoneID');
    $this->db->where('cms_dImage.nImageTypeID', 8);
    //$this->db->where('cms_dImage.nImageTypeID', 9);
    $this->db->where('cms_dPhone.sSlug', $slug);
    $query =  $this->db->get($this->tables['phone']);

    return $query->result();
  }   
  
   public function get_phone_promo($id){
    $this->db->select();
    $this->db->where('cms_dImage.nImageTypeID', 9);
    $this->db->where('cms_dImage.nReferenceID', $id);
    $query =  $this->db->get($this->tables['images']);

    return $query->result();
  }  
  
  public function get_phone_assets($id){
    $where = 'dActive IS NOT NULL AND sDocument IS NULL AND nPhoneID = '.$id;
    $this->db->select();
    //$this->db->where('cms_dPhoneAsset.sDocument', 'IS NULL');
    //$this->db->where('cms_dPhoneAsset.dActive', 'IS NOT NULL');
    //$this->db->where('cms_dPhoneAsset.nPhoneID', $id);
    $this->db->where($where);
    $this->db->order_by('cms_dPhoneAsset.nSequence');
    $query =  $this->db->get($this->tables['phoneAsset']);
    
    return $query->result();

  }
}
?>