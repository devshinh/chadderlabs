<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Refer_colleague_model extends HotCMS_Model {

  private $tables;

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('refer_colleague/refer_colleague', TRUE);
    $this->tables = $this->config->item('tables', 'refer_colleague');
  }

    /**
     * Check to see if a quote slug already exists
     * @param  str   quote slug
     * @param  int   exclude quote id
     * @return bool
     */
    public function slug_exists($slug, $exclude_id = 0) {
        $query = $this->db->select('id')
                ->where('site_id', $this->site_id)
                ->where('slug', $slug);
        if ($exclude_id > 0) {
            $this->db->where('id != ', $exclude_id);
        }
        $query = $this->db->get($this->tables['quote']);
        return $query->num_rows();
    }

    /**
     * Get a random slug for showcase purpose
     */
    public function get_random_slug() {
        $query = $this->db->select('slug')
                ->where('status', 1)
                ->where('site_id', $this->site_id)
                ->order_by('', 'random')
                ->limit(1)
                ->get($this->tables['quote']);
        if ($query->num_rows > 0) {
            $result = $query->row()->slug;
        } else {
            $result = '';
        }
        return $result;
    }

    /**
     * Process quote request
     * @param  object  quote object
     * @param  array  form postback
     * @return bool
     */
    public function process_refer($attr, $user_id) {

        // insert general information
        $site_id = (int) ($this->site_id);
        $this->db->set('site_id', $site_id);
        $this->db->set('first_name', array_key_exists('firstname', $attr) ? $attr['firstname'] : '');
        $this->db->set('last_name', array_key_exists('lastname', $attr) ? $attr['lastname'] : '');
        $this->db->set('email', array_key_exists('email', $attr) ? $attr['email'] : '');
        $this->db->set('referal_user_id', $user_id);
        $this->db->set('create_timestamp', time());
        $inserted = $this->db->insert($this->tables['refer_col']);

        return $inserted;
    }

    /**
     * Email a refer coleague 
     * @param  string  email address
     * @param  object  quote object
     * @param  array  form postback
     * @return bool
     */
    public function email_request($refer_info, $user_fullname) {



        $message = '<p>Hey ' . $refer_info['firstname'] . ',</p>';

        $message .='<p>You really should join me in becoming a member of <a href="http://www.cheddarlabs.com/" title="Cheddar Labs | Homepage">Cheddar Labs</a>. The site gives you the opportunity to learn about the products we sell and earn points that can be exchanged for awesome prizes. We already do a great job &mdash; why not get rewarded for it? Speaking of rewards, I get bonus points if you sign up and verify your employment. So, help me out here. I want free stuff!</p>';
        $message .='<p>Joining only takes about 2 minutes and it\'s free. Simply <a href="http://www.cheddarlabs.com/signup">click here</a> to register!</p>';
        $message .='<p>Cheers,</p>';
        $message .='<p>' . $user_fullname . '</p>';
        $message .='<p style="font-size: 8pt;"><em>Cheddar Labs takes you privacy seriously. We will not contact you again, nor allow any other referrals to this email address unless you consent to further communications when registering at <a href="http://www.cheddarlabs.com/" title="Cheddar Labs | Homepage">cheddarlabs.com</a>.</em></p>';

        $this->postmark->clear();
        $config['mailtype'] = "html";
        $this->postmark->initialize($config);
        $this->postmark->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->postmark->to($refer_info['email']);
        $this->postmark->bcc('jan@hottomali.com');
        $this->postmark->subject('One of your co-workers thinks you should check this out');
        $this->postmark->message_html($message);

        if ($this->postmark->send()) {
            return TRUE;
        } else {
            return FALSE;
        }

    }
    
    public function add_to_feed($user_id, $ref_id){

        $this->db->set('point_type', 'reffer_colleague');
        $this->db->set('ref_table', 'reffer_colleague');
        $this->db->set('ref_id', $ref_id);
        $this->db->set('user_id', $user_id);
        $this->db->set('create_timestamp', time());
        $this->db->set('description', 'refered a colleague.');
        $inserted = $this->db->insert($this->tables['feed']);
        
        
        return $inserted;
    }    

