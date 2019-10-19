<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
if(@$_POST['save'] == 'Update'){
	//var_dump($_POST); die;
	if($_POST['fn'])
		$_POST['fn']($_POST,$con);
}

if(@$_GET['delete'] == 'update'){
	//var_dump($_GET); die;
	if($_GET['fn'])
		$_GET['fn']($_GET,$con);
}
$q_ty = 0; $tot = ""; $reference_date = "2016-11-21"; $reference_date_cat_1_no_tm = "2017-10-14";

$_GET['record'] = PDB($_GET['record'],true,$con);

$record = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.* from pa_records WHERE PatientRecordID='{$_GET['record']}'",$con),$multirows=false,$con);

$patient = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE PatientID='{$record['PatientID']}'",$con),$multirows=false,$con);

$service = formatResultSet($rslt=returnResultSet($sql="SELECT se_records.*, se_name.ServiceCode from se_records, se_name WHERE se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID='{$record['PatientRecordID']}'",$con),$multirows=false,$con);

$insurance_info = returnAllData("SELECT in_category.*, in_price.*, in_name.* FROM in_name, in_price,in_category WHERE in_name.CategoryID = in_category.InsuranceCategoryID && in_price.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceNameID='{$record['InsuranceNameID']}'",$con);
 /* Variable used to determine weather the patient pay TM or Not */
$remove_tm = ($record['DateIn'] >= $reference_date_cat_1_no_tm)?false:true;
$check_tm_data = $insurance_info[0]['InsuranceName'] == 'CBHI' && $record['FamilyCategory'] <= 2?true:false;
$check_tm_data_cat_1 = false;
if($record['DateIn'] >= $reference_date_cat_1_no_tm){
	$check_tm_data_cat_1 = $insurance_info[0]['InsuranceName'] == 'CBHI' && $record['FamilyCategory'] < 2?true:false;
}
$consultation = returnAllData("SELECT co_records.*, co_price.Amount, co_category.ConsultationCategoryName FROM co_records, co_price,co_category WHERE co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && co_records.PatientRecordID='{$_GET['record']}' && co_price.Amount >= 0 && co_price.InsuranceCategoryID='{$insurance_info[0]['InsuranceCategoryID']}'",$con);
//search for exams 
//var_dump($insurance_info);
$exams = array();
//var_dump($consultation);
$diag = "";
foreach($consultation as $cons_){
	//var_dump($cons_); echo "<hr />";
	$exam = returnAllData($sql = "SELECT la_records.*, la_exam.ExamName, la_price.Amount FROM la_records, la_exam, la_price WHERE la_records.ExamPriceID = la_price.ExamPriceID && la_price.Amount > 0 && la_price.ExamID = la_exam.ExamID && la_price.InsuranceTypeID = '{$insurance_info[0]['InsuranceCategoryID']}' && la_records.ConsultationRecordID='{$cons_['ConsultationRecordID']}'",$con);
	JoinArrays($exams, $exam,$exams);
	//select diagnostic to be displayed
	$dg = returnAllData($s = "SELECT co_diagnostic.DiagnosticName FROM co_diagnostic, co_diagnostic_records WHERE co_diagnostic.DiagnosticID = co_diagnostic_records.DiagnosticID && co_diagnostic_records.ConsulationRecordID = '{$cons_['ConsultationRecordID']}'",$con);
	$sd = array();
	fromDto1D($dg, $sd);
	foreach($sd as $dia)
		$diag .= $dia."<br /> ";
	//echo $s;
}
//echo $diag;
//die;
$medecines = array();
foreach($consultation as $cons_){
	$medecine = returnAllData("SELECT md_records.*, md_name.MedecineName, md_name.Emballage, md_price.Amount FROM md_records, md_name, md_price WHERE md_price.Amount > 0 && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.ConsultationRecordID='{$cons_['ConsultationRecordID']}'",$con);
	/* check if the patient has a anti-malaria medicine */
	if($check_tm_data_cat_1){

	} else {
		$check_anti_malaria = returnAllData("SELECT md_records.*, md_name.MedecineName, md_name.Emballage, md_price.Amount FROM md_records, md_name, md_price WHERE md_price.Amount > 0 && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.ConsultationRecordID='{$cons_['ConsultationRecordID']}' && (md_name.MedecineName LIKE('coartem%') || md_name.MedecineName LIKE('quinine%')  || md_name.MedecineName LIKE('artesunate%') )",$con);
		if(count($check_anti_malaria) <= 0){
			$check_tm_data = false;
		}
	}
	JoinArrays($medecines,$medecine,$medecines);
}

