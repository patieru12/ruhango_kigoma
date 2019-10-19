<?php
session_start();
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
set_time_limit(0);
//connect the SIS Additional Library
require_once "../plugins/sis_v1.0.php";
//var_dump($_POST);

include_once 'Sample_Header.php';
// New Word Document
echo date('H:i:s'), ' Create new PhpWord object', EOL;
$phpWord = new \PhpOffice\PhpWord\PhpWord();

$phpWord->setDefaultFontName("Times New Roman");
$phpWord->setDefaultFontSize("9");
$page_margin = array("marginTop"=>20, 'marginLeft'=>20, 'marginRight'=>20, 'marginBottom'=>20);


$section = $phpWord->addSection($page_margin);

$header = array('size' => 12, 'bold' => true, 'color'=>'FFFFFF');
$header_small = array('size' => 9, 'bold' => true, 'color'=>'000000');
$font_small_8 = array('size' => 8, 'bold' => false, 'color'=>'000000');
$font_small_7 = array('size' => 7, 'bold' => false, 'color'=>'000000');
$font_small_8_bold = array('size' => 8, 'bold' => true, 'color'=>'000000');

$rows = 1; $cols = 2;
$table_width = 91 * 91;
$styleCell = array('valign' => 'center',"alignment"=> \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
$fontStyle = array('bold' => true, 'size'=>14,"align"=>"center");
$simple_cell_style = array('bold' => false, 'size'=>9,"align"=>"left");
$simple_cell_style_bold = array('bold' => true, 'size'=>9,"align"=>"left");

$cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
 
$table = $section->addTable();
$table->addRow(1000, array('exactHeight' => true));
$table->addCell(200)->addImage('../images/rwanda.png', array('width'=>59, 'height'=>66));
$table->addCell(9800, $styleCell)->addText('Private Dispensary Monthly HMIS Report', $fontStyle);

$styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'color'=>'FFFFFF', 'bgColor' => '000000');
$styleLastRow = array('borderBottomSize' => 6, 'borderBottomColor' => '000000', 'color'=>'FFFFFF', 'bgColor' => '000000');

$phpWord->addTableStyle('ID Table', $styleTable, $styleFirstRow);
$phpWord->addTableStyle('Process Table', $styleTable);

$table = $section->addTable("ID Table");
$table->addRow(300, array('exactHeight' => true));


$cellColSpan = array('gridSpan' => 4, 'valign' => 'center');
$cellHCentered = array('alignment' => 'left');
$cellVCentered = array('valign' => 'center');

$cell1 = $table->addCell(10000, $cellColSpan);
$textrun2 = $cell1->addTextRun($cellHCentered);
$textrun2->addText(htmlspecialchars('I. Identification and Important communications', ENT_COMPAT, 'UTF-8'),$header);

//$table->addCell(10000)->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), $header);
$table->addRow(300,array('exactHeight'=>true));

/* 
$cellColSpan = array('gridSpan' => 4, 'valign' => 'center');
$cellHCentered = array('alignment' => 'left');
$cellVCentered = array('valign' => 'center');
 */
$cell1 = $table->addCell(10000, $cellColSpan);
$textrun2 = $cell1->addTextRun($cellHCentered);
$textrun2->addText(htmlspecialchars('A) Report Identification', ENT_COMPAT, 'UTF-8'));

$tbl_content = array(
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>5000,'content'=>'1. Facility Name'),
			array('size'=>2500,'content'=>$organisation),
			array('size'=>1500,'content'=>'5. Year'),
			array('size'=>1000,'content'=>$_POST['year'])
		)
	),
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>3000,'content'=>'2. Catchment Area Population'),
			array('size'=>2500,'content'=>''),
			array('size'=>1500,'content'=>'6. Month'),
			array('size'=>1000,'content'=>$_POST['month'])
		)
	),
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>3000,'content'=>'3. Province'),
			array('size'=>2500,'content'=>$_PROVINCE),
			array('size'=>1500,'content'=>'7. Sector'),
			array('size'=>1000,'content'=>$_SECTOR)
		)
	),
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>3000,'content'=>'4. District'),
			array('size'=>2500,'content'=>$_DISTRICT),
			array('size'=>1500,'content'=>'8. Cell'),
			array('size'=>1000,'content'=>$_CELL)
		)
	)
);

foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		$table->addCell($cell_prop['size'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'));
	}
}

$section->addTextBreak(1);

