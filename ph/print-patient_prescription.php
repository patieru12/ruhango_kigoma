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
															c.phoneNumber AS phoneNumber
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
// $patient['InsuranceName'] = 'MEDIPLAN';
$tableData = '
<style type="text/css">
	table{
		border-collapse: collapse;
	}

	.main{
		width:100%;
	}
</style>
<table class="main" border="0" width="100%" style="width:100%">
	<tr>
		<td>
			<table border="0" style="font-size: 80%; width: 100%;">
				<tr><td style="text-align: left;">REPUBLIC OF RWANDA</td></tr>
				<tr>
					<td style="vertical-align: middle; text-align: left;">
						<img src="../images/rwanda.png" />
					</td>
				</tr>
				<tr><td>'.strtoupper($_PROVINCE).' PROVINCE</td></tr>
				<tr><td>'.strtoupper($_DISTRICT).' DISTRICT</td></tr>
				<tr><td>'.strtoupper($_SECTOR).' SECTOR</td></tr>
				<tr><td>'.strtoupper($organisation).'</td></tr>
				<tr><td>Tel: '.$organisation_phone.'</td></tr>
				<tr><td>Email: '.strtolower($organisation_email).'</td></tr>
			</table>
		</td>
		<td style="font-size: 80%; width: 40%;">
			<table style="font-size: 100%;">
				<tr><td>Patient name: <b>'.$patient['patientName'].'</b></td></tr>
				<tr><td>Date of Birth: <b>'.$patient['DateofBirth'].'</b></td></tr>
				<tr><td>Age: <b>'. getAge($patient['DateofBirth'],$notation=1, $current_date=$patient['DateIn']).'</b></td></tr>
				<tr><td>Weight: <b>'. $patient['Weight'].' Kg</b></td></tr>
				<tr><td>Temperature: <b>'. ($patient['Temperature']>0? $patient['Temperature']."<sup>o</sup>C":"").'</b></td></tr>
				<tr style="border-top: 1px solid #000;"><td>Insurance: <b>'. $patient['InsuranceName'].'</b></td></tr>
				<tr><td>Phone: <b>'. $patient['phoneNumber'].'</b></td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<h5>Patient\'s Prescribed Medicines</h5>
			<form action="./save_distributed_medicines.php" method="POST" id="distributed_medicines_form">
				<input type="hidden" name="patientID" value="'. $patientID.'" />
				<table border="1" style="width:100%; font-size: 80%;">
					<tr>
						<th>#</th>
						<th>Medicine name</th>
						<th>Prescription</th>
						<th>Quantity</th>
						<th>Received</th>
					</tr>';
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
							
							$medicines = formatResultSet($rslt=returnResultSet("SELECT 	b.Amount AS Amount,
																						c.MedecineName AS MedecineName,
																						a.Quantity AS Quantity,
																						a.status AS status,
																						a.MedecineRecordID AS MedecineRecordID,
																						a.SpecialPrescription AS SpecialPrescription,
																						COALESCE(d.Quantity, 0) AS stockLevel,
																						a.received AS received
																						FROM md_records AS a
																						INNER JOIN md_price AS b
																						ON a.MedecinePriceID = b.MedecinePriceID
																						INNER JOIN md_name AS c
																						ON b.MedecineNameID = c.MedecineNameID
																						LEFT JOIN md_stock AS d
																						ON c.MedecineNameID = d.MedicineNameID
																						WHERE a.ConsultationRecordID ='{$value['ConsultationRecordID']}'
																						", $con), true, $con);

							JoinArrays($medicinesData, $medicines,$medicinesData);
						}
						
						if(count($medicinesData) > 0){
							// echo '<tr><th colspan="7" style="font-size:150%; padding:5px;"><lable style="color:blue; ">Medicines Prescribed</label></th></tr>';
							foreach($medicinesData AS $medicine){
								$tableData .= "<tr class='".( ( $medicine['stockLevel'] < $medicine['Quantity'] || (strtolower($patient['InsuranceName']) != "cbhi" && !$value['status'] ))?"locked":"")."'>";
									$tableData .=  "<td>".(++$counter)."</td>";
									$tableData .=  "<td>".$medicine['MedecineName']."</td>
													<td>".$medicine['SpecialPrescription']."</td>";
									// $tableData .=  "<td class='text-right'>".$medicine['stockLevel']."</td>";
									$tableData .=  "<td class='text-right'>".$medicine['Quantity']."</td>";
									// $tableData .=  "<td class='text-right'>".$medicine['Amount']."</td>";
									// $tableData .=  "<td class='text-right'>".($medicine['Amount']*$medicine['Quantity'])."</td>";
									$tableData .=  "<td>".($medicine['stockLevel'] < $medicine['Quantity']?"No Stock": (strtolower($patient['InsuranceName']) != "cbhi" && !$value['status']?"Not Paid":($medicine['received']?"Delivered":"<label><input type='checkbox' checked value='1' name='md_{$medicine['MedecineRecordID']}' /> Deliver</label> ") ) )."</td>";
								$tableData .=  "</tr>";
								$total_to_paid += $medicine['status']?0:($medicine['Amount']*$medicine['Quantity']);
							}
						}
					}
					$tableData .= '
				</table>
			</form>
		</td>
	</tr>
</table>';
//require the MPDF Library
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage();

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($tableData);
$pdf->setHTMLFooter("<div style='font-size:7px; font-family:arial; font-weight:bold; text-align:right; border-top:1px dashed #dfdfdf; color:#dfdfdf;'>printed using care software | easy one ltd</div>");
// $filename = "./files/".$record['DocID'].".pdf";
//echo $filename;
$pdf->Output(); 
die;
echo $tableData;
?>
