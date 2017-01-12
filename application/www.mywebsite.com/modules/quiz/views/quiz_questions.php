<style type="text/css">
  label.error {
    color:red;
    float:right !important;
    margin:2px 5px;
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
  .question input[type="radio"]{
      margin-right: 10px;
  }
  .question label {
    margin-top: 8px;
    font-style: italic;
  }
  .mulitple.question .answers{
    position: relative;
    padding-top: 15px;
  }
  .mulitple.question label.error {
    width: auto;
    position: absolute;
    top:0;
    right: 0
  }
  #divCountdown {
    position: fixed;
    top: 187px;
    left: 70%;
    padding: 15px;
    background: #fff;
  }
  #quizCountdown {
    font-size: 44px;
    height: 48px;
    padding-top: 10px;
  }
  @media(max-width: 810px) {
    #divCountdown {
      font-size: 14px;
      line-height: 1;
      left: auto;
      right: 5px;
      top: 135px;
    }
    #divCountdown > * {
      display: inline-block;
      *display: inline;
      zoom: 1;
    }
    #divCountdown *,
    #divCountdown #divCountdown,
    #divCountdown p {
      font-size:14px;
      height: auto;
      line-height: 1;
      margin: 0;
      padding: 0;
    }
  }
  .is-countdown {
    background-color: #fff;
    border: none;
    color: #008AC5;
  }
  .red {
    color: red;
  }
</style>
<script type="text/javascript">
  jQuery('document').ready(function() {
    var pageWidth = jQuery(window).width();
    jQuery(window).resize(function() {
      pageWidth = jQuery(window).width();
    });
    jQuery(window).scroll(function() {
      if (pageWidth < 811) {
        var toDocTop = jQuery(window).scrollTop() - 135;
        if (toDocTop < -134) {
          jQuery('#divCountdown').css('top', '135px');
        } else if (toDocTop < 0) {
          jQuery('#divCountdown').css('top', (135 + toDocTop) + 'px');
        } else {
          jQuery('#divCountdown').css('top', '0px');
        }
      }
    });
  });
</script>
<?php
$time_left = 0;
// consider time passed in case the user refreshes the browser
if ($item->type->time_limit * 60 > $history->time_passed) {
  $time_left = $item->type->time_limit * 60 - $history->time_passed;
}
?>
<script type="text/javascript">
  var timelimit = <?php print($time_left); ?>;
</script>

<div class="container-fluid quiz"  id="quiz-<?php print($history->id); ?>">
  <div class="row-fluid">
    <div class="box-featured-title">Training center</div>
    <?php
    printf('<h1>%s</h1>', $item->name);
    ?>
    <div id="divCountdown">
      <h2>TIMER</h2>
      <div id="quizCountdown"></div>
      <p><i>Left to complete quiz</i></p>
    </div>
  </div>

  <?php
  $counter_true_false = 0;
  $header_true_false = true;
  $counter_multiple = 0;
  $header_multiple = true;
  //$column_shifted = FALSE;
  $true_false_options = array(1 => 'true', 2 => 'false');
  foreach ($history->questions as $q) {
    switch ($q->question_type) {
      case '1': // true/false
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
            if ($q->required) {
              printf("<div class='question-sentense required'><strong>%s.</strong> %s</div>", $counter_true_false, $q->question);
            }
            else {
              printf("<div class='question-sentense'><strong>%s.</strong> %s</div>", $counter_true_false, $q->question);
            }

            foreach ($true_false_options as $k => $v) {
              $data = array(
                'name' => 'quiz-' . $q->id,
                'id' => 'quiz-' . $v . '-' . $q->id,
                'value' => $v,
                'checked' => ($this->input->post('quiz-' . $q->id) == $v ? 'checked' : ''),
                'style' => 'margin:10px',
                'class' => ($q->required ? $v . ' required' : $v)
              );
              print form_radio($data);
              print form_label(ucfirst($v), 'quiz-' . $v . '-' . $q->id);
            }
            ?>
          </div> <!-- span9 -->
        </div> <!-- row-fluid -->
        <?php
        break;

      case '2': // multiple
        $counter_multiple += 1;
        ?>
        <div class="row-fluid">
          <div class="span3">
            <?php
            if ($header_multiple) {
              print '<div class="questions-section">Multiple choice</div>';
              $header_multiple = false;
            }
            ?>
          </div>
          <div class="span9 question mulitple">
            <?php
            if ($q->required) {
              printf("<div class='question-sentense required'><strong>%s.</strong> %s</div>", $counter_multiple, $q->question);
            }
            else {
              printf("<div class='question-sentense'><strong>%s.</strong> %s    </div>", $counter_multiple, $q->question);
            }
            ?>
            <div class="answers">
            <?php
            for ($i=97; $i<=101; $i++) {
              $j = 'option_' . chr($i); // e.g. option_a, option_b, ...
              if ((int)($q->$j) > 0) {
                $k = 'option_' . $q->$j; // e.g. option_1, option_2, ...
                $data = array(
                  'name' => 'quiz-' . $q->id,
                  'id' => 'quiz-option-' . $q->$j . '-' . $q->id,
                  'value' => $q->$j,
                  'checked' => ($this->input->post() && $this->input->post('quiz-' . $q->id) == $q->$j ? 'checked' : ''),
                  'class' => ($q->required ? 'required' : '')
                );
                echo '<label for="quiz-option-' . $q->$j . "-" . $q->id. '">'.form_radio($data) . " " . $q->$k . "</label>";
                echo '<div class="clearfix"></div>';
              }
            }
            ?>
            </div> <!-- answers -->
          </div> <!-- span9 -->
        </div> <!-- row-fluid -->
        <?php
        break;
    }
    ?>

  <?php } ?>
  <div class="row-fluid">
    <div class="span3">
    </div>
    <div class="span9 question">
      <div class="buttons">
        <input type="hidden" name="action" value="submit" />
        <input type="hidden" name="qhid" value="<?php echo $history->id; ?>" />
        <input type="submit" name="quiz" value="Submit" class="btn btn-primary btn-large" />
      </div>
    </div>
  </div>
</div> <!-- container-fluid -->
<div class="clearfix"></div>
