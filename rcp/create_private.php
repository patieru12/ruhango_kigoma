<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
/* header("Location:./create_cbhi.php");
 */
//var_dump($_POST);
$error = "";
//die;
//var_dump($_SESSION);
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceName='Private' && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$insurance_all = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name, sy_users WHERE sy_users.UserID = se_name.DirectorID && sy_users.CenterID = '{$_SESSION['user']['CenterID']}' && se_name.Status=1 ORDER BY ServiceName ASC",$con),$multirows=true,$con);
$active = "create";
//var_dump($service); die;
require_once "../lib2/cssmenu/rcp_header.html";

$tm_data = <<<Data
	<label><input type=radio name=tm value=200 />TM Paid</label> <label><input type=radio name='tm' value='0' />TM Not Paid</label>
Data;
?>

  <div id="w">
    <div id="content" style='text-align:left;'><!--
      <h1>WELCOME TO CARE SOFTWARE</h1>-->
	  
	  <style>
		.a td{
			border:0px solid #000; padding:2px;
		}
		
		.patient_found{
			max-height:200px;
			overflow:auto;
		}
	  </style>
	  <b>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	  <?php echo $error; ?>
	  <div id=upload_out></div>
	  <div style='padding-bottom:10px; font-size:14px;'>
		<?php 
			if($insurance_all){
				$i=0; 
				$nav = "<div class=_inner_nav>";
				foreach($insurance_all as $in){ 
					//echo "<label style='padding-right:10px;'><input ".($i++ == 0 ?"checked":"")." type=radio onclick='window.location=\"./create_".str_replace(" ","_",strtolower($in['InsuranceName'])).".php\"; if($(\"#in{$in['InsuranceNameID']}\").prop(\"checked\")){ $(\"#insurance\").val(\"{$in['InsuranceNameID']}\") } if(\"{$in['InsuranceName']}\" == \"CBHI\"){CBHI();} else if(\"{$in['InsuranceName']}\" == \"RSSB RAMA\"){RAMA();} else if(\"{$in['InsuranceName']}\" == \"MMI\"){MMI();} else if(\"{$in['InsuranceName']}\" == \"MEDIPLAN\"){MEDIPLAN();} else{ OTHER();}' id='in{$in['InsuranceNameID']}' name='insurance_' ".(@$_POST['insurance'] == $in['InsuranceNameID']?"selected":"").">{$in['InsuranceName']}</label>"; 
					$nav .= "<span onclick='window.location=\"./create_".str_replace(" ","_",strtolower($in['InsuranceName'])).".php\";' class=_link ".($in['InsuranceName'] == $insurance[0]['InsuranceName']?"id='active'":"").">".strtoupper($in['InsuranceName'])."</span>";
				}
				$nav .= "</div>";
				echo $nav;
			}
			?>
	  </div>
	  <h1>PRIVATE PATIENT MEDICAL DOCUMENT</h1>
	  <form method=post action="rcv_patient_private.php" id='rcv_patient_frm'>
	  <input type='hidden' name='rcv_patient_btn' value='save' />
	  <input type=hidden id='insurance' name='insurance' value='<?php echo $insurance[0]['InsuranceNameID'] ?>' />
	  <input type=hidden id='patient_id' name='patient_id' />
	  <table border=0 class="a" style='background-color:#fff;'>
		<tr>
			<td colspan=2><!--Insurance:<span class=error-text>*</span>
			<?php 
			if($insurance){
				$i=0; 
				foreach($insurance as $in){ 
					echo "<label><input ".($i++ == 0 ?"checked":"")." type=radio onclick='if($(\"#in{$in['InsuranceNameID']}\").prop(\"checked\")){ $(\"#insurance\").val(\"{$in['InsuranceNameID']}\") } if(\"{$in['InsuranceName']}\" == \"CBHI\"){CBHI();} else if(\"{$in['InsuranceName']}\" == \"RSSB RAMA\"){RAMA();} else if(\"{$in['InsuranceName']}\" == \"MMI\"){MMI();} else if(\"{$in['InsuranceName']}\" == \"MEDIPLAN\"){MEDIPLAN();} else{ OTHER();}' id='in{$in['InsuranceNameID']}' name='insurance_' ".(@$_POST['insurance'] == $in['InsuranceNameID']?"selected":"").">{$in['InsuranceName']}</label>"; 
				} 
			}
			?>-->
			<input id="patient_search_" type=hidden placeholder='Search' class='txtfield1' style='' />
			Consultation Date: <input id="date" type=text name='date' onblur='' class='txtfield1' onclick="ds_sh(this,'date')" style='width:150px;' value='<?php echo date("Y-m-d",time()) ?>' />
			</td>
				<td colspan="2" style="display: none;">
					Daily Number: <input id="DailyNumberID" type=text name='DailyNumberID' class='txtfield1' style='width:200px;' placeholder="Daily Number" />
					<span class="info"></span>
				</td>
		</tr>
		<tr>
			<td class="found_link" colspan=6><!--<input id="doc_id" type=text name='doc_id' class='txtfield1' onclick="" style='' value='<?php echo date("Y-m-d",time()) ?>' />--></td>
		</tr>
		<tr class="patient_found_search">
			<td colspan="6">
				<a href='./force-selection-private.php' id=display_now rel="#overlay"><img src='../images/view.png' style='width:0px;' /></a>
				<div class="patient_found"></div>
			</td>
		</tr>
		<tr><!--<td>Card ID:<span class=error-text>*</span></td>--><td>Name:<span class=error-text>*</span></td><td>Age [<label class=as_link>yyyy-mm-dd or yyyy</label>]:</td><td>Sex<span class=error-text>*</span><!--<td>Family Chief ID Card:</td><td>Family Position:</td>-->
		</tr>
		<tr>
			<td><input id="patient_search" autocomplete="off" type=hidden name='card_id' class='txtfield1' style='' /><!--</td>
			<td>--><input type=text name='name' autocomplete="off" onkeyup='if(familyChief()){$("#father").val($("#name").val());}' id=name class='txtfield1' style='' /></td>
			<td><input type=text name='age' autocomplete="off" id=age class='txtfield1' onclick="ds_sh(this,'age')" style='' /></td>
			<td><label><input type=radio name=sex value="Female" required id=female>Female</label> <label><input type=radio name=sex value="Male" required id=male>Male</label></td>
			
		</tr>
		<tr><!--<td>Affiliate<span class=error-text>*</span></td>--><td class=h_label>House Manager:</td></td><td class=fcategory>Category<span class=error-text>*</span></td></td><td class=affection_location></td><td class=sex></td>
		</tr>
		<tr><!--
			<td>
				<label class=as_link_ onclick='$("#father").val($("#name").val()); $("#rama_affiliate").val("Self")'><input type=radio name='ralation'>Self</label>
				<label class=as_link_ onclick='$("#father").val(""); $("#rama_affiliate").val("Conjoint")'><input type=radio name='ralation'>Conjoint</label>
				<label class=as_link_ onclick='$("#father").val(""); $("#rama_affiliate").val("Child")'><input type=radio name='ralation'>Parent</label>
				<input type=hidden name="rama_affiliate" id='rama_affiliate' />
			</td>-->
			<td><input type=text name='father' autocomplete="off" id=father class='txtfield1' style='' /></td>
			<td class=fcategorydata><input autocomplete="off" type=text name=fcategory id=fcategory value=3 class=txtfield1 /></td>
			<td class=affection_locationdata><!--<input type=text name=fcategory id=fcategory value=3 class=txtfield1 />--></td>
			<td class=sexdata><!--<input type=text name=fcategory id=fcategory value=3 class=txtfield1 />--></td>
		</tr>
		<tr>
			<td>Village</td><td>Cell</td><td>Sector</td><td>District</td>
		</tr>
		<tr>
			<td><input type=text name='village' autocomplete="off" id=village class="txtfield1" style='' /></td>
			<td><input type=text name='cell' autocomplete="off" id=cell class="txtfield1" style='' /></td>
			<td><input type=text name='sector' autocomplete="off" id=sector class="txtfield1" style='' /></td>
			<td><input type=text name='district' autocomplete="off" id=district class="txtfield1" style='' /></td>
		</tr>
		<tr>
			<td colspan=6><div class='address_search' style='max-height:150px; overflow:auto;'></div></td>
		</tr>
		<tr>
			<td colspan=3>Service<span class=error-text>*</span> <br />
			<?php 
			if($service){
				$i=0; 
				foreach($service as $in){ echo "<label style='padding:0 10px;'><input type=radio name='service' ".($in['ServiceCode'] == "CPC"?"checked":"")." value='{$in['ServiceNameID']}' id='{$in['ServiceCode']}'>{$in['ServiceCode']}</label>"; } 
			} else{
				echo "<span class=error-text>No Service Available</span>";
			}
			//check for consultation type now please
			// $cat = dayCategory(date("Y-m-d",time()));// echo $cat;
			// $tm_to_pay = preg_match("/jour$/",strtolower($cat))?1040:1220;
			
			?></td>
		</tr>
		<tr style="display: none;">
			<td>Weight</td><td>Temperature</td><td>Length (Taille)</td><td>MUAC</td>
		</tr>
		<tr style="display: none;">
			<td><input type=text name='weight' autocomplete="off" id='weight' class='txtfield1' style='' /></td>
			<td><input type=text name='temperature' autocomplete="off" id='temperature' class='txtfield1' style='' /></td>
			<td><input type=text name='length' autocomplete="off" id='length' class='txtfield1' style='' /></td>
			<td><input type=text name='muac' autocomplete="off" id='muac' class='txtfield1' style='' /></td>
		</tr>
		<tr>
			<td class=tm_view><label><input type=radio id='paid' name='tm' value='200' />TM Paid</label> <label><input type=radio id='not_paid' name='tm' value="0" />TM Not Paid</label></td>
		</tr>
		<!--
		<tr>
			<td>Consultation Price</td>
			<td>Receipt</td>
		</tr>
		<tr>
			<td class=tm_view><label><input type=radio id='paid' name='tm' value='200' />TM Paid</label> <label><input type=radio id='not_paid' name='tm' value="0" />TM Not Paid</label></td>
		</tr>-->
		<tr><td colspan=4 align=center><label id='steps'></label><input type=hidden name=next_frequency id=next_frequency value='1' /><input type=hidden name=document_list id=document_list value='' /><label><input type=checkbox id=update disabled> Update</label>
		<input type=submit id=save class="flatbtn-blu" name=rcv_patient value='Save' /> <input type=reset class="flatbtn-blu" onclick='window.location="./create_private.php";' value='Clear' /></td></tr>
	  </table>
	  <input type=hidden id=update_ name=update value='' />
	  </form>
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
  <!-- make all links with the 'rel' attribute open overlays -->
