<?php
session_start();
/* header("Content-Type: application/pdf"); */
//var_dump($_SESSION);
require_once "../lib/db_function.php";
header("Title='CBHI FORM");
$record = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.* from pa_records WHERE PatientRecordID='{$_GET['records']}'",$con),$multirows=false,$con);
$tm_paid = formatResultSet($rslt=returnResultSet($sql="SELECT mu_tm.* from mu_tm WHERE PatientRecordID='{$_GET['records']}'",$con),$multirows=false,$con);
//var_dump($tm_paid); die;
$tm = "";
if($tm_paid['TicketPaid'] == 0){
	$tm = "<th style='font-size:18px; border:1px solid #000;'>TM Not Paid!</th>";
}
$service = formatResultSet($rslt=returnResultSet($sql="SELECT se_records.*, se_name.ServiceCode from se_records, se_name WHERE se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID='{$record['PatientRecordID']}'",$con),$multirows=false,$con);
$date_ = date("Y-m-d",time());
//var_dump($service); die;
$count_consult = 3; count($conslt = formatResultSet($rslt=returnResultSet($sql="SELECT co_category.* from co_category, se_consultation WHERE se_consultation.ServiceID = '{$service['ServiceNameID']}' && se_consultation.ConsulationID = co_category.ConsultationCategoryID ORDER BY ConsultationCategoryName ASC",$con),$multirows=true,$con));
$patient = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE PatientID='{$record['PatientID']}'",$con),$multirows=false,$con);
$patient_address = formatResultSet($rslt=returnResultSet($sql="SELECT ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName from ad_village, ad_cell, ad_sector, ad_district WHERE ad_village.ViillageID='{$patient['VillageID']}' && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID",$con),$multirows=false,$con);
//$family = !$patient['FamilyCode']?null:formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE N_ID='{$patient['FamilyCode']}'",$con),$multirows=false,$con);
$existing_cons = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$record['PatientRecordID']}'",$con);
//$records = null;
if($existing_cons)
	$date_ = $existing_cons[0]['Date'];
//select three last records and display them
$records = formatResultSet($rslt=returnResultSet($sql="SELECT co_records.*, pa_records.DocID, pa_records.PatientRecordID from pa_records, co_records  WHERE pa_records.PatientRecordID != '{$_GET['records']}' && pa_records.PatientRecordID = co_records.PatientRecordID && pa_records.PatientID='{$record['PatientID']}' && pa_records.DateIn < '{$date_}' ORDER BY co_records.Date DESC LIMIT 0, 2",$con),$multirows=true,$con);
$to_day_records = formatResultSet($rslt=returnResultSet($sql="SELECT co_records.*, pa_records.DocID from pa_records, co_records  WHERE pa_records.PatientRecordID = co_records.PatientRecordID && pa_records.PatientRecordID='{$_GET['records']}' ORDER BY co_records.Date DESC LIMIT 0, 2",$con),$multirows=true,$con);
//var_dump($to_day_records);

