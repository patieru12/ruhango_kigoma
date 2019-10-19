<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

set_time_limit(0);
// var_dump($_POST); die();
$startDate 	= PDB($_POST['start_date'], true,$con);
$endDate 	= PDB($_POST['end_date'], true,$con);
$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.CategoryID AS insuranceCategory,
															c.Name AS patientName,
															c.DateofBirth AS DateofBirth,
															b.InsuranceName AS InsuranceName,
															d.TypeofPayment AS TypeofPayment,
															d.ValuePaid AS ValuePaid,
															c.phoneNumber AS phoneNumber,
															c.sex AS patientGender,
															b.InsuranceNameID AS InsuranceNameID,
															c.phoneNumber AS phoneNumber,
															e.VillageName AS VillageName,
															f.CellName AS CellName,
															g.SectorName AS SectorName,
															h.DistrictName AS DistrictName,
															c.FamilyCode AS FamilyCode,
															COALESCE(i.RegisterNumber, '')AS RegisterNumber,
															i.ConsultationRecordID AS ConsultationRecordID,
															k.ServiceCode AS ServiceCode,
															COALESCE(l.id, 0) AS tbId,
															m.id AS tbRecordId,
															m.coughDuration AS coughDuration,
															m.fever AS fever,
															m.night_sweat AS night_sweat,
															m.weight_loss AS weight_loss,
															m.tb_contact AS tb_contact,
															m.hiv AS hiv,
															m.prisoner AS prisoner,
															m.presumptive_case AS presumptive_case,
															m.tst AS tst,
															m.labo_result AS labo_result,
															m.comment AS comment
															FROM pa_records AS a
															INNER JOIN in_name AS b
															ON a.InsuranceNameID = b.InsuranceNameID
															INNER JOIN pa_info AS c
															ON a.PatientID = c.PatientID
															INNER JOIN in_price AS d
															ON b.InsuranceNameID = d.InsuranceNameID
															INNER JOIN ad_village AS e
															ON a.VillageID = e.ViillageID
															INNER JOIN ad_cell AS f
															ON e.CellID = f.CellID
															INNER JOIN ad_sector AS g
															ON f.SectorID = g.SectorID
															INNER JOIN ad_district AS h
															ON g.DistrictID = h.DistrictID
															INNER JOIN co_records AS i
															ON a.PatientRecordID = i.PatientRecordID
															INNER JOIN se_records AS j
															ON a.PatientRecordID = j.PatientRecordID
															INNER JOIN se_name AS k
															ON j.ServiceNameID = k.ServiceNameID
															INNER JOIN tb_records AS l
															ON a.PatientRecordID = l.PatientRecordID
															LEFT JOIN tb_co_records AS m
															ON l.id = m.tbId
															WHERE a.DateIn >= '{$startDate}' &&
																  a.DateIn <= '{$endDate}'
															", $con), true, $con);
// var_dump($_POST);
$tableInformation = <<<TABLE
<div style='height:90%; overflow:auto;'>
	<table border=1 style='width:100%; border-collapse:collapse;'>
		<tr>
			<th colspan=5>IDENTIFICATION</th>
			<th colspan=8>TB SCREENING</th>
			<th rowspan=2 colspan=5>High Risk Group</th>
			<th rowspan=2 colspan=2>TB Presumptive Case</th>
			<th rowspan=2>Tuberculin Skin Test</th>
			<th rowspan=2 colspan=3 style='width:30px;'>Laboratory Result Smear or Genexpert</th>
			<th rowspan=2>Observations</th>
		</tr>
		<tr>
			<th rowspan=3>Date</th>
			<th rowspan=3>N<sup><u>o</u></sup></th>
			<th rowspan=3 style='width:200px;'>Names</th>
			<th rowspan=3>Age</th>
			<th rowspan=3>Sex</th>
			<th colspan=6>BY SYMPTOMS</th>
			<th colspan=2>By Chest X-ray</th>
		</tr>
		<tr>
			<th colspan=3>COUGH</th>
			<th rowspan=2>Fever</th>
			<th rowspan=2>Night Sweat</th>
			<th rowspan=2>Weight Loss</th>
			<th rowspan=2>Done</th>
			<th rowspan=2>Not done</th>
			<th rowspan=2>< 15 years </th>
			<th rowspan=2>≥ 55 years </th>
			<th rowspan=2>TB Contact </th>
			<th rowspan=2>HIV+ </th>
			<th rowspan=2>Prisonner </th>
			<th rowspan=2>Yes </th>
			<th rowspan=2>No </th>
			<th rowspan=2>Inducation in mm </th>
			<th rowspan=2>Pos. </th>
			<th rowspan=2>Neg. </th>
			<th rowspan=2>Not Done </th>
			<th rowspan=2>&nbsp; </th>
		</tr>
		<tr>
			<th>Yes</th>
			<th>No</th>
			<th>Duration (if Yes)</th>
		</tr>
