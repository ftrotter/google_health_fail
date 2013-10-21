<?php

require_once('mysql.php');

$sXml = $HTTP_RAW_POST_DATA;

# LOAD XML FILE
$XML = new DOMDocument();
$XML->loadXML( $sXml );

# START XSLT
$xslt = new XSLTProcessor();
$XSL = new DOMDocument();
$XSL->load( 'PreparednessToCCR.xslt', LIBXML_NOCDATA);
$xslt->importStylesheet( $XSL );
#PRINT
$ccr = $xslt->transformToXML( $XML ); 

//PreparednessToCCR.xslt

//create a new key 
$key = generateId(15);

//check to see if it already exists...
//TODO

$xpath = new DOMXPath($XML);
$myFile = "log.txt";

$PatientLastNames = $xpath->query("//PatientLastName");
foreach($PatientLastNames as $PatientLastName){
	$patient_last_name = $PatientLastName->nodeValue;
}


$PatientFirstNames = $xpath->query("//PatientFirstName");
foreach($PatientFirstNames as $PatientFirstName){
	$patient_first_name = $PatientFirstName->nodeValue;
}

$PatientMiddleNames = $xpath->query("//PatientMiddleName");
foreach($PatientMiddleNames as $PatientMiddleName){
	$patient_middle_name = $PatientMiddleName->nodeValue;
}

//TODO
//There are many phones. 
//This needs to be fixed to point to 
//Just one phone, same for email address etc etc 

$Phones = $xpath->query("//Phone");
foreach($Phones as $Phone){
	$phone = $Phone->nodeValue;
}


$Emails = $xpath->query("//EmailID");
foreach($Emails as $Email){
	$email = $Email->nodeValue;
}

$new_person_sql = "
INSERT INTO `person` (
`id` ,
`first_name` ,
`middle_name` ,
`last_name` ,
`cell_phone` ,
`home_phone` ,
`email` ,
`address_line1` ,
`state` ,
`city` ,
`zip`
)
VALUES (
NULL , '$patient_first_name', '$patient_middle_name', '$patient_last_name', '$phone', '$phone', '$email', 'none', 'TX', 'Houston', '0'
);
";

mysql_query($new_person_sql);
$person_id = mysql_insert_id();

$patient_sql = "
INSERT INTO `patient` (
`id` ,
`person_id` ,
`key`
)
VALUES (
NULL , '$person_id', '$key'
);";

mysql_query($patient_sql);
$patient_id = mysql_insert_id();

$ccr_sql = "

INSERT INTO `ccrrecords` (
`id` ,
`patient_id` ,
`xml` ,
`source` ,
`date` ,
`note`
)
VALUES (
NULL , '$patient_id', '$ccr', '2',
CURRENT_TIMESTAMP , 'test'
);

";

mysql_query($ccr_sql);


//$print_me = var_export($_POST,true);
$myFile = "original_xml.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $sXml);
fclose($fh);

$myFile = "translated_xml.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $ccr);
fclose($fh);


$myFile = "ccr_sql.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $ccr_sql);
fclose($fh);




require_once('Zend/Pdf.php');


// Create new PDF 
$pdf = Zend_Pdf::load('card.pdf'); 

// Add new page to the document 
//$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4); 
//$pdf->pages[] = $page; 
$page = $pdf->pages[0];

$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12); 


$text = "Hi $patient_first_name $patient_last_name your QHR can be accessed at:";
$url="http://qhrdev.healthquilt.org/qhr/index.php?key=$key";

  $page->drawText($text,20,($page->getHeight()-300));
  $page->drawText($url,20,($page->getHeight()-330));
//  $page->drawText('Higher X',150,650);
//  $page->drawText('Higher Y',20,550);

$location = 750;
// Draw something on a page 
//$page->drawText($print_me, 100, 510); 
//while (strlen($print_me)>60 && $location > 100) {
//  $line = substr($print_me, 0, 60);
//  $page->drawText('Hi Mom',20,$location);
//  $location = $location - 10;
 // $print_me = substr($print_me, 60);
//}


// Get PDF document as a string 
$pdfData = $pdf->render(); 

header("Content-Disposition: inline; filename=result.pdf"); 
header("Content-type: application/pdf"); 
echo $pdfData; 


function generateId($length)
{
	$id = '';
        for ($i = 0; $i < $length; $i++) $id .= rand(0,1) ? chr(rand(48, 57)) : chr(rand(97, 122));
        return $id;
}



?>