$old_consultation = "";
$last = "";
if($records){
	//$records[] = $records[0];
	$last .= "<div>
	Historique 2 dernierès visites
	<table class=_history><tr><td>Date</td><td>Diagnostic</td><td>Exams</td><td>Medecines</td><td>Acts</td><td>Hosp.</td></tr>";
	foreach($records as $r){
		//var_dump($r);
		$last .= "<tr valign=top><td>{$r['Date']}</td><td style='width:150px;'>";
		//search for diagnostic
		$diag_ = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic_records.*, co_diagnostic.DiagnosticName FROM co_diagnostic_records, co_diagnostic  WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = '{$r['ConsultationRecordID']}' ORDER BY DiagnosticType ASC",$con),$multirows=true,$con);
		if($diag_){
			foreach($diag_ as $dg){
				$last .= ($dg['DiagnosticType'] == 1?"Pr.":"Sec.").": ".$dg['DiagnosticName']."; ".($dg['CaseType']?"AC":"NC")."<br />";
			}
		}
		$last .= "</td><td style='width:150px;'>";
		//select exams
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT la_records.*, la_exam.ExamName, la_result.ResultName from la_result_record, la_result, la_records, la_price, la_exam  WHERE la_result.ResultID = la_result_record.ResultID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && la_records.ConsultationRecordID='{$r['ConsultationRecordID']}'",$con),$multirows=true,$con);
		//echo $sql;
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['ExamName']}: <font color=red>{$ex['ResultName']}</font><br />";
			}
		}
		$last .= "</td><td style='width:170px;'>";
		//select medecines
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT md_records.*, md_name.MedecineName from md_records, md_price, md_name  WHERE md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.ConsultationRecordID='{$r['ConsultationRecordID']}'",$con),$multirows=true,$con);
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['Quantity']} {$ex['MedecineName']}<br />";
			}
		}
		$last .= "</td><td style='width:150px'>";
		//select acts
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT ac_records.*, ac_name.Name FROM ac_records, ac_price, ac_name  WHERE ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['Name']}<br />";
			}
		}
		$last .= "</td><td>";
		//select hospitalisation history now
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT ho_record.* FROM ho_record WHERE ho_record.RecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['Days']} Jrs<br />";
			}
		}
		$last .= "</td></tr>";
	}
	$last .= "</table></div>";
} else{
	$last = "No Previous Record";
}
//var_dump($records);
//die;
$consult_str = "<table><tr>";
$empty_cells = "";
$old_consultation_cells = "";
//var_dump($record);
$existing_cons = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$record['PatientRecordID']}'",$con);
//var_dump($existing_cons);
$invisibl_ = true;
foreach($conslt as $c){
	//var_dump($c);
	if($c['ConsultationCategoryName'] != "invisible"){
		$invisibl_ = false;
	}
	$consult_str .= $c['ConsultationCategoryName'] != "invisible"?"<td style='text-align:center; border:0px solid #000; width:120px; text-align:right;'>{$c['ConsultationCategoryName']}</td><td style='vertical-align:bottom; text-align:center; width:40px;'>".($c['ConsultationCategoryID'] == returnSingleField("SELECT co_price.ConsultationCategoryID FROM co_price WHERE co_price.ConsultationPriceID='{$to_day_records[0]['ConsultationPriceID']}'","ConsultationCategoryID",true,$con)?"<img src='../images/close.png' />":"&nbsp;&nbsp;&nbsp;")."</td>":"";
	//var_dump($existing_cons);
	if($existing_cons)
		$empty_cells .= "<td style='height:20px'>".(($ssss = returnSingleField($s_q_l = "SELECT co_price.ConsultationPriceID FROM co_price WHERE ConsultationPriceID='{$existing_cons[0]['ConsultationPriceID']}' && ConsultationCategoryID='{$c['ConsultationCategoryID']}'","ConsultationPriceID",$data=true, $con)) == $existing_cons[0]["ConsultationPriceID"]?"<center><img src='../images/close.png' width='30px' /></center>":"")."</td>";
	else
		$empty_cells .= "<td style='height:20px'></td>";
}
if(!$invisibl_)
	$consult_str .= "<td style='text-align:center; border:0px solid #000; width:150px; text-align:right;'>Register No:</td><td style='width:100px;'></td>";