TABLE;

if(count($patient)> 0){
	$counter = 1;
	foreach($patient AS $p){
		$tableInformation .= "<tr>";
			$tableInformation .= "<td>";
				$tableInformation .= $p['DateIn'];
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				$tableInformation .= $counter++;
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				$tableInformation .= ucwords(strtolower($p['patientName']));
			$tableInformation .= "</td>";
			$myAge = getAge($p['DateofBirth'],$notation=1, $p['DateIn']);
			$tableInformation .= "<td>";
				$tableInformation .= $myAge;
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				$tableInformation .= $p['patientGender'];
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['tbId']){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else{
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['tbId']){
					// $tableInformation .= "<span class='fa fa-times'></span>";
				} else{
					$tableInformation .= "<span class='fa fa-check'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				$tableInformation .= $p['coughDuration']?($p['coughDuration']. " day".($p['coughDuration']> 1?"s":"")):"";
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['fever']){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else{
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['night_sweat']){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else{
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['weight_loss']){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else{
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				// $tableInformation .= "<span class='fa fa-times'></span>";
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				// $tableInformation .= "<span class='fa fa-check'></span>";
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if(preg_match("/yrs$/", $myAge)){
					// echo "The Year Pattern Found.";
					$onlyAge = preg_replace("/yrs$/", "", $myAge);
					// echo " ".$onlyAge;
					if($onlyAge < 15){
						$tableInformation .= '<span class="fa fa-check text-danger"></span>';
					} else{
						// $tableInformation .= '<span class="fa fa-times text-success"></span>';
					}
				} else{
					$tableInformation .= '<span class="fa fa-check text-danger"></span>';
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if(preg_match("/yrs$/", $myAge)){
					// echo "The Year Pattern Found.";
					$onlyAge = preg_replace("/yrs$/", "", $myAge);
					// echo " ".$onlyAge;
					if($onlyAge >= 55){
						$tableInformation .= '<span class="fa fa-check text-danger"></span>';
					} else{
						// $tableInformation .= '<span class="fa fa-times text-success"></span>';
					}
				} else{
					// $tableInformation .= '<span class="fa fa-times text-success"></span>';
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['tb_contact']){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else{
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['hiv']){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else{
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['prisoner']){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else{
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['presumptive_case']){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else if(!is_null($p['presumptive_case']) ){
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if(!is_null($p['presumptive_case']) && !$p['presumptive_case']){
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}  else if(!is_null($p['presumptive_case']) ){
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				$tableInformation .= $p['tst']?($p['coughDuration']. " mm"):"";
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['labo_result'] == 1){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else if(!is_null($p['labo_result']) ){
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['labo_result'] == 2){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else if(!is_null($p['labo_result']) ){
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				if($p['labo_result'] == 3){
					$tableInformation .= "<span class='fa fa-check'></span>";
				} else if(!is_null($p['labo_result']) ){
					// $tableInformation .= "<span class='fa fa-times'></span>";
				}
			$tableInformation .= "</td>";
			$tableInformation .= "<td>";
				$tableInformation .= $p['comment'];
			$tableInformation .= "</td>";
		$tableInformation .= "</tr>";
	}
}

$tableInformation .= "
	</table>
</div>
";
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=5,$mgb=5);

$pdf->Open();

$pdf->AddPage("L");

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML(str_replace("<span class='fa fa-check'></span>", "<img src='../images/check_btn.png' />",str_replace("<span class='fa fa-times'></span>", "<img src='../images/times_btn.png' />",$tableInformation)));
$pdf->setHTMLFooter("<div style='font-size:7px; font-family:arial; font-weight:bold; text-align:right; border-top:1px dashed #dfdfdf; color:#dfdfdf;'><div style='float:right; border:0px solid red; width:49%;'>printed using care software | easy one ltd</div></div>");
$filename = "./tb_screening.pdf";
//echo $filename;
$pdf->Output($filename); 
$tableInformation .= "<a style='color:blue' title='Download in PDF Format' href='{$filename}' target='_blank'>download</a>";
echo $tableInformation;