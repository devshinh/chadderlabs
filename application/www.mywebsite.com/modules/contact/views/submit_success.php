<script type="text/javascript">
  _gaq.push(['_trackEvent', 'Contact', 'Submission', '<?php echo $concerns ?>']);
</script>

<h1>Thank you for contacting us.</h1>

<?php
$date1 = mktime(0,0,0,12,25,2010); //2010-12-25;
$date2 = mktime(0,0,0,12,29,2010); //2010-12-29;
$dateNow = time();
if ($dateNow > $date1 && $dateNow < $date2){
?>
<p>It may take us longer to respond due to the holiday season. We will do our best to get to your inquiry as soon as possible.</p>
<?php
}else{
?>
<p>We will provide an email response within 72 hours.</p>
<?php
}
?>
<br />

<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<br /><br /><br /><br /><br />



<div class="clear"> </div>
