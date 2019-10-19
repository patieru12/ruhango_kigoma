<?php
ob_start();
system('ipconfig /all');
$mycom = ob_get_contents();
ob_clean();
$findme = "Physical";
$mycom = substr($mycom, strpos($mycom,"Ethernet"));
$pos = strpos($mycom, $findme);
$macp = substr($mycom, ($pos + 36), 17);
echo "The mac id of this system is:".$macp;
?>
<hr />
