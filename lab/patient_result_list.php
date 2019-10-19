<?php
session_start();
set_time_limit(5);
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//if the delete request is submitted delete the corresponding result
if(@$_POST['action'] == 'delete' && is_numeric($_POST['id'])){
	saveData("DELETE FROM la_result_record WHERE ExamRecordID='".PDB($_POST['id'],true,$con)."'",$con);
	saveData("DELETE FROM la_records WHERE ExamRecordID='".PDB($_POST['id'],true,$con)."'",$con);
}
//now try to filter
$sp_condition = "";
if(@$_POST['filter']){
	$sp_condition .= " && (
		pa_records.InsuranceCardID LIKE('%{$_POST['filter']}%') ||
		pa_info.Name LIKE('%{$_POST['filter']}%') ||
		pa_info.FamilyCode LIKE('%{$_POST['filter']}%')
	)";
}
//var_dump($_POST);
//select all taken samples to fill the corresponding data
$sql = "SELECT DISTINCT la_records.*, pa_info.Name, pa_records.InsuranceCardID, ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName
			
		FROM 
			la_records, co_records, pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district
			,la_exam, la_price
		WHERE
			la_records.ConsultationRecordID = co_records.ConsultationRecordID &&
			co_records.PatientRecordID = pa_records.PatientRecordID &&
			pa_records.PatientID = pa_info.PatientID &&
			la_records.ResultDate='{$_POST['ResultDate']}' &&
			pa_info.VillageID = ad_village.ViillageID &&
			ad_village.CellID = ad_cell.CellID &&
			ad_cell.SectorID = ad_sector.SectorID &&
			ad_sector.DistrictID = ad_district.DistrictID &&
			la_records.ExamPriceID = la_price.ExamPriceID &&
			la_price.ExamID = la_exam.ExamID &&
			la_exam.ExamID = '{$_POST['ExamID']}'
			{$sp_condition}
";
//echo $sql;

