<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Shop module helper
 *
 * Some functions to share with HotCMS core and other modules
 */

/**
 * Return card type giving a transaction object
 * @param object $transaction
 * @return string
 */
if (!function_exists('shop_creditcard_type'))
{
  function shop_creditcard_type($transaction)
  {
    $cardtype = "";
    switch ($transaction->sAT) {
      case 'V': $cardtype = 'Visa';
	      break;
      case 'M': $cardtype = 'Master Card';
	      break;
      case 'AX': $cardtype = 'AMEX';
	      break;
      case 'D': $cardtype = 'Diners';
	      break;
    }
    return $cardtype;
  }
}

/**
 * format order summary
 * @param object $order
 * @return string
 */
if (!function_exists('shop_format_order'))
{
  function shop_format_order($order)
  {
    $title = 'Order Summary';
    $order_subtotal = number_format($order->subtotal, 0);
    $order_tax = number_format($order->tax_amount, 0);
    $order_total = number_format($order->order_total, 0);

    $topup_notice = <<<EOF
      <tr>
        <td colspan="2" style="padding:0 0 10px 0; font-weight:bold;">
        Your purchase has been topped up to your account!
        </td>
      </tr>
EOF;
    $topup_error = <<<EOF
      <tr>
        <td colspan="2" style="padding:5px; color:#a00; background-color:#FBE6F2; font-weight:bold; border:1px solid #D893A1;">
        Sorry, there was a technical issue. Please contact our customer service.
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
EOF;
    $formatted = <<<EOF
      <tr>
        <td colspan="2" class="title" bgcolor="#246" style="padding:10px; color:#fff; font-weight:bold; border:1px solid #193E68; background-color:#246; background:#246 url(https://www.speakout7eleven.ca/asset/images/bg_title.jpg) repeat-x left top;">
          {$title}
        </td>
      </tr>
EOF;
    foreach($order->items as $item) {
      $item_name = $item->product_name;
      $item_price = number_format($item->price, 0);

      if ( $item->option_title > '') {
        $item_name .= '<br /><span>' . $item->option_title . '</span>';
        $item_price .= '<br /><span>' . number_format($item->option_price, 0, '.', ',') . '</span>';
        if ($item->promo_id > 0 && $item->promo_amount > 0) {
          $item_name .= '<br /><span style="font-size:11px">Promotional Discount (' . $item->promo_title . ')</span>';
          $item_price .= '<br /><span style="font-size:11px;color:red">-' . number_format($item->promo_amount, 0, '.', ',') . '</span>';
        }
      }

      $formatted .= <<<EOF
      <tr valign="top">
        <td style="border-bottom: 1px dotted #ccc; padding:5px 0;">{$item_name}</td>
        <td style="width: 120px; text-align: right; border-bottom: 1px dotted #ccc; padding:5px 0;">{$item_price}</td>
      </tr>
EOF;
    }
    $formatted .= <<<EOF
    <tr>
      <td style="padding:5px 0; text-align: right;">Subtotal</td>
      <td style="padding:5px 0; width: 120px; text-align: right; ">{$order_subtotal}</td>
    </tr>
    <tr>
      <td style="padding:5px 0; text-align: right;">Tax ({$order->tax_name})</td>
      <td style="padding:5px 0; width: 120px; text-align: right;">{$order_tax}</td>
    </tr>
EOF;
    if ($order->shipping_fee > 0) {
      $shippingFee = number_format($order->shipping_fee, 0);
      if (strpos($order->shipping_class, 'GROUND') > 0) {
        $shippingClass = 'Ground';
      }
      elseif (strpos($order->shipping_class, 'EXPRESS') > 0) {
        $shippingClass = 'Express';
      }
      $formatted .= <<<EOF
      <tr>
        <td style="padding:5px 0; text-align: right;">Shipping ({$shippingClass})</td>
        <td style="padding:5px 0; width: 120px; text-align: right;">{$shippingFee}</td>
      </tr>
EOF;
    }
    $formatted .= <<<EOF
    <tr>
      <td style="padding:5px 0; text-align: right; font-weight: bold; color: #246; border-top: 1px dotted #ccc;">Order Total</td>
      <td style="padding:5px 0; width: 120px; text-align: right; font-weight: bold; border-top: 1px dotted #ccc;">{$order_total}</td>
    </tr>
EOF;
    $formatted = '<table width="400" border="0" align="left" cellspacing="0" cellpadding="0">' . $formatted . '</table>';
    return $formatted;
  }
}

