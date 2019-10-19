<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}


//select all patients related to the found key search
$patient = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.* from pa_records WHERE PatientRecordID='{$_GET['key']}'",$con),$multirows=false,$con);
//var_dump($patients);
//echo $sql;
$service = formatResultSet($rslt=returnResultSet($sql="SELECT se_records.*, se_name.ServiceCode from se_records, se_name WHERE se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID='{$patient['PatientRecordID']}'",$con),$multirows=false,$con);
//var_dump($_GET); die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name WHERE InsuranceNameID='{$patient['InsuranceNameID']}' ORDER BY InsuranceName ASC",$con),$multirows=true,$con);
if($patient){
//var_dump($patient);
//select the performed consultation on this records now
$insurance_category_id = returnSingleField($sql="SELECT CategoryID from in_name WHERE InsuranceNameID='{$patient['InsuranceNameID']}'",$field="CategoryID",$data=true, $con);
$cons2 = $cons = formatResultSet($rslt=returnResultSet($sql="SELECT co_category.*, co_price.ConsultationPriceID, co_records.* FROM co_category, co_price, se_consultation, co_records WHERE co_records.ConsultationPriceID = co_price.ConsultationPriceID && se_consultation.ConsulationID = co_category.ConsultationCategoryID && co_category.ConsultationCategoryID = co_price.ConsultationCategoryID && co_price.InsuranceCategoryID = '{$insurance_category_id}' && co_records.PatientRecordID='{$patient['PatientRecordID']}' ORDER BY ConsultationCategoryName ASC",$con),$multirows=false,$con);
//echo $sql;
$dignostic_1 = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic.*, co_diagnostic_records.* FROM co_diagnostic_records, co_diagnostic WHERE co_diagnostic.DiagnosticID = co_diagnostic_records.DiagnosticID && ConsulationRecordID='{$cons['ConsultationRecordID']}' && DiagnosticType='1'",$con),$multirows=false,$con);
$dignostic_2 = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic.*, co_diagnostic_records.* FROM co_diagnostic_records, co_diagnostic WHERE co_diagnostic.DiagnosticID = co_diagnostic_records.DiagnosticID && ConsulationRecordID='{$cons['ConsultationRecordID']}' && DiagnosticType='2'",$con),$multirows=false,$con);
//var_dump($dignostic_1);

//search for medicines prescription now
$medecines = formatResultSet($rslt=returnResultSet($sql="SELECT md_records.MedecineRecordID, md_records.Quantity, md_records.Date, md_name.* FROM md_records, md_price, md_name WHERE md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && ConsultationRecordID='{$cons['ConsultationRecordID']}' ",$con),$multirows=true,$con);
//var_dump($medecines);
//echo $sql;
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
			if($result_count++ > 0){
				$str_data .= "; ";
			}
			$str_data .= $r['ResultName'];
			if($_exams_[$k]['ResultType'] && $result_count == 1){
				$str_data .= "; ";
			}
		}
		$_exams_[$k]['Results'] = str_replace("+","_s",$str_data);
	}
}
//var_dump($medecines);
$_SESSION['exams'] = $_exams_;
//var_dump($_exams_);

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
//var_dump($medecines); die;
//echo $sql;