$patients = formatResultSet($rslt = returnResultSet($sql,$con),$multirows=true,$con);
//var_dump($patients)
if($patients){
	?>
	<script>
		$("#filter").click(function(e){
			//$(".patient_found").load("document_list_cbhi.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + prompt("Enter Filter Key",'<?= @$_GET['filter'] ?>').replace(/ /g,"%20"));
			$.post(
				"./patient_result_list.php",
				{
					"ExamID":"<?= @$_POST['ExamID'] ?>",
					"ResultDate":"<?= @$_POST['ResultDate'] ?>",
					"filter":prompt("Enter Filter Key",'<?= @$_POST['filter'] ?>').replace(/ /g,"%20")
				},
				function(data){
					/* $(".error-text").fadeOut(000); */
					$(".doc_selected").html(data);
				}
			);
			return e.preventDefault();
		});
		$("#filter_remove").click(function(e){
			$("#filter_").val("");
			//$(".patient_found").load("document_list_cbhi.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
			$.post(
				"./patient_result_list.php",
				{
					"ExamID":"<?= @$_POST['ExamID'] ?>",
					"ResultDate":"<?= @$_POST['ResultDate'] ?>",
					"filter":$("#filter_").val()
				},
				function(data){
					/* $(".error-text").fadeOut(000); */
					$(".doc_selected").html(data);
				}
			);
			return e.preventDefault();
		});
		$("#refresh").click(function(e){
			//$("#filter_").val("");
			//$(".patient_found").load("document_list_cbhi.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
			$.post(
				"./patient_result_list.php",
				{
					"ExamID":"<?= @$_POST['ExamID'] ?>",
					"ResultDate":"<?= @$_POST['ResultDate'] ?>",
					"filter":$("#filter_").val()
				},
				function(data){
					/* $(".error-text").fadeOut(000); */
					$(".doc_selected").html(data);
				}
			);
			return e.preventDefault();
		});
	</script>
	<div class=ad_result>
	</div>
	<div style='max-height:430px;' id='note' >
		<input type=hidden id=refresh />
		<span>Valid Sample << <label style='font-weight: bold;'><?= count($patients) ?></label> >> on Exam Name:<label style='font-weight: bold;'><?php echo $exam_name = returnSingleField("SELECT * FROM `la_exam` WHERE `ExamID` = '{$_POST['ExamID']}' ",'ExamName'); ?></label> Date:<label style='font-weight: bold;'><?= $_POST['ResultDate'] ?></label></span>
		<span style='float:right;'><?= @$_POST['filter']?"<script>$('#filter_').val('{$_POST['filter']}');</script><a href='#' id=filter_remove style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' ><img src='../images/filter_remove.png' /> Remove Filter</a>":"" ?><a href='#' id=filter style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' > <img src='../images/filter.png' /> Filter </a></span>
	</div>
	<?php
	echo "<table class=table style='width:100%'><tr><th></th><th>Exam</th><th>Name</th><th>Insurance Card</th><th>District</th><th>Sector</th><th>Cell</th><th>Village</th><th>Result List</th><th colspan=2></th></tr>";
	for($i=0;$i<count($patients);$i++){
		echo "<tr>";
		echo "<td>".($i + 1)."</td>";
		echo "<td>".($number = substr($patients[$i]['ExamNumber'],6))."</td>";
		echo "<td>{$patients[$i]['Name']}</td>";
		echo "<td>{$patients[$i]['InsuranceCardID']}</td>";
		echo "<td>{$patients[$i]['DistrictName']}</td>";
		echo "<td>{$patients[$i]['SectorName']}</td>";
		echo "<td>{$patients[$i]['CellName']}</td>";
		echo "<td>{$patients[$i]['VillageName']}</td>";
		//select all results assigned to this exam
		$result_s = formatResultSet($rslt=returnResultSet("SELECT ResultName FROM la_result, la_result_record WHERE la_result.ResultID = la_result_record.ResultID && la_result_record.ExamRecordID='{$patients[$i]['ExamRecordID']}'",$con),$multirows=true,$con);
		$string_data = "";
		//var_dump($result_s);
		arraytostring($result_s, $string_data, 0);
		echo "<td>{$string_data}</td>";
		echo "<td width=15px><img onclick='if(confirm(\"Delete {$exam_name} Sample for {$patients[$i]['Name']} Exam Number {$number}\")){deleteSample(\"{$patients[$i]['ExamRecordID']}\");}' src='../images/b_drop.png' width=20px /></td>";
		echo "<td width=15px><img onclick='if(confirm(\"Add Result of {$exam_name} for {$patients[$i]['Name']} Exam Number {$number}\")){addResult(\"{$patients[$i]['ExamRecordID']}\");}' title='Add Result' src='../images/256.png' width=20px /></td>";
		echo "</tr>";
	}
} else{
	echo "<span class=error-text>No Sample Found in The System on {$_POST['ResultDate']}</span>";
}
?>

<style type="text/css">
	.table{
		table-layout: inherit;
	}
	.table td, .table th{
		border:1px solid #000;
		padding:1px;
	}
	.tr-p td {
		padding: 5px;
	}
	.td-r,.th-r{
		text-align: left;
		padding-left: 10px;
	}

</style>
	<style>
		.td-r{
			text-align:center;
			cursor:pointer;
			color:green;
		}
		.td-r:hover{
			color:blue;
			text-decoration:underline;
		}
		.table tr:hover{
			background-color:#efefef;
			cursor:pointer;
		}
	</style>
<script>
	$(".ad_result").hide();
	function addResult(record_id){
		$(".ad_result").show(700);
		$.post(
			"./add_patient_result.php",
			{
				"exam_record_id":record_id
			},
			function(data){
				/* $(".error-text").fadeOut(000); */
				$(".ad_result").html(data);
			}
		);
	}
	function deleteSample(record_id){
		$(".ad_result").show(700);
		$.post(
			"./patient_result_list.php",
			{
				"ExamID":"<?= @$_POST['ExamID'] ?>",
				"ResultDate":"<?= @$_POST['ResultDate'] ?>",
				"filter":$("#filter_").val(),
				"action":"delete",
				"id":record_id
			},
			function(data){
				/* $(".error-text").fadeOut(000); */
				$(".doc_selected").html(data);
			}
		);
	}
</script>