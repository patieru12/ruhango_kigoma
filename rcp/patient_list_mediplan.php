<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
if(!@$_GET['key']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}
$select = "";
$post = "";
$posts = explode("_", $_GET['post']);
//var_dump($posts);
$count = count($post);
$current = 1;
$sys = "("; $sys_s = 0;
$ok = false;
foreach($posts as $pst){
	$ps = returnSingleField($sql="SELECT CenterName FROM sy_center WHERE CenterID='{$pst}'",$field="CenterName",$data=true, $con);
	//var_dump($ps);
	if($ps != null){
		$ok = true;
		if($post && $current++ == $count)
			$post .= " And ";
		else
			$post .= " ";
		$post .= $ps;
		if($sys_s++ > 0)
			$sys .= " || ";
		$sys .= "sy_center.CenterName = '{$ps}'";
	}
}
$sys .= ")";
if(!$ok){
	echo "<span class=error>No Post Selected</span>";
	return;
}
//var_dump($_GET);
//echo $sys;
if(strlen($_GET['key'])){
	//select all possible information on the comming id
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientRecordID, pa_records.FamilyCategory, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.*, pa_insurance_cards.AffiliateNumber FROM pa_insurance_cards, pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center WHERE pa_insurance_cards.PatientID = pa_info.PatientID && pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && pa_records.InsuranceNameID='{$_GET['key']}' && DateIn LIKE('{$_GET['year']}-{$_GET['month']}-{$_GET['day']}') ORDER BY pa_records.DateIn ASC, pa_records.PatientRecordID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 

//echo $sql;
if($patients){
	?>
	<b class=visibl>
	<span class=success style='font-weight:bold; font-size:20px;'>
	MMI Patient List<br />
	Post: <?= $post ?><br />
	Date: <?= $_GET['day']."/".$_GET['month']."/".$_GET['year'] ?><br />
	Number: <?= count($patients) ?> Patient<?= count($patients)>1?"s":"" ?> 
	</span>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
	</style>
	<span class=styling></span>
	<div style='max-height:350px; border:0px solid #000; overflow:auto;'>
	<table class=list id=vsbl border="1" style='width:40%; font-size:30px;'>
		<tr><th>ID</th><th>Affiliation Number</th><th>Name</th><th>Age</th><th>Sex</th></tr>
		<?php
		$t = array();
		$kk_ = "";
		$data = array();
		$data[] = array("Nr","Insurance","Age","Sex","Name","House Holder","Family Code","Cons Cost","Lab","Imaging","Hosp","Procedures","Consumables","Drugs","Total","Detterent","Total to be paid");
		for($i=0;$i<count($patients);$i++){
			$r = $patients[$i];
			$ddd = array();
			$kk_ .=  "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
				<td>".($i+1)."</td>
				<td class='edit{$i}3' onclick='if($(\"#edit_mode\").val() == 0){edit_function(\"edit{$i}3\",\"{$r['InsuranceCardID']}\",\"pa_records\",\"{$r['PatientRecordID']}\",\"PatientRecordID\",\"InsuranceCardID\");}'>{$r["InsuranceCardID"]}</td>
				
				<td>{$patients[$i]["Name"]}</td>
				<td>".($patients[$i]["DateofBirth"] == "0000-00-00"?"":$patients[$i]["DateofBirth"])."</td>
				<td>{$patients[$i]["Sex"]}</td>";
				
				$ddd[] = ($i+1);
				$ddd[] = $r["InsuranceCardID"];
				$ddd[] = $r["Name"];
				$ddd[] = $r["DateofBirth"];
				$ddd[] = $r["Sex"];
				
			$kk_ .= "</tr>";
			$data[] = $ddd;
		}
		echo $kk_;
		?>
	</table>
	</div>
	</b>
	<?php
	if(count($data)>0){
		$_SESSION['report'] = $data;
		$_SESSION['header'] = array(
									array("PROVINCE / MVK"=>"SUD"),
									array("ADMINISTRATIVE DISTRICT"=>"HUYE", "Period"=>$_GET['month']."/".$_GET['year']),
									array("ADMINISTRATIVE SECTION"=>"HUYE"),
									array("HEALTH FACILITY"=>"BUSORO HEALTH CENTER"),
									array("CODE / HEALTH FACILITY"=>"40211013"),
								);
		$_SESSION['report_title'] = array("title"=>"MEDIPLAN PATIENT LIST");
		?>
		<style>
			.img_links{
				height:40px; 
				width:40px; 
				cursor:pointer;
			}
			.img_links:hover{
				/* height:37px;  */
				border-bottom:3px solid red;
			}
		</style>
		<a href='./print_report.php?format=pdf' onclick='alert("Sorry Not Availabel"); return false;' target="_blank"><img title='View in PDF' src="../images/b_pdfdoc.png" class=img_links width=30px /></a>
		<a href='./print_report.php?format=excel&in=cbhi' onclick='alert("Sorry Not Availabel"); return false;' target="_blank"><img title="View in EXCEL" src="../images/excel.png" class=img_links width=40px /></a>
		<?php
	}
	//echo "<pre>";var_dump($data);
/* } else{
	
	echo "<span class=error-text>No Match Found</span>";
}
 */
} else{
	echo "<span class=error-text>No Patient in the selected month {$_GET['day']}/{$_GET['month']}/{$_GET['year']} at selected station {$post}</span>";
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
<div id=upload_out></div><!--
<form action="upload_patient.php" id=upload_patient enctype='multipart/form-data' method=post>
	<input type=hidden value='<?= $_GET['key'] ?>' name=insurance_id />
	<input type=file name=patient_list class=txtfield1 id=excel_file style='width:300px;' />
</form>-->