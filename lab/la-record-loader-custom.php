<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
if(!preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$_POST['date1']) || !preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$_POST['date2'])){
	echo "<span class=error-text>Invalid Date</span>";
	return;
}
$header = "";
$query = ""; $query_counter = 0; $header_data=array();
$result_summary = array();
$examsRequestID = "";
$truncate = @$_POST['general_register']?1:0;
foreach($_POST as $examid=>$examname){
	if(is_numeric($examid)){
		if(strlen($examsRequestID) > 0){
			$examsRequestID .= ", ";
		}

		$examsRequestID .= $examid;

		if($truncate){
			$examname = substr($examname, 0, 20);
		}
		
		//add the exam in the header to be displayed
		$header .= "<th class='rotateData'" .($truncate?"text-rotate='90'":"").">{$examname}</th>";
		$header_data[] = $examid;
		$result_summary[$examid] = array();
	}
}
?>

<button class="flatbtn-blu" type="button" id="downloadReport"><i class="fa fa-download"></i> Download</button>

<span class="print_result"></span>
<div style='margin-top: 10px;' id="downloadableContent">
<?php
//die;
if(@$_POST['general_register']){
	// var_dump($_POST);
	$sql = "SELECT 	la_records.MonthlyID AS MonthlyID,
					GROUP_CONCAT(la_exam.ExamID) AS ExamID,
					la_records.GeneralID AS GeneralID,
					la_records.ResultNumber, 
					la_records.ResultDate,
					pa_records.DocID, 
					pa_records.DateIn, 
					pa_records.PatientRecordID, 
					pa_info.*, 
					ad_village.VillageName, 
					ad_cell.CellName, 
					ad_sector.SectorName, 
					ad_district.DistrictName,
					co_records.ConsultationRecordID,
					co_records.RegisterNumber,
					CONCAT(sy_register.name, ' ', sy_register.registerCode) AS registerName
					FROM la_records, 
					la_price, 
					la_exam, 
					co_records, 
					pa_records, 
					pa_info, 
					ad_village, 
					ad_cell, 
					ad_sector, 
					ad_district,
					sy_register
					WHERE la_exam.ExamID = la_price.ExamID && 
						  la_price.ExamPriceID = la_records.ExamPriceID && 
						  pa_records.DateIn >= '{$_POST['date1']}' && 
						  pa_records.DateIn <= '{$_POST['date2']}' && 
						  la_records.ConsultationRecordID=co_records.ConsultationRecordID && 
						  co_records.PatientRecordID = pa_records.PatientRecordID && 
						  pa_records.PatientID = pa_info.PatientID && 
						  pa_info.VillageID = ad_village.ViillageID && 
						  ad_village.CellID = ad_cell.CellID && 
						  ad_cell.SectorID = ad_sector.SectorID && 
						  ad_sector.DistrictID = ad_district.DistrictID && 
						  co_records.registerId = sy_register.id &&
						  la_records.MonthlyID IS NOT NULL
					GROUP BY PatientRecordID
					ORDER BY la_records.GeneralID ASC
				";
	// echo $sql;
	$exam_price_ids = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	?>
		<style type="text/css">
			.rotateData{
				/*transform: rotate(-90deg);*/
				width: 10px;
				height: 120px;
			}
		</style>
	<table class='table tables' border='1' style='width: 100%;text-align: center; font-size:12px;' >
		<thead style='font-weight: bold;'>
			<tr style="vertical-align: bottom;">
				<th>Number</th>
				<th>Register</th>
				<th>DocID</th>
				<th>Date</th>
				<th>Name</th>
				<th style='padding:0 10px;'>Age</th>
				<th>Sex</th>
				<th>District</th>
				<th>Sector</th>
				<th>Cell</th>
				<th>Village</th>
				<?= $header ?>
			</tr>
		</thead>
		<tbody>
			<?php
			$s = 0;
			if($exam_price_ids)
			foreach ($exam_price_ids as $get){
			?>
				<tr class='tr-p' >
					<td style='text-align:left;'><?= /* substr( */$get['GeneralID']/* ,6) */ ?></td>
					<td style='text-align:left;'><?= $get['RegisterNumber'] ." ". $get['registerName'] ?></td>
					<td style='text-align:left;'><?= $get['DocID'] ?></td>
					<td style='text-align:left;'><?= @$get['ResultDate'] ?></td>
					<td style="text-align: left;"><?= $get['Name'] ?></td>
					<td style='text-align:left;' title="DOB: <?= $get['DateofBirth'] ?>"><?= getAge($get['DateofBirth'],1,$get['DateIn']) ?></td>
					<td style='text-align:left;'><?= $get['Sex'] ?></td>
					<td style='text-align:left;'><?= $get['DistrictName'] ?></td>
					<td style='text-align:left;'><?= $get['SectorName'] ?></td>
					<td style='text-align:left;'><?= $get['CellName'] ?></td>
					<td style='text-align:left;'><?= $get['VillageName'] ?></td>
					<?php
					foreach($header_data as $exam_id){
						$str = ""; //$get['ConsultationRecordID'];
						?>
						<td class='td-r'>
							<?php
							//select the result assigned to the given result
							// $sql = "SELECT la_result.ResultName, la_result.Appear FROM la_result, la_result_record, la_records WHERE la_records.ExamRecordID = la_result_record.ExamRecordID && la_result.ResultID = la_result_record.ResultID && la_records.ResultDate = '{$get['ResultDate']}' && la_records.ResultNumber = '{$get['ResultNumber']}' && la_result.ExamID='{$exam_id}'";
							$sql = "SELECT 	a.ExamRecordID,
											d.ResultName,
											d.ResultID
											FROM la_records AS a
											INNER JOIN la_price AS b
											ON a.ExamPriceID = b.ExamPriceID
											INNER JOIN la_result_record AS c
											ON a.ExamRecordID = c.ExamRecordID
											INNER JOIN la_result AS d
											ON c.ResultID = d.ResultID
											WHERE a.ConsultationRecordID = '{$get['ConsultationRecordID']}' &&
												  a.MonthlyID = {$get['MonthlyID']} &&
												  b.ExamID = {$exam_id}
											";
							//echo $sql;
							//echo $exam_id.":".$get['ResultNumber'];
							$array = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
							if(is_array($array)){
								$str .= "<span class='fa fa-check'></span>";
							}
							echo $str;
							
							?>
						</td>
						<?php
					}
					?>
				</tr>
			<?php
			}
			?>
		</tbody>
	</table>
	<?php
	// die();
} else {
//select all result corresponding to the selected month and exams
$sql = "SELECT 	DISTINCT la_records.MonthlyID,
				la_records.ResultNumber, 
				la_records.ResultDate,
				pa_records.DocID, 
				pa_records.DateIn, 
				pa_records.PatientRecordID, 
				pa_info.*, 
				ad_village.VillageName, 
				ad_cell.CellName, 
				ad_sector.SectorName, 
				ad_district.DistrictName,
				co_records.ConsultationRecordID,
				co_records.RegisterNumber,
				CONCAT(sy_register.name, ' ', sy_register.registerCode) AS registerName
				FROM la_records, 
				la_price, 
				la_exam, 
				co_records, 
				pa_records, 
				pa_info, 
				ad_village, 
				ad_cell, 
				ad_sector, 
				ad_district,
				sy_register
				WHERE la_exam.ExamID = la_price.ExamID && 
					  la_price.ExamPriceID = la_records.ExamPriceID && 
					  pa_records.DateIn >= '{$_POST['date1']}' && 
					  pa_records.DateIn <= '{$_POST['date2']}' && 
					  la_records.ConsultationRecordID=co_records.ConsultationRecordID && 
					  co_records.PatientRecordID = pa_records.PatientRecordID && 
					  pa_records.PatientID = pa_info.PatientID && 
					  pa_info.VillageID = ad_village.ViillageID && 
					  ad_village.CellID = ad_cell.CellID && 
					  ad_cell.SectorID = ad_sector.SectorID && 
					  ad_sector.DistrictID = ad_district.DistrictID && 
					  co_records.registerId = sy_register.id &&
					  la_exam.ExamID IN({$examsRequestID}) &&
					  la_records.MonthlyID IS NOT NULL
				ORDER BY la_records.ResultDate ASC, 
						 MonthlyID ASC";
//echo $sql; die;
$exam_price_ids = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
?>
<table class='table tables' border='1' style='width: 100%;text-align: center; font-size:12px;' >
	<thead style='font-weight: bold;'>
		<th>Number</th>
		<th>Register</th>
		<th>DocID</th>
		<th>Date</th>
		<th>Name</th>
		<th style='padding:0 10px;'>Age</th>
		<th>Sex</th>
		<th>District</th>
		<th>Sector</th>
		<th>Cell</th>
		<th>Village</th>
		<?= $header ?>
	</thead>
	<tbody>
		<?php
		$s = 0;
		if($exam_price_ids)
		foreach ($exam_price_ids as $get){
		?>
			<tr class='tr-p' >
				<td style='text-align:left;'><?= /* substr( */$get['MonthlyID']/* ,6) */ ?></td>
				<td style='text-align:left;'><?= $get['RegisterNumber'] ." ". $get['registerName'] ?></td>
				<td style='text-align:left;'><?= $get['DocID'] ?></td>
				<td style='text-align:left;'><?= @$get['ResultDate'] ?></td>
				<td style='text-align:left;'><?= $get['Name'] ?></td>
				<td style='text-align:left;' title="DOB: <?= $get['DateofBirth'] ?>"><?= getAge($get['DateofBirth'],1,$get['DateIn']) ?></td>
				<td style='text-align:left;'><?= $get['Sex'] ?></td>
				<td style='text-align:left;'><?= $get['DistrictName'] ?></td>
				<td style='text-align:left;'><?= $get['SectorName'] ?></td>
				<td style='text-align:left;'><?= $get['CellName'] ?></td>
				<td style='text-align:left;'><?= $get['VillageName'] ?></td>
				<?php
				foreach($header_data as $exam_id){
					$str = ""; //$get['ConsultationRecordID'];
					?>
					<td style='text-align:left;' class='td-r'>
						<?php
						//select the result assigned to the given result
						// $sql = "SELECT la_result.ResultName, la_result.Appear FROM la_result, la_result_record, la_records WHERE la_records.ExamRecordID = la_result_record.ExamRecordID && la_result.ResultID = la_result_record.ResultID && la_records.ResultDate = '{$get['ResultDate']}' && la_records.ResultNumber = '{$get['ResultNumber']}' && la_result.ExamID='{$exam_id}'";
						$sql = "SELECT 	a.ExamRecordID,
										d.ResultName,
										d.ResultID
										FROM la_records AS a
										INNER JOIN la_price AS b
										ON a.ExamPriceID = b.ExamPriceID
										INNER JOIN la_result_record AS c
										ON a.ExamRecordID = c.ExamRecordID
										INNER JOIN la_result AS d
										ON c.ResultID = d.ResultID
										WHERE a.ConsultationRecordID = '{$get['ConsultationRecordID']}' &&
											  a.MonthlyID = {$get['MonthlyID']} &&
											  b.ExamID = {$exam_id}
										";
						//echo $sql;
						//echo $exam_id.":".$get['ResultNumber'];
						$array = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
						if(is_array($array)){
							foreach($array AS $r){
								if(strlen($str) > 0){
									$str .= ", ";
								}
								$str .= $r['ResultName'];

								if(isset($result_summary[$exam_id][$r['ResultID']])){
									$result_summary[$exam_id][$r['ResultID']]++;
								} else{
									$result_summary[$exam_id][$r['ResultID']] = 1;
								}
							}
						}
						echo $str;
						
						?>
					</td>
					<?php
				}
				?>
			</tr>
		<?php
		}
		?>
	</tbody>
	<tr>
		<td colspan=11></td>
		<?php
		if(count($result_summary) > 0){
			foreach($result_summary as $xamid=>$result_summary_data){
				echo "<th>";
				foreach($result_summary_data as $rs=>$rn){
					echo returnSingleField("SELECT ResultName FROM la_result WHERE ResultID='{$rs}'","ResultName",true,$con)." = ".number_format($rn)."<br />";
				}
				echo "</th>";
			}
		}
		?>
	</tr>
</table>
<?php
}
?>
</div>
<style type="text/css">
	.table{
		table-layout: inherit;
	}
	.tr-p td {
		padding: 5px;
	}
	.td-r,.th-r{
		text-align: left;
		padding-left: 10px;
	}
}
</style>

<script type="text/javascript">
	$("#downloadReport").click(function(e){
		var download = $("#downloadableContent").html();
		$(".print_result").html('<img style="height:25px;" src="../images/loading.gif" alt="Generating Word Format. Please Wait"/>'); 
		$.ajax({
			type: "POST",
			url: "./printPDFFormat.php",
			data: "data=" + download + "&format=<?= $truncate?1:2; ?>&url=ajax",
			cache: false,
			success: function(result){
				$(".print_result").html(result);
			}
		});
	});
</script>