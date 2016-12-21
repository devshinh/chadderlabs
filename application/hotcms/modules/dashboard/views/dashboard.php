<?php echo form_open('dashboard', array('id' => 'dashboard_form')); ?>
  <h2 class="welcome">
<?php if (!$this->session->userdata( 'user_id' )) { ?>
  <?php echo lang( 'hotcms__dashboard__0' ) ?>
<?php } else { ?>
  <?php echo lang( 'hotcms__dashboard__1A' ) ?>
  <span><?php echo $this->session->userdata( 'username' ) ?> / <?php echo $this->session->userdata( 'user_email' ) ?></span>
  <?php echo lang( 'hotcms__dashboard__1B' ) ?><?php echo lang( 'hotcms__colon' ); ?>
  <select id="cboSite" name="cboSite">
  <?php foreach ($aSite as $row){ ?>
    <option value="<?php echo $row->id ?>" <?php if ($row->id == $this->session->userdata( 'siteID' )){ ?> selected="selected"<?php } ?>>
      <?php echo $row->name ?> &mdash; <?php echo $row->domain; ?>
    </option>
  <?php } ?>
  </select>
<?php } ?>
  </h2>
<?php /*
  <div class="input <?php if ($this->session->userdata( 'userID' )){ ?>dashboard<?php } else { ?>login<?php } ?>">
    <div class="panel">
<?php if (!$this->session->userdata( 'user_id' )){ ?>
      <div class="row">
        <label for="user_login"><?php echo lang( 'hotcms_name_user' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
        <input class="text" id="user_login" name="user_login" value="<?php echo set_value( 'user_login', get_cookie( 'hotcms_user' ) ) ?>" maxlength="50" />
      </div>
      <div class="row">
        <label for="txtPassword"><?php echo lang( 'hotcms_password' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
        <input class="text" type="password" id="password" name="password" value="<?php echo set_value( 'txtPassword' ) ?>" maxlength="10" />
      </div>
      <?php
      <div class="row space">
        <label for="chkRemember"><?php echo lang( 'hotcms_remember_me' ) ?><?php echo lang( 'hotcms__colon' ) ?></label>
        <input type="checkbox" id="chkRemember" value="true"<?php echo set_checkbox( 'chkRemember', 'true', get_cookie( 'hotcms_user' ) ? true : false ) ?> />
      </div>
      ?>
    </div>
    <div class="submit">
     <input class="submit red_button" type="submit" value="<?php echo lang( 'hotcms_login' ) ?>" />
     <?php echo form_hidden('hdnMode', 'insert') ?>
<?php } ?>
    </div>
  </div>
*/ ?>
<?php echo form_close(); ?>
