<?php 
//phpinfo();
////$today = date('Ymd');
//$date = strtotime($today .' -1 days');
//$time = mktime(23, 59, 59, 05, 13, 2013);
//$time = mktime(20, 30, 0, 05, 11, 2013);
//echo $time;

    //$time = mktime(23, 59, 59, 3, 14, 2013);
    //$start_time = $time - 2592000; // 30 days ago
    //echo $time;
    
    //1386230423
//echo date('Y-m-d H:i:s', '1359875101');
/*
echo '<br />';
//echo date('Y-m-d', $date);
echo date('Y-m-d H:i:s', '1354011575');
echo ' - 4167204965<br />';
echo date('Y-m-d H:i:s', '1365461646');
echo ' - 6476396361<br />';
echo date('Y-m-d H:i:s', '1365461641');
echo ' - 6474017257<br />';
 * 
 */
    /*

// Create connection
$con=mysqli_connect("localhost","root","r00tjan10","speakout_orders");

// Check connection
if (mysqli_connect_errno($con))
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

  $result = mysqli_query($con,"SELECT * FROM cms_dOrder");
  //var_dump( $result);
        $new_order_item_id = 0;
      $new_securelog_id = 0;
      $new_transactions_id = 0;
  foreach($result as $row)
  {
      echo '<pre>';
  echo var_dump($row['nOrderID']);

      //update other tables with new order id
      $new_order_id = $row['nOrderID'] - 229482;
      echo ' '.$new_order_id;
      

      //UPDATE ORDER ITEM TABLE
      $orderItems = mysqli_query($con,"SELECT * FROM cms_dOrderItem WHERE nOrderID=".$row['nOrderID']);

      foreach ($orderItems as $order){    
          $new_order_item_id++;
            mysqli_query($con,'UPDATE cms_dOrderItem SET nOrderID = '.$new_order_id.',nOrderItemID = '.$new_order_item_id.' WHERE nOrderItemID='.$order['nOrderItemID']);
      }
      //UPDATE cms_dOrderSecureLog
      $secureLogs = mysqli_query($con,"SELECT * FROM cms_dOrderSecureLog WHERE nOrderID=".$row['nOrderID']);
      foreach ($secureLogs as $log){
          $new_securelog_id++;
          mysqli_query($con,'UPDATE cms_dOrderSecureLog SET nOrderID = '.$new_order_id.', nTransactionID = '.$new_securelog_id.' WHERE nTransactionID='.$log['nTransactionID']);
        //var_dump($log);   
      }     
      
      //UPDATE cms_dOrderTransaction
      $transactions = mysqli_query($con,"SELECT * FROM cms_dOrderTransaction WHERE nOrderID=".$row['nOrderID']);
      foreach ($transactions as $tra){
          $new_transactions_id++;
          mysqli_query($con,'UPDATE cms_dOrderTransaction SET nOrderID = '.$new_order_id.', nTransactionID = '.$new_transactions_id.' WHERE nTransactionID='.$tra['nTransactionID']);
        //var_dump($order);   
      }  
      //update order table
      mysqli_query($con,'UPDATE cms_dOrder SET nOrderID = '.$new_order_id.' WHERE nOrderID='.$row['nOrderID']);
      //update order
  echo "<br />";
  }

mysqli_close($con);
     
     */
 ?>
