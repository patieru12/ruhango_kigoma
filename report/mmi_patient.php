<?php
session_start();
//var_dump($_SESSION);
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
		$sys .= "j.CenterName = '{$ps}'";
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
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT c.ServiceCode, 
																	a.PatientRecordID, 
																	a.DocID, 
																	a.DateIn, 
																	a.InsuranceCardID, 
																	d.*, 
																	e.*, 
																	f.*, 
																	g.*, 
																	h.*,
																	l.Name AS AffiliateName
																	FROM pa_records AS a
																	INNER JOIN se_records AS b
																	ON a.PatientRecordID = b.PatientRecordID
																	INNER JOIN se_name AS c
																	ON b.ServiceNameID = c.ServiceNameID
																	INNER JOIN pa_info AS d
																	ON a.PatientID = d.PatientID
																	INNER JOIN ad_village AS e
																	ON d.VillageID = e.ViillageID
																	INNER JOIN ad_cell AS f
																	ON e.CellID = f.CellID
																	INNER JOIN ad_sector AS g
																	ON f.SectorID = g.SectorID 
																	INNER JOIN ad_district AS h
																	ON g.DistrictID = h.DistrictID
																	INNER JOIN sy_users AS i
																	ON a.ReceptionistID = i.UserID
																	INNER JOIN sy_center AS j
																	ON i.CenterID = j.CenterID
																	INNER JOIN co_records AS k
																	ON a.PatientRecordID = k.PatientRecordID 
																	LEFT JOIN pa_info AS l
																	ON d.FamilyCode = l.FamilyCode AND l.AffiliateName = '@1'
																	WHERE {$sys}  && a.InsuranceNameID='{$_GET['key']}' && a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') ORDER BY DateIn ASC, DocID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 

