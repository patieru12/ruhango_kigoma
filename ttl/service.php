<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("ttl" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con) || !is_numeric($_GET['service'])){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

	
	var_dump($_POST); //die;
	$error = "";
	
	if(@$_GET['delete'] && is_numeric($_GET['user'])){
		if(saveData("UPDATE se_name SET Status=0 WHERE ServiceNameID='{$_GET['user']}'",$con))
			$error = "<span class=error>Service Deactivated Successfuly</span>";
	}
	$data = null;
	
	if(@$_GET['update'] && is_numeric($_GET['user'])){
		$data = returnAllData("SELECT * FROM se_name WHERE ServiceNameID='{$_GET['user']}'",$con);
	}
	//var_dump($data);
	if (isset($_POST['save'])) {
		
		if(!@$_POST['director']){
			$error = "<span class=error>No Director found</span>";
			goto end_save;
		}
		if(!@$_POST['name']){
			$error = "<span class=error>No Service Name found</span>";
			goto end_save;
		}
		if(!@$_POST['code']){
			$error = "<span class=error>No Service Code found</span>";
			goto end_save;
		}
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['name']));
		$code  = mysql_real_escape_string(trim($_POST['code']));
		$director  = mysql_real_escape_string(trim($_POST['director']));
		//var_dump($name); die;
		//check if the form is the update version
		if(@$_POST['service_id'] && is_numeric($_POST['service_id'])){
			saveData("UPDATE se_name SET ServiceName='{$name}', ServiceCode='{$code}', DirectorID='{$director}' WHERE ServiceNameID='{$_POST['service_id']}'",$con);
		} else{
			if(returnSingleField($sql="SELECT ServiceNameID FROM se_name WHERE ServiceCode='{$code}'",$field="ServiceNameID",$data=true, $con)){
				$error = "<span class=error-text>The Code In Use!</span>";
			} else{
				//update the existing status
				//saveData("UPDATE md_price SET Status=0 WHERE MedecineNameID='{$name}' && Status=1",$con);
				//save new data
				if(saveData($sql="INSERT INTO se_name SET  ServiceName='{$name}', ServiceCode='{$code}', DirectorID='{$director}', Status=1",$con)){
					$error = "<span class=succees>New User Saved Now</span>";
				}
			}
		}
		end_save:
	}
//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category WHERE in_name.CategoryID=in_category.InsuranceCategoryID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
require_once "../lib2/cssmenu/ttl_header.html";
?>
  <div id="w">
    <div id="content">
      <h1 style='margin-top:-55px'><?= returnSingleField("SELECT CenterName FROM sy_center WHERE CenterID='{$_GET['service']}'","CenterName",true, $con); ?> SERVICE CONFIGURATION</h1>
      <b>
	  <?= $error ?>
	  <form action="./service.php?service=<?= $_GET['service'] ?>" method=post />
	  <?= @$data[0]['ServiceNameID']?"<input type=hidden name=service_id value='{$data[0]['ServiceNameID']}' />":"" ?>
	  <table class=frm>
		<tr>
			<td>Service Name</td>
			<td>Service Code</td>
			<td>Director of the service</td>
		<tr>
		<tr>
			<td><input type=text name=name class=txtfield1 value='<?= @$data[0]['ServiceName']?$data[0]['ServiceName']:@$_POST['name'] ?>' style='width:250px; font-size:16px;' /></td>
			<td><input type=text name=code class=txtfield1 value='<?= @$data[0]['ServiceCode']?$data[0]['ServiceCode']:@$_POST['code'] ?>' style='width:250px; font-size:16px;' /></td>
			
			<td>
				<?php
				$office = returnAllData("SELECT sy_users.* FROM sy_users, sy_post WHERE sy_users.PostID = sy_post.PostID && sy_post.PostCode='cst' && sy_users.CenterID='{$_GET['service']}'",$con);
				if($office){
					//var_dump($data);
					//var_dump($office);
					echo "<select name='director' class=txtfield1 style='width:250px; font-size:16px;'>";
					
					foreach($office as $of){
						//if(returnSingleField($sql="SELECT DirectorID FROM se_name WHERE DirectorID='{$of['UserID']}'","DirectorID",$data=true, $con))
							if($data[0]['DirectorID'] != $of['UserID']){
								if(returnSingleField($sql="SELECT DirectorID FROM se_name WHERE DirectorID='{$of['UserID']}'","DirectorID",$data=true, $con))
									continue;
							}
						echo "<option ".($data[0]['DirectorID'] == $of['UserID'] || @$_POST['director'] == $of['UserID']?'selected':"")." value='{$of['UserID']}'>{$of['Name']}</option>";
					}
					echo "</select>";
				} else{
					echo "<span class=error-text>No Director for any new service!</span>";
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
		$office = returnAllData("SELECT se_name.*, sy_users.Name, sy_users.Phone FROM se_name, sy_users, sy_center WHERE se_name.Status=1 && se_name.DirectorID=sy_users.UserID && sy_users.CenterID = sy_center.CenterID && sy_center.CenterID='{$_GET['service']}'",$con);
		if($office){
			//var_dump($office);
			echo "<table class=list><tr><th>#</th><th>Service</th><th>Code</th><th>Director</th><th colspan=2>&nbsp;</th></tr>";
			$i=1;
			foreach($office as $of){
				echo "<tr>";
				echo "<td>".($i++)."</td>";
				echo "<td>{$of['ServiceName']}</td>";
				echo "<td>{$of['ServiceCode']}</td>";
				echo "<td>{$of['Name']} - {$of['Phone']}</td>";
				//echo "<td>".returnSingleField("SELECT PostName FROM sy_post WHERE PostID='{$of['PostID']}'",$field="PostName",$data=true, $con)."</td>";
				//echo "<td>".returnSingleField("SELECT CenterName FROM sy_center WHERE CenterID='{$of['CenterID']}'",$field="CenterName",$data=true, $con)."</td>";
				echo "<td><a style='color:blue;' href='./service.php?service={$_GET['service']}&update=update&user={$of['ServiceNameID']}' />Update</a></td>";
				echo "<td><a style='color:blue;' href='./service.php?service={$_GET['service']}&delete=delete&user={$of['ServiceNameID']}' onclick='return confirm(\"Delete {$of['ServiceName']} From Possible Service List!\")' />Delete</a></td>";
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