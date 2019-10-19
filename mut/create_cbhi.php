<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("mut" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";
//die;
//var_dump($_SESSION);
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceName='CBHI' && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$insurance_all = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceName='CBHI' && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name, sy_users WHERE sy_users.UserID = se_name.DirectorID && sy_users.CenterID = '{$_SESSION['user']['CenterID']}' && se_name.Status=1 ORDER BY ServiceCode ASC",$con),$multirows=true,$con);
$active = "create";
//var_dump($insurance); die;
require_once "../lib2/cssmenu/mut_header.html";

$tm_data = <<<Data
	<label><input type=radio name=tm value=200 />TM Paid</label> <label><input type=radio name='tm' value='0' />TM Not Paid</label>
Data;
?>
<script>
  var hide_calendar = false;
</script>
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
	  <div id=upload_out></div>
	  <h1>RSSB CBHI PATIENT MEDICAL DOCUMENT <a style="color: blue; font-size: 10px;" href="../forms/frm_2.3_blank.php" target="_blank">Print empty forms</a></h1>
	  <form method=post action="rcv_patient.php" id='rcv_patient_frm'>
	  <input type='hidden' name='rcv_patient_btn' value='save' />
	  <input type=hidden id='insurance' name='insurance' value='<?php echo $insurance[0]['InsuranceNameID'] ?>' />
	  <input type=hidden id='patient_id' name='patient_id' />
	  <table border=0 style='background-color:#fff;'>
		<tr>
			<td colspan=2><!--Insurance:<span class=error-text>*</span>-->
			<?php 
			/* if($insurance_all){
				$i=0; 
				foreach($insurance_all as $in){ 
					echo "<label><input ".($i++ == 0 ?"checked":"")." type=radio onclick='if($(\"#in{$in['InsuranceNameID']}\").prop(\"checked\")){ $(\"#insurance\").val(\"{$in['InsuranceNameID']}\") } if(\"{$in['InsuranceName']}\" == \"CBHI\"){CBHI();} else if(\"{$in['InsuranceName']}\" == \"RSSB RAMA\"){RAMA();} else if(\"{$in['InsuranceName']}\" == \"MMI\"){MMI();} else if(\"{$in['InsuranceName']}\" == \"MEDIPLAN\"){MEDIPLAN();} else{ OTHER();}' id='in{$in['InsuranceNameID']}' name='insurance_' ".(@$_POST['insurance'] == $in['InsuranceNameID']?"selected":"").">{$in['InsuranceName']}</label>"; 
				} 
			} */
			?>
			<input id="patient_search_" type=hidden placeholder='Search' class='txtfield1' style='' />
			Consultation Date: <input id="date" type=text name='date' onblur='' class='txtfield1 date_on' onclick="ds_sh(this,'date')" style='width:150px;' value='<?php echo date("Y-m-d",time()) ?>' />
			</td>
			<!-- <td colspan="2">
				Patient ID: <input id="patientID" type=text name='patientID' class='txtfield1' style='width:200px;' placeholder="Patient ID" />
				<span class="info"></span>
			</td> -->
		</tr>
		<tr>
			<td class="found_link" colspan=4><!--<input id="doc_id" type=text name='doc_id' class='txtfield1' onclick="" style='' value='<?php echo date("Y-m-d",time()) ?>' />--></td>
		</tr>
		<tr class="patient_found_search">
			<td colspan="6">
			<a href='./force-selection.php' id=display_now rel="#overlay"><img src='../images/view.png' style='width:0px;' /></a>
	
			<div class="patient_found"></div></td>
		</tr>
		<tr>
			<td style="width: 250px; border:0px solid #000;">
				<table border=0 style='text-align:center; font-size:12px;'>
					<tr>
						<!--<td style='width:50px;'>D</td><td>S</td>-->
						<td style=' font-size:14px'>
							<label><input type=radio name='id_card_' checked value="InsuranceCardID"/>ID Card</label>/ 
							<label><input type=radio name='id_card_' value="applicationNumber"/>Application Number</label>
						</td>
					</tr>
				</table>
			</td>
			<td>
				Name:<span class=error-text>*</span>
			</td>
			<td>
				Age [<label class=as_link>yyyy-mm-dd or yyyy</label>]:
			</td>
			<td>
				Sex <span class=error-text>*</span>
			</td>
		</tr>
		<tr>
			<td>
			
			<input id="patient_search" autocomplete="off" type=hidden name='card_id' class='txtfield1' style='' />
				<table border=0 style='width:100%; text-align:center;'>
					<!--<tr>
						<td style='width:50px;'>D</td><td>S</td><td style='width:100px;'>P</td>
					</tr>-->
					<tr>
						<!--<td style='width:50px;'><input type=text autocomplete="off" onkeypress='return isNumberKey(event);' class='txtfield1' id=di style='width:97%; font-size:12px;' /></td>
						<td>
						/<input type=text class='txtfield1' autocomplete="off" id=se style='width:70%; font-size:12px;' /></td>-->
						<td style=''>
						<input type=text autocomplete="off"  class='txtfield1' id=pa style='width:100%; font-size:16px;' /></td>
					</tr>
				</table>
			
			</td>
			
			<td><input type=text name='name' autocomplete="off" onkeyup='if(familyChief()){$("#father").val($("#name").val());}' id=name class='txtfield1' style='' /></td>
			<td><input type=text name='age' autocomplete="off" onblur='' id=age class='txtfield1 date_on' onclick="ds_sh(this,'age')" style='' /></td>
			<td><label><input type=radio name=sex value="Female" required id=female>Female</label> <label><input type=radio name=sex value="Male" required id=male>Male</label></td>
			
		</tr>
		<tr>
			<td>
				
			</td>
			<!--<input id="patient_search" autocomplete="off" type=text name='card_id' class='txtfield1' style='' />
			<td><input type=text name='name' autocomplete="off" onkeyup='if(familyChief()){$("#father").val($("#name").val());}' id=name class='txtfield1' style='' /></td>
			<td><input type=text name='age' autocomplete="off" onblur='setTimeout("ds_hi()",1000)' id=age class='txtfield1' onclick="ds_sh(this,'age')" style='' /></td>
			<td><label><input type=radio name=sex value="Female" required id=female>Female</label> <label><input type=radio name=sex value="Male" required id=male>Male</label></td>
			-->
		</tr>
		<tr>
			<td class=h_label>
				House Manager ID:<label class=as_link >*</label>
				<span class=error-text>*</span>
			</td>
			<td class=h_label>
				House Manager Name:<label class=as_link onclick='$("#father").val($("#name").val())'>Self</label>
				<span class=error-text>*</span>
			</td>
			<td class=h_label>
				Phone Number
			</td>
			<td class=affection_location>
				
			</td>
			<td class=sex>
				
			</td>
		</tr>
		<tr>
			<td><input type=text name='fatherID' autocomplete="off" id=fatherID class='txtfield1' style='width: 100%;' /></td>
			<td><input type=text name='father' autocomplete="off" id=father class='txtfield1' style='' /></td>
			<td><input type=text name='phoneNumber' autocomplete="off" id=phoneNumber class='txtfield1' style='width: 100%;' /></td>
			
			<td class=affection_locationdata><!--<input type=text name=fcategory id=fcategory value=3 class=txtfield1 />--></td>
			<td class=sexdata><!--<input type=text name=fcategory id=fcategory value=3 class=txtfield1 />--></td>
		</tr>
		<tr>
			<td class=fcategory>
				Category<span class=error-text>*</span>
			</td>
		</tr>
		<tr>
			<td class=fcategorydata colspan=3>
				<input autocomplete="off" type="hidden" name=fcategory id=fcategory value='' class=txtfield1 />
				<label style="cursor:pointer;"><input type=radio onclick="$('#fcategory').val(1); $('#cat').removeAttr('checked');" name=cbhi_category value='1' id='cbhi_category1'>Category 1</label>
				<label style="cursor:pointer;"><input type=radio onclick="$('#fcategory').val(2); $('#cat').removeAttr('checked');" name=cbhi_category value='2' id='cbhi_category2'>Category 2</label>
				<label style="cursor:pointer;"><input type=radio onclick="$('#fcategory').val(3); $('#cat').removeAttr('checked');" name=cbhi_category value='3' id='cbhi_category3'>Category 3</label>
				<label style="cursor:pointer;"><input type=radio onclick="$('#fcategory').val(4); $('#cat').removeAttr('checked');" name=cbhi_category value='4' id='cbhi_category4'>Category 4</label>
			</td>
		</tr>
		<tr>
			<td style="padding-top: 20px;">Village</td><td>Cell</td><td>Sector</td><td>District</td>
		</tr>
		<tr>
			<td><input type=text name='village' autocomplete="off" id=village class="txtfield1" style='' /></td>
			<td><input type=text name='cell' autocomplete="off" id=cell class="txtfield1" style='' /></td>
			<td><input type=text name='sector' autocomplete="off" id=sector class="txtfield1" style='' /></td>
			<td><input type=text name='district' autocomplete="off" id=district class="txtfield1" style='' /></td>
		</tr>
		<tr>
			<td colspan=4><div class='address_search' style='max-height:150px; overflow:auto;'></div></td>
		</tr>
		<tr><td colspan=4 align=center><label id='steps'></label><input type=hidden name=next_frequency id=next_frequency value='1' /><input type=hidden name=document_list id=document_list value='' /><label><input type=checkbox id=update disabled> Update</label>
		<input type=submit id=save class="flatbtn-blu" name=rcv_patient value='Save' /> <input type=reset class="flatbtn-blu" id='reset_form' value='Clear' /></td></tr>
	  </table>
	  <input type=hidden id=update_ name=update value='' />
	  </form>
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
  <!-- make all links with the 'rel' attribute open overlays -->
<script>
function familyChief(){
	var family = /01$/;
	if(family.test($("#patient_search").val())){
		return true;
	}
	return false;
}

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

function receivePatient(id,ins=""){
	$(".patient_selected").load("receive_patient.php?key=" + id + "&ins=" + ins);
}

function addrSearch(level,key){
	$(".address_search").html("");
	$(".address_search").load("search_address.php?key=" + key + "&level=" + level);
}
$(document).ready(function(){
	$("#patientID").keydown(function(e){
		if(e.which == 13 ){
			return false;
		}
	});

	var docsearch = false;
	$("#patientID").keyup(function(e){
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
					$(".patient_found").load("search_patient.php?key="+$("#patientID").val().replace(" ","_") + "&ins=" + $("#insurance").val() + "&date=" + $("#date").val());

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
		$(".doc_selected").html("");
		$(".doc_found").html("<span style='position:absolute; top:112px; left:700px;' >Searching <img src='../images/ajax_clock_small.gif' /></span>");
		if(docsearch ==false){
			docsearch = true;
			setTimeout(function(e){
				$(".doc_found").load("reprint_doc.php?key="+$("#doc_search").val().replace(/ /g,"%20"));
				$("#info").html("");
				docsearch = false;
			}, 1000);
		}
		return e.preventDefault();
	});


	$(".uncheck_tm").click(function(){
		$("#pst").removeProp("checked");
	});
	$("#di").keydown(function(e){
		if(e.which == 191){
			nextField("di",4,"se");
			return false;
		}
		return true;

			
	});
	$("#pa").keyup(function(e){
		//setTimeout(function(e){
		$("#patient_search").val($("#pa").val());
		//nextField("pa",8,"name");
		//send search request if the cbhi code is completed
		if($("#pa").val().length >= 8){
			$("#patient_search").keyup();
		}
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
	$('#reset_form').click(function(){
		window.location="./create_cbhi.php";
	});
	var gender = "";
	$("#female").click(function(e){
		gender = "Female";
	});
	$("#male").click(function(e){
		gender = "Male";
	});
	var category___ = "";
	$("#cbhi_category1").click(function(e){
		category___ = "1";
	});
	$("#cbhi_category2").click(function(e){
		category___ = "2";
	});
	$("#cbhi_category3").click(function(e){
		category___ = "3";
	});
	$("#cbhi_category4").click(function(e){
		category___ = "4";
	});
	$('#save').click(function(e){ 
		e.preventDefault();
		// Here Preview the provided Information
		var str = "Provided Information are:\n\n";
		str += "Insurance Card: " + $("#pa").val() + "\n";
		str += "Patient Name: " + $("#name").val() + "\n";
		str += "Date of Birth: " + $("#age").val() + "\n";
		str += "Gender: " + gender+ "\n";
		str += "House Manager ID: " + $("#fatherID").val() + "\n";
		str += "House Manager Name: " + $("#father").val() + "\n";
		str += "Family Category: " + category___ + "\n";
		if(!confirm(str)){
			return;
		}
		
		//$('#save').attr("desabled",":true");
		$("#upload_out").html('');
		$("#upload_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
		$("#rcv_patient_frm").ajaxForm({ 
			target: '#upload_out'
		}).submit();
			
	});
	
	$(".patient_found_search").hide();
	//if the search button is clicked search the patient_found
	$(".patient_found").click(function(){
		;//$(".patient_found_search").hide(500);
	});
	$("#search").click(function(e){
		$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
		$(".patient_selected").html("");
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
	var searc_request_sent = false;
	$("#patient_search").keyup(function(e){
		var searchableData =  $("#patient_search").val().trim();
		if(!searc_request_sent && searchableData){
			searc_request_sent = true;
			setTimeout(function(){
				$(".found_link").html("");
				$("#patient_search_").val($("#patient_search").val());
				 
				$(".patient_found_search").show(500);
				$(".patient_selected").html("");
				$("#patient_id").val("");
					 
				$(".patient_found").load("search_patient.php?key="+$("#patient_search_").val().replace(/ /g,"_") + "&ins=" + $("#insurance").val());
				searc_request_sent = false;
			}, 1000);
		}
		// return e.preventDefault();
	});
	
	//set focus one the district code now
	$("#di").focus();
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
            wrap.load(this.getTrigger().attr("href") + "?key="+$("#patient_search_").val().replace(/ /g,"_") + "&ins=" + $("#insurance").val() );
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
            wrap.load(this.getTrigger().attr("href") + "&code=" + $("#patient_search").val() );
        }

    });
});
</script>

<script type="text/javascript">
$(function(){
	$("#receipt_number").focus(function(){
		$("#paid").attr("checked",":true");
		$("#receipt_number").removeAttr("readonly");
	});
	$("#paid").click(function(){
		$("#receipt_number").focus();
	});
	$("#not_paid,#pst").click(function(){
		$("#receipt_number").val("");
		$("#receipt_number").attr("readonly",":true");
	});
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
  $('body').mouseup(function(e) {
		//setTimeout(function(e){
		//var clicked = $(e.target);
		//console.log(clicked.is(".ds_tbl"));
		//var pattern = /ds_tbl/;
		/* if(pattern.test(clicked.parent().parent().parent()[0].is(".ds_tbl"))){
			console.log("Done");
		} */
		//console.log(clicked.parent().parent().parent()[0]);
			//alert(hide_calendar);
			/* if(hide_calendar ==true)
				ds_hi(); */
		//}, 100);
	});
	
	$(".ds_box").click(function(e){
		hide_calendar = false;
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