<div class="clear"></div>

<div class="wrapper">
  <h2>Frequently Asked Questions</h2>
  <?php foreach ($faq_groups as $group) { ?>
  <div class="faqGroup">
    <a name="<?php printf('%s', url_title(strtolower($group->name),'dash'))?>">    
      <h3><?php printf('%s',$group->name)?></h3>
    </a>
    <?php foreach ($faqs as $faq) { ?>
      <?php if($group->id == $faq->group_id) { ?>
      <div class="question closed" id="q<?php printf('%s',$faq->id)?>">
        <h3 class="headerQuestion"><?php printf('&ldquo;%s&rdquo;', $faq->question)?> <img id="ar<?php printf('%s',$faq->id)?>" src="/asset/images/faq/question-arrow.jpg" alt="question arrow" /></h3>
        <div class="answer closed" id="a<?php printf('%s',$faq->id)?>">
          <?php printf('&ldquo;%s&rdquo;', $faq->answer)?>
        </div>
      </div><!-- end of question -->
      <?php } ?>
    <?php } ?>
    <hr />
  </div><!-- end of faqGroup -->
  <?php } ?>   
</div><!-- end of wrapper -->

<div class="clear"></div>
