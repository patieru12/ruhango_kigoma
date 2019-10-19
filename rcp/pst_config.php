<?php
//var_dump($_GET);
if(trim(@$_GET['code']) == ""){
	echo "<span class=error>The Invalid Code Found!</span>";
	return;
}

//now select all records related to this code
require_once "../lib/db_function.php";
$last_five = returnAllData("SELECT mu_tm.TicketPaid, se_name.ServiceCode, pa_records.DateIn, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM mu_tm, se_name, se_records, pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE mu_tm.PatientRecordID = pa_records.PatientRecordID && se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.InsuranceCardID='".PDB($_GET['code'],true,$con)."' ORDER BY DateIn DESC LIMIT 0, 5",$con);
//var_dump($last_five);
?>
Code: <b><?= $_GET['code'] ?></b><br />
Name: <b><?= $last_five[0]['Name'] ?></b><br />
CF: <b><?= $last_five[0]['FamilyCode'] ?></b><br />
<table style='margin-top:20px; width:90%;' border=1 cellspacing=0>
	<tr><td>Date</td><td>Service</td><td>TM</td></tr>
	<?php
	if($last_five)
		foreach($last_five as $l){
			echo "<tr><td>{$l['DateIn']}</td><td>{$l['ServiceCode']}</td><td>{$l['TicketPaid']}</td></tr>";
		}
	?>
</table>
<script>
	$("#pst_").attr("checked",":true");
</script>