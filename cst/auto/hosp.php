<?php
	//echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	require_once "./ds_con.php";
	//echo $database_name;
	require_once "../../lib/main_file.php";
	mysql_select_db($database_name)or die(mysql_error());
	$my_data=mysql_real_escape_string($q);
	$sql="SELECT Name FROM ho_type WHERE Name LIKE '%$my_data%' ORDER BY Name";
	$result = mysql_query($sql);
	//echo $sql;
	if($result)
	{
		while($row=mysql_fetch_array($result))
		{
			echo $row['Name']."\n";
		}
	}
?>