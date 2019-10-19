<?php
// var_dump($_POST['data']);
require_once '../lib2/Phpword/src/PhpWord/Autoloader.php';
date_default_timezone_set('UTC');

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
use \PhpOffice\PhpWord\Shared\Html;

Autoloader::register();
Settings::loadConfig();

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();

$sectionStyle = array (
					'orientation' => 'landscape',
					'marginTop' => 600,
					'colsNum' => 2,
				);

$section = $phpWord->addSection($sectionStyle);
$section->getStyle()->setPageNumberingStart(1);

Html::addHtml($section, "<h1>Welcome Here</h1><h2>Welcome Again</h2><h3>And Welcome</h3>");
Html::addHtml($section, $_POST['data']);

// file_put_contents('./results/test.html', $_POST['data'] );
$file = "test.docx";
header("Content-Description: File Transfer");
// header('Content-Type: application/octet-stream');
// header('Content-Disposition: attachment;filename="test.docx"');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
// $objWriter->save('./results/test.docx');

// 
?>