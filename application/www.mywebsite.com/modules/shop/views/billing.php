<div id="shopping-billing" class="container-fluid hero-unit"><?php if ($sTitle>''){ ?><h1><?php echo $sTitle;?></h1><?php } ?><div id="cart-summary-wrapper" class="cart-summary-panel">  <?php if (!is_null($cart)) { ?>  <div id="cart-summary-wrapper-left">  <?php if (count($cart)>0) { ?>    <div class="cart-header">      <p class="title">Order Summary</p>    </div>    <div id="cart-summary">    <?php foreach ($cart as $item) { ?>      <div class="cart-summary-item">        <div class="cart-summary-item-wrapper">          <div class="cart-summary-item-description"><?php            if ((int)$item['qty'] > 1) {              echo $item['qty'] . " X ";            }            echo $item['name'];            if (array_key_exists('options', $item) && is_array($item['options']) && $item['options']['phone'] > '') {              echo ' &ndash; ' . format_phone_number($item['options']['phone']);            }          ?></div>        <div class="cart-summary-item-total"><?php echo number_format($item['subtotal'], 0, '.', ','); ?></div>        </div>      </div>    <?php } ?>      <div class="cart-summary-total">        <div class="cart-summary-total-subtotal"><?php echo number_format($subtotal, 0, '.', ','); ?></div>        <div class="cart-summary-total-label">Subtotal:</div>      </div>    </div>  <?php } ?>  </div>  <?php } ?></div><div class="cart-summary-panel">  <h3>Checkout Step 1 of 2: Please enter your billing information below.</h3>  <p>All fields below are required unless otherwise indicated.</p>  <div id="messageContainer">    <?php    if (isset($messages) && is_array($messages)) {      foreach ($messages as $msg) {        if (is_array($msg) && $msg['message'] > '') {          echo '<div class="message ' . $msg['type'] . '">';          echo '<div class="message_close"><a onClick="closeMessage()">[close]</a></div>';          echo  $msg['message'] . '</div>';        }      }    }    ?>  </div>  <div id="cart-billing">  <form id="form_billing" name="form_billing" method="post">    <ul>      <li>        <h4>Billing Information</h4>        <label for="firstname">First Name:</label>        <?php echo form_input($firstname);?>      </li>      <li>        <label for="lastname">Last Name:</label>        <?php echo form_input($lastname);?>      </li>      <li>        <label for="street1">Address Line 1:</label>        <?php echo form_input($street1);?>      </li>      <li>        <label for="street2">Address Line 2:</label>        <?php echo form_input($street2);?> (optional)      </li>      <li>        <label for="city">City:</label>        <?php echo form_input($city);?>      </li>      <li>        <label for="province">Province:</label>        <?php //echo form_dropdown('province', $provinces, $province['value']);?>        <select id="province" name="province">          <option value="" selected="selected"> - Select - </option>          <?php            foreach($provinces as $k => $v) {              echo '<option value="' . $k . '" ' . ($this->input->post('province') == $k ? 'selected="selected"' : '') . '>' . $v . '</option>' . "\n";            }          ?>        </select>      </li>      <li>        <label for="postal">Postal Code:</label>        <?php echo form_input($postal);?>      </li>      <li>        <label for="email">Email:</label>        <?php echo form_input($email);?>      </li>      <li>        <label for="email_confirm">Confirm Email:</label>        <?php echo form_input($email_confirm);?>      </li>      <li>        <label for="phone">Contact Phone Number:</label>        <?php echo form_input($phone);?>      </li><?php  //if ($order_type > 0) {?>      <li>       <table>       <tr>         <td>           <input type="checkbox" class="check" id="duplicate" name="duplicate" value="1" />         </td>         <td>          <label for="duplicate" id="lblDuplicate"><br />My billing and shipping addresses are the same.</label>         </td>       </tr>       <tr>         <td>           &nbsp;         </td>         <td>           <span id="poboxnote">&nbsp;(We currently do not ship to P.O. Boxes.)</span>         </td>       </tr>      </table>      </li>      <li class="shipping">        <h4>Shipping Information</h4>        <label for="shipping_firstname">First Name:</label>        <?php echo form_input($shipping_firstname);?>      </li>      <li class="shipping">        <label for="shipping_lastname">Last Name:</label>        <?php echo form_input($shipping_lastname);?>      </li>      <li class="shipping">        <label for="shipping_street1">Address Line 1:</label>        <?php echo form_input($shipping_street1);?>      </li>      <li class="shipping">        <label for="shipping_street2">Address Line 2:</label>        <?php echo form_input($shipping_street2);?> (optional)      </li>      <li class="shipping">        <label for="shipping_city">City:</label>        <?php echo form_input($shipping_city);?>      </li>      <li class="shipping">        <label for="shipping_province">Province:</label>        <?php //echo form_dropdown('province', $provinces, $province['value']);?>          <select id="shipping_province" name="shipping_province">            <option value="" selected="selected"> - Select - </option>            <?php              foreach($provinces as $k => $v) {                echo '<option value="' . $k . '" ' . ($this->input->post('province') == $k ? 'selected="selected"' : '') . '>' . $v . '</option>' . "\n";              }            ?>          </select>      </li>      <li class="shipping">        <label for="shipping_postal">Postal Code:</label>        <?php echo form_input($shipping_postal); ?>      </li>      <li>        <label for="shipping_method">Shipping Method:</label>        <select id="shipping_method" name="shipping_method">          <option value="" selected="selected"> - Select - </option>          <?php            foreach($shipping_methods as $k => $v) {              echo '<option value="' . $k . '" ' . ($this->input->post('shipping_method') == $k ? 'selected="selected"' : '') . '>' . $v . '</option>' . "\n";            }          ?>        </select>      </li><?php //} ?>      <li>        <input type="checkbox" class="check" id="terms" name="terms" value="1" />        <label for="terms" id="lblTerms">I have read and agree with the <a href="/privacy-policy" title="privacy policy" target="_blank">privacy policy</a> and <a href="/terms" title="website terms and conditions of use" target="_blank">website terms and conditions of use</a>.</label>      </li>    </ul>    <div id="cart-billing-submit">      <div id="cart-billing-buttons">        <a href="#" class="aTriangleButton" onclick="document.forms['form_billing'].submit(); return false;">CONFIRM</a>        <a href="shop/cart" class="cart-return-link">Return to previous page <img width="8" height="8" alt="Return to previous page" src="/asset/images/link_arrow.png" /></a>      </div>      <p>Click &#147;Proceed to Payment&#148; to review your order and enter your payment information.</p>    </div>  </form>  </div></div></div>