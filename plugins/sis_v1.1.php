<?php
if(!isset($con) || $con == null){
	echo "<span class=error-text>SIS Plugin can not work without valid MySQL Connection</span>";
	die;
}

// var_dump($_POST);


function getDiagnosticRecords(&$resultSet, $minDateIn, $maxDateIn, $centerCondition, &$con){
	$sql = "SELECT 	a.DiagnosticRecordID AS DiagnosticRecordID,
					a.CaseType AS CaseType,
					a.DiagnosticID AS DiagnosticID,
					b.Date AS ConsultationDate,
					e.DateofBirth AS DateofBirth,
					e.Sex AS PatientGender,
					e.PatientID AS PatientID,
					(TO_DAYS(b.Date) - TO_DAYS(e.DateofBirth)) AS numberOfDays,
					(TO_SECONDS(b.Date) - TO_SECONDS(e.DateofBirth)) AS numberOfSeconds
					FROM co_diagnostic_records AS a
					INNER JOIN co_records AS b
					ON a.ConsulationRecordID = b.ConsultationRecordID
					INNER JOIN pa_records AS c
					ON b.PatientRecordID = c.PatientRecordID
					INNER JOIN co_diagnostic AS d
					ON a.DiagnosticID = d.DiagnosticID
					INNER JOIN sy_users
					ON c.ReceptionistID = sy_users.UserID
					INNER JOIN pa_info AS e
					ON c.PatientID = e.PatientID
					WHERE b.Date >= '{$minDateIn}' && 
						  b.Date <= '{$maxDateIn}' && 
						  {$centerCondition}
					";
	$sql = "SELECT 	a.*,
					ROUND((a.numberOfDays / 7),1) AS numberOfWeeks,
					ROUND((a.numberOfDays / 30),1) AS numberOfMonth
					FROM ({$sql}) AS a";
	$sql = "SELECT 	a.*,
					ROUND((a.numberOfMonth/12), 1) AS numberOfYears
					FROM ({$sql}) AS a";
	// echo $sql;
	$resultSet = formatResultSet($rslt=returnResultSet($sql, $con), true, $con);
}

function getDiagnosticUsed(&$resultSet, $minDateIn, $maxDateIn, $centerCondition, &$con){
	$sql = "SELECT 	a.DiagnosticID AS DiagnosticID,
					d.DiagnosticName AS DiagnosticName
					FROM co_diagnostic_records AS a
					INNER JOIN co_records AS b
					ON a.ConsulationRecordID = b.ConsultationRecordID
					INNER JOIN pa_records AS c
					ON b.PatientRecordID = c.PatientRecordID
					INNER JOIN co_diagnostic AS d
					ON a.DiagnosticID = d.DiagnosticID
					INNER JOIN sy_users
					ON c.ReceptionistID = sy_users.UserID
					INNER JOIN pa_info AS e
					ON c.PatientID = e.PatientID
					WHERE b.Date >= '{$minDateIn}' && 
						  b.Date <= '{$maxDateIn}' && 
						  {$centerCondition}
					GROUP BY DiagnosticID
					ORDER BY DiagnosticName
					";
	// echo $sql;
	$resultSet = formatResultSet($rslt=returnResultSet($sql, $con), true, $con);
}

