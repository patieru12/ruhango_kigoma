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

$date = $_POST['ResultDate'];

//var_dump($_POST);

//select all result corresponding to the selected month and exams
$sql = "SELECT la_records.*, pa_records.DocID, pa_info.*, ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName FROM la_records, `la_price`, la_exam, co_records, pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE la_exam.`ExamID` = '{$examId}' && la_exam.ExamID = la_price.ExamID && la_price.ExamPriceID = la_records.ExamPriceID && la_records.ResultDate LIKE('{$_POST['ResultDate']}%') && la_records.ConsultationRecordID=co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_info.VillageID = ad_village.ViillageID && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID ORDER BY ResultNumber ASC";
$exam_price_ids = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

?>
<div style='max-height:430px;' id='note' >
	<span>Result Founded << <label style='font-weight: bold;'><?= count($exam_price_ids) ?></label> >> on Exam-code:<label style='font-weight: bold;'><?php echo returnSingleField("SELECT * FROM `la_exam` WHERE `ExamID` = '{$examId}' ",'ExamCode'); ?></label> Date:<label style='font-weight: bold;'><?= $date ?></label></span>
</div>
<div style='margin-top: 10px;max-height:310px; overflow:auto;'>
<table class='table tables' border='1' style='width: 100%;text-align: center; font-size:12px;' >
	<thead style='font-weight: bold;'>
		<th>Number</th>
		<th>DocID</th>
		<th>Date</th>
		<th>Name</th>
		<th>Age</th>
		<th>Sex</th>
		<th>District</th>
		<th>Sector</th>
		<th>Cell</th>
		<th>Village</th>
		<th class='th-r'>Result</th>
	</thead>
	<tbody>
		<?php
		$s = 0; $result_summary = array();
		if($exam_price_ids)
		foreach ($exam_price_ids as $get){
		?>
			<tr class='tr-p' >
				<td style='text-align:right;'><?= /* substr( */$get['ResultNumber']/* ,6) */ ?></td>
				<td><?= @$get['DocID'] ?></td>
				<td><?= @$get['ResultDate'] ?></td>
				<td><?= $get['Name'] ?></td>
				<td title='<?= $get['DateofBirth'] ?>'><?= getAge($get['DateofBirth'],2) ?></td>
				<td><?= $get['Sex'] ?></td>
				<td><?= $get['DistrictName'] ?></td>
				<td><?= $get['SectorName'] ?></td>
				<td><?= $get['CellName'] ?></td>
				<td><?= $get['VillageName'] ?></td>
				<td class='td-r'>
					<?php
					//select the result assigned to the given result
					$sql = "SELECT la_result.ResultName FROM la_result, la_result_record WHERE la_result.ResultID = la_result_record.ResultID && la_result_record.ExamRecordID = '{$get['ExamRecordID']}'";
					//echo $sql;
					$array = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
					$str = "";
					//var_dump($array);
					foreach($array as $result){
						if(!@$result_summary[trim($result['ResultName'])])
							$result_summary[trim($result['ResultName'])] = 1;
						else
							$result_summary[trim($result['ResultName'])] += 1;
					}
					arraytostring($array, $str);
					echo $str;
					
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
</table>
</div>
	<?php
		//display the result summary now
		$sum = 0;
		if(count($result_summary) > 0){
			echo "<table class=sm>";
				foreach($result_summary as $rs=>$rn){
					//var_dump($rs);
					if(preg_match("/tropho/",strtolower($rs)))
						$sum += $rn;
					echo "<tr><td>{$rs}</td><td class=right>{$rn}</td></tr>";
				}
			echo "</table>";
		}
	?>
	<script>
		$(".ge_summary").html("<?= $sum ?>");
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