<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$reference_date = "2016-11-17";
//echo (60*60*24);
$start_time = mktime(0,0,0,$_GET['month'],1,$_GET['year']);
$step = (60*60*24);
$_GET['medicine'] = "coartem";
$_GET['day'] = !@$_GET['day']?date("d",time()):$_GET['day'];
if($_GET['month'] < 10)
	$_GET['month'] = "0".$_GET['month'];
//echo date("Y-m-d",($start_time + $step));
$report_title = "Anti-Malaria unpaid Distribution";
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
//$date = $_GET['year']."-".$_GET['month']."-".$_GET['day'];
$date = $_GET['year']."-".$_GET['month'];
$str = "
<style>
	table td, table th{
		border:1px solid #000;
	}
	.title{
		font-size:20px;
	}
	#new{ font-size:13px;}
	.link_active{
		color:#000;
		background-color:#fff;
	}
</style>
<span class=title>
	<span class=success style='font-weight:bold; font-size:20px;'>
{$report_title}<br />
Post: {$post}<br />
Month: {$date}
</span>
</span><br />
";
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

//$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
$activeSheet->setCellValue(($first_column).($row++), $report_title);
$activeSheet->setCellValue(($first_column).($row++), "Post: ".$post);
$activeSheet->setCellValue(($first_column).($row++), "Date: ".$date);

$valid_dates = array();
$tb_data = "";
$coartems = formatResultSet($rslt=returnResultSet($sql = "SELECT DISTINCT MedecineName FROM md_name WHERE md_name.MedecineName LIKE('%{$_GET['medicine']}%') || md_name.MedecineName LIKE('%quinine%') || md_name.MedecineName LIKE('%tesunate%')",$con),$multirows=true,$con);
//echo $sql;
/* for($start_time; preg_match("/^{$_GET['year']}-{$_GET['month']}$/",date("Y-m",$start_time)); $start_time += $step){
	$str .= "<label onclick='$(\".patient_found\").load(\"coartem_unpaid_data.php?year=\" + $(\"#year\").val() + \"&month=\" + $(\"#month\").val() + \"&post=\" + $(\"#post\").val() + \"&day=".date("d",$start_time)."\");' class='flatbtn-blu".($_GET['day'] == date("d",$start_time)?" link_active":"")."' style='padding:0 10px;'>".date("d",$start_time)."</label>";
	
	$valid_dates[] = date("Y-m-d",$start_time);
} */
$total = array("Name"=>"Total");
$row = 5; $first_column = 'A';
$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
$activeSheet->setCellValue(($first_column++).($row), "ID");

$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
$activeSheet->setCellValue(($first_column++).($row), "Patient");

$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
$activeSheet->setCellValue(($first_column++).($row), "Code");

$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
$activeSheet->setCellValue(($first_column++).($row), "Category");

for($k = 0; $k<count($coartems); $k++){
	$tb_data .= "<th>".(preg_match("/oartem/",$coartems[$k]['MedecineName'])?substr($coartems[$k]['MedecineName'],strpos(strtolower($coartems[$k]['MedecineName']),"6x"),3):($coartems[$k]['MedecineName']))."</th>";
	$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
	$activeSheet->setCellValue(($first_column++).($row), (preg_match("/oartem/",$coartems[$k]['MedecineName'])?substr($coartems[$k]['MedecineName'],strpos(strtolower($coartems[$k]['MedecineName']),"6x"),3):($coartems[$k]['MedecineName'])));

	array("MedecineName"=>$coartems[$k]['MedecineName']);
	$total[$coartems[$k]['MedecineName']] = 0;
	//$total['Total']= 0;
}
$tb_data .= "</tr>";
SpanCells($activeSheet,"A1:C1",$align='left');
SpanCells($activeSheet,"A2:C2",$align='left');
SpanCells($activeSheet,"A3:C3",$align='left');

SpanCells($activeSheet,"A4:D4",$align='center');
SpanCells($activeSheet,"E4:H4",$align='center');
$activeSheet->setCellValue("E4", "Coartem");

$row = 6;
$str .= "
<div style='max-height:350px; padding-top:5px; border:0px solid #000; overflow:auto;'>
<table id=new class=list border=1>
<tr><th colspan=5></th><th colspan=4>Coartem</th><th colspan=3>&nbsp;</th></tr>
<tr><th>#</th><th>Date</th><th>Patient</th><th>Code</th><th>Category</th>";
$str .= $tb_data;

