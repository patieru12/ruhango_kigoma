<?php
session_start();
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$patientID = $_GET['record_id'];
$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.CategoryID AS insuranceCategory,
															c.Name AS patientName,
															c.DateofBirth AS DateofBirth,
															b.InsuranceName AS InsuranceName,
															d.TypeofPayment AS TypeofPayment,
															d.ValuePaid AS ValuePaid,
															c.sex AS patientGender,
															b.InsuranceNameID AS InsuranceNameID,
															c.phoneNumber AS phoneNumber,
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
															INNER JOIN ad_village AS e
															ON a.VillageID = e.ViillageID
															INNER JOIN ad_cell AS f
															ON e.CellID = f.CellID
															INNER JOIN ad_sector AS g
															ON f.SectorID = g.SectorID
															INNER JOIN ad_district AS h
															ON g.DistrictID = h.DistrictID
															WHERE PatientRecordID ='{$patientID}'
															", $con), false, $con);
// var_dump($patient);
$debtInfo = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															(a.availableAmount + COALESCE(SUM(b.paidAmount), 0) ) AS availableAmount
															FROM sy_debt_records AS a
															LEFT JOIN sy_debt_payment AS b
															ON a.id = b.debtID
															WHERE a.PatientRecordID = '{$patientID}'
															", $con), false, $con);

$debtInfo = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.DocID AS DocID,
																	b.Name AS Name,
																	a.DateIn AS Date,
																	a.PatientRecordID,
																	c.Amount,
																	c.id
																	FROM pa_records AS a
																	INNER JOIN pa_info AS b
																	ON a.PatientID = b.PatientID
																	INNER JOIN (
																		SELECT 	a.PatientRecordID,
																				a.Amount AS Amount,
																					a.id
																				FROM rpt_cbhi AS a
																				WHERE a.PatientRecordID='{$patientID}'
																				AND a.itemName='Retained'
																				GROUP BY a.PatientRecordID
																		UNION
																			SELECT 	a.PatientRecordID,
																					a.Amount AS Amount,
																					a.id
																					FROM rpt_mmi AS a
																					WHERE a.PatientRecordID='{$patientID}'
																					AND a.itemName='Retained'
																					GROUP BY a.PatientRecordID
																		UNION
																			SELECT 	a.PatientRecordID,
																					a.Amount AS Amount,
																					a.id
																					FROM rpt_rssb_rama AS a
																					WHERE a.PatientRecordID='{$patientID}'
																					AND a.itemName='Retained'
																					GROUP BY a.PatientRecordID
																		UNION
																			SELECT 	a.PatientRecordID,
																					a.Amount AS Amount,
																					a.id
																					FROM rpt_private AS a
																					WHERE a.PatientRecordID='{$patientID}'
																					AND a.itemName='Retained'
																					GROUP BY a.PatientRecordID
																	) AS c
																	ON a.PatientRecordID = c.PatientRecordID
																	WHERE a.PatientRecordID = '$patientID'
																	", $con),false, $con);
?>
Name: <b><?= $patient['patientName'] ?></b><br />
Insurance: <b><?= $patient['InsuranceName'] ?></b> Card ID: <b><?= $patient['InsuranceCardID'] ?></b> <br />
<h2>Refund Status</h2>
<table border=1 style="width: 100%; margin-bottom: 10px;">
	<tr>
		<td>Reference: <?= $patient['DocID'] ?></td>
		<td>Name: <?= $patient['patientName'] ?></td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: center;">Required Amount: <?= number_format($debtInfo['Amount']); ?> RWF</td>
	</tr>
</table>
<span class="save_adds"></span>
<form action="save-paid-refund.php" id="add_additional" method="POST">
	<input type='hidden' name='patientID' value='<?= $patientID ?>' />
	<input type='hidden' name='refundId' value='<?= $_GET['refund'] ?>' />
	<input type='hidden' name='tabName' value='<?= $_GET['tbName'] ?>' />
	<br />
	Refund Amount: 
	<input type='text' name='paidAmount' value='' class='txtfield1' placeholder="Paid Amount" /><br />
	<input type='submit' id='save' name='addFees' value='Save & Close' class='flatbtn-blu'>
</form>

<script type="text/javascript">
	$('#save').click(function(e){ 
		e.preventDefault();
		//$('#save').attr("desabled",":true");
		$(".save_adds").html('');
		$(".save_adds").html('<img src="../images/loading.gif" alt="Saving"/>'); 
		$("#add_additional").ajaxForm({ 
			target: '.save_adds'
		}).submit();
			
	});
</script>
