<?php
	//echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	require_once "./ds_con.php";
	require_once "../../lib/main_file.php";
	mysql_select_db($database_name)or die(mysql_error());
	$my_data=mysql_real_escape_string($q);
	$sql="SELECT Name FROM ac_name WHERE Name LIKE '%$my_data%' ORDER BY Name";
	$result = mysql_query($sql);
	
	if($result)
	{
		while($row=mysql_fetch_array($result))
		{
			echo $row['Name']."\n";
		}
	}
?>