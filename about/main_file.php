<?php
$database_name = "care_full_v1_gihundwe";

$app_name = "Care";
$version = "Version 2";
$build = " 2";

$project_title = $app_name." ".$version." ".($build?"| build".$build:"");

$project_name = "CARE Medical Information System";

$release_date = "2018-07-01";
$pharmacy_module_release_date = "2018-10-01";
$Start_Year = "2018";

$app_name = "Care";
$app_level = "Health Center";
$developer = "<span style='font-size:10px; font-family:sans-serif;'>Powered By Digital Schooling Ltd</span>";
$designer = strtoupper("Daphrose IYAKAREMYE");
$designer_name = strtoupper("Gihundwe Health Center");

$types = array("md"=>"Medicines","cn"=>"Consumable", "ac"=>"Acts", "la"=>"Exams");
$types_data = array("md"=>"MedecineName","cn"=>"MedecineName", "ac"=>"Name", "la"=>"ResultName");
$types_data_c = array("md"=>"MedecineNameID","cn"=>"MedecineNameID", "ac"=>"ActNameID","la"=>"ResultID");
$signs = array(">=;<="=>"Between", ">"=>"Greater Than", ">="=>"Greater Than or Equal", "<"=>"Less Than", "<="=>"Less Than or Equal", "="=>"Equal");

/***********************Variable to auto keyword ************/
$tables = array(
				"md"=>array("tb"=>"md_name","fld_v"=>"MedecineName","fld_c"=>"MedecineNameID"),
				"la"=>array("tb"=>"la_result","fld_v"=>"ResultName","fld_c"=>"ResultID","spt"=>", `la_exam`","spf"=>", `ExamName`","spc"=>"")
			);
/***********************Variable to auto keyword ************/



/*$quaters = array(
				"01"=>"Q1",
				"02"=>"Q1",
				"03"=>"Q1",
				"04"=>"Q2",
				"05"=>"Q2",
				"06"=>"Q2",
				"07"=>"Q3",
				"08"=>"Q3",
				"09"=>"Q3",
				"10"=>"Q4",
				"11"=>"Q4",
				"12"=>"Q4"
				);*/
$quaters = array(
				"01"=>"Q1",
				"02"=>"Q2",
				"03"=>"Q3",
				"04"=>"Q4",
				"05"=>"Q5",
				"06"=>"Q6",
				"07"=>"Q7",
				"08"=>"Q8",
				"09"=>"Q9",
				"10"=>"Q10",
				"11"=>"Q11",
				"12"=>"Q12"
				);
$force_field_update = false;
$PROVINCE = ":WESTERN"; $_PROVINCE = "Western";
$DISTRICT = ":RUSIZI"; $_DISTRICT = "Rusizi";
$SECTOR = ":KAMEMBE"; $_SECTOR = "Kamembe";
$CELL = ":"; $_CELL = "";

$month = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
$amezi = array(1=>"Mutarama","Gashyantare","Werurwe","Mata","Gicurasi","Kamena","Nyakanga","Kanama","Nzeri","Ukwakira","Ugushyingo","Ukuboza");
$messages = array(0=>"", "", "Transfer", "Transfer with ambulance");
$cst_data = array(
				"VisitType"=>array(1, 2),
				"DeseaseEpisode"=> array(1,2),
				"visitPurpose"=> array(1,2,3,4,5)
			);
$transfer_data = array(
						0=>"No",
						1=>"No",
						2=>"Transfer",
						3=>"Ambulance"
					);
$zone_cells = array(29, 34, 43, 45, 46);
$zone_districts = array(2);

$availableInsurance = array("CBHI", "MMI", "RSSB RAMA", "PRIVATE");

$daily_reception_report = array(
								"TM Paid" => array(
													"rpt_cbhi"=>array("Number","Amount"),
													"rpt_mmi"=>array("Number", "Amount"),
													"rpt_rssb_rama"=>array("Number", "Amount"),
													"rpt_private"=>array("", ""),
												),
								"Fiche de Prestation" => array(
													"rpt_cbhi"=>array("Number","Amount"),
													"rpt_mmi"=>array("", ""),
													"rpt_rssb_rama"=>array("", ""),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Consultation" => array(
													"rpt_cbhi"=>array("",""),
													"rpt_mmi"=>array("", ""),
													"rpt_rssb_rama"=>array("", ""),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Laboratory" => array(
													"rpt_cbhi"=>array("",""),
													"rpt_mmi"=>array("", ""),
													"rpt_rssb_rama"=>array("", ""),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Medicines" => array(
													"rpt_cbhi"=>array("",""),
													"rpt_mmi"=>array("", ""),
													"rpt_rssb_rama"=>array("", ""),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Accouchement" => array(
													"rpt_cbhi"=>array("",""),
													"rpt_mmi"=>array("", ""),
													"rpt_rssb_rama"=>array("", ""),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Other Acts" => array(
													"rpt_cbhi"=>array("",""),
													"rpt_mmi"=>array("", ""),
													"rpt_rssb_rama"=>array("", ""),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Consumables" => array(
													"rpt_cbhi"=>array("",""),
													"rpt_mmi"=>array("", ""),
													"rpt_rssb_rama"=>array("", ""),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Hospitalisation" => array(
													"rpt_cbhi"=>array("Number","Amount"),
													"rpt_mmi"=>array("Number", "Amount"),
													"rpt_rssb_rama"=>array("Number", "Amount"),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Other Printings" => array(
													"rpt_cbhi"=>array("Number","Amount"),
													"rpt_mmi"=>array("Number", "Amount"),
													"rpt_rssb_rama"=>array("Number", "Amount"),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Lunettes" => array(
													"rpt_cbhi"=>array("",""),
													"rpt_mmi"=>array("", ""),
													"rpt_rssb_rama"=>array("", ""),
													"rpt_private"=>array("Number", "Amount"),
												),
								"Mousticaire" => array(
													"rpt_cbhi"=>array("",""),
													"rpt_mmi"=>array("", ""),
													"rpt_rssb_rama"=>array("", ""),
													"rpt_private"=>array("Number", "Amount"),
												),
							);
