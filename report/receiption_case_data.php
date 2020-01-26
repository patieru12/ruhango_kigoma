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
$report_title = "Received Money In Caisse";
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
$str = "
<style>
	table td, table th{
		border:0px solid #000;
	}
	.title{
		font-size:20px;
	}
	#new{ font-size:13px;}
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
<table border=1 id=new class=list border=1 style='width: 100%; border-collapse: collapse;'><tr>";
$valid_dates = array();
$patients = array();
$total_patients = array();
$tms = array();
$total_tms = array();
$start_date = date("Y-m-d",$start_time);

$cols = 6;
$currentCol = 0;
$totalAmount = 0;
for($start_time; preg_match("/^{$_GET['year']}-{$_GET['month']}$/",date("Y-m",$start_time)); $start_time += $step){

	if($currentCol >= 5){
		$str .= "</tr><tr>";
		$currentCol = 1;
	} else {
		$currentCol++;
	}
	$myDate = date("Y-m-d", $start_time);
	$amount = returnSingleField("SELECT amount FROM sy_caisse WHERE date='{$myDate}'", "amount", true, $con);
	$totalAmount += $amount;
	$str .= "<td style='width:20%; border: 1px solid #000; text-align: center;'><strong>".date("d F Y",$start_time)."</strong><br />".number_format($amount)." RWF</td>";
	$valid_dates[date("Y-m-d",$start_time)] = 0;
	$patients[date("Y-m-d",$start_time)] = 0;
	$total_patients[date("Y-m-d",$start_time)] = 0;
	$tms[date("Y-m-d",$start_time)] = 0;
	$total_tms[date("Y-m-d",$start_time)] = 0;
}
$end_date =  date("Y-m-d",$start_time);

$str .= "</tr><tr><th colspan='5' style='text-align: center;'>Total<br />".number_format($totalAmount)." RWF</th></tr>";

$str .= "</table>
</div>";
echo $str;
//var_dump($medecines);
//var_dump($_GET); 

$provinceData = strtoupper($_PROVINCE);
$distictData = strtoupper($_DISTRICT);
$sectorData = strtoupper($_SECTOR);
$healthCenter = strtoupper($organisation);
$hcemail = strtolower($organisation_email);

$dataToPrintHeader = <<<PDFINFO
		<table style="font-size: 12px; width:100%; border:0px solid #000;">
		<tr>
			<td style="text-align: left;">REPUBLIC OF RWANDA</td>
		</tr>
		<tr>
			<td style="vertical-align: middle; padding-bottom: 2px; text-align: left;">
				<img src="../images/rwanda.png" style='width:50px' /><br />
			</td>
		</tr>
		<tr><td>{$provinceData} PROVINCE</td></tr>
		<tr><td>{$distictData} DISTRICT</td></tr>
		<tr><td>{$sectorData} SECTOR</td></tr>
		<tr><td>{$healthCenter}</td></tr>
		<tr><td>Tel: {$organisation_phone}</td></tr>
		<tr><td>Email: {$hcemail}</td></tr>
	</table>
	<div style='text-align:center; border-bottom:1px solid #000; font-weight:bold; margin-bottom:5px;'>
		Monthly Caisse Summary {$month_}
	</div>
	
PDFINFO;
$str = $dataToPrintHeader.$str;
// echo $str;
$pdf = new mPDF();

	$pdf->Open();

	$pdf->AddPage("P");

	$pdf->SetFont("Arial","N",10);
	$css = file_get_contents("./pdf_css.css");
	
	$pdf->WriteHTML($css,1);
	
	$pdf->WriteHTML($str);
	$filename = "./dailyreceptiondata.pdf";
	//echo $filename;
	$pdf->Output($filename); 
	echo "<a style='font-size:12px; font-weight:bold;' href='./{$filename}' target='_blank'>Download</a>";

die;
