<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	// unset($_SESSION['user']['ServiceID']);
	if(!isset($_SESSION['user']['ServiceID'])){
		echo "<script>window.location='../cst/se_select.php?msg=Please select the service and register tobe used';</script>";
		return;
	}
	$error = "";
	
$active = "patient";
require_once "../lib2/cssmenu/cst_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='text-align: center; margin-top:-55px;'>Consultation Dashboard</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table border="0" cellpadding="0" cellspacing="0" style='font-size:14px; width: 100%'>
    		<tr>
    			<!-- Left container -->
				<td style="width: 20%;">
					<h3>All Patient</h3>
					<input type=text name=search autocomplete=off style='width:100%' id=search placeholder="Filter Patient" />
					<span id=bar style='position:relative; top:-20px; left:93%; height:0px;'>&nbsp;</span>
					<!-- <div style="text-align:center; vertical-align:middle; display: table-cell; position:absolute; top:15%; left:0%;border:1px solid blue; height:50px; background-size: 100%; width: 50px; background-repeat: no-repeat; background-image: url('../images/label_blank_blue.png');">
						0
					</div> -->
					<div class=all_patient style='height:400px; width:350px; overflow:auto;'>
						<script>
							//send the request to display lowest stock status
							var firstNumber = 0;
							var requestCompleted = true;
							var noRetry = false;
							$(".all_patient").load("./patient-list.php");
						</script>
					</div>
					<div class="newNotifications" style="border:0px solid green; padding:10px;">
						
					</div>
					<!-- <hr />
					<h3>Requested Exams</h3>
					<input type=text name=search autocomplete=off style='width:100%' id=searchExam placeholder="Filter Exam Result" />
					<span id=bar style='position:relative; top:-20px; left:93%; height:0px;'>&nbsp;</span>
					<div class=all_result style='height:200px; width:350px; overflow:auto;'>
						<script>
							//send the request to display lowest stock status
							// $(".all_result").load("./patient-list-exam.php");
						</script>
					</div> -->
				</td>
				<td style="border-left: 1px solid #000;" id="mainInput">
					<h2>Patient Identification</h2>
					<div id="patientIdentification" style="min-height: 85px;">
						<!-- Here is the Patient Identification -->
						<span class="error-text">Please Select a Patient to view the full address</span>
					</div>
					<input type="hidden" name="" id="patientIDRecords" />
				<div style=" max-height: 435px; overflow: auto;">
					<?= $error; ?>
					<h2>Consultation Summary</h2>
					<div id="patientConsultation">
						<!-- Here is the List of Recorded Diagnostic -->
					</div>
					<div>
						<!-- Form for new Diagnostic -->
						<form>
							<!-- <input type="text" id="consultationData" class="txtfield1" name="" placeholder="Enter New Diagnostic" /> -->
							<textarea style="width: 85%;" id="consultationDataToSave" rows="2" placeholder="Enter Consultation Summary Here"></textarea>
							<button type="button" id="saveConsultationUpdate" class="flatbtn-blu editPatientRecord" style="margin-top: -20px;">Add New</button>
						</form>
					</div>
					<hr />
					
					<h2>Exams</h2>
					<div>
						<!-- Here is the List of Recorded Diagnostic -->
						<table border="1" style="width: 100%">
							<thead>
								<tr><th style="width: 40px;">ID</th><th style="width: 50%;">Name</th><th>Result</th><th style="width: 80px;"></th></tr>
							</thead>
							<tbody id="requestedExams">
								<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="4" style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;">
										<input type="hidden" name="examRecorID" id="examRecorID">
										<input type="text" id="requestExam" class="txtfield1" name=""> &nbsp;
										<span id="tdrResult"></span>
										<button id="requestExamBtn" class="flatbtn-blu editPatientRecord">Add New</button>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<hr />
					<h2>Diagnostic</h2>
					<div id="patientDiagnostic">
						<!-- Here is the List of Recorded Diagnostic -->
					</div>
					<div>
						<!-- Form for new Diagnostic -->
						<form>
							<input type="text" id="diagnosticData" class="txtfield1" style="width: 60%" name="" placeholder="Enter New Diagnostic" />
							<label><input type="radio" name="diagEpisode" class="caseEpisode" value="0" id="newCaseEpisode">New Case</label>&nbsp;&nbsp;
							<label><input type="radio" name="diagEpisode" class="caseEpisode" value="1" id="oldCaseEpisode">Old Case</label>&nbsp;&nbsp;
							<br />
							<button type="button" id="saveDiagnosticUpdate" class="flatbtn-blu editPatientRecord">Add New</button> <input type="hidden" name="DiagnosticRecordID" id="DiagnosticRecordID">
						</form>
					</div>
					<hr />

					<h2>Medicines</h2>
					<div>
						<!-- Here is the List of Recorded Diagnostic -->
						<table border="1" style="width: 100%" style="font-size:13px; ">
							<thead>
								<tr><th style="width:20%;">Medicine</th><th style="width:30%;">Prescription</th><th>Dose Total</th><th></th></tr>
							</thead>
							<tbody id="requestedMedicines">
								<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>
							</tbody>
							<tfoot>
								<tr>
									<td style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;">
										<table>
											<tr>
												<td style="font-size: 12px;">Medicine Name <span onclick="noRetry = false; $('#requestMedicines').val('');" style="color:blue; text-decoration: none; cursor: pointer; font-size: 11px;">reset</span><span id=barEdit style='position:relative; top:24px; left:100%; height:0px;'>&nbsp;</span></td>
											</tr>
											<tr>
												<td>
													<input id="requestMedicines" type="text" class="txtfield1" name="">
													<input type="hidden" name="MedecineRecordID" id="MedecineRecordID">
												</td>
											</tr>
										</table>	
									</td>
									<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;">
										<table>
											<tr>
												<td style="font-size: 12px;">Qty/prise</td>
												<td style="font-size: 12px;"># de fois</td>
												<td style="font-size: 12px;"># de jours</td>
											</tr>
											<tr>
												<td><input id="dosage" type="text" class="txtfield1" name="" style="width: 100px;"></td>
												<td><input id="cnDay" type="text" class="txtfield1" name="" style="width: 100px;"> </td>
												<td><input id="nbrDays" type="text" class="txtfield1" name="" style="width: 100px;"> </td>
											</tr>
										</table>
										
									</th>
									<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;">
										<table>
											<tr>
												<td style="font-size: 12px;">Dose Total</td>
											</tr>
											<tr>
												<td>
													<input id="requestMedicinesQty" type="text" class="txtfield1" name="">
												</td>
											</tr>
										</table>
									</th>
									<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;">
										<table>
											<tr>
												<td style="font-size: 12px;">&nbsp;</td>
											</tr>
											<tr>
												<td>
													<button id="requestMedicinesBtn" class="flatbtn-blu editPatientRecord">Add Medicine</button>
												</td>
											</tr>
										</table>
									</th>
								</tr>
								<tr>
									<td style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;">
										<div id="stockAlert" style="font-size: 12px;"></div>
									</td>
									<td colspan="2"  style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;">
										<div style="text-align: right; font-size: 12px;">
											Prescription: <input id="requestMedicinesInstruction" type="text" class="txtfield1" name="" style="width: 350px;">
										</div>
									</td>
									<td style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;">
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<hr />

					<h2>Performed Acts</h2>
					<div>
						<!-- Here is the List of Recorded Diagnostic -->
						<table border="1" style="width: 100%">
							<thead>
								<tr><th>Act</th><th>Quantity</th><th></th></tr>
							</thead>
							<tbody id="requestedActs">
								<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>
							</tbody>
							<tfoot>
								<tr>
									<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><input id="requestActs" placeholder="Act Name" type="text" class="txtfield1" name=""> <input type="hidden" name="ActRecordID" id="ActRecordID"> </th>
									<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><input id="requestActsQty" placeholder="Quantity" type="text" class="txtfield1" name="" value="1"> </th>
									<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><button id="requestActsBtn" class="flatbtn-blu editPatientRecord">Add Act</button></th>
								</tr>
							</tfoot>
						</table>
					</div>
					<hr />

					<h2>Consumables</h2>
					<div>
						<!-- Here is the List of Recorded Diagnostic -->
						<table border="1" style="width: 100%">
							<thead>
								<tr><th>Consumable</th><th>Quantity</th><th></th></tr>
							</thead>
							<tbody id="requestedConsumables">
								<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>
							</tbody>
							<tfoot>
								<tr>
									<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><input id="requestConsumables" placeholder="Consumable Name" type="text" class="txtfield1" name=""> <input type="hidden" name="ConsumableRecordID" id="ConsumableRecordID"> </th>
									<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><input id="requestConsumablesQty" placeholder="Consumable Quantity" type="text" class="txtfield1" name=""> </th>
									<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><button id="requestConsumablesBtn" class="flatbtn-blu editPatientRecord">Add Consumable</button></th>
								</tr>
							</tfoot>
						</table>
					</div>
					
				</div>
				<h2>Hosptalisation Service</h2>
				<div>
					<!-- Here is the List of Recorded Diagnostic -->
					<table border="1" style="width: 100%">
						<thead>
							<tr><th>Type</th><th>Entry Date</th><th>Out Date</th><th>Number of Days</th><th>&nbsp;</th></tr>
						</thead>
						<tbody id="requestedHostpitalization">
							<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>
						</tbody>
						<tfoot>
							<tr>
								<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><input id="requestType" placeholder="Select Hospitalization Room Type" type="text" class="txtfield1" name=""><!--<button id="requestHospitalizationIn" class="flatbtn">Get In</button>--> </th>
								<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><input id="requestDateIn" onclick="ds_sh(this,'requestDateIn');" placeholder="Select Hospitalization Date In" type="text" class="txtfield1" name=""><!--<button id="requestHospitalizationIn" class="flatbtn">Get In</button>--> </th>
								<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><input id="requestDateOut" onclick="ds_sh(this,'requestDateOut');" placeholder="Select Hospitalization Date Out" type="text" class="txtfield1" name=""></th>
								<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"><input type="hidden" id="HORecordID" name=""><button id="requestHospitalizationOut" class="flatbtn-blu editPatientRecord">Add Hosp</button></th>
								<th style="border:1px solid #fff; border-top:1px solid #000; padding-top: 5px;"></th>
							</tr>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id="requestedDecision" style="margin-top: 15px;">
				</div>
				<a href="" rel="#overlay" id="preview_history" ></a>
				<a href="" rel="#overlay2" id="transfer_patient" ></a>
				<button class="flatbtn" onclick="checkHistory();">History</button>
				<button class="flatbtn editPatientRecord" onclick="transfer(2)">Transfer</button>
				<button class="flatbtn editPatientRecord" onclick="transfer(3)">Transfer With Ambulance</button> 
				<button class="flatbtn editPatientRecord" onclick="checkTrasfer();">Internal Transfer</button>
				
    			</td>
    		</tr>
    	</table>
    	</b>
	</div>
