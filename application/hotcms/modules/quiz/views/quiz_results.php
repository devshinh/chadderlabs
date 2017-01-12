<style type="text/css">
  .correct {
    color: green;
    float: left;
  }
  .wrong {
    color: red;
    float:left;
  }
  .question-sentense,
  .answer{
    clear: both;
  }
  .question-result {
    margin-bottom: 20px;
    border-bottom: 1px solid grey;
    overflow: auto;
  }
  .question {
    border-top: 1px dotted grey;
    padding-left: 20px;
    padding-bottom: 10px;
    padding-top: 10px;
    border-left: 1px solid grey;
  }
  .questions-section {
    width: 100%;
    background-color: black;
    text-transform: uppercase;
    color: white;
    padding: 5px 6px 5px 9px;
    font-weight: bold;
  }
  .question label,
  .question input{
    float: left;
  }
  .question label {
    margin-top: 8px;
    font-style: italic;
  }
  .multiple.question label {
    width: 335px
  }
  .result-icon {
    margin-top: 5px;
  }
  .no-answer {
    color: red;
  }
</style>

<div class="container-fluid quiz" id="quiz-<?php print($item->id); ?>">
  <div class="row-fluid">
    <div class="box-featured-title">Training center</div>
    <?php
    printf('<h1>%s</h1>', $item->name);
    ?>
  </div>
  <div class="row-fluid">
    <div class="span4">
      <?php printf('<img class="reflection_less" src="%s" alt="%s" title="%s" />', $training->featured_image->full_path, $training->featured_image->name, $training->featured_image->description) ?>
    </div>
    <div class="span8">
      <?php if ($history->correct_percent < 100) { ?>
        <p><b>It looks like you need to do your homework.</b></p>
        <p style="padding-right: 50px;">
          <a class="link-with-arrow" href="/labs/product/<?php echo $training->slug; ?>"><span class="link-arrows">» </span>TRY TO REVIEW THE TRAINING MATERIAL</a>
          for <?php echo $training->title ?> again. Then take the quiz again and get a higher score for more points.
        </p>
      <?php } else { ?>
        <p><b>Good job!</b>Now go spend those hard-earned points.</p>
      <?php } ?>
      <div class="item-subtitle">Quiz Completion:</div>
      <div class="row-fluid">
        <?php printf('<p>You got %s out of %s questions correct.</p>', $history->correct_answers, count($history->questions)); ?>
      </div>
      <div class="row-fluid">
        <div class="span6">
          <div class="progress">
            <div class="bar" style="width: <?php print($history->correct_percent); ?>%"></div>
          </div>
        </div>
        <div class="span6">
          <span class="blue"><?php print($history->correct_percent); ?>%</span>
        </div>
      </div>
      <div class="row-fluid">
        <div class="item-subtitle">Points Achieved: <span class="blue"> <?php print($history->points_earned) ?> Points</span></div>
      </div>
    </div>
  </div>
  <?php if (isset($message) && $message > '') { ?><div class="message"><?php echo $message; ?></div><?php } ?>
  <?php if (isset($error) && $error > '') { ?><div class="error"><?php echo $error; ?></div><?php } ?>
  <br />
  <?php
  $counter_true_false = 0;
  $header_true_false = true;
  $counter_multiple = 0;
  $header_multiple = true;
  $true_false_options = array(1 => 'true', 2 => 'false');
  $msg_no_answer = '<div class="answer no-answer">This question has not been answered.</div>';
  foreach ($history->questions as $q) {
    if ($q->user_answer > 0) {
      if ($q->correct_answer == $q->user_answer) {
        $answer_status = 'correct';
      }
      else {
        $answer_status = 'wrong';
      }
    }
    else {
      $answer_status = 'missing';
    }

    switch ($q->question_type) {
      case 1: // true/false
        ?>
        <div class="row-fluid">
          <div class="span3">
            <?php
            if ($header_true_false) {
              print '<div class="questions-section">True or False</div>';
              $header_true_false = false;
            }
            ?>
          </div>
          <div class="span9 question">
            <?php
            $counter_true_false += 1;
            printf('<div class="question-sentense %s"><strong>%s.</strong> %s</div>', ($q->required ? 'required' : ''), $counter_true_false, $q->question);
            ?>
            <div class="fluid-row"><div class="span11">
              <?php
              foreach ($true_false_options as $k => $v) {
                if ($q->user_answer == $k) {
                  $class_name = ($q->correct_answer == $k ? 'correct' : 'wrong');
                  printf('<div class="%s">', $class_name);
                }
                else {
                  print('<div>');
                }
                $data = array(
                  'name' => 'quiz-' . $q->id,
                  'id' => 'quiz-' . $v . '-' . $q->id,
                  'value' => $v,
                  'checked' => ($q->user_answer == $k ? 'checked' : ''),
                  'style' => 'margin:10px',
                  'disabled' => 'disabled'
                );
                print form_radio($data);
                print form_label(ucfirst($v), 'quiz-' . $v . '-' . $q->id);
                print '</div>';
              }
              if ($answer_status == 'missing') {
                print $msg_no_answer;
              }
              ?>
            </div> <!-- span10 -->
            <div class="span1 result-icon">
              <?php
              switch ($answer_status) {
                case 'correct':
                  print('<img height="22" width="22" src="/themes/earetailprofessionals/images/quiz-icons/icon-correct.png" title="Correct answer!" alt="correct" />');
                  break;
                case 'wrong':
                case 'missing':
                  print('<img height="22" width="22" src="/themes/earetailprofessionals/images/quiz-icons/icon-wrong.png" title="Wrong answer!" alt="false" />');
                  break;
              }
              ?>
            </div> <!-- span1 -->
            </div> <!-- fluid-row -->
          </div> <!-- span9 question -->
        </div> <!-- row-fluid -->
        <?php
        break;

      case 2: // multiple choice
        $counter_multiple += 1;
        ?>
        <div class="row-fluid">
          <div class="span3">
            <?php
            if ($header_multiple) {
              print '<div class="questions-section">Multiple Choice</div>';
              $header_multiple = false;
            }
            ?>
          </div>
          <div class="span9 question multiple">
            <?php
            printf("<div class='question-sentense %s'><strong>%s.</strong> %s</div>", ($q->required == 1 ? 'required' : ''), $counter_multiple, $q->question);
            ?>
            <div class="fluid-row"><div class="span11">
            <?php
            for ($i=97; $i<=101; $i++) {
              $j = 'option_' . chr($i); // e.g. option_a, option_b, ...
              if ((int)($q->$j) > 0) {
                $class_name = '';
                if ($q->user_answer > 0 && $q->correct_answer == $q->$j) {
                  $class_name = 'correct';
                }
                elseif ($q->user_answer > 0 && $q->user_answer == $q->$j && $q->correct_answer != $q->$j) {
                  $class_name = 'wrong';
                }
                if ($class_name > '') {
                  $class_name = 'class="' . $class_name . '"';
                }
                printf('<div %s>', $class_name);
                $data = array(
                  'name' => 'quiz-' . $q->id,
                  'id' => 'quiz-option-' . $q->$j . '-' . $q->id,
                  'value' => $q->$j,
                  'checked' => ($q->user_answer == $q->$j ? 'checked' : ''),
                  'style' => 'margin:10px',
                  'disabled' => 'disabled'
                );
                echo form_radio($data);
                $k = 'option_' . $q->$j; // e.g. option_1, option_2, ...
                echo form_label($q->$k, 'quiz-option-' . $q->$j . '-' . $q->id);
                echo '</div>';
                echo '<div class="clearfix"></div>';
              }
            }
            if ($answer_status == 'missing') {
              print $msg_no_answer;
            }
            ?>
            </div> <!--span11 -->
            <div class="span1 result-icon-wrapper">
              <?php
              switch ($answer_status) {
                case 'correct':
                  print('<img height="22" width="22" src="/themes/earetailprofessionals/images/quiz-icons/icon-correct.png" title="Correct answer!" alt="correct" />');
                  break;
                case 'wrong':
                case 'missing':
                  print('<img height="22" width="22" src="/themes/earetailprofessionals/images/quiz-icons/icon-wrong.png" title="Wrong answer!" alt="false" />');
                  break;
              }
              ?>
            </div> <!-- span1 -->
            </div> <!-- fluid-row -->
          </div> <!--span9-->
        </div> <!--row-fluid-->
        <?php
        break;
  }
}
?>
</div> <!--container-fluid -->
<div class="clearfix"></div>
