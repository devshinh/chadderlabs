<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  E-Newsletter Config
* 
* Author: jeffrey@hottomali.com
*          
* Created:  09.08.2010
* Last updated:  09.08.2010
* 
* Description:  E-newsletter sign up module.
* 
*/

class Model_newsletter extends HotCMS_Model {
  
  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('newsletter', TRUE);
    $this->tables = $this->config->item('tables', 'newsletter');
  }
  
  /**
  * sign up for the newsletter
  */
  public function register($firstname, $lastname, $email, $postal, $phone, $nonumber, $signupfrom='newsletter'){
    $query = $this->db->select('sEmail')
      ->where('sEmail', $email)
      ->get($this->tables['recipient']);
    $emails = $query->num_rows();
    if ($emails == 0){
      $data = array(
        'sFirstName' => $firstname,
        'sLastName' => $lastname,
        'sEmail'    => $email,
        'sPostalcode'  => $postal,
        'sPhone'  => $phone,
        'bNoNumber'   => $nonumber,
        'sSignupFrom'   => $signupfrom,
      );
      $result = $this->db->insert($this->tables['recipient'], $data);
      return $result;
    }
    else{
      // already signed up, fine
      return true;
    } 
  }

}
?>