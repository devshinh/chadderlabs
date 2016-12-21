<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shop extends HotCMS_Controller {

  /**
   * Constructor method
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      //redirect($this->config->item('login_page'));
      $messages = 'Sorry but only a logged user can shop online.';
      $json = array('result' => FALSE, 'messages' => $messages);
      return $json;
      //show_error('Sorry but only a verified user can shop online.');
    }
    if (!has_permission('view_content')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    // only verified users can shop online
    $user = $this->ion_auth->get_user();
    if ($user->verified != 1) {
      $messages = '<div class="message error">Sorry but only a verified user can shop online.</div>';
      $json = array('result' => FALSE, 'messages' => $messages);
      return $json;
    }

    $this->load->model('shop_model');
    $this->load->config('shop', TRUE);
    $this->load->helper('badge/badge');
    $this->load->helper('account/account');
    $this->module_header = $this->lang->line('hotcms_shop');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . " " . $this->lang->line('hotcms_shop');
    $this->module_url = $this->config->item('module_url', 'shop');
    $this->module_route = $this->config->item('module_route', 'shop');
    $this->cart_alias = $this->config->item('cart_alias', 'shop');
    $this->cart_url = $this->module_route . '/' . $this->cart_alias;

    //$this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'shop');
    //$this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'shop');

    // prepare module information
    $this->aModuleInfo = array(
      'name'             => 'shop',
      'title'            => $this->config->item('module_title', 'shop'),
      'url'              => $this->config->item('module_url', 'shop'),
      'meta_description' => $this->config->item('meta_description', 'shopping cart'),
      'meta_keyword'     => $this->config->item('meta_keyword', 'shopping cart'),
      'css'              => $this->config->item('css', 'shop'),
      'javascript'       => $this->config->item('js', 'shop'),
      'meta_title'       => $this->config->item('module_title', 'shop'),
    );
  }

  /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function index()
  {
    // this URL is reserved by an ordinary CMS page
    // if the page does not exist, redirect to cart page
    $this->cart();
    //redirect($this->cart_url);
    //exit;
  }

  /**
   * Displays shopping cart
   */
  public function cart()
  {
    // Validation rules
    $this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|xss_clean');

    if ($this->form_validation->run()) {
      $item = array(
        'rowid' => $this->input->post('rowid'),
        'id' => $this->input->post('product'),
        'qty' => $this->input->post('quantity'),
      );
      $result = $this->cart->update($item);
      if ($result) {
        redirect($this->cart_url);
        exit();
      }
    }
    else {
      // Return the validation error
      if (validation_errors()>'') {
        $this->add_message('error', validation_errors());
      }
    }

    $this->load->model('product/product_model');
    $cart_contents = $this->cart->contents();
    foreach ($cart_contents as $k => $v) {
      $product = $this->product_model->get_product($v['id']);
      if ($product->featured_image) {
        //var_dump($product->featured_image);
        $cart_contents[$k]['featured_image'] = $product->featured_image;
      }
      else {
        $cart_contents[$k]['featured_image'] = 'Images Placeholder';
      }
    }
    $data['cart'] = $cart_contents;

    // load module view
    $this->disable_browser_caching();
    self::loadModuleView( $this->aModuleInfo, $data, 'cart' );
  }

  /**
   * Adds an item to the shopping cart
   */
  public function add()
  {
    $pid = (int)($this->input->post('product'));
    $qty = (int)($this->input->post('quantity'));
    $this->_add($pid, $qty);
    redirect($this->cart_url);
  }

  /**
   * Adds an item to the shopping cart using Ajax
   */
  public function ajax_add()
  {
    $pid = (int)($this->input->post('product'));
    $qty = (int)($this->input->post('quantity'));
    $result = $this->_add($pid, $qty);
    echo json_encode($result);
  }

  /**
   * Adds an item to the shopping cart
   * @param int $pid
   * @param int $qty
   */
  private function _add($pid, $qty)
  {
    $result = FALSE;
    $messages = '';
    
    $user_id = (int)($this->session->userdata('user_id'));    
         if ($user_id <= 0) {
          $messages = '<div class="message error">Sorry, you must be logged in to shop.</div>';
           $json = array('result' => FALSE, 'messages' => $messages);
           return $json;          
        }
    // only verified users can shop online
    $user = $this->ion_auth->get_user();
    if ($user->verified != 1) {
      $messages = '<div class="message error">Sorry, only a verified user can shop online. Learn how to become verified <a href="http://earetailprofessionals.cheddarlabs.com/overview/verification">here</a>.</div>';
      $json = array('result' => FALSE, 'messages' => $messages);
      return $json;
    }        
        
    if ($pid > 0 && $qty > 0) {
      //TODO: load product helper instead of model
      $this->load->model('product/product_model');
      $this->load->helper('account/account');
      $product = $this->product_model->get_product($pid);
      $user_points = account_get_points($user_id);
      if ($product) {
        //validate product stock, user points etc.
        if ($product->stock >=0 && $product->stock < $qty) {
          $messages .= '<div class="message error">Sorry but there is not enough stock.</div>';
        }
        elseif ($product->price * $qty > $user_points) {
          $messages .= '<div class="message error">You do not have enough points.</div>';
        }
        else {
          $item = array(
            'id' => $pid,
            'qty' => $qty,
            'price' => $product->price,
            'name' => $product->name
          );
          if ($this->input->post('option')) {
            $item['options'] = array(
              'option_title' => $this->input->post('option'),
              'option_value' => $this->input->post('option'),
              'option_price' => $product->type
            );
          }
          $result = $this->cart->insert($item);
          if ($result) {
            $messages .= '<div class="message confirm">'.$product->name . ' has been added to your shopping cart.</div><br /><a class="btn btn-primary btn-large" href="/shop/cart">Proceed to Checkout</a>';
          }
          else {
            $messages .= '<div class="message error">Sorry but there was an error when try to add that item to your cart. Please try again.</div>';
          }
        }
      }
      else {
        $messages .= 'Invalid product.';
      }
    }
    $json = array('result' => $result, 'messages' => $messages);
    return $json;
  }

  /**
   * Removes an item from the shopping cart
   * @param  int  cart row ID
   */
  public function remove($rowid)
  {
    $this->cart->update(array(
      'rowid' => $rowid,
      'qty' => 0
    ));
    redirect($this->cart_url);
  }

  /**
   * Removes an item from the shopping cart using Ajax
   * @param  int  cart row ID
   */
  public function ajax_remove($rowid)
  {
    $result = $this->cart->update(array(
      'rowid' => $rowid,
      'qty' => 0
    ));
    $json = array('result' => $result);
    echo json_encode($json);
  }

/*
  public function total() {
    echo $this->cart->total();
  }

  public function destroy() {
    $this->cart->destroy();
    //echo "destroy() called";
  }
*/

  /**
   * Checkout step 1: billing information
   *
  public function checkout_billing()
  {
    if (empty($_SERVER['HTTPS']) && $_SERVER['HTTP_HOST'] != 'www.mywebsite.com') {
      header("Location:https://" . $_SERVER['HTTP_HOST'] . "/" . $this->module_route . "/checkout-billing");
      exit;
    }
    if ($this->cart->total() == 0) {
      redirect($this->cart_url);
    }

    $this->disable_browser_caching();
    $this->data['sTitle'] = "Checkout";
    $this->data['cart'] = $this->cart->contents();
    $this->data['subtotal'] = $this->cart->total();
    //$this->data['product'] = $this->input->post('product');

    // Validation rules
    $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|callback__alphanumeric_dashspace|xss_clean');
    $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|callback__alphanumeric_dashspace|xss_clean');
    $this->form_validation->set_rules('street1', 'Address Line 1', 'trim|required|callback__alphanumeric_address|xss_clean');
    $this->form_validation->set_rules('street2', 'Address Line 2', 'trim|callback__alphanumeric_address|xss_clean');
    $this->form_validation->set_rules('city', 'City', 'trim|required|callback__alphanumeric_dashspace|xss_clean');
    $this->form_validation->set_rules('province', 'Province', 'trim|required|callback__validate_province|xss_clean');
    $this->form_validation->set_rules('postal', 'Postal Code', 'trim|required|callback__validate_postal|xss_clean');
    $this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email');
    $this->form_validation->set_rules('email_confirm', 'Confirm Email Address', 'required|matches[email]');
    $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|xss_clean');
    $this->form_validation->set_rules('shipping_firstname', 'Shipping First Name', 'trim|required|callback__alphanumeric_dashspace|xss_clean');
    $this->form_validation->set_rules('shipping_lastname', 'Shipping Last Name', 'trim|required|callback__alphanumeric_dashspace|xss_clean');
    $this->form_validation->set_rules('shipping_street1', 'Shipping Address Line 1', 'trim|required|callback__alphanumeric_address|xss_clean');
    $this->form_validation->set_rules('shipping_street2', 'Shipping Address Line 2', 'trim|callback__alphanumeric_address|xss_clean');
    $this->form_validation->set_rules('shipping_city', 'Shipping City', 'trim|required|callback__alphanumeric_dashspace|xss_clean');
    $this->form_validation->set_rules('shipping_province', 'Shipping Province', 'trim|required|callback__validate_province|xss_clean');
    $this->form_validation->set_rules('shipping_postal', 'Shipping Postal Code', 'trim|required|callback__validate_postal|xss_clean');
    $this->form_validation->set_rules('shipping_method', 'Shipping Method', 'trim|required|xss_clean');
    // terms
    $this->form_validation->set_rules('terms', 'Privacy Policy', 'callback__validate_terms' );

    if ($this->form_validation->run()) {
      $order_array = array(
        'billing_firstname' => $this->input->post('firstname'),
        'billing_lastname'  => $this->input->post('lastname'),
        'billing_street1'  => $this->input->post('street1'),
        'billing_street2'  => $this->input->post('street2'),
        'billing_city'   => $this->input->post('city'),
        'billing_province'  => $this->input->post('province'),
        'billing_postal' => strtoupper($this->input->post('postal')),
        'billing_email'  => $this->input->post('email'),
        'billing_phone'  => extract_number($this->input->post('phone')),
        'nSubtotal'  => $this->cart->total(),
        'nProductCount'  => $this->cart->total_items(),
      );
      $order_array += array(
        'shipping_firstname' => $this->input->post('shipping_firstname'),
        'shipping_lastname'  => $this->input->post('shipping_lastname'),
        'shipping_street1'  => $this->input->post('shipping_street1'),
        'shipping_street2'  => $this->input->post('shipping_street2'),
        'shipping_city'   => $this->input->post('shipping_city'),
        'shipping_province' => $this->input->post('shipping_province'),
        'shipping_postal' => strtoupper($this->input->post('shipping_postal')),
        'shipping_class'  => $this->input->post('shipping_method'),
      );

      $result = $this->shop_model->new_order($order_array, $this->data['cart']);
      if ($result) {
        redirect($this->module_route . '/confirm');
        exit();
      }
    }
    else {
      // Return the validation error
      if (validation_errors() > '') {
//        $aaa = validation_errors();
//        var_dump($aaa);
//        die;
        $this->add_message('error', validation_errors());
      }
    }

    $this->data['firstname'] = array('name'    => 'firstname',
                                      'id'      => 'firstname',
                                      'type'    => 'text',
                                      'value'   => $this->form_validation->set_value('firstname'),
                                      );
    $this->data['lastname']  = array('name'    => 'lastname',
                                      'id'      => 'lastname',
                                      'type'    => 'text',
                                      'value'   => $this->form_validation->set_value('lastname'),
                                      );
    $this->data['street1'] = array('name'    => 'street1',
                                      'id'      => 'street1',
                                      'type'    => 'text',
                                      'value'   => $this->form_validation->set_value('street1'),
                                      );
    $this->data['street2'] = array('name'    => 'street2',
                                      'id'      => 'street2',
                                      'type'    => 'text',
                                      'value'   => $this->form_validation->set_value('street2'),
                                      );
    $this->data['city'] = array('name'    => 'city',
                                      'id'      => 'city',
                                      'type'    => 'text',
                                      'value'   => $this->form_validation->set_value('city'),
                                      );
    $this->data['provinces'] = $this->shop_model->list_provinces();
    $this->data['province'] = array('name'    => 'province',
                                      'id'      => 'province',
                                      'type'    => 'select',
                                      'options' => $this->data['provinces'],
                                      'value'   => $this->form_validation->set_value('province'),
                                      );
    $this->data['postal']  = array('name'    => 'postal',
                                      'id'      => 'postal',
                                      'type'    => 'text',
                                      'value'   => $this->form_validation->set_value('postal'),
                                      );
    $this->data['email']      = array('name'    => 'email',
                                      'id'      => 'email',
                                      'type'    => 'text',
                                      'value'   => $this->form_validation->set_value('email'),
                                      );
    $this->data['email_confirm']      = array('name'    => 'email_confirm',
                                      'id'      => 'email_confirm',
                                      'type'    => 'text',
                                      'value'   => $this->form_validation->set_value('email_confirm'),
                                      );
    $this->data['phone']   = array('name'    => 'phone',
                                      'id'      => 'phone',
                                      'type'    => 'text',
                                      'value'   => $this->form_validation->set_value('phone'),
                                      );
    //if ($order_type > 0) {
      // shipping information
      $this->data['shipping_firstname'] = array('name'    => 'shipping_firstname',
                                        'id'      => 'shipping_firstname',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('shipping_firstname'),
                                       );
      $this->data['shipping_lastname']  = array('name'    => 'shipping_last_name',
                                        'id'      => 'shipping_last_name',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('shipping_last_name'),
                                       );
      $this->data['shipping_street1'] = array('name'    => 'shipping_street1',
                                        'id'      => 'shipping_street1',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('shipping_street1'),
                                       );
      $this->data['shipping_street2'] = array('name'    => 'shipping_street2',
                                        'id'      => 'shipping_street2',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('shipping_street2'),
                                       );
      $this->data['shipping_city'] = array('name'    => 'shipping_city',
                                        'id'      => 'shipping_city',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('shipping_city'),
                                       );
      $this->data['shipping_province'] = array('name'    => 'shipping_province',
                                        'id'      => 'shipping_province',
                                        'type'    => 'select',
                                        'options' => $this->data['provinces'],
                                        'value'   => $this->form_validation->set_value('shipping_province'),
                                       );
      $this->data['shipping_postal']  = array('name'    => 'shipping_postal',
                                        'id'      => 'shipping_postal',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('shipping_postal'),
                                       );
      $this->data['shipping_methods'] = $this->shop_model->list_shipping_methods();
      $this->data['shipping_method'] = array('name'    => 'shipping_method',
                                        'id'      => 'shipping_method',
                                        'type'    => 'select',
                                        'options' => $this->data['shipping_methods'],
                                        'value'   => $this->form_validation->set_value('shipping_method'),
                                       );
    //}
    // load module view
    self::loadModuleView($this->aModuleInfo, $this->data, 'billing');
  } */

  /**
   * Checkout step 2: shipping information
   */
  public function checkout_shipping()
  {
    //if (empty($_SERVER['HTTPS']) && $_SERVER['HTTP_HOST'] != 'www.mywebsite.com') {
      //header("Location:https://" . $_SERVER['HTTP_HOST'] . "/" . $this->module_route . "/checkout-billing");
      //exit;
    //}
    if ($this->cart->total() == 0) {
      redirect($this->cart_url);
    }
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    $this->load->helper('account/account');
    $this->load->helper('contact/contact');

    $user_id = (int)($this->session->userdata('user_id'));

    $this->disable_browser_caching();
    $this->data['sTitle'] = "Confirm Shipping Info";

    // Validation rules
    $this->form_validation->set_rules('address', 'Shipping Address', 'trim|required|xss_clean');
    $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|xss_clean');
   //$this->form_validation->set_rules('instruction', 'Delivery Instruction', 'trim|required|xss_clean');

    if ($this->form_validation->run()) {
      $addr_id = (int)($this->input->post('address'));
      $phone = trim($this->input->post('phone'));
      $instruction = $this->input->post('instruction');
      if ($addr_id > 0) {
        $user = account_get_user($user_id);
        $addr = contact_get($addr_id);
        if ($user && $addr) {
          $order_array = array(
            'user_id'  => $user_id,
            'subtotal'  => $this->cart->total(),
            'product_count'  => $this->cart->total_items(),
            'billing_firstname' => $user->first_name,
            'billing_lastname'  => $user->last_name,
            'billing_street1'  => $addr->address_1,
            'billing_street2'  => $addr->address_2,
            'billing_city'   => $addr->city,
            'billing_province'  => $addr->province,
            'billing_postal' => $addr->postal_code,
            'billing_email'  => $user->email,
            'billing_phone'  => $phone,
            'shipping_firstname' => $user->first_name,
            'shipping_lastname'  => $user->last_name,
            'shipping_street1'  => $addr->address_1,
            'shipping_street2'  => $addr->address_2,
            'shipping_city'   => $addr->city,
            'shipping_province' => $addr->province,
            'shipping_postal' => $addr->postal_code,
            'shipping_instruction'  => $instruction,
            //'shipping_class'  => $this->input->post('shipping_method'), //TBD
          );
          $this->data['cart'] = $this->cart->contents();
          $result = $this->shop_model->new_order($order_array, $this->data['cart']);
          if ($result) {
            $order = $this->shop_model->load_order();
            // deduct points and email receipt
            $result = $this->shop_model->process_order($order);
            if (!$result) {
              $this->add_message('error', 'Sorry but there was an error when trying to process the order.');
            }
            redirect($this->module_route . '/confirm');
            exit();
          }
          else {
            $this->add_message('error', 'Sorry but there was an error when trying to place the order.');
          }
        }
      }
    }
    else {
      // Return the validation error
      if (validation_errors() > '') {
        $this->add_message('error', validation_errors());
      }
    }
    $this->data['user_id'] = $user_id;
    $this->data['address'] = (int)($this->input->post('address'));
    $this->data['addresses'] = contact_list('user', $user_id);
    $this->data['phone'] = array('name'  => 'phone',
                                 'id'    => 'phone',
                                 'type'  => 'text',
                                 'value' => $this->form_validation->set_value('phone'),
                                );
    $this->data['instruction'] = array('name' => 'instruction',
                                       'id'   => 'instruction',
                                       'type' => 'text',
                                       'rows'  => 3,
                                       'cols' => 23,
                                       'value' => $this->input->post('instruction'),
                                      );
    $this->data['contact_form'] = contact_form_new();
    // load module view
    self::loadModuleView($this->aModuleInfo, $this->data, 'shipping');
  }

  /*
  public function checkout_payment()
  {
    if (empty($_SERVER['HTTPS']) && $_SERVER['HTTP_HOST'] != 'www.mywebsite.com') {
      header("Location:https://" . $_SERVER['HTTP_HOST'] . "/" . $this->module_route . "/checkout-payment");
      exit;
    }
    //make sure this page refreshes before session times out
    $session_timeout = (int)ini_get('session.gc_maxlifetime');
    if ($session_timeout > 0) {
      $refresh = $session_timeout-60;
      if($refresh <= 60) {
	      $refresh = $session_timeout-5;
      }
      if ($refresh > 0) {
	      $this->data['error'] = "Sorry, the session has expired. Please try again.";
	      header("Refresh: {$refresh}; url=/" . $this->cart_url);
      }
      else {
	      $this->data['error'] = "Server misconfiguration - Session Timeout Too Short";
	      header("Location:https://" . $_SERVER['HTTP_HOST'] . "/" . $this->cart_url);
	      exit;
      }
    }
    $this->disable_browser_caching();
    $this->data['sTitle'] = "Checkout";

    $order = $this->shop_model->load_order(NULL, 'in_checkout');
    if (is_null($order)) {
      redirect($this->cart_url);
      exit;
    }
    else {
      $this->data['order'] = $order;
    }

    // calculate taxes, shipping, and order total
    $this->data['subtotal'] = $order->nSubtotal;
    $this->data['tax_name'] = $order->tax_name;
    $this->data['tax'] = $order->nTax;
    $this->data['order_total'] = $order->nOrderTotal;

    // load module view
    $this->disable_browser_caching();
    self::loadModuleView( $this->aModuleInfo, $this->data, 'payment' );
  } */

  public function checkout_confirm()
  {
    $this->data['sTitle'] = "Order Confirmation";

    // load and display original order information
    $order = $this->shop_model->load_order();
    if (is_null($order)) {
      redirect($this->cart_url);
      exit;
    }

    $this->data['order'] = $order;
    if ($order->order_status == 'pending') {
      // now reset shopping cart
      $this->cart->destroy();
      $this->data['sTitle'] = "Congratulations!";
      $this->data['success'] = "1";
      
      $this->data['cart_review'] = $this->cart_review();
      
      //first order and don't have a badge? -> swagger badge
      $num_orders = $this->shop_model->get_order_count_user_id($this->session->userdata('user_id'));
            
      $has_swagger = check_user_badge($this->session->userdata('user_id'),'swagger');

      if ($num_orders == 1 && !$has_swagger){
          account_add_badge($this->session->userdata('user_id'),'swagger');
      }
      
    }
    else {
      $this->data['error'] = "Sorry but there was an error when trying to process your order.";
      $this->data['success'] = "0";
    }

    // load module view
    $this->disable_browser_caching();
    //$this->load->view('payment_confirm', $this->data);
    self::loadModuleView( $this->aModuleInfo, $this->data, 'confirm' );
  }

  /**
   * Display a receipt for the last purchase
   */
  public function receipt()
  {
    // a receipt can only be viewed by the current customer with active session
    // or a logged in user who previously made this purchase
    $order_id = $this->session->userdata('order_id');
    if ($order_id > 0) {
      $order = $this->shop_model->load_order($order_id);
    }
    else {
      if ($this->session->userdata('user_id') > 0) {
        $order = $this->shop_model->load_last_order($this->session->userdata('user_id'));
      }
      else {
        exit;
      }
    }

    // load and display order information
    if (is_null($order) || ($order->order_status != 'pending' && $order->order_status != 'completed')) {
      redirect($this->cart_url);
      exit;
    }
    else {
      $this->data['order'] = $order;
    }

    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data, 'receipt' );
  }

  // validate the input to only allow alpha-numeric characters, spaces, underscores, and dashes
  public function _alphanumeric_dashspace($str)
  {
    if ($str == '' || preg_match("/^([-a-z0-9_ ])+$/i", $str)) {
      return TRUE;
    }
    else {
      $this->form_validation->set_message('_alphanumeric_dashspace', 'The %s field may only contain alpha-numeric characters, spaces, underscores, and dashes.');
      return FALSE;
    }
  }

  // validate the input to allow alpha-numeric plus # () for street address kind of fields
  public function _alphanumeric_address($str)
  {
    if ($str == '' || preg_match("/^([-a-z0-9_ #\.\(\)])+$/i", $str)) {
      return TRUE;
    }
    else {
      $this->form_validation->set_message('_alphanumeric_address', 'The %s field may only contain alpha-numeric characters, spaces, dashes, and number sign.');
      return FALSE;
    }
  }

  public function _validate_postal($str)
  {
    $isValid = false;
    $postal = str_replace(' ','',$str);
    if(preg_match("/^([abcdefghijklmnopqrstuvwxyz]){1}[0-9]{1}[abcdefghijklmnopqrstuvwxyz]{1}[0-9]{1}[abcdefghijklmnopqrstuvwxyz]{1}[0-9]{1}$/i",$postal)) {
      $isValid = true;
    }
    else {
      $this->form_validation->set_message( '_validate_postal', 'The postal code you entered is not valid.' );
    }
    return $isValid;
  }

  public function _validate_province($str)
  {
    $valid_provinces = $this->shop_model->list_provinces();
    $isValid = array_key_exists($str, $valid_provinces);
    if (!$isValid) {
      $this->form_validation->set_message( '_validate_province', 'The province you entered is not valid. Please enter again.' );
    }
    return $isValid;
  }

  public function _validate_terms()
  {
    $isValid = $this->input->post( 'terms' );
    // if terms not checked / accepted
    if ( !$isValid ) {
      // assign error message
      $this->form_validation->set_message( '_validate_terms', 'You must read and accept the privacy policy and website terms and conditions of use.' );
    }
    return $isValid;
  }

  private function disable_browser_caching()
  {
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
  }
  
  public function empty_cart(){
    $this->cart->destroy();
    $this->index();
  }
  
  /**
   * Displays shopping cart
   */
  public function cart_review()
  {

    $this->load->model('shop/shop_model');
    $this->load->model('product/product_model');
    //$cart_contents = $this->cart->contents();
    $order = $this->shop_model->load_last_order($this->session->userdata('user_id'));
      $i=0;
    foreach ($order->items as $oi) {
      
      $product = $this->product_model->get_product($oi->product_id);     
      if ($product->featured_image->thumb_html) {
        $oi->featured_image = $product->featured_image->thumb_html;
      }
      else {
        $oi->featured_image = 'Images Placeholder';
      }
    }

    $data['cart'] = $order;

    // load module view
    $this->disable_browser_caching();
    return $this->load->view('cart_review', $data,true);
    //return self::loadModuleView( $this->aModuleInfo, $data, 'cart_view' );
  }  
}
