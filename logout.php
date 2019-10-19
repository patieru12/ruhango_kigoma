<?php
session_start();
require_once "./lib/db_function.php";
saveData("UPDATE sy_register SET consultantId=NULL WHERE consultantId='{$_SESSION['user']['UserID']}'", $con);
session_destroy();
?>
<span class=success>Success</span>
<script>setTimeout("window.location='./'",1000);</script>