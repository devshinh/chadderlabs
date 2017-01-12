<!--<div class="hero-unit">-->
  <div class="box-featured-title">Training Center</div>
  <h1 class="item-header"><?php print($item->name); ?></h1>

  <p>
    Welcome to the <?php print($item->name); ?> Quiz. After clicking "Continue" your quiz will begin and you will have
    <span class="red uppercase"><?php print($item->type->time_limit); ?> minutes </span>
    to complete it.
  </p>

  <?php if ($item->type->tries_per_day > 0 || $item->type->tries_per_week > 0) { ?>
  <p>
    As friendly reminder, you may only take this quiz
    <span class="red">
    <?php echo $item->type->tries_per_day > 0 ? $item->type->tries_per_day . ' times a day' : ''?>
    </span>
    <?php echo $item->type->tries_per_day > 0 && $item->type->tries_per_week > 0 ? ' and ' : ''?>
    <span class="red">
    <?php echo $item->type->tries_per_week > 0 ? $item->type->tries_per_week . ' times a week' : ''?>
    </span>
    so please ensure that you have taken the necessary steps to prepare for it.
  </p>
  <?php } ?>

  <p class="red">
    Remember, you only have
    <span class="red uppercase"><?php print($item->type->time_limit); ?> minutes </span>
    to complete the quiz.
  </p>
  <a class="btn btn-primary" href="/quiz/<?php echo $item->slug; ?>?start=yes">CONTINUE</a>
<!--</div>-->