<?php
	//echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	$mysqli=mysql_connect('localhost','root','') or die("Database Error");
	mysql_select_db('care_min_v1');
	$my_data=mysql_real_escape_string($q);
	$sql="SELECT MedecineName FROM md_name WHERE MedecineName LIKE '%$my_data%' ORDER BY MedecineName";
	$result = mysql_query($sql);
	
	if($result)
	{
		while($row=mysql_fetch_array($result))
		{
			echo $row['MedecineName']."\n";
		}
	}
?>