<script>
function familyChief(){
	var family = /01$/;
	if(family.test($("#patient_search").val())){
		return true;
	}
	return false;
}

function RAMA(){
	$(".h_label").html("Affiliate Name: <label class=as_link onclick='$(\"#father\").val($(\"#name\").val())'>Self</label> <label class=error-text>*</label>");
	$(".fcategory").html("Affectation <label class=error-text>*</label>");
	$(".fcategorydata").html("<input type=text name=fcategory id=fcategory class=txtfield1 />");
	$("#fcategory").val("");
	$(".tm_view").html("<input type=hidden name=tm value=0 />");
	$(".affection_location").html("Location <label class=error-text>*</label>");
	$(".affection_locationdata").html("<input type=text name=location id=location class=txtfield1 />");
	$(".sex").html("");
	$(".sexdata").html("");

}

function MEDIPLAN(){
	$(".h_label").html("Police Number <label class=error-text>*</label>");
	
	$(".tm_view").html("<input type=hidden name=tm value=0 />");
	$(".fcategory").html("");
	$(".affection_location").html("");
	$(".affection_locationdata").html("");
	//$(".h_label").html("House Manager <label class=as_link onclick='$(\"#father\").val($(\"#name\").val())'>Self</label> <label class=error-text>*</label>");
	$(".fcategorydata").html("<input name=fcategory id=fcategory type=hidden class=txtfield1 value=-1 />");
	$(".sex").html("");
	$(".sexdata").html("");

}
/* MEDIPLAN(); */
function CopyMMI(){
	$("#father").val($("#name").val());
	$("#fcategory").val($("#patient_search").val());
	$("#location").val($("#age").val());
	if($("#male").prop("checked")){
		$("#affmale").attr("checked",":true");
	} else if($("#female").prop("checked")){
		$("#afffemale").attr("checked",":true");
	}
}
function MMI(){
	$(".h_label").html("Affiliate Name: <label class=as_link onclick='CopyMMI();'>Self</label>");
	$(".fcategory").html("Affiliate Number");
	$(".fcategorydata").html("<input type=text name=fcategory id=fcategory class=txtfield1 />");
	$("#fcategory").val("");
	$(".tm_view").html("<input type=hidden name=tm value=0 />");
	$(".affection_location").html("Age");
	$(".affection_locationdata").html("<input type=text name=location id=location class=txtfield1 />");
	$(".sex").html("Sex");
	$(".sexdata").html("<label><input type=radio name=affsex value=Female required id=afffemale>Female</label> <label><input type=radio name=affsex value=Male required id=affmale>Male</label>");
}
//MMI();
function CBHI(){
	$(".h_label").html("House Manager: <label class=as_link onclick='$(\"#father\").val($(\"#name\").val())'>Self</label> <label class=error-text>*</label>");
	$(".tm_view").html("<label><input type=radio name=tm value=200 />TM Paid</label> <label><input type=radio name=tm value=0 />TM Not Paid</label>"); 
	$(".fcategory").html("Category: <label class=error-text>*</label>"); 
	$(".fcategorydata").html("<input type=text name=fcategory id=fcategory class=txtfield1 value=3 />");
	$(".affection_location").html("");
	$(".affection_locationdata").html("");
	$(".sex").html("");
	$(".sexdata").html("");
	$("#update").removeAttr("checked");
}

