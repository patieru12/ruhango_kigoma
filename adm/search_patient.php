<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("adm" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

// var_dump($_GET);
if(!@$_GET['key']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}
$select = "";
//var_dump($_GET);
if(strlen($_GET['key'])){
	//select all possible information on the comming id
	$records = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientID from pa_records WHERE InsuranceNameID='{$_GET['key']}' LIMIT 0, 20 ",$con),$multirows=true,$con);
	//echo $sql;
	if($records){
		$select .= "&& (";
		$count = 0;
		foreach($records as $f)
			$select .= ($count++ >0 ?" || ":"")."pa_info.PatientID ='{$f['PatientID']}'";
		$select .= ")";
	

//select all patience related to the found key search
$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* from pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID {$select} ORDER BY Name ASC LIMIT 0,20;",$con),$multirows=true,$con);
//var_dump($patients);
//echo $sql;
if($patients){
	?>
	<b class=visibl>
	<style>table#vsbl td, table#vsbl th{font-size:16px;}</style>
	<span class=styling></span>
	<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
		<tr><th>Assurance</th><th>Non</th><th>Age</th><th>Sex</th><th>Chef de Famille</th><th>District</th><th>Secteur</th><th>Cellure</th><th>Village</th></tr>
		<?php
		for($i=0;$i<count($patients);$i++){
			$r = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.* from pa_records WHERE pa_records.PatientID='{$patients[$i]["PatientID"]}' ORDER BY DateIn DESC LIMIT 0,1",$con),$multirows=false,$con);
			$str = <<<STR
			 $("#patient_search").val("{$r["InsuranceCardID"]}");
			 $("#name").val("{$patients[$i]["Name"]}");
			 $("#patient_id").val("{$patients[$i]["PatientID"]}");
			 $("#age").val("{$patients[$i]["DateofBirth"]}");
			 $("#father").val("{$patients[$i]["FamilyCode"]}");
			 $("#update").attr("checked",":true");
			 if("{$patients[$i]["Sex"]}" == "Male"){
				$("#male").attr("checked",":true");
			 }
			 if("{$patients[$i]["Sex"]}" == "Female"){
				$("#female").attr("checked",":true");
			 }
			 $("#district").val("{$patients[$i]["DistrictName"]}");
			 $("#sector").val("{$patients[$i]["SectorName"]}");
			 $("#cell").val("{$patients[$i]["CellName"]}");
			 $("#village").val("{$patients[$i]["VillageName"]}");
STR;
			echo "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\"); {$str}' id='id{$i}'><td>{$r["InsuranceCardID"]}</td><td>{$patients[$i]["Name"]}</td><td>{$patients[$i]["DateofBirth"]}</td><td>{$patients[$i]["Sex"]}</td><td>{$patients[$i]["FamilyCode"]}</td><td>{$patients[$i]["DistrictName"]}</td><td>{$patients[$i]["SectorName"]}</td><td>{$patients[$i]["CellName"]}</td><td>{$patients[$i]["VillageName"]}</td></tr>";
		}
		?>
	</table></b>
	<?php
} else{
	
	echo "<span class=error-text>No Match Found</span>";
}

}
}
?>
<script>
	$(document).ready(function() { 
		$('#excel_file').live('change', function(){ 
			
			$("#upload_out").html('');
			$("#upload_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
			$("#upload_patient").ajaxForm({ 
				target: '#upload_out'
			}).submit(); 
		}); 
	});
</script>
<div id=upload_out></div>
<form action="upload_patient.php" id=upload_patient enctype='multipart/form-data' method=post>
	<input type=hidden value='<?= $_GET['key'] ?>' name=insurance_id />
	<input type=file name=patient_list class=txtfield1 id=excel_file style='width:300px;' />
</form>