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
$sql = "SELECT 	a.ActNameID AS id,
				a.Name AS name
				FROM ac_name AS a
				ORDER BY a.name ASC";
$records = formatResultSet($rslt=returnResultSet($sql, $con), true, $con);
?>

<select name="data" class="form-control">
	<?php
	foreach($records AS $r){
		?>
		<option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
		<?php
	}
	?>
</select>