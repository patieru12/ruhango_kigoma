<?php
// Connect tot the database for searchingnew results
$q=$_GET['q'];
require_once "../../cst/auto/ds_con.php";
//$mysqli=mysql_connect('localhost','root','') or die("Database Error");
//echo $database_name;
require_once "../../lib/main_file.php";
mysql_select_db($database_name)or die(mysql_error());
$my_data=mysql_real_escape_string($q);

$sql="SELECT 	a.ResultName 
				FROM la_result AS a
				INNER JOIN la_exam AS b
				ON a.ExamID = b.ExamID
				WHERE b.ExamName = '{$_GET['exam']}' && a.ResultName LIKE '%$my_data%' && a.Appear = 1
				ORDER BY a.ResultName";
$result = mysql_query($sql);
// echo $sql;
if($result) {
	while($row=mysql_fetch_array($result))
	{
		echo $row['ResultName']."\n";
	}
}
?>