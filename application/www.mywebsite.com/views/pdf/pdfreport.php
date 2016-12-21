<?php
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

$img_file = K_PATH_IMAGES.'CL-certificate-full-w1.jpg';

//var_dump($img_file);die();
$obj_pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

$obj_pdf->SetAutoPageBreak($auto_page_break, $bMargin);
$obj_pdf->setPageMark();
ob_start();
    // we can have any view part here like HTML, PHP etc
print $sphero;

    $content = ob_get_contents();
ob_end_clean();
$obj_pdf->writeHTML($content, true, false, true, false, '');
$filename='cl_sphero_certificate_'.time().'.pdf';
$filelocation = "/Users/antlik/www/hotCMS/httpdocs/certificates"; //Linux
//$obj_pdf->Output($filelocation.'/'.$filename, 'F'); //save to a local server file with the name given by name.


$obj_pdf->Output($filename, 'E'); //return the document as base64 mime multi-part email attachment (RFC 2045)