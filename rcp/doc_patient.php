<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

$select = "";
//var_dump($_GET);
if(strlen($_GET['key'])){
	if(!preg_match("/^[0-9]{4}/",$_GET['key']))
		$_GET['key'] = date("Ymd",time()).$_GET['key'];
	//select all possible information on the comming id
	$records = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.*, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.*  from pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_info.PatientID=pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.DocID LIKE('%{$_GET['key']}%') && pa_records.Status=0 ORDER BY DateIn DESC LIMIT 0, 10 ",$con),$multirows=true,$con);
	//echo $sql;
	if($records){
		
		$count = 0;
		

	?>
	<b class=visibl>
	<style>table#vsbl td, table#vsbl th{font-size:12px;}</style>
	<span class=styling></span>
	<table class=list id=vsbl border="1" style='width:100%; font-size:12px;'>
		<tr><th>#</th><th>Date</th><th>Doc. ID</th><th>Insurance</th><th>Name</th><th>Age</th><th>Sex</th><th>Family Chief</th><th>District</th><th>Sector</th><th>Cell</th><th>Village</th></tr>
		<?php
		for($i=0;$i<count($records);$i++){
			
			$str = <<<STR
			 $("#patient_search").val("{$records[$i]["InsuranceCardID"]}");
			 $("#name").val("{$records[$i]["Name"]}");
			 $("#patient_id").val("{$records[$i]["PatientID"]}");
			 $("#age").val("{$records[$i]["DateofBirth"]}");
			 $("#father").val("{$records[$i]["FamilyCode"]}");
			 $("#update").attr("checked",":true");
			 if("{$records[$i]["Sex"]}" == "Male"){
				$("#male").attr("checked",":true");
			 }
			 if("{$records[$i]["Sex"]}" == "Female"){
				$("#female").attr("checked",":true");
			 }
			 $("#district").val("{$records[$i]["DistrictName"]}");
			 $("#sector").val("{$records[$i]["SectorName"]}");
			 $("#cell").val("{$records[$i]["CellName"]}");
			 $("#village").val("{$records[$i]["VillageName"]}");
STR;
			echo "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\"); if($(\"#edit_mode\").val() == 0){receivePatient(\"{$records[$i]["PatientRecordID"]}\");}' id='id{$i}'>
			<td>".($i+1)."</td>
			<td>{$records[$i]["DateIn"]}</td>
			<td>{$records[$i]["DocID"]}</td>
			<td>{$records[$i]["InsuranceCardID"]}</td>
			<td>{$records[$i]["Name"]}</td>
			<td>{$records[$i]["DateofBirth"]}</td>
			<td>{$records[$i]["Sex"]}</td>
			<td>{$records[$i]["FamilyCode"]}</td>
			<td>{$records[$i]["DistrictName"]}</td>
			<td>{$records[$i]["SectorName"]}</td>
			<td>{$records[$i]["CellName"]}</td>
			<td>{$records[$i]["VillageName"]}</td></tr>";
		}
		?>
	<table></b>
	<?php
} else{
	
	echo "<span class=error-text>No Match Found</span>";
}
?>
<script>
	$(document).ready(function(){
		//document.getElementById("update").checked=false;
	});
	
</script>
<?php

}
?>