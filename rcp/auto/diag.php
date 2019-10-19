<?php
header("Content Type:text/html");
	//echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	require_once "./ds_con.php";
	require_once "../../lib/db_function.php";
	mysql_select_db($database_name)or die(mysql_error());
	$my_data=mysql_real_escape_string($q);
	$sql="SELECT DISTINCT DiagnosticCode, DiagnosticName FROM co_diagnostic WHERE (DiagnosticName LIKE '%$my_data%' || English LIKE '%$my_data%' || DiagnosticCode = '$my_data') && DiagnosticCode != '' ORDER BY DiagnosticName";
	$result = mysql_query($sql);
	
	if($result) {
		while($row=mysql_fetch_array($result)) {
			echo $row['DiagnosticName']."\n";
		}
	}
?>

<?php
die;
header("Content Type:text/html");
	//echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	$mysqli=mysql_connect('localhost','root','') or die("Database Error");
	require_once "../../lib/main_file.php";
	mysql_select_db($database_name)or die(mysql_error());
	$my_data=mysql_real_escape_string($q);
	$sql="SELECT DISTINCT DiagnosticName FROM co_diagnostic WHERE PECIME = 0 && DiagnosticName LIKE '%$my_data%' && Reported=1 ORDER BY DiagnosticName";
	$result = mysql_query($sql);
	
	if($result)
	{
		while($row=mysql_fetch_array($result))
		{
			echo $row['DiagnosticName']."\n";
		}
	}
?>