$consumables = returnAllData($cn_sql = "SELECT cn_records.*, cn_name.MedecineName, cn_price.Amount FROM cn_records, cn_name, cn_price WHERE cn_price.Amount > 0 && cn_records.MedecinePriceID = cn_price.MedecinePriceID && cn_price.MedecineNameID = cn_name.MedecineNameID && cn_records.PatientRecordID='{$_GET['record']}' && cn_records.Facture <= 1",$con);
//echo $cn_sql;
$acts = returnAllData("SELECT ac_records.*, ac_name.Name, ac_price.Amount FROM ac_records, ac_name, ac_price WHERE ac_price.Amount > 0 && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_records.PatientRecordID='{$_GET['record']}' && ac_price.InsuranceCategoryID='{$insurance_info[0]['InsuranceCategoryID']}'",$con);
$host = returnAllData($k = "SELECT ho_record.*, ho_type.Name, ho_price.Amount FROM ho_record, ho_type, ho_price WHERE ho_price.Amount > 0 && ho_record.HOPriceID = ho_price.HOPriceID && ho_price.HOTypeID = ho_type.TypeID && ho_record.RecordID='{$_GET['record']}' && ho_price.InsuranceCategoryID='{$insurance_info[0]['InsuranceCategoryID']}'",$con);
//echo $k;
//echo $sql;
//var_dump($consumables);
$date = date("Y-m-d",time());
$cons = "";
$all_tot = 0;
$printed_dates = array();
//var_dump($insurance_info);
if($consultation){
	foreach($consultation as $c){
		$cons_tot = 0;
		if(@$_GET['print'] == "print2" && $c['ConsultationCategoryName'] == "invisible"){
			continue;
		}
		$cons .= "<tr>";
		$cons .= "<td>".(!in_array($c['Date'],$printed_dates)?$c['Date']:"")."</td>";
		if(!in_array($c['Date'],$printed_dates))
			$printed_dates[] = $c['Date'];
		$cons .= "<td>".(!@$_GET['print']?"<a href='./print_bill.php?sy=care%20mini%20v1&record={$_GET['record']}&tkn=".Enc(date("d",time()))."&edit=co&rcnslt={$c['ConsultationRecordID']}&i={$insurance_info[0]['InsuranceCategoryID']}' title='Click to Change'>":"")."".($c['ConsultationCategoryName'] != "invisible"?$c['ConsultationCategoryName']:"no consultation")."</a> ".(@$_GET['print']?"":"<!--<a href='./print_bill.php?record={$_GET['record']}&delete=co&data={$c['ConsultationRecordID']}' onclick='return confirm(\"Remove {$c['ConsultationCategoryName']} From The Bill?\")' style='color:red;'>X</a>-->")."</td>";
		$cons .= "<td class=number></td>";
		$cons .= "<td class=number>{$c['Amount']}</td>";
		$cons_tot += $c['Amount'];
		$all_tot += $c['Amount'];
		$cons .= "<td class=numbers>{$cons_tot}</td>";
		$cons .= "</tr>";
	}
}
$ex = ""; #var_dump($exams); die;
if($exams){
	$ttt = 0;
	foreach($exams as $c){
		//var_dump($c);
		$ex_tot = 0;
		$ex .= "<tr>";
		$ex .= "<td>".(!in_array($c['ResultDate'],$printed_dates)?$c['ResultDate']:"")."</td>";
		if(!in_array($c['ResultDate'],$printed_dates))
			$printed_dates[] = $c['ResultDate'];
		$ex .= "<td>{$c['ExamName']}".(!@$_GET['print']?"<a href='./print_bill.php?record={$_GET['record']}&delete=update&record_id={$c['ExamRecordID']}&fn=ex_delete' onclick='return confirm(\"DELETE {$c['ExamName']} With {$c['ExamNumber']} From Exams List\")' style='font-size:12px; color:red;' title='Delete {$c['ExamName']}: {$c['ExamNumber']} From Exam List'>:{$c['ExamNumber']}</a>":"")."</td>";
		$ex .= "<td class=number></td>";
		$ex .= "<td class=number>{$c['Amount']}</td>";
		$ex_tot += $c['Amount'];
		$all_tot += $c['Amount'];
		$ex .= "<td class=numbers>{$ex_tot}</td>";
		$ex .= "</tr>";
		
		/* Check if the exam is only GE for patient in category 1 and 2 */
		if($check_tm_data && !$check_tm_data_cat_1){
			if($c['ExamName'] != 'GE'){
				$remove_tm = false;
				$check_tm_data = false;
			}
		}
	}
}
$md = "";
$emb = 0;
if($medecines){
	$q_ty = 0; $count__ = 0;
	$remove_amount = $check_tm_data?1:0;
	if($check_tm_data && count($medecines) == 2 && !$check_tm_data_cat_1){
		foreach($medecines as $c){
			if( !preg_match('/coartem/',strtolower($c['MedecineName'])) ){ 
				if(!preg_match('/paracet/',strtolower($c['MedecineName'])) ){
					$remove_amount = 0;
				}
			}
		}
	}
	foreach($medecines as $c){
		$md_tot = 0;
		
		if(!@$_GET['expand'] && $medecines[($count__)]['MedecineName'] == @$medecines[(++$count__)]['MedecineName']){
			$q_ty += $medecines[($count__ - 1)]['Quantity'];
			continue;
		}
		
		$md .= "<tr>";
		$md .= "<td>".(!in_array($c['Date'],$printed_dates)?$c['Date']:"")."</td>";
		if(!in_array($c['Date'],$printed_dates))
			$printed_dates[] = $c['Date'];
		$md .= "<td>".(!@$_GET['print']?"<a href='./print_bill.php?record={$_GET['record']}".(!@$_GET['expand'] && $q_ty>0?"&expand=1":"&tkn=".Enc(date("d",time()))."&sy=care%20mini%20v1&edit=md&rcnslt={$c['MedecineRecordID']}&i={$insurance_info[0]['InsuranceCategoryID']}&record_md={$c['ConsultationRecordID']}")."'>":"")."{$c['MedecineName']}</a>".(!@$_GET['print']?" <a href='./print_bill.php?record={$_GET['record']}&delete=update&record_id={$c['MedecineRecordID']}&fn=md_delete' onclick='return confirm(\"Delete {$c['MedecineName']} From the List Now\")' style='font-size:10px; color:red;' title='Delete {$c['MedecineName']} From the List'>X</a>":"")."</td>";
		$md .= "<td class=number>".round(($c['Quantity'] + $q_ty),2)."</td>";
		if($check_tm_data && !$check_tm_data_cat_1){
			if(preg_match('/coartem/',strtolower($c['MedecineName']))){
				$c['Amount'] = $remove_amount?0:$c['Amount'];
			} else {
				$remove_tm = false;
			}
		}
		$md .= "<td class=number>{$c['Amount']}</td>";
		$md_tot += round(($c['Amount'] * ($c['Quantity'] + $q_ty)),2);
		$all_tot += ($c['Amount'] * ($c['Quantity'] + $q_ty));
		$md .= "<td class=numbers>{$md_tot}</td>";
		$md .= "</tr>";
		if($c['Emballage'])
			$emb++;
	}
}
$act = "";
$printed_dates = array();
if($acts){
	$q_ty = 1; $count__ = 0;
	foreach($acts as $c){
		$act_tot = 0;
		/* if(!@$_GET['expand'] && $acts[($count__)]['Name'] == @$acts[(++$count__)]['Name']){
			$q_ty += $c['Quantity'];
			continue;
		} */
		//echo $q_ty;
		/* if($q_ty > 0)
			$q_ty += 1; */
		$act_tot += ($c['Amount']*$c['Quantity']);
		$all_tot += ($c['Amount']*$c['Quantity']);
		$act .= "<tr>";
		$act .= "<td>".(!in_array($c['Date'],$printed_dates)?$c['Date']:"")."</td>";
		if(!in_array($c['Date'],$printed_dates))
			$printed_dates[] = $c['Date'];
		$act .= "<td>".(!@$_GET['print']?"<a href='./print_bill.php?record={$_GET['record']}".(!@$_GET['expand'] && $q_ty>1?"&expand=1":"")."'>":"")."{$c['Name']}".(!@$_GET['print']?" <a href='./print_bill.php?record={$_GET['record']}&delete=update&record_id={$c['ActRecordID']}&fn=ac_delete' onclick='return confirm(\"Delete {$c['Name']} From the List Now\")' style='font-size:10px; color:red;' title='Delete {$c['Name']} From the List'>X</a>":"")."</td>";
		$act .= "<td class=number>{$c['Quantity']}</td>";
		$act .= "<td class=number>{$c['Amount']}</td>";
		$act .= "<td class=numbers>{$act_tot}</td>";
		$act .= "</tr>";
		$q_ty = 1;
		if($check_tm_data && !$check_tm_data_cat_1){
			$remove_tm = false;
		}
	}
}
$cn = "";
//echo $emb;
//echo "<pre>"; var_dump($consumables);
if($consumables){
	$emb_found = false;
	$q_ty = 0; $count__ = 0;
	foreach($consumables as $c){
		$cn_tot = 0;
		if(strtoupper($c['MedecineName']) == "SACHETS"){
			$emb_found = true;
		}
		if($consumables[($count__)]['MedecineName'] == @$consumables[(++$count__)]['MedecineName']){
			$q_ty += $consumables[($count__ - 1)]['Quantity'];
			continue;
		}
		
		$cn_tot += ($c['Amount'] * ($c['Quantity'] + $q_ty));
		$all_tot += ($c['Amount'] * ($c['Quantity'] + $q_ty));
		$q_ty += $c['Quantity'];
		
		$cn .= "<tr>";
		$cn .= "<td>".(!in_array($c['Date'],$printed_dates)?$c['Date']:"")."</td>";
		if(!in_array($c['Date'],$printed_dates))
			$printed_dates[] = $c['Date'];
		$cn .= "<td>".(!@$_GET['print']?"<a href='./print_bill.php?record={$_GET['record']}".(!@$_GET['expand'] && $q_ty>0?"&expand=1":"&tkn=".Enc(date("d",time()))."&sy=care%20mini%20v1&edit=cn&rcnslt={$c['ConsumableRecordID']}&i={$insurance_info[0]['InsuranceCategoryID']}")."'>":"")."{$c['MedecineName']}</a>".(!@$_GET['print']?" <a href='./print_bill.php?record={$_GET['record']}&delete=update&record_id={$c['ConsumableRecordID']}&fn=cn_delete' onclick='return confirm(\"Delete {$c['MedecineName']} From the List Now\")' style='font-size:10px; color:red;' title='Delete {$c['MedecineName']} From the List'>X</a>":"")."</td>";
		$cn .= "<td class=number>{$q_ty}</td>";
		$cn .= "<td class=number>{$c['Amount']}</td>";
		$cn .= "<td class=numbers>{$cn_tot}</td>";
		$cn .= "</tr>";
		$q_ty = 0;
		if($check_tm_data && !$check_tm_data_cat_1){
			if(strtolower($c['MedecineName']) != 'sachets'){
				$remove_tm = false;
			}
		}
	}
	if(!$emb_found && $emb){
		$cn .= "<tr>";
		$cn .= "<td>{$c['Date']}</td>";
		$cn .= "<td>Sachets</td>";
		$cn .= "<td class=number>{$emb}</td>";
		$cn .= "<td class=number>".($amount = returnSingleField("SELECT Amount FROM md_price WHERE MedecineNameID='".returnSingleField("SELECT MedecineNameID FROM md_name WHERE MedecineName='SACHET POUR MEDICAMENTS'","MedecineNameID",$data=true, $con)."' && Status=1","Amount",$data=true, $con))."</td>";
		$em_tot = ($amount * $emb);
		$all_tot += ($amount * $emb);
		$cn .= "<td class=numbers>{$em_tot}</td>";
		$cn .= "</tr>";
	}
}

