<?php
//var_dump($_GET);
$one_day_sec = 60*60*24;
//echo $one_day_sec;
//echo date("Y-m-d",time()+ $one_day_sec);
//get the first second of the current month
$first_sec = mktime(0,0,0, $_GET['month'],1,$_GET['year']);
?>
<select name=year class=txtfield1 style='width:70px;' id=day>
	<?php
	for($k = $first_sec; date("m",$k) == ($_GET['month']);$k += $one_day_sec){
		echo "<option ".(date("d",$k) == (@$_GET['day']?$_GET['day']:date("d",time()))?"selected":"").">".date("d",$k)."</option>";
	}
	?>
  </select>