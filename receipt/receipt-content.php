<?php
session_start();
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
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
															c.FamilyCode AS FamilyCode,
															COALESCE(a.InsuranceCardID, a.applicationNumber, '') AS InsuranceCardID
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
									<td>Insurance: <b><?= $patient['InsuranceName'].(strtolower($patient['InsuranceName']) == "cbhi"?("&nbsp;&nbsp;&nbsp;Cat.:".$patient['FamilyCategory']):"") ?></b></td>
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
			<h1>Patient Bill Information</h1>
			<form action="./print_code_only.php" method="POST" id="code_only_form">
				<input type="hidden" id="patientIDDebt" name="patientID" value="<?= $patientID ?>" />
			</form>
			<form action="./save_paid_service.php" method="POST" id="paid_service_form">
				<input type="hidden" id="patientIDDebt" name="patientID" value="<?= $patientID ?>" />
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
					$counter 		= 0;
					$total_to_paid 	= 0;
					$main_total 	= 0;
					$special_Hosp 	= 0;

					if(count($Consultation) > 0){
						$examsData 		= array();
						$medicinesData 	= array();
						$totalConsulations = 0;
						foreach ($Consultation as $key => $value) {
							$className = ( (strtolower($patient['InsuranceName']) == "cbhi" || $value['status'] == 1 )?"available":"locked" );
							if($value['status'] == -1){
								$className = "unlocked";
							}
							echo "<tr class='".$className."' >";
								echo "<td>".(++$counter)."</td>";
								echo "<td>".($value['ConsultationCategoryName'] == "invisible"?"No Consultation":$value['ConsultationCategoryName']);
								if(strtolower($patient['InsuranceName']) == "private"){
									// echo "CBHI____"
									if($value['status'] == 0){
										echo "&nbsp;<span style='cursor:pointer;' title='Click to Unlock' class='fa fa-lock fa-lg text-danger' onclick='unlockWithoutPayment(\"co_records\", \"ConsultationRecordID\", \"{$value['ConsultationRecordID']}\", \"status\", \"-1\", \"Do you realy want to unlock the consultation for {$patient['patientName']}\", \"{$patientID }\")'></span>";
									} else if($value['status'] == -1){
										echo "&nbsp;<span style='cursor:pointer;' title='Click to Lock Again' class='fa fa-unlock fa-lg text-success' onclick='unlockWithoutPayment(\"co_records\", \"ConsultationRecordID\", \"{$value['ConsultationRecordID']}\", \"status\", \"0\", \"Do you realy want to lock again the consultation for {$patient['patientName']}\", \"{$patientID }\")'></span>";
									}
								}
								echo "</td>";
								echo "<td class='text-right'>1</td>";
								echo "<td class='text-right'>".$value['Amount']."</td>";
								echo "<td class='text-right'>".$value['Amount']."</td>";
								// if($value['Amount'] <= 0 || $className == "available"){
								/*if($value['Amount'] <= 0 ){
									echo "<td>&nbsp;</td>";
								} else {*/
									$totalConsulations += $value['status'] != 1?$value['Amount']:0;
									echo "<td>".($value['status']==1?"Paid":"<label><input type='checkbox' checked onclick='return false;' value='{$value['ConsultationRecordID']}' name='consultation' /> Pay this</label> ")."</td>";
								// }
							echo "</tr>";
							$main_total 	+= $value['Amount'];
							$total_to_paid += $value['status']==1?0:$value['Amount'];
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
						if((strtolower($patient['InsuranceName']) == "private") && $totalConsulations > 0){
							echo "<input type='hidden' name='totalCONS' value='{$totalConsulations}' />";
						}
						$totalExamLabo = 0;
						if(count($examsData) > 0){
							echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Requested Exam</th></tr>';
							foreach($examsData AS $exam){
								$className = ( (strtolower($patient['InsuranceName']) == "cbhi" || $exam['status'] ==1 )?"available":"locked" );
								// $className = ( (strtolower($patient['InsuranceName']) == "private" && !$exam['status'] )?"locked":"available" );
								if($exam['status'] == -1){
									$className = "unlocked";
								}
								echo "<tr class='".$className."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$exam['ExamName'];
									if(strtolower($patient['InsuranceName']) == "private"){
										// echo "CBHI____"
										if($exam['status'] == 0){
											echo "&nbsp;<span style='cursor:pointer;' title='Click to Unlock' class='fa fa-lock fa-lg text-danger' onclick='unlockWithoutPayment(\"la_records\", \"ExamRecordID\", \"{$exam['ExamRecordID']}\", \"status\", \"-1\", \"Do you realy want to unlock the Exam for {$patient['patientName']}\", \"{$patientID }\")'></span>";
										} else if($exam['status'] == -1){
											echo "&nbsp;<span style='cursor:pointer;' title='Click to Lock Again' class='fa fa-unlock fa-lg text-success' onclick='unlockWithoutPayment(\"la_records\", \"ExamRecordID\", \"{$exam['ExamRecordID']}\", \"status\", \"0\", \"Do you realy want to lock again the Exam for {$patient['patientName']}\", \"{$patientID }\")'></span>";
										}
									}
									echo "</td>";
									echo "<td class='text-right'>1</td>";
									echo "<td class='text-right'>".$exam['Amount']."</td>";
									echo "<td class='text-right'>".$exam['Amount']."</td>";
									/*if($className == "available"){
										echo "<td></td>";
									} else{*/
										$totalExamLabo += $exam['status'] !=1?$exam['Amount']:0;
										echo "<td>".($exam['status'] == 1?"Paid":"<label><input type='checkbox' checked onclick='return false;' value='1' name='la_{$exam['ExamRecordID']}' /> Pay this</label> ")."</td>";
									// }
								echo "</tr>";
								$main_total += $exam['Amount'];
								$total_to_paid += $exam['status'] == 1?0:$exam['Amount'];
							}
						}

						if( (strtolower($patient['InsuranceName']) == "private") && $totalExamLabo > 0){
							echo "<input type='hidden' name='totalExamLabo' value='{$totalExamLabo}' />";
						}
						$totalMedicines= 0;
						if(count($medicinesData) > 0){
							echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Medicines Prescribed</th></tr>';
							foreach($medicinesData AS $medicine){
								$catOneCherker=false;
								if( preg_match("/oartem/", $medicine['MedecineName']) && strtolower($patient['InsuranceName']) == "cbhi" && $patient['FamilyCategory'] <= 2){
									$catOneCherker=true;
								}
								$className = ( (strtolower($patient['InsuranceName']) == "cbhi"|| $medicine['status'] )?"available":"locked" );
								// $className = ( (strtolower($patient['InsuranceName']) == "private" && !$medicine['status'] )?"locked":"available" );
								if($medicine['status'] == -1){
									$className = "unlocked";
								}
								if($catOneCherker){
									$medicine['Amount'] = 0;
								}
								echo "<tr class='".$className."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$medicine['MedecineName']."</td>";
									echo "<td class='text-right'>".$medicine['Quantity']."</td>";
									echo "<td class='text-right'>".$medicine['Amount']."</td>";
									echo "<td class='text-right'>".($medicine['Amount']*$medicine['Quantity'])."</td>";
									/*if($className == "available"){
										echo "<td></td>";
									} else{*/
										$totalMedicines += $medicine['status'] != 1?($medicine['Amount']*$medicine['Quantity']):0;
										echo "<td>".($medicine['status'] == 1?"Paid":"<label><input type='checkbox' checked onclick='return false;' value='1' name='md_{$medicine['MedecineRecordID']}' /> Pay this</label> ")."</td>";
									// }
								echo "</tr>";
								$main_total += $medicine['Amount']*$medicine['Quantity'];
								$total_to_paid += $medicine['status'] == 1?0:($medicine['Amount']*$medicine['Quantity']);
							}
						}

						if( (strtolower($patient['InsuranceName']) == "private") && $totalMedicines > 0){
							echo "<input type='hidden' name='totalMED' value='{$totalMedicines}' />";
						}

						// SELECT ALL Acts related to the current Patient
						$otherConsumables = 0;
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
						$actsOther = 0;
						$actsAccouhement = 0;
						if(count($actsData) > 0){
							echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">Act Performed</th></tr>';
							foreach($actsData AS $act){
								$className = ( (strtolower($patient['InsuranceName']) == "cbhi" || $act['status'] == 1 )?"available":"locked" );
								// $className = ( (strtolower($patient['InsuranceName']) == "private" && !$act['status'] )?"locked":"available" );
								if($act['status'] == -1){
									$className = "unlocked";
								}
								echo "<tr class='".$className."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$act['actName']."</td>";
									echo "<td class='text-right'>".$act['actQuantity']."</td>";
									echo "<td class='text-right'>".$act['actPrice']."</td>";
									echo "<td class='text-right'>".($act['actPrice']*$act['actQuantity'])."</td>";
									if(preg_match("/^Acc./", $act['actName'])){
										$actsAccouhement += $act['status'] != 1?($act['actPrice']*$act['actQuantity']):0;
									} else{
										$actsOther += $act['status'] != 1?($act['actPrice']*$act['actQuantity']):0;
									}
									/*if($className == "available"){
										echo "<td></td>";
									} else{*/
										// $otherConsumables += !$act['status']?($act['actPrice']*$act['actQuantity']):0;
										echo "<td>".($act['status'] == 1?"Paid":"<label><input type='checkbox' checked onclick='return false;' value='1' name='ac_{$act['ActRecordID']}' /> Pay this</label> ")."</td>";
									// }
								echo "</tr>";
								$main_total += $act['actPrice']*$act['actQuantity'];
								$total_to_paid += $act['status'] == 1?0:($act['actPrice']*$act['actQuantity']);
							}
						}

						if( (strtolower($patient['InsuranceName']) == "private") && $actsOther > 0){
							echo "<input type=text name='totalPROC_OTHER' value='{$actsOther}' />";
						}

						if( (strtolower($patient['InsuranceName']) == "private") && $actsAccouhement > 0){
							echo "<input type=text name='totalPROC_ACC' value='{$actsAccouhement}' />";
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
								$className = ( strtolower($patient['InsuranceName'] == "cbhi" || $consumable['status'] == 1 )?"available":"locked" );
								// $className = ( (strtolower($patient['InsuranceName']) == "private" && !$consumable['status'] )?"locked":"available" );
								if($consumable['status'] == -1){
									$className = "unlocked";
								}
								echo "<tr class='".$className."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$consumable['medicineName']."</td>";
									echo "<td class='text-right'>".$consumable['mdQuantity']."</td>";
									echo "<td class='text-right'>".$consumable['Amount']."</td>";
									echo "<td class='text-right'>".($consumable['Amount']*$consumable['mdQuantity'])."</td>";
									/*if($className == "available"){
										echo "<td></td>";
									} else{*/
										$otherConsumables += $consumable['status'] != 1?($consumable['Amount']*$consumable['mdQuantity']):0;
										echo "<td>".($consumable['status']?"Paid":"<label><input type='checkbox' checked onclick='return false;' value='1' name='cn_{$consumable['ConsumableRecordID']}' /> Pay this</label> ")."</td>";
									// }
								echo "</tr>";
								$main_total += $consumable['Amount']*$consumable['mdQuantity'];
								$total_to_paid += $consumable['status'] == 1?0:($consumable['Amount']*$consumable['mdQuantity']);
							}
						}



						if( (strtolower($patient['InsuranceName']) == "private") && $otherConsumables > 0){
							echo "<input type=text name='totalCONSU' value='{$otherConsumables}' />";
						}

						// Check if the Patient has any hospitalization record
						$hospitalisaData = formatResultSet($rslt=returnResultSet("SELECT 	a.HORecordID AS HORecordID,
																							c.Name AS Name,
																							a.Days AS Days,
																							a.status AS status,
																							a.StartDate AS StartDate,
																							a.EndDate AS EndDate,
																							b.Amount AS Amount
																							FROM ho_record AS a
																							INNER JOIN ho_price AS b
																							ON a.HOPriceID = b.HOPriceID
																							INNER JOIN ho_type AS c
																							ON b.HOTypeID = c.TypeID
																							WHERE a.RecordID={$patientID}
																							", $con), true, $con);
						$hospitalisation = 0;
						$hospitType = 0;
						if(count($hospitalisaData) > 0){
							echo '<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">hospitalization Record</th></tr>';
							foreach($hospitalisaData AS $hosp){
								$className = ( strtolower($patient['InsuranceName'] == "cbhi" || $hosp['status'] == 1 )?"available":"locked" );
								// $className = ( (strtolower($patient['InsuranceName']) == "private" && !$consumable['status'] )?"locked":"available" );
								if($hosp['status'] == -1){
									$className = "unlocked";
								}
								$days = getAge($hosp['StartDate'], 1, $hosp['EndDate'], true);
								echo "<tr class='".$className."'>";
									echo "<td>".(++$counter)."</td>";
									echo "<td>".$hosp['Name']."</td>";
									echo "<td class='text-right'>".$days."</td>";
									echo "<td class='text-right'>".$hosp['Amount']."</td>";
									echo "<td class='text-right'>".($hosp['Amount']*$days)."</td>";
									/*if($className == "available"){
										echo "<td></td>";
									} else{*/
									if(preg_match("/chambre/", $hosp['Name'])){
										$hospitType = 1;
										$special_Hosp += $hosp['status'] != 1?($hosp['Amount']*$days):0;
									} else{
										$main_total += $hosp['Amount']*$days;
										$total_to_paid += $hosp['status'] == 1?0:($hosp['Amount']*$days);
									}
									$hospitalisation += $hosp['status'] != 1?($hosp['Amount']*$days):0;
									echo "<td>".($hosp['status'] == 1?"Paid":"<label><input type='checkbox' checked onclick='return false;' value='1' name='ho_{$hosp['HORecordID']}' /> Pay this</label> ")."</td>";
									// }
								echo "</tr>";
								
							}
						}



						if( ((strtolower($patient['InsuranceName']) == "private" || $hospitType == 1 )) && $hospitalisation > 0){
							echo "<input type=hidden name='totalHOSP' value='{$hospitalisation}' />";
						}

/*
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
						}*/
					}

					// var_dump($total_to_paid); die();
					$patient_part = 0;
					if( (strtolower($patient['InsuranceName']) == "cbhi") /*&& $patient['FamilyCategory'] == 1*/) {
						$tmInformation = formatResultSet($rslt=returnResultSet("SELECT * FROM mu_tm WHERE PatientRecordID ='{$patientID}'", $con), false, $con);
						
						$patient_part = $tmInformation['TicketPaid'];
						$patient['ValuePaid'] = $tmInformation['TicketPaid'];
					} else {
						$patient_part = $patient['TypeofPayment']?($total_to_paid*$patient['ValuePaid']/100):$patient['ValuePaid'];
					}
					// $patient_part = RoundUp($patient_part, 20);
					$insurancePart = 0;
					if($total_to_paid > $patient_part){
						$insurancePart = round(($total_to_paid - $patient_part), 2);
					}

					$tmInformation = formatResultSet($rslt=returnResultSet("SELECT * FROM mu_tm WHERE PatientRecordID ='{$patientID}'", $con), false, $con);
					// var_dump($tmInformation);
					$total_to_be_paid_now = 0;
					if($tmInformation['status'] == 0){
						/*if((strtolower($patient['InsuranceName']) == "cbhi")){
							$total_to_be_paid_now = $patient_part;
						} else */
						if((strtolower($patient['InsuranceName']) == "private") && $tmInformation['TicketPaid'] < $patient_part) {
							$total_to_be_paid_now =  $patient_part - $tmInformation['TicketPaid'];
						} else {
							$total_to_be_paid_now = $patient_part;
						}

						if((strtolower($patient['InsuranceName']) != "private")){
							; //echo " <input type='hidden' name='totalTM' value='{$total_to_be_paid_now}' />";
						}
					} else if( (strtolower($patient['InsuranceName']) == "private") ){
						// var_dump( $tmInformation, $patient_part);
						$total_to_be_paid_now = $patient_part;
					}
					$total_to_be_paid_now += $special_Hosp;
					?>
					<tr><th colspan="6" style="font-size:150%; color:blue; padding:5px;">&nbsp;</th></tr>
					<tr>
						<th colspan="2">Main Total <?= ($main_total + $special_Hosp ) ?> RWF</th>
						<th>Total: <?= $total_to_paid ?> RWF </th>
						<th <?= $tmInformation['status'] == 1 && $tmInformation['TicketPaid'] == $patient_part?"class='available'":"class='locked'" ?> >
							<label><?= $tmInformation['status'] == 1 && $tmInformation['TicketPaid'] == $patient_part?"Paid ":"<input type='checkbox' name='patientTM_{$tmInformation['TicketID']}' value='{$patient_part}' checked onclick='return false;' />" ; ?>Patient <?= $patient['ValuePaid'].($patient['TypeofPayment']?"%":"RWF") ?>: <?= number_format($patient_part); ?> RWF</label>
							<?php
							// if($tmInformation['Type'] == "CATEGORY" && $tmInformation['status'] == 0){
							if($tmInformation['Type'] == "CATEGORY"){
								?>
								<a title="Ticket Modulateur Modifier" href="./edit-tm.php?patientID=<?= $patientID ?>&TicketID=<?= $tmInformation['TicketID'] ?>" rel="#overlay"><img src="../images/edit.png" /></a>
								<?php
							}
							// var_dump($tmInformation);
							?>
						</th>
						<th>Insurance: <?= $insurancePart ?> RWF</th>
						<th></th>
					</tr>
				</table>
				<?php
				$sy_records = formatResultSet($rslt=returnResultSet("SELECT a.id AS recordID,
																			a.ProductPriceID AS ProductPriceID,
																			a.status AS recordStatusID,
																			b.Amount AS productAmount,
																			c.ProductName AS ProductName,
																			a.status AS status,
																			a.priscribedBy AS priscribedBy,
																			a.Quantity AS Quantity
																			FROM sy_records AS a
																			INNER JOIN sy_tarif AS b
																			ON a.ProductPriceID = b.TarifID
																			INNER JOIN sy_product AS c
																			ON b.ProductID = c.ProductID
																			WHERE a.PatientRecordID = '{$patientID}'
																			", $con), true, $con);
				// var_dump($sy_records);
				if($sy_records){
					echo "<table border=1 style='margin-top: 10px; width:50%;'>";
						echo "<tr><th>#</th><th>Fee Type</th><th>Quantity</th><th>Amount</th><th>Status</th></tr>";
						$ig = 1;
						$otherProducts = 0;
						$fiche = 0;

						$Lunette = 0;
						$Supanet = 0;
						foreach($sy_records AS $s){
							$className = $s['status']?"available":"locked";
							echo "<tr class='{$className}'>";
								echo "<td>" . ($ig++) . "</td>";
								echo "<td>{$s['ProductName']}</td>";
								echo "<td style='text-align:right; padding-right:5px;'>".number_format($s['Quantity'])."</td>";
								echo "<td style='text-align:right; padding-right:5px;'>".number_format($s['productAmount']*$s['Quantity'])."</td>";
								echo "<td>";
								if($s['status'] == 1){
									echo "Paid";
								} else {
									echo "<label><input type='checkbox'checked onclick='return false;' name='sy_{$s['recordID']}' value='1' /> Pay this</label>";
									if($s['priscribedBy'] == $_SESSION['user']['UserID']){
										echo " | <a title='Remove {$s['ProductName']} From charges' href='#' style='color:red; text-decoration:none;'> <img src='../images/b_drop.png' /></a>";
									}
									if($s['ProductName'] == $exceptInReport){
										$fiche += ($s['productAmount']*$s['Quantity']);
									} else {
										if(preg_match("/lunette/", strtolower($s['ProductName']))){
											$Lunette += ($s['productAmount']*$s['Quantity']);
										} else if(preg_match("/supanet/", strtolower($s['ProductName']))){
											$Supanet += ($s['productAmount']*$s['Quantity']);
										} else {
											$otherProducts += ($s['productAmount']*$s['Quantity']);
										}
									}
									$total_to_be_paid_now += ($s['productAmount']*$s['Quantity']);
								}
								echo "</td>";
							echo "</tr>";
						}

					echo "</table>";
					if($otherProducts > 0){
						echo "<input type='hidden' name='totalOTHER' value='{$otherProducts}' />";
					}
					if($fiche > 0){
						echo "<input type='hidden' name='totalFiche' value='{$fiche}' />";
					}
					if($Lunette > 0){
						echo "<input type='hidden' name='totalLunette' value='{$Lunette}' />";
					}
					if($Supanet > 0){
						echo "<input type='hidden' name='totalSupanet' value='{$Supanet}' />";
					}
				}

				// Check if there is a debt information related tot the current patient
				/*$debtInfor = formatResultSet($rslt=returnResultSet("SELECT 	*
																			FROM sy_debt_records AS a
																			WHERE a.PatientRecordID = '{$patientID}'
																			", $con), false, $con);*/
				$total_adjusted_amount = $total_to_be_paid_now;
				$total_to_be_paid_now = RoundUp($total_to_be_paid_now, 10);
				$total_adjusted_amount = $total_to_be_paid_now - $total_adjusted_amount;
				?>
				<table style="width: 100%">
					<tr>
						<td>
							<h1 style="text-align: left; font-size:14px;">
								Total Amount to pay: <?= number_format($total_to_be_paid_now) ?> RWF 
								<input type="hidden" name="totalBill" id="totalBill" value="<?= $total_to_be_paid_now; ?>">
								<input type="hidden" name="totalAdjust" id="totalAdjust" value="<?= $total_adjusted_amount; ?>">
							</h1>
						</td>
						<td>
							<h1 style="text-align: left; font-size:14px;">
								Paid: <span id="displayPaid"><?= number_format($total_to_be_paid_now) ?></span> RWF 
								<input type="hidden" name="totalPaid" id="totalPaid" value="<?= $total_to_be_paid_now; ?>">
							</h1>
						</td>
						<td>
							<h1 style="text-align: right; font-size:14px;"  >
								<?php
								$currentHours = date("H", time());
								if( false && ($currentHours >= 16 || $currentHours < 6)){
									echo "<span class=error-text>During Night Some operations are disabled!</span>";
								} else {
									?>
									<label style="cursor: pointer;"><input type="checkbox" id="pay_selected" name="pay_selected">Save Paid Amount & Print</label>
									<?php
									if(strtolower($patient['InsuranceName']) != 'cbhi'){
										?>
										&nbsp;| <label style="cursor: pointer;"><input type="checkbox" id="print_code_only" name="print_code_only">Print Code</label>
										<?php
									}
								}
								?>
							</h1>
						</td>
					</tr>
				</table>
				<?php
					$currentHours = date("H", time());
					if( false && ($currentHours >= 16 || $currentHours < 6)){
						echo "<span class=error-text>During Night Some operations are disabled!</span>";
					} else {
						?>
						<a style="color:blue; text-decoration: none;" href="./receipt.pdf" target="_blank">Print Paid Services</a> | 
						<a style="color:blue; text-decoration: none;" href="./additional_fees.php?patientID=<?= $patientID ?>" rel="#overlay">Add Charges</a>
						<?php
					}
					?>
				<div class="save_edits"></div>
				<fieldset>
					<legend><h3>If Debt fill this form</h3></legend>
					<table>
						<tr>
							<td>
								Available Amount
							</td>
							<td>
								<input type="text" name="availableAmountToPay" value="<?= isset($debtInfor['availableAmount'])?$debtInfor['availableAmount']:""; ?>" id="availableAmountToPay" class="txtfield1">
							</td>

							<td>
								Due Date
							</td>
							<td>
								<input type="text" name="debtDueDate" value="<?= @$debtInfor['dueDate']?$debtInfor['dueDate']:""; ?>" id="debtDueDate" onclick="ds_sh(this, 'debtDueDate');" class="txtfield1">
							</td>
						</tr>
						<tr>
							<td>
								Patient Phone Number
							</td>
							<td>
								<input type="text" name="debtPhoneNumber" value="<?= @$debtInfor['phoneNumber']?$debtInfor['phoneNumber']:""; ?>" id="debtPhoneNumber" class="txtfield1">
							</td>
						
							<td>
								House Holder ID Card
							</td>
							<td>
								<input type="text" name="debtIDCard" value="<?= @$debtInfor['idCard']?$debtInfor['idCard']:""; ?>" id="debtIDCard" class="txtfield1">
							</td>
						</tr>
						<tr>
							<td>
								Exact Address
							</td>
							<td>
								<input type="text" name="debtAdress" value="<?= @$debtInfor['address']?$debtInfor['address']:""; ?>" id="debtAdress" class="txtfield1">
								<!-- <input type="button" name="requestLoan" id="requestLoan" value="Request Dept" class="flatbtn-blu" /><span id="debtRequestProcess"></span> -->
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
			
		</td>
	</tr>
</table>

<style type="text/css">
	tr.available, th.available{
		background-color: #77bb56;
	}
</style>
<script type="text/javascript">
	var stopRequest = false;
	var stopSearching = false;

	var removePrompt = false;
</script>
<?php
/*if(@$debtInfor['id']){
	?>
	<script type="text/javascript">
		stopRequest = true;
		$("#availableAmountToPay").focus();
		setTimeout(function(e){
			$("#availableAmountToPay").blur();
			checkAccountStatus();
		}, 600);
	</script>
	<?php
} else {
	?>
	<script type="text/javascript">
		stopSearching = true;
	</script>
	<?php
}*/
?>
<script type="text/javascript">
	
	function checkAccountStatus(){
		if(stopSearching){
			return;
		}
		var patientID = $("#patientIDDebt").val();

		$.ajax({
				type: "GET",
				url: "./account-status.php",
				data: "PatientRecordID=" + patientID,
				cache: false,
				success: function(result){
					if(result != "OK"){
						$("#debtRequestProcess").html(result);
						setTimeout(function(e){
							checkAccountStatus();
						}, 2000);
					} else {
						stopRequest = false;
						alert("No you can submit the Debt is approved");
					}
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
	}
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

	    $("#print_code_only").change(function(e){
	    	if(confirm("Print the Identification only")){
	    		$(".save_edits").html('<img src="../images/loading.gif" alt="Saving"/>'); 
				$("#code_only_form").ajaxForm({ 
					target: '.save_edits'
				}).submit();
	    	} else{
	    		$(".save_edits").html('<span class="error">Print command aborted. Please check everything before printing</span>');
	    	}
	    });
		
	    $("#pay_selected").change(function(e){
	    	setTimeout(function(e){
	    		var currentValue  = $(this).prop("checked");
		    	var totalBill = $("#totalBill").val()*1;
		    	var amount 	  = $("#availableAmountToPay").val().trim();
		    	if(amount){
		    		totalBill -= amount*1;
		    	}

		    	var totalPaid = $("#totalPaid").val();
		    	if( (totalPaid*1) < 0){
		    		$(".save_edits").html('<span class="error">No need to print receipt. ' + totalPaid + ' RWF amount is paid and the paper is money</span>');
		    	}else if(removePrompt || confirm("********************************************************************\n*    PLEASE REMEMBER SOME REPORTS ARE                         *\n*    BUILT AT THIS TIME AND THEY CAN'T BE ROLLBACKED  *\n*    SO BE CAREFULL BEFORE SUBMITTING THIS                   *\n*******************************************************************\nYou have Received " + totalPaid + " RWF\nIn the cash box print the receipt")){
		    		removePrompt = false;
		    		$(".save_edits").html('');
					$(".save_edits").html('<img src="../images/loading.gif" alt="Saving"/>'); 
					$("#paid_service_form").ajaxForm({ 
						target: '.save_edits'
					}).submit();
		    	} else{
		    		$(".save_edits").html('<span class="error">Print command aborted. Please check everything before printing</span>');
		    	}
	    	}, 200);
	    	// var actionPage = $("#paid_service_form").prop("action");
	    	// alert(actionPage);
	    	

	    	// alert(currentValue);
	    	/*if(stopRequest){
	    		alert("Please Wait for the confirmation from the accountant.");
	    	} else if(currentValue){
	    		// Subit the Form now
	    		$(".save_edits").html('');
				$(".save_edits").html('<img src="../images/loading.gif" alt="Saving"/>'); 
				$("#paid_service_form").ajaxForm({ 
					target: '.save_edits'
				}).submit();
	    	}*/
	    });

	    $("#availableAmountToPay").keyup(function(e){
	    	var availableAmountChecker = $(this).val()*1;
	    	// alert(availableAmountChecker);
	    	var totalBill = $("#totalBill").val()*1;
	    	if(totalBill <= availableAmountChecker){
	    		$("#paid_service_form").prop("action", "save_paid_service.php");
	    		var actionPage = $("#paid_service_form").prop("action");
	    		alert("ERROR!\nYou said available amount is greater than required amount.\nPlease Save & print the Receipt no Debt")
	    		$("#availableAmountToPay").val("");
	    		$("#displayPaid").html(totalBill);
	    		$("#totalPaid").val(totalBill);
	    	}
	    });

	    $("#availableAmountToPay").blur(function(e){

	    	// Here Check if any difference will be generated and desable submission untill approved
	    	var availableAmountChecker = $(this).val();
	    	if(availableAmountChecker.trim() != ""){
	    		// alert(availableAmountChecker);
		    	if(availableAmountChecker >= 0){
		    		$("#paid_service_form").prop("action", "request-debt.php");
		    		var actionPage = $("#paid_service_form").prop("action");
		    		// alert(actionPage);
		    		$("#displayPaid").html(availableAmountChecker);
		    		$("#totalPaid").val(availableAmountChecker);

		    	}
	    	} else{
	    		$("#paid_service_form").prop("action", "save_paid_service.php");
	    		var actionPage = $("#paid_service_form").prop("action");
	    		// alert(actionPage);
	    	}
	    	
	    });

	    $("#requestLoan").click(function(e){
	    	var patientID = $("#patientIDDebt").val();
	    	var requiredAmount = $("#totalBill").val();
	    	var availableAmountToPay = $("#availableAmountToPay").val();
	    	var debtDueDate = $("#debtDueDate").val();
	    	var phoneNumber = $("#debtPhoneNumber").val();
	    	var debtIDCard = $("#debtIDCard").val();
	    	var addressExact = $("#debtAdress").val();
	    	$.ajax({
				type: "POST",
				url: "./request-debt.php",
				data: "patientID=" + patientID + "&requiredAmount=" + requiredAmount + "&availableAmountToPay=" + availableAmountToPay + "&debtIDCard=" + debtIDCard + "&addressExact=" + addressExact + "&debtDueDate=" + debtDueDate + "&phoneNumber=" + phoneNumber+ "&url=ajax",
				cache: false,
				success: function(result){
					$("#debtRequestProcess").html(result);
					setTimeout(function(e){
						checkAccountStatus();
					}, 500);
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
	    });
	});

	function unlockWithoutPayment(tableName, fieldName, valueData, statusField, statusValue, messageAlert, patientIDData){
		if(confirm(messageAlert)){
			$.ajax({
				type: "POST",
				url: "./unlock.php",
				data: "tableName=" + tableName + "&fieldName=" + fieldName + "&valueData=" + valueData + "&statusField=" + statusField + "&statusValue=" + statusValue + "&patientID=" + patientIDData + "&url=ajax",
				cache: false,
				success: function(result){
					$(".save_edits").html(result);
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
		}
	}
</script>
<?php
if(@$_GET['print'] == hash('sha256',$patientID)){
	?>
	<script type="text/javascript">
		removePrompt = true;
		setTimeout(function(e){
			$("#pay_selected").trigger("change");
		}, 400);
		
	</script>
	<?php
}
?>