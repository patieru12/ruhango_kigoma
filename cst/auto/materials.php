<?php
	//echo "Done\nDone1\nDone2\nDone22\nDone21\nName1\nName2\n"; die;
	$q=$_GET['q'];
	require_once "./ds_con.php";
	require_once "../../lib/main_file.php";
	mysql_select_db($database_name)or die(mysql_error());
	
	$_GET['date'] = date("Y-m-d", time());
	
	$d = mysql_query("SELECT Date FROM cn_price WHERE Date <= '{$_GET['date']}' ORDER BY Date DESC LIMIT 0,1");
	$active_date = mysql_fetch_assoc($d)['Date'];
	
	$my_data=mysql_real_escape_string($q);
	$sql="SELECT MedecineName FROM cn_name, cn_price WHERE cn_price.MedecineNameID = cn_name.MedecineNameID && cn_price.Date = '{$active_date}' && MedecineName LIKE '%$my_data%'  ORDER BY MedecineName";
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