<?php
session_start();
require_once "../../../lib/db_function.php";
// Check if the current Consultant has a registerd to be used
$registerId = returnSingleField("SELECT id FROM sy_register WHERE consultantId='{$_SESSION['user']['UserID']}'", "id", true, $con);
if(!$registerId){
	echo "<script> window.location='../../se_select.php?msg=select the register please and service'; </script>";
	return;
}

// Select all active Act
$sql = "SELECT 	a.MedecineNameID AS id,
				a.MedecineName AS name,
				b.MedecineCategoryName AS categoryName
				FROM md_name AS a
				INNER JOIN md_category AS b
				ON a.MedecineCategorID = b.MedecineCategoryID
				ORDER BY b.MedecineCategoryName ASC, a.MedecineName ASC";
$records = formatResultSet($rslt=returnResultSet($sql, $con), true, $con);
?>

<select name="data" class="form-control">
	<?php
	$groupOpen = false;
	$lastGroup = "";
	foreach($records AS $r){
		if($lastGroup != $r["categoryName"]){
			if($groupOpen){
				echo "</optgroup>";
				$groupOpen = false;
			}
			echo "<optgroup label='{$r["categoryName"]}'>";
			$groupOpen = true;
			$lastGroup = $r["categoryName"];
		}
		?>
		<option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
		<?php
	}
	if($groupOpen){
		echo "</optgroup>";
		$groupOpen = false;
	}
	?>
</select>