//search for hospitalisation information now
$hospit = formatResultSet($rslt=returnResultSet($sql="SELECT ho_record.* FROM ho_record WHERE ho_record.RecordID ='{$patient['PatientRecordID']}' ",$con),$multirows=false,$con);
//var_dump($hospit);
?>

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
<br />&nbsp;
<input type=hidden name=disable_submit id=disable_submit name=deisable_submit value='0' />
<form action="./save_from_cons_doc.php" method=post style='border:1px solid #efefef;' id="frm_data">
	<input type=hidden name=patientid value='<?php echo $patient['PatientRecordID'] ?>' />
	
	<div class=bb>
	<input type=submit id=save_data_and_print name=print_bill style='font-size:8px; display:none;' class=flatbtn value='Save' />
	<input type=submit id=save_data name=print_bill style='font-size:8px; position:absolute; top:180px; right:5%;' class=flatbtn value='Save & Print' />
	</div>
	<div style='height:1px; border:0px solid #000;'>
		<input type=text class=txtfield1 onblur='setTimeout("ds_hi()",500);' style='width:75px; font-size:12px; position:relative; top: -8px; left:102px;' name=consultation_date id=consultation_date onclick='ds_sh(this,"consultation_date")' value='<?= $patient['DateIn']?$patient['DateIn']:date("Y-m-d",time()) ?>' />
	</div>
	<table class=frm border=0>
		<tr><td style='width:180px;'>Consultation </td>
		<th>
			<!-- <input type=text id=cons_date_view />
			Register: --><input type='hidden' autocomplete="off" class=txtfield1 id=register_id name=register_id  style='width:90px; font-size:12px;' value='<?= @$cons['RegisterNumber'] ?>'/>
		<?php
			//var_dump($cons2);
			if(@$cons){
				echo "<input type=hidden name='consultationexistbefore' value='{$cons['ConsultationRecordID']}' />";
			}
			//echo $patient['InsuranceNameID'];
			//echo $insurance_category_id;
			//select all consultion that can be provided in the center
			$cons_list = returnAllData($s = "SELECT co_category.*, co_price.ConsultationPriceID FROM co_category, co_price, se_consultation WHERE se_consultation.ServiceID='{$service['ServiceNameID']}' && se_consultation.ConsulationID = co_category.ConsultationCategoryID && co_category.ConsultationCategoryID = co_price.ConsultationCategoryID && co_price.InsuranceCategoryID = '{$insurance_category_id}' && co_price.Status=1 ORDER BY ConsultationCategoryName ASC",$con);
			//echo $s;
			if($cons_list){
				if($cons_list[0]['ConsultationCategoryName'] == "invisible"){
					echo "<input type=hidden name=consultation value='{$cons_list[0]['ConsultationPriceID']}' />";
				} else{
					$i = 0;
					foreach($cons_list as $cons){
						if(preg_match("/CPN/",$cons['ConsultationCategoryName'])/*  == "CPN" */){
							echo "<input type=hidden name='consultation' value='{$cons['ConsultationPriceID']}' />";
						} else{
							continue;
						}
						//var_dump($cons['ConsultationCategoryName'] == "CPC Jour");
						//var_dump(( $cons2['ConsultationPriceID'] == $cons['ConsultationPriceID'] || (($i++) == 0 || $cons['ConsultationCategoryName'] == "CPC Jour")));
						$i++;
						//echo "<label><input type=radio ".(( $cons2['ConsultationPriceID'] == $cons['ConsultationPriceID'] || ($i) == 0 || $cons['ConsultationCategoryName'] == "CPC Jour")?"checked":"")." name=consultation value='{$cons['ConsultationPriceID']}'>".($cons['ConsultationCategoryName'] != 'invisible'?$cons['ConsultationCategoryName']:"No consultation")."</label> ";
					}
				}
			} else{
				echo "<span class=error-text>You Can not Provide Any Consultation Service to this type of Insurance</span>";
			}
		?>
		
		</th></tr><!--
		<tr><td>Diagnostic</td><td>
			<table class=list-1>
				
				<tr>
					<td colspan=2 class='_some_data_'>
					</td>
				</tr>
				<tr>
					<td id=ppr>Princ. <input type=text placeholder="Enter Principal Diagnostic" id=principaldiagnostic name=principaldiagnostic class='txtfield1' /></td>
					<td id=ssr>Sec. <input type=text placeholder="Enter Secondary Diagnostic" id=secondarydiagnostic name=secondarydiagnostic class=txtfield1 style='width:300px' /></td>
				</tr>
				<tr>
					<td colspan=2 class='_some_data'>
						<label><input type=radio required onclick='if($("#nopecime").prop("checked")){$("#ppr").load("./nopecime.php?wght=<?= @$patient['Weight']; ?>&diag=<?= str_replace(" ","%20",$dignostic_1['DiagnosticName']) ?>&diag_id=<?= str_replace(" ","%20",$dignostic_1['DiagnosticRecordID']) ?>");$("#ssr").load("./nopecime_sec.php?wght=<?= @$patient['Weight']; ?>&diag=<?= str_replace(" ","%20",$dignostic_2['DiagnosticName']) ?>&diag_id=<?= str_replace(" ","%20",$dignostic_2['DiagnosticRecordID']) ?>");}' id='nopecime' checked name=pecime value='0'>NO PECIME</label>
						<label><input type=radio required onclick='if($("#pecime").prop("checked")){$("#ppr").load("./pecime.php?wght=<?= @$patient['Weight']; ?>&diag=<?= str_replace(" ","%20",$dignostic_1['DiagnosticName']) ?>&diag_id=<?= str_replace(" ","%20",$dignostic_1['DiagnosticRecordID']) ?>");$("#ssr").load("./pecime_sec.php?wght=<?= @$patient['Weight']; ?>&diag=<?= str_replace(" ","%20",$dignostic_2['DiagnosticName']) ?>&diag_id=<?= str_replace(" ","%20",$dignostic_2['DiagnosticRecordID']) ?>");}' id='pecime' name=pecime value='1'>PECIME</label>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label><input checked type=radio name=case value='0'>NC</label> <label><input type=radio name=case value='1'>AC</label>
					</td>
				</tr>
			</table>
		</td></tr>-->
		<tr><td><input type=hidden id='exam_counter' />Exams</td>
			<td>
				&nbsp;
				<div class=exam1></div>
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

				</div><div style='' class=consumable1></div>
			</td>
		</tr>
		<tr><td>Hospitalisation 
		<input type=hidden id=hosp_show value='1' />
		<img class="view_hospitalization" src='../images/view.png' style='cursor:pointer;' title='Hospitalisation View' /></td>
			<td class='hosp'>
				<div class=hospitalisation1>
					<table class=list-1>
						<tr>
							<td>Days: <?= $hospit['HORecordID']?"<input type=hidden name='hospitalisationexistbefore' value='{$hospit['HORecordID']}' />":"" ?> <input type=text name=hospitalizationdays placeholder='Enter Days' class=txtfield1 style='width:90px; font-size:10px;' value='<?= @$hospit['Days'] ?>' /><!--</td>
							<td>In: --><input type=hidden name=hospitalizationdatein id=datein placeholder='Enter Date In' class=txtfield1 style='width:90px; font-size:10px;' value='<?= @$hospit['StartDate'] ?>' /><!--</td>
							<td>Out: --><input type=hidden name=hospitalizationdateout id=dateout placeholder='Enter Date Out' class=txtfield1 style='width:90px; font-size:10px;' value='<?= @$hospit['EndDate'] ?>' />
							<?php
							//select all room category in the system
							$cons_list = returnAllData("SELECT ho_type.*, ho_price.HOPriceID FROM ho_type,ho_price WHERE ho_price.HOTypeID= ho_type.TypeID && ho_price.InsuranceCategoryID='{$insurance_category_id}' ORDER BY Name DESC",$con);
							if($cons_list){
								$i = 0;
								foreach($cons_list as $cons){
									echo "<label><input type=radio name=hospitalization_room_type value='{$cons['HOPriceID']}' ".(($cons['HOPriceID'] == @$hospit['HOPriceID'] || $i == 0)?"Checked":"").">{$cons['Name']}</label> ";
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
						<tr>
							<td colspan=3>
							<?php
							//select all room category in the system
							/* $cons_list = returnAllData("SELECT ho_type.*, ho_price.HOPriceID FROM ho_type,ho_price WHERE ho_price.HOTypeID= ho_type.TypeID && ho_price.InsuranceCategoryID='{$insurance_category_id}' ORDER BY Name DESC",$con);
							if($cons_list){
								$i = 0;
								foreach($cons_list as $cons){
									echo "<label><input type=radio name=hospitalization_room_type value='{$cons['HOPriceID']}' ".(($cons['HOPriceID'] == @$hospit['HOPriceID'] || $i++ == 0)?"Checked":"").">{$cons['Name']}</label> ";
								}
								?>
								
								<?php
							} else{
								echo "<span class=error-text>You Can not Provide Any Hospitalization Service</span>";
							} */
							?>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr><td style='width:5%'>Decision</td>
			<td>
				<!--<input type=text class=txtfield1 onblur='setTimeout("ds_hi()",500);' style='width:75px; font-size:12px; position:relative; top: 2px; left:-80px;' name=rv_date id=rv_date onclick='ds_sh(this,"rc_date")' value='<?= $patient['DateIn']?(date("Y-m-d",(mktime(0,0,0, explode("-",$patient['DateIn'])[1], explode("-",$patient['DateIn'])[2], explode("-",$patient['DateIn'])[0]) + (60*60*24*2)))):date("Y-m-d",(time() + (60*60*24*2))) ?>' />-->
				<div class=ambulance1>
					<table class=list-1>
						<tr>
							<td colspan=2>
								
								<label><input type=radio checked name=decision value="1" />Pas de Transfert</label>
								<label><input type=radio <?= $patient['Status'] == "2"?"checked":"" ?> name=decision value='2' />Transfert</label>
								<label><input type=radio <?= $patient['Status'] == "3"?"checked":"" ?> name=decision value='3' />Transfert avec Ambulance</label>
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
	
<script>
	
	$(document).ready(function(){
		/* $(".view_hospitalization").click(function(){
			if($("#hosp_show").val() == 1){ 
				$(".hosp").show(); 
				$("#hosp_show").val("0") 
			} else {
				$(".hosp").hide(); 
				$("#hosp_show").val("1"); 
			}
		}); */
		
		//hide the hospitalisation interface now
		//$(".hosp").hide();
		<?php
		/* if($hospit != null){
			?>
			$(".view_hospitalization").click();
			//alert('After Clicking!');
			<?php
		} */
		?>
		//move some
		/* $("._some_data_").html($("._some_data").html());
		$("._some_data").html(""); */
		//$("#ppr").load("./nopecime.php?wght=<?= @$patient['Weight']; ?>&diag=<?= str_replace(" ","%20",$dignostic_1['DiagnosticName']) ?>&diag_id=<?= str_replace(" ","%20",$dignostic_1['DiagnosticRecordID']) ?>");
		//$("#ssr").load("./nopecime_sec.php?wght=<?= @$patient['Weight']; ?>&diag=<?= str_replace(" ","%20",$dignostic_2['DiagnosticName']) ?>&diag_id=<?= str_replace(" ","%20",$dignostic_2['DiagnosticRecordID']) ?>");
		$(".exam1").load("./adds-on/exam_cpn.php?wght=<?= @$patient['Weight']; ?>&number=1<?= $_exams_ != null?"&code=4":"" ?>&key=<?= $_GET['key'] ?>");
		$(".medecine1").load("./adds-on/medecine.php?number=1<?= $medecines != null?"&code=4":"&code=30" ?>");
		
		$(".act1").load("./adds-on/acts.php?number=1<?= $_acts_ != null?"&code=4":"" ?>");
		
		$(".consumable1").load("./adds-on/consumable.php?number=1<?= $_consumables_ != null?"&code=4":"" ?>");
		
		$("#consumablequantity0").val("<?= @$emballage[0]['Quantity'] ?>");
		$("#consumableexistbefore0").val("<?= @$emballage[0]['ConsumableRecordID'] ?>");
		
		//hide the hospitalisation interface now
		//$(".hosp").hide();
		$("#consultation_date").keyup(function(e){
			$(".all_date").val($("#consultation_date").val());
		});
		
		var last_exam;
		var exam_name;
		$("#save_data").click(function(e){
			/* validate exams */
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
						}
					}
					//check the exam result if is available
					if($("#examresult" + i).val().trim() == ''){
						if(!confirm("Exam Number: " + i + " does not have The Result. Press OK to confirm")){
							$("#examresult" + i).focus();
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
						}
					}
					
					var pattern = /^coartem/;
					if(pattern.test($("#medecinename" + i).val() )){
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
						}
					}
				}
				i++;
			}
			$("#save_and_print_value").val("1");
			e.preventDefault();
			//check if the submit is disabled
			if($("#disable_submit").val() == "1"){
				alert("To re-enable submit click fill icon.")
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