$table = $section->addTable("Process Table");
/* 
$table->addRow(300, array('exactHeight' => true));
 */
$cellColSpan = array('gridSpan' => 4, 'valign' => 'center');
$cellHCentered = array('alignment' => 'left');
$cellVCentered = array('valign' => 'center');
/* 
$cell1 = $table->addCell(10000, $cellColSpan);
$textrun2 = $cell1->addTextRun($cellHCentered);
$textrun2->addText(htmlspecialchars('I. Identification et Remarques importantes', ENT_COMPAT, 'UTF-8'),$header);
 */
//$table->addCell(10000)->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), $header);
$table->addRow(300,array('exactHeight'=>true));

/* 
$cellColSpan = array('gridSpan' => 4, 'valign' => 'center');
$cellHCentered = array('alignment' => 'left');
$cellVCentered = array('valign' => 'center');
 */
$cell1 = $table->addCell($table_width, $cellColSpan);
$textrun2 = $cell1->addTextRun($cellHCentered);
$textrun2->addText(htmlspecialchars('B) Report Approvals Processing', ENT_COMPAT, 'UTF-8'),$header_small);

$tbl_content = array(
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>($table_width * 0.2),'content'=>'1. Name of in-charge'),
			array('size'=>($table_width * 0.3),'content'=>$organisation_represantative ),
			array('size'=>($table_width * 0.2),'content'=>'5. Date received'),
			array('size'=>($table_width * 0.3),'content'=>'')
		)
	),
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>($table_width * 0.2),'content'=>'2. Qualification'),
			array('size'=>($table_width * 0.3),'content'=>$organisation_represantative_degree ),
			array('size'=>($table_width * 0.2),'content'=>'6. Name'),
			array('size'=>($table_width * 0.3),'content'=>'')
		)
	),
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>($table_width * 0.2),'content'=>'3. Date of sent'),
			array('size'=>($table_width * 0.3),'content'=>''),
			array('size'=>($table_width * 0.2),'content'=>'7. Signature'),
			array('size'=>($table_width * 0.3),'content'=>'')
		)
	),
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>($table_width * 0.3),'content'=>'4. Signature'),
			array('size'=>($table_width * 0.2),'content'=>''),
			array('size'=>($table_width * 0.3),'content'=>'8. Date entered in DHIS'),
			array('size'=>($table_width * 0.2),'content'=>'')
		)
	)
);

foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		$table->addCell($cell_prop['size'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'));
	}
}

$section->addTextBreak(1);

$table = $section->addTable("ID Table");
/* 
$table->addRow(300, array('exactHeight' => true));
 */
$cellColSpan = array('gridSpan' => 4, 'valign' => 'center');
$cellHCentered = array('alignment' => 'left');
$cellVCentered = array('valign' => 'center');
/* 
$cell1 = $table->addCell(10000, $cellColSpan);
$textrun2 = $cell1->addTextRun($cellHCentered);
$textrun2->addText(htmlspecialchars('I. Identification et Remarques importantes', ENT_COMPAT, 'UTF-8'),$header);
 */
//$table->addCell(10000)->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), $header);
$table->addRow(300,array('exactHeight'=>true));

$cellColSpan = array('gridSpan' => 10, 'valign' => 'center');
$cellColSpanInner = array('gridSpan' => 7, 'valign' => 'center');
$cellColSpanInner2 = array('gridSpan' => 2, 'valign' => 'center');
$cellColSpanInner3 = array('gridSpan' => 3, 'valign' => 'center');
$cellColSpanInner4 = array('gridSpan' => 4, 'valign' => 'center');
$cellColSpanInner5 = array('gridSpan' => 5, 'valign' => 'center');
$cellColSpanInner6 = array('gridSpan' => 6, 'valign' => 'center');
$cellColSpanInner7 = array('gridSpan' => 7, 'valign' => 'center');
$cellColSpanInner8 = array('gridSpan' => 8, 'valign' => 'center');
$cellColSpanInner9 = array('gridSpan' => 9, 'valign' => 'center');
$cellColSpanInner10 = array('gridSpan' => 10, 'valign' => 'center');
$cellColSpanInner11 = array('gridSpan' => 11, 'valign' => 'center');
$cellColSpanInner12 = array('gridSpan' => 12, 'valign' => 'center');
$cellColSpanInner13 = array('gridSpan' => 13, 'valign' => 'center');
$cellColSpanInner15 = array('gridSpan' => 15, 'valign' => 'center');
$cellColSpanInner18 = array('gridSpan' => 18, 'valign' => 'center');
$cellColSpanInner21 = array('gridSpan' => 21, 'valign' => 'center');
$nocellColSpanInner = array('gridSpan' => 1, 'valign' => 'center');

