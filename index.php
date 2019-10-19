<?php
require_once "./lib/db_function.php";
// check if to day is closed day
$today = date("Y-m-d", time());
$closedDays = returnAllData($sql="SELECT * FROM sy_conge WHERE Date='{$today}'",$con);
// var_dump($closedDays);

$weekend_days= array("Sat", "Sun");
$thisDayPrefix = date("D", time());

$currentHour = date("H", time());

$loginString = "";
if($currentHour >= 17 || $currentHour <= 7){
	$loginString = " Night";
} else if( count($closedDays) > 0) {
	$loginString = " Closed Day";
} else if(in_array($thisDayPrefix, $weekend_days)){
	$loginString = " Week-End";
}
?>
<!doctype html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  <title><?= $project_title; ?></title>
  <link rel="shortcut icon" href="./images/footer_care.png">
  <link rel="icon" href="./images/footer_care.png">
  <link rel="stylesheet" type="text/css" media="all" href="style.css">
  <link rel="stylesheet" type="text/css" media="all" href="./lib2/cssmenu/styles_menu.css">
  <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="js/jquery.leanModal.min.js"></script>
  <!-- jQuery plugin leanModal under MIT License http://leanmodal.finelysliced.com.au/ -->
</head>

<body>
	<!-- <div id="banner">
		<img src="./images/logo.png" alt="IGISUBIZO MEDICAL CLINIC" />
	</div> -->
	<div id="topbar">
		<div  id='cssmenu'>
			<ul>
				<li>
					<a href='#'>CARE</a>
				</li>
			</ul>
		</div>
	</div>
  <div id="w">
    <div id="content">
      <!--<h1>WELCOME TO CARE SOFTWARE</h1>-->
      <center>
	  <!--<img src='./images/home.png' style='border-radius:4px;' />-->
	  <?= $project_name; ?>:
	  <?= $version; ?>
	  <a href="#loginmodal" style='margin-top:160px;' class="flatbtn" id="modaltrigger">Login</a>
	  </center>
    </div>
  </div>
  <div id="loginmodal" style="display:none;">
    <h1>
    	User Login
    	<?php
    	echo $loginString;
    	?>
    </h1>
    <form id="loginform" name="loginform" method="post" action="index.html">
      <label for="username">Username:</label>
      <input type="text" name="username" id="username" class="txtfield" tabindex="1">
      
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" class="txtfield" tabindex="2">
      
      <div class="center"><input type="submit" name="loginbtn" id="loginbtn" class="flatbtn-blu" value="Log In" tabindex="3"></div>
	  <div class="login_result"></div>
    </form>
  </div>
  
  <?php
  $systemPath = "./";
  include_once "./footer.html";
  ?>
<script type="text/javascript">
$(function(){

	$("#username").keypress(function(e){
		$("#username").removeClass("error");
	});
	$("#password").keypress(function(e){
		$("#password").removeClass("error");
	});
  $('#loginform').submit(function(e){
	var username = $("#username").val();
	var password = $("#password").val();
	if(username == ""){
		$("#username").addClass("error");
		return e.preventDefault();
	}
	
	if(password == ""){
		$("#password").addClass("error");
		return e.preventDefault();
	}
	//submit the request using JQuery Ajax function
	$.ajax({
		type: "POST",
		url: "./login.php",
		data: "username=" + $("#username").val() + "&password=" + $("#password").val() + "&url=ajax",
		cache: false,
		success: function(result){
			$(".login_result").html(result);
		}
	});
    return e.preventDefault();
	
  });
  
  $('#modaltrigger').leanModal({ top: 110, overlay: 0.45, closeButton: ".hidemodal" });
  $("#modaltrigger").click(function(e){
	$("#username").focus();
  });
  $("#modaltrigger").click();
  $('body').mousedown(function(e) {
	var clicked = $(e.target); // get the element clicked
	if (clicked.is('#loginmodal') || clicked.parents().is('#loginmodal')) {
		return; // click happened within the dialog, do nothing here
   } else { // click was outside the dialog, so close it
     //$('.overlay').hide();
	 return false;
   }
});
});
</script>
</body>
</html>