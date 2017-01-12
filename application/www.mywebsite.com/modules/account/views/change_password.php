<div class="hero-unit">
<h1>Change Password</h1>
<form method="post" id="change_password">
<table id="table_form">
  <tr>
    <td colspan="2">
<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>
    </td>
  </tr>
  <tr>
    <td>Current Password:</td>
    <td><input type="password" id="password" name="password" value="" /></td>
  </tr>
  <tr>
    <td>New Password:</td>
    <td><input type="password" id="new_password" name="new_password" value="" /></td>
  </tr>
  <tr>
    <td>Confirm New Password:</td>
    <td><input type="password" id="new_password_confirm" name="new_password_confirm" value="" /></td>
  </tr>
  <tr>
    <td ><a class="view-all-link" href="/my-account/profile"><span class="view-all-arrows">» </span>Cancel</a></td>      
    <td><input class="btn btn-primary" name="submit" id="submit" value="Change Password" type="submit" /></td>
  </tr>
 
</table>
</form>
</div>