<?php
/* if(@$_GET['noretry']){
	return;
} */
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
	
	//select all possible information on the coming id
	$records = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.*, mu_tm.*, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.*  from pa_records, mu_tm, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_info.PatientID=pa_records.PatientID && mu_tm.PatientRecordID = pa_records.PatientRecordID && pa_info.VillageID = ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.DateIn != '0000-00-00' && (pa_records.DocID LIKE('{$_GET['key']}')) ORDER BY  pa_records.Status ASC, DateIn DESC, DocID ASC LIMIT 0, 300 ",$con),$multirows=true,$con);
	//echo $sql;
	if($records){
		$count = 0;
		//var_dump($records);
		
	?>
	<b class=visibl>
	<style>table#vsbl td, table#vsbl th{ font-size:10px; font-weight:bold; }</style>
	<span class=styling></span>
	<span class=track style='display:none;'></span>
	<table class=list id=vsbl border="1" style='width:100%;'>
		<tr><th colspan=2>&nbsp;</th><th>#</th><th>Date</th><th>Service</th><th>TM</th><th>Insurance</th><th>Cat.</th><th>Name</th><th>Age</th><th>Sex</th><th>Family</th><th>Weight</th><th>Temp.</th><th>District</th><th>Sector</th><th>Cell</th><th>Village</th></tr>
		<?php
		$service = null;
		for($i=0;$i<count($records);$i++){
			
			$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.*, se_records.* FROM se_records, se_name WHERE se_records.PatientRecordID='{$records[$i]['PatientRecordID']}' && se_name.ServiceNameID = se_records.ServiceNameID",$con),$multirows=false,$con);
			$age = getAge($records[$i]["DateofBirth"],1,$records[$i]["DateIn"]);
			echo "<tr id='id{$i}'>
					<td onclick='$(\".track\").html(\"pa_{$i}9\"); $(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\"); receivePatient(\"{$records[$i]["PatientRecordID"]}\",\"{$records[$i]["DocID"]}\",\"cpc\",false)' title='Edit Patient File'><img src='../images/save.png' height='11' alt='E' /></td>
					<!--<td onclick='$(\".track\").html(\"pa_{$i}9\"); $(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\"); receivePatient(\"{$records[$i]["PatientRecordID"]}\",\"{$records[$i]["DocID"]}\",\"{$service['ServiceCode']}\",false)' title='Edit Patient File'><img src='../images/save.png' height='11' alt='E' /></td>-->
					<td><a href='../forms/".returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='{$records[$i]['InsuranceNameID']}'",$field="FormFile",$data=true, $con)."?records={$records[$i]['PatientRecordID']}' title='Print Patient File' target='_blank' style='color:blue; text-decoration:none;'><img src='../images/print.png' alt='P' height='11' /></a></td>
					<td>".($i+1)."</td>
					<td>{$records[$i]["DateIn"]}</td>
					<td class='pa_{$i}1' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}1\",\"{$service['ServiceCode']}\",\"se_records\",\"{$service['ServiceRecordID']}\",\"ServiceRecordID\",\"ServiceNameID\",\"save_rqst/save_service.php\");}'>{$service['ServiceCode']}</td>
					<td class='pa_{$i}9' onclick='if(true){ return false;} if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}9\",\"{$records[$i]['TicketPaid']}\",\"mu_tm\",\"{$records[$i]['TicketID']}\",\"TicketID\",\"TicketPaid\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["TicketPaid"]}</td>
					<td class='pa_{$i}2' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}2\",\"{$records[$i]['InsuranceCardID']}\",\"pa_records\",\"{$records[$i]['PatientRecordID']}\",\"PatientRecordID\",\"InsuranceCardID\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["InsuranceCardID"]}</td>
					<td title='Now You Can Edit Family Category By Clicking the Green Cell' style='background-color:green; color:white' class='pa_{$i}10' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}10\",\"{$records[$i]['FamilyCategory']}\",\"pa_records\",\"{$records[$i]['PatientRecordID']}\",\"PatientRecordID\",\"FamilyCategory\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["FamilyCategory"]}</td>
					<td class='pa_{$i}3' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}3\",\"{$records[$i]['Name']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"Name\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Name"]}</td>
					<td class='pa_{$i}4' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}4\",\"{$records[$i]['DateofBirth']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"DateofBirth\",\"save_rqst/save_pa_info.php\");}'>{$age}</td>
					<td class='pa_{$i}5' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}5\",\"{$records[$i]['Sex']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"Sex\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Sex"]}</td>
					<td class='pa_{$i}6' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}6\",\"{$records[$i]['FamilyCode']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"FamilyCode\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["FamilyCode"]}</td>
					<td class='pa_{$i}7' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}7\",\"{$records[$i]['Weight']}\",\"pa_records\",\"{$records[$i]['PatientRecordID']}\",\"PatientRecordID\",\"Weight\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Weight"]}</td>
					<td class='pa_{$i}8' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}8\",\"{$records[$i]['Temperature']}\",\"pa_records\",\"{$records[$i]['PatientRecordID']}\",\"PatientRecordID\",\"Temperature\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Temperature"]}</td>
					<td>{$records[$i]["DistrictName"]}</td>
					<td>{$records[$i]["SectorName"]}</td>
					<td>{$records[$i]["CellName"]}</td>
					<td>{$records[$i]["VillageName"]}<a style='float:right;' href='./edit_address.php?patientid={$records[$i]['PatientRecordID']}' rel='#overlay' title='Edit Address'><img src='../images/edit.png' /></a></td>
				</tr>";
		}
		/* if(count($records) == 1){
			?>
			<script>
				
					receivePatient("<?= $records[0]["PatientRecordID"] ?>","<?= $records[0]["DocID"] ?>","cpc",true);
					//receivePatient("<?= $records[0]["PatientRecordID"] ?>","<?= $records[0]["DocID"] ?>","<?= $service['ServiceCode'] ?>",true);
			</script>
			<?php
		} */
		?>
	<table></b>
	<?php
} else{
	echo "<span class=error-text>No Match Found</span>";
}

}
?>
	<div class="apple_overlay" id="overlay">
	  <!-- the external content is loaded inside this tag -->
	  <div class="contentWrap"></div>
	</div>
<script>

$(function() {
    // if the function argument is given to overlay,
    // it is assumed to be the onBeforeLoad event listener
    $("a[rel]").overlay({

        mask: '#206095',
        effect: 'apple',
        onBeforeLoad: function() {

            // grab wrapper element inside content
            var wrap = this.getOverlay().find(".contentWrap");

            // load the page specified in the trigger
            wrap.load(this.getTrigger().attr("href"));
        }

    });
});
</script>