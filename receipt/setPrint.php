<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST); die();
if(isset($_POST['printerID']) && strlen($_POST['printerID']) == 10){
	$_SESSION['user']['printerID'] = $_POST['printerID'];
	// var_dump($_POST); die();
	// Here Update the Register information fromhere
	header("Location:./");
	exit();
}
$error = "";
//die;
if(@$_GET['msg']){
	$error = "<span class=error>".$_GET['msg']."</span>";
}
// echo $sql;
$active = "reprint";

require_once "../lib2/cssmenu/rcp_header.html";

?>

<script>
  var hide_calendar = false;
</script>
  <div id="w">
    <div id="content" style='text-align:left;'><!--
      <h1>WELCOME TO CARE SOFTWARE</h1>-->
	  
	  <style>
		.a td{
			border:0px solid #000; padding:2px;
		}
		
		.patient_found{
			max-height:200px;
			overflow:auto;
		}
	  </style>
	  <b>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	  <?php echo $error; ?>
	  <div id=upload_out></div>
	  <h1>Select Current service on <?= date("D F Y", time()) ?></h1>
	  <form method=post action="setPrint.php" id=''>
	  
	  <table border=0 style='background-color:#fff;'>
		
		<tr>
			<td colspan=4><div class='address_search' style='max-height:150px; overflow:auto;'></div></td>
		</tr>
		<tr>
			<td colspan=3 style='padding-bottom:10px;'>
				Printer ID<span class=error-text>*</span> <br />
				<input type="text" name="printerID" value="<?= @$_SESSION['user']['printerID'] ?>" class="txtfield1">
			</td>
		</tr>
		<tr><td colspan=4 align=center>
		<input type=submit id=save class="flatbtn-blu" name=rcv_patient value='Save' /> <input type=reset class="flatbtn-blu" id='reset_form' value='Clear' /></td></tr>
	  </table>
	  </form>
	  </b>
  </div>
 </div> 
  <?php
  include_once "../footer.html";
  ?>

</body>
</html>