$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center', 'bgColor' => 'FFFFFF');
$cellRowSpanColSpanInner2 = array('vMerge' => 'restart', 'gridSpan'=>2, 'valign' => 'center', 'bgColor' => 'FFFFFF');
$cellRowSpanColSpanInner3 = array('vMerge' => 'restart', 'gridSpan'=>3, 'valign' => 'center', 'bgColor' => 'FFFFFF');
$cellRowContinue = array('vMerge' => 'continue');
$cellRowContinueColSpanInner2 = array('vMerge' => 'continue', 'gridSpan'=>2);
$cellRowContinueColSpanInner3 = array('vMerge' => 'continue', 'gridSpan'=>3);
/* 
$cellHCentered = array('alignment' => 'left');
$cellVCentered = array('valign' => 'center');
 */
$cell1 = $table->addCell($table_width, $cellColSpan);
$textrun2 = $cell1->addTextRun($cellHCentered);
$textrun2->addText(htmlspecialchars('II. Outpatient Consultations', ENT_COMPAT, 'UTF-8'), $header);


$withBorder = array('valign' => 'top', 'borderSize' => 6, 'borderColor' => '000000');
$withBorderBottomOnly = array('valign' => 'top', 'borderSize'=>0, 'borderBottomSize' => 6, 'borderColor'=>'FFFFFF','borderBottomColor' => '000000');
$noBorder = array('valign' => 'top', 'borderSize' => 0, 'borderColor' => 'FFFFFF');
$tbl_content = array(
	array(
		'size'=>300,
		'content'=>array(
			array(
				'size'=>($table_width * 0.5),
				'content'=>'A) Outpatient Morbidity summary table',
				'format'=>$withBorder,
				'colspan'=>$cellColSpanInner,
				'rowspan'=>null,
				'font'=>$header_small
			),
			array(
				'size'=>($table_width * 0.01),
				'content'=>'',
				'format'=>$noBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.4),
				'content'=>'B) Health insurance status of new cases',
				'format'=>$withBorder,
				'colspan'=>$nocellColSpanInner,
				'rowspan'=>null,
				'font'=>$header_small
			),
			array(
				'size'=>($table_width * 0.09),
				'content'=>'TOTAL',
				'format'=>$withBorder,
				'colspan'=>$nocellColSpanInner,
				'rowspan'=>null
			)
		)
	) ,
	array(
		'size'=>300,
		'content'=>array(
			array(
				'size'=>($table_width * 0.2),
				'content'=>'',
				'format'=>$withBorder,
				'colspan'=>$nocellColSpanInner,
				'rowspan'=>$cellRowSpan
			),
			array(
				'size'=>($table_width * 0.1),
				'content'=>'<5 yr',
				'format'=>$withBorder,
				'colspan'=>$cellColSpanInner2,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.1),
				'content'=>'5 - 19 yr',
				'format'=>$withBorder,
				'colspan'=>$cellColSpanInner2,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.1),
				'content'=>'≥20 yr',
				'format'=>$withBorder,
				'colspan'=>$cellColSpanInner2,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.01),
				'content'=>'',
				'format'=>$noBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.4),
				'content'=>'Insured (Mutuelle or other insurance members)',
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>$cellRowSpan
			),
			array(
				'size'=>($table_width * 0.09),
				'content'=>$insured_current_patient ,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>$cellRowSpan
			)
		)
	),
	array(
		'size'=>300,
		'content'=>array(
			array(
				'size'=>null,
				'rowspan'=>$cellRowContinue,
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>'M',
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>'F',
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>'M',
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>'F',
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>'M',
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>'F',
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.01),
				'content'=>'',
				'format'=>$noBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>null,
				'rowspan'=>$cellRowContinue,
			),
			array(
				'size'=>null,
				'rowspan'=>$cellRowContinue,
			)
		)
	) ,
	array(
		'size'=>300,
		'content'=>array(
			array(
				'size'=>($table_width * 0.2),
				'content'=>' New cases(NC)',
				'format'=>$withBorder,
				'colspan'=>$nocellColSpanInner,
				'rowspan'=>$cellRowSpan
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$maleLessThan5YearWithConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$femaleLessThan5YearWithConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$malebetween5and19WithConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$femalebetween5and19WithConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$maleGreateThan20WithConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$femaleGreateThan20WithConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.01),
				'content'=>'',
				'format'=>$noBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.4),
				'content'=>'Non-Paying New cases',
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.09),
				'content'=>$uninsured_current_patient,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			)
		)
	) ,
	array(
		'size'=>300,
		'content'=>array(
			array(
				'size'=>($table_width * 0.2),
				'content'=>'Old cases',
				'format'=>$withBorder,
				'colspan'=>$nocellColSpanInner,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$maleLessThan5YearWithoutConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$femaleLessThan5YearWithoutConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$malebetween5and19WithoutConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$femalebetween5and19WithoutConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$maleGreateThan20WithoutConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.05),
				'content'=>$femaleGreateThan20WithoutConsultation,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.01),
				'content'=>'',
				'format'=>$noBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.4),
				'content'=>'Number of indigent new cases ',
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			),
			array(
				'size'=>($table_width * 0.09),
				'content'=>$indigent_uninsured_current_patient,
				'format'=>$withBorder,
				'colspan'=>null,
				'rowspan'=>null
			)
		)
	) 
);

