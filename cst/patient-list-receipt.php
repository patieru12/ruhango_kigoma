<?php
	session_start();
	require_once "../lib/db_function.php";
	//var_dump($_GET);
	$data = <<<ALLDATA
	<style>
		.list{
			width:100%;
		}
		.list tr:hover{
			background-color:#ddd;
			
		}
	</style>
ALLDATA;
	$sql = "";
	if(@$_GET['filter'] && trim($_GET['keyword'])){
		$keyword = $_GET['keyword'];
		$time = time();
		$sql = "SELECT 	({$time} - COALESCE(b.TimeIn,0)) AS timeIn,
						b.TimeIn AS realTimeIn,
						a.Name AS patientName,
						a.DateofBirth AS dateOfBirth,
						a.Sex patientGender,
						b.PatientRecordID AS patientID
						FROM pa_info AS a
						INNER JOIN pa_records AS b
						ON a.PatientID = b.PatientID
						WHERE a.PatientID LIKE('%{$keyword}%') || a.Name LIKE('%{$keyword}%') || b.InsuranceCardID LIKE('%{$keyword}%')
						ORDER BY timeIn ASC";
		// return;
	} else {
		// Here Search for Patient
		$date = date("Y-m-d", time());
		$time = time();
		$sql = "SELECT 	({$time} - COALESCE(b.TimeIn,0)) AS timeIn,
						b.TimeIn AS realTimeIn,
						a.Name AS patientName,
						a.DateofBirth AS dateOfBirth,
						a.Sex patientGender,
						b.PatientRecordID AS patientID
						FROM pa_info AS a
						INNER JOIN pa_records AS b
						ON a.PatientID = b.PatientID
						WHERE DateIn = '{$date}' && b.DocStatus ='new'
						ORDER BY timeIn ASC";
	}
	//select all active diagnostic now
	$diagnostic = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	// var_dump($diagnostic);
	if($diagnostic){
		$data .= "<table border=0 class=list-1 style='width:100%;'>";
		for($i=0; $i<count($diagnostic); $i++){
			$ommit = false;
			$Waiting_Min = (int) ($diagnostic[$i]['timeIn']/60);
			$Waiting_sec = ($diagnostic[$i]['timeIn']%60);

			$Waiting_hour = (int) ($Waiting_Min/60);
			$Waiting_Min %= 60;
			$Waiting_time = ($Waiting_hour < 10?"0".$Waiting_hour:$Waiting_hour).":"
							.($Waiting_Min < 10?"0".$Waiting_Min:$Waiting_Min).":"
							.($Waiting_sec < 10?"0".$Waiting_sec:$Waiting_sec);
			if($Waiting_hour > 12){
				$display = ommitStringPart($str=$diagnostic[$i]['patientName'],$char_to_display=30,$ommit)."<br /><span class='error'>Received on ".date("Y-m-d H:i:s", $diagnostic[$i]['realTimeIn'])."</span>";
			} else{
				$display = ommitStringPart($str=$diagnostic[$i]['patientName'],$char_to_display=30,$ommit)."<br /><span class='error'>Waiting Time: ".$Waiting_time."</span>";
			}
			$data .= "<tr class='activePatient' id='myTr{$diagnostic[$i]['patientID']}' onclick='LoadProfile(\"{$diagnostic[$i]['patientID']}\")'>
				<td style='padding-bottom:5px; border-bottom: 1px solid #000;'>".$display."</td>
				</tr>";
			//echo "<div style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">".$display." {$diagnostic[$i]['Quantity']}</div>";
		}
		$data .= "</table>";
	} else{
		$data .= "<span class=error-text >No Patient on the List</span>";
	}

	$numberOfPatient = count($diagnostic);
$data .= <<<ALLDATA
<style type="text/css">
	.activeP{
		background-color: #efefef;
	}
</style>
<script type="text/javascript">
	function LoadProfile(patientID){
		// Load the Diagnostic Information from here
		$(".activePatient").removeClass("activeP");
		$("#myTr" + patientID).addClass("activeP");
		$("#receiptcontent").load("./receipt-content.php?patientID=" + patientID);

	}
</script>
ALLDATA;
if(@$_GET['records']){
	$data .= <<<AUTOCLICK
	<script type="text/javascript">
		setTimeout(function(){
			LoadProfile({$_GET['records']});
		}, 100);
	</script>
AUTOCLICK;
}
if(@$_GET['response'] == 'ajax'){
	echo json_encode(array('foundPatient'=>$numberOfPatient, 'foundText'=>$data));
} else{
	echo $data;
}