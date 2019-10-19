<?php
	//echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	// var_dump($_GET);
	require_once "./ds_con.php";
	//echo $database_name;
	require_once "../../lib/main_file.php";
	mysql_select_db($database_name)or die(mysql_error());

	$examName = mysql_real_escape_string(trim($_GET['examName']));
	$my_data=mysql_real_escape_string($q);
	$sql="SELECT 	a.ResultName 
					FROM la_result AS a
					INNER JOIN la_exam AS b
					ON a.ExamID = b.ExamID
					WHERE b.ExamName = '{$examName}' && a.ResultName LIKE '%$my_data%' ORDER BY ResultName";
	$result = mysql_query($sql);
	// echo $sql;
	if($result)
	{
		while($row=mysql_fetch_array($result))
		{
			echo $row['ResultName']."\n";
		}
	}
?>