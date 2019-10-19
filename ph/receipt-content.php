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
															c.phoneNumber AS phoneNumber,
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
// $patient['InsuranceName'] = 'MEDIPLAN';
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
							<a id="printForm" href='../forms/<?= returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='{$patient['InsuranceNameID']}'",$field="FormFile",$data=true, $con) ?>?records=<?= $patient['PatientRecordID'] ?>' title='Print Patient File' target='_blank' style='color:blue; text-decoration:none;'>
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
		<td style="font-size: 80%; width: 40%;">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php
			// Get the Consultation Record
			$digno = formatResultSet($rslt=returnResultSet("SELECT 	GROUP_CONCAT(c.DiagnosticName) AS DiagnosticName
																			FROM co_records AS a
																			INNER JOIN co_diagnostic_records AS b
																			ON a.ConsultationRecordID = b.ConsulationRecordID
																			INNER JOIN co_diagnostic AS c
																			ON b.DiagnosticID = c.DiagnosticID
																			WHERE a.PatientRecordID ='{$patientID}'
																			GROUP BY PatientRecordID
																			", $con), false, $con);
			if($digno['DiagnosticName']){
				echo "<div style='font-size: 18px; color:green;'>".$digno['DiagnosticName']."</div>";
			} else{
				echo "<span class=error>No Diagnostic. No need to provide Medicines.</span>"; die();
			}
			?>
			<h1>Patient's Prescribed Medicines</h1>
			<form action="./save_distributed_medicines.php" method="POST" id="distributed_medicines_form">
				<input type="hidden" name="patientID" value="<?= $patientID ?>" />
				<table border="1" style="width:100%; font-size: 80%;">
					<tr>
						<th>#</th>
						<th>Description</th>
						<th>Stock</th>
						<th>Quantity</th>
						<th>Unit Price</th>
						<th>Total</th>
						<th>Received</th>
					</tr>
					<!-- <tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Consultation Price <a href="./edit-consultation.php?patientID=<?= $patientID ?>" rel="#overlay"><img src="../images/edit.png" /></a></th></tr> -->
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
						$className = "";
						if(count($medicinesData) > 0){
							echo '<tr><th colspan="7" style="font-size:150%; padding:5px;"><lable style="color:blue; ">Medicines Prescribed</label></th></tr>';
							foreach($medicinesData AS $medicine){
								if( !$className && ( $medicine['stockLevel'] < $medicine['Quantity'] || (strtolower($patient['InsuranceName']) != "cbhi" && !$medicine['status'] ))){
									$className = "locked";
								}
								echo "<tr class='".( ( $medicine['stockLevel'] < $medicine['Quantity'] || (strtolower($patient['InsuranceName']) != "cbhi" && !$medicine['status'] ))?"locked":"")."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$medicine['MedecineName']." ".$medicine['SpecialPrescription']."</td>";
									echo "<td class='text-right'>".$medicine['stockLevel']."</td>";
									echo "<td class='text-right'>".$medicine['Quantity']."</td>";
									echo "<td class='text-right'>".$medicine['Amount']."</td>";
									echo "<td class='text-right'>".($medicine['Amount']*$medicine['Quantity'])."</td>";
									echo "<td>".($medicine['stockLevel'] < $medicine['Quantity']?"No Stock": (strtolower($patient['InsuranceName']) != "cbhi" && !$medicine['status']?"Not Paid":($medicine['received']?"Delivered":"<label><input type='checkbox' checked value='1' name='md_{$medicine['MedecineRecordID']}' /> Deliver</label> ") ) )."</td>";
								echo "</tr>";
								$total_to_paid += $medicine['status']?0:($medicine['Amount']*$medicine['Quantity']);
							}
						}

						// $patientID = $_GET['patientID'];
						$materialsRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.ConsumableRecordID AS ConsumableRecordID,
																							c.MedecineName AS consumableName,
																							a.Quantity AS Quantity,
																							a.status AS status,
																							b.Amount AS Amount
																							FROM cn_records AS a
																							INNER JOIN cn_price AS b
																							ON a.MedecinePriceID = b.MedecinePriceID
																							INNER JOIN cn_name AS c
																							ON b.MedecineNameID = c.MedecineNameID
																							WHERE a.PatientRecordID = {$patientID}
																							",$con),true, $con);
						$tbData = "";
						// var_dump($medicinesRecords);
						if(count($materialsRecords) > 0){
							echo '<tr><th colspan="7" style="font-size:150%; padding:5px;"><lable style="color:blue; ">Consumables Prescribed</label></th></tr>';

							foreach($materialsRecords AS $r){
								$tbData .= "<tr>";
									$tbData .= "<td>".(++$counter)."</td>";
									$tbData .= "<td>{$r['consumableName']}</td>";
									$tbData .= "<td></td>";
									$tbData .= "<td class='text-right'>{$r['Quantity']}</td>";
									$tbData .= "<td class='text-right'>{$r['Amount']}</td>";
									$mAmount = $r['Quantity'] * $r['Amount'];
									$tbData .= "<td class='text-right'>{$mAmount}</td>";
									$tbData .= "<td></td>";
								$tbData .= "</tr>";
							}
							echo $tbData;
						} else{
							echo "<tr><td colspan=4><span class=error-text>The Current Patient Does not have Assigned Consumables!</span></td></tr>";
						}
					}
					?>
					<tr>
						<th colspan="4">&nbsp;</th>
						<th colspan="3">
							<?php
							if($className){
								echo "<span class=error style='font-size:20px; font-weight:bold;'>Unable to distribute.</span><span class=error-text style='font-size:60px; position:absolute; top:30%; left:35%; font-weight:bold;'>Please Double Check before distribution.</span>";
							} else {
								?>
								<label>Distribute Selected Medicines <input type="checkbox" id="pay_selected" name="pay_selected"></label>
								<?php
							}
							?>
							</th>
					</tr>
				</table>
			</form>
			<a href="./print-patient_prescription.php?patientID=<?= $patientID ?>" target="_blank">Print Prescription</a>
			<div class="save_edits"></div>
		</td>
	</tr>
</table>

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
				$("#distributed_medicines_form").ajaxForm({ 
					target: '.save_edits'
				}).submit();
	    	}
	    });
	});
</script>