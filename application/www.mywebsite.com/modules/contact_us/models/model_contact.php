<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Contact Us Model
 * 
 * Author: jeffrey@hottomali.com
 *          
 * Created:  09.08.2011
 * Last updated:  09.08.2011
 * 
 * Description:  Contact Us module.
 * 
 */

class Model_contact extends HotCMS_Model {
  
  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('contact', TRUE);
    $this->tables = $this->config->item('tables', 'contact');
  }
  
  /**
   * process user contact request
   */
  public function contact_request($firstname, $lastname, $email, $postal, $concerns, $comment) {
    if (is_array($concerns) && count($concerns)) {
      $concern = implode(', ', $concerns);
    }
    $logged = $this->log_request($firstname, $lastname, $email, $postal, $concern, $comment);
    $sent = $this->notice_email($firstname, $lastname, $email, $postal, $concern, $comment);
    $result = ($logged & $sent);
    return $result;
  }
  
  /**
   * add user contact request to database
   */
  private function log_request($firstname, $lastname, $email, $postal, $concerns, $comment) {
    $data = array(
      'firstname'  => $firstname,
      'lastname'   => $lastname,
      'email'      => $email,
      'postalcode' => $postal,
      'concerns'   => $concerns,
      'comment'    => $comment,
    );
    $result = $this->db->insert($this->tables['contact'], $data);
    return $result;
  }

  /**
   * send a notice email to an admin
   */
  private function notice_email($firstname, $lastname, $email, $postal, $concerns, $comment) {
    $this->load->library('email');

    $subject = 'Contact Us Form Submission';
    $message = 'First Name: ' . $firstname . "\n";
    $message .= 'Last Name: ' . $lastname . "\n";
    $message .= 'Email: ' . $email . "\n";
    $message .= 'Postal Code: ' . $postal . "\n";
    $message .= 'Enquiry Concerns: ' . $concerns . "\n";
    $message .= 'Comments: ' . $comment . "\n";

    $to = $this->config->item('notice_email_to', 'contact');
    $this->email->from($this->config->item('notice_email_from', 'contact'), 'Example.com');
    $this->email->to($to);
    $this->email->subject($subject);
    $this->email->message($message);

    return true; //$this->email->send(); // no email on my local environment
  }

}
?>