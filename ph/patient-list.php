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
	$newResultsIDs = "";
	$availableResults = 0;
	if(@$_GET['filter'] && trim($_GET['keyword'])){
		$keyword = $_GET['keyword'];
		$time = time();
		$sql = "SELECT 	({$time} - COALESCE(b.TimeIn,0)) AS timeIn,
						b.TimeIn AS realTimeIn,
						a.Name AS patientName,
						a.DateofBirth AS dateOfBirth,
						a.Sex patientGender,
						b.PatientRecordID AS patientID,
						c.prescribedOn AS prescribedOn,
						b.dailyID AS DailyNumber
						FROM pa_info AS a
						INNER JOIN pa_records AS b
						ON a.PatientID = b.PatientID
						INNER JOIN (
							SELECT 	a.PatientRecordID AS PatientRecordID,
									MAX(c.presribedOn) AS prescribedOn
									FROM pa_records AS a
									INNER JOIN co_records AS b
									ON a.PatientRecordID = b.PatientRecordID
									INNER JOIN md_records AS c
									ON b.ConsultationRecordID = c.ConsultationRecordID
									WHERE c.received = 0
									GROUP BY PatientRecordID
						) AS c
						ON b.PatientRecordID = c.PatientRecordID
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
						b.PatientRecordID AS patientID,
						c.prescribedOn AS prescribedOn,
						b.dailyID AS DailyNumber
						FROM pa_info AS a
						INNER JOIN pa_records AS b
						ON a.PatientID = b.PatientID
						INNER JOIN (
							SELECT 	a.PatientRecordID AS PatientRecordID,
									MAX(c.presribedOn) AS prescribedOn
									FROM pa_records AS a
									INNER JOIN co_records AS b
									ON a.PatientRecordID = b.PatientRecordID
									INNER JOIN md_records AS c
									ON b.ConsultationRecordID = c.ConsultationRecordID
									WHERE a.DateIn = '{$date}' && 
										  c.received = 0
									GROUP BY PatientRecordID
						) AS c
						ON b.PatientRecordID = c.PatientRecordID
						WHERE DateIn = '{$date}' && b.DocStatus ='new'
						ORDER BY prescribedOn ASC, timeIn ASC";
	}
	//select all active diagnostic now
	$diagnostic = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	// var_dump($diagnostic);
	if($diagnostic){
		$data .= "<table border=0 class=list-1 style='width:100%;'>";
		for($i=0; $i<count($diagnostic);$i++){

			$ommit = false;
			$Waiting_Min = (int) ($diagnostic[$i]['timeIn']/60);
			$Waiting_sec = ($diagnostic[$i]['timeIn']%60);

			$Waiting_hour = (int) ($Waiting_Min/60);
			$Waiting_Min %= 60;
			$prescribed = explode(" ", $diagnostic[$i]['prescribedOn']);
			$prescriptionTime = "";
			if($prescribed[0] < date("Y-m-d", time())){
				$prescriptionTime = $diagnostic[$i]['prescribedOn'];
			} else if($prescribed[0] == date("Y-m-d", time())){
				$prescriptionTime = $prescribed[1];
			}
			$Waiting_time = ($Waiting_hour < 10?"0".$Waiting_hour:$Waiting_hour).":"
							.($Waiting_Min < 10?"0".$Waiting_Min:$Waiting_Min).":"
							.($Waiting_sec < 10?"0".$Waiting_sec:$Waiting_sec);
			if($Waiting_hour > 12){
				$display = ommitStringPart($str=$diagnostic[$i]['patientName'],$char_to_display=30,$ommit)."<br /><span class='error'>Received on ".date("Y-m-d H:i:s", $diagnostic[$i]['realTimeIn'])."</span>&nbsp;<span class='error'>Prescribed: ".$prescriptionTime."</span>";
			} else{
				$display = ommitStringPart($str=$diagnostic[$i]['patientName'],$char_to_display=30,$ommit)."<br /><span class='error'>Waiting Time: ".$Waiting_time."</span>&nbsp;<span class='error'>Prescribed: ".$prescriptionTime."</span>";
			}
			$data .= "<tr class='activePatient' id='myTr{$diagnostic[$i]['patientID']}' onclick='LoadProfile(\"{$diagnostic[$i]['patientID']}\")'>
				<td style='padding-bottom:5px; border-bottom: 1px solid #000;'>{$diagnostic[$i]['DailyNumber']}. ".$display."</td>
				</tr>";

			if(!preg_match("/myTr{$diagnostic[$i]['patientID']}/", $newResultsIDs)){
				if(strlen($newResultsIDs) > 0){
					$newResultsIDs .= ", ";
				}
				$newResultsIDs .= "#myTr{$diagnostic[$i]['patientID']}";
				$availableResults++;
			}
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
	firstNumber = "{$numberOfPatient}"
</script>
ALLDATA;


if(@$_GET['response'] == 'ajax'){
	if($availableResults < 10){
		$availableResults = "&nbsp;&nbsp;".$availableResults."&nbsp;&nbsp;";
	} else if($availableResults < 100){
		$availableResults = "&nbsp;".$availableResults."&nbsp;";
	}
	$newResultsStyleData = <<<STYLES
		<style>
			{$newResultsIDs} {
				background-image: url('../images/new.png');
				background-repeat: no-repeat;
				background-position: right;
			}
		</style>
		<span id=out class=success style='position:absolute; top:13%; left:27%; background-color:#fff; padding-top:10px; padding-bottom:10px; -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; border-color: #00ee00;outline: none; border-radius:50%;'>&nbsp;{$availableResults}&nbsp;</span>
STYLES;
	echo json_encode(array('foundPatient'=>$numberOfPatient, 'foundText'=>$data, "styleData"=>$newResultsStyleData));
} else{
	echo $data;
}