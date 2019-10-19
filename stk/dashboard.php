<?php
session_start();
require_once "../lib/db_function.php";
	$UserPersonID	    = "";
	$schoolID 			= "";
	$permissionsArray 	= "";
	$rolesID 			= "";

	$FirstModuleNumber ;
	$FirstModuleName ;
	$IsFirstModuleFound = false;

	$AcademicsSchoolBranchs = [];

	$Cust_TeacherName = "";
	$Cust_ClassTeacher= "";
	$Cust_SubjectName = "";
	$Cust_TermName 	  = "";

	$permissionsArray = [8];
?>

<script type="text/javascript">
	var Cust_TeacherName 	= <?php echo '"'.$Cust_TeacherName.'"'; ?> ;
	var Cust_ClassTeacher 	= <?php echo '"'.$Cust_ClassTeacher.'"'; ?> ;
	var Cust_SubjectName 	= <?php echo '"'.$Cust_SubjectName.'"'; ?> ;
	var Cust_TermName 		= <?php echo '"'.$Cust_TermName.'"'; ?> ;

	var banksOptions 		= "";

</script>

<!-- BEGIN CONTENT -->
<aside class="right-side">
	<!-- BEGIN CONTENT HEADER -->
	<section class="content-header">
		<div id="dashSubmenu">
		</div>
	</section>
	<!-- BEGIN MAIN CONTENT -->
	<section class="content">
		<div class="row">
			<div class="grid">
				<div class="grid-body">
					<ul class="nav nav-tabs">
						<?php if(in_array( 8 ,$permissionsArray)){ ?>
							<li ><a href="#Payments" data-toggle="tab" onclick="dashModuleTabClick(8)"> <i class="fa fa-dollar"></i> Stock</a></li>
						<?php } ?>


					</ul>
					<div class="tab-content">
						
						<?php 
							if(in_array( 8, $permissionsArray)){

							$Accounting_ExpenseType 			= []; //$DashData['Accounting']['ExpenseType'];
							$Accounting_BudgetItemCategory 	    = []; //$DashData['Accounting']['BudgetItemCategory'];
							$Accounting_SchoolStaff 		    = []; //$DashData['Accounting']['SchoolStaff'];
							$Accounting_CountryBankList 		= []; //$DashData['Accounting']['CountryBankList'];
							$Accounting_SchoolBankAccounts 		= []; //$DashData['Accounting']['SchoolBankAccounts'];
							$Accounting_SchoolCashAccounts 		= []; //$DashData['Accounting']['SchoolCashAccounts'];
							$Accounting_SchoolVendor 		    = []; //$DashData['Accounting']['SchoolVendor'];
							$Accounting_SchoolBudgetItems 		= []; //$DashData['Accounting']['SchoolBudgetItems'];
							$Accounting_SchoolBudgetItemsRent 	= []; //$DashData['Accounting']['SchoolBudgetItemsRent'];
							$Accounting_SchoolCurrency 			= []; //$DashData['Accounting']['SchoolCurrency'];


							if (!$IsFirstModuleFound) {
									
								$IsFirstModuleFound = true;
								$FirstModuleNumber	= 8;
								$FirstModuleName	= 'Payments';
							}

						?>
							<div class="tab-pane" id="Payments">
								<div class="row">
									<div class="col-sm-12 top-buffer " >

										<h1><?= $app_level ?> non-medical stock Information
										<!-- <div class="col-sm-3">
											
										</div>
										<div class="col-sm-6" >
										    <div class="container bottom-buffer ">
										        <div class="collapse navbar-collapse">
										            <ul class="nav navbar-nav ">
										            	<div class="btn-group">

										  					<div class="btn-group">
										  						<li>
										  							<a href="#" id="acc_Stock">
										  								<button type="button" class="btn btn-primary" ><i class="fa fa-university"></i>&nbsp;Stock&nbsp;</button>
										  							</a>
										  						</li>
										  					</div>
										  				</div>
										            </ul>
										        </div>
										    </div>
										</div>
										<div class="col-sm-3">
											<div class="btn-group pull-right">
												
										  		<div class="btn-group">
										  			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								                   		<button type="button" class="btn btn-default" id="acc_Reports" ><i class="fa fa-bar-chart"></i>&nbsp;Reports&nbsp;&nbsp;</button>
								                    </a>
										  		</div>
										  	</div>
										</div> -->
									</div>
								</div>
								<div class="row top-buffer"></div>
							</div>
							<?php
							include_once "accounting_modal.php";
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<div  id="moduleDashHomeContainer">
			
			
			<?php if(in_array( 8, $permissionsArray)){ ?>
				<div id="dashContentainer_Payments">
					<div class="row"  id="dashPayments_content">

					</div>
				</div>
			<script type="text/javascript">

				var schoolCurrency = "";
			    dashboardAccounting();	

			</script>
				
			<?php } ?>
			<div id="dashContentainer_new">
				<div class="grid">
					<div class="grid-body">
						<div class="row" id="dashNew_content">

						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<div>
		<div class="grid">
			<div class="grid-body">
				<div class="row text-center">
					<?php
					require_once "../footer.html";
					?>
				</div>
			</div>
		</div>
	</div>
	</section>
	<!-- END MAIN CONTENT -->
</aside>
<!-- END CONTENT -->

<div class="modal fade" id="Model_LoginAgain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title text-center" id="myModalLabel2">Please log in to continue.</h4>
				</div>
				<form class="form-horizontal" id="formLoginAgain" role="form" action="private" method="POST" >
					<div class="modal-body">
						<div class="form-group">
							<label class="col-sm-3 control-label">Email</label>
							<div class="col-sm-7">
								<input name="email"  type="text" class="form-control" value="{{ $username }}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Password</label>
							<div class="col-sm-7">
								<input name="password" type="password" class="form-control" id="LoginAgainPassword">
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<div class="btn-group">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">LOGIN</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">

	ab_page_necessity();
	getSelect2();

	<?php if ($IsFirstModuleFound){ ?> 
		defaultDashModuleTabClick(<?php echo $FirstModuleNumber ;?>,'<?php echo $FirstModuleName; ?>');
	<?php } ?>

	
	
	(function (global) {

		if(typeof (global) === "undefined")
		{
			throw new Error("window is undefined");
		}

	    var _hash = "!";
	    var noBackPlease = function () {
	        global.location.href += "#";
			// making sure we have the fruit available for juice....
			// 50 milliseconds for just once do not cost much (^__^)
	        global.setTimeout(function () {
	            global.location.href += "!";
	        }, 50);
	    };
		
		// Earlier we had setInerval here....
	    global.onhashchange = function () {
	        if (global.location.hash !== _hash) {
	            global.location.hash = _hash;

	        }
	    };

	    global.onload = function () {

			noBackPlease();

			// disables backspace on page except on input fields and textarea..
			document.body.onkeydown = function (e) {

	            var elm = e.target.nodeName.toLowerCase();
	            if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
	                e.preventDefault();
	            }
	            // stopping event bubbling up the DOM tree..
	            e.stopPropagation();
	        };
			
	    };

	})(window);
	
</script>