foreach($tbl_content as $row_prop){
	//var_dump($row_prop);
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'));
	}
}

$section->addTextBreak(1);

$table = $section->addTable("Process Table");
/* 
$table->addRow(300, array('exactHeight' => true));
 */
$cellColSpan = array('gridSpan' => 4, 'valign' => 'center');
$cellHCentered = array('alignment' => 'left');
$cellVCentered = array('valign' => 'center');

$tbl_content = array(
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>($table_width * 0.4),'content'=>'C) Referrals','font'=>$header_small),
			array('size'=>($table_width * 0.1),'content'=>'Total','font'=>null),
			array('size'=>($table_width * 0.01),'content'=>'','font'=>null,'format'=>$noBorder),
			array('size'=>($table_width * 0.4),'content'=>'D) Origin of outpatients','font'=>$header_small),
			array('size'=>($table_width * 0.09),'content'=>'Total','font'=>null)
		)
	), 
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>($table_width * 0.4),'content'=>'1. Referred to other levels','font'=>null),
			array('size'=>($table_width * 0.1),'content'=>$transferts,'font'=>null),
			array('size'=>($table_width * 0.01),'content'=>'','font'=>null,'format'=>$noBorder),
			array('size'=>($table_width * 0.4),'content'=>'1. New cases from the catchment area (zone)','font'=>null),
			array('size'=>($table_width * 0.09),'content'=>$patient_zone,'font'=>null)
		)
	), 
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>($table_width * 0.4),'content'=>'2. Counter referrals received','font'=>null),
			array('size'=>($table_width * 0.1),'content'=>'','font'=>null),
			array('size'=>($table_width * 0.01),'content'=>'','font'=>null,'format'=>$noBorder),
			array('size'=>($table_width * 0.4),'content'=>'2. New cases (hors zone)','font'=>null),
			array('size'=>($table_width * 0.09),'content'=>$patient_hors_zone,'font'=>null)
		)
	), 
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>($table_width * 0.4),'content'=>'3. Adults referrals by CHW','font'=>null),
			array('size'=>($table_width * 0.1),'content'=>'','font'=>null),
			array('size'=>($table_width * 0.01),'content'=>'','font'=>null,'format'=>$noBorder),
			array('size'=>($table_width * 0.4),'content'=>'3. International patients (hors pays)','font'=>null),
			array('size'=>($table_width * 0.09),'content'=>'','font'=>null)
		)
	), 
	array(
		'size'=>300,
		'content'=>array(
			array('size'=>($table_width * 0.4),'content'=>'4. Enfants de  < 5 ans Référés par CHW','font'=>null),
			array('size'=>($table_width * 0.1),'content'=>'','font'=>null),
			array('size'=>($table_width * 0.01),'content'=>'','font'=>null,'format'=>$noBorder),
			array('size'=>($table_width * 0.4),'content'=>'','font'=>null),
			array('size'=>($table_width * 0.09),'content'=>'','font'=>null)
		)
	)
);

foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		$table->addCell($cell_prop['size'],@$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'),$cell_prop['font']);
	}
}
//goto end_try;
$section->addTextBreak(1);
//select all diagnostic in new case of deases
$new_case = returnAllDataInTable($tbl="co_diagnostic",$con, $condition = "WHERE DiagnosticCategoryID='1' && DiagnosticCode = '' ORDER BY DiagnosticID ASC");
//echo "<pre>";
NCMNoPECIME($new_case,$con); //die();
//var_dump($new_case); die;
$tbl_content = array(
					array(
						'size'=>300,
						'content'=>array(
										array('size'=>($table_width * 0.7),'content'=>'E) New cases of priority health problems in General OPD','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner3, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'Under 5 years','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'5 to 19 years','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'≥ 20 years and above','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null)
										)
						)
					);

//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");
$row_size = 300;
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.05),'content'=>'#','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.55),'content'=>'Diagnosis','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'ICD-10','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
										)
						);
for($i=0;$i<count($new_case);$i++){
	$tbl_content[] = array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.05),'content'=>($i+1),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.55),'content'=>$new_case[$i]['DiagnosticName'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.1),'content'=>$new_case[$i]['Code'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['M5_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['F5_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['M5_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['F5_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['M20'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['F20'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
										)
							);
}

$table = $section->addTable("Process Table");
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'));
	}
}

$section->addTextBreak(1);
//select all diagnostic in new case of deases
$new_case = returnAllDataInTable($tbl="co_diagnostic",$con, $condition = "WHERE DiagnosticCategoryID='2' && DiagnosticCode = '' ORDER BY DiagnosticID ASC");
//var_dump($new_case);
$tbl_content = array(
					array(
						'size'=>300,
						'content'=>array(
										array('size'=>($table_width * 0.8),'content'=>'F) New cases of HIV/AIDS/STI/TB and Non-Communicable deseases','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner3, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'Under 5 years','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'5 to 19 years ','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'≥ 20 yrs and above','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null)
										)
						)
					);

//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");
$row_size = 300;
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.05),'content'=>'#','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.55),'content'=>'Diagnosis','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'ICD-10','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
										)
						);
for($i=0;$i<count($new_case);$i++){
	$tbl_content[] = array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.05),'content'=>($i+1),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.55),'content'=>$new_case[$i]['DiagnosticName'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.1),'content'=>$new_case[$i]['Code'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
										)
							);
}

$table = $section->addTable("Process Table");
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'));
	}
}

$cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);

$section->addTextBreak(1);
//select all diagnostic in new case of deases
$new_case = returnAllDataInTable($tbl="co_diagnostic",$con, $condition = "WHERE DiagnosticCategoryID='7' && DiagnosticCode = '' ORDER BY DiagnosticID ASC");
//var_dump($new_case);
MC($new_case,$con);

$table = $section->addTable("ID Table");
$table->addRow($row_size , array('exactHeight'=>true));
$cell1 = $table->addCell($table_width * 1, $cellColSpanInner21);
$textrun2 = $cell1->addTextRun(array("alignment"=>"left"));
$textrun2->addText(htmlspecialchars('III. Chronic Deseases', ENT_COMPAT, 'UTF-8'), $header);

//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");

$tbl_content = array(
						array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.03),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.27),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.1),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.25),'content'=>'New cases','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner6, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.25),'content'=>'Old cases','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner6, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.25),'content'=>'Deaths','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner6, 'rowspan'=>null,'align'=>$cellHCentered)
										)
						)
					);
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.27),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'AGE->','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'0 - 19','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'20 - 39','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'≥ 40','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'0 - 19','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'20 - 39','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'≥ 40','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'0 - 19','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'20 - 39','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'≥ 40','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered)
										)
						);
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>'','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.27),'content'=>'Medical condition','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'ICD-10','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered)
										)
						);
//
for($i=0;$i<count($new_case);$i++){
	$tbl_content[] = array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.03),'content'=>($i+1),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.27),'content'=>$new_case[$i]['DiagnosticName'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.1),'content'=>$new_case[$i]['Code'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NM0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NF0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NM20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NF20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NM40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NF40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['AM0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['AF0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['AM20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['AF20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['AM40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['AF40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DM0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DF0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DM20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DF20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DM40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DF40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered)
										)
							);
}
 
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),$cell_prop['font'],@$cell_prop['align']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
	}
}

/******************************************************************************************/
$section->addPageBreak();

//select all diagnostic in new case of deases
$new_case = returnAllDataInTable($tbl="co_diagnostic",$con, $condition = "WHERE DiagnosticCategoryID='9' ORDER BY DiagnosticID ASC");
//var_dump($new_case);
B($new_case, $con);

