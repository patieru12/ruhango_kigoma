<?php
session_start();
require_once "../../../lib/db_function.php";
// Check if the current Consultant has a registerd to be used
$registerId = returnSingleField("SELECT id FROM sy_register WHERE consultantId='{$_SESSION['user']['UserID']}'", "id", true, $con);
if(!$registerId){
	echo "<script> window.location='../../se_select.php?msg=select the register please and service'; </script>";
	return;
}
// var_dump($_GET);
$patientID 		= PDB($_GET['patientID'], true, $con);

$sql = "SELECT 	a.userId,
				a.serviceYear,
				jan.usedMethod AS `Jan`,
				feb.usedMethod AS `Feb`,
				mar.usedMethod AS `Mar`,
				apr.usedMethod AS `Apr`,
				may.usedMethod AS `May`,
				jun.usedMethod AS `Jun`,
				jul.usedMethod AS `Jul`,
				aug.usedMethod AS `Aug`,
				sep.usedMethod AS `Sep`,
				oct.usedMethod AS `Oct`,
				nov.usedMethod AS `Nov`,
				dec.usedMethod AS `Dec`
				FROM (
					SELECT 	a.userId,
							SUBSTRING(a.date, 1, 4) AS serviceYear
							FROM pf_records AS a
							WHERE a.userId = '{$patientID}'
							GROUP BY serviceYear
							ORDER BY serviceYear DESC
				) AS a
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '01'
				) AS jan
				ON a.serviceYear = jan.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '02'
				) AS feb
				ON a.serviceYear = feb.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '03'
				) AS mar
				ON a.serviceYear = mar.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '04'
				) AS apr
				ON a.serviceYear = apr.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '05'
				) AS may
				ON a.serviceYear = may.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '06'
				) AS jun
				ON a.serviceYear = jun.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '07'
				) AS jul
				ON a.serviceYear = jul.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '08'
				) AS aug
				ON a.serviceYear = aug.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '09'
				) AS sep
				ON a.serviceYear = sep.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '10'
				) AS oct
				ON a.serviceYear = oct.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '11'
				) AS nov
				ON a.serviceYear = nov.serviceYear
				LEFT JOIN (
					SELECT 	a.userId,
							a.date AS serviceDate,
							b.abbreviation AS usedMethod,
							SUBSTRING(a.date, 1, 4) AS serviceYear,
							SUBSTRING(a.date, 6, 2) AS serviceMonth
							FROM pf_records AS a
							INNER JOIN pf_method AS b
							ON a.methodId = b.id
							WHERE a.userId = '{$patientID}'
							HAVING serviceMonth = '12'
				) AS `dec`
				ON a.serviceYear = `dec`.serviceYear
				ORDER BY serviceYear DESC
				LIMIT 5;
				";

$records = formatResultSet($rslt=returnResultSet($sql, $con), true, $con);
// var_dump($records);
if(is_array($records)){
	foreach($records AS $record){
		?>
		<h4>Family Planning Usage <?= $record["serviceYear"] ?></h4>
		<table class="table table-bordered">
			<thead>
				<tr>
					<?php
					foreach($month AS $monthNumber=>$monthName){
						echo "<th>".(substr($monthName, 0, 3))."</th>";
					}
					?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php
					foreach($month AS $monthNumber=>$monthName){
						echo "<td>".$record[substr($monthName, 0, 3)]."</td>";
					}
					?>
				</tr>
			</tbody>
		</table>
		<?php
	}
}
?>