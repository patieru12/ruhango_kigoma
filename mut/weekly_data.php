<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("mut" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
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
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.DocID, pa_records.Status, pa_records.PatientRecordID, pa_records.FamilyCategory, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center WHERE pa_info.PatientID = pa_records.PatientID && pa_records.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys}  && InsuranceNameID='{$_GET['key']}' && DateIn >= '{$_GET['start_date']}' && DateIn <= '{$_GET['end_date']}' {$sp_condition} ORDER BY pa_records.DateIn ASC, pa_records.PatientRecordID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 

//echo $sql;
if($patients){
	?>
	<script>
		$("#filter").click(function(e){
			$(".patient_found").load("document_list_cbhi.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + prompt("Enter Filter Key",'<?= @$_GET['filter'] ?>').replace(/ /g,"%20"));
			return e.preventDefault();
		});
		$("#filter_remove").click(function(e){
			$("#filter_").val("");
			$(".patient_found").load("document_list_cbhi.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
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
					$(".patient_found").load("document_list_cbhi.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
				}
			});
		}
	</script>
	<b class=visibl>
	<span class=success style='font-weight:bold; font-size:20px;'>
	CBHI UTILIZATION OF MEDICAL SERVICES <br />
	PROVINCE:SOUTH<br />
	RSSB BRANCH / DISTRICT:<?= strtoupper($_DISTRICT); ?><br /><!--
	Post: <?= $post ?><br />-->
	Period: <?= $_GET['start_date']." - ".$_GET['end_date'] ?><br />
	Number: <?= count($patients) ?> Patient<?= count($patients)>1?"s":"" ?> 
	</span>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
		.number_right{ text-align:right; }
	</style>
	<span class=styling></span>
	<?= @$_GET['filter']?"<script>$('#filter_').val('{$_GET['filter']}');</script><br /><span class=error-text>".count($patients)." Result".(count($patients)>1?"s":"")." found for ".$_GET['filter']."</span>":"" ?> <span style='float:right;'><?= @$_GET['filter']?"<a href='#' id=filter_remove style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' ><img src='../images/filter_remove.png' /> Remove Filter</a>":"" ?><a href='#' id=filter style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' > <img src='../images/filter.png' /> Filter </a></span>
	<div style='max-height:350px; margin-top:2px; width:100%; border:0px solid #000; overflow:auto;'>
	
	<br />
	<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
		<tr><th rowspan=3>ID</th><th rowspan=3>Day</th><th colspan=6>NUMBER OF PATIENTS FREQUENTING HEALTH FACILITIES</th><th colspan=5>SPECIAL CASES TO BE REPORTED</th><th colspan=6>ESTIMATED COST OF MEDICAL BENEFITS TO CBHI MEMBERS</th></tr>
		<tr><th colspan=3>Out Patient</th><th colspan=3>In Patient</th><th rowspan=2>Indigent</th><th rowspan=2>Cost Ind.</th><th rowspan=2>Prisoner</th><th rowspan=2>Cost Pr.</th><th rowspan=2>Transfer</th><th rowspan=2>Proc. & Mat.</th><th rowspan=2>Cost of Drugs</th><th rowspan=2>Ambulance</th><th rowspan=2>Total Cost</th><th rowspan=2>TM</th><th rowspan=2>Payable</th></tr>
		<tr><th>Z</th><th>HZ</th><th>HD</th><th>Z</th><th>HZ</th><th>HD</th></tr>
		<tbody>
		<?php
		//die;
		$t = array();
		$kk_ = "";
		$data = array();
		
		$report_data_all = array();
		
		$report_data_all[] = array("ID","Day","NUMBER OF PATIENTS FREQUENTING HEALTH FACILITIES","SPECIAL CASES TO BE REPORTED","ESTIMATED COST OF MEDICAL BENEFITS TO CBHI MEMBERS");
		
		$report_data_all[] = array("Out Patient","In Patient","Indigent","Cost Ind.","Prisoner","Cost Pr.","Transfer","Proc. & Mat.","Cost of Drugs","Ambulance","Total Cost","TM","Payable");
		$report_data_all[] = array("Z","HZ","HD","Z","HZ","HD");
		
		//var_dump($data); die;
		$row_count = 0;
		$kk = "";
		$kk_2 = "";
		$oz = 0; $ohz = 0; $ohd = 0;
		$total_oz = 0; $total_ohz = 0; $total_ohd = 0;
		$iz = 0; $ihz = 0; $ihd = 0;
		$total_iz = 0; $total_ihz = 0; $total_ihd = 0;
		$ind = 0; $ind_cost = 0; 
		$total_ind = 0; $total_ind_cost = 0; 
		$pris = 0; $pris_cost = 0; 
		$total_pris = 0; $total_pris_cost = 0; 
		$transfer = 0;
		$total_transfer = 0;
		$cons_cost = 0;
		$total_cons_cost = 0;
		$med_cost = 0;
		$total_med_cost = 0;
		$amb = 0;
		$total_amb = 0;
		$total_all = 0;
		$total_total_all = 0;
		$total_tm = 0;
		$total_total_tm = 0;
		$total_payable = 0;
		$total_total_payable = 0;
		
		$last = count($patients); $printed_day = date("l",mktime(0,0,0,explode("-",$patients[0]['DateIn'])[1],explode("-",$patients[0]['DateIn'])[2],explode("-",$patients[0]['DateIn'])[0]));
		$p = "";
		for($i=0;$i<$last;$i++){
			$r = $patients[$i];
			$printed = date("l",mktime(0,0,0,explode("-",$r['DateIn'])[1],explode("-",$r['DateIn'])[2],explode("-",$r['DateIn'])[0]));
		
			$ddd = array();
			$report_data = array();
			$tot = 0;
			$tm_p = returnSingleField("SELECT TicketPaid FROM mu_tm WHERE PatientRecordID='{$patients[$i]['PatientRecordID']}'","TicketPaid",true,$con);
			//echo $tm_p."<br />";
			if($patients[$i]["FamilyCategory"] == 1)
				$ind++;
			
			$tm_i = returnSingleField("SELECT TicketID FROM mu_tm WHERE PatientRecordID='{$patients[$i]['PatientRecordID']}'","TicketID",true,$con);
			$row_count++;
			$zones = array("Z","HZ","HD");
			//check if the patient is in hospital
			$ho_patient = returnSingleField("SELECT HoRecordID FROM ho_record WHERE RecordID='{$patients[$i]['PatientRecordID']}'","HoRecordID",true,$con);
			//check if the user has paid his TM
			/* $indigent = returnSingleField("SELECT TicketPaid FROM pa_records, se_records, se_name, mu_tm WHERE mu_tm.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode='CPC' && pa_records.PatientRecordID='{$patients[$i]['PatientRecordID']}'","TicketPaid",true,$con);
			if($indigent == 0)
				$ind++; */
			if($ho_patient){
				if(in_array($r['CellID'], $zone_cells)){
					//$zone = "<img src='../images/box-checked.png' />";
					$iz++;
					$p .= $r['Name'].",";
				} else if(in_array($r['DistrictID'], $zone_districts)){
					//$horszone = "<img src='../images/box-checked.png' />";
					$ihz++;
				} else {
					//$horsdistrict = "<img src='../images/box-checked.png' />";
					$ihd++;
				}
				//(preg_match("#^0204/12/#",$r['InsuranceCardID']))?($iz++):((preg_match("#^0204/#",$r['InsuranceCardID']))?($ihz++):($ihd++));
			} else{
				if(in_array($r['CellID'], $zone_cells)){
					//$zone = "<img src='../images/box-checked.png' />";
					$oz++;
				} else if(in_array($r['DistrictID'], $zone_districts)){
					//$horszone = "<img src='../images/box-checked.png' />";
					$ohz++;
				} else {
					//$horsdistrict = "<img src='../images/box-checked.png' />";
					$ohd++;
				}
				//(preg_match("#^0204/12/#",$r['InsuranceCardID']))?($oz++):((preg_match("#^0204/#",$r['InsuranceCardID']))?($ohz++):($ohd++));
			}
				
			$patients[$i]["Status"]>=2?($transfer++):$transfer;
			$kk_ .=  "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
				<td>".($i+1)."</td>
				<td>".($printed)."</td>
				<td>{$patients[$i]["Name"]}</td>
				<td>".($r['InsuranceCardID'])."</td>
				<td>{$patients[$i]["FamilyCategory"]}</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>".($patients[$i]["FamilyCategory"] == 1?"yes":"no")."</td>
				<td></td>
				<td></td>
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
						//select all exam related to this consultation record now
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
								$labo[] = "<td>{$l['ExamName']}{$rslt_lst}: <!--</td><td class=number_right>-->".round($l['Amount'],0)."</td>";
								$labo_content[] = array($l['ExamName']." ".$rslt_lst, round($l['Amount'],0));
								$lab += $l['Amount'];
							}
						}
						//select all drugs related to this consultion record now
						$medecines = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT md_name.MedecineName, md_price.Amount, md_records.Quantity FROM md_name, md_records, md_price WHERE md_name.MedecineNameID = md_price.MedecineNameID && md_records.MedecinePriceID = md_price. MedecinePriceID && md_records.ConsultationRecordID='{$c['ConsultationRecordID']}'",$con),$multirows=true,$con);
						if($medecines){
							foreach($medecines as $l){
								$mdo_rows++;
								$mdo[] = "<td>{$l['MedecineName']}: <!--</td><td class='number_right edit{$row_count}1' >{$l['Quantity']}</td><td class=number_right>".round($l['Amount'],1)."</td><td>-->".($l['Amount'] * $l['Quantity'])."</td>";
								$mdo_content[] = array($l['MedecineName'],$l['Quantity'], round($l['Amount'],1), round(($l['Amount'] * $l['Quantity']),1));
								$md += ($l['Amount'] * $l['Quantity']);
							}
						}
					}
				}
				if(@$consul_found[0]){
					$kk_ .= "<td class='number_right edit{$row_count}1'>{$consul_found[0]}</td>";
					$ddd[] = $consul_found[0];
					//$cons_cost += $consul_found[0];
				} else{
					$kk_ .= "<td></td>";
					$ddd[] = "";
				}
				//add laboratory
				if(@$labo[0]){
					$kk_ .= $labo[0];
					$ddd[] = $labo_content[0][0];
					$ddd[] = $labo_content[0][1];
					//$cons_cost += $labo_content[0][1];
				} else{
					$kk_ .= "<td></td><!--<td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
				}
				
				//add medical imaging
				$kk_ .= "<td></td>";
				$dd[] = "";
				//add hospitalization
				$ho = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ho_price.Amount, ho_record.Days FROM ho_record, ho_price WHERE ho_record.HoPriceID = ho_price. HOPriceID && ho_record.RecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$hosp = 0; $hospo = array(); $hospo_content = array(); $hospo_rows = 0;
				if($ho){
					foreach($ho as $c){
						$hospo_rows++;
						$hospo[] = "<td>{$c['Days']}: <!--</td><td class=number_right>-->".round(($c['Amount'] * $c['Days']),0)."</td>";
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
					$kk_ .= "<td></td><!--<td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
				}
				
				//now select acts and print the price in the table
				$act = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ac_name.Name, ac_price.Amount, ac_records.ActRecordID FROM ac_name, ac_records, ac_price WHERE ac_name.ActNameID = ac_price.ActNameID && ac_records.ActPriceID = ac_price. ActPriceID && ac_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$acts = 0; $actso = array(); $actso_content = array(); $actso_rows = 0;
				if($act){
					foreach($act as $c){
						$actso_rows++;
						$actso[] = "<td>{$c['Name']}: <!--</td><td class=number_right>-->".round($c['Amount'],0)."</td>";
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
					$kk_ .= "<td></td><!--<td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
				}
				
				//now select consumables and print the price in the table
				$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT md_name.MedecineName, md_price.Amount, cn_records.Quantity FROM md_name, cn_records, md_price WHERE md_name.MedecineNameID = md_price.MedecineNameID && cn_records.MedecinePriceID = md_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$consumables = 0; $consumo = array(); $consumo_content = array(); $consumo_rows = 0;
				if($consum){
					foreach($consum as $c){
						$consumo_rows++;
						$consumo[] = "<td><!--{$c['Quantity']} -->{$c['MedecineName']}<!--</td><td class=number_right>-->".round(($c['Amount'] * $c['Quantity']),1)."</td>";
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
					$kk_ .= "<td></td><!--<td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
				}
				
				//add ambulance
				$kk_ .= "<td></td>";
				$dd[] = "";
				
				//add transfer
				$kk_ .= "<td></td>";
				$dd[] = "";
				//add other
				$kk_ .= "<td></td>";
				$dd[] = "";
				
				//add medecines
				if(@$mdo[0]){
					$kk_ .= $mdo[0];
					$ddd[] = $mdo_content[0][0];
					$ddd[] = $mdo_content[0][1];
					$ddd[] = $mdo_content[0][2];
					$ddd[] = $mdo_content[0][3];
				} else{
					$kk_ .= "<td></td><!--<td></td><td></td><td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
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
					<td></td>
					<td></td>
					<td></td>
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
					$kk_ .= "<td></td><!--<td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
				}
				
				//add medical imaging
				$kk_ .= "<td></td>";
				$dd[] = "";
				
				if(@$hospo[$n]){
					$kk_ .= $hospo[$n];
					$ddd[] = $hospo_content[$n][0];
					$ddd[] = $hospo_content[$n][1];
				}else{
					$kk_ .= "<td></td><!--<td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
				}
				if(@$actso[$n]){
					$kk_ .= $actso[$n];
					$ddd[] = $actso_content[$n][0];
					$ddd[] = $actso_content[$n][1];
				} else{
					$kk_ .= "<td></td><!--<td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
				}
				if(@$consumo[$n]){
					$kk_ .= $consumo[$n];
					$ddd[] = $consumo_content[$n][0];
					$ddd[] = $consumo_content[$n][1];
				} else {
					$kk_ .= "<td></td><!--<td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
				}
				//add ambulance
				$kk_ .= "<td></td>";
				$dd[] = "";
				//add transfer
				$kk_ .= "<td></td>";
				$dd[] = "";
				//add other
				$kk_ .= "<td></td>";
				$dd[] = "";
				//add medecines
				if(@$mdo[$n]){
					$kk_ .= $mdo[$n];
					$ddd[] = $mdo_content[$n][0];
					$ddd[] = $mdo_content[$n][1];
					$ddd[] = $mdo_content[$n][2];
					$ddd[] = $mdo_content[$n][3];
				} else{
					$kk_ .= "<td></td><!--<td></td><td></td><td></td>-->";
					$ddd[] = "";
					$ddd[] = "";
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
			$total = $tot - $tm['TicketPaid'];
			$ddd = array();
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = $cons;
			$cons_cost += $cons;
			
			
			if($patients[$i]['FamilyCategory'] == 1)
				$ind_cost += $cons;
			
			$ddd[] = "";
			$ddd[] = $lab;
			$cons_cost += $lab;
			
			
			if($patients[$i]['FamilyCategory'] == 1)
				$ind_cost += $lab;
			
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = "";
			$ddd[] = $md;
			$med_cost += $md;
			
			if($patients[$i]['FamilyCategory'] == 1)
				$ind_cost += $md;
			
			$ddd[] = "";
			$ddd[] = $hosp;
			$cons_cost += $hosp;
			
			
			if($patients[$i]['FamilyCategory'] == 1)
				$ind_cost += $hosp;
				
			$ddd[] = "";
			$ddd[] = $acts;
			$cons_cost += $acts;
			
			
			if($patients[$i]['FamilyCategory'] == 1)
				$ind_cost += $acts;
			
			$ddd[] = "";
			$ddd[] = $consumables;
			
			$cons_cost += $consumables;
			if($patients[$i]['FamilyCategory'] == 1)
				$ind_cost += $consumables;
			
			
			$ddd[] = $tot;
			$total_all += $tot;
			$ddd[] = $tm['TicketPaid'];
			//echo $total_tm."+".$tm_p." = " .
			($total_tm += $tm_p);//."<br />";			
			//if the patient is category 1 add his cost to the ind_cost
				
			$ddd[] = $total;
			$kk_ .= "<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class=number_right>{$cons}</td><!--
					<td></td>-->
					<td class=number_right>{$lab}</td>
					<td></td><!---->
					<td class=number_right>{$hosp}</td><!--
					<td></td>-->
					<td class=number_right>{$acts}</td><!--
					<td></td>-->
					<td class=number_right>{$consumables}</td>
					<td></td>
					<td></td>
					<td></td>
					<td class=number_right>{$md}</td>
					<td class=number_right>{$tot}</td>
					<td class=number_right>{$tm['TicketPaid']}</td>
					<td class=number_right>{$total}</td>
					
			</tr>";
			/* if($tot > 0){
				$kk .= $kk_;
				$kk_ = "";
			} */
			$data[] = $ddd;
			if($printed != $printed_day || ($i + 1) == $last){
				$total_all = $cons_cost + $med_cost;
				$total_payable = $total_all - $total_tm;
				$report_data = array();
				$report_data[] = "";
				$report_data[] = $printed_day;
				$report_data[] = $oz; $report_data[] = $ohz; $report_data[] = $ohd;
				$report_data[] = $iz; $report_data[] = $ihz; $report_data[] = $ihd;
				$report_data[] = $ind; $report_data[] = $ind_cost;
				$report_data[] = $pris; $report_data[] = $pris_cost;
				$report_data[] = $transfer;
				$report_data[] = $cons_cost;
				$report_data[] = $med_cost;
				$report_data[] = $amb;
				$report_data[] = $total_all;
				$report_data[] = $total_tm;
				$report_data[] = $total_payable;
				
				$report_data_all[] = $report_data;
				
				$kk_2 .= "<tr><td></td><td>{$printed_day}</td>
							<td>{$oz}</td><td>{$ohz}</td><td>{$ohd}</td>
							<td>{$iz}</td><td>{$ihz}</td><td>{$ihd}</td>
							<td>{$ind}</td><td>{$ind_cost}</td>
							<td>{$pris}</td><td>{$pris_cost}</td>
							<td>{$transfer}</td>
							<td>{$cons_cost}</td>
							<td>{$med_cost}</td>
							<td>{$amb}</td>
							<td>{$total_all}</td>
							<td>{$total_tm}</td>
							<td>{$total_payable}</td>
							</tr>";
				$total_oz += $oz; $total_ohz += $ohz; $total_ohd += $ohd;
				$oz = 0; $ohz = 0; $ohd = 0;
				$total_iz += $iz; $total_ihz += $ihz; $total_ihd += $ihd;
				$iz = 0; $ihz = 0; $ihd = 0;
				$total_ind += $ind; $total_ind_cost += $ind_cost; 
				$ind = 0; $ind_cost = 0; 
				$total_pris += $pris; $total_pris_cost += $pris_cost; 
				$pris = 0; $pris_cost = 0; 
				$total_transfer += $transfer;
				$transfer = 0;
				$total_cons_cost += $cons_cost;
				$cons_cost = 0;
				$total_med_cost += $med_cost;
				$med_cost = 0;
				$total_amb += $amb;
				$amb = 0;
				$total_total_all += $total_all;
				$total_all = 0;
				$total_total_tm += $total_tm;
				$total_tm = 0;
				$total_total_payable += $total_payable;
				$total_payable = 0;
			}
			
			$printed_day = $printed;
		}
		
		$total_all = $cons_cost + $med_cost;
		$total_payable = $total_all - $total_tm;
		
		$report_data = array();
		$report_data[] = "";
		$report_data[] = "Total";
		$report_data[] = $total_oz; $report_data[] = $total_ohz; $report_data[] = $total_ohd;
		$report_data[] = $total_iz; $report_data[] = $total_ihz; $report_data[] = $total_ihd;
		$report_data[] = $total_ind; $report_data[] = $total_ind_cost;
		$report_data[] = $total_pris; $report_data[] = $total_pris_cost;
		$report_data[] = $total_transfer;
		$report_data[] = $total_cons_cost;
		$report_data[] = $total_med_cost;
		$report_data[] = $total_amb;
		$report_data[] = $total_total_all;
		$report_data[] = $total_total_tm;
		$report_data[] = $total_total_payable;
		
		$report_data_all[] = $report_data;
		$kk_2 .= "<tr><td colspan=2>Total</td>
						<td>{$total_oz}</td><td>{$total_ohz}</td><td>{$total_ohd}</td>
						<td>{$total_iz}</td><td>{$total_ihz}</td><td>{$total_ihd}</td>
						<td>{$total_ind}</td><td>{$total_ind_cost}</td>
						<td>{$total_pris}</td><td>{$total_pris_cost}</td>
						<td>{$total_transfer}</td>
						<td>{$total_cons_cost}</td>
						<td>{$total_med_cost}</td>
						<td>{$total_amb}</td>
						<td>{$total_total_all}</td>
						<td>{$total_total_tm}</td>
						<td>{$total_total_payable}</td>
						</tr>";
		echo $kk_2;
		//var_dump($data);
		?>
		</tbody>
	</table>
	</div>
	</b>
	<?php
	if(count($data)>0){
		$_SESSION['report'] = $report_data_all;
		$_SESSION['header'] = array(
									array(" "=>"CBHI UTILIZATION OF MEDICAL SERVICES"),
									array("PROVINCE"=>"SUD"),
									array("RSSB BRANCH / DISTRICT"=>"HUYE"),
									array("Period:"=>$_GET['start_date']." - ".$_GET['end_date'])
									
								);
		$_SESSION['report_title'] = array("title"=>"");
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
		<a href='./print_report_weekly.php?format=excel&in=cbhi' target="_blank"><img title="View in EXCEL" src="../images/excel.png" class=img_links width=40px /></a>
		<?php
	}
	
} else{
	echo "<span class=error-text>No Patient in the selected range {$_GET['start_date']} - {$_GET['end_date']} at selected station {$post}</span>";
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