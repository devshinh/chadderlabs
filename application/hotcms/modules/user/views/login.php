<?php if ($sTitle>''){ ?><h1><?php echo $sTitle;?></h1><?php } ?>

<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<p class="title">Log in to your online Account</p>
<div class="module_header">Login Page</div>
  <form action="login" method="post">
  <table>
    <tr>
      <th><label for="email">Email:</label></th>
      <td><?php echo form_input($username);?></td>
    </tr>
    <tr>
      <th><label for="password">Password:</label></th>
      <td><?php echo form_input($password);?></td>
    </tr>
    <tr>
      <td colspan="2"><p class="footnote">Forgot password? <a href="/forgot-password" title="forgot password">Click here.</a></p></td>
    </tr>

    <tr>
      <td colspan="2">
        <input class="submit red_button" type="submit" value="<?php echo lang( 'hotcms_login' ) ?>" />
      </td>
    </tr>
  </table>
  </form>

</div>
<br /><br />
