<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Shop Model
 *
 * Author: jeffrey@hottomali.com
 *
 * Created:  04.12.2011
 * Last updated:  05.10.2011
 *
 * Description:  Shop model.
 *
 */

class Shop_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('shop/shop', TRUE);
    $this->tables = $this->config->item('tables', 'shop');
  }

  /**
   * Save order information into the database
   */
  public function new_order($order_array, $items_array)
  {
    $order_array['order_status'] = 'in_checkout';
    //$order_array['payment_method'] = 'CC';
    $order_array['create_timestamp'] = time();
    $order_array['update_timestamp'] = time();
    $order_array['client_ip'] = $_SERVER['REMOTE_ADDR'];

    // calculate taxes based on province
    $order_array['tax_name'] = ''; //self::tax_class($order_array['billing_province']);
    $order_array['tax_amount'] = 0; //self::calc_tax((float)$order_array['subtotal'], $order_array['billing_province']);
    $order_array['order_total'] = (float)$order_array['subtotal'] + (float)$order_array['tax_amount'];

    $result = $this->db->insert($this->tables['order'], $order_array);
    if ($result) {
      $orderID = $this->db->insert_id();
      foreach ($items_array as $item) {
//        $option = '';
//        if (is_array($item['options']) && $item['options']['phone'] > '') {
//          $option = strip_number($item['options']['phone']);
//        }
        $item_array = array(
          'order_id' => $orderID,
          'product_id' => $item['id'],
          'product_name' => $item['name'],
          'qty' => $item['qty'],
          'price' => $item['price'],
          //'option' => $option,
        );
        $result = $this->db->insert($this->tables['order_item'], $item_array);
      }
      // reserve order id for updating order detail later
      $this->session->set_userdata('order_id', $orderID);
      //$this->session->set_userdata('order_city', $order_array['billing_city']);
      //$this->session->set_userdata('order_postal', $order_array['billing_postal']);
      // reserve province code for tax calculation
      $this->session->set_userdata('province_code', $order_array['billing_province']);
    }

    return $result;
  }

  /**
   * Read order information and items from database
   */
  public function load_order($order_id = NULL, $status = NULL)
  {
    if (is_null($order_id)) {
      $order_id = (int)$this->session->userdata('order_id');
    }
    if ($order_id > 0) {
      $conditions = array('id' => $order_id);
      if ($status > '') {
        $conditions['order_status'] = $status;
      }
      $order = $this->db->get_where($this->tables['order'], $conditions)->row();
      if ($order) {
        $items = $this->db->get_where($this->tables['order_item'], array('order_id' => $order_id))->result();
        $order->items = $items;
      }
    }
    return $order;
  }

  /**
   * Read order information and items from database
   */
  public function load_last_order($user_id = NULL)
  {
    if ($user_id > 0) {    
      //$order = $this->db->get_where($this->tables['order'], array('user_id' => $user_id))->orderby('id', 'DESC')->limit(1)->row();
    $order_query = $this->db->select()
            ->where('user_id', $user_id)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get($this->tables['order']);
    $order = $order_query->row();
      if ($order) {
        //$items = $this->db->get_where($this->tables['order_item'], array('order_id' => $order->id))->result();
          $items = $this->db->select()
                  ->where('order_id', $order->id)
                  ->get($this->tables['order_item']);
        $order->items = $items->result();
      }
    }
    return $order;
  }

  /**
   * Update order information
   */
  public function update_order($order_id, $order_array, $items_array = NULL)
  {
    $result = $this->db->update($this->tables['order'], $order_array, array('id'=>$order_id), 1);

    /*
    if ($result) {
      $orderID = $this->db->insert_id();
      foreach ($items_array as $item) {
        if (is_array($item['options']) && $item['options']['phone'] > '') {
          $phone = strip_number($item['options']['phone']);
        }
        $item_array = array(
          'order_id' => $orderID,
          'nProductID' => $item['id'],
          'sTitle' => $item['name'],
          'sOption' => $phone,
          'nQty' => $item['qty'],
          'nPrice' => $item['price'],
        );
        $result = $this->db->insert($this->tables['order_item'], $item_array);
      }
    } */

    return $result;
  }

  /**
   * Read transaction information from database
   */
  public function load_transaction($tran_id)
  {
    if ($tran_id > '') {
      $filter = array('nTransactionID' => $tran_id);
      $results = $this->db->get_where($this->tables['transaction'], $filter)->result();
      $transaction = $results[0];
    }
    return $transaction;
  }

  /**
   * Get user tax class based on their province
   */
  public function tax_class($province = NULL)
  {
    if (is_null($province)) {
      $province = $this->session->userdata('province_code');
    }
    if ($province > '') {
      //$results = $this->db->get_where($this->tables['tax'], array('province_code' => $province))->result();
      $query = $this->db->select('name')
        ->where('province_code', $province)
        ->get($this->tables['tax']);
      $result = $query->row()->name;
    }
    return $result;
  }

  /**
   * Get user tax class based on their country/province
   */
  public function calc_tax($subtotal = 0, $province = NULL)
  {
    $result = 0;
    if (is_null($province)) {
      $province = $this->session->userdata('province_code');
    }
    if ($subtotal > 0 && $province > '') {
      $results = $this->db->get_where($this->tables['tax'], array('province_code' => $province))->result();
      $rate = $results[0]->rate;
      $result = $subtotal * $rate;
    }
    return $result;
  }

  /**
   * Process in_checkout order: deduct points, email receipt and update stock number
   * @param  object  $order
   * @return bool
   */
  public function process_order($order)
  {
    $result = FALSE;
    if ($order && $order->order_status == 'in_checkout') {
      $items = $this->db->get_where($this->tables['order_item'], array('order_id' => $order->id))->result();
      // deduct points here
      $this->load->helper('account/account');
      $description = 'placed an order and spent ' . number_format($order->order_total, 0) . ' points.';
      $result = account_add_points($order->user_id, '-' . $order->order_total, 'order', $this->tables['order'], $order->id, $description);
      if ($result) {
        // if all look good, change order status from "in_checkout" to "pending"
        $update_array = array('order_status' => 'pending', 'update_timestamp' => time());
        $result = $this->db->update($this->tables['order'], $update_array, array('id' => $order->id), 1);
      }
      //TODO: send out a confirmation email
      $this->email_receipt($order, $items);
      foreach ($items as $item){
          //get old stock for id
            $query = $this->db->select('stock')
            ->where('id', $item->product_id)
            ->get($this->tables['product']);
            $old_stock = $query->row()->stock;
          //update with new qty
          $new_stock = (int)$old_stock - $item->qty;
          if($new_stock > 0){
            $query= $this->db->set('stock',$new_stock)
                    ->where('id', $item->product_id)
                    ->update($this->tables['product']);
          }else{
            $query= $this->db->set('stock',0)
                    ->where('id', $item->product_id)
                    ->update($this->tables['product']);          
          }
      }
      
    }
    return $result;
  }
  
  /* Get number of orders for user
   * 
   * @param int user_id
   * 
   * @return int # of orders
   */
  
  public function get_order_count_user_id($user_id){
      $query = $this->db->select('id')
              ->where('user_id',$user_id)
              ->get($this->tables['order']);
      return $query->num_rows();
  }

  /**
   * List provinces
   */
  public function list_provinces()
  {
    $provinceCode = array(
      'AB' => 'Alberta',
      'BC' => 'British Columbia',
      'MB' => 'Manitoba',
      'NB' => 'New Brunswick',
      'NL' => 'Newfoundland and Labrador',
      'NS' => 'Nova Scotia',
      'ON' => 'Ontario',
      'PE' => 'Prince Edward Island',
      'SK' => 'Saskatchewan',
      'NT' => 'Northwest Territories',
      'NU' => 'Nunavut',
      'QC' => 'Quebec',
      'YT' => 'Yukon'
    );
    return $provinceCode;
  }

  /**
   * List shipping options
   */
  public function list_shipping_methods($order_type = 2) {
    // shipping methods for SIM cards
    $arrayCard = array(
        'CRDGROUND' => 'Ground Shipping: $4.00 + tax',
        'CRDEXPRESS' => 'Air Express: $17.00 + tax');
    // shipping methods for phone devices
    $arrayDevice = array(
        'WSTGROUND' => 'Ground Shipping: $13.50 + tax, 1-3 business days',
        'WSTEXPRESS' => 'Air Express: $21.50 + tax',
        'CTRGROUND' => 'Ground Shipping: $16.75 + tax, 3-5 business days',
        'CTREXPRESS' => 'Air Express: $23.50 + tax',
        'ESTGROUND' => 'Ground Shipping: $24.00 + tax, 5-6 business days',
        'ESTEXPRESS' => 'Air Express: $29.00 + tax');
    switch ($order_type) {
      case 1:
        return $arrayCard;
        break;
       case 2:
        return $arrayDevice;
        break;
      default:
        return array();
    }
  }

  /**
   * email receipt
   */
  private function email_receipt($order, $items, $errors = '')
  {
      //$from_address = "example@example.com";
      //$from_name = "Example.com";

      $order_total = ''.number_format($order->order_total,0).' points';
      //$order_tax = ''. number_format($order->tax,0).' points';
      $order_subtotal = ''.number_format($order->subtotal,0).' points';
      $order_date = date("Y-m-d", $order->create_timestamp);
      //$transaction_date = date('Y-m-d h:i:s a', $order->create_timestamp);

  //<tr><td style="padding-top; 5px; padding-bottom: 5px; padding-left: 20px; padding-right: 20px; background: white; font-size: 9pt; width: 730px;">
  //Please retain this receipt for your records. If this message is not displaying properly, <a style="color: #36B8EA; text-decoration: none" href="https://{$_SERVER['HTTP_HOST']}/my-account">click here <img border="0" width="8" height="8" alt="Click here" src="http://www.example.com/asset/images/link_arrow.png" /></a></td></tr>
      $message_send = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <title>Your purchase receipt</title>
  </head>
  <body style="font: 13px/18px arial, sans-serif; background-color: #e5e5e5; width:100%; margin: 0; padding: 0; color:#4e4e4e">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
	<td style="padding-top: 30px; padding-left: 20px; padding-bottom: 30px;">
	  <table cellspacing="0" cellpadding="0" border="0" style="width: 750px; background: white; border-right: 2px solid #DDD; border-bottom: 2px solid #BBB">
	    <tr>
	      <td width="730px" style="padding-top: 12px; padding-left: 10px">
		<a href="http://www.cheddarlabs.com"><img width="730px" height="175px" src="http://{$_SERVER['HTTP_HOST']}/asset/images/purchase-email-header_03.jpg" alt="Thank you" /></a>
	      </td>
	    </tr>
	    <tr>
	      <td style="padding-left: 10px; padding-top: 10px; padding-right: 10px; valign="top">
		<table cellpadding="0" cellspacing=0" align="left" width="730px;" style="background-color: white;">
		  <tr>
		    <td>
		      <table width="730px" border="0" align="left" cellspacing="0" cellpadding="0" style="padding-right: 20px; padding-left: 10px; border-right: solid 4px #FFF; background-color: white">
			<tr>
			  <td style="padding-top: 20px"><h2 style="font-size: 1.25em; color: #b39451">Congratulations on your purchase.</h2></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 5px;">Purchase Date: {$order_date}</td>
			</tr>
			<tr>
			  <td style="padding-bottom: 5px;">Order No: {$order->id}</td>
			</tr>
			<tr>
			  <td style="padding-left: 0px; padding-top: 12px">
			    <table width="700" border="0" align="left" cellspacing="0" cellpadding="0">
			      <tr>
				<td colspan="2" style="padding: 12px; color: #FFF; background-color: #dbdbdb; font-weight: bold">Order Summary</td>
			      </tr>
EOF;
      $line_number = 0;
      foreach($items as $item) {
	    $item_price = ''.number_format($item->price,0).' points';
	    $item_error_begin = "";
	    $item_error_end = "";
	    if (!empty($errors[$line_number]) && $errors[$line_number]) {
	      $item_error_begin = '<span style="color: red">* ';
	      $item_error_end = '</span>';
	    }
	    $item_option = !empty($item->option) ? ' <br />' . format_phone_number($item->option) : "";
	    $message_send .= <<<EOF
	    <tr valign="top">
	      <td style="padding-left:12px;border-bottom: 1px dotted #CCC; padding-top: 5px; padding-bottom: 5px;">{$item_error_begin}{$item->qty} X {$item->product_name}{$item_option}{$item_error_end}</td>
	      <td style="padding-right:12px;width: 120px; text-align: right; border-bottom: 1px dotted #CCC; padding-top: 5px; padding-bottom: 5px">{$item_error_begin}{$item_price}{$item_error_end}</td>
	    </tr>
EOF;
	    $line_number++;
	}
	$message_send .= <<<EOF
			      <tr>
				<td style="padding-top: 5px; text-align: right;">Subtotal</td>
				<td style="padding-top: 5px; width: 120px; text-align: right; ">{$order_subtotal}
				</td>
			      </tr>
			      <tr>
				<td style="padding-top: 5px; padding-bottom: 5px; text-align: right; border-bottom: 1px dotted #CCC; font-weight: bold; color: #b2954f">Order Total</td>
				<td style="padding-top: 5px; padding-bottom: 5px; width: 120px; text-align: right; border-bottom: 1px dotted #CCC; font-weight: bold;">{$order_total}</td>
			      </tr>
			    </table>
			  </td>
			</tr>
			<tr>
			  <td style="border-top: 1px dotted #CCC; padding-bottom: 5px; padding-top: 5px; font-weight: bold; color: #b3945">Bill to:</td>
			</tr>
			<tr>
			  <td>{$order->billing_firstname} {$order->billing_lastname}</td>
			</tr>
			<tr>
			  <td>{$order->billing_street1}</td>
			</tr>
EOF;
      if(!empty($order->billing_street2)) {
	$message_send .= <<<EOF
      <tr>
	<td>{$order->billing_street2}</td>
      </tr>
EOF;
      }
      $message_send .= <<<EOF
			<tr>
			  <td>{$order->billing_city}, {$order->billing_province}</td>
			</tr>
			<tr>
			  <td style="padding-bottom: 50px;">{$order->billing_postal}</td>
			</tr>
		      </table>
		    </td>
		    <td valign="top" style="padding-left: 20px; padding-right: 20px; padding-top: 20px; background-color: white">

		    </td>
		  </tr>
		</table>
	      </td>
	    </tr>
	  </table>
	</td>
      </tr>
      <tr>
	<td style="border-top: 1px dotted #CCC; padding-top; 5px; padding-left: 20px; padding-right: 20px; background: white; font-size: 10pt; width: 730px;"></td>
      </tr>
    </table>
  </body>
</html>
EOF;
      //mail($order->billing_email, "Purchase Receipt", $message_send, $headers);
        
        $this->postmark->clear();
        $config['mailtype'] = "html";
        $this->postmark->initialize($config);
        $this->postmark->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->postmark->to($order->billing_email);
        $this->postmark->bcc('jan@hottomali.com');
        $this->postmark->subject('Your Cheddar Labs Swag Purchase');
        $this->postmark->message_html($message_send);

        if ($this->postmark->send()) {
            return TRUE;
        } else {
            return FALSE;
        }      
              
        
  }
}
