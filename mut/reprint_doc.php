<?php
/* if(@$_GET['noretry']){
	return;
} */
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("mut" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

$select = "";
// var_dump($_GET);
if(strlen(trim($_GET['key'])) ){
	//select all possible information on the coming id
	
	$records = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT 	pa_records.*,
																			pa_info.*, 
																			in_name.InsuranceName,
																			COALESCE(pa_records.InsuranceCardID, pa_records.applicationNumber, '') AS InsuranceCardID
																			FROM pa_records, 
																			pa_info, 
																			in_name
																			WHERE pa_info.PatientID=pa_records.PatientID &&
																				  pa_records.InsuranceNameID = in_name.InsuranceNameID &&
																				  (pa_records.DocID LIKE('%{$_GET['key']}%') || 
																				   pa_records.InsuranceCardID LIKE('%{$_GET['key']}%') || 
																				   pa_info.Name LIKE('%{$_GET['key']}%')
																				  ) 
																				  ORDER BY DateIn DESC, PatientRecordID DESC
																			",$con),$multirows=true,$con);

} else {
	$date = date("Y-m-d", time());
	$records = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT 	pa_records.*, 
																			pa_info.*, 
																			in_name.InsuranceName,
																			COALESCE(pa_records.InsuranceCardID, pa_records.applicationNumber, '') AS InsuranceCardID
																			FROM pa_records, 
																			pa_info,
																			in_name
																			WHERE pa_info.PatientID=pa_records.PatientID &&
																				  pa_records.InsuranceNameID = in_name.InsuranceNameID &&
																				  in_name.InsuranceNameID = 1 && 
																				  pa_records.DateIn = '{$date}'
																				  ORDER BY DateIn DESC, PatientRecordID DESC
																			",$con),$multirows=true,$con);
}
	//echo $sql;
	if($records){
		$count = 0;
	?>
	<b class=visibl>
	<style>table#vsbl td, table#vsbl th{ font-size:10px; font-weight:bold; }</style>
	<span class=styling></span>
	<span class=track style='display:none;'></span>
	<table class=list id=vsbl border="1" style='width:100%;'>
		<tr><!--<th colspan=2>&nbsp;</th>--><th>#</th><th>Date</th><th>Card</th><th>Cat</th><th>Name</th><th>Age</th><th>Sex</th><th>House Manager</th><th>Family Code</th></tr>
		<?php
		$service = null;
		$k = 0;
		for($i=0;$i<count($records);$i++){

			/*if( ($i == count($records) - 1) && $k <= 20) {
				$i = 0;
				$k++;
			}*/
			
			$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.*, se_records.* FROM se_records, se_name WHERE se_records.PatientRecordID='{$records[$i]['PatientRecordID']}' && se_name.ServiceNameID = se_records.ServiceNameID",$con),$multirows=false,$con);
			$age = getAge($records[$i]["DateofBirth"],1,$records[$i]["DateIn"]);
			/*echo "<tr id='id{$i}'>
					
					<td>".($i+1)."</td>
					<td>{$records[$i]["DateIn"]}</td>
					
					<td class='pa_{$i}2' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}2\",\"{$records[$i]['InsuranceCardID']}\",\"pa_records\",\"{$records[$i]['PatientRecordID']}\",\"PatientRecordID\",\"InsuranceCardID\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["InsuranceCardID"]}</td>
					<td class='pa_{$i}10' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}10\",\"{$records[$i]['FamilyCategory']}\",\"pa_records\",\"{$records[$i]['PatientRecordID']}\",\"PatientRecordID\",\"FamilyCategory\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["FamilyCategory"]}</td>
					<td class='pa_{$i}3' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}3\",\"{$records[$i]['Name']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"Name\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Name"]}</td>
					<td class='pa_{$i}4' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}4\",\"{$records[$i]['DateofBirth']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"DateofBirth\",\"save_rqst/save_pa_info.php\");}'>{$age}</td>
					<td class='pa_{$i}5' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}5\",\"{$records[$i]['Sex']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"Sex\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["Sex"]}</td>
					<td class='pa_{$i}6' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}6\",\"{$records[$i]['FamilyCode']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"FamilyCode\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["FamilyCode"]}</td>
					<td class='pa_{$i}7' onclick='if($(\"#edit_mode\").val() == 0){ edit_function(\"pa_{$i}7\",\"{$records[$i]['HouseManagerID']}\",\"pa_info\",\"{$records[$i]['PatientID']}\",\"PatientID\",\"HouseManagerID\",\"save_rqst/save_pa_info.php\");}'>{$records[$i]["HouseManagerID"]}</td>
				</tr>";*/
			echo "<tr id='id{$i}'>
					
					<td>".($i+1)."</td>
					<td>{$records[$i]["DateIn"]}</td>
					
					<td>{$records[$i]["InsuranceCardID"]}</td>
					<td>{$records[$i]["FamilyCategory"]}</td>
					<td>{$records[$i]["Name"]}</td>
					<td>{$age}</td>
					<td>{$records[$i]["Sex"]}</td>
					<td>{$records[$i]["FamilyCode"]}</td>
					<td>{$records[$i]["HouseManagerID"]}</td>
				</tr>";
		}
		/*if(count($records) == 1){
			?>
			<script>
				
					//receivePatient("<?= $records[0]["PatientRecordID"] ?>","<?= $records[0]["DocID"] ?>","cpc",true);
					receivePatient("<?= $records[0]["PatientRecordID"] ?>","<?= $records[0]["DocID"] ?>","<?= $service['ServiceCode'] ?>",true);
			</script>
			<?php
		}*/
		?>
	<table></b>
	<?php
} else{
	echo "<span class=error-text>No Match Found</span>";
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