<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
$prefix = returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con);
if(!in_array($prefix , array("rcp","com")) ){
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
	//select all possible information on the comming id
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.ServiceCode, pa_records.PatientRecordID, pa_records.serialNumber AS DocID, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, se_records, se_name, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center, co_records WHERE pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && InsuranceNameID='{$_GET['key']}' && DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') ORDER BY DateIn ASC, DocID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 

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
		
		<tr><th>No</th><th>Date</th><th>Service</th><th>VOUCHER ID.</th><th>Affiliation N.</th><th>Age</th><th>Sex</th><th>Name</th><th>Affiliate Name</th><th>Affectation</th><th>Cons Cost</th><th>Lab</th><th>Imaging</th><th>Hosp.</th><th>Proc & Mat.</th><th>Consum.</th><th>Drugs.</th><th>Total</th><th>Total 85%</th></tr>
		<?php
		$t = array();
		$data = array();
		$data[] = array("Nr","Date","Service","Voucher ID.","Aff. Number","Age","Sex","Name","Affiliate Name","Affectation","Consult","Lab","Imaging","Hosp","Procedures","Consumables","Drugs","Total","Total 85%");
		for($i=0;$i<count($patients);$i++){
			$affiliation = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_insurance_cards.* FROM pa_insurance_cards WHERE pa_insurance_cards.PatientID = '{$patients[$i]['PatientID']}' && Status=1",$con),$multirows=false,$con);
	
			$r = $patients[$i];
			$ddd = array();
			echo "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
				<td>".($i+1)."</td>
				<td>{$r["DateIn"]}</td>
				<td>{$r["ServiceCode"]}</td>
				<td>{$r['DocID']}</td>
				<td>{$r["InsuranceCardID"]}</td>
				<td>".($patients[$i]["DateofBirth"] == "0000-00-00"?"":$patients[$i]["DateofBirth"])."</td>
				<td>{$patients[$i]["Sex"]}</td>
				<td>{$patients[$i]["Name"]}</td>
				<td>{$affiliation["AffiliateName"]}</td>
				<td>".$affiliation['AffiliateNumber']."</td>";
				
				$ddd[] = ($i+1);
				$ddd[] = $r["DateIn"];
				$ddd[] = $r["ServiceCode"];
				$ddd[] = $r['DocID'];
				$ddd[] = $r["InsuranceCardID"];
				$ddd[] = $r["DateofBirth"];
				$ddd[] = $r["Sex"];
				$ddd[] = $r["Name"];
				$ddd[] = $affiliation["AffiliateName"];
				$ddd[] = $affiliation["AffiliateNumber"];
				
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
								$lab += round($l['Amount']);
							}
						}
						//select all drugs related to this consultion record now
						$medecines = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT md_price.Amount, md_records.Quantity FROM md_records, md_price WHERE md_records.MedecinePriceID = md_price. MedecinePriceID && md_records.ConsultationRecordID='{$c['ConsultationRecordID']}'",$con),$multirows=true,$con);
						if($medecines){
							foreach($medecines as $l){
								$md += (round($l['Amount']) * $l['Quantity']);
							}
						}
					}
				}
				echo "<td>{$cons}</td>";
				$ddd[] = $cons;
				$tot += $cons;
				if(!@$t[$t_counter])
					$t[$t_counter] = round($cons);
				else
					$t[$t_counter] += round($cons);
				$t_counter++;
				echo "<td>{$lab}</td>";
				$ddd[] = round($lab);
				$tot += round($lab);
				if(!@$t[$t_counter])
					$t[$t_counter] = round($lab);
				else
					$t[$t_counter] += round($lab);
				$t_counter++;
				echo "<td>&nbsp;</td>";
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
						$hosp += (round($c['Amount']) * $c['Days']);
						
					}
				}
				echo "<td>{$hosp}</td>";
				$ddd[] = $hosp;
				$tot += $hosp;
				if(!@$t[$t_counter])
					$t[$t_counter] = round($hosp);
				else
					$t[$t_counter] += round($hosp);
				$t_counter++;
				//now select acts and print the price in the table
				$act = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ac_price.Amount, ac_records.ActRecordID, ac_records.Quantity FROM ac_records, ac_price WHERE ac_records.ActPriceID = ac_price. ActPriceID && ac_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$acts = 0;
				if($act){
					foreach($act as $c){
						$acts += (round($c['Amount'])*$c['Quantity']);
						
					}
				}
				echo "<td>{$acts}</td>";
				$ddd[] = round($acts);
				$tot += round($acts);
				if(!@$t[$t_counter])
					$t[$t_counter] = round($acts);
				else
					$t[$t_counter] += round($acts);
				$t_counter++;
				//now select consumables and print the price in the table
				$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT cn_price.Amount, cn_records.Quantity FROM cn_records, cn_price WHERE cn_records.MedecinePriceID = cn_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$consumables = 0;
				if($consum){
					foreach($consum as $c){
						$consumables += (round($c['Amount']) * $c['Quantity']);
						
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
							$consumables += (sounr($c['Amount']) * $c['Quantity']);
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
							$consumables += (round($c['Amount']) * $c['Quantity']);
						}
					}
				}
				//add fiche de consultation on bill
				//$consumables += FICHE;
				echo "<td>{$consumables}</td>";
				$ddd[] = $consumables;
				$tot += $consumables;
				if(!@$t[$t_counter])
					$t[$t_counter] =round($consumables);
				else
					$t[$t_counter] += round($consumables);
				$t_counter++;
				echo "<td>{$md}</td>";
				$ddd[] = round($md);
				$tot += round($md);
				if(!@$t[$t_counter])
					$t[$t_counter] = round($md);
				else
					$t[$t_counter] += round($md);
				$t_counter++;
				echo "<td>{$tot}</td>";
				$ddd[] = round($tot);
				if(!@$t[$t_counter])
					$t[$t_counter] = round($tot);
				else
					$t[$t_counter] += round($tot);
				/* $t_counter++;
				$tm = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT mu_tm.TicketPaid FROM mu_tm WHERE mu_tm.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=false,$con);
				echo "<td>{$tm['TicketPaid']}</td>";
				$ddd[] = $tm['TicketPaid'];
				if(!@$t[$t_counter])
					$t[$t_counter] = $tm['TicketPaid'];
				else
					$t[$t_counter] += $tm['TicketPaid']; */
				$t_counter++;
				$tp_tot = $tot * 0.85;
				echo "<td>".round($tp_tot)."</td>";
				$ddd[] = $tp_tot;
				if(!@$t[$t_counter])
					$t[$t_counter] = round($tp_tot);
				else
					$t[$t_counter] += round($tp_tot);
				$t_counter++;
			echo "</tr>";
			$data[] = $ddd;
		}
		?>
		<tr><th colspan=10>Total</th>
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
		?>
		</tr>
		<tr><th colspan=10>Total</th>
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
		$empty =  0;
		foreach($t as $tt){
			if($empty++ == (count($t) -1) ){
				continue;
			}
			echo "<th>".round( ($tt*0.85),1)."</th>";
			$ddd[] = ($tt*0.85);
		}

		// $ddd[(count($ddd)-1)] = ""; //

		$data[] = $ddd;
			?>
		</tr>
	</table>
	</div></b>
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
		$_SESSION['report_title'] = array("title"=>"S U M M A R Y   OF  V O U C H E R S   F O R   R W A N D A  S O C I A L  S E C U R I T Y  B O A D (R S S B)");
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
		<!--<a href='./print_report.php?format=pdf' target="_blank"><img title='View in PDF' src="../images/b_pdfdoc.png" class=img_links width=30px /></a> -->
		<a href='#./print_report.php?format=excel&in=rama' onclick="printExcel(); return false;"><img title="View in EXCEL" src="<?= in_array($prefix, array("com"))?"../../":"../";?>images/excel.png" class=img_links width=40px /></a>
		<span id="print_result"></span>
		<script type="text/javascript">
			function printExcel(){
				$("#print_result").html("Please Wait <img src='<?= in_array($prefix, array("com"))?"../../":"../";?>images/loading.gif' />");
				$.ajax({
					type: "GET",
					url: "<?= in_array($prefix, array("com"))?"../../report/":"./";?>print_report.php",
					data: "format=excel&in=rama",
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