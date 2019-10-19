<?php
	//echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	//$mysqli=mysql_connect('localhost','root','GHC2@E') or die("Database Error");
	require_once "./ds_con.php";
	require_once "../../lib/main_file.php";
	mysql_select_db($database_name)or die(mysql_error());
	$my_data=mysql_real_escape_string($q);
	$_GET['date'] = date("Y-m-d", time());
	//search for active date now
	// $d = mysql_query("SELECT Date FROM md_price WHERE Date <= '{$_GET['date']}' ORDER BY Date DESC LIMIT 0,1");
	// $active_date = mysql_fetch_assoc($d)['Date'];
	$sql = "SELECT 	a.MedecineName AS MedecineName,
					b.Date AS Date
					FROM md_name AS a
					INNER JOIN md_price AS b
					ON a.MedecineNameID = b.MedecineNameID
					WHERE a.MedecineName LIKE '%$my_data%' AND
						  b.Date <= '{$_GET['date']}'
					GROUP BY a.MedecineName
					ORDER BY a.MedecineName
					";
	// echo $sql."\n";
	// $sql="SELECT DISTINCT MedecineName, md_price.Date FROM md_name, md_price WHERE md_price.MedecineNameID = md_name.MedecineNameID && md_price.Date = '{$active_date}' && MedecineName LIKE '%$my_data%' && MedecineCategorID != 2 ORDER BY MedecineName";
	// echo $sql."\n";
	$result = mysql_query($sql);
	
	if($result) {
		while($row=mysql_fetch_array($result)){
			echo $row['MedecineName']."\n";
		}
	}
?>