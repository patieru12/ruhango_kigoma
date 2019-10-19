<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
set_time_limit(0);
//validate the POST array now
$_POST['diagnostic'] = !$_POST['diagnostic']?"all":$_POST['diagnostic'];
$_POST['exam'] = !$_POST['exam']?"all":$_POST['exam'];
$_POST['medicines'] = !$_POST['medicines']?"all":$_POST['medicines'];
$_POST['acts'] = !$_POST['acts']?"all":$_POST['acts'];
$_POST['consumable'] = !$_POST['consumable']?"all":$_POST['consumable'];
$_POST['transfer'] = !$_POST['transfer']?"all":$_POST['transfer'];
$tb_list = "";
$add_cn = "";
$add_cn_close = "";
$add_cn_close_after = "";
$diag_added = false;
/* if((@$_POST['conf_diag'] && $_POST['diagnostic'] != 'all') || (@$_POST['conf_exam'] && $_POST['exam'] != 'all') || (@$_POST['conf_medicines'] && $_POST['medicines'] != 'all') || (@$_POST['conf_acts'] && $_POST['acts'] != 'all') || (@$_POST['conf_consumable'] && $_POST['consumable'] != 'all') || (@$_POST['conf_transfer'] && $_POST['transfer'] != 'all') ){
	$add_cn_close .= " && (";
	$add_cn_close_after .= ") ";
} */
if(@$_POST['conf_diag'] && $_POST['diagnostic'] != 'all' ){
	$diag_added = true;
	$tb_list .= ", co_records, co_diagnostic_records";
	$add_cn .= "&& pa_records.PatientRecordID = co_records.PatientRecordID 
				&& co_records.ConsultationRecordID = co_diagnostic_records.ConsulationRecordID";
	
	//have an array of ids
	$diag_ids = explode(";",preg_replace("/;$/","",$_POST['diagnostic']));
	$ssstr = "";
	arraytostring($diag_ids, $ssstr, $position = 0, $sep = "' || co_diagnostic_records.DiagnosticID ='");
	//echo $ssstr;
	$add_cn_close .= " && (co_diagnostic_records.DiagnosticID = '".$ssstr."') ";
}

if(@$_POST['conf_exam'] && $_POST['exam'] != 'all' ){
	if(!$diag_added){
		$tb_list .= ", co_records";
		$diag_added = true;
	} else{
		$add_cn_close .= " ";
	}
	
	$tb_list .= ", la_records, la_price";
	
	$add_cn .= "&& pa_records.PatientRecordID = co_records.PatientRecordID
				&& co_records.ConsultationRecordID = la_records.ConsultationRecordID 
				&& la_records.ExamPriceID = la_price.ExamPriceID";
	//have an array of ids
	$exam_ids = explode(";",preg_replace("/;$/","",$_POST['exam']));
	$ssstr = "";
	//var_dump($exam_ids);
	arraytostring($exam_ids, $ssstr, $position = 0, $sep = "' || la_price.ExamID='");
	//echo $ssstr;
	$add_cn_close .= " && (la_price.ExamID='".$ssstr."') ";
}
$md_added_before = false;
$add_cn_close_md = "";
if(@$_POST['conf_medicines'] && $_POST['medicines'] != 'all' ){
	if(!$diag_added){
		$tb_list .= ", co_records";
		$add_cn .= "&& pa_records.PatientRecordID = co_records.PatientRecordID ";
		$diag_added = true;
	} else{
		$add_cn_close .= " ";
	}
	
	$tb_list .= ", md_records, md_price, md_name";
	$md_added_before = true;
	$add_cn .= "&& co_records.ConsultationRecordID = md_records.ConsultationRecordID 
				&& md_records.MedecinePriceID = md_price.MedecinePriceID
				&& md_price.MedecineNameID = md_name.MedecineNameID ";
	//have an array of ids
	$medicines_ids = explode(";",preg_replace("/;$/","",$_POST['medicines']));
	$ssstr = "";
	arraytostring($medicines_ids, $ssstr, $position = 0, $sep = "' || md_name.MedecineName='");
	
	$add_cn_close .= " && (md_name.MedecineName='".$ssstr."') ";
}

