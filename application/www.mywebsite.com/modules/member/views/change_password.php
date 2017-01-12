<hr />
<form method="post" id="changePwForm" onsubmit="return submitChangePw();">
<table>
  <tr>
    <td colspan="2"><h3>Change Password</h3></td>
  </tr>
  <tr>
    <td colspan="2">
<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>
    </td>
  </tr>
  <tr>
    <th>Current Password:</th>
    <td><input type="password" id="password" name="password" value="" /></td>
  </tr>
  <tr>
    <th>New Password:</th>
    <td><input type="password" id="new_password" name="new_password" value="" /></td>
  </tr>
  <tr>
    <th>Confirm New Password:</th>
    <td><input type="password" id="new_password_confirm" name="new_password_confirm" value="" /></td>
  </tr>
  <tr>
    <td colspan="2"><input class="triangle" name="submit" value="Change Password" type="submit" /></td>
  </tr>
  <tr>
    <td colspan="2"><a class="triangle" href="/my-account/profile">Cancel</a></td>
  </tr>
 
</table>
</form>
<br />