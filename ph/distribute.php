<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("ph" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";
//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name ORDER BY InsuranceName ASC",$con),$multirows=true,$con);
$active = "distribute";
require_once"../lib2/cssmenu/ph_header.html" ;
?>
<div id="w">
	<h1 class="title_bar">Distribute Medicines to Patient as prescribed.</h1>
    <div id="content" style='height:98%; overflow:auto;'>
    	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
			<tr>
				<td id="ds_calclass"></td>
			</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" style='font-size:14px; width: 100%'>
			<tr>
				<td style="width: 20%;">
					<h3>All Patient</h3>
					<input type=text name=search autocomplete=off style='width:100%' id=search placeholder="Filter Patient" />
					<span id=bar style='position:relative; top:-20px; left:93%; height:0px;'>&nbsp;</span>
					<div class=all_patient style='height:450px; width:350px; overflow:auto;'>
						<script>
							var firstNumber = 0;
							$(".all_patient").load("./patient-list.php");
						</script>
					</div>
					<div class="newNotifications" style="border:0px solid green; padding:10px;">
						<span class="error">New</span>
					</div>
				</td>
				<td style="border-left: 1px solid #000;">
					<?= $error; ?>
					<div id="receiptcontent">
						<!-- Content Here -->
					</div>
				</td>
			</tr>
		</table>
    </div>
</div>
<?php
	include_once "../footer.html";
?>
<div class="apple_overlay" id="overlay">
	<!-- the external content is loaded inside this tag -->
	<div class="contentWrap"></div>
</div>
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
<script type="text/javascript">
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
						$(".all_patient").load("./patient-list.php?filter=true&keyword=" + $("#search").val().replace(/ /g,"%20"));
						$("#bar").html("");
					}
					query_sent = false;
				},1000);
			}
		});
	});

	function unDistributeMedicines(previousNumber){
		var url = "./patient-list.php?response=ajax";
		if($("#search").val().trim != ""){
			// url = "./patient-list.php?filter=true&keyword=" + $("#search").val().replace(/ /g,"%20");
		}
		var currentNumber = 0; //previousNumber;
		var foundTextContent = "";
		$.getJSON( url, function(data) {
			console.log(previousNumber + ":" + data.foundPatient);
			if(previousNumber != data.foundPatient){
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
			console.log(currentNumber + ":" + previousNumber);
			if(currentNumber != previousNumber){
				$(".all_patient").html(foundTextContent);
			}
			setTimeout(function(){

				unDistributeMedicines(currentNumber);
			}, 6000);
			
		});
	}
	setTimeout(function(e){
		unDistributeMedicines(firstNumber);
	}, 6000);
</script>