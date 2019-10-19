<?php
session_start();
/* header("Content-Type: application/pdf"); */
//var_dump($_SESSION);
require_once "../lib/db_function.php";
header("Title='CBHI FORM'");
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
	._history td{vertical-align:top; border-top:1px solid #000; font-size:12px; font-weight:normal; font-family:arial; }
</style>
<table width=100% border=0 style="border-collapse:collapse;">
	<tr>
		<th align=left>
			RWANDA SOCIAL SECURItY BOARD (RSSB)<br />
			Community Based Health Insurance (CBHI) <!--
			Health Center: <br />
			CBHI: {$cbhi_name}<br />-->
		</th>
		<th>&nbsp;</th>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left;  border: 0px solid #000;">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: center; border: 1px solid #000; ">
			HEALTH CARE INVOICE / FACTURE POUR SOINS DE SANTE N<sup><u>0</u></sup>: <b>.. .. .. .. .. .. .. .. ..</b>
		</td>
	<tr>
	<tr>
		<td colspan=2 style="text-align: left;  border: 0px solid #000;">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; background-color:#dfdfdf; border-top: 1px solid #000;  border-left: 1px solid #000;  border-right: 1px solid #000; ">
			I. HEALTH FACILITY INFORMATION / INFROMATION SUR LA FORMATION SANITAIRE
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left;  border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:6px; ">
			Health facility name / Nom de la formation sanitaire: <b>{$institution_name}</b>
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left;  border-left: 1px solid #000;  border-right: 1px solid #000; ">
			District name / Nom du District: <b>{$_DISTRICT}</b>
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; border-bottom: 1px solid #000;  border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:10px; ">
			Type of health facility / Type de formation sanitaire:
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			HC/CS
			<img src="../images/box-checked.png" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			HP/PS
			<img src="../images/box.png" />
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left;  border: 0px solid #000;">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; background-color:#dfdfdf; border-top: 1px solid #000;  border-left: 1px solid #000;  border-right: 1px solid #000; ">
			II. PATIENtT INFORMATION / INFORMATION SUR LE PATIENT
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left;  border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:15px; padding-top:12px; ">
			Name Head of the household/Nom du chef de menage: <b>.. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. ..</b>
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left;  border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:15px; ">
			Head of the household CBHI Affiliation number/Numero d'affiliation du chef de menage: <b>&nbsp;&nbsp;.. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. ..</b>
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left;  border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:15px; ">
			Beneficiary Name/Nom du beneficiaire: <b>&nbsp;.. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. ..</b>
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left;  border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:15px; ">
			Beneficiary CBHI Affiliation number/Numero d'affiliation du beneficiaire: <b>.. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. ..</b>
		</td>
	</tr>
	<tr>
		<td style="text-align: left;  border-left: 1px solid #000; padding-bottom:15px; width:150px; ">
			Catchment Area/Zone de rayonnement:
			Z <img src="../images/box.png" />
			&nbsp;
			HZ <img src="../images/box.png" />
			&nbsp;
			HD <img src="../images/box.png" />
		</td>
		<td style="text-align: left;  border-right: 1px solid #000; padding-bottom:15px; ">
			Telephone Number/Numero de Telephone:<b> .. .. .. .. .. .. .. ..</b>
		</td>
	</tr>
	<tr>
		<td style="text-align: left;  border-left: 1px solid #000; padding-bottom:15px;">
			Sex/Sexe:
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Female <img src="../images/box.png" />
			&nbsp;
			Male <img src="../images/box.png" />
		</td>
		<td style="text-align: left;  border-right: 1px solid #000; padding-bottom:15px; ">
			Ubudehe Category/Categorie ubudehe (1, 2, 3, 4):<b>. .. .. .. ..</b>
		</td>
	</tr>
	<tr>
		<td style="text-align: left;  border-left: 1px solid #000; padding-bottom:15px;">
			Age: <b>.. .. .. ..</b>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Date of Birth: <b>.. ../.. ../.. ..</b>
		</td>
		<td style="text-align: left;  border-right: 1px solid #000; padding-bottom:15px; ">
			Prisonner/Prisonier:
			&nbsp;&nbsp;
			Yes <img src="../images/box.png" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			No <img src="../images/box-checked.png" />
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; border-bottom: 1px solid #000;  border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:5px; ">
			District: <b>.. .. .. .. .. .. ..</b>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Sector: <b>.. .. .. .. .. .. .. ..</b>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Cell: <b>.. .. .. .. .. .. .. .. ..</b>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Village: <b>.. .. .. .. .. .. .. ..</b>
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left;  border: 0px solid #000;">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; background-color:#dfdfdf; border-top: 1px solid #000;  border-left: 1px solid #000;  border-right: 1px solid #000; ">
			II. DETAILS OF MEDICAL CARE RECEIVED/DETAILS DES SOINS RECUS
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; font-size:12px; border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:6px; ">
			Type of Medical Visit/Type de visite Medicale: 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Outpatient/Ambulatoire <img src="../images/box.png" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Inpatient/Hospitalization <img src="../images/box.png" />
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; font-size:12px; border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:6px; ">
			Deasese Episode/Episode de la maladie: 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			New Case/Nouveaux Cas <img src="../images/box.png" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Old Case/Ancien Cas <img src="../images/box.png" />
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; font-size:12px; border-bottom: 1px solid #000;  border-left: 1px solid #000;  border-right: 1px solid #000; padding-bottom:10px; ">
			Purpose of the visit/Motif de la visite:<br />
			Natural Deasese/Maladie naturelle <img src="../images/box.png" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Occupational Deases/Maladie Professionnelle <img src="../images/box.png" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Other/Autre <img src="../images/box.png" /><br />
			Road Traffic Accident/Accident de la circulation <img src="../images/box.png" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Work Accident/Accident de Travail <img src="../images/box.png" />
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; background-color:#dfdfdf; border-top: 1px solid #000;  border-left: 1px solid #000;  border-right: 1px solid #000; ">
			DIAGNOSIS/DIAGNOSTIC
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; font-size:12px; border-bottom: 0px solid #000;  border-left: 1px solid #000;  border-right: 1px solid #000;">
			&nbsp;<br />
			&nbsp;<br />
			&nbsp;<br />
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; border-bottom: 1px solid #000;  border-left: 1px solid #000;  border-right: 1px solid #000;">
			<table border=1 style="border-collapse:collapse; font-size:12px; margin-left:-3px; margin-right:-3px; margin-bottom:-3px;">
				<tr>
					<td colspan=2 style="text-align: center; background-color:#dfdfdf; border-left: 0 solid #000; width:580px; ">
						Description
					</td>
					<td style="text-align: center; background-color:#dfdfdf; width:120px; ">
						Quantity/Days
						Quantite/Jour
					</td>
					<td style="text-align: center; background-color:#dfdfdf; border: 1px solid #000; width:100px;  ">
						Unit Cost/ Cout Initaire
					</td>
					<td style="text-align: center; background-color:#dfdfdf; border-right: 0px solid #000; width:100px;  ">
						Total Cost/ Cout Total
					</td>
				</tr>
				<tr>
					<td style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; ">
						Consultation
					</td>
					<td style="text-align: left; width:440px; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; border-top: 0 solid #000; ">
						Date: 
					</td>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left; border-left: 0 solid #000; border-top: 0 solid #000; ">
						&nbsp;
					</td>
					<td colspan=3 style="text-align: left;">
						Total Cost Consultation/Cout total consultation
					</td>
					<td style="text-align: left; background-color:#dfdfdf; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td rowspan=3 style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; ">
						Laboratory Test/ Examens de laboratoire
					</td>
					<td style="text-align: left; width:440px; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; border-top: 0 solid #000; ">
						Date: 
					</td>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left; border-left: 0 solid #000; border-top: 0 solid #000; ">
						&nbsp;
					</td>
					<td colspan=3 style="text-align: left;">
						Total Cost Laboratory Test/Cout total examens de laboratoire
					</td>
					<td style="text-align: left; background-color:#dfdfdf; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td rowspan=2 style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; ">
						Hospitalization/ Hospitalisation<br />
						From/Du: <br />
						To/Au:
					</td>
					<td style="text-align: left; width:440px; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan=3 style="text-align: left;">
						Total Cost Hospitalization/Cout total hospitalisation
					</td>
					<td style="text-align: left; background-color:#dfdfdf; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td rowspan=3 style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; ">
						Procedures & Consumables/ Acts et Consommables
					</td>
					<td style="text-align: left; width:440px; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; border-top: 0 solid #000; ">
						Date: 
					</td>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left; border-left: 0 solid #000; border-top: 0 solid #000; ">
						&nbsp;
					</td>
					<td colspan=3 style="text-align: left;">
						Total Cost Medical Procedures & Consumables/Cout total Acts et consomable medicaux
					</td>
					<td style="text-align: left; background-color:#dfdfdf; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td rowspan=4 style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; ">
						Medicines/ Midicaments<br />
						(Form/Forme & Dosage)
					</td>
					<td style="text-align: left; width:440px; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; border-top: 0 solid #000; ">
						Date: 
					</td>
					<td style="text-align: left;">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td style="text-align: left; border-left: 0 solid #000; border-top: 0 solid #000; ">
						&nbsp;
					</td>
					<td colspan=3 style="text-align: left;">
						Total Cost Medicines/Cout total Medicaments
					</td>
					<td style="text-align: left; background-color:#dfdfdf; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td rowspan=2 style="text-align: left; border-left: 0 solid #000; border-bottom: 0 solid #000; ">
						Ambulance<br />
						Date:
					</td>
					<td style="text-align: left; width:440px; ">
						&nbsp;
					</td>
					<td style="text-align: right; ">
						KM&nbsp;
					</td>
					<td style="text-align: left; ">
						&nbsp;
					</td>
					<td style="text-align: left; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan=3 style="text-align: left; border-bottom: 0 solid #000; ">
						Total Cost Ambulance/Cout total ambulance
					</td>
					<td style="text-align: left; background-color:#dfdfdf; border-bottom: 0 solid #000; border-right: 0px solid #000; ">
						&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; border: 0px solid #000;">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; border-top: 1px solid #000; font-size:12px; border-left: 1px solid #000;  border-right: 1px solid #000; ">
			Beneficiary name & signature/Nom et Signature du beneficiaire: <b>.. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. .. ..</b><br />&nbsp;
		</td>
	</tr>
	<tr>
		<td style="text-align: left; border-bottom: 1px solid #000; font-size:12px; border-left: 1px solid #000;  border-right: 0px solid #000;">
			Nurse name & signature/Nom et signature infirmier(ere) traitant <br />
			&nbsp;<br />
			Health facility Stamp/Cacher du CS/PS
		</td>
		<td style="text-align: left; border-bottom: 1px solid #000; font-size:12px; border-left: 0px solid #000;  border-right: 1px solid #000; padding-bottom:20px;">
			Approval of CBHI Verification Agent/Approbation du verificateur CBHI<br />
			&nbsp;<br />
			CBHI Stamp/Cachet
		</td>
	</tr>
</table>
</body>

</html>
INFO;

// echo $info; die;

//require the MPDF Library
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage();

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($info);
$pdf->setHTMLFooter("<div style='font-size:7px; font-family:arial; font-weight:bold; text-align:right; border-top:1px dashed #dfdfdf; color:#dfdfdf;'>printed using care software | easy one ltd</div>");
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