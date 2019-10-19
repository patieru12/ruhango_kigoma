<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}

require_once "../lib2/cssmenu/cst_header.html";
// var_dump($service);
	//var_dump($_POST); //die;
	$error = "";
	
	if(@$_GET['delete'] && is_numeric($_GET['user'])){
		if(saveData("UPDATE se_consultation SET Status=0 WHERE ServiceConsultationID='{$_GET['user']}'",$con))
			$error = "<span class=error>Consultation Deactivated Successfuly</span>";
	}
	
	if (isset($_POST['save'])) {
		
		//var_dump($_POST); die;
		$cst_id = mysql_real_escape_string(trim($_POST['cst_id']));
		$date  = mysql_real_escape_string(trim($_POST['date']));
		
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT ServiceConsultationID FROM se_consultation WHERE ServiceID='{$myService[0]['ServiceNameID']}' && ConsulationID='{$cst_id}' && Status=1",$field="ServiceConsultationID",$data=true, $con)){
			$error = "<span class=error-text>The Consultation allready assigned to the service!</span>";
		} else{
			//save new data
			if(saveData($sql="INSERT INTO se_consultation SET  ServiceID='{$myService[0]['ServiceNameID']}', ConsulationID='{$cst_id}', Date='{$date}', Status=1",$con)){
				$error = "<span class=success>New Consultation Recorded Now</span>";
			}
		}
	}
?>
	<div id='w' style='height: auto;'>
		<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
		   style="display: none;">
		<tr>
			<td id="ds_calclass"></td>
		</tr></table>
		
			<div id="content" style='height: auto;'>
				<h1 style='margin-top:-55px'>Available Consultation Services for <?= $myService[0]['ServiceName'] ?></h1>
				<b>
					<?= $error ?>
					<?php
					$diagno = returnAllDataInTable($tbl="co_diagnostic",$con, $condition = "");
					//var_dump($diagno);
					?>
					<label id=out_put></label>
	  <form action="./consultion_services.php" method=post />
	  <?= @$data[0]['ServiceNameID']?"<input type=hidden name=service_id value='{$data[0]['ServiceNameID']}' />":"" ?>
	  <table class=frm>
		<tr>
			<td>Consultation</td>
			<td>Date</td>
		<tr>
		<tr>
			<td>
			<?php
			//select available consultation in the system regardless the station
			$cst = returnAllDataInTable("co_category",$con);
				if($cst){
					//var_dump($data);
					//var_dump($office);
					echo "<select name='cst_id' class=txtfield1 style='width:250px; font-size:16px;'>";
					
					foreach($cst as $of){
						//if(returnSingleField($sql="SELECT DirectorID FROM se_name WHERE DirectorID='{$of['UserID']}'","DirectorID",$data=true, $con))
							if($data[0]['DirectorID'] != $of['UserID']){
								if(returnSingleField($sql="SELECT DirectorID FROM se_name WHERE DirectorID='{$of['ConsultationCategoryID']}'","DirectorID",$data=true, $con))
									continue;
							}
						echo "<option ".($data[0]['DirectorID'] == $of['UserID'] || @$_POST['director'] == $of['UserID']?'selected':"")." value='{$of['ConsultationCategoryID']}'>".($of['ConsultationCategoryName'] == "invisible"?"No Consultation":$of['ConsultationCategoryName'])."</option>";
					}
					echo "</select>";
				} else{
					echo "<span class=error-text>No Director for any new service!</span>";
				}
				?>
			</td>
			<td><input type=text name=date class=txtfield1 id=date onclick='ds_sh(this,"date");' value='<?= date("Y-m-d",time()) ?>' style='width:250px; font-size:16px;' /></td>
			
		<tr>
		<tr>
			<td><input type=submit name='save' value='Save' class=flatbtn-blu style='font-size:16px;' /></td>
		</tr>
	  <table>
	  </form>
	  <span class=success style='font-weight:bold; font-size:24px;'>
	  Service:<?= $myService[0]['ServiceName'] ?><br />
	  Code:<?= $myService[0]['ServiceCode'] ?><br />
	  Post: <?= @$myService['CenterName'] ?>
	  </span>
	  <?php
		$office = returnAllData("SELECT se_consultation.*, co_category.* FROM se_consultation, co_category WHERE se_consultation.ServiceID='{$myService[0]['ServiceNameID']}' && se_consultation.ConsulationID = co_category.ConsultationCategoryID && se_consultation.Status=1",$con);
		if($office){
			//var_dump($office);
			echo "<table class=list><tr><th>#</th><th>Consulation</th><th colspan=2>&nbsp;</th></tr>";
			$i=1;
			foreach($office as $of){
				echo "<tr>";
				echo "<td>".($i++)."</td>";
				echo "<td>".($of['ConsultationCategoryName'] == "invisible"?"No Consultation":$of['ConsultationCategoryName'])."</td>";
				echo "<td><a style='color:blue;' href='./consultion_services.php?delete=delete&user={$of['ServiceConsultationID']}' onclick='return confirm(\"Delete {$of['ConsultationCategoryName']} From Possible Consultation Service List in {$myService[0]['ServiceName']}!\")' />Delete</a></td>";
				echo "</tr>";
			}
			echo "</table>";
		}
		?>
				</b>
			</div>
	</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("#excel_file").change(function(){
			$("#act_frm").ajaxForm({
				target:"#out_put"
			}).submit();
		});
	});
</script>

</body>
</html>