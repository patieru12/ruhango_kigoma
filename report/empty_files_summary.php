<?php
session_start();
//var_dump($_SESSION);
set_time_limit(0);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);

$select = "";
$post = "";
$posts = explode("_", $_POST['post']);
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
			$post .= " && ";
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
$date = $_POST['date'];
if(strlen($_POST['date'])){
	//select all possible information on the coming id
	//$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientRecordID, pa_records.FamilyCategory, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center, co_records WHERE co_records.PatientRecordID = pa_records.PatientRecordID && pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && InsuranceNameID='{$_GET['key']}' && DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') ORDER BY pa_records.DateIn ASC, pa_records.PatientRecordID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT 	DISTINCT in_name.InsuranceName, 
																	pa_records.PatientRecordID, 
																	pa_records.FamilyCategory, 
																	pa_records.DateIn, 
																	pa_records.InsuranceCardID, 
																	se_name.ServiceCode, 
																	pa_info.*, 
																	ad_village.*, 
																	ad_cell.*, 
																	ad_sector.*, 
																	ad_district.* 
																	FROM in_name, 
																	pa_records, 
																	pa_info, 
																	se_name, 
																	se_records, 
																	ad_village, 
																	ad_cell, 
																	ad_sector, 
																	ad_district, 
																	sy_users, 
																	sy_center 
																	WHERE pa_info.PatientID = pa_records.PatientID && 
																		  pa_records.InsuranceNameID = in_name.InsuranceNameID && 
																		  pa_info.VillageID=ad_village.ViillageID && 
																		  ad_village.CellID=ad_cell.CellID && 
																		  ad_cell.SectorID=ad_sector.SectorID && 
																		  ad_sector.DistrictID=ad_district.DistrictID && 
																		  pa_records.ReceptionistID = sy_users.UserID && 
																		  sy_users.CenterID = sy_center.CenterID && {$sys} && 
																		  DateIn = '{$_POST['date']}' && 
																		  se_records.PatientRecordID = pa_records.PatientRecordID && 
																		  se_records.ServiceNameID = se_name.ServiceNameID 
																	ORDER BY pa_records.DateIn ASC, 
																			 pa_records.PatientRecordID ASC
																	",$con),$multirows=true,$con); // && pa_records.Status != 0 
	// var_dump($sql);
	//echo $sql;
	if($patients){
			$total_empty_data_services = array();
			$total_empty_data = array();
			$total_empty_data_insurances = array();
			$total_patients_insurances = array();
			$total_patients = array();
	
			$t = array();
			$c_i = 0;
			for($i=0;$i<count($patients);$i++){
				$r = $patients[$i];
				$ddd = array();
				
				$display = "<td>{$r["DateIn"]}</td>
					<td>{$r['ServiceCode']}</td>
					<td>{$r['FamilyCategory']}</td>
					<td>{$r["InsuranceCardID"]}</td>
					<td>".($patients[$i]["DateofBirth"] == "0000-00-00"?"":$patients[$i]["DateofBirth"])."</td>
					<td>{$patients[$i]["Sex"]}</td>
					<td>{$patients[$i]["Name"]}</td>
					<td>{$patients[$i]["FamilyCode"]}</td>
					<td>".(@explode('/',$r['InsuranceCardID'])[2])."</td>";
					
					//select the laboratory fees related to this request now
					$conslt = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT co_price.Amount, co_records.ConsultationRecordID FROM co_records, co_price WHERE co_records.ConsultationPriceID = co_price. ConsultationPriceID && co_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
					$cons = 0; $lab = 0; $md = 0; $tot = 0; $t_counter = 0;
					if($conslt){
						foreach($conslt as $c){
							$cons += $c['Amount'];
							//select all exam related to this consultion record now
							$lab_exams = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT la_price.Amount, la_records.ExamRecordID FROM la_records, la_price WHERE la_records.ExamPriceID = la_price. ExamPriceID && la_records.ConsultationRecordID='{$c['ConsultationRecordID']}'",$con),$multirows=true,$con);
							if($lab_exams){
								foreach($lab_exams as $l){
									$lab += $l['Amount'];
								}
							}
							//select all drugs related to this consultion record now
							$medecines = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT md_price.Amount, md_records.Quantity FROM md_records, md_price WHERE md_records.MedecinePriceID = md_price. MedecinePriceID && md_records.ConsultationRecordID='{$c['ConsultationRecordID']}'",$con),$multirows=true,$con);
							if($medecines){
								foreach($medecines as $l){
									$md += ($l['Amount'] * $l['Quantity']);
								}
							}
						}
					}
					//$display .= "<td>{$cons}</td>";
					//$ddd[] = $cons;
					$tot += $cons;
					if(!@$t[$t_counter])
						$t[$t_counter] = $cons;
					else
						$t[$t_counter] += $cons;
					$t_counter++;
					//$display .= "<td>{$lab}</td>";
					//$ddd[] = $lab;
					$tot += $lab;
					if(!@$t[$t_counter])
						$t[$t_counter] = $lab;
					else
						$t[$t_counter] += $lab;
					$t_counter++;
					//$display .= "<td>&nbsp;</td>";
					//$ddd[] = "";
					if(!@$t[$t_counter])
						$t[$t_counter] = 0;
					else
						$t[$t_counter] += 0;
					$t_counter++;
					$ho = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ho_price.Amount, ho_record.Days FROM ho_record, ho_price WHERE ho_record.HoPriceID = ho_price. HOPriceID && ho_record.RecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
					$hosp = 0;
					if($ho){
						foreach($ho as $c){
							$hosp += ($c['Amount'] * $c['Days']);
							
						}
					}
					//$display .= "<td>{$hosp}</td>";
					//$ddd[] = $hosp;
					$tot += $hosp;
					if(!@$t[$t_counter])
						$t[$t_counter] = $hosp;
					else
						$t[$t_counter] += $hosp;
					$t_counter++;
					//now select acts and print the price in the table
					$act = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ac_price.Amount, ac_records.ActRecordID FROM ac_records, ac_price WHERE ac_records.ActPriceID = ac_price. ActPriceID && ac_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
					$acts = 0;
					if($act){
						foreach($act as $c){
							$acts += ($c['Amount']);
							
						}
					}
					//$display .= "<td>{$acts}</td>";
					//$ddd[] = $acts;
					$tot += $acts;
					if(!@$t[$t_counter])
						$t[$t_counter] = $acts;
					else
						$t[$t_counter] += $acts;
					$t_counter++;
					//now select consumables and print the price in the table
					$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT md_price.Amount, cn_records.Quantity FROM cn_records, md_price WHERE cn_records.MedecinePriceID = md_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
					$consumables = 0;
					if($consum){
						foreach($consum as $c){
							$consumables += ($c['Amount'] * $c['Quantity']);
							
						}
					}
					//$display .= "<td>{$consumables}</td>";
					//$ddd[] = $consumables;
					$tot += $consumables;
					if(!@$t[$t_counter])
						$t[$t_counter] = $consumables;
					else
						$t[$t_counter] += $consumables;
					$t_counter++;
					//$display .= "<td>{$md}</td>";
					//$ddd[] = $md;
					$tot += $md;
					if(!@$t[$t_counter])
						$t[$t_counter] = $md;
					else
						$t[$t_counter] += $md;
					$t_counter++;
					//$display .= "<td>{$tot}</td>";
					//$ddd[] = $tot;
					if(!@$t[$t_counter])
						$t[$t_counter] = $tot;
					else
						$t[$t_counter] += $tot;
					$t_counter++;
					$tm = 0;//formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT mu_tm.TicketPaid FROM mu_tm WHERE mu_tm.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=false,$con);
					//$display .= "<td>{$tm['TicketPaid']}</td>";
					//$ddd[] = $tm['TicketPaid'];
					if(!@$t[$t_counter])
						$t[$t_counter] = $tm['TicketPaid'];
					else
						$t[$t_counter] += $tm['TicketPaid'];
					$t_counter++;
					$tp_tot = $tot - $tm['TicketPaid'];
					//$display .= "<td>{$tp_tot}</td>";
					//$ddd[] = $tp_tot;
					if(!@$t[$t_counter])
						$t[$t_counter] = $tp_tot;
					else
						$t[$t_counter] += $tp_tot;
					$t_counter++;
				$display .= "</tr>";
				//check if the current insurance has ever exist before
				if(isset($total_patients_insurances[$r['ServiceCode']])){
					//increment its value
					$total_patients_insurances[$r['ServiceCode']]++;
				} else{
					$total_patients_insurances[$r['ServiceCode']] = 1;
				}
				
				//check if the current insurance has ever exist before
				if(isset($total_patients[$r['InsuranceName']][$r['ServiceCode']])){
					//increment its value
					$total_patients[$r['InsuranceName']][$r['ServiceCode']]++;
				} else{
					$total_patients[$r['InsuranceName']][$r['ServiceCode']] = 1;
				}
				
				if($tp_tot <= 0){
					$display = "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
					<td>".(++$c_i)."</td>".$display;
					//var_dump($tp_tot);
					//check if the current service has ever exist before
					if(isset($total_empty_data_services[$r['ServiceCode']])){
						//increment its value
						$total_empty_data_services[$r['ServiceCode']]++;
					} else{
						$total_empty_data_services[$r['ServiceCode']] = 1;
					}
					
					//check if the current service has ever exist before
					if(isset($total_empty_data[$r['InsuranceName']][$r['ServiceCode']])){
						//increment its value
						$total_empty_data[$r['InsuranceName']][$r['ServiceCode']]++;
					} else{
						$total_empty_data[$r['InsuranceName']][$r['ServiceCode']] = 1;
					}
					//check if the current insurance has ever exist before
					if(isset($total_empty_data_insurances[$r['InsuranceName']])){
						//increment its value
						$total_empty_data_insurances[$r['InsuranceName']]++;
					} else{
						$total_empty_data_insurances[$r['InsuranceName']] = 1;
					}
				}
			}
		
		//var_dump($total_empty_data_services,$total_empty_data_insurances,$total_patients_insurances);
		//write the loop to produce all necessary output now
		$str = "";
		$tot = 0;
		foreach($total_empty_data_services as $srv=>$number){
			$str .= "$(\".empty{$srv}\").html(\"{$number}\");
			";
			$tot += $number;
		}
		$str .= "$(\".emptyServiceTotal\").html(\"<a target='_blank' href='./empty_file_all.php?day=".(explode("-",$_POST['date'])[2])."&month=".(explode("-",$_POST['date'])[1])."&year=".(explode("-",$_POST['date'])[0])."'>{$tot}</a>\");
			";
		$tot = 0;
		foreach($total_empty_data_insurances as $ins=>$number){
			$str .= "$(\".empty".str_replace(" ","",strtoupper($ins))."\").html(\"{$number}\");
			";
			$tot += $number;
		}
		foreach($total_empty_data as $ins=>$data_){
			$tot_ins = 0;
			foreach($data_ as $srv=>$number){
				$str .= "$(\".empty".str_replace(" ","",strtoupper($ins)).str_replace(" ","",strtoupper($srv))."\").html(\"{$number}\");
				";
				$tot_ins += $number;
			}
		}
		$str .= "$(\".emptyInsuranceTotal\").html(\"{$tot}\");
			";
		
		// var_dump($total_patients);
		//$patient_summary_id = ;
		foreach($total_patients as $ins=>$data_){
			$tot_ins = 0;
			foreach($data_ as $srv=>$number){
			
				$str .= "$(\".totals".str_replace(" ","",strtoupper($ins)).str_replace(" ","",strtoupper($srv))."\").html(\"{$number}\");
				";
				$tot_ins += $number;
				//save in the pa_summary table
				if($patient_summary_id = returnSingleField("SELECT PatientSummaryID FROM pa_summary WHERE Date='{$date}' && InsuranceName='{$ins}'","PatientSummaryID",true,$con)){
					//update the record with new information
					saveData("UPDATE pa_summary SET `{$srv}`='{$number}' WHERE PatientSummaryID='{$patient_summary_id}'",$con);
				} else{
					//save new record now
					saveData("INSERT INTO pa_summary SET InsuranceName = '{$ins}', Date='{$date}', `{$srv}`='{$number}'",$con);
				}
			}
			$str .= "$(\".totals".str_replace(" ","",strtoupper($ins))."\").html(\"{$tot_ins}\");
			";
		}
		$tot = 0;
		//var_dump($total_patients);
		foreach($total_patients_insurances as $ins=>$number){
			
			$str .= "$(\".totals".str_replace(" ","",strtoupper($ins))."\").html(\"{$number}\");
			";
			$tot += $number;
		}
		$str .= "$(\".totalspatientsServices\").html(\"{$tot}\");
			";
		//var_dump($total_empty_data);
		//echo $sql;
		//echo $display;
		?>
		<script><?= $str ?></script>
		<?php
	} else{
		echo "<span class=error-text>No Patient in the selected month {$_POST['date']} at selected station {$post}</span>";
	}
}
?>