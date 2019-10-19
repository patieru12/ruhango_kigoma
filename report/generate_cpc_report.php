<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

// var_dump($_POST); die;
$post = array();
$diag = array();
$spdiag = array();
foreach($_POST as $key=>$value){
	if(preg_match("/^post/",$key)){
		if(!in_array($value,$post))
			$post[] = $value;
	} elseif(preg_match("/^diag/",$key)){
		if(!in_array($value,$diag))
			$diag[] = $value;
	} elseif(preg_match("/^spdiag/",$key)){
		if(!in_array($value,$spdiag))
			$spdiag[] = $value;
	}
}
//var_dump($post);
//get the first second for the report
$start = explode("-",$_POST['start_date']);
$start_second = mktime(0,0,0, $start[1],$start[2],$start[0]);
//echo date("Y-m-d",$start_second);
$step = 60*60*24;
//echo date("Y-m-d",($start_second + $step));
$dates = array();
$post_str = ""; $i=1;
$_post_condition = "";
if($post){
	$_post_condition = "(";
	foreach($post as $p){
		if($i++ > 1){
			if(($i - 1) == count($post))
				$post_str .= " and ";
			else
				$post_str .= ", ";
			$_post_condition .= " || ";
		}
		//var_dump($p);
		$_post_condition .= "sy_users.CenterID='{$p}'";
		$post_str .= " ".returnSingleField($sql="SELECT CenterName FROM sy_center WHERE CenterID='{$p}'",$field="CenterName",$data=true, $con);
	}
	$_post_condition .= ")";
} else{
	echo "<span class=error-text>Select The Post to view Information</span>";
	return;
}
$cinpans = time() - (60*60*24*30*12*5);
//echo date("Y-m-d",$cinpans);echo "<br />";
$report = "
<style>
#new_ td{
	border:1px solid grey; 
	font-size:10px;
}

</style>
CPC Daily Report<br />
Post: {$post_str}<br />
Period: {$_POST['start_date']} - {$_POST['end_date']}
<table border=1 style='font-size:11px; font-weight:bold; font-family:arial;'>";
$report .= "<tr><th rowspan=2>&nbsp;</th>";
$tr = "<tr>";
for($start_second; date("Y-m-d",$start_second)<= $_POST['end_date']; $start_second += $step){
	$dates[] = date("Y-m-d",$start_second);
	$report .= "<th colspan=2>".date("d",$start_second)."</th>";
	$tr .= "<td><5</td><td>>=5</td>";
}
$report .= "<th colspan=2>Tot.</th></tr>";
$report .= $tr."<td><5</td><td>>=5</td></tr>";
foreach($spdiag as $spd){
	$tot1 = 0;
	$tot2 = 0;
	$spe = returnSingleField($sql="SELECT SpecialName FROM co_special_diagnostic WHERE SpecialID='{$spd}'",$field="SpecialName",$data=true, $con);
	$report .= "<tr id=tr ><td>{$spe}</td>";
	//select all component that are included in the 
	$component = returnAllData($sql="SELECT * FROM co_special_data WHERE SpecialID='{$spd}'",$con);
	$count_sp = 1;
	$sp_component = "";
	if($component){
		$sp_component = " && (";
		foreach($component as $c){
			if($count_sp++ > 1)
				$sp_component .= " || ";
			$sp_component .= "co_diagnostic_records.DiagnosticID = '{$c['DiagnosticID']}'";
		}
		$sp_component .= ")";
	}
	//echo $sp_component;
	//echo "<pre>";var_dump($component);
	foreach($dates as $date){
		$pecime = 0;
		if($component){
			$sql= "SELECT COUNT(`DiagnosticRecordID`) as Data FROM co_diagnostic_records, co_records, pa_records, sy_users, pa_info WHERE pa_info.PatientID = pa_records.PatientID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.ReceptionistID = sy_users.UserID && pa_info.DateofBirth > '".(date("Y-m-d",$cinpans))."'&& co_records.Date = '{$date}' && {$_post_condition} {$sp_component}";
			$sql2= "SELECT COUNT(`DiagnosticRecordID`) as Data FROM co_diagnostic_records, co_records, pa_records, sy_users, pa_info WHERE pa_info.PatientID = pa_records.PatientID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.ReceptionistID = sy_users.UserID && pa_info.DateofBirth <= '".(date("Y-m-d",$cinpans))."'&& co_records.Date = '{$date}' && {$_post_condition} {$sp_component}";
			//echo $sql;echo "<br />";
			$pecime = returnSingleField($sql,$field="Data",$data=true, $con);
			$nopecime = returnSingleField($sql2,$field="Data",$data=true, $con);
		}
		$tot1 += $pecime;
		$pecime = $pecime == 0?"":$pecime;
		$tot2 += $nopecime;
		$nopecime = $nopecime == 0?"":$nopecime;
		$report .= "<td>{$pecime}</td>";
		$report .= "<td>{$nopecime}</td>";
	}
	$report .= "<td>{$tot1}</td><td>{$tot2}</td></tr>";
}
//echo $sp_component;
foreach($diag as $d){
	$e = returnSingleField($sql="SELECT DiagnosticName FROM co_diagnostic WHERE DiagnosticID='{$d}'",$field="DiagnosticName",$data=true, $con);
	$report .= "<tr id=tr><td>{$e}</td>";
	$tot1 = 0;
	$tot2 = 0;
	foreach($dates as $date){
		$sql= "SELECT COUNT(`DiagnosticRecordID`) as Data FROM co_diagnostic_records, co_records, pa_records, sy_users, pa_info WHERE pa_info.PatientID = pa_records.PatientID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.ReceptionistID = sy_users.UserID && co_diagnostic_records.DiagnosticID='{$d}' && pa_info.DateofBirth > '".(date("Y-m-d",$cinpans))."' && co_records.Date = '{$date}' && {$_post_condition}";
		$sql2= "SELECT COUNT(`DiagnosticRecordID`) as Data FROM co_diagnostic_records, co_records, pa_records, sy_users, pa_info WHERE pa_info.PatientID = pa_records.PatientID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.ReceptionistID = sy_users.UserID && co_diagnostic_records.DiagnosticID='{$d}' && pa_info.DateofBirth <= '".(date("Y-m-d",$cinpans))."' && co_records.Date = '{$date}' && {$_post_condition}";
		//echo $sql;echo "<br />";
		$pecime = returnSingleField($sql,$field="Data",$data=true, $con);
		$tot1 += $pecime;
		$pecime = $pecime == 0?"":$pecime;
		$nopecime = returnSingleField($sql2,$field="Data",$data=true, $con);
		$tot2 += $nopecime;
		$nopecime = $nopecime == 0?"":$nopecime;
		$report .= "<td>{$pecime}</td>";
		$report .= "<td>{$nopecime}</td>";
	}
	$report .= "<td>{$tot1}</td><td>{$tot2}</td></tr>";
}
$report .= "</table>";

echo $report;
?>