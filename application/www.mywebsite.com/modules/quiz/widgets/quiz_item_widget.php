<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Quiz_item_widget extends Widget {

    public function run($args = array()) {
        $this->load->config('quiz/quiz', TRUE);
        $this->load->config('quiz/sphero_badge_email', TRUE);
        $this->load->library('quiz/CmsQuiz');
        $this->load->library('training/CmsTraining');

        $this->load->helper('badge/badge');
        $this->load->helper('quiz/quiz');
        $this->load->helper('account/account');
        $this->load->helper('asset/asset');

        $data = array();
        $data['environment'] = $this->config->item('environment');
        $data['js'] = $this->config->item('js', 'quiz');
        $data['css'] = $this->config->item('css', 'quiz');
        $module_title = 'Quiz Detail';

        // check permission
        $data['userid'] = (int) ($this->session->userdata("user_id"));
        if (!has_permission('view_quiz')) {
            return array(
                'content' => '<div class="hero-unit"><p>You have to have user account to take a quiz.<br /> Please <a href="/login">login</a> or <a href="/register">register</a>.</p></div>',
                'meta_subtitle' => 'Quiz Detail'
            );
        }
        $site_id = $this->session->userdata('siteID');
        if (get_realtime_balance($site_id) < -25000) {
            $data['point_balance'] = 'out';
            //load up site name
            $site_name = $this->site_model->get_site_by_id($site_id)->name;
            return array(
                'content' => '
<div class="hero-unit">            
<div id="no-cheddar-img"></div>
    <br />
    <div class="box-title">This lab is all outta Cheddar!</div>
    <p>
      Training and quizzes for ' . $site_name . ' are currently unavailable due to insufficient Cheddar Points for the Training Lab.
    </p>
    </div>'
                ,
                'meta_subtitle' => 'Quiz Detail'
            );
        }

        // in the backend Page Publisher, randomly pick an item for demonstration.
        if ($data['environment'] == 'admin_panel') {
            $args['slug'] = CmsQuiz::random_slug();
        }

        if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
            $slug = $args['slug'];
            if (array_key_exists('title', $args)) {
                $data['title'] = $args['title'];
            }

            $data['error'] = $this->session->flashdata('error');

            if ($slug > '') {
                // is this a preview?
                $preview = get_cookie('preview_quiz');
                if ($preview > '' && strpos($preview, ':') > 0) {
                    $ids = explode(':', $preview);
                    $quiz_id = $ids[0];
                    $rev_id = $ids[1];
                    $rev_slug = $ids[2];
                    if ($rev_slug == $slug) {
                        if ($rev_id > 0) {
                            // preview a revision
                            $quiz = new CmsQuiz($quiz_id);
                            $item = $quiz->get_revision($rev_id);
                            if ($item) {
                                $item->title .= ' [revision: ' . date('Y-m-d H:i:s', $item->create_timestamp) . ']';
                            }
                        } else {
                            // preview a quiz
                            $quiz = new CmsQuiz($quiz_id);
                            $item = $quiz->type;
                            if ($item) {
                                $item->title .= ' [quiz preview]';
                            }
                        }
                    } else {
                        $item = new CmsQuiz($slug, TRUE);
                    }
                } else {
                    // load a published item
                    $item = new CmsQuiz($slug, TRUE);
                }
                if (!$item || $item->id == 0) {
                    // item not found. set 404 status
                    $this->output->set_status_header('404');
                    redirect('page-not-found');
                    return '<div class="hero-unit"><p>Quiz not found.</p></div>';
                }

                $data['item'] = $item;
                if ($this->input->post() == FALSE) {
                    // check how many attempts this user had made before
                    $under_limits = $item->quiz_history_check_attempts($data['userid']);
                    if (!$under_limits) {
                        $content = '<img class="img-responsive" src="/themes/cheddarLabs/images/img-quiz-limit-3Strikes.jpg" />';
                        $content .="<p>" . $item->errors() . "</p>";
                    } else {
                        // display the quiz questions, but only on the front end
                        if ($this->input->get('start') == 'yes' && $data['environment'] != 'admin_panel') {
                            // initialize a quiz history, and display randomly generated questions
                            $quiz_history = $item->quiz_start($data['userid']);
                            if (!$quiz_history || $quiz_history->id < 1) {
                                // item not found. set 404 status
                                $this->output->set_status_header('404');
                                redirect('page-not-found');
                                return '<div class="hero-unit"><p>Failed to initialize a quiz.</p></div>';
                            }
                            // load widget view
                            $data['history'] = $quiz_history;
                            $content = '<div id="quiz_form_wrapper"><form method="post" id="quiz_form">';
                            $content .= $this->render('quiz_questions', $data);
                            $content .= "</form></div>\n";
                        } else {
                            // display rules and a welcome screen
                            $content = $this->render('quiz_welcome', $data);
                        }
                    }
                } else {
                    // results submited
                    $quiz_history_id = (int) ($this->input->post('qhid'));
                    if ($quiz_history_id == 0) {
                        // item not found. set 404 status
                        $this->output->set_status_header('404');
                        redirect('page-not-found');
                        return '<div class="hero-unit"><p>Invalid quiz history ID.</p></div>';
                    }
                    $training = new CmsTraining($item->training_id, TRUE);
                    if (!$training || $training->id == 0) {
                        // item not found. set 404 status
                        $this->output->set_status_header('404');
                        redirect('page-not-found');
                        return '<div class="hero-unit"><p>Training subject not found.</p></div>';
                    }
                    $data['training'] = $training;
                    $user_answers = $this->input->post();
                    $result = $item->quiz_finish($quiz_history_id, $data['userid'], $user_answers);

                    //check previous results results for badges
                    //sphero badge
                    if(!check_user_badge($data['userid'], 'sphero')){
                        if (check_sphero_badge($data['userid'])) {
                        account_add_badge($data['userid'], 'sphero');
                        $data['sphero_badge_icon'] = badge_get_icon('sphero');
                        $data['sphero_badge'] = true;

                        //prepare pdf certificate

                        $this->load->helper('pdf_helper');

                        $user_info = $this->account_model->get_info($data['userid']);

                        $data['first_name'] = $user_info->first_name;
                        $data['last_name'] = $user_info->last_name;

                        $data['screen_name'] = $user_info->screen_name;
                        $data['date_issued'] = date('m-d-Y', time());

                        $cert['sphero'] = $this->load->view('sphero_cert', $data, true);
  
                        $email_att_file = $this->pdf_cert($cert);
                        $array_filelocation = explode('/', $email_att_file);
                        $cert_filename = end($array_filelocation);
                        //save info about certificate to db
                        $this->account_model->add_user_certificate('Sphero',$cert_filename,$data['userid']);
                   

                        //prepare and sent email
                        $message = $this->render('email_sphero_badge', $data);
                        $subject = "Sphero Expert Badge and discount code!";
                        $config['mailtype'] = "html";
                        $this->postmark->initialize($config);
                        //$this->email->set_newline("\r\n");
                        $this->postmark->from($this->config->item('admin_email', 'sphero_badge_email'), $this->config->item('site_title', 'sphero_badge_email'));
                        //get user's email
                        $this->load->model("account/account_model");
                        $user_info = $this->account_model->get_user($this->session->userdata("user_id"));
                        $this->postmark->to($user_info->email);
                        $this->postmark->subject($subject);
                        $this->postmark->message_html($message);
                        
                        $this->postmark->attach($email_att_file);
                        
                        $this->postmark->send();
                        
                        //save info about certificate to db
                        }
                    }
                    //genius badge
                    if (!check_user_badge($data['userid'], 'genius')) {
                        if (check_sphero_badge($data['userid'])) {
                            account_add_badge($data['userid'], 'genius');
                            $data['genius_badge_icon'] = badge_get_icon('genius');
                            $data['genius_badge'] = true;
                        }
                    }

                    if (!$result) {
                        $data['error'] = $item->errors();
                    }
                    $quiz_history = $item->quiz_history($quiz_history_id);
                    if (!$quiz_history || $quiz_history->id == 0) {
                        // item not found. set 404 status
                        $this->output->set_status_header('404');
                        redirect('page-not-found');
                        return '<div class="hero-unit"><p>Quiz history not found.</p></div>';
                    }
                    $data['history'] = $quiz_history;
                    // load widget view
                    $content = '<div id="quiz_results">';
                    $content .= $this->render('quiz_results', $data);
                    $content .= "</div>\n";
                }

                $content = '<div class="hero-unit">' . $content . '</div>';
                return array(
                    'meta_subtitle' => $item->name,
                    'content' => $content
                );
            }

            // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
            return '<div class="hero-unit"><p>Quiz not found.</p></div>';
        }

        if ($data['environment'] == 'admin_panel') {
            return '<div class="hero-unit"><p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p></div>';
        }
    }

    private function pdf_cert($html) {
        tcpdf();
        $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $obj_pdf->SetCreator(PDF_CREATOR);
        $title = "CheddarLabs";
//$obj_pdf->SetTitle($title);
//$obj_pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, PDF_HEADER_STRING);
        $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $obj_pdf->SetDefaultMonospacedFont('helvetica');
        $obj_pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $obj_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        $obj_pdf->setPrintHeader(false);

        $bMargin = $obj_pdf->getBreakMargin();
        $auto_page_break = $obj_pdf->getAutoPageBreak();

        $obj_pdf->SetAutoPageBreak(FALSE, 0);
        $obj_pdf->SetFont('helvetica', '', 9);
        $obj_pdf->setFontSubsetting(false);
        $obj_pdf->AddPage();

        $img_file = K_PATH_IMAGES . 'CL-certificate-full-w1.jpg';

        $obj_pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

        $obj_pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $obj_pdf->setPageMark();
        ob_start();
        // we can have any view part here like HTML, PHP etc
        print $html['sphero'];

        $content = ob_get_contents();
        ob_end_clean();
        $obj_pdf->writeHTML($content, true, false, true, false, '');
        $filename = 'cl_sphero_certificate_' . time() . '.pdf';
	$filelocation = "/var/www/vhosts/cheddarlabs.com/httpdocs/certificates"; //Linux
        //$filelocation = "/Users/antlik/www/hotCMS/httpdocs/certificates"; //Linux
        $obj_pdf->Output($filelocation . '/' . $filename, 'F'); //save to a local server file with the name given by name.


        //return $obj_pdf->Output($filename, 'E'); //return the document as base64 mime multi-part email attachment (RFC 2045)
    
        return $filelocation . '/' . $filename;
    }

}

?>