if(@$_POST['conf_acts'] && $_POST['acts'] != 'all' ){
	if($diag_added){
		$add_cn_close .= " ";
	} else{
		$diag_added = true;
	}
	
	$tb_list .= ", ac_records, ac_price, ac_name";
	
	$add_cn .= "&& ac_records.PatientRecordID = pa_records.PatientRecordID 
				&& ac_records.ActPriceID = ac_price.ActPriceID
				&& ac_price.ActNameID = ac_name.ActNameID";
	//have an array of ids
	$acts_ids = explode(";",preg_replace("/;$/","",$_POST['acts']));
	$ssstr = "";
	arraytostring($acts_ids, $ssstr, $position = 0, $sep = "' || ac_name.Name='");
	
	$add_cn_close .= " && (ac_name.Name='".$ssstr."') ";
}

if(@$_POST['conf_consumable'] && $_POST['consumable'] != 'all' ){
	if($diag_added){
		$add_cn_close .= " ";
	} else{
		$diag_added = true;
	}
	
	$tb_list .= ", cn_records, cn_name, cn_price";
	/* if(!$md_added_before)
		$tb_list .= ", md_price, md_name"; */
	$add_cn .= "&& cn_records.PatientRecordID = pa_records.PatientRecordID 
				&& cn_records.MedecinePriceID = cn_price.MedecinePriceID
				&& cn_price.MedecineNameID = cn_name.MedecineNameID";
	//echo $_POST['consumable'];
	//have an array of ids
	//var_dump($_POST['consumable']);
	$consumable_ids = explode(";",preg_replace("/;$/","",$_POST['consumable']));
	$ssstr = "";
	//var_dump($consumable_ids);
	arraytostring($consumable_ids, $ssstr, $position = 0, $sep = "' || cn_name.MedecineName='");
	//echo $ssstr;
	//echo $add_cn_close;echo "<br />";
	//$add_cn_close .= " && (md_name.MedecineName='".$ssstr."') ";
	$add_cn_close .= " && (cn_name.MedecineName='".$ssstr."')";
	//echo $add_cn_close;
}
//$add_cn_close .= $add_cn_close_md.($add_cn_close_md != ""?")":"");
if(@$_POST['conf_transfer'] && $_POST['transfer'] != "all" ){
	if($diag_added){
		$add_cn_close .= "";
	}
	//have an array of ids
	$transfer_ids = explode(";",preg_replace("/;$/","",$_POST['transfer']));
	$ssstr = "";
	arraytostring($transfer_ids, $ssstr, $position = 0, $sep = "' || pa_records.Status ='");
	$add_cn_close .= " && (pa_records.Status ='{$ssstr}')";
}
//die;
$add_cn .= $add_cn_close.$add_cn_close_after;
//get the list of active patient now
//var_dump($tb_list);
//echo $tb_list."<br />"; echo $add_cn; die;
//var_dump($add_cn);
$patient_records = returnAllData($sql = "SELECT DISTINCT
pa_records.DateIn, pa_records.Status, pa_records.InsuranceCardID, pa_records.PatientRecordID
, pa_info.Name
, in_name.InsuranceName
, se_name.ServiceCode 
FROM 
pa_records, pa_info, in_name, se_name, se_records {$tb_list}
WHERE 
pa_records.DateIn >= '".PDB($_POST['start_date'],true,$con)."' && pa_records.DateIn <= '".PDB($_POST['end_date'],true,$con)."' 
&& pa_records.PatientID = pa_info.PatientID 
&& pa_records.InsuranceNameID = in_name.InsuranceNameID 
&& pa_records.PatientRecordID = se_records.PatientRecordID
&& se_records.ServiceNameID = se_name.ServiceNameID {$add_cn}
ORDER BY DateIn ASC",$con);
//var_dump($patient_records);
//echo $sql;// die;
if(!$patient_records){
	echo "<span class=error-text>No Match Found</span>";
	return;
}
$header = "";
$array_data = array();
$max_diag = 0;
$max_exam = 0;
$max_md = 0;
$max_ac = 0;
$max_cn = 0;
$max_tr = 0;
for($i = 0; $i<count($patient_records); $i++){
	$cons = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$patient_records[$i]['PatientRecordID']}'",$con);
	//var_dump($cons);
	if(@$_POST['conf_diag']){
		//select the one all diagnostic records related to the current patient
		$diagnostic_records = returnAllData("SELECT co_diagnostic.DiagnosticName FROM co_diagnostic_records, co_diagnostic WHERE co_diagnostic_records.ConsulationRecordID='{$cons[0]['ConsultationRecordID']}' && co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID",$con);
		//$header .= "<th colspan=2>Diagnostic</th>";
		$patient_records[$i]['diagnostic'] = $diagnostic_records;
		if($max_diag < count($diagnostic_records))
			$max_diag = count($diagnostic_records);
	}

	if(@$_POST['conf_exam']){
		//$header .= "<th colspan=2>Exam</th>";
		$exam_records = returnAllData("SELECT la_exam.ExamName , la_records.ExamRecordID FROM la_records, la_price, la_exam WHERE la_records.ConsultationRecordID='{$cons[0]['ConsultationRecordID']}' && la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID",$con);
		for($ex=0;$ex<count($exam_records);$ex++){
			//select registered result here
			$results_records = returnAllData($str_ = "SELECT la_result.ResultName FROM la_result_record, la_result WHERE la_result_record.ResultID = la_result.ResultID && la_result_record.ExamRecordID = '{$exam_records[$ex]['ExamRecordID']}'",$con);
			//echo $str_;
			$rslt = "";
			arraytostring($results_records,$rslt,0,";");
			$exam_records[$ex]['Result'] = $rslt;
		}
		$patient_records[$i]['exams'] = $exam_records;
		if($max_exam < count($exam_records))
			$max_exam = count($exam_records);
	}
	if(@$_POST['conf_medicines']){
		$md_records = returnAllData("SELECT md_name.MedecineName, md_records.Quantity FROM md_records, md_price, md_name WHERE md_records.ConsultationRecordID='{$cons[0]['ConsultationRecordID']}' && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID ORDER BY md_name.MedecineName ASC",$con);
		$patient_records[$i]['medicines'] = $md_records;
		if($max_md < count($md_records))
			$max_md = count($md_records);
	}
	if(@$_POST['conf_acts']){
		$ac_records = returnAllData("SELECT ac_name.Name, ac_records.Quantity FROM ac_records, ac_price, ac_name WHERE ac_records.PatientRecordID='{$patient_records[$i]['PatientRecordID']}' && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID ORDER BY ac_name.Name ASC",$con);
		$patient_records[$i]['acts'] = $ac_records;
		if($max_ac < count($ac_records))
			$max_ac = count($ac_records);
	}
	if(@$_POST['conf_consumable']){
		$cn_records = returnAllData("SELECT cn_name.MedecineName, cn_records.Quantity FROM cn_records,cn_price, cn_name WHERE cn_records.PatientRecordID='{$patient_records[$i]['PatientRecordID']}' && cn_records.MedecinePriceID = cn_price.MedecinePriceID && cn_price.MedecineNameID = cn_name.MedecineNameID ORDER BY cn_name.MedecineName ASC",$con);
		$patient_records[$i]['consumables'] = $cn_records;
		if($max_cn < count($cn_records))
			$max_cn = count($cn_records);
	}
	if(@$_POST['conf_transfer']){
		$header .= "<th>Transfer</th>";
		$max_tr = 1;
	}
}
	$file_name = "./prest/cpc_data.xlsx";
	//echo $file_name;
	//include phpExcel library in the file
	require_once "../lib2/PHPExcel/IOFactory.php";
	require_once "../lib2/PHPExcel.php";
	//instantiate the PHPExcel object
	$objPHPExcel = new PHPExcel;
	
	//instantiate the writer object
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel, "Excel2007");
	
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	
	$objPHPExcel->setActiveSheetIndex(0);
	
	$activeSheet = $objPHPExcel->getActiveSheet();
	
	$row = 1;
	$first_column = 'A';
	$fheader = array("ID","Date","Patient","Insurance","Code","Service");
	foreach($fheader as $hv){
		$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
		$activeSheet->setCellValue(($first_column++).$row, $hv);
	}
