<?php
session_start();
require_once "../lib/db_function.php";
?>
<!DOCTYPE html>
<html lang="en" >
<head>
	<meta charset="utf-8">
	<!--[if IE]>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<![endif]-->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	
	<title><?= $project_title ?></title>
	<link rel="shortcut icon" href="../images/footer_care.png">
	
	<link rel="stylesheet" type="text/css" media="all" href="./assets/css/all_2.min.css" />
	
	<link rel="stylesheet" type="text/css" media="all" href="./assets/plugins/REDIPS_drag/redips-drag-min.css" />
	<link rel="stylesheet" type="text/css" media="all" href="./assets/plugins/summernote/summernote.css" />
	
	<!-- END CSS TEMPLATE -->
</head>
<body class="skin-dark" >

<!-- BEGIN HEADER -->
	<header class="header">
		
		<!-- END LOGO -->
		<!-- BEGIN NAVBAR -->
		<nav class="navbar navbar-static-top" role="navigation">
			
			
			<div class="navbar-right">
				<ul class="nav navbar-nav">
					
						
					<li class="dropdown profile-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-cog fa-lg"></i>
							<span class="username">Me</span>
							<i class="caret"></i>
						</a>
						<ul class="dropdown-menu box profile">
							<li><div class="up-arrow"></div></li>
							<!-- <li class="border-top">
								<a href="pages-user.html"><i class="fa fa-user"></i>My Account</a>
							</li> -->
							<li>
								<a href="../logout.php" ><i class="fa fa-power-off"></i>Log Out</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
		<!-- END NAVBAR -->
	</header>
	<!-- END HEADER -->
	<body>
		<div class="wrapper row-offcanvas row-offcanvas-left">
<div id="moduleContainer">
	<aside class="right-side">
		<section class="content-header">
			<div id="dashSubmenu">
			</div>
		</section>
		<section class="content">
			<div class="row">
				<div class="grid text-center">
					<img src="./assets/img/loading_dash.gif" alt=""> Loading, Please wait ...
				</div>
			</div>
		</section>
	</aside>
</div>
<div class="scroll-to-top"></div>
		</div>

<!--  Plugins -->
	<script type="text/javascript" charset="utf-8" src="./assets/plugins/aa_all_2.min.js"></script>

 	<script type="text/javascript" charset="utf-8" src="./assets/plugins/REDIPS_drag/redips-drag-min.js"></script>

 	<script type="text/javascript" charset="utf-8" src="./assets/plugins/summernote/summernote.min.js"></script>

<!-- Other -->
	<script type="text/javascript" charset="utf-8" src="./assets/js/aa_all.js"></script>

	<script type="text/javascript" charset="utf-8" src="./assets/js/forms/aa_all.min.js"></script>

 	
	    <script type="text/javascript" charset="utf-8" src="./assets/js/module/aa_all_15_03_2018.min.js"></script>
	  
 		
		<script type="text/javascript" charset="utf-8" src="./assets/plugins/chartJs/Chart.bundle.min.js"></script>
		{<script type="text/javascript" charset="utf-8" src="./assets/plugins/jquery-totemticker/jquery.totemticker.min.js"></script>
	
		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.js"></script>
		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.expenses.js"></script>
		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.income.schoolfees.js"></script>
		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.incomes.submit.js"></script>
		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.incomes.validate.js"></script>
		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.incomes.schoolfees.distribution.js"></script>
		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.income.schoolfees.forms.js"></script>
		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.income.editforms.js"></script>
		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.report.balancesheet.js"></script>

		<script type="text/javascript" charset="utf-8" src="./assets/js/module/accounting.stock_15_03_2018.js"></script>

		<script type="text/javascript">
			$('.ticker').totemticker({
				row_height	: '60px',
				mousestop	: false,
				interval	: 10000
			});
		</script>

</body>
</html>
	</body>