$table = $section->addTable("ID Table");
$table->addRow($row_size , array('exactHeight'=>true));
$cell1 = $table->addCell($table_width * 1, $cellColSpanInner15);
$textrun2 = $cell1->addTextRun(array("alignment"=>"left"));
$textrun2->addText(htmlspecialchars('IV. Injuries', ENT_COMPAT, 'UTF-8'), $header);

//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");

$tbl_content = array(
						array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.03),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.27),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.1),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.3),'content'=>'New cases','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner6, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.3),'content'=>'Deaths','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner6, 'rowspan'=>null,'align'=>$cellHCentered)
										)
						)
					);
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.27),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'AGE->','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'0 - 19','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'20 - 39','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'≥ 40','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'0 - 19','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'20 - 39','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'≥ 40','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered)
										)
						);
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>'','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.27),'content'=>'Cause of Injury','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.1),'content'=>'Code ICD-10','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered)
										)
						);
//
for($i=0;$i<count($new_case);$i++){
	$tbl_content[] = array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.03),'content'=>($i+1),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.27),'content'=>$new_case[$i]['DiagnosticName'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.1),'content'=>$new_case[$i]['Code'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NM0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NF0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NM20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NF20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NM40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['NF40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DM0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DF0_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DM20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DF20_39'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DM40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['DF40'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered)
										)
							);
}
 
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),$cell_prop['font'],@$cell_prop['align']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
	}
}



/******************************************************************************************/
//$section->addTextBreak(3);
$section->addTextBreak(1);

$new_case = array(
	"GBV victims with symptoms of sexual violence (new cases)",
	"GBV victims with symptoms of physical violence (new cases)",
	"GBV victims referred for care to higher level health facility",
	"GBV victims to this facility by police",
	"GBV victims referred to this facility by community health workers",
	"GBV victims HIV+ sero-conversion 3 months after exposure",
	"GBV victims with irreversible disabilities due to GBV",
	"GBV victims deaths",
	"GBV victims pregnant 4 weeks after exposure",
	"GBV victims received emergence contraception within 72 hours",
	"GBV victims received post exposure HIV prophylaxis within 48 hours"
); //returnAllDataInTable($tbl="co_diagnostic",$con, $condition = "WHERE DiagnosticCategoryID='7' ORDER BY DiagnosticID ASC");
//var_dump($new_case);

$table = $section->addTable("ID Table");
$table->addRow($row_size , array('exactHeight'=>true));
$cell1 = $table->addCell($table_width * 1, $cellColSpanInner12);
$textrun2 = $cell1->addTextRun(array("alignment"=>"left"));
$textrun2->addText(htmlspecialchars('V.  Gender Based Violence(GBV)', ENT_COMPAT, 'UTF-8'), $header);

//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");

$tbl_content = array(
						array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
											array('size'=>($table_width * 0.45),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
											array('size'=>($table_width * 0.1),'content'=>'< Under 5 years','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.1),'content'=>'5-9 years','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.1),'content'=>'10-18 years','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.1),'content'=>'19-24 years','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
											array('size'=>($table_width * 0.1),'content'=>'25 years and above','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered)
										)
						)
					);
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>null, 'rowspan'=>$cellRowContinue ),
										array('size'=>null, 'rowspan'=>$cellRowContinue ),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered)
										)
						);
//
for($i=0;$i<count($new_case);$i++){
	$tbl_content[] = array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.05),'content'=>($i+1),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.45),'content'=>$new_case[$i],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
											
										)
							);
}
 
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),$cell_prop['font'],@$cell_prop['align']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
	}
}

/**************************************************************************************/
$section->addPageBreak();
//select all diagnostic in new case of deases
$new_case = array(
	"BCG",
	"Polio-Zero (P0)",
	"Polio-1 (OPV1)",
	"Polio-2 (OPV2)",
	"Polio-3 (OPV3)",
	"DTP-HepB-Hib1",
	"DTP-HepB-Hib2",
	"DTP-HepB-Hib3",
	"Pneumococus 1",
	"Pneumococus 2",
	"Pneumococus 3",
	"Rotavirus 1",
	"Rotavirus 2",
	"Rotavirus 3",
	"Measles and Rubella (MR) Rougeole et Rubéole (RR)",
	"Insecticide impregnated bed nets distributed"
); //returnAllDataInTable($tbl="co_diagnostic",$con, $condition = "WHERE DiagnosticCategoryID='7' ORDER BY DiagnosticID ASC");
//var_dump($new_case);

