<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("prc" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$name = $_GET['name'];
// Get the Next Patient ID
$sql = "SELECT 	*
				FROM pa_info AS a
				WHERE a.Name LIKE('%{$name}%')
				";
// echo $sql;
$patients = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
// var_dump($lastID);
// var_dump($lastID);
// echo $lastID;
if(is_array($patients)){
	?>
	<table border=1 style="width: 100%;">
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Gender</th>
				<th>Age</th>
				<th>House Holder</th>
			</tr>
		</thead>
		<tbody class="myBody">
			<?php
			$i=0;
			foreach ($patients as $value) {
				$jsString = "";
				$jsString = <<<STR
					$("#patientIDSearch").val("{$value['PatientID']}");
					$("#patientName").val("{$value['Name']}");
STR;
				echo "<tr id='id{$i}' onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\"); {$jsString}'>";
					echo "<td></td>";
					echo "<td>{$value['Name']}</td>";
					echo "<td>{$value['Sex']}</td>";
					echo "<td>{$value['DateofBirth']}</td>";
					echo "<td>{$value['FamilyCode']}</td>";
				echo "</tr>";
				$i++;
			}
			?>
		</tbody>
	</table>

	<div class="styling"></div>
	<style type="text/css">
		.myBody tr:hover{
			background: #e5e5e3;
			cursor: pointer;
		}
	</style>
	<?php
} else{
	?>
	No Patient found under <b><?= $name ?></b>
	
	<?php
}
?>
<br />
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