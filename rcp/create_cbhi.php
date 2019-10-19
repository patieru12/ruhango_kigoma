<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";
//die;
//var_dump($_SESSION);
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceName='CBHI' && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$insurance_all = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name, sy_users WHERE sy_users.UserID = se_name.DirectorID && sy_users.CenterID = '{$_SESSION['user']['CenterID']}' && se_name.Status=1 ORDER BY ServiceCode ASC",$con),$multirows=true,$con);
$active = "create";
//var_dump($insurance); die;
require_once "../lib2/cssmenu/rcp_header.html";

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
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
		<tr>
			<td id="ds_calclass"></td>
		</tr>
	</table>
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
	<h1>RSSB CBHI PATIENT MEDICAL DOCUMENT <a style="color: blue; font-size: 10px;" href="../forms/frm_2.2_blank.php" target="_blank">Print empty forms</a></h1>
	<form method=post action="rcv_patient_cbhi.php" id='rcv_patient_frm'>
	  	<input type='hidden' name='rcv_patient_btn' value='save' />
	  	<input type=hidden id='insurance' name='insurance' value='<?php echo $insurance[0]['InsuranceNameID'] ?>' />
	  	<input type=hidden id='patient_id' name='patient_id' />
	  	<table border=0 style='background-color:#fff;'>
			<tr>
				<td colspan=2><!--Insurance:<span class=error-text>*</span>-->
				<input id="patient_search_" type=hidden placeholder='Search' class='txtfield1' style='' />
				Consultation Date: <input id="date" type=text name='date' onblur='' class='txtfield1 date_on' onclick="ds_sh(this,'date')" style='width:150px;' value='<?php echo date("Y-m-d",time()) ?>' />
				</td>
				<td colspan="2" >
					<!-- Daily Number:  --><input id="DailyNumberID" type=hidden name='DailyNumberID' class='txtfield1' style='width:200px;' placeholder="Daily Number" />
					<span class="info"></span>
				</td>
				<td rowspan="14" style="width:40%;">
					<input type="text" name="chbiPatientFilter" style="width:100%;" placeholder="Enter Name or Insurance ID" id="chbiPatientFilter" />
					<span id=bar style='position:relative; top:-20px; left:93%; height:0px;'>&nbsp;</span>
					<div id="cbhiPatientlist" style="border:1px solid #000; width: 100%; max-height: 395px; min-height: 390px; overflow: auto;">
						
					</div>
				</td>
			</tr>
			<tr>
				<td class="found_link" colspan=4><!--<input id="doc_id" type=text name='doc_id' class='txtfield1' onclick="" style='' value='<?php echo date("Y-m-d",time()) ?>' />--></td>
			</tr>
			<tr class="patient_found_search">
				<td colspan="6">
				<a href='./force-selection-cbhi.php' id=display_now rel="#overlay"><img src='../images/view.png' style='width:0px;' /></a>
		
				<div class="patient_found"></div></td>
			</tr>
			<tr>
				<td>
					<table border=0 style='width:200px; text-align:center; font-size:12px;'>
						<tr>
							<td style='width:100px; font-size:14px'>
								<label><input type=radio name='id_card_' checked value="InsuranceCardID"/>ID Card</label>/ 
								<label><input type=radio name='id_card_' value="applicationNumber"/>Appl. Number</label>
							</td>
						</tr>
					</table>
				</td>
				<td>Name:<span class=error-text>*</span></td><td>Age [<label class=as_link>yyyy-mm-dd or yyyy</label>]:</td><td>Sex<span class=error-text>*</span><!--<td>Family Chief ID Card:</td><td>Family Position:</td>-->
			</tr>
		<tr>
			<td>
			
			<input id="patient_search" autocomplete="off" type=hidden name='card_id' class='txtfield1' style='' />
				<table border=0 style='width:200px; text-align:center;'>
					<!--<tr>
						<td style='width:50px;'>D</td><td>S</td><td style='width:100px;'>P</td>
					</tr>-->
					<tr>
						<!--<td style='width:50px;'><input type=text autocomplete="off" onkeypress='return isNumberKey(event);' class='txtfield1' id=di style='width:97%; font-size:12px;' /></td>
						<td>
						/<input type=text class='txtfield1' autocomplete="off" id=se style='width:70%; font-size:12px;' /></td>-->
						<td style='width:100px;'>
						<input  type=text autocomplete="off"  class='txtfield1' id=pa style='width:100%; font-size:16px;' /></td>
					</tr>
				</table>
			
			</td>
			
			<td><input disabled="true" type=text name='name' autocomplete="off" onkeyup='if(familyChief()){$("#father").val($("#name").val());}' id=name class='txtfield1' style='' /></td>
			<td><input disabled="true" type=text name='age' autocomplete="off" onblur='' id=age class='txtfield1 date_on' onclick="ds_sh(this,'age')" style='' /></td>
			<td><label><input disabled="true" type=radio name=sex value="Female" required id=female>Female</label> 
				<label><input disabled="true" type=radio name=sex value="Male" required id=male>Male</label>
			</td>
			
		</tr>
		<!--<tr>
			<td>
				
			</td>
			<input id="patient_search" autocomplete="off" type=text name='card_id' class='txtfield1' style='' />
			<td><input type=text name='name' autocomplete="off" onkeyup='if(familyChief()){$("#father").val($("#name").val());}' id=name class='txtfield1' style='' /></td>
			<td><input type=text name='age' autocomplete="off" onblur='setTimeout("ds_hi()",1000)' id=age class='txtfield1' onclick="ds_sh(this,'age')" style='' /></td>
			<td><label><input type=radio name=sex value="Female" required id=female>Female</label> <label><input type=radio name=sex value="Male" required id=male>Male</label></td>
			
		</tr>-->
		<tr>

			<td class=h_label>
				House Manager ID:<label class=as_link >*</label>
				<span class=error-text>*</span>
			</td>
			<td class=h_label>House Manager<span class=error-text>*</span></td>

			<td class=h_label>
				Phone Number
			</td>
			<td class=affection_location></td>
			<!-- <td class=sex></td> -->
		</tr>
		<tr>
			
			<td><input type=text name='fatherID' autocomplete="off" id=fatherID class='txtfield1' style='width: 100%;' /></td>
			<td><input disabled="true" type=text name='father' autocomplete="off" id=father class='txtfield1' style='' /></td>
			<td><input type=text name='phoneNumber' autocomplete="off" id=phoneNumber class='txtfield1' style='width: 100%;' /></td>
			
			<td class=affection_locationdata></td>
			<!-- <td class=sexdata></td> -->
		</tr>

		<tr>
			<td class=fcategory colspan="4">
				Category<span class=error-text>*</span>
			</td>
		</tr>
		<tr style="border-bottom: 1px solid #000;">
			<td class=fcategorydata colspan=4>
				<input autocomplete="off" type=hidden name=fcategory id=fcategory value='' class=txtfield1 />
				<label style="cursor:pointer;"><input disabled="true" type=radio onclick="$('#fcategory').val(1); $('#cat').removeAttr('checked');" name=cbhi_category value='1' id='cbhi_category1'>Category 1</label>
				<label style="cursor:pointer;"><input disabled="true" type=radio onclick="$('#fcategory').val(2); $('#cat').removeAttr('checked');" name=cbhi_category value='2' id='cbhi_category2'>Category 2</label>
				<label style="cursor:pointer;"><input disabled="true" type=radio onclick="$('#fcategory').val(3); $('#cat').removeAttr('checked');" name=cbhi_category value='3' id='cbhi_category3'>Category 3</label>
				<label style="cursor:pointer;"><input disabled="true" type=radio onclick="$('#fcategory').val(4); $('#cat').removeAttr('checked');" name=cbhi_category value='4' id='cbhi_category4'>Category 4</label>
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
		<tr>
			<td colspan=4 style='padding-bottom:10px;'>Service<span class=error-text>*</span> <br />
			<?php 
			if($service){
				$i=0; 
				foreach($service as $in){
					$in['ServiceCode'] = str_replace("_","",$in['ServiceCode']);
					echo "<label style='padding:0 10px;'><input type=radio name='service' ".($in['ServiceCode'] == "CPC"?"":"")." ".($in['ServiceCode'] == "PST"?" rel=\"#overlay\" href=\"./pst_config.php?pst=check\" id=pst_ ":" class='uncheck_tm'")." value='{$in['ServiceNameID']}' id='{$in['ServiceCode']}'>{$in['ServiceCode']}</label>";
				} 
			} else{
				echo "<span class=error-text>No Service Available</span>";
			}
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
		<tr style="border-top: 1px solid #000;">
			<td class=tm_view colspan=4 style='border:0px solid #000; padding-top: 10px;'>
				<label>
					<input type=radio id='paid' onclick='if($("#cbhi_category1").prop("checked")){alert("Category 1 never pay TM"); $("#not_paid").prop("checked", "true"); return false } else { return true; }' name='tm' value='200' />200 RWF TM Paid
				</label>
				<input type=hidden id=receipt_number name=receipt_number class=txtfield1 style='width:160px;' value='78885454' placeholder='Enter Amount' />
				<label style='margin-left:20px;'>
					<input type=radio id='cmp' name='tm' value="COMPASSION" />Compassion
				</label>
				<label style='margin-left:20px;'>
					<input type=radio id='dettes' name='tm' value="DETTES" />Dettes
				</label>
				<label style='margin-left:20px;'>
					<input type=radio id='not_paid' name='tm' value="INDIGENT" />Indigent
				</label>
				<label style='margin-left:20px;'>
					<input onclick='if(!$("#pst_").prop("checked")){ alert("Pour PST Seulement"); return false; };' type=radio id='pst' name='tm' value="PANSEMENT" />AC PST
				</label>
				<label style='margin-left:20px;'>
					<input onclick='if($("#cbhi_category1").prop("checked") || $("#cbhi_category2").prop("checked") ){ return true; }else { alert("Pour Category 1 & 2 Seulement"); return false; };' type=radio id='cat' name='tm' value="CATEGORY" />CAT 1 & 2
				</label>
				<label style='margin-left:20px;'>
					<input onclick='' type=radio id='ac' name='tm' value="AC" />AC
				</label><br />
				<label><input type="checkbox" disabled="true" checked="" name="fiche_cbhi" id="fiche_cbhi" value="20">Prestation document Paid 20Rwf</label>
			</td>
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
	setTimeout(function(e){
		$("#cbhiPatientlist").load("./patient-list.php");

	},1500);