//echo $sql;
if($patients){
	?>
	<b class=visibl>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
	</style>
	<span class=styling></span>
	<div style='max-height:90%; padding-top:5px; border:0px solid #000; overflow:auto;'>
	<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
																																												<!--	Total	85%-->
		<tr><th>No</th><th>Service</th><th>No du Voucher</th><th>Date</th><th>No du bénéficiaire</th><th>Nom et Prénom du bénéficiaire</th><th>Affiliate Name</th><th>Consultation</th><th>Examens</th><th>Imaging</th><th>Hospitalisation</th><th>Acte</th><th>Consommables</th><th>Medicaments</th><th>Autres</th><th>Total</th><th>Total 85%</th></tr>
		<?php
		$t = array();
		$data = array();

		$data[] = array("No","Service","No du Voucher","Date","No du bénéficiaire","Nom et Prénom du bénéficiaire","Consultation","Examens","Imaging","Hospitalisation","Acte","Consumables","Medicaments","Autres","Total","Total 85%");
		for($i=0;$i<count($patients);$i++){
			$affiliation = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_insurance_cards.* FROM pa_insurance_cards WHERE pa_insurance_cards.PatientID = '{$patients[$i]['PatientID']}' && Status=1",$con),$multirows=false,$con);
	
			$r = $patients[$i];
			$ddd = array();
			echo "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
				<td>".($i+1)."</td>
				<td>{$r['ServiceCode']}</td>
				<td>{$r['DocID']}</td>
				<td>{$r["DateIn"]}</td>
				<td>{$r["InsuranceCardID"]}</td>
				<td>{$patients[$i]["Name"]}</td>
				<td>{$patients[$i]["AffiliateName"]}</td>";
				
				$ddd[] = ($i+1);
				$ddd[] = $r['ServiceCode'];
				$ddd[] = $r['DocID'];
				$ddd[] = $r["DateIn"];
				$ddd[] = $r["InsuranceCardID"];
				$ddd[] = $r["Name"];
				$ddd[] = $r["AffiliateName"];
				
				//select the laboratory fees related to this request now
				$conslt = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT co_price.Amount, co_records.ConsultationRecordID FROM co_records, co_price WHERE co_records.ConsultationPriceID = co_price. ConsultationPriceID && co_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$cons = 0; $lab = 0; $md = 0; $tot = 0; $t_counter = 0; $other = 0;
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
				echo "<td>{$cons}</td>";
				$ddd[] = $cons;
				$tot += $cons;
				if(!@$t[$t_counter])
					$t[$t_counter] = $cons;
				else
					$t[$t_counter] += $cons;
				$t_counter++;
				echo "<td>{$lab}</td>";
				$ddd[] = $lab;
				$tot += $lab;
				if(!@$t[$t_counter])
					$t[$t_counter] = $lab;
				else
					$t[$t_counter] += $lab;
				$t_counter++;
				echo "<td>&nbsp;</td>";
				$ddd[] = "";
				if(!@$t[$t_counter])
					$t[$t_counter] = 0;
				else
					$t[$t_counter] += 0;
				$t_counter++;
				$ho = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ho_price.Amount, DATEDIFF( ho_record.EndDate, ho_record.StartDate) AS Days FROM ho_record, ho_price WHERE ho_record.HoPriceID = ho_price. HOPriceID && ho_record.RecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$hosp = 0;
				if($ho){
					foreach($ho as $c){
						$hosp += ($c['Amount'] * $c['Days']);
						
					}
				}
				echo "<td>{$hosp}</td>";
				$ddd[] = $hosp;
				$tot += $hosp;
				if(!@$t[$t_counter])
					$t[$t_counter] = $hosp;
				else
					$t[$t_counter] += $hosp;
				$t_counter++;
				//now select acts and print the price in the table
				$act = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ac_price.Amount, ac_records.ActRecordID, ac_records.Quantity FROM ac_records, ac_price WHERE ac_records.ActPriceID = ac_price. ActPriceID && ac_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$acts = 0;
				if($act){
					foreach($act as $c){
						$acts += ($c['Amount'] * $c['Quantity']);
						
					}
				}
				echo "<td>{$acts}</td>";
				$ddd[] = $acts;
				$tot += $acts;
				if(!@$t[$t_counter])
					$t[$t_counter] = $acts;
				else
					$t[$t_counter] += $acts;
				$t_counter++;
				//now select consumables and print the price in the table
				$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT cn_price.Amount, cn_records.Quantity FROM cn_records, cn_price WHERE cn_records.MedecinePriceID = cn_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$consumables = 0;
				if($consum){
					foreach($consum as $c){
						$consumables += ($c['Amount'] * $c['Quantity']);
						
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
					$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT cn_price.Amount, cn_records.Quantity FROM cn_records, cn_price WHERE cn_records.MedecinePriceID = cn_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
					$consumables = 0;
					if($consum){
						foreach($consum as $c){
							$consumables += ($c['Amount'] * $c['Quantity']);
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
				$t_counter++;
				echo "<td>{$md}</td>";
				$ddd[] = $md;
				$tot += $md;
				if(!@$t[$t_counter])
					$t[$t_counter] = $md;
				else
					$t[$t_counter] += $md;
				$t_counter++;
				//autres
				echo "<td></td>";
				$ddd[] = null;
				if(!@$t[$t_counter])
					$t[$t_counter] = $other;
				else
					$t[$t_counter] += $other;
				$t_counter++;
				
				echo "<td>{$tot}</td>";
				$ddd[] = $tot;
				if(!@$t[$t_counter])
					$t[$t_counter] = $tot;
				else
					$t[$t_counter] += $tot;
				
				$t_counter++;
				$tp_tot = $tot * 0.85;
				echo "<td align=right>".round($tp_tot)."</td>";
				$ddd[] = $tp_tot;
				if(!@$t[$t_counter])
					$t[$t_counter] = $tp_tot;
				else
					$t[$t_counter] += $tp_tot;
				$t_counter++;
			echo "</tr>";
			$data[] = $ddd;
		}
		?>
		<tr><th colspan=7>Total</th>
		<?php 
		$ddd = array();
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "";
		$ddd[] = "TOTAL";
		$ddd[] = "";
		foreach($t as $tt){
			echo "<th>".round($tt,1)."</th>";
			$ddd[] = $tt;
		}
		$data[] = $ddd;
			?>
		</tr>
	</table>
	</div>
	</b>
	<?php
	if(count($data)>0){
		$_SESSION['report'] = $data;
		$_SESSION['header'] = array(
									array("PROVINCE / MVK"=>strtoupper($_PROVINCE)),
									array("ADMINISTRATIVE DISTRICT"=>strtoupper($_DISTRICT), "Period"=>$_GET['month']."/".$_GET['year']),
									array("ADMINISTRATIVE SECTION"=>strtoupper($_SECTOR)),
									array("HEALTH FACILITY"=>strtoupper($organisation)),
									array("CODE / HEALTH FACILITY"=>$organisation_code_minisante),
								);
		$_SESSION['report_title'] = array("title"=>"RELEVE DES FACTURES A L'ASSURANCE MALADIE DES MILITAIRES (MMI) No {$_GET['year']}/{$_GET['month']}");
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
		<!-- <a href='./print_report.php?format=pdf' target="_blank"><img title='View in PDF' src="../images/b_pdfdoc.png" class=img_links width=30px /></a> -->
		<a href='#print_report.php?format=excel&in=mmi' onclick="printExcel(); return false;"><img title="View in EXCEL" src="../images/excel.png" class=img_links width=40px /></a>
		<span id="print_result"></span>
		<script type="text/javascript">
			function printExcel(){
				$("#print_result").html("Please Wait <img src='../images/loading.gif' />");
				$.ajax({
					type: "GET",
					url: "./print_report.php",
					data: "format=excel&in=mmi",
					cache: false,
					success: function(result){
						$("#print_result").html(result)
						
					},
					error: function(err){
						console.log(err.responseText);
					}
				});
			}
		</script><?php
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