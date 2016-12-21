<h1><?php echo $title; ?></h1>
<form action="my-account/edit" method="post">
<table id="register_form">  
  <tr>
    <td colspan="2">
<?php if ( !empty($message) ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( !empty($error)) { ?><div class="error"><?php echo $error;?></div><?php } ?>
    </td>
  </tr> 
  <tr>
    <th><label for="first_name">First Name:</label></th>
    <td><?php echo form_input('first_name', $user->first_name);?></td>
  </tr>
  <tr>
    <th><label for="last_name">Last Name:</label></th>
    <td><?php echo form_input('last_name', $user->last_name);?></td>
  </tr>
  <tr>
    <th><label for="postal">Postal Code:</label></th>
    <td><?php echo form_input('postal',$user->postal);?></td>
  </tr>
  <tr>
    <th><label for="email">Email Address<br/>(Username):</label></th>
   <td><?php echo  $user->email;?> <?php echo form_hidden('email',$user->email);?></td>
    
  </tr>
  <tr>
    <th><label for="password">Change password:</label></th>
    <td><a href="my-account/change-password">click here</a></td>
  </tr>
  <tr>
    <td colspan="2">
      <input type="submit" name="submit" class="triangle" value="Edit" />
    </td>
  </tr>  
</table>
</form>
