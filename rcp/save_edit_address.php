<?php
session_start(); 
//var_dump($_SESSION); die;

require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($field="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
//save the district
$district_id = returnSingleField($sql="SELECT * FROM ad_district WHERE DistrictName='".ucfirst($_POST['district'])."'",$field="DistrictID",$data=true, $con);
if(!$district_id)
	$district_id = saveAndReturnID("INSERT INTO ad_district SET DistrictName='".ucfirst($_POST['district'])."'", $con);
	
$sector_id = returnSingleField($sql="SELECT * FROM ad_sector WHERE SectorName='".ucfirst($_POST['sector'])."' && DistrictID='{$district_id}'",$field="SectorID",$data=true, $con);
if(!$sector_id)
	$sector_id = saveAndReturnID("INSERT INTO ad_sector SET SectorName='".ucfirst($_POST['sector'])."', DistrictID='{$district_id}'", $con);
	
$cell_id = returnSingleField($sql="SELECT * FROM ad_cell WHERE CellName='".ucfirst($_POST['cell'])."' && SectorID='{$sector_id}'",$field="CellID",$data=true, $con);
if(!$cell_id)
	$cell_id = saveAndReturnID("INSERT INTO ad_cell SET CellName='".ucfirst($_POST['cell'])."', SectorID='{$sector_id}'", $con);
	
$village_id = returnSingleField($sql="SELECT * FROM ad_village WHERE VillageName='".ucfirst($_POST['village'])."' && CellID='{$cell_id}'",$field="ViillageID",$data=true, $con);
if(!$village_id)
	$village_id = saveAndReturnID("INSERT INTO ad_village SET VillageName='".ucfirst($_POST['village'])."', CellID='{$cell_id}'", $con);
//echo $village_id;
saveData($sql = "UPDATE pa_records SET VillageID='{$village_id}' WHERE PatientRecordID='".PDB($_POST['patient'],true,$con)."'",$con);
//echo $sql;
// saveData($sql = "UPDATE pa_info SET VillageID='{$village_id}' WHERE PatientID='".returnSingleField("SELECT PatientID FROM pa_records WHERE PatientRecordID='".PDB($_POST['patient'],true,$con)."'","PatientID",true,$con)."'",$con);
//echo $sql; die;
?>
<span class=success>Address Saved</span>
<script>
	setTimeout(function(){
		$(".close").click();
	},200);
	setTimeout(function(){
		$("#search").click();
	},400);
</script>