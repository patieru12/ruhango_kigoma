<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

?>
<div style='border:0px solid #000; max-height:1px;'>
<img src='../images/close.png' class='close'>
</div>
<?php
//var_dump($_POST);
$sql = "SELECT DISTINCT la_records.*, la_exam.ExamName, la_exam.ExamID, pa_info.Name, pa_records.DocID, ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName
			
		FROM 
			la_records, co_records, pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district
			,la_exam, la_price
		WHERE
			la_records.ConsultationRecordID = co_records.ConsultationRecordID &&
			co_records.PatientRecordID = pa_records.PatientRecordID &&
			pa_records.PatientID = pa_info.PatientID &&
			pa_info.VillageID = ad_village.ViillageID &&
			ad_village.CellID = ad_cell.CellID &&
			ad_cell.SectorID = ad_sector.SectorID &&
			ad_sector.DistrictID = ad_district.DistrictID &&
			la_records.ExamPriceID = la_price.ExamPriceID &&
			la_price.ExamID = la_exam.ExamID &&
			la_records.ExamRecordID = '{$_POST['exam_record_id']}'
";
//echo $sql;

$patient = formatResultSet($rslt = returnResultSet($sql,$con),$multirows=false,$con);
if($patient){
	?>
	<div class=result_now></div>
	Name:<?= $patient['Name'] ?><br />
	DocID:<?= $patient['DocID'] ?><br />
	Exam:<?= $patient['ExamName'] ?><br />
		Result List
		<table border=0 style='width:100%'>
			<tr>
				<td>
					<textarea style='width:100%' placeholder='Result List Here' id='result_list'></textarea>
				</td>
				<td style='vertical-align:top; text-align:center; width:20px; padding:2px;'>
					<button title='save' id=save class='flatbtn-blu' style='font-size:10px; margin-top:2px;'>Save</button><br />
					<button title='save and close the box' id=save_close class='flatbtn' style='font-size:10px; margin-top:5px;'>Save</button>
				</td>
			</tr>
		</table>
	<div class='ads_result_' style='border:0px solid #000; max-height:150px; overflow:auto;'>
		
	</div>
	<?php
	}
?>
<style>
	.ad_result{
		position:absolute;
		top:30%;
		left:30%;
		border:5px solid #555;
		border-radius:10px;
		background-color:#ffffff;
		width:400px;
		height:300px;
		padding:10px;
		font-size:14px; 
		font-weight:bold;
	}
	.close{
		position:relative;
		top:-30px;
		left:99%;
		cursor:pointer;
	}
	
</style>

<script>
	$(document).ready(function(e){
		$(".close").click(function(e){
			$(".ad_result").fadeOut(2000);
			//set the time to refresh the page now
			setTimeout(function(e){
				$("#refresh").click();
			},2000);
		});
		setTimeout(function(e){
			$(".ads_result_").load("./adds-on/ads_result_1.php?exam=<?= $patient['ExamName'] ?>");
		},500);
		$("#save").click(function(e){
			$.post(
				"./save_result.php",
				{
					"result":$("#result_list").val().trim(),
					"exam_id":"<?= $patient['ExamID'] ?>",
					"la_id":"<?= @$_POST['exam_record_id'] ?>",
					"activity":"save"
				},
				function(data){
					/* $(".error-text").fadeOut(000); */
					$(".result_now").html(data);
				}
			);
		});
		$("#save_close").click(function(e){
			$.post(
				"./save_result.php",
				{
					"result":$("#result_list").val().trim(),
					"exam_id":"<?= $patient['ExamID'] ?>",
					"la_id":"<?= @$_POST['exam_record_id'] ?>",
					"activity":"save_close"
				},
				function(data){
					/* $(".error-text").fadeOut(000); */
					$(".result_now").html(data);
				}
			);
		});
	});
</script>