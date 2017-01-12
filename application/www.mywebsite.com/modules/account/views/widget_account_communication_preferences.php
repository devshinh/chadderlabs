<div id="messageContainer">
  <?php if ($message > '') { ?>
    <div class="message">  
<!--      <div class="message_close">
        <a onclick="closeMessage()">[close]</a>
      </div>-->
      <?php echo $message; ?>
    </div><?php } ?>
  <?php if ($error > '') { ?>
    <div class="message error">
<!--      <div class="message_close">
        <a onclick="closeMessage()">[close]</a>
      </div>-->
      <?php echo $error; ?></div><?php } ?>
</div>

<div class="hero-unit">
    <div class="row-fluid">
        <h2 class="pull-left"><?php echo $title ?></h2>
                <div class="pull-right">
                    <a class="view-all-link" href="/profile">
                        <span class="view-all-arrows">» </span>Back</a>
                </div> 
    </div>
    <div class="row-fluid">
    <p><?php echo $welcome_text; ?></p>
</div>
    <div class="row-fluid">
        <p>I would like to receive:</p>
    </div>
    <form action="profile-newsletters-edit" method="POST" id="subscriptions">
        <div class="row-fluid">              
            <input id="newsletter-monthly" name="newsletter-monthly" type="checkbox" <?php print($newsletters['monthly']['active'] ? 'checked' : ''); ?>/> 
            <label for="newsletter-monthly">Monthly Newsletters</label>
        </div>
        <div class="row-fluid">
            <input id="newsletter-new-swag" name="newsletter-new-swag" type="checkbox" <?php print($newsletters['swag']['active'] ? 'checked' : ''); ?>/> 
            <label for="newsletter-new-swag">Alerts about new SWAG</label>
        </div>
        <div class="row-fluid">
            <input id="newsletter-new-lab" name="newsletter-new-lab" type="checkbox" <?php print($newsletters['labs']['active'] ? 'checked' : ''); ?>/> 
            <label for="newsletter-new-lab">Alerts about new Labs</label>
        </div> 
        <div class="row-fluid">
            <input id="newsletter-survey" name="newsletter-survey" type="checkbox" <?php print($newsletters['survey']['active'] ? 'checked' : ''); ?>/> 
            <label for="newsletter-survey">Survey Invitations</label>
        </div>     
        <br />
        <div class="row-fluid">
            <button class="btn btn-primary" type="submit">Change</button>
        </div>
    </form>
</div>