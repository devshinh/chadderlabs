<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Quiz Controller
 *
 * @package		HotCMS
 * @author		jeffrey@hottomali.com
 * @copyright	Copyright (c) 2012, HotTomali.
 * @since		Version 3.0
 */
class Quiz extends HotCMS_Controller {

  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_quiz')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->config('quiz/quiz', TRUE);
    $this->load->library('quiz/CmsQuiz');

    $this->module_url = $this->config->item('module_url', 'quiz');
    $this->module_header = $this->lang->line('hotcms_quiz');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_quiz'));
    $this->front_theme = $this->config->item('theme');
    $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'quiz');
  }

  /**
   * list all quizzes
   * @param  int  page number
   */
  public function index($page_num = 1, $message = '', $type_id = 1)
  {
    $data = array(
      'module_url' => $this->module_url,
      'module_header' => $this->module_header,
      'add_new_text' => $this->add_new_text,
      'java_script' => $this->java_script,
    );

    $right_data['css'] = 'modules/' . $this->module_url . '/css/quiz.css';

    $filters = array('type_id' => 0, 'status' => '');

    $left_data = array(
      'quiz_list' => CmsQuiz::list_quiz($filters)
    );

    // paginate configuration
    $this->load->library('pagination');
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = 10;

    $pagination_config['total_rows'] = CmsQuiz::count_all($filters);
    $right_data['items'] = CmsQuiz::list_quiz($filters, $page_num, $pagination_config['per_page']);

    // paginate
    $this->pagination->initialize($pagination_config);
    $right_data['pagination'] = $this->pagination->create_links();


    //settings tab
    $quiz_type = new CmsQuizType;
    $quiz_types = CmsQuizType::list_type();
    $object_quiz_type = array();
    foreach ($quiz_types as $type) {
      $type->sections = $quiz_type->type_list_section($type->id);
      $object_quiz_type[$type->id] = new CmsQuizType($type->id, TRUE);
      //$type_id = $type->id;
    }

    $right_data['quiz_by_type'] = $this->quiz_model->quiz_count($type_id, FALSE);
    //set message
    if (!empty($message)) {
      $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
      $data['message'] = self::setMessage(false);
    }

    $left_data['quiz_list'] = CmsQuiz::list_quiz($filters);

    $right_data['front_theme'] = $this->front_theme;
    $right_data['java_script'] = 'modules/' . $this->module_url . '/js/quiz_setting.js';

    // generate form
    $right_data['form'] = self::_setting_form($quiz_types);
    $right_data['section_type_array'] = array('1' => 'True/False', '2' => 'Multiple Choice');
    $right_data['quiz_types'] = $quiz_types;
    $right_data['quiz_types_object'] = $object_quiz_type;
    $quiz_type_dropdown = array();
    foreach ($quiz_types as $type) {
      $quiz_type_dropdown[$type->id] = $type->name;
    }
    $right_data['selected_type'] = $type_id;
    $right_data['quiz_type_dropdown'] = $quiz_type_dropdown;
    //$trainings = array('' => ' -- select training item -- ');
    //foreach (CmsTraining::list_training() as $v) {
    //  $trainings[$v->id] = $v->title;
    //}
    //$right_data['trainings'] = $trainings;

    $right_data['module_url'] = $this->module_url;
    $right_data['settings'] = $this->load->view('quiz_setting', $right_data, true);
    self::loadBackendView($data, 'quiz/quiz_leftbar', $left_data, 'quiz/quiz', $right_data);
  }

  /**
   * Create a new item
   */
  public function create($message = '')
  {
    //$this->output->enable_profiler(TRUE);
    // check permission
    if (!(has_permission('manage_quiz') || has_permission('create_quiz'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $data = array(
      'module_url' => $this->module_url,
      'module_header' => $this->module_header,
      'add_new_text' => $this->add_new_text,
      'java_script' => $this->java_script,
    );

    $this->form_validation->set_rules('quiz_type_id', 'lang:hotcms_quiz_type', 'required');
    $this->form_validation->set_rules('training_id', 'lang:hotcms_training', 'required');
    $this->form_validation->set_rules('name', 'lang:hotcms_name', 'trim|required');

    //set message
    if (!empty($message)) {
      $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
      $data['message'] = self::setMessage(false);
    }

    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $quiz = new CmsQuiz;
      $created = $quiz->create($attr);
      if ($created) {
        $data['message'] = $this->add_message('confirm', 'Quiz ' . $this->lang->line('hotcms_created_item'));
        redirect($this->module_url . '/edit/' . $quiz->id);
        exit;
      }
      else {
        //$this->add_message('error', $quiz->errors());
        $message = array();
        $message['type'] = 'error';
        $message['value'] = $quiz->errors();
      }
    }
    else {
      if (validation_errors() != "") {
        $message = array();
        $message['type'] = 'error';
        $message['value'] = validation_errors();
      }
//      $this->add_message('error', validation_errors());
    }

    $filters = array('type_id' => 0, 'status' => '');

    $left_data = array();
    $left_data['quiz_list'] = CmsQuiz::list_quiz($filters);

    // generate form
    $right_data = array();
    $right_data['css'] = 'modules/' . $this->module_url . '/css/quiz_create.css';
    $right_data['form']['name_input'] = $this->_create_text_input('name', '', 100, 40, '');
    $quiz_types = array('' => ' -- select type -- ');
    foreach (CmsQuizType::list_type() as $v) {
      $quiz_types[$v->id] = $v->name;
    }
    $right_data['quiz_types'] = $quiz_types;
    $this->load->library('training/CmsTraining');
    $trainings = array('' => ' -- select training item -- ');
    foreach (CmsTraining::list_training(NULL, FALSE) as $v) {
      $trainings[$v->id] = $v->title;
    }
    $right_data['trainings'] = $trainings;
    $right_data["targets"] = $this->quiz_model->get_all_target_options();
    $right_data["target_of_trainings"] = $this->quiz_model->get_all_training_s_target();
    //$this->load_messages();
    $data['message'] = $message;
    self::loadBackendView($data, 'quiz/quiz_leftbar', $left_data, 'quiz/quiz_create', $right_data);
  }

  /**
   * Edit an existing item
   * @param  int  id
   */
  public function edit($id)
  {
    $data = array(
      'module_url' => $this->module_url,
      'module_header' => $this->module_header,
      'add_new_text' => $this->add_new_text,
    );

    $quiz = new CmsQuiz($id);

    // check permission
    if (!((has_permission('create_quiz') && $quiz->author_id == $this->user_id)
      || has_permission('edit_quiz') || has_permission('manage_quiz'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    // assign validation rules
    $this->form_validation->set_rules('quiz_type_id', 'lang:hotcms_quiz_type', 'required');
    $this->form_validation->set_rules('training_id', 'lang:hotcms_training', 'required');
    $this->form_validation->set_rules('target_id', 'lang:hotcms_training', 'required');
    $this->form_validation->set_rules('name', 'lang:hotcms_name', 'trim|required');

    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $saved = $quiz->save($attr);
    }
    else {
      $this->add_message('error', validation_errors());
    }

    $data['message'] = self::setMessage(false);

    $filters = array('type_id' => 0, 'status' => '');

    $left_data['quiz_list'] = CmsQuiz::list_quiz($filters);

    foreach ($quiz->questions as $q) {
      $q->admin_display = $this->render_question_admin_display($q);
      //$q->admin_form = $this->render_question_admin_form($q);
    }
    $right_data['item'] = $quiz;
    $right_data['front_theme'] = $this->front_theme;
    //$right_data['schedule_array'] = array('0'=>'No Schedule', '1'=>'Scheduled');
    $right_data['java_script'] = 'modules/' . $this->module_url . '/js/quiz_edit.js';
    $right_data['css'] = 'modules/' . $this->module_url . '/css/quiz_edit.css';

    // generate form
    $right_data['form'] = self::_edit_form($quiz);
    $right_data['status_array'] = array('0' => 'Inactive', '1' => 'Active');
    $right_data['section_array'] = array('1' => 'True/False', '2' => 'Multiple Choice');
    $quiz_types = array('' => ' -- select type -- ');
    foreach (CmsQuizType::list_type() as $v) {
      $quiz_types[$v->id] = $v->name;
    }
    $right_data['quiz_types'] = $quiz_types;

    $this->load->library('training/CmsTraining');
    $trainings = array('' => ' -- select training item -- ');
    foreach (CmsTraining::list_training(NULL, FALSE) as $v) {
      $trainings[$v->id] = $v->title;
    }
    $right_data['trainings'] = $trainings;
    $right_data["targets"] = $this->quiz_model->get_all_target_options();
    $right_data["target_of_trainings"] = $this->quiz_model->get_all_training_s_target();

    $this->load_messages();

    $cookie = $this->set_tab_cookie(0);
    $this->input->set_cookie($cookie);

    self::loadBackendView($data, 'quiz/quiz_leftbar', $left_data, 'quiz/quiz_edit', $right_data);
  }

  /**
   * Save quiz into database using Ajax
   * @param  int  quiz ID
   */
  public function ajax_save($quiz_id)
  {
    $this->form_validation->set_rules('quiz_type_id', 'lang:hotcms_quiz_type', 'required');
    $this->form_validation->set_rules('training_id', 'lang:hotcms_training', 'required');
    $this->form_validation->set_rules('target_id', 'lang:hotcms_training', 'required');
    $this->form_validation->set_rules('name', 'lang:hotcms_name', 'trim|required');
    $messages = '';
    if ($this->form_validation->run()) {
      $attr = $this->input->post();
      $quiz = new CmsQuiz($quiz_id);
      $result = $quiz->save($attr);
      $messages = $quiz->messages() . $quiz->errors();
    }
    else {
      $result = FALSE;
      $error = validation_errors();
      $messages .= strip_tags($error) . "\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Adds a question to quiz
   * @param  int  section id
   * @param  int  section type id
   */
  public function ajax_add_question($section_id, $type_id)
  {
    $result = FALSE;
    $messages = '';
    $question_form = '';
    if ($this->input->post() !== FALSE) {
      $id = (int) ($this->input->post('quiz_id'));
      $section_id = (int) $section_id;
      $type_id = (int) $type_id;
      if ($id > 0 && $section_id > 0 && $type_id > 0) {
        $attr = array(
          'section_id' => $section_id,
          'question_type' => $type_id,
        );
        try {
          $quiz = new CmsQuiz($id);
          $question_id = $quiz->add_question($attr);
          if ($question_id > 0) {
            $question = $quiz->load_question($question_id);
            $question_form = $this->render_question_admin_form($question);
            $result = TRUE;
          }
          $messages = $quiz->messages() . $quiz->errors();
        }
        catch (Exception $e) {
          $messages = 'There was an error when trying to add question: ' . $e->getMessage();
        }
      }
      else {
        $messages = 'Invalid section or question type.';
      }
    }
    $json = array('result' => $result, 'messages' => $messages, 'question_form' => $question_form);
    echo json_encode($json);
  }

  /**
   * Displays a question as text
   * @param  int  question id
   */
  public function ajax_question_display($question_id)
  {
    if ($question_id > 0) {
      //$quiz_id = (int)($attr['quiz_id']);
      $quiz = new CmsQuiz();
      $question = $quiz->load_question($question_id);
      $text = $this->render_question_admin_display($question);
      echo $text;
    }
  }

  /**
   * Generates a question editing form
   * @param  int  question id
   */
  public function ajax_question_edit_form($question_id)
  {
    if ($question_id > 0) {
      //$quiz_id = (int)($attr['quiz_id']);
      $quiz = new CmsQuiz();
      $question = $quiz->load_question($question_id);
      $form = $this->render_question_admin_form($question);
    }
    else {
      // form for adding new question
      $form = $this->render_question_admin_form();
    }
    echo $form;
  }

  /**
   * Updates a quiz question
   * @param  int  question id
   */
  public function ajax_save_question($question_id)
  {
    $messages = '';
    $attr = $this->input->post();
    if ($question_id > 0 && !empty($attr)) {
      //$quiz_id = (int)($attr['quiz_id']);
      $quiz = new CmsQuiz();
      $result = $quiz->update_question($question_id, $attr);
      $messages = $quiz->messages() . $quiz->errors();
    }
    else {
      $result = FALSE;
      $messages = "Quiz question not found.\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Deletes a quiz question
   * @param  int  question id
   */
  public function ajax_delete_question($question_id)
  {
    $messages = '';
    if ($question_id > 0) {
      $quiz = new CmsQuiz();
      $result = $quiz->delete_question($question_id);
      $messages = $quiz->messages() . $quiz->errors();
    }
    else {
      $result = FALSE;
      $messages = "Quiz question not found.\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Quiz editing form
   * @param  object  quiz object
   */
  private function _edit_form($item)
  {
    $data = array();
    $data['hidden_fields'] = array();
    $data['hidden_fields']['quiz_id'] = $item->id;

    $data['name_input'] = $this->_create_text_input('name', $item->name, 100, 40, '');
//    $data['scheduled_publish_date_input'] = $this->_create_text_input('scheduled_publish_date', $item->scheduled_publish_timestamp > 0 ? date('Y-m-d H:i:s', $item->scheduled_publish_timestamp) : '', 100, 20, 'schedule');
//    $data['scheduled_archive_date_input'] = $this->_create_text_input('scheduled_archive_date', $item->scheduled_archive_timestamp > 0 ? date('Y-m-d H:i:s', $item->scheduled_archive_timestamp) : '', 100, 20, 'schedule');
    return $data;
  }

  /**
   * Calling delete function from model class
   * @param  int  id of item
   * @return void
   */
  public function delete($id)
  {
    $quiz = new CmsQuiz($id);
    // check permission
    if (!((has_permission('create_quiz') && $quiz->author_id == $this->user_id)
      || has_permission('edit_quiz') || has_permission('manage_quiz'))) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $quiz->delete();

    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_item');

    $cookie = $this->set_tab_cookie(0);
    $this->input->set_cookie($cookie);

    $this->index(1, $message);
  }

  /**
   * Renders a question as text for displaying
   * @param  object  question object
   * @return string
   */
  protected function render_question_admin_display($question)
  {
    $result = '<div class="question_num"></div>';
    $result .= form_hidden('question_type_' . $question->id, $question->question_type);
    switch ($question->question_type) {
      // true/false quiz
      case 1:
        $result .= '<p class="question_input">' . $question->question . '</p>';
        $result .= '<p class="question_answer">';
        $option = 'True';
        if ($question->correct_answer == 1) {
          $option = '<b>' . $option . '</b>';
        }
        $result .= $option . ' &nbsp; &nbsp; ';
        $option = 'False';
        if ($question->correct_answer == 2) {
          $option = '<b>' . $option . '</b>';
        }
        $result .= $option;
        if ($question->must_show == 1) {
          $result .= '&nbsp; &nbsp; (Must Show)';
        }
        break;
      // multiple choice
      case 2:
        $result .= '<p class="question_input">' . $question->question;
        if ($question->must_show == 1) {
          $result .= '&nbsp; &nbsp; (Must Show)';
        }
        $result .= '<br /><br />';
        $option = $question->option_1;
        if ($question->correct_answer == 1) {
          $option = '<b>' . $option . ' (correct)</b>';
        }
        $result .= 'A) ' . $option . '<br /><br />';
        $option = $question->option_2;
        if ($question->correct_answer == 2) {
          $option = '<b>' . $option . ' (correct)</b>';
        }
        $result .= 'B) ' . $option . '<br /><br />';
        $option = $question->option_3;
        if ($question->correct_answer == 3) {
          $option = '<b>' . $option . ' (correct)</b>';
        }
        $result .= 'C) ' . $option . '<br /><br />';
        $option = $question->option_4;
        if ($question->correct_answer == 4) {
          $option = '<b>' . $option . ' (correct)</b>';
        }
        $result .= 'D) ' . $option;
        $result .= '</p>';
        break;
    }
    $result .= '<div class="controls">';
    $result .= '<a href="' . $question->id . '" class="red_button edit_question_link" target="_blank">Edit</a>';
    $result .= ' &nbsp; &nbsp; &nbsp; ';
    $result .= '<a href="' . $question->id . '" class="red_button delete_question_link" target="_blank">Delete</a>';
    $result .= '</div>';
    return $result;
  }

  /**
   * Renders a question administration form
   * @param  object  question object
   * @return string
   */
  protected function render_question_admin_form($question)
  {
    $qid = $question->id;
    $result = '<div class="question_num"></div>';
    $result .= form_hidden('question_type_' . $qid, $question->question_type);
    switch ($question->question_type) {
      // true/false question
      case 1:
        if ($question->question == '') {
          $question->question = 'Enter question here.';
        }
        $result .= form_textarea(array(
          'name' => 'question_' . $qid,
          'id' => 'question_' . $qid,
          'value' => $question->question,
          'cols' => '100',
          'rows' => '4',
          ));
        $result .= '<div class="clear"></div>';
        $result .= '<div class="question_answer_form">';
        $result .= form_radio(array(
          'name' => 'correct_answer_' . $qid,
          'id' => 'correct_answer_' . $qid . '_1',
          'value' => '1',
          'checked' => $question->correct_answer == 1,
          ));
        $result .= ' ' . form_label('True', 'correct_answer_' . $qid . '_1');
        $result .= ' &nbsp; &nbsp; &nbsp; ';
        $result .= form_radio(array(
          'name' => 'correct_answer_' . $qid,
          'id' => 'correct_answer_' . $qid . '_2',
          'value' => '2',
          'checked' => $question->correct_answer == 2,
          ));
        $result .= ' ' . form_label('False', 'correct_answer_' . $qid . '_2');
        $result .= ' &nbsp; &nbsp; &nbsp; ';
        $result .= form_checkbox(array(
          'name' => 'question_mustshow_' . $qid,
          'id' => 'question_mustshow_' . $qid,
          'value' => '1',
          'checked' => $question->must_show == 1,
          ));
        $result .= ' ' . form_label('Must Show', 'question_mustshow_' . $qid);
//        $result .= ' &nbsp; &nbsp; &nbsp; ';
//        $result .= form_checkbox(array(
//            'name' => 'question_required_' . $qid,
//            'id' => 'question_required_' . $qid,
//            'value' => '1',
//            'checked' => $question->required == 1,
//                ));
//        $result .= ' ' . form_label('Required', 'question_required_' . $qid);
        $result .= '</div>';
        break;
      // multiple choice
      case 2:
        if ($question->question == '')
          $question->question = 'Enter question here.';
        $result .= '<div class="question_input_form">';
        $result .= form_textarea(array(
          'name' => 'question_' . $qid,
          'id' => 'question_' . $qid,
          'value' => $question->question,
          'cols' => '100',
          'rows' => '4',
          ));
        $result .= '</div>';
        $result .= '<div class="required_question_input">';
        $result .= form_checkbox(array(
          'name' => 'question_mustshow_' . $qid,
          'id' => 'question_mustshow_' . $qid,
          'value' => '1',
          'checked' => $question->must_show == 1,
          ));
        $result .= ' ' . form_label('Must Show', 'question_mustshow_' . $qid);
//        $result .= form_checkbox(array(
//            'name' => 'question_required_' . $qid,
//            'id' => 'question_required_' . $qid,
//            'value' => '1',
//            'checked' => $question->required == 1,
//                ));
//        $result .= ' ' . form_label('Required', 'question_required_' . $qid);
        $result .= '</div>';
        $result .= '<div class="clear"></div>';
        // option 1
        if ($question->option_1 == '')
          $question->option_1 = 'answer A';
        $result .= '<div class="answer_header">Answer A) </div>';
        $result .= '<div class="answer_correct">';
        $result .= form_radio(array(
          'name' => 'correct_answer_' . $qid,
          'id' => 'correct_answer_' . $qid . '_1',
          'value' => '1',
          'checked' => $question->correct_answer == 1,
          ));
        $result .= ' ' . form_label('Correct', 'correct_answer_' . $qid . '_1') . '<br />';
        $result .= '</div>';
        $result .= '<div class="clear"></div>';
        $result .= form_textarea(array(
          'name' => 'option_1_' . $qid,
          'id' => 'option_1_' . $qid,
          'value' => $question->option_1,
          'cols' => '100',
          'rows' => '4',
          ));
        $result .= '<div class="clear"></div>';
        // option 2
        if ($question->option_2 == '')
          $question->option_2 = 'answer B';
        $result .= '<div class="answer_header">Answer B) </div>';
        $result .= '<div class="answer_correct">';
        $result .= form_radio(array(
          'name' => 'correct_answer_' . $qid,
          'id' => 'correct_answer_' . $qid . '_2',
          'value' => '2',
          'checked' => $question->correct_answer == 2,
          ));
        $result .= ' ' . form_label('Correct', 'correct_answer_' . $qid . '_2') . '<br />';
        $result .= '</div>';
        $result .= '<div class="clear"></div>';
        $result .= form_textarea(array(
          'name' => 'option_2_' . $qid,
          'id' => 'option_2_' . $qid,
          'value' => $question->option_2,
          'cols' => '100',
          'rows' => '4',
          ));
        $result .= '<div class="clear"></div>';
        // option 3
        if ($question->option_3 == '')
          $question->option_3 = 'answer C';
        $result .= '<div class="answer_header">Answer C) </div>';
        $result .= '<div class="answer_correct">';
        $result .= form_radio(array(
          'name' => 'correct_answer_' . $qid,
          'id' => 'correct_answer_' . $qid . '_3',
          'value' => '3',
          'checked' => $question->correct_answer == 3,
          ));
        $result .= ' ' . form_label('Correct', 'correct_answer_' . $qid . '_3') . '<br />';
        $result .= '</div>';
        $result .= '<div class="clear"></div>';
        $result .= form_textarea(array(
          'name' => 'option_3_' . $qid,
          'id' => 'option_3_' . $qid,
          'value' => $question->option_3,
          'cols' => '100',
          'rows' => '4',
          ));
        $result .= '<div class="clear"></div>';
        // option 4
        if ($question->option_4 == '')
          $question->option_4 = 'answer D';
        $result .= '<div class="answer_header">Answer D) </div>';
        $result .= '<div class="answer_correct">';
        $result .= form_radio(array(
          'name' => 'correct_answer_' . $qid,
          'id' => 'correct_answer_' . $qid . '_4',
          'value' => '4',
          'checked' => $question->correct_answer == 4,
          ));
        $result .= ' ' . form_label('Correct', 'correct_answer_' . $qid . '_4') . '<br />';
        $result .= '</div>';
        $result .= '<div class="clear"></div>';
        $result .= form_textarea(array(
          'name' => 'option_4_' . $qid,
          'id' => 'option_4_' . $qid,
          'value' => $question->option_4,
          'cols' => '100',
          'rows' => '4',
          ));
        $result .= '<div class="clear"></div>';
        break;
    }
    $result .= '<div class="controls">';
    $result .= '<a href="' . $qid . '" class="red_button save_question_link" target="_blank">Save</a>';
    $result .= ' &nbsp; &nbsp; &nbsp; ';
    $result .= '<a href="' . $qid . '" class="red_button cancel_question_link" target="_blank">Cancel</a>';
    $result .= '</div>';
    return $result;
  }

  /**
   * Quiz settings
   */

  /**
   * Save setting for quiz type
   * @param  int  quiz type id
   */
  public function save_setting($type_id)
  {
    $this->load->model('quiz_model');

    $this->form_validation->set_rules('time_limit_' . $type_id, 'lang:time_limit', 'trim|required');
    $this->form_validation->set_rules('name_' . $type_id, 'lang:hotcms_name', 'trim|required');

    $sections = $this->quiz_model->type_list_section($type_id);
    $changed_sections = array();
    foreach ($sections as $s) {

      //set validation rules

      $this->form_validation->set_rules('question_pool_' . $s->id, 'lang:question_pool', 'trim|required|numeric');
      $this->form_validation->set_rules('questions_per_quiz_' . $s->id, 'lang:questions_per_quiz', 'trim|required|numeric');

      //check if section was changed
      if (($s->section_type != $this->input->post('section_type_' . $s->id))
        || ($s->question_pool != $this->input->post('question_pool_' . $s->id))
        || ($s->questions_per_quiz != $this->input->post('questions_per_quiz_' . $s->id))) {
        $changed_sections[$s->id] = $s->id;
      }
    }

    if ($this->form_validation->run()) {
      $this->quiz_model->type_update($type_id, $this->input->post());

      //save changed sections
      $attr = array();
      foreach ($changed_sections as $cs) {
        $attr['section_type_' . $cs] = $this->input->post('section_type_' . $cs);
        $attr['question_pool_' . $cs] = $this->input->post('question_pool_' . $cs);
        $attr['questions_per_quiz_' . $cs] = $this->input->post('questions_per_quiz_' . $cs);
        $this->quiz_model->type_update_section($cs, $attr);
      }

      $message = array();
      $message['type'] = 'confirm';
      $message['value'] = 'Quiz setting' . $this->lang->line('hotcms_updated_item');
    }
    else {
      $message = array();
      $message['type'] = 'error';
      $message['value'] = validation_errors();
    }

    $cookie = $this->set_tab_cookie(1);
    $this->input->set_cookie($cookie);

    $this->index(1, $message, $type_id);
  }

  /**
   * Save setting for quiz section
   * @param  int  section id
   *
   * unused
   */
  public function save_setting_section($section_id, $type_id)
  {
    $this->load->model('quiz/quiz_model');

    $this->form_validation->set_rules('questions_per_quiz_' . $section_id, 'lang:questions_per_quiz', 'trim|required');
    $this->form_validation->set_rules('question_pool_' . $section_id, 'lang:question_pool', 'trim|required');


    if ($this->form_validation->run()) {
      $this->quiz_model->type_update_section($section_id, $this->input->post());

      $message = array();
      $message['type'] = 'confirm';
      $message['value'] = $this->lang->line('hotcms_updated_item');
    }
    else {
      $message = array();
      $message['type'] = 'error';
      $message['value'] = validation_errors();
    }
  }

  /**
   * Add new quiz section
   * @param  int  quiz type id
   */
  public function add_section($type_id)
  {
    $this->load->model('quiz_model');
    $this->quiz_model->add_section($type_id);

    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_created_item');

    $cookie = $this->set_tab_cookie(1);
    $this->input->set_cookie($cookie);

    $this->index(1, $message, $type_id);
  }

  /**
   * Delete quiz section
   * @param  int section id
   * @param int type id
   */
  public function delete_section($section_id, $type_id)
  {
    $this->load->model('quiz_model');
    $this->quiz_model->delete_section($section_id, $type_id);

    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_item');

    $cookie = $this->set_tab_cookie(1);
    $this->input->set_cookie($cookie);

    $this->index(1, $message);
  }

  /**
   * Add new quiz type
   */
  public function add_quiz_type()
  {
    $this->load->model('quiz_model');
    $id = $this->quiz_model->add_quiz_type();
    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_created_item');

    $cookie = $this->set_tab_cookie(1);
    $this->input->set_cookie($cookie);

    $this->index(1, $message, $id);
  }

  /**
   * Quiz setting form
   * @param  object  quiz types
   */
  private function _setting_form($quiz_types)
  {
    $data = array();
    $data['hidden_fields'] = array();
    //$data['hidden_fields']['type_id'] = $item->id;

    foreach ($quiz_types as $type) {
      $id = $type->id;
      $data['hidden_fields']['icon_image_id_' . $id] = $type->icon_image_id;
      //$data['hidden_fields']['icon_image_id'] = $type->icon_image_id;
      $data['name_' . $id] = $this->_create_text_input('name_' . $id, $type->name, 100, 20, '');
      $data['time_limit_' . $id] = $this->_create_text_input('time_limit_' . $id, $type->time_limit, 100, 20, '');
      $data['expiry_period_' . $id] = $this->_create_text_input('expiry_period_' . $id, $type->expiry_period, 100, 20, '');
      $data['tries_per_day_' . $id] = $this->_create_text_input('tries_per_day_' . $id, $type->tries_per_day, 100, 20, '');
      $data['tries_per_week_' . $id] = $this->_create_text_input('tries_per_week_' . $id, $type->tries_per_week, 100, 20, '');
      $data['points_pre_expiry_' . $id] = $this->_create_text_input('points_pre_expiry_' . $id, $type->points_pre_expiry, 100, 20, '');
      $data['points_post_expiry_' . $id] = $this->_create_text_input('points_post_expiry_' . $id, $type->points_post_expiry, 100, 20, '');
      $data['contest_entries_pre_expiry_' . $id] = $this->_create_text_input('contest_entries_pre_expiry_' . $id, $type->contest_entries_pre_expiry, 100, 20, '');
      $data['contest_entries_post_expiry_' . $id] = $this->_create_text_input('contest_entries_post_expiry_' . $id, $type->contest_entries_post_expiry, 100, 20, '');      
      foreach ($type->sections as $section) {
        $data['question_pool_' . $section->id] = $this->_create_text_input('question_pool_' . $section->id, $section->question_pool, 100, 20, 'required');
        $data['questions_per_quiz_' . $section->id] = $this->_create_text_input('questions_per_quiz_' . $section->id, $section->questions_per_quiz, 100, 20, 'required');
      }
    }
    return $data;
  }

  /**
   * Updates quiz setting using Ajax
   * @param  int  quiz type ID
   */
  public function ajax_setting_update($type_id)
  {
    $messages = '';
    $attr = $this->input->post();
    if ($type_id > 0 && !empty($attr)) {
      $quiz_type = new CmsQuizType($type_id);
      $result = $quiz_type->update($attr);
      $messages = $quiz_type->messages() . $quiz_type->errors();
    }
    else {
      $result = FALSE;
      $messages = "Quiz type not found.\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Deletes a quiz type
   * @param  int  id of item
   * @return void
   */
  public function delete_type($type_id)
  {
    // check permission
    //if (!((has_permission('create_quiz') && $quiz->author_id == $this->user_id)
    //        || has_permission('edit_quiz') || has_permission('manage_quiz'))) {
    //  show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    //}
    //check if there is no quiz in deleted type
    $this->load->model('quiz_model');
    $number_of_quizzes = $this->quiz_model->check_quiz_per_type($type_id);

    $cookie = $this->set_tab_cookie(1);
    $this->input->set_cookie($cookie);

    if ($number_of_quizzes > 0) {
      $message = array();
      $message['type'] = 'error';
      $message['value'] = 'Quiz for this type exists!';
      $this->index(1, $message);
    }
    else {
      //delete quiz type + all sections
      $this->quiz_model->delete_sections_by_type_id($type_id);
      $this->quiz_model->delete_quiz_type($type_id);

      $message = array();
      $message['type'] = 'confirm';
      $message['value'] = $this->lang->line('hotcms_deleted_item');

      $this->index(1, $message);
    }
    //redirect($this->module_url . '/setting');
  }

  /**
   * Updates quiz setting sections using Ajax
   * @param  int  quiz type ID
   */
  public function ajax_setting_section_update($section_id)
  {
    $messages = '';
    $attr = $this->input->post();
    if ($section_id > 0 && !empty($attr)) {
      $quiz_type = new CmsQuizType();
      $result = $quiz_type->update_section($section_id, $attr);
      $messages = $quiz_type->messages() . $quiz_type->errors();
    }
    else {
      $result = FALSE;
      $messages = "Quiz type not found.\n";
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }

  /**
   * Results
   * @param  int  quiz type ID
   */
  public function result($qid)
  {
    //save result to db
    $this->load->model('quiz_model');
    $this->quiz_model->save_result($qid, $this->input->post());
    //load quiz data
    $quiz = new CmsQuiz($qid);
    $data = array();
    $data['results'] = $this->input->post();
    $data['quiz'] = $quiz;
    $this->load->view('quiz_results', $data);
    //$this->loadView('quiz_results', $quiz);
  }

  /**
   * Reload quiz index page when type is changed
   * @param  int  quiz type ID
   */
  public function setting_reload()
  {
    $type_id = $this->input->post('quiz_type');
    $cookie = $this->set_tab_cookie(1);
    $this->input->set_cookie($cookie);
    $this->index(1, '', $type_id);
  }

  /**
   * function to set active tab cookie
   * @param int tab index
   *
   * @return array cookie setting
   */
  private function set_tab_cookie($tab_id)
  {
    return $cookie = array(
      'name' => 'selectedTab',
      'value' => $tab_id,
      'expire' => '3600',
      'domain' => $this->config->item('domain'),
      'prefix' => '',
      'secure' => FALSE,
      'path' => '/hotcms/quiz/'
    );
  }

  /**
   * Image selection form
   * @param  string  asset ID
   * @param  string  training ID
   * @return string
   */
  public function ajax_image_chooser($asset_id = 0, $quiz_type_id = 0)
  {
    $this->load->library('asset/asset_item');
    $this->load->helper('asset/asset');
    $result = FALSE;
    $messages = '';
    $content = '';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('asset_id', $attr) && $attr['asset_id'] > 0 && $quiz_type_id > 0) {
      $result = $this->quiz_model->asset_update($field_id, $attr);
    }
    else {
      $result = TRUE;
    }
    $image = NULL;
    $asset_id = (int) $asset_id;
    if ($asset_id > 0) {
      $image = asset_load_item($asset_id);
    }
    $data['asset_id'] = $asset_id;
    $data['image'] = $image;

    $asset_category_id = 1; // default image category
    $data['asset_category_id'] = $asset_category_id;
    $images = array();

    // build the config form
    $category_context = 'quiz_icons';
    $asset_categories = asset_list_categories(array('context' => $category_context));

    $options = array('' => ' -- select category -- ');
    foreach ($asset_categories as $c) {
      $options[$c->id] = $c->name;
    }
    $data['asset_categories'] = $options;


    $args = array();
    $args['asset_category_id'] = $asset_category_id;
    //var_dump($args);
    //var_dump(asset_upload_ui($args));
    $data['media_upload_ui'] = asset_upload_ui($args);
    $images = asset_images_ui($args + array('single_selection' => 'ON'));
    $data['media_library_ui'] = $images['formatted'];
    $content = $this->load->view('quiz_image_chooser', $data, true);
    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }

}

?>
