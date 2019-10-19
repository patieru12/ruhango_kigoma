<?php
session_start();
require_once "../../lib/db_function.php";
// var_dump($_GET);
$patientID = $_GET['patientID'];
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
															e.VillageName AS VillageName,
															f.CellName AS CellName,
															g.SectorName AS SectorName,
															h.DistrictName AS DistrictName,
															c.FamilyCode AS FamilyCode
															FROM pa_records AS a
															INNER JOIN in_name AS b
															ON a.InsuranceNameID = b.InsuranceNameID
															INNER JOIN pa_info AS c
															ON a.PatientID = c.PatientID
															INNER JOIN in_price AS d
															ON b.InsuranceNameID = d.InsuranceNameID
															INNER JOIN co_records AS i
															ON a.PatientRecordID = i.PatientRecordID
															INNER JOIN ad_village AS e
															ON a.VillageID = e.ViillageID
															INNER JOIN ad_cell AS f
															ON e.CellID = f.CellID
															INNER JOIN ad_sector AS g
															ON f.SectorID = g.SectorID
															INNER JOIN ad_district AS h
															ON g.DistrictID = h.DistrictID
															WHERE i.ConsultationRecordID ='{$patientID}'
															", $con), false, $con);

// echo "<pre>";var_dump($patient);
?>
<table style="width: 100%"; border="1" class="patientIdentification">
	<thead>
		<tr>
			<th>
				&nbsp;
			</th>
			<th>Date</th>
			<th>Name</th>
			<th>Phone Number</th>
			<th>Age</th>
			<th>Gender</th>
			<th>Vital Sign</th>
		</tr>
	</thead>
	<tbody>
		<tr style="">
			<td style="text-align: right;">
				&nbsp;
			</td>
			<td><?= $patient['DateIn'] ?></td>
			<td colspan="2" style="vertical-align: middle;">Name: <?= $patient['patientName'] ?><br />House Holder: <?= $patient['FamilyCode'] ?></td>
			<!-- <td><?= $patient['phoneNumber'] ?></td> -->
			<td style="text-align: center;"><?= $patient['DateofBirth'] ?><br /><?= getAge($patient['DateofBirth'],$notation=1, $current_date=$patient['DateIn'])  ?></td>
			<td><?= $patient['patientGender'] ?></td>
			<td>
				<table style="width: 100%;">
					<tr>
						<td style="width: 50%;">
							Weight: <b><?= ($patient['Weight'] >0?$patient['Weight']." Kg":"") ?></b>
						</td>
						<td>
							Temp.: <b><?= $patient['Temperature'] > 0?$patient['Temperature']." <sup>o</sup>C":"" ?></b>
						</td>
					</tr>
					<tr>
						<td>
							Length: <b><?= $patient['Temperature'] > 0?$patient['Temperature']." m":"" ?></b>
						</td>
						<td>
							MUAC: <b><?= $patient['Temperature'] > 0?$patient['Temperature']." cm":"" ?></b>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table style="width: 100%;" border="1">
					<tr>
						<td>District: <b><?= $patient['DistrictName'] ?></b></td>
						<td>Sector: <b><?= $patient['SectorName'] ?></b></td>
						<td>Cell: <b><?= $patient['CellName'] ?></b></td>
						<td>Village: <b><?= $patient['VillageName'] ?></b></td>
					</tr>
				</table>
			</td>
			<td colspan="3">
				<table style="width: 100%;" border="1">
					<tr>
						<td>Insurance: <b><?= $patient['InsuranceName'] ?></b></td>
						<td>Card ID: <b><?= $patient['InsuranceCardID'] ?></b></td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<?php
/*
<table border="0" width="100%" style="">
	<tr>
		<td>
			<table border="0" style="font-size: 80%; width: 100%;">
				<tr><td style="text-align: left;">REPUBLIC OF RWANDA</td></tr>
				<tr>
					<td style="vertical-align: middle; text-align: left;">
						<img src="../images/rwanda.png" />
					</td>
				</tr>
				<tr><td><?= strtoupper($_PROVINCE) ?> PROVINCE</td></tr>
				<tr><td><?= strtoupper($_DISTRICT) ?> DISTRICT</td></tr>
				<tr><td><?= strtoupper($_SECTOR) ?> SECTOR</td></tr>
				<tr><td><?= strtoupper($organisation) ?></td></tr>
				<tr><td>Tel: <?= $organisation_phone ?></td></tr>
				<tr><td>Email: <?= strtolower($organisation_email) ?></td></tr>
			</table>
		</td>
		<td style="font-size: 80%; width: 60%;">
			<table style="font-size: 100%;">
				<tr><td>Patient name: <b><?= $patient['patientName'] ?></b></td></tr>
				<tr><td>Date of Birth: <b><?= $patient['DateofBirth'] ?></b></td></tr>
				<tr><td>Gender: <b><?= $patient['patientGender'] ?></b></td></tr>
				<tr><td>Age: <b><?= getAge($patient['DateofBirth'],$notation=1, $current_date=$patient['DateIn'])  ?></b></td></tr>
				<tr><td>Weight: <b><?= ($patient['Weight'] >0?$patient['Weight']." Kg":"") ?></b></td></tr>
				<tr style="border-bottom:1px solid #000;"><td>Temperature: <b><?= $patient['Temperature'] > 0?$patient['Temperature']."<sup>o</sup>C":"" ?></b></td></tr>
				<tr><td>Insurance: <b><?= $patient['InsuranceName'] ?></b></td></tr>
				<tr><td>Phone: <b><?= $patient['phoneNumber'] ?></b></td></tr>
				<tr>
					<td style="text-align: right; padding-top: 20px;">
						<a href='../forms/<?= returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='{$patient['InsuranceNameID']}'",$field="FormFile",$data=true, $con) ?>?records=<?= $patient['PatientRecordID'] ?>' title='Print Patient File' target='_blank' style='color:blue; text-decoration:none;'>
							Print Consultation Document
						</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
*/
?>