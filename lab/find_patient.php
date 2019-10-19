<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

$select = "";
//var_dump($_GET);
if(strlen($_GET['key'])){
	
	//select all possible information on the comming id
	$records = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.*, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.*  from pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_info.PatientID=pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.DateIn != '0000-00-00' && (pa_records.DocID LIKE('%{$_GET['key']}%') || pa_info.Name LIKE('%{$_GET['key']}%')) ORDER BY  pa_records.Status ASC, DateIn DESC, DocID ASC LIMIT 0, 20 ",$con),$multirows=true,$con);
	//echo $sql;
	if($records){
		$count = 0;
		//var_dump($records);
	?>
	<b class=visibl>
	<style>table#vsbl td, table#vsbl th{ font-size:10px; font-weight:bold; }</style>
	<span class=styling></span>
	<table class=list id=vsbl border="1" style='width:100%;'>
		<tr><th colspan=2>&nbsp;</th><th>#</th><th>Date</th><th>Service</th><th>Insurance</th><th>Name</th><th>Age</th><th>Sex</th><th>Family Chief</th><th>Weight</th><th>Temp.</th><th>District</th><th>Sector</th><th>Cell</th><th>Village</th></tr>
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
			$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.*, se_records.* FROM se_records, se_name WHERE se_records.PatientRecordID='{$records[$i]['PatientRecordID']}' && se_name.ServiceNameID = se_records.ServiceNameID",$con),$multirows=false,$con);
			$age = getAge($records[$i]["DateofBirth"]);
			echo "<tr id='id{$i}'>
					<td onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\"); receivePatient(\"{$records[$i]["PatientRecordID"]}\",\"{$records[$i]["DocID"]}\")' title='Click to save all document records'><img src='../images/save.png' height='11' alt='Fill Document Data' /></td>
					<td><a href='../forms/".returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='{$records[$i]['InsuranceNameID']}'",$field="FormFile",$data=true, $con)."?records={$records[$i]['PatientRecordID']}' title='Click to reprint' target='_blank' style='color:blue; text-decoration:none;' onclick='alert(\"Unable to locate Print Function\"); return false'><img src='../images/print.png' alt='p' height='11' /></a></td>
					<td>".($i+1)."</td>
					<td>{$records[$i]["DateIn"]}</td>
					<td class='pa_{$i}1' onclick='if(true){return false;} if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}1\",\"{$service['ServiceCode']}\",\"se_records\",\"{$service['ServiceRecordID']}\",\"ServiceRecordID\",\"ServiceNameID\",\"save_rqst/save_service.php\");}'>{$service['ServiceCode']}</td>
					<td class='pa_{$i}2' onclick='if(true){return false;} if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}2\",\"{$records[$i]['InsuranceCardID']}\",\"pa_records\",\"{$records[$i]['PatientRecordID']}\",\"PatientRecordID\",\"InsuranceCardID\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["InsuranceCardID"]}</td>
					<td class='pa_{$i}3' onclick='if(true){return false;} if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}3\",\"{$records[$i]['Name']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"Name\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Name"]}</td>
					<td class='pa_{$i}4' onclick='if(true){return false;} if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}4\",\"{$records[$i]['DateofBirth']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"DateofBirth\",\"save_rqst/save_pa_info.php\");}'>{$age}</td>
					<td class='pa_{$i}5' onclick='if(true){return false;} if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}5\",\"{$records[$i]['Sex']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"Sex\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Sex"]}</td>
					<td class='pa_{$i}6' onclick='if(true){return false;} if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}6\",\"{$records[$i]['FamilyCode']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"FamilyCode\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["FamilyCode"]}</td>
					<td class='pa_{$i}7' onclick='if(true){return false;} if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}7\",\"{$records[$i]['Weight']}\",\"pa_records\",\"{$records[$i]['PatientRecordID']}\",\"PatientRecordID\",\"Weight\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Weight"]}</td>
					<td class='pa_{$i}8' onclick='if(true){return false;} if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}8\",\"{$records[$i]['Temperature']}\",\"pa_records\",\"{$records[$i]['PatientRecordID']}\",\"PatientRecordID\",\"Temperature\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Temperature"]}</td>
					<td>{$records[$i]["DistrictName"]}</td>
					<td>{$records[$i]["SectorName"]}</td>
					<td>{$records[$i]["CellName"]}</td>
					<td>{$records[$i]["VillageName"]}</td>
				</tr>";
		}
		?>
	<table></b>
	<?php
} else{
	
	echo "<span class=error-text>No Match Found</span>";
}


}
?>