//check if the patient has transferred
$sy_products = formatResultSet(returnResultSet($ss = "SELECT DISTINCT ProductName FROM sy_product, sy_tarif WHERE sy_tarif.ProductID = sy_product.ProductID && sy_tarif.InsuranceNameID='{$insurance_info[0]['InsuranceNameID']}' && sy_tarif.Date <= '{$record['DateIn']}'",$con),true,$con);
$str_products;
/* echo $ss;
var_dump($sy_products); die; */
$added_patient = 0;
if($sy_products){
	foreach($sy_products as $p){
		//browse for active tarif
		$added_amount = returnSingleField($sql = "SELECT Amount FROM sy_tarif, sy_product WHERE sy_tarif.ProductID = sy_product.ProductID && sy_tarif.Date <= '{$record['DateIn']}' && sy_product.ProductName='{$p['ProductName']}' && sy_tarif.InsuranceNameID = '{$insurance_info[0]['InsuranceNameID']}' ORDER BY Date DESC LIMIT 0, 1","Amount",true,$con);
		//echo $added_amount;
		$added_patient += $added_amount;
	}
}
//die;
arraytostring($sy_products, $str_products, $position = 0, $sep = ";");
if($record['Status'] > 1){
	$str_products .= ($str_products?"+":"")."Transfer";
	$added_patient += 50;
	
}
$added_patient = 0;
if($added_patient > 0){
	$str_products = preg_replace('/;$/','',$str_products);
	$str_products = preg_replace('/;/','+',$str_products);
	//$cn .= "<tr><td></td><td>{$str_products}</td><td></td><td class=number>{$added_patient}</td><td class=numbers>{$added_patient}</td></tr>";
}
/* var_dump($sy_products);
die; */
$ho = "";

