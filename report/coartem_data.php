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
$_GET['medicine'] = "coartem";
$_GET['day'] = !@$_GET['day']?date("d",time()):$_GET['day'];
if($_GET['month'] < 10)
	$_GET['month'] = "0".$_GET['month'];
//echo date("Y-m-d",($start_time + $step));
$report_title = "Anti-Malaria Distribution Report";
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
$date = $_GET['year']."-".$_GET['month']."-".$_GET['day'];
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
$valid_dates = array();
$tb_data = "";
$coartems = formatResultSet($rslt=returnResultSet($sql = "SELECT DISTINCT MedecineName FROM md_name WHERE md_name.MedecineName LIKE('%{$_GET['medicine']}%') || md_name.MedecineName LIKE('%quinine%') || md_name.MedecineName LIKE('%tesunate%')",$con),$multirows=true,$con);
//echo $sql;
for($start_time; preg_match("/^{$_GET['year']}-{$_GET['month']}$/",date("Y-m",$start_time)); $start_time += $step){
	$str .= "<label onclick='$(\".patient_found\").load(\"coartem_data.php?year=\" + $(\"#year\").val() + \"&month=\" + $(\"#month\").val() + \"&post=\" + $(\"#post\").val() + \"&day=".date("d",$start_time)."\");' class='flatbtn-blu".($_GET['day'] == date("d",$start_time)?" link_active":"")."' style='padding:0 10px;'>".date("d",$start_time)."</label>";
	
	$valid_dates[] = date("Y-m-d",$start_time);
}
$total = array("Name"=>"Total");
for($k = 0; $k<count($coartems); $k++){
	$tb_data .= "<th>".(preg_match("/oartem/",$coartems[$k]['MedecineName'])?substr($coartems[$k]['MedecineName'],strpos(strtolower($coartems[$k]['MedecineName']),"6x"),3):($coartems[$k]['MedecineName']))."</th>";
	array("MedecineName"=>$coartems[$k]['MedecineName']);
	$total[$coartems[$k]['MedecineName']] = 0;
	//$total['Total']= 0;
}
$tb_data .= "</tr>";
$str .= "
<div style='max-height:350px; padding-top:5px; border:0px solid #000; overflow:auto;'>
<table id=new class=list border=1><th colspan=2>&nbsp;</th>";
$str .= $tb_data;

//var_dump($valid_dates);
//select all valid medicines now
$sql = "SELECT DISTINCT md_name.*, pa_info.* FROM md_name, md_price, md_records, co_records, pa_records, sy_users, sy_center, pa_info WHERE co_records.ConsultationRecordID = md_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys} && md_name.MedecineNameID = md_price.MedecineNameID && md_records.ConsultationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && md_name.MedecineName LIKE('%{$_GET['medicine']}%') ORDER BY md_records.Date ASC, md_records.MedecineRecordID ASC";
$sql = "SELECT DISTINCT md_name.MedecineName, pa_info.*, md_records.Quantity FROM md_records, md_price, md_name, co_records, pa_records, pa_info, sy_users, sy_center
		WHERE 
		md_records.MedecinePriceID = md_price.MedecinePriceID && 
		md_price.MedecineNameID = md_name.MedecineNameID &&
		(md_name.MedecineName LIKE('%{$_GET['medicine']}%') || md_name.MedecineName LIKE('%quinine%') || md_name.MedecineName LIKE('%tesunate%')) &&
		md_records.ConsultationRecordID = co_records.ConsultationRecordID &&
		co_records.PatientRecordID = pa_records.PatientRecordID &&
		pa_records.PatientID = pa_info.PatientID &&
		pa_records.ReceptionistID = sy_users.UserID &&
		sy_users.CenterID = sy_center.CenterID &&
		{$sys} &&
		md_records.Date ='{$date}'
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
	
	$str .= "<tr>";
	if($medecines[$i]['Name'] != 'Total'){
		$str .= "<td>".($count++)."</td>";
		$str .= "<td>{$medecines[$i]['Name']}</td>";
	} else
		$str .= "<td colspan=2 style='text-align:center;'>&nbsp;</td>";
	$tot = 0;
	foreach($coartems as $date){
		if($medecines[$i]['Name'] == 'Total'){
			$str .= "<td>{$medecines[$i][$date['MedecineName']]}</td>";
			continue;
		}
		//select the sum of the current medecines for the current days
		$all_medecines = preg_match("/oartem/",strtolower($medecines[$i]['MedecineName']))?($date['MedecineName'] == $medecines[$i]['MedecineName']?1:""):($date['MedecineName'] == $medecines[$i]['MedecineName']?$medecines[$i]['Quantity']:"");//returnSingleField("SELECT SUM(Quantity) as SM FROM md_records WHERE {$cnd} && Date='{$date}'",$field="SM",$data=true, $con);
		$str .= "<td>{$all_medecines}</td>";
		$tot += $all_medecines;
		$total[$date['MedecineName']] += $all_medecines;
		$medecines[(count($medecines) - 1)][$date['MedecineName']] += $all_medecines;
	}
	//$str .= "<td>{$tot}</td>";
	$str .= "</tr>";
}
$str .= "</table>
</div>";
echo $str;
//var_dump($medecines);
//var_dump($_GET); 