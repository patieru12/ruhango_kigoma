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
$report_title = "Malaria Summary";
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
for($start_time; preg_match("/^{$_GET['year']}-{$_GET['month']}$/",date("Y-m",$start_time)); $start_time += $step){
	$str .= "<th>".date("d",$start_time)."</th>";
	//write this at the excel worksheet
	$activeSheet->getColumnDimension($start_column)->setAutoSize(true);
	$activeSheet->setCellValue(($start_column++) . $start_row, " ".date("d",$start_time));
	
	$valid_dates[date("Y-m-d",$start_time)] = 0;
	$patients[date("Y-m-d",$start_time)] = 0;
	$total_patients[date("Y-m-d",$start_time)] = 0;
	$tms[date("Y-m-d",$start_time)] = 0;
	$total_tms[date("Y-m-d",$start_time)] = 0;
}
$end_date =  date("Y-m-d",$start_time);
///echo $end_date;
$str .= "<th>Total</th></tr>";
$activeSheet->getColumnDimension($start_column)->setAutoSize(true);
$activeSheet->setCellValue(($start_column++) . ($start_row++), "Total");
	//var_dump($tms);
//select all valid medicines now
$sql = "SELECT DISTINCT `coartem 6x1`, `coartem 6x2`, `coartem 6x3`, `coartem 6x4`, `quinine`, `artesunate`, `PharmacyData` as `Total Prescription`, `Ge Pos`, `Ge Neg & TDR Pos`, `LaboData` as `Total Labo` FROM la_malaria WHERE Date LIKE('{$_date_}%')";
//echo $sql; echo "<br /><br />";
$medecines = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con)[0];
if($medecines == null){
	echo "<span class=error-text>No Prescription is Registered<br />Please Regenerate using the Prescription Summary Report<br />Under Reports and choose <br />Health Centre Report</span>";
	unset($activeSheet);
	unset($objWriter);
	unset($objPHPExcel);
	
	return;
}
$total_p = array(array());

foreach($medecines as $fld=>$value){
	//$str .= "<tr><td>{$fld}</td>";
	$sub_total = 0;
	$start_column = "A";
	$activeSheet->getColumnDimension($start_column)->setAutoSize(true);
	$activeSheet->setCellValue(($start_column++) . ($start_row), $fld);
	
	if(!preg_match("/otal/",strtolower($fld))){
		$str .= "<tr><td>{$fld}</td>";
		//now loop all usable date and select the saved result
		foreach($valid_dates as $dates=>$v){
			//select the value
			$data = returnSingleField("SELECT `{$fld}` FROM la_malaria WHERE Date='{$dates}'",$fld,true,$con);
			$data = $data?$data:"";
			$str .= "<td align=right>{$data}</td>";
			$activeSheet->setCellValue(($start_column++) . ($start_row), $data);
			
			$rename = (preg_match("/^g/",strtolower($fld))?"Total Labo":"Total Prescription");
			if(isset($total_p[$rename][$dates]))
				$total_p[$rename][$dates] += $data;
			else
				$total_p[$rename][$dates] = $data;
			
			$sub_total += $data;
		}
	} else{
		$str .= "<tr><td>{$fld}</td>";
		foreach($valid_dates as $dates=>$v){
			//select the value
			$total_p[$fld][$dates] = $total_p[$fld][$dates]?$total_p[$fld][$dates]:"";
			if(isset($total_p[$fld][$dates]))
				$str .= "<th align=right>{$total_p[$fld][$dates]}</th>";
			else
				$str .= "<th></th>";
			$activeSheet->setCellValue(($start_column++) . ($start_row), $total_p[$fld][$dates]);
			$sub_total += $total_p[$fld][$dates];
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
$filename = "./prest/malaria_summary.pdf";
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
$filename = "./prest/malaria_summary.xlsx";
$objWriter->save($filename);
echo "<a style='color:blue' href='{$filename}' target='_blank'><img src='../images/excel.png'> Get in EXCEL</a>";
?>