?>
<style>
	table.list td, table.list th{
		font-size:10px;
	}
</style>
<div style='height:90%; overflow:auto;'>
<table class=list>
	<tr>
		<th>#</th><th>Date</th><th>Patient</th><th>Insurance</th>
		<th>Code</th><th>Service</th>
		<?php
		for($header_counter=0; $header_counter<$max_diag; $header_counter++){
			echo "<th>Diag".($header_counter+1)."</th>";
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Diag".($header_counter+1));
		}
		for($header_counter=0; $header_counter<$max_exam; $header_counter++){
			echo "<th>Exam".($header_counter+1)."</th>";
			echo "<th>Result".($header_counter+1)."</th>";
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Exam".($header_counter+1));
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Result".($header_counter+1));
		}
		for($header_counter=0; $header_counter<$max_md; $header_counter++){
			echo "<th>Medicine".($header_counter+1)."</th>";
			echo "<th>QTY".($header_counter+1)."</th>";
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Medicine".($header_counter+1));
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Quantity".($header_counter+1));
		}
		for($header_counter=0; $header_counter<$max_ac; $header_counter++){
			echo "<th>Act".($header_counter+1)."</th>";
			echo "<th>QTY".($header_counter+1)."</th>";
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Act".($header_counter+1));
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Quantity".($header_counter+1));
		}
		for($header_counter=0; $header_counter<$max_cn; $header_counter++){
			echo "<th>Consumable".($header_counter+1)."</th>";
			echo "<th>QTY".($header_counter+1)."</th>";
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Consumable".($header_counter+1));
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Quantity".($header_counter+1));
		}
		for($header_counter=0; $header_counter<$max_tr; $header_counter++){
			echo "<th>Transfer</th>";
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).$row, "Transfer");
		}
		//echo $header;
		?>
	</tr>
	<?php
		$row++;
		//var_dump($patient_records); die;
		for($i = 0; $i<count($patient_records); $i++){
			$first_column = "A";
			echo "<tr>";
			echo "<td>".($i + 1)."</td>";
			$activeSheet->setCellValue(($first_column++).$row, ($i + 1));
			echo "<td>{$patient_records[$i]['DateIn']}</td>";
			$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['DateIn']);
			echo "<td>{$patient_records[$i]['Name']}</td>";
			$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['Name']);
			echo "<td>{$patient_records[$i]['InsuranceName']}</td>";
			$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['InsuranceName']);
			echo "<td>{$patient_records[$i]['InsuranceCardID']}</td>";
			$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['InsuranceCardID']);
			echo "<td>{$patient_records[$i]['ServiceCode']}</td>";
			$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['ServiceCode']);
			for($diag_counter=0; $diag_counter<$max_diag; $diag_counter++){
				if(@$patient_records[$i]['diagnostic'][$diag_counter]){
					echo "<td>{$patient_records[$i]['diagnostic'][$diag_counter]['DiagnosticName']}</td>";
					$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['diagnostic'][$diag_counter]['DiagnosticName'] );
				} else{
					echo "<td></td>";
					$activeSheet->setCellValue(($first_column++).$row, "");
				}
			}
			for($diag_counter=0; $diag_counter<$max_exam; $diag_counter++){
				if(@$patient_records[$i]['exams'][$diag_counter]){
					echo "<td>{$patient_records[$i]['exams'][$diag_counter]['ExamName']}</td>";
					$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['exams'][$diag_counter]['ExamName']);
					echo "<td>{$patient_records[$i]['exams'][$diag_counter]['Result']}</td>";
					$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['exams'][$diag_counter]['Result']);
				} else{
					echo "<td></td><td></td>";
					$activeSheet->setCellValue(($first_column++).$row, "");
					$activeSheet->setCellValue(($first_column++).$row, "");
				}
			}
			for($diag_counter=0; $diag_counter<$max_md; $diag_counter++){
				if(@$patient_records[$i]['medicines'][$diag_counter]){
					echo "<td>{$patient_records[$i]['medicines'][$diag_counter]['MedecineName']}</td>";
					$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['medicines'][$diag_counter]['MedecineName']);
					echo "<td>{$patient_records[$i]['medicines'][$diag_counter]['Quantity']}</td>";
					$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['medicines'][$diag_counter]['Quantity']);
				} else{
					echo "<td></td><td></td>";
					$activeSheet->setCellValue(($first_column++).$row, "");
					$activeSheet->setCellValue(($first_column++).$row, "");
				}
			}
			for($diag_counter=0; $diag_counter<$max_ac; $diag_counter++){
				if(@$patient_records[$i]['acts'][$diag_counter]){
					echo "<td>{$patient_records[$i]['acts'][$diag_counter]['Name']}</td>";
					$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['acts'][$diag_counter]['Name']);
					echo "<td>{$patient_records[$i]['acts'][$diag_counter]['Quantity']}</td>";
					$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['acts'][$diag_counter]['Quantity']);
				} else{
					echo "<td></td><td></td>";
					$activeSheet->setCellValue(($first_column++).$row, "");
					$activeSheet->setCellValue(($first_column++).$row, "");
				}
			}
			for($diag_counter=0; $diag_counter<$max_cn; $diag_counter++){
				if(@$patient_records[$i]['consumables'][$diag_counter]){
					echo "<td>{$patient_records[$i]['consumables'][$diag_counter]['MedecineName']}</td>";
					$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['consumables'][$diag_counter]['MedecineName']);
					echo "<td>{$patient_records[$i]['consumables'][$diag_counter]['Quantity']}</td>";
					$activeSheet->setCellValue(($first_column++).$row, $patient_records[$i]['consumables'][$diag_counter]['Quantity']);
				} else{
					echo "<td></td><td></td>";
					$activeSheet->setCellValue(($first_column++).$row, "");
					$activeSheet->setCellValue(($first_column++).$row, "");
				}
			}
			for($diag_counter=0; $diag_counter<$max_tr; $diag_counter++){
				if(@$patient_records[$i]['Status'] > 1){
					echo "<td>Yes</td>";
					$activeSheet->setCellValue(($first_column).$row, "Yes");
				} else{
					echo "<td>".($patient_records[$i]['Status'] == 0?"":"No")."</td>";
					$activeSheet->setCellValue(($first_column).$row, ($patient_records[$i]['Status'] == 0?"":"No"));
				}
			}
			echo "</tr>";
			$row++;
		}
		
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF000000'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle("A1:".$first_column.(--$row))->applyFromArray($styleArray);
		
		$activeSheet->setTitle("Prescription Summary");

		// Create a new worksheet, after the default sheet
		$objPHPExcel->createSheet();
		//save the file
		$objWriter->save($file_name);
		
	?>
</table>
</div>
<a style='color:blue' title='Download in Excel Format' href='<?= $file_name ?>'>download</a>