$reportRenames = array(
						"totalTM" 			=> "TM Paid",
						"totalFiche" 		=> "Fiche de Prestation",
						"totalCONS" 		=> "Consultation",
						"totalExamLabo" 	=> "Laboratory",
						"totalMED" 			=> "Medicines",
						"totalPROC_ACC" 	=> "Accouchement",
						"totalPROC_OTHER" 	=> "Other Acts",
						"totalCONSU" 		=> "Consumables",
						"totalHOSP" 		=> "Hospitalisation",
						"totalOTHER" 		=> "Other Printings",
						"totalLunette"		=> "Lunettes",
						"totalSupanet"		=> "Mousticaire",
					);
$exceptInReport = "Fiche de Prestation";
$ficheName 		= "Fiche de Prestation";
$envelopeName = "sachets pour medicaments";
/*[insuranceID=>[consulatationCategory=>currentPriceID]]*/
$defaultConsultationPriceID = null;
$ReportAgeRange 			= array("0-6 Days", "7-2 Month", "2-59 month", "5-19 Years", "20-39 Years", "40 Years and more");
$diagnosticReportAgeRange = array(
								0 			=> array(
															"prefix"=> "Days",
															"min"	=> 0,
															"max" => 6,
															"range" => 0
														), 
								1 		=> array(
															"prefix"=> "Weeks",
															"min"	=> 1,
															"max" => 8,
															"range" => 2
														),
								2		=> array(
															"prefix"=> "Month",
															"min"	=> 2,
															"max" => 59,
															"range" => 0
														), 
								3		=> array(
															"prefix"=> "Years",
															"min"	=> 5,
															"max" => 19,
															"range" => 0
														), 
								4		=> array(
															"prefix"=> "Years",
															"min"	=> 20,
															"max" 	=> 39,
															"range" => 0
														), 
								5	=> array(
															"prefix"=> "Years",
															"min"	=> 40,
															"max" 	=> -1,
															"range" => 0
														)
							);

$organisation 						= "Gihundwe Health Center";
$organisation_code_minisante 		= "";
$organisation_represantative 		= "DAPHROSE IYAKAREMYE";
$organisation_accontant 			= "UWIMBABAZI Béatrice";
$organisation_represantative_degree = "INFIRMIERE A1";
$organisation_phone 				= "(250)7 86 28 24 20";
$organisation_email 				= "centredesante.gihundwe@yahoo.fr";
$organisation_tin 					= "";
$organisation_account_number 		= "0652057-01-35";
$organisation_bank_name 			= "I&M Bank";


$cbhiMonthlyBillHeader = array(
							array("PROVINCE / MVK"=>strtoupper($_PROVINCE)),
							array("ADMINISTRATIVE DISTRICT"=>strtoupper($_DISTRICT), "Period"=>"GET_MONTH_HERE/GET_YEAR_HERE"),
							array("ADMINISTRATIVE SECTION"=>strtoupper($_SECTOR)),
							array("HEALTH FACILITY"=> strtoupper($organisation)),
							array("CODE / HEALTH FACILITY"=>$organisation_code_minisante),
						);
$cbhiMonthlyBillReportTitle = array("title"=>"S U M M A R Y  O F V O U C H E R S  F O R  R W A N D A S O C I A L S E C U R I T Y B O A D (R S S B) / CBHI");
$cbhiMonthlyBillDataHeader 	= array(array("No","Number","Date","Service","Cat.","Name","ID Number","Age","Sex","House Holder","ID Number of Household","Cons Cost","Lab","Imaging","Hosp","Procedures & Consumables", "Ambulance","Other Consumables","Drugs","Total","Co-payment","Amount after verification"),
									array("","","","","","","","","","","","100%","100%","100%","100%","100%", "100%","100%","100%","100%","200 RWF /10%","")
								);

$client = "GIHUNDWE";
$client_description = "About Gihundwe Health Center";
$client_abbr = "gihundwe"; // <<<<<=============This will hold the file name with html extension to describe the client

$organisationKiny	= "Ikigo Nderabuzima cya Gihundwe";
$rssb_rama_region 	= "RUSIZI";
$rssb_rama_district = "GIHUNDWE";

$systemPath = "../";
$systemPDFFooter = '
<pagefooter name="odds" content-right="Odd Footer" footer-style-right="color: #880000; font-style: italic;" line="1" />
<pagefooter name="evens" content-right="{DATE j-m-Y}" content-center="{PAGENO}/{nb}" footer-style="color: #880000; font-style: italic;" />

<setpagefooter name="odds" page="O" value="on" />
<setpagefooter name="evens" page="E" value="1" />
';
/***** USABLE CONSTANT ********/
define("FACTURE",20);
define("FICHE",100);

?>