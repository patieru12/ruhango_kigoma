<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("prc" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";
if(@$_POST['rcv_patient']){
	//check if the date in is valid
	$_GET['key'] = $_POST['patientid'];
	if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$_POST['date'])){
		$error = "<span class=error-text>Invalid Date format</span>";
		
	} else if(date("Y-m-d",time()) < $_POST['date']){
		$error = "<span class=error-text>Invalid Date</span>";
		
	} elseif(!is_numeric($_POST['insurance']) || !isDataExist($sql="SELECT in_name.* FROM in_name,in_category,in_price,in_forms WHERE in_name.CategoryID= in_category.InsuranceCategoryID && in_forms.InsuranceNameID = in_name.InsuranceNameID && in_price.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceNameID='{$_POST['insurance']}'", $con)){
		$error = "<span class=error-text>Unsupported Insurance</span>";
	} elseif(returnSingleField($sql="SELECT InsuranceName From in_name WHERE InsuranceNameID='{$_POST['insurance']}'",$field="InsuranceName",$data=true, $con) != "Private" && PDB($_POST['car_id'],true, $con)){
		//save the new record if not exist
		$error = "<span class=error-text>No Insurance Card ID Found</span>";
	} else{
		//generate the document id now
		$docid = generateDocID($_POST['insurance']);
		//var_dump($docid);
		//return;
		$sql = "INSERT INTO pa_records SET DocID='{$docid}',
				PatientID='{$_POST['patientid']}',
				InsuranceNameID='{$_POST['insurance']}',
				InsuranceCardID='{$_POST['car_id']}',
				Weight='',
				Temperature='',
				DateIn='{$_POST['date']}',
				Status=0,
				ReceptionistID='{$_SESSION['user']['UserID']}'";
				
		if($record = saveAndReturnID($sql, $con)){
			$error = "<span class=success><b>New Patient Received <a href='../forms/".returnSingleField($sql="SELECT in_forms.* FROM in_name,in_category,in_price,in_forms WHERE in_name.CategoryID= in_category.InsuranceCategoryID && in_forms.InsuranceNameID = in_name.InsuranceNameID && in_price.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceNameID='{$_POST['insurance']}'","FormFile",$data=true, $con)."?records=".$record."' target='_blank'>Print</a></b></span>";
			//header("Location:);
			//return;
		} else{
			$error = "<span class=error-text>Unable to Save New Document</span>";
		}
		
	}
}


