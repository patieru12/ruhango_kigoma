<?php
session_start();
//var_dump($_SESSION);

require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
/*var_dump($_GET);
if(!@$_GET['ins']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}*/
$select = "";
//var_dump($_GET);

// if(strlen($_GET['key']) > (@$_GET['ins'] == 1?11:($_GET['ins'] == 3?6:3))){
	//select all possible information on the comming id
	//$records = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientID, pa_records.InsuranceCardID from pa_records WHERE InsuranceCardID LIKE('%{$_GET['key']}%') && InsuranceNameID='{$_GET['ins']}' ORDER BY pa_records.InsuranceCardID ASC Limit 0, 20 ",$con),$multirows=true,$con);
	//echo $sql;
	//var_dump($records);

//select all patience related to the found key search  FROM  WHERE 
//$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.InsuranceCardID, pa_records.FamilyCategory, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_records.PatientID = pa_info.PatientID&& pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.InsuranceCardID LIKE('%{$_GET['key']}%') && pa_records.InsuranceNameID='{$_GET['ins']}' ORDER BY InsuranceCardID ASC LIMIT 0, 10",$con),$multirows=true,$con);
$patients = $_SESSION['patient_records'];

if($patients){
	//echo count($patients);
	?>
	<b class=visibl>
	<style>table#vsbl td, table#vsbl th{font-size:10px;} table tr.doubled{ font-weight:bold; background-color:#a66; color:#fff; }</style>
	<span class=styling></span>
	<table class=list id=vsbl border="1" style='width:95%; font-size:30px; margin-top:20px; margin-left:15px;'>
		<tr><!--<th>Date</th>--><th>Assurance</th><th>Nom</th><th>Age</th><th>Sex</th><th>Numero de Police</th><th>Phone Number</th><th>Secteur</th><th>Cellure</th><th>Village</th></tr>
		<?php
		$printed_cards = array();
		for($i=0;$i<count($patients);$i++){
			//ad the village name to patient data
			/* $village = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ad_village.VillageName, ad_village.ViillageID from ad_village WHERE ad_village.ViillageID ='{$patients[$i]["VillageID"]}'",$con),$multirows=false,$con);
			$patients[$i]['VillageName'] = $village['VillageName'];
			 */
			$doc_id = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.* from pa_records WHERE pa_records.PatientID='{$patients[$i]["PatientID"]}' && DateIn != '0000-00-00' && DateIn < '{$patients[$i]["dateRequest"]}' ORDER BY DateIn DESC LIMIT 0,1",$con),$multirows=false,$con);
			//var_dump($doc_id);
			$form = returnSingleField($sql="SELECT in_forms.*, in_name.InsuranceName FROM in_name,in_category,in_price,in_forms WHERE in_name.CategoryID= in_category.InsuranceCategoryID && in_forms.InsuranceNameID = in_name.InsuranceNameID && in_price.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceNameID='{$doc_id['InsuranceNameID']}'","InsuranceName",$data=true, $con);
			$frm = returnSingleField($sql="SELECT in_forms.*, in_name.InsuranceName FROM in_name,in_category,in_price,in_forms WHERE in_name.CategoryID= in_category.InsuranceCategoryID && in_forms.InsuranceNameID = in_name.InsuranceNameID && in_price.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceNameID='{$doc_id['InsuranceNameID']}'","FormFile",$data=true, $con);
			//var_dump($form);
			//get the last service now
			$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name, se_records WHERE se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID='{$doc_id["PatientRecordID"]}'",$con),$multirows=false,$con);
			//var_dump($patients);
			$es = ""; $pst_search = ""; $pst_add = ""; $fcat = "";
			$rama = "";
			if($form == "CBHI"){
				$fcat .= <<<CAT
					$("#fcategory").val("{$patients[$i]["FamilyCategory"]}");
					$("#cbhi_category{$patients[$i]["FamilyCategory"]}").prop("checked", "true");
CAT;
			}
			if($form == "RAMA"){
				$rama = <<<RAMA
					$("#fcategory").val("{$patients[$i]["AffiliateNumber"]}");
					$("#location").val("{$patients[$i]["Affectation"]}");
RAMA;
			}
			if($form == "CBHI" && $service['ServiceCode'] == "PST"){
				//search for other previous record in the pst_records table now
				$pst_id = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pst_records.* from pst_records WHERE pst_records.PatientID='{$patients[$i]["PatientID"]}' ORDER BY PSTRecordID DESC LIMIT 0,1",$con),$multirows=false,$con);
				if($pst_id){
					$next = $pst_id['Frequency'] + 1;
					$up_ = ($next % 10) == 1?"<sup>st</sup>":(($next % 10) == 2?"<sup>nd</sup>":(($next % 10) == 3?"<sup>rd</sup>":("<sup>th</sup>")));
					
$pst_add = <<<P
			 
			 $("#weight").val("{$doc_id["Weight"]}");
			 $("#temperature").val("{$doc_id["Temperature"]}");
			 $("#document_list").val("{$pst_id['DocIDs']};");
P;
					if($next <= 5){
$pst_add .= <<<P
				$("#not_paid").attr("checked",":true");
				$("#next_frequency").val("{$next}");
				$("#document_list").val("{$pst_id['DocIDs']};");
				$("#steps").html("{$next}{$up_} Time");
P;
					} else{
						
$pst_add .= <<<P
				$("#paid").attr("checked",":true");
				$("#next_frequency").val("1");
				$("#document_list").val("");
				$("#steps").html("1<sup>st</sup> Time");
P;
					}
				}
			}
			if($doc_id)
			$es = <<<STR
			 $(".found_link").html("<span class=error>Existing Created On {$doc_id['DateIn']} <a href=\"../forms/{$frm}?records={$doc_id['PatientRecordID']}\" target=\"_blank\">Open It?</a> {$pst_search}</span>");
STR;
			$str = <<<STR
			 $("#patient_search").val("{$patients[$i]["InsuranceCardID"]}");
			 $("#pa").val("{$patients[$i]["InsuranceCardID"]}");
			 {$es}
			 $("#name").val("{$patients[$i]["Name"]}");
			 $("#patient_id").val("{$patients[$i]["PatientID"]}");
			 $("#age").val("{$patients[$i]["DateofBirth"]}");
			 $("#father").val("{$patients[$i]["FamilyCode"]}");
			 $("#phonenumber").val("{$patients[$i]["phoneNumber"]}");
			 
			 $("#update_").val("1");
			 $("#update").attr("checked",":true");
			 if("{$patients[$i]["Sex"]}" == "Male" || "{$patients[$i]["Sex"]}" == "M"){
				$("#male").attr("checked",":true");
			 }
			 if("{$patients[$i]["Sex"]}" == "Female" || "{$patients[$i]["Sex"]}" == "F"){
				$("#female").attr("checked",":true");
			 }
			 $("#district").val("{$patients[$i]["DistrictName"]}");
			 $("#sector").val("{$patients[$i]["SectorName"]}");
			 $("#cell").val("{$patients[$i]["CellName"]}");
			 $("#village").val("{$patients[$i]["VillageName"]}");


			 $("#weight").val("{$patients[$i]["PatientWeight"]}");
			 $("#temperature").val("{$patients[$i]["PatientTemperature"]}");
			 $("#length").val("{$patients[$i]["PatientLngth"]}");
			 $("#muac").val("{$patients[$i]["PatientMuac"]}");
			 
			 {$pst_add}
			 {$rama}
			 {$fcat}
			 setTimeout(function(){ $(".close").click();},100);
STR;
			if(!in_array($patients[$i]["PatientID"],$printed_cards)){
				echo "<tr ".($patients[$i]["DateIn"] == date("Y-m-d",(time() - (60*60*24)))?"class=doubled":"")." onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\"); {$str}' id='id{$i}'><!--<td>{$patients[$i]["PatientID"]}</td>--><td>{$patients[$i]["InsuranceCardID"]}</td><td>{$patients[$i]["Name"]}</td><td>{$patients[$i]["DateofBirth"]}</td><td>{$patients[$i]["Sex"]}</td><td>{$patients[$i]["FamilyCode"]}</td><td>{$patients[$i]["phoneNumber"]}</td><!--<td>{$patients[$i]["DistrictName"]}</td>--><td>{$patients[$i]["SectorName"]}</td><td>{$patients[$i]["CellName"]}</td><td>{$patients[$i]["VillageName"]}</td></tr>";
				$printed_cards[] = $patients[$i]["PatientID"];
			}
		}
		unset($printed_cards);
		?>
	<table></b>
	<script>
		//$("#display_now").click();
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
/*
} else{
	echo "Searching....";
}*/
?>