<?php
session_start();
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
set_time_limit(0);
//var_dump($_POST);

include_once 'Sample_Header.php';
// New Word Document
echo date('H:i:s'), ' Create new PhpWord object', EOL;
$phpWord = new \PhpOffice\PhpWord\PhpWord();

$phpWord->setDefaultFontName("Times New Roman");
$phpWord->setDefaultFontSize("9");
$page_margin = array("marginTop"=>100, 'marginLeft'=>100, 'marginRight'=>9000, 'marginBottom'=>100);


$section = $phpWord->addSection($page_margin);

$header = array('size' => 12, 'bold' => true, 'color'=>'FFFFFF');
$header_small = array('size' => 9, 'bold' => true, 'color'=>'000000');
$font_small_8 = array('size' => 8, 'bold' => false, 'color'=>'000000');
$font_small_7 = array('size' => 7, 'bold' => false, 'color'=>'000000');
$font_small_8_bold = array('size' => 8, 'bold' => true, 'color'=>'000000');

$rows = 1; $cols = 2;
$table_width = 91 * 80;
$styleCell = array('valign' => 'center',"alignment"=> \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
$fontStyle = array('bold' => true, 'size'=>14,"align"=>"center");
$simple_cell_style = array('bold' => false, 'size'=>9,"align"=>"left");
$simple_cell_style_bold = array('bold' => true, 'size'=>9,"align"=>"left");

$cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);

$styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'color'=>'FFFFFF', 'bgColor' => '000000');
$styleLastRow = array('borderBottomSize' => 6, 'borderBottomColor' => '000000', 'color'=>'FFFFFF', 'bgColor' => '000000');

$withBorder = array('valign' => 'top', 'borderSize' => 6, 'borderColor' => '000000');
$withBorderBottomOnly = array('valign' => 'top', 'borderSize'=>0, 'borderBottomSize' => 6, 'borderColor'=>'FFFFFF','borderBottomColor' => '000000');
$noBorder = array('valign' => 'top', 'borderSize' => 0, 'borderColor' => 'FFFFFF');
$cellColSpanInner2 = array('gridSpan' => 2, 'valign' => 'center');
$row_size = 300;

$phpWord->addTableStyle('ID Table', $styleTable, $styleFirstRow);
$phpWord->addTableStyle('Process Table', $styleTable);

$section->addTextBreak(1);

$table = $section->addTable("Process Table");
/* 
$table->addRow(300, array('exactHeight' => true));
 */
$cellColSpan = array('gridSpan' => 4, 'valign' => 'center');
$cellHCentered = array('alignment' => 'left');
$cellVCentered = array('valign' => 'center');



$section->addTextBreak(1);
//select all diagnostic in new case of deases
$new_case = array();//returnAllDataInTable($tbl="co_diagnostic",$con, $condition = "WHERE DiagnosticCategoryID='3' ORDER BY DiagnosticID ASC");
//var_dump($new_case);
$tbl_content = array(/*
					array(
						'size'=>300,
						'content'=>array(
										array('size'=>($table_width * 0.4),'content'=>'J) Synthèse par âge','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null),
										array('size'=>($table_width * 0.15),'content'=>'< 1 an','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null, 'align'=>$cellHCentered),
										array('size'=>($table_width * 0.15),'content'=>'1 à 4 ans','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null, 'align'=>$cellHCentered),
										array('size'=>($table_width * 0.15),'content'=>'5 à 19 ans','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null, 'align'=>$cellHCentered),
										array('size'=>($table_width * 0.15),'content'=>'≥ 20 ans','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null, 'align'=>$cellHCentered)
									)
						)*/
					);

//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");

$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.1),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.17),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										)
						);


$tbl_content[] = array(
						'size'=>600,
						'content'=>array(
										array('size'=>($table_width * 0.1),'content'=>'1','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.17),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										)
						);

$tbl_content[] = array(
						'size'=>600,
						'content'=>array(
										array('size'=>($table_width * 0.1),'content'=>'2','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.17),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										)
						);


$table = $section->addTable("Process Table");
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
	}
}


/* 
$cell1 = $table->addCell(10000, $cellColSpan);
$textrun2 = $cell1->addTextRun($cellHCentered);
$textrun2->addText(htmlspecialchars('I. Identification et Remarques importantes', ENT_COMPAT, 'UTF-8'),$header);
 */


$section->addTextBreak(1);

/*  */
end_try:
// Save file
echo write($phpWord, basename(__FILE__, '.php'), $writers);
if (!CLI) {
    include_once 'Sample_Footer.php';
}
