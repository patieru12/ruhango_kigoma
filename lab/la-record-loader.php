<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
echo "<script>window.location='../logout.php';</script>";
return;
}
if (!isset($_POST['ExamID']) OR !isset($_POST['ResultDate']) ) {
	echo "<script>window.location='./laboratory.php';</script>";
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
if(preg_match("/^[0-9]{4}-[0-9]{2}$/", $_POST['ResultDate'])){
	$_POST['ResultDate2'] = $_POST['ResultDate']."-31";
	$_POST['ResultDate'] = $_POST['ResultDate']."-01";
}
if($_POST['ResultDate2'] < $_POST['ResultDate']){
	$temp = $_POST['ResultDate2'];
	$_POST['ResultDate2'] = $_POST['ResultDate'];
	$_POST['ResultDate'] = $temp;
}

$date = $_POST['ResultDate'];
$date2 = $_POST['ResultDate2'];

// var_dump($_POST);

//select all result corresponding to the selected month and exams
$sql = "SELECT la_records.*, pa_records.DocID, pa_records.DateIn, pa_records.PatientRecordID, pa_info.*, ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName FROM la_records, `la_price`, la_exam, co_records, pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE la_exam.`ExamID` = '{$examId}' && la_exam.ExamID = la_price.ExamID && la_price.ExamPriceID = la_records.ExamPriceID && la_records.ResultDate >= '{$_POST['ResultDate']}' && la_records.ResultDate <= '{$_POST['ResultDate2']}' && la_records.ConsultationRecordID=co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_info.VillageID = ad_village.ViillageID && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID ORDER BY la_records.ResultDate asc, ResultNumber ASC";
//echo $sql;
$exam_price_ids = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
$pdf_htmldata = "";
?>
<div style='max-height:430px;' id='note' >
	<span>Result Founded << <label style='font-weight: bold;'><?= count($exam_price_ids) ?></label> >> on Exam-code:<label style='font-weight: bold;'><?php echo returnSingleField("SELECT * FROM `la_exam` WHERE `ExamID` = '{$examId}' ",'ExamCode'); ?></label> Date:<label style='font-weight: bold;'><?= $date ?> - <?= $date2 ?></label></span>
</div>
<?php
$pdf_htmldata .= <<<PDFDATA
Laboratory Register From {$_POST['ResultDate']} - {$_POST['ResultDate2']}<br />
<div style='margin-top: 10px;'>
<table class='table tables' border='1' style='width: 100%;text-align: center; border-collapse: collapse' >
	<thead style='font-weight: bold;'>
		<th>Number</th>
		<th>DocID</th>
		<th>Date</th>
		<th>Name</th>
		<th style='padding:0 50px;'>Age</th>
		<th>Sex</th>
		<th>District</th>
		<th>Sector</th>
		<th>Cell</th>
		<th>Village</th>
		<th class='th-r'>Result</th>
	</thead>
	<tbody>
PDFDATA;
	?>
<div style='margin-top: 10px;'>
<table class='table tables' border='1' style='width: 100%;text-align: center;' >
	<thead style='font-weight: bold;'>
		<th>Number</th>
		<th>DocID</th>
		<th>Date</th>
		<th>Name</th>
		<th style='padding:0 50px;'>Age</th>
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
			$pdf_htmldata .= "<tr class='tr-p' >";
				$pdf_htmldata .= "<td style='text-align:right; padding:2px; font-size:11px; font-family:arial'>".$get['ResultNumber']."</td>";
				$pdf_htmldata .= "<td style='text-align:right; padding:2px; font-size:11px; font-family:arial'>{$get['DocID']}</td>";
				$pdf_htmldata .= "<td style='text-align:right; padding:2px; font-size:11px; font-family:arial'>{$get['ResultDate']}</td>";
				$pdf_htmldata .= "<td style='text-align:left; padding:2px; font-size:11px; font-family:arial; width:200px'>{$get['Name']}</td>";
				$pdf_htmldata .= "<td style='text-align:left; padding:2px; font-size:11px; font-family:arial; width:100px'>".getAge($get['DateofBirth'],1,$get['DateIn'])."</td>";
				$pdf_htmldata .= "<td style='text-align:left; padding:2px; font-size:11px; font-family:arial'>{$get['Sex']}</td>";
				$pdf_htmldata .= "<td style='text-align:left; padding:2px; font-size:11px; font-family:arial'>{$get['DistrictName']}</td>";
				$pdf_htmldata .= "<td style='text-align:left; padding:2px; font-size:11px; font-family:arial'>{$get['SectorName']}</td>";
				$pdf_htmldata .= "<td style='text-align:left; padding:2px; font-size:11px; font-family:arial'>{$get['CellName']}</td>";
				$pdf_htmldata .= "<td style='text-align:left; padding:2px; font-size:11px; font-family:arial'>{$get['VillageName']}</td>";
				$pdf_htmldata .= "<td class='td-r' style='text-align:left; padding:2px; font-size:11px; font-family:arial'>";
			?>
			<tr class='tr-p' >
				<td style='text-align:right;'><?= /* substr( */$get['ResultNumber']/* ,6) */ ?></td>
				<td><?= $get['DocID'] ?></td>
				<td><?= $get['ResultDate'] ?></td>
				<td><?= $get['Name'] ?></td>
				<td title="DOB: <?= $get['DateofBirth'] ?>"><?= getAge($get['DateofBirth'],1,$get['DateIn']) ?></td>
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
					if($array){
						foreach($array as $result){
							if(!@$result_summary[trim($result['ResultName'])])
								$result_summary[trim($result['ResultName'])] = 1;
							else
								$result_summary[trim($result['ResultName'])] += 1;
						}
					}
					arraytostring($array, $str);
					echo $str;
					$pdf_htmldata .= $str."</td></tr>";
					?>
				</td>
			</tr>
		<?php
		}
	$pdf_htmldata .= <<<PDFDATA
	</tbody>
</table>
PDFDATA;
		?>
	</tbody>
</table>
	<?php
		//display the result summary now
		if(count($result_summary) > 0){
			echo "<table>";
			$pdf_htmldata_header = "<table>";
				foreach($result_summary as $rs=>$rn){
					//var_dump($rs);
					$pdf_htmldata_header .= "<tr><th style='text-align:left;'>{$rs}</th><th style='text-align:left;'>{$rn}</th></tr>";
					echo "<tr><th style='text-align:left;'>{$rs}: </th><th style='text-align:left;'>{$rn}</th></tr>";
				}
			echo "</table>";
			$pdf_htmldata_header .= "</table>";
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
<?php
require_once "../lib/mpdf57/mpdf.php";
$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage("L");

$pdf->SetFont("Arial","N",10);
/*$css = file_get_contents("./pdf_css.css");

$pdf->WriteHTML($css,1);*/

$pdf->WriteHTML($pdf_htmldata_header.$pdf_htmldata);
$filename = "./laboData.pdf";
//echo $filename;
$pdf->Output($filename); 
?>
<a href="<?php echo $filename ?>" id="dn_download" target="_blank">Download</a>