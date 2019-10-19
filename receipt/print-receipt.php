<?php
session_start();
require_once "../lib/db_function.php";
// var_dump($_GET);
$patientID = $_GET['patientID'];
$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.CategoryID AS insuranceCategory,
															c.Name AS patientName,
															c.DateofBirth AS DateofBirth,
															b.InsuranceName AS InsuranceName,
															d.TypeofPayment AS TypeofPayment,
															d.ValuePaid AS ValuePaid,
															c.PatientID AS patientID
															FROM pa_records AS a
															INNER JOIN in_name AS b
															ON a.InsuranceNameID = b.InsuranceNameID
															INNER JOIN pa_info AS c
															ON a.PatientID = c.PatientID
															INNER JOIN in_price AS d
															ON b.InsuranceNameID = d.InsuranceNameID
															WHERE PatientRecordID ='{$patientID}'
															", $con), false, $con);

// echo "<pre>";var_dump($patient);
$age = getAge($patient['DateofBirth'],$notation=1, $current_date=$patient['DateIn']);

$otherHeader = "<tr><td>".strtoupper($_PROVINCE)." PROVINCE</td></tr>
				<tr><td>".strtoupper($_DISTRICT)." DISTRICT</td></tr>
				<tr><td>".strtoupper($_SECTOR)." SECTOR</td></tr>
				<tr><td style='padding-top:10px;'>".strtoupper($organisation)."</td></tr>
				<tr><td>Tel: ".$organisation_phone."</td></tr>
				<tr><td>Email: ".strtolower($organisation_email)."</td></tr>";
$billData = <<<BILLDATA
<table border="0" width="100%">
	<tr>
		<td>
			<table style="font-size: 12px;">
				<tr><td style="text-align: center;">REPUBLIC OF RWANDA</td></tr>
				<tr>
					<td style="vertical-align: middle; padding-bottom: 10px; text-align: center;">
						<img src="../images/rwanda.png" /><br />
						MINISTRY OF HEALTH
					</td>
				</tr>
				{$otherHeader}
			</table>
		</td>
		<td style="width: 180px;">
			<table style="font-size: 12px;">
				<tr><td>CODE: <b>{$patient['patientID']}</b></td></tr>
				<tr><td>Patient: <b>{$patient['patientName']}</b></td></tr>
				<tr><td>Date of Birth: <b>{$patient['DateofBirth']}</b></td></tr>
				<tr><td>Age: <b>{$age}</b></td></tr>
				<tr><td>Insurance: <b>{$patient['InsuranceName']}</b></td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style=" padding-top:15px; ">
			<center><h1 style="text-align: center; font-size:16px;">Patient Bill Information</h1></center>
			<table border="1" style="width:100%; font-size: 12px; border-collapse: collapse; margin-top:15px;">
				<tr>
					<th>#</th>
					<th>Description</th>
					<th>Quantity</th>
					<th>Unit Price</th>
					<th>Total</th>
				</tr>
