<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
/* echo "<span class=error>Please Use Version <a href='../../care_v2.0.1/rcp/reprint.php'>2.0.1</a></span>";
return; */
//var_dump($_GET);
//clear the content of the sachets session
$_SESSION['sachets'] = array();
//select all patients related to the found key search
$patient = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.*, pa_info.DateofBirth from pa_records, pa_info WHERE PatientRecordID='{$_GET['key']}' && pa_info.PatientID = pa_records.PatientID",$con),$multirows=false,$con);
//var_dump($patient);
$age = getAge($patient['DateofBirth'],$notation=2, $patient['DateIn']);
//select TM Payment
$tm = formatResultSet($rslt=returnResultSet($sql="SELECT mu_tm.* from mu_tm WHERE PatientRecordID='{$_GET['key']}'",$con),$multirows=false,$con);

$service = formatResultSet($rslt=returnResultSet($sql="SELECT se_records.*, se_name.ServiceCode from se_records, se_name WHERE se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID='{$patient['PatientRecordID']}'",$con),$multirows=false,$con);
//var_dump($service);
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name WHERE InsuranceNameID='{$patient['InsuranceNameID']}' ORDER BY InsuranceName ASC",$con),$multirows=true,$con);
//var_dump($tm);
if($patient){
//var_dump($patient);
//select the performed consultation on this records now
$insurance_category_id = returnSingleField($sql="SELECT CategoryID from in_name WHERE InsuranceNameID='{$patient['InsuranceNameID']}'",$field="CategoryID",$data=true, $con);
$cons2 = $cons = formatResultSet($rslt=returnResultSet($sql="SELECT co_category.*, co_price.ConsultationPriceID, co_records.* FROM co_category, co_price, se_consultation, co_records WHERE co_records.ConsultationPriceID = co_price.ConsultationPriceID && se_consultation.ServiceID='{$service['ServiceNameID']}' && se_consultation.ConsulationID = co_category.ConsultationCategoryID && co_category.ConsultationCategoryID = co_price.ConsultationCategoryID && co_price.InsuranceCategoryID = '{$insurance_category_id}' && co_records.PatientRecordID='{$patient['PatientRecordID']}' ORDER BY ConsultationCategoryName ASC",$con),$multirows=false,$con);
$dignostic_1 = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic.*, co_diagnostic_records.* FROM co_diagnostic_records, co_diagnostic WHERE co_diagnostic.DiagnosticID = co_diagnostic_records.DiagnosticID && ConsulationRecordID='{$cons['ConsultationRecordID']}' && DiagnosticType='1'",$con),$multirows=false,$con);
$diagnostic_all_data = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic.*, co_diagnostic_records.* FROM co_diagnostic_records, co_diagnostic WHERE co_diagnostic.DiagnosticID = co_diagnostic_records.DiagnosticID && ConsulationRecordID='{$cons['ConsultationRecordID']}' && co_diagnostic.DiagnosticCode !=''",$con),$multirows=true,$con);
$dignostic_2 = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic.*, co_diagnostic_records.* FROM co_diagnostic_records, co_diagnostic WHERE co_diagnostic.DiagnosticID = co_diagnostic_records.DiagnosticID && ConsulationRecordID='{$cons['ConsultationRecordID']}' && DiagnosticType='2'",$con),$multirows=false,$con);
//var_dump($diagnostic_all_data);
$_SESSION['diagnostics'] = $diagnostic_all_data;

//search for medicines prescription now
$medecines = formatResultSet($rslt=returnResultSet($sql="SELECT md_records.MedecineRecordID, md_records.Quantity, md_records.Date, md_name.* FROM md_records, md_price, md_name WHERE md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && ConsultationRecordID='{$cons['ConsultationRecordID']}' ",$con),$multirows=true,$con);
//var_dump($medecines);
$_SESSION['medecines'] = $medecines;

//search for exams now
$_exams_ = formatResultSet($rslt=returnResultSet($sql="SELECT la_records.*, la_exam.ExamName, la_exam.ResultType FROM la_records, la_price, la_exam WHERE la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && ConsultationRecordID='{$cons['ConsultationRecordID']}' ",$con),$multirows=true,$con);
//var_dump($_exams_);
//search for results
for($k = 0; $k<count($_exams_); $k++){
	$_exams_[$k]['ExamNumber'] = preg_match("/[0-9]{4}Q/",$_exams_[$k]['ExamNumber'])?substr($_exams_[$k]['ExamNumber'],6):$_exams_[$k]['ExamNumber'];
	//select all result and make a good string for them
	$result = formatResultSet($rslt=returnResultSet($sql="SELECT la_result_record.*, la_result.ResultName FROM la_result, la_result_record WHERE la_result.ResultID = la_result_record.ResultID && la_result_record.ExamRecordID = '{$_exams_[$k]['ExamRecordID']}' ",$con),$multirows=true,$con);
	if($result){
		//form the out put now
		$str_data = ""; $result_count = 0;
		foreach($result as $r){
			if($result_count++ > 0 && !preg_match("/;$/",$str_data) ){
				$str_data .= ";";
			}
			$str_data .= $r['ResultName'];
			if($_exams_[$k]['ResultType'] && $result_count == 1 && !preg_match("/;$/",$str_data) ){
				$str_data .= ";";
			}
		}
		$_exams_[$k]['Results'] = str_replace("+","_s",$str_data);
	}
}
//var_dump($medecines);
$_SESSION['exams'] = $_exams_;
//var_dump($_exams_);

//search for consumable prescription now
$_consumables_ = array();

$emballage = formatResultSet($rslt=returnResultSet($sql="SELECT cn_records.ConsumableRecordID, cn_records.Date, cn_records.Quantity, cn_name.* FROM cn_records, cn_price, cn_name WHERE cn_records.MedecinePriceID = cn_price.MedecinePriceID && cn_price.MedecineNameID = cn_name.MedecineNameID && PatientRecordID='{$patient['PatientRecordID']}' && cn_name.MedecineName LIKE('%sachets%') ",$con),$multirows=true,$con);
//var_dump($emballage); echo $sql;
$_consumables_sachets = formatResultSet($rslt=returnResultSet($sql="SELECT cn_records.ConsumableRecordID, cn_records.Date, cn_records.Quantity, cn_name.* FROM cn_records, cn_price, cn_name WHERE cn_records.MedecinePriceID = cn_price.MedecinePriceID && cn_price.MedecineNameID = cn_name.MedecineNameID && PatientRecordID='{$patient['PatientRecordID']}' && cn_name.MedecineName NOT LIKE('%sachets%') ",$con),$multirows=true,$con);
JoinArrays($_consumables_, $_consumables_sachets, $_consumables_);
//var_dump($_consumables_); //echo $sql;
$_SESSION['consumables'] = $_consumables_;

//search for acts prescription now
$_acts_ = formatResultSet($rslt=returnResultSet($sql="SELECT ac_records.ActRecordID, ac_records.Date, ac_records.Quantity, ac_name.* FROM ac_records, ac_price, ac_name WHERE ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && PatientRecordID='{$patient['PatientRecordID']}' ",$con),$multirows=true,$con);
//var_dump($_acts_);
$_SESSION['acts'] = $_acts_;
//var_dump($medecines);
//echo $sql;


//search for hospitalisation information now
$hospit = formatResultSet($rslt=returnResultSet($sql="SELECT ho_record.* FROM ho_record WHERE ho_record.RecordID ='{$patient['PatientRecordID']}' ",$con),$multirows=false,$con);
//var_dump($hospit);
?>
<script>

tropho_sent = false;
ascaris_sent = false;
kehist_sent = false;
trichomnas_sent = false;
levure_sent = false;
gb_sent = false;
ankylostome_sent = false;
gr_sent = false;

pst_sent = false;
acc_sent = false;
perf_sent = false;
enabled = false;

</script>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	
	<style>
		.frm{ width:100%; }
		.frm td, .frm th{ border-bottom:3px solid #eee;}
		.frm th{text-align:left;}
		.bb{ text-align:right;}
	</style>
<br />
<?php
//var_dump($insurance);
if($insurance[0]['InsuranceName'] == "RSSB RAMA"){
	// echo "OK";
	// var_dump($patient);
	// Get the Insurance Card informarion to allow new update
	$sql = "SELECT * FROM pa_insurance_cards WHERE InsuranceCardsID='{$patient['InsuranceCardID']}' && PatientID='{$patient['PatientID']}'";
	$insurance_cards = formatResultSet($rslt=returnResultSet($sql, $con),$multirows=false,$con);
	// echo $sql;
	// var_dump($insurance_cards);
	?>
	<div style='border:1px solid #000; width:60%; font-size:13px; margin:2px; padding:5px;'>
		Change Affiliate information: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
		<label>
			<input onclick='changeAffiliateInfo("Self")' type=radio <?= $insurance_cards['Relation'] == 'Self'?"checked":"" ?> id='self' name='relation' value='Self' />Self
		</label>
		<label>
			<input onclick='changeAffiliateInfo("Conjoint")' type=radio <?= $insurance_cards['Relation'] == 'Conjoint'?"checked":"" ?> id='conjoint' name='relation' value='Conjoint' />Conjoint
		</label>
		<label>
			<input onclick='changeAffiliateInfo("Parent")' type=radio <?= $insurance_cards['Relation'] == 'Parent'?"checked":"" ?> id='Pparent' name='relation' value='Parent' />Parent
		</label>
	</div>

	<script>
		function changeAffiliateInfo(value){
			//alert($(".track").html());
			//return;
			//send the ajax query
			$.ajax({
				type: "POST",
				url: "./change_affiliate.php",
				data: "Relation=" + value + "&PatientInsuranceCardsID=<?= $insurance_cards['PatientInsuranceCardsID'] ?>" + "&url=ajax",
				cache: false,
				success: function(result){
					var tm_field = "";
					/*tm_field = $(".track").html() == ""?"pa_09":$(".track").html();
					$("." + tm_field).html(result);*/
					console.log(result);
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
		}
	</script>
	<?php
}
if($insurance[0]['InsuranceName'] == "CBHI"){
	//var_dump($tm);
	?>
	<div style='border:1px solid #000; width:60%; font-size:13px; margin:2px; padding:5px;'>
		<?php
		if($patient['FamilyCategory'] <= 2 && $service['ServiceCode'] != 'PST'){
			echo "<div style='font-size:18px; text-align:center; color:red; padding:5 5px;'>Patient Category is: ".$patient['FamilyCategory']."</div>";
		}
		?>
		Change Co-Payment information: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
		<label>
			<input onclick='changeTM(200,"OK", prompt("Receipt Number"))' type=radio <?= $tm['Type'] == 'OK'?"checked":"" ?> id='paid' name='tm' value='200' />TM Paid
		</label>
		<label style='margin-left:30px;'>
			<input onclick='changeTM(200,"COMPASSION")' type=radio id='cmp' <?= $tm['Type'] == 'COMPASSION'?"checked":"" ?> name='tm' value="COMPASSION" />Compassion
		</label>
		<label style='margin-left:30px;'>
			<input onclick='changeTM(0,"INDIGENT")' type=radio id='not_paid'<?= $tm['Type'] == 'INDIGENT'?"checked":"" ?> name='tm' value="INDIGENT" />Indigent
		</label>
		<label style='margin-left:30px;'>
			<input onclick='changeTM(0,"PANSEMENT")' type=radio id='pst' name='tm' <?= $tm['Type'] == 'PANSEMENT'?"checked":"" ?> value="PANSEMENT" />AC PST
		</label>
		<label style='margin-left:30px;'>
			<input onclick='changeTM(0,"CATEGORY")' type=radio id='pst' name='tm' <?= $tm['Type'] == 'CATEGORY'?"checked":"" ?> value="CATEGORY" />CAT 1 & 2
		</label>
		<span class=change></span>
	</div>
	<script>
		function changeTM(value, type, rc_number=""){
			//alert($(".track").html());
			//return;
			//send the ajax query
			$.ajax({
				type: "POST",
				url: "./change_tm.php",
				data: "tm=" + value + "&type=" + type + "&rcpnumber=" + rc_number + "&ticketid=<?= $tm['TicketID'] ?>" + "&url=ajax",
				cache: false,
				success: function(result){
					var tm_field = "";
					tm_field = $(".track").html() == ""?"pa_09":$(".track").html();
					$("." + tm_field).html(result);
					console.log(tm_field);
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
		}
	</script>
	<?php
}
return; // Here is for full version because any other change could be made by conultant person.
?><!--
<div id=pst_ribbon style='text-align:left; font-size:14px; margin-top:10px;'>
	<label style='padding-right:10px;'><input type=radio name=pst onclick='pansement("pansement simple")' />pst simple</label>
	<label style='padding-right:10px;'><input type=radio name=pst onclick='pansement("pansement compliqué")' />pst compliqu&eacute;</label>
	<label style='padding-right:10px;'><input type=radio name=pst onclick='pansement("suture simple","<?= @$patient['Weight']; ?>")' />suture simple</label>
	<label style='padding-right:10px;'><input type=radio name=pst onclick='pansement("suture compliqué","<?= @$patient['Weight']; ?>")' />suture compliqu&eacute;e</label>
</div>-->
<input type=hidden id=lock_malaria value='0'>
<input type=hidden id=lock_malaria_anti value='0'>
<?php
//track the status of the file now
if($_acts_ || $medecines){
	//disable reception date to be dispatched
	?>
	<input type=hidden id=default_date value='' />
	<?php
} else{
	//enable reception date to dispatched
	?>
	<input type=hidden id=default_date value='<?= @$patient['DateIn'] ?>' />
	<?php
}
?>
<input type=hidden name=disable_submit id=disable_submit name=deisable_submit value='0' />
<form action="./save_from_cons_doc.php" method=post style='border:1px solid #efefef;' id="frm_data">
	<input type=hidden name=patientid value='<?php echo $patient['PatientRecordID'] ?>' />
	
	<div class=bb>
	<input type=submit id=save_data_and_print name=print_bill style='font-size:8px; display:none;' class=flatbtn value='Save & Print' />
	</div>
	<div style='height:1px; border:0px solid #000;'>
		<input type=text class=txtfield1 onblur='setTimeout("ds_hi()",200);' style='width:75px; font-size:12px; position:relative; top: 16px; left:18px;' name=consultation_date id=consultation_date onclick='ds_sh(this,"consultation_date")' value='<?= $patient['DateIn']?$patient['DateIn']:date("Y-m-d",time()) ?>' />
	</div>
	<table class=frm border=0>
		<tr><td>Consul. </td>
		<th>
			&nbsp;<br />
			<input type=hidden id=consultation_data />
			Register: <input type='text' autocomplete="off" class=txtfield1 id=register_id name=register_id  style='width:90px; font-size:12px;' value='<?= @$cons['RegisterNumber'] ?>'/>
		<?php
			$cat = dayCategory($patient['DateIn']);
			//echo $cat;
			//var_dump($cons2);
			if(@$cons){
				echo "<input type=hidden name='consultationexistbefore' value='{$cons['ConsultationRecordID']}' />";
			}
			//echo $patient['InsuranceNameID'];
			//echo $insurance_category_id;
			//select all consultion that can be provided in the center
			$cons_list = returnAllData($s = "SELECT co_category.*, co_price.ConsultationPriceID FROM co_category, co_price, se_consultation WHERE se_consultation.ServiceID='{$service['ServiceNameID']}' && se_consultation.ConsulationID = co_category.ConsultationCategoryID && co_category.ConsultationCategoryID = co_price.ConsultationCategoryID && co_price.InsuranceCategoryID = '{$insurance_category_id}' && co_price.Status=1 && se_consultation.Status=1 ORDER BY ConsultationCategoryName ASC",$con);
			//echo $s;
			//var_dump($cons_list);
			if($cons_list){
				if($cons_list[0]['ConsultationCategoryName'] == "invisible"){
					echo "<input type=hidden name=consultation value='{$cons_list[0]['ConsultationPriceID']}' />";
				} else{
					$i = 0;
					foreach($cons_list as $cons){
						//var_dump($cons['ConsultationCategoryName'] == "CPC Jour");
						//var_dump(( $cons2['ConsultationPriceID'] == $cons['ConsultationPriceID'] || (($i++) == 0 || $cons['ConsultationCategoryName'] == "CPC Jour")));
						$i++;
						echo "<label><input class=cons_cat type=radio ".(( $cons2['ConsultationPriceID'] == $cons['ConsultationPriceID'] || ($i) == 0 || $cons['ConsultationCategoryName'] == "CPC Jour" || $cons['ConsultationCategoryName'] == $cat || ($cons['ConsultationCategoryName'] == "invisible" && ($service['ServiceCode'] == 'MAT' || $service['ServiceCode'] == 'PST')))?"checked":"")." name=consultation onclick='$(\"#consultation_data\").val(\"{$cons['ConsultationPriceID']}\");' value='{$cons['ConsultationPriceID']}'>".($cons['ConsultationCategoryName'] != 'invisible'?$cons['ConsultationCategoryName']:"No consultation")."</label> ";
						if(( $cons2['ConsultationPriceID'] == $cons['ConsultationPriceID'] || ($i) == 0 || $cons['ConsultationCategoryName'] == "CPC Jour" || $cons['ConsultationCategoryName'] == $cat)){
							?>
							<script>
								$("#consultation_data").val("<?= $cons['ConsultationPriceID'] ?>");
							</script>
							<?php
						}
					}
				}
			} else{
				echo "<span class=error-text>You Can not Provide Any Consultation Service to this type of Insurance</span>";
			}
		?>
		
		</th>
			
		</tr>
		<tr><td><input type=hidden id='diag_counter' />Diagnostic</td>
			<td style='border:0px solid #000;'>
				&nbsp;
				<div class=diag1 style='border:0px solid #000;'></div>
			</td>
		</tr>
		<tr><td><input type=hidden id='exam_counter' />Exams</td>
			<td style='border:0px solid #000;'>
				&nbsp;
				<div class=exam1 style='border:0px solid #000;'></div>
			</td>
		</tr>
		<tr><td><input type=hidden id='med_counter' />Medicines</td>
			<td>
				&nbsp;
				<div class=medecine1></div>
			</td>
		</tr>
		<tr><td><input type=hidden id='ac_counter' />Acts</td>
			<td>
				&nbsp;
				<div class=act1></div>
			</td>
		</tr>
		<tr valign=top><td><input type=hidden style='width:30px;' id='cons_counter' />Consumables</td>
			<td>
				&nbsp;
				<div style='' class=consumable0>
					
					<div style='height:1px; border:0px solid #000;'>
						<input value='' type=text id='consumabledate0' name='consumabledate0' placeholder='Date' class="txtfield1 all_date" style='width:75px; font-size:12px; font-weight:bold; position:relative; top: 2px; left:-80px;' onclick="ds_sh(this,'consumabledate0')" />
					</div>
					<table class=list-1>
						<tr>
							<td><input readonly type=text value='sachets' id='consumablename0' name='consumablename0' placeholder='Consumable Name' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' /></td>
							<td><input type=text id='consumablequantity0' name='consumablequantity0' placeholder='Consumable Quantity' class=txtfield1 style='width:65px; font-size:12px; font-weight:bold;' /></td>
						</tr>
					</table>
					<input type=hidden id='consumableexistbefore0' name='consumableexistbefore0' />

				</div>
				<div style='' class=consumable1></div>
			</td>
		</tr>
		<tr><td>Hospitalisation 
		<input type=hidden id=hosp_show value='1' />
		<img class="view_hospitalization" src='../images/view.png' style='cursor:pointer; width:0px;' title='Hospitalisation View' /></td>
			<td class='hosp'>
				<div class=hospitalisation1>
					<table class=list-1>
						<tr>
							<td>Days: <?= $hospit['HORecordID']?"<input type=hidden name='hospitalisationexistbefore' value='{$hospit['HORecordID']}' />":"" ?> 
							<input type=text name=hospitalizationdays id=hospitalizationdays placeholder='Enter Days' class=txtfield1 style='width:90px; font-size:10px;' value='<?= @$hospit['Days'] ?>' />
							<input type=hidden id=hospitalizationtype placeholder='Enter Days' class=txtfield1 style='width:90px; font-size:10px;' value='' /><!--</td>
							<td>In: --><input type=hidden name=hospitalizationdatein id=datein placeholder='Enter Date In' class=txtfield1 style='width:90px; font-size:10px;' value='<?= @$hospit['StartDate'] ?>' /><!--</td>
							<td>Out: --><input type=hidden name=hospitalizationdateout id=dateout placeholder='Enter Date Out' class=txtfield1 style='width:90px; font-size:10px;' value='<?= @$hospit['EndDate'] ?>' />
							<?php
							//select all room category in the system
							$cons_list = returnAllData("SELECT ho_type.*, ho_price.HOPriceID FROM ho_type,ho_price WHERE ho_price.HOTypeID= ho_type.TypeID && ho_price.InsuranceCategoryID='{$insurance_category_id}' ORDER BY Name DESC",$con);
							if($cons_list){
								$i = 0;
								foreach($cons_list as $cons){
									echo "<label><input type=radio name=hospitalization_room_type value='{$cons['HOPriceID']}' ".(($cons['HOPriceID'] == @$hospit['HOPriceID'] || $i == 0)?"Checked":"").">{$cons['Name']}</label> ";
									?>
									<script>
										$("#hospitalizationtype").val("<?= $cons['HOPriceID']; ?>")
									</script>
									<?php
									$i++;
									break;
								}
								?>
								
								<?php
							} else{
								echo "<span class=error-text>You Can not Provide Any Hospitalization Service</span>";
							}
							?>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr><td style='width:5%'>Decision</td>
			<td>
				<div class=ambulance1>
					<table class=list-1>
						<tr>
							<td colspan=2>
								<input type=hidden id=decision_data value='0' />
								<label><input class="cons_cat" type=radio name=decision value='1' <?= @$patient['Status'] == 1?"checked":"" ?> onclick='$("#decision_data").val("0");' />Pas de Transfert</label>
								<label><input class="cons_cat" type=radio name=decision value='2' <?= @$patient['Status'] == 2?"checked":"" ?> onclick='$("#decision_data").val("50");' />Transfert</label>
								<label><input class="cons_cat" type=radio name=decision value='3' <?= @$patient['Status'] == 3?"checked":"" ?> onclick='$("#decision_data").val("50");' />Transfert avec Ambulance</label>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	
	<div class=bbb>
	<input type=submit id=save_data_ name=print_bill style='font-size:8px; display:none;' class=flatbtn value='Save' />
	<input type=submit id=save_data_and_print_ name=print_bill style='font-size:8px; display:none;' class=flatbtn value='Save & Print' /><span class='other_out'></span>
	</div>
	<input type=hidden name='print_bill' value='Save' />
	<input type=hidden name=save_and_print id=save_and_print_value value='0' />
</form>
	<div class=preview style='position:absolute; top:100px; right:2px; border:0px solid #000; border-radius:10px; padding:5px; font-size:12px; background-color:#fff; width:200px;'>
		<div class=progress>Preview</div>
		<input type=submit id=save_data name=print_bill style='font-size:8px; position:absolute; top:180px; right:15%;' class=flatbtn value='Save & Print' />
		<input type=submit id=simulate_data name="" style='font-size:8px; position:absolute; top:205px; right:15%;' class=flatbtn value='Check' />
		<div class=consultation_simulator></div>
		<div class=exam_simulator style='border:0px solid #0df;'></div>
		<div class=medicine_simulator style='border:0px solid #d0f;'></div>
		<div class=act_simulator></div>
		<div class=consumable_simulator></div>
		<div class=hospitalization_simulator></div>
		<div class=decision_simulator></div>
	</div>
	<input type=hidden id='insurance_category_id' value ='<?= $insurance_category_id ?>' />
<script>
	
	$(document).ready(function(){
		
		/************* NOW DISABLE SUBMIT BUTTON **********/
		//every test is passed then disable submit process now
		$("#disable_submit").val("1");
		//send acknowledgement to the user
		$("#save_data").css("background-color","#dfdfdf");
		$("#simulate_data").css("background-color","#dfdfdf");
		$("#save_data").css("color","#000000");
		$("#simulate_data").css("color","#000000");
		/******** SIMULATION CODE HERE ***************/
		$("#simulate_data").click(function(e){
			setTimeout(function(){
				
				runSimulation();
				
				
				
			}, 200);
		});
		/******** SIMULATION CODE HERE ***************/
		
		/************* NOW DISABLE SUBMIT BUTTON **********/
		
		$("#consumablequantity0").val("<?= @$emballage[0]['Quantity'] ?>");
		$("#consumableexistbefore0").val("<?= @$emballage[0]['ConsumableRecordID'] ?>");
		$(".view_hospitalization").click(function(){
			if($("#hosp_show").val() == 1){ 
				$(".hosp").show(); 
				$("#hosp_show").val("0") 
			} else {
				$(".hosp").hide(); 
				$("#hosp_show").val("1"); 
			}
		});
		
		//hide the hospitalisation interface now
		$(".view_hospitalization").click();
		//$(".view_hospitalization").hide();
		
		
		/* $("#ppr").load("./nopecime.php?wght=<?= @$patient['Weight']; ?>&diag=<?= str_replace(" ","%20",$dignostic_1['DiagnosticName']) ?>&diag_id=<?= str_replace(" ","%20",$dignostic_1['DiagnosticRecordID']) ?>");
		$("#ssr").load("./nopecime_sec.php?wght=<?= @$patient['Weight']; ?>&diag=<?= str_replace(" ","%20",$dignostic_2['DiagnosticName']) ?>&diag_id=<?= str_replace(" ","%20",$dignostic_2['DiagnosticRecordID']) ?>");
		 */
		$(".diag1").load("./adds-on/diag.php?wght=<?= @$patient['Weight']; ?>&age=<?= $age; ?><?= $diagnostic_all_data != null?"&code=4":"" ?>");
		$(".exam1").load("./adds-on/exam.php?wght=<?= @$patient['Weight']; ?>&number=1<?= $_exams_ != null?"&code=4":"" ?>&key=<?= $_GET['key'] ?>");
		$(".medecine1").load("./adds-on/medecine.php?number=1<?= $medecines != null?"&code=4":"" ?>");
		$(".act1").load("./adds-on/acts.php?number=1<?= $_acts_ != null?"&code=4":"" ?>");
		$(".consumable1").load("./adds-on/consumable.php?number=1<?= $_consumables_ != null?"&code=4":"" ?>");
		//delay for 5 seconds to enable simulation
		$(".progress").html("<img style='position:absolute; top:200px; right:10%;' src='../images/ajax_clock_small.gif' />");
		setTimeout(function(){
			enabled = true;
			//runSimulation();
			//$("#disable_submit").val("1");
			//send acknowledgement to the user
			
			$("#simulate_data").css("background-color","#6bb642");
			$("#simulate_data").css("color","#f3faef");
			$(".progress").html("");
		}, 1000);
		//hide the hospitalisation interface now
		//$(".hosp").hide();
		$("#consultation_date").keyup(function(e){
			$(".all_date").val($("#consultation_date").val());
		});
		var last_exam;
		var exam_name;
		
		/******************* TRY SIMULATION *********
		$(".cons_cat").click(function(e){
			setTimeout(function(){
				runSimulation();
			}, 200);
		}); 
		*********************************************/
		function runSimulationFromJS(){
			//console.log("Executed!");
			//start with the consultation record
			$(".consultation_simulator").load("./sim/cons.php?date=" + $("#consultation_date").val() + "&cons=" + $("#consultation_data").val());
			//simulate the exam submission
			//loop from 1 to the latest exam record found 
			var i=1; var text = ""; var small_wait=0;
			
			while(i<= $("#exam_counter").val()){
				
				//send the AJAX query to get the response text for concatenation
				if($("#exam_date" + i).val().trim() == "" || $("#examname" + i).val().trim() == "" || $("#examid" + i).val().trim() == "" || $("#examresult" + i).val().trim() == ""){
					i++;
					continue;
				}
				console.log("No Skipped!");
				$.ajax({
					type: "GET",
					url: "./sim/exam.php",
					data: "examdate=" + $("#exam_date" + i).val() + "&examname=" + $("#examname" + i).val() + "&examid=" + $("#examid" + i).val() + "&existing_id=" + $("#examexistbefore" + i).val() + "&insurance=<?= $insurance_category_id ?>" + "&url=ajax",
					cache: false,
					success: function(result){
						text = text + result;
						console.log(result);
					}
				});
				small_wait += 500;
				i++;
				//if all information are available now send the request
			}
			//wait a bit for exam query to finish();
			setTimeout(function(){
				$(".exam_simulator").html(text);
			}, small_wait);
		}
		/******************* END SIMULATION **************/
		
		$("#save_data").click(function(e){
			/* validate exams */
			//alert($("#disable_submit").val());
			
			last_exam = $("#exam_counter").val();
			i=1;
			while(i<=last_exam){
				//check if the examname is there
				exam_name = $("#examname" + i).val().trim();
				if(exam_name != ""){
					//check the exam number if is available
					if($("#examid" + i).val().trim() == ''){
						if(!confirm("Exam Number: " + i + " does not have The number. Press OK to confirm")){
							$("#examid" + i).focus();
							return e.preventDefault();
						} else{
							return e.preventDefault();
						}
					}
					//check the exam result if is available
					if($("#examresult" + i).val().trim() == ''){
						if(!confirm("Exam Number: " + i + " does not have The Result. Press OK to confirm")){
							$("#examresult" + i).focus();
							return e.preventDefault();
						} else{
							return e.preventDefault();
						}
					}
				}
				i++;
			}
			/* validate medicines*/
			last_exam = $("#med_counter").val();
			i=1;
			while(i<=last_exam){
				//check if the examname is there
				exam_name = $("#medecinename" + i).val().trim();
				if(exam_name != ""){
					//check the exam number if is available
					if($("#medecinequantity" + i).val().trim() == ''){
						if(!confirm("Medicine number " + i + " does not have quantity. Press OK to confirm")){
							$("#medecinequantity" + i).focus();
							return e.preventDefault();
						} else{
							return e.preventDefault();
						}
					}
					
					var pattern = /^coartem/;
					if(pattern.test($("#medecinename" + i).val() ) && $("#medecinequantity" + i).val()>2 ){
						$("#medecinequantity" + i).val("1");
					} 
				}
				i++;
			}
			/* validate consummables*/
			last_exam = $("#cons_counter").val();
			i=1;
			while(i<=last_exam){
				//check if the examname is there
				exam_name = $("#consumablename" + i).val().trim();
				if(exam_name != ""){
					//check the exam number if is available
					if($("#consumablequantity" + i).val().trim() == ''){
						if(!confirm("Consumable number " + i + " does not have quantity. Press OK to confirm")){
							$("#consumablequantity" + i).focus();
							return e.preventDefault();
						} else{
							return e.preventDefault();
						}
					}
				}
				i++;
			}
			$("#save_and_print_value").val("1");
			e.preventDefault();
			//check if the submit is disabled
			if($("#disable_submit").val() == "1"){
				alert("Click Check First to Test For Malaria")
				return e.preventDefault();
			}
			
			if($("#lock_malaria").val() != $("#lock_malaria_anti").val()){
				alert("Malaria Case Without Treatment\n OR \nTreatment Without Malaria Case.");
				return e.preventDefault();
			}
			
			//every test is passed then disable submit process now
			$("#disable_submit").val("1");
			//send acknowledgement to the user
			$(this).css("background-color","#dfdfdf");
			$(this).css("color","#000000");
			
			$(".frm_out").html('');
			$(".frm_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
			$("#frm_data").ajaxForm({ 
				target: '.frm_out'
			}).submit();
			//alert("Bill Completed!");
		});
		
		var done = false;
		$("#save_data_").click(function(e){
			$("#save_and_print_value").val("0");
			e.preventDefault();
			$(".frm_out").html('');
			$(".frm_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
			$("#frm_data").ajaxForm({ 
				target: '.frm_out'
			}).submit();
			
		});
		
		$("#save_data_and_print_").click(function(e){
			$("#save_and_print_value").val("1");
			e.preventDefault();
			$(".frm_out").html('');
			$(".frm_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
			$("#frm_data").ajaxForm({ 
				target: '.frm_out'
			}).submit();
			
		});
		
		$("#save_data_and_print").click(function(e){
			$("#save_and_print_value").val("1");
			e.preventDefault();
			$(".frm_out").html('');
			$(".frm_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
			$("#frm_data").ajaxForm({ 
				target: '.frm_out'
			}).submit();
		});
		
		$("#register_id").focus();
	});
</script>
	<?php
}
?>