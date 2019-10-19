<?php
	#echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	$my_data=mysql_real_escape_string($q);
	$mysqli=mysqli_connect('localhost','root','','autofield') or die("Database Error");
	$sql="SELECT name FROM tag WHERE name LIKE '%$my_data%' ORDER BY name";
	$result = mysqli_query($mysqli,$sql) or die(mysqli_error());
	
	if($result)
	{
		while($row=mysqli_fetch_array($result))
		{
			echo $row['name']."\n";
		}
	}
?>