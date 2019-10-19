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

// echo "<pre>";var_dump($patient);
?>

<table border="0" width="100%">
	<tr>
		<td colspan="2">
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
							<a href='../forms/<?= returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='{$patient['InsuranceNameID']}'",$field="FormFile",$data=true, $con) ?>?records=<?= $patient['PatientRecordID'] ?>' title='Print Patient File' target='_blank' style='color:blue; text-decoration:none;'>
								Print
							</a>
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
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
		<td style="font-size: 80%; width: 60%; padding-left: 40px;">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<h1>Patient Bill Information</h1>
			<form action="./save_paid_service.php" method="POST" id="paid_service_form">
				<input type="hidden" name="patientID" value="<?= $patientID ?>" />
				<table border="1" style="width:100%; font-size: 80%;">
					<tr>
						<th>#</th>
						<th>Description</th>
						<th>Quantity</th>
						<th>Unit Price</th>
						<th>Total</th>
						<th>Status</th>
					</tr>
					<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Consultation Price <a href="./edit-consultation.php?patientID=<?= $patientID ?>" rel="#overlay"><img src="../images/edit.png" /></a></th></tr>
					<?php
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
							echo "<tr class='".( (strtolower($patient['InsuranceName']) == "private" && !$value['status'] )?"locked":"available" )."' >";
								echo "<td>".(++$counter)."</td>";
								echo "<td>".($value['ConsultationCategoryName'] == "invisible"?"No Consultation":$value['ConsultationCategoryName'])."</td>";
								echo "<td class='text-right'>1</td>";
								echo "<td class='text-right'>".$value['Amount']."</td>";
								echo "<td class='text-right'>".$value['Amount']."</td>";
								if($value['Amount'] <= 0){
									echo "<td>&nbsp;</td>";
								} else {
									echo "<td>".($value['status']?"Paid":"<label><input type='checkbox' checked value='{$value['ConsultationRecordID']}' name='consultation' /> Pay this</label> ")."</td>";
								}
							echo "</tr>";
							$total_to_paid += $value['status']?0:$value['Amount'];
							// Get All Exam Records Relatedtot the Current Patient
							$exams = formatResultSet($rslt=returnResultSet("SELECT 	b.Amount AS Amount,
																					c.ExamName AS ExamName,
																					a.status AS status,
																					a.ExamRecordID AS ExamRecordID
																					FROM la_records AS a
																					INNER JOIN la_price AS b
																					ON a.ExamPriceID = b.ExamPriceID
																					INNER JOIN la_exam AS c
																					ON b.ExamID = c.ExamID
																					WHERE a.ConsultationRecordID ='{$value['ConsultationRecordID']}'
																					", $con), true, $con);

							JoinArrays($examsData, $exams,$examsData);
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
																						WHERE a.ConsultationRecordID ='{$value['ConsultationRecordID']}'
																						", $con), true, $con);

							JoinArrays($medicinesData, $medicines,$medicinesData);
						}
						if(count($examsData) > 0){
							echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Requested Exam</th></tr>';
							foreach($examsData AS $exam){
								echo "<tr class='".( (strtolower($patient['InsuranceName']) == "private" && !$exam['status'] )?"locked":"available" )."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$exam['ExamName']."</td>";
									echo "<td class='text-right'>1</td>";
									echo "<td class='text-right'>".$exam['Amount']."</td>";
									echo "<td class='text-right'>".$exam['Amount']."</td>";
									echo "<td>".($exam['status']?"Paid":"<label><input type='checkbox' checked value='1' name='la_{$exam['ExamRecordID']}' /> Pay this</label> ")."</td>";
								echo "</tr>";
							$total_to_paid += $exam['status']?0:$exam['Amount'];
							}
						}

						if(count($medicinesData) > 0){
							echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Medicines Prescribed</th></tr>';
							foreach($medicinesData AS $medicine){
								echo "<tr class='".( (strtolower($patient['InsuranceName']) == "private" && !$medicine['status'] )?"locked":"available" )."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$medicine['MedecineName']."</td>";
									echo "<td class='text-right'>".$medicine['Quantity']."</td>";
									echo "<td class='text-right'>".$medicine['Amount']."</td>";
									echo "<td class='text-right'>".($medicine['Amount']*$medicine['Quantity'])."</td>";
									echo "<td>".($medicine['status']?"Paid":"<label><input type='checkbox' checked value='1' name='md_{$medicine['MedecineRecordID']}' /> Pay this</label> ")."</td>";
								echo "</tr>";
								$total_to_paid += $medicine['status']?0:($medicine['Amount']*$medicine['Quantity']);
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
							echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Act Performed</th></tr>';
							foreach($actsData AS $act){
								echo "<tr class='".( (strtolower($patient['InsuranceName']) == "private" && !$act['status'] )?"locked":"available" )."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$act['actName']."</td>";
									echo "<td class='text-right'>".$act['actQuantity']."</td>";
									echo "<td class='text-right'>".$act['actPrice']."</td>";
									echo "<td class='text-right'>".($act['actPrice']*$act['actQuantity'])."</td>";
									echo "<td>".($act['status']?"Paid":"<label><input type='checkbox' checked value='1' name='ac_{$act['ActRecordID']}' /> Pay this</label> ")."</td>";
								echo "</tr>";
								$total_to_paid += $act['status']?0:($act['actPrice']*$act['actQuantity']);
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
							echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Consumable Used</th></tr>';
							foreach($consumablesData AS $consumable){
								echo "<tr class='".( (strtolower($patient['InsuranceName']) == "private" && !$consumable['status'] )?"locked":"available" )."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$consumable['medicineName']."</td>";
									echo "<td class='text-right'>".$consumable['mdQuantity']."</td>";
									echo "<td class='text-right'>".$consumable['Amount']."</td>";
									echo "<td class='text-right'>".($consumable['Amount']*$consumable['mdQuantity'])."</td>";
									echo "<td>".($consumable['status']?"Paid":"<label><input type='checkbox' checked value='1' name='cn_{$consumable['ConsumableRecordID']}' /> Pay this</label> ")."</td>";
								echo "</tr>";
								$total_to_paid += $consumable['status']?0:($consumable['Amount']*$consumable['mdQuantity']);
							}
						}

						if(strtolower($patient['InsuranceName']) == "private"){
							// Here Try to other Health insurance Consumption
							echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Others</th></tr>';
							// foreach($consumablesData AS $consumable){
							IF(FICHE > 0){
								echo "<tr class='".( (strtolower($patient['InsuranceName']) == "private" && !$Consultation[0]['status'] )?"locked":"available" )."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>Fiche</td>";
									echo "<td class='text-right'>1</td>";
									echo "<td class='text-right'>".FICHE."</td>";
									echo "<td class='text-right'>".FICHE."</td>";
									echo "<td>".($Consultation[0]['status']?"Paid":"<label><input type='checkbox' checked value='{$value['ConsultationRecordID']}' name='fiche' /> Pay this</label> ")."</td>";
								echo "</tr>";
								$total_to_paid += $Consultation[0]['status']?0:(FICHE*1);
							}
							// foreach($consumablesData AS $consumable){
							IF(FACTURE > 0){
								echo "<tr class='".( (strtolower($patient['InsuranceName']) == "private" && !$Consultation[0]['status'] )?"locked":"available" )."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>Facture</td>";
									echo "<td class='text-right'>1</td>";
									echo "<td class='text-right'>".FACTURE."</td>";
									echo "<td class='text-right'>".FACTURE."</td>";
									echo "<td>".($Consultation[0]['status']?"Paid":"<label><input type='checkbox' checked value='{$value['ConsultationRecordID']}' name='fiche' /> Pay this</label> ")."</td>";
								echo "</tr>";
								$total_to_paid += $Consultation[0]['status']?0:(FACTURE*1);
							}
							// }
						}
					}


					$patient_part = $patient['TypeofPayment']?($total_to_paid*$patient['ValuePaid']/100):$patient['ValuePaid'];
					$patient_part = RoundUp($patient_part);
					?>
					<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">&nbsp;</th></tr>
					<tr>
						<th colspan="2">Total</th>
						<th>Total: <?= $total_to_paid ?> RWF </th>
						<th>Patient <?= $patient['ValuePaid'].($patient['TypeofPayment']?"%":"RWF") ?>: <?= $patient_part ?>RWF</th>
						<th>Insurance: <?= $total_to_paid - $patient_part ?> RWF</th>
						<th><!--<label><input type="checkbox" id="pay_selected" name="pay_selected">Pay Selected</label>--></th>
					</tr>
				</table>
			</form>
			<a href="./print-receipt.php?patientID=<?= $patientID ?>" target="_blank">Print Paid Services</a>
			<div class="save_edits"></div>
		</td>
	</tr>
</table>

<style type="text/css">
	
	tr.available{
		background-color: #77bb56;
	}
</style>

<script type="text/javascript">
	$(document).ready(function(){
		$("a[rel]").overlay({
	        mask: '#206095',
	        effect: 'apple',
	        onBeforeLoad: function() {

	            // grab wrapper element inside content
	            var wrap = this.getOverlay().find(".contentWrap");
				
	            // load the page specified in the trigger
	            wrap.load(this.getTrigger().attr("href"));
	        }

	    });

	    $("#pay_selected").change(function(e){
	    	var currentValue  = $(this).prop("checked");
	    	// alert(currentValue);
	    	if(currentValue){
	    		// Subit the Form now
	    		$(".save_edits").html('');
				$(".save_edits").html('<img src="../images/loading.gif" alt="Saving"/>'); 
				$("#paid_service_form").ajaxForm({ 
					target: '.save_edits'
				}).submit();
	    	}
	    });
	});
</script>