BILLDATA;
// echo $billData;
					// Get the Consultation Record
					$Consultation = formatResultSet($rslt=returnResultSet("SELECT 	b.Amount AS Amount,
																					c.ConsultationCategoryName AS ConsultationCategoryName,
																					a.ConsultationRecordID AS ConsultationRecordID,
																					a.status AS status,
																					a.ConsultationRecordID AS ConsultationRecordID
																					FROM co_records AS a
																					INNER JOIN co_price AS b
																					ON a.ConsultationPriceID = b.ConsultationPriceID
																					INNER JOIN co_category  AS c
																					ON b.ConsultationCategoryID = c.ConsultationCategoryID
																					WHERE PatientRecordID ='{$patientID}'
																					", $con), true, $con);
					$counter = 0;
					$total_to_paid = 0;

					if(count($Consultation) > 0){
						$examsData 		= array();
						$medicinesData 	= array();
						foreach ($Consultation as $key => $value) {
							$billData .= "<tr>";
								$billData .= "<td>".(++$counter)."</td>";
								$billData .= "<td>".$value['ConsultationCategoryName']."</td>";
								$billData .= "<td class='text-right'>1</td>";
								$billData .= "<td class='text-right'>".$value['Amount']."</td>";
								$billData .= "<td class='text-right'>".$value['Amount']."</td>";
								// echo "<td>".($value['status']?"Paid":"<label><input type='checkbox' checked value='{$value['ConsultationRecordID']}' name='consultation' /> Pay this</label> ")."</td>";
							$billData .= "</tr>";
							$total_to_paid += $value['status']==0?0:$value['Amount'];
							// Get All Exam Records Relatedtot the Current Patient
							$exams = formatResultSet($rslt=returnResultSet("SELECT 	b.Amount AS Amount,
																					c.ExamName AS ExamName,
																					a.status AS status,
																					a.ExamRecordID AS ExamRecordID,
																					a.sampleTaken AS sampleTaken
																					FROM la_records AS a
																					INNER JOIN la_price AS b
																					ON a.ExamPriceID = b.ExamPriceID
																					INNER JOIN la_exam AS c
																					ON b.ExamID = c.ExamID
																					WHERE a.ConsultationRecordID ='{$value['ConsultationRecordID']}' && a.sampleTaken IS NOT NULL
																					", $con), true, $con);

							JoinArrays($examsData, $exams,$examsData);
							// var_dump($examsData); die();
							$medicines = formatResultSet($rslt=returnResultSet("SELECT 	b.Amount AS Amount,
																						c.MedecineName AS MedecineName,
																						a.Quantity AS Quantity,
																						a.status AS status,
																						a.MedecineRecordID AS MedecineRecordID
																						FROM md_records AS a
																						INNER JOIN md_price AS b
																						ON a.MedecinePriceID = b.MedecinePriceID
																						INNER JOIN md_name AS c
																						ON b.MedecineNameID = c.MedecineNameID
																						WHERE a.ConsultationRecordID ='{$value['ConsultationRecordID']}' && a.status =1
																						", $con), true, $con);

							JoinArrays($medicinesData, $medicines,$medicinesData);
						}
						if(count($examsData) > 0){
							// echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Requested Exam</th></tr>';
							foreach($examsData AS $exam){
								$billData .= "<tr>";
									$billData .= "<td>".(++$counter)."</td>";
									$billData .= "<td>".$exam['ExamName']."</td>";
									$billData .= "<td class='text-right'>1</td>";
									$billData .= "<td class='text-right'>".$exam['Amount']."</td>";
									$billData .= "<td class='text-right'>".$exam['Amount']."</td>";
									// echo "<td>".($exam['status']?"Paid":"<label><input type='checkbox' checked value='1' name='la_{$exam['ExamRecordID']}' /> Pay this</label> ")."</td>";
								$billData .= "</tr>";
							$total_to_paid += is_null($exam['sampleTaken'])?0:$exam['Amount'];
							}
						}

						if(count($medicinesData) > 0){
							// echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Medicines Prescribed</th></tr>';
							foreach($medicinesData AS $medicine){
								$billData .= "<tr>";
									$billData .= "<td>".(++$counter)."</td>";
									$billData .= "<td>".$medicine['MedecineName']."</td>";
									$billData .= "<td class='text-right'>".$medicine['Quantity']."</td>";
									$billData .= "<td class='text-right'>".$medicine['Amount']."</td>";
									$billData .= "<td class='text-right'>".($medicine['Amount']*$medicine['Quantity'])."</td>";
									// echo "<td>".($medicine['status']?"Paid":"<label><input type='checkbox' checked value='1' name='md_{$medicine['MedecineRecordID']}' /> Pay this</label> ")."</td>";
								$billData .= "</tr>";
								$total_to_paid += $medicine['status']==0?0:($medicine['Amount']*$medicine['Quantity']);
							}
						}

						// SELECT ALL Acts related to the current Patient
						$actsData = formatResultSet($rslt=returnResultSet("SELECT 	a.ActPriceID AS priceID,
																					c.Name AS actName,
																					a.Quantity AS actQuantity,
																					b.Amount AS actPrice,
																					a.status AS status,
																					a.ActRecordID AS ActRecordID
																					FROM ac_records AS a
																					INNER JOIN ac_price AS b
																					ON a.ActPriceID = b.ActPriceID
																					INNER JOIN ac_name AS c
																					ON b.ActNameID = c.ActNameID
																					WHERE PatientRecordID ='{$patientID}'
																					", $con), true, $con);
						
						if(count($actsData) > 0){
							// echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Act Performed</th></tr>';
							foreach($actsData AS $act){
								$billData .= "<tr class='".( (strtolower($patient['InsuranceName']) == "private" && !$act['status'] )?"locked":"available" )."'>";
									$billData .= "<td>".(++$counter)."</td>";
									$billData .= "<td>".$act['actName']."</td>";
									$billData .= "<td class='text-right'>".$act['actQuantity']."</td>";
									$billData .= "<td class='text-right'>".$act['actPrice']."</td>";
									$billData .= "<td class='text-right'>".($act['actPrice']*$act['actQuantity'])."</td>";
									// $billData .= "<td>".($act['status']?"Paid":"<label><input type='checkbox' checked value='1' name='ac_{$act['ActRecordID']}' /> Pay this</label> ")."</td>";
								$billData .= "</tr>";
								$total_to_paid += !$act['status']?0:($act['actPrice']*$act['actQuantity']);
							}
						}

						$consumablesData = formatResultSet($rslt=returnResultSet("SELECT 	a.MedecinePriceID AS priceID,
																							c.MedecineName AS medicineName,
																							a.Quantity AS mdQuantity,
																							a.status AS status,
																							a.ConsumableRecordID AS ConsumableRecordID,
																							b.Amount AS Amount
																							FROM cn_records AS a
																							INNER JOIN cn_price AS b
																							ON a.MedecinePriceID = b.MedecinePriceID
																							INNER JOIN cn_name AS c
																							ON b.MedecineNameID = c.MedecineNameID
																							WHERE a.PatientRecordID={$patientID}
																							", $con), true, $con);

						if(count($consumablesData) > 0){
							// echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Consumable Used</th></tr>';
							foreach($consumablesData AS $consumable){
								$billData .= "<tr class='".( (strtolower($patient['InsuranceName']) == "private" && !$consumable['status'] )?"locked":"available" )."'>";
									$billData .= "<td>".(++$counter)."</td>";
									$billData .= "<td>".$consumable['medicineName']."</td>";
									$billData .= "<td class='text-right'>".$consumable['mdQuantity']."</td>";
									$billData .= "<td class='text-right'>".$consumable['Amount']."</td>";
									$billData .= "<td class='text-right'>".($consumable['Amount']*$consumable['mdQuantity'])."</td>";
									// $billData .= "<td>".($consumable['status']?"Paid":"<label><input type='checkbox' checked value='1' name='cn_{$consumable['ConsumableRecordID']}' /> Pay this</label> ")."</td>";
								$billData .= "</tr>";
								$total_to_paid += !$consumable['status']?0:($consumable['Amount']*$consumable['mdQuantity']);
							}
						}
					}
					$pt = $patient['ValuePaid'].($patient['TypeofPayment']?"%":"RWF");
					$pat = $patient_part = $patient['TypeofPayment']?($total_to_paid*$patient['ValuePaid']/100):$patient['ValuePaid'];
					$pat = roundUp($pat, 10);
					$remains = $total_to_paid - $patient_part;
					$remains = round($remains, 2);
					$billData .= <<<BILLDATA
					<tr>
						<th colspan="2">Total</th>
						<th>Total: {$total_to_paid} RWF </th>
						<th>Patient {$pt}: {$pat} RWF</th>
						<th>Insurance: {$remains} RWF</th>
					</tr>
				</table>
		</td>
	</tr>
</table>
BILLDATA;
// echo $billData;
$date = date("Y-m-d", time());
$bill = <<<BILL
<table border=0 style="width: 100%">
	<tr>
		<td style="width:49.9%">
			{$billData}
			Cashier: <b>{$_SESSION['user']['Name']}</b><br />&nbsp;<br />
			Signature: <br />
			Date: <b>{$date}</b>
		</td>
		<td style='border-left: 1px solid #000; padding-left:5px;'>
			{$billData}
			Cashier: <b>{$_SESSION['user']['Name']}</b><br />&nbsp;<br />
			Signature: <br />
			Date: <b>{$date}</b>
		</td>
	</tr>
</table>
BILL;
// saveData("UPDATE pa_records SET DocStatus='old' WHERE PatientRecordID='{$patientID}'",$con);
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage("L");

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($bill);

$pdf->Output(); 
die;