if($host){
	foreach($host as $c){
		//var_dump($host);
		$ho_tot = 0;
		$ho .= "<tr>";
		$ho .= "<td> </td>";
		/* if(!in_array($c['Date'],$printed_dates))
			$printed_dates[] = $c['Date']; */
		$ho .= "<td>".(!@$_GET['print']?"<a href=''>":"")."{$c['Name']}</td>";
		$ho .= "<td class=number>{$c['Days']} day".($c['Days']>1?"s":"")."</td>";
		$ho .= "<td class=number>{$c['Amount']}/day</td>";
		$ho_tot += ($c['Amount']*$c['Days']);
		$all_tot += ($c['Amount']*$c['Days']);
		$ho .= "<td class=numbers>{$ho_tot}</td>";
		$ho .= "</tr>";
	}
}
$fiche = "";
if($record['DateIn'] < "2016-08-01"){
	if($insurance_info[0]['InsuranceName'] == "RSSB RAMA"){
		$fiche .= "<tr>";
		$fiche .= "<td></td>";
		
		$fiche .= "<td>Fiche</td>";
		$fiche .= "<td class=number></td>";
		$fiche .= "<td class=number></td>";
		
		$all_tot += FICHE;
		$fiche .= "<td class=numbers>".FICHE."</td>";
		$fiche .= "</tr>";
	}
}
$fiche_private = "";

