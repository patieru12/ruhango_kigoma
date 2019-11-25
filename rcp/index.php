<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$_SESSION['user']['printerID'] = "2018200001";
header("location:./create_cbhi.php");
exit();
//var_dump($_POST);
$error = "";
//var_dump($_POST); echo "<br /><br />";
if(@$_POST['print_bill']){
	//save the consultation if not the second time it comes
	$cons_type = mysql_real_escape_string(trim($_POST['consultation']));
	
	$patientid = mysql_real_escape_string(trim($_POST['patientid'])); //get the document id
	//echo $patientid;
	//check if the patient has any consultation assigned to this document
	$consid = returnSingleField($sql="SELECT ConsultationRecordID from co_records WHERE PatientRecordID='{$patientid}'",$field="ConsultationRecordID",$data=true, $con);
	if(!$consid){
		//save the consultation record now
		$sql = "INSERT INTO co_records SET PatientRecordID='{$patientid}',
				Date=NOW(),
				ConsultationPriceID='".PDB($_POST['consultation'],true,$con)."',
				ConsultantID=''";
		//echo $sql;
		//save and return the consultation id
		$consid = saveAndReturnID($sql, $con);
	}
	//echo $consid;
	//save principal diagnostic if found
	if(@$_POST['principaldiagnostic']){
		//save the diagnostice if not saved
		$diag_id = returnSingleField($sql="SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($_POST['principaldiagnostic'],true,$con)."'",$field="DiagnosticID",$data=true, $con);
		if($diag_id && !returnSingleField($sql="SELECT DiagnosticID FROM co_diagnostic_records WHERE DiagnosticID='{$diag_id}' && ConsulationRecordID='{$consid}'",$field="DiagnosticID",$data=true, $con))
			saveData("INSERT INTO co_diagnostic_records SET ConsulationRecordID='{$consid}', DiagnosticID='{$diag_id}', DiagnosticType='1'", $con);
	}
	//save secondary diagnostic if found
	if(@$_POST['principaldiagnostic']){
		//save the diagnostice if not saved
		$diag_id = returnSingleField($sql="SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($_POST['principaldiagnostic'],true,$con)."'",$field="DiagnosticID",$data=true, $con);
		if($diag_id && !returnSingleField($sql="SELECT DiagnosticID FROM co_diagnostic_records WHERE DiagnosticID='{$diag_id}' && ConsulationRecordID='{$consid}'",$field="DiagnosticID",$data=true, $con))
			saveData("INSERT INTO co_diagnostic_records SET ConsulationRecordID='{$consid}', DiagnosticID='{$diag_id}', DiagnosticType='0'", $con);
	}
	
	//continue saving other information
	//echo $consid;
	//isolate every field type
	$exams = array(); $exam_counter=-1;
	$medecines = array(); $medecine_counter=-1;
	$consumables = array(); $consumable_counter=-1;
	$acts = array(); $act_counter=-1;
	//loop all element of the the post array searching for exams
	foreach($_POST as $key=>$value){
		if(preg_match("/^exam/",$key)){
			if(preg_match("/^examname/",$key)){
				$exam_counter++;
			}
			$exams[$exam_counter][$key] = $value;
		}
		if(preg_match("/^medecine/",$key)){
			if(preg_match("/^medecinename/",$key)){
				$medecine_counter++;
			}
			$medecines[$medecine_counter][$key] = $value;
		}
		if(preg_match("/^consumable/",$key)){
			if(preg_match("/^consumablename/",$key)){
				$consumable_counter++;
			}
			$consumables[$consumable_counter][$key] = $value;
		}
		if(preg_match("/^act/",$key)){
			if(preg_match("/^act/",$key)){
				$act_counter++;
			}
			$acts[$act_counter][$key] = $value;
		}
	}
	
	/******************** Simulation area *****************************************/
	$consumables[0]=array("consumablename1"=>"GANT STERILE (paire)","consumablequantity1"=>"1");
	//$exams[1]=$exams[0];
	//$exams[2]=$exams[0];
	//$acts[0]=array("act1"=>"Injection IV");
	/*$_POST['hospitalization_room_type'] = 1;
	$_POST['hospitalizationdays'] = 3;
	$_POST['ambulancelength'] = 3;
	$_POST['ambulancemedical_document'] = "on"; */
	/******************** End of Simulation area *****************************************/
	
	//var_dump($exams);echo "<br /><br /><br />";
	//var_dump($medecines);echo "<br /><br /><br />";
	//var_dump($consumables);echo "<br /><br /><br />";
	//var_dump($acts);echo "<br /><br /><br />";
	//die;
	//search for the patient insurance category id
	//echo $patientid;
	//select insurance name id
	$ins_name_id = returnSingleField($sql="SELECT InsuranceNameID FROM pa_records WHERE PatientRecordID='{$patientid}'",$field="InsuranceNameID",$data=true, $con);
	$ins_cat_id = returnSingleField($sql="SELECT CategoryID FROM in_name WHERE InsuranceNameID='{$ins_name_id}'",$field="CategoryID",$data=true, $con);
	echo $ins_cat_id; //die;
	//now save found exams and continue
	foreach($exams as $exam){
		//save one exam records
		$skip = false;
		//check data validity
		$sql = "INSERT INTO la_records SET ";
		$sql2 = "INSERT INTO la_result_record SET ";
		$f = 0; $examid=null; $ff = 0;
		foreach($exam as $key=>$value){
			if(preg_match("/^examresult/",$key)){
				if($ff++ != 0)
					$sql2 .= ", ";
			}
			else if($f++ != 0)
				$sql .= ", ";
			if(preg_match("/^examdate/",$key))
				$sql .= "ResultDate='{$value}'";
			elseif(preg_match("/^examresult/",$key)){
				//select the exam price id based on the found name
				$exam_id = returnSingleField("SELECT ResultID FROM la_result WHERE ResultName='{$value}'",$field="ResultID",$data=true, $con);
				//$price_id = returnSingleField("SELECT ExamPriceID FROM la_price WHERE ExamID='{$exam_id}' && InsuranceTypeID='{$ins_cat_id}'",$field="ExamPriceID",$data=true, $con);
				if(!$exam_id){
					$skip = true;
					break;
				}
				//$sql .= "ExamPriceID='{$price_id}'";
				$sql2 .= "ResultID='{$exam_id}'";
			}
			elseif(preg_match("/^examid/",$key)){
				$sql .= "ExamNumber='{$value}'";
				$examid = $value;
			}
			elseif(preg_match("/^examname/",$key)){
				//select the exam price id based on the found name
				$exam_id = returnSingleField("SELECT ExamID FROM la_exam WHERE ExamName='{$value}'",$field="ExamID",$data=true, $con);
				$price_id = returnSingleField("SELECT ExamPriceID FROM la_price WHERE ExamID='{$exam_id}' && InsuranceTypeID='{$ins_cat_id}'",$field="ExamPriceID",$data=true, $con);
				if(!$price_id){
					$skip = true;
					break;
				}
				$sql .= "ExamPriceID='{$price_id}'";
			}
		}
		if($skip || returnSingleField("SELECT ExamRecordID FROM la_records WHERE ExamNumber='{$examid}'",$field="ExamRecordID",$data=true, $con))
			continue;
		$sql .= ", ConsultationRecordID='{$consid}',
				ConsultantID=0,
				LabAgent=0";
		//save the new exam records now and continue;
		$examrecordid = saveAndReturnID($sql, $con);
		//saveData($sql, $con);
		//echo $sql."<br /><br />";
		$sql2 .= ", ExamRecordID='{$examrecordid}'";
		saveData($sql2, $con);
		//echo $sql2."<br /><br />";
	}
	//die;
	//now save found medecines and continue
	foreach($medecines as $exam){
		//save one exam records
		$skip = false;
		//check data validity
		$sql = "INSERT INTO md_records SET ";
		$sql2 = "INSERT INTO md_prescription SET ";
		$f = 0; $p = 0; $examid=null; $presc_found=false; $price_id=0; $qty=0;
		foreach($exam as $key=>$value){
			if(preg_match("/pres_/",$key)){
				if($p++ != 0)
					$sql2 .= ", ";
			}
			else if($f++ != 0)
				$sql .= ", ";
			if(preg_match("/^medecinepres_morng/",$key)){
				$sql2 .= "Morning='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinepres_noon/",$key)){
				$sql2 .= "Noon='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinepres_evening/",$key)){
				$sql2 .= "Evening='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinepres_night/",$key)){
				$sql2 .= "Midnight='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinespecial/",$key)){
				$sql .= "SpecialPrescription='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinequantity/",$key)){
				if($value >0 ){
					$sql .= "Quantity='{$value}'";
					$qty = $value;
				} else{
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^medecinename/",$key)){
				//select the exam price id based on the found name
				$exam_id = returnSingleField("SELECT MedecineNameID FROM md_name WHERE MedecineName='{$value}'",$field="MedecineNameID",$data=true, $con);
				$price_id = returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$exam_id}' && Status='1'",$field="MedecinePriceID",$data=true, $con);
				if(!$price_id){
					$skip = true;
					break;
				}
				$sql .= "MedecinePriceID='{$price_id}'";
			}
			
		}
		if($skip)
			continue;
		$sql .= ", ConsultationRecordID='{$consid}',
				ConsulatantID=0,
				Date=NOW()";
		/* //verify the if a medecine has the prescption found
		if(!$presc_found){
			continue;
		} */
		//echo $sql;
		//save the new medecine records now and continue;
		if(!$md_id = returnSingleField("SELECT MedecineRecordID FROM md_records WHERE MedecinePriceID='{$price_id}' && Quantity='{$qty}'",$field="MedecineRecordID",$data=true, $con))
			$md_id = saveAndReturnID($sql, $con);
		/* $sql2 .= ", MedecineRecordID='{$md_id}'";
		//echo $sql."<br /><br />";
		//save the medecine prescription because now no error in the query
		if($md_id && !returnSingleField("SELECT MedecineRecordID FROM  md_prescription WHERE MedecineRecordID='{$md_id}'",$field="MedecineRecordID",$data=true, $con))
			saveData($sql2, $con); //echo $sql2."<br /><br />"; */
	}
	//die;
	//now save found consumables and continue
	foreach($consumables as $exam){
		//save one exam records
		$skip = false;
		//check data validity
		$sql = "INSERT INTO cn_records SET ";
		
		$f = 0; $examid=null; $price_id=0; $qty=0;
		foreach($exam as $key=>$value){
			if($f++ != 0)
				$sql .= ", ";
			if(preg_match("/^consumablequantity/",$key)){
				if($value){
					$sql .= "Quantity='{$value}'";
					$qty = $value;
				} else{
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^consumablename/",$key)){
				//select the exam price id based on the found name
				$exam_id = returnSingleField("SELECT MedecineNameID FROM md_name WHERE MedecineName='{$value}'",$field="MedecineNameID",$data=true, $con);
				$price_id = returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$exam_id}' && Status='1'",$field="MedecinePriceID",$data=true, $con);
				if(!$price_id){
					$skip = true;
					break;
				}
				$sql .= "MedecinePriceID='{$price_id}'";
			}
			
		}
		if($skip)
			continue;
		$sql .= ", PatientRecordID='{$patientid}',
				Date=NOW()";
		
		//save the consumable found no problem in the request
		if($price_id && !returnSingleField("SELECT ConsumableRecordID FROM  cn_records WHERE MedecinePriceID='{$price_id}' && PatientRecordID='{$patientid}' && Date='".(date("Y-m-d",time()))."'",$field="ConsumableRecordID",$data=true, $con))
			saveData($sql, $con); //echo $sql."<br /><br />";
	}
	
	//now save found consumables and continue
	foreach($acts as $exam){
		//save one exam records
		$skip = false;
		//check data validity
		$sql = "INSERT INTO ac_records SET ";
		
		$f = 0; $examid=null; $price_id=0; $qty=0;
		foreach($exam as $key=>$value){
			if($f++ != 0)
				$sql .= ", ";
			if(preg_match("/^consumablequantity/",$key)){
				if($value){
					$sql .= "Quantity='{$value}'";
					$qty = $value;
				} else{
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^act/",$key)){
				//select the exam price id based on the found name
				$exam_id = returnSingleField("SELECT ActNameID FROM ac_name WHERE Name='{$value}'",$field="ActNameID",$data=true, $con);
				$price_id = returnSingleField("SELECT ActPriceID FROM ac_price WHERE ActNameID='{$exam_id}' && Status='1' && InsuranceCategoryID='{$ins_cat_id}'",$field="ActPriceID",$data=true, $con);
				if(!$price_id){
					$skip = true;
					break;
				}
				$sql .= "ActPriceID='{$price_id}'";
			}
			
		}
		/* if($skip)
			continue; */
		$sql .= ", PatientRecordID='{$patientid}',
				Date=NOW(),
				NurseID=0";
		
		//save the consumable found no problem in the request
		if($price_id && !returnSingleField("SELECT ActRecordID FROM  ac_records WHERE ActPriceID='{$price_id}' && PatientRecordID='{$patientid}' && Date='".(date("Y-m-d",time()))."'",$field="ActRecordID",$data=true, $con))
			saveData($sql, $con); //echo $sql."<br />{$price_id}<br />";
	}
	//echo ;
	//now try to save the hospitalization if available
	if(@$_POST['hospitalizationdays'] && is_numeric($_POST['hospitalizationdays']) && $_POST['hospitalizationdays']>0 && is_numeric($_POST['hospitalization_room_type'])){
		//now save hospitalization record 
		$sql = "INSERT INTO ho_record SET RecordID='{$patientid}', Days='{$_POST['hospitalizationdays']}', HOPriceID='{$_POST['hospitalization_room_type']}'";
		if(!returnSingleField("SELECT HORecordID FROM  ho_record WHERE RecordID='{$patientid}' && Days='{$_POST['hospitalizationdays']}' && HOPriceID='{$_POST['hospitalization_room_type']}'",$field="HORecordID",$data=true, $con))
			saveData($sql, $con); 
		echo $sql;
	}
	
	//now try to save the ambulance if available
	if(@$_POST['ambulancelength'] && is_numeric($_POST['ambulancelength']) && $_POST['ambulancelength']>0){
		//now save hospitalization record 
		$sql = "INSERT INTO am_records SET PatientRecordID='{$patientid}', Length='{$_POST['ambulancelength']}'";//" HOPriceID='{$_POST['hospitalization_room_type']}'";
		$sql2 = "SELECT AmbulanceRecordID from am_records WHERE PatientRecordID='{$patientid}' && Length='{$_POST['ambulancelength']}'";//" HOPriceID='{$_POST['hospitalization_room_type']}'";
		if(@$_POST['ambulancemedical_document'] == "on"){
			$sql .= ", MedicalDocument='{$_POST['ambulancemedical_document']}'";
			$sql2 .= " && MedicalDocument='{$_POST['ambulancemedical_document']}'";
			
		}
		if(@$_POST['ambulanceconsultation_document'] == "on"){
			$sql .= ", ConsultationDocument='{$_POST['ambulanceconsultation_document']}'";
			$sql2 .= " && ConsultationDocument='{$_POST['ambulanceconsultation_document']}'";
		}
		if(@$_POST['ambulanceordonance_document'] == "on"){
			$sql .= ", Ordonance='{$_POST['ambulanceordonance_document']}'";
			$sql2 .= " && Ordonance='{$_POST['ambulanceordonance_document']}'";
		}
		$sql .= ", Date=NOW()";
		$sql2 .= " && Date='".date("Y-m-d",time())."'";
		if(!returnSingleField($sql2,$field="AmbulanceRecordID",$data=true, $con))
			saveData($sql, $con); 
		//echo $sql2.$sql;
	}
	$error = "<span class=success-text>Bill Completed <a target='_blank' href='./print_bill.php?record={$patientid}'>Print</a></span>";
}