//  /**
//   * Check if user was refered by other user
//   * @param  string  email address
//   * @return ind user id (0 when not found)
//   */  
//  public function check_referal($email){
//          $query = $this->db->select('referal_user_id')
//            ->where('email', $email)
//            ->get($this->tables['refer_col']);
//          $query->row()->user_id;
//  }
  
  /**
   * check_referral() - check if user was refered by someone
   * @param string email
   * @return array user info
   */
  public function check_referral($email,$short = FALSE){
    $this->db->select('id,referal_user_id');
    $this->db->where($this->tables['refer_col'] . '.email', $email);
    $query = $this->db->get($this->tables['refer_col']);
    
    if($short){
      $result = $query->row();
    }else{
        if ($query->num_rows > 0) {
            //check if user wasn't already awarded
            $previous_ref = $this->check_previous_referral($query->row()->id, $query->row()->referal_user_id);
            if ($previous_ref){
                $result = $query->row();
            }else{
                $result = 0;
            }
        }else{
          $result = 0;  
        }
    }
    
    return $result;
  }
  
  /**
   * check_previous_referral() - check if user got reffer coleague bonus
   * @param int id of referral
   * @param int id of user who did the referral
   * @return bool true when user is eligible for award
   */  
  public function check_previous_referral($id, $user_id){
    $this->db->select('id');
    $this->db->where('point_type', 'reffer_veri');
    $this->db->where('user_id', $user_id);
    $this->db->where('ref_id', $id);
    $query = $this->db->get($this->tables['draws']);       

    if($query->num_rows > 0) {
        return FALSE;
    }else{
        return TRUE;
    }
   }  

  /**
   * get_number_of_referrals() - get amount of succesfull referrals for user id
   * @param int id of user who did the referral
   * @return int numeber of referrals
   */     
   public function get_number_of_referrals($user_id){
    $this->db->select('id');
    $this->db->where('point_type', 'reffer_veri');
    $this->db->where('user_id', $user_id);
    $query = $this->db->get($this->tables['draws']);   
    
    return $query->num_rows();
   }
   
    /**
     * Get history of referal for user profile page
     * @param  int  user_id
     * @return array all refered users
     */    
    
    public function referal_user_history($user_id){
        
        $query = $this->db->select()
                ->where('referal_user_id', $user_id)
                ->order_by('create_timestamp','DESC')
                ->get($this->tables['refer_col']);
        if ($query->num_rows > 0) {
            return $query->result();  
        } else {
            return FALSE;  
        }
              
    }    
    

     /**
     * Check state for referal
     * @param  string refered email
     * @return int (0 - not started, 1 - registered, 2 - verified)
     */    
    
    public function referal_user_check($email){
        
        $query = $this->db->select('u.id, p.verified')
                ->join($this->tables['profile'] . ' p', 'p.user_id = u.id')
                ->where('u.email', $email)
                ->get($this->tables['user'].' u');
        if ($query->num_rows == 0) {
            return 0;
        } else {
           
            if($query->row()->verified == 1){
                return 2;
            }else{
                return 1;
            }
        }
        return 0;     
    }  
    
    public function get_user_screen_name($email){
        $query = $this->db->select('p.screen_name')
                ->join($this->tables['profile'] . ' p', 'p.user_id = u.id')
                ->where('u.email', $email)
                ->get($this->tables['user'].' u'); 
        return $query->row()->screen_name;
    }

    public function get_draw_amount($id){
        $query = $this->db->select('draws')
                ->where('ref_table', 'refer_colleague')
                ->where('ref_id', $id)
                ->get($this->tables['draws']); 
        return $query->row()->draws;
    }   
  
}

?>