/**
 * format transaction information in a receipt
 * @param object $order
 * @return string
 */
if (!function_exists('shop_format_transaction'))
{
  function shop_format_transaction( $order )
  {
    $order_date = date("Y-m-d", $order->create_timestamp);
    $transaction_date = date('Y-m-d h:i:s a', $order->create_timestamp);
    $formatted = <<<EOF
    <table width="400px" cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td colspan="2" style="padding:5px 0; font-weight: bold; color: #246">Transaction Details:</td>
      </tr>
      <tr>
        <td style="width: 200px; padding-bottom: 5px;" align="right">{$transaction_date}</td>
      </tr>
      <tr>
        <td style="width: 200px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;"><span style="font-weight: bold;"></span></td>
        <td style="width: 200px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;" align="right"><span style="font-weight: bold;">Order #: </span>{$order->id}</td>
      </tr>
			<tr>
			  <td colspan="2" style="padding:5px 0;"><span style="font-weight: bold">Transaction Description:</span> Purchase</td>
			</tr>
    </table>
EOF;
    return $formatted;
  }
}


/**
 * format a receipt
 * @param object $order
 * @param object $transaction
 * @return string
 */
if (!function_exists('shop_format_receipt'))
{
  function shop_format_receipt($order)
  {
    $order_date = date("Y-m-d", $order->create_timestamp);
    $transaction_date = date('Y-m-d h:i:s a', $order->create_timestamp);

    $order_summary = shop_format_order($order);
    $transaction_detail = shop_format_transaction( $order );

    if ($order->shipping_class > '') {
      $shipping_title = 'Ship to:';
      $shipping_city = $order->shipping_city . ', ' . $order->shipping_province;
    }
    else {
      $shipping_title = '';
      $shipping_city = '';
    }

    $formatted = <<<EOF
		<table cellpadding="0" cellspacing="0" align="left" width="730px" style="background-color: white">
		  <tr>
		    <td>
		<table width="430px" border="0" align="left" cellspacing="0" cellpadding="0" style="padding-right: 20px; padding-left: 10px; border-right: solid 4px #FFF; background-color: #f8f9fa">
			<tr>
			  <td style="padding-top: 20px"><h2 style="font-size: 1.25em; color: #246">Congratulations on your SpeakOut purchase.</h2></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 5px;">Purchase Date: {$order_date}</td>
			</tr>
			<tr>
			  <td style="padding-bottom: 5px;">Order No: {$order->id}</td>
			</tr>
			<tr>
			  <td style="padding-left: 0px; padding-top: 3px">
			    {$order_summary}
			  </td>
			</tr>
			<tr>
			  <td>&nbsp;</td>
			</tr>
			<tr>
			  <td>
			    {$transaction_detail}
			  </td>
			</tr>
			<tr>
			  <td>
			    <table cellpadding="0" cellspacing=0" align="left" width="100%">
            <tr>
              <td style="border-top: 1px dotted #ccc; padding:5px 0; font-weight: bold; color: #246">Bill to:</td>
              <td style="border-top: 1px dotted #ccc; padding:5px 0; font-weight: bold; color: #246">{$shipping_title}</td>
            </tr>
            <tr>
              <td>{$order->billing_firstname} {$order->billing_sastname}</td>
              <td>{$order->shipping_firstname} {$order->shipping_lastname}</td>
            </tr>
            <tr>
              <td>{$order->billing_street1}</td>
              <td>{$order->shipping_street1}</td>
            </tr>
EOF;
    if(!empty($order->billing_street2) || !empty($order->shipping_street2)) {
      $formatted .= <<<EOF
            <tr>
              <td>{$order->billing_street2}</td>
              <td>{$order->shipping_street2}</td>
            </tr>
EOF;
    }
    $formatted .= <<<EOF
            <tr>
              <td>{$order->billing_city}, {$order->billing_province}</td>
              <td>{$shipping_city}</td>
            </tr>
            <tr>
              <td style="padding-bottom: 50px;">{$order->billing_postal}</td>
              <td style="padding-bottom: 50px;">{$order->shipping_postal}</td>
            </tr>
          </table>
			  </td>
			</tr>
EOF;
    $vouchers = '';
    $formatted .= <<<EOF
    </table>
        </td>
        <td valign="top" style="padding: 20px; background-color: #f8f9fa">
          {$vouchers}
        </td>
      </tr>
    </table>
EOF;
    return $formatted;
  }
}

/* End of file shop_helper.php */
/* Location: ./helpers/shop_helper.php */