if($insurance_info[0]['InsuranceName'] == "Private"){
	$fiche .= "<tr>";
	$fiche .= "<td></td>";
	
	$fiche .= "<td>Fiche + Facture + PH</td>";
	$fiche .= "<td class=number></td>";
	$fiche .= "<td class=number></td>";
	
	$all_tot += FICHE + 40;
	$fiche .= "<td class=numbers>".(FICHE + 40)."</td>";
	$fiche .= "</tr>";
}

//print_r($insurance_info);
$all_tot = round($all_tot,0);
$pat = 0;
if($all_tot){
	$tot = "<tr><td colspan=5>&nbsp;</td></tr><tr>";
	$tot .= "<td colspan=2 rowspan=3 align=center></td>";
	$tot .= "<td colspan=2 align=left>Total</td>";
	$tot .= "<td class=numbers>".($all_tot + $added_patient)."</td>";
	$tot .= "</tr><tr>";
	$tot .= "<td colspan=2 align=left>".(strtolower($insurance_info[0]['InsuranceName']) == "private"?"Paid ":"Patient ".($insurance_info[0]['InsuranceName'] != "CBHI"?$insurance_info[0]['ValuePaid']."% ":""))."</td>";
	$pat_un = ($insurance_info[0]['InsuranceName'] == "CBHI" || $insurance_info[0]['InsuranceName'] == "Private")?returnSingleField("SELECT * FROM mu_tm WHERE PatientRecordID='{$_GET['record']}'","TicketPaid",$data=true, $con):(round(($all_tot * $insurance_info[0]['ValuePaid'])/100,0));
	//var_dump($check_tm_data);
	if($check_tm_data_cat_1){
		//echo "HERE";
		saveData($sql = "UPDATE mu_tm SET TicketPaid=0 WHERE PatientRecordID='{$_GET['record']}'", $con);
	} else if($check_tm_data && $remove_tm){
		saveData($sql = "UPDATE mu_tm SET TicketPaid=0 WHERE PatientRecordID='{$_GET['record']}' && Type != 'INDIGENT'", $con);
		//echo $sql;
	} else if($check_tm_data && !$remove_tm && $pat_un == 0 && $service['ServiceCode'] != 'PST'){
		saveData($sql = "UPDATE mu_tm SET TicketPaid=200 WHERE PatientRecordID='{$_GET['record']}' && Type != 'INDIGENT'", $con);
		//echo $sql;
	} else if ($check_tm_data == false && $insurance_info[0]['InsuranceName'] == "CBHI" && $pat_un == 0  && $service['ServiceCode'] != 'PST'){
		saveData($sql = "UPDATE mu_tm SET TicketPaid=200 WHERE PatientRecordID='{$_GET['record']}' && Type != 'INDIGENT'", $con);
		//echo $sql;
	}
	//die();
	//echo $pat_un; die;
	$pat_un = ($insurance_info[0]['InsuranceName'] == "CBHI" || $insurance_info[0]['InsuranceName'] == "Private")?returnSingleField("SELECT * FROM mu_tm WHERE PatientRecordID='{$_GET['record']}'","TicketPaid",$data=true, $con):(round(($all_tot * $insurance_info[0]['ValuePaid'])/100,0));
	//echo $pat_un; die;
	//$pat = $pat_un;
	//var_dump($remove_tm);
	$pat = RoundUp($pat_un,5);
	$tot .= "<td class=numbers>".(!@$_GET['print'] && $insurance_info[0]['InsuranceName'] == "CBHI"?
																									:"").($pat + $added_patient)."</a></td>";
	$tot .= "</tr><tr>";
	$ins = round($all_tot - $pat,0);
	$tot .= "<td colspan=2 align=left style='width:260px; border:0px solid #000;'>".(strtolower($insurance_info[0]['InsuranceName']) == "private"?"To be paid ":"Insurance ".($insurance_info[0]['InsuranceName'] != "CBHI"?( 100 - $insurance_info[0]['ValuePaid'])."%":""))."</td>";
	$tot .= "<td class=numbers>{$ins}</td>";
	$tot .= "</tr>";
	if($insurance_info[0]['InsuranceName'] == "Private"){
		$pat = round($all_tot,5);
	}
}

