<h1><?php echo $title; ?></h1>
<table id="register_form">
  <tr>
    <td colspan="2">
<?php if ( !empty($message) ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( !empty($error)) { ?><div class="error"><?php echo $error;?></div><?php } ?>
    </td>
  </tr>
  <tr>
    <th><label for="first_name">First Name:</label></th>
    <td><?php echo $user->first_name; ?></td>
  </tr>
  <tr>
    <th><label for="last_name">Last Name:</label></th>
    <td><?php echo $user->last_name; ?></td>
  </tr>
  <tr>
    <th><label for="postal">Postal Code:</label></th>
    <td><?php echo $user->postal; ?></td>
  </tr>
  <tr>
    <th><label for="email">Email Address:</label></th>
    <td><?php echo $user->email; ?></td>
  </tr>
  <tr>
    <td colspan="2">
     <a href="/my-account/edit">Edit profile</a>
    </td>
  </tr>
</table>

<?php
	echo $items_list;
?>
