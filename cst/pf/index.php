<?php
	session_start();
	
	require_once "../../lib/db_function.php";
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
require_once "../../lib2/cssmenu/cst_pf_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Family Planning Additional Data Entry</h1>
		<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
			<tr>
				<td id="ds_calclass"></td>
			</tr>
		</table>
    	<table border="0" cellpadding="0" cellspacing="0" style='font-size:14px; width: 100%'>
    		<tr>
    			<!-- Left container -->
				<td style="width: 20%;">
					<h3>All PF Users</h3>
					<input type=text name=search autocomplete=off style='width:100%' id=search placeholder="Filter Patient" />
					<span id=bar style='position:relative; top:-20px; left:93%; height:0px;'>&nbsp;</span>
					<!-- <div style="text-align:center; vertical-align:middle; display: table-cell; position:absolute; top:15%; left:0%;border:1px solid blue; height:50px; background-size: 100%; width: 50px; background-repeat: no-repeat; background-image: url('../images/label_blank_blue.png');">
						0
					</div> -->
					<div class=all_patient style='height:400px; width:300px; overflow:auto;'>
						<script>
							//send the request to display lowest stock status
							var firstNumber = 0;
							var requestCompleted = true;
							$(".all_patient").load("./patient-list.php");
						</script>
					</div>
				</td>
				<td style="border-left: 1px solid #000;" id="mainInput">
					<h2>User Identification</h2>
					<div id="patientIdentification" style="min-height: 85px;">
						<!-- Here is the Patient Identification -->
						<span class="error-text">Please Select a Patient to view the full address</span>
					</div>
					<input type="hidden" name="" id="patientIDRecords" />
					<h2>Family Planning Usage History</h2>
					<div style=" max-height: 485px; overflow: auto;">
						<?= $error; ?>
						<div id="usageHistory">
							<!-- Here is the List of Recorded Diagnostic -->
						</div>
						<hr />
					</div>
    			</td>
    		</tr>
    	</table>
    	</b>
	</div>
</div>

  <?php
  $systemPath = "../../";
  include_once "../../footer.html";
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
	});


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
			$("#bar").html("<img src='../../images/ajax_clock_small.gif' />");
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