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
		$sys .= "sy_center.CenterName = '{$ps}'";
	}
}
$sys .= ")";
if(!$ok){
	echo "<span class=error>No Post Selected</span>";
	return;
}
//var_dump($_GET);
$sp_condition = "";
if(@$_GET['filter']){
	$sp_condition .= " && (
		pa_records.InsuranceCardID LIKE('%{$_GET['filter']}%') ||
		pa_info.Name LIKE('%{$_GET['filter']}%') ||
		pa_info.FamilyCode LIKE('%{$_GET['filter']}%')
	)";
}
//echo $sys;
if(strlen($_GET['key'])){
	//select all possible information on the comming id
	//$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientRecordID, pa_records.DocID, pa_records.FamilyCategory, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center WHERE pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && InsuranceNameID='{$_GET['key']}' && DateIn LIKE('{$_GET['year']}-{$_GET['month']}-{$_GET['day']}') {$sp_condition} ORDER BY pa_records.DateIn ASC, pa_records.PatientRecordID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.ServiceCode, pa_records.PatientRecordID, pa_records.DocID, pa_records.FamilyCategory, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, se_records, se_name, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center WHERE pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && InsuranceNameID='{$_GET['key']}' && DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') {$sp_condition} ORDER BY pa_records.DateIn ASC, pa_records.PatientRecordID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 

//echo $sql;
if($patients){
	?>
	<script>
		$("#filter").click(function(e){
			$(".patient_found").load("document_list_private.php?key=" + $("#insurance").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + prompt("Enter Filter Key",'<?= @$_GET['filter'] ?>').replace(/ /g,"%20"));
			return e.preventDefault();
		});
		$("#filter_remove").click(function(e){
			$("#filter_").val("");
			$(".patient_found").load("document_list_private.php?key=" + $("#insurance").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val());
			return e.preventDefault();
		});
		//$("#filter").focus();
		function deleteProfileNow(record_id){
			$.ajax({
				type: "POST",
				url: "./delete_patient_file.php",
				data: "record_id=" + record_id,
				cache: false,
				success: function(result){
					$(".doc_selected").html(result);
					$(".patient_found").load("document_list_private.php?key=" + $("#insurance").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
				}
			});
		}
	</script>
	<b class=visibl>
	<span class=success style='font-weight:bold; font-size:20px;'>
	Private Prestation Document<br />
	Post: <?= $post ?><br />
	Date: <?= $_GET['year']."/".$_GET['month'] ?><br />
	Number: <?= count($patients) ?> Patient<?= count($patients)>1?"s":"" ?> 
	</span>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
		.number_right{ text-align:right; }
	</style>
	<span class=styling></span>
	<?= @$_GET['filter']?"<script>$('#filter_').val('{$_GET['filter']}');</script><br /><span class=error-text>".count($patients)." Result".(count($patients)>1?"s":"")." found for ".$_GET['filter']."</span>":"" ?> <span style='float:right;'><?= @$_GET['filter']?"<a href='#' id=filter_remove style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' ><img src='../images/filter_remove.png' /> Remove Filter</a>":"" ?><a href='#' id=filter style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' > <img src='../images/filter.png' /> Filter </a></span>
	<div style='height:73%; margin-top:2px; width:100%; border:0px solid #000; overflow:auto;'>
	
	<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
		<tr><th>ID</th><th>Date</th><th>Service</th><th>Name</th><th>House Holder</th><th>Consul.</th><th>Labo</th><th>Price</th><th colspan=4>Medicines</th><th>Hosp</th><th>Price</th><th>Actes</th><th>Price</th><th>Consum.</th><th>Price</th><th>G. Total</th><th>TM</th><th>Payable Total</th></tr>
		<tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th>Nature</th><th>QTY</th><th>UN.P</th><th>TOT.P</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
		<tbody>
		<?php
		$t = array();
		$kk_ = "";
		$data = array();
		$data[] = array("ID","Code Bebeneficiaire","Nom Beneficiaire","Chef de Famille","Consul.","Test Labo","Price","Medicines","Hosp.","Price","Act","Price","Consum","Price","General Total","TM","Payable Total");
		
		$data[] = array("","","","","","","","Nature","QTY","UN.P","TOT.P","","","","","","","","","");
		//var_dump($data); die;
		$row_count = 0;
		for($i=0;$i<count($patients);$i++){
			$r = $patients[$i];
			$ddd = array();
			$tot = 0;
			$tm_p = returnSingleField("SELECT TicketPaid FROM mu_tm WHERE PatientRecordID='{$patients[$i]['PatientRecordID']}'","TicketPaid",true,$con);
			$tm_i = returnSingleField("SELECT TicketID FROM mu_tm WHERE PatientRecordID='{$patients[$i]['PatientRecordID']}'","TicketID",true,$con);
			$row_count++;
			$kk_ .=  "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
				<td>".($i+1)."</td>
				<td>".($r['DateIn'])."</td>
				<td>".($r['ServiceCode'])."</td>
				<td>{$patients[$i]["Name"]}</td>
				<td>{$patients[$i]["FamilyCode"]}</td>
				";
			$ddd[] = $i+1;
			$ddd[] = $r['InsuranceCardID'];
			$ddd[] = $patients[$i]['Name'];
			$ddd[] = $patients[$i]['FamilyCode'];
			
				//select the laboratory fees related to this request now
				$conslt = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT co_price.Amount, co_records.ConsultationRecordID FROM co_records, co_price WHERE co_records.ConsultationPriceID = co_price. ConsultationPriceID && co_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$cons = 0; $lab = 0; $md = 0; $tot = 0; $t_counter = 0;
				$lab_rows = 0;$mdo_rows = 0;
				$labo = array(); $labo_content = array(); $labo_count = 0;
				$mdo = array(); $mdo_content = array(); $mdo_count = 0;
				$consul_found = array(); $consul_found_count = 0;
				if($conslt){
					foreach($conslt as $c){
						$consul_found_count++;
						$consul_found[] = round($c['Amount'],0);
						$cons += $c['Amount'];
						//select all exam related to this consultion record now
						$lab_exams = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT la_exam.ExamName, la_price.Amount, la_records.ExamRecordID FROM la_exam, la_records, la_price WHERE la_exam.ExamID = la_price.ExamID && la_records.ExamPriceID = la_price. ExamPriceID && la_records.ConsultationRecordID='{$c['ConsultationRecordID']}'",$con),$multirows=true,$con);
						if($lab_exams){
							foreach($lab_exams as $l){
								//result found in the test
								$lab_results = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT la_result.ResultName FROM la_result, la_result_record WHERE la_result.ResultID = la_result_record.ResultID && la_result_record.ExamRecordID = '{$l['ExamRecordID']}'",$con),$multirows=true,$con);
								//var_dump($lab_results);
								$rslt_lst = "";
								$c_c = 0;
								if($lab_results){
									foreach($lab_results as $lst){
										if($c_c++ > 0)
											$rslt_lst .= "; ";
										else
											$rslt_lst .= ":";
										$rslt_lst .= $lst['ResultName'];
									}
								}
								$lab_rows++;
								$labo[] = "<td>{$l['ExamName']}{$rslt_lst}</td><td class=number_right>".round($l['Amount'],0)."</td>";
								$labo_content[] = array($l['ExamName']." ".$rslt_lst, round($l['Amount'],0));
								$lab += $l['Amount'];
							}
						}
						//select all drugs related to this consultion record now
						$medecines = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT md_name.MedecineName, md_price.Amount, md_records.Quantity FROM md_name, md_records, md_price WHERE md_name.MedecineNameID = md_price.MedecineNameID && md_records.MedecinePriceID = md_price. MedecinePriceID && md_records.ConsultationRecordID='{$c['ConsultationRecordID']}'",$con),$multirows=true,$con);
						if($medecines){
							foreach($medecines as $l){
								$mdo_rows++;
								$mdo[] = "<td>{$l['MedecineName']}</td><td class='number_right edit{$row_count}1' >{$l['Quantity']}</td><td class=number_right>".round($l['Amount'],1)."</td><td>".($l['Amount'] * $l['Quantity'])."</td>";
								$mdo_content[] = array($l['MedecineName'],$l['Quantity'], round($l['Amount'],1), round(($l['Amount'] * $l['Quantity']),1));
								$md += ($l['Amount'] * $l['Quantity']);
							}
						}
					}
				}
				if(@$consul_found[0]){
					$kk_ .= "<td class='number_right edit{$row_count}1'>{$consul_found[0]}</td>";
					$ddd[] = $consul_found[0];
				} else{
					$kk_ .= "<td></td>";
					$ddd[] = "";
				}
				//add laboratory
				if(@$labo[0]){
					$kk_ .= $labo[0];
					$ddd[] = $labo_content[0][0];
					$ddd[] = $labo_content[0][1];
				} else{
					$kk_ .= "<td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
				}
				//add medecines
				if(@$mdo[0]){
					$kk_ .= $mdo[0];
					$ddd[] = $mdo_content[0][0];
					$ddd[] = $mdo_content[0][1];
					$ddd[] = $mdo_content[0][2];
					$ddd[] = $mdo_content[0][3];
				} else{
					$kk_ .= "<td></td><td></td><td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
					$ddd[] = "";
					$ddd[] = "";
				}
				//add hospitalization
				$ho = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ho_price.Amount, ho_record.Days FROM ho_record, ho_price WHERE ho_record.HoPriceID = ho_price. HOPriceID && ho_record.RecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$hosp = 0; $hospo = array(); $hospo_content = array(); $hospo_rows = 0;
				if($ho){
					foreach($ho as $c){
						$hospo_rows++;
						$hospo[] = "<td>{$c['Days']}</td><td class=number_right>".round(($c['Amount'] * $c['Days']),0)."</td>";
						$hospo_content[] = array($c['Days'],round(($c['Amount'] * $c['Days']),0));
						$hosp += ($c['Amount'] * $c['Days']);
					}
				}
				//add hospitalization the print queue
				if(@$hospo[0]){
					$kk_ .= $hospo[0];
					$ddd[] = $hospo_content[0][0];
					$ddd[] = $hospo_content[0][1];
				} else {
					$kk_ .= "<td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
				}
				
				//now select acts and print the price in the table
				$act = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ac_name.Name, ac_price.Amount, ac_records.ActRecordID FROM ac_name, ac_records, ac_price WHERE ac_name.ActNameID = ac_price.ActNameID && ac_records.ActPriceID = ac_price. ActPriceID && ac_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$acts = 0; $actso = array(); $actso_content = array(); $actso_rows = 0;
				if($act){
					foreach($act as $c){
						$actso_rows++;
						$actso[] = "<td>{$c['Name']}</td><td class=number_right>".round($c['Amount'],0)."</td>";
						$actso_content[] = array($c['Name'],round($c['Amount'],0));
						$acts += ($c['Amount']);
					}
				}
				//add acts to the print queue
				if(@$actso[0]){
					$kk_ .= $actso[0];
					$ddd[] = $actso_content[0][0];
					$ddd[] = $actso_content[0][1];
				} else{
					$kk_ .= "<td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
				}
				
				//now select consumables and print the price in the table
				$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT cn_name.MedecineName, cn_price.Amount, cn_records.Quantity FROM cn_name, cn_records, cn_price WHERE cn_name.MedecineNameID = cn_price.MedecineNameID && cn_records.MedecinePriceID = cn_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$consumables = 0; $consumo = array(); $consumo_content = array(); $consumo_rows = 0;
				if($consum){
					foreach($consum as $c){
						$consumo_rows++;
						$consumo[] = "<td>{$c['Quantity']} {$c['MedecineName']}</td><td class=number_right>".round(($c['Amount'] * $c['Quantity']),1)."</td>";
						$consumo_content[] = array($c['Quantity']." ".$c['MedecineName'], round(($c['Amount'] * $c['Quantity']),1));
						$consumables += ($c['Amount'] * $c['Quantity']);
						
					}
				}
				//echo "<td>{$consumables}</td>";
				//add acts to the print queue
				if(@$consumo[0]){
					$kk_ .= $consumo[0];
					$ddd[] = $consumo_content[0][0];
					$ddd[] = $consumo_content[0][1];
				} else{
					$kk_ .= "<td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
				}
				//add total to the print queue
				$kk_ .= "<td></td>";
				$ddd[] = "";
				//add TM to the print queue
				$kk_ .= "<td></td>";
				$ddd[] = "";
				//add final total to the print queue
				$kk_ .= "<td></td>";
				$ddd[] = "";
			$kk_ .= "</tr>";
			$data[] = $ddd;
			//var_dump($data);echo "<br /><br />";
			//loop to display all patient information in separate rows
			$numbers = array($consul_found_count,$lab_rows,$mdo_rows, $hospo_rows,$consumo_rows);
			$max = max_value($numbers);
			//var_dump($hospo_rows);
			for($n=1;$n<max_value($numbers);$n++){
				$row_count++;
				$ddd = array();
				$kk_ .= "<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				";
				$ddd[] = "";
				$ddd[] = "";
				$ddd[] = "";
				$ddd[] = "";
				if(@$consul_found[$n]){
					$kk_ .= "<td>{$consul_found[$n]}</td>";
					$ddd[] = $consul_found[$n];
				} else{
					$kk_ .= "<td></td>";
					$ddd[] = "";
				}
				if(@$labo[$n]){
					$kk_ .= $labo[$n];
					$ddd[] = $labo_content[$n][0];
					$ddd[] = $labo_content[$n][1];
				}else{
					$kk_ .= "<td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
				}
				if(@$mdo[$n]){
					$kk_ .= $mdo[$n];
					$ddd[] = $mdo_content[$n][0];
					$ddd[] = $mdo_content[$n][1];
					$ddd[] = $mdo_content[$n][2];
					$ddd[] = $mdo_content[$n][3];
				} else{
					$kk_ .= "<td></td><td></td><td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
					$ddd[] = "";
					$ddd[] = "";
				}
				if(@$hospo[$n]){
					$kk_ .= $hospo[$n];
					$ddd[] = $hospo_content[$n][0];
					$ddd[] = $hospo_content[$n][1];
				}else{
					$kk_ .= "<td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
				}
				if(@$actso[$n]){
					$kk_ .= $actso[$n];
					$ddd[] = $actso_content[$n][0];
					$ddd[] = $actso_content[$n][1];
				} else{
					$kk_ .= "<td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
				}
				if(@$consumo[$n]){
					$kk_ .= $consumo[$n];
					$ddd[] = $consumo_content[$n][0];
					$ddd[] = $consumo_content[$n][1];
				} else {
					$kk_ .= "<td></td><td></td>";
					$ddd[] = "";
					$ddd[] = "";
				}
				//totals
				$kk_ .= "<td></td>";
				$ddd[] = "";
				//TM
				$kk_ .= "<td></td>";
				$ddd[] = "";
				//add final total to the print queue
				$kk_ .= "<td></td>";
				$ddd[] = "";
				
				$kk_ .= "</tr>";
				$data[] = $ddd;
			}
			
			$tot += $cons + $lab + $md + $hosp + $acts + $consumables;
			$tm = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT mu_tm.TicketPaid FROM mu_tm WHERE mu_tm.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=false,$con);
			$total = ceil($tot) - $tm['TicketPaid'];
			$ddd = array();
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = $cons;
			$ddd[] = "";
			$ddd[] = $lab;
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = $md;
			$ddd[] = "";
			$ddd[] = $hosp;
			$ddd[] = "";
			$ddd[] = $acts;
			$ddd[] = "";
			$ddd[] = $consumables;
			$ddd[] = $tot;
			$ddd[] = $tm['TicketPaid'];
			$ddd[] = $total;
			$kk_ .= "<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class=number_right>{$cons}</td>
					<td></td>
					<td class=number_right>{$lab}</td>
					<td></td>
					<td></td>
					<td></td>
					<td class=number_right>{$md}</td>
					<td></td>
					<td class=number_right>{$hosp}</td>
					<td></td>
					<td class=number_right>{$acts}</td>
					<td></td>
					<td class=number_right>{$consumables}</td>
					<td class=number_right>{$tot}</td>
					<td class=number_right>{$tm['TicketPaid']}</td>
					<td class=number_right><a href='./reprint.php?key={$patients[$i]['DocID']}&patientid={$patients[$i]['PatientRecordID']}&rcv_patient=".sha1('rcv_patient')."' title='Click to Reprint!' target='_blank' style='color:blue; text-decoration:none;'>{$total}</a>
					<a href='' onclick='if(confirm(\"Delete Completely the File For {$patients[$i]["Name"]} - {$r["InsuranceCardID"]} - {$r["DocID"]} From The System\")){ deleteProfileNow(\"{$r['PatientRecordID']}\"); } return false;' style='color:red; font-size:10px; font-weight:bold; text-decoration:none;' title='Delete The Patient File'>X</a></td>
			</tr>";
			$data[] = $ddd;
		}
		echo $kk_;
		//var_dump($data);
		?>
		</tbody>
	</table>
	</div>
	</b>
	<?php
	if(count($data)>0){
		$_SESSION['report'] = $data;
		$_SESSION['header'] = array(
									array("PROVINCE / MVK"=>"SUD"),
									array("ADMINISTRATIVE DISTRICT"=>"HUYE", "Period"=>$_GET['year']."/".$_GET['month']),
									array("ADMINISTRATIVE SECTION"=>"HUYE"),
									array("HEALTH FACILITY"=>"BUSORO HEALTH CENTER"),
									array("CODE / HEALTH FACILITY"=>"40211013"),
								);
		$_SESSION['report_title'] = array("title"=>"S U M M A R Y  OF CORAR  P R E S T A T I O N");
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
		<a href='./print_report.php?format=pdf' onclick='alert("Sorry Not Availabel"); return false;' target="_blank"><img title='View in PDF' src="../images/b_pdfdoc.png" class=img_links width=30px /></a>
		<a href='./print_report_pres.php?format=excel&in=cbhi' target="_blank"><img title="View in EXCEL" src="../images/excel.png" class=img_links width=40px /></a>
		<?php
	}
	
} else{
	echo "<span class=error-text>No Patient in the selected month {$_GET['year']}-{$_GET['month']} at selected station {$post}</span>";
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
<div id=upload_out></div>