//var_dump($valid_dates);
//select all valid medicines now
//$sql = "SELECT DISTINCT md_name.*, pa_info.* FROM md_name, md_price, md_records, co_records, pa_records, sy_users, sy_center, pa_info WHERE co_records.ConsultationRecordID = md_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys} && md_name.MedecineNameID = md_price.MedecineNameID && md_records.ConsultationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && md_name.MedecineName LIKE('%{$_GET['medicine']}%') ORDER BY md_records.Date ASC, md_records.MedecineRecordID ASC";
$sql = "SELECT DISTINCT md_name.MedecineName, pa_records.InsuranceCardID, pa_records.DateIn, pa_records.FamilyCategory, pa_info.*, md_records.Quantity FROM md_records, md_price, md_name, co_records, pa_records, in_name, pa_info, sy_users, sy_center
		WHERE 
		md_records.MedecinePriceID = md_price.MedecinePriceID && 
		md_price.MedecineNameID = md_name.MedecineNameID &&
		(md_name.MedecineName LIKE('%{$_GET['medicine']}%') || md_name.MedecineName LIKE('%quinine%') || md_name.MedecineName LIKE('%tesunate%')) &&
		md_records.ConsultationRecordID = co_records.ConsultationRecordID &&
		co_records.PatientRecordID = pa_records.PatientRecordID &&
		pa_records.PatientID = pa_info.PatientID &&
		pa_records.ReceptionistID = sy_users.UserID &&
		sy_users.CenterID = sy_center.CenterID &&
		pa_records.InsuranceNameID = in_name.InsuranceNameID &&
		{$sys} &&
		md_records.Date LIKE('{$date}%') &&
		pa_records.FamilyCategory <= 2 &&
		pa_records.DateIN >= '{$reference_date}' &&
		in_name.InsuranceName = 'CBHI'
		";
/* echo $sql; echo "<br /><br />"; die;
if($_GET['medicine'] != "ALL")
	$sql = "SELECT DISTINCT md_name.*, md_category.*, md_price.* from md_name, md_price, md_category WHERE md_name.MedecineCategorID = md_category.MedecineCategoryID && md_name.MedecineNameID = md_price.MedecineNameID && md_name.MedecineName LIKE('%{$_GET['medicine']}%') ORDER BY MedecineCategoryName ASC, MedecineName ASC";
 */
 //var_dump($coartems);
$medecines = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
$medecines[] = $total;
//echo "<pre>";var_dump($medecines);
$category_ = ""; $count = 1;
for($i=0;$i<count($medecines);$i++){
	$first_column = 'A';
	$str .= "<tr>";
	if($medecines[$i]['Name'] != 'Total'){
		$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
		$activeSheet->setCellValue(($first_column++).($row), $count);
		$str .= "<td>".($count++)."</td>";
		
		$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
		$activeSheet->setCellValue(($first_column++).($row), $medecines[$i]['DateIn']);
		$str .= "<td>{$medecines[$i]['DateIn']}</td>";
		
		$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
		$activeSheet->setCellValue(($first_column++).($row), $medecines[$i]['Name']);
		
		$str .= "<td>{$medecines[$i]['Name']}</td>";
		
		$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
		$activeSheet->setCellValue(($first_column++).($row), $medecines[$i]['InsuranceCardID']);
		
		$str .= "<td>{$medecines[$i]['InsuranceCardID']}</td>";
		
		$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
		$activeSheet->setCellValue(($first_column++).($row), $medecines[$i]['FamilyCategory']);
		
		$str .= "<td>{$medecines[$i]['FamilyCategory']}</td>";
		
	} else {
		$str .= "<td colspan=5 style='text-align:center;'>&nbsp;</td>";
		
		SpanCells($activeSheet,($first_column.$row).":D".($row),$align='center');
		$activeSheet->setCellValue(($first_column++).($row), "");
		
	}
	$tot = 0; $started = false;
	foreach($coartems as $date){
		if($medecines[$i]['Name'] == 'Total'){
			if(!$started){
				$first_column = 'E';
				$started = true;
			}
			$str .= "<td>{$medecines[$i][$date['MedecineName']]}</td>";
			
			$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
			$activeSheet->setCellValue(($first_column++).($row), $medecines[$i][$date['MedecineName']]);
			
			continue;
		}
		//select the sum of the current medecines for the current days
		$all_medecines = preg_match("/oartem/",strtolower($medecines[$i]['MedecineName']))?($date['MedecineName'] == $medecines[$i]['MedecineName']?1:""):($date['MedecineName'] == $medecines[$i]['MedecineName']?$medecines[$i]['Quantity']:"");//returnSingleField("SELECT SUM(Quantity) as SM FROM md_records WHERE {$cnd} && Date='{$date}'",$field="SM",$data=true, $con);
		$str .= "<td>{$all_medecines}</td>";
		$activeSheet->setCellValue(($first_column++).($row), $all_medecines);
		$tot += $all_medecines;
		$total[$date['MedecineName']] += $all_medecines;
		$medecines[(count($medecines) - 1)][$date['MedecineName']] += $all_medecines;
	}
	//$str .= "<td></td>";
	$str .= "</tr>";
	$row++;
}
$str .= "</table>
</div>";
echo $str;

$styleArray = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('argb' => 'FF000000'),
		),
	),
);
$objPHPExcel->getActiveSheet()->getStyle("A4:K".(--$row))->applyFromArray($styleArray);

$activeSheet->setTitle("Medicine Distribution");

// Create a new worksheet, after the default sheet
$objPHPExcel->createSheet();
//save the file
$objWriter->save($file_name);
//var_dump($medecines);
//var_dump($_GET);
?>
<a href='<?= $file_name ?>' target='_blank'>download</a>