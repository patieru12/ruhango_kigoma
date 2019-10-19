<?php
session_start();
//var_dump($_SESSION);
set_time_limit(0);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_GET);
if(!@$_GET['key']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}
$select = "";
$post = "";

$posts = explode("_", $_GET['post']);
//var_dump($posts);
$count = count($post);
$current = 1;
$sys = "("; $sys_s = 0;
$ok = false;
foreach($posts as $pst){
	$ps = returnSingleField($sql="SELECT CenterName FROM sy_center WHERE CenterID='{$pst}'",$field="CenterName",$data=true, $con);
	//var_dump($ps);
	if($ps != null){
		$ok = true;
		if($post && $current++ == $count)
			$post .= " And ";
		else
			$post .= " ";
		$post .= $ps;
		if($sys_s++ > 0)
			$sys .= " || ";
		$sys .= "sy_center.CenterName = '{$ps}'";
	}
}
$sys .= ")";
if(!$ok){
	echo "<span class=error>No Post Selected</span>";
	return;
}
//var_dump($_GET);
//echo $sys;
if(strlen($_GET['key'])){
	//select all possible information on the comming id
	$patients = formatResultSet(
					$rslt=returnResultSet(
						$sql="SELECT DISTINCT 
								se_name.ServiceCode,
								pa_records.DocID,
								pa_records.PatientRecordID, 
								pa_records.FamilyCategory, 
								pa_records.DateIn, 
								pa_records.InsuranceCardID,
								pa_records.HouseManagerID,
								pa_records.InsuranceNameID,
								co_records.registerId,
								co_records.RegisterNumber,
								pa_info.*, ad_village.*, 
								ad_cell.*, ad_sector.*, 
								ad_district.* 
							  FROM 
								pa_records, 
								se_records, 
								se_name, 
								pa_info, 
								ad_village, 
								ad_cell, 
								ad_sector, 
								ad_district, 
								sy_users, 
								sy_center, 
								co_records 
							  WHERE 
								pa_records.PatientRecordID = se_records.PatientRecordID && 
								se_records.ServiceNameID = se_name.ServiceNameID && 
								(co_records.PatientRecordID = pa_records.PatientRecordID) && 
								pa_info.PatientID = pa_records.PatientID && 
								pa_info.VillageID=ad_village.ViillageID && 
								ad_village.CellID=ad_cell.CellID && 
								ad_cell.SectorID=ad_sector.SectorID && 
								ad_sector.DistrictID=ad_district.DistrictID && 
								pa_records.ReceptionistID = sy_users.UserID && 
								sy_users.CenterID = sy_center.CenterID && {$sys}  && 
								pa_records.InsuranceNameID='{$_GET['key']}' && 
								pa_records.DateIn = '{$_GET['year']}-{$_GET['month']}-{$_GET['day']}' 
							   ORDER BY 
								pa_records.DocNumber ASC
								",$con),$multirows=true,$con); // && pa_records.Status != 0 

//echo $sql;
	$dateRequest = $_GET['year']."-".$_GET['month']."-".$_GET['day'];
	// var_dump(count($patients));
if($patients){
	
	?>
	<b class=visibl>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
	</style>
	<span class=styling></span>
	<div style='max-height:85%; padding-top:5px; border:0px solid #000; overflow:auto;'>
	<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
		<tr><th>#</th><th>N<sup>o</sup></th><th>Date</th><th>Service</th><th>Cat.</th><th>Name</th><th>ID Number</th><th>Age</th><th>Sex</th><th>House Holder</th><th>ID Number of Household</th><th>Cons Cost</th><th>Lab</th><th>Imaging</th><th>Hosp.</th><th>Proc & Mat.</th><th>Ambul.</th><th>Consum.</th><th>Drugs.</th><th>Total</th><th>Co-payment</th><th>Amount after verif.</th>
			<?php
			$additionalCharges = returnAllData($sql = "SELECT * FROM sy_product ORDER BY ProductName",$con);
			$secondRow = "";
			$additionalChargesTotal = array();
			if($additionalCharges){
				foreach($additionalCharges AS $ad){
					$additionalChargesTotal[$ad['ProductName']] = array("Number"=>0, "Amount"=>0);
					echo "<th>{$ad['ProductName']}</th>";
					$secondRow .= "<th>&nbsp;</th>";
				}
			}
			?>
		</tr>
		<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>200 RWF / 10%</th><th></th><?= $secondRow ?></tr>
		<?php
		$t = array();
		$medicinesMainTotal = 0;
		$othersMainTotal 	= 0;
		$start_date_ = "2016-11-21"; $reference_date_cat_1_no_tm = "2017-10-14";
		$data = array();
		$data[] = array("No","Date","Service","Cat.","Name","ID Number","Age","Sex","House Holder","ID Number of Household","Cons Cost","Lab","Imaging","Hosp","Procedures & Consumables", "Ambulance","Other Consumables","Drugs","Total","Co-payment","Amount after verification");
		$data[] = array("","","","","","","","","","","100%","100%","100%","100%","100%", "100%","100%","100%","100%","200 RWF /10%","");
		$cbhi_total_tm_number = 0;
		$cbhi_total_tm_amount = 0;

		for($i=0;$i<count($patients);$i++){
			$r = $patients[$i];
			$registerName = returnSingleField("SELECT CONCAT(a.name, ' ', a.registerCode) AS RegisterData FROM sy_register AS a WHERE id='{$r['registerId']}'", "RegisterData", true, $con);
			$ddd = array();
			$remove_tm = ($r['DateIn'] >= $reference_date_cat_1_no_tm)?false:true;
			$check_tm_data = false; $check_tm_data_cat_1 = false;
			if($r['DateIn'] >= $reference_date_cat_1_no_tm && $r['FamilyCategory'] == 1){
				$check_tm_data_cat_1 = true;
			}else{
				$remove_tm =false;
			}
			echo "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
				<td>".$registerName."/".($r['RegisterNumber'])."</td>
				<td><a href='../forms/".returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='{$r['InsuranceNameID']}'",$field="FormFile",true, $con)."?records={$r['PatientRecordID']}' title='Print Patient File' target='_blank' style='color:blue; text-decoration:none;'>{$r['DocID']}</a></td>
				<td>{$r["DateIn"]}</td>
				<td>{$r["ServiceCode"]}</td>
				<td>{$r['FamilyCategory']}</td>
				<td>{$patients[$i]["Name"]}</td>
				<td>{$r["InsuranceCardID"]}</td>
				<td>".($patients[$i]["DateofBirth"] == "0000-00-00"?"":$patients[$i]["DateofBirth"])."</td>
				<td>{$patients[$i]["Sex"]}</td>
				<td>{$patients[$i]["FamilyCode"]}</td>
				<td>".$r['HouseManagerID']."</td>";
				$ddd[] = $r['DocID'];
				$ddd[] = $r["DateIn"];
				$ddd[] = $r["ServiceCode"];
				$ddd[] = $r['FamilyCategory'];
				$ddd[] = $r["Name"];
				$ddd[] = $r["InsuranceCardID"].(is_numeric($r["InsuranceCardID"])?" ":"");
				$ddd[] = $r["DateofBirth"];
				$ddd[] = $r["Sex"];
				$ddd[] = $r["FamilyCode"];
				$ddd[] = $r['HouseManagerID']." ";
				
				//select the laboratory fees related to this request now
				$conslt = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT co_price.Amount, co_records.ConsultationRecordID FROM co_records, co_price WHERE co_records.ConsultationPriceID = co_price. ConsultationPriceID && co_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$cons = 0; $lab = 0; $md = 0; $tot = 0; $t_counter = 0;
				if($conslt){
					foreach($conslt as $c){
						$cons += $c['Amount'];
						//select all exam related to this consultion record now
						$lab_exams = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT la_price.Amount, la_records.ExamRecordID, la_exam.ExamName FROM la_records, la_price, la_exam WHERE la_exam.ExamID = la_price.ExamID && la_records.ExamPriceID = la_price. ExamPriceID && la_records.ConsultationRecordID='{$c['ConsultationRecordID']}'",$con),$multirows=true,$con);
						if($lab_exams){
							foreach($lab_exams as $l){
								$lab += $l['Amount'];
								if($check_tm_data && !$check_tm_data_cat_1){
									if($l['ExamName'] != 'GE'){
										$remove_tm = false;
										$check_tm_data = false;
									}
								}
							}
						}
						if( !$check_tm_data_cat_1){
							$sql_anti_malaria = "SELECT DISTINCT md_price.Amount, md_records.Quantity, md_name.MedecineName FROM md_records, md_price, md_name WHERE md_name.MedecineNameID = md_price.MedecineNameID && md_records.MedecinePriceID = md_price. MedecinePriceID && (md_name.MedecineName LIKE('coartem%') || md_name.MedecineName LIKE('paracet%') ) && md_records.ConsultationRecordID='{$c['ConsultationRecordID']}'";
							/* send this command to check if the patient has an anti-malaria consumetion and preapare to remove the TM and Anti-malaria price now */
							$check_anti_malaria = formatResultSet($rslt=returnResultSet($sql_anti_malaria,$con),$multirows=true,$con);
							if(count($check_anti_malaria) <= 0 || count($check_anti_malaria) != 2){
								$check_tm_data = false;
								$remove_tm = false;
							}
						}
						/*************** End of part to verify the   */
						//select all drugs related to this consultion record now
						$sql="SELECT DISTINCT md_price.Amount, md_records.Quantity, md_name.MedecineName FROM md_records, md_price, md_name WHERE md_name.MedecineNameID = md_price.MedecineNameID && md_records.MedecinePriceID = md_price. MedecinePriceID && md_records.ConsultationRecordID='{$c['ConsultationRecordID']}'";
						$medecines = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
						if($medecines){
							$remove_amount = 1;
							if($check_tm_data  && !$check_tm_data_cat_1){
								foreach($medecines as $c){
									if( !preg_match('/coartem/',strtolower($c['MedecineName']))){
										if(!preg_match('/paracet/',strtolower($c['MedecineName'])) ){
											$remove_amount = 0;
										}
									}
								}
							}
							
							foreach($medecines as $l){
								if($r['FamilyCategory'] <= 2){
									if(preg_match('/coartem/',strtolower($l['MedecineName']))){
										$l['Amount'] = 0;
									} /*else{
									$remove_tm = false;
									}*/
								}
								$md += ($l['Amount'] * $l['Quantity']);
								
							}
						}
						
					}
				}
				echo "<td>{$cons}</td>";
				$ddd[] = $cons;
				$tot += $cons;
				if(!@$t[$t_counter])
					$t[$t_counter] = $cons;
				else
					$t[$t_counter] += $cons;
				$othersMainTotal += $cons;
				$t_counter++;
				echo "<td>{$lab}</td>";
				$ddd[] = $lab;
				$tot += $lab;
				if(!@$t[$t_counter])
					$t[$t_counter] = $lab;
				else
					$t[$t_counter] += $lab;
				$othersMainTotal += $lab;
				$t_counter++;
				echo "<td>&nbsp;</td>";
				$ddd[] = "";
				if(!@$t[$t_counter])
					$t[$t_counter] = 0;
				else
					$t[$t_counter] += 0;
				$othersMainTotal += 0;
				$t_counter++;
				$ho = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ho_record.StartDate, ho_record.EndDate, ho_price.Amount, ho_record.Days FROM ho_record, ho_price WHERE ho_record.HoPriceID = ho_price.HOPriceID && ho_record.RecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$hosp = 0;
				if($ho){
					foreach($ho as $c){
						$days = getAge($c['StartDate'], 1, $c['EndDate'], true);
						$cost_hosp = $days * $c['Amount'];
						$hosp += $cost_hosp;
						
					}
				}
				echo "<td>{$hosp}</td>";
				$ddd[] = $hosp;
				$tot += $hosp;
				if(!@$t[$t_counter])
					$t[$t_counter] = $hosp;
				else
					$t[$t_counter] += $hosp;
				$othersMainTotal += $hosp;
				$t_counter++;
				//now select acts and print the price in the table
				$act = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ac_price.Amount, ac_records.ActRecordID, ac_records.Quantity FROM ac_records, ac_price WHERE ac_records.ActPriceID = ac_price. ActPriceID && ac_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$acts = 0;
				if($act){
					foreach($act as $c){
						$acts += ($c['Amount'] * $c['Quantity']);
						if($check_tm_data  && !$check_tm_data_cat_1){
							$remove_tm = false;
						}
					}
				}
				echo "<td>{$acts}</td>";
				$ddd[] = $acts;
				$tot += $acts;
				if(!@$t[$t_counter])
					$t[$t_counter] = $acts;
				else
					$t[$t_counter] += $acts;
				$othersMainTotal += $acts;
				$t_counter++;

				echo "<td></td>";
				$ddd[] = "";
				$t[$t_counter] = 0;
				$othersMainTotal += 0;
				$t_counter++;

				//now select consumables and print the price in the table
				$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT cn_price.Amount, cn_records.Quantity, cn_name.MedecineName FROM cn_records, cn_price, cn_name WHERE cn_name.MedecineNameID = cn_price.MedecineNameID && cn_records.MedecinePriceID = cn_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$consumables = 0;
				if($consum){
					foreach($consum as $c){
						$consumables += ($c['Amount'] * $c['Quantity']);
						if($check_tm_data && !$check_tm_data_cat_1){
							if(strtolower($c['MedecineName']) != 'sachets'){
								$remove_tm = false;
							}
						}
					}
				}
				//if the found consumable is zero dig more on previous tables
				if($consumables == 0 && $r["DateIn"] >= $release_date){
					//var_dump($consumables);
					//$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT cn_price.Amount, cn_records.Quantity FROM cn_records, cn_price WHERE cn_records.MedecinePriceID = cn_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
					$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT md_price.Amount, cn_records.Quantity, cn_records.ConsumableRecordID, cn_records.Date, md_name.MedecineName FROM cn_records, md_price, md_name WHERE cn_records.MedecinePriceID = md_price. MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
					
					//var_dump($consum);
					$consumables = 0;
					if($consum){
						foreach($consum as $c){
							$consumables += ($c['Amount'] * $c['Quantity']);
							//now try to change the price id for best in consumable table
							//now get the real consumable name from the new table
							$consumable_id = returnSingleField("SELECT MedecineNameID FROM cn_name WHERE MedecineName='{$c['MedecineName']}'","MedecineNameID",true,$con);
							//search the correct pricing according to the registration date
							$consumable_price_id = returnSingleField("SELECT MedecinePriceID FROM cn_price WHERE MedecineNameID='{$consumable_id}' && Date <= '{$c['Date']}' ORDER BY Date DESC LIMIT 0, 1","MedecinePriceID",true,$con);
							if($consumable_price_id)
								saveData("UPDATE cn_records SET MedecinePriceID='{$consumable_price_id}' WHERE ConsumableRecordID='{$c['ConsumableRecordID']}'",$con);
						}
					}
					
					//Re-select new Data for error recovery
					$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT cn_price.Amount, cn_records.Quantity, cn_name.MedecineName FROM cn_records, cn_price, cn_name WHERE cn_name.MedecineNameID = cn_price.MedecineNameID && cn_records.MedecinePriceID = cn_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
					$consumables = 0;
					if($consum){
						foreach($consum as $c){
							$consumables += ($c['Amount'] * $c['Quantity']);
							if($check_tm_data && !$check_tm_data_cat_1){
								if(strtolower($c['MedecineName']) != 'sachets'){
									$remove_tm = false;
								}
							}
						}
					}
				}
				echo "<td>{$consumables}</td>";
				$ddd[] = $consumables;
				$tot += $consumables;
				if(!@$t[$t_counter])
					$t[$t_counter] = $consumables;
				else
					$t[$t_counter] += $consumables;
				$othersMainTotal += $consumables;
				$t_counter++;
				echo "<td>{$md}</td>";
				$ddd[] = $md;
				$tot += $md;
				if(!@$t[$t_counter])
					$t[$t_counter] = $md;
				else
					$t[$t_counter] += $md;
				$medicinesMainTotal += $md;
				$t_counter++;
				echo "<td>{$tot}</td>";
				$ddd[] = $tot;
				if(!@$t[$t_counter])
					$t[$t_counter] = $tot;
				else
					$t[$t_counter] += $tot;
				$t_counter++;
				//var_dump($check_tm_data_cat_1);
				$tm = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT mu_tm.TicketPaid, mu_tm.Type FROM mu_tm WHERE mu_tm.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=false,$con);
				/* Check if the type tm operation was PANSEMENT and remove additional change made to it */
				if($r['FamilyCategory'] == 1){
					saveData($sql = "UPDATE mu_tm SET TicketPaid=0,Type = 'INDIGENT' WHERE PatientRecordID='{$r['PatientRecordID']}'", $con);
				}/* else if($check_tm_data_cat_1){
					saveData($sql = "UPDATE mu_tm SET TicketPaid=0 WHERE PatientRecordID='{$r['PatientRecordID']}'", $con);
				} else if($tm['Type'] == "PANSEMENT" || ($remove_tm && $tm['TicketPaid'] != 0) ){
					//echo "Now Remove TM"
					saveData("UPDATE mu_tm SET TicketPaid=0 WHERE mu_tm.PatientRecordID='{$r['PatientRecordID']}' && Type != 'INDIGENT'", $con);
				} else if(!$remove_tm && $tm['TicketPaid'] == 0 && $r['ServiceCode'] != 'PST'){
					//echo "Now Remove TM second Operation";
					saveData("UPDATE mu_tm SET TicketPaid=200 WHERE mu_tm.PatientRecordID='{$r['PatientRecordID']}' && Type != 'INDIGENT'", $con);
				} else if ($check_tm_data == false && $tm['TicketPaid'] == 0 && $r['ServiceCode'] != 'PST'){
					saveData($sql = "UPDATE mu_tm SET TicketPaid=200 WHERE PatientRecordID='{$r['PatientRecordID']}' && Type != 'INDIGENT'", $con);
				}*/
				$tm = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT mu_tm.TicketPaid FROM mu_tm WHERE mu_tm.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=false,$con);
				if($tm['TicketPaid'] > 0){
					$cbhi_total_tm_number++;
					$cbhi_total_tm_amount += $tm['TicketPaid'];
				}
				echo "<td>{$tm['TicketPaid']}</td>";
				$ddd[] = $tm['TicketPaid'];
				if(!@$t[$t_counter])
					$t[$t_counter] = $tm['TicketPaid'];
				else
					$t[$t_counter] += $tm['TicketPaid'];
				$t_counter++;
				$tp_tot = $tot - $tm['TicketPaid'];
				$othersMainTotal -= $tm['TicketPaid'];
				echo "<td>".round($tp_tot,0)."</td>";
				$ddd[] = $tp_tot;
				if(!@$t[$t_counter])
					$t[$t_counter] = $tp_tot;
				else
					$t[$t_counter] += $tp_tot;
				$t_counter++;

				if($additionalCharges){
					foreach($additionalCharges AS $ad){
						// Get the Sy_records infroamation
						$sql = "SELECT 	b.Amount AS Amount
										FROM sy_records AS a
										INNER JOIN sy_tarif AS b
										ON a.ProductPriceID = b.TarifID
										INNER JOIN sy_product AS c
										ON b.ProductID = c.ProductID
										WHERE a.PatientRecordID = '{$r['PatientRecordID']}' &&
											  a.status = 1 &&
											  c.ProductName = '{$ad['ProductName']}'
										";
						$dataRecords = returnSingleField($sql, "Amount", true, $con);
						if($dataRecords > 0){
							$additionalChargesTotal[$ad['ProductName']]['Number']++;
							$additionalChargesTotal[$ad['ProductName']]['Amount'] += $dataRecords;
						}
						echo "<th>{$dataRecords}</th>";
					}
				}
			echo "</tr>";
			$data[] = $ddd;
		}
		?>
		<tr><th colspan=11>Total</th>
		<?php 
		$ddd = array();
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "SUB TOTAL";
		$ddd[] = "";
		$ddd[] = "";
		foreach($t as $tt){
			echo "<th>".round($tt,1)."</th>";
			$ddd[] = $tt;
		}
		$data[] = $ddd;
			if($additionalCharges){
				foreach($additionalCharges AS $ad){
					echo "<th>".($additionalChargesTotal[$ad['ProductName']]['Amount'])."</th>";
				}
			}
			?>
		</tr>
	</table>
	<?php
	// Check if to day the current data has information about
	$dayInfoID = returnSingleField($sqlData = "SELECT id FROM rpt_cbhi WHERE itemName='TM Paid' && Date='{$dateRequest}'", "id", true, $con);
	if(!$dayInfoID){
		$SQL_INFO = "INSERT INTO rpt_cbhi SET itemName='TM Paid', Number={$cbhi_total_tm_number}, Amount='{$cbhi_total_tm_amount}', Date='{$dateRequest}'";
		// echo $SQL_INFO."<hr />";
		// saveData($SQL_INFO, $con);
	} else{
		$SQL_INFO = "UPDATE rpt_cbhi SET itemName='TM Paid', Number={$cbhi_total_tm_number}, Amount='{$cbhi_total_tm_amount}', Date='{$dateRequest}' WHERE id={$dayInfoID}";
		// echo $SQL_INFO."<hr />";
		// saveData($SQL_INFO, $con);
	}
	/*echo "TM Number: ".$cbhi_total_tm_number."<br />";
	echo "TM Amount: ".$cbhi_total_tm_amount."<hr />";
	var_dump($additionalChargesTotal);*/
	if($additionalCharges){
		foreach($additionalCharges AS $ad){
			$itemName = PDB($ad['ProductName'], true, $con);
			$itemNumber = $additionalChargesTotal[$ad['ProductName']]['Number'];
			$itemamount = $additionalChargesTotal[$ad['ProductName']]['Amount'];

			$dayInfoID = returnSingleField($sqlData = "SELECT id FROM rpt_cbhi WHERE itemName='{$itemName}' && Date='{$dateRequest}'", "id", true, $con);
			
			if(!$dayInfoID){
				$SQL_INFO = "INSERT INTO rpt_cbhi SET itemName='{$itemName}', Number={$itemNumber}, Amount='{$itemamount}', Date='{$dateRequest}'";
				// echo $SQL_INFO."<hr />";
				// saveData($SQL_INFO, $con);
			} else{
				$SQL_INFO = "UPDATE rpt_cbhi SET itemName='{$itemName}', Number={$itemNumber}, Amount='{$itemamount}', Date='{$dateRequest}' WHERE id={$dayInfoID}";
				// echo $SQL_INFO."<hr />";
				// saveData($SQL_INFO, $con);
			}
		}
	}
	?>
	</div></b>
	<?php
	if(count($data)>0){
		$_SESSION['report'] = $data;
		$_SESSION['header'] = array(
									array("PROVINCE / MVK"=>strtoupper($_PROVINCE)),
									array("ADMINISTRATIVE DISTRICT"=>strtoupper($_DISTRICT), "Period"=>$_GET['month']."/".$_GET['year']),
									array("ADMINISTRATIVE SECTION"=>strtoupper($_SECTOR)),
									array("HEALTH FACILITY"=> strtoupper($organisation)),
									array("CODE / HEALTH FACILITY"=>$organisation_code_minisante),
								);
		$_SESSION['report_title'] = array("title"=>"S U M M A R Y  O F V O U C H E R S  F O R  R W A N D A S O C I A L S E C U R I T Y B O A D (R S S B) / CBHI");
		

		$reportSummaryData = array();
		$reportSummaryData[] = array("HEALTH FACILITY:", "", "", strtoupper($organisation));
		$reportSummaryData[] = array("CODE H F:", "", "", strtoupper($organisation_code_minisante));
		$reportSummaryData[] = array("RSSB/CBHI INVOICE NUMBER:", "", "", strtoupper($organisation_code_minisante));
		$reportSummaryData[] = array("TIN:", "", "", strtoupper($organisation_tin));
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("","", "   T O T A L       B I L L");
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("Rwanda Social Board (RSSB) has to pay ");
		$reportSummaryData[] = array("To                 ".strtoupper($organisation)."           The sum of") ;
		$mainTotal = $t[(count($t) - 1)];
		$mainTotal = round($mainTotal, 0);
		// var_dump( $mainTotal);
		$reportSummaryData[] = array("(In Figure)","", number_format($mainTotal)." Frw" ) ;
		$reportSummaryData[] = array("In word","", getEnglishNumber($mainTotal)." Rwandan Francs" ) ;
		$reportSummaryData[] = array("for all medical care given its affiliate" ) ;
		$reportSummaryData[] = array("This amount includes" ) ;
		$medicinesMainTotal  = round($medicinesMainTotal, 0);
		// var_dump($medicinesMainTotal);
		$reportSummaryData[] = array("(In Figure)","", number_format($medicinesMainTotal)." Frw" ) ;
		$reportSummaryData[] = array("In word","", getEnglishNumber($medicinesMainTotal)." Rwandan Francs" ) ;
		$reportSummaryData[] = array("for all medicines" ) ;
		$othersMainTotal 	 = round($othersMainTotal, 0);
		// var_dump($othersMainTotal);
		$reportSummaryData[] = array("(In Figure)","", number_format($othersMainTotal)." Frw" ) ;
		$reportSummaryData[] = array("In word","", getEnglishNumber($othersMainTotal)." Rwandan Francs" ) ;
		$reportSummaryData[] = array("for all medical procedures, invetigation and other services" ) ;
		$reportSummaryData[] = array( $month[((int)$_GET['month'])].", ".$_GET['year'] ) ;
		$reportSummaryData[] = array("This amount will put into account number ".strtoupper($organisation_account_number) ) ;
		$reportSummaryData[] = array("At ".strtoupper($organisation_bank_name) ) ;
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("","", "Done at ".strtoupper($client).", Date ".date("Y F d") );
		$reportSummaryData[] = array("","", strtoupper($organisation_represantative));
		$reportSummaryData[] = array("","", 'Titulaire of '.strtoupper($organisation));
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("(Names signature for responsable & stamp of ..)");

		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("Amount approved after reconciliation");
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("(in figures)", "" , "");
		$reportSummaryData[] = array("(in words)", "" , "... ... ... ... ... ... ... ... ... ... ...");
		$reportSummaryData[] = array("", "" , "... ... ... ... ... ... ... ... ... ... ...");
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("Date & Signature");
		$reportSummaryData[] = array("Names");
		$reportSummaryData[] = array("Post");

		$_SESSION['report_summary'] = $reportSummaryData;
		?>
		<style>
			.img_links{
				height:40px; 
				width:40px; 
				cursor:pointer;
			}
			.img_links:hover{
				/* height:37px;  */
				border-bottom:3px solid red;
			}
		</style>
		<a href='./print_report.php?format=pdf' target="_blank"><img title='View in PDF' src="../images/b_pdfdoc.png" class=img_links width=30px /></a>
		<a href='#' onclick="printExcel(); return false;"><img title="View in EXCEL" src="../images/excel.png" class=img_links width=40px /></a>
		<span id="print_result"></span>
		<script type="text/javascript">
			function printExcel(){
				$("#print_result").html("Please Wait <img src='../images/loading.gif' />");
				$.ajax({
					type: "GET",
					url: "./print_report.php",
					data: "format=excel&in=cbhi",
					cache: false,
					success: function(result){
						$("#print_result").html(result)
						
					},
					error: function(err){
						console.log(err.responseText);
					}
				});
			}
		</script>
		<?php
	}
	//echo "<pre>";var_dump($data);
/* } else{
	
	echo "<span class=error-text>No Match Found</span>";
}
 */
} else{
	echo "<span class=error-text>No Patient in the selected month {$_GET['month']}/{$_GET['year']} at selected station {$post}</span>";
}
}
?>
<script>
	$(document).ready(function() { 
		$('#excel_file').live('change', function(){ 
			
			$("#upload_out").html('');
			$("#upload_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
			$("#upload_patient").ajaxForm({ 
				target: '#upload_out'
			}).submit(); 
		}); 
	});
</script>
<div id=upload_out></div><!--
<form action="upload_patient.php" id=upload_patient enctype='multipart/form-data' method=post>
	<input type=hidden value='<?= $_GET['key'] ?>' name=insurance_id />
	<input type=file name=patient_list class=txtfield1 id=excel_file style='width:300px;' />
</form>-->