<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$error = "";
$active = "newResult";
require_once "../lib2/cssmenu/lab_header.html";
?>
<div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Adding Result to requested Exams</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table border="0" cellpadding="0" cellspacing="0" style='font-size:14px; width: 100%'>
    		<tr>
    			<!-- Left container -->
				<td style="width: 30%;">
					
					<h3>Requested Exams</h3>
					<input type=text name=search autocomplete=off style='width:100%' id=searchExam placeholder="Filter Exam Result" />
					<span id=bar style='position:relative; top:-20px; left:93%; height:0px;'>&nbsp;</span>
					<div class=all_result style='height:400px; width:100%; overflow:auto;'>
						<script>
							//send the request to display lowest stock status
							var firstNumber = 0;
							var activeID = "";
							$(".all_result").load("./exam-request.php");
						</script>
					</div>
				</td>
				<td style="border-left: 1px solid #000;">
					<?= $error; ?>
					<h2>Patient Identification</h2>
					<div id="patientIdentification" style="min-height: 155px;">
						<!-- Here is the Patient Identification -->
						<span class="error-text">Please Select a Patient to view the full address</span>
					</div>
					<h2>Exams</h2>
					<div>
						<!-- Here is the List of Recorded Diagnostic -->
						<form action='' id='printSelectedSamples'>
							<table class="tableSmallContent" border="1" style="width: 100%">
								<thead>
									<tr><td>&nbsp;</td><td>ID</td><td style="width:70%;">Name</td><td style="width: 20%">Result</td><td></td></tr>
								</thead>
								<tbody id="requestedExams">
									<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="2">
											<input type="text" id="requestExamID" class="txtfield1" name="" style="width:50px;">
											<input type="text" id="requestRecordID" class="txtfield1" name="" style="display: none;">
										</td>
										<td><input type="text" id="requestExamName" class="txtfield1" name="requestExamName" style="width:100%;"></td>
										<td><input type="text" id="requestExamResult" class="txtfield1" name="" style="width:100%;"></td>
										<td><button id="requestExamBtn" class="flatbtn-blu">Save</button></td>
									</tr>
								</tfoot>
							</table>
						</form>
					</div>
					
    			</td>
    		</tr>
    	</table>
    	<div style='height: 10px;'></div>
    	</b>
	</div>
</div>
  <?php
  include_once "../footer.html";
  ?>
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
<style type="text/css">
	.tableSmallContent td{
		font-size:12px;
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

	$(document).ready(function(){
		var query_sent = false;
		$("#searchExam").focus();

		$("#requestExamID").keydown(function(e){
			return e.preventDefault();
		});

		$("#requestExamBtn").click(function(e){
			e.preventDefault();
			$.ajax({
				type: "POST",
				url: "./pa/save-exam-result.php",
				data: "recordID=" + $("#requestRecordID").val() + "&resultID=" + $("#requestExamID").val() + "&data=" + $("#requestExamResult").val() + "&url=ajax",
				cache: false,
				success: function(result){
					// $("#requestedExams").html(result)
					LoadProfile(result);
					// AllowToAddResult(result);
					//console.log(result);
					$("#requestRecordID").val("");
					$("#requestExamID").val("");
					$("#requestExamResult").val("");
					$("#requestExamName").val("");
				},
				error: function(err){
					alert("An error occured while requesting Result Saver Service\nPlease Try Again Later");
					console.log(err.responseText);
				}
			});
		});

		$("#searchExam").keyup(function(){
			$("#bar").html("<img src='../images/ajax_clock_small.gif' />");
			//now try send the filter query
			if(query_sent == false){
				query_sent = true;
				setTimeout(function(){
					if($("#searchExam").val().trim != ""){
						$(".all_result").load("./exam-request.php?response=html&filter=true&keyword=" + $("#searchExam").val().replace(/ /g,"%20"));
						$("#bar").html("");
					}
					query_sent = false;
				},1000);
			}
		});

	});

	function autoRefresh(lastPatientNumber){
		var url = "./exam-request.php?response=ajax";
		var currentNumber = 0;
		var foundTextContent = "";
		$.getJSON( url, function(data) {
			if(lastPatientNumber != data.foundPatient){
				currentNumber = data.foundPatient;
				foundTextContent += data.foundText;
			} else{
				currentNumber = data.foundPatient;
			}
			// $(".newNotifications").html(data.styleData);
		}).done(function(){
			if(currentNumber != lastPatientNumber){
				$(".all_result").html(foundTextContent);

				$("#myTr" + activeID).addClass("activeP");
			}
			setTimeout(function(){
				autoRefresh(currentNumber);
			}, 10000);
		});
	}
	setTimeout(function(){
		autoRefresh(firstNumber);
	}, 10000);
</script>