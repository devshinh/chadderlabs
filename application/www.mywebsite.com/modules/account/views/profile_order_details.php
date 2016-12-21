<div class="container">
    <div class="span8">
        <div class="hero-unit">
            <div class="row-fluid">
                <div class="span12">
                    <h1 class="pull-left" >Order details</h1>
                    <div class="pull-right">
                        <a href="/profile" class="btn btn-primary">Back</a>                    
                    </div>
                </div>
            </div>
            <div class="row-fluid item-tag-wrapper">
                <div class="span4 tag-type-wrapper">Order #</div><div class="span8 item-tag-name-wrapper"><?php print($orderDetails->id); ?></div> 
            </div>            
            <div class="row-fluid item-tag-wrapper">
                <div class="span4 tag-type-wrapper">Order status</div><div class="span8 item-tag-name-wrapper"><?php print(ucfirst($orderDetails->order_status)); ?></div> 
            </div>      
            <?php if(!empty($orderDetails->fedex_number)){?>
            <div class="row-fluid item-tag-wrapper">
                <div class="span4 tag-type-wrapper">Fedex Tracking number</div><div class="span8 item-tag-name-wrapper"><?php print($orderDetails->fedex_number); ?></div> 
            </div>           
            <?php }?>
            <div class="row-fluid item-tag-wrapper">
                <div class="span4 tag-type-wrapper">Date created</div><div class="span8 item-tag-name-wrapper"><?php print(date('Y-m-d H:i', $orderDetails->create_timestamp)); ?></div> 
            </div>             
            <div class="row-fluid item-tag-wrapper">
                <div class="span4 tag-type-wrapper">Points spent</div><div class="span8 item-tag-name-wrapper"><?php print($orderDetails->subtotal); ?></div> 
            </div>               
            <div class="row-fluid item-tag-wrapper">
                <div class="span4 tag-type-wrapper">Shipping address</div>
                <div class="span8 item-tag-name-wrapper"><?php printf('%s, %s<br />%s, %s<br />%s', $orderDetails->shipping_street1, $orderDetails->shipping_street2, $orderDetails->shipping_city, $orderDetails->shipping_province,$orderDetails->shipping_postal) ?></div>
            </div>               
            <div class="row-fluid item-tag-wrapper">
                <div class="span4 tag-type-wrapper">Order items</div><div class="span8 item-tag-name-wrapper"></div> 
            </div>         
            <?php foreach ($orderDetails->items as $item) { ?>
                <div class="row-fluid item-tag-wrapper">
                    <div class="span4 item-tag-name-wrapper"><?php printf('%sx %s', $item->qty, $item->product_name); ?></div> 
                    <div class="span6 item-tag-name-wrapper"><?php printf('%s Points', $item->price); ?></div> 
                </div>                     
            <?php } ?>
        </div>
    </div>
</div>
