<?php
session_start();
// var_dump($_GET);
require_once "../lib/db_function.php";
if("com" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

$requiredPost = str_replace("_", ", ",preg_replace("/^_/", "", $_GET["post"]));
// echo $requiredPost;
$monthPattern = $_GET["year"]."-".$_GET['month'];
// echo $monthPattern;
// GET THE LIST OF MEDICINES PRISCRIBED TO PATIENT OF THE SELECTED MONTH

$sql = "SELECT 	d.Date
				FROM pa_records AS a
				INNER JOIN co_records AS b
				ON a.PatientRecordID = b.PatientRecordID
				INNER JOIN md_records AS c
				ON b.ConsultationRecordID = c.ConsultationRecordID
				INNER JOIN md_price AS d
				ON c.MedecinePriceID  = d.MedecinePriceID
				WHERE a.DateIn LIKE('{$monthPattern}%')
				GROUP BY d.Date
				";

$validDates = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$emptyColumns = "";
$subQuery 		= "";
$subQueryChars	= 'f';
$validCounter 	= 0;

foreach($validDates AS $dateArray){
	$date = $dateArray['Date'];
	if($validCounter++ >0){
		$emptyColumns .= ", ";
	}
	
	$emptyColumns .= "COALESCE({$subQueryChars}.Amount, '') AS `{$date}`";
	$subQuery		.= " LEFT JOIN md_price AS ".$subQueryChars;
	$subQuery 		.= " ON e.MedecineNameID = ".($subQueryChars).".MedecineNameID && ".($subQueryChars++).".Date='{$date}' ";
}

$sql = "SELECT 	d.MedecineNameID,
				e.MedecineName,
				e.MedecineCategorID,
				aa.MedecineCategoryName,
				{$emptyColumns}
				FROM pa_records AS a
				INNER JOIN co_records AS b
				ON a.PatientRecordID = b.PatientRecordID
				INNER JOIN md_records AS c
				ON b.ConsultationRecordID = c.ConsultationRecordID
				INNER JOIN md_price AS d
				ON c.MedecinePriceID  = d.MedecinePriceID
				INNER JOIN md_name AS e
				ON d.MedecineNameID = e.MedecineNameID
				INNER JOIN md_category AS aa
				ON e.MedecineCategorID = aa.MedecineCategoryID
				{$subQuery}
				WHERE a.DateIn LIKE('{$monthPattern}%')
				GROUP BY d.MedecineNameID
				ORDER BY e.MedecineCategorID ASC, e.MedecineName ASC
				";
$validMedicines = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

// var_dump($validMedicines)
?>
<div style='max-height:85%; padding-top:5px; border:0px solid #000; overflow:auto;'>
	<table border="1">
		<thead>
			<tr>
				<?php
				foreach($validMedicines[0] AS $fieldName=>$fieldValue){
					if(in_array($fieldName, array("MedecineCategorID", "MedecineCategoryName"))){
						continue;
					}
					echo "<th>";
					if($fieldName == "MedecineNameID"){
						echo "#";
					} else if($fieldName == "MedecineName"){
						echo "Medicine Name";
					} else{
						echo $fieldName;
					}
					echo "</th>";
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			$i=1;
			$currentCategory = "";
			foreach($validMedicines AS $mdTarif){
				if($currentCategory != $mdTarif['MedecineCategorID']){
					echo "<tr style='background-color:#b8b8b8; color: #ffffff; font-weight: bold;' >";
						echo "<th colspan='".(count($mdTarif) - 2)."'>".$mdTarif["MedecineCategoryName"]."</th>";
					echo "<tr>";
					$currentCategory = $mdTarif['MedecineCategorID'];
				}
				echo "<tr>";
					foreach($mdTarif AS $fieldName=>$fieldValue){
						if(in_array($fieldName, array("MedecineCategorID", "MedecineCategoryName"))){
							continue;
						}
						echo "<td>";
						if($fieldName == "MedecineNameID"){
							echo $i++;
						} else{
							echo $fieldValue;
						}
						echo "</td>";
					}
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
</div>