$user = "<tr>
	<td colspan=2 style='padding-top:30px;'>Received by {$_SESSION['user']['Name']}<br />Stamp & Signature</td>";
if( ($check_tm_data && $remove_tm) || $check_tm_data_cat_1 ){
	$user .= "<td colspan=3 style='padding-top:30px; border:1px solid red; color:red;'>TM Removed because patient is in {$record['FamilyCategory']} with valid CBHI Insurance card</td>";
}

$user .= "</tr>";
//var_dump($patient);
$cat_display = "";
if($check_tm_data || $check_tm_data_cat_1){
	$cat_display = " | Category: {$record['FamilyCategory']}";
}
$str = <<<STR
<style>
	a{color:blue; text-decoration:none;}
	a:hover{color:red; text-decoration:underline;}
	table{widtd:100%; border-collapse:collapse; }
	table td, table td{padding: 2 8px; font-size:16px;}
	.date{float:right;}
	.doc_id{text-align:center}
	.numbers{ text-align:right}
	.sp{height:40px; border:0px solid #000;}
	.spp{border: 2px dashed #000;}
	table.intern tr:hover{background-color:e0e0e0; cursor:pointer; margin-top:3px;}
	.bill_c{height:480px; border:0px solid #000;}
	
.number{text-align:center;}
</style>
<table style='' border=0>
	<tr>
		<td style='border:0px solid #000;'>
			Name: {$patient['Name']}<br />
			{$insurance_info[0]['InsuranceName']} Code: {$record['InsuranceCardID']}{$cat_display}
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sex: {$patient['Sex']}
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date of Birth: {$patient['DateofBirth']}<br />
		</td>
	</tr>
	<tr>
		<td style='padding-top:20px;'>
			Diagnostic: <br />{$diag}
			<table border=0 class=intern style='width:440px; margin-top:10px;'>
				<tr>
					<td>Date</td><td  style='width:250px;'>Libell&eacute;</td><td>Quantity</td><td>Unit Price(RWF)</td><td>Total(RWF)</td>
				<tr>
				{$cons}
				{$ex}
				{$md}
				{$act}
				{$cn}
				{$ho}
				{$fiche}
				{$tot}
				{$user}
			</table>
		</td>
	</tr>
</table>
<br /><br />
STR;
if(!@$_GET['print']){
	$str .= "<a href='./print_bill.php?record={$_GET['record']}&print=print2'>Print</a>";
} else{
	//save the TM for every patient
	saveData($sql="UPDATE mu_tm SET TicketPaid='{$pat}' WHERE PatientRecordID='{$_GET['record']}'",$con);
	//echo $sql."<br />";
	saveData($sql = "UPDATE pa_records SET DocStatus='old' WHERE PatientRecordID='{$_GET['record']}'",$con);
	//echo $sql;
	//return;
}
$sstr = <<<TABLE
<table border=0 style=" border:0px solid #000; font-size:14px; font-family:sans-serif; width:900px;">
<tr><td class=bill_c style="width:50%; border:0px solid #000; vertical-align:top;">
	<table>
		<tr>
			<td colspan=2>
				Province: {$_PROVINCE}<br />
				District: {$_DISTRICT}<br />
				Sector: {$_SECTOR}<br />
				{$organisation}<br />
			</td>
		</tr>
			<tr><td style='text-align:center; border:0px solid #000;width:800px;'>
				Bill N<sup>r</sup>: {$record['DocID']}</td></tr>
		<tr><td style='text-align:center; border:0px solid #000;'>{$str}</td></tr>
		
	</table>
	</td><td class=bill_c style="width:450px; border-left:1px solid #000; vertical-align:top;">
	<table>
		<tr>
			<td colspan=2>
				Province: {$_PROVINCE}<br />
				District: {$_DISTRICT}<br />
				Sector: {$_SECTOR}<br />
				{$organisation}<br />
			</td>
		</tr>
			<tr><td style='text-align:center; border:0px solid #000;width:100%;'>
				Bill N<sup>r</sup>: {$record['DocID']}</td></tr>
		<tr><td colspan=2>{$str}</td></tr>
	</table>
	</td></tr>
	<tr>
	<td>{$developer}</td>
	<td style='text-align:right;'>{$developer}</td>
	</tr>
</table>
TABLE;
if(!@$_GET['print']){
	echo $sstr; 
	die;
}
//require tde MPDF Library
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage("L");

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($sstr);

$pdf->Output(); 
die;
?>
