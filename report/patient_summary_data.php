<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//echo (60*60*24);
$start_time = mktime(0,0,0,$_GET['month'],1,$_GET['year']);
$step = (60*60*24);
if($_GET['month'] < 10)
	$_GET['month'] = "0".$_GET['month'];
//echo date("Y-m-d",($start_time + $step));
$report_title = "Patients Summary";
$month_ = $_GET['month']."/".$_GET['year'];
$_date_ = $_GET['year']."-".$_GET['month'];
$post = "";
$posts = explode("_", $_GET['post']);
//var_dump($posts);
$count = count($post);
$current = 1;
$sys = "("; $sys_s = 0;
foreach($posts as $pst){
	$ps = returnSingleField($sql="SELECT CenterName FROM sy_center WHERE CenterID='{$pst}'",$field="CenterName",$data=true, $con);
	//var_dump($ps);
	if($ps != null){
		if($post && $current++ == $count)
			$post .= " And ";
		else
			$post .= " ";
		$post .= $ps;
		if($sys_s++ > 0)
			$sys .= " || ";
		$sys .= "sy_center.CenterName = '{$ps}'";
	}
}
$sys .= ")";

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
	

$str = "
<style>
	#new{
		border-collapse:collapse;
	}
	table td, table th{
		border:0px solid #000;
	}
	.title{
		font-size:20px;
	}
	 #new th{ font-weight:bold;}
	#new td{ width:50px; }
	td.number_right{ text-align:right; }
</style>
<span class=title>
	<span class=success style='font-weight:bold; font-size:20px;'>
{$report_title}<br />
Post: {$post}<br />
Month: {$month_}
</span>
</span>

<div style='max-height:350px; border:0px solid #000; overflow:auto;'>
<table id=new class=list border=1><tr><th style='width:200px;'>&nbsp;</th>";
$activeSheet->setCellValue("A2", "Title: ".$report_title);
$activeSheet->setCellValue("A3", "Post: {$post}");
$activeSheet->setCellValue("A4", "Month: {$month_}");

$start_row = 5;

$valid_dates = array();
$patients = array();
$total_patients = array();
$tms = array();
$total_tms = array();
$start_date = date("Y-m-d",$start_time);
$start_column = 'B';
$sub_header = "<tr><th></th>";
$services = array("CPC","CPN","PST","MAT");
for($start_time; preg_match("/^{$_GET['year']}-{$_GET['month']}$/",date("Y-m",$start_time)); $start_time += $step){
	$str .= "<th colspan=4>".date("d",$start_time)."</th>";
	//write this at the excel worksheet
	$activeSheet->setCellValue(($start_column) . $start_row, " ".date("d",$start_time));
	$span_label = $start_column;
	$activeSheet->getColumnDimension($start_column)->setAutoSize(true);
	$activeSheet->setCellValue(($start_column++) . ($start_row + 1), "CPC");
	
	$activeSheet->getColumnDimension($start_column)->setAutoSize(true);
	$activeSheet->setCellValue(($start_column++) . ($start_row + 1), "CPN");
	
	$activeSheet->getColumnDimension($start_column)->setAutoSize(true);
	$activeSheet->setCellValue(($start_column++) . ($start_row + 1), "PST");
	
	$activeSheet->getColumnDimension($start_column)->setAutoSize(true);
	SpanCells($activeSheet, $span_label.($start_row).":".$start_column.($start_row),$align='center');
	$activeSheet->setCellValue(($start_column++) . ($start_row + 1), "MAT");
	
	$sub_header .= "<th>CPC</th><th>CPN</th><th>PST</th><th>MAT</th>";
	$valid_dates[date("Y-m-d",$start_time)] = 0;
	$patients[date("Y-m-d",$start_time)] = 0;
	$total_patients[date("Y-m-d",$start_time)] = 0;
	$tms[date("Y-m-d",$start_time)] = 0;
	$total_tms[date("Y-m-d",$start_time)] = 0;
}
$end_date =  date("Y-m-d",$start_time);
///echo $end_date;
$start_row++;
$str .= "<th>Total</th></tr>".$sub_header."</tr>";
$activeSheet->getColumnDimension($start_column)->setAutoSize(true);
$activeSheet->setCellValue(($start_column++) . ($start_row++), "Total");
	//var_dump($tms);
//select all valid medicines now
$sql = "SELECT DISTINCT `InsuranceName` FROM pa_summary WHERE Date LIKE('{$_date_}%')";
//echo $sql; echo "<br /><br />";
$medecines = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
if($medecines == null){
	echo "<span class=error-text>No Prescription is Registered<br />Please Regenerate using the Daily Reception Summary Report<br />Under Reports and choose <br />Health Centre Report</span>";
	unset($activeSheet);
	unset($objWriter);
	unset($objPHPExcel);
	
	return;
}
$total_p = array(array());
foreach($medecines as $flds){
	$fld = $flds['InsuranceName'];
	//$str .= "<tr><td>{$fld}</td>";
	$sub_total = 0;
	$start_column = "A";
	$activeSheet->getColumnDimension($start_column)->setAutoSize(true);
	$activeSheet->setCellValue(($start_column++) . ($start_row), $fld);
	
	$str .= "<tr><td>{$fld}</td>";
	//now loop all usable date and select the saved result
	foreach($valid_dates as $dates=>$v){
		//select the value
		foreach($services as $srv){
			$data = returnSingleField("SELECT `{$srv}` FROM pa_summary WHERE Date='{$dates}' && InsuranceName='{$fld}'",$srv,true,$con);
			$data = $data?$data:"";
			$str .= "<td>{$data}</td>";
			$activeSheet->setCellValue(($start_column++) . ($start_row), $data);
			$sub_total += $data;
		}
		
	}
	
	$str .= "<th align=right>{$sub_total}</th>";
	//echo $start_column;
	$activeSheet->setCellValue(($start_column) . ($start_row++), $sub_total);
	$str .= "</tr>";
	//increment the row availability
	//$start_row++;
}
//var_dump($total_p);
$str .= "</table>
</div>";
echo "
	<style>
		#new td, #new th{ font-size:10px;}
	<style>";
echo $str;
//generate a pdf file now
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage("L");

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($str);
$filename = "./prest/patient_summary.pdf";
//echo $filename;
$pdf->Output($filename); 
echo "<a style='color:blue' href='{$filename}' target='_blank'><img src='../images/pdf.png'> Get in PDF</a> &nbsp;";
//write the Excel File now

$styleArray = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('argb' => 'FF000000'),
		),
	),
);
//echo $start_column++;
$objPHPExcel->getActiveSheet()->getStyle("A5:".($start_column).(--$start_row))->applyFromArray($styleArray);
$activeSheet->setTitle("Prescription Summary");

// Create a new worksheet, after the default sheet
$objPHPExcel->createSheet();
//save the file
$filename = "./prest/patient_summary.xlsx";
$objWriter->save($filename);
echo "<a style='color:blue' href='{$filename}' target='_blank'><img src='../images/excel.png'> Get in EXCEL</a>";
?>