</div>

  <?php
  include_once "../footer.html";
  ?>
  	<div class="apple_overlay_custom1" id="overlay">
	  	<!-- the external content is loaded inside this tag -->
	  	<div class="contentWrap"></div>
	</div>
  	<div class="apple_overlay" id="overlay2">
	  	<!-- the external content is loaded inside this tag -->
	  	<div class="contentWrap"></div>
	</div>
</body>
</html>
<style type="text/css">
	table td{
		font-size: 13px;
	}
	table th{
		font-size: 14px;
	}
	h2{
		background-color: #77bb56;
		font-size: 14px;
		text-align: center;
		text-transform: uppercase;
		color: #fff;
		font-weight: bold;
	}
</style>

<script>
	var consultationID;
	function regulateStock(stock_id, position_){
		var new_value = prompt("Enter New Stock Value",0);
		//$("#nn" + position_).html(new_value);
		$.ajax({
			type: "POST",
			url: "./regulate.php",
			data: "stock_id=" + stock_id + "&old_value=" + $("#nn" + position_).html() + "&new_value=" + new_value + "&position=" + position_ + "&url=ajax",
			cache: false,
			success: function(result){
				$("#nn" + position_).html(result)
				//console.log(result);
			},
			error: function(err){
				console.log(err.responseText());
			}
		});
	}
	var temporisationStarted = false;
	var TDR_Found = false;
	$("#requestExam").keyup(function(e){
		// Here try to check if the exam is TDR and request for the Result now for better saving
		/*if(!temporisationStarted){
			temporisationStarted = true;
			$("#tdrResult").html("checking TDR");
			setTimeout(function(e){
				var examName = $("#requestExam").val();
				if(examName == "TDR" || examName == "tdr"){
					TDR_Found = true;
					// alert("We found TDR Please request for result to save the new exam");
					$("#tdrResult").html("<input type='text' placeholder='Enter TDR Result Here' class='txtfield1' name='tdrResult' id='tdrNewResult' />");
					setTimeout(function(e){
						$("#tdrNewResult").autocomplete("./auto/exam_result.php?examName=" + examName, {
							selectFirst: true
						});
					}, 50);
				} else{
					$("#tdrResult").html("");
				}
				temporisationStarted = false;
			}, 2000);
		}*/
	});

	function checkHistory(){
		// Check if the Patietn is selected
		var patientID = $("#patientIDRecords").val().trim();
		if(!patientID){
			alert("No Previous Record!");
		} else{
			$("#preview_history").prop("href", "./patient-history.php?records=" + patientID);
			setTimeout(function(e){
				$("#preview_history")[0].click();
			}, 20);
		}
	}

	function checkTrasfer(){
		// Check if the Patietn is selected
		var patientID = $("#patientIDRecords").val().trim();
		if(!patientID){
			alert("Please select a patient to be transfered");
		} else{
			$("#transfer_patient").prop("href", "./patient-transfer.php?records=" + patientID);
			setTimeout(function(e){
				$("#transfer_patient")[0].click();
			}, 20);
		}
	}

	function transfer(level){
		var patientID = $("#patientIDRecords").val().trim();
		if(!patientID){
			alert("Please Select a Patient");
		} else{
			$.ajax({
				type: "POST",
				url: "./pa/save-transfer.php",
				data: "consultationID=" + consultationID + "&status=" + level + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedDecision").html(result)
					
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
		}
	}

	function patientVisit(value, field){
		var patientID = $("#patientIDRecords").val().trim();
		if(!patientID){
			alert("Please Select a Patient");
		} else{
			$.ajax({
				type: "POST",
				url: "./pa/save-logs.php",
				data: "patientID=" + patientID + "&field=" + field + "&value=" + value + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedLogs").html(result)
					
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
		}
	}

	$(document).ready(function(){
		var query_sent = false;
		$("#search").focus();
		
		$("#search").keyup(function(){
			$("#bar").html("<img src='../images/ajax_clock_small.gif' />");
			//now try send the filter query
			if(query_sent == false){
				query_sent = true;
				setTimeout(function(){
					if($("#search").val().trim != ""){
						$(".all_patient").load("./patient-list.php?response=html&filter=true&keyword=" + $("#search").val().replace(/ /g,"%20"));
						$("#bar").html("");
					}
					query_sent = false;
				},1000);
			}
		});

		$("#diagnosticData").autocomplete("./auto/diag.php", {
			selectFirst: true
		});

		$("#requestExam").autocomplete("./auto/exam.php", {
			selectFirst: true
		});

		$("#requestMedicines").autocomplete("./auto/medecine.php",{
			selectFirst: true
		});

		$("#requestActs").autocomplete("./auto/act.php", {
			selectFirst: true
		});

		$("#requestConsumables").autocomplete("./auto/materials.php", {
			selectFirst: true
		}); 
		$("#requestType").autocomplete("./auto/hosp.php", {
			selectFirst: true
		}); 

		$("#dosage").keydown(function(e){
			if(e.which == 13){
				return false;
			}
		});

		$("#cnDay").keydown(function(e){
			if(e.which == 13 ){
				return false;
			}
		});

		$("#nbrDays").keydown(function(e){
			if(e.which == 13 ){
				return false;
			}
		});

		var smallUnit = null;
		var smallUnitMesure = "";
		var mesurePattern = "";
		var usableQty = null;
		var calculateTotal = true;
		// var specialData = "";

		$("#dosage").focus(function(e){
			if(noRetry){
				return;
			}
			// console.log(specialData);
			setTimeout(function(){
				var mdname = $("#requestMedicines").val();
				if(!mdname){
					$("#requestMedicines").focus();
					alert("Which Medicine are you prescribing?\nPlease fill in the Medicine name first");

					return;
				}
				$("#dosage").prop("disabled", ":true");
				$("#dosage").val("please wait...");

				var url = "./getSmallUnit.php?medicinename=" + mdname;
				requestCompleted = false;
				$.getJSON( url, function(data) {
					smallUnit = data.smUnit;
					smallUnitMesure = data.smMesure;
					mesurePattern = "/" + smallUnitMesure + "/";

					if(data.stockLevel <= data.criticalLevel){
						$("#stockAlert").html("<span class='error-text'>" + $("#requestMedicines").val() + " Stock Value:" + data.stockLevel + "<br />Ne Prescrit pas ce medicament.</span>");
						$("#requestMedicines").val("");
					} else if(data.stockLevel <= data.lowLevel){
						$("#stockAlert").html("<span class='error-text'>" + $("#requestMedicines").val() + " Stock Value:" + data.stockLevel + "</span>");
					} else{
						$("#stockAlert").html($("#requestMedicines").val() + " Stock Value:" + data.stockLevel);
					}
				}).done(function(){
					$("#dosage").removeProp("disabled");
					$("#dosage").val("");
					noRetry = true;
					$("#dosage").focus();
					requestCompleted = true;
					/*if(specialData.trim()){
						$("#dosage").val("");
						setTimeout(function(e){
							$("#cnDay").focus();
						},500);
					} else {*/
						
					// }
				});
			}, 50);
		});

		$("#dosage").blur(function(e){
			var dosage = $("#dosage").val();
			var usableUnit = dosage.replace(smallUnitMesure, "").trim();
			// console.log("Unit Before " + usableUnit);
			var numberPattern = /\d$/;
			if(usableUnit.match(numberPattern)){
				var unitRounding = usableUnit/smallUnit;
				var unitRounded = unitRounding.toFixed(1);
				
				usableQty = unitRounded;
				// console.log("Qty " + usableQty);
				// console.log("Unit " + usableUnit);
				calculateTotal = true;
			} else{
				// alert("The Prescription does not match the Medicine Dosage!\nPlease Check the Medicine name and Prescription\n" + usableUnit);
				// $("#dosage").val("");
				calculateTotal = false;
			}
		});
		// Activate total Autocalculation
		$("#cnDay").keyup(function(e){
			var cnDay = $(this).val();

			var InstructionData = "";
			InstructionData = $("#dosage").val() + " "+ $("#cnDay").val() + " fois par jr/pendant " + $("#nbrDays").val() + " jrs";
			$("#requestMedicinesInstruction").val(InstructionData);

			if(!calculateTotal){
				return;
			}

			var totalQty = usableQty * cnDay*1;
			var numberOfDays = $("#nbrDays").val();
			if(numberOfDays > 0){
				totalQty *= numberOfDays;
			}

			var siropPattern = /sirop/;
			var coartemPattern = /coartem/;
			var requestMedicines = $("#requestMedicines").val().trim();

			if(requestMedicines.match(coartemPattern)){
				$("#requestMedicinesQty").val("1");
			} else if(requestMedicines.match(siropPattern)){
				$("#requestMedicinesQty").val("1");
			} else{
				$("#requestMedicinesQty").val(totalQty);
			}
		});

		// Activate total Autocalculation
		$("#nbrDays").keyup(function(e){
			var nbrDays = $(this).val();
			var InstructionData = "";
			InstructionData = $("#dosage").val() + " " + $("#cnDay").val() + " fois par jr/pendant " + $("#nbrDays").val() + " jrs";
			$("#requestMedicinesInstruction").val(InstructionData);

			if(!calculateTotal){
				return;
			}
			var totalQty = usableQty * nbrDays*1;
			var cnDay = $("#cnDay").val();
			// alert("Daily Consumtion is not valid!");

			if(cnDay > 0){
				totalQty *= cnDay;
			} else{
				alert("Daily Consumtion is not valid!");
			}

			// alert( (totalQty%1) + ":" + (totalQty/1) );
			var rounding = totalQty/1;
			var rounded = rounding.toFixed(0);
			if(rounding > rounded){
				rounded++;
			} 
			// alert(rounded);
			
			var siropPattern = /sirop/;
			var coartemPattern = /coartem/;
			var requestMedicines = $("#requestMedicines").val().trim();

			if(requestMedicines.match(coartemPattern)){
				$("#requestMedicinesQty").val("1");
			} else if(requestMedicines.match(siropPattern)){
				$("#requestMedicinesQty").val("1");
			} else{
				$("#requestMedicinesQty").val(rounded);
			}

		});
	});

	$("#requestHospitalizationOut").click(function(e){
		var requestHospType = $("#requestType").val().trim();
		var requestDateIn 	= $("#requestDateIn").val().trim();
		var requestDateOut 	= $("#requestDateOut").val().trim();
		if(!requestHospType){
			alert("Please fill in the room Type!");
			return;
		}
		if(!requestDateIn){
			alert("Please fill in the entry date!");
			return;
		}

		if(!requestDateOut){
			alert("Please fill in the out date!");
			return;
		}
		if(!consultationID){
			alert("Please!\nSelect a patient to be hospitalized!\nThanks.");
			return;
		}
		$.ajax({
				type: "POST",
				url: "./pa/save-ho.php",
				data: "consultationID=" + consultationID + "&hotype=" + requestHospType + "&dateIn=" + requestDateIn + "&dateOut=" + requestDateOut + "&operation=in&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedHostpitalization").html(result);
					$("#requestType").val("");
					$("#requestDateIn").val("");
					$("#requestDateOut").val("");
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
	});

	$("#requestHospitalizationIn").click(function(e){
		$.ajax({
				type: "POST",
				url: "./pa/save-ho.php",
				data: "consultationID=" + consultationID + "&operation=in&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedHostpitalization").html(result)
					
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
	});

	$("#requestConsumablesBtn").click(function(e){
		// Here Submit medicine request
		var requestConsumables 		= $("#requestConsumables").val().trim();
		var requestConsumablesQty 	= $("#requestConsumablesQty").val().trim();
		var ConsumableRecordID 		= $("#ConsumableRecordID").val().trim();

		if(!requestConsumables){
			alert("No Consumable Name is Specified!")
		} else if(!requestConsumablesQty){
			alert("No Consumable Quantity is Specified!");
		} else{
			$.ajax({
				type: "POST",
				url: "./pa/save-cn.php",
				data: "consultationID=" + consultationID + "&ConsumableRecordID=" + ConsumableRecordID + "&cnname=" + requestConsumables + "&cnqty=" + requestConsumablesQty + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedConsumables").html(result)
					//console.log(result);
					$("#requestConsumables").val("");
					$("#requestConsumablesQty").val("");
					$("#ConsumableRecordID").val("");
					// $("#requestMedicinesInstruction").val("");
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
		}

	});


	$("#requestActsBtn").click(function(e){
		var requestActName 	= $("#requestActs").val().trim();
		var requestActQty 	= $("#requestActsQty").val().trim();
		var ActRecordID 	= $("#ActRecordID").val().trim();
		if(!requestExam){
			alert("No Act Selected\nPlease Make Sure you select the Act located on the pricing plan \nFrom the Provided List");
		} else if(!requestActQty){
			alert("Quantity is required")
		} else if(requestActQty > 2 ){
			if(confirm("It is uncommon to have act performed more that 2 times\nIs it ok?")){
				// console.log(test);
				$.ajax({
					type: "POST",
					url: "./pa/save-act.php",
					data: "consultationID=" + consultationID + "&ActRecordID=" + ActRecordID + "&actname=" + requestActName + "&actqty=" + requestActQty + "&url=ajax",
					cache: false,
					success: function(result){
						$("#requestedActs").html(result)
						//console.log(result);
						$("#requestActs").val("");
						$("#requestActsQty").val("1");
						$("#ActRecordID").val("");
					},
					error: function(err){
						console.log(err.responseText());
					}
				});
			} else{
				$("#requestActsQty").val("1");
			}
		} else{
			$.ajax({
				type: "POST",
				url: "./pa/save-act.php",
				data: "consultationID=" + consultationID + "&ActRecordID=" + ActRecordID + "&actname=" + requestActName + "&actqty=" + requestActQty + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedActs").html(result)
					//console.log(result);
					$("#requestActs").val("");
					$("#requestActsQty").val("1");
					$("#ActRecordID").val("");
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	});

	$("#requestExamBtn").click(function(e){
		var requestExam = $("#requestExam").val().trim();
		var examrecordID = $("#examRecorID").val().trim();
		var resultString = "";
		if(TDR_Found){
			var resultData = $("#tdrNewResult").val().trim();
			if(!resultData){
				alert("No Result for TDR Exam\nPlease Make Sure you enter the Result Before saving");
				return;
			}
			resultString = "&resultData=" + resultData;
			// alert("We have TDR as Exam please fill in the result to continue");
			// return;
		}
		if(!requestExam){
			alert("No Exam Selected\nPlease Make Sure you select the Exam \nFrom the Provided List");
		} else{
			$.ajax({
				type: "POST",
				url: "./pa/save-exam.php",
				data: "consultationID=" + consultationID + "&examrecordID=" + examrecordID + "&data=" + requestExam + resultString + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedExams").html(result)
					//console.log(result);
					$("#requestExam").val("");
					$("#examRecorID").val("");

					if(TDR_Found){
						// $("#tdrNewResult").val("");
						$("#tdrResult").html("");
						TDR_Found = false
					}
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	});

	$("#requestMedicinesBtn").click(function(e){
		// Here Submit medicine request
		var requestMedicines 			= $("#requestMedicines").val().trim();
		var requestMedicinesQty 		= $("#requestMedicinesQty").val().trim();
		var requestMedicinesInstruction = $("#requestMedicinesInstruction").val().trim();
		var qtyPrise 					= $("#dosage").val().trim();
		var numberOfTimes 				= $("#nbrDays").val().trim();
		var numberOfDay 				= $("#cnDay").val().trim();
		var MedecineRecordID 			= $("#MedecineRecordID").val().trim();

		var siropPattern 				= /sirop/;
		var coartemPattern 				= /coartem/;
		
		noRetry 						= false;

		if(!requestMedicines){
			alert("No Medicine Name is Specified!")
		} else if(!requestMedicinesQty){
			alert("No Medicine Quantity is Specified! " + requestMedicinesQty);
		} else if(requestMedicinesQty > 2 && requestMedicines.match(siropPattern)){
			// Here is for sirop manupulation
			if(confirm("It is uncommon to have more than 2 units of Sirop Type Medicines\nIs it ok?")){
				$.ajax({
					type: "POST",
					url: "./pa/save-md.php",
					data: "consultationID=" + consultationID + "&MedecineRecordID=" + MedecineRecordID + "&qtyPrise=" + qtyPrise + "&numberOfTimes=" + numberOfTimes + "&numberOfDay=" + numberOfDay + "&mdname=" + requestMedicines + "&mdqty=" + requestMedicinesQty + "&mdins=" + requestMedicinesInstruction + "&url=ajax",
					cache: false,
					success: function(result){
						$("#requestedMedicines").html(result);
						$("#requestedConsumables").load("./pa/materials.php?patientID=" + consultationID + "&autorelaod=true");
						//console.log(result);
						$("#requestMedicines").val("");
						$("#requestMedicinesQty").val("");
						$("#requestMedicinesInstruction").val("");
						usableQty = null;
						$("#MedecineRecordID").val("");
						$("#dosage").val("");
						$("#nbrDays").val("");
						$("#cnDay").val("");
						$("#stockAlert").html("");
					},
					error: function(err){
						console.log(err.responseText);
					}
				});
			} else{
				$("#requestMedicinesQty").val("1");
			}
		} else if(requestMedicinesQty > 1 && requestMedicines.match(coartemPattern)){
			// Here is for sirop manupulation
			if(confirm("It is uncommon to have more than 1 package of Coartem Type Medicines\nIs it ok?")){
				$.ajax({
					type: "POST",
					url: "./pa/save-md.php",
					data: "consultationID=" + consultationID + "&MedecineRecordID=" + MedecineRecordID + "&qtyPrise=" + qtyPrise + "&numberOfTimes=" + numberOfTimes + "&numberOfDay=" + numberOfDay + "&mdname=" + requestMedicines + "&mdqty=" + requestMedicinesQty + "&mdins=" + requestMedicinesInstruction + "&url=ajax",
					cache: false,
					success: function(result){
						$("#requestedMedicines").html(result);
						$("#requestedConsumables").load("./pa/materials.php?patientID=" + consultationID + "&autorelaod=true");
						//console.log(result);
						$("#requestMedicines").val("");
						$("#requestMedicinesQty").val("");
						$("#requestMedicinesInstruction").val("");

						usableQty = null;
						$("#MedecineRecordID").val("");
						$("#dosage").val("");
						$("#nbrDays").val("");
						$("#cnDay").val("");
						$("#stockAlert").html("");
					},
					error: function(err){
						console.log(err.responseText);
					}
				});
			} else{
				$("#requestMedicinesQty").val("1");
			}
		} else{
			$.ajax({
				type: "POST",
				url: "./pa/save-md.php",
				data: "consultationID=" + consultationID + "&MedecineRecordID=" + MedecineRecordID + "&qtyPrise=" + qtyPrise + "&numberOfTimes=" + numberOfTimes + "&numberOfDay=" + numberOfDay + "&mdname=" + requestMedicines + "&mdqty=" + requestMedicinesQty + "&mdins=" + requestMedicinesInstruction + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedMedicines").html(result);
					$("#requestedConsumables").load("./pa/materials.php?patientID=" + consultationID + "&autorelaod=true");
					//console.log(result);
					$("#requestMedicines").val("");
					$("#requestMedicinesQty").val("");
					$("#requestMedicinesInstruction").val("");

					usableQty = null;
					$("#MedecineRecordID").val("");
					$("#dosage").val("");
					$("#nbrDays").val("");
					$("#cnDay").val("");
					$("#stockAlert").html("");
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
		}

	});

	$("#saveConsultationUpdate").click(function(e){
		var consultationDataToSave = $("#consultationDataToSave").val().trim().replace(/\n/g, "<br />");
		// alert(consultationDataToSave); return;
		if(!diagnosticData){
			alert("No consultation to save\nPlease fill in diagnostic!");
		} else{
			$.ajax({
				type: "POST",
				url: "./pa/save-cons-comment.php",
				data: "consultationID=" + consultationID + "&data=" + consultationDataToSave + "&url=ajax",
				cache: false,
				success: function(result){
					$("#patientConsultation").html(result)
					//console.log(result);
					$("#consultationDataToSave").val("");
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	});

	$("#saveDiagnosticUpdate").click(function(e){
		if(!consultationID){
			alert("System got an error\nPlease select the patient\nIf the error perisist refresh the page\nTHANKS.");
			return
		}
		// alert(consultationID);
		var diagnosticData = $("#diagnosticData").val().trim();
		var diagnosticRecordID = $("#DiagnosticRecordID").val().trim();

		if(!$("#newCaseEpisode").prop("checked") && !$("#oldCaseEpisode").prop("checked")){
			alert("Diagnostic Episode is required\nPlease Select one for current diagnostic!");
		} else if(!diagnosticData){
			alert("No Diagnostic to save\nPlease fill in diagnostic!");
		} else{
			var diagnosticEpisode = $("#newCaseEpisode").prop("checked")?0:1;

			$.ajax({
				type: "POST",
				url: "./pa/save-diag.php",
				data: "consultationID=" + consultationID + "&diagnosticRecordID=" + diagnosticRecordID + "&data=" + diagnosticData +"&episode=" + diagnosticEpisode + "&url=ajax",
				cache: false,
				success: function(result){
					$("#patientDiagnostic").html(result)
					//console.log(result);
					$("#diagnosticData").val("");
					$("#DiagnosticRecordID").val("");
					$(".caseEpisode").removeProp("checked");
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	});

	function autoRefresh(lastPatientNumber){
		var url = "./patient-list.php?response=ajax";
		var currentNumber = 0;
		var foundTextContent = "";
		$.getJSON( url, function(data) {
			if(lastPatientNumber != data.foundPatient){
				// console.log("CurreNumber:" + currentNumber);
				// console.log("ReturnedNumber:" + data.foundPatient);
				currentNumber = data.foundPatient;
				foundTextContent += data.foundText;
			} else{
				currentNumber = data.foundPatient;
				// console.log("Hello Guy!" + currentNumber);
			}

			$(".newNotifications").html(data.styleData);
		}).done(function(){
			if(currentNumber != lastPatientNumber){
				$(".all_patient").html(foundTextContent);
			}
			setTimeout(function(){
				autoRefresh(currentNumber);
			}, 6000);
			
		});

	}

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
	            wrap.load(this.getTrigger().attr("href") );
	        }

	    });
	    $("input[rel]").overlay({

	        mask: '#206095',
	        effect: 'apple',
	        onBeforeLoad: function() {

	            // grab wrapper element inside content
	            var wrap = this.getOverlay().find(".contentWrap");
				
				/* alert($("#pst_").attr("checked")); */
	            // load the page specified in the trigger
	            wrap.load(this.getTrigger().attr("href") );
	        }

	    });
	});

	setTimeout(function(){
		autoRefresh(firstNumber);
	}, 5000);
</script>