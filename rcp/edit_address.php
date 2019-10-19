<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//select patient Address Information now
$records = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientRecordID, ad_village.*, ad_cell.*, ad_sector.*, ad_district.*  from pa_records, ad_village, ad_cell, ad_sector, ad_district WHERE  pa_records.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.PatientRecordID = '".PDB($_GET['patientid'],true,$con)."' ",$con),$multirows=false,$con);
//var_dump($records);
?>
<style>
.list tr td,.list tr th{
	font-size:12px;
}
</style>
<span class=save_edits></span>
<form action="./save_edit_address.php" id=edit_address method=post>
	<input type=hidden name=patient value='<?= $records['PatientRecordID'] ?>' />
	<table>
		<tr>
			<td>Village</td><td>Cell</td><td>Sector</td><td>District</td>
		</tr>
		<tr>
			<td><input type=text value='<?= @$records['VillageName'] ?>' name='village' autocomplete="off" id=village class="txtfield1" style='width:120px' /></td>
			<td><input type=text value='<?= @$records['CellName'] ?>' name='cell' autocomplete="off" id=cell class="txtfield1" style='width:120px' /></td>
			<td><input type=text value='<?= @$records['SectorName'] ?>' name='sector' autocomplete="off" id=sector class="txtfield1" style='width:110px' /></td>
			<td><input type=text value='<?= @$records['DistrictName'] ?>' name='district' autocomplete="off" id=district class="txtfield1" style='width:110px' /></td>
		</tr>
		<tr>
			<td colspan=4><div class='address_search' style='max-height:300px; overflow:auto; font-size:10px;'></div></td>
		</tr>
	</table>
	<input type=submit id=save class="flatbtn-blu" name=update_address value='Update' />
</form>
<script>
	function addrSearch(level,key){
		$(".address_search").html("");
		$(".address_search").load("search_address.php?key=" + key + "&level=" + level);
	}
	$("#village").keyup(function(e){
		addrSearch(4,$("#village").val());
	});
	$("#cell").keyup(function(e){
		addrSearch(3,$("#cell").val());
	});
	$("#sector").keyup(function(e){
		addrSearch(2,$("#sector").val());
	});
	$("#district").keyup(function(e){
		addrSearch(1,$("#district").val());
	});
	
	$('#save').click(function(e){ 
			e.preventDefault();
			//$('#save').attr("desabled",":true");
			$(".save_edits").html('');
			$(".save_edits").html('<img src="../images/loading.gif" alt="Saving"/>'); 
			$("#edit_address").ajaxForm({ 
				target: '.save_edits'
			}).submit();
			
	});
</script>