function familyChief(){
	var family = /01$/;
	if(family.test($("#patient_search").val())){
		return true;
	}
	return false;
}
function nextField(current, numbers, next){
	
	if($("#" + current).val().length == numbers){
		//add the slash to the patient_search
		var ch = next == "name"?"":"/";
		//console.log($("#patient_search").val());
		$("#patient_search").val($("#patient_search").val() + ch);
		$("#" + next).focus();
		$("#" + next).keyup();
	}
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
	$(".tm_view").html("<input type=hidden name=tm value=0 />");
	$(".fcategory").html("");
	$(".affection_location").html("");
	$(".affection_locationdata").html("");
	$(".h_label").html("House Manager <label class=as_link onclick='$(\"#father\").val($(\"#name\").val())'>Self</label> <label class=error-text>*</label>");
	$(".fcategorydata").html("<input name=fcategory id=fcategory type=hidden class=txtfield1 value=-1 />");
	$(".sex").html("");
	$(".sexdata").html("");
}
function receivePatient(id,ins=""){
	$(".patient_selected").load("receive_patient.php?key=" + id + "&ins=" + ins);
}
function addrSearch(level,key){
	$(".address_search").html("");
	$(".address_search").load("search_address.php?key=" + key + "&level=" + level);
}
$(document).ready(function(){

	var query_sent = false;
	$("#chbiPatientFilter").focus();
	
	$("#chbiPatientFilter").keyup(function(){
		$("#bar").html("<img src='../images/ajax_clock_small.gif' />");
		//now try send the filter query
		if(query_sent == false){
			query_sent = true;
			setTimeout(function(){
				if($("#chbiPatientFilter").val().trim != ""){
					$("#cbhiPatientlist").load("./patient-list.php?filter=true&keyword=" + $("#chbiPatientFilter").val().replace(/ /g,"%20"));
					$("#bar").html("");
				}
				query_sent = false;
			},1000);
		}
	});

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
					$(".patient_found").load("search_patient_cbhi.php?key="+$("#DailyNumberID").val().replace(" ","_") + "&ins=" + $("#insurance").val() + "&date=" + $("#date").val());

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
	$('#save').click(function(e){ 
			e.preventDefault();
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
		$(".patient_found").load("search_patient_cbhi.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val() + "&date=" + $("#date").val());
		$(".patient_selected").html("");
		return e.preventDefault();
	});
	$("#insurance").change(function(e){
		$(".patient_selected").html("");
		$(".patient_found").load("search_patient_cbhi.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val() + "&date=" + $("#date").val());
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
		$(".patient_found").load("search_patient_cbhi.php?key="+$("#patient_search_").val().replace(" ","_") + "&ins=" + $("#insurance").val() + "&date=" + $("#date").val());
		return e.preventDefault();
	});
	$("#patient_search").keyup(function(e){
		 $(".found_link").html("");
		 $("#patient_search_").val($("#patient_search").val());
		 
		$(".patient_found_search").show(500);
		$(".patient_selected").html("");
		$("#patient_id").val("");
			 
		$(".patient_found").load("search_patient_cbhi.php?key="+$("#patient_search_").val().replace(/ /g,"_") + "&ins=" + $("#insurance").val() + "&date=" + $("#date").val());
		return e.preventDefault();
	});
	
	//set focus one the district code now
	$("#pa").focus();
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