$consult_str .= "</tr></table>";
$diagno = "";
$existing_medicines = "";
$existing_medicines_date = "";
$existing_consumables = "";
$existing_consumables_date = "";
$existing_exams = "";
$existing_exams_date = "";
if($existing_cons){
	//select all diagno for the existing record
	$diag = returnAllData("SELECT co_diagnostic.*, co_diagnostic_records.* FROM co_diagnostic, co_diagnostic_records WHERE co_diagnostic.DiagnosticID = co_diagnostic_records.DiagnosticID && co_diagnostic_records.ConsulationRecordID='{$existing_cons[0]['ConsultationRecordID']}' ORDER BY DiagnosticType ASC",$con);
	//var_dump($diag);
	foreach($diag as $di){
		$diagno .= ($di['DiagnosticType'] == 1?"Principal":"Secondary").":".$di['DiagnosticName']." ".($dg['CaseType']?"AC":"NC")."<br />";
	}
	
	//select all exams for the existing record
	$exm = returnAllData("SELECT la_exam.*, la_result.*, la_records.* FROM la_exam, la_records, la_result_record, la_result WHERE la_exam.ExamID = la_result.ExamID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_records.ConsultationRecordID = '{$existing_cons[0]['ConsultationRecordID']}'",$con);
	//var_dump($exm);
	$count_ = 1;
	foreach($exm as $di){
		$existing_exams .= ($count_++).". ".$di['ExamName']." Result:".$di['ResultName']."<br />";
		if(!preg_match("/{$di['ResultDate']}/",$existing_exams_date))
			$existing_exams_date .= $di['ResultDate']."<br />";
		else
			$existing_exams_date .= "<br />";
	}
	
	//select all medecines for the existing record
	$mds = returnAllData("SELECT md_name.*, md_records.* FROM md_name, md_records, md_price WHERE md_name.MedecineNameID = md_price.MedecineNameID && md_price.MedecinePriceID = md_records.MedecinePriceID && md_records.ConsultationRecordID = '{$existing_cons[0]['ConsultationRecordID']}'",$con);
	//var_dump($exm);
	$count_ = 1;
	foreach($mds as $di){
		$existing_medicines .= ($count_++).". ".$di['MedecineName']." Qty:".$di['Quantity']."<br />";
		if(!preg_match("/{$di['Date']}/",$existing_medicines_date))
			$existing_medicines_date .= $di['Date']."<br />";
		else
			$existing_medicines_date .= "<br />";
	}
	
	//select all consummables for the existing record
	$cns = returnAllData("SELECT md_name.*, cn_records.* FROM md_name, cn_records, md_price WHERE md_name.MedecineNameID = md_price.MedecineNameID && md_price.MedecinePriceID = cn_records.MedecinePriceID && cn_records.PatientRecordID = '{$existing_cons[0]['PatientRecordID']}'",$con);
	//var_dump($cns);
	$count_ = 1;
	foreach($cns as $di){
		$existing_consumables .= ($count_++).". ".$di['MedecineName']." Qty:".$di['Quantity']."<br />";
		if(!preg_match("/{$di['Date']}/",$existing_consumables_date))
			$existing_consumables_date .= $di['Date']."<br />";
		else
			$existing_consumables_date .= "<br />";
	}
}
$age = getAge($patient['DateofBirth']);
$file_number = explode("/",$record['InsuranceCardID'])[2];
//echo $file_number; die;
$info = <<<INFO
<html><head><title>CBHI Patients</title></head><body>
<style>
	.withborders td{
		border:1px solid #000;
		vertical-align:top;
	}
	
	.inner_table td{margin-left:-2px;}
	.inner_table{ border:0px solid #000; margin-left:-3px;}
	._history{ border-collapse:collapse; }
	._history td{vertical-align:top; border-bottom:1px solid #000; font-size:12px; font-weight:normal; font-family:arial; }
</style>
<table width=100% border=0>
	<tr>
		<th align=left>
			REPUBLIC OF RWANDA<br />
			MINISTRY OF HEALTH <br />
			Health Center: {$institution_name}<br />
			CBHI: {$cbhi_name}<br />
		</th>
		{$tm}
	</tr>
	<tr>
		<td colspan=2>
			<table class=inner_table width=100% style='width:100%'>
				<tr>
					<td colspan=3>CBHI Code: <b>{$record['InsuranceCardID']}</b></td>
					<td>Village: <b>{$patient_address['VillageName']}</b></td>
				</tr>
				<tr>
					<td style='width:370px'>Patient: <b>{$patient['Name']}</b></td>
					<td style='width:210px'>Age: <b>{$age}</b></td>
					<td style='width:130px; text-align:left;'>Sex: <b>{$patient['Sex']}</b></td>
					<td>Cell: <b>{$patient_address['CellName']}</b></td>
				</tr>
				<tr>
					<td>Family Chief: <b>{$patient['FamilyCode']}</b></td>
					<td colspan=2>Family Category: <b>{$record['FamilyCategory']}</b></td>
					<td> Sector: <b>{$patient_address['SectorName']}</b></td>
				</tr>
				<tr>
					<td>
						File Number: {$file_number}
					</td>
					<td colspan=2>Service: <b>{$service['ServiceCode']}</b></td>
					<td>District: <b>{$patient_address['DistrictName']}</b></td>
				</tr>
				<tr>
					<td colspan=4 style='text-align:center'>Document Number:{$record['DocID']}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2>
		<br />
			{$last}
		<br />
		</td>
	</tr>
	<tr>
		<td colspan=2 style='border:0px solid #f00;'>
			<br />
			Details on received acts
			<table class='withborders' style='width:900px;border-collapse:collapse;'>
				<tr valign='top'>
					<td rowspan=2  style='width:90px;'>Date</td><td colspan='2'>ITEM</td>
					<td rowspan=2 style='width:500px;'><table style=''>
					<tr><td style='width:150px;border:0px solid #000;'>Diagnostic:</td><td style='border:0px solid #000;'>NC</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td><td style='border:0px solid #000;'>AC</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td><td style='border:0px solid #000;'>PECIME</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td><td style='border:0px solid #000;'>Hosp.</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td></tr>
					<tr><td style='width:150px;border:0px solid #000;'>Principal:</td></tr>
					<tr><td style='width:150px;border:0px solid #000;'>Secondary:</td></tr>
					</table><br />{$diagno} </td>
				</tr>
				<tr>
					<td style='width:180px;'>Temperature(<sup>o</sup>C): <b>{$record['Temperature']}</b></td><td style='width:160px;'>Weight(Kg): <b>{$record['Weight']}</b></td>
										
				</tr>
				
				<tr>
					<td style="text-align:center;">{$record['DateIn']}</td>
					<td colspan=3>{$consult_str}</td>
				</tr>
				<tr valign=top>
					<td style='height:100px'><br />{$existing_exams_date}<br /><br /><br />&nbsp;</td>
					<td colspan='{$count_consult}'>Exam:<br />{$existing_exams}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:100px'><br />{$existing_medicines_date}<br /><br /><br />&nbsp;</td>
					<td colspan='{$count_consult}'>Medicines:<br />{$existing_medicines}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:90px'></td>
					<td colspan='{$count_consult}'>Acts:<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:100px'><br />{$existing_consumables_date}<br /><br /><br />&nbsp;</td>
					<td colspan='{$count_consult}'>Consumables:<br />{$existing_consumables}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:50px'></td>
					<td colspan='{$count_consult}'>
						Hospitalization: Days and Type: <br /> <br />
						Date In: ......./........./.........&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Date Out: ......./......../.........&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
				<tr valign=top>
					<td style='height:30px'></td>
					<td colspan='{$count_consult}'>Decision:
						<table>
							<tr>
								<td style='text-align:center; border:0px solid #000; width:220px; text-align:right;'>Sortie Definitif</td><td style='width:40px'></td>
								<td style='text-align:center; border:0px solid #000; width:220px; text-align:right;'>Transfer Avec Ambulance</td><td style='width:40px'></td>
								<td style='text-align:center; border:0px solid #000; width:220px; text-align:right;'>Transfer Sans Ambulance</td><td style='width:40px'></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 style='height:20px;'>Date: {$record['DateIn']}</td>
	</tr>
	<tr>
		<td colspan=2 style='border:0px solid #000;'>
			<table width=100% style='border:0px solid #000;' border=0>
				<tr valign=top>
					<td style='width:400px; border:0px solid #000;'>
						Nurse's Name & Signature
					</td>
					<td style='width:510px; text-align:right; border:0px solid #000;'>
						Beneficiary's Name: 
						{$patient['Name']}
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>

</html>
INFO;

//echo $info; die;

//require the MPDF Library
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage();

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($info);
$filename = "./files/".$record['DocID'].".pdf";
//echo $filename;
$pdf->Output(); 
die;
?>
<script type="text/javascript" language=JavaScript>
    function CheckIsIE()
    {
        if (navigator.appName.toUpperCase() == 'MICROSOFT INTERNET EXPLORER') 
            { return true;  }
        else 
            { return false; }
    }
    function PrintThisPage()
    {
         if (CheckIsIE() == true)
         {
            document.content.focus();
            document.content.print();
         }
         else
         {
            window.frames['iframeprint'].focus();
            window.frames['iframeprint'].print();
         }
     }
</script> 
<link href="./print.css" rel="stylesheet" type="text/css" media="print" />
<button value='print' onclick='PrintThisPage()'></button>
<script type="text/javaScript" src='../js/jquery.full.js'></script>
<script>
jQuery(document).ready(function($) {
  function print(url)
  {
      var _this = this,
          iframeId = 'iframeprint',
          $iframe = $('iframe#iframeprint');
		  
      $iframe.attr('src', url);

      $iframe.load(function() {
          callPrint(iframeId);
		  //console.log($iframe);
      });
  }

  //initiates print once content has been loaded into iframe
  function callPrint(iframeId) {
	  //alert("Print trigger_error");
      var PDF = document.getElementById(iframeId);
		//window.frames['iframeprint'].focus();
		//window.frames['iframeprint'].print();
      /* PDF.focus();
	  try{
			
			console.log("Attempt to print");
			PDF.contentWindow.print();
			//
			console.log("End of print attempt");
	  } catch(e){
		  console.log(e);
		  window.print();
	  } */
  }
  //try to call print function now
  print("<?= $filename ?>");
});
</script>

<iframe id="iframeprint" name="iframeprint" width='99%' height='98%'></iframe>

<?php die; ?>
<style type="text/css">
    
        .dontprint{display:none} 
    
</style>
<script type="text/javascript">
    function printIframePdf(){
        window.frames["printf"].focus();
        try {
            window.frames["printf"].print();
        }
        catch(e){
            window.print();
            console.log(e);
        }
    }
    function printObjectPdf() {
        try{            
            document.getElementById('idPdf').Print();
        }
        catch(e){
            printIframePdf();
            console.log(e);
        }
    }

    function idPdf_onreadystatechange() {
        if (idPdf.readyState === 4){
			alert("Ready to print now"); return;
            setTimeout(printObjectPdf, 1000);
		}
    }
</script>
<div class="dontprint" >
    <form><input type="button" onClick="printObjectPdf()" class="btn" value="Print"/></form>
</div>

<iframe id="printf" onreadystatechange='alert("OK");' name="printf" src="<?= $filename ?>" frameborder="0" width="440" height="580" style="width: 99%; height: 98%;display: none;"></iframe>
<object id="idPdf" onreadystatechange="alert('OK');idPdf_onreadystatechange()"
    width="440" height="580" style="width: 99%; height: 98%;" type="application/pdf"
    data="<?= $filename ?>">
    <embed src="<?= $filename ?>" width="440" height="580" style="width: 440px; height: 580px;" type="application/pdf">
    </embed>
    <span>PDF plugin is not available.</span>
</object>