function getOrganizedData(&$diagnostic, &$resultSet, &$returnData, &$conditions){
	// var_dump($diagnostic[0]);
	$genderArray = ["Male", "Female"];
	$EpisodeArray = ["NC", "AC"];

	$returnData = [];
	foreach($diagnostic AS $diag){
		if(!isset($returnData[$diag['DiagnosticID']])){
			$returnData[$diag['DiagnosticID']] = [];
		}

		foreach($conditions AS $key=>$condition){
			// echo $key;
			// echo "Column to Search in ".$columnName." ";
			if(!isset($returnData[$diag['DiagnosticID']][$key])){
				$returnData[$diag['DiagnosticID']][$key] = [];
			}

			foreach($genderArray AS $gender){
				if(!isset($returnData[$diag['DiagnosticID']][$key][$gender])){
					$returnData[$diag['DiagnosticID']][$key][$gender] = [];
				}

				foreach($EpisodeArray AS $episode){
					if(!isset($returnData[$diag['DiagnosticID']][$key][$gender][$episode])){
						$returnData[$diag['DiagnosticID']][$key][$gender][$episode] = 0;
					}
				}
			}
			// echo "<hr />";
		}
	}

	// echo "<pre>"; var_dump($returnData);
	foreach($resultSet AS $patient){
		$episodeType = $patient['CaseType']?"AC":"NC";
		$genderToUse = ucfirst(strtolower($patient["PatientGender"]));
		$diagID = $patient['DiagnosticID'];

		// echo $genderToUse."&nbsp; &nbsp;".$diagID."&nbsp; &nbsp;".$patient["PatientID"]."&nbsp;&nbsp;";
		// echo $genderToUse;
		// echo $patient["PatientID"]."........: ";
		foreach($conditions AS $key=>$condition){
			// echo $key;

			$columnName = "numberOf".$condition['prefix'];
			// echo "&nbsp;&nbsp;".$columnName."&nbsp;&nbsp;".$patient[$columnName];
			// echo "<br />Condition Minimum: ".$condition['min']."&nbsp;&nbsp;Condition Maximum: ".$condition['max'];
			if($condition['range'] == 0){
				// Check the program has a minimum bound
				if($condition['min'] >= 0 && $condition['max'] >= 0){
					if($patient[$columnName] >= $condition['min'] && $patient[$columnName] <= $condition['max']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "1. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "[{$condition['min']}, {$condition['max']}]";
					}
				} else if($condition['min'] < 0 && $condition['max'] >= 0){
					if($patient[$columnName] <= $condition['max']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "2. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "]{$condition['min']}, {$condition['max']}]";
					}
				} else if($condition['min'] >= 0 && $condition['max'] < 0){
					if($patient[$columnName] >= $condition['min']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "3. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "[{$condition['min']}, {$condition['max']}[";
					}
				}
			} else if($condition['range'] == 1){
				// Check the program has a minimum bound
				if($condition['min'] >= 0 && $condition['max'] >= 0){
					if($patient[$columnName] >= $condition['min'] && $patient[$columnName] <= $condition['max']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "1. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "[{$condition['min']}, {$condition['max']}]";
					}
				} else if($condition['min'] < 0 && $condition['max'] >= 0){
					if($patient[$columnName] <= $condition['max']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "2. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "]{$condition['min']}, {$condition['max']}]";
					}
				} else if($condition['min'] >= 0 && $condition['max'] < 0){
					if($patient[$columnName] >= $condition['min']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "3. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "[{$condition['min']}, {$condition['max']}[";
					}
				}
			} else if($condition['range'] == 2){
				// Check the program has a minimum bound
				if($condition['min'] >= 0 && $condition['max'] >= 0){
					if($patient[$columnName] >= $condition['min'] && $patient[$columnName] < $condition['max']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "1. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "[{$condition['min']}, {$condition['max']}]";
					}
				} else if($condition['min'] < 0 && $condition['max'] >= 0){
					if($patient[$columnName] < $condition['max']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "2. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "]{$condition['min']}, {$condition['max']}]";
					}
				} else if($condition['min'] >= 0 && $condition['max'] < 0){
					if($patient[$columnName] >= $condition['min']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "3. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "[{$condition['min']}, {$condition['max']}[";
					}
				}
			} else if($condition['range'] == 3){
				// Check the program has a minimum bound
				if($condition['min'] >= 0 && $condition['max'] >= 0){
					if($patient[$columnName] > $condition['min'] && $patient[$columnName] < $condition['max']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "1. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "[{$condition['min']}, {$condition['max']}]";
					}
				} else if($condition['min'] < 0 && $condition['max'] >= 0){
					if($patient[$columnName] < $condition['max']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "2. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "]{$condition['min']}, {$condition['max']}]";
					}
				} else if($condition['min'] >= 0 && $condition['max'] < 0){
					if($patient[$columnName] > $condition['min']){
						$returnData[$diagID][$key][$genderToUse][$episodeType]++;
						// echo "3. ".$diagID." {$key} {$genderToUse} {$episodeType} ";
						// echo "[{$condition['min']}, {$condition['max']}[";
					}
				}
			}
			// echo "<hr />";
		}
		// break;
		// echo "<hr />";
	}
}
?>