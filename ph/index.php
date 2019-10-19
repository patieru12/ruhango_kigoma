<?php
header("Location:./distribute.php");
exit();
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("ph" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
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
require_once '../lib2/cssmenu/ph_header.html';
?>
  <div id="w">
    <div id="content"><!--
      <h1>WELCOME TO CARE SOFTWARE</h1>-->
      <b>
	  <center>
	  <input type=text name=search_patient style='width:300px;' placeholder="Enter Document Number" <?php echo @$_GET['key']?"value='{$_GET['key']}'":"" ?> id=doc_search class=txtfield1 />
	  <a href="#loginmodal" class="flatbtn" id="search">Search</a></center>
	  <div class="doc_found">
	  
	  </div>
	  <?php echo $error; ?>
	  <div class="doc_selected">
	  
	  </div>
	  </b>
    </div>
  </div>
  
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