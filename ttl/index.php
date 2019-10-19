<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("ttl" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

	
	//var_dump($_POST); //die;
	$error = "";
	
	if(@$_GET['delete']){
		if(saveData("UPDATE sy_users SET Status=0 WHERE UserID='{$_GET['user']}'",$con))
			$error = "<span class=error>User Deactivated Successfuly</span>";
	}
	if (isset($_POST['save'])) {
		
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['name']));
		$phone  = mysql_real_escape_string(trim($_POST['phone']));
		$password  = sha1(mysql_real_escape_string(trim($_POST['password'])));
		$office  = mysql_real_escape_string(trim($_POST['office']));
		$center  = mysql_real_escape_string(trim($_POST['center']));
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT UserID FROM sy_users WHERE Phone='{$phone}'",$field="UserID",$data=true, $con)){
			$error = "<span class=error-text>The Phone Number In Use!</span>";
		} else{
			//update the existing status
			//saveData("UPDATE md_price SET Status=0 WHERE MedecineNameID='{$name}' && Status=1",$con);
			//save new data
			if(saveData($sql="INSERT INTO sy_users SET  Name='{$name}', Phone='{$phone}', Password='{$password}', PostID='{$office}', CenterID={$center}, Status=1",$con)){
				$error = "<span class=succees>New User Saved Now</span>";
			}
		}
	}
//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category WHERE in_name.CategoryID=in_category.InsuranceCategoryID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
require_once "../lib2/cssmenu/ttl_header.html";
?>
  <div id="w">
    <div id="content">
      <h1 style='margin-top:-55px'>USER CONFIGURATION</h1>
      <b>
	  <?= $error ?>
	  <form action="./users.php" method=post />
	  <table class=frm>
		<tr>
			<td>Name</td>
			<td>Phone Number</td>
			<td>Password</td>
			<td>Office</td>
			<td>Post</td>
		<tr>
		<tr>
			<td><input type=text name=name class=txtfield1 style='width:250px; font-size:16px;' /></td>
			<td><input type=text name=phone class=txtfield1 style='width:250px; font-size:16px;' /></td>
			<td><input type=password name=password class=txtfield1 style='width:250px; font-size:16px;' /></td>
			<td>
				<?php
				$office = returnAllDataInTable($tbl="sy_post",$con);
				if($office){
					//var_dump($office);
					echo "<select name='office' class=txtfield1 style='width:250px; font-size:16px;'>";
					
					foreach($office as $of){
						echo "<option value={$of['PostID']}>{$of['PostName']}</option>";
					}
					echo "</select>";
				}
				?>
			</td>
			<td>
				<?php
				$office = returnAllDataInTable($tbl="sy_center",$con);
				if($office){
					//var_dump($office);
					echo "<select name='center' class=txtfield1 style='width:250px; font-size:16px;'>";
					
					foreach($office as $of){
						echo "<option value='{$of['CenterID']}'>{$of['CenterName']}</option>";
					}
					echo "</select>";
				}
				?>
				</td>
		<tr>
		<tr>
			<td><input type=submit name='save' value='Save' class=flatbtn-blu style='font-size:16px;' /></td>
		</tr>
	  <table>
	  </form>
	  <?php
		$office = returnAllDataInTable($tbl="sy_users",$con, "WHERE Status=1");
		if($office){
			//var_dump($office);
			echo "<table class=list>";
			$i=1;
			foreach($office as $of){
				echo "<tr>";
				echo "<td>".($i++)."</td>";
				echo "<td>{$of['Name']}</td>";
				echo "<td>{$of['Phone']}</td>";
				echo "<td>".returnSingleField("SELECT PostName FROM sy_post WHERE PostID='{$of['PostID']}'",$field="PostName",$data=true, $con)."</td>";
				echo "<td>".returnSingleField("SELECT CenterName FROM sy_center WHERE CenterID='{$of['CenterID']}'",$field="CenterName",$data=true, $con)."</td>";
				echo "<td><a href='#' />Update</a></td>";
				echo "<td><a href='./users.php?delete=delete&user={$of['UserID']}' onclick='return confirm(\"Delete {$of['Name']} From Autholized Users\")' />Delete</a></td>";
				echo "</tr>";
			}
			echo "</table>";
		}
		?>
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

function findByInsurance(ins){
	//$(".patient_selected").html("");
	$(".patient_found").load("search_patient.php?key=" + ins);
	
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