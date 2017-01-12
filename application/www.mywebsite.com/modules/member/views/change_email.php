<hr />
<form method="post" id="changeEmailForm" onsubmit="return submitChangeEmail();">
<table>
  <tr>
    <td colspan="2"><h3>Change Email</h3></td>
  </tr>
  <tr>
    <td colspan="2">
<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>
    </td>
  </tr>
  <tr>
    <th>New Email:</th>
    <td><?php echo form_input($email);?></td>
  </tr>
  <tr>
    <th>Confirm Email:</th>
    <td><?php echo form_input($email_confirm);?></td>
  </tr>
  <tr>
    <td colspan="2"><input class="triangle" name="submit" value="Change Email" type="submit" /></td>
  </tr>
  <tr>
    <td colspan="2"><a class="triangle" href="/my-account/profile">Cancel</a></td>
  </tr>
</table>
</form>
<br />