<?php
session_start();
//var_dump($_SESSION);

require_once "../lib/db_function.php";
if("mut" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
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

if(strlen($_GET['key'])){
	$PatientID = PDB($_GET['key'], true, $con);
	$InsuranceNameID = PDB($_GET['ins'], true, $con);
	/*$sql="SELECT DISTINCT pa_records.*, mu_tm.*, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.*  from pa_records, mu_tm, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_info.PatientID=pa_records.PatientID && mu_tm.PatientRecordID = pa_records.PatientRecordID && 
	pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && 
	ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && 
	pa_records.DateIn != '0000-00-00' && (pa_records.DocID LIKE('%{$_GET['key']}%') || pa_records.InsuranceCardID LIKE('%{$_GET['key']}%') || pa_info.Name LIKE('%{$_GET['key']}%')) ORDER BY  pa_records.Status ASC, DateIn DESC, DocID ASC LIMIT 0, 20 "*/
	$sql = "SELECT 	a.*,
					b.InsuranceCardID AS InsuranceCardID,
					b.FamilyCategory AS FamilyCategory,
					b.InsuranceNameID AS InsuranceNameID,
					b.DateIn AS DateIn,
					c.VillageName AS VillageName,
					d.CellName AS CellName,
					e.SectorName AS SectorName,
					f.DistrictName AS DistrictName,
					b.PatientRecordID AS PatientRecordID,
					IF(b.InsuranceNameID = '{$InsuranceNameID}',1,0) AS orderToFollow,
					COALESCE(b.HouseManagerID,'') AS HouseManagerID
					FROM pa_info AS a
					LEFT JOIN pa_records AS b
					ON a.PatientID = b.PatientID AND b.InsuranceNameID = '{$InsuranceNameID}'
					INNER JOIN ad_village AS c
					ON a.VillageID = c.ViillageID
					INNER JOIN ad_cell AS d
					ON c.CellID = d.CellID
					INNER JOIN ad_sector AS e
					ON d.SectorID = e.SectorID
					INNER JOIN ad_district AS f
					ON e.DistrictID = f.DistrictID
					WHERE b.InsuranceCardID = '{$PatientID}'
					ORDER BY orderToFollow DESC, PatientRecordID DESC
					";
	// echo $sql;

	//$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.InsuranceCardID, pa_records.FamilyCategory, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_records.PatientID = pa_info.PatientID&& pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.InsuranceCardID LIKE('%{$_GET['key']}%') && pa_records.InsuranceNameID='{$_GET['ins']}' ORDER BY InsuranceCardID ASC LIMIT 0, 10",$con),$multirows=true,$con);
	$_SESSION['patient_records'] = $patients = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	// echo "<pre>"; var_dump($patients);
	// return;
	//echo "OK";
	if($patients){
		//echo count($patients);
		?>
		<script>
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
	echo "<span class='error-text'>No Search Key Provided</span>";
}
?>