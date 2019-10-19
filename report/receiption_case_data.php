<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//echo (60*60*24);
$start_time = mktime(0,0,0,$_GET['month'],1,$_GET['year']);
$step = (60*60*24);
if($_GET['month'] < 10)
	$_GET['month'] = "0".$_GET['month'];
//echo date("Y-m-d",($start_time + $step));
$report_title = "Received Money In Caisse";
$month_ = $_GET['month']."/".$_GET['year'];
$_date_ = $_GET['year']."-".$_GET['month'];
$post = "";
$posts = explode("_", $_GET['post']);
//var_dump($posts);
$count = count($post);
$current = 1;
$sys = "("; $sys_s = 0;
foreach($posts as $pst){
	$ps = returnSingleField($sql="SELECT CenterName FROM sy_center WHERE CenterID='{$pst}'",$field="CenterName",$data=true, $con);
	//var_dump($ps);
	if($ps != null){
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
$str = "
<style>
	table td, table th{
		border:0px solid #000;
	}
	.title{
		font-size:20px;
	}
	#new{ font-size:13px;}
	#new td{ width:50px; }
	td.number_right{ text-align:right; }
</style>
<span class=title>
	<span class=success style='font-weight:bold; font-size:20px;'>
{$report_title}<br />
Post: {$post}<br />
Month: {$month_}
</span>
</span>

<div style='max-height:350px; border:0px solid #000; overflow:auto;'>
<table id=new class=list border=1><tr><th colspan=2>&nbsp;</th>";
$str_total = "<tr><th rowspan=2>Total</th><td>Patients</td>";
$str_total2 = "<tr><td>TM</td>";
$valid_dates = array();
$patients = array();
$total_patients = array();
$tms = array();
$total_tms = array();
$start_date = date("Y-m-d",$start_time);
for($start_time; preg_match("/^{$_GET['year']}-{$_GET['month']}$/",date("Y-m",$start_time)); $start_time += $step){
	$str .= "<th>".date("d",$start_time)."</th>";
	$valid_dates[date("Y-m-d",$start_time)] = 0;
	$patients[date("Y-m-d",$start_time)] = 0;
	$total_patients[date("Y-m-d",$start_time)] = 0;
	$tms[date("Y-m-d",$start_time)] = 0;
	$total_tms[date("Y-m-d",$start_time)] = 0;
}
$end_date =  date("Y-m-d",$start_time);
$str .= "<th>Total</th></tr>";
//var_dump($tms);
//select all valid medicines now
$sql = "SELECT DISTINCT in_name.*, pa_records.*, mu_tm.* from in_name, pa_records, mu_tm, sy_users, sy_center, in_category WHERE in_category.InsuranceCategoryID = in_name.CategoryID && in_name.InsuranceNameID = pa_records.InsuranceNameID && pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && pa_records.DateIn >= '{$start_date}' && pa_records.DateIn < '{$end_date}' && {$sys} ORDER BY in_category.InsuranceCode ASC, in_name.InsuranceName DESC, pa_records.DateIn ASC";
//echo $sql; echo "<br /><br />";
$medecines = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
$category_ = ""; $count = 1;
//var_dump($medecines);
$str_data = array();
//echo $medecines[93]['InsuranceName'];
$checked_index = array(); $last_index = count($medecines) - 1;
$insurances = array(); $count = -1;
for($i=0;$i<count($medecines);$i++){
	
	if($category_ !== $medecines[$i]['InsuranceName']){
		$category_ = $medecines[$i]['InsuranceName'];
		$ins = array(
					"name"=>$medecines[$i]['InsuranceName'],
					"patients"=>$patients,
					"tms"=>$tms
					);
		
		if($medecines[$i]['InsuranceName'] == 'CBHI')
			$ins['not_paid']= $patients;
		$count++;
		$insurances[$count] = $ins;
	}
	if($medecines[$i]['InsuranceName'] == 'CBHI')
			if($medecines[$i]['TicketPaid'] == 0)
				$insurances[$count]['not_paid'][$medecines[$i]['DateIn']]++;
	//var_dump($insurances);
	$insurances[$count]['patients'][$medecines[$i]['DateIn']]++;
	$total_patients[$medecines[$i]['DateIn']]++;
	$insurances[$count]['tms'][$medecines[$i]['DateIn']] += $medecines[$i]['TicketPaid'];
	$total_tms[$medecines[$i]['DateIn']] += $medecines[$i]['TicketPaid'];
	
}
$insurances[] = array("name"=>"Total","patients"=>$total_patients,"tms"=>$total_tms);
//var_dump($insurances);
foreach($insurances as $insurance){
	//var_dump($insurance['name']);
	$str_3 = "<tr><td>TM not Paid</td>";
	$str_1 = "<tr><td rowspan='".($insurance['name']=="CBHI"?"3":"2")."'>{$insurance['name']}</td><td>Patients</td>";
	$str_2 = "<tr><td>TM</td>";
	$tot = 0;
	foreach($insurance['patients'] as $date=>$count_patients){
		$str_1 .= "<td class=number_right>".($count_patients?$count_patients:"")."</td>";
		$tot += $count_patients;
	}
	$str_1 .= "<td class=number_right>{$tot}</td>";
	if(@$insurance['not_paid']){
		$tot = 0;
		foreach($insurance['not_paid'] as $date=>$count_patients){
			$str_3 .= "<td class=number_right>".($count_patients?$count_patients:"")."</td>";
			$tot += $count_patients;
		}
		$str_3 .= "<td class=number_right>{$tot}</td>";
	}
	$tot = 0;
	foreach($insurance['tms'] as $date=>$count_tms){
		$str_2 .= "<td class=number_right>".($count_tms?$count_tms:"")."</td>";
		$tot += $count_tms;
	}
	$str_2 .= "<td class=number_right>{$tot}</td></tr>";
	$str_3 .= "</tr>";
	$str_1 .= "</tr>";
	if($insurance['name'] != "CBHI")
		$str_3 = "";
	
	$str_1 .= $str_3.$str_2;
	
	
	$str .= $str_1;
}
$str .= "</table>
</div>";
echo $str;
//var_dump($medecines);
//var_dump($_GET); 
die;
if(!@$_GET['key']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}
$select = "";
//var_dump($_GET);
if(strlen($_GET['key'])){
	//select all possible information on the comming id
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientRecordID, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* FROM pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && InsuranceNameID='{$_GET['key']}' && DateIn >= '{$_GET['start']}' && DateIn <= '{$_GET['end']}' && pa_records.Status != 0 LIMIT 0, 20 ",$con),$multirows=true,$con);

//echo $sql;
if($patients){
	?>
	<b class=visibl>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:0px solid #000;}
		#vsbl td{width:80px;}
	</style>
	<span class=styling></span>
	
	<div style='max-height:350px; border:0px solid #000; overflow:auto;'>
	<table class=list id=vsbl border="1" style=''>
		<tr><th>ID</th><th>Date</th><th>Cat.</th><th>Insurance</th><th>Age</th><th>Sex</th><th>Name</th><th>House Holder</th><th>Family Code</th><th>Cons Cost</th><th>Lab</th><th>Imaging</th><th>Hosp.</th><th>Proc & Mat.</th><th>Consum.</th><th>Drugs.</th><th>Total</th><th>Detterent Fees</th><th>Total to be Paid</th></tr>
		<?php
		$t = array();
		$data = array();
		$data[] = array("Nr","Date","Cat.","Insurance","Age","Sex","Name","House Holder","Family Code","Cons Cost","Lab","Imaging","Hosp","Procedures","Consumables","Drugs","Total","Detterent","Total to be paid");
		for($i=0;$i<count($patients);$i++){
			$r = $patients[$i];
			$ddd = array();
			echo "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
				<td>".($i+1)."</td>
				<td>{$r["DateIn"]}</td>
				<td>&nbsp;</td>
				<td>{$r["InsuranceCardID"]}</td>
				<td>".($patients[$i]["DateofBirth"] == "0000-00-00"?"":$patients[$i]["DateofBirth"])."</td>
				<td>{$patients[$i]["Sex"]}</td>
				<td>{$patients[$i]["Name"]}</td>
				<td>{$patients[$i]["FamilyCode"]}</td>
				<td>".(substr($r['InsuranceCardID'],8,5))."</td>";
				
				$ddd[] = ($i+1);
				$ddd[] = $r["DateIn"];
				$ddd[] = "";
				$ddd[] = $r["InsuranceCardID"];
				$ddd[] = $r["DateofBirth"];
				$ddd[] = $r["Sex"];
				$ddd[] = $r["Name"];
				$ddd[] = $r["FamilyCode"];
				$ddd[] = substr($r['InsuranceCardID'],8,5);
				
				//select the laboratory fees related to this request now
				$conslt = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT co_price.Amount, co_records.ConsultationRecordID FROM co_records, co_price WHERE co_records.ConsultationPriceID = co_price. ConsultationPriceID && co_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$cons = 0; $lab = 0; $md = 0; $tot = 0; $t_counter = 0;
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
				$ho = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ho_price.Amount, ho_record.Days FROM ho_record, ho_price WHERE ho_record.HoPriceID = ho_price. HOPriceID && ho_record.RecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
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
				$act = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT ac_price.Amount, ac_records.ActRecordID FROM ac_records, ac_price WHERE ac_records.ActPriceID = ac_price. ActPriceID && ac_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$acts = 0;
				if($act){
					foreach($act as $c){
						$acts += ($c['Amount']);
						
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
				$consum = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT md_price.Amount, cn_records.Quantity FROM cn_records, md_price WHERE cn_records.MedecinePriceID = md_price. MedecinePriceID && cn_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
				$consumables = 0;
				if($consum){
					foreach($consum as $c){
						$consumables += ($c['Amount'] * $c['Quantity']);
						
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
				echo "<td>{$tot}</td>";
				$ddd[] = $tot;
				if(!@$t[$t_counter])
					$t[$t_counter] = $tot;
				else
					$t[$t_counter] += $tot;
				$t_counter++;
				$tm = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT mu_tm.TicketPaid FROM mu_tm WHERE mu_tm.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=false,$con);
				echo "<td>{$tm['TicketPaid']}</td>";
				$ddd[] = $tm['TicketPaid'];
				if(!@$t[$t_counter])
					$t[$t_counter] = $tm['TicketPaid'];
				else
					$t[$t_counter] += $tm['TicketPaid'];
				$t_counter++;
				$tp_tot = $tot - $tm['TicketPaid'];
				echo "<td>{$tp_tot}</td>";
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
			echo "<th>{$tt}</th>";
			$ddd[] = $tt;
		}
		$data[] = $ddd;
			?>
		</tr>
	</table></b>
	<?php
	if(count($data)>0){
		$_SESSION['report'] = $data;
		$_SESSION['header'] = array(
									array("PROVINCE / MVK"=>"SUD"),
									array("ADMINISTRATIVE DISTRICT"=>"HUYE", "Period"=>$_GET['start']." To ".$_GET['end']),
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
		<a href='./print_report.php?format=excel' target="_blank"><img title="View in EXCEL" src="../images/excel.png" class=img_links width=40px /></a>
		<?php
	}
	//echo "<pre>";var_dump($data);
/* } else{
	
	echo "<span class=error-text>No Match Found</span>";
}
 */
} else{
	echo "<span class=error-text>No Patient in the selected Range From {$_GET['start']} To {$_GET['end']}</span>";
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