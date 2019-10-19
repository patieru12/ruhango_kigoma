<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("adm" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
if(!@$_GET['focus'])
	$_GET['focus'] = 'dob';

if(!@$_GET['key']){
	echo "<span class=error-text>Select Insurance</span>";
	return;
}
$select = "";
//var_dump($_GET);
if(strlen($_GET['key'])){


$sp_condition = "";
if(@$_GET['dob']){
	$sp_condition .= " && pa_info.DateOfBirth='".(PDB($_GET['dob'],true, $con))."'";
}

if(@$_GET['datein']){
	$sp_condition .= " && pa_records.DateIn='".(PDB($_GET['datein'],true, $con))."'";
}

if(@$_GET['vill']){
	$sp_condition .= " && ad_village.VillageName='".(PDB((strtolower($_GET['vill'])=="empty"?"":$_POST['vill']),true, $con))."'";
}

if(@$_GET['cell']){
	$sp_condition .= " && ad_cell.CellName='".(PDB((strtolower($_GET['cell'])=="empty"?"":$_POST['cell']),true, $con))."'";
}
//select patient that match the given condition and prepare the elimination for the system 
//echo $sp_condition;
//select all patience related to the found key search
$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientRecordID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* from pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.PatientID = pa_info.PatientID && pa_records.InsuranceNameID='".PDB($_GET['key'],true,$con)."' {$sp_condition} ORDER BY Name ASC LIMIT 0,100;",$con),$multirows=true,$con);

//var_dump($patients);
//echo $sql;
if($patients){
	?>
	<b class=visibl>
	<style>table#vsbl td, table#vsbl th{font-size:12px;}</style>
	<span class=styling></span>
	Delete Patient From System WHERE
	<input type=text id=dob class=txtfield1 placeholder="Date of Birth Condition" style='width:140px; font-size:12px;' name='date_of_birth' value='' /> OR 
	<input type=text class=txtfield1 style='width:120px; font-size:12px;' id=datein placeholder="Date In Condition" name='date_in' value='' /> OR 
	<input type=text class=txtfield1 placeholder="Village Condition" id=vill style='width:100px; font-size:12px;' name='date_in' value='' /> OR 
	<input type=text class=txtfield1 placeholder="Cell Condition" id=cell style='width:100px; font-size:12px;' name='date_in' value='' />
	<input type=button id=delete  name=delete value=Delete style='font-size:12px;' class='flatbtn' />
	<div class=delete_process></div>
	<div style='max-height:400px; overflow:auto'>
	<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
		<tr><th></th><th>Assurance</th><th>Non</th><th>Age</th><th>Sex</th><th>Chef de Famille</th><th>District</th><th>Secteur</th><th>Cellure</th><th>Village</th></tr>
		<?php
		for($i=0;$i<count($patients);$i++){
			$r = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.* from pa_records WHERE pa_records.PatientID='{$patients[$i]["PatientID"]}' ORDER BY DateIn DESC LIMIT 0,1",$con),$multirows=false,$con);
			$str = <<<STR
			 $("#patient_search").val("{$r["InsuranceCardID"]}");
			 $("#name").val("{$patients[$i]["Name"]}");
			 $("#patient_id").val("{$patients[$i]["PatientID"]}");
			 $("#age").val("{$patients[$i]["DateofBirth"]}");
			 $("#father").val("{$patients[$i]["FamilyCode"]}");
			 $("#update").attr("checked",":true");
			 if("{$patients[$i]["Sex"]}" == "Male"){
				$("#male").attr("checked",":true");
			 }
			 if("{$patients[$i]["Sex"]}" == "Female"){
				$("#female").attr("checked",":true");
			 }
			 $("#district").val("{$patients[$i]["DistrictName"]}");
			 $("#sector").val("{$patients[$i]["SectorName"]}");
			 $("#cell").val("{$patients[$i]["CellName"]}");
			 $("#village").val("{$patients[$i]["VillageName"]}");
STR;
			echo "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\"); {$str}' id='id{$i}'><td>{$i}</td><td>{$r["InsuranceCardID"]}</td><td>{$patients[$i]["Name"]}</td><td>{$patients[$i]["DateofBirth"]}</td><td>{$patients[$i]["Sex"]}</td><td>{$patients[$i]["FamilyCode"]}</td><td>{$patients[$i]["DistrictName"]}</td><td>{$patients[$i]["SectorName"]}</td><td>{$patients[$i]["CellName"]}</td><td>{$patients[$i]["VillageName"]}</td></tr>";
		}
		?>
	</table>
	</div>
	</b>
	<?php
} else{
	
	echo "<span class=error-text>No Match Found</span>";
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
		
		$("#<?= @$_GET['focus'] ?>").focus();
		$("#dob").val("<?= @$_GET['dob'] ?>");
		$("#datein").val("<?= @$_GET['datein'] ?>");
		$("#vill").val("<?= @$_GET['vill'] ?>");
		
		$("#cell").val("<?= @$_GET['cell'] ?>");
		
		$("#dob").keyup(function(e){
			pattern = /^[0-9]{4}-[0-9]{2}-[0-9]{2}/
			if($("#dob").val().trim() != "" && pattern.test($("#dob").val())){
				/* alert("DOB FOUND"); */
				$(".patient_found").load("mtn_patient.php?key=<?= $_GET['key'] ?>&dob=" + $("#dob").val() + "&datein=" + $("#datein").val() + "&vill=" + $("#vill").val().replace(/ /g,"%20") + "&cell=" + $("#cell").val().replace(/ /g,"%20") + "&focus=dob");
			}
		});
		
		$("#datein").keyup(function(e){
			pattern = /^[0-9]{4}-[0-9]{2}-[0-9]{2}/
			if($("#datein").val().trim() != "" && pattern.test($("#datein").val())){
				/* alert("DOB FOUND"); */
				$(".patient_found").load("mtn_patient.php?key=<?= $_GET['key'] ?>&dob=" + $("#dob").val() + "&datein=" + $("#datein").val() + "&vill=" + $("#vill").val().replace(/ /g,"%20") + "&cell=" + $("#cell").val().replace(/ /g,"%20") + "&focus=datein");
			}
		});
		
		
		var sent = false;
		$("#vill").keyup(function(e){
			/* if(sent)
				return e.preventDefault();
			 */
			pattern = /^[0-9]{4}-[0-9]{2}-[0-9]{2}/
			if($("#vill").val().trim()){
				/* alert("DOB FOUND"); */
				if(!sent){
				sent = true;
				setTimeout(function(){
					$(".patient_found").load("mtn_patient.php?key=<?= $_GET['key'] ?>&dob=" + $("#dob").val() + "&datein=" + $("#datein").val() + "&vill=" + $("#vill").val().replace(/ /g,"%20") + "&cell=" + $("#cell").val().replace(/ /g,"%20") + "&focus=vill");
					
					},2000);
				}
			}
		});
		
		$("#cell").keyup(function(e){
			/* if(sent)
				return e.preventDefault();
			 */
			pattern = /^[0-9]{4}-[0-9]{2}-[0-9]{2}/
			if($("#cell").val().trim()){
				/* alert("DOB FOUND"); */
				if(!sent){
				sent = true;
				setTimeout(function(){
					$(".patient_found").load("mtn_patient.php?key=<?= $_GET['key'] ?>&dob=" + $("#dob").val() + "&datein=" + $("#datein").val() + "&vill=" + $("#vill").val().replace(/ /g,"%20") + "&cell=" + $("#cell").val().replace(/ /g,"%20") + "&focus=cell");
					
					},2000);
				}
			}
		});
		
		$("#delete").click(function(e){
			//alert(e.toString());
			$(".delete_process").html("<img src='../images/loading.gif' style='position:relative; top:-45px; left:84%;' />");
			str = "delete=<?= $_GET['key'] ?>";
			pattern = /^[0-9]{4}-[0-9]{2}-[0-9]{2}/
			if($("#dob").val().trim() != "" && pattern.test($("#dob").val())){
				str = str + "&dob=" + $("#dob").val();
			}
			if($("#datein").val().trim() != "" && pattern.test($("#datein").val())){
				str = str + "&datein=" + $("#datein").val();
			}
			if($("#vill").val().trim() != ""){
				str = str + "&vill=" + $("#vill").val();
			}
			if($("#cell").val().trim() != ""){
				str = str + "&cell=" + $("#cell").val();
			}
			$.ajax({
				type: "POST",
				url: "./delete_patient.php",
				data: str,
				cache: false,
				success: function(result){
					$(".delete_process").html(result);
				}
			});
		});
	});
</script>
<div id=upload_out></div>
