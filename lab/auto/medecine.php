<?php
	//echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	$mysqli=mysql_connect('localhost','root','') or die("Database Error");
	require_once "../../lib/main_file.php";
	mysql_select_db($database_name)or die(mysql_error());
	$my_data=mysql_real_escape_string($q);
	$sql="SELECT DISTINCT MedecineName FROM md_name WHERE MedecineName LIKE '%$my_data%' && MedecineCategorID != 2 ORDER BY MedecineName";
	//echo $sql."\n";
	$result = mysql_query($sql);
	
	if($result)
	{
		while($row=mysql_fetch_array($result))
		{
			echo $row['MedecineName']."\n";
		}
	}
?>