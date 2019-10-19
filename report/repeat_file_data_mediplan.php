<?php
session_start();
//var_dump($_SESSION);
set_time_limit(0);
if(@$_GET['rg'] == "generate"){
	//force data to searched now
	unset($_SESSION['data_display']);
}
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
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
	//select all possible information on the coming id
	//$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientRecordID, pa_records.FamilyCategory, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center, co_records WHERE co_records.PatientRecordID = pa_records.PatientRecordID && pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && InsuranceNameID='{$_GET['key']}' && DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') ORDER BY pa_records.DateIn ASC, pa_records.PatientRecordID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 
	$reference = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.ServiceCode, pa_records.PatientRecordID, pa_records.DateIn, pa_records.InsuranceCardID FROM pa_records, se_records, se_name, sy_users, sy_center WHERE pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && InsuranceNameID='{$_GET['key']}' && DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') ORDER BY pa_records.DateIn ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 
	//var_dump($reference);
	echo $sql; //die;
	if($reference){
		//start a loop to check if a given code is repeated with the provided range
		$kk__ = 0;
		$printed_ids = array();
	
			?>
			<b class=visibl>
			<style>
				table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
				.r1{ background-color:#a5a5a5; }
				.r2{ background-color:#c5c5c5; }
			</style>
			<span class=styling></span>
			<div style='height:90%; padding-top:5px; border:0px solid #000; overflow:auto;'>
			<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
				<tr><th>ID</th><th>Date</th><th>Service</th><th>DocID</th><th>Insurance</th><th>Age</th><th>Sex</th><th>Name</th><th>House Holder</th><th>Family Code</th><th>Cons Cost</th><th>Lab</th><th>Imaging</th><th>Hosp.</th><th>Proc & Mat.</th><th>Consum.</th><th>Drugs.</th><th>Total</th><th>Dett. Fees</th><th>Total to be Paid</th></tr>
				<?php
		$rpt_counter = 0; $change = 1;
		if(!@$_SESSION['data_display']){
			foreach($reference as $ref){
				$srvice = $ref['ServiceCode'];
				$start_day = "";
				$end_day = "";
				$select_data = true;
				$three_day = 60*60*24*2; //seconds of two days
				$to_day = mktime(0,0,0,explode("-",$ref["DateIn"])[1],explode("-",$ref["DateIn"])[2],explode("-",$ref["DateIn"])[0]); // seconds of to day
				switch($srvice){
					case "PST":
						$end_day = $start_day = $ref['DateIn']; //the same day only
						break;
					case "CPC":
					case "CPN":
					case "MAT":
						$start_day = date("Y-m-d",($to_day - $three_day)); //three days before this one
						$end_day = date("Y-m-d",($to_day + $three_day)); //three days after this one
						break;
					default:
						$select_data = false;
				}
				if(!$select_data)
					continue;
				//echo $ref['ServiceCode'].": Start FROM ".$start_day." End To:".$end_day."<br />";
				//die;
				//check if the patient record id is displayed and skip it
				if(in_array($ref['PatientRecordID'],$printed_ids))
					continue;
				//select all the current insurance code
				$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.ServiceCode, pa_records.PatientRecordID, pa_records.DocID, pa_records.FamilyCategory, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, se_records, se_name, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center WHERE pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && InsuranceNameID='{$_GET['key']}' && pa_records.InsuranceCardID='{$ref['InsuranceCardID']}' && DateIn >= '{$start_day}' && DateIn <= '{$end_day}' ORDER BY pa_records.DateIn ASC, pa_records.PatientRecordID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 
				//bck ==> $patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.ServiceCode, pa_records.PatientRecordID, pa_records.DocID, pa_records.FamilyCategory, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, se_records, se_name, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center WHERE pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && InsuranceNameID='{$_GET['key']}' && DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') ORDER BY pa_records.InsuranceCardID ASC, pa_records.DateIn DESC, pa_records.PatientRecordID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 
				if(count($patients) <= 1)
					continue;
				//var_dump($patients); continue;
				//echo $sql;
				//pick the color to be used for better reference
				$color = $change?"r1":"r2";
				$change = !$change;
				//print all found without filter because they already filtered
				if($patients){
					
						$t = array();
						$data = array();
						$data[] = array("Nr","Date","Service","Cat.","Insurance","Age","Sex","Name","House Holder","Family Code","Cons Cost","Lab","Imaging","Hosp","Procedures","Consumables","Drugs","Total","Detterent","Total to be paid");
						$c_i = 0;
						$existing_patient = array(array());
						$one_day = 60*60*24;
						$two_day = 60*60*24*2;
						
						for($i=0;$i<count($patients);$i++){
							$r = $patients[$i];
							$ddd = array();
							
							$display = "<td>{$r["DateIn"]}</td>
								<td>{$r['ServiceCode']}</td>
								<td>{$r['DocID']}</td>
								<td>{$r["InsuranceCardID"]}</td>
								<td>".($patients[$i]["DateofBirth"] == "0000-00-00"?"":$patients[$i]["DateofBirth"])."</td>
								<td>{$patients[$i]["Sex"]}</td>
								<td>{$patients[$i]["Name"]}</td>
								<td>{$patients[$i]["FamilyCode"]}</td>
								<td>".(@explode('/',$r['InsuranceCardID'])[2])."</td>";
								
								$ddd[] = ($i+1);
								$ddd[] = $r["ServiceCode"];
								$ddd[] = $r["DateIn"];
								$ddd[] = $r['FamilyCategory'];
								$ddd[] = $r["InsuranceCardID"];
								$ddd[] = $r["DateofBirth"];
								$ddd[] = $r["Sex"];
								$ddd[] = $r["Name"];
								$ddd[] = $r["FamilyCode"];
								$ddd[] = (@explode('/',$r['InsuranceCardID'])[2])." ";
								
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
								$display .= "<td>{$cons}</td>";
								$ddd[] = $cons;
								$tot += $cons;
								if(!@$t[$t_counter])
									$t[$t_counter] = $cons;
								else
									$t[$t_counter] += $cons;
								$t_counter++;
								$display .= "<td>{$lab}</td>";
								$ddd[] = $lab;
								$tot += $lab;
								if(!@$t[$t_counter])
									$t[$t_counter] = $lab;
								else
									$t[$t_counter] += $lab;
								$t_counter++;
								$display .= "<td>&nbsp;</td>";
								$ddd[] = "";
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
								$display .= "<td>{$hosp}</td>";
								$ddd[] = $hosp;
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
								$display .= "<td>{$acts}</td>";
								$ddd[] = $acts;
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
								$display .= "<td>{$consumables}</td>";
								$ddd[] = $consumables;
								$tot += $consumables;
								if(!@$t[$t_counter])
									$t[$t_counter] = $consumables;
								else
									$t[$t_counter] += $consumables;
								$t_counter++;
								$display .= "<td>{$md}</td>";
								$ddd[] = $md;
								$tot += $md;
								if(!@$t[$t_counter])
									$t[$t_counter] = $md;
								else
									$t[$t_counter] += $md;
								$t_counter++;
								$display .= "<td>{$tot}</td>";
								$ddd[] = $tot;
								if(!@$t[$t_counter])
									$t[$t_counter] = $tot;
								else
									$t[$t_counter] += $tot;
								$t_counter++;
								$tm = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT mu_tm.TicketPaid FROM mu_tm WHERE mu_tm.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=false,$con);
								$display .= "<td>{$tm['TicketPaid']}</td>";
								$ddd[] = $tm['TicketPaid'];
								if(!@$t[$t_counter])
									$t[$t_counter] = $tm['TicketPaid'];
								else
									$t[$t_counter] += $tm['TicketPaid'];
								$t_counter++;
								$tp_tot = $tot - $tm['TicketPaid'];
								//enable editing and deleting
								$display .= "<td>
								<a title='Click to Edit File' href='../rcp/reprint.php?key={$r['DocID']}&patientid={$r['PatientRecordID']}&rcv_patient=".sha1('receive_patient')."' style='color:blue; text-decoration:none;' target='_blank'>{$tp_tot}</a>
								<a href='#' onclick='if(confirm(\"Delete Completely the File For {$patients[$i]["Name"]} - {$r["InsuranceCardID"]} - {$r["DocID"]} From The System\")){ deleteProfileNow(\"{$r['PatientRecordID']}\",\"{$rpt_counter}\"); } return false;' style='color:red; font-size:10px; font-weight:bold; text-decoration:none; float:right;' title='Delete The Patient File'>X</a>
								</td>";
								$ddd[] = $tp_tot;
								if(!@$t[$t_counter])
									$t[$t_counter] = $tp_tot;
								else
									$t[$t_counter] += $tp_tot;
								$t_counter++;
							$display .= "</tr>";
							
							$last_day = date("Ymd",($one_day + mktime(0,0,0,explode("-",$r["DateIn"])[1],explode("-",$r["DateIn"])[2],explode("-",$r["DateIn"])[0])));
							$two_last_day = date("Ymd",($two_day + mktime(0,0,0,explode("-",$r["DateIn"])[1],explode("-",$r["DateIn"])[2],explode("-",$r["DateIn"])[0])));
							$three_last_day = date("Ymd",($three_day + mktime(0,0,0,explode("-",$r["DateIn"])[1],explode("-",$r["DateIn"])[2],explode("-",$r["DateIn"])[0])));
							$existing_patient[$to_day][0] = "";
							$existing_patient[$last_day][0] = "";
							$existing_patient[$two_last_day][0] = "";
							$existing_patient[$three_last_day][0] = "";
							
								$display = "<tr class='{$color}' onclick='$(\".styling\").html(\"<style>#id{$rpt_counter}{background-color:#e5e5e3;}</style>\");' id='id{$rpt_counter}'>
								<td>".(++$rpt_counter)."</td>".$display;
								echo $display;
								$_SESSION['data_display'][] = $display;
								$data[] = $ddd;
							//map the patientrecordid as printed to never repeat it again
							$printed_ids[] = $r['PatientRecordID'];
							
							/* if($tot >= -200){
								$display = "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
								<td>".(++$c_i)."</td>".$display;
								echo $display;
								$data[] = $ddd;
							} */
						}
				}
			}
		}  else{
			foreach($_SESSION['data_display'] as $tr_){
				echo $tr_;
			}
		}
				?>
				<!--
				<tr><th colspan=9>Total</th>
				<?php 
				
				$ddd = array();
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
					$tt = round($tt,0,PHP_ROUND_HALF_UP);
					//echo "<th>{$tt}</th>";
					$ddd[] = $tt;
				}
				$data[] = $ddd;
					?>
				</tr>-->
			</table>
			</div></b>
			<?php
			if(count($data)>0){
				$_SESSION['report'] = $data;
				$_SESSION['header'] = array(
											array("PROVINCE / MVK"=>"SUD"),
											array("ADMINISTRATIVE DISTRICT"=>"HUYE", "Period"=>$_GET['month']."/".$_GET['year']),
											array("ADMINISTRATIVE SECTION"=>"HUYE"),
											array("HEALTH FACILITY"=>"BUSORO HEALTH CENTER"),
											array("CODE / HEALTH FACILITY"=>"40211013"),
										);
				$_SESSION['report_title'] = array("title"=>"S U M M A R Y  OF V OUC H E R S  F O R  R W A N D A S O C I A L S E C U R I T Y B O A D (R S S B) / CBHI");
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
				<a href='./print_report.php?format=excel&in=cbhi' target="_blank"><img title="View in EXCEL" src="../images/excel.png" class=img_links width=40px /></a>
				<?php
				//echo "<pre>";var_dump($existing_patient);
			}
			//echo "<pre>";var_dump($data);
		/* } else{
			
			echo "<span class=error-text>No Match Found</span>";
		}
		 */
	}
}
?>
<script>
	function deleteProfileNow(record_id,index_){
		//alert(index_); return;
		$(".doc_selected").html("Deleting... <img src=' ../images/ajax_clock_small.gif' />");
		$.ajax({
			type: "POST",
			url: "../rcp/delete_patient_file.php",
			data: "record_id=" + record_id + "&index=" + index_,
			cache: false,
			success: function(result){
				$(".doc_selected").html(result);
				$(".patient_found").html("Please Wait... <br /><img src='../images/loading.gif' />");
				$(".patient_found").load("repeat_file_data.php?key=" + $("#insurance").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val());
			}
		});
	}
		
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