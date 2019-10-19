<?php
session_start();
//var_dump($_SESSION);

require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
if(!@$_GET['ins']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}
$select = "";
//var_dump($_GET);

if(strlen($_GET['key']) > (@$_GET['ins'] == 1?11:($_GET['ins'] == 3?6:0))){

	//$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.InsuranceCardID, pa_records.FamilyCategory, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_records.PatientID = pa_info.PatientID&& pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.InsuranceCardID LIKE('%{$_GET['key']}%') && pa_records.InsuranceNameID='{$_GET['ins']}' ORDER BY InsuranceCardID ASC LIMIT 0, 10",$con),$multirows=true,$con);
	$_SESSION['patient_records'] = $patients = $records = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.*, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.*  from pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_info.PatientID=pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.DateIn != '0000-00-00' && ( pa_info.Name LIKE('{$_GET['key']}%') || pa_info.phoneNumber LIKE('{$_GET['key']}%') || pa_info.PatientID ='{$_GET['key']}' ) ORDER BY  pa_records.Status ASC, DateIn DESC, DocID ASC LIMIT 0, 20 ",$con),$multirows=true,$con);
	// echo $sql;
	// var_dump($patients);
	//echo "OK";
	if($patients){
		// echo count($patients);
		?>
		<script>
			// alert($("#display_now").html());
			$("#display_now").click();
		</script>
		<!-- make all links with the 'rel' attribute open overlays -->
		<?php
	} else{
		
		echo "<span class=error-text>No Match Found</span>";
	}
	?>
	<script>
		$(document).ready(function(){
			document.getElementById("update").checked=false;
		});
		
	</script>
	<?php

} else{
	echo "Searching....";
}
?>