//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name ORDER BY InsuranceName ASC",$con),$multirows=true,$con);
$active = "index";
require_once"../lib2/cssmenu/rcp_header.html" ;
?>
  <script type="text/javascript">
  function save_request( tbl, fld, ref_val,ref_field){
	$.ajax({
		type: "POST",
		url: "./save_pa_request.php",
		data: "tbl=" + tbl + "&field=" + fld + "&val=" + $("#focus_now").val() + "&ref_field=" + ref_field + "&ref_val=" + ref_val + "&url=ajax",
		cache: false,
		success: function(result){
			$("#search").click();
			$("#edit_mode").val("0");
		}
	});
  }
  function edit_function(cl,ex_val,tbl,ref_val,ref_field, fld){
	  $("." + cl).html("<input id=focus_now onclick='' class=full onblur='save_request(\""+ tbl +"\",\""+ fld +"\",\""+ ref_val +"\",\""+ ref_field +"\");' type=text value='" + ex_val + "' />");
	  $("#focus_now").focus();
	  $("#edit_mode").val("1");
  }
  
  </script>
  <style>
	.full{
		width:98%;
	}
  </style>
  <div id="w">
    <div id="content" style='min-height:470px;'>
      <h1 style='margin-top:-50px'>COMPLETE RECEIVED ACTS [SEARCH BY DOCUMENT ID]</h1>
      <b>
	  <input type=hidden value=0 id=edit_mode />
	  <center>
	  <input type=text name=search_patient style='width:300px;' placeholder="Enter Document Number" <?php echo @$_GET['key']?"value='{$_GET['key']}'":"" ?> id=doc_search class=txtfield1 />
	  <a href="#loginmodal" class="flatbtn" id="search">Search</a></center>
	  
	  <?php echo $error; ?>
	  <div class="frm_out"></div>
	  <div class="doc_found">
	  
	  </div>
	  <div class="doc_selected">
	  
	  </div>
	  <div class="frm_out2"></div>
	  </b>
    </div>
  </div>
  <?php
  include_once "../footer.html";
  ?>
	<div class="apple_overlay" id="overlay">
	  <!-- the external content is loaded inside this tag -->
	  <div class="contentWrap"></div>
	</div>
  
  <?php
  //if the key get alelement is their searh automaticaly
  if(@$_GET['key'] && is_numeric($_GET['key'])){
	?>
	<script>
		$(document).ready(function(){
			$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
			
		});
	</script>
	<?php
  }
  ?>
  <!-- make all links with the 'rel' attribute open overlays -->