function OTHER(){
	$(".tm_view").html("<input type=hidden name=tm value=0 /><input type=hidden id=receipt_number name=receipt_number class=txtfield1 style='width:70px;' />");
	$(".fcategory").html("Phone Number");
	$(".fcategorydata").html("<input name=phonenumber id=phonenumber type=text class=txtfield1 value='' /><input name=fcategory id=fcategory type=hidden class=txtfield1 value=-1 />");
	$(".affection_location").html("");
	$(".affection_locationdata").html("");
	$(".h_label").html("House Manager <label class=as_link onclick='$(\"#father\").val($(\"#name\").val())'>Self</label> <label class=error-text>*</label>");
	$(".sex").html("");
	$(".sexdata").html("");
}
OTHER();
function receivePatient(id,ins=""){
	$(".patient_selected").load("receive_patient.php?key=" + id + "&ins=" + ins);
}
function addrSearch(level,key){
	$(".address_search").html("");
	$(".address_search").load("search_address.php?key=" + key + "&level=" + level);
}
$(document).ready(function(){
	$("#DailyNumberID").keydown(function(e){
		if(e.which == 13 ){
			return false;
		}
	});

	var docsearch = false;
	$("#DailyNumberID").keyup(function(e){
		e.preventDefault();
		// alert("Patient Search Button Clicked!");
		$(".info").html("<br /><span class='error-text'>Press Enter to Search</span>");
		$(".found_link").html("");
		if(e.which == 13 ){
			$(".doc_found").html("<span style='position:absolute; top:112px; left:700px;' >Searching <img src='../images/ajax_clock_small.gif' /></span>");
			//$("#search").click();

			if(docsearch ==false){
				docsearch = true;
				setTimeout(function(e){
					$(".patient_found").load("search_patient_private.php?key="+$("#DailyNumberID").val().replace(" ","_") + "&ins=" + $("#insurance").val() + "&date=" + $("#date").val());

					// $(".doc_found").load("reprint_doc.php?key="+$("#doc_search").val().replace(/ /g,"%20"));
					$("#info").html("");
					docsearch = false;
					$(".patient_found_search").show();
					$(".info").html("");
				}, 50);
			}
			return 
		}
		return e.preventDefault();
	});

	$("#receipt_number").focus(function(){
		$("#paid").attr("checked",":true");
		$("#receipt_number").removeAttr("readonly");
	});
	$("#paid").click(function(){
		$("#receipt_number").focus();
	});
	$("#village").keyup(function(e){
		addrSearch(4,$("#village").val());
	});
	$("#cell").keyup(function(e){
		addrSearch(3,$("#cell").val());
	});
	$("#sector").keyup(function(e){
		addrSearch(2,$("#sector").val());
	});
	$("#district").keyup(function(e){
		addrSearch(1,$("#district").val());
	});
	$('#save').click(function(e){ 
			e.preventDefault();
			$("#upload_out").html('');
			$("#upload_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
			$("#rcv_patient_frm").ajaxForm({ 
				target: '#upload_out'
			}).submit();
			
	});
	
	$(".patient_found_search").hide();
	//if the search button is clicked search the patient_found
	$(".patient_found").click(function(){
		$(".patient_found_search").hide(500);
	});
	$("#search").click(function(e){
		$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
		$(".patient_selected").html("");
		return e.preventDefault();
	});
	$("#patientIDSearch").keyup(function(e){
		if(e.which != 13 ){
			$(".patient_found").html("<span style='' >Searching <img src='../images/ajax_clock_small.gif' /></span>");
			// $("#search").click();
			return;
		}
		$(".patient_found").html("<span style='' >Searching <img src='../images/ajax_clock_small.gif' /></span>");
		$(".found_link").html("");
		$("#patient_search_").val($("#patient_search").val());
		 
		$(".patient_found_search").show(2000);
		$(".patient_selected").html("");
		$("#patient_id").val("");
			 
		$(".patient_found").load("search_patient_id.php?key="+$("#patientIDSearch").val().replace(/ /g,"_") + "&ins=" + $("#insurance").val());
		return e.preventDefault();
	});
	$("#insurance").change(function(e){
		$(".patient_selected").html("");
		$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
	});
	$("#name").change(function(e){
		$(".patient_found").hide;
		//$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
	});
	$("#patient_search_").keyup(function(e){
			 $(".found_link").html("");
			 $("#name").val("");
			 $("#patient_search").val("");
			 $("#patient_id").val("");
			 $("#age").val("");
			 $("#father").val("");
			 $("#update_").val("0");
			 $("#update").removeAttr("checked");
			
			 $("#district").val("");
			 $("#sector").val("");
			 $("#cell").val("");
			 $("#village").val("");
			 
		$(".patient_found_search").show(2000);
		$(".patient_selected").html("");
		$(".patient_found").load("search_patient.php?key="+$("#patient_search_").val().replace(" ","_") + "&ins=" + $("#insurance").val());
		return e.preventDefault();
	});
	$("#patient_search").keyup(function(e){
		/*$(".found_link").html("");
		$("#patient_search_").val($("#patient_search").val());
		 
		$(".patient_found_search").show(2000);
		$(".patient_selected").html("");
		$("#patient_id").val("");
			 
		$(".patient_found").load("search_patient.php?key="+$("#patient_search_").val().replace(/ /g,"_") + "&ins=" + $("#insurance").val());
		return e.preventDefault();*/
	});
	
	$("#name").focus();
	// $("#DailyNumberID").focus();
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
		//receivePatient("<?php echo @$_POST['patientid'] ?>","<?php echo @$_POST['insurance'] ?>");
	</script>
	<?php
}
?>
</body>
</html>