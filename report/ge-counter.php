<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
echo "<script>window.location='../logout.php';</script>";
return;
}
if (!isset($_POST['ExamID']) OR !isset($_POST['ResultDate']) ) {
	echo "<script>window.location='./daily.php';</script>";
	return;
}

function age($date='')
{
	if ($date != "") {
		$ex = explode('-', $date);
		$year = $ex[0];
		$now = date("Y",time());
		return ($now - $year );
	}
	else return "";
}

//Get all price with ExamId 
$examId = $_POST['ExamID'];
$examIdTdr = $_POST['TDRID'];

$date = $_POST['ResultDate'];

//var_dump($_POST);

//select all result corresponding to the selected month and exams
$sql = "SELECT la_records.*, pa_records.DocID, pa_info.*, ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName FROM la_records, `la_price`, la_exam, co_records, pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE la_exam.`ExamID` = '{$examId}' && la_exam.ExamID = la_price.ExamID && la_price.ExamPriceID = la_records.ExamPriceID && la_records.ResultDate LIKE('{$_POST['ResultDate']}%') && la_records.ConsultationRecordID=co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_info.VillageID = ad_village.ViillageID && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID ORDER BY ResultNumber ASC";
$exam_price_ids = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
$sql = "SELECT la_records.*, pa_records.DocID, pa_info.*, ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName FROM la_records, `la_price`, la_exam, co_records, pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE la_exam.`ExamID` = '{$examIdTdr}' && la_exam.ExamID = la_price.ExamID && la_price.ExamPriceID = la_records.ExamPriceID && la_records.ResultDate LIKE('{$_POST['ResultDate']}%') && la_records.ConsultationRecordID=co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_info.VillageID = ad_village.ViillageID && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID ORDER BY ResultNumber ASC";
$exam_price_tdr = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

		$s = 0; $result_summary = array(); $tdr_total = 0;
		if($exam_price_ids)
		foreach ($exam_price_ids as $get){
		?>
			
					<?php
					//select the result assigned to the given result
					$sql = "SELECT la_result.ResultName FROM la_result, la_result_record WHERE la_result.ResultID = la_result_record.ResultID && la_result_record.ExamRecordID = '{$get['ExamRecordID']}'";
					//echo $sql;
					$array = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
					$str = "";
					if($array){
					//var_dump($array);
					foreach($array as $result){
						if(!@$result_summary[trim($result['ResultName'])])
							$result_summary[trim($result['ResultName'])] = 1;
						else
							$result_summary[trim($result['ResultName'])] += 1;
							
						//if the result is like negatif search for tdr
						if(preg_match("/neg/",strtolower($result['ResultName']))){
							//$tdr_total += 1;
							$sql = "SELECT la_result.ResultName 
FROM 
la_records, 
co_records, 
la_result_record, 
la_result
WHERE 

la_records.ConsultationRecordID = co_records.ConsultationRecordID &&
la_records.ExamRecordID = la_result_record.ExamRecordID &&
la_result_record.ResultID = la_result.ResultID &&
la_result.ExamID = '{$examIdTdr}' && 
la_records.ResultDate LIKE('{$_POST['ResultDate']}%') && 
la_records.ConsultationRecordID='{$get['ConsultationRecordID']}' ORDER BY ResultNumber ASC";
							
							$exam_price_tdr = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
							if($exam_price_tdr){
								//echo $sql;
								foreach($exam_price_tdr as $td_a){
									//var_dump($td_a);
									if(preg_match("/pos/",strtolower($td_a['ResultName']))){
										$tdr_total++;
										//echo $td_a['ResultName'];
									}
								}
							}
						}
					}
					}
		}
		
		?>
	<?php
		//display the result summary now
		$sum = 0;
		if(count($result_summary) > 0){
			//echo "<table class=sm>";
				foreach($result_summary as $rs=>$rn){
					//var_dump($rs);
					if(!preg_match("/neg/",strtolower($rs)))
						$sum += $rn;
					//echo "<tr><td>{$rs}</td><td class=right>{$rn}</td></tr>";
				}
			//echo "</table>";
		}
		
		//update The database of malaria records
		if($malariaid = returnSingleField("SELECT MalariaID FROM la_malaria WHERE Date='{$date}'","MalariaID",true,$con)){
			saveData("UPDATE la_malaria SET LaboData='".($sum + $tdr_total)."' WHERE MalariaID='{$malariaid}'",$con);
		} else{
			$malariaid = saveAndReturnID("INSERT INTO la_malaria SET LaboData='".($sum + $tdr_total)."', Date='{$date}'",$con);
		}
		saveData("UPDATE la_malaria SET `Ge Pos`='".($sum)."' WHERE MalariaID='{$malariaid}'",$con);
		saveData("UPDATE la_malaria SET `Ge Neg & TDR Pos`='".($tdr_total)."' WHERE MalariaID='{$malariaid}'",$con);
		$sum_tdr = 0;
	?>
	<script>
		$(".ge_summary").html("<?= $sum ?>");
		$(".tdr_summary").html("<?= $tdr_total ?>");
		$(".care_labo_summary").html("<?= ($sum + $tdr_total) ?>");
	</script>
<style type="text/css">
	.table{
		table-layout: inherit;
	}
	.tr-p td {
		padding: 5px;
	}
	.sm tr td.right{
		text-align:right;
	}
	.td-r,.th-r{
		text-align: left;
		padding-left: 10px;
	}
}
</style>