//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name ORDER BY InsuranceName ASC",$con),$multirows=true,$con);
require_once '../lib2/cssmenu/prc_header.html';
?>
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
		<tr>
			<td id="ds_calclass"></td>
		</tr>
	</table>
  <div id="w">
    <div id="content">
    	<form action="rcv_patient.php" method="POST" id="savePatientform">
    		<input id="patientName" type=hidden name='patientName' onblur='' class='txtfield1' onclick="" style='' value='' />
	    	<table border=0 style='background-color:#fff; width: 100%;'>
				<tr>
					<td colspan="2" rowspan="8" style="border:0px solid #000; ">
						Search for Existing by name:<br />
						<input id="patientNameSearch" type=text name='patientNameSearch' placeholder="Enter Patien Name" class='txtfield1' style="width: 100%" />
						<div id="searchResult" style="min-height: 200px">
							<label class=as_link id="fillInNextID">Create New Record</label>
							<script type="text/javascript">
								var oldText = $("#newpatientOutput").html();
								$("#fillInNextID").click(function(e){
									$("#newpatientOutput").html(oldText);
									// Check if the date is entered correclty
									var date = $("#date").val();
									if(date){
										// Find the next Patient ID
										$.ajax({
											type: "GET",
											url: "./nexpatientid.php",
											data: "date=" + date.replace(/ /g, "%20") ,
											cache: false,
											success: function(result){
												$("#patientIDSearch").val(result);
											}
										});
									} else{
										$("#newpatientOutput").html("<span class='error-text'>Please Select the Date</span>");
									}
								});

							</script>
						</div>
					</td>
					<td style=" width: 10%">
						Date<span class=error-text>*</span>
					</td>
					<td style=" width: 10%">
						<input id="date" type=text name='date' onblur='' class='txtfield1 date_on' onclick="ds_sh(this,'date')" style='' value='<?php echo date("Y-m-d",time()) ?>' disabled />
					</td>
				</tr>
				<tr>
					<td>
						Patient ID<span class=error-text>*</span> 
					</td>
					<td>
						<input id="patientIDSearch" type=text name='patientIDSearch' onblur='' class='txtfield1' onclick="" style='' value='' />
						<br />
					</td>
				</tr>
				
						
				<tr>
					<td>
						Weigth<span class=error-text>*</span> 
					</td>
					<td>
						<input id="patientWeigth" type=text name='patientWeigth' onblur='' class='txtfield1' onclick="" style='' value='' />
					</td>
				</tr>
				<tr>
					<td>
						Temp
					</td>
					<td>
						<input id="patientTemp" type=text name='patientTemp' onblur='' class='txtfield1' onclick="" style='' value='' />
					</td>
				</tr>
				<tr>
					<td>
						Height (Taille)
					</td>
					<td>
						<input id="patientLength" type=text name='patientLength' onblur='' class='txtfield1' onclick="" style='' value='' />
					</td>
				</tr>
				<tr>
					<td>
						MUAC
					</td>
					<td>
						<input id="patientMUAC" type=text name='patientMUAC' onblur='' class='txtfield1' onclick="" style='' value='' />
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
					<td>
						<input type=submit id=save class="flatbtn-blu" name=rcv_patient value='Save' />
						<div id="save_result">
							
						</div>
					</td>
				</tr>
			</table>
		</form>
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
function receivePatient(id,ins=""){
	$(".patient_selected").load("receive_patient.php?key=" + id + "&ins=" + ins);
}
$(document).ready(function(){
	
	$("#patientNameSearch").keydown(function(e){
		if(e.which == 13 ){
			return false;
		}
	});

	$("#patientNameSearch").keyup(function(e){
		var name = $("#patientNameSearch").val().replace(/ /g, "%20");
		if(e.which == 13 ){
			var url = "./patientname.php?name=" + name;
			var receivedData;
			// Here find the next
			$("#searchResult").load(url);
		} else{
			$("#searchResult").html("<span class='error-text'>Press Enter to submit</span>");
		}
	});

	$("#searchPatient").click(function(e){
		var date= $("#date").val();
		var id 	= $("#patientIDSearch").val();

		if(id && date){
			var url = "./patientName.php?patientid=" + id + "&date=" + date;
			// Here find the next
			$.getJSON( url, function(data) {
				console.log(data);
			}).done(function(){
				console.log("Done Loading");
			});
		}
	});
	
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
  	$('#save').click(function(e){
		e.preventDefault();
				//$('#save').attr("desabled",":true");
		$("#save_result").html('');
		$("#save_result").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
		$("#savePatientform").ajaxForm({ 
			target: '#save_result'
		}).submit();
	
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
	
	/************* HIDE THE CALENDAR AUTOMATICALLY  ********/
	var c_clicked = false;
	
	$(".date_on").focus(function(){
		c_clicked = false;
	});
	$(".date_on").blur(function(){
		console.log("Date Field Loast Focus Test Calendar Status");
		setTimeout(function(){
			if(c_clicked == false){
				console.log("Now Close the Calendar NOW");
				ds_hi();
			}
		},300);
	});
	$(".ds_box").click(function(){
		c_clicked = true;
		console.log("Calendar Clicked! Dont Close");
	});
	/************* HIDE THE CALENDAR AUTOMATICALLY  ********/
});
</script>
</body>
</html>