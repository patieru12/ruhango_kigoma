<?php
	session_start();
	// var_dump($_SESSION);
	require_once "../../lib/db_function.php";
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
	$newResultsIDs = "";
	$availableResults = 0;
// var_dump($_GET);
	if(@$_GET['filter'] && trim($_GET['keyword'])){
		$time = time();
		$d = mysql_query("SELECT Date FROM md_price WHERE Date <= '".(date("Y-m-d",time()))."' ORDER BY Date DESC LIMIT 0,1");
		$active_date = mysql_fetch_assoc($d)['Date'];
		$keyword = PDB($_GET['keyword'], true, $con);
		$sql = "SELECT 	({$time} - COALESCE(b.TimeIn,0)) AS timeIn,
						a.Name AS patientName,
						a.DateofBirth AS dateOfBirth,
						a.Sex patientGender,
						b.PatientRecordID AS patientID,
						e.status AS status,
						f.InsuranceName AS InsuranceName,
						b.TimeIn AS realTimeIn,
						d.ServiceCode AS ServiceCode,
						g.RegisterNumber AS RegisterNumber,
						b.DateIn AS patientDateIn
						FROM pa_info AS a
						INNER JOIN pa_records AS b
						ON a.PatientID = b.PatientID
						INNER JOIN se_records AS c
						ON b.PatientRecordID = c.PatientRecordID
						INNER JOIN se_name AS d
						ON c.ServiceNameID = d.ServiceNameID
						INNER JOIN co_records AS e
						ON b.PatientRecordID = e.PatientRecordID
						INNER JOIN in_name AS f
						ON b.InsuranceNameID = f.InsuranceNameID
						INNER JOIN (
							SELECT 	a.ConsultationRecordID AS ConsultationRecordID,
									CONCAT(COALESCE(b.registerCode, ''), ': ',a.RegisterNumber, ' ') AS RegisterNumber
									FROM co_records AS a
									LEFT JOIN sy_register AS b
									ON a.registerId = b.id
									WHERE b.consultantId = '{$_SESSION['user']['UserID']}' ||
										  a.registerId IS NULL ||
										  a.transfer LIKE('%{$_SESSION['user']['ServiceID']}, %')
						) AS g
						ON e.ConsultationRecordID = g.ConsultationRecordID
						WHERE 
							  (a.PatientID LIKE('%{$keyword}%') || a.Name LIKE('%{$keyword}%') || b.InsuranceCardID LIKE('%{$keyword}%'))
						ORDER BY timeIn ASC";

		$diagnostic = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
		//echo $sql;
		if($diagnostic){
			$data .= "<table border=0 class=list-1 style='width:100%;'>";
			for($i=0; $i<count($diagnostic); $i++){
				// $stock = formatResultSet($rslt=returnResultSet($sql="SELECT md_stock.* FROM md_stock WHERE md_stock.MedicineNameID ='".PDB($diagnostic[$i]['MedecineNameID'],true,$con)."' ORDER BY Quantity ASC",$con),$multirows=false,$con);
				$insurance = strtolower($diagnostic[$i]['InsuranceName']);
				$ommit = false;
				$Waiting_Min = (int) ($diagnostic[$i]['timeIn']/60);
				$Waiting_sec = ($diagnostic[$i]['timeIn']%60);

				$Waiting_hour = (int) ($Waiting_Min/60);
				$Waiting_Min %= 60;
				$Waiting_time = ($Waiting_hour < 10?"0".$Waiting_hour:$Waiting_hour).":"
								.($Waiting_Min < 10?"0".$Waiting_Min:$Waiting_Min).":"
								.($Waiting_sec < 10?"0".$Waiting_sec:$Waiting_sec);
				$display = ommitStringPart($str=$diagnostic[$i]['patientName'],$char_to_display=30,$ommit)."<br /><span class='error'>Waiting Time: ".$Waiting_time."</span>".($insurance == "private" && $diagnostic[$i]['status'] == 0?" <span class='error'>Not Paid</span>":"");
				
				if($Waiting_hour > 12){
					$display = ommitStringPart($str=$diagnostic[$i]['patientName'],$char_to_display=30,$ommit)."<br /><span class='error'>Received on ".date("Y-m-d H:i:s", $diagnostic[$i]['realTimeIn'])."</span>";
				} else{
					$display = ommitStringPart($str=$diagnostic[$i]['patientName'],$char_to_display=30,$ommit)."<br /><span class='error'>Waiting Time: ".$Waiting_time."</span>";
				}
				$display = $diagnostic[$i]['RegisterNumber'].$display;
				$display .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$diagnostic[$i]['ServiceCode'];
				$data .= "<tr class='activePatient ".($diagnostic[$i]["patientDateIn"] != date("Y-m-d", time())?"notForDay":"")."' id='myTr{$diagnostic[$i]['patientID']}' onclick='".($insurance == "private" && $diagnostic[$i]['status'] == 0?"alert(\"Patient Did not Pay Consultation Fees\")":"LoadProfile(\"{$diagnostic[$i]['patientID']}\")")."'>
					<td style='padding-bottom:5px; border-bottom: 1px solid #000;'>".$display."</td>
					</tr>";
			}
			$data .= "</table>";
		} else{
			$data .= "<span class=error-text >No Patient Found</span>";
		}
		// return;
	} else {
		// Here Search for Patient
		$date = date("Y-m-d", time());
		$time = time();
		$sql = "SELECT 	({$time} - COALESCE(b.TimeIn,0)) AS timeIn,
						a.Name AS patientName,
						a.DateofBirth AS dateOfBirth,
						a.Sex patientGender,
						b.PatientRecordID AS patientID,
						e.status AS status,
						f.InsuranceName AS InsuranceName,
						b.TimeIn AS realTimeIn,
						e.ConsultationRecordID AS ConsultationRecordID,
						d.ServiceCode AS ServiceCode,
						g.RegisterNumber AS RegisterNumber
						FROM pa_info AS a
						INNER JOIN pa_records AS b
						ON a.PatientID = b.PatientID
						INNER JOIN se_records AS c
						ON b.PatientRecordID = c.PatientRecordID
						INNER JOIN se_name AS d
						ON c.ServiceNameID = d.ServiceNameID
						INNER JOIN co_records AS e
						ON b.PatientRecordID = e.PatientRecordID
						INNER JOIN in_name AS f
						ON b.InsuranceNameID = f.InsuranceNameID
						INNER JOIN (
							SELECT 	a.ConsultationRecordID AS ConsultationRecordID,
									CONCAT(COALESCE(b.registerCode, ''), ': ',a.RegisterNumber, ' ') AS RegisterNumber
									FROM co_records AS a
									LEFT JOIN sy_register AS b
									ON a.registerId = b.id
									WHERE a.Date = '{$date}' &&
										  (b.consultantId = '{$_SESSION['user']['UserID']}' ||
										  a.registerId IS NULL ||
										  a.transfer LIKE('%{$_SESSION['user']['ServiceID']}, %') )
						) AS g
						ON e.ConsultationRecordID = g.ConsultationRecordID
						WHERE b.DateIn = '{$date}' && 
							  (d.ServiceNameID = '{$_SESSION['user']['ServiceID']}' ||
							  e.transfer LIKE ('%{$_SESSION['user']['ServiceID']}, %') )
						ORDER BY timeIn ASC";
		// echo $sql;
		$sql = "SELECT 	a.serialNumber AS RegisterNumber,
						a.dateIn AS dateIn,
						b.Name AS patientName,
						b.Sex AS Sex,
						b.DateofBirth AS DateofBirth,
						b.phoneNumber AS phoneNumber,
						a.id AS patientID,
						'' AS methodUsed,
						a.status AS status
						FROM pf_user AS a
						INNER JOIN pa_info AS b
						ON a.patientId = b.PatientID
						WHERE a.status = 1";
		//echo time();
		//select all active diagnostic now
		$diagnostic = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
		// var_dump($diagnostic);
		if($diagnostic){
			$data .= "<table border=0 class=list-1 style='width:100%;'>";
			for($i=0; $i<count($diagnostic); $i++){
				
				$ommit = false;
				$Waiting_time = $diagnostic[$i]['dateIn'];
				$display = ommitStringPart($str=$diagnostic[$i]['patientName'],$char_to_display=30,$ommit)."<br />".$diagnostic[$i]['RegisterNumber']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='error'>Start: ".$Waiting_time."</span>";
				
				// $display = $display;
				$display .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$diagnostic[$i]['methodUsed'];
				$data .= "<tr class='activePatient' id='myTr{$diagnostic[$i]['patientID']}' onclick='".($diagnostic[$i]['status'] == 0?"alert(\"User Has Stoped using Family Planning Method on\")":"LoadProfile(\"{$diagnostic[$i]['patientID']}\")")."'>
					<td style='padding-bottom:5px; border-bottom: 1px solid #000;'>".$display."</td>
					</tr>";
				//echo "<div style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">".$display." {$diagnostic[$i]['Quantity']}</div>";
			}
			$data .= "</table>";
		} else{
			$data .= "<span class=error-text >No Patient on the List</span>";
		}
	}

	$numberOfPatient = count($diagnostic);
$data .= <<<ALLDATA
<style type="text/css">
	.activeP{
		background-color: #efefef;
	}
	.notForDay{
		background-color: #fea587;
	}
</style>
<script type="text/javascript">
	function LoadProfile(patientID){
		// Load the Diagnostic Information from here
		$(".activePatient").removeClass("activeP");
		$("#myTr" + patientID).addClass("activeP");
		$("#patientIdentification").load("./pa/patient_identification.php?patientID=" + patientID);
		$("#usageHistory").load("./pa/usage_summary.php?patientID=" + patientID);
	}
	firstNumber = "{$numberOfPatient}"
</script>
ALLDATA;

// var_dump($_REQUEST);
if(@$_GET['response'] == 'ajax'){
	echo json_encode(array('foundPatient'=>$numberOfPatient, 'foundText'=>$data));
} else{
	echo $data;
}