$table = $section->addTable("ID Table");
$table->addRow($row_size , array('exactHeight'=>true));
$cell1 = $table->addCell($table_width * 1, $cellColSpanInner4);
$textrun2 = $cell1->addTextRun(array("alignment"=>"left"));
$textrun2->addText(htmlspecialchars('VI.	Vaccinations', ENT_COMPAT, 'UTF-8'), $header);
//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");

$tbl_content = array(
					array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>'','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.57),'content'=>'Vaccine Antigen/Item distributed','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.2),'content'=>'0 - 11 Month','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.2),'content'=>'≥ 1 year','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered)
									)
					)
				);
					
for($i=0;$i<count($new_case);$i++){
	$tbl_content[] = array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.03),'content'=>($i+1),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.57),'content'=>$new_case[$i],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
										)
							);
}

$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>'','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5")),
										array('size'=>($table_width * 0.57),'content'=>"Vaccines for other age groups :",'font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"),'align'=>$cellHCentered),
										array('size'=>($table_width * 0.2),'content'=>'12 years','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"),'align'=>$cellHCentered),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"),'align'=>$cellHCentered)
									)
				);
				
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>(++$i),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.57),'content'=>"HPV 1",'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"),'align'=>$cellHCentered)
									)
				);
				
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>(++$i),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.57),'content'=>"HPV 2",'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"),'align'=>$cellHCentered)
									)
				);
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>(++$i),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.57),'content'=>"HPV 3",'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"),'align'=>$cellHCentered)
									)
				);
				
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>'','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5")),
										array('size'=>($table_width * 0.57),'content'=>"",'font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"),'align'=>$cellHCentered),
										array('size'=>($table_width * 0.2),'content'=>'15 months','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"),'align'=>$cellHCentered),
										array('size'=>($table_width * 0.2),'content'=>' 16 months +','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"),'align'=>$cellHCentered)
									)
				);
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.03),'content'=>(++$i),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.57),'content'=>"Measles Vaccination",'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.2),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
									)
				);
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),$cell_prop['font'],@$cell_prop['align']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
	}
}

/**************************************************************************************/

$section->addTextBreak(1);
//select all diagnostic in new case of deases


$table = $section->addTable("ID Table");
$table->addRow($row_size , array('exactHeight'=>true));
$cell1 = $table->addCell($table_width * 1, $cellColSpanInner8);
$textrun2 = $cell1->addTextRun(array("alignment"=>"left"));
$textrun2->addText(htmlspecialchars('VII.	Family Planning', ENT_COMPAT, 'UTF-8'), $header);
//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");

$tbl_content = array(
					array(
						'size'=>($row_size),
						'content'=>array(
										array('size'=>($table_width * 0.03333),'content'=>'','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.34999),'content'=>'Methods','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.13333),'content'=>'New  Acceptors','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.13333),'content'=>'Stopped FP','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.13333),'content'=>'Active users at end of month','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.13333),'content'=>'Quantity distributed','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.13333),'content'=>'Stock at end of month','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.13333),'content'=>'Days of stockout','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null,'align'=>$cellHCentered)
									)
					)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'1','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Oral Contraceptives, progestative','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'2','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Oral Contraceptives, combined','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'3','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Injectables (Depo-Provera)','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'4','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Injectables (Norristerat)','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'5','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Implants','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'6','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'IUD','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'7','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Male condoms','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'8','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Female condoms','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size * 1.8),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'9','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Other barrier methods (gel, diaphragm)','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'10','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Cycle beads','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'11','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Auto-observation ','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5")),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5")),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"))
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size * 2),
					'content'=>array(
									array('size'=>($table_width * 0.03333),'content'=>'12','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.34999),'content'=>'Number of new women users referred by CHWs for modern family planning method','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5")),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5")),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5")),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5")),
									array('size'=>($table_width * 0.13333),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array("bgColor"=>"D5D5D5"))
								)
				);
				
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),$cell_prop['font'],@$cell_prop['align']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
	}
}

/**************************************************************************************/

$section->addPageBreak();
//select all diagnostic in new case of deases
$new_case = array(
); //returnAllDataInTable($tbl="co_diagnostic",$con, $condition = "WHERE DiagnosticCategoryID='7' ORDER BY DiagnosticID ASC");
//var_dump($new_case);

$table = $section->addTable("ID Table");
$table->addRow($row_size , array('exactHeight'=>true));
$cell1 = $table->addCell($table_width * 1, $cellColSpanInner6);
$textrun2 = $cell1->addTextRun(array("alignment"=>"left"));
$textrun2->addText(htmlspecialchars('XXI.	Laboratory', ENT_COMPAT, 'UTF-8'), $header);
//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");
//die;
$tbl_content = array(
					array(
						'size'=>($row_size),
						'content'=>array(
										array('size'=>($table_width * 0.55),'content'=>'','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner3, 'rowspan'=>null),
										array('size'=>($table_width * 0.3),'content'=>'Results','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null,'align'=>$cellHCentered),
										array('size'=>($table_width * 0.15),'content'=>'Total','font'=>$header_small,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan,'align'=>$cellHCentered),
									)
					)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.55),'content'=>'Exams','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner3, 'rowspan'=>null, 'align'=>$cellHCentered),
									array('size'=>($table_width * 0.15),'content'=>'Positive','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'Negative','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>null, 'rowspan'=>$cellRowContinue )
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.55),'content'=>'1. Blood Smears','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner3, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$ge_negative,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$ge_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.07),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.45),'content'=>'1.1 Plasmodium','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$ge_plasmodium,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'1.2 Micro-filaria','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$ge_microfilaire,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'1.3 Borellia','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$ge_borellia,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'1.4 Trypanosoma','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$ge_trypanosome,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
				
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.55),'content'=>'2. Rapid diagnostic Test for Malaria','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner3, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$tdr_positive,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$tdr_negative,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$tdr_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.55),'content'=>'3. Stools (number of samples analyzed)','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner3, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$selles_negative,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$selles_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.07),'content'=>'of which','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.45),'content'=>'3.1 Entamoeba histolytica','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$selles_entamoeba_hist,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'3.2 Giardia','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$selles_giardia,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'3.3 Ascariasis','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$selles_ascaris,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'3.4 Ankylostomiasis (hookworms)','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$selles_ankylostome,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'3.5 Schistosoma','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$selles_schistosome,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'3.6 Trichuris','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$selles_trichuris,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'3.7 Tænia','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$selles_tenia,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'3.8 Other parasites','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$selles_others,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5'))
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 1),'content'=>'4. Urine','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner6, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.07),'content'=>'of which','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.45),'content'=>'4.2 Sugar','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$glycosurie_positive,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$glycosurie_negative,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$glycosurie_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'4.3 Albumin','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$albumine_positive,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$albumine_negative,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$albumine_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'4.3 pregnancy test','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$grossesse_positive,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$grossesse_negative,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$grossesse_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 1),'content'=>'5. Sputum (Nombre de patients)','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner6, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.07),'content'=>'of which','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.45),'content'=>'5.2 Diagnosis of TB by microscopy','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$crachat_positive,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$crachat_negative,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$crachat_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'5.2 Control of TB positive patients','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
				
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 1),'content'=>'6. Blood','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner6, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.03),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.07),'content'=>'of which','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>$cellRowSpan),
									array('size'=>($table_width * 0.45),'content'=>'6.1. RPR ','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$rpr_positive,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$rpr_negative,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$rpr_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'6.2. HIV final result','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$hiv_positive,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$hiv_negative,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>$hiv_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'6.3. Hemoglobin','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$hb_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'6.4. ESR/VS','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$vs_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'6.5. Full Blood Count(FBC/NFS)','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$nfs_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'6.6. ALAT (GPT)','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$gpt_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'6.7. Creatinine','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$glycemie_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'6.8. Blood glucose (glycemie)','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$amylase_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'6.9. Amylase','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$amylase_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'6.10. CD4','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>$cd4_total,'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>null, 'rowspan'=>$cellRowContinue ),
									array('size'=>($table_width * 0.45),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
$tbl_content[] = array(
					'size'=>($row_size),
					'content'=>array(
									array('size'=>($table_width * 0.45),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner3, 'rowspan'=>null),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>array('bgColor'=>'D5D5D5')),
									array('size'=>($table_width * 0.15),'content'=>'','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
								)
				);
				
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),$cell_prop['font'],@$cell_prop['align']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font'],@$cell_prop['align']);
	}
}



/**************************************************************************************/

end_try:
// Save file
echo write($phpWord, basename(__FILE__, '.php'), $writers);
if (!CLI) {
    include_once 'Sample_Footer.php';
}