<script>
function receivePatient(id){
	$(".doc_selected").load("receive_patient.php?key=" + id );
}
$(document).ready(function(){
	$("#doc_search").focus();
	//if the search button is clicked search the patient_found
	$("#search").click(function(e){
		$(".doc_selected").html("");
		$(".doc_found").load("doc_patient.php?key="+$("#doc_search").val());
		return e.preventDefault();
	});
	$("#insurance").change(function(e){
		$(".patient_selected").html("");
		$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
	});
	$("#doc_search").keyup(function(e){
		$(".doc_selected").html("");
		$(".doc_found").load("doc_patient.php?key="+$("#doc_search").val());
		return e.preventDefault();
	});
});

$(function() {

    // if the function argument is given to overlay,
    // it is assumed to be the onBeforeLoad event listener
    $("a[rel]").overlay({

        mask: '#206095',
        effect: 'apple',
        onBeforeLoad: function() {

            // grab wrapper element inside content
            var wrap = this.getOverlay().find(".contentWrap");

            // load the page specified in the trigger
            wrap.load(this.getTrigger().attr("href"));
        }

    });
});
</script>

<script type="text/javascript">
$(function(){

	$("#username").keypress(function(e){
		$("#username").removeClass("error");
	});
	$("#password").keypress(function(e){
		$("#password").removeClass("error");
	});
  $('#loginform').submit(function(e){
	var username = $("#username").val();
	var password = $("#password").val();
	if(username == ""){
		$("#username").addClass("error");
		return e.preventDefault();
	}
	
	if(password == ""){
		$("#password").addClass("error");
		return e.preventDefault();
	}
	//submit the request using JQuery Ajax function
	$.ajax({
		type: "POST",
		url: "./login.php",
		data: "username=" + $("#username").val() + "&password=" + $("#password").val() + "&url=ajax",
		cache: false,
		success: function(result){
			$(".login_result").html(result);
		}
	});
    return e.preventDefault();
	
  });
  
  $('body').mousedown(function(e) {
	var clicked = $(e.target); // get the element clicked
	if (clicked.is('#overlay') || clicked.parents().is('#overlay')) {
		return; // click happened within the dialog, do nothing here
   } else { // click was outside the dialog, so close it
     //$('.overlay').hide();
	 //return false;
   }
});
});
</script>
<?php
if(@$_POST['rcv_patient']){
	?>
	<script>
		receivePatient("<?php echo @$_POST['patientid'] ?>","<?php echo @$_POST['insurance'] ?>");
	</script>
	<?php
}
?>
</body>
</html>