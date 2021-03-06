
var CurrentSelectedTab = null;

function getDashboard(){

	$( "#leftNotificationContainer" ).hide();

	var moduleContainer = $("#moduleContainer");

	var content = '';
		content +='<aside class="right-side">';
			content +='<section class="content-header">';
				content +='<div id="dashSubmenu">';
				content +='</div>';
			content +='</section>';
			content +='<section class="content">';
				content +='<div class="row">';
					content +='<div class="grid text-center">';
						content +='<img src="../packages/assets/plugins/img/loading.gif" alt=""> Loading, Please wait ...';
							content +='';
						content +='</div>';
					content +='</div>';
				content +='</div>';
			content +='</section>';
		content +='</aside>';
		
		moduleContainer.html(content);

		var url 			 = base_url+"dashboard";

		 $.ajax({
	        url: url ,
	        type: "GET",
	        dataType: "html",
	        success: function (data) {
	            
	            $("#moduleContainer").html(data);

	        },
	        error: function (xhr, status) {

	        var	content = '';
	        	content +='<aside class="right-side">';
					content +='<section class="content-header">';
						content +='<div id="dashSubmenu">';
						content +='</div>';
					content +='</section>';
					content +='<section class="content">';
						content +='<div class="grid text-center">';
							content +='<div class="alert alert-warning">';
								content +='<div class="row">';
									content +="Something went wrong! Check your internet and try again";
								content +='</div>';
								content +='<div class="row top-buffer">';
									content +="<strong><a href='#' onclick='getDashboard()' ><i class='fa fa-refresh'></i>&nbsp;&nbsp;Try again</a></strong>";
								content +='</div>';
							content +='</div>';							
						content +='</div>';
					content +='</section>';
				content +='</aside>';

	        	$("#moduleContainer").html(content);
	        	
	        },
	        complete: function (xhr, status) {
	           
	        }
	    });

}

function ab_load_studentPaFee(TotalAmount, TotalAmountPaid, TotalAmountRemain)
{
  $( "#donut-dashboardContainer" ).empty();

  Morris.Donut({
    element: 'donut-dashboardContainer',
    data: [
      {label: "Paid", value:  ((TotalAmountPaid / TotalAmount) *100).toFixed(2) },
      {label: "Remain", value:  ((TotalAmountRemain / TotalAmount) *100).toFixed(2)  }
    ],
    colors: ['#5CB85C','#D3D3D3','#679DC6','#95BBD7','#B0CCE1','#095791','#095085','#083E67','#052C48','#F56954'],
    formatter: function (value, data) { return formatNumber(value)+ ' %'; }
  });

}

function ab_load_studentPaFeeDashTab(TotalAmount,TotalAmountPaid,TotalAmountRemain)
{

  $( "#donut-dashContentainer_Payments" ).empty();

  Morris.Donut({
    element: 'donut-dashContentainer_Payments',
    data: [
      {label: "Paid", value:  ((TotalAmountPaid / TotalAmount) *100).toFixed(2) },
      {label: "Remain", value:  ((TotalAmountRemain / TotalAmount) *100).toFixed(2)  }
    ],
    colors: ['#5CB85C','#D3D3D3','#679DC6','#95BBD7','#B0CCE1','#095791','#095085','#083E67','#052C48','#F56954'],
    formatter: function (value, data) { return formatNumber(value)+ ' %'; }
  });

}

function defaultDashModuleTabClick(id , FirstModuleName)
{
	$('.nav-tabs a[href="#' + FirstModuleName + '"]').tab('show');

  	dashModuleTabClick(id);    
}

function dashModuleTabClick(id)
{
	
	CurrentSelectedTab = id ;

	$('#moduleDashHomeContainer > div').hide();

	switch(id) {
        case 1:
        	dashOnTabSelectedAcademics();   
            break;

        case 2:
            dashOnTabSelectedAttendance(); 
            break;

        case 3:
            dashOnTabSelectedDispline(); 
            break;

        case 4:
            dashOnTabSelectedGradebook(); 
            break;

        case 5:
            dashOnTabSelectedHumanResource(); 
            break;

        case 6:
            dashOnTabSelectedLibrary(); 
            break;

        case 8:
            dashOnTabSelectedPayments(); 
            break;

        case 10:
            dashOnTabSelectedSchoolAssets(); 
            break; 

        case 11:
            dashOnTabSelectedSchools(); 
            break; 

        case 12:
            dashOnTabSelectedSecurity(); 
            break; 

        case 13:
            dashOnTabSelectedSmartCard(); 
            break; 

        case 14:
            dashOnTabSelectedStudents(); 
            break;  

        case 15:
            dashOnTabSelectedAlumnus(); 
            break; 

        case 16:
            dashOnTabSelectedAdmission(); 
            break;  

        case 17:
            dashOnTableSelectedStudentPermission(); 
            break;

        case 18:
        	dashOnTabSelectedTimetable();
        	break;

        }

}


function dashOnTabSelectedTimetable()
{
	loadTtTimetableDash();
	$( "#dashContentainer_Timetable").slideDown();
}

function dashOnTableSelectedStudentPermission()
{
	$( "#dashContentainer_Displine").slideDown();
    var container = $( "#dashDispline_content" );

    container.empty();

    var url = base_url+"api/StudPer/StudentPermissionAnalytics";
    content = '';

    $.getJSON( url, function(data) {

        content = '<div class="col-sm-12">';
        content += '<div class="row"><div class="col-sm-12 text-center"><h4><strong>Current Valid Student Permissions</strong></h4></div></div>'; 
        content += '<div class="row"><table class="table table-striped" id="dashStudPerTable" ><thead><tr><th class="text-center" style="width: 10%;">Date</th><th style="width: 25%;">Name</th><th style="width: 10%;" class="text-center">Reg. Number</th><th style="width: 10%;" class="text-center">From</th><th style="width: 10%;" class="text-center">To</th><th style="width: 10%;" class="text-center">Where</th><th style="width: 15%;" class="text-center">Reason</th></tr></thead><tbody>'; //<th style="width: 10%;" class="text-center">Action</th>
    
        $.each(data, function(i, item) {

            content +="<tr id='"+item.id+"'>"; 
              content +="<td style='width: 10%;' class='text-center'>"+date_HM_moment(item.created_at)+"</td>";
              content +="<td style='width: 25%;' class='text-left'>"+item.studentNames+"</td>";
              content +="<td style='width: 10%;' class='text-center'>"+item.studRegNber+"</td>";
              content +="<td style='width: 10%;' class='text-center'>"+date_HM_moment(item.FromDateTime)+"</td>";
              content +="<td style='width: 10%;' class='text-center'>"+date_HM_moment(item.ToDateTime)+"</td>";
              content +="<td style='width: 10%;' class='text-center'>"+item.ToWhere+"</td>";
              content +="<td style='width: 15%;' class='text-left'>"+item.Reason+"</td>";
              //content +="<td style='width: 10%;' class='row text-center'><a target='_blank' href='PDF/StudentPermission/'><i class='fa fa-print text-green'></i> Print</a></td>";
            content +="</tr>";

        });
      
         content +='</tbody></table></div>';
        content +='</div>';
      
    }).done(function() {

        container.append( content );
        abTablePagingTable('dashStudPerTable');

        //DiDeductionTableClicked();

    })
    .fail(function() {
    })
    .always(function() {
    });

}


function dashOnTabSelectedAcademics()
{

	DashAcSelectAllClassroom();
	$( "#dashContentainer_Academics").slideDown();
}

function dashOnTabSelectedAttendance()
{

	loadAttendanceAnalytics();
	$( "#dashContentainer_Attendance").slideDown();

	// $( "#dashContentainer_Attendance").slideDown( function()
	// {
	// 	$(".eventCalendar-wrap").empty();
	// 	$("#eventCalendarCustomDate").eventCalendar({
	// 			eventsjson: 'api/At/events',
	// 			dateFormat: 'dddd MM-D-YYYY'
	// 		});
	// 	setAtTypeClickListner();
	// 	setAtDashSelected2();
	// });

}

function dashOnTabSelectedDispline()
{
	$( "#dashContentainer_Displine").slideDown();
    var container          = $( "#dashDispline_content" );

    container.empty();

    var url = base_url+"api/Di/Sanctions";

    content = '';

    $.getJSON( url, function(data) {

    	var isDoubleRecord = data.isDoubleRecord ;
    	var Deductions     = data.Deductions ;

        content = '<div class="col-sm-12">';
        content += '<div class="row"><div class="col-sm-12 text-center"><h4><strong>Deductions</strong></h4></div></div>'; 
        if (isDoubleRecord) {
        	content += '<div class="row"><table class="table table-striped" id="dashDiDeductionTable" ><thead><tr><th>Date</th><th>Name</th><th class="text-center">Reg. Number</th><th class="text-center">Metric</th><th class="text-center">Points</th><th class="text-center">Comments</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead><tbody>';
        }else{
        	content += '<div class="row"><table class="table table-striped" id="dashDiDeductionTable" ><thead><tr><th>Date</th><th>Name</th><th class="text-center">Reg. Number</th><th class="text-center">Fault</th><th class="text-center">Deducted Points</th><th class="text-center">Comments</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead><tbody>';
        }
        
        $.each( Deductions, function(i, item) {

            content +="<tr id='"+item.id+"' student_person_id ='"+item.personID+"' fault_id='"+item.faultID+"' deducted_point='"+item.deductedPoint+"'>";
              content +="<td>"+date_HM_moment(item.created_at)+"</td>";
              content +="<td>"+item.studentNames+"</td>";
              content +="<td class='text-center'>"+item.studRegNber+"</td>";
              content +="<td class='text-left'>"+item.faultName+"</td>";
              content +="<td class='text-center'>";
              	 content += ( item.faultType == 0 ) ? "<span class='text-red'>- "+item.deductedPoint+"</span>" : "<span class='text-blue'>+ "+item.deductedPoint+"</span>";
              content +="</td>";
              content +="<td class='text-center diDeductionViewComment text-blue'><a href='#'>"+item.numberOfComments+" Comment(s)</a></td>";
              content +="<td class='text-center diDeductionEdit'><a href='#'>&nbsp;<i class='fa fa-pencil'></i>&nbsp;</a></td>";
              content +="<td class='text-center diDeductionDelete'><a href='#''>&nbsp;<i class='fa fa-times text-red'></i>&nbsp;</a></td>";
            content +="</tr>";

        });
      
        content +='</tbody></table></div>';
        content +='</div>';
      
    }).done(function() {

        container.append( content );
        abTablePagingTable('dashDiDeductionTable');

        DiDeductionTableClicked();

    })
    .fail(function() {
    })
    .always(function() {
    });
}

var AnGroupedSubjects_SG 	     = [[]];
var AnGroupedSubjects 			 = [[]];
var AnGroupedAssessment 		 = [[]];
var AnGroupedAssPeriods 		 = [[[]]];
var AnGroupedSchoolTermPeriods   = [];
var AnGroupedAssessmentType      = [];
var AnGroupedAssessmentTypeUnique= [];
var AnGroupedSchoolAssessmentType= [];

function dashOnTabSelectedGradebook()
{

	$( "#dashContentainer_Gradebook").slideDown();
	$( "#dashGradebook_contentOther").hide();
	$( "#dashGradebook_contentAnalytics").slideDown();

	var dashGrStudentPerformanceContainer = $( "#dashGrStudentPerformanceContainer");

	var DashAnPeriodSelect 		= $( "#DashAnPeriodSelect");
	var DashAnClassSelect 		= $( "#DashAnClassSelect");
	var DashAnSubjectSelect 	= $( "#DashAnSubjectSelect");
	var DashAnAssSelect 		= $( "#DashAnAssSelect");

	var url = base_url+"api/Gr/Analytics";

	$.getJSON( url, function(data) {

		var classrooms 				  = data.classrooms;
		var classroomsSubjects  	  = data.classroomsSubjects;
		var assessment 				  = data.assessment;
		var assessmentPeriods 		  = data.assessmentPeriods;
		var schoolTermPeriods 		  = data.SchoolTermPeriods;
		var SchoolAssessmentType      = data.SchoolAssessmentType;
		var ResultsPeriodAverage      = data.ResultsPeriodAverage;
		var GradebookStudents 		  = data.GradebookStudents;

		var classroomsSize 		 	  = classrooms.length;
		var SubjectSize 		 	  = classroomsSubjects.length;
		var assessmentSize       	  = assessment.length;
		var assessmentPeriodSize 	  = assessmentPeriods.length;
		var schoolTermPeriodSize 	  = schoolTermPeriods.length;
		var SchoolAssessmentTypeSize  = SchoolAssessmentType.length;
		var ResultsPeriodAverageSize  = ResultsPeriodAverage.length;
		var GradebookStudentsSize 	  = GradebookStudents.length;

		var ChartData = [];
		 
	  	for(var i = 0; i < ResultsPeriodAverageSize ; i++){

	        ChartData.push({y: ResultsPeriodAverage[i].period, value: ResultsPeriodAverage[i].score});
	  	}

	  	dashGrStudentPerformanceContainer.empty();

	  	Morris.Line({
	       element: 'dashGrStudentPerformanceContainer',
	       data: ChartData,
	       xkey: 'y',
	       ykeys: ['value'],
	       labels: ['y'],
	       smooth: false,
	       parseTime: false,
	       hoverCallback: function (index, options, content, row) {
			  return " Average Percentage in <span class='text-blue'><b>" + row.y + "</b></span> is <span class='text-blue'><b>" + row.value +"%</b></span>";
			},
		   resize: true ,
	     });
		//Feed chart 

		//Store Classes - Students 
			GroupedProcStudentsClassArray 		= [[]];

			for ( var i=0; i< classroomsSize ; i++ ) {

				GroupedProcStudentsClassArray[classrooms[i].id] = new Array();
			}

			for ( var i = 0; i < GradebookStudentsSize; i++) {

				var personID 	= GradebookStudents[i].personID;
				var regNumber   = GradebookStudents[i].studRegNber;
				var studentNames= GradebookStudents[i].studentNames;
				var classId 	= GradebookStudents[i].classId;	

				var row = [];

				row['personID'] 	= personID;
				row['regNumber'] 	= regNumber;
				row['studentNames'] = studentNames;

				GroupedProcStudentsClassArray[classId].push(row);

			}

		//classes
		var AnClassOptions 	= '<option value="0">All</option>';

		var Anclassidentifier = "";
		var AnisTheFirsRecord = true;

		for ( var i=0; i< classroomsSize ; i++ ) {

			if ( classrooms[i].classidentifier == Anclassidentifier ){
			    AnClassOptions += '<option value="'+classrooms[i].id+'" data-yearname="'+classrooms[i].YearName+'" data-level="'+classrooms[i].levelID+'" >'+classrooms[i].name+'</option>';
			
			}else{

			    Anclassidentifier = classrooms[i].classidentifier;
			    	if ( AnisTheFirsRecord ){

				    	AnisTheFirsRecord = false; 

			    	}else{
				    	AnClassOptions += '</optgroup>';
				    }

			    	AnClassOptions += '<optgroup label="'+classrooms[i].YearName+'">';
			   		AnClassOptions += '<option value="'+classrooms[i].id+'" data-yearname="'+classrooms[i].YearName+'" data-level="'+classrooms[i].levelID+'" >'+classrooms[i].name+'</option>';
			}
				    
		}

		DashAnClassSelect.html(AnClassOptions);
		DashAnClassSelect.select2({ 
	            formatResult: newStudentformatResult,
	            formatSelection: newStudentformatSelection
	    }).bind('change', getDashAnClassSelected);


		//store subjects in array 
			for ( var i = 0; i < classroomsSize; i++) {

				AnGroupedSubjects[classrooms[i].id] = new Array();

			}

			for ( var i = 0; i < SubjectSize; i++ ) {

				var subjectid 	= classroomsSubjects[i].id;
				var classId   	= classroomsSubjects[i].classId;
				var subjectname = classroomsSubjects[i].subjectname;

				var row = [];

				row['id'] 			= subjectid;
				row['subjectname'] 	= subjectname;

				AnGroupedSubjects[classId].push(row);
			}

		var AnSubjectsOptions 	= '<option value="0">All</option>';
		DashAnSubjectSelect.html(AnSubjectsOptions);
		DashAnSubjectSelect.select2().bind('change', getDashAnSubjectSelected );

		//store assessments in array 
			for ( var i = 0; i < SubjectSize; i++) {

				AnGroupedAssessment[classroomsSubjects[i].id] = new Array();

			}

			for ( var i = 0; i < assessmentSize; i++ ) {

				var anClsrmSbjtID   	= assessment[i].anClsrmSbjtID;
				var assessmentTypeId   	= assessment[i].assessmentTypeId;
				var isExamType 			= assessment[i].isExamType;
				var assessmentType		= assessment[i].assessmentType;

				var row = [];

				row['anClsrmSbjtID'] 	= anClsrmSbjtID;
				row['assessmentTypeId'] = assessmentTypeId;
				row['isExamType'] 		= isExamType;
				row['assessmentType'] 	= assessmentType;

				AnGroupedAssessmentType.push(assessmentTypeId);
				AnGroupedAssessment[anClsrmSbjtID].push(row);

			}

			$.each(AnGroupedAssessmentType, function(i, el){
			    if($.inArray(el, AnGroupedAssessmentTypeUnique) === -1) AnGroupedAssessmentTypeUnique.push(el);
			});

			AssessmentTypeSize = AnGroupedAssessmentTypeUnique.length;

			for ( var i = 0; i < SubjectSize; i++) {

				var anClsrmSbjtID = classroomsSubjects[i].id;
				AnGroupedAssPeriods[anClsrmSbjtID] = new Array();

				for (var j = 0; j < AssessmentTypeSize ; j++) {
					
					var AssTypeID = AnGroupedAssessmentTypeUnique[j];
					AnGroupedAssPeriods[anClsrmSbjtID][AssTypeID] = new Array();

				}

			}

			for ( var i = 0; i < assessmentPeriodSize; i++) {

				var assessmentPeriod = assessmentPeriods[i];

				var id 				= assessmentPeriod['id'];
				var anClsrmSbjtID   = assessmentPeriod['anClsrmSbjtID'];
				var asTypeId 		= assessmentPeriod['asTypeId'];
				var period 			= assessmentPeriod['period'];

				AnGroupedAssPeriods[anClsrmSbjtID][asTypeId].push( period );

			}
		
		//periods
		var AnPeriodOptions 	= '<option value="0">All</option>';

		for ( var i = 0; i < schoolTermPeriodSize; i++) {

			AnGroupedSchoolTermPeriods.push( schoolTermPeriods[i].period );

			AnPeriodOptions += '<option value="'+schoolTermPeriods[i].period+'" >'+schoolTermPeriods[i].period+'</option>';
		}

		DashAnPeriodSelect.html( AnPeriodOptions );
		DashAnPeriodSelect.select2();

		//SchoolAssessmentType
		var AssessmentOptions 	 = '';

		var assessmentIdentifier = "";
		var isTheFirsRecord 	 = true;

		var assessments 			= SchoolAssessmentType;
		var assessmentsSize 		= assessments.length;

		if ( assessmentsSize > 0 )
		 {	

		 	AssessmentOptions 	= '<option value="0">All</option>';

		 	for ( var i = 0; i< assessmentsSize ; i++ ) {

		 		var row = [];

				var id 				= SchoolAssessmentType[i].id;
				var name 			= SchoolAssessmentType[i].name;
				var isExamType 		= SchoolAssessmentType[i].isExamType;

				row['id'] 			= id;
				row['name'] 		= name;
				row['isExamType'] 	= isExamType;

				AnGroupedSchoolAssessmentType.push( row );

				if ( isExamType == assessmentIdentifier ){
				    AssessmentOptions += '<option value="'+id+'" data-isexamtype="'+isExamType+'" >'+ name + '</option>';
				
				}else{

				    assessmentIdentifier = assessments[i]['isExamType'];

			    	if ( isTheFirsRecord ){

				    	isTheFirsRecord = false; 

			    	}else{
				    	 AssessmentOptions += '</optgroup>';
				    }

				    var labelName = ( isExamType == 1 ) ? "Exam" : "CAT";

			    	AssessmentOptions += '<optgroup label="'+labelName+'">';
			   		AssessmentOptions += '<option value="'+id+'" data-isexamtype="'+isExamType+'" >'+ name + '</option>';
			
				}
					    
			}

		 }else{

		 	AssessmentOptions += '<option value="-1">Empty</option>';
		 }

	    DashAnAssSelect.html(AssessmentOptions);
	   	DashAnAssSelect.select2().bind('change', getDashAnAssessmentSelected );


	}).done(function() {
	})
	.fail(function() {
	})
	.always(function() {
	});
}

function dashOnTabSelectedHumanResource()
{
}

function dashOnTabSelectedLibrary()
{
	//$( "#dashContentainer_Library").slideDown();

	$("#dashContentainer_Library").slideDown();
	$("#dashLibrary_content").show();
	$("#dashLibrary_contentOthers").hide();
	$("#dashLibrary_contentAnalytics").show();

	loadLibraryHome();

}

function dashOnTabSelectedPayments()
{
	PaNewIncomeJS();
	PaNewExpenseJS();
}

function dashOnTabSelectedSchoolAssets()
{
	loadDashSchoolAsset();
}

function dashOnTabSelectedSchools()
{

}

function dashOnTabSelectedSecurity()
{
}

function dashOnTabSelectedStudents()
{	
	$("#dashContentainer_Students").slideDown();
	$("#dashStudents_content").show();
	$("#dashStudents_contentProfile").show();
	$("#dashStudents_contentAnalytics").show();
	$("#dashStudents_contentLists").show();


	loadStudentHome();
}

function dashOnTabSelectedAlumnus()
{
	$("#dashContentainer_Alumnus").slideDown();
	$("#dashAlumnus_content").show();
	$("#dashAlumnus_contentAll").show();
	$("#dashAlumnus_contentAnalytics").show();

	loadAlumniHome();
}

function dashOnTabSelectedAdmission(){

	$("#dashContentainer_Admission").slideDown();

	//dashboardAdNewAdmission();

	get_ap_NewApplication();
}	

function loadAlumniHome()
{

	  $( "#dashAlumnus_contentAll" ).hide();
	  $( "#dashAlumnus_contentAnalytics" ).slideDown();

	  var url = base_url+"dash/alumnus";

	  //
	  var AlumnusTotal  	= $( "#AlumnusTotal" );
	  var AlumnusCountries 	= $( "#AlumnusCountries" );
	  var AlumnusLeftYear 	= $( "#AlumnusLeftYear" );

	  var AlumnusTotalContent 	 	;
	  var AlumnusCountriesContent 	= '' ;
	  var AlumnusLeftYearContent 	= '' ;

	  $.getJSON( url, function(data) {

	  	  var AlumnusTotalContentData 	 	= data.Total;
	 	  var AlumnusCountriesContentData 	= data.Countries;
	      var AlumnusLeftYearContentData   	= data.LeftYear;

	      //
	      	AlumnusCountriesContent += '<div class="row"><div class="col-sm-12 text-center"><strong> By country they live in.</strong></div></div>';
			    AlumnusCountriesContent += '<div class="row top-buffer"><table class="table"><thead><tr><th>Country</th><th class="text-center">Number</th><th class="text-right">Percentage</th></thead><tbody>';

			    $.each(AlumnusCountriesContentData, function(i, item) {

			        AlumnusCountriesContent +="<tr >";
			          AlumnusCountriesContent +="<td>"+item.country+"</td>";
			          AlumnusCountriesContent +="<td class='text-center'>"+item.numberOfAlumin+"</td>";
			          AlumnusCountriesContent +="<td class='text-right'>"+( ( item.numberOfAlumin / AlumnusTotalContentData )*100 ).toFixed(2)+" %</td>";
			        AlumnusCountriesContent +="</tr>";

			    });

			    AlumnusCountriesContent +='</tbody></table></div>';
		    AlumnusCountriesContent +='</div>';

		   AlumnusLeftYearContent += '<div class="row"><div class="col-sm-12 text-center"><strong> By the year they left</strong></div></div>';
			    AlumnusLeftYearContent += '<div class="row top-buffer"><table class="table"><thead><tr><th>Left Year</th><th class="text-center">Number</th><th class="text-right">Percentage</th></thead><tbody>';

			    $.each(AlumnusLeftYearContentData, function(i, item) {

			        AlumnusLeftYearContent +="<tr >";
			          AlumnusLeftYearContent +="<td>"+item.leftYear+"</td>";
			          AlumnusLeftYearContent +="<td class='text-center'>"+item.numberOfAlumin+"</td>";
			          AlumnusLeftYearContent +="<td class='text-right'>"+( ( item.numberOfAlumin / AlumnusTotalContentData )*100 ).toFixed(2)+" %</td>";
			        AlumnusLeftYearContent +="</tr>";

			    });

			    AlumnusLeftYearContent +='</tbody></table></div>';
		    AlumnusLeftYearContent +='</div>';

		   // 
		   AlumnusTotal.text(AlumnusTotalContentData);
		   AlumnusCountries.html(AlumnusCountriesContent);
		   AlumnusLeftYear.html(AlumnusLeftYearContent);



	  }).done(function() {


	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });

}

function dashLoadAllAlumni(){
	
	  var container               	= $( "#dashAlumnus_contentAll" );
	  
	  $( "#dashAlumnus_contentAll" ).slideDown();
	  $( "#dashAlumnus_contentAnalytics" ).hide();

	  var AlumnusContainer = '';

	  container.empty();

	  var url = base_url+"alumni/school";

	  $.getJSON( url, function(data) {
	      
	    AlumnusContainer = '<div class="col-md-10 col-md-offset-1">';
		    AlumnusContainer += '<div class="row"><div class="col-sm-12 text-center"><h4><strong> Alumni</strong></h4></div></div>';
			    AlumnusContainer += '<div class="row top-buffer"><table class="table" id="dashTableAlumnus"><thead><tr><th>Names</th><th>Left Year</th><th>Email</th><th>Phone Number</th><th>Occupation</th><th>Organization</th><th>Country</th></tr></thead><tbody>';


			    $.each(data, function(i, item) {

			        AlumnusContainer +="<tr >";
			          AlumnusContainer +="<td>"+item.names+"</td>";
			          AlumnusContainer +="<td>"+item.yearleft+"</td>";
			          AlumnusContainer +="<td>"+item.email+"</td>";
			          AlumnusContainer +="<td>"+item.phonenumber+"</td>";
			          AlumnusContainer +="<td>"+item.occupation+"</td>";
			          AlumnusContainer +="<td>"+item.organization+"</td>";
			          AlumnusContainer +="<td>"+item.country+"</td>";
			        AlumnusContainer +="</tr>";

			    });

			    AlumnusContainer +='</tbody></table></div>';
		    AlumnusContainer +='</div>';

	}).done(function() {

  		container.html(AlumnusContainer);
        abTablePagingTable('dashTableAlumnus');

    })
    .fail(function() {
    })
    .always(function() {
    });

}

//academics
function dashboardAcademics( hasBranch )
{	
	
	if ( hasBranch ) {
		$("#newClassBranchID").select2();
	}

    $("#dashboardAcademicSelectTeacher").select2().bind('change', getAcTeacherSelected);

	var dashboardAcademicSelectClassroom = $("#dashboardAcademicSelectClassroom");

	dashboardAcademicSelectClassroom.select2({ 
		    formatResult: classroomformatResult, 
		    formatSelection: classroomformatSelection, 
		    escapeMarkup: function(m) { return m; } 
	}).bind('change', getAcClassroomSelected );

	$("#selectAcChangeClassTeacherTeacher").select2();

	$("#dashboardAcSelectAddStudent").select2();

	$("#selectAddStudentTerm").select2();

	var elementTerm  = $("#selectAddStudentTerm");
    var selectedTerm = [];
    elementTerm.find("option").each(function(i,e){
       selectedTerm[selectedTerm.length]=$(e).attr("value");
    });
    elementTerm.select2().val(selectedTerm).trigger('change');

    $(document).on("click", "#Model_NewClassroomOpen", function () {
	    
	    $('#Model_NewClassroomTitle').html("New Class");
		$('#newClassActionType').val("0");
		$('#newClassSubmitBtn').text("Save");

		$('#newClassStream').show();
		$('#newClassStreamName').hide();

		clearAcNewUpdateClassForm();
	});

    

    $(document).on("click", "#Model_StudyGroupOpen", function () {
	    
	    $('#Model_NewSubjectTitle').html("Add Subject to Classroom");
		$('#newSubjectActionType').val("0");
		$('#newSubjectSubmitBtn').text("Save");

		$('#newSubjectClassContainer').show();

	});

	$(document).on("click", "#Model_SubjectToClassroomOpen", function () {
	    
	    $('#subjectSubjectIDContainer').hide();
	    $('#Model_NewSubjectTitle').html("Add Subject to Classroom");
		$('#newSubjectActionType').val("0");
		$('#newSubjectSubmitBtn').text("Save");

		$('#newSubjectClassContainer').show();

		clearAcNewUpdateSubjectForm();

	});

	var GrNewGroupStudentStudentContainer = $("#GrNewGroupStudentStudentContainer");
	var GrNewGroupStudentClassContainer   = $("#GrNewGroupStudentClassContainer");

	GrNewGroupStudentStudentContainer.show();
    GrNewGroupStudentClassContainer.hide();

    //Classes 
    	//Add students 
    		var AcClassEnrollmentStudentContainer 			=  $("#AcClassEnrollmentStudentContainer");
    		var AcClassEnrollmentLastYearClassContainer 	=  $("#AcClassEnrollmentLastYearClassContainer");
    		var AcClassEnrollmentLastYearStudentsContainer  =  $("#AcClassEnrollmentLastYearStudentsContainer");
    		var AcClassEnrollmentLoadingContainer 			=  $("#AcClassEnrollmentLoadingContainer");

    		AcClassEnrollmentStudentContainer.slideDown();
    		AcClassEnrollmentLastYearClassContainer.hide();
    		AcClassEnrollmentLastYearStudentsContainer.hide();
    		AcClassEnrollmentLoadingContainer.hide();

    	 	$('input[type=radio][name=AcClassEnrollmentSource]').change(function() {

    	 		AcClassEnrollmentLastYearStudentsContainer.hide();
    	 		// AcClassEnrollmentLastYearStudents.empty();

    	 		$("#AcClassEnrollmentSelectStudent").select2('data', null);
    	 		$("#AcClassEnrollmentLastYearClass").select2('data', null);
    	 		
		    	 if ( this.value == 1 )
		    	 {
	    	 		AcClassEnrollmentLastYearClassContainer.hide();
	    	 	    AcClassEnrollmentStudentContainer.slideDown();
    					

		    	 }else if( this.value == 2 ){
	    			AcClassEnrollmentStudentContainer.hide();
	    	 	    AcClassEnrollmentLastYearClassContainer.slideDown();

		    	 }
		        
		    });

    	 	$("#AcClassEnrollmentSelectStudent").select2();
    	 	
    	 	$("#AcClassEnrollmentLastYearClass").select2({
    	 			formatResult: classroomformatResult, 
			    	formatSelection: classroomformatSelection, 
			    	escapeMarkup: function(m) { return m; } 
    	 	}).bind('change', onAcClassEnrollmentLastYearClassChange );

    	 	submitAcClassEnrollmentForm();
    
    	//Add Subjects 

    		var AcClassSubjectThisYearClassContainer 			= $("#AcClassSubjectThisYearClassContainer");
    		var AcClassSubjectLastYearClassContainer 			= $("#AcClassSubjectLastYearClassContainer");
    		var AcClassSubjectsSelectedClassSubjectsContainer 	= $("#AcClassSubjectsSelectedClassSubjectsContainer");
    		var AcClassSubjectLoadingContainer 		 			= $("#AcClassSubjectLoadingContainer");

    		AcClassSubjectLastYearClassContainer.hide();
	    	AcClassSubjectThisYearClassContainer.slideDown();
	    	AcClassSubjectsSelectedClassSubjectsContainer.hide();
	    	AcClassSubjectLoadingContainer.hide();
	    	
    		$('input[type=radio][name=AcClassSubjectSource]').change(function() {

    	 		$("#AcClassSubjectThisYearClass").select2('data', null);
    	 		$("#AcClassSubjectLastYearClass").select2('data', null);
    	 		
		    	 if ( this.value == 1 )
		    	 {
	    	 		AcClassSubjectLastYearClassContainer.hide();
	    	 	    AcClassSubjectThisYearClassContainer.slideDown();
    					

		    	 }else if( this.value == 2 ){
	    			AcClassSubjectThisYearClassContainer.hide();
	    	 	    AcClassSubjectLastYearClassContainer.slideDown();

		    	 }
		        
		    });

		    $("#AcClassSubjectThisYearClass").select2({
    	 			formatResult: classroomformatResult, 
			    	formatSelection: classroomformatSelection, 
			    	escapeMarkup: function(m) { return m; } 
    	 	}).bind('change', onAcClassSubjectThisYearClassChange );

    	 	$("#AcClassSubjectLastYearClass").select2({
    	 			formatResult: classroomformatResult, 
			    	formatSelection: classroomformatSelection, 
			    	escapeMarkup: function(m) { return m; } 
    	 	}).bind('change', onAcClassSubjectLastYearClassChange );

    	 	submitAcClassSubjectsForm();

}

function getAcTeacherSelected()
{
	var dashboardAcademicSelectTeacher = $("#dashboardAcademicSelectTeacher");
	var selected           			   = dashboardAcademicSelectTeacher.select2('val');
	var teachername  	   	           = dashboardAcademicSelectTeacher.select2('data').text;
	var staffPhotoId              	   = dashboardAcademicSelectTeacher.select2().find(":selected").data("photoid");

	loadDashboardAcTeacherClassroom( selected , teachername , staffPhotoId  );
}

function getAcClassroomSelected(){

	var e = $("#dashboardAcademicSelectClassroom");
	var selected           = e.select2('val');
	var classname  		   = e.select2('data').text;
	var yearname           = e.find(":selected").data("yearname");

	loadDashSeClassroom(selected , yearname+' '+classname );

}



// function getAcStudyGroupSelected()
// {	
// 	var dashAcademicSelectStudyGroup = $("#dashAcademicSelectStudyGroup");

// 	var selected  	= dashAcademicSelectStudyGroup.select2('val');
// 	var groupName   = dashAcademicSelectStudyGroup.select2('data').text;

// 	console.log("dashboardAcademicSelectStudyGroup: "+ selected );
	
// 	$( "#dashContentainer_Academics").slideDown();
//     var container          = $( "#dashAcademics_content" );

//     container.empty();

//     var url = base_url+"api/Ac/studyGroup/"+selected;

//     leftContent = '';

//     $.getJSON( url, function(data) {

//     	console.log("data");
//     	console.log(data);

//         leftContent = '<div class="col-sm-12">';
//         leftContent += '<div class="col-sm-5">';
//         leftContent += '<div class="row"><div class="col-sm-6 text-right"><h4><strong>'+groupName+'</strong></h4></div><div class="col-sm-6"><a class="btn btn-primary pull-right" id="dashGrAddStudentToGroupBTN"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add students to this group</a></div></div>'; 
//         leftContent += '<div class="row"><table class="table table-hover" id="dashHrStudentAdvisorTable" ><thead><tr><th>Name</th><th class="text-center">Number Of Students</th><th class="text-center">Action</th></tr></thead><tbody>';
    
//         $.each(data, function(i, item) {

//             leftContent +="<tr id='"+item.id+"' >";
//               leftContent +="<td>"+item.studentNames+"</td>";
//               leftContent +="<td class='text-center'>"+item.studRegNber+"</td>";
//               leftContent +="<td class='text-center'>Remove</td>";
//             leftContent +="</tr>";

//         });
      
//         leftContent +='</tbody></table></div>';
//         leftContent +='</div>';
//         leftContent += '<div class="col-sm-1"></div>';
//         leftContent += '<div class="col-sm-5" id="dashHrSelectedStudentAdvisor">';
//         leftContent +='</div>';
      
//     }).done(function() {

//         container.append( leftContent );

//         //dashHrStudentAdvisorSelected();
//         //abTablePagingTable('dashHrStudentAdvisorTable');

//         $( "#dashGrAddStudentToGroupBTN" ).click(function() {
//             $('#Model_GrAddStudentToGroup').modal('show');
//         });

//         // if ( isSelected ) {
//         //     get_hr_LoadAdvisorStudents(advisorId, advisorName);
//         // }

//     })
//     .fail(function() {
//     })
//     .always(function() {
//     });

// }

//Displine 
function dashboardDispline( isDoubleRecord )
{	

	//New Fault 
		var displineNewFaultLoading = $("#displineNewFaultLoading");
		var DiFaultTypeContainer 	= $("#DiFaultTypeContainer");
		var DiFaultPointsName    	= $("#DiFaultPointsName");

		displineNewFaultLoading.hide();

		if ( !isDoubleRecord ) {
			DiFaultTypeContainer.hide();
		}	

		$('input[type=radio][name=faultType]').change(function() {

	    	 if ( this.value == 0 )
	    	 {
		 	    DiFaultPointsName.html("Points to deduct");

	    	 }else if( this.value == 1 ){

				DiFaultPointsName.html("Points to reward");

	    	 }
	        
	    });

	//New Deduct 
		var displineNewDeductionLoding = $("#displineNewDeductionLoding");
		displineNewDeductionLoding.hide();

	//
	$("#deleteDeducationLoading").hide();
	var DiDeductionDeleteConfirm = $("#DiDeductionDeleteConfirm");
	DiDeductionDeleteConfirm.click(function(){

		deleteDiDeducation();

	});

	var DiNewDeductionStudentContainer = $("#DiNewDeductionStudentContainer");
	var DiNewDeductionClassContainer   = $("#DiNewDeductionClassContainer");
    DiNewDeductionClassContainer.hide();
    DiNewDeductionStudentContainer.show();

	$("#dashboardDeSelectStudent").select2().bind('change', onDiViewStudentDispline );
	$("#dashboardDeSelectClass").select2({ 
            closeOnSelect: false,
            formatResult: newStudentformatResult,
            formatSelection: newStudentformatSelection
    }).bind('change', onDiViewClassDispline );

	$("#selectDiNewDeductionStudent").select2();
	$("#selectDiNewDeductionClass").select2({ 
            closeOnSelect: false,
            formatResult: newStudentformatResult,
            formatSelection: newStudentformatSelection
    });
    $("#selectDiNewDeductionFault").select2().bind('change', onDiSelectedDFault );

    //Update Deducation 
    	var selectDiUpdateDeductionStudent 	= $("#selectDiUpdateDeductionStudent");
    	var selectDiUpdateDeductionFault 	= $("#selectDiUpdateDeductionFault");

    	selectDiUpdateDeductionStudent.select2();
    	selectDiUpdateDeductionFault.select2();

    if ( isDoubleRecord ) {

    	var DiNewDeductionFaultContainer = $('#DiNewDeductionFaultContainer');

    	$('input[type=radio][name=NewRecordType]').change(function() {

	    	if ( this.value == 1 )
	    	{
	    	 	DiNewDeductionFaultContainer.slideDown();

	    	}else if( this.value == 2 ){
	    	 	DiNewDeductionFaultContainer.hide();
	    	}

	    });

    }

    $('input[type=radio][name=NewDeductionType]').change(function() {

    	 if ( this.value == 1 )
    	 {
	 	    DiNewDeductionClassContainer.hide();
			DiNewDeductionStudentContainer.slideDown();

    	 }else if( this.value == 2 ){
			DiNewDeductionStudentContainer.hide();
			DiNewDeductionClassContainer.slideDown();

    	 }else if( this.value == 3 ){
	 		DiNewDeductionStudentContainer.hide();
			DiNewDeductionClassContainer.hide();
    	 }
        
    });

}

function onDiViewStudentDispline()
{
	var dashboardDeSelectStudent= $("#dashboardDeSelectStudent");
	var selected            	= dashboardDeSelectStudent.select2('val');

  	var studentNameRegNumber    = dashboardDeSelectStudent.select2('data').text;
  	var regnumber               = dashboardDeSelectStudent.select2().find(":selected").data("regnumber");
  	var photoID 				= dashboardDeSelectStudent.select2().find(":selected").data("photoid");

 	get_studentDispline( regnumber, selected, studentNameRegNumber, photoID );

}

function onDiViewClassDispline()
{
	var DashboardDeSelectClass  = $("#dashboardDeSelectClass");
	var Selected            	= DashboardDeSelectClass.select2('val');
	var StreamName    			= DashboardDeSelectClass.select2('data').text;
	var YearName                = DashboardDeSelectClass.select2().find(":selected").data("yearname");

	get_DiClassDispline( Selected ,YearName , StreamName );

}

function onDiSelectedDFault()
{
	var selected  = $("#selectDiNewDeductionFault").select2('val');
	var points    = $("#selectDiNewDeductionFault").select2().find(":selected").data("points");
	var type      = $("#selectDiNewDeductionFault").select2().find(":selected").data("type");

	if ( type == 0 ) {
		$("#newDiDeductedPointsContainer").html("Points to deduct");
		
	}else if( type == 1 ){
		$("#newDiDeductedPointsContainer").html("Points to reward");
	}

	function setNewDiDeductedPoints( points )
	{
		$("#newDiDeductedPoints").val(points);
	}
	
	$.when( setNewDiDeductedPoints(points) ).done(function() {
		$('#displineNewDeduction').formValidation('revalidateField', 'points');
    });

}

//Student 
function dashboardStudents()
{
	$("#dashboardSeSelectStudent").select2({
	}).bind('change', onSeViewStudentProfile);

    $("#dashboardSeSelectStaff").select2().bind('change', onSeViewStaffProfile);

    //New Arrival 
	    $("#DashStNewArrivalLoding").hide();
	 	$("#NewArrivalStudentSelect").select2({
		    allowClear: true
		});

	// $( ".datepicker" ).datepicker();
	// $( ".datepicker" ).datepicker('option', 'dateFormat' , 'dd/mm/yy');

	 	$("#NewArrivalDate").datepicker({
	        "autoclose": true ,
	        dateFormat : 'dd/mm/yy'
		});

	 	$("#NewArrivalTimeHour").select2();
	 	$("#NewArrivalTimeMin").select2();

	 	submitStSaveNewArrival();
}


//Student Permission 
function dashboardStudentsPermission()
{	
	$.fn.modal.Constructor.prototype.enforceFocus = function () {};

	$("#NewStudentPermissionLoading").hide();

	// $("#dashboardStuPerSelectStudent").select2({
	//     formatResult: INPUTStudent_formatResult,
	//     formatSelection: INPUTStudent_formatSelection,
	//     escapeMarkup: function(m) { return m; }
	// });

	$("#NewStudPerSelectStudent").select2();

	var nowDateTime = moment.unix(moment().unix()).zone('+0200').format("DD/MM/YYYY hh:mm");
	
	$("#NewStudPerValidPeriod").daterangepicker({
	    "showDropdowns": true,
	    "showWeekNumbers": true,
	    "showISOWeekNumbers": true,
	    "timePicker": true,
	    "timePicker24Hour": true,
	    "autoApply": true,
	    "locale": {
	        "format": "DD/MM/YYYY HH:mm",
	        "separator": " - ",
	        "applyLabel": "Set Period",
	        "cancelLabel": "Cancel",
	        "fromLabel": "From",
	        "toLabel": "To",
	        "customRangeLabel": "Custom",
	        "daysOfWeek": [
	            "Su",
	            "Mo",
	            "Tu",
	            "We",
	            "Th",
	            "Fr",
	            "Sa"
	        ],
	        "monthNames": [
	            "January",
	            "February",
	            "March",
	            "April",
	            "May",
	            "June",
	            "July",
	            "August",
	            "September",
	            "October",
	            "November",
	            "December"
	        ],
	        "firstDay": 1 
	    },
	    "startDate": nowDateTime ,
	    "endDate": nowDateTime
	}, function(start, end, label) {
	  //console.log( "New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
	});

	//     $("#dashboardSeSelectStaff").select2().bind('change', onSeViewStaffProfile);
    //    //New Arrival 
	//     $("#DashStNewArrivalLoding").hide();
	//  	$("#NewArrivalStudentSelect").select2({
	// 	    allowClear: true
	// 	});
	// // $( ".datepicker" ).datepicker();
	// // $( ".datepicker" ).datepicker('option', 'dateFormat' , 'dd/mm/yy');
	//  	$("#NewArrivalDate").datepicker({
	// 	        "autoclose": true,
	// 	        dateFormat : 'dd/mm/yy'
	// 	});
	//  	$("#NewArrivalTimeHour").select2();
	//  	$("#NewArrivalTimeMin").select2();

	submitStSaveNewStudentPermission();
}

function dashboardSchoolTimeTable()
{	
	$("#dashboardTTSelectTeacher").select2().bind('change', onTTViewTeacherTimeTable );
	$("#dashboardTTSelectClass").select2({ 
        closeOnSelect: false ,
        formatResult: newStudentformatResult ,
        formatSelection: newStudentformatSelection
    }).bind('change', onTTViewClassTimeTable );

    getTtGenerateTimeTableValidate();
    getTtNewSubjectGroupValidate();

    $("#formDashTtNewTimeTableLoading").hide();
    $("#formDashTtNewTimeTable").show();

    $("#formDashTtNewGroupLoading").hide();
    $("#formTtStudyGroupGroup").show();

}

function loadStudentHome()
{
	
  $("#dashStudents_contentProfile").hide();
  $("#dashStudents_contentAnalytics").show();
  $("#dashStudents_contentLists").hide();
  
  var url = base_url+"dash/students";

  //
  var st_anlytEnrolledStudents  = $( "#st_anlytEnrolledStudents" );
  var st_anlytAverageAge 		= $( "#st_anlytAverageAge" );
  var st_anlytFemale 			= $( "#st_anlytFemale" );
  var st_anlytMale 				= $( "#st_anlytMale" );
  var st_anlytBoardingDay 		= $( "#st_anlytBoardingDay" );
  var st_anlytBoardingBoarding  = $( "#st_anlytBoardingBoarding" );

  var st_anlytParentBoth 		= $( "#st_anlytParentBoth" );
  var st_anlytParentBothPercent = $( "#st_anlytParentBothPercent" );

  var st_anlytParentOneFa 		= $( "#st_anlytParentOneFa" );
  var st_anlytParentOneFaPercent= $( "#st_anlytParentOneFaPercent" );

  var st_anlytParentOneMo 		= $( "#st_anlytParentOneMo" );
  var st_anlytParentOneMoPercent= $( "#st_anlytParentOneMoPercent" );

  var st_anlytParentOrphan 		 = $( "#st_anlytParentOrphan" );
  var st_anlytParentOrphanPercent= $( "#st_anlytParentOrphanPercent" );

  var st_anlytParentPrimary 	 = $( "#st_anlytParentPrimary" );
  var st_anlytParentsEmails      = $( "#st_anlytParentsEmails" );
  var missingPicture 			 = $( "#missingPicture");

  var st_anlytSchoolBranch 		 = $( "#st_anlytSchoolBranch" );

  var NumberOfStudent 	 ;
  var StudentsNberFemale ;
  var StudentsNberMale ;
  var NumberInBoarding;
  var NumberInDay;

  var schoolBranchContent = '';

  $.getJSON( url, function(data) {

  	  NumberOfStudent 	 = data.NumberOfStudent;
 	  StudentsNberFemale = data.StudentsNberFemale;
      StudentsNberMale   = data.StudentsNberMale;
      NumberInBoarding   = data.NumberInBoarding; 
      NumberInDay 		 = data.NumberInDay; 
      SchoolBranch       = data.SchoolBranch ;

	  st_anlytEnrolledStudents.text(NumberOfStudent);
	  st_anlytAverageAge.text(data.StudentsAverageAge);
	  st_anlytFemale.text(StudentsNberFemale);
	  st_anlytMale.text(StudentsNberMale);
	  st_anlytBoardingDay.text(NumberInDay);
	  st_anlytBoardingBoarding.text(NumberInBoarding );

	  st_anlytParentBoth.text(data.StudentWithBothParent);
	  st_anlytParentBothPercent.text(data.StudentWithBothParentPercentage);

	  st_anlytParentOneFa.text(data.StudentWithParentOneFa);
	  st_anlytParentOneFaPercent.text(data.StudentWithParentOneFaPercentage);

	  st_anlytParentOneMo.text(data.StudentWithParentOneMo);
	  st_anlytParentOneMoPercent.text(data.StudentWithParentOneMoPercentage);

	  st_anlytParentOrphan.text(data.StudentWithParentPassedAway);
	  st_anlytParentOrphanPercent.text(data.StudentWithParentPassedAwayPercentage);

	  st_anlytParentPrimary.text(data.StudentWithNoPrimaryContact);
	  st_anlytParentsEmails.text(data.StudentWithNoEmails);
	  missingPicture.text(data.MissingPicture);

	   //Current Fees (This Term) Payments Status
      schoolBranchContent +='<table class="table table-bordered" id="st_anlytBranchTable" ><thead>';
      	schoolBranchContent +='<tr ><th rowspan="2" class="text-center">Branch</th><th rowspan="2" class="text-center">Level</th><th rowspan="2" style="background-color:#e5e5ff;" class="text-center">Students</th><th rowspan="2" class="text-center">Class</th><th colspan="2" class="text-center">GENDER</th><th colspan="2" class="text-center">BOARDING</th></tr>';
      	schoolBranchContent +='<tr ><th class="text-center">Girl</th><th class="text-center">Boy</th><th class="text-center">Boarding</th><th class="text-center">Day</th></tr>';
      schoolBranchContent +='</thead>';
      schoolBranchContent +='<tbody>';

      var ExistingIdentifier = "";
	  var isTheFirsRecord    = true;

	  var TOT_Students 	= 0;
	  var TOT_Class		= 0;	

	  var TOT_Girl      = 0;
	  var TOT_Boy       = 0;

	  var TOT_Boarding  = 0;
	  var TOT_Day 		= 0;

	  var ALL_TOT_Students 	= 0;
	  var ALL_TOT_Class		= 0;	

	  var ALL_TOT_Girl      = 0;
	  var ALL_TOT_Boy       = 0;

	  var ALL_TOT_Boarding  = 0;
	  var ALL_TOT_Day 		= 0;

      $.each( SchoolBranch, function(i, item) {

      		CurrentIdentifier = item.id;

      		if ( CurrentIdentifier == ExistingIdentifier ) {

      		}else{

      			ExistingIdentifier = CurrentIdentifier ;

      			if (isTheFirsRecord) {
      				isTheFirsRecord = false;

      			}else{

         		 schoolBranchContent +="<tr>";
         			schoolBranchContent +="<td style='background-color:#ededed;' class='text-left'><i><b>TOTAL:</b></i></td>";
         		 	schoolBranchContent +="<td style='background-color:#ccccff;' class='text-center'>"+formatNumber(TOT_Students)+"</td>";
		            schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Class)+" </td>";
		            schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Girl)+" </td>";
		            schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Boy)+" </td>";
		            schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Boarding)+" </td>";
		            schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Day)+" </td>";
         		 schoolBranchContent +="</tr>";

         		  ALL_TOT_Students 	+= parseFloat(TOT_Students);
				  ALL_TOT_Class		+= parseFloat(TOT_Class);
				  ALL_TOT_Girl      += parseFloat(TOT_Girl);
				  ALL_TOT_Boy       += parseFloat(TOT_Boy);
				  ALL_TOT_Boarding  += parseFloat(TOT_Boarding);
				  ALL_TOT_Day 		+= parseFloat(TOT_Day);
 
         		  TOT_Students 	= 0;
				  TOT_Class		= 0;	
				  TOT_Girl      = 0;
				  TOT_Boy       = 0;
				  TOT_Boarding  = 0;
				  TOT_Day 		= 0;

      			}

      			var numberRowSpan = parseFloat(item.numberOfLevel) + 1 ;

      			schoolBranchContent +="<tr id="+item.id+">";
      				schoolBranchContent +="<td rowspan='"+numberRowSpan+"' >"+item.branchName+" </td>";
      		}
          			
          			TOT_Students 	+= parseFloat(item.numberOfStudent);
          			TOT_Class		+= parseFloat(item.numberOfClass);

          			TOT_Girl      	+= parseFloat(item.numberOfGirls);
          			TOT_Boy       	+= parseFloat(item.numberOfBoys);

          			TOT_Boarding  	+= parseFloat(item.numberOfBoarding);
          			TOT_Day 		+= parseFloat(item.numberOfDay);

		            schoolBranchContent +="<td class='text-left'>"+item.level+" </td>";
		            schoolBranchContent +="<td style='background-color:#e5e5ff;' class='text-center'>"+formatNumber(item.numberOfStudent)+" </td>";
		            schoolBranchContent +="<td class='text-center'>"+item.numberOfClass+" </td>";
		            schoolBranchContent +="<td class='text-center'>"+formatNumber(item.numberOfGirls)+" </td>";
		            schoolBranchContent +="<td class='text-center'>"+formatNumber(item.numberOfBoys)+" </td>";
		            schoolBranchContent +="<td class='text-center'>"+formatNumber(item.numberOfBoarding)+" </td>";
		            schoolBranchContent +="<td class='text-center'>"+formatNumber(item.numberOfDay)+" </td>";
		          schoolBranchContent +="</tr>";
      
      });

      schoolBranchContent +="<tr>";
		schoolBranchContent +="<td style='background-color:#ededed;' class='text-left'><i><b>TOTAL:</b></i></td>";
	 	schoolBranchContent +="<td style='background-color:#ccccff;' class='text-center'>"+formatNumber(TOT_Students)+" </td>";
    	schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Class)+" </td>";
    	schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Girl)+" </td>";
    	schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Boy)+" </td>";
    	schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Boarding)+" </td>";
   	 	schoolBranchContent +="<td style='background-color:#ededed;' class='text-center'>"+formatNumber(TOT_Day)+" </td>";
	 schoolBranchContent +="</tr>";

	  ALL_TOT_Students 	+= parseFloat(TOT_Students);
	  ALL_TOT_Class		+= parseFloat(TOT_Class);
	  ALL_TOT_Girl      += parseFloat(TOT_Girl);
	  ALL_TOT_Boy       += parseFloat(TOT_Boy);
	  ALL_TOT_Boarding  += parseFloat(TOT_Boarding);
	  ALL_TOT_Day 		+= parseFloat(TOT_Day);

	  schoolBranchContent +="<tr>";
		schoolBranchContent +="<td colspan='2' style='background-color:#d3d3d3;' class='text-left'><i><b>TOTAL:</b></i></td>";
	 	schoolBranchContent +="<td style='background-color:#b2b2ff; font-weight: 900; ' class='text-center'>"+formatNumber(ALL_TOT_Students)+" </td>";
    	schoolBranchContent +="<td style='background-color:#d3d3d3;' class='text-center'>"+formatNumber(ALL_TOT_Class)+" </td>";
    	schoolBranchContent +="<td style='background-color:#d3d3d3;' class='text-center'>"+formatNumber(ALL_TOT_Girl)+" </td>";
    	schoolBranchContent +="<td style='background-color:#d3d3d3;' class='text-center'>"+formatNumber(ALL_TOT_Boy)+" </td>";
    	schoolBranchContent +="<td style='background-color:#d3d3d3;' class='text-center'>"+formatNumber(ALL_TOT_Boarding)+" </td>";
   	 	schoolBranchContent +="<td style='background-color:#d3d3d3;' class='text-center'>"+formatNumber(ALL_TOT_Day)+" </td>";
	 schoolBranchContent +="</tr>";

      schoolBranchContent +="</tbody></table>";

      st_anlytSchoolBranch.html(schoolBranchContent);

  }).done(function() {

        $( "#donut-studentSchoolGender" ).empty();
        $( "#donut-studentSchoolBoarding" ).empty();

        var PercantageBoarding = ( ( NumberInBoarding / NumberOfStudent ) *100 ).toFixed(2);
        var PercantageDay      = ( ( NumberInDay / NumberOfStudent )*100 ).toFixed(2) ;

        var PercentageMale 	   = ( ( StudentsNberMale / NumberOfStudent )  *100 ).toFixed(2) ;
        var PercentageFemale   = ( ( StudentsNberFemale / NumberOfStudent )*100 ).toFixed(2) ;

        Morris.Donut({
		    element: 'donut-studentSchoolGender',
		    data: [
		      {label: "Male", value: PercentageMale  },
		      {label: "Female", value: PercentageFemale  }
		    ],
		    resize: true,
		    colors: ['#5CB85C','#679DC6','#679DC6','#95BBD7','#B0CCE1','#095791','#095085','#083E67','#052C48','#F56954'],
		    formatter: function (value, data) { return formatNumber(value)+ ' %'; }
		});

         Morris.Donut({
		    element: 'donut-studentSchoolBoarding',
		    data: [
		      {label: "Boarding", value:  PercantageBoarding },
		      {label: "Day", value:  PercantageDay }
		    ],
		    resize: true,
		    colors: ['#777','#95BBD7','#679DC6','#95BBD7','#B0CCE1','#095791','#095085','#083E67','#052C48','#F56954'],
		    formatter: function (value, data) { return formatNumber(value)+ ' %'; }
		});
    })
    .fail(function() {
    })
    .always(function() {
    });

}

function onMissingSelected()
{
	$( "table#stTableMissing" ).delegate( "td", "click", function() {

	  	id 			= $(this).closest('tr').attr('id');

	  	switch(id) {
		    case "1":
		    	studentGetMissedPrimaryContact();
		        break;

		    case "2":
		    	studentGetMissedParentEmails();
		       break;

	        case "3":
	        	console.log("3 Clicked");
		        break;

		    case "4":
	        	studentGetMissingPicture();
		        break;
		}

	});

}

function onInvalidSelected()
{
	$( "table#stTableInvalid" ).delegate( "td", "click", function() {

	  	id 			= $(this).closest('tr').attr('id');

		//alert(id);

	});
}


function studentGetMissedPrimaryContact()
{	
	$('#Model_studentMissingInvalid').modal();

	var ModalMissingInvalidTitle= $('#ModalMissingInvalidTitle');
	var ModalMissingInvalidTip	= $('#ModalMissingInvalidTip'); 
	var st_missingInvalidLoding = $('#st_missingInvalidLoding');
	var st_missingInvalidContent= $('#st_missingInvalidContent');

	var missedInvalidPrint 		= $('#missedInvalidPrint');
	missedInvalidPrint.html('<a class="btn btn-default pull-right" target="_blank" href="api/St/Students/PDF/missedPrimaryContact"><i class="fa fa-download"></i> Download</a>');

	ModalMissingInvalidTitle.text("Student with no Primary Contacts");
	ModalMissingInvalidTip.text("primary contact");

	st_missingInvalidLoding.show();
	st_missingInvalidContent.hide();

	var url      = base_url+"api/St/Students/missedPrimaryContact"; 
	var missedPC = '';

    $.getJSON( url, function(data) {

	    var studentIndex = 1;

    	missedPC += '<div class="row">';
    	missedPC += '<div class="col-sm-12"><table class="table table-hover" id="dashStMissedPC" ><thead><tr><th>Name</th><th class="text-center">Registration Number</th><th>Class</th></tr></thead><tbody>';

	    $.each(data, function(i, item) {

	        missedPC +="<tr class='atAddMissedPC' regnumber='"+item.studRegNber+"' >";
	          missedPC +="<td>"+item.studentNames+"</td>";
	          missedPC +="<td class='text-center' >"+item.studRegNber+"</td>";
	          missedPC +="<td >"+item.classroomName+"</td>";
	        missedPC +="</tr>";

			studentIndex++;


	    });

	    missedPC +='</tbody></table></div></div>';

    })
    .done(function() {

    	st_missingInvalidContent.html(missedPC);

    	stAddPrimaryContactClicked();
    	abTablePagingTable('dashStMissedPC');

    })
    .fail(function() {

    })
    .always(function() {

    	st_missingInvalidLoding.hide();
		st_missingInvalidContent.show();

    });
}

function studentGetMissedParentEmails()
{

	$('#Model_studentMissingInvalid').modal();

	var ModalMissingInvalidTitle= $('#ModalMissingInvalidTitle');
	var ModalMissingInvalidTip	= $('#ModalMissingInvalidTip'); 
	var st_missingInvalidLoding = $('#st_missingInvalidLoding');
	var st_missingInvalidContent= $('#st_missingInvalidContent');

	var missedInvalidPrint 		= $('#missedInvalidPrint');
	missedInvalidPrint.html('<a class="btn btn-default pull-right" target="_blank" href="api/St/Students/PDF/missedParentsEmails"><i class="fa fa-download"></i> Download</a>');

	ModalMissingInvalidTitle.text("Student Without Parents' Emails");
	ModalMissingInvalidTip.text("parents emails");

	st_missingInvalidLoding.show();
	st_missingInvalidContent.hide();

	var url      = base_url+"api/St/Students/missedParentsEmails"; 
	var missedPC = '';

    $.getJSON( url, function(data) {

	    var studentIndex = 1;

    	missedPC += '<div class="row">';
    	missedPC += '<div class="col-sm-12"><table class="table table-hover" id="dashStMissedPC" ><thead><tr><th>Name</th><th class="text-center">Registration Number</th><th>Class</th></tr></thead><tbody>';

	    $.each(data, function(i, item) {

	        missedPC +="<tr class='atAddMissedPC' regnumber='"+item.studRegNber+"' >";
	          missedPC +="<td>"+item.studentNames+"</td>";
	          missedPC +="<td class='text-center' >"+item.studRegNber+"</td>";
	          missedPC +="<td >"+item.classroomName+"</td>";
	        missedPC +="</tr>";

			studentIndex++;

	    });

	    missedPC +='</tbody></table></div></div>';

    })
    .done(function() {

    	st_missingInvalidContent.html(missedPC);

    	stAddPrimaryContactClicked();
    	abTablePagingTable('dashStMissedPC');

    })
    .fail(function() {

    })
    .always(function() {

    	st_missingInvalidLoding.hide();
		st_missingInvalidContent.show();

    });
}

function studentGetMissingPicture()
{	
	$('#Model_studentMissingInvalid').modal();

	var ModalMissingInvalidTitle= $('#ModalMissingInvalidTitle');
	var ModalMissingInvalidTip	= $('#ModalMissingInvalidTip'); 
	var st_missingInvalidLoding = $('#st_missingInvalidLoding');
	var st_missingInvalidContent= $('#st_missingInvalidContent');

	var missedInvalidPrint 		= $('#missedInvalidPrint');
	missedInvalidPrint.html('<a class="btn btn-default pull-right" target="_blank" href="api/St/Students/PDF/missedPicture"><i class="fa fa-download"></i> Download</a>');


	ModalMissingInvalidTitle.text("Missing Pictures");
	ModalMissingInvalidTip.text("Picture");

	st_missingInvalidLoding.show();
	st_missingInvalidContent.hide();

	var url      = base_url+"api/St/Students/missedPicture"; 
	var missedPC = '';

    $.getJSON( url, function(data) {

	     var studentIndex = 1;

    	missedPC += '<div class="row">';
    	missedPC += '<div class="col-sm-12"><table class="table table-hover" id="dashStMissedPC" ><thead><tr><th>Name</th><th class="text-center" >Registration Number</th><th>Class</th></tr></thead><tbody>';

	    $.each(data, function(i, item) {

	        missedPC +="<tr class='atAddMissedPC' regnumber='"+item.studRegNber+"' >";
	          missedPC +="<td>"+item.studentNames+"</td>";
	          missedPC +="<td class='text-center'>"+item.studRegNber+"</td>";
	          missedPC +="<td>"+item.classroomName+"</td>";
	        missedPC +="</tr>";
			studentIndex++;
			
	    });

	    missedPC +='</tbody></table></div></div>';

    })
    .done(function() {

    	st_missingInvalidContent.html(missedPC);

    	stAddPrimaryContactClicked();
    	abTablePagingTable('dashStMissedPC');

    })
    .fail(function() {

    })
    .always(function() {

    	st_missingInvalidLoding.hide();
		st_missingInvalidContent.show();

    });
}

function stAddPrimaryContactClicked()
{	
	$( "table#dashStMissedPC" ).delegate( "td", "click", function() {

	    var regnumber 	= $(this).closest('tr').attr('regnumber');
	  	$('#Model_studentMissingInvalid').modal('toggle');
	  	get_profile_student(regnumber);

	});
}

function onSeViewStudentProfile()
{

	var dashboardSeSelectStudent= $("#dashboardSeSelectStudent");
	var selected            	= dashboardSeSelectStudent.select2('val');
  	var studentNameRegNumber    = dashboardSeSelectStudent.select2('data').text;
  	var regnumber               = dashboardSeSelectStudent.select2().find(":selected").data("regnumber");

 	get_profile_student(regnumber);


}


//HumanResource
function dashboardHumanResource()
{
	$("#dashHrSelectStudent").select2().bind('change', onHrStaffSelected);

}

function onHrStaffSelected()
{
	var selected      = $("#dashHrSelectStudent").select2('val');
	get_profile_staff(selected);

	$("#dashContentainer_HumanResource").show();
}

//Gradebook
	function dashboardGradebook() {

	    $("#dashboardTecherselectSubject").select2().bind('change', onDashTeClassroomSubject);
	    $("#dashTeacherSelectWorkType").select2().bind('change', onDashTeWorkTypeChange);
	    $("#dashTeAssessmentPartition").hide();
	    $("#generatedReportLoading").hide();

	    $( "#dashGrTeachersComments" ).click(function() {
		  	onDashLoadTeachersSubjects();

		});

	    $( "#dashGrBtnResults" ).click(function() {
		  
		  	onDashLoadResultSection(0 , false , null , null );

		});

	    $( "#dashGrBtnReportCards" ).click(function() {
		  	onDashLoadReportSection(true);

		});

	     $( "#dashGrBtnAssessmentsTracking" ).click(function() {
		  	onDashLoadAssessmentsTracking();

		});

	    $( "#dashGrBtnDeliberation" ).click(function() {
		  	onDashLoadDeliberationSection();

		});

	    $( "#dashGrBtnProclamation" ).click(function() {
		  	onDashLoadProclamationSection();

		});

	    $( "#dashGrBtnSendResults" ).click(function() {
		  	onDashLoadSendResultsSection(true);

		});

		var generatedReportDeleteConfirm = $("#generatedReportDeleteConfirm");
		generatedReportDeleteConfirm.click(function(){

			deleteGrDownload();

		});

		var dashGrProStudentSelect = $( "#dashGrProStudentSelect" );
		dashGrProStudentSelect.select2();

		var DashGrProcEditAssessmentLoding = $( "#DashGrProcEditAssessmentLoding" );
		DashGrProcEditAssessmentLoding.hide();

		var formDashGrProcMessage		   = $( "#formDashGrProcMessage" );
		formDashGrProcMessage.show();

		$( "#formDashGrProcMessage" ).submit(function( event ) {
		  event.preventDefault();

		  submitDashGrProclamationSendSMS();
		  
		  DashGrProcEditAssessmentLoding.show();
		  formDashGrProcMessage.hide();
		  
		});

	}


	var ReGroupedStudyGroupAssessment = [[]];
	var ReGroupedSubjects 			  = [[]];
	var ReGroupedAssessment 		  = [[]];


	function onDashLoadResultSection( AssessmentID, isNewAssessment , AssMax , AssTeacherName )
	{
		var dashGradebook_contentAnalytics 	= $("#dashGradebook_contentAnalytics")
		var dashGradebook_contentOther 		= $("#dashGradebook_contentOther");

		dashGradebook_contentAnalytics.hide();
		dashGradebook_contentOther.slideDown();

		var content  = '';
		var url 	 = base_url+"api/Gr/Assessment"; 

		$.getJSON( url, function(data) {

			var IsTeachingSet 		= data.IsTeachingSet ;

			if ( IsTeachingSet ) {

				var academicsStudyGroups 	= data.academicsStudyGroups ;
				var assessment 				= data.assessment ;
				var assessmentResults 		= data.assessmentResults ;

				var StudyGroupsSize 		= academicsStudyGroups.length ;
				var assessmentSize 			= assessment.length ;


				content 	+= '<div  class="col-sm-12">';
					content  	+= '<div  class="col-sm-3 ">';
						content  	+= '<div class="row text-center"><h4>Result</h4></div>';
						content  	+= '<form  id="formDashGetSelectedResults" class="form-horizontal top-buffer" role="form" >';
							content  	+= '<input type="hidden" name="getResultIsTeachingSet" id="getResultIsTeachingSet"  value="1" />';
							content  	+= '<div class="form-group"><label class="col-sm-3 control-label">Teaching Set</label><div class="col-sm-7"><select name="getResultStudyGroup" id="getResultStudyGroupSelect" class="form-control" >';
								
								//add options
								var StudyGroupsOptions 	= '<option></option>';

								for ( var i=0; i< StudyGroupsSize ; i++ ) {

									 StudyGroupsOptions += '<option value="'+academicsStudyGroups[i].id+'" data-teacher="'+academicsStudyGroups[i]['teacherID'] +'" data-teacherphoto="'+academicsStudyGroups[i]['teacherphoto']+'" data-teachername="'+academicsStudyGroups[i]['teachername']+'" >'+academicsStudyGroups[i].name+'</option>';
										    
								}

							content 	+= StudyGroupsOptions;
							content  	+= '</select></div></div>';

							content  	+= '<div class="form-group" ><label class="col-sm-3 control-label">Assessment</label><div class="col-sm-7"><select name="getResultAssessment" id="getResultAssessmentSelect" class="form-control" >';
							content 	+= '</select></div></div>';

							content 	+= '<div class="form-group top-buffer"><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-primary btn-block" type="submit" id="getResultSubmit" >View Results</button></div></div>';
						content 	+= '</form>';
					content 	+= '</div>';
					content 	+= '<div class="col-sm-9">';

						content 	+= '<div id="dashGrResultSection">';

							content  	+= '<div class="row text-center"><h4>Recorded Results</h4></div>';
							content  	+= '<div class="row">';

								var TableContent ='';
								TableContent +='<table class="table table-striped abDataTable" id="paGrAssessementRecords" ><thead><tr><th>Teaching Set</th><th>Teacher</th><th>Assessment</th><th class="text-center">Max</th><th class="text-center">Average</th><th class="text-center">Recorded Marks</th><th class="text-center">Added on</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead><tbody>';
								
								$.each(assessmentResults, function(i, item) {

							          TableContent +="<tr id="+item.id+" tb_IsTeachingSet= 1 sg_studygroupid="+item.sg_studyGroupID+"  asstype="+item.asTypeId+" period="+item.period+" max='"+item.maximumMarks+"' assessmentdate='"+item.assessmentDate+"' teachername='"+item.teachername+"' >";
							            TableContent +="<td >"+item.studyGroupName+"</td>";
							            TableContent +="<td >"+item.teachername+"</td>";
							            TableContent +="<td >"+item.assessment+"</td>"; 
							            TableContent +="<td class='text-right'>"+item.maximumMarks+"</td>"; 
							            TableContent +="<td class='text-right'>";
							            	TableContent += ( item.average > 0 ) ? item.average+"%" : "<span class='text-red'>Not Recorded</span>";
							            TableContent +="</td>";
							            TableContent +="<td class='text-center'><b>"+item.numberOfRecords+"</b> of <b>"+item.numberOfStudent+"</b></td>";
							            TableContent +="<td class='text-center'>"+date_moment(item.addedOn)+"</td>";
							            TableContent += "<td class='text-center GrAssessmentEdit'><a href='#'><i class=' fa fa-pencil text-blue'></i></a></td>";
							            	if ( item.numberOfRecords > 0 ) 
							            	{
							            		TableContent += "<td class='text-center'></td>";
							            	}else{
							            		TableContent += "<td class='text-center GrAssessmentDelete'><a href='#'><i class=' fa fa-times text-red'></i></a></td>";
							            	}
							          TableContent +="</tr>";

							   	});

								TableContent +="</tbody></table>";
							content  	+= '</div>';
						content     += TableContent;

						content 	+= '</div>';

						content 	+= '<div id="dashGrResultSelectedSection">';
							content  	+= '<div class="row top-buffer">';
								content 	+= '<div class="col-sm-12" id="dashGrSelectResultDescription">';
								content 	+= '</div>';
							content 	+= '</div>';
							content  	+= '<div class="row top-buffer">';
								content 	+= '<div class="col-sm-12" id="dashGrSelectResultSection" with="200px">';
										content 	+= '<div id="dashGrSelectResultSLoading">';
											content 	+= '<center><div class="row"><img src="../packages/assets/img/loading.gif" alt="Loading..." height="35"></div><div class="row"><p>Loading results...</p></div></center>';
										content 	+= '</div>';
										content 	+= '<div id="dashGrSelectResultSResults" class="ab_SlickGrid" style="width:550px;">';
										content 	+= '</div>';
								content 	+= '</div>';
							content 	+= '</div>';
						content 	+= '</div>';

					content 	+= '</div>';
				content 	+= '</div>';

				//store assessments in array 
				for ( var i = 0; i < StudyGroupsSize; i++) {

					ReGroupedStudyGroupAssessment[academicsStudyGroups[i].id] = new Array();

				}

				for ( var i = 0; i < assessmentSize; i++ ) {


					var assessmentid 		= assessment[i].id;
					var sg_studyGroupID   	= assessment[i].sg_studyGroupID;
					var assessmentNumber 	= assessment[i].assessmentNumber;
					var maximumMarks 		= assessment[i].maximumMarks;
					var assessmentDate 		= assessment[i].assessmentDate;
					var isExamType 			= assessment[i].isExamType;
					var teachTheSubject		= assessment[i].teachTheSubject;
					var assessmentType		= assessment[i].assessmentType;

					var row = [];

					row['assessmentid'] 	= assessmentid;
					row['sg_studyGroupID'] 	= sg_studyGroupID;
					row['assessmentNumber'] = assessmentNumber;
					row['maximumMarks'] 	= maximumMarks;
					row['assessmentDate'] 	= assessmentDate;
					row['isExamType'] 		= isExamType;
					row['teachTheSubject'] 	= teachTheSubject;
					row['assessmentType'] 	= assessmentType;

					ReGroupedStudyGroupAssessment[sg_studyGroupID].push(row);

				}

				dashGradebook_contentOther.html(content);

				$(" #getResultStudyGroupSelect" ).select2().bind('change', getResultStudyGroupSelected);

				// $(" #getResultSubjectSelect" ).select2().bind('change', getResultSubjectSelected);

				$(" #getResultAssessmentSelect" ).select2();
				$( "#dashGrSelectResultSLoading" ).hide();

				onDashLoadResultSectionValidate();

				// var dashGrResultSection 		= $( "#dashGrResultSection" );
				// var dashGrResultSelectedSection = $( "#dashGrResultSelectedSection" );
				
				// dashGrResultSection.show();
				// dashGrResultSelectedSection.hide();

				abTablePagingTable("paGrAssessementRecords");
				dashGrRecordedResultsSelected();
				dashGrRecordedResultsDelete();
				
				dashGrRecordedResultsEdit();

				if ( AssessmentID > 0 ) 
				{

					AssessmentID 				   = AssessmentID ;
			  		assessmentMax 				   = AssMax ;
			  		// assessmentDate 				   = AssDate ;
			  		teacherName 				   = AssTeacherName ;

					var dashGrSelectResultSLoading 		= $("#dashGrSelectResultSLoading");
					var dashGrSelectResultSResults 		= $("#dashGrSelectResultSResults");
					var dashGrSelectResultDescription 	= $("#dashGrSelectResultDescription");

					dashGrSelectResultSLoading.slideDown();
					dashGrSelectResultSResults.hide();

					var ResultsInfo = '';
					ResultsInfo += '<div class="row"><div class="col-sm-12" >';
						ResultsInfo +='<div class="col-sm-1" ><b>Max: </b><span id="getResultAssessmentMax" >'+assessmentMax+'</span></div>';
						// ResultsInfo +='<div class="col-sm-2" ><b>Done on: </b> '+date_moment(assessmentDate)+'</div>';
						ResultsInfo +='<div class="col-sm-3" ><span id="slickGridConsole"></span></div>';
						ResultsInfo +='<div class="col-sm-4" >';
							ResultsInfo +='<span id="editResultsOptions"></span>&nbsp;&nbsp;&nbsp;';
						ResultsInfo +='</div>';
						ResultsInfo +='<div class="col-sm-2 pull-right" ><b>Teacher:</b> '+teacherName+'</div>';
					ResultsInfo += '</div></div>';

					$("#dashGrSelectResultDescription").html(ResultsInfo);

					DashTeLoadResults( 1, 0, AssessmentID, null );

		            dashGrSelectResultSLoading.hide();
		         	dashGrSelectResultSResults.slideDown();

				}

			}else{

				var classrooms 			= data.classrooms ;
				var classroomsSubjects 	= data.classroomsSubjects ;
				var assessment 			= data.assessment ;
				var assessmentResults 	= data.assessmentResults ;

				var classroomsSize 		= classrooms.length ;
				var SubjectSize 		= classroomsSubjects.length ;
				var assessmentSize 		= assessment.length ;

				content 	+= '<div  class="col-sm-12">';
					content  	+= '<div  class="col-sm-3 ">';
						content  	+= '<div class="row text-center"><h4>Result</h4></div>';
						content  	+= '<form  id="formDashGetSelectedResults" class="form-horizontal top-buffer" role="form" >';

							content  	+= '<input type="hidden" name="getResultIsTeachingSet" id="getResultIsTeachingSet"  value="0" />';
							content  	+= '<div class="form-group"><label class="col-sm-3 control-label">Class</label><div class="col-sm-7"><select name="getResultClass" id="getResultClassSelect" class="form-control" >';
								
								//add options
								var ClassOptions 	= '<option></option>';

								var classidentifier = "";
								var isTheFirsRecord = true;

								for ( var i=0; i< classroomsSize ; i++ ) {

									if ( classrooms[i].classidentifier == classidentifier ){
									    ClassOptions += '<option value="'+classrooms[i].id+'" data-yearname="'+classrooms[i].YearName+'" data-level="'+classrooms[i].levelID+'" >'+classrooms[i].name+'</option>';
									
									}else{

									    classidentifier = classrooms[i].classidentifier;
								    	if ( isTheFirsRecord ){

									    	isTheFirsRecord = false; 

								    	}else{
									    	 ClassOptions += '</optgroup>';
									    }

								    	ClassOptions += '<optgroup label="'+classrooms[i].YearName+'">';
								   		ClassOptions += '<option value="'+classrooms[i].id+'" data-yearname="'+classrooms[i].YearName+'" data-level="'+classrooms[i].levelID+'" >'+classrooms[i].name+'</option>';
								
									}
										    
								}

							content 	+= ClassOptions;
							content  	+= '</select></div></div>';

							content  	+= '<div class="form-group" ><label class="col-sm-3 control-label">Subject</label><div class="col-sm-7"><select name="getResultSubject" id="getResultSubjectSelect" class="form-control" >';
							content  	+= '</select></div></div>';

							content  	+= '<div class="form-group" ><label class="col-sm-3 control-label">Assessment</label><div class="col-sm-7"><select name="getResultAssessment" id="getResultAssessmentSelect" class="form-control" >';
							content 	+= '</select></div></div>';

							content 	+= '<div class="form-group top-buffer"><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-primary btn-block" type="submit" id="getResultSubmit" >View Results</button></div></div>';
						content 	+= '</form>';
					content 	+= '</div>';
					content 	+= '<div class="col-sm-9">';

						content 	+= '<div id="dashGrResultSection">';

							content  	+= '<div class="row text-center"><h4>Recorded Results </h4></div>';
							content  	+= '<div class="row">';

								var TableContent ='';
								TableContent +='<table class="table table-striped abDataTable" id="paGrAssessementRecords" ><thead><tr><th>Class</th><th>Subject</th><th>Assessment</th><th class="text-center">Max</th><th class="text-center">Average</th><th class="text-center">Recorded Marks</th><th class="text-center">Added on</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead><tbody>';
								
								$.each(assessmentResults, function(i, item) {

							          TableContent +="<tr id="+item.id+" tb_IsTeachingSet= 0 classid="+item.annualClassroomID+" anclasssubjid ="+item.anClsrmSbjtID+" asstype="+item.asTypeId+" period="+item.period+" max='"+item.maximumMarks+"' assessmentdate='"+item.assessmentDate+"' teachername='"+item.teachername+"' >";
							            TableContent +="<td id='Assessment_"+item.id+"' >"+item.className+"</td>";
							            TableContent +="<td >"+item.subjectName+"</td>";
							            TableContent +="<td >"+item.assessment+"</td>"; 
							            TableContent +="<td class='text-right'>"+item.maximumMarks+"</td>"; 
							            TableContent +="<td class='text-right'>";
							            	TableContent += ( item.average > 0 ) ? item.average+"%" : "<span class='text-red'>Not Recorded</span>";
							            TableContent +="</td>";
							            TableContent +="<td class='text-center'><b>"+item.numberOfRecords+"</b> of <b>"+item.numberOfStudent+"</b></td>";
							            TableContent +="<td class='text-center'>"+date_moment(item.addedOn)+"</td>";
							            TableContent += "<td class='text-center GrAssessmentEdit'><a href='#'><i class=' fa fa-pencil text-blue'></i></a></td>";
							            	if ( item.numberOfRecords > 0 ) 
							            	{
							            		TableContent += "<td class='text-center'></td>";
							            	}else{
							            		TableContent += "<td class='text-center GrAssessmentDelete'><a href='#'><i class=' fa fa-times text-red'></i></a></td>";
							            	}
							          TableContent +="</tr>";

							   	});

								TableContent +="</tbody></table>";
							content  	+= '</div>';
						content     += TableContent;

						content 	+= '</div>';

						content 	+= '<div id="dashGrResultSelectedSection">';
							content  	+= '<div class="row top-buffer">';
								content 	+= '<div class="col-sm-12" id="dashGrSelectResultDescription">';
								content 	+= '</div>';
							content 	+= '</div>';
							content  	+= '<div class="row top-buffer">';
								content 	+= '<div class="col-sm-12" id="dashGrSelectResultSection" with="200px">';
										content 	+= '<div id="dashGrSelectResultSLoading">';
											content 	+= '<center><div class="row"><img src="../packages/assets/img/loading.gif" alt="Loading..." height="35"></div><div class="row"><p>Loading results...</p></div></center>';
										content 	+= '</div>';
										content 	+= '<div id="dashGrSelectResultSResults" class="ab_SlickGrid" style="width:550px;">';
										content 	+= '</div>';
								content 	+= '</div>';
							content 	+= '</div>';
						content 	+= '</div>';

					content 	+= '</div>';
				content 	+= '</div>';

				//store subjects in array 
				for ( var i = 0; i < classroomsSize; i++) {

					ReGroupedSubjects[classrooms[i].id] = new Array();

				}

				for ( var i = 0; i < SubjectSize; i++ ) {

					var subjectid 			= classroomsSubjects[i].id;
					var classId   			= classroomsSubjects[i].classId;
					var subjectname 		= classroomsSubjects[i].subjectname;
					var teacherId   		= classroomsSubjects[i].teacherID;
					var teacherphoto 		= classroomsSubjects[i].teacherphoto;
					var teacherName         = classroomsSubjects[i].teachername;

					var row = [];

					row['id'] 				= subjectid;
					row['subjectname'] 		= subjectname;
					row['teacherid'] 		= teacherId;
					row['teacherphoto'] 	= teacherphoto;
					row['teachername'] 		= teacherName;

					ReGroupedSubjects[classId].push(row);

				}

				//store assessments in array 
				for ( var i = 0; i < SubjectSize; i++) {

					ReGroupedAssessment[classroomsSubjects[i].id] = new Array();

				}

				for ( var i = 0; i < assessmentSize; i++ ) {

					var assessmentid 		= assessment[i].id;
					var anClsrmSbjtID   	= assessment[i].anClsrmSbjtID;
					var assessmentNumber 	= assessment[i].assessmentNumber;
					var maximumMarks 		= assessment[i].maximumMarks;
					var assessmentDate 		= assessment[i].assessmentDate;
					var isExamType 			= assessment[i].isExamType;
					var teachTheSubject		= assessment[i].teachTheSubject;
					var assessmentType		= assessment[i].assessmentType;

					var row = [];

					row['assessmentid'] 	= assessmentid;
					row['anClsrmSbjtID'] 	= anClsrmSbjtID;
					row['assessmentNumber'] = assessmentNumber;
					row['maximumMarks'] 	= maximumMarks;
					row['assessmentDate'] 	= assessmentDate;
					row['isExamType'] 		= isExamType;
					row['teachTheSubject'] 	= teachTheSubject;
					row['assessmentType'] 	= assessmentType;

					ReGroupedAssessment[anClsrmSbjtID].push(row);

				}

				dashGradebook_contentOther.html(content);

				$(" #getResultClassSelect" ).select2({ 
			            formatResult: newStudentformatResult,
			            formatSelection: newStudentformatSelection
			    }).bind('change', getResultClassSelected);

				$(" #getResultSubjectSelect" ).select2().bind('change', getResultSubjectSelected);
				$(" #getResultAssessmentSelect" ).select2();
				
				$( "#dashGrSelectResultSLoading" ).hide();

				onDashLoadResultSectionValidate();

				var dashGrResultSection 		= $( "#dashGrResultSection" );
				var dashGrResultSelectedSection = $( "#dashGrResultSelectedSection" );
				
				dashGrResultSection.show();
				dashGrResultSelectedSection.hide();

				abTablePagingTable("paGrAssessementRecords");

				dashGrRecordedResultsSelected();
				dashGrRecordedResultsDelete();
				dashGrRecordedResultsEdit();

				if ( AssessmentID > 0 ) 
				{

					AssessmentID 				   = AssessmentID ;
			  		assessmentMax 				   = AssMax ;
			  		// assessmentDate 				   = AssDate ;
			  		teacherName 				   = AssTeacherName ;

					var dashGrSelectResultSLoading 		= $("#dashGrSelectResultSLoading");
					var dashGrSelectResultSResults 		= $("#dashGrSelectResultSResults");
					var dashGrSelectResultDescription 	= $("#dashGrSelectResultDescription");

					dashGrSelectResultSLoading.slideDown();
					dashGrSelectResultSResults.hide();

					var ResultsInfo = '';
					ResultsInfo += '<div class="row"><div class="col-sm-12" >';
						ResultsInfo +='<div class="col-sm-1" ><b>Max: </b><span id="getResultAssessmentMax" >'+assessmentMax+'</span></div>';
						// ResultsInfo +='<div class="col-sm-2" ><b>Done on: </b> '+date_moment(assessmentDate)+'</div>';
						ResultsInfo +='<div class="col-sm-3" ><span id="slickGridConsole"></span></div>';
						ResultsInfo +='<div class="col-sm-4" >';
							ResultsInfo +='<span id="editResultsOptions"></span>&nbsp;&nbsp;&nbsp;';
						ResultsInfo +='</div>';
						ResultsInfo +='<div class="col-sm-2 pull-right" ><b>Teacher:</b> '+teacherName+'</div>';
					ResultsInfo += '</div></div>';

					$("#dashGrSelectResultDescription").html(ResultsInfo);

					DashTeLoadResults( 0,  0, AssessmentID, null );

		            dashGrSelectResultSLoading.hide();
		         	dashGrSelectResultSResults.slideDown();

				}

			}


		})
		.done(function() {

		})
		.fail(function() {
		})
		.always(function() {
		});
	}

	function onDashLoadResultSectionValidate()
	{
		//validation
		var formDashGetSelectedResults = $('#formDashGetSelectedResults');

		formDashGetSelectedResults.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
            	getResultStudyGroup: {
                    validators: {
                        notEmpty: {
                            message: 'Teaching Set is required'
                        }
                    }
                },
                getResultClass: {
                    validators: {
                        notEmpty: {
                            message: 'Class is required'
                        }
                    }
                },
                getResultSubject: {
                    validators: {
                        notEmpty: {
                            message: 'Subject is required'
                        }
                    }
                },
                getResultAssessment: {
                    validators: {
                        notEmpty: {
                            message: 'Assessment is required'
                        }
                    }
                }
            }
        })
		.on('success.form.fv', function(e , data) {

        	e.preventDefault();

        	var frm = formDashGetSelectedResults;
			var url = base_url+"api/Gr/Results"; 

			var dashGrSelectResultSLoading 		= $("#dashGrSelectResultSLoading");
			var dashGrSelectResultSResults 		= $("#dashGrSelectResultSResults");
			var dashGrSelectResultDescription 	= $("#dashGrSelectResultDescription");

			var getResultIsTeachingSet 			=  $("#getResultIsTeachingSet").val();

			dashGrSelectResultSLoading.slideDown();
			dashGrSelectResultSResults.hide();

			if ( getResultIsTeachingSet == 1 ) {

				var getResultStudyGroupSelect  = $("#getResultStudyGroupSelect");
				var subjectteacher         	   = getResultStudyGroupSelect.find(":selected").data("teacher");
				var teacherPhoto 			   = getResultStudyGroupSelect.find(":selected").data("teacherphoto");
				var teacherName 			   = getResultStudyGroupSelect.find(":selected").data("teachername");

			}else{

				var getResultSubjectSelect     = $("#getResultSubjectSelect");
				var subjectteacher         	   = getResultSubjectSelect.find(":selected").data("teacher");
				var teacherPhoto 			   = getResultSubjectSelect.find(":selected").data("teacherphoto");
				var teacherName 			   = getResultSubjectSelect.find(":selected").data("teachername");
			}	

			var getResultAssessmentSelect  = $("#getResultAssessmentSelect");
			var selectedAssessment         = getResultAssessmentSelect.select2('val');
			var selectedAsType 			   = getResultAssessmentSelect.select2().find(":selected").data("assessmentname");
			var assessmentMax			   = getResultAssessmentSelect.select2().find(":selected").data("assessmentmax");
			var assessmentDate			   = getResultAssessmentSelect.select2().find(":selected").data("assessmentdate"); 

			var ResultsInfo = '';
			ResultsInfo += '<div class="row"><div class="col-sm-12" >';
				ResultsInfo +='<div class="col-sm-1" ><b>Max: </b><span id="getResultAssessmentMax">'+assessmentMax+'</span></div>';
				ResultsInfo +='<div class="col-sm-2" ><b>Done on: </b> '+date_moment(assessmentDate)+'</div>';
				ResultsInfo +='<div class="col-sm-3" ><span id="slickGridConsole"></span></div>';
				ResultsInfo +='<div class="col-sm-4" >';
					ResultsInfo +='<span id="editResultsOptions"></span>&nbsp;&nbsp;&nbsp;';
					//ResultsInfo +='<span  class="btn btn-default" id=""><i class="fa fa-print"></i>&nbsp;&nbsp;Print</span>&nbsp;&nbsp;&nbsp;';
					//ResultsInfo +='<span  class="btn btn-default" id=""><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Excel</span>';
				ResultsInfo +='</div>';
				//ResultsInfo +='<div class="col-sm-4" ><span id="editResultsOptions"></span>&nbsp;&nbsp;&nbsp;<span  class="btn btn-default" id=""><i class="fa fa-print"></i>&nbsp;&nbsp;Print</span>&nbsp;&nbsp;&nbsp;<span  class="btn btn-default" id=""><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Excel</span></div>';
				ResultsInfo +='<div class="col-sm-2 pull-right" ><b>Teacher:</b> '+teacherName+'</div>';
			ResultsInfo += '</div></div>';

			$("#dashGrSelectResultDescription").html(ResultsInfo);
			DashTeLoadResults( getResultIsTeachingSet, 1 , null , frm );

            dashGrSelectResultSLoading.hide();
         	dashGrSelectResultSResults.slideDown();

         	var getResultSubmit = $('#getResultSubmit');
				getResultSubmit.removeClass('disabled'); 
				getResultSubmit.removeAttr('disabled');
	        }).end();

	}

	function getResultStudyGroupSelected()
	{

		var getResultStudyGroupSelect 	= $("#getResultStudyGroupSelect");
		var selected              		= getResultStudyGroupSelect.select2('val');

		var assessments 				= ReGroupedStudyGroupAssessment[selected];
		var assessmentsSize 		    = assessments.length;

		var getResultAssessmentSelect = $("#getResultAssessmentSelect");

		getResultAssessmentSelect.empty(); 
		getResultAssessmentSelect.append('<option ></option>');

		//
		var AssessmentOptions 	 = '';

		var assessmentIdentifier = "";
		var isTheFirsRecord 	 = true;

		for ( var i = 0; i< assessmentsSize ; i++ ) {

			if ( assessments[i]['isExamType'] == assessmentIdentifier ){
			    AssessmentOptions += '<option value="'+assessments[i]['assessmentid']+'" data-assessmentname="'+assessments[i]['assessmentType']+'" data-assessmentmax="'+assessments[i]['maximumMarks']+'" data-assessmentdate="'+assessments[i]['assessmentDate']+'" >'+ assessments[i]['assessmentType'] +' '+assessments[i]['assessmentNumber'] + '</option>';
			
			}else{

			    assessmentIdentifier = assessments[i]['isExamType'];
			    	if ( isTheFirsRecord ){

				    	isTheFirsRecord = false; 

			    	}else{
				    	 AssessmentOptions += '</optgroup>';
				    }

				    var labelName = ( assessments[i]['isExamType'] == 1 ) ? "Exam" : "CAT";

			    	AssessmentOptions += '<optgroup label="'+labelName+'">';
			   		AssessmentOptions += '<option value="'+assessments[i]['assessmentid']+'" data-assessmentname="'+assessments[i]['assessmentType']+'" data-assessmentmax="'+assessments[i]['maximumMarks']+'" data-assessmentdate="'+assessments[i]['assessmentDate']+'" >'+ assessments[i]['assessmentType'] +' '+assessments[i]['assessmentNumber'] + '</option>';
			}
				    
		}

	    getResultAssessmentSelect.append(AssessmentOptions);
	    getResultAssessmentSelect.select2().trigger('change');

		// var getResultSubjectSelect  = $("#getResultSubjectSelect");
		// getResultSubjectSelect.empty(); 
		// getResultSubjectSelect.append('<option ></option>');
	 //    for (var i=0; i< subjectsSize ; i++) {
	 //      getResultSubjectSelect.append('<option value="' + subjects[i]['id'] + '" data-teacher="'+subjects[i]['teacherid'] +'" data-teacherphoto="'+subjects[i]['teacherphoto']+'" data-teachername="'+subjects[i]['teachername']+'" >' + subjects[i]['subjectname'] + '</option>');
	 //    }
	 //    getResultSubjectSelect.select2().trigger('change');


	}

	function getResultClassSelected()
	{

		var getResultClassSelect 	= $("#getResultClassSelect");
		var selected              	= getResultClassSelect.select2('val');

		var subjects 			  	= ReGroupedSubjects[selected];
		var subjectsSize 		  	= subjects.length;

		var getResultSubjectSelect  = $("#getResultSubjectSelect");

		getResultSubjectSelect.empty(); 
		getResultSubjectSelect.append('<option ></option>');

	    for (var i=0; i< subjectsSize ; i++) {
	      getResultSubjectSelect.append('<option value="' + subjects[i]['id'] + '" data-teacher="'+subjects[i]['teacherid'] +'" data-teacherphoto="'+subjects[i]['teacherphoto']+'" data-teachername="'+subjects[i]['teachername']+'" >' + subjects[i]['subjectname'] + '</option>');
	    }

	    getResultSubjectSelect.select2().trigger('change');

	}

	function getResultSubjectSelected()
	{

		var getResultSubjectSelect 	= $("#getResultSubjectSelect");
		var selected              	= getResultSubjectSelect.select2('val');

		if ( selected > 0) 
			{	

				$( "#getResultAssessmentContainer" ).slideDown();

				var assessments 			  = ReGroupedAssessment[selected];
				var assessmentsSize 		  = assessments.length;

				var getResultAssessmentSelect = $("#getResultAssessmentSelect");

				getResultAssessmentSelect.empty(); 
				getResultAssessmentSelect.append('<option ></option>');

				//
				var AssessmentOptions 	 = '';

				var assessmentIdentifier = "";
				var isTheFirsRecord 	 = true;

				for ( var i = 0; i< assessmentsSize ; i++ ) {

					if ( assessments[i]['isExamType'] == assessmentIdentifier ){
					    AssessmentOptions += '<option value="'+assessments[i]['assessmentid']+'" data-assessmentname="'+assessments[i]['assessmentType']+'" data-assessmentmax="'+assessments[i]['maximumMarks']+'" data-assessmentdate="'+assessments[i]['assessmentDate']+'" >'+ assessments[i]['assessmentType'] +' '+assessments[i]['assessmentNumber'] + '</option>';
					
					}else{

					    assessmentIdentifier = assessments[i]['isExamType'];
					    	if ( isTheFirsRecord ){

						    	isTheFirsRecord = false; 

					    	}else{
						    	 AssessmentOptions += '</optgroup>';
						    }

						    var labelName = ( assessments[i]['isExamType'] == 1 ) ? "Exam" : "CAT";

					    	AssessmentOptions += '<optgroup label="'+labelName+'">';
					   		AssessmentOptions += '<option value="'+assessments[i]['assessmentid']+'" data-assessmentname="'+assessments[i]['assessmentType']+'" data-assessmentmax="'+assessments[i]['maximumMarks']+'" data-assessmentdate="'+assessments[i]['assessmentDate']+'" >'+ assessments[i]['assessmentType'] +' '+assessments[i]['assessmentNumber'] + '</option>';
					}
						    
				}

			    getResultAssessmentSelect.append(AssessmentOptions);
			    getResultAssessmentSelect.select2().trigger('change');

			}
		
	}

	var GroupedStudentsClassArray ;

	function onDashLoadTeachersSubjects()
	{
		var dashGradebook_contentAnalytics 	= $("#dashGradebook_contentAnalytics");
		var dashGradebook_contentOther 		= $("#dashGradebook_contentOther");

		var content  = '';
			content 	+= '<div  class="col-sm-12">';
				content  	+= '<div class="row text-center"><h4>My Subjects</h4></div>';
			content 	+= '<div  class="row">';
				content 	+= '<div  class="col-sm-8 col-sm-offset-2">'; 
					content 	+= '<div id="dashGrTeachersSubjectSection"></div>';
				content 	+= '</div>';
			content 	+= '</div>';
		content 	+= '</div>';

		dashGradebook_contentOther.html(content);

		dashGradebook_contentAnalytics.hide();
		dashGradebook_contentOther.slideDown();

		var url = base_url+"api/Gr/TeachersComments"; 

		var SubjectCommentContent = '';
		//get 
		$.getJSON( url, function(data) {

			SubjectCommentContent +='<table class="table table-striped" id="grTeachersSubjectTable" ><thead><tr class="text-left"><th class="text-left">Class</th><th class="text-left">Subject</th><th class="text-center">Comments to add</th></tr></thead><tbody>';
				
			$.each( data, function(i, item) {

		          SubjectCommentContent +="<tr id='"+item.id+"' classname='"+item.name+"' subjectname='"+item.subjectName+"'' subjectmaximum='"+item.subjectMaximum+"'' >";
		          	SubjectCommentContent +="<td >"+item.name+" </td>";
		            SubjectCommentContent +="<td class='text-left'>"+item.subjectName+" </td>";

	            	var LeftNumberOfComment = parseFloat(item.numberOfStudents) - parseFloat(item.numberOfComment);
	            	if ( LeftNumberOfComment == 0 )
	            	 {
	            	 	SubjectCommentContent +="<td class='text-center text-green'><span><i class='fa fa-thumbs-up'></i> Completed</span></td>";

	            	 }else{
	            	 	SubjectCommentContent +="<td class='text-center'>"+LeftNumberOfComment+"</td>";
	            	 }

		          SubjectCommentContent +="</tr>";

		    });

			SubjectCommentContent +="</tbody></table>";
			$("#dashGrTeachersSubjectSection").html(SubjectCommentContent);

			abTablePagingTable("grTeachersSubjectTable");
			dashGrTeachersSubjectSelected();

	    })
	    .done(function() {
	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });

	}
	
	function onDashTeWorkTypeChange()
	{	
		var selected    	= $("#dashTeacherSelectWorkType").select2('val');
		var container     	= $( "#dashTeAssessmentPartition" );

		var type_work = selected.split('_');
		var type = type_work[0];
		var work = type_work[1];

		if ( type == 1 )
		 {
		 	$("#dashTeAssessmentPartition").show();
		 	$('#formDashNewGrAssessment').formValidation('addField','#partition');

		 	var partition   = $("#dashboardTecherselectSubject").select2().find(":selected").data("partition");

		 	container.empty();
		 	container.append('<label class="col-sm-3 control-label">Partition</label><div class="col-sm-7"><input name="partition" id="input_partition" type="text" class="form-control"></div>');
			
			$.mask.definitions['#']='[0-1]';
		 	$("#input_partition").mask("#99%");
				 	 
		 }else{

		 	container.hide();
		 	$('#formDashNewGrAssessment').formValidation('removeField','#partition');
		 	container.empty();

		 }

	}

	function onDashTeClassroomSubject()
	{

		$('#formDashTeNewAssessment').trigger("reset");

		var dashboardTecherselectSubject = $("#dashboardTecherselectSubject");

		var selectedSubject    = dashboardTecherselectSubject.select2('val');

	  	var subjectName        = dashboardTecherselectSubject.select2('data').text;
	  	var classroomName	   = dashboardTecherselectSubject.select2().find(":selected").data("classname");
	  	var partition 		   = dashboardTecherselectSubject.select2().find(":selected").data("partition");

	  	$( "#dashSubmenu" ).html('<ol class="breadcrumb"><li><a href="#" onclick="getDashboard()" >Home</a></li><li><a href="#" >'+classroomName+'</a></li><li><a href="#" >'+subjectName+'</a></li></ol>');
	  	
	  	$( "#DashTeNewAssessmentClassSubject" ).html('New Assessment of <strong>'+subjectName +'</strong> in '+classroomName);

	  	$("#max_partition").val(partition);
	  	$("#classsubject").val(selectedSubject);

		var container     			 = $( "#dashGradebook_content" );
		container.empty();

		var CATContainer  = '';
		var EXAMContainer = '';

		var url = base_url+"api/Ac/subjects/"+selectedSubject; 


		$.getJSON( url, function(data) {

			var AssessmentCAT  = data.cat;
			var AssessmentEXAM = data.exams;

			var AssessmentCATSize  = AssessmentCAT.length;
			var AssessmentEXAMSize = AssessmentEXAM.length;

				if ( AssessmentCATSize > 0)
				{

					CATContainer += '<div class="col-sm-3"><div class="row"><i class="fa fa-table"></i><span class="grid-title"> <strong>CAT</strong></span></div><div class="row"><table class="table" id="AssessmentCATTabble" ><thead><tr><th>Name</th><th>Number</th><th>Maximum</th><th>Partition</th></tr></thead><tbody>';

					$.each(AssessmentCAT, function(i, item) {

					  CATContainer +="<tr id='"+item.id+"' catname='"+item.name+" "+item.workNumber+"' assmax='"+item.maximumMarks+"'>";
				        CATContainer +="<td>"+item.name+"</td>";
						CATContainer +="<td>"+item.workNumber+"</td>";
						CATContainer +="<td>"+item.maximumMarks+"</td>";
						CATContainer +="<td>"+item.partition+"%</td>";
				      CATContainer +="</tr>";

					});

					CATContainer +='<tbody></table></div></div>';
				}

				if ( AssessmentEXAMSize > 0)
				{
					EXAMContainer +='<div class="col-sm-3">';
						EXAMContainer +='<div class="row">';
							EXAMContainer +='<i class="fa fa-table"></i><span class="grid-title"><strong> EXAM</strong></span>';
						EXAMContainer +='</div>';
						EXAMContainer +='<div class="row">';
							EXAMContainer +='<table class="table" id="AssessmentExamTabble" >';
								EXAMContainer +='<thead><tr><th>Name</th><th>Maximum</th></tr></thead>';
								EXAMContainer +='<tbody>';

									$.each(AssessmentEXAM, function(i, item) {

									  EXAMContainer +="<tr id='"+item.id+"' examname='"+item.name+"' assmax='"+item.maximumMarks+"' >";
								        EXAMContainer +="<td>"+item.name+"</td>";
								        EXAMContainer +="<td>"+item.maximumMarks+"</td>";
								      EXAMContainer +="</tr>";

									});

								EXAMContainer +='</tbody>';
							EXAMContainer +='</table>';
						EXAMContainer +='</div>';
					EXAMContainer +='</div>';

				}


		    })
		    .done(function() {

		    	container.append('<div class="row"><div class="col-sm-12" ><div class="col-sm-4" id="DashAssessmentType" ></div><div class="col-sm-8" ><a class="btn btn-primary" data-toggle="modal" data-target="#Model_NewAssessment"><i class="fa fa-plus"></i>&nbsp;&nbsp;NEW ASSESSMENT OF '+subjectName+'</a></div></div></div>');

		    	container.append('<br/><div class="row">');

		    	container.append('<div class="col-sm-3" ><div id="dashResultsContainer" ></div></div>');

		    	container.append('<div class="col-sm-1"></div>'+ CATContainer  +'<div class="col-sm-1"></div>'+ EXAMContainer +'</div>');

		    	container.append('</div>');

		    	container.show();

		    	DashTeLoadCATResults();
		    	DashTeLoadExamResults();

		    	$( "#dashContentainer_Gradebook" ).slideDown();

		    })
		    .fail(function() {
		    })
		    .always(function() {
		    });


	}

	var ResultsStudyGroup      	 	 = '';
	var ResultsSelectedClass      	 = '';
	var ResultsSelectedSubject       = '';
	var ResultsSelectedAssessment 	 = '';
	var ResultsSelectedAssessmentMax = '';

	var dashGrSelectResultSResults = '';
	var commandQueue 		 	   = [];
    var grid;
    var EditAccess = false;

    var numberOfStudents ;

	function queueAndExecuteCommand(item, column, editCommand) {
     	commandQueue.push(editCommand);
		editCommand.execute();
	}

	function ab_slickGridUndo() {
	    var command = commandQueue.pop();
	    if (command && Slick.GlobalEditorLock.cancelCurrentEdit()) {
	      command.undo();
	      grid.gotoCell(command.row, command.cell, true);
	   //    grid.setActiveCell();
		  // grid.focus();
	    }
	}

	function ab_stickGridSaveUnsavedMarks(AssessmentID )
	{	
		var slickGridConsole =  $("#slickGridConsole");

		var resultSaved = false ;
		$.ajax({
		    url: base_url+"api/Gr/Results",
		    dataType: 'json',
		    type: 'POST',
		    data: { 
		    		saveAll      : 1 ,
	        		AssessmentID : AssessmentID ,
	        		AllResults   : grid.getData()
	        	  } ,
		    success: function ( res ) {

		    	if (res.saved)
		    	 {
		    	 	resultSaved = true;
		    	 }

		    }

		  }).done(function() {

		  if ( resultSaved )
		   {
		   		slickGridConsole.hide();
		   		slickGridConsole.html('<span class="text-green">All Marks saved!</span>');
		   		slickGridConsole.slideDown();

		   }else{

		   		slickGridConsole.hide();
		   		slickGridConsole.html('<span class="text-red">Failed to save unsaved marks</span>');
		   		slickGridConsole.slideDown();

		   }

		}).fail(function() {

			slickGridConsole.hide();
	   		slickGridConsole.html('<span class="text-red">Failed to save unsaved marks, Check your internet and click on "<b>Save unsaved marks</b>"</span>');
	   		slickGridConsole.slideDown();

		}).always(function() {

			if (resultSaved) 
			{	
				$.gritter.add({
                  title: 'Success',
                  text: 'All results saved',
                  class_name: 'success gritter-center',
                  time: ''
                });

                //Remove red row 

                for (var i = 0; i < numberOfStudents ; i++) {
                	
                	var cellStyle = {};
					cellStyle[i] = {}; // <- your particular cell in a row with number rowNum

					cellStyle[i]["ID"] 		     = "slickGrid_highlight";
					cellStyle[i]["Marks"] 		 = "slickGrid_highlight";
					cellStyle[i]["RegNumber"]    = "slickGrid_highlight";
					cellStyle[i]["Studentname"]  = "slickGrid_highlight";
					grid.removeCellCssStyles("notsaved_row_"+i+"_3");
                }

			}else{

				$.gritter.add({
		            title: 'Result not saved',
		            text: 'Check your internet and try again',
		            class_name: 'danger gritter-center',
		            time: ''
		        });
			}
			

		});
	}

	var undoRow ;
	var undoCol ;

	function abslickGridUndoToRowCol( IndexRow, IndexCell )
	{	
		do
		{
			var command = commandQueue.pop();

			if(typeof command === 'undefined'){
			  
				break;

			}else{

				undoRow = command.row;
			    undoCol = command.cell;

		     	if (command && Slick.GlobalEditorLock.cancelCurrentEdit()) {
				    command.undo();
				  	grid.gotoCell(undoRow, undoCol, true);
				  	// grid.setActiveCell();
				  	// grid.focus();
			    }

			}


		} while( ! ( (undoRow == IndexRow) && (undoCol == IndexCell) ) );

	}

	function NumericRangeEditor(args) {

	    var $input;
        var defaultValue;
        var scope 	= this;
    	var condObj = null;

        this.init = function() {

	        $input = $("<INPUT type='text' class='editor-text' />")
	                .appendTo(args.container)
	                .bind("keydown.nav", function(e) {
	                        if (e.keyCode === $.ui.keyCode.LEFT || e.keyCode === $.ui.keyCode.RIGHT) {
	                                e.stopImmediatePropagation();
	                        }
	                })
	                .focus()
	                .select();
        };

        this.destroy = function() {
                $input.remove();
        };

        this.focus = function() {
                $input.focus();
        };

        this.getValue = function() {
                return $input.val();
        };

        this.setValue = function(val) {
                $input.val(val);
        };

        this.loadValue = function(item) {
                defaultValue = item[args.column.field] || "";
                $input.val(defaultValue);
                $input[0].defaultValue = defaultValue;
                $input.select();
        };

        this.serializeValue = function() {
                return $input.val();
        };

        this.applyValue = function(item,state) {
                item[args.column.field] = state;
        };

        this.isValueChanged = function() {
                return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
        };

        this.validate = function() {
	        
	        var condObj 	= args.column.editorOptions;
	        var returnMsg 	= null;
	        var returnValid = true;

	        if ( $input.val().trim().length > 0 ) 
	        {
	        	if ( ! (!isNaN(parseFloat($input.val() )) && isFinite($input.val())) )
		        {
		         	returnMsg = "Marks should be a number";
	                returnValid = false;
		        }
		        
		        if($input.val() < 0 ) {
	                returnMsg = "Marks should not be less than <b>0</b>";
	                returnValid = false;
	            }

	            if($input.val() > ResultsSelectedAssessmentMax ) {
	                returnMsg = "Marks should not be greater than <b>"+ResultsSelectedAssessmentMax+"</b>";
	                returnValid = false;
	            }
	        }

	        return {
	            valid: returnValid,
	            msg: returnMsg
	        }

        };

        this.init();

	}


	function DashTeLoadResults( getResultIsTeachingSet, hasForm , AssessmentID , frm )
	{	
		var getResultClassSelect 		= $("#getResultClassSelect");
		var getResultSubjectSelect 		= $("#getResultSubjectSelect");
		var getResultAssessmentSelect 	= $("#getResultAssessmentSelect");

		var slickGridConsole		    = $("#slickGridConsole");

		if ( getResultIsTeachingSet == 1 ) {

		}else{

		}

		dashGrSelectResultSResults 		= $("#dashGrSelectResultSResults");
		ResultsSelectedClass 			= getResultClassSelect.select2('val');
		ResultsSelectedSubject 			= getResultSubjectSelect.select2('val');
		ResultsSelectedAssessment 		= getResultAssessmentSelect.select2('val');
		
		getResultAssessmentMax 			= $("#getResultAssessmentMax");
		ResultsSelectedAssessmentMax    = parseInt(getResultAssessmentMax.text());

		if (hasForm == 1) 
		{
			AssessmentID = ResultsSelectedAssessment;
			var SendUrl  = base_url+"api/Gr/Results";
			var SendData = frm.serialize();

		}else{
			var SendUrl  = base_url+"api/Gr/Results?Assessment="+AssessmentID+"&getResultIsTeachingSet="+getResultIsTeachingSet;
			var SendData = null ;
		}

	  $(function () {

	  	var DataAssessmentData  = null ;

	  	$.ajax({
	        url: SendUrl ,
	        dataType: 'json',
	        type: 'GET',
	        data:  SendData ,
	        success: function ( data ) {

	         	DataAssessmentData = data;
	        }

	      }).done(function() {

	      	DashTeLoadResultsData(getResultIsTeachingSet, DataAssessmentData , hasForm , AssessmentID );

		});

	  });

	}

	function DashGrLoadResultSelected( IsTeachingSet, AssessmentID )
	{
		$.ajax({
	        url: base_url+"api/Gr/Results?Assessment="+AssessmentID+"&getResultIsTeachingSet="+IsTeachingSet ,
	        dataType: 'json',
	        type: 'GET',
	        success: function ( data ) {

	         	DataAssessmentData = data;
	        }

	      }).done(function() {

	      	DashTeLoadResultsData( DataAssessmentData, false , AssessmentID );

		});
	}

	//var notSavedArray = [];

	function DashTeLoadResultsData( IsTeachingSet, DataAssessmentData, hasForm , AssessmentID )
	{	
		  //notSavedArray = [];
		  var slickdata 	= [];
		  var commandQueue	= [];


		  var data      = DataAssessmentData;
		  var res  		= data.SelectResults;
	      EditAccess 	= data.HasAccess;

	      numberOfStudents =  res.length;

	      var slickid = 0 ;

	      $.each(res, function(i, item) {

	       var d = (slickdata[slickid] = {});

	       d["ID"] 			= slickid+1;
	       d["Studentname"] = item.studentNames;
	       d["RegNumber"] 	= item.RegNumber;
	       d["Marks"] 		= item.score;

	       slickid++;

	      });

	        var dashGrResultSection 		= $( "#dashGrResultSection" );
			var dashGrResultSelectedSection = $( "#dashGrResultSelectedSection" );
			
			dashGrResultSection.hide();
			dashGrResultSelectedSection.show();

	      	var options = {
			  	autoHeight: true,
			    editable: true,
			    enableAddRow: false,
			    enableCellNavigation: true,
			    asyncEditorLoading: false,
			    autoEdit: true,
			    editCommandHandler: queueAndExecuteCommand
			};

	      	 if ( EditAccess )
            {
            	var columns = [

				  	{id: "ID", name: "&nbsp;ID", field: "ID" , width: 50  },
				    {id: "Studentname", name: " Student name", field: "Studentname", width: 200  , defaultSortAsc:true },
				    {id: "RegNumber", name: " Regitration number", field: "RegNumber" , width: 200 , defaultSortAsc:true  },
				    {id: "Marks", name: " Marks", field: "Marks" , width: 100 , editor: Slick.Editors.Text , defaultSortAsc:true , cssClass: "text-right" , editor: NumericRangeEditor }
				  
				  ];
            }else{

           		var columns = [

				  	{id: "ID", name: "&nbsp;ID", field: "ID" , width: 50  },
				    {id: "Studentname", name: " Student name", field: "Studentname", width: 200  , defaultSortAsc:true },
				    {id: "RegNumber", name: " Regitration number", field: "RegNumber" , width: 200 , defaultSortAsc:true  },
				    {id: "Marks", name: " Marks", field: "Marks" , width: 100  , defaultSortAsc:true , cssClass: "text-right"  }
				  
				  ];
            }

	        grid = new Slick.Grid( dashGrSelectResultSResults , slickdata, columns, options);
		    grid.setSelectionModel(new Slick.CellSelectionModel());
			grid.setActiveCell( 0, 3);

			dashGrSelectResultSResults.on('blur', 'input.editor-text', function() {
			    Slick.GlobalEditorLock.commitCurrentEdit();
			});

			var editResultsOptions = $("#editResultsOptions");
			editResultsOptions.html('<span class="btn btn-default" onclick="ab_slickGridUndo()"><i class="fa fa-undo"></i>&nbsp;&nbsp;Undo</span>&nbsp;&nbsp;&nbsp;<span class="btn btn-primary" onclick="ab_stickGridSaveUnsavedMarks('+AssessmentID+')"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save unsaved marks</span>');

			grid.onValidationError.subscribe(function (e, args) { 
	     		slickGridConsole = $("#slickGridConsole");
	        	slickGridConsole.hide();
				slickGridConsole.html('<span class="text-red">'+args.validationResults.msg+'</span>');
				slickGridConsole.slideDown();
				
		    });
		    
			grid.onCellChange.subscribe(function (e,args) { 

				slickGridConsole    = $("#slickGridConsole");

				var currentCellCol 	= args['cell'];
				var currentCellRow 	= args['row'];

		        var newMarks  	  	= args.item['Marks'];
		        var newMarksFlaot 	= parseFloat(newMarks);  

		        var RegNumber 		= args.item['RegNumber'] ; 
		        var resultSaved 	= false ;
		        

		        if ( IsTeachingSet == 1 ) 
		        {

			        var AjaxData = { 
				    		saveAll      		:0 ,
				    		hasForm             :hasForm , 
				    		IsTeachingSet       :IsTeachingSet ,
				    		AssessmentID        :AssessmentID ,
			        		regNumber 			:RegNumber ,
			        		newMarks 			:newMarksFlaot 
			        	  } ;

		        }else{

		        	var AjaxData = { 
				    		saveAll      		:0 ,
				    		hasForm             :hasForm , 
				    		IsTeachingSet       :IsTeachingSet ,
				    		AssessmentID        :AssessmentID ,
			        		regNumber 			:RegNumber ,
			        		newMarks 			:newMarksFlaot ,
			        		getResultClass 		:ResultsSelectedClass ,
			        		getResultSubject	:ResultsSelectedSubject ,
			        		getResultAssessment :ResultsSelectedAssessment
			        	  } ;
			        	  
		        }

		        $.ajax({
				    url: base_url+"api/Gr/Results",
				    dataType: 'json',
				    type: 'POST',
				    data: AjaxData ,
				    success: function ( res ) {

				    	if (res.saved)
				    	 {
				    	 	resultSaved = true;
				    	 }

				    }

				  }).done(function() {

				  if ( resultSaved )
				   {
				   		slickGridConsole.hide();
				   		slickGridConsole.html('<span class="text-green">Marks saved!</span>');
				   		slickGridConsole.slideDown();

				   		var cellStyle = {};
						cellStyle[currentCellRow] = {}; // <- your particular cell in a row with number rowNum

						cellStyle[currentCellRow]["ID"] 		  = "slickGrid_highlight";
						cellStyle[currentCellRow]["Marks"] 		  = "slickGrid_highlight";
						cellStyle[currentCellRow]["RegNumber"]    = "slickGrid_highlight";
						cellStyle[currentCellRow]["Studentname"]  = "slickGrid_highlight";
						grid.removeCellCssStyles("notsaved_row_"+currentCellRow+"_"+currentCellCol);

				   }else{

				   		slickGridConsole.hide();
				   		slickGridConsole.html('<span class="text-red">Failed to save marks, Please click on "<b>Save unsaved marks</b>"</span>');
				   		slickGridConsole.slideDown();

				   		var cellStyle = {};
						cellStyle[currentCellRow] = {}; // <- your particular cell in a row with number rowNum

						cellStyle[currentCellRow]["ID"] 		  = "slickGrid_highlight";
						cellStyle[currentCellRow]["Marks"] 		  = "slickGrid_highlight";
						cellStyle[currentCellRow]["RegNumber"]    = "slickGrid_highlight";
						cellStyle[currentCellRow]["Studentname"]  = "slickGrid_highlight";
						grid.addCellCssStyles("notsaved_row_"+currentCellRow+"_"+currentCellCol, cellStyle);

				   }

				}).fail(function() {

					slickGridConsole.hide();
			   		slickGridConsole.html('<span class="text-red">Failed to save marks, Check your internet and click on "<b>Save unsaved marks</b>"</span>');
			   		slickGridConsole.slideDown();

					$.gritter.add({
			            title: 'Result not saved',
			            text: 'Check your internet and try again',
			            class_name: 'danger gritter-center',
			            time: ''
			        });

					var cellStyle = {};
					cellStyle[currentCellRow] = {}; // <- your particular cell in a row with number rowNum
					cellStyle[currentCellRow]["Marks"] 		  = "slickGrid_highlight";
					cellStyle[currentCellRow]["RegNumber"]    = "slickGrid_highlight";
					cellStyle[currentCellRow]["Studentname"]  = "slickGrid_highlight";
					grid.addCellCssStyles("notsaved_row_"+currentCellRow+"_"+currentCellCol, cellStyle);

				}).always(function() {

				});

		    });

	}

//payement 
		//var studentFeesLoaded = false; 
		function dashboardPayment()
		{

			$('#ac_Income_SchoolFees_Payments_Assigned').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Payments_Assigned();
		    });

			$('#ac_Income_SchoolFees_Payments_Unassigned').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Payments_Unassigned();
		    });

		    $('#ac_Income_SchoolFees_Fees_CurrentInvoices').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Fees_CurrentInvoices();
		    });

		    $('#ac_Income_SchoolFees_Fees_StudentsWithoutInvoice').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Fees_StudentsWithoutInvoice();
		    });

			$('#ac_Income_SchoolFees_Fees_FeeTypes').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Fees_FeeTypes();
		    });

		    $('#ac_Income_SchoolFees_Deposits_Cash').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Deposits_Cash();
		    });

			$('#ac_Income_SchoolFees_Deposits_Bank').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Deposits_Bank();
		    });

		    $('#ac_Income_SchoolFees_Deposits_MobiCash').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Deposits_MobiCash();
		    });

			$('#ac_Income_SchoolFees_Debts_UnPaid').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Debts_UnPaid();
		    });

		    $('#ac_Income_SchoolFees_Debts_Paid').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Debts_Paid();
		    });

			$('#ac_Income_SchoolFees_Organization').click(function(e) 
		    { 
		    	load_ac_Income_SchoolFees_Organization();
		    });

			$('#ac_Income_SoldAssets').click(function(e) 
		    { 
		    	load_ac_Income_SoldAssets();
		    });

		    $('#ac_Income_OtherSchoolIncome').click(function(e) 
		    { 
		    	load_ac_Income_OtherSchoolIncome();
		    });

			$('#acc_Expense_Recorded').click(function(e) 
		    { 
		    	load_acc_Expense_Recorded();
		    });

		    $('#acc_Expense_Types').click(function(e) 
		    { 
		    	load_acc_Expense_Types();
		    });

			$('#acc_Assets_Current_Cash').click(function(e) 
		    { 
		    	load_acc_Assets_Current_Cash();
		    });

		    $('#acc_Assets_Current_BankAccounts').click(function(e) 
		    { 
		    	load_acc_Assets_Current_BankAccounts();
		    });

			$('#acc_Assets_Current_AccountReceivable').click(function(e) 
		    { 
		    	load_acc_Assets_Current_AccountReceivable();
		    });

		    $('#acc_Assets_Fixed_All').click(function(e) 
		    { 
		    	load_acc_Assets_Fixed_All();
		    });

			$('#acc_Liabilities_Current').click(function(e) 
		    { 
		    	load_acc_Liabilities_Current();
		    });

		    $('#acc_Liabilities_Long_Term').click(function(e) 
		    { 
		    	load_acc_Liabilities_Long_Term();
		    });

		    $('#acc_Liabilities_Loans').click(function(e) 
		    { 
		    	load_acc_Liabilities_Loans();
		    });

			$('#acc_Capital').click(function(e) 
		    { 
		    	load_acc_Capital();
		    });

			$('#acc_Budget_Expense').click(function(e) 
		    { 
		    	load_acc_Budget_Expense();
		    });

			$('#acc_Reports').click(function(e) 
		    { 
		    	load_acc_Reports();
		    });

		}

//secretary
		function dashboardSecretary() {


		}

		function onDashSeClassroomChange()
		{
			  var selected            = $("#dashboardSeSelectClassroom").select2('val');

		 	  var class_name          = $("#dashboardSeSelectClassroom").select2('data').text;
		  	  var yearname            = $("#dashboardSeSelectClassroom").select2().find(":selected").data("yearname");

		  	  $("#dashboardSeSelectClassroom").select2('data', {id: selected, text: yearname+' '+class_name });
		  	
		  	  var container           = $( "#dashboardContent" );

			  container.hide();
			  container.empty();

			  $( "#donut-dashboardContainer" ).empty();

			  var TotalAmount       = 0;
			  var TotalAmountPaid   = 0;
			  var TotalAmountRemain = 0;
			  var AmountRemain      = 0;
			  var currency;

			  var classroomPaymentsContainer ='<div class="col-sm-6"><div class="row"><i class="fa fa-table"></i><span class="grid-title"> <strong> '+yearname+' '+class_name+'</strong></span><div class="pull-right"><button type="button" class="btn btn-success"><i class="fa fa-send"></i> Remind To All to Pay</button></div></div><div class="row"><table class="table"><thead><tr><th>Student</th><th>TOTAL</th><th>PAID</th><th>REMAIN</th></tr></thead><tbody>';

			  var url = base_url+"payments/classroomPayments/"+selected; 

			  $.getJSON( url, function(data) {

			    $.each(data, function(i, item) {

			      currency           = item.currency;
			      AmountRemain       = parseFloat(item.Total_amountToPay) - parseFloat(item.Total_paidAmount);

			      TotalAmountPaid    = parseFloat(TotalAmountPaid) + parseFloat(item.Total_paidAmount);
			      TotalAmountRemain  = parseFloat(TotalAmountRemain) + parseFloat(AmountRemain);

			      // container.append(item);
			      classroomPaymentsContainer +="<tr>";
			        classroomPaymentsContainer +="<td><img class='img-circle' data-toggle='tooltip' data-original-title="+item.studentNames+" style='max-width:30px; max-height:30px;' src='images/student/50/"+item.personID+"/"+item.photoID+"' /> "+item.studentNames+" </td>";
			        classroomPaymentsContainer +="<td>"+formatNumber(item.Total_amountToPay)+" "+item.currency+"</td>";
			        classroomPaymentsContainer +="<td><span class='text-green'>"+formatNumber(item.Total_paidAmount)+" "+item.currency+"</span></td>";
			        classroomPaymentsContainer +="<td><span class='text-red'>"+formatNumber(AmountRemain)+" "+item.currency+"</span></td>";
			      classroomPaymentsContainer +="</tr>";

			    });


			    classroomPaymentsContainer +="</tbody></table></div>";
			    TotalAmount = parseFloat(TotalAmountPaid.toFixed(0)) + parseFloat(TotalAmountRemain.toFixed(0));

			   
			    })
			    .done(function() {

			      if (TotalAmount > 0 )
			       {
			          classroomPaymentsContainer +='<div class="row"><div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title">TOTAL</span><span class="value">'+formatNumber(TotalAmount)+' '+ currency+'</span><span >&nbsp;</span></div></div></div><div class="col-sm-4"><div class="grid widget "><div class="grid-body"><span class="title">PAID</span><span class="value">'+formatNumber(TotalAmountPaid.toFixed(0))+' '+ currency+'</span><span >&nbsp;</span></div></div></div><div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title">REMAIN</span><span class="value">'+formatNumber(TotalAmountRemain.toFixed(0))+' '+ currency+'</span><span >&nbsp;</span></div></div></div></div></div>';
			          
			          container.append(classroomPaymentsContainer);

			          container.show();
			          $( "#dashboardContainer" ).slideDown( "slow" , function()
			          {
			            ab_load_studentPaFee( TotalAmount, TotalAmountPaid, TotalAmountRemain );

			          });

			       }else{

			             container.append("No student Added yet");
			             container.show();
			             $( "#dashboardContainer" ).slideDown( "slow" );
			        }

			    })
			    .fail(function() {
			    })
			    .always(function() {
			    });

		}

		

		function onSeViewStaffProfile()
		{

			var selected            	= $("#dashboardSeSelectStaff").select2('val');
			
		 	get_profile_staff(selected);
		 	

		}

		function dashPaNewPayment()
		{
			$('#formNewSchoolFee')
	        .formValidation({
	            framework: 'bootstrap',
	            icon: {
	                valid: 'glyphicon glyphicon-ok',
	                invalid: 'glyphicon glyphicon-remove',
	                validating: 'glyphicon glyphicon-refresh'
	            },
	            // This option will not ignore invisible fields which belong to inactive panels
	            exclude: ':disabled',
	            fields: {
	                description: {
	                    validators: {
	                        notEmpty: {
	                            message: 'description required'
	                        }
	                    }
	                }

	            }
	        });
		}

//attendance 
		
		function setAtDashSelected2()
		{

			 $("#dashboardAtSelectSubject").select2({ 
		            closeOnSelect: false,
		            formatResult: classroomSubjectformatResult,
		            formatSelection: classroomSubjectformatSelection
		    }).bind('change', onDashTeAtSubjectChange);

			$("#dashboardAtselectClassroom").select2({ 
		            closeOnSelect: false,
		            formatResult: classroomformatResult,
		            formatSelection: classroomformatSelection
		    }).bind('change', onDashTeAtClassroomChange);
		}

		function onDashTeAtSubjectChange()
		{

			var sel 			= $("#dashboardAtSelectSubject");
			var selected 		= sel.select2('val');
			var classname       = sel.select2().find(":selected").data("classname");
			var subjectname     = sel.select2('data').text;

			LoadAtSelectedStudentPeriod( selected , classname, subjectname );

		}

		function onDashTeAtClassroomChange()
		{
			var sel 		= $("#dashboardAtselectClassroom");
			var selected 	= sel.select2('val');
			var classname   = sel.select2().find(":selected").data("yearname");
			
			LoadAtSelectedStudentDay(selected, classname);

		}


//library 
	function dashboardLibrary()
	{

		$("#dashboardLiSelectLibraryItem").select2().bind('change', onDashLiLibraryItemChange);

		//New Item
			$("#LiNewLibraryItemLoading").hide(); 
			$("#dashLiNewItemBookSection").hide();
			$("#LiNewItemType").select2().bind('change', onLiNewItemTypeChange);

			$('#newItemIsbnType10').hide();
			$('#newItemIsbnType13').hide();

			$('input[name|="isbnType"]').change(function() {
				
				var selected = $(this).val();

				if ( selected == 10 )
				 {
				 	$("#newItemIsbnType10").show( "slow" );
				 	$("#newItemIsbnType13").hide( "fast" );

				 }else if( selected == 13 ){

				 	$("#newItemIsbnType10").hide( "fast" );
				 	$("#newItemIsbnType13").show( "slow" );

				 }

			});

			$('input[name|="borrowable"]').change(function() {
				
				var selected = $(this).val();

				if ( selected == 1 )
				 {
				 	$("#newItemBorrowerType").show( "slow" );

				 }else {

				 	$("#newItemBorrowerType").hide( "fast" );

				 }

			});


		//New Borrow 
			$("#LiNewBorrowLoading").hide();
			$("#borrowItemStaff").hide();
			$("#NewBorrowItemCopyContainer").hide();
			$("#NewBorrowItemNewIndexNumberContainer").hide();

			$("#selectNewMewBorrowStaff").select2();
			$("#selectnewBorrowStudent").select2();
			
			$("#selectNewBorrowItems").select2({
				minimumInputLength: 2
			}).bind('change', onDashNewBorrowItemsChange );

			$('input[name|="newBorrowType"]').change(function() {
				
				var selected = $(this).val();

				if ( selected == 0 )
				 {

				 	$("#borrowItemStaff").hide();
				 	$("#borrowItemStudent").show( "slow" );

				 }else if( selected == 1 )
				 {
				 	$("#borrowItemStudent").hide();
				 	$("#borrowItemStaff").show( "slow" );
				 }

			});

			$('#NewBorrowDueDate').daterangepicker({
		        singleDatePicker: true,
		        showDropdowns: true , 
		        autoUpdateInput : false ,
		        locale: {
		            format: 'DD/MM/YYYY'
		        }
		    }, function(chosen_date) {

			  
			  function setNewBorrowDueDate( chosen_date ){

			  		$('#NewBorrowDueDate').val(chosen_date.format('DD/MM/YYYY'));
			  }

			  $.when( setNewBorrowDueDate(chosen_date) ).done(function() {
					$('#formLiNewBorrow').formValidation('revalidateField', 'returnDueDate');
			  });

			});

		//New Return 
			$("#LiReturnLoading").hide();
			$("#ReturnItemStaff").hide();
			$("#ReturnItemBorrowedItemsContainer").hide();
			
			$("#selectNewReturnItemStudent").select2().bind('change', onDashNewReturnItemStudentChange );
			$("#selectNewReturnItemStaff").select2().bind('change', onDashNewReturnItemStaffChange );

			$('input[name|="ReturnItemType"]').change(function() {
				
				var selected = $(this).val();

				if ( selected == 0 )
				 {
				 	$("#ReturnItemStaff").hide();
				 	$("#ReturnItemStudent").show( "slow" );

				 }else if( selected == 1 )
				 {
				 	$("#ReturnItemStudent").hide();
				 	$("#ReturnItemStaff").show( "slow" );
				 }

			});

	}

	function onDashNewReturnItemStudentChange(){

		var sel 		= $("#selectNewReturnItemStudent");
		var selected 	= sel.select2('val');
		onDashLiLoadItemBorrowed( sel, selected, 0);
	}

	function onDashNewReturnItemStaffChange(){

		var sel 		= $("#selectNewReturnItemStaff");
		var selected 	= sel.select2('val');
		onDashLiLoadItemBorrowed( sel, selected, 1);
	}	

	function onDashLiLoadItemBorrowed( sel, id, BorrowerType){

		var formLiReturnItem = $('#formLiReturnItem');

	    var selectedItem = $('#selectReturnItemItems').val();
	    var url 	 	 = base_url+"api/Li/Borrow/"+id;

	    var ReturnItemBorrowedItemsContainer 		= $('#ReturnItemBorrowedItemsContainer');

	    var selectReturnItemBorrowedItemsContainer  = $('#selectReturnItemBorrowedItemsContainer');
	    var selectReturnItemBorrowedMsgContainer    = $('#selectReturnItemBorrowedMsgContainer');

	    ReturnItemBorrowedItemsContainer.slideDown();

	    selectReturnItemBorrowedItemsContainer.hide();
	    selectReturnItemBorrowedMsgContainer.html('<center><img src="../packages/assets/plugins/img/loading.gif" alt="" > Looking borrowed items...</center>');

		$.getJSON( url, function(data) {
		 	
		 	if ( data.length > 0 ) {

		 		var ItemCopyOptions = '';

			   	  $.each( data, function(i, item) {

			   	  	ItemCopyOptions +='<option value="' + item.id + '"  >' + item.title + ' ( '+item.indexNumber+' )</option>';
			      
			      });

			   	var selectReturnItemBorrowedItems = $('#selectReturnItemBorrowedItems');
				selectReturnItemBorrowedItems.html( ItemCopyOptions );
				selectReturnItemBorrowedItems.select2();

				selectReturnItemBorrowedMsgContainer.hide();
				selectReturnItemBorrowedItemsContainer.slideDown();

		 	}else{

		 		selectReturnItemBorrowedMsgContainer.html('<center><span class="text-orange" > No item to return found for the selected person!</span></center>');
	    		sel.select2('data', null);

		 		selectReturnItemBorrowedItemsContainer.hide();
				selectReturnItemBorrowedMsgContainer.slideDown();
		 	}


		})
		.done(function() {

			

		 })
		.fail(function() {

		    selectReturnItemBorrowedMsgContainer.html('<center><span class="text-red" > Failed to load the available copies, check your internet and try again</span></center>');
	    	sel.select2('data', null);

			selectReturnItemBorrowedItemsContainer.hide();
			selectReturnItemBorrowedMsgContainer.slideDown();

		})
		.always(function() {

		});

	}

	function onDashNewBorrowItemsChange(){

		var sel 		= $("#selectNewBorrowItems");
		var selected 	= sel.select2('val');

		if ( selected > 0 ) {

			var NewBorrowItemCopyContainer 		 		= $('#NewBorrowItemCopyContainer');
			var NewBorrowItemNewIndexNumberContainer 	= $('#NewBorrowItemNewIndexNumberContainer');
			
			NewBorrowItemCopyContainer.slideDown();
			NewBorrowItemNewIndexNumberContainer.slideDown();

			var NewBorrowItemCopySelectContainer = $('#NewBorrowItemCopySelectContainer');
			var NewBorrowItemCopyMsgContainer    = $('#NewBorrowItemCopyMsgContainer');

			NewBorrowItemCopySelectContainer.hide();

			NewBorrowItemCopyMsgContainer.html('<center><img src="../packages/assets/plugins/img/loading.gif" alt="" > Looking available copies...</center>');

			var url = base_url+"api/Li/LibraryItems/"+selected;

		   	ItemsCopiesContainer = '';
		   
		   	$.getJSON( url, function(data) {

		   		var data_length = data.length;
		   		if ( data_length > 0 ) {

		   			var ItemCopyOptions = '';

				   	  $.each( data, function(i, item) {

				   	  	ItemCopyOptions +='<option value="' + item.id + '"  data-isprinted="'+ item.isPrinted +'" >' + item.indexNumber + '</option>';
				      
				      });

				   	var selectNewBorrowItemCopy = $('#selectNewBorrowItemCopy');
					selectNewBorrowItemCopy.html( ItemCopyOptions );
					selectNewBorrowItemCopy.select2().bind('change', onDashNewBorrowItemCopyChange );

					NewBorrowItemCopyMsgContainer.hide();
			    	NewBorrowItemCopySelectContainer.slideDown();

			    	$('#formLiNewBorrow').formValidation('revalidateField', 'itemCopy');

		   		}else{

		   			NewBorrowItemCopyMsgContainer.html('<center><span class="text-orange" > No copy available to borrow! Select other Library item above</span></center>');
		   
			    	NewBorrowItemCopySelectContainer.hide();
			    	NewBorrowItemCopyMsgContainer.slideDown();

		    		sel.select2('data', null);

		   		}
			   	

		    }).done(function() {
		     })
		    .fail(function() {

		    	NewBorrowItemCopyMsgContainer.html('<center><span class="text-red" > Failed to load the available copies, check your internet and try again</span></center>');
		    	
		    	NewBorrowItemCopySelectContainer.hide();
			    NewBorrowItemCopyMsgContainer.slideDown();

		    	sel.select2('data', null);

		     })
		    .always(function() { });

		}

	}

	function onDashNewBorrowItemCopyChange()
	{

		var sel 			= $("#selectNewBorrowItemCopy");

		var selected 		= sel.select2('val');

		if ( selected > 0 ) {

			var copyIsPrinted   = sel.find(":selected").data("isprinted");

			var NewBorrowItemNewIndexNumberContainer = $("#NewBorrowItemNewIndexNumberContainer");

			if ( copyIsPrinted == 1 ) {
				NewBorrowItemNewIndexNumberContainer.hide();

			}else{
				NewBorrowItemNewIndexNumberContainer.slideDown();
			}

		}
		
	}	

	function onLiNewItemTypeChange()
	{
			var sel 		= $("#LiNewItemType");
			var selected 	= sel.select2('val');

			if ( selected == 1 )
			 {
			 	$("#dashLiNewItemBookSection").slideDown( "slow" );

			 }else{

			 	$("#dashLiNewItemBookSection").slideUp();

			 }

			
	}

	function onDashLiLibraryItemChange()
	{
		var sel 			= $("#dashboardLiSelectLibraryItem");
		var selected 		= sel.select2('val');
		var selectedItem 	= sel.select2('data').text;

		DashLiLoadCopiesOfSelected(selected , selectedItem);
	}

	
	function loadLibraryHome()
	{

	  var li_anlytBookType 		 = $( "#li_anlytBookType" );
	  var li_anlytBookCopy 		 = $( "#li_anlytBookCopy" );

	  var li_anlytOtherItemsType = $( "#li_anlytOtherItemsType" );
	  var li_anlytOtherItemsCopy = $( "#li_anlytOtherItemsCopy" );

	  var li_anlyAvailable 		 = $( "#li_anlyAvailable" );
	  var li_anlyBorrowed 	     = $( "#li_anlyBorrowed" );
	  var li_anlyLost 		     = $( "#li_anlyLost" );
	  var li_anlyRemoved 	     = $( "#li_anlyRemoved" );
	  var li_anlyDamaged 	     = $( "#li_anlyDamaged" );

	  var li_anlytOverduePayment = $( "#li_anlytOverduePayment" ); 

	  var url = base_url+"dash/library";

	  $.getJSON( url, function(data) {
	      	
	      	var TotalBook 			= data.TotalBook ;
	      	var TotalBookCopies 	= data.TotalBookCopies ;
	      	var TotalOther 			= data.TotalOther ;
	      	var TotalOtherCopies 	= data.TotalOtherCopies ;
	      	var LibraryItemStatus   = data.LibraryItemStatus ;
	      	var borrowedItems 		= data.borrowedItems ;

	      	li_anlytBookType.text(formatNumber(TotalBook.numberOfBook));
	      	li_anlytBookCopy.text(formatNumber(TotalBookCopies.numberOfBook));

	      	li_anlytOtherItemsType.text(formatNumber(TotalOther.numberOfBook));
	      	li_anlytOtherItemsCopy.text(formatNumber(TotalOtherCopies.numberOfBook));

	      	li_anlyAvailable.text(formatNumber(LibraryItemStatus.TotalAvailable));
	      	li_anlyBorrowed.text(formatNumber(LibraryItemStatus.TotalBorrowed));
	      	li_anlyLost.text(formatNumber(LibraryItemStatus.TotalLost));
	      	li_anlyRemoved.text(formatNumber(LibraryItemStatus.TotalRemoved));
	      	li_anlyDamaged.text(formatNumber(LibraryItemStatus.TotalDamaged));

	      	li_anlytOverduePayment.text(formatNumber(borrowedItems.numberOfOverDue));

	  }).done(function() {

	  }).fail(function() {
	  }).always(function() {
	  });

	}
	
	function dashLiBorrowedTableSelected()
	{	

		$( ".dashboardLiItemReturn" ).delegate("button", "click", function() {

		  	id 					 = $(this).closest('tr').attr('id');

		  	LiReturnLibraryItem(id);

		});
		
	}

	function DashLiLoadCopiesOfSelected(id , selectedItem)
	{
		  var container               		= $( "#dashLibrary_contentOthers" );
		  var dashLibrary_contentAnalytics  = $( "#dashLibrary_contentAnalytics" );

		  dashLibrary_contentAnalytics.hide();
		  container.show();

		  $( "#NewCopiesItemID" ).val(id);

		  var url = base_url+"api/Li/ItemsCopies/"+id;

		  ItemsCopiesContainer = '';
		  $.getJSON( url, function(data) {

		  		var LibraryItemType = data.Type;
		  		var ItemInfo		= data.ItemInfo;
		  		var ItemTags 		= data.ItemTags;
		  		var ItemCopies  	= data.ItemCopies;
		  		var Book 			= data.Book;
		  		var Authors 		= data.Authors;

		  		ItemsCopiesContainer += '<div class="row text-center"><h4>Title<Strong>: <strong>'+selectedItem+'</strong></h4></div>';

		  		ItemsCopiesContainer += '<div class="row top-buffer"><div class="col-sm-12">';

					ItemsCopiesContainer +='<div class="col-sm-1"></div>';
					ItemsCopiesContainer +='<div class="col-sm-5">';

						ItemsCopiesContainer +='<div class="row text-center lead"><strong>Item Info</strong></div>';
						ItemsCopiesContainer +='<div class="row top-buffer"><div class="col-sm-5 text-right"><em>Language: </em></div><div class="col-sm-7">'+ItemInfo.language+'</div></div>';
						ItemsCopiesContainer +='<div class="row"><div class="col-sm-5 text-right"><em>Price: </em></div><div class="col-sm-7">'+ItemInfo.price+' '+ItemInfo.currency+'</div></div>';
						
						ItemsCopiesContainer +='<div class="row"><div class="col-sm-5 text-right"><em>Borrowable: </em></div><div class="col-sm-7">';
							if (ItemInfo.isBorrowable == 1 ) {
								if (ItemInfo.isBorrowableByTeacher == 1 ){
									ItemsCopiesContainer +='<span class="label label-success">Staff</span>&nbsp;';
								}
								if (ItemInfo.isBorrowableByStudent == 1 ){
									ItemsCopiesContainer +='<span class="label label-success">Students</span>&nbsp;';
								}
							
							}else{
								ItemsCopiesContainer +='<span class="label label-warning">No</span>&nbsp;';
							}
						ItemsCopiesContainer +='</div></div>';

						ItemsCopiesContainer +='<div class="row"><div class="col-sm-5 text-right"><em>Tags: </em></div><div class="col-sm-7">';

							 $.each(ItemTags, function(i, tag) {

							 	ItemsCopiesContainer +='<span class="label label-default">'+tag.tag+'</span>&nbsp;';

							 });

						ItemsCopiesContainer +='</div></div>';

						if ( LibraryItemType == 1 )
						 {

							ItemsCopiesContainer +='<div class="row top-buffer"><div class="col-sm-5 text-right"><em>Edition: </em></div><div class="col-sm-7 ">'+Book.edition+'</div></div>';
							ItemsCopiesContainer +='<div class="row"><div class="col-sm-5 text-right"><em>Isbn13: </em></div><div class="col-sm-6 ">'+Book.isbn13+'</div></div>';
							ItemsCopiesContainer +='<div class="row"><div class="col-sm-5 text-right"><em>Publisher: </em></div><div class="col-sm-6 ">'+Book.publisher+'</div></div>';
							ItemsCopiesContainer +='<div class="row"><div class="col-sm-5 text-right"><em>Published on: </em></div><div class="col-sm-7 ">'+Book.publisherDate+'</div></div>';

							ItemsCopiesContainer +='<div class="row"><div class="col-sm-5 text-right"><em>Author: </em></div><div class="col-sm-7">';

								 $.each(Authors, function(i, Author) {

								 	ItemsCopiesContainer +='<span class="label label-default">'+Author.names+'</span>&nbsp;';

								 });

							ItemsCopiesContainer +='</div></div>';

						 }

					ItemsCopiesContainer +='</div>';

					ItemsCopiesContainer += '<div class="col-sm-5">';
					ItemsCopiesContainer += '<div class="row"><div class="col-sm-6 text-center lead"><strong>Copies</strong></div><div class="col-sm-6 text-right"><a class="btn btn-primary" data-toggle="modal" data-target="#Model_LiNewLibraryItemCopy"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Copies</a></div></div>';
						ItemsCopiesContainer += '<div class="row top-buffer"><table class=" table abDataTable" id="dashLibraryItemCopies" ><thead><tr><th><center>Added on</center></th><th><center>Item Index number</center></th><th><center>Status</center></th><th><center>Printed</center></th></tr></thead><tbody>';

					    $.each(ItemCopies, function(i, item) {

					        ItemsCopiesContainer +="<tr id='"+item.id+"' itemindexnumber='"+item.indexNumber+"' >";
					          ItemsCopiesContainer +="<td><center>"+date_moment(item.added_on)+"</center></td>";
					          ItemsCopiesContainer +="<td><center>"+item.indexNumber+"</center></td>";
					          ItemsCopiesContainer +="<td><center>"+LibraryGetStatus(item.statusID)+"</center></td>";
					          
					          if ( item.IsPrinted == 1 ) {
					          	ItemsCopiesContainer +="<td><center><span class='xeditableCopyItemPrinted' data-type='select2' data-pk='1' data-title='Select Printed Status' data-url='api/Li/ItemsCopies/?id="+item.id+"&printed=1'>Yes</span></center></td>";

					          }else{
					          	ItemsCopiesContainer +="<td><center><span class='xeditableCopyItemPrinted' data-type='select2' data-pk='1' data-title='Select Printed Status' data-url='api/Li/ItemsCopies/?id="+item.id+"&printed=0'>No</span></center></td>";

					          }

					        ItemsCopiesContainer +="</tr>";

					    });

					    ItemsCopiesContainer +='</tbody></table></div>';
				    ItemsCopiesContainer +='</div>';

		    	ItemsCopiesContainer +='</div></div>';

		  }).done(function() {


		        container.html(ItemsCopiesContainer);
		        
		        abDataTable();
		        
		        $.fn.editable.defaults.mode = 'inline';
		        $('.xeditableCopyItemPrinted').editable({
			        source: [
			              {id: '1', text: 'Yes'},
			              {id: '0', text: 'No'}
			           ],
				        select2: {
				           width: 70 ,
				           multiple: false
				        },
				        success: function(response, newValue) {
					        if(response.status == 'error') return response.msg;
					    }
				    });

		    })
		    .fail(function() { })
		    .always(function() { });
	}

	function LibraryGetStatus(id)
	{
		var content ='';

		switch(id) {
		    case "1":
		    	content = '<span class="badge bg-green"> Available</span>';
		        break;

		    case "2":
		       content = '<span class="badge bg-yellow"> Borrowed</span>';
		       break;

	        case "3":
	        	content = '<span class="badge bg-red"> Lost</span>';
		        break;

		    case "4":
	        	content = '<span class="badge bg-aqua"> Removed</span>';
		        break;
		}

		return content;

	}

//School Asset
function dashboardSchoolAssets()
{
	 $("#selectSANewDeskType").select2();
	 $("#selectSANewDeskRoomStatus").select2().val('1').trigger('change');
	 $("#selectSANewDeskDeskStatus").select2().val('1').trigger('change');;

}	

function loadDashSchoolAsset()
{

	  var container               	= $( "#dashSchoolAssets_content" );
	  container.empty();

	  var RoomContainer = '';
	  var DeskContainer = '';
	  

	  var url = base_url+"dash/schoolAsset";

	  $.getJSON( url, function(data) {
	      
	    SchoolRoom   = data.SchoolRoom;
	    SchoolDesk   = data.SchoolDesk;

	    RoomContainer = '<div class="col-sm-5">';
		    RoomContainer += '<div class="row"><div class="col-sm-12 text-center"><h4><strong>Rooms</strong></h4></div></div>';
			    RoomContainer += '<div class="row"><table class=" table abDataTable" ><thead><tr><th>Number</th><th>Type</th><th>Occupied</th><th>Status</th><th>Action</th></tr></thead><tbody>';


			    $.each(SchoolRoom, function(i, item) {

			        RoomContainer +="<tr id='"+item.id+"' >";
			          RoomContainer +='<td >'+item.number+'</td>';
			          RoomContainer +='<td >'+item.type+'</td>';
			          RoomContainer +='<td >';
			          if( item.classroom != 0 ){
						  RoomContainer += item.classroomName;
					  }
			          RoomContainer +='</td>';
			          RoomContainer +='<td >'+(item.statusID == 1 ? '<span class="text-green">'+item.status+'</span>' : '<span class="text-red">'+item.status+'</span>')+'</td>';
					  RoomContainer +="<td class='dashSaRoomAlocate' ><span class='badge bg-blue'><i class='fa fa-exchange'></i></span></td>";
					RoomContainer +="</tr>";

			    });

			RoomContainer +='</tbody></table></div>';
		RoomContainer +='</div>';

	    DeskContainer = '<div class="col-sm-4">';
		    DeskContainer += '<div class="row"><div class="col-sm-12 text-center"><h4><strong>Desk</strong></h4></div></div>';
			DeskContainer += '<div class="row"><table class=" table abDataTable"><thead><tr><th>Index Number</th><th>Number Of Students</th><th>Location</th><th>Status</th><th>Action</th></tr></thead><tbody>';


			    $.each(SchoolDesk, function(i, item) {

			        DeskContainer +="<tr id='"+item.id+"' >";
			          DeskContainer +="<td class='text-center'>"+item.number+"</td>";
			          DeskContainer +="<td class='text-center'>"+item.capacity+"</td>";
			          DeskContainer +='<td class="text-center">';
			          if( item.classroom != 0 ){
						  DeskContainer += item.classroomName;
					  }
			          DeskContainer +='</td>';
			          DeskContainer +='<td class="text-center">'+(item.statusID == 1 ? '<span class="text-green">'+item.status+'</span>' : '<span class="text-red">'+item.status+'</span>')+'</td>';
			          DeskContainer +="<td class='dashSaDeskAlocate' ><span class='badge bg-blue'><i class='fa fa-exchange'></i></span></td>";
					DeskContainer +="</tr>";

					

			    });

			DeskContainer +='</tbody></table></div>';
		DeskContainer +='</div>';

	  }).done(function() {

	  	container.append('<div class="row"><div class="col-sm-12"><div class="col-sm-1"></div>'+RoomContainer+'<div class="col-sm-1"></div>'+DeskContainer+'</div></div>');

	    $( "#dashContentainer_SchoolAssets" ).slideDown();

	    DashSaAlocateClickedRoom();
	    DashSaAlocateClickedDesk();
    })
    .fail(function() {
    })
    .always(function() {
    });

}

//admission 
function dashboardAdmission()
{
	$('ul.admission-menu li a').click(function(e) 
    { 
     	$(".admission-menu .active").removeClass('active');
	    $(this).parent().addClass('active'); 
	    e.preventDefault();

	    var id = $(this).parent().attr('id');

	    switch(id) {
		    case "1":
		    	dashboardAdNewAdmission();
		        break;

		    case "2":
		    	dashboardAdAdmitted();
		       break;

	        case "3":
	        	dashboardAdMayBe();
		        break;

		    case "4":
	        	dashboardAdRejected();
		        break;
		}

    });
}

function dashboardAdNewAdmission()
{
	var moduleAdMain 	= $("#moduleAdMain");

	var content = '';
		content+='<div class="row">';
			content+= '<div class="col-sm-12">';
				content+='<div class="row text-center">';
					content+= '<h4>New Applications</h4>';
				content+= '</div>';
				content+='<div class="row top-buffer">';
					content+= '<span ><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#Model_DashAdNewAdmission" ><strong><i class="fa fa-plus"></i> New Application</strong></button></span>';  
				content+= '</div>';
				content+= '<div class="row top-buffer">';
					content +='<table class="table abDataTable" id="adApplication" ><thead><tr><th>Names</th><th>Apply for</th><th>School</th><th>Country</th><th>Phone</th><th>Boarding</th><th>Action</th></tr></thead><tbody>';
						content +="<tr id='1'>";
						    content +="<td> Manzi Paul</td>";
						    content +="<td> S1</td>";
						    content +="<td> IFAK</td>";
						    content +="<td> Rwanda</td>";
						    content +="<td> 078-898-8989</td>";
						    content +="<td> <span class='label label-primary'>Boarding</span></td>";
						    content +="<td> <i class='fa fa-thumbs-up text-green'></i> &nbsp;&nbsp;&nbsp;<i class='fa fa-thumbs-down text-red'></i></td>";
					  	content +="</tr>";
					content +="</tbody></table>";
				content+= '</div>';
			content+= '</div>';
		content+= '</div>';

	moduleAdMain.html(content);

	//abTablePagingTable("paPaymentsRecords");

}

function dashboardAdAdmitted()
{
	var moduleAdMain 	= $("#moduleAdMain");

	var content = '';
		content+='<div class="row">';
			content+= '<div class="col-sm-12">';
				content+='<div class="row text-center">';
					content+= '<h4>Admitted</h4>';
				content+= '</div>';
				content+= '<div class="row top-buffer">';
					content +='<table class="table abDataTable" id="adApplication" ><thead><tr><th>Names</th><th>Apply for</th><th>School</th><th>Country</th><th>Phone</th><th>Boarding</th><th>Status</th></tr></thead><tbody>';
						content +="<tr id='1'>";
						    content +="<td> Manzi Paul</td>";
						    content +="<td> S1</td>";
						    content +="<td> IFAK</td>";
						    content +="<td> Rwanda</td>";
						    content +="<td> 078-898-8989</td>";
						    content +="<td> <span class='label label-primary'>Boarding</span></td>";
						    content +="<td> <i class='fa fa-thumbs-up text-green'></i> <span class='text-green'> Approved</span></td>";
					  	content +="</tr>";
					content +="</tbody></table>";
				content+= '</div>';
			content+= '</div>';
		content+= '</div>';

	moduleAdMain.html(content);

}

function dashboardAdMayBe(){

	var moduleAdMain 	= $("#moduleAdMain");

	var content = '';
		content+='<div class="row">';
			content+= '<div class="col-sm-12">';
				content+='<div class="row text-center">';
					content+= '<h4>May be</h4>';
				content+= '</div>';
				content+= '<div class="row top-buffer">';
					content +='<table class="table abDataTable" id="adApplication" ><thead><tr><th>Names</th><th>Apply for</th><th>School</th><th>Country</th><th>Phone</th><th>Boarding</th><th>Status</th></tr></thead><tbody>';
						content +="<tr id='1'>";
						    content +="<td> Manzi Paul</td>";
						    content +="<td> S1</td>";
						    content +="<td> IFAK</td>";
						    content +="<td> Rwanda</td>";
						    content +="<td> 078-898-8989</td>";
						    content +="<td> <span class='label label-primary'>Boarding</span></td>";
						    content +="<td> <i class='fa fa-question text-blue'></i> <span class='text-blue'> May be</span></td>";
					  	content +="</tr>";
					content +="</tbody></table>";
				content+= '</div>';
			content+= '</div>';
		content+= '</div>';

	moduleAdMain.html(content);

}


function dashboardAdRejected()
{

	var moduleAdMain 	= $("#moduleAdMain");

	var content = '';
		content+='<div class="row">';
			content+= '<div class="col-sm-12">';
				content+='<div class="row text-center">';
					content+= '<h4>Rejected</h4>';
				content+= '</div>';
				content+= '<div class="row top-buffer">';
					content +='<table class="table abDataTable" id="adApplication" ><thead><tr><th>Names</th><th>Apply for</th><th>School</th><th>Country</th><th>Phone</th><th>Boarding</th><th>Status</th></tr></thead><tbody>';
						content +="<tr id='1'>";
						    content +="<td> Manzi Paul</td>";
						    content +="<td> S1</td>";
						    content +="<td> IFAK</td>";
						    content +="<td> Rwanda</td>";
						    content +="<td> 078-898-8989</td>";
						    content +="<td> <span class='label label-primary'>Boarding</span></td>";
						    content +="<td> <i class='fa fa-thumbs-down text-red'></i> <span class='text-red'> Rejected</span></td>";
					  	content +="</tr>";
					content +="</tbody></table>";
				content+= '</div>';
			content+= '</div>';
		content+= '</div>';

	moduleAdMain.html(content);

}


function newGeneralformatSelection(state)
{

    if (state.id > 0)
    {
        var originalOption = $(state.element);
        return originalOption.data('optgroup_name') + " " + state.text;
    }
    else{

        return state.text;
    }

}

function newGeneralformatResult(state)
{
    return state.text;
}


function get_ap_NewApplication(){
	
	$("#AdmissionModuleMain").load("admission/newApplication").fadeIn( 1000 );

}

function get_ap_Admited(){
	
	$("#AdmissionModuleMain").load("admission/admitted").fadeIn( 1000 );

}

function get_ap_Rejected(){
	
	$("#AdmissionModuleMain").load("admission/rejected").fadeIn( 1000 );

}

function get_ap_Later(){
	
	$("#AdmissionModuleMain").load("admission/later").fadeIn( 1000 );
}

function studentApplicationSelected()
{
	$( "table#admissionNewApplicationTable" ).delegate( "td", "click", function() {

	  	id 					 = $(this).closest('tr').attr('id');
	  	studentname 		 = $(this).closest('tr').attr('studentname');

	  	loadStudentApplication( id , 1 , studentname);

	});
}

function studentAdmitedSelected()
{
	$( "table#admissionAdmitedTable" ).delegate( "td", "click", function() {

	  	id 					 = $(this).closest('tr').attr('id');
	  	studentname 		 = $(this).closest('tr').attr('studentname');

	  	loadStudentApplication( id , 2 , studentname);

	});
}

function studentRejectedSelected()
{
	$( "table#admissionRejectedTable" ).delegate( "td", "click", function() {

	  	id 					 = $(this).closest('tr').attr('id');
	  	studentname 		 = $(this).closest('tr').attr('studentname');

	  	loadStudentApplication( id , 3 , studentname);

	});
}

function studentLaterSelected()
{
	$( "table#admissionLaterTable" ).delegate( "td", "click", function() {

	  	id 					 = $(this).closest('tr').attr('id');
	  	studentname 		 = $(this).closest('tr').attr('studentname');

	  	loadStudentApplication( id , 4 , studentname);

	});
}

function loadStudentApplication( id , type, studentname )
{

	console.log("Called");

	var application_Student_Content   = $( "#application_Student_Content" );
	var application_StudentName 	  = $( "#application_StudentName" );
	var application_StudentAction 	  = $( "#application_StudentAction");

	application_Student_Content.empty();
	application_StudentAction.empty();

  	application_StudentName.html("<h4>"+studentname+"</h4>");

    var url = base_url+"api/Adm/StudentApplication/"+id; 


   if(! ( typeof url === 'undefined' ) ){
	   
	   	$.getJSON( url, function(data) {

	   		var studentApplicationData 	 = data;
	   		var messagesReportContainer  = '<div class="col-sm-12 ">';

	   		switch( type )
	   		{
	   			case 1:
	   				messagesReportContainer 	+= '<div class="col-sm-4 "><button type="button" class="btn btn-primary" onclick="set_ad_Accepted('+id+', 1)" ><strong><i class="fa fa-thumbs-up"></i> Accept</strong></button></div>';
	   				messagesReportContainer 	+= '<div class="col-sm-4 "><button type="button" class="btn btn-danger" onclick="set_ad_Reject('+id+',1)" ><strong><i class="fa fa-thumbs-down"></i> Reject</strong></button></div>';
	   				messagesReportContainer 	+= '<div class="col-sm-4 "><button type="button" class="btn btn-warning" onclick="set_ad_Later('+id+',1)" ><strong><i class="fa fa-question"></i> Process Later </strong></button></div>';

	   				break;

	   			case 2: 
	   				messagesReportContainer 	+= '<div class="col-sm-4 "><button type="button" class="btn btn-danger" onclick="set_ad_Reject('+id+',2)" ><strong><i class="fa fa-thumbs-down"></i> Reject</strong></button></div>';
	   				messagesReportContainer 	+= '<div class="col-sm-4 "><button type="button" class="btn btn-warning" onclick="set_ad_Later('+id+',2)" ><strong><i class="fa fa-question"></i> Process Later </strong></button></div>';

	   				break;

	   			case 3: 
	   				messagesReportContainer 	+= '<div class="col-sm-4 "><button type="button" class="btn btn-primary" onclick="set_ad_Accepted('+id+',3)" ><strong><i class="fa fa-thumbs-up"></i> Accept</strong></button></div>';
	   				messagesReportContainer 	+= '<div class="col-sm-4 "><button type="button" class="btn btn-warning" onclick="set_ad_Later('+id+',3)" ><strong><i class="fa fa-question"></i> Process Later </strong></button></div>';

	   				break;

	   			case 4:
	   				messagesReportContainer 	+= '<div class="col-sm-4 "><button type="button" class="btn btn-primary" onclick="set_ad_Accepted('+id+',4)" ><strong><i class="fa fa-thumbs-up"></i> Accept</strong></button></div>';
	   				messagesReportContainer 	+= '<div class="col-sm-4 "><button type="button" class="btn btn-danger" onclick="set_ad_Reject('+id+',4)" ><strong><i class="fa fa-thumbs-down"></i> Reject</strong></button></div>';

	   				break;


	   		}

	   		messagesReportContainer  += '</div>';
	   		
	   		application_StudentAction.html( messagesReportContainer );

	    	var content = '<div class="">';

	    		content += '<div class="row"><div class="col-sm-4 text-right"><em>Gender: </em></div><div class="col-sm-8 "><strong>';
	    						if ( studentApplicationData.St_Gender == 1 )
	    						 {
	    						 	content +='<span class="badge bg-blue"> Day </span>';
	    						 }else if( studentApplicationData.St_Gender == 2 )
	    						 {
	    						 	content +='<span class="badge bg-green"> Boarding </span>';
	    						 }

	    		content += '</strong></div></div>';
	    		content += '<div class="row"><div class="col-sm-4 text-right"><em>Country: </em></div><div class="col-sm-8 "><strong>'+studentApplicationData.CountryName+'</strong></div></div>';
	    		content += '<div class="row"><div class="col-sm-4 text-right"><em>Nationality: </em></div><div class="col-sm-8 "><strong>'+studentApplicationData.Nationality+'</strong></div></div>';
	    		content += '<div class="row"><div class="col-sm-4 text-right"><em>Expected Year: </em></div><div class="col-sm-8 "><strong>'+studentApplicationData.St_ExpectedYear+'</strong></div></div>';
	    		content += '<div class="row"><div class="col-sm-4 text-right"><em>Studing Mode: </em></div><div class="col-sm-8 "><strong>';
	    						if ( studentApplicationData.St_Mode == 1 )
	    						 {
	    						 	content +='<span class="badge bg-blue"> Day </span>';
	    						 }else if( studentApplicationData.St_Mode == 2 )
	    						 {
	    						 	content +='<span class="badge bg-green"> Boarding </span>';
	    						 }
	    		content += '</strong></div></div>';
	    		content += '<div class="row"><div class="col-sm-4 text-right"><em> Previous School: </em></div><div class="col-sm-8 "><strong>'+studentApplicationData.St_PreviousSchool+'</strong></div></div>';
	    		content += '<div class="row top-bufferplus"><div class="col-sm-4 text-right"></div><div class="col-sm-8 "><strong>Contact Person</strong></div></div>';
	    		content += '<div class="row"><div class="col-sm-4 text-right"><em>Names : </em></div><div class="col-sm-8 "><strong>'+studentApplicationData.St_Cp_Names+'</strong></div></div>';
	    		content += '<div class="row"><div class="col-sm-4 text-right"><em>Email : </em></div><div class="col-sm-8 "><strong>'+studentApplicationData.St_Cp_Email+'</strong></div></div>';
	    		content += '<div class="row"><div class="col-sm-4 text-right"><em>Phone number : </em></div><div class="col-sm-8 "><strong>'+studentApplicationData.St_Cp_PhoneNumber+'</strong></div></div>';
	    	    content += '</div >';

	    	    application_Student_Content.append( content ) ;

	  //   		<img class="img-responsive img-circle avatar" src="personimage/50/0" /><div class="message"><div class="panel panel-shadow panel-white"><div class="panel-body panel-arrow-left"><div class="chat-box-timeline-title"><strong>rerer</strong><div class="pull-right text-semi"><i class="fa fa-clock-o"></i> moment.unix(item.time).fromNow()</div><center><span> item.subject </span></center></div><div class="chat-box-timeline-content"><blockquote>item.content</blockquote><span class="pull-right">getMessageStatusSpan( item.messageStatus )</span></div></div></div></div></div>';
			// 

	    
	    })
	    .done(function() {

	    	application_Student_Content.animate({ 
	    		//scrollTop: application_Student_Content[0].scrollHeight
	    	}, "slow");

	    	// $("#MessageReSend").click(function(){

		    //    	MsMessageReSend(id,type);

		    // });

	    	// $("#MessageSendNow").click(function(){

		    //   	MsMessageSendNow(id,type);

		    // });

	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });

   };

}

function set_ad_Accepted(id , source )
{
	console.log("Accepted "+source );

	set_adm_StatusChange( id, 1 , source );
}

function set_ad_Reject( id, source )
{
	console.log("Reject "+source );
	set_adm_StatusChange( id, 2 , source );
}

function set_ad_Later( source )
{
	console.log("Later "+source );
	set_adm_StatusChange( id, 3 , source );
}


function set_adm_StatusChange( id, type , source )
{

	var url = base_url+"api/Adm/StudentApplication"; 

    $.post(url, 
     { id: id , type : type , source: source }, 
     function(data, status, xhr){

     	console.log("data "+data );

       if ( data.success )
        {   
        	switch( data.Source )
        	{
        		case "1" :
        			get_ap_NewApplication();
        			break ;

        		case "2" :
        			get_ap_Admited();
        			break ;

        		case "3" :
        			get_ap_Rejected();
        			break ;

        		case "4" :
        			get_ap_Later();
        			break ; 

        		default :
        			console.log( "Can't find the source" );
        			break;

        	}

        	$.gritter.add({
							title: 'Success',
							text: 'Alevel option added.',
							class_name: 'success gritter-center',
							time: ''
						});

		} else {

            $.gritter.add({
				title: 'Failed',
				text: 'Failed . Please try again ',
				class_name: 'danger gritter-center',
				time: ''
			});

        }

    });
}


// function get_mn__inbox()
// {
// 	$("#moduleMain").load("messages/mn__inbox").fadeIn(1000);
// }

// function get_mn_sent()
// {
// 	$("#moduleMain").load("messages/mn_sent").fadeIn(1000);
// }

// function get_mn_templates()
// {
// 	$("#moduleMain").load("messages/mn_templates").fadeIn(1000);
// }

// function get_messages()
// {
// 	$("#moduleMain").load("messages/messages").fadeIn(1000);
// }

// function get_newMessage()
// {
// 	$("#moduleMain").load("messages/new_message").fadeIn(1000);
// }

// function getSentMessageAfterSubmit(threadID,Subject)
// {
// 	$("#moduleContainer").load("messages/main", function() {

// 		$("#moduleMain").load("messages/mn_sent", function() {

// 			messageLoadMessages( threadID , 2 , Subject );

// 		});
// 	});
// }

// function submitMessageNewMessage()
// {

// 	var formNewMessageLoading		= $('#formNewMessageLoading');
// 	var formNewMessage 				= $('#formNewMessage');

// 	formNewMessageLoading.hide();

// 	formNewMessage.on('submit', function(e){
			
// 		var frm = formNewMessage;
// 		var url = base_url+"api/Me/Messages"; 

// 		formNewMessage.hide();
// 		formNewMessageLoading.slideDown();

// 	    e.preventDefault();

// 	    $.ajax({
// 		  type: 'POST',
// 		  url: url,
// 		  data: frm.serialize(),
// 		  success: function(data) {

// 		  			if (data.success)
// 	                {  
// 	    				formNewMessageLoading.hide();

// 	    				 $.gritter.add({
// 							title: 'Success',
// 							text: 'Messages successfuly sent.',
// 							class_name: 'success gritter-center',
// 							time: ''
// 						});

// 	                    $('#moduleMain').load('messages/mn__inbox', function() {
	                   		
// 	                   		 $("#moduleMain").show();
// 	                   		 messageLoadMessages( data.threadID , 1 , data.Subject );

// 	                    });

// 	                } else {

// 	                	$("#moduleMain").show();

// 	                    $.gritter.add({
// 							title: 'Failed',
// 							text: 'Failed to send message.',
// 							class_name: 'danger gritter-center',
// 							time: ''
// 						});

// 	                }

//                   },
//           dataType: "JSON",
// 		  async: true
// 		});

// 	 //    var url2 	    = ''; 
// 	 //    var isFirst     = true;
// 	 //    var isLast  	= false;
// 	 //    var hasStarted  = false;

// 	 //    var TryCounter  = 0;

// 	 //    var MessageNumber ;
// 	 //    var ThreadId 	  ; 

// 	 //    var ThreadOwner	 	  = $('#threadOwner').val();
// 	 //    var IsLastThreadFound = $('#threadFound').val(); 
// 	 //    var LastThreadId 	  = $('#threadFoundId').val();

// 	 //    var progresspump = setInterval(function(){
						    
// 		//     url2 = base_url+"api/Me/Messages?first=true&ThreadOwner="+ThreadOwner+"&IsLastThreadFound="+IsLastThreadFound+"&LastThreadId="+LastThreadId;

// 		//     $.get(url2, function(data){

// 		//     	if( data.isLast )
// 		//     	{
// 		//     		isLast = true;
// 		//     	}

// 		//     	if ( data.hasStarted ) {

// 		//     		hasStarted = true;
// 		//     	}

// 		//     	if( !hasStarted ){

// 		//     		TryCounter++;
// 		//     		console.log("Trying "+TryCounter );

// 		//     		if (TryCounter == 10 )
// 		//     		 {
// 		//     		 	console.log("We have tried "+TryCounter+" times");
// 		//     		 	clearInterval(progresspump);
// 		//     		 }

// 		//     	}else if( data.isMessageReport ) {

// 		//     	 	clearInterval(progresspump);

// 		//     	 	console.log( " Last call " );
// 		//     	 	console.log( data.MessageReport );
		    	 	 
// 		//     	}else if(  isFirst && isLast ) {

// 		//     		MessageNumber  = data.messageNumber;
// 		//     	 	ThreadId 	   = data.threadId;

// 		//     	 	url2 = base_url+"api/Me/Messages?first=false&report=true&threadId="+ThreadId+"&messageNumber="+MessageNumber;

// 		//     	 	console.log(" we have one message and let get report ");

// 		//     	}else if ( isFirst ) {	

// 		//     	 	MessageNumber  = data.messageNumber;
// 		//     	 	ThreadId 	   = data.threadId;
// 		//     	 	hasStarted 	   = data.hasStarted;

// 		//     	 	if (hasStarted)
// 		//     	 	 {
// 		//     	 	 	isFirst = false;
// 		//     	 	 }

// 		//     	 	url2 = base_url+"api/Me/Messages?first=false&report=false&threadId="+ThreadId+"&messageNumber="+MessageNumber;

// 		//     	 	console.log(" The fist call and we continue ");

// 		//     	}else if( isLast ) {

// 		//     	 	 url2 = base_url+"api/Me/Messages?first=false&report=true&threadId="+ThreadId+"&messageNumber="+MessageNumber;

// 		//     	 	 console.log(" This last and let us get report ");

// 		//     	}else{

// 		//     	 	url2 = base_url+"api/Me/Messages?first=false&report=false&threadId="+ThreadId+"&messageNumber="+MessageNumber;

// 		//     	 	console.log(" This normal ... let get the number of sent messages and we continue ");
// 		//     	}

// 		//     })

// 		// }, 1000);

//     });

// }

// function submitMessageNewTemplate()
// {
// 	$('#formMessageNewTemplate').on('submit', function(e){
		
// 	  var frm = $('#formMessageNewTemplate');
// 	  var url = base_url+"api/Me/Templates"; 
//       e.preventDefault();
//       $.post(url, 
//          frm.serialize(), 
//          function(data, status, xhr){
//            if (data.success)
//                 {   
//                 	$('#Model_NewTemplate').modal('toggle');
//                     $.gritter.add({
// 						title: 'Success',
// 						text: 'Template successfuly created.',
// 						class_name: 'success gritter-center',
// 						time: ''
// 					});
// 					get_mn_templates();
//                 } else {

//                     $.gritter.add({
// 						title: 'Failed',
// 						text: 'Failed to add template .',
// 						class_name: 'danger gritter-center',
// 						time: ''
// 					});

//                 }
//          });
//     });
// }

// function onClickMessageTemplate()
// {

// 	$( "table#messageTemplatesTable" ).delegate( "td", "click", function() {

// 	  	id 			= $(this).closest('tr').attr('id');
// 	  	subject 	= $(this).closest('tr').attr('subject');
// 	  	content 	= $(this).closest('tr').attr('content');

// 	  	var messageSelectedTemplate = $( "#messageSelectedTemplate" );
// 	  	messageSelectedTemplate.html('<div class="row text-center"><h4><strong>'+subject+'</strong></h4></div><div class="row lead"><p>'+content+'</p></div>');
	  	
// 	});

// }

// function onMeNewMessageTemplateSelected()
// {

// 	var selectedOption  = $("#newMessageTemplateSelect");
// 	var selected      	= selectedOption.select2('val');
// 	var subject     	= selectedOption.select2('data').text;
// 	var templateContent = selectedOption.select2().find(":selected").data("templatecontent");

// 	$("#newMessageSubject").val(subject);
// 	$("#newMessageContent").val(templateContent);
	
// }

// function messageNewMessageSelect2()
// {
// 	$("#newMessageTemplateSelect").select2().bind('change', onMeNewMessageTemplateSelected);

// 	$("#newMessageSelectStudent").select2();

// 	$("#newMessageSelectClassroom").select2({ 
// 		    formatResult: classroomformatResult, 
// 		    formatSelection: classroomformatSelection, 
// 		    escapeMarkup: function(m) { return m; } 
// 	});

// 	$("#newMessageSelectStaff").select2();
	
// 	$("#newMessageStaffSelectRoles").select2();
// 	$("#selectNewFeeClassroom").select2();

// 	var elementOrganization = $("#selectPaymentOrganization");
//     var selectedOrganization = [];
//     elementOrganization.find("option").each(function(i,e){
//         selectedOrganization[selectedOrganization.length]=$(e).attr("value");
//     });

//     elementOrganization.select2().val(selectedOrganization).trigger('change');

//     $("#selectAlumni").select2();

// }

// function messageNewMessageJS()
// {
// 	   //hide
// 	    var staffTypeContainer 		= $('#staffTypeContainer');
// 	    var studentTypeContainer 	= $('#studentTypeContainer');
// 	    var alumniTypeContainer 	= $('#alumniTypeContainer');

// 	    $("#newMessageSelectTemplateDiv").hide();

// 	    staffTypeContainer.hide();
// 	    alumniTypeContainer.hide();

// 	    var customStudentContainer 		= $("#customStudentContainer");
// 	    var chooseClassesContainer 		= $("#chooseClassesContainer");
// 	    var studentSelectSomeContainer  = $("#studentSelectSomeContainer");
// 	    var staffSelectRoleContainer 	= $("#staffSelectRoleContainer");
// 	    var staffSelectSomeContainer 	= $("#staffSelectSomeContainer");
// 	    var staffSelectAlumnusContainer = $("#staffSelectAlumnusContainer"); 
// 	    var alumniSelectSomeContainer 	= $("#alumniSelectSomeContainer"); 
// 	    var selectOrganizationContainer	= $("#selectOrganizationContainer");

// 	    customStudentContainer.hide();
// 	    chooseClassesContainer.hide();
// 	    studentSelectSomeContainer.hide();
// 	    staffSelectRoleContainer.hide();
// 	    staffSelectSomeContainer.hide();
// 	    staffSelectAlumnusContainer.hide();
// 	    alumniSelectSomeContainer.hide();

// 	    $('input[type=radio][name=receiverType]').change(function() {
	        
// 	        var selected = $(this).val();

// 	        studentSelectSomeContainer.hide();
// 		    customStudentContainer.hide();
// 			staffSelectRoleContainer.hide();
// 	    	staffSelectSomeContainer.hide();

// 	    	staffSelectAlumnusContainer.hide();
// 	    	alumniSelectSomeContainer.hide();

// 	    	staffTypeContainer.hide();
// 	    	studentTypeContainer.hide();
// 	    	alumniTypeContainer.hide();

// 	    	// $('input[type=radio][name=studentType]').prop('checked', false);

// 	    	$("input[name=studentType][value='1']").prop("checked",true);
// 	    	$("input[name=staffType][value='1']").prop("checked",true);
// 	    	$("input[name=alumniType][value='1']").prop("checked",true);

// 		    if ( selected == 1 ) {

// 		    	studentTypeContainer.slideDown();
		    	

// 		    }else if( selected == 2 ) {

// 		    	staffTypeContainer.slideDown();

// 		    }else if ( selected == 3 ) {

// 		    	alumniTypeContainer.slideDown();
// 		    }


// 	    });

// 	    $('input[type=radio][name=studentType]').change(function() {
	        
// 	        var selected = $(this).val();

// 	        customStudentContainer.hide();
// 	        chooseClassesContainer.hide();
// 			studentSelectSomeContainer.hide();

// 		    if ( selected == 1 ) {
		    	

// 		    }else if( selected == 2 ) {

// 		    	customStudentContainer.slideDown();


// 		    }else if( selected == 3 ) {

// 		    	studentSelectSomeContainer.slideDown();
// 		    }

// 	    });

// 	    $('input[type=radio][name=staffType]').change(function() {
	        
// 	        var selected = $(this).val();

// 	        staffSelectRoleContainer.hide();
// 	        staffSelectSomeContainer.hide();

// 		    if ( selected == 1 ) {


// 		    }else if( selected == 2 ) {
				
// 				staffSelectRoleContainer.slideDown();

// 		    }else if( selected == 3 ) {

// 		    	staffSelectSomeContainer.slideDown();
// 		    }

// 	    });


// 	    $('input[type=radio][name=alumniType]').change(function() {
	        
// 	        var selected = $(this).val();

// 	        staffSelectAlumnusContainer.hide();
// 	        alumniSelectSomeContainer.hide();

// 		    if ( selected == 1 ) {

// 		    }else if( selected == 2 ) {
				
// 				staffSelectAlumnusContainer.slideDown();

// 		    }else if( selected == 3 ) {

// 		    	alumniSelectSomeContainer.slideDown();
// 		    }

// 	    });

// 	     $('input[type=radio][name=studentClass]').change(function() {
	        
// 	        var selected = $(this).val();

// 	        chooseClassesContainer.hide();

// 		    if ( selected == 0 ) {

// 		    	chooseClassesContainer.slideDown();

// 		    }else if( selected == 1 ) {
				
// 		    }

// 	    });

// 	    $('#studentSponsorOrg').change(function() {
	        
// 	        if ( $(this).is(":checked") )
// 	         {
// 	         	selectOrganizationContainer.slideDown();

// 	         }else{

// 	         	selectOrganizationContainer.hide();
// 	         }
	        
// 	    });



// 	 //    $("#newMessagesStudentContainer").hide();
// 		// $('#newMessageClassroomType').hide();
// 		// $('#newMessageClassroomContainer').hide();
// 		// $('#newMessageStaffTypeContainer').hide();
// 		// $('#newMessageStaffContainer').hide();
// 		// $('#newMessageStaffRolesContainer').hide();

// 		// $('input[name|="audienceType"]').change(function() {

// 		//     var selected = $(this).val();

// 		//     if ( selected == 1 ) {

// 		//     	$('#newMessageClassroomContainer').hide();
// 		// 		$('#newMessageStaffTypeContainer').hide();
// 		// 		$('#newMessageStaffContainer').hide();
// 		// 		$('#newMessageStaffRolesContainer').hide();

// 		//     	$('#newMessageClassroomType').show();

// 		//     }else if( selected == 0 ) {

// 		//     	$('#newMessageClassroomType').hide();
// 		// 		$('#newMessageClassroomContainer').hide();
// 		// 		newMessagesStudentContainer.hide();

// 		// 		$('#newMessageStaffContainer').hide();
// 		// 		$('#newMessageStaffRolesContainer').hide();

// 		//     	$('#newMessageStaffTypeContainer').show();

// 		//     }

// 		// });

// 		// $('input[name|="studentType"]').change(function() {

// 		//     var selected = $(this).val();

// 		// 	$('#newMessageStaffTypeContainer').hide();
// 		// 	$('#newMessageStaffContainer').hide();
// 		// 	$('#newMessageStaffRolesContainer').hide();

// 		// 	newMessagesStudentContainer.hide();
// 		// 	$('#newMessageClassroomContainer').hide();
// 		// 	$('#newMessageClassroomContainer').hide();

// 		// 	if(  selected == 0 ){

// 		// 		$('#newMessageClassroomContainer').show();

// 		//     }else if( selected == 1 ){

// 		//     	$('#newMessageClassroomContainer').hide();

// 		//     }else if( selected == 2 )
// 		//     {
// 		//     	newMessagesStudentContainer.show();
// 		//     }

// 		// });

// 		// $('input[name|="staffType"]').change(function() {
			
// 		// 	var selected = $(this).val();

// 		// 	$('#newMessageClassroomType').hide();
// 		// 	$('#newMessageClassroomContainer').hide();
// 		// 	$('#newMessageStaffContainer').hide();
// 		// 	$('#newMessageStaffRolesContainer').hide();

// 		// 	if ( selected == 1 ) 
// 		// 	 {

// 		// 	 }
// 		// 	 else if(  selected == 2 )
// 		// 	 {
// 		// 	 	$('#newMessageStaffContainer').show();
// 		// 	 }
// 		// 	 else if( selected == 3 )
// 		// 	 {
// 		// 	 	$('#newMessageStaffRolesContainer').show();
// 		// 	 }


// 		// });

// 	 //    $('input[name|="messageType"]').change(function() {

// 		//     var selected = $(this).val();

// 		//     if ( selected == 1 ) {

// 		//     	$('#newMessageSelectTemplateDiv').hide();

// 		//     }else if( selected == 0 ) {

// 		//     	$('#newMessageSelectTemplateDiv').show('slow');

// 		//     }

// 		// });

// }

// function messageInboxTreadSelected()
// {
// 	$( "table#messageInboxTreadMessages" ).delegate( "td", "click", function() {

// 	  	id 					 = $(this).closest('tr').attr('id');
// 	  	msgsubject 			 = $(this).closest('tr').attr('msgsubject');

// 	  	messageLoadMessages( id , 1 , msgsubject);
// 	});
// }

// function messageSentTreadSelected()
// {
// 	$( "table#messageInboxTreadMessages" ).delegate( "td", "click", function() {

// 	  	id 					 = $(this).closest('tr').attr('id');
// 	  	msgsubject 			 = $(this).closest('tr').attr('msgsubject');

// 	  	messageLoadMessages( id , 2 , msgsubject);

// 	});
// }




// function MsMessageReSend(ThreadId, MsgType)
// {
// 	var application_StudentAction 		= $("#application_StudentAction");
// 	var resendMsgLoading 	= $("#resendMsgLoading");

// 	application_StudentAction.hide();
// 	resendMsgLoading.slideDown();

// 	var url = base_url+"api/Me/MessageReSend?Thread="+ThreadId; 

// 	$.getJSON( url, function(data) {

//    		if (data.success)
//         {   
// 			resendMsgLoading.hide();

// 			 $.gritter.add({
// 				title: 'Success',
// 				text: 'Messages successfuly sent.',
// 				class_name: 'success gritter-center',
// 				time: ''
// 			});

//             $('#moduleMain').load('messages/mn__inbox', function() {
            	
//            		 $("#moduleMain").show();
//            		 messageLoadMessages( data.threadID , MsgType , data.Subject );

//             });

//         } else {

// 			resendMsgLoading.hide();
//         	application_StudentAction.slideDown();

//             $.gritter.add({
// 				title: 'Failed',
// 				text: 'Failed to re send message.',
// 				class_name: 'danger gritter-center',
// 				time: ''
// 			});

//         }

//     })
//     .done(function() {

//     })
//     .fail(function() {
//     })
//     .always(function() {
//     });

// }

// function MsMessageSendNow(ThreadId, MsgType)
// {

// 	var application_StudentAction 		= $("#application_StudentAction");
// 	var resendMsgLoading 	= $("#resendMsgLoading");

// 	application_StudentAction.hide();
// 	resendMsgLoading.slideDown();

// 	var url = base_url+"api/Me/MessageSendNow?Thread="+ThreadId; 

// 	$.getJSON( url, function(data) {

//    		if (data.success)
//         {   
// 				resendMsgLoading.hide();

// 				 $.gritter.add({
// 					title: 'Success',
// 					text: 'Messages successfuly sent.',
// 					class_name: 'success gritter-center',
// 					time: ''
// 				});

// 	            $('#moduleMain').load('messages/mn__inbox', function() {

// 	           		$("#moduleMain").show();
// 	           		messageLoadMessages( data.threadID , MsgType , data.Subject );

// 	            });

// 	        } else {

// 	        	resendMsgLoading.hide();
//         		application_StudentAction.slideDown();

// 	            $.gritter.add({
// 					title: 'Failed',
// 					text: 'Failed to send message.',
// 					class_name: 'danger gritter-center',
// 					time: ''
// 				});

// 	        }

//     })
//     .done(function() {
	

//     })
//     .fail(function() {
//     })
//     .always(function() {
//     });

// }

// function getMessageStatusSpan( status )
// {
// 	var content ='';

// 	switch(status) {
// 	    case "1":
// 	    	content = '<span class="badge"> Pending...</span>';
// 	        break;

// 	    case "2":
// 	       content = '<span class="badge bg-aqua"> Sending...</span>';
// 	        break;

//         case "3":
//         	content = '<span class="badge bg-blue "> Sent</span>';
// 	        break;

// 	    case "4":
//         	content = '<span class="badge  bg-red "> Failed</span>';
// 	        break;

// 	    case "5":
// 	    	content = '<span class="badge  bg-yellow "> Received</span>';
// 	        break;

// 	    case "6":
//         	content = '<span class="badge bg-green "> Seen</span>';
// 	        break;

// 	    case "7":
//         	content = '<span class="badge bg-maroon "> Invalid Phone Number</span>';
// 	        break;
// 	}

// 	return content ;
// }

//academics 
	//main 
	function getAcademics(){
		
		$("#moduleContainer").load("academics/main", function() { getClassrooms(); } ).fadeIn(4000);

	}

	//classrooms
	function getClassrooms()
	{
		$("#moduleMain").load("academics/mn__classrooms").fadeIn(4000);
	}

	//subjects
	function get_mn_subjects()
	{	
		$(this).parent("li").addClass('active');
		$("#moduleMain").load("academics/mn_subjects").fadeIn(4000);
	}
	//Programmes offered

	function get_mn_programmeOffered()
	{
		$("#moduleMain").load("academics/mn_programmeOffered").fadeIn(4000);
	}

	//Academic years
	function get_mn_academicYears()
	{
		$("#moduleMain").load("academics/mn_academicYears").fadeIn(4000);
	}

	//Terms
	function get_mn_terms()
	{
		$("#moduleMain").load("academics/mn_terms").fadeIn(4000);
	}

	//Levels
	function get_mn_levels()
	{
		$("#moduleMain").load("academics/mn_levels").fadeIn(4000);
	}

	//A Level Options
	function get_mn_aLevelOptions()
	{
		$("#moduleMain").load("academics/mn_aLevelOptions").fadeIn(4000);
	}

	function get_profile_classroom()
	{
		$("#moduleContainer").load("academics/profile_classroom").fadeIn(4000);
	}

	function onAcNewLevelChange()
	{
		var selected = $("#selectAcademicNewLevel").select2('val');
		var index = selected -1;

		$("#selectYearName").select2("val",selected); 
		$('#firstYear').val("1");
		$('#lastYear').val(yearname[index]['yearsOfStudy']);

		$('#academics_newLevelFrom').show("slow");
		$('#academics_newLevelTo').show("slow");

	}

	function hiddPaymentNewFee()
	{

	}

	function hideAcademicNewClassroom()
	{
		$("#newClassYear").hide();
		$("#newClassALevel").hide();
		$("#newClassStream").hide();
	}

	function hideNewSubject()
	{
		$("#subjectSubject").hide("fast");
	}

	function onAcNewLevelYearNameChange()
	{
		var selected = $("#selectYearName").select2('val');

		switch(selected) {
		    case 1:
		       //set name 
		       
		        break;
		    case 2:
		       
		        break;
	        case 3:
	        	
		        break;
		}

	}

	function academicNewLevelSelect2()
	{
		$("#selectAcademicNewLevel").select2({
	        allowClear: true,
	        placeholder: "select level"
	    }).bind('change', onAcNewLevelChange);

		$("#selectYearName").select2({
	        allowClear: true,
	        placeholder: "select level"
	    }).bind('change', onAcNewLevelYearNameChange);

	}

	function hideAllAcademicsNewLevel()
	{
		$("#academics_newLevelFrom").hide("fast");
		$("#academics_newLevelTo").hide("fast");

	}

	function academicsNewAlevel()
	{	

		var sel = $("#selectYearName");
		    sel.empty();
		    for (var i=0; i<yearname.length; i++) {
		      sel.append('<option value="' + yearname[i]['id'] + '">' + yearname[i]['name'] + '</option>');
		    }

	}

	function onAcClassEnrollmentLastYearClassChange()
	{

		var AcClassEnrollmentLastYearClass  = $("#AcClassEnrollmentLastYearClass");
		var selected  						= AcClassEnrollmentLastYearClass.select2('val');

		var AcClassEnrollmentLastYearStudentsContainer = $("#AcClassEnrollmentLastYearStudentsContainer");
		AcClassEnrollmentLastYearStudentsContainer.slideDown();

		var AcClassEnrollmentLastYearStudents = $("#AcClassEnrollmentLastYearStudents");
		AcClassEnrollmentLastYearStudents.html('<img src="../packages/assets/plugins/img/loading.gif" alt="Loading students..." > Loading students...');

		var url = base_url+"api/Ac/classrooms/"+selected+"?ClassInfoType=3";
		
		  $.getJSON( url, function(data) {

	  		var ClassStudentsYearEnrollment = data.ClassStudentsYearEnrollment;

	  		var content = '';

	  		var defaultStatusId = 1 ;
	  		var statusName 		= "Promoted";

	  		content +='<div class="row"><span class="col-sm-12 text-center"><label class="text-blue lead top-buffer">Promoted</label><br><span id="deliberationStatusSelectAll_1" ><a  href="#"> <i class="fa fa-check-square-o"></i> Select All</a></span></span></div>';

			$.each( ClassStudentsYearEnrollment, function(i, item) {

				if ( item.deliberationStatusID != defaultStatusId ) {

					defaultStatusId = item.deliberationStatusID ;
					statusName 		= item.statusName ;

					if ( defaultStatusId == 2 ) {
						content +='<div class="row"><span class="col-sm-12 text-center"><label class="text-black lead top-buffer">'+statusName+'</label><br><span id="deliberationStatusSelectAll_2" ><a href="#"> <i class="fa fa-check-square-o"></i> Select All</a></span> </span></div>';

					}else if ( defaultStatusId == 3 ) {
						content +='<div class="row"><span class="col-sm-12 text-center"><label class="text-orange lead top-buffer">'+statusName+'</label><br><span id="deliberationStatusSelectAll_3" ><a href="#"> <i class="fa fa-check-square-o"></i> Select All</a></span> </span></div>';

					}else if ( defaultStatusId == 4 ) {
						content +='<div class="row"><span class="col-sm-12 text-center"><label class="text-red lead top-buffer">'+statusName+'</label><br><span  id="deliberationStatusSelectAll_4" ><a  href="#"> <i class="fa fa-check-square-o"></i> Select All</a></span> </span></div>';

					}

				}

			 	content +='<div class="row"><span class="col-sm-12 checkbox"><label><input class="deliberationStatus_'+defaultStatusId+'" type="checkbox" name="lastYearClassStudent[]" value="'+item.studRegNber+'" >'+item.studentNames+' - '+item.studRegNber+'</label></span></div>';

		    });

			AcClassEnrollmentLastYearStudents.html(content);

			AcClassEnrollment_OnSelect(1);
			AcClassEnrollment_OnSelect(2);
			AcClassEnrollment_OnSelect(3);
			AcClassEnrollment_OnSelect(4);

		  }).done(function() { 
		  })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}

	function submitAcClassEnrollmentForm()
	{	
		var AcClassEnrollmentSubmitForm = $("#AcClassEnrollmentSubmitForm") ;

		AcClassEnrollmentSubmitForm.submit(function( event ) {
		 
		  event.preventDefault();

		    var AcClassEnrollmentLoadingContainer =  $("#AcClassEnrollmentLoadingContainer") ;
			var AcClassEnrollmentSubmitContainer  =  $("#AcClassEnrollmentSubmitContainer") ;

			AcClassEnrollmentSubmitContainer.hide();
			AcClassEnrollmentLoadingContainer.slideDown();

		    $.ajax({
		      url: base_url+"api/Ac/classrooms/manageEnrollment",
		      dataType: 'json',
		      type: 'POST', 
		      data: AcClassEnrollmentSubmitForm.serialize(),
		      success: function ( res ) {
		      	
		        if ( res.success )
		        {

		              $.gritter.add({
		                  title: 'Success',
		                  text: 'Students added.',
		                  class_name: 'success gritter-center',
		                  time: ''
		              });

		              $('#modal_AcClassEnrollment').modal('toggle');
		              loadAcademicsClassrooms();

		        }else{  

		              $.gritter.add({
		                  title: 'Failed',
		                  text: 'Failed to add students.',
		                  class_name: 'danger gritter-center',
		                  time: ''
		              });

		        }

		      }

		    }).done(function() { 

		    	AcClassEnrollmentLoadingContainer.hide();
		    	AcClassEnrollmentSubmitContainer.slideDown();
			
		    }).fail(function() {

		       $.gritter.add({
		                  title: 'Failed',
		                  text: 'Failed to add students.Please try again',
		                  class_name: 'danger gritter-center',
		                  time: ''
		              });

		    })
		    .always(function() {
		    
		    });

		});
	}

	function submitAcClassSubjectsForm(){

		var AcClassSubjectSubmitForm = $("#AcClassSubjectSubmitForm") ;
		AcClassSubjectSubmitForm.submit(function( event ) {
		 
		  event.preventDefault();

		    var AcClassSubjectLoadingContainer =  $("#AcClassSubjectLoadingContainer");
			var AcClassSubjectSubmitContainer  =  $("#AcClassSubjectSubmitContainer");

			AcClassSubjectSubmitContainer.hide();
			AcClassSubjectLoadingContainer.slideDown();

			var AcClassSubjectsSelectedClassSubjects = $("#AcClassSubjectsSelectedClassSubjects");
			AcClassSubjectsSelectedClassSubjects.empty();

			// Iterate over all selected checkboxes
		      $.each(rows_selected_ClassSubject , function(index, rowId){

			    // Create a hidden element 
			        AcClassSubjectsSelectedClassSubjects.append(
			             $('<input>')
			                .attr('type', 'hidden')
			                .attr('name', 'SelectedClassSubjectId[]')
			                .val(rowId)
			        );
		      });

		    $.ajax({
		      url: base_url+"api/Ac/classrooms/manageSubjects",
		      dataType: 'json',
		      type: 'POST', 
		      data: AcClassSubjectSubmitForm.serialize(),
		      success: function ( res ) {
		      	
		        if ( res.success )
		        {

		              $.gritter.add({
		                  title: 'Success',
		                  text: 'Subjects added.',
		                  class_name: 'success gritter-center',
		                  time: ''
		              });

						var AcClassSubjectsSelectedClassSubjectsStudents = $("#AcClassSubjectsSelectedClassSubjectsStudents");
						var AcClassSubjectsSelectedClassSubjects 		 = $("#AcClassSubjectsSelectedClassSubjects");

						var AcClassSubjectLoadingContainer 		 		 = $("#AcClassSubjectLoadingContainer");

				    	AcClassSubjectsSelectedClassSubjectsStudents.empty();
				    	AcClassSubjectsSelectedClassSubjects.empty();

				    	AcClassSubjectLoadingContainer.hide();
				    	
					    $("#AcClassSubjectThisYearClass").select2('data', null);
					    $("#AcClassSubjectLastYearClass").select2('data', null);

		              $('#modal_AcClassSubject').modal('toggle');
		              loadAcademicsClassrooms();

		        }else{  

		              $.gritter.add({
		                  title: 'Failed',
		                  text: 'Failed to add Subjects.',
		                  class_name: 'danger gritter-center',
		                  time: ''
		              });

		        }

		      }

		    }).done(function() { 

		    }).fail(function() {

		       $.gritter.add({
		                  title: 'Failed',
		                  text: 'Failed to add Subjects.Please try again',
		                  class_name: 'danger gritter-center',
		                  time: ''
		              });

		    })
		    .always(function() {
		    	
		    	AcClassSubjectLoadingContainer.hide();
		    	AcClassSubjectSubmitContainer.slideDown();

		    });

		});

	}

	function AcClassEnrollment_OnSelect( status ){

		$( "#deliberationStatusSelectAll_"+status ).delegate( "a", "click", function( event ) {
			  
		  	event.preventDefault();
		  	$( this ).parent().replaceWith('<span id="deliberationStatusDeSelectAll_'+status+'"><a href="#"><i class="fa fa-square-o"></i> Deselect All</a></span>');
		  	$('.deliberationStatus_'+status ).prop('checked', true);

		  	AcClassEnrollment_DeSelect( status );

		});

	}

	function AcClassEnrollment_DeSelect( status ){

		$( "#deliberationStatusDeSelectAll_"+status ).delegate( "a", "click", function( event ) {
			  
		  	event.preventDefault();
		  	$( this ).parent().replaceWith('<span id="deliberationStatusSelectAll_'+status+'"><a href="#"><i class="fa fa-check-square-o"></i> Select All</a></span>');
		  	$('.deliberationStatus_'+status ).prop('checked', false);

		  	AcClassEnrollment_OnSelect( status );
		  	
		});
	}

	function onNewClassroomLevelChange()
	{
		
		var selected = $("#selectAcademicsNewClassroom").select2('val');
		
		var sel = $("#selectAcademicsNewClassroomYear");

		sel.empty();

		for (var i=0; i<levels.length; i++) {


			if (levels[i]['id'] == selected)
			{
				var firstYear = Number(levels[i]['firstYear']);
				var lastYear  = Number(levels[i]['lastYear']);

				for ( var j = firstYear; j <= lastYear ; j++) {

					sel.append('<option value="'+j+'">' + j+ '</option>');

				}

			}

	    }

	    $("#newClassYear").show("slow");

	    var actionType = $('#newClassActionType').val();

	    if ( actionType == 0 ) {
	    	$('#newClassStream').slideDown();
			$('#newClassStreamName').hide();

	    }else{

	    	$('#newClassStream').hide();
			$('#newClassStreamName').slideDown();
	    }
	    
	}

	function onNewClassroomYearChange()
	{

		var selectAcademicsNewClassroom 	= $("#selectAcademicsNewClassroom");
		var selectAcademicsNewClassroomYear = $("#selectAcademicsNewClassroomYear");

		var selectedLevel = selectAcademicsNewClassroom.select2('val');
		var levelyearid   = selectAcademicsNewClassroom.select2().find(":selected").data("levelyearid");

		var selectedYear  = selectAcademicsNewClassroomYear.select2('val');
		var actionType = $('#newClassActionType').val();
		
		if ((levelyearid == 3) && (parseFloat(selectedYear) > 3) )
		{
			$("#newClassALevel").slideDown();

			if ( actionType == 0 ) {
		    	$("#newClassStream").hide();
				$("#newClassStreamName").hide();
				
		    }else{
		    	$("#newClassStream").hide();
				$("#newClassStreamName").slideDown();

		    }

		}else{

			$("#newClassALevel").slideUp();

			if ( actionType == 0 ) {
		    	$('#newClassStream').slideDown();
				$('#newClassStreamName').hide();
				
		    }else{

		    	$('#newClassStream').hide();
				$('#newClassStreamName').slideDown();
		    }

		}

	}

	function populateNewSubejctSubject(subjects)
	{	
		var selSubjects = $("#selectSubjectSubject");
		selSubjects.empty();

		var subjectType 	= "";
		var isTheFirsRecord = true;

		var subjectsSize = subjects.length;

		var subjectOptions = '';
		
		subjectOptions +='<option ></option>';

		for (var i = 0; i<subjectsSize; i++) {

			if (subjectType == subjects[i]['subjectTypeID'])
			{
				subjectOptions +='<option value="'+subjects[i]['subjectID']+'" data-type="'+subjects[i]['subjectTypeID']+'" >' +subjects[i]['name']+ '</option>';

			}else{

				subjectType = subjects[i]['subjectTypeID'];

				if (isTheFirsRecord)
				{
					isTheFirsRecord = false;

				} else{
					subjectOptions +='</optgroup>';
				}

					subjectOptions +='<optgroup label="'+subjects[i]['subjectType']+'">';
					subjectOptions +='<option value="'+subjects[i]['subjectID']+'" data-type="'+subjects[i]['subjectTypeID']+'" >' +subjects[i]['name']+ '</option>';
			}

	    }

	    $("#subjectSubject").show("slow");

	    selSubjects.append(subjectOptions);
		selSubjects.select2().bind('change', onNewSubjectSubjectChange).trigger('change');

	}

	function onNewSubjectLevelChange()
	{	

		var sel 		= $("#selectSubjectLevel");

		var selected 	= sel.select2('val');
		var level 		= sel.find(":selected").data("level"); 

		switch(level){
			case 1:
				populateNewSubejctSubject(subjectsLevel1);
			break;
			case 2:
				populateNewSubejctSubject(subjectsLevel2);
			break;
			case 3:
				populateNewSubejctSubject(subjectsLevel3);
			break;

			case 4:
				populateNewSubejctSubject(subjectsLevel4);
			break;

		}

	}
    
    function onNewSubjectSubjectChange()
    {
    	var sel 			= $("#selectSubjectSubject");
		var subjectTypeID 	= sel.select2().find(":selected").data("type");

		$('#selectSubjectType').val(subjectTypeID);

    }


	function onNewClassroomTeacherChange()
	{
		var selected = $("#selectNewClassroomTeacher").select2('val');
		$('#selectNewClassroomRecorder').val(selected).trigger('change');
		
	}

	function formatSelection(state) { 

    	if (state.id > 0)
    	{
    		return "<img class='img-circle' style='max-width:30px; max-height:30px;' src='images/schoolStaff/50/"+state.id+"' /> " + state.text;
    	}
    	else{

    		return state.text;
    	}
	    
	} 

	function formatResult(state) { 

    	if (state.id > 0)
    	{
    		return "<img class='img-circle'  src='images/schoolStaff/50/"+state.id+"' /> " + state.text; 
    	}
    	else{
    		
    		return state.text;
    	}
	    
	} 

	function academicNewClassroomSelect2()
	{	
		$("#selectAcademicsNewClassroom").select2({
	        allowClear: true,
	        placeholder: "select level"
	    }).bind('change', onNewClassroomLevelChange);

	    $("#selectAcademicsNewClassroomYear").select2({
	        allowClear: true,
	        placeholder: "select level"
	    }).bind('change', onNewClassroomYearChange);

	    $("#selectAlphabet").select2({
	        allowClear: true,
	        placeholder: "select level"
	    });

	    $("#selectAcademicsNewClassroomRoom").select2({
	        allowClear: true,
	        placeholder: "select level"
	    });

	    $("#selectAcademicsNewClassALevel").select2();

		$("#selectNewClassroomTeacher").select2().bind('change', onNewClassroomTeacherChange);

		$("#selectNewClassroomRecorder").select2(); 

	}

	function academicNewSubjectSelect2()
	{	
		$("#selectSubjectLevel").select2({ 
			    formatResult: classroomformatResult, 
			    formatSelection: classroomformatSelection, 
			    escapeMarkup: function(m) { return m; } 
		}).bind('change', onNewSubjectLevelChange );

		$("#selectNewSubjectTeacher").select2();
		
		$("#sg_selectSubjectLevel").select2({ 
			    formatResult: classroomformatResult, 
			    formatSelection: classroomformatSelection, 
			    escapeMarkup: function(m) { return m; } 
		});
		
		$("#sg_selectSubjectType").select2();
		$("#sg_selectSubjectSubject").select2();
		$("#sg_selectNewSubjectTeacher").select2();
	}

	//submit 
	function submitAcademicsNewAlevelOption()
	{
		$('#schoolNewAlevelOption').on('submit', function(e){
			
		  var frm = $('#schoolNewAlevelOption');
		  var url = base_url+"api/Ac/alevelOptions"; 

	      e.preventDefault();
	      $.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){
	           		if (data.success)
	                {   
	                	$('#Model_newALevelOption').modal('toggle');
	                    $.gritter.add({
							title: 'Success',
							text: 'Alevel option added.',
							class_name: 'success gritter-center',
							time: ''
						});
						
	                    get_mn_aLevelOptions();

	                } else {

	                    $.gritter.add({
							title: 'Failed',
							text: 'Failed to add option .',
							class_name: 'danger gritter-center',
							time: ''
						});

	                }
	         });
	    });
	}

	function submitAcademicsNewAlevel()
	{
		$('#academicsSchoolLevelOption').on('submit', function(e){
			
		  var frm = $('#academicsSchoolLevelOption');
		  var url = base_url+"api/Ac/levels"; 
	      e.preventDefault();
	      $.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){
	           if (data.success)
	                {   
	                	$('#Model_newLevel').modal('toggle');
	                    $.gritter.add({
							title: 'Success',
							text: 'Level added.',
							class_name: 'success gritter-center',
							time: ''
						});

	                    get_mn_levels();

	                } else {

	                    $.gritter.add({
							title: 'Failed',
							text: 'Failed to add level .',
							class_name: 'danger gritter-center',
							time: ''
						});

	                }
	         });
	    });
	}

	function submitAcademicsNewProgramme()
	{
		$('#academicsSchoolProgramme').on('submit', function(e){
			
		  var frm = $('#academicsSchoolProgramme');
		  var url = base_url+"api/Ac/programmeOffered"; 
	      e.preventDefault();
	      $.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){
	           if (data.success)
	                {   
	                	$('#Model_AddProgramme').modal('toggle');
	                    $.gritter.add({
							title: 'Success',
							text: 'Programme added.',
							class_name: 'success gritter-center',
							time: ''
						});

	                    get_mn_programmeOffered();

	                } else {

	                    $.gritter.add({
							title: 'Failed',
							text: 'Failed to add programme.',
							class_name: 'danger gritter-center',
							time: ''
						});

	                }
	         });
	    });
	}

	function submitAcademicsNewUpdateClassroom()
	{
		$('#academicsNewClassroom').on('submit', function(e){
			
		    var frm        = $('#academicsNewClassroom');
		    var actionType = $('#newClassActionType').val();

		    e.preventDefault();

		    if ( actionType == '0')
		    {
		   		var url = base_url+"api/Ac/classrooms"; 
		    	submitAcademicsNewClassroom(frm,url);

		    }else if( actionType == '1' )
		    {	
		   		var classID = $('#newClassClassid').val();
		   		var url 	= base_url+"api/Ac/classrooms/"+classID; 
		   		submitAcademicsUpdateClassroom(frm,url,classID);
		    }

	    });
	}

	function submitAcademicsNewClassroom(frm,url)
	{
         $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
            if (data.success)
            {   
            	$('#Model_NewClassroom').modal('toggle');
                $.gritter.add({
					title: 'Success',
					text: 'Classroom added.',
					class_name: 'success gritter-center',
					time: ''
				});
				
                clearAcNewUpdateClassForm();
                DashAcSelectAllClassroom();

            } else {

            	if (data.duplicate)
            	{
            		var yearname 		 = $("#yearname").text();
            		var duplicatedyear   = data.duplicatedyear;
            		var duplicatedstream = data.duplicatedstream;

            		$.gritter.add({
						title: 'Failed',
						text: 'Stream <b>'+duplicatedstream+'</b> already exist in <b>'+yearname+' '+duplicatedyear+' </b>, select different stream.',
						class_name: 'warning gritter-center',
						time: ''
					});
            	}else{

            		$.gritter.add({
						title: 'Failed',
						text: 'Failed to add classroom.',
						class_name: 'danger gritter-center',
						time: ''
					});
            	}
            }
        });
	  
	}

	function submitAcademicsUpdateClassroom(frm,url,classID)
	{
		$.ajax({
                url: url,
                type: 'PUT',
                data: frm.serialize(),
                success: function(data) {
                	if (data.success)
		            {   
		            	$('#Model_NewClassroom').modal('toggle');
		                $.gritter.add({
							title: 'Success',
							text: 'Classroom updated.',
							class_name: 'success gritter-center',
							time: ''
						});
						
		                clearAcNewUpdateClassForm();
		                DashAcSelectAllClassroom();

		            } else {

		            	if (data.duplicate)
		            	{
		            		var yearname 		 = $("#yearname").text();
		            		var duplicatedyear   = data.duplicatedyear;
		            		var duplicatedstream = data.duplicatedstream;

		            		$.gritter.add({
								title: 'Failed',
								text: 'Stream <b>'+duplicatedstream+'</b> already exist in <b>'+yearname+' '+duplicatedyear+' </b>.',
								class_name: 'warning gritter-center',
								time: ''
							});
		            	}else{

		            		$.gritter.add({
								title: 'Failed',
								text: 'Failed to update classroom.',
								class_name: 'danger gritter-center',
								time: ''
							});
		            	}
		            }

                }
        });
	}

	function clearAcNewUpdateClassForm()
	{
		$("#selectAcademicsNewClassroomYear").select2('val', '');
		$("#selectAlphabet").select2('val', '');
		$("#selectAcademicsNewClassroomRoom").select2('val', '');
		$("#selectAcademicsNewClassALevel").select2('val', '');
		$("#selectNewClassroomTeacher").select2('val', '');
		$("#selectNewClassroomRecorder").select2('val', '');
		$("#newClassCapacity").val("");
	}

	function  clearAcNewUpdateSubjectForm()
	{	
		$("#selectSubjectLevel").select2('val', '');
		$("#newSubjectMaximum").val("");
		$("#selectSubjectType").val("");
		$("#selectSubjectSubject").select2('val', '');
		$("#selectNewSubjectTeacher").select2('val', '');
		$("input[name=isOnReport][value=1]").prop('checked', true);
		$("input[name=isCalculated][value=1]").prop('checked', true);

		$('#subjectSubject').hide();
	}

	function submitAcademicsNewUpdateSubject()
	{	
		$('#academicsNewSubject').on('submit', function(e){
			
		    var frm        = $('#academicsNewSubject');
		    var actionType = $('#newSubjectActionType').val();

		    e.preventDefault();

		    if ( actionType == '0')
		    {
		   		var url = base_url+"api/Ac/subjects"; 
		    	submitAcademicsNewSubject(frm,url);

		    }else if( actionType == '1' )
		    {	
		   		var classSubjectID = $('#newSubjectSubjectid').val();
		   		var url 	  	   = base_url+"api/Ac/subjects/"+classSubjectID; 
		   		submitAcademicsUpdateSubject(frm,url,classSubjectID);
		    }

	    });

		$('#sg_academicsNewSubject').on('submit', function(e){
			
		    var frm     = $('#sg_academicsNewSubject');
		    var url 	= base_url+"api/Ac/studyGroup";

		    e.preventDefault();

		    $.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){
	           if (data.success)
	                {   
	                	$('#Model_StudyGroup').modal('toggle');

	                    $.gritter.add({
							title: 'Success',
							text: 'Study Group added.',
							class_name: 'success gritter-center',
							time: ''
						});
	               
	                } else {

	                    $.gritter.add({
							title: 'Failed',
							text: 'Failed to add subject.',
							class_name: 'danger gritter-center',
							time: ''
						});

	                }
	        });

	    });


	}	

	function submitAcademicsNewSubject(frm,url)
	{

		$.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
           if (data.success)
                {   
                	$('#Model_SubjectToClassroom').modal('toggle');
                    $.gritter.add({
						title: 'Success',
						text: 'Subject added.',
						class_name: 'success gritter-center',
						time: ''
					});
					
					clearAcNewUpdateSubjectForm();
               
                } else {

                    $.gritter.add({
						title: 'Failed',
						text: 'Failed to add subject.',
						class_name: 'danger gritter-center',
						time: ''
					});

                }
        });

	}

	function submitAcademicsUpdateSubject(frm, url, subjectID)
	{
		$.ajax({
                url: url,
                type: 'PUT',
                data: frm.serialize(),
                success: function(data) {
                	if (data.success)
		            {   
		            	$('#Model_SubjectToClassroom').modal('toggle');
		                $.gritter.add({
							title: 'Success',
							text: 'Subject updated.',
							class_name: 'success gritter-center',
							time: ''
						});
						
		                clearAcNewUpdateSubjectForm();
		                var  ClassID 	= $('#dashAcNewClassteacherClass').val();
	    				var  ClassName  = $('#dashAcNewClassteacherClassName').val();

	    				loadDashSeClassroom(ClassID ,ClassName );
	    				
		            } else {
		            	
	            		$.gritter.add({
							title:'Failed',
							text: 'Failed to update subject.',
							class_name: 'danger gritter-center',
							time: ''
						});
	            	}
		         
                }
        });
	}

	function loadAcademicsClassrooms()
	{
	 
	var container          = $( "#dashAcademics_content" ); 

 	container.empty();

	 var url = base_url+"dash/secretary";

	  $.getJSON( url, function(data) {
	      
	    TotalStudent    = data.TotalStudent;
	    TotalStaff    	= data.TotalStaff;
	    Classrooms      = data.classrooms;
	    hasBranch  		= data.hasBranch;

	    leftContent = '<div class="col-sm-12"><div class="col-sm-12">';
	    leftContent += '<div class="row"><div class="col-sm-12 text-center"><h4><strong>Classes</strong></h4></div></div>'; 
	   	
	   	if (hasBranch) {
	   		leftContent += '<div class="row"><table class="table table-striped table-hover dashSeClassroomTableCl" id="dashSeClassroomTable" ><thead><tr><th>Branch</th><th>Name</th><th class="text-center">Enrolled Students<br>(View,add,remove)</th><th class="text-center">Students List</th><th class="text-center">Number Of Subject</th><th class="text-center">Available Seats</th><th>Class Teacher</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead><tbody>';
		
	   	}else{
	   		leftContent += '<div class="row"><table class="table table-striped table-hover dashSeClassroomTableCl" id="dashSeClassroomTable" ><thead><tr><th>Name</th><th class="text-center">Enrolled Students<br>(View,add,remove)</th><th class="text-center">Students List</th><th class="text-center">Number Of Subject</th><th class="text-center">Available Seats</th><th>Class Teacher</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead><tbody>';
		
	   	}
		
		var classIndex = 1;

	   	$.each(Classrooms, function(i, item) {

	        leftContent +="<tr id='"+item.id+"' classname='"+item.name+"' >";

	          if (hasBranch) {
	          	leftContent +="<td>"+item.branchName+"</td>";
	          }

	          leftContent +="<td>"+item.name+"</td>"; 
	          if ( item.numberOfStudent > 0 ) {
	          	 leftContent +='<td class="text-center AcClassroomManageList"><a href="#">'+item.numberOfStudent+' <i class="fa fa-align-right text-green"></i></a></td>';
	          	 leftContent +="<td class='row text-center AcClassroomDownload'><a target='_blank' href='api/Ac/PDF/ClassList/"+item.id+"'><i class=' fa fa-download text-green'></i> Download</a></td>";

	          }else{
	          	 leftContent +='<td class="text-center AcClassroomImportStudent"><a href="#"><i class="fa fa-plus text-green"></i> Add Students</a></td>';
	          	 leftContent +="<td ></td>";
	          }

	          if ( item.numberOfSubject > 0 ) {
	          	 leftContent +='<td class="text-center AcClassroomManageSubject"><a href="#">'+item.numberOfSubject+'<i class="fa fa-align-right text-green"></i></a></td>';
	          }else{
	          	 leftContent +='<td class="text-center AcClassroomManageSubject"><a href="#"><i class="fa fa-plus text-green"></i> Add Subjects</a></td>';
	          }
	          leftContent +="<td class='text-center'>"+ClassroomCongestion(item.numberOfStudent,item.capacity)+"</td>";
	          leftContent +="<td> "+item.classTeacherNames+"</td>";
	          leftContent +='<td class="text-center AcClassroomEdit"><a href="#"><i class="fa fa-pencil"></i></a></td>';
	          if ( item.numberOfStudent > 0 ) {
	          	 leftContent +='<td class="text-center"></td>';
	          }else{
	          	 leftContent +='<td class="text-center AcClassroomDelete"><a href="#"><i class="fa fa-times text-red"></i></a></td>';
	          }
	        leftContent +="</tr>";
			
			classIndex++;

	    });
		    
	    
	  }).done(function() {

	  	leftContent +='</tbody></table></div>';
	  		leftContent +='</div>';

		container.html(leftContent);

  	 	$( "#dashAcademics_content" ).slideDown();

  	 	abTableNoPaging('dashSeClassroomTable');

  	 	AcActionClassClicked();

      })
      .fail(function() {
      })
      .always(function() {
      });

	}

	function loadDashboardAcTeacherClassroom(id , teachername , staffPhotoId )
	{
		var container   = $( "#dashAcademics_content" );

		var content 	= '';

		container.hide();
		container.empty();

		var url = base_url+"api/Ac/subjects/Teacher/"+id;

		var EditSubject_SubjectTypes = null;

		  $.getJSON( url, function(data) {

		  	var TeacherSubjects = data.TeacherSubjects;

		  	content += '<div class="row"><div class="col-sm-12 text-center lead"><i class="fa fa-building-o"></i>&nbsp;&nbsp;Subjects taught by <strong> <i>'+teachername+'</i></strong></div></div>';
		  	content += '<div class="row">';
		  		content += "<div class='col-sm-3'><div class=' top-buffer'><center><div class='row'><img src='images/schoolStaff/200/"+id+"/"+staffPhotoId+"' class='img-circle thumbnail' alt=''></div><div class='row'><div ><strong>"+teachername+"</strong></div></div><div class='row'><div ><i>Teacher</i></div></div></center></div></div>";
		    	content += '<div class="col-sm-7 col-sm-offset-1">';
			    content += '<table class="table table-striped abDataTable " id="dashAcTeacherSubjectTable" ><thead><tr><th>Classroom</th><th>Subject</th><th>Maximum</th><th>Subject Type</th><th>Non-taking<br/>Students</th><th>Delete</th></thead><tbody>';

			    $.each( TeacherSubjects , function(i, item) {

			        content +="<tr id='"+item.id+"' subjectname='"+item.subjectName+"' classname='"+item.classname+"' subjectname='"+item.subjectName+"' teacher_id='"+id+"' >";
			          content +='<td>'+item.classname+'</td>';
			          content +='<td>'+item.subjectName+'</td>';
			          content +='<td  class="text-center"><span class="xeditableTeacherSubjectSubjectMaximum" data-type="text" data-pk="'+item.id+'" data-title="Enter Subject Maximum" data-url="'+base_url+'api/Ac/classrooms/updateSubject?UpdateType=1" >'+item.subjectMaximum+'</span></td>'; 
			          content +='<td><span class="xeditableTeacherSubjectSubjectType" data-type="select2" data-pk="'+item.id+'" data-title="Select Subject Type" data-url="'+base_url+'api/Ac/classrooms/updateSubject?UpdateType=2">'+item.subjectType+'</span></td>';
					  content +='<td class="text-center AcTeacherubjectExclude"><a href="#">'+item.numberOfExcluded+' <i class="fa fa-list text-green"></i></a></td>';
				      content +='<td class="text-center AcTeacherSubjectDelete"><a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a></td>';
			        content +="</tr>";

			    });

			    content +='</tbody></table></div>';
	    	content +='</div>';

	    	 //Exeditable 
			    EditSubject_SubjectTypes 	= data.SubjectTypes ;

			   


		  }).done(function() {

			// var combinedContent = '<div class="col-sm-12 ">'+leftContainer+rightContainer+'</div>'; 

			container.html(content);
	  	 	container.slideDown();

	  	 	abTableNoPaging("dashAcTeacherSubjectTable");

	  	 	AcTeachSubjectActionClicked();

	  	 	 	$.fn.editable.defaults.mode = 'inline';
		  		$.fn.editable.defaults.ajaxOptions = {type: "POST" };

				$('.xeditableTeacherSubjectSubjectMaximum').editable({
					emptytext: 'Click to add',
			    	success: function(response, newValue) {
				        if(response.status == 'error') return response.msg;
				    }
				});

				$('.xeditableTeacherSubjectSubjectType').editable({
			        source: EditSubject_SubjectTypes ,
			        select2: {
			           width:150,
			           multiple: false
			        },
			        success: function(response, newValue) {
				        if(response.status == 'error') return response.msg;
				    }
			    });

	      })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}

	function AcTeachSubjectActionClicked()
	{
		$( "table#dashAcTeacherSubjectTable" ).delegate( "td.AcTeacherubjectExclude", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var id 			= $(this).closest('tr').attr('id');
		  	var classname   = $(this).closest('tr').attr('classname');
		  	var subjectname = $(this).closest('tr').attr('subjectname');
		  	var teacher_id  = $(this).closest('tr').attr('teacher_id');

		  	loadTeacherSubjectExclude( $(this), id, classname, subjectname);  

		});

		$( "table#dashAcTeacherSubjectTable" ).delegate( "td.AcTeacherSubjectDelete", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

    		var id 			= $(this).closest('tr').attr('id');
		  	var subjectname = $(this).closest('tr').attr('subjectname');
		  	var teacher_id  = $(this).closest('tr').attr('teacher_id');
		  	
		  	GrDeleteClassSubject( id , $(this) );

		});
	}


	function loadTeacherSubjectExclude( td_subject, id, Classname , SubjectName )
	{	

		var classroomSubjectExcludeForm 	 = $("#ClassroomSubjectExcludeForm");
		var classroomSubjectExcludeLoading 	 = $("#ClassroomSubjectExcludeLoading");

		classroomSubjectExcludeLoading.hide();
		classroomSubjectExcludeForm.show();
		
		var Content  = "";
		var ExcludeStudentSubjectList = $('#ExcludeStudentSubjectList');
		var SubjectExcludeStudentTitle= $('#SubjectExcludeStudentTitle');

		var dashboardAcSubjectExcludeStudentSubjectID = $("#dashboardAcSubjectExcludeStudentSubjectID");
		dashboardAcSubjectExcludeStudentSubjectID.val(id);

		SubjectExcludeStudentTitle.html(": "+Classname+": <b>"+SubjectName+"</b>");

		var url = base_url+"api/Ac/subjectsExclude/"+id+"/edit";

		var classIndex = 0 ;

	  	$.getJSON( url, function(data) {

	  	var ClassroomSubjectExclude = data.AnnualClassroomSubjectExclude ;
	  	var StudentClass 			= data.StudentClass ;

	  	$('#Model_AcClassroomSubjectExcludeStudent').modal('show');

	    Content   = "";
	  	Content  += '<table class="table table-hover ExcludeStudentSubjectListTable" id="ExcludeStudentSubjectListTable" ><thead><tr><th class="text-center">Name</th><th class="text-center">Remove</th></tr></thead><tbody>';

	   	$.each( ClassroomSubjectExclude , function(i, item) {

	        Content +="<tr id='"+item.id+"' studregnber='"+item.studRegNber+"' >";
	          Content +="<td>"+item.studentNames+"</td>";
	          Content +='<td class="text-center AcExcludeStudentClassroomSubjectDelete"><a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a></td>';
	        Content +="</tr>";
		
			classIndex++;

	    });

	  	Content +='</tbody></table>';
	  	
	  	var subjectOptions ='<option ></option>';

	  	$.each( StudentClass , function(i, item) {

	        subjectOptions +='<option value="'+item.studRegNber+'" >' +item.studentNames+ ' ( ' +item.studRegNber+ ' )</option>';
	    });

	  	var dashboardAcSelectExcludeStudentStudent = $('#dashboardAcSelectExcludeStudentStudent');
	  	dashboardAcSelectExcludeStudentStudent.html( subjectOptions );
	  	dashboardAcSelectExcludeStudentStudent.select2();


	  }).done(function() {	

	  	ExcludeStudentSubjectList.html( Content );
	  	abTablePagingTable('ExcludeStudentSubjectListTable');

	  	$( "table#ExcludeStudentSubjectListTable" ).delegate( "td.AcExcludeStudentClassroomSubjectDelete", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var studRegNber  = $(this).closest('tr').attr('studRegNber');
		  	AcDeleteExcludeStudentSubject( td_subject, id , $(this) , studRegNber );

		});

		submitExcludeStudent( td_subject );

      }).fail(function() {
      })
      .always(function() {
      });

	}

	function dashSeStudentSelected()
	{

		$( "table#dashSeStudentTable" ).delegate( "td", "click", function() {

		  	var studregnber = $(this).closest('tr').attr('studregnber');

		  	get_profile_student(studregnber);

		});

	}

	///
	function DashAcSelectAllClassroom()
	{

		loadAcademicsClassrooms();
	}

	function AcNewClassTeacher(id , classname )
	{
		$('#dashAcNewClassteacherClass').val(id);
		$('#dashAcNewClassteacherClassName').val(classname);
		
	}

	function AcActionClassClicked()
	{	

		$( "table#dashSeClassroomTable" ).delegate( "td.AcClassroomManageList", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var id 			= $(this).closest('tr').attr('id');
		  	var classname   = $(this).closest('tr').attr('classname');

		  	loadClassroom_ManageStudentList( id , classname, $(this) , 1 );

		});

		$( "table#dashSeClassroomTable" ).delegate( "td.AcClassroomImportStudent", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var id 			= $(this).closest('tr').attr('id');
		  	var classname   = $(this).closest('tr').attr('classname');

		  	loadClassroom_ManageStudentList( id , classname, $(this) , 2 );

		});

		$( "table#dashSeClassroomTable" ).delegate( "td.AcClassroomManageSubject", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var id 			= $(this).closest('tr').attr('id');
		  	var classname   = $(this).closest('tr').attr('classname');

		  	loadClassroom_ManageSubject( id, classname, $(this) );

		});

		$( "table#dashSeClassroomTable" ).delegate( "td.AcClassroomImportSubject", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var id 			= $(this).closest('tr').attr('id');
		  	var classname   = $(this).closest('tr').attr('classname');

		  	loadClassroom_ImportSubject(id , classname, $(this) );

		});

		$( "table#dashSeClassroomTable" ).delegate( "td.AcClassroomEdit", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var id 			= $(this).closest('tr').attr('id');
		  	var classname   = $(this).closest('tr').attr('classname');

		  	loadClassroomEdit(id , classname, $(this) );

		});

		$( "table#dashSeClassroomTable" ).delegate( "td.AcClassroomDelete", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var id 			= $(this).closest('tr').attr('id');
		  	var classname   = $(this).closest('tr').attr('classname');

		  	loadClassroomDelete(id , classname, $(this) );

		});


	}

	function loadClassroom_ManageStudentList( id , className, td , defaultSelect )
	{	

		//Intialize 
			var AcClassEnrollmentStudentContainer 			=  $("#AcClassEnrollmentStudentContainer");
			var AcClassEnrollmentLastYearClassContainer 	=  $("#AcClassEnrollmentLastYearClassContainer");
			var AcClassEnrollmentLastYearStudentsContainer  =  $("#AcClassEnrollmentLastYearStudentsContainer");
			var AcClassEnrollmentLoadingContainer 			=  $("#AcClassEnrollmentLoadingContainer");

			AcClassEnrollmentStudentContainer.slideDown();
			AcClassEnrollmentLastYearClassContainer.hide();
			AcClassEnrollmentLastYearStudentsContainer.hide();
			AcClassEnrollmentLoadingContainer.hide();

			$("#AcClassEnrollmentSelectStudent").select2('data', null);
	    	$("#AcClassEnrollmentLastYearClass").select2('data', null);

	    //
		var AcClassEnrollmentClassID = $("#AcClassEnrollmentClassID");
		AcClassEnrollmentClassID.val(id);

		$('#modal_AcClassEnrollment').modal('show');

		var AcClassEnrollmentTitle 			  = $('#AcClassEnrollmentTitle');
		AcClassEnrollmentTitle.html("<b>"+className+"</b> Enrollment");

		var AcClassEnrollmentSubmit           = $('#AcClassEnrollmentSubmit');
		AcClassEnrollmentSubmit.html(className);

		var AcClassEnrollmentLastYearStudents = $('#AcClassEnrollmentEnrolledStudents');
		AcClassEnrollmentLastYearStudents.empty();

		var AcClassEnrollmentLastYearClass    = $('#AcClassEnrollmentLastYearClass');
		AcClassEnrollmentLastYearClass.empty();

		var AcClassEnrollmentEnrolledStudents = $('#AcClassEnrollmentEnrolledStudents');
		AcClassEnrollmentEnrolledStudents.html("Loading students...");

		var url = base_url+"api/Ac/classrooms/"+id+"?ClassInfoType=1";
		
		  $.getJSON( url, function(data) {

		  	var classrooms 			= data.LastYearClassrooms ;
		  	var classroomsSize 		= classrooms.length ;

		  		//add options
					var ClassOptions 	= '<option></option>';

					var classidentifier = "";
					var isTheFirsRecord = true;

					for ( var i=0; i< classroomsSize ; i++ ) {

						if ( classrooms[i].classidentifier == classidentifier ){
						    ClassOptions += '<option value="'+classrooms[i].id+'" data-yearname="'+classrooms[i].YearName+'" data-level="'+classrooms[i].levelID+'" >'+classrooms[i].name+'</option>';
						
						}else{

						    classidentifier = classrooms[i].classidentifier;

					    	if ( isTheFirsRecord ){

						    	isTheFirsRecord = false; 

					    	}else{
						    	 ClassOptions += '</optgroup>';
						    }

					    	ClassOptions += '<optgroup label="'+classrooms[i].YearName+'">';
					   		ClassOptions += '<option value="'+classrooms[i].id+'" data-yearname="'+classrooms[i].YearName+'" data-level="'+classrooms[i].levelID+'" >'+classrooms[i].name+'</option>';
					
						}
							    
					}

				AcClassEnrollmentLastYearClass.html(ClassOptions);

			//Enrolled Students 
				var ClassStudentsYearEnrollment = data.ClassStudentsYearEnrollment;

				Content   = "";
			  	Content  += '<table class="table table-hover" id="AcClassEnrollmentStudentListTable" ><thead><tr><th class="text-center">#</th><th class="text-center">Name</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';

			  	var studentListIndex = 1;

			   	$.each( ClassStudentsYearEnrollment , function(i, item) {

			        Content +="<tr id='"+item.studRegNber+"' >";
			          Content +="<td>"+studentListIndex+"</td>";
			          Content +="<td>"+item.studentNames+"</td>";
			          Content +="<td class='text-center'>"+item.studRegNber+"</td>";
			        Content +="</tr>";

			        studentListIndex++;

			    });

			  	Content +='</tbody></table>';

			  	AcClassEnrollmentEnrolledStudents.html(Content);

			  	abTableNoPaging("AcClassEnrollmentStudentListTable");

		  }).done(function() { 
		  })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}


	function loadClassroom_ManageSubject( id, className, td )
	{

		var AcClassSubjectThisYearClassContainer 		 = $("#AcClassSubjectThisYearClassContainer");
		var AcClassSubjectLastYearClassContainer 		 = $("#AcClassSubjectLastYearClassContainer");
		var AcClassSubjectsSelectedClassSubjectsStudents = $("#AcClassSubjectsSelectedClassSubjectsStudents");
		var AcClassSubjectsSelectedClassSubjects 		 = $("#AcClassSubjectsSelectedClassSubjects");

		var AcClassSubjectLoadingContainer 		 		 = $("#AcClassSubjectLoadingContainer");

		AcClassSubjectLastYearClassContainer.hide();
    	AcClassSubjectThisYearClassContainer.slideDown();
    	AcClassSubjectsSelectedClassSubjectsStudents.empty();
    	AcClassSubjectsSelectedClassSubjects.empty();

    	AcClassSubjectLoadingContainer.hide();
    	
	    $("#AcClassSubjectThisYearClass").select2('data', null);
	    $("#AcClassSubjectLastYearClass").select2('data', null);

	    $("#AcClassSubjectSubmit").html(className);

		$('#modal_AcClassSubject').modal('show');

		var AcClassSubjectTitle 	= $('#AcClassSubjectTitle');
		AcClassSubjectTitle.html("<b>"+className+"</b> Subjects");

		var AcClassSubjectAddedSubjects = $('#AcClassSubjectAddedSubjects');
		AcClassSubjectAddedSubjects.html("Loading subjects...");

		$('#AcClassSubjectClassID').val(id);

		var EditSubject_SubjectTypes = null;
		var EditSubject_Teachers 	 = null;

		var url = base_url+"api/Ac/classrooms/"+id+"?ClassInfoType=2";
		
		  $.getJSON( url, function(data) {

		  	var thisYear_classrooms 	= data.ThisYearClassrooms ;
		  	var thisYear_classroomsSize = thisYear_classrooms.length ;

		  		//add options
					var ClassOptions 	= '<option></option>';

					var classidentifier = "";
					var isTheFirsRecord = true;

					for ( var i=0; i< thisYear_classroomsSize ; i++ ) {

						if ( thisYear_classrooms[i].classidentifier == classidentifier ){
						    ClassOptions += '<option value="'+thisYear_classrooms[i].id+'" data-yearname="'+thisYear_classrooms[i].YearName+'" data-level="'+thisYear_classrooms[i].levelID+'" >'+thisYear_classrooms[i].name+'</option>';
						
						}else{

						    classidentifier = thisYear_classrooms[i].classidentifier;

					    	if ( isTheFirsRecord ){

						    	isTheFirsRecord = false; 

					    	}else{
						    	 ClassOptions += '</optgroup>';
						    }

					    	ClassOptions += '<optgroup label="'+thisYear_classrooms[i].YearName+'">';
					   		ClassOptions += '<option value="'+thisYear_classrooms[i].id+'" data-yearname="'+thisYear_classrooms[i].YearName+'" data-level="'+thisYear_classrooms[i].levelID+'" >'+thisYear_classrooms[i].name+'</option>';
					
						}
							    
					}

				$('#AcClassSubjectThisYearClass').html(ClassOptions);

			//
				var lastYear_classrooms 	= data.LastYearClassrooms ;
			  	var lastYear_classroomsSize = lastYear_classrooms.length ;

			  		//add options
						var ClassOptions 	= '<option></option>';

						var classidentifier = "";
						var isTheFirsRecord = true;

						for ( var i=0; i< lastYear_classroomsSize ; i++ ) {

							if ( lastYear_classrooms[i].classidentifier == classidentifier ){
							    ClassOptions += '<option value="'+lastYear_classrooms[i].id+'" data-yearname="'+lastYear_classrooms[i].YearName+'" data-level="'+lastYear_classrooms[i].levelID+'" >'+lastYear_classrooms[i].name+'</option>';
							
							}else{

							    classidentifier = lastYear_classrooms[i].classidentifier;

						    	if ( isTheFirsRecord ){

							    	isTheFirsRecord = false; 

						    	}else{
							    	 ClassOptions += '</optgroup>';
							    }

						    	ClassOptions += '<optgroup label="'+lastYear_classrooms[i].YearName+'">';
						   		ClassOptions += '<option value="'+lastYear_classrooms[i].id+'" data-yearname="'+lastYear_classrooms[i].YearName+'" data-level="'+lastYear_classrooms[i].levelID+'" >'+lastYear_classrooms[i].name+'</option>';
						
							}
								    
						}

				$('#AcClassSubjectLastYearClass').html(ClassOptions);

			//Subjects 	
				var ClassroomsSubjects 	  = data.ClassSubjects ;

				var content = '';
				content += '<div class="col-sm-10 col-sm-offset-1"><table class="table table-hover" id="dashSeSubjectTable" ><thead><tr><th>Name</th><th>Max</th><th>Type</th><th>Teacher</th><th>Delete</th></thead><tbody>';
			     $.each(ClassroomsSubjects, function(i, item) {

			        content +="<tr id='"+item.id+"' subjectname='"+item.SubjectName+"' classid='"+id+"' >";
			          content +='<td>'+item.SubjectName+'</td>';
			          content +='<td class="text-center"><span class="xeditableClassSubjectSubjectMaximum" data-type="text" data-pk="'+item.id+'" data-title="Enter Subject Maximum" data-url="'+base_url+'api/Ac/classrooms/updateSubject?UpdateType=1" >'+item.subjectMaximum+'</span></td>'; 
			          content +='<td><span class="xeditableClassSubjectSubjectType" data-type="select2" data-pk="'+item.id+'" data-title="Select Subject Type" data-url="'+base_url+'api/Ac/classrooms/updateSubject?UpdateType=2">'+item.SubjectType+'</span></td>';
					  content +='<td><span class="xeditableClassSubjectSubjectTeacher" data-type="select2" data-pk="'+item.id+'" data-title="Select Teacher" data-url="'+base_url+'api/Ac/classrooms/updateSubject?UpdateType=3">'+item.teachername+'</span></td>';
					  content +='<td class="text-center AcClassroomSubjectDelete"><a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a></td>';
					content +="</tr>";

			    });

			    content +='</tbody></table></div>';

			    $('#AcClassSubjectAddedSubjects').html(content);
			    abTableNoPaging("dashSeSubjectTable");
			    AcSubjectActionClicked();

			    //Exeditable 
			    	EditSubject_SubjectTypes 	= data.SubjectTypes ;
			    	EditSubject_Teachers 		= data.Teachers ;


		  }).done(function() {

		  		$.fn.editable.defaults.mode = 'inline';
		  		$.fn.editable.defaults.ajaxOptions = {type: "POST" };

				$('.xeditableClassSubjectSubjectMaximum').editable({
					emptytext: 'Click to add',
			    	success: function(response, newValue) {
				        if(response.status == 'error') return response.msg;
				    }
				});

				$('.xeditableClassSubjectSubjectType').editable({
			        source: EditSubject_SubjectTypes ,
			        select2: {
			           width:150,
			           multiple: false
			        },
			        success: function(response, newValue) {
				        if(response.status == 'error') return response.msg;
				    }
			    });

				$('.xeditableClassSubjectSubjectTeacher').editable({
			        source: EditSubject_Teachers ,
			        select2: {
			           width:150,
			           multiple: false
			        },
			        success: function(response, newValue) {
				        if(response.status == 'error') return response.msg;
				    }
			    });

		  })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}

	function onAcClassSubjectThisYearClassChange()
	{
		var AcClassSubjectThisYearClass  = $("#AcClassSubjectThisYearClass");
		var selected  					 = AcClassSubjectThisYearClass.select2('val');

		onAcClassSubjectLoadClassSubjects(selected);
		
	}

	function onAcClassSubjectLastYearClassChange()
	{
		var AcClassSubjectLastYearClass  = $("#AcClassSubjectLastYearClass");
		var selected  					 = AcClassSubjectLastYearClass.select2('val');

		onAcClassSubjectLoadClassSubjects(selected);
		
	}

	var rows_selected_ClassSubject = [];
	function onAcClassSubjectLoadClassSubjects( classid )
	{

		var AcClassSubjectsSelectedClassSubjectsStudents = $("#AcClassSubjectsSelectedClassSubjectsStudents");
		AcClassSubjectsSelectedClassSubjectsStudents.html('<img src="../packages/assets/plugins/img/loading.gif" > Loading subjects...');
		
		var AcClassSubjectsSelectedClassSubjectsContainer= $("#AcClassSubjectsSelectedClassSubjectsContainer");
		AcClassSubjectsSelectedClassSubjectsContainer.slideDown();

		  var url = base_url+"api/Ac/classrooms/"+classid+"?ClassInfoType=4";
		
		  $.getJSON( url, function(data) {

		  	var ClassSubjects = data.ClassSubjects;

		  	var content = '';
			content += '<div class="col-sm-12"><table class="table table-hover" id="dashSelectedClassSubjectsTable" ><thead><tr><th><input name="select_all" value="1" type="checkbox"> All</th><th>Name</th><th>Max</th><th>Teacher</th></thead><tbody>';
		    
		    $.each( ClassSubjects, function(i, item) {

		        content +="<tr id='"+item.id+"' subjectname='"+item.SubjectName+"' >";
		          content +="<td ></td>";
		          content +='<td>'+item.SubjectName+'</td>';
		          content +='<td  class="text-center">'+item.subjectMaximum+'</td>';
				  content +="<td>"+item.teachername+"</td>";
		        content +="</tr>";

		    });

		    content +='</tbody></table></div>';

			AcClassSubjectsSelectedClassSubjectsStudents.html(content);

			//
				function updateDataTableSelectAllCtrl(table){
				   
				   var $table             = table.table().node();
				   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
				   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
				   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

				   // If none of the checkboxes are checked
				   if($chkbox_checked.length === 0){
				      chkbox_select_all.checked = false;
				      if('indeterminate' in chkbox_select_all){
				         chkbox_select_all.indeterminate = false;
				      }

				   // If all of the checkboxes are checked
				   } else if ($chkbox_checked.length === $chkbox_all.length){
				      chkbox_select_all.checked = true;
				      if('indeterminate' in chkbox_select_all){
				         chkbox_select_all.indeterminate = false;
				      }

				   // If some of the checkboxes are checked
				   } else {
				      chkbox_select_all.checked = true;
				      if('indeterminate' in chkbox_select_all){
				         chkbox_select_all.indeterminate = true;
				      }

				   }

				}

		   // Array holding selected row IDs
		  	   rows_selected_ClassSubject = [];
			   var table = $('#dashSelectedClassSubjectsTable').DataTable({
			   		paging: false,
			        'columnDefs': [{
			        'targets': 0,
			         'searchable': false,
			         'orderable': false,
			         'width': '1%',
			         'className': 'dt-body-center',
			         'render': function (data, type, full, meta){
			             return '<input type="checkbox">';
			         }
			      }],
			      'order': [[1, 'asc']],
			      'rowCallback': function(row, data, dataIndex){
			         // Get row ID
			         var rowId = data[3];

			         // If row ID is in the list of selected row IDs
			         if($.inArray(rowId, rows_selected_ClassSubject) !== -1){
			            $(row).find('input[type="checkbox"]').prop('checked', true);
			            $(row).addClass('selected');
			         }
			      }
			   });

		   // Handle click on checkbox
			   $('#dashSelectedClassSubjectsTable tbody').on('click', 'input[type="checkbox"]', function(e){

			      var $row = $(this).closest('tr');
			      // Get row data
			      var data = table.row($row).data();
			      // Get row ID
			      var rowId = $(this).closest('tr').attr('id');

			      // Determine whether row ID is in the list of selected row IDs 
			      var index = $.inArray(rowId, rows_selected_ClassSubject);

			      // If checkbox is checked and row ID is not in list of selected row IDs
			      if(this.checked && index === -1){
			         rows_selected_ClassSubject.push(rowId);

			      // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
			      } else if (!this.checked && index !== -1){
			         rows_selected_ClassSubject.splice(index, 1);
			      }

			      if(this.checked){
			         $row.addClass('selected');

			      } else {
			         $row.removeClass('selected');
			      }

			      // Update state of "Select all" control
			      updateDataTableSelectAllCtrl(table);

			      // Prevent click event from propagating to parent
			      e.stopPropagation();
			   });

		   // Handle click on table cells with checkboxes
			   $('#dashSelectedClassSubjectsTable').on('click', 'tbody td, thead th:first-child', function(e){
			      $(this).parent().find('input[type="checkbox"]').trigger('click');
			   });

		   // Handle click on "Select all" control
			   $('thead input[name="select_all"]', table.table().container()).on('click', function(e){
			      if(this.checked){
			         $('#dashSelectedClassSubjectsTable tbody input[type="checkbox"]:not(:checked)').trigger('click');
			      } else {
			         $('#dashSelectedClassSubjectsTable tbody input[type="checkbox"]:checked').trigger('click');
			      }

			    // Prevent click event from propagating to parent
			      e.stopPropagation();

			   });

		   // Handle table draw event
		   	  table.on('draw', function(){
		      	// Update state of "Select all" control
		      		updateDataTableSelectAllCtrl(table);
		  	  });

		  }).done(function() { 
		  })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}

	function loadClassroom_ImportSubject( id , className, td )
	{
		td.html('<img src="../packages/assets/plugins/img/loading.gif" alt="Loading..." ></div>');

		var url = base_url+"api/Ac/classrooms/"+id+"?ClassInfoType=2";
		
		  $.getJSON( url, function(data) {

		  	$('#modal_generalPurpose').modal('show');
		  	$('#modal_generalPurpose_title').html("Manage Class Subjects");
		  	$('#modal_generalPurpose_subTitle_left').html("Add subjects using last year class");
		  	$('#modal_generalPurpose_subTitle_right').html("Added Subjects");

		  }).done(function() { })
	      .fail(function() {
	      })
	      .always(function() {
	      });
	}

	function loadClassroomEdit(id , className, td )
	{

		td.html('<img src="../packages/assets/plugins/img/loading.gif" alt="Loading..." ></div>');

		var url = base_url+"api/Ac/classrooms/"+id+"/edit";

		  $.getJSON( url, function(data) {

		  	var selectedClass 	 		= data.AnnualClassroom;
		  	var SchoolLevel 			= data.SchoolLevel;

		  	var class_id 	  	 		= selectedClass.id;
		  	var class_scLevelID 		= SchoolLevel.scLevelID;
		  	
		  	var class_roomID 			= selectedClass.roomID;
		  	var class_clTcherID 		= selectedClass.clTcherID;
		  	var class_dlyCLSAttRcderID	= selectedClass.dlyCLSAttRcderID;
		  	var class_aLevelOptionID 	= selectedClass.aLevelOptionID;
		  	var class_isALevel 			= Number(selectedClass.isALevel);
		  	var class_year 				= selectedClass.year;
		  	var class_name 				= selectedClass.name;
		  	var class_capacity 			= selectedClass.capacity;

		  	//show model add new class 
		  	$('#Model_NewClassroomTitle').html("Update Class");
		  	$('#newClassActionType').val("1");
		  	$('#newClassSubmitBtn').text("Update");

		  	$('#Model_NewClassroom').modal('show');

		  	$('#newClassClassid').val(class_id);
		  	$('#selectAcademicsNewClassroom').select2().val(class_scLevelID).trigger('change');
		  	$('#selectAcademicsNewClassroomRoom').select2().val(class_roomID).trigger('change');
		  	$('#selectNewClassroomTeacher').select2().val(class_clTcherID).trigger('change');
			$('#selectNewClassroomRecorder').select2().val(class_dlyCLSAttRcderID).trigger('change');
			$('#selectAcademicsNewClassroomYear').select2().val(class_year).trigger('change');
			
			if(class_isALevel == 0)
			{
				$('#selectAlphabet').select2().val(class_name).trigger('change');

			}else if(class_isALevel == 1) {

				$('#selectAcademicsNewClassALevel').select2().val(class_aLevelOptionID).trigger('change');

			}
			
			$('#newClassStream').hide();
			$('#newClassStreamName').show();

			$('#selectStreamName').val(class_name);

			$('#newClassCapacity').val(class_capacity);

		  }).done(function() {

		  	td.html('<a href="#"><i class="fa fa-pencil"></i></a>');

	      })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}

	function loadClassroomDelete(id , className, td )
	{

		var url     = base_url+"api/Ac/classrooms/"+id;
	    td.html("Deleting...");

	      $.ajax({
	            url: url,
	            type: 'DELETE',
	            success: function(data) {
	           
	               if ( data.success )
	               {
	                    $.gritter.add({
	                      title: 'Success',
	                      text: 'Class successfully deleted.',
	                      class_name: 'success gritter-center',
	                      time: ''
	                    });

	                    DashAcSelectAllClassroom();

	               }else{

	                 $.gritter.add({
	                    title: 'Failed',
	                    text: 'Failed to delete class, '+data.error ,
	                    class_name: 'danger gritter-center',
	                    time: ''
	                  });

	                 td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');
	             }
	        }

	    });

	}



	function AcSubjectActionClicked()
	{	
		$( "table#dashSeSubjectTable" ).delegate( "td.AcClassroomSubjectDelete", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var id 			= $(this).closest('tr').attr('id');
		  	var ClassID 	= $(this).closest('tr').attr('classid');

		  	GrDeleteClassSubject( id , $(this) );

		});
	}

	function GrDeleteClassSubject( classSubjectID , td )
	{

	    var url     = base_url+"api/Ac/subjects/"+classSubjectID;
	    td.html("Deleting...");

	      $.ajax({
	            url: url,
	            type: 'DELETE',
	            success: function(data) {
	           
	               if ( data.success )
	               {
	                    $.gritter.add({
	                      title: 'Success',
	                      text: 'Subject removed.',
	                      class_name: 'success gritter-center',
	                      time: ''
	                    });

	                    td.closest('tr').find('td').fadeOut('fast', 
				        function(here){ 
				            $(here).parents('tr:first').remove();                    
				        });   

	               }else{

	                 $.gritter.add({
	                    title: 'Failed',
	                    text: 'Failed to remove subject , '+ data.error,
	                    class_name: 'danger gritter-center',
	                    time: ''
	                  });

	                 td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');
	             }
	        }

	    });

	}

	function loadSubjectExclude( td_subject, id, SubjectName )
	{	

		var Content  = "";
		var ExcludeStudentSubjectList = $('#ExcludeStudentSubjectList');
		var SubjectExcludeStudentTitle= $('#SubjectExcludeStudentTitle');

		var dashboardAcSubjectExcludeStudentSubjectID = $("#dashboardAcSubjectExcludeStudentSubjectID");
		dashboardAcSubjectExcludeStudentSubjectID.val(id);

		SubjectExcludeStudentTitle.html(": <b>"+SubjectName+"</b>");

		var url = base_url+"api/Ac/subjectsExclude/"+id+"/edit";

		var classIndex = 0 ;

	  	$.getJSON( url, function(data) {

	  	var ClassroomSubjectExclude = data.AnnualClassroomSubjectExclude ;
	  	var StudentClass 			= data.StudentClass ;

	  	$('#Model_AcClassroomSubjectExcludeStudent').modal('show');

	    Content   = "";
	  	Content  += '<table class="table table-hover ExcludeStudentSubjectListTable" id="ExcludeStudentSubjectListTable" ><thead><tr><th class="text-center">Name</th><th class="text-center">Remove</th></tr></thead><tbody>';

	   	$.each( ClassroomSubjectExclude , function(i, item) {

	        Content +="<tr id='"+item.id+"' studregnber='"+item.studRegNber+"' >";
	          Content +="<td>"+item.studentNames+"</td>";
	          Content +='<td class="text-center AcExcludeStudentClassroomSubjectDelete"><a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a></td>';
	        Content +="</tr>";
		
			classIndex++;

	    });

	  	Content +='</tbody></table>';
	  	
	  	var subjectOptions ='<option ></option>';

	  	$.each( StudentClass , function(i, item) {

	        subjectOptions +='<option value="'+item.studRegNber+'" >' +item.studentNames+ ' ( ' +item.studRegNber+ ' )</option>';
	    });

	  	var dashboardAcSelectExcludeStudentStudent = $('#dashboardAcSelectExcludeStudentStudent');
	  	dashboardAcSelectExcludeStudentStudent.html( subjectOptions );
	  	dashboardAcSelectExcludeStudentStudent.select2();


	  }).done(function() {	

	  	ExcludeStudentSubjectList.html( Content );
	  	abTablePagingTable('ExcludeStudentSubjectListTable');

	  	$( "table#ExcludeStudentSubjectListTable" ).delegate( "td.AcExcludeStudentClassroomSubjectDelete", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var studRegNber  = $(this).closest('tr').attr('studRegNber');
		  	AcDeleteExcludeStudentSubject( td_subject, id , $(this) , studRegNber );

		});

		submitExcludeStudent( td_subject );

      }).fail(function() {
      })
      .always(function() {
      });

	}

	function loadSubjectEdit(id,subjectname)
	{

		  var selectSubjectTypeID = $('#selectSubjectTypeID');

		  var url = base_url+"api/Ac/subjects/"+id+"/edit";

		  $.getJSON( url, function(data) {

		  	var selectedClassroomSubject= data.AnnualClassroomSubject;
		  	var SubjectType 			= data.SubjectType ;

		  	var subject_ID 				= selectedClassroomSubject.id;
		  	var subject_teacherID   	= selectedClassroomSubject.teacherID;
		  	var subject_isOnReport  	= selectedClassroomSubject.isOnReport;
		  	var subject_isCalculated  	= selectedClassroomSubject.isCalculated;
		  	var subject_subjectMaximum  = selectedClassroomSubject.subjectMaximum;
		  	var subjectTypeID 			= selectedClassroomSubject.subjectTypeID;
		  	
		  	$('#subjectSubjectIDContainer').show();

		  	var subjectTypeOptions ='<option ></option>';

		  	$.each( SubjectType , function(i, item) {

		        subjectTypeOptions +='<option value="'+item.id+'" >' +item.name+ ' </option>';
		    });

			selectSubjectTypeID.html( subjectTypeOptions );
			selectSubjectTypeID.select2().val(subjectTypeID).trigger('change');

		  	//show model add new class 
		  	$('#Model_NewSubjectTitle').html("Update Subject: <b>"+subjectname+"</b>");
		  	$('#newSubjectActionType').val("1");
		  	$('#newSubjectSubmitBtn').text("Update");

		  	$('#Model_SubjectToClassroom').modal('show');

		  	$('#newSubjectSubjectid').val(subject_ID);
		  	$('#newSubjectMaximum').val(subject_subjectMaximum);
		    $('#selectNewSubjectTeacher').select2().val(subject_teacherID).trigger('change');
		    $("input[name=isOnReport][value=" + subject_isOnReport + "]").prop('checked', true);
		    $("input[name=isCalculated][value=" + subject_isCalculated + "]").prop('checked', true);

		 	$('#newSubjectClassContainer').hide();
		 	$('#subjectSubject').hide();

		  }).done(function() {

	      }).fail(function() {
	      })
	      .always(function() {
	      });

	}

	function submitExcludeStudent( td_subject )
	{
		var classroomSubjectExcludeForm 	 = $("#ClassroomSubjectExcludeForm");
		var classroomSubjectExcludeLoading 	 = $("#ClassroomSubjectExcludeLoading");

		 var formAcClassroomSubjectExcludeStudent =  $('#formAcClassroomSubjectExcludeStudent');

		  formAcClassroomSubjectExcludeStudent.on('submit', function(e){

		  var excludeStudentSubjectID = $('#dashboardAcSubjectExcludeStudentSubjectID').val() ;

		  classroomSubjectExcludeForm.hide();
		  classroomSubjectExcludeLoading.show();

		  var ClassID   = $('#dashAcNewClassteacherClass').val();
		  var ClassName = $('#dashAcNewClassteacherClassName').val();
		  
		  var frm = formAcClassroomSubjectExcludeStudent;
		  var url = base_url+"api/Ac/subjectsExclude"; 
		  e.preventDefault();

		  $.post(url, 
		     frm.serialize(), 
	         function(data, status, xhr){

	              if ( data.success )
	              {   
	                $.gritter.add({
	                    title: 'Success',
	                    text: 'Students added.',
	                    class_name: 'success gritter-center',
	                    time: ''
	                });

	                $('#dashboardAcSelectExcludeStudentStudent').select2('data', null);

	                loadExcludeStudentSubjectListTable( td_subject, excludeStudentSubjectID );

	              } else {
	                  
	                  $.gritter.add({
	                    title: 'Failed',
	                    text: 'Something went wrong' ,
	                    class_name: 'warning gritter-center',
	                    time: ''
	                  });

	              }
	                 
	         },"json")
	          .done(function() { 

	          })
	          .fail(function() {

	              $.gritter.add({
	                title: 'Failed',
	                text: 'Failed to add student, Please Try Again',
	                class_name: 'danger gritter-center',
	                time: ''
	              });
	          })
	          .always(function() {
	            
	            classroomSubjectExcludeForm.show();
		  		classroomSubjectExcludeLoading.hide();

	          });
		
		});

	}

	function AcDeleteExcludeStudentSubject( td_subject, excludeStudentSubjectID , td , studRegNber ){

		var url     = base_url+"api/Ac/subjectsExclude/"+excludeStudentSubjectID+"?studRegNber="+studRegNber ;
	    td.html("Removing...");

	      $.ajax({
	            url: url,
	            type: 'DELETE',
	            success: function(data) {
	           
	               if ( data.success )
	               {
	                    $.gritter.add({
	                      title: 'Success',
	                      text: 'Student removed.',
	                      class_name: 'success gritter-center',
	                      time: ''
	                    });

	                    loadExcludeStudentSubjectListTable( td_subject, excludeStudentSubjectID );

	               }else{

	                 $.gritter.add({
	                    title: 'Failed',
	                    text: 'Failed to remove student , '+ data.error,
	                    class_name: 'danger gritter-center',
	                    time: ''
	                  });

	                 td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');
	             }
	        }

	    });

	}

	function loadExcludeStudentSubjectListTable( td_subject, subjectID )
	{	
		var Content  = "";
		var ExcludeStudentSubjectList = $('#ExcludeStudentSubjectList');
		
		var classIndex = 0 ;

		var url = base_url+"api/Ac/subjectsExclude/"+subjectID;

	  	$.getJSON( url, function(data) {

	  	var ClassroomSubjectExclude = data.AnnualClassroomSubjectExclude ;
	  	var StudentClass 			= data.StudentClass ;

	    Content      = "" ;
	  	Content 	+= '<table class="table table-hover ExcludeStudentSubjectListTable" id="ExcludeStudentSubjectListTable" ><thead><tr><th class="text-center">Name</th><th class="text-center">Remove</th></tr></thead><tbody>';

	   	$.each( ClassroomSubjectExclude, function(i, item) {

	        Content +="<tr id='"+item.id+"' studregnber='"+item.studRegNber+"' >";
	          Content +="<td>"+item.studentNames+"</td>";
	          Content +='<td class="text-center AcExcludeStudentClassroomSubjectDelete"><a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a></td>';
	        Content +="</tr>";
		
			classIndex++;

	    });

	  	Content +='</tbody></table>';

	  	var subjectOptions ='<option ></option>';

	  	$.each( StudentClass , function(i, item) {

	        subjectOptions +='<option value="'+item.studRegNber+'" >' +item.studentNames+ ' ( ' +item.studRegNber+ ' )</option>';
	    });

	  	var dashboardAcSelectExcludeStudentStudent = $('#dashboardAcSelectExcludeStudentStudent');
	  	dashboardAcSelectExcludeStudentStudent.html( subjectOptions );
	  	dashboardAcSelectExcludeStudentStudent.select2();

	  }).done(function() {	

	  	ExcludeStudentSubjectList.html( Content );
	  	abTablePagingTable('ExcludeStudentSubjectListTable');

	  	$( "table#ExcludeStudentSubjectListTable" ).delegate( "td.AcExcludeStudentClassroomSubjectDelete", "click", function(e) {

			e.preventDefault();
    		e.stopPropagation();

		  	var excludeStudentSubjectID = $(this).closest('tr').attr('id');
		  	var studRegNber 			= $(this).closest('tr').attr('studRegNber');

		  	AcDeleteExcludeStudentSubject( td_subject, subjectID , $(this) , studRegNber );

		});

		if ( td_subject != null ) {
	  		td_subject.html('<a href="#">'+classIndex+' <i class="fa fa-list text-green"></i></a>');
	  	}


      }).fail(function() {
      })
      .always(function() {
      });

	}



// function dashNewEvent()
// {
// 	$("#ab_btn_dashNewEvent").click(function() {

// 		$('#moduleDashHomeContainer > div').hide();
// 		$("#dashNew_content").load("attendance/new_event" ,function(){ $( "#dashContentainer_new").slideDown(); }).fadeIn(1000);
		
// 	});

// 	$("#dashboardAtSelectEvent").select2().bind('change', onAteEventSelected);

// }

// function onAteEventSelected()
// {
// 	var dashAtSelectedEvent     			 = $('#dashAtSelectedEvent');

// 	var eventCalendarEventShow     			 = $("#eventCalendarEventShow");
// 	var attendanceEventDescription 			 = $("#attendanceEventDescription");
// 	var attendanceEventDescriptionStatistics = $("#attendanceEventDescriptionStatistics");
// 	var attendanceEventDescriptionAbout 	 = $("#attendanceEventDescriptionAbout");

// 	var dashAtSelectSubject 				 = $("#dashAtSelectSubject");
// 	var dashAtSelectClassroom 				 = $("#dashAtSelectClassroom");

// 	var dashboardAtSelectSubject 			 = $("#dashboardAtSelectSubject");
// 	var dashboardAtselectClassroom 			 = $("#dashboardAtselectClassroom");

// 	var sel 								 = $("#dashboardAtSelectEvent");

// 	var audiencetype 		    			= sel.select2().find(":selected").data("audiencetype");
// 	var description 						= sel.select2().find(":selected").data("description");
// 	var selected 							= sel.select2('val');
// 	var eventName							= sel.select2('data').text;
	
// 	dashAtSelectedEvent.html("<span class='lead'><strong>"+eventName+"</strong></span>")

// 	eventCalendarEventShow.html('<div class="row"><img src="../packages/assets/img/loading.gif" alt="Saving..." height="35"></div>');


// 	attendanceEventDescription.hide();
// 	attendanceEventDescriptionStatistics.hide();
// 	attendanceEventDescriptionAbout.html("<p>"+description+"</p>");
// 	attendanceEventDescription.slideDown();

// 	dashAtSelectSubject.hide();
// 	dashAtSelectClassroom.hide();

// 	dashboardAtSelectSubject.select2("val", "");
// 	dashboardAtselectClassroom.select2("val", "");

// 	var url = base_url+"api/At/events/"+selected; 

// 	$.getJSON( url, { audiencetype : audiencetype , date : currentDateUnixRealMonth } ,function(data) {

// 		onAtLoadAttendanceRecordTable( audiencetype ,data  ,eventCalendarEventShow ,attendanceEventDescriptionStatistics );
    	
//     })
//     .done(function() {


//     })
//     .fail(function() {
//     })
//     .always(function() {
//     });
// }

// function AtSelectedStudentPeriod()
// {
// 	$("#atStudentPeriod").click(function() {

// 		var eventCalendarEventShow     			 = $("#eventCalendarEventShow");
// 		var attendanceEventDescription 			 = $("#attendanceEventDescription");

// 		eventCalendarEventShow.hide();
// 		attendanceEventDescription.hide();

// 		$("#dashAtSelectedEvent").html("<span class='lead'><strong>Period Student Attendance</strong></span>")

// 		$('#dashAtSelectSubject').slideDown();
// 		$('#attendanceEventAboutContainer').hide();
// 		$('#dashAtSelectClassroom').hide();

// 		$("#dashboardAtSelectEvent").select2("val", "");
// 		$("#dashboardAtSelectSubject").select2("val", "");
// 		$("#dashboardAtselectClassroom").select2("val", "");

// 	});
// }

// function AtSelectedStudentDay()
// {
// 	 $("#atStudentDay").click(function() {

// 	 	var eventCalendarEventShow     			 = $("#eventCalendarEventShow");
// 		var attendanceEventDescription 			 = $("#attendanceEventDescription");

// 		eventCalendarEventShow.hide();
// 		attendanceEventDescription.hide();

//   		$("#dashAtSelectedEvent").html("<span><strong>Day Student Attendance</strong></span>")
		
// 		$('#dashAtSelectSubject').hide();
// 		$('#attendanceEventAboutContainer').hide();
// 		$('#dashAtSelectClassroom').slideDown();

// 		$("#dashboardAtSelectEvent").select2("val", "");
// 		$("#dashboardAtSelectSubject").select2("val", "");
// 		$("#dashboardAtselectClassroom").select2("val", "");
		
//     });

// }

// function AtSelectedStaffDay()
// {
// 	$("#atStaffDay").click(function() {

// 	 	var eventCalendarEventShow     			 = $("#eventCalendarEventShow");
// 		var attendanceEventDescription 			 = $("#attendanceEventDescription");
// 		var attendanceEventDescriptionStatistics = $("#attendanceEventDescriptionStatistics");
// 		var attendanceEventDescriptionAbout 	 = $("#attendanceEventDescriptionAbout");

// 		eventCalendarEventShow.html('<div class="row"><img src="../packages/assets/img/loading.gif" alt="Saving..." height="35"></div>');
		
// 		attendanceEventDescriptionStatistics.hide();
// 		attendanceEventDescriptionAbout.html("<p>This is attendance of staff on <b>"+currentDateString+"</b></p>");
// 		attendanceEventDescription.hide();
// 		attendanceEventDescription.slideDown();

// 	 	$("#dashAtSelectedEvent").html("<span class='lead'><strong>Day Staff Attendance</strong></span>")
	
// 		$('#dashAtSelectSubject').hide();
// 		$('#dashAtSelectClassroom').hide();
	 	
// 	 	$("#dashboardAtSelectEvent").select2("val", "");
// 		$("#dashboardAtSelectSubject").select2("val", "");
// 		$("#dashboardAtselectClassroom").select2("val", "");

//        	var url = base_url+"api/At/Record_dailyStaff/"+currentDateUnixRealMonth; 

//        	$.getJSON( url, function(data) {

// 			onAtLoadAttendanceRecordTable( 0, data ,eventCalendarEventShow, attendanceEventDescriptionStatistics );
		    
// 	    })
// 	    .done(function() {

// 	    })
// 	    .fail(function() {
// 	    })
// 	    .always(function() {
// 	    });

//     });

// }

// function LoadAtSelectedStudentPeriod( selected , classname, subjectname )
// {
// 	var eventCalendarEventShow     			 = $("#eventCalendarEventShow");
// 	var attendanceEventDescription 			 = $("#attendanceEventDescription");
// 	var attendanceEventDescriptionStatistics = $("#attendanceEventDescriptionStatistics");
// 	var attendanceEventDescriptionAbout 	 = $("#attendanceEventDescriptionAbout");

// 	eventCalendarEventShow.html('<div class="row"><img src="../packages/assets/img/loading.gif" alt="Saving..." height="35"></div>');
	
// 	var moment_date_notation = ab_moment_plus(currentDateUnixRealMonth);

// 	attendanceEventDescriptionStatistics.hide();
// 	attendanceEventDescriptionAbout.html("<p>This is student attendance from <b>"+classname+"</b> in <b>"+subjectname+" </b> subject  on <b>"+currentDateString+" ("+moment_date_notation+")</b></p>");
// 	attendanceEventDescription.hide();
// 	attendanceEventDescription.slideDown();

// 	var url = base_url+"api/At/Record_period/"+selected; 

// 	$.getJSON( url, {date : currentDateUnixRealMonth } ,function(data) {

// 		onAtLoadAttendanceRecordTable(1, data  ,eventCalendarEventShow, attendanceEventDescriptionStatistics );
    
//     })
//     .done(function() {


//     })
//     .fail(function() {
//     })
//     .always(function() {
//     });

// }

// function LoadAtSelectedStudentDay( selected , className)
// {		
// 	var eventCalendarEventShow     			 = $("#eventCalendarEventShow");
// 	var attendanceEventDescription 			 = $("#attendanceEventDescription");
// 	var attendanceEventDescriptionStatistics = $("#attendanceEventDescriptionStatistics");
// 	var attendanceEventDescriptionAbout 	 = $("#attendanceEventDescriptionAbout");

// 	eventCalendarEventShow.html('<div class="row"><img src="../packages/assets/img/loading.gif" alt="Saving..." height="35"></div>');
	
// 	attendanceEventDescriptionStatistics.hide();
// 	attendanceEventDescriptionAbout.html("<p>This is attendance of student in <b>"+className+"</b> on <b>"+currentDateString+"</b></p>");
// 	attendanceEventDescription.hide();
// 	attendanceEventDescription.slideDown();

// 	var url = base_url+"api/At/Record_dailyClass/"+selected; 

// 	var eventID;

// 	$.getJSON( url, {date : currentDateUnixRealMonth } ,function(data) {

// 		onAtLoadAttendanceRecordTable( 1, data ,eventCalendarEventShow, attendanceEventDescriptionStatistics );
    
//     })
//     .done(function() {
//     })
//     .fail(function() {
//     })
//     .always(function() {
//     });


// }


// function  onAtRecordedEventSelected(eventrecordid, eventdate, audiencetype ,eventitle, description)
// {

// 	currentDateUnixTime 	= eventdate;
// 	currentDateString		= moment.unix(eventdate).format("DD/MM/YYYY");

// 	var dashAtSelectedDateEvent    	   = $("#dashAtSelectedDateEvent");
// 	var dashAtSelectedDateEventContent = "<span class='lead'>"+currentDateString+"</span>";
// 	dashAtSelectedDateEvent.html(dashAtSelectedDateEventContent);

// 	var dashAtSelectedEvent     			 = $('#dashAtSelectedEvent');

// 	var eventCalendarEventShow     			 = $("#eventCalendarEventShow");
// 	var attendanceEventDescription 			 = $("#attendanceEventDescription");
// 	var attendanceEventDescriptionStatistics = $("#attendanceEventDescriptionStatistics");
// 	var attendanceEventDescriptionAbout 	 = $("#attendanceEventDescriptionAbout");

// 	var dashAtSelectSubject 				 = $("#dashAtSelectSubject");
// 	var dashAtSelectClassroom 				 = $("#dashAtSelectClassroom");

// 	var dashboardAtSelectSubject 			 = $("#dashboardAtSelectSubject");
// 	var dashboardAtselectClassroom 			 = $("#dashboardAtselectClassroom");
	
// 	dashAtSelectedEvent.html("<span class='lead'><strong>"+eventitle+"</strong></span>");

// 	eventCalendarEventShow.html('<div class="row"><img src="../packages/assets/img/loading.gif" alt="Saving..." height="35"></div>');


// 	attendanceEventDescription.hide();
// 	attendanceEventDescriptionStatistics.hide();
// 	attendanceEventDescriptionAbout.html("<p>"+description+"</p>");
// 	attendanceEventDescription.slideDown();

// 	dashAtSelectSubject.hide();
// 	dashAtSelectClassroom.hide();

// 	dashboardAtSelectSubject.select2("val", "");
// 	dashboardAtselectClassroom.select2("val", "");

// 	var url = base_url+"api/At/Record_events/"+eventrecordid; 

// 	$.getJSON( url, { audiencetype : audiencetype ,date : currentDateUnixRealMonth } ,function(data) {

// 		onAtLoadAttendanceRecordTable( audiencetype ,data  ,eventCalendarEventShow ,attendanceEventDescriptionStatistics );
    	
//     })
//     .done(function() {


//     })
//     .fail(function() {
//     })
//     .always(function() {
//     });

// }



// function onAtLoadAttendanceRecordTable(audiencetype, data ,eventCalendarEventShow , attendanceEventDescriptionStatistics )
// {
// 	NotRecorded     = 0;
// 	Present 		= 0;
// 	Absent 			= 0;
// 	ExcusedAbsent   = 0;
// 	Late			= 0;
// 	Sick			= 0;

// 	var eventRecord = data.results;
// 	eventID			= data.eventID;

// 	var AttendanceContainer = '';

//   	if ( audiencetype == 0 ) 
//   	{
//   		AttendanceContainer +='<table class="table abDataTable table-hover" id="dashTeAtSchoolEventRecord" ><thead><tr><th>Staff</th><th>Status</th></thead><tbody>';

//   		$.each(eventRecord, function(i, item) {

// 	      AttendanceContainer +="<tr id='"+item.personID+"' >";
// 	        AttendanceContainer +="<td><img class='img-circle' data-toggle='tooltip' data-original-title="+item.names+" style='max-width:30px; max-height:30px;' src='images/schoolStaff/50/"+item.personID+"/"+item.photoID+"' /> "+item.names+" </td>";
// 	        AttendanceContainer +="<td class='audiencetStatus' ><center><span><span id='status_"+item.personID+"'>"+getAtStatusSpanContentFirst( item.attRcdStatusID )+"</span></a><ul class='attendanceStatus-group' selectedStatus='"+item.attRcdStatusID+"' id='"+item.personID+"' ><li id='2' ><a href='#' class='list-group-item' >Present</a></li><li id='5'><a href='#' class='list-group-item'>Late</a></li><li id='4'><a href='#' class='list-group-item'>Excused absent</a></li><li id='6'><a href='#' class='list-group-item'>Sick</a></li><li id='3'><a href='#' class='list-group-item'>Absent</a></li><li id='1' ><a href='#' class='list-group-item'>Not recorded</a></li></ul></span></center></td>";
// 	      AttendanceContainer +="</tr>";

// 	    });

//   	}else if( audiencetype == 1 ) {

//   		AttendanceContainer +='<table class="table abDataTable table-hover" id="dashTeAtSchoolEventRecord" ><thead><tr><th>Student</th><th>Registration Number</th><th>Status</th></thead><tbody>';

// 			$.each(eventRecord, function(i, item) {

// 	      AttendanceContainer +="<tr id='"+item.personID+"' studentNameRegNumber='"+item.names+" ( "+item.studRegNber+" )' regNumber='"+item.studRegNber+"' >";
// 	        AttendanceContainer +="<td><img class='img-circle' data-toggle='tooltip' data-original-title="+item.names+" style='max-width:30px; max-height:30px;' src='images/student/50/"+item.personID+"/"+item.photoID+"' /> "+item.names+" </td>";
// 	        AttendanceContainer +="<td >"+item.studRegNber+"</td>";
// 	        AttendanceContainer +="<td class='audiencetStatus' ><center><span><span id='status_"+item.personID+"'>"+getAtStatusSpanContentFirst( item.attRcdStatusID )+"</span></a><ul class='attendanceStatus-group' selectedStatus='"+item.attRcdStatusID+"' id='"+item.personID+"' ><li id='2' ><a href='#' class='list-group-item' >Present</a></li><li id='5'><a href='#' class='list-group-item'>Late</a></li><li id='4'><a href='#' class='list-group-item'>Excused absent</a></li><li id='6'><a href='#' class='list-group-item'>Sick</a></li><li id='3'><a href='#' class='list-group-item'>Absent</a></li><li id='1' ><a href='#' class='list-group-item'>Not recorded</a></li></ul></span></center></td>";
// 	      AttendanceContainer +="</tr>";

// 	    });

// 		}

//     AttendanceContainer +="</tbody></table>";

// 	eventCalendarEventShow.html(AttendanceContainer);
// 	eventCalendarEventShow.slideDown( "slow");

// 	abTableNoPaging('dashTeAtSchoolEventRecord');

// 	onAtAudienceAttendanceStatusChange( eventID );

// 	onAtTdHover();

// 	attendanceUpdateSatistics(NotRecorded, Present, Absent, ExcusedAbsent, Late, Sick );

//     attendanceEventDescriptionStatistics.slideDown();
//     $("#attendanceEventAboutContainer").slideDown();
// }

function getAtStatusSpanContentFirst( status )
{
	var content ='';

	switch(status) {
	    case "1":
	    	NotRecorded 	++;
	    	content = '<span class="badge"> Not recorded</span>';
	        break;

	    case "2":
	       Present 		++;
	       content = '<span class="badge bg-blue"> Present</span>';
	        break;

        case "3":
        	Absent 			++;
        	content = '<span class="badge bg-red"> Absent</span>';
	        break;

	    case "4":
	    	ExcusedAbsent 	++;
        	content = '<span class="badge bg-aqua"> Excused absent</span>';
	        break;

	    case "5":
	    	Late			++;
        	content = '<span class="badge bg-green"> Late</span>';
	        break;

	    case "6":
	    	Sick			++;
        	content = '<span class="badge bg-yellow"> Sick</span>';
	        break;
	}

	return content ;
}

// function getAtStatusSpanContentUpdate( status ,oldselectedStatus )
// {
// 	var content ='';

// 	switch(status) {
// 	    case "1":
// 	    	content = '<span class="badge"> Not recorded</span>';
	    	
// 	    	NotRecorded 	++;

// 	        break;

// 	    case "2":
// 	       content = '<span class="badge bg-blue"> Present</span>';
	       
// 	       Present 		++;
	       
// 	       break;

//         case "3":
//         	content = '<span class="badge bg-red"> Absent</span>';
        	
// 			Absent 			++;
			
// 	        break;

// 	    case "4":
//         	content = '<span class="badge bg-aqua"> Excused absent</span>';
        	
// 			ExcusedAbsent 	++;
// 	        break;

// 	    case "5":
//         	content = '<span class="badge bg-green"> Late</span>';
        	
// 			Late			++;
// 	        break;

// 	    case "6":
//         	content = '<span class="badge bg-yellow"> Sick</span>';

// 			Sick			++;
// 	        break;
// 	}

// 	switch(oldselectedStatus) {

// 	    case "1":
// 	    	NotRecorded 	--;
// 	        break;

// 	    case "2":
// 	        Present 		--;
// 	        break;

//         case "3":
//         	Absent 			--;
// 	        break;

// 	    case "4":
//         	ExcusedAbsent 	--;
// 	        break;

// 	    case "5":
//         	Late			--;
// 	        break;

// 	    case "6":
//         	Sick			--;
// 	        break;
// 	}

// 	return content ;
// }

// function onAtAudienceAttendanceStatusChange( eventID )
// {
// 	$( ".attendanceStatus-group" ).delegate( "li", "click", function() {

// 			var tempScrollTop = $(window).scrollTop();

// 		  	audienceId 		   = $(this).closest('ul').attr('id');
// 		  	oldselectedStatus  = $(this).closest('ul').attr('selectedStatus'); 
// 		  	StatusID           = $(this).attr('id');

// 		  	if ( oldselectedStatus != 	StatusID ) {

		  		
// 				atchangeStatus( audienceId , StatusID , $(this) , eventID , oldselectedStatus);
		  		
// 		  	}
		  
// 		  	$(window).scrollTop(tempScrollTop);

// 		  	return false;
// 		});
// }

// function onAtTdHover(){

// 	$('.audiencetStatus').bind('mouseover', openSubMenu);
// 	$('.audiencetStatus').bind('mouseout', closeSubMenu);
	
// 	function openSubMenu() {
// 		$(this).find('.attendanceStatus-group').css('visibility', 'visible');	
// 	};
	
// 	function closeSubMenu() {
// 		$(this).find('.attendanceStatus-group').css('visibility', 'hidden');	
// 	};

// }

// function attendanceUpdateSatistics (NotRecorded, Present, Absent, ExcusedAbsent, Late, Sick )
// {
// 	$( "#at_Present" ).html("<b>"+Present+"</b>");
// 	$( "#at_Late" ).html("<b>"+Late+"</b>");
// 	$( "#at_Excusedabsent" ).html("<b>"+ExcusedAbsent+"</b>");
// 	$( "#at_Sick" ).html("<b>"+Sick+"</b>");
// 	$( "#at_Absent" ).html("<b>"+Absent+"</b>");
// 	$( "#at_NotRecorded" ).html("<b>"+NotRecorded+"</b>");

// }

// function atchangeStatus(audienceId , statusId , e , eventID , oldselectedStatus )
// {
	 
// 	  var url = base_url+"api/At/Record_events"; 

// 	  e.closest('ul').css('visibility', 'hidden');

//       $.post(url, 
//          {audienceId : audienceId , statusId: statusId , eventID: eventID }, 
//          function(data, status, xhr){

//            if (data.success)
//                 {   
//                 	e.closest('ul').attr('selectedStatus', statusId );

//                 	statusContainer = $('#status_'+audienceId);
// 					statusContainer.html(getAtStatusSpanContentUpdate( statusId ,oldselectedStatus ));

// 					attendanceUpdateSatistics(NotRecorded, Present, Absent, ExcusedAbsent, Late, Sick );
				
// 				} else {

//                     $.gritter.add({
// 						title: 'Failed',
// 						text: 'Failed to save .',
// 						class_name: 'danger gritter-center',
// 						time: ''
// 					});

//                 }
//          });

     
// }


//new event 
	function newEventJS(){

		//hide
		    var staffTypeContainer 		= $('#staffTypeContainer');
		    var studentTypeContainer 	= $('#studentTypeContainer');

		    staffTypeContainer.hide();

		    var customStudentContainer 		= $("#customStudentContainer");
		    var chooseClassesContainer 		= $("#chooseClassesContainer");
		    var studentSelectSomeContainer  = $("#studentSelectSomeContainer");
		    var staffSelectRoleContainer 	= $("#staffSelectRoleContainer");
		    var staffSelectSomeContainer 	= $("#staffSelectSomeContainer");
		    var staffSelectAlumnusContainer = $("#staffSelectAlumnusContainer"); 
		    var selectOrganizationContainer	= $("#selectOrganizationContainer");

		    customStudentContainer.hide();
		    chooseClassesContainer.hide();
		    studentSelectSomeContainer.hide();
		    staffSelectRoleContainer.hide();
		    staffSelectSomeContainer.hide();
		    staffSelectAlumnusContainer.hide();

	    $('input[type=radio][name=personType]').change(function() {
	        
	        var selected = $(this).val();

	        studentSelectSomeContainer.hide();
		    customStudentContainer.hide();
			staffSelectRoleContainer.hide();
	    	staffSelectSomeContainer.hide();

	    	staffTypeContainer.hide();
	    	studentTypeContainer.hide();

	    	$("input[name=studentType][value='1']").prop("checked",false);
	    	$("input[name=studentType][value='2']").prop("checked",false);
	    	$("input[name=studentType][value='3']").prop("checked",false);

	    	$("input[name=staffType][value='1']").prop("checked",true);

		    if ( selected == 1 ) {

		    	studentTypeContainer.slideDown();

		    }else if( selected == 2 ) {

		    	staffTypeContainer.slideDown();

		    }


	    });

	    $('input[type=radio][name=studentType]').change(function() {
	        
	        var selected = $(this).val();

	        customStudentContainer.hide();
	        chooseClassesContainer.hide();
			studentSelectSomeContainer.hide();

		    if ( selected == 1 ) {

		    }else if( selected == 2 ) {

		    	customStudentContainer.slideDown();
		    }else if( selected == 3 ) {

		    	studentSelectSomeContainer.slideDown();
		    }

	    });

	    $('input[type=radio][name=staffType]').change(function() {
	        
	        var selected = $(this).val();

	        staffSelectRoleContainer.hide();
	        staffSelectSomeContainer.hide();

		    if ( selected == 1 ) {


		    }else if( selected == 2 ) {
				
				staffSelectRoleContainer.slideDown();

		    }else if( selected == 3 ) {

		    	staffSelectSomeContainer.slideDown();
		    }

	    });

	     $('input[type=radio][name=studentClass]').change(function() {
	        
	        var selected = $(this).val();

	        chooseClassesContainer.hide();

		    if ( selected == 0 ) {

		    	chooseClassesContainer.slideDown();

		    }else if( selected == 1 ) {
				
		    }

	    });

	    $('#studentSponsorOrg').change(function() {
	        
	        if ( $(this).is(":checked") )
	         {
	         	selectOrganizationContainer.slideDown();

	         }else{

	         	selectOrganizationContainer.hide();
	         }
	        
	    });


	}

	function atNewEventSelect2()
	{

		$("#selectAtRecorder").select2();
		$("#newEventSelectClassroom").select2({ 
	            closeOnSelect: false,
	            formatResult: newStudentformatResult,
	            formatSelection: newStudentformatSelection
	    });

		$("#newEventSelectStudent").select2();
		$("#newEventSelectStaff").select2();
		$("#newEventStaffSelectRoles").select2();
		$("#selectPaymentOrganization").select2();

	}

	function submitAttendanceNewEvent()
	{
		$('#formNewEvent').on('submit', function(e){
			
		  var frm = $('#formNewEvent');
		  var url = base_url+"api/At/events"; 
	      e.preventDefault();
	      $.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){
	           if (data.success)
	                {   
	                    $.gritter.add({
							title: 'Success',
							text: 'Event created.',
							class_name: 'success gritter-center',
							time: ''
						});
						
	                    getDashboard();

	                } else {

	                    $.gritter.add({
							title: 'Failed',
							text: 'Failed to add event.',
							class_name: 'danger gritter-center',
							time: ''
						});

	                }
	         });
	    });

	}
	

	function dashAtNewEvent()
	{
		var container          = $( "#dashAttendance_content" ); 
	 	container.empty();
		container.load("attendance/new_event").fadeIn(1000);
	}

	function dashAtSicknessRecords()
	{
		var container       = $( "#dashAttendance_content" ); 

	 	container.empty();
	 	var content = '';

	 	content +='<div  class="col-sm-12">';
			content +='<div class="row">';
				content +='<div class="col-sm-10 text-center"><h4><strong>Sickness Records</strong></h4></div>';
				content +='<div class="col-sm-2"><button type="button" class="btn btn-danger pull-right" onclick="get_NewSicknessRecord()"><strong><i class="fa fa-plus"></i> New Sickness Record</strong></button></div>';
			content +='</div>';
			content +='<div class="row" id="AtEventAtSicknessRecords">';
				content +='<div class="row text-center>Loading sickness records...</div>';
			content +='</div>';
		content +='</div>';

		container.html(content);

		var url = base_url+"api/At/sickness";

		  $.getJSON( url, function(data) {

		    	var SicknessRecordsContent     = '';	
			    	SicknessRecordsContent += '<table class="table table-bordered table-striped" id="SicknessRecordsTable" >';
					    SicknessRecordsContent += '<thead>';
					    	SicknessRecordsContent += '<th class="text-center">Date</th>';
					    	SicknessRecordsContent += '<th class="text-center">Student</th>';
					    	SicknessRecordsContent += '<th class="text-center">Reg. Number</th>';
					    	SicknessRecordsContent += '<th class="text-center">Class</th>';
					    	SicknessRecordsContent += '<th class="text-center">Sickness</th>';
					    	SicknessRecordsContent += '<th class="text-center">Comment</th>';
					    	SicknessRecordsContent += '<th class="text-center">Added By</th>';
				    	SicknessRecordsContent += '</thead>';
			    	SicknessRecordsContent += '<tbody>';

			    	var RowIndex 	 = 1;

					$.each( data, function(i, item) {

				        SicknessRecordsContent +="<tr >";
				          SicknessRecordsContent +="<td class='text-center'>"+date_moment(item.date)+"</td>";
				          SicknessRecordsContent +="<td>"+item.studentNames+"</td>";
				          SicknessRecordsContent +="<td class='text-center'>"+item.studRegNber+"</td>";
				          SicknessRecordsContent +="<td class='text-left'>"+item.classroomName+"</td>";

				          if ( item.canEdit == 1) {
				          	SicknessRecordsContent +='<td class="text-center"><span class="xeditableSicknessSickness" data-type="text" data-pk="'+item.id+'" data-title="Enter Sickness Type" data-url="'+base_url+'api/At/sickness/'+item.id+'?UpdateType=1" >'+item.sickness+'</span></td>';
				          	SicknessRecordsContent +='<td class="text-center"><span class="xeditableSicknessComment" data-type="text" data-pk="'+item.id+'" data-title="Enter Comment" data-url="'+base_url+'api/At/sickness/'+item.id+'?UpdateType=2" >'+item.comment+'</span></td>';
				          	
				          }else{
				          	SicknessRecordsContent +="<td class='text-left'>"+item.sickness+"</td>";
				          	SicknessRecordsContent +="<td class='text-left'>"+item.comment+"</td>";

				          }
				          SicknessRecordsContent +="<td class='text-left'>"+item.addedBy+"</td>";
				        SicknessRecordsContent +="</tr>";

				    });

				    SicknessRecordsContent +='</tbody></table>';
			    $( "#AtEventAtSicknessRecords" ).html(SicknessRecordsContent);

		  }).done(function() {

		  	abTablePagingTable('SicknessRecordsTable');

		  	$.fn.editable.defaults.mode = 'inline';
            $.fn.editable.defaults.ajaxOptions = {type: "PUT" };

            $('.xeditableSicknessSickness').editable({
                emptytext: 'Click to add',
                success: function(response, newValue) {
                    if(response.status == 'error') return response.msg;
                }
            });

            $('.xeditableSicknessComment').editable({
				emptytext: 'Click to add comment',
				tpl: '<textarea ></textarea>' ,
				rows: 10,
		    	success: function(response, newValue) {
			        if(response.status == 'error') return response.msg;
			    }
			});

	      })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}

	function get_NewSicknessRecord()
	{
	  $('#Model_NewSicknessRecord').modal('toggle');
	}

	function dashboardAttendance()
	{
		$("#dashboardAtSelectEvent").select2().bind('change', onAteEventSelected);
		$("#deleteEventCLoading").hide();
		$("#deleteEventContent").show();

		var DeleteAtEventConfirm = $("#DeleteAtEventConfirm");
		DeleteAtEventConfirm.click(function(){

			deleteAtEvent();

		});

		$("#DashAtNewSicknessLoding").hide();
		$("#formDashAtNewSickness").show();
		$("#NewSicknessStudentSelect").select2();
		$('#NewSicknessDate').datepicker().datepicker("setDate", new Date());
		submitAtSaveNewSickness();
	}

	function submitAtSaveNewSickness()
	{
	    //validation
	    var formDashAtNewSickness = $('#formDashAtNewSickness');
	    formDashAtNewSickness.formValidation({
	            framework: 'bootstrap',
	            icon: {
	                valid: 'glyphicon glyphicon-ok',
	                invalid: 'glyphicon glyphicon-remove',
	                validating: 'glyphicon glyphicon-refresh'
	            },
	            exclude: ':disabled',
	            fields: {
	                NewSicknessStudent: {
	                    validators: {
	                        notEmpty: {
	                            message: 'Student is required'
	                        }
	                    }
	                },
	                NewSicknessSicknessType: {
	                    validators: {
	                        notEmpty: {
	                            message: 'Sickness is required'
	                        }
	                    }
	                },
	                NewSicknessComment: {
	                    validators: {
	                        notEmpty: {
	                            message: 'Comment is required'
	                        }
	                    }
	                },
	                NewSicknessDate: {
	                    validators: {
	                        notEmpty: {
	                            message: 'Date is required'
	                        }
	                    }
	                }
	            }
	        })
	    .on('success.form.fv', function(e , data) {

	        e.preventDefault();

	        dashAtSubmitValidatedNewSickness();

	     }).find('[name="NewArrivalDate"]').change(function(e) {
	         formDashAtNewSickness.formValidation('revalidateField', 'NewSicknessDate');
	                                                           
	     }).end();

	}

	function dashAtSubmitValidatedNewSickness()
	{

	    var DashAtNewSicknessLoding = $( "#DashAtNewSicknessLoding" );
	    var formDashAtNewSickness   = $( "#formDashAtNewSickness" );

	    DashAtNewSicknessLoding.show();
	    formDashAtNewSickness.hide();

	    var frm = formDashAtNewSickness;
	    var url = base_url+"api/At/sickness"; 

	    $.post(url, 
	       frm.serialize(), 
	       function(data, status, xhr){

	        if ( data.success )
	        {   

	          $('#Model_NewSicknessRecord').modal('toggle');
	          $.gritter.add({
	              title: 'Success',
	              text:  'Sickness record added',
	              class_name: 'success gritter-center',
	              time: '' 
	          });

	          formDashAtNewSickness.show();
	          DashAtNewSicknessLoding.hide();

	          dashAtSicknessRecords();

	          $('#NewSicknessStudentSelect').select2('data', null);
	          $('#NewSicknessSicknessType').select2('data', null);
	          $('#NewSicknessComment').val(null);
	          
	        } else {

	            formDashAtNewSickness.show();
	            DashAtNewSicknessLoding.hide();
	        
	            $.gritter.add({
	              title: 'Failed',
	              text: 'Failed to add Sickness record',
	              class_name: 'danger gritter-center',
	              time: '' 
	            });
	        }
	             
	     },"json")
	      .done(function() {
	      })
	      .fail(function() {  

	          formDashAtNewSickness.show();
	          DashAtNewSicknessLoding.hide();

	          $.gritter.add({
	            title: 'Failed',
	            text: 'Something went wrong, Please Try Again',
	            class_name: 'danger gritter-center',
	            time: ''
	          });

	      })
	      .always(function() {});
	}

	function deleteAtEvent()
	{

		var atDeleteEventID     = $("#atDeleteEventID").val();

	    var deleteEventCLoading  = $("#deleteEventCLoading");
	    var deleteEventContent   = $("#deleteEventContent");

	    deleteEventContent.hide();
	    deleteEventCLoading.slideDown();

	    var url     = base_url+"api/At/events/"+atDeleteEventID; 

	      $.ajax({
	            url: url,
	            type: 'DELETE',
	            success: function(data) {
	              
	              deleteEventCLoading.hide();
	              deleteEventContent.slideDown();
	              
	              $('#Model_DashAtDeleteEvent').modal('toggle');

	               if ( data.success )
	               {
	                    $.gritter.add({
	                      title: 'Success',
	                      text: 'Event deleted.',
	                      class_name: 'success gritter-center',
	                      time: ''
	                    });

	                    dashOnTabSelectedAttendance();

	               }else{

	                 $.gritter.add({
	                    title: 'Failed',
	                    text: 'Failed to delete event, Something went wrong.',
	                    class_name: 'danger gritter-center',
	                    time: ''
	                  });

	             }
	        },
	        error: function(){
	       
		        deleteEventCLoading.hide();
		        deleteEventContent.slideDown();

		          $.gritter.add({
		            title: 'Failed',
		            text: 'Failed to delete eventID.',
		            class_name: 'danger gritter-center',
		            time: ''
		          });
	    	}

	    });
	}

	function onAteEventSelected(){

		var sel 			= $("#dashboardAtSelectEvent");

		var audiencetype 	= sel.select2().find(":selected").data("audiencetype");
		var description 	= sel.select2().find(":selected").data("description");
		var selected 		= sel.select2('val');
		var eventName		= sel.select2('data').text;


		$("#atDeleteEventID").val(selected);

		var container       = $( "#dashAttendance_content" ); 

	 	container.empty();
	 	var content = '';

	 	content +='<div  class="col-sm-12">';
			content +='<div  class="col-sm-4">';
				content +='<div class="row text-center">';
					content +='<h4><strong>'+eventName+'</strong></h4>';
				content +='</div>';
				content +='<div class="row top-buffer">';
					content +='<div class="col-sm-4 text-right">';
						content +='Description:';
					content +='</div>';
					content +='<div class="col-sm-8">';
						content += description ;
					content +='</div>';
				content +='</div>';
				content +='<div class="row">';
					content +='<div class="col-sm-4 text-right">';
						content +='For:';
					content +='</div>';
					content +='<div class="col-sm-8">';
						if ( audiencetype == 1 ) {
							content +='<span class="label label-default">Student</span>';

						}else if( audiencetype == 0 )
						{
							content +='<span class="label label-default">Staff</span>';
						}
					content +='</div>';
				content +='</div>';
				content +='<div class="row">';
					content +='<div class="col-sm-4 text-right">';
						content +='Recorder:';
					content +='</div>';
					content +='<div class="col-sm-8" id="AtEventRecorder" >';
					content +='</div>';
				content +='</div>';
				content +='<div class="row top-buffer">';
					content +='<button class="col-sm-8 col-sm-offset-2 btn btn-danger" data-id="0" data-toggle="modal" data-target="#Model_DashAtDeleteEvent" ><i class="fa fa-trash-o"></i> Delete Event</button>';
				content +='</div>';
			content +='</div>';
			content +='<div  class="col-sm-7">';
				content +='<div class="row">';
					content +='<div class="col-sm-12 text-center"><h4><strong>Attendees</strong></h4></div>'; 
				content +='</div>';
				content +='<div class="row" id="AtEventAttendees">';
					content +='<div class="row text-center>Loading event\'s attendees...</div>';
				content +='</div>';
			content +='</div>';
		content +='</div>';

		container.html(content);

		var AtEventAttendees = $( "#AtEventAttendees" ); 
		var AtEventRecorder  = $( "#AtEventRecorder" );

		var url = base_url+"api/At/events/"+selected;

		  $.getJSON( url, function(data) {
		    	
		    	var SchoolEventAudience = data.SchoolEventAudience ;
		    	var SchoolEventRecorder = data.SchoolEventRecorder ;

		    	var AudienceContent     = '';	
			    	AudienceContent += '<table class="table table-bordered table-striped" id="AtAudienceContentTable" ><thead><tr>';
			    	AudienceContent += '<th>#</th>';
			    	AudienceContent += '<th>Names</th>';
			    	AudienceContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-blue">&nbsp;&nbsp;Present&nbsp;&nbsp;</span></span></th>';
			    	AudienceContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-red">&nbsp;&nbsp;Absent&nbsp;&nbsp;</span></span></th>';
			    	AudienceContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-aqua">&nbsp;&nbsp;Excused Absent&nbsp;&nbsp;</span></span></th>';
			    	AudienceContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-green">&nbsp;&nbsp;Late&nbsp;&nbsp;</span></span></th>';
			    	AudienceContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-yellow">&nbsp;&nbsp;Sick&nbsp;&nbsp;</span></span></th>';
			    	AudienceContent += '</thead>';

			    	AudienceContent += '<tfoot><tr>';
			    		AudienceContent += '<th colspan="2" style="text-align:right">Total:</th>';
			    		AudienceContent += '<th class="text-blue" style="text-align:center "></th>';
			    		AudienceContent += '<th class="text-red" style="text-align:center "></th>';
			    		AudienceContent += '<th class="text-aqua" style="text-align:center "></th>';
			    		AudienceContent += '<th class="text-green" style="text-align:center "></th>';
			    		AudienceContent += '<th class="text-yellow" style="text-align:center "></th>';
			    	AudienceContent += '</tr></tfoot>';

			    	AudienceContent += '<tbody>';

			    	var RowIndex 	 = 1;

					$.each( SchoolEventAudience, function(i, item) {

				        AudienceContent +="<tr >";
				          AudienceContent +="<td>"+RowIndex+"</td>";
				          AudienceContent +="<td>"+item.names+"</td>";
				          AudienceContent +="<td class='text-center text-blue'>"+item.Total_Present+"</td>";
				          AudienceContent +="<td class='text-center text-red'>"+item.Total_Absent+"</td>";
				          AudienceContent +="<td class='text-center text-aqua'>"+item.Total_ExcusedAbsent+"</td>";
				          AudienceContent +="<td class='text-center text-green'>"+item.Total_Late+"</td>";
				          AudienceContent +="<td class='text-center text-yellow'>"+item.Total_Sick+"</td>";
				        AudienceContent +="</tr>";

				        RowIndex++;

				    });

				    AudienceContent +='</tbody></table>';
			    AtEventAttendees.html(AudienceContent);

			    AtEventRecorder.empty();
		    	$.each( SchoolEventRecorder, function(i, item) {
		    		AtEventRecorder.append('<span class="label label-default">'+item.names+'</span>');
		    	});

		  }).done(function() {

		  		 $('#AtAudienceContentTable').DataTable( {
		  		   paging: false ,
		          "aaSorting": [] ,
		          language: {
		                searchPlaceholder: "Search attendee here... "
		          },
		          "footerCallback": function ( row, data, start, end, display ) {
		              var api = this.api(), data;
		 
		            // Remove the formatting to get integer data for summation
			            var intVal = function ( i ) {
			                return typeof i === 'string' ?
			                    i.replace(/[\$,]/g, '')*1 :
			                    typeof i === 'number' ?
			                        i : 0;
			            };

		 			// Present  
			            // Total over all pages
			                total = api
			                    .column( 2 )
			                    .data()
			                    .reduce( function (a, b) {
			                        return intVal(a) + intVal(b);
			                    }, 0 );

			            // Update footer
			                $( api.column( 2 ).footer() ).html(
			                    ' '+total+' '
			                );

			        // Absent 
			            // Total over all pages
			                total = api
			                    .column( 3 )
			                    .data()
			                    .reduce( function (a, b) {
			                        return intVal(a) + intVal(b);
			                    }, 0 );

			            // Update footer
			                $( api.column( 3 ).footer() ).html(
			                    ' '+total+' '
			                );

			        // Excused Absent 
			            // Total over all pages
			                total = api
			                    .column( 4 )
			                    .data()
			                    .reduce( function (a, b) {
			                        return intVal(a) + intVal(b);
			                    }, 0 );

			            // Update footer
			                $( api.column( 4 ).footer() ).html(
			                    ' '+total+' '
			                );

			        // Late   
			            // Total over all pages
			                total = api
			                    .column( 5 )
			                    .data()
			                    .reduce( function (a, b) {
			                        return intVal(a) + intVal(b);
			                    }, 0 );

			            // Update footer
			                $( api.column( 5 ).footer() ).html(
			                    ' '+total+' '
			                );

			        // Sick    
			            // Total over all pages
			                total = api
			                    .column( 6 )
			                    .data()
			                    .reduce( function (a, b) {
			                        return intVal(a) + intVal(b);
			                    }, 0 );

			            // Update footer
			                $( api.column( 6 ).footer() ).html(
			                    ' '+total+' '
			                );

		          }
		      } );

	      })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}

	function onDashAtRecordAttendance(){

		var container          = $( "#dashAttendance_content" ); 
	 	container.empty();

	 	var content = '';
	 	content +='<div  class="col-sm-12">';
			content +='<div  class="col-sm-4 ">';
				content +='<div class="row text-center">';
					content +='<h4>Record Attendance</h4>';
				content +='</div>';
				content +='<form id="formAtNewAtRecord" class="form-horizontal top-buffer" role="form" >';
					content +='<div class="form-group">';
						content +='<label class="col-sm-3 control-label"></label>';
						content +='<div class="col-sm-2 "></div>';
						content +='<div class="col-sm-3 radio">';
							content +='<label><input type="radio" name="AtNewAtRecordType" id="AtNewAtRecordType_Student" value="1" checked > Student</label>';
						content +='</div>';
						content +='<div class="col-sm-3 radio">';
							content +='<label><input type="radio" name="AtNewAtRecordType" id="AtNewAtRecordType_Staff" value="2" > Staff</label>';
						content +='</div>';
					content +='</div>';
					content +='<div class="form-group" id="AtNewAtRecordStudPerContainer" >';
						content +='<label class="col-sm-3 control-label">Per</label>';
						content +='<div class="col-sm-1 "></div>';
						content +='<div class="col-sm-2 radio">';
							content +='<label><input type="radio" name="AtNewAtRecordStudPer"  value="1" checked > Day</label>';
						content +='</div>';
						content +='<div class="col-sm-2 radio">';
							content +='<label><input type="radio" name="AtNewAtRecordStudPer"  value="2" > Period</label>';
						content +='</div>';
						content +='<div class="col-sm-2 radio">';
							content +='<label><input type="radio" name="AtNewAtRecordStudPer"  value="3" > Events</label>';
						content +='</div>';
					content +='</div>';
					content +='<div class="form-group" id="AtNewAtRecordStaffPerContainer" >';
						content +='<label class="col-sm-3 control-label">Per</label>';
						content +='<div class="col-sm-1 "></div>';
						content +='<div class="col-sm-2 radio">';
							content +='<label><input type="radio" name="AtNewAtRecordStaffPer"  value="1" checked > Day</label>';
						content +='</div>';
						content +='<div class="col-sm-2 radio">';
						content +='</div>';
						content +='<div class="col-sm-2 radio">';
							content +='<label><input type="radio" name="AtNewAtRecordStaffPer"  value="3" > Events</label>';
						content +='</div>';
					content +='</div>';
					content +='<div class="form-group" id="AtNewAtRecordClassContainer" >';
						content +='<label class="col-sm-3 control-label">Class</label>';
						content +='<div class="col-sm-7">';
							content +='<select name="AtNewAtRecordClass" id="AtNewAtRecordClass" class="form-control"  >';
							content +='</select>';
						content +='</div>';
					content +='</div>';
					content +='<div class="form-group" id="AtNewAtRecordClassSubjectContainer" >';
						content +='<label class="col-sm-3 control-label">Class Subject</label>';
						content +='<div class="col-sm-7">';
							content +='<select name="AtNewAtRecordClassSubject" id="AtNewAtRecordClassSubject" class="form-control"  >';
							content +='</select>';
						content +='</div>';
					content +='</div>';
					content +='<div class="form-group" id="AtNewAtRecordStudEventContainer" >';
						content +='<label class="col-sm-3 control-label">Student Event</label>';
						content +='<div class="col-sm-7">';
							content +='<select name="AtNewAtRecordStudEvent" id="AtNewAtRecordStudEvent" class="form-control"  >';
							content +='</select>';
						content +='</div>';
					content +='</div>';
					content +='<div class="form-group" id="AtNewAtRecordStaffEventContainer" >';
						content +='<label class="col-sm-3 control-label">Staff Event</label>';
						content +='<div class="col-sm-7">';
							content +='<select name="AtNewAtRecordEventStaff" id="AtNewAtRecordEventStaff" class="form-control"  >';
							content +='</select>';
						content +='</div>';
					content +='</div>';
					content +='<div class="form-group">';
						content +='<label class="col-sm-3 control-label">Date</label>';
						content +='<div class="col-sm-7">';
							content +='<input name="AtNewAtRecordDate" id="AtNewAtRecordDate" class="form-control"  >';
						content +='</div>';
					content +='</div>';
					content +='<div class="form-group top-buffer">';
						content +='<label class="col-sm-3 control-label"></label>';
						content +='<div class="col-sm-7 text-center">';
							content +='<button class="btn btn-lg btn-primary btn-block" type="submit">Start Recording</button>';
						content +='</div>';
					content +='</div>';
				content +='</form>';
			content +='</div>';
			content +='<div  class="col-sm-8" id="AtNewAtRecordRightSide" >';
				content +='<div class="row">';
					content +='<div class="col-sm-12 text-center"><h4><strong>Recorded Attendance</strong></h4></div>';
				content +='</div>';
				content +='<div class="row top-buffer" id="AtNewAtRecordRightSideContent">';
					content +='<div class="row text-center>Loading Recorded...</div>';
				content +='</div>';
			content +='</div>';
		content +='</div>';

		container.html(content);

		var AtNewAtRecordStudPerContainer 		= $('#AtNewAtRecordStudPerContainer') ;
		var AtNewAtRecordStaffPerContainer		= $('#AtNewAtRecordStaffPerContainer') ;
		var AtNewAtRecordClassContainer 		= $('#AtNewAtRecordClassContainer') ;
		var AtNewAtRecordClassSubjectContainer	= $('#AtNewAtRecordClassSubjectContainer') ;
		var AtNewAtRecordStudEventContainer 	= $('#AtNewAtRecordStudEventContainer') ;
		var AtNewAtRecordStaffEventContainer 	= $('#AtNewAtRecordStaffEventContainer') ;

		var AtNewAtRecordClass  				= $('#AtNewAtRecordClass');
		var AtNewAtRecordClassSubject  			= $('#AtNewAtRecordClassSubject');
		var AtNewAtRecordStudEvent  			= $('#AtNewAtRecordStudEvent');
		var AtNewAtRecordEventStaff  			= $('#AtNewAtRecordEventStaff');

		AtNewAtRecordStaffPerContainer.hide();
		AtNewAtRecordClassSubjectContainer.hide();
		AtNewAtRecordStudEventContainer.hide();
		AtNewAtRecordStaffEventContainer.hide();

		$('input[type=radio][name=AtNewAtRecordType]').change( function() {
		        
	        var selected = $(this).val();

	        AtNewAtRecordStudPerContainer.hide();
	        AtNewAtRecordStaffPerContainer.hide();
	        AtNewAtRecordClassContainer.hide();
			AtNewAtRecordClassSubjectContainer.hide();
			AtNewAtRecordStudEventContainer.hide();
			AtNewAtRecordStaffEventContainer.hide();

		    if ( selected == 1 ) { //Student 

		    	AtNewAtRecordStudPerContainer.slideDown();
		    	$("[name=AtNewAtRecordStudPer]").val(["1"]);

		    	AtNewAtRecordClassContainer.slideDown();

		    }else if( selected == 2 ) { //Staff 

		    	AtNewAtRecordStaffPerContainer.slideDown();
		    	$("[name=AtNewAtRecordStaffPer]").val(["1"]);

		    }

	    });

		$('input[type=radio][name=AtNewAtRecordStudPer]').change( function() {
		        
	        var selected = $(this).val();

	        AtNewAtRecordClassContainer.hide();
			AtNewAtRecordClassSubjectContainer.hide();
			AtNewAtRecordStudEventContainer.hide();

		    if ( selected == 1 ) { //Day 
		    	AtNewAtRecordClassContainer.slideDown();

		    }else if( selected == 2 ) { //Staff 
		    	AtNewAtRecordClassSubjectContainer.slideDown();

		    }else if( selected == 3 ){ //Event 
		    	AtNewAtRecordStudEventContainer.slideDown();
		    }

	    });

		$('input[type=radio][name=AtNewAtRecordStaffPer]').change( function() {
		        
	        var selected = $(this).val();

	        AtNewAtRecordStaffEventContainer.hide();

		    if ( selected == 1 ) { //Day 
		    	AtNewAtRecordStaffEventContainer.hide();

		    }else if( selected == 3 ){ //Event 
		    	AtNewAtRecordStaffEventContainer.slideDown();
		    }

	    });

		$('#AtNewAtRecordDate').datepicker().datepicker("setDate", new Date());

		var url = base_url+"api/At/events";

		  $.getJSON( url, function(data) {
			    
			    var AttendanceClassroom 		= data.AttendanceClassroom;
			    var AttendanceClassroomSubject	= data.AttendanceClassroomSubject;
			    var HasStaffRecordingAccess 	= data.HasStaffRecordingAccess;
			    var SchoolEventForStudent 		= data.SchoolEventForStudent;
			    var SchoolEventForStaff 	   	= data.SchoolEventForStaff;
			    var ABEventRecorded 			= data.ABEventRecorded;
			  
			    if ( !HasStaffRecordingAccess ) {
			    	$("#AtNewAtRecordType_Staff").prop("disabled", true);
			    }

			    //Classes 
					var AnClassOptions 	  = '';
						AnClassOptions 	  = '<option ></option>';

					var Anclassidentifier = "";
					var AnisTheFirsRecord = true;

					$.each( AttendanceClassroom, function(i, item) {

						if ( item.classidentifier == Anclassidentifier ){
						    AnClassOptions += '<option value="'+item.id+'" data-optgroup_name="'+item.YearName+'" data-level="'+item.levelID+'" >'+item.name+'</option>';
						
						}else{

						    Anclassidentifier = item.classidentifier;
					    	if ( AnisTheFirsRecord ){

						    	AnisTheFirsRecord = false; 

					    	}else{
						    	AnClassOptions += '</optgroup>';
						    }

					    	AnClassOptions += '<optgroup label="'+item.YearName+'">';
					   		AnClassOptions += '<option value="'+item.id+'" data-optgroup_name="'+item.YearName+'" data-level="'+item.levelID+'" >'+item.name+'</option>';
					
						}
							    
					});	

					AtNewAtRecordClass.html( AnClassOptions );
					AtNewAtRecordClass.select2({ 
			            formatResult: newGeneralformatResult ,
			            formatSelection: newGeneralformatSelection
				    });

				//Subjects 
					var SubjectsOptions 	  = '';
						SubjectsOptions 	  = '<option ></option>';

					var Anidentifier = "";
					var AnisTheFirsRecord = true;

					$.each( AttendanceClassroomSubject, function(i, item) {

						if ( item.id == Anidentifier ){
						    SubjectsOptions += '<option value="'+item.id+'" data-optgroup_name="'+item.classroomName+' - " >'+item.subjectName+'</option>';
						
						}else{

						    Anidentifier = item.id;
					    	if ( AnisTheFirsRecord ){

						    	AnisTheFirsRecord = false; 

					    	}else{
						    	SubjectsOptions += '</optgroup>';
						    }

					    	SubjectsOptions += '<optgroup label="'+item.classroomName+'">';
					   		SubjectsOptions += '<option value="'+item.id+'" data-optgroup_name="'+item.classroomName+' - " >'+item.subjectName+'</option>';
					
						}
							    
					});	

					AtNewAtRecordClassSubject.html( SubjectsOptions );
					AtNewAtRecordClassSubject.select2({ 
			            formatResult: newGeneralformatResult ,
			            formatSelection: newGeneralformatSelection
				    });

				//Student Event Options 
				 	var OptionsEventsStudent 	= '<option ></option>';

					$.each( SchoolEventForStudent, function(i, item) {
						OptionsEventsStudent += "<option value="+item.id+" >"+item.name+"</option>";

				 	});	

					AtNewAtRecordStudEvent.html( OptionsEventsStudent );
					AtNewAtRecordStudEvent.select2();

				//Staff Event Options 
				 	var OptionsEventsStaff 	= '<option ></option>';

					$.each( SchoolEventForStaff, function(i, item) {
						OptionsEventsStaff += "<option value="+item.id+" >"+item.name+"</option>";

				 	});	

					AtNewAtRecordEventStaff.html( OptionsEventsStaff );
					AtNewAtRecordEventStaff.select2();

				//

					var attendanceListContent     = '';	
					    attendanceListContent += '<table class="table table-bordered table-striped" id="AtRecordedTable" >';
					    	attendanceListContent += '<thead><tr>';
						    	attendanceListContent += '<th>Date</th>';
						    	attendanceListContent += '<th>Type</th>';
						    	attendanceListContent += '<th>Description</th>';
						    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-blue">&nbsp;&nbsp;Present&nbsp;&nbsp;</span></span></th>';
						    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-green">&nbsp;&nbsp;Late&nbsp;&nbsp;</span></span></th>';
						    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-red">&nbsp;&nbsp;Absent&nbsp;&nbsp;</span></span></th>';
						    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-yellow">&nbsp;&nbsp;Sick&nbsp;&nbsp;</span></span></th>';
						    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-aqua">&nbsp;&nbsp;Excused Absent&nbsp;&nbsp;</span></span></th>';
								attendanceListContent += '<th>Recorded by</th>';
					    	attendanceListContent += '</tr></thead>';

					    	attendanceListContent += '<tfoot><tr>';
					    		attendanceListContent += '<th style="text-align:right" colspan="3">Total</th>';
					    		attendanceListContent += '<th style="text-align:center"></th>';
					    		attendanceListContent += '<th style="text-align:center"></th>';
					    		attendanceListContent += '<th style="text-align:center"></th>';
					    		attendanceListContent += '<th style="text-align:center"></th>';
					    		attendanceListContent += '<th style="text-align:center"></th>';
					    		attendanceListContent += '<th style="text-align:center"></th>';
					    	attendanceListContent += '</tr></tfoot>';

					    	attendanceListContent += '<tbody>';

							$.each( ABEventRecorded, function(i, item) {

						        attendanceListContent +="<tr >";
						          attendanceListContent +="<td >"+date_moment(item.date)+"</td>";
						          attendanceListContent +="<td >"+getAtType(item.typeID)+"</td>";
						          attendanceListContent +="<td >"+item.name+"</td>";
						          attendanceListContent +="<td >"+item.Total_Present+"</td>";
						          attendanceListContent +="<td >"+item.Total_Late+"</td>";
						          attendanceListContent +="<td >"+item.Total_Absent+"</td>";
						          attendanceListContent +="<td >"+item.Total_Sick+"</td>";
						          attendanceListContent +="<td >"+item.Total_ExcusedAbsent+"</td>";
						          attendanceListContent +="<td>"+item.names+"</td>";
						        attendanceListContent +="</tr>";

						    });

						attendanceListContent +='</tbody></table>';
					    $('#AtNewAtRecordRightSideContent').html(attendanceListContent);

					    $('#AtRecordedTable').DataTable( {
				  		   paging: false ,
				          "aaSorting": [] ,
				          language: {
				                searchPlaceholder: "Search here... "
				          },
				          "footerCallback": function ( row, data, start, end, display ) {
				              var api = this.api(), data;
				 
				            // Remove the formatting to get integer data for summation
					            var intVal = function ( i ) {
					                return typeof i === 'string' ?
					                    i.replace(/[\$,]/g, '')*1 :
					                    typeof i === 'number' ?
					                        i : 0;
					            };

				 			// Present  
					            // Total over all pages
					                total = api
					                    .column( 3 )
					                    .data()
					                    .reduce( function (a, b) {
					                        return intVal(a) + intVal(b);
					                    }, 0 );

					            // Update footer
					                $( api.column( 3 ).footer() ).html(
					                    ' '+total+' '
					                );

					        // Absent 
					            // Total over all pages
					                total = api
					                    .column( 4 )
					                    .data()
					                    .reduce( function (a, b) {
					                        return intVal(a) + intVal(b);
					                    }, 0 );

					            // Update footer
					                $( api.column( 4 ).footer() ).html(
					                    ' '+total+' '
					                );

					        // Excused Absent 
					            // Total over all pages
					                total = api
					                    .column( 5 )
					                    .data()
					                    .reduce( function (a, b) {
					                        return intVal(a) + intVal(b);
					                    }, 0 );

					            // Update footer
					                $( api.column( 5 ).footer() ).html(
					                    ' '+total+' '
					                );

					        // Late   
					            // Total over all pages
					                total = api
					                    .column( 6 )
					                    .data()
					                    .reduce( function (a, b) {
					                        return intVal(a) + intVal(b);
					                    }, 0 );

					            // Update footer
					                $( api.column( 6 ).footer() ).html(
					                    ' '+total+' '
					                );

					        // Sick    
					            // Total over all pages
					                total = api
					                    .column( 7 )
					                    .data()
					                    .reduce( function (a, b) {
					                        return intVal(a) + intVal(b);
					                    }, 0 );

					            // Update footer
					                $( api.column( 7 ).footer() ).html(
					                    ' '+total+' '
					                );

				          }
				      } );

			    
		  }).done(function() {

		  		AtNewAtRecordValidate();
	      })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}

	function AtNewAtRecordValidate(){

		var formAtNewAtRecord = $('#formAtNewAtRecord');

	    formAtNewAtRecord.formValidation({
	      framework: 'bootstrap',
	      excluded: [':disabled' , ':hidden' ],
	      icon: {
	          valid: 'glyphicon glyphicon-ok',
	          invalid: 'glyphicon glyphicon-remove',
	          validating: 'glyphicon glyphicon-refresh'
	      },
	      fields: {
	        AtNewAtRecordType : {
	              validators: {
	                notEmpty: {
	                    message: 'Type is required'
	                }

	              }

	        },
	        AtNewAtRecordStudPer : {
	              validators: {
	                notEmpty: {
	                    message: 'Per is required'
	                }

	              }

	        },
	        AtNewAtRecordStaffPer : {
	              validators: {
	                notEmpty: {
	                    message: 'Per is required'
	                }

	              }

	        },
	        AtNewAtRecordClass : {
	              validators: {
	                notEmpty: {
	                    message: 'Class is required'
	                }

	              }

	        },
	        AtNewAtRecordClassSubject : {
	              validators: {
	                notEmpty: {
	                    message: 'Class - Subject is required'
	                }

	              }

	        },
	        AtNewAtRecordStudEvent : {
	              validators: {
	                notEmpty: {
	                    message: 'Student event is required'
	                }

	              }

	        },
	        AtNewAtRecordEventStaff : {
	              validators: {
	                notEmpty: {
	                    message: 'Staff event is required'
	                }

	              }

	        },
	        AtNewAtRecordDate : {
	              validators: {
	                notEmpty: {
	                    message: 'Date is required'
	                }

	              }

	        }

	      }

	    }).on('success.field.fv', function(e, data) {

	          if (data.fv.getInvalidFields().length <= 0) {   
	              data.fv.disableSubmitButtons(false);
	          }

	    })
	    .on('success.form.fv', function( e ) {

	        e.preventDefault();
	        submitAtNewAtRecord();

	    });

	}

	var AtEventAttendeePersonIDs = [] ;

	function submitAtNewAtRecord(){

		var AtNewAtRecordRightSide = $('#AtNewAtRecordRightSide');

		var content = '';
		content +='<div class="row">';
			content +='<div class="col-sm-12 text-center"><h4><strong>Attendance</strong></h4></div>'; 
		content +='</div>';
		content +='<div class="row" id="AtNewAtRecordRightSideContent">';
			content +='<div class="row text-center>Loading attendance list...</div>';
		content +='</div>';

		AtNewAtRecordRightSide.html(content);

		var frm = $('#formAtNewAtRecord');
		var url = base_url+"api/At/eventsAttendance"; 

	    $.post(url, 
         	frm.serialize(), 
         	function(data, status, xhr){

         	var attendanceListContent     = '';	
			    attendanceListContent += '<table class="table table-bordered table-striped" id="AtRecordContentTable" >';
			    	attendanceListContent += '<thead><tr>';
				    	attendanceListContent += '<th>#</th>';
				    	attendanceListContent += '<th>Names</th>';
				    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-blue">&nbsp;&nbsp;Present&nbsp;&nbsp;</span></span></th>';
				    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-green">&nbsp;&nbsp;Late&nbsp;&nbsp;</span></span></th>';
				    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-red">&nbsp;&nbsp;Absent&nbsp;&nbsp;</span></span></th>';
				    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-yellow">&nbsp;&nbsp;Sick&nbsp;&nbsp;</span></span></th>';
				    	attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-aqua">&nbsp;&nbsp;Excused Absent&nbsp;&nbsp;</span></span></th>';
						attendanceListContent += '<th class="text-center"><span style="writing-mode: vertical-rl; transform: rotate(180deg);"><span class="badge bg-gray">&nbsp;&nbsp;Not Recorded&nbsp;&nbsp;</span></span></th>';
			    	attendanceListContent += '</tr></thead>';

			    	attendanceListContent += '<tbody>';

			    	attendanceListContent +="<tr >";
			          attendanceListContent +="<td class='text-right' ></td>";
			          attendanceListContent +="<td class='text-right' >All Attendees</td>";
			          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_ALL_td_2' ><input id='AtAttendanceStatus_ALL_2' name='select_all' value='1' type='radio'>&nbsp;All</td>";
			          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_ALL_td_5' ><input id='AtAttendanceStatus_ALL_5' name='select_all' value='1' type='radio'>&nbsp;All</td>";
			          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_ALL_td_3' ><input id='AtAttendanceStatus_ALL_3' name='select_all' value='1' type='radio'>&nbsp;All</td>";
			          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_ALL_td_6' ><input id='AtAttendanceStatus_ALL_6' name='select_all' value='1' type='radio'>&nbsp;All</td>";
			          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_ALL_td_4' ><input id='AtAttendanceStatus_ALL_4' name='select_all' value='1' type='radio'>&nbsp;All</td>";
			          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_ALL_td_1' ><input id='AtAttendanceStatus_ALL_1' name='select_all' value='1' type='radio'>&nbsp;All</td>";
			        attendanceListContent +="</tr>";

			    	var RowIndex 	 = 1;

			    	var EventID 		  = data.EventID ;
			    	var AttendanceRecords = data.AttendanceRecords ;

			    	AtEventAttendeePersonIDs = [];

					$.each( AttendanceRecords, function(i, item) {

						AtEventAttendeePersonIDs.push(item.personID);

				        attendanceListContent +="<tr >";
				          attendanceListContent +="<td>"+RowIndex+"</td>";
				          attendanceListContent +="<td>"+item.names+"</td>";
				          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_td_ID"+item.id+"_2' ><input id='AtAttendanceStatus_ID"+item.id+"_2' class='AtAttendanceStatus AtAttendanceStatus_2' name='AtAttendanceStatusID["+item.personID+"]' value='2' type='radio'></td>";
				          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_td_ID"+item.id+"_5' ><input id='AtAttendanceStatus_ID"+item.id+"_5' class='AtAttendanceStatus AtAttendanceStatus_5' name='AtAttendanceStatusID["+item.personID+"]' value='5' type='radio'></td>";
				          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_td_ID"+item.id+"_3' ><input id='AtAttendanceStatus_ID"+item.id+"_3' class='AtAttendanceStatus AtAttendanceStatus_3' name='AtAttendanceStatusID["+item.personID+"]' value='3' type='radio'></td>";
				          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_td_ID"+item.id+"_6' ><input id='AtAttendanceStatus_ID"+item.id+"_6' class='AtAttendanceStatus AtAttendanceStatus_6' name='AtAttendanceStatusID["+item.personID+"]' value='6' type='radio'></td>";
				          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_td_ID"+item.id+"_4' ><input id='AtAttendanceStatus_ID"+item.id+"_4' class='AtAttendanceStatus AtAttendanceStatus_4' name='AtAttendanceStatusID["+item.personID+"]' value='4' type='radio'></td>";
				          attendanceListContent +="<td class='text-center' id='AtAttendanceStatus_td_ID"+item.id+"_1' ><input id='AtAttendanceStatus_ID"+item.id+"_1' class='AtAttendanceStatus AtAttendanceStatus_1' name='AtAttendanceStatusID["+item.personID+"]' value='1' type='radio'></td>";
				        attendanceListContent +="</tr>";

				        RowIndex++;

				    });

				attendanceListContent +='</tbody></table>';
			    $('#AtNewAtRecordRightSideContent').html(attendanceListContent);

			    //
			    	$('#AtRecordContentTable').DataTable( {
				          paging: false ,
				          "aaSorting": [] ,
				          language: {
				                searchPlaceholder: "Search here... "
				          }
				      } );

			    //Set Record from server 
				    $.each( AttendanceRecords, function(i, item) {
				    	$('#AtAttendanceStatus_ID'+item.id+'_'+item.attRcdStatusID ).attr('checked', 'checked');
				    });
                
                //ALL
	                //TD 
	                	$( "#AtAttendanceStatus_ALL_td_2" ).click(function() {
							  $('#AtAttendanceStatus_ALL_2' ).attr('checked', 'checked');
							  $('.AtAttendanceStatus_2' ).attr('checked', 'checked');
							  attendanceRecordSave(EventID, 2, true, null );
						});

	                	$( "#AtAttendanceStatus_ALL_td_5" ).click(function() {
							  $('#AtAttendanceStatus_ALL_5' ).attr('checked', 'checked');
							  $('.AtAttendanceStatus_5' ).attr('checked', 'checked');
							  attendanceRecordSave(EventID, 5, true, null );
						});

						$( "#AtAttendanceStatus_ALL_td_3" ).click(function() {
							  $('#AtAttendanceStatus_ALL_3' ).attr('checked', 'checked');
							  $('.AtAttendanceStatus_3' ).attr('checked', 'checked');
							  attendanceRecordSave(EventID, 3, true, null );
						});

						$( "#AtAttendanceStatus_ALL_td_6" ).click(function() {
							  $('#AtAttendanceStatus_ALL_6' ).attr('checked', 'checked');
							  $('.AtAttendanceStatus_6' ).attr('checked', 'checked');
							  attendanceRecordSave(EventID, 6, true, null );
						});

	                	$( "#AtAttendanceStatus_ALL_td_4" ).click(function() {
							  $('#AtAttendanceStatus_ALL_4' ).attr('checked', 'checked');
							  $('.AtAttendanceStatus_4' ).attr('checked', 'checked');
							  attendanceRecordSave(EventID, 4, true, null );
						});

						$( "#AtAttendanceStatus_ALL_td_1" ).click(function() {
							  $('#AtAttendanceStatus_ALL_1' ).attr('checked', 'checked');
							  $('.AtAttendanceStatus_1' ).attr('checked', 'checked');
							  attendanceRecordSave(EventID, 1, true, null );
						});

					//Radio
		                $( "#AtAttendanceStatus_ALL_2" ).change(function() {
			               $('.AtAttendanceStatus_2' ).attr('checked', 'checked');
			            });

		                $( "#AtAttendanceStatus_ALL_5" ).change(function() {
		                	$('.AtAttendanceStatus_5' ).attr('checked', 'checked');
			            });

		                $( "#AtAttendanceStatus_ALL_3" ).change(function() {
		                	$('.AtAttendanceStatus_3' ).attr('checked', 'checked');
			            });

		                $( "#AtAttendanceStatus_ALL_6" ).change(function() {
		                	$('.AtAttendanceStatus_6' ).attr('checked', 'checked');
			            });

			            $( "#AtAttendanceStatus_ALL_4" ).change(function() {
			            	$('.AtAttendanceStatus_4' ).attr('checked', 'checked');
			            });

		                $( "#AtAttendanceStatus_ALL_1" ).change(function() {
		                	$('.AtAttendanceStatus_1' ).attr('checked', 'checked');
			            });

		        //Single 
		        	//td 
	                	$.each( AttendanceRecords, function(i, item) {
					    	
					    	$( "#AtAttendanceStatus_td_ID"+item.id+"_2" ).click(function() {
					    		attendanceUncheckAll();
					    		$('#AtAttendanceStatus_ID'+item.id+'_2' ).attr('checked', 'checked');
					    		attendanceRecordSave(EventID, 2, false, item.personID );
							});

		                	$( "#AtAttendanceStatus_td_ID"+item.id+"_5" ).click(function() {
		                		attendanceUncheckAll();
							  	$('#AtAttendanceStatus_ID'+item.id+'_5' ).attr('checked', 'checked');
							  	attendanceRecordSave(EventID, 5, false, item.personID );
							});

							$( "#AtAttendanceStatus_td_ID"+item.id+"_3" ).click(function() {
								attendanceUncheckAll();
							  	$('#AtAttendanceStatus_ID'+item.id+'_3' ).attr('checked', 'checked');
							  	attendanceRecordSave(EventID, 3, false, item.personID );
							});

							$( "#AtAttendanceStatus_td_ID"+item.id+"_6" ).click(function() {
								attendanceUncheckAll();
							  	$('#AtAttendanceStatus_ID'+item.id+'_6' ).attr('checked', 'checked');
							  	attendanceRecordSave(EventID, 6, false, item.personID );
							});

		                	$( "#AtAttendanceStatus_td_ID"+item.id+"_4" ).click(function() {
		                		attendanceUncheckAll();
							  	$('#AtAttendanceStatus_ID'+item.id+'_4' ).attr('checked', 'checked');
							  	attendanceRecordSave(EventID, 4, false, item.personID );
							});

							$( "#AtAttendanceStatus_td_ID"+item.id+"_1" ).click(function() {
								attendanceUncheckAll();
							  	$('#AtAttendanceStatus_ID'+item.id+'_1' ).attr('checked', 'checked');
							  	attendanceRecordSave(EventID, 1, false, item.personID );
							});

					    });

         	},"json")
          	.done(function() {

          	})
          	.fail(function() {

          	})
          	.always(function() { 

          	});

	}

	function attendanceUncheckAll(){

		$('#AtAttendanceStatus_ALL_2' ).removeAttr('checked');
        $('#AtAttendanceStatus_ALL_5' ).removeAttr('checked');
        $('#AtAttendanceStatus_ALL_3' ).removeAttr('checked');
        $('#AtAttendanceStatus_ALL_6' ).removeAttr('checked');
        $('#AtAttendanceStatus_ALL_4' ).removeAttr('checked');
        $('#AtAttendanceStatus_ALL_1' ).removeAttr('checked');
	}

	function attendanceRecordSave(EventID, Status, IsAll, PersonID ){

		var ABEventPersonID = null ;

		if ( IsAll ) {
			ABEventPersonID = AtEventAttendeePersonIDs;

		}else{
			ABEventPersonID = PersonID;
		}

		var url = base_url+"api/At/eventsAttendance/"+EventID+"?Status="+Status+"&IsAll="+IsAll+"&ABEventPersonID="+ABEventPersonID ;

        $.ajax({
          url: url,
          dataType: 'json',
          type: 'PUT', 
          data: null,
          success: function ( res ) {

          	console.log(res);
           
          }

        }).done(function() { 

        }).fail(function() {

        }).always(function() {
            
        });

	}

	function loadAttendanceAnalytics()
	{

		var container          = $( "#dashAttendance_content" ); 
	 	container.empty();

	 	var content = '';
	 	content +='<div  class="col-sm-12">';

			//content +='<div  class="col-sm-4 ">';
			// 	content +='<div class="row text-center">';
			// 		content +='<h4>Statistics</h4>';
			// 	content +='</div>';
			// 	content +='<form id="formDashGetAnalytics" class="form-horizontal top-buffer" role="form" >';
			// 		content +='<div class="form-group">';
			// 			content +='<label class="col-sm-3 control-label"></label>';
			// 			content +='<div class="col-sm-2 "></div>';
			// 			content +='<div class="col-sm-3 radio">';
			// 				content +='<label><input type="radio" name="DashAnFilter"  value="1" checked > Student</label>';
			// 			content +='</div>';
			// 			content +='<div class="col-sm-3 radio">';
			// 				content +='<label><input type="radio" name="DashAnFilter"  value="0" > Staff</label>';
			// 			content +='</div>';
			// 		content +='</div>';
			// 		content +='<div class="form-group">';
			// 			content +='<label class="col-sm-3 control-label">Per</label>';
			// 			content +='<div class="col-sm-1 "></div>';
			// 			content +='<div class="col-sm-2 radio">';
			// 				content +='<label><input type="checkbox" name="DashAnFilter"  value="1" checked > Day</label>';
			// 			content +='</div>';
			// 			content +='<div class="col-sm-2 radio">';
			// 				content +='<label><input type="checkbox" name="DashAnFilter"  value="0" checked > Period</label>';
			// 			content +='</div>';
			// 			content +='<div class="col-sm-2 radio">';
			// 				content +='<label><input type="checkbox" name="DashAnFilter"  value="2" checked > Events</label>';
			// 			content +='</div>';
			// 		content +='</div>';
			// 		content +='<div class="form-group">';
			// 			content +='<label class="col-sm-3 control-label">Class</label>';
			// 			content +='<div class="col-sm-7">';
			// 				content +='<select name="DashAnClass" id="DashAnClassSelect" class="form-control"  >';
			// 					content +='<option value="0">All</option>';
			// 				content +='</select>';
			// 			content +='</div>';
			// 		content +='</div>';
			// 		content +='<div class="form-group">';
			// 			content +='<label class="col-sm-3 control-label">From</label>';
			// 			content +='<div class="col-sm-7">';
			// 				content +='<input class="form-control AtAnalyticsFrom" >';
			// 			content +='</div>';
			// 		content +='</div>';
			// 		content +='<div class="form-group">';
			// 			content +='<label class="col-sm-3 control-label">To</label>';
			// 			content +='<div class="col-sm-7">';
			// 				content +='<input class="form-control AtAnalyticsTo" >';
			// 			content +='</div>';
			// 		content +='</div>';
			// 		content +='<div class="form-group top-buffer">';
			// 			content +='<label class="col-sm-3 control-label"></label>';
			// 			content +='<div class="col-sm-7 text-center">';
			// 				content +='<button class="btn btn-lg btn-primary btn-block" type="submit">View Attendance Statistics</button>';
			// 			content +='</div>';
			// 		content +='</div>';
			// 	content +='</form>';
			// content +='</div>';
			content +='<div  class="col-sm-8 col-sm-offset-2">';
				content +='<div class="row">';
					content +='<div class="col-sm-12 text-center"><h4><strong>Top missing students</strong></h4></div>';
				content +='</div>';
				content +='<div class="row">';
					content +='<div class="col-sm-12 text-center" id="AtNewAtRecordRightSidePeriod"></div>';
				content +='</div>';
				content +='<div class="row top-buffer" id="AtNewAtRecordRightSideContent">';
					content +='<div class="row text-center>Loading Statistics...</div>';
				content +='</div>';
			content +='</div>';
		content +='</div>';

		container.html(content);

		$('.AtAnalyticsFrom').datepicker();
		$('.AtAnalyticsTo').datepicker().datepicker("setDate", new Date());

		var url = base_url+"api/At/eventsAttendance";

		  $.getJSON( url, function(data) {
			    
		    var ABEventRecorded 	 = data.ABEventRecorded;
		    var SchoolTerm_startDate = data.SchoolTerm_startDate;
		    var SchoolTerm_endDate 	 = data.SchoolTerm_endDate;

		    var attendanceListContent     = '';	
			    attendanceListContent += '<table class="table table-bordered table-striped" id="AtRecordedMissingTable" >';
			    	attendanceListContent += '<thead><tr>';
				    	attendanceListContent += '<th>#</th>';
						attendanceListContent += '<th>Names</th>';
						attendanceListContent += '<th>Reg. Number</th>';
						attendanceListContent += '<th>Class</th>';
						attendanceListContent += '<th class="text-center">Number of absents</th>';
			    	attendanceListContent += '</tr></thead>';

			    	attendanceListContent += '<tbody>';

			    	var rowNumber = 1 ;

					$.each( ABEventRecorded, function(i, item) {

				        attendanceListContent +="<tr >";
				          attendanceListContent +="<td >"+rowNumber+"</td>";
				          attendanceListContent +="<td >"+item.names+"</td>";
				          attendanceListContent +="<td >"+item.studRegNber+"</td>";
				          attendanceListContent +="<td >"+item.classroomName+"</td>";
				          attendanceListContent +="<td class='text-right' >"+item.Total_Absent+"</td>";
				        attendanceListContent +="</tr>";
				        rowNumber++;

				    });

				attendanceListContent +='</tbody></table>';
				
				$('#AtNewAtRecordRightSidePeriod').html("<h5>From: <i>"+date_moment(SchoolTerm_startDate)+"</i> To: <i>"+date_moment(SchoolTerm_endDate)+"</i></h5>");
			    $('#AtNewAtRecordRightSideContent').html(attendanceListContent);

			    abTablePagingTable('AtRecordedMissingTable');

		  }).done(function() {
	      })
	      .fail(function() {
	      })
	      .always(function() {
	      });

	}

	function getAtType( status )
	{
		var content ='';

		switch(status) {
		    case "1":
		    	content = '<span class="badge bg-light-blue"> Student Daily</span>';
		        break;

		    case "2":
		       content = '<span class="badge bg-blue"> Student Periodic</span>';
		        break;

	        case "3":
	        	content = '<span class="badge bg-green"> Staff Daily</span>';
		        break;

		    case "4":
	        	content = '<span class="badge bg-navy"> Event</span>';
		        break;
		}

		return content;
	}

	// "columnDefs": [
 //            {
 //                "targets": [ 8, 9, 10, 11, 12, 13 ],
 //                "visible": false,
 //                "searchable": false
 //            }
 //    ],
			            



function getDispline(){
	$("#moduleContainer").load("displine/main", function() { get_mn__sanctions(); } ).fadeIn(1000);

}

function get_mn__sanctions()
{
	$("#moduleMain").load("displine/mn__sanctions", function() { get_subMenu_displine();  get_record_sanctions(); } ).fadeIn(1000);
}

function get_mn_studentsDisplinePoints()
{
	$("#moduleMain").load("displine/mn_studentsDisplinePoints", function() { get_subMenu_displine(); } ).fadeIn(1000);
}

function get_mn_faults()
{
	$("#moduleMain").load("displine/mn_faults", function() { EmpltySubMenu(); } ).fadeIn(1000);
}

function get_mn_defaultStudentDisplinePoints()
{
	$("#moduleMain").load("displine/mn_defaultStudentDisplinePoints", function() { EmpltySubMenu(); } ).fadeIn(1000);
}

function get_subMenu_displine ()
{
	$("#subMenu").load("displine/subMenu_Displine").fadeIn(1000);
}

function get_record_sanctions(){
	$("#record_displine").load("displine/record_sanctions").fadeIn(1000);
}

function get_record_studentDispline()
{
	$("#record_displine").load("displine/record_studentDispline").fadeIn(1000);
}

function EmpltySubMenu()
{
	$('#subMenu').html("");
}

function get_studentDispline(regnumber, person, regnumberNames, photoID){

	var container        = $( "#dashDispline_content" );
	container.empty();   

  var url = base_url+"api/Di/StudentsDisplinePoints/"+regnumber; 

  var LeftContainer 	 = '';
  var RightContainer   = '';

  $.getJSON( url, function(data) {

      var Points        = data.points;
      var Deduction     = data.Deduction;

      var TotalDeducted = 0;
      var TotalRewarded = 0;

      RightContainer   += '<div class="col-sm-7"><div class="row text-center"><h4><strong>Discipline Records</strong></h4></div><div class="row top-buffer"><table class="table abDataTable table-hover" id="dashDiStudentDeductedTable" ><thead><tr><th class="text-center"> Date</th><th class="text-center"> Metric</th><th class="text-center"> Points</th><th class="text-center"> Comments</th></tr></thead><tbody>';

          $.each(Deduction, function(i, item) {

              RightContainer  +='<tr id="'+item.id+'" regnumber="'+regnumber+'" >';
                RightContainer  +='<td class="text-center">'+date_moment(item.date)+'</td>';
                RightContainer  +='<td class="text-left">'+item.faultName+'</td>';
                RightContainer  +='<td class="text-center">';
                  RightContainer += ( item.faultType == 0 ) ? "<span class='text-red'>- "+item.deductedPoint+"</span>" : "<span class='text-blue'>+ "+item.deductedPoint+"</span>";
                RightContainer  +='</td>';
                RightContainer  +='<td class="text-center"><span class="text-blue"><i class="fa fa-comment"></i> '+item.commentNumber+'</span></td>'; 
              RightContainer  +='</tr>';

              if ( item.faultType == 0 ) {
                TotalDeducted = parseFloat(TotalDeducted) + parseFloat(item.deductedPoint);

              }else if( item.faultType == 1 ){
                TotalRewarded = parseFloat(TotalRewarded) + parseFloat(item.deductedPoint);

              }

              
          });
          
          RightContainer  +='</tbody></table></div></div>';

      var currrentDisplinePoints =  parseFloat(Points) - parseFloat(TotalDeducted) + parseFloat(TotalRewarded) ;

       LeftContainer  += '<div class="col-sm-3">';
     		LeftContainer  += '<div class="row"><div class="top-buffer"><center><div class="row"><img src="images/student/200/'+person+'/'+photoID+'" class="img-circle thumbnail" alt=""></div><div class="row"><h4>'+regnumberNames+'</h4></div></center></div></div>';
      	LeftContainer  += '<div class="row top-buffer">';
      		LeftContainer  += '<div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title ">Discipline Points</span><span class="value ">'+currrentDisplinePoints+'</span><span >&nbsp;</span></div></div></div>';
      		LeftContainer  += '<div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title text-red">Total Deducted</span><span class="value text-red">'+TotalDeducted+'</span><span >&nbsp;</span></div></div></div>';
      	  LeftContainer  += '<div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title text-green">Total Rewarded</span><span class="value text-green">'+TotalRewarded+'</span><span >&nbsp;</span></div></div></div>';
        LeftContainer  += '</div>';
      LeftContainer  += '</div>';

  })
  .done(function() {

  	var AllTogether = ''+LeftContainer+'<div class="col-sm-1"></div>'+RightContainer+'';
        container.append(AllTogether);
        container.show();

	      $("#dashContentainer_Displine").slideDown();

        abDataTable();
        dashDiStudentSanctionComments();

  })
  .fail(function() {
  })
  .always(function() {
  });

}

function DashDiSelectAllFaults( isDoubleRecord ){

	   var container            =  $( "#dashDispline_content" );
	   container.empty();   

        var url = base_url+"api/Di/Faults/"; 

        var FaultContainer 	 = '';

        $.getJSON( url, function(data) {

          FaultContainer   += '<div class="col-sm-8 col-sm-offset-2">';
            FaultContainer   += '<div class="row text-center"><h4><strong>';

              if (isDoubleRecord) {
                FaultContainer   += 'Metrics';
              }else{  
                FaultContainer   += 'Faults';
              }

            FaultContainer   += '</strong></h4></div>';
              FaultContainer   += '<div class="row"><table class="table abDataTable table-hover" ><thead><tr><th class="text-left">Name</th><th class="text-left"> Points</th></tr></thead><tbody>';

                $.each(data, function(i, item) {

                    FaultContainer  +='<tr>';
                      FaultContainer  +='<td >'+item.faultName+'</td>';
                      FaultContainer  +='<td class="text-left">';
                         FaultContainer += ( item.faultType == 0 ) ? "<span class='text-red'>- "+item.sanctionPoint+"</span>" : "<span class='text-blue'>+ "+item.sanctionPoint+"</span>";
                      FaultContainer  +='</td>';
                    FaultContainer  +='</tr>';
                });
            FaultContainer  +='</tbody></table></div>';
          FaultContainer  +='</div>';

        })
        .done(function() {

            container.append(FaultContainer);
            container.show();

 			$("#dashContentainer_Displine").slideDown();

            abDataTable();

        })
        .fail(function() {
        })
        .always(function() {
        });

}

var DiClassSubject = [[]];
var DiMetrics      = [[]];

function DashDiConduct(){

  var container            =  $( "#dashDispline_content" );
  container.empty();   

  var dashDispline_content  = $("#dashDispline_content");
  var url = base_url+"api/Di/Conduct"; 

  var LeftContainer    = '';
  var RightContainer   = '';

  $.getJSON( url, function(data) {

      var Classrooms        = data.classrooms ;
      var ClassroomsSubjects= data.Subjects ;
      var Metrics           = data.Metrics ;

      var ClassroomsSize         = Classrooms.length ;
      var ClassroomsSubjectsSize = ClassroomsSubjects.length ;
      var MetricsSize            = Metrics.length ;

      //DiMetrics
        DiMetrics      = [] ;

        DiMetrics[0] = new Array();
        DiMetrics[1] = new Array();

        for ( var i = 0; i < MetricsSize; i++) {

          var Metric_id         = Metrics[i].id;
          var Metric_name       = Metrics[i].name;
          var Metric_isGeneral  = Metrics[i].isGeneral;

          var row = [];
          
          row['id']    = Metric_id;
          row['name']  = Metric_name;

          DiMetrics[Metric_isGeneral].push(row);

        } 

      //DiClassSubject
      DiClassSubject = [[]];

      for ( var i = 0; i < ClassroomsSize; i++) {

        DiClassSubject[Classrooms[i].id] = new Array();

      }

      for ( var i = 0; i < ClassroomsSubjectsSize; i++ ) {

        var subjectid   = ClassroomsSubjects[i].id;
        var classId     = ClassroomsSubjects[i].ClassID;
        var subjectname = ClassroomsSubjects[i].SubjectName;

        var row = [];

        row['id']           = subjectid;
        row['subjectname']  = subjectname;

        DiClassSubject[classId].push(row);
      }

      var AnClassOptions    = "<option ></option>";
      var MetricsOptions    = "<option ></option>";

      var Anclassidentifier = "";
      var AnisTheFirsRecord = true;

      for ( var i=0; i< ClassroomsSize ; i++ ) {

        if ( Classrooms[i].classidentifier == Anclassidentifier ){
            AnClassOptions += '<option value="'+Classrooms[i].id+'" data-yearname="'+Classrooms[i].YearName+'" data-level="'+Classrooms[i].levelID+'" >'+Classrooms[i].name+'</option>';
        
        }else{

            Anclassidentifier = Classrooms[i].classidentifier;
            if ( AnisTheFirsRecord ){

              AnisTheFirsRecord = false; 

            }else{
              AnClassOptions += '</optgroup>';
            }

            AnClassOptions += '<optgroup label="'+Classrooms[i].YearName+'">';
            AnClassOptions += '<option value="'+Classrooms[i].id+'" data-yearname="'+Classrooms[i].YearName+'" data-level="'+Classrooms[i].levelID+'" >'+Classrooms[i].name+'</option>';
      
        }
              
      }

      for ( var i=0; i< MetricsSize ; i++ ) {

        MetricsOptions += '<option value="'+Metrics[i].id+'" >'+Metrics[i].name+'</option>';
      
      }

      LeftContainer  += '<div class="col-sm-4">';
        LeftContainer   += '<div class="row text-center"><h4>Student Conduct</h4></div>';
          LeftContainer   += '<form  id="formDashDiConduct" class="form-horizontal top-buffer" role="form" >';
            LeftContainer   += '<div class="form-group"><label class="col-sm-3 control-label"></label><div class="col-sm-1"></div><div class="col-sm-7"><div class="radio"><label><input type="radio" name="DiConductType" value="1" /> General</label></div><div class="radio"><label><input type="radio" name="DiConductType" value="0" /> By Subject</label></div></div></div>';
            LeftContainer   += '<div class="form-group"><label class="col-sm-3 control-label">Class</label><div class="col-sm-7"><select name="DiConductClass" id="selectDiConductClass" class="form-control select2-input" >'+AnClassOptions+'</select></div></div>';
            LeftContainer   += '<div class="form-group" id="DiConductSubjectContainer" ><label class="col-sm-3 control-label">Subject</label><div class="col-sm-7"><select name="DiConductSubject" id="selectDiConductSubject" class="form-control select2-input" ></select></div></div>';
            LeftContainer   += '<div class="form-group"><label class="col-sm-3 control-label">Metric</label><div class="col-sm-7"><select name="DiConductMetric" id="selectDiConductMetric" class="form-control select2-input" >'+MetricsOptions+'</select></div></div>';
            LeftContainer   += '<div class="form-group top-buffer" id="DiConductLoadingContainer" ><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-default btn-block" >Loading...</button></div></div>';
            LeftContainer   += '<div class="form-group top-buffer" id="GrConductSubmitContainer"><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-primary btn-block" type="submit" id="DiConductSubmit" >View Students\' Conduct</button></div></div>';
            LeftContainer   += '<div class="form-group" id="GrConductInfoContainer"><label class="col-sm-12 control-label"><p class="text-center text-info "> <i class="fa fa-info-circle"></i>Remember to cick on button above anytime you change Metric</span></p></label></div>';
         LeftContainer   += '</form>';
        LeftContainer   += '</div>';
      LeftContainer  += '</div>';

      RightContainer   += '<div class="col-sm-8 pull-left"><div id="dashDiSelectDiConductScore" class="ab_DiConduct_SlickGrid" style="width:650px;"></div>';


  })
  .done(function() {

      var AllTogether = ''+LeftContainer+'<div class="col-sm-1"></div>'+RightContainer+'';
      container.append(AllTogether);
      container.show();

      $("#DiConductLoadingContainer").hide();
      $("#dashContentainer_Displine").slideDown();

      var DiConductSubjectContainer = $("#DiConductSubjectContainer");
      DiConductSubjectContainer.hide();

      $('input[name|="DiConductType"]').change(function() {
        
        var selected = $(this).val();
       
        var MetricsTypes      = DiMetrics[selected];
        var MetricsTypesSize  = MetricsTypes.length;

        var MetricsTypesOptions  = "";
            MetricsTypesOptions += "<option ></option>";

        for ( var i=0; i< MetricsTypesSize ; i++ ) {

            MetricsTypesOptions += '<option value="'+MetricsTypes[i].id+'" >'+MetricsTypes[i].name+'</option>';   
        }

        var selectDiConductMetric  = $("#selectDiConductMetric");
        selectDiConductMetric.html( MetricsTypesOptions );
        selectDiConductMetric.select2('data', null).trigger('change');

        if ( selected == 1 )
         {

          DiConductSubjectContainer.hide();

         }else {

          DiConductSubjectContainer.slideDown();

         }

      });

      $("#selectDiConductClass").select2({ 
              formatResult: newStudentformatResult,
              formatSelection: newStudentformatSelection
      }).bind('change', onDiConductClassChange);

      $("#selectDiConductSubject").select2();
      $("#selectDiConductMetric").select2().bind('change', onDiConductMetricChange);
      $("#GrConductInfoContainer").hide();
      
      DiConductFormValidate();

  })    
  .fail(function() {
  })
  .always(function() {
  });

}

function onDiConductMetricChange(){

    $("#GrConductInfoContainer").fadeIn(1000).delay(5000).fadeOut(2000);

}

function onDiConductClassChange(){

    var sel                   = $("#selectDiConductClass");
    var selectDiConductMetric = $("#selectDiConductMetric");
    
    var selected  = sel.select2('val');

    var ClassSubjects     = DiClassSubject[selected];
    var ClassSubjectsSize = ClassSubjects.length;

    var ClassSubjectsOptions  = "";
        ClassSubjectsOptions += "<option ></option>";

    for ( var i=0; i< ClassSubjectsSize ; i++ ) {

        ClassSubjectsOptions += '<option value="'+ClassSubjects[i].id+'" >'+ClassSubjects[i].subjectname+'</option>';   
    }

    var selectDiConductSubject  = $("#selectDiConductSubject");
    selectDiConductSubject.html( ClassSubjectsOptions );
    selectDiConductSubject.select2('data', null).trigger('change');
    selectDiConductMetric.select2('data', null).trigger('change');
}

function DiConductFormValidate()
{
  //validation
  var formDashDiConduct = $('#formDashDiConduct');

  formDashDiConduct.formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        exclude: ':disabled',
        fields: {
            DiConductType: {
                validators: {
                    notEmpty: {
                        message: 'Conduct Type is required'
                    }
                }
            },
            DiConductClass: {
                validators: {
                    notEmpty: {
                        message: 'Class is required'
                    }
                }
            },
            DiConductSubject: {
                validators: {
                    notEmpty: {
                        message: 'Subject is required'
                    }
                }
            },
            DiConductMetric: {
                validators: {
                    notEmpty: {
                        message: 'Metric is required'
                    }
                }
            }
        }
    })
  .on('success.form.fv', function(e , data) {

  e.preventDefault();

    onDashLoadDiClassMetricCondactContent();

    var DiConductSubmit = $('#DiConductSubmit');
    DiConductSubmit.removeClass('disabled'); 
    DiConductSubmit.removeAttr('disabled');
  
  }).end();

}

function onDashLoadDiClassMetricCondactContent()
{     
      var DiConductLoadingContainer = $("#DiConductLoadingContainer");
      var GrConductSubmitContainer  = $("#GrConductSubmitContainer");

      GrConductSubmitContainer.hide();
      DiConductLoadingContainer.slideDown();

      var dashDiSelectDiConductScore =  $('#dashDiSelectDiConductScore');

      var frm = $('#formDashDiConduct');
      var url = base_url+"api/Di/Conduct"; 

      var content = ""; 

      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){

          var ClassMetrics_Type           = data.Type ;
          var ClassMetrics_ClassID        = data.ClassID ;
          var ClassMetrics_SubjectID      = data.SubjectID ;
          var ClassMetrics_MetricID       = data.MetricID ;
          var ClassMetrics_BehaviourName  = data.BehaviourName ;
          var ClassMetrics_Data           = data.Data ;

          content += '<div class="row text-center"><h4>'+ClassMetrics_BehaviourName+'</h4></div><div class="row"><table class="table table-striped" id="dashDiMetricCondactTable" ><thead><tr class="text-center"><th>Student Name</th><th class="text-center">Reg. Number</th><th class="text-center">Excellent</th><th class="text-center">Good</th><th class="text-center">Satisfactory</th><th class="text-center">Cause of Concern</th></tr></thead><tbody>';

            $.each( ClassMetrics_Data, function(i, item) {

                content +="<tr di_type='"+ClassMetrics_Type+"' di_classid='"+ClassMetrics_ClassID+"' di_subject='"+ClassMetrics_SubjectID+"' di_metric="+ClassMetrics_MetricID+" >";
                  content +="<td>"+item.StudentNames+"</td>";
                  content +="<td class='text-center'>"+item.RegNumber+"</td>";

                  if ( item.Status == 1 ) {
                    content +="<td class='text-center'><input type='radio' class='studentMetricStatus' name='studentMetricStatus["+item.RegNumber+"]' value='1' checked /></td>";
                  }else{
                    content +="<td class='text-center'><input type='radio' class='studentMetricStatus' name='studentMetricStatus["+item.RegNumber+"]' value='1' /></td>";
                  }

                  if ( item.Status == 2 ) {
                     content +="<td class='text-center'><input type='radio' class='studentMetricStatus' name='studentMetricStatus["+item.RegNumber+"]' value='2' checked /></td>";

                  }else{
                     content +="<td class='text-center'><input type='radio' class='studentMetricStatus' name='studentMetricStatus["+item.RegNumber+"]' value='2' /></td>";

                  }

                  if ( item.Status == 3 ) {
                    content +="<td class='text-center'><input type='radio' class='studentMetricStatus' name='studentMetricStatus["+item.RegNumber+"]' value='3' checked /></td>";
                  }else{
                    content +="<td class='text-center'><input type='radio' class='studentMetricStatus' name='studentMetricStatus["+item.RegNumber+"]' value='3' /></td>";
                  }

                  if ( item.Status == 4 ) {
                    content +="<td class='text-center'><input type='radio' class='studentMetricStatus' name='studentMetricStatus["+item.RegNumber+"]' value='4' checked /></td>";
                  }else{
                    content +="<td class='text-center'><input type='radio' class='studentMetricStatus' name='studentMetricStatus["+item.RegNumber+"]' value='4' /></td>";
                  }
                  
                  content +="</td>";
                content +="</tr>";

              });
          
            content +='</tbody></table></div>';

            dashDiSelectDiConductScore.html( content );

            $( ".studentMetricStatus" ).change(function() {

                Input_Name  = $(this).attr("name") ;
                Input_Value = $(this).val() ;
                ClassMetrics_SubjectID

                var radioButtons = $(this) ;

                var selectedIndex = radioButtons.index(radioButtons.filter(':checked'));

                  var url     = base_url+"api/Di/Conduct/"+ClassMetrics_MetricID+"?Type="+ClassMetrics_Type+"&ClassID="+ClassMetrics_ClassID+"&SubjectID="+ClassMetrics_SubjectID+"&Name="+Input_Name+"&Value="+Input_Value;
                  $.ajax({
                        url: url,
                        type: 'PUT',
                        success: function(data) {

                    },
                    error: function(){

                          $.gritter.add({
                            title: 'Failed',
                            text: 'Failed to delete.',
                            class_name: 'danger gritter-center',
                            time: ''
                          });
                    }

                });
      
            });
             
     },"json")
      .done(function() {
        
      })
      .fail(function() {

          $.gritter.add({
            title: 'Failed',
            text: 'Something went wrong, Please Try Again',
            class_name: 'danger gritter-center',
            time: ''
          });

      })
      .always(function() {
        
        DiConductLoadingContainer.hide();
        GrConductSubmitContainer.slideDown();

      });           
}

function DiValidateNewDeducation( isDoubleRecord )
{

    var displineNewDeduction = $('#displineNewDeduction');
    displineNewDeduction.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                NewDeductionType: {
                    validators: {
                        notEmpty: {
                            message: 'Type is required'
                        }
                    }
                },
                'student[]': {
                    validators: {
                        notEmpty: {
                            message: 'Metric name is required'
                        }
                    }
                },
                'classes[]': {
                    validators: {
                        notEmpty: {
                            message: 'Points are required'
                        }
                    }
                },
                fault: {
                    validators: {
                        notEmpty: {
                            message: 'Metric is required'
                        }
                    }
                },
                points: {
                    validators: {
                        notEmpty: {
                            message: 'Points are required'
                        },
                        numeric: {
                            message: 'Points should be numeric'
                        }
                    }
                }
            }
        })
    .on('success.form.fv', function(e , data) {

        e.preventDefault();
        submitDiNewDeducation(isDoubleRecord);

     });

}

function DiValidateNewFault( isDoubleRecord ){

  //validation
    var displineNewFault = $('#displineNewFault');

    if ( isDoubleRecord ) {

        displineNewFault.formValidation({
              framework: 'bootstrap',
              icon: {
                  valid: 'glyphicon glyphicon-ok',
                  invalid: 'glyphicon glyphicon-remove',
                  validating: 'glyphicon glyphicon-refresh'
              },
              exclude: ':disabled',
              fields: {
                  faultType: {
                      validators: {
                          notEmpty: {
                              message: 'Metric Type is required'
                          }
                      }
                  },
                  faultName: {
                      validators: {
                          notEmpty: {
                              message: 'Metric name is required'
                          }
                      }
                  },
                  sanctionPoint: {
                      validators: {
                          notEmpty: {
                              message: 'Points are required'
                          },
                          numeric: {
                              message: 'Points should be numeric'
                          }
                      }
                  }
              }
          })
      .on('success.form.fv', function(e , data) {

          e.preventDefault();
          submitDiNewFault(isDoubleRecord);

       });

    }else{

        displineNewFault.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                faultType: {
                    validators: {
                        notEmpty: {
                            message: 'Fault name is required'
                        }
                    }
                },
                faultName: {
                    validators: {
                        notEmpty: {
                            message: 'Points to deduct are required'
                        }
                    }
                }
            }
        })
    .on('success.form.fv', function(e , data) {

        e.preventDefault();
        submitDiNewFault(isDoubleRecord);

     });

    }

}

function submitDiNewFault(isDoubleRecord)
{

  var displineNewFaultLoading = $("#displineNewFaultLoading");
  var displineNewFault        = $('#displineNewFault');

  var DiNewFaultName   = $('#DiNewFaultName');
  var DiNewFaultPoints = $('#DiNewFaultPoints');

  var frm = displineNewFault;

  if ( DiNewFaultName.val().length > 0 && DiNewFaultPoints.val().length > 0 ) {

      displineNewFault.hide();
      displineNewFaultLoading.slideDown();

      var url = base_url+"api/Di/Faults"; 

      $.post(url, 
      frm.serialize(), 
      function(data, status, xhr){

          if (data.success){   

            $('#Model_DiNewFault').modal('toggle');

              $.gritter.add({
                title: 'Success',
                text: 'Successfully added .',
                class_name: 'success gritter-center',
                time: ''
              });

              DiNewFaultName.val("");
              DiNewFaultPoints.val("");

          } else {

              $.gritter.add({
                title: 'Failed',
                text: 'Failed to add fault.',
                class_name: 'danger gritter-center',
                time: ''
              });

          }

           
      },"json")
      .done(function() {

      })
      .fail(function() {

          $.gritter.add({
                title: 'Failed',
                text: 'Failed to add fault. try again ',
                class_name: 'danger gritter-center',
                time: ''
              });

      })
      .always(function() { 

          displineNewFaultLoading.hide();
          displineNewFault.slideDown();

      });

  }else{

    frm.formValidation('revalidateField', 'faultName');
    frm.formValidation('revalidateField', 'sanctionPoint');

  }

}

function submitDiNewDeducation(isDoubleRecord)
{ 
    var displineNewDeductionLoding = $('#displineNewDeductionLoding');
    var displineNewDeduction       = $('#displineNewDeduction');

    var selectDeductactionType     =  $('input[name=NewDeductionType]:checked', '#displineNewDeduction').val();
    var selectDiNewDeductionStudent=  $('#selectDiNewDeductionStudent');
    var selectDiNewDeductionClass  =  $('#selectDiNewDeductionClass');
    var selectDiNewDeductionFault  =  $('#selectDiNewDeductionFault');
    var newDiDeductedPoints        =  $('#newDiDeductedPoints');
    var newDiDeductComment         =  $('#newDiDeductComment');

    var frm = displineNewDeduction;

    if ( ( 
            ( selectDeductactionType == 1 && selectDiNewDeductionStudent.select2('val').length  > 0 ) 
            ||
            ( selectDeductactionType == 2 && selectDiNewDeductionClass.select2('val').length  > 0 ) 
            || 
            selectDeductactionType == 3 
          ) && selectDiNewDeductionFault.select2('val') > 0 && newDiDeductedPoints.val().length > 0  ) {

      displineNewDeduction.hide();
      displineNewDeductionLoding.slideDown();
      
      var url = base_url+"api/Di/Sanctions"; 

      $.post(url, 
      frm.serialize(), 
      function(data, status, xhr){

       if (data.success)
        {   

            $('#Model_DiNewDeduction').modal('toggle');

            $.gritter.add({
                title: 'Success',
                text: 'Successfully added .',
                class_name: 'success gritter-center',
                time: ''
            });
              
            selectDiNewDeductionStudent.select2('data', null);
            selectDiNewDeductionClass.select2('data', null);
            selectDiNewDeductionFault.select2('data', null);
            newDiDeductedPoints.val("");
            newDiDeductComment.val("");

            dashOnTabSelectedDispline();

        } else {

            $.gritter.add({
              title: 'Failed',
              text: 'Failed to add.',
              class_name: 'danger gritter-center',
              time: ''
            });

        }

      },"json")
      .done(function() {
        
      })
      .fail(function() {

        $.gritter.add({
            title: 'Failed',
            text: 'Failed to add. try again',
            class_name: 'danger gritter-center',
            time: ''
          });

      })
      .always(function() { 

          displineNewDeductionLoding.hide();
          displineNewDeduction.slideDown();
      });

    }else{

      frm.formValidation('revalidateField', 'student[]');
      frm.formValidation('revalidateField', 'classes[]');
      frm.formValidation('revalidateField', 'fault');
      frm.formValidation('revalidateField', 'points');

    }

}

function submitDiUpdateDeducation(isDoubleRecord)
{
  $('#displineUpdateDeduction').on('submit', function(e){
    
    $('#Model_DiUpdateDeduction').modal('toggle');

    var deductionID = $('#deductionID').val();

    var frm = $('#displineUpdateDeduction');
    var url = base_url+"api/Di/Sanctions/"+deductionID ; 
    e.preventDefault();
    $.ajax({
            url: url,
            type: 'PUT',
            data: frm.serialize(),
            success: function(data) {

              if (data.success)
              {   

                dashOnTabSelectedDispline();

                $.gritter.add({
                  title: 'Success',
                  text: 'Deduction updated.',
                  class_name: 'success gritter-center',
                  time: ''
                });


              } else {

                  $('#Model_DiUpdateDeduction').modal('show');

                  $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to update Deduction.',
                    class_name: 'danger gritter-center',
                    time: ''
                  });
            
              }
        }
    });
  });
}

function dashDiStudentSanctionComments()
{

  $( "table#dashDiStudentDeductedTable" ).delegate( "td", "click", function() {

      var id            = $(this).closest('tr').attr('id');
      var regnumber     = $(this).closest('tr').attr('regnumber');

      var content = '';
      var container           = $('#dashDiCommentSection') ;
      
      $('#Model_DiDeductionComment').modal('show');
    
        var url = base_url+"api/Di/Sanctions/Comment/"+id; 

        $.getJSON( url , function(data) {

          $.each(data, function(i, item) {

              content +='<div class="chat-box-timeline"><img class="img-responsive img-circle avatar" src="images/schoolStaff/50/'+item.commenter+'/'+item.photoID+'" /><div class="message"><div class="panel panel-shadow panel-white"><div class="panel-body panel-arrow-left"><div class="chat-box-timeline-title"><strong>'+item.commenterName+'</strong><div class="pull-right text-semi"><i class="fa fa-clock-o"></i> '+moment.unix(item.addedOn).fromNow()+'</div></div><div class="chat-box-timeline-content"><blockquote>'+item.comment+'</blockquote></div></div></div></div></div>';
                    
            });

        })
        .done(function() {

          container.html( content );
          $('#deduction').val(id);

        })
        .fail(function() {
        })
        .always(function() {
        });

  });

}

function submitDiNewComment()
{
  $('#displineNewComment').on('submit', function(e){
    
    var container = $('#dashDiCommentSection');
    var content   = '';
    var comment   = $('#comment').val();

    var frm = $('#displineNewComment');
    var url = base_url+"api/Di/Sanctions/Comment"; 
      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
           if (data.success)
           {   

              var commenter = data.commenter;
              var addedOn   = data.addedOn;
              var names     = data.names;
              var photoID   = data.photoID;

              content +='<div class="chat-box-timeline"><img class="img-responsive img-circle avatar" src="images/schoolStaff/50/'+commenter+'/'+photoID+'" /><div class="message"><div class="panel panel-shadow panel-white"><div class="panel-body panel-arrow-left"><div class="chat-box-timeline-title"><strong>'+names+'</strong><div class="pull-right text-semi"><i class="fa fa-clock-o"></i> '+moment.unix(addedOn).fromNow()+'</div></div><div class="chat-box-timeline-content"><blockquote>'+comment+'</blockquote></div></div></div></div></div>';
               
              container.append(content);

              $('#comment').val('');

           } 

         });
    });
}


function DiDeductionTableClicked(){

    $( "table#dashDiDeductionTable" ).delegate( "td.diDeductionViewComment", "click", function(e) {

        e.preventDefault();
        e.stopPropagation();

        var id                  = $(this).closest('tr').attr('id');
        var container           = $('#dashDiCommentSection') ;

        $('#Model_DiDeductionComment').modal('show');
    
        var url = base_url+"api/Di/Sanctions/Comment/"+id; 

        var content = '';

        $.getJSON( url , function(data) {

          $.each(data, function(i, item) {

              content +='<div class="chat-box-timeline"><img class="img-responsive img-circle avatar" src="images/schoolStaff/50/'+item.commenter+'/'+item.photoID+'" /><div class="message"><div class="panel panel-shadow panel-white"><div class="panel-body panel-arrow-left"><div class="chat-box-timeline-title"><strong>'+item.commenterName+'</strong><div class="pull-right text-semi"><i class="fa fa-clock-o"></i> '+moment.unix(item.addedOn).fromNow()+'</div></div><div class="chat-box-timeline-content"><blockquote>'+item.comment+'</blockquote></div></div></div></div></div>';
                    
            });

        })
        .done(function() {

          container.html( content );
          $('#deduction').val(id);

        })
        .fail(function() {

        })
        .always(function() {

        });


    });

    $( "table#dashDiDeductionTable" ).delegate( "td.diDeductionEdit", "click", function(e) {

        e.preventDefault();
        e.stopPropagation();

        var id                 = $(this).closest('tr').attr('id');
        var student_person_id  = $(this).closest('tr').attr('student_person_id');
        var fault_id           = $(this).closest('tr').attr('fault_id');
        var deducted_point     = $(this).closest('tr').attr('deducted_point');

        var deductionID                     = $('#deductionID');
        var selectDiUpdateDeductionStudent  = $("#selectDiUpdateDeductionStudent");
        var selectDiUpdateDeductionFault    = $("#selectDiUpdateDeductionFault");
        var UpdateDiDeductedPoints          = $("#UpdateDiDeductedPoints");

        deductionID.val(id);
        selectDiUpdateDeductionStudent.select2().val(student_person_id).trigger('change');
        selectDiUpdateDeductionFault.select2().val(fault_id).trigger('change');
        UpdateDiDeductedPoints.val( deducted_point );

        $('#Model_DiUpdateDeduction').modal('show');

    });

    $( "table#dashDiDeductionTable" ).delegate( "td.diDeductionDelete", "click", function(e) {

        e.preventDefault();
        e.stopPropagation();

        var id      = $(this).closest('tr').attr('id');
        var diDeleteDeducationID = $("#diDeleteDeducationID");

        diDeleteDeducationID.val(id);

        $('#Model_DashDiDeleteDeduction').modal('show');

    });
}

  function deleteDiDeducation()
  {

    var diDeleteDeducationID    = $("#diDeleteDeducationID").val();

    var deleteDeducationLoading  = $("#deleteDeducationLoading");
    var deleteDeducationContent  = $("#deleteDeducationContent");

    deleteDeducationContent.hide();
    deleteDeducationLoading.slideDown();

    var url     = base_url+"api/Di/Sanctions/"+diDeleteDeducationID; 

      $.ajax({
            url: url,
            type: 'DELETE',
            success: function(data) {
              
              deleteDeducationLoading.hide();
              deleteDeducationContent.slideDown();
              
              $('#Model_DashDiDeleteDeduction').modal('toggle');

               if ( data.success )
               {
                    $.gritter.add({
                      title: 'Success',
                      text: 'Deduction deleted.',
                      class_name: 'success gritter-center',
                      time: ''
                    });

                    dashOnTabSelectedDispline();

               }else{

                 $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to delete deduction, Something went wrong.',
                    class_name: 'danger gritter-center',
                    time: ''
                  });

             }
        },
        error: function(){
       
        deleteDeducationLoading.hide();
        deleteDeducationContent.slideDown();

          $.gritter.add({
            title: 'Failed',
            text: 'Failed to delete deduction.',
            class_name: 'danger gritter-center',
            time: ''
          });
    }

    });

  }

function get_DiClassDispline( ClassID ,YearName , StreamName  )
{

    $( "#dashContentainer_Displine").slideDown();
    var container          = $( "#dashDispline_content" );

    container.empty();

    var url = base_url+"api/Di/StudentsDisplinePoints/Class/"+ClassID;

    content = '';

    $.getJSON( url, function(data) {

        var StudentInitialDiscipline = data.points ; 
        var ClassDispline            = data.ClassDispline ; 

        content = '<div class="col-sm-10 col-sm-offset-1">';
        content += '<div class="row"><div class="col-sm-6 text-center"><h4><strong>'+YearName+' '+StreamName+' - Discipline</strong></h4></div><div class="col-sm-3 text-center">Student start discipline points : <b>'+StudentInitialDiscipline+'</b></div><div class="col-sm-3 text-center"><a class="btn btn-default pull-right" href="api/Di/StudentsDisplinePoints/PDF/Class/'+ClassID+'" target="_blank"><i class="fa fa-print"></i>Print</a></div></div>'; 
        content += '<div class="row"><table class="table table-bordered" id="dashDiClassDisplineTable" ><thead>';
          content += '<tr><th rowspan="2">Student Name</th><th rowspan="2" class="text-center">Reg. Number</th><th rowspan="2" class="text-center">Points</th><th colspan="2" class="text-center" >Deductions</th><th colspan="2" style="background-color:#e5e5ff;" class="text-center">Rewards</th></tr>';
          content += '<tr><th class="text-center">Total</th><th class="text-center">Number</th><th style="background-color:#e5e5ff;" class="text-center">Total</th><th style="background-color:#e5e5ff;" class="text-center">Number</th></tr>';
        content += '</thead><tbody>';
    
        $.each( ClassDispline, function(i, item) {

            var StudentsDisplinePoints = parseFloat(StudentInitialDiscipline) - parseFloat(item.TotalDeducted) + parseFloat(item.TotalRewarded);

            content +="<tr id='"+item.id+"' >";
              content +="<td>"+item.studentNames+"</td>";
              content +="<td class='text-center'>"+item.studRegNber+"</td>";
              content +="<td class='text-center'>"+StudentsDisplinePoints+"</td>";
              content +="<td class='text-center'  >"+item.TotalDeducted+"</td>";
              content +="<td class='text-center'  >"+item.NumberOfDeductation+"</td>";
              content +="<td class='text-center' style='background-color:#e5e5ff;' >"+item.TotalRewarded+"</td>";
              content +="<td class='text-center' style='background-color:#e5e5ff;' >"+item.NumberOfRewards+"</td>";
              content +="</td>";
            content +="</tr>";

        });
      
        content +='</tbody></table></div>';
        content +='</div>';
      
    }).done(function() {

        container.append( content );
        abTablePagingTable('dashDiClassDisplineTable');

    })
    .fail(function() {
    })
    .always(function() {
    });

}

  // var dashDiSelectDiConductScore = '' ;
  // var ClassMetrics_commandQueue  = [] ;
  // var ClassMetrics_grid;
  // var ClassMetrics_EditAccess    = true ;

  // function ClassMetrics_queueAndExecuteCommand(item, column, editCommand) {
  //     ClassMetrics_commandQueue.push(editCommand);
  //     editCommand.execute();
  // }

  // function ClassMetricsRangeEditor(args) {

  //       var $select;
  //       var defaultValue;
  //       var scope = this;

  //       this.init = function() {

  //           console.log( "init");

  //           option_str = ""
  //           option_str += "<OPTION value='1'>Excellent</OPTION>";
  //           option_str += "<OPTION value='2'>Good</OPTION>";
  //           option_str += "<OPTION value='2'>Satisfactory</OPTION>";
  //           option_str += "<OPTION value='2'>Cause of Concern</OPTION>";

  //           $select = $("<SELECT tabIndex='0' class='editor-select'>"+ option_str +"</SELECT>");
  //           $select.appendTo(args.container);
  //           $select.focus();
  //       };

  //       this.destroy = function() {
  //           console.log( "destroy");
  //           $select.remove();
  //       };

  //       this.focus = function() {
  //           $select.focus();
  //           console.log( "focus");
  //       };

  //       this.loadValue = function(item) {
  //           console.log( "loadValue");
  //           defaultValue = item[args.column.field];
  //           $select.val(defaultValue);
  //       };

  //       this.serializeValue = function() {
  //           console.log( "serializeValue");

  //           if(args.column.options){
  //             return $select.val();
  //           }else{
  //             return ($select.val() == "yes");
  //           }
  //       };

  //       this.applyValue = function(item,state) {
  //           console.log( "applyValue");
  //           item[args.column.field] = state;
  //       };

  //       this.isValueChanged = function() {

  //           console.log( "isValueChanged");
  //           return ($select.val() != defaultValue);
  //       };

  //       this.validate = function() {
  //           console.log( "validate");
  //           return {
  //               valid: true,
  //               msg: null
  //           };
  //       };

  //       this.init();

  // }

  // function DashDiLoadClassMetricsRecords( ClassMetrics_ClassID , ClassMetrics_Data, ClassMetrics_MetricID )
  // {     

  //       var dashDiSelectDiConductScore = $( "#dashDiSelectDiConductScore");

  //       var ClassMetrics_slickdata     = [];
  //       var ClassMetrics_commandQueue  = [];

  //       var res           = ClassMetrics_Data;
  //       numberOfStudents  = res.length;

  //       var slickid = 0 ;

  //       $.each(res, function(i, item) {

  //           var d = ( ClassMetrics_slickdata[slickid] = {} );

  //           d["ID"]          = slickid+1;
  //           d["Studentname"] = item.StudentNames;
  //           d["RegNumber"]   = item.RegNumber;
  //           d["Status"]      = item.Status;

  //           slickid++;

  //       });

  //       // var dashGrResultSection         = $( "#dashGrResultSection" );
  //       // var dashGrResultSelectedSection = $( "#dashGrResultSelectedSection" );
    
  //       // dashGrResultSection.hide();
  //       // dashGrResultSelectedSection.show();

  //       var options = {
  //           autoHeight: true,
  //           editable: true,
  //           enableAddRow: false,
  //           enableCellNavigation: true,
  //           asyncEditorLoading: false,
  //           autoEdit: true,
  //           editCommandHandler: ClassMetrics_queueAndExecuteCommand
  //       };

  //          if ( ClassMetrics_EditAccess )
  //           {

  //             var columns = [

  //               {id: "ID", name: "&nbsp;ID", field: "ID" , width: 50  },
  //               {id: "Studentname", name: " Student name", field: "Studentname", width: 200  , defaultSortAsc:true },
  //               {id: "RegNumber", name: " Regitration number", field: "RegNumber" , width: 200 , defaultSortAsc:true  },
  //               {id: "Status", name: " Status", field: "Status" , width: 200 , editor: ClassMetricsRangeEditor }
              
  //             ];

  //           }else{

  //             var columns = [

  //           {id: "ID", name: "&nbsp;ID", field: "ID" , width: 50  },
  //           {id: "Studentname", name: " Student name", field: "Studentname", width: 200  , defaultSortAsc:true },
  //           {id: "RegNumber", name: " Regitration number", field: "RegNumber" , width: 200 , defaultSortAsc:true  },
  //           {id: "Status", name: " Status", field: "Status" , width: 200  , defaultSortAsc:true , cssClass: "text-right"  }
          
  //         ];
  //           }

  //         ClassMetrics_grid = new Slick.Grid( dashDiSelectDiConductScore , ClassMetrics_slickdata, columns, options);
  //       ClassMetrics_grid.setSelectionModel(new Slick.CellSelectionModel());
  //     ClassMetrics_grid.setActiveCell( 0, 3);

  //     dashDiSelectDiConductScore.on('blur', 'input.editor-text', function() {
  //         Slick.GlobalEditorLock.commitCurrentEdit();
  //     });

  //     //var editResultsOptions = $("#editResultsOptions");
  //     //editResultsOptions.html('<span class="btn btn-default" onclick="ab_slickGridUndo()"><i class="fa fa-undo"></i>&nbsp;&nbsp;Undo</span>&nbsp;&nbsp;&nbsp;<span class="btn btn-primary" onclick="ab_stickGridSaveUnsavedMarks('+AssessmentID+')"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save unsaved marks</span>');

  //     ClassMetrics_grid.onValidationError.subscribe(function (e, args) { 

  //       // slickGridConsole = $("#slickGridConsole");
  //       // slickGridConsole.hide();
  //       // slickGridConsole.html('<span class="text-red">'+args.validationResults.msg+'</span>');
  //       // slickGridConsole.slideDown();
        
  //     });
        
  //     ClassMetrics_grid.onCellChange.subscribe( function (e,args) { 

  //           var currentCellCol  = args['cell'];
  //           var currentCellRow  = args['row'];

  //           var newStatus       = args.item['Status'];
  //           var newStatusFlaot  = parseFloat( newStatus );  

  //           var RegNumber       = args.item['RegNumber'] ; 
  //           var resultSaved     = false ;

  //           var AjaxData = { 
  //               saveAll         :0 ,
  //               regNumber       :RegNumber ,
  //               newStatus       :newStatusFlaot } ;

  //           $.ajax({

  //             url: base_url+"api/Gr/Results",
  //             dataType: 'json',
  //             type: 'POST',
  //             data: AjaxData ,

  //             success: function ( res ) {

  //               if (res.saved)
  //                {
  //                   resultSaved = true;
  //                }

  //             }

  //         }).done(function() {

  //         // if ( resultSaved )
  //         //  {
  //         //     slickGridConsole.hide();
  //         //     slickGridConsole.html('<span class="text-green">Marks saved!</span>');
  //         //     slickGridConsole.slideDown();

  //         //     var cellStyle = {};
  //         //   cellStyle[currentCellRow] = {}; // <- your particular cell in a row with number rowNum

  //         //   cellStyle[currentCellRow]["ID"]       = "slickGrid_highlight";
  //         //   cellStyle[currentCellRow]["Marks"]      = "slickGrid_highlight";
  //         //   cellStyle[currentCellRow]["RegNumber"]    = "slickGrid_highlight";
  //         //   cellStyle[currentCellRow]["Studentname"]  = "slickGrid_highlight";
  //         //   grid.removeCellCssStyles("notsaved_row_"+currentCellRow+"_"+currentCellCol);

  //         //  }else{

  //         //     slickGridConsole.hide();
  //         //     slickGridConsole.html('<span class="text-red">Failed to save marks, Please click on "<b>Save unsaved marks</b>"</span>');
  //         //     slickGridConsole.slideDown();

  //         //     var cellStyle = {};
  //         //   cellStyle[currentCellRow] = {}; // <- your particular cell in a row with number rowNum

  //         //   cellStyle[currentCellRow]["ID"]       = "slickGrid_highlight";
  //         //   cellStyle[currentCellRow]["Marks"]      = "slickGrid_highlight";
  //         //   cellStyle[currentCellRow]["RegNumber"]    = "slickGrid_highlight";
  //         //   cellStyle[currentCellRow]["Studentname"]  = "slickGrid_highlight";
  //         //   grid.addCellCssStyles("notsaved_row_"+currentCellRow+"_"+currentCellCol, cellStyle);

  //         //  }

  //       }).fail(function() {

  //         // slickGridConsole.hide();
  //         //   slickGridConsole.html('<span class="text-red">Failed to save marks, Check your internet and click on "<b>Save unsaved marks</b>"</span>');
  //         //   slickGridConsole.slideDown();

  //         // $.gritter.add({
  //         //         title: 'Result not saved',
  //         //         text: 'Check your internet and try again',
  //         //         class_name: 'danger gritter-center',
  //         //         time: ''
  //         //     });

  //         // var cellStyle = {};
  //         // cellStyle[currentCellRow] = {}; // <- your particular cell in a row with number rowNum
  //         // cellStyle[currentCellRow]["Marks"]      = "slickGrid_highlight";
  //         // cellStyle[currentCellRow]["RegNumber"]    = "slickGrid_highlight";
  //         // cellStyle[currentCellRow]["Studentname"]  = "slickGrid_highlight";
  //         // grid.addCellCssStyles("notsaved_row_"+currentCellRow+"_"+currentCellCol, cellStyle);

  //       }).always(function() {

  //       });

  //       });

  // }




function AB_isEmpty(str) {
    return (!str || 0 === str.length);
}

function getDashGrRcValidate()
{
	//validation
	var formDashGetSelectedReport = $('#formDashGetSelectedReport');

	formDashGetSelectedReport.formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        exclude: ':disabled',
        fields: {
        	GrReportType: {
                validators: {
                    notEmpty: {
                        message: 'Report Type is required'
                    }
                }
            },
            GrReportClass: {
                validators: {
                    notEmpty: {
                        message: 'Class is required'
                    }
                }
            },
             GrReportYear: {
                validators: {
                    notEmpty: {
                        message: 'Year is required'
                    }
                }
            },
            GrReportStudents: {
                validators: {
                    notEmpty: {
                        message: 'Student is required'
                    }
                }
            },
            whenFinish: {
                validators: {
                    notEmpty: {
                        message: 'How you want you want to notified is required'
                    }
                }
            }
        }
    })
	.on('success.form.fv', function(e , data) {

	e.preventDefault();
	onDashLoadReportSectionContent();

 	var GrReportStudentsSubmit = $('#GrReportStudentsSubmit');
		GrReportStudentsSubmit.removeClass('disabled'); 
		GrReportStudentsSubmit.removeAttr('disabled');

    }).end();

}

function deleteGrDownload()
{

	var generatedReportID 		= $("#generatedReportID").val();
	var generatedDownloadName   = $("#generatedReportDownloadName").val();

    var generatedReportLoading  = $("#generatedReportLoading");
    var generatedReportContent  = $("#generatedReportContent");

    generatedReportContent.hide();
    generatedReportLoading.slideDown();

    var url     = base_url+"api/Gr/ReportCards/"+generatedReportID+"?DownloadName="+generatedDownloadName+""; 

      $.ajax({
            url: url,
            type: 'DELETE',
            success: function(data) {
           
              generatedReportLoading.hide();
              generatedReportContent.slideDown();
              
              $('#Model_DashGrDeleteGeneratedReport').modal('toggle');

               if ( data.success )
               {
                    $.gritter.add({
                      title: 'Success',
                      text: 'Report deleted.',
                      class_name: 'success gritter-center',
                      time: ''
                    });

                    onDashLoadReportSection(false);

               }else{

                 $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to delete report, Something went wrong.',
                    class_name: 'danger gritter-center',
                    time: ''
                  });

             }
        },
        error: function(){
		   
		    generatedReportLoading.hide();
            generatedReportContent.slideDown();

		   $.gritter.add({
	            title: 'Failed',
	            text: 'Failed to delete reports, Try again later.',
	            class_name: 'danger gritter-center',
	            time: ''
	          });
		}

    });

}

function dashGrRecordedResultsSelected()
{
	$( "table#paGrAssessementRecords" ).delegate( "td:not(.GrAssessmentDelete, .GrAssessmentEdit)", "click", function() {

	  		AssessmentID 				   = $(this).closest('tr').attr('id');
	  		tb_IsTeachingSet 			   = $(this).closest('tr').attr('tb_IsTeachingSet');
	  		assessmentMax 				   = $(this).closest('tr').attr('max');
	  		assessmentDate 				   = $(this).closest('tr').attr('assessmentdate');
	  		teacherName 				   = $(this).closest('tr').attr('teachername');

			var dashGrSelectResultSLoading 		= $("#dashGrSelectResultSLoading");
			var dashGrSelectResultSResults 		= $("#dashGrSelectResultSResults");
			var dashGrSelectResultDescription 	= $("#dashGrSelectResultDescription");

			dashGrSelectResultSLoading.slideDown();
			dashGrSelectResultSResults.hide();

			var ResultsInfo = '';
			ResultsInfo += '<div class="row"><div class="col-sm-12" >';
				ResultsInfo +='<div class="col-sm-1" ><b>Max: </b><span id="getResultAssessmentMax" >'+assessmentMax+'</span></div>';
				ResultsInfo +='<div class="col-sm-2" ><b>Done on: </b> '+date_moment(assessmentDate)+'</div>';
				ResultsInfo +='<div class="col-sm-3" ><span id="slickGridConsole"></span></div>';
				ResultsInfo +='<div class="col-sm-4" >';
					ResultsInfo +='<span id="editResultsOptions"></span>&nbsp;&nbsp;&nbsp;';
				ResultsInfo +='</div>';
				ResultsInfo +='<div class="col-sm-2 pull-right" ><b>Teacher:</b> '+teacherName+'</div>';
			ResultsInfo += '</div></div>';

			$("#dashGrSelectResultDescription").html(ResultsInfo);

			DashTeLoadResults( tb_IsTeachingSet,  0, AssessmentID, null );

            dashGrSelectResultSLoading.hide();
         	dashGrSelectResultSResults.slideDown();

	});

}


function dashGrRecordedResultsDelete()
{	
	$( "table#paGrAssessementRecords" ).delegate( "td.GrAssessmentDelete", "click", function(e) {

		e.preventDefault();
		e.stopPropagation();

	  	var id 			= $(this).closest('tr').attr('id');
	  	GrDeleteAssessment(id, $(this) );

	});
}

function dashGrRecordedResultsEdit()
{	
	$( "table#paGrAssessementRecords" ).delegate( "td.GrAssessmentEdit", "click", function(e) {

		e.preventDefault();
		e.stopPropagation();

	  	var id 			    = $(this).closest('tr').attr('id');
	  	var classid 		= $(this).closest('tr').attr('classid');
	  	var anclasssubjid   = $(this).closest('tr').attr('anclasssubjid');
	  	var asstype 		= $(this).closest('tr').attr('asstype');
	  	var period 			= $(this).closest('tr').attr('period');
	  	var max 			= $(this).closest('tr').attr('max');
	  	var assessmentdate 	= $(this).closest('tr').attr('assessmentdate');

	  	var dashEditAssClassSelect 	 = $("#dashEditAssClassSelect");
	  	var dashEditAssSubjectSelect = $("#dashEditAssSubjectSelect");

		$("#EditAssessmentID").val(id);
		$("#EditAssessmentClassID").val(classid);
		$("#EditAssessmentClassSubjectID").val(anclasssubjid);

		dashEditAssClassSelect.val(classid).trigger("change");
		dashEditAssSubjectSelect.val(anclasssubjid).trigger("change");
		$("#dashEditAssTypeSelect").val(asstype).trigger("change");
		$("#dashEditAssPeriodSelect").val(period).trigger("change");
		$("#dashEditAssMaximum").val(max);
		$("#EditAssDate").val(moment.unix(assessmentdate).format("MM/DD/YYYY")).trigger("change");

		dashEditAssClassSelect.select2("enable",false);
		dashEditAssSubjectSelect.select2("enable",false);

	  	var Model_EditAssessment = $("#Model_EditAssessment");
	  	Model_EditAssessment.modal('show');

	  	//GrDeleteAssessment(id, $(this) );

	});
}

function GrDeleteAssessment( assessmentID , td )
{

    var url     = base_url+"api/Gr/Assessment/"+assessmentID;
    td.html("Deleting...");
    
      $.ajax({
            url: url,
            type: 'DELETE',
            success: function(data) {
           
               if ( data.success )
               {
                    $.gritter.add({
                      title: 'Success',
                      text: 'Assessment deleted.',
                      class_name: 'success gritter-center',
                      time: ''
                    });

                    onDashLoadResultSection(0 , false , null, null, null );

               }else{

                 $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to delete Assessment, '+ data.error,
                    class_name: 'danger gritter-center',
                    time: ''
                  });

                  td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

             }
        }

    });

}

function dashGrRecordedResultSeled(selectedAssessment)
{
	
}

function dashGrTeachersSubjectSelected()
{
	$( "table#grTeachersSubjectTable" ).delegate( "td", "click", function() {

	  	id 				= $(this).closest('tr').attr('id');
	  	classname   	= $(this).closest('tr').attr('classname');
	  	subjectname 	= $(this).closest('tr').attr('subjectname');
	  	subjectmaximum 	= $(this).closest('tr').attr('subjectmaximum');

	  	GrTeachersClassSubject(id, classname, subjectname , subjectmaximum );

	});

}

function GrTeachersClassSubject(id, classname, subjectname , subjectmaximum)
{
	var dashGradebook_contentAnalytics 	= $("#dashGradebook_contentAnalytics");
	var dashGradebook_contentOther 		= $("#dashGradebook_contentOther");

	var content  = '';
		content 	+= '<div class="row">';
			content+= '<div class="col-sm-12">';
				content+= '<div class="col-sm-4">';
	                    content+= '<span><button type="button" class="btn btn-default" onClick="onDashLoadTeachersSubjects()"><strong><i class="fa fa-arrow-circle-left"></i> Back to all Subjects</strong></button></span>'; 
	            content+= '</div>';
	            content+= '<div class="col-sm-8">';
	            	content +='<h4>Comments on <b>'+subjectname+'</b> in <b>'+classname+'</b></h4>';
	            content+= '</div>';
	        content+= '</div>';
        content+= '</div>';
		content 	+= '<div  class="row">';
			content 	+= '<div  class="col-sm-10 col-sm-offset-1">'; 
				content 	+= '<div id="dashGrSubjectCommentSection"></div>';
			content 	+= '</div>';
		content 	+= '</div>';
	content 	+= '</div>';

	dashGradebook_contentOther.html(content);

	dashGradebook_contentAnalytics.hide();
	dashGradebook_contentOther.slideDown();

	var url = base_url+"api/Gr/TeachersComments/"+id; 

	var SubjectCommentContent = '';
	//get 
	$.getJSON( url, function(data) {

		var TeachersCommentData = data.TeachersCommentData;
		var SchoolID 			= data.School;

		if ( SchoolID  == 5 ) {

			SubjectCommentContent +='<table class="table table-striped" id="grSubjectCommentTable" ><thead><tr class="text-left"><th class="text-left">Student</th><th class="text-center">CWI</th><th class="text-center">MTM</th><th class="text-center">CWII</th><th class="text-center">TEM</th><th class="text-center">ETM</th><th class="text-center">Teacher\'s Comment (Max: 500 characters)</th></tr></thead><tbody>';

			$.each( TeachersCommentData, function(i, item) {

				  var ETM= null;
				  var StudentSubjectTotal = null;

				  //CWI
		          if ( !AB_isEmpty(item.CWI) )
		          {
		              StudentSubjectTotal    = item.CWI * 30 ;

		          }

		          //MTM
		          if ( !AB_isEmpty(item.MTM) )
		          {
		              StudentSubjectTotal    += item.MTM * 15 ;

		          }

		          //CWII
		          if ( !AB_isEmpty(item.CWII) )
		          {
		              StudentSubjectTotal    += item.CWII * 30 ;

		          }

		          //TEM
		          if ( !AB_isEmpty(item.TEM) )
		          {
		              StudentSubjectTotal    += item.TEM * 25 ;

		          }

		          if ( !AB_isEmpty( StudentSubjectTotal ) ) {

		            var ETM = Math.round( StudentSubjectTotal /100 ); 

		          }else{

		            var ETM  = "";

		          }

		          SubjectCommentContent +="<tr >";
		          	SubjectCommentContent +="<td class='text-left'>"+item.studentNames+" </td>";
		            SubjectCommentContent +="<td class='text-center'>"+item.CWI+"</td>";
		            SubjectCommentContent +="<td class='text-center'>"+item.MTM+"</td>";
		            SubjectCommentContent +="<td class='text-center'>"+item.CWII+"</td>";
		            SubjectCommentContent +="<td class='text-center'>"+item.TEM+"</td>";
		            SubjectCommentContent +="<td class='text-center'><b>"+ETM+"</b></td>";
		            SubjectCommentContent +="<td class='text-center'><span style='width:100%' class='CommentXeditable' data-type='text' data-pk='1' data-title='Enter Your Comment' data-url='api/Gr/TeachersComments/"+id+"?studRegNber="+item.studRegNber+"' >"+item.Comment+"</span></td>";
		          SubjectCommentContent +="</tr>";

		    });

			SubjectCommentContent +="</tbody></table>";
			$("#dashGrSubjectCommentSection").html(SubjectCommentContent);


			$('#grSubjectCommentTable').dataTable( {
			  paging: false,
		      "aaSorting": [] ,
		      "bAutoWidth": false,
			  "aoColumns": [
			    { "sWidth": "20%" },
			    { "sWidth": "10%" },
			    { "sWidth": "10%" },
			    { "sWidth": "10%" },
			    { "sWidth": "10%" },
			    { "sWidth": "10%" },
			    { "sWidth": "30%" }
			  ]
			} );


		}else if ( SchoolID  == 11 ) {

			SubjectCommentContent +='<table class="table table-striped" id="grSubjectCommentTable" ><thead><tr class="text-left"><th class="text-left">Student</th><th class="text-center">CW</th><th class="text-center">MTE</th><th class="text-center">EOT</th><th class="text-center">TOT</th><th class="text-center">Teacher\'s Comment (Max: 500 characters)</th></tr></thead><tbody>';

			$.each( TeachersCommentData, function(i, item) {

				  var ETM= null;
				  var StudentSubjectTotal = null;

				  //CW
		          if ( !AB_isEmpty(item.CW) )
		          {
		              StudentSubjectTotal    = item.CW * 15 ;

		          }

		          //MTE
		          if ( !AB_isEmpty(item.MTE) )
		          {
		              StudentSubjectTotal    += item.MTE * 25 ;

		          }

		          //EOT
		          if ( !AB_isEmpty(item.EOT) )
		          {
		              StudentSubjectTotal    += item.EOT * 60 ;

		          }

		          if ( !AB_isEmpty( StudentSubjectTotal ) ) {

		            var ETM = Math.round( StudentSubjectTotal /100 ); 

		          }else{

		            var ETM  = "";

		          }

		          SubjectCommentContent +="<tr >";
		          	SubjectCommentContent +="<td class='text-left'>"+item.studentNames+" </td>";
		            SubjectCommentContent +="<td class='text-center'>"+item.CW+"</td>";
		            SubjectCommentContent +="<td class='text-center'>"+item.MTE+"</td>";
		            SubjectCommentContent +="<td class='text-center'>"+item.EOT+"</td>";
		            SubjectCommentContent +="<td class='text-center'><b>"+ETM+"</b></td>";
		            SubjectCommentContent +="<td class='text-center'><span style='width:100%' class='CommentXeditable' data-type='text' data-pk='1' data-title='Enter Your Comment' data-url='api/Gr/TeachersComments/"+id+"?studRegNber="+item.studRegNber+"' >"+item.Comment+"</span></td>";
		          SubjectCommentContent +="</tr>";

		    });

			SubjectCommentContent +="</tbody></table>";
			$("#dashGrSubjectCommentSection").html(SubjectCommentContent);


			$('#grSubjectCommentTable').dataTable( {
			  paging: false,
		      "aaSorting": [] ,
		      "bAutoWidth": false,
			  "aoColumns": [
			    { "sWidth": "20%" },
			    { "sWidth": "10%" },
			    { "sWidth": "10%" },
			    { "sWidth": "10%" },
			    { "sWidth": "10%" },
			    { "sWidth": "40%" }
			  ]
			} );

		}
		
		

		$.fn.editable.defaults.ajaxOptions = {type: "PUT" };
		$.fn.editable.defaults.mode = 'inline';

		$('.CommentXeditable').editable({
			emptytext: 'Click to add comment',
			tpl: '<textarea maxlength="500"></textarea>' ,
	    	success: function(response, newValue) {
		        if(response.status == 'error') return response.msg;
		    }
		});

    })
    .done(function() {
    })
    .fail(function() {
    })
    .always(function() {
    });
}

function onDashLoadReportSectionContent()
{	
	  
	  var GrReportSubmitContainer  = $("#GrReportSubmitContainer");
	  var GrReportLoadingContainer = $("#GrReportLoadingContainer");

	  var selectGrReportType 	=  $("#selectGrReportType");
	  var GrReportTypeOnlyYears =  $("#GrReportTypeOnlyYears");
 	  var selectGrReportClass 	=  $("#selectGrReportClass");
 	  var selectGrReportYear 	=  $("#selectGrReportYear");

 	  var frm = $('#formDashGetSelectedReport');
      var url = base_url+"api/Gr/ReportCards"; 

      if ( 
      		selectGrReportType.select2('val') > 0 && 
      		( GrReportTypeOnlyYears.val() == 0 && selectGrReportClass.select2('val') > 0 ) 
      		|| 
      		( GrReportTypeOnlyYears.val() == 1 && selectGrReportYear.select2('val').length > 0 ) 
      	 ) {

      	GrReportSubmitContainer.hide();
	  	GrReportLoadingContainer.slideDown();

      	$.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){
	            
	          if ( data.success )
	          {   
	           
	           	onDashLoadReportSection(false);

	           	$.gritter.add({
		            title: 'Success',
		            text:  'The reports are being generated, This may take some time. Please wait',
		            class_name: 'success gritter-center',
		            time: '' 
		        });

		        // $('#selectGrReportType').select2('data', null);
		        $('#selectGrReportStudents').val("0").trigger("change");
				$('#selectGrReportYear').select2('data', null);
				$('#selectGrReportClass').select2('data', null);

	          } else {
	              
	              $.gritter.add({
		            title: 'Failed',
		            text: 'Failed to generate reports,'+data.error ,
		            class_name: 'danger gritter-center',
		            time: '' 
		          });
	          }
	             
	    },"json")
	      .done(function() {
	        
	    }).fail(function() {

	          $.gritter.add({
	            title: 'Failed',
	            text: 'Something went wrong, Please Try Again',
	            class_name: 'danger gritter-center',
	            time: ''
	          });

	          GrReportLoadingContainer.hide();
		  	  GrReportSubmitContainer.slideDown();

	    }).always(function() { });     

      }else{

      	frm.formValidation('revalidateField', 'GrReportType');
	  	frm.formValidation('revalidateField', 'GrReportClass');
	  	frm.formValidation('revalidateField', 'GrReportYear');

      }

}

function getDashGrRcClassSelected()
{
	var selectGrRcClass 		= $("#selectGrReportClass");
	var selectGrRcStudent 		= $("#selectGrReportStudents");

	var selected            	= selectGrRcClass.select2('val');
	var ClassStudents 			= GroupedStudentsClassArray[selected];
	var StudentsOptions			= '';

	var StudentsOptions 		= '<option value="0">All</option>';

	$.each(ClassStudents, function(i, item) {
		StudentsOptions += "<option value="+item.personID+" data-regnumber="+item.regNumber+" >"+item.regNumber+" ("+item.studentNames+") </option>";

    });	

	selectGrRcStudent.html( StudentsOptions );
	selectGrRcStudent.select2().val('0').trigger('change');

}

function getDashGrRcTypeSelected()
{
	var selectGrReportType = $("#selectGrReportType");
	var selected           = selectGrReportType.select2('val');

	var GrReportPeriodContainer  = $("#GrReportPeriodContainer");
	var GrReportStudentsontainer = $("#GrReportStudentsontainer");
	var GrReportTypeOnlyYears 	 = $("#GrReportTypeOnlyYears");

	if ( selected == 1 ) {
		GrReportPeriodContainer.slideDown();
		GrReportStudentsontainer.slideDown();

	}else if ( selected == 5 ) {
		GrReportPeriodContainer.hide();
		GrReportStudentsontainer.hide();

	}else if ( selected == 6 ) {
		GrReportPeriodContainer.slideDown();
		GrReportStudentsontainer.hide();

	}else{
		GrReportPeriodContainer.hide();
		GrReportStudentsontainer.slideDown();
	}

}

function getDashGrAnalyticsForm()
{	
	
	$("#DashAnPlaceTypeSelect").select2();

	var DashFilterPercentageContainer   = $("#DashFilterPercentageContainer");
    var DashFilterPlaceContainer 		= $("#DashFilterPlaceContainer");

    DashFilterPlaceContainer.hide();

	$('input[type=radio][name=DashAnFilter]').change( function() {
	        
        var selected = $(this).val();

        DashFilterPercentageContainer.hide();
    	DashFilterPlaceContainer.hide();

	    if ( selected == 0 ) {

	    	DashFilterPlaceContainer.slideDown();
	    	
	    }else if( selected == 1 ) {

	    	DashFilterPercentageContainer.slideDown();

	    }

    });

	var dashGrStudentPerformanceLoding = $("#dashGrStudentPerformanceLoding");
	dashGrStudentPerformanceLoding.hide();
}

function DashGrNewAssessmentValidator()
{
	//validation
	var formDashGetAnalytics = $('#formDashGetAnalytics');

	formDashGetAnalytics.formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        exclude: ':disabled',
        fields: {
            DashAnClassSelect: {
                validators: {
                    notEmpty: {
                        message: 'Class is required'
                    }
                }
            },
            DashAnAssSelect: {
                validators: {
                    notEmpty: {
                        message: 'Subject is required'
                    }
                }
            },
            DashAnAssSelect: {
                validators: {
                    notEmpty: {
                        message: 'Assessment is required'
                    }
                }
            }
        }
    });

}


function getDashAnClassSelected()
{

	var DashAnClassSelect 		= $("#DashAnClassSelect");
	var selected            	= DashAnClassSelect.select2('val');

	var DashAnSubjectSelect 	= $("#DashAnSubjectSelect");

	if( selected >0 )
	{
		var subjects 			= AnGroupedSubjects[selected];
		var subjectsSize 		= subjects.length;

		DashAnSubjectSelect.empty(); 
		DashAnSubjectSelect.append('<option value="0">All</option>');

	    for ( var i=0; i< subjectsSize ; i++) {
	      	DashAnSubjectSelect.append('<option value="' + subjects[i]['id'] + '">' + subjects[i]['subjectname'] + '</option>');
	    }

	}else{

		var DashAnSubjectSelect 	=  $("#DashAnSubjectSelect");
		var DashAnAssSelect 		=  $("#DashAnAssSelect");
		var DashAnPeriodSelect 		=  $("#DashAnPeriodSelect");

		DashAnSubjectSelect.empty(); 
		DashAnSubjectSelect.append('<option value="0">All</option>');
		DashAnSubjectSelect.select2().trigger('change');

		getDashAnLoadAsType();

		var AnPeriodOptions 				= '<option value="0">All</option>';
	    var AnGroupedSchoolTermPeriodsSize 	= AnGroupedSchoolTermPeriods.length;

		for ( var i = 0; i < AnGroupedSchoolTermPeriodsSize	; i++) {

			AnPeriodOptions += '<option value="'+AnGroupedSchoolTermPeriods[i]+'" >'+AnGroupedSchoolTermPeriods[i]+'</option>';
		}

		DashAnPeriodSelect.html( AnPeriodOptions );
		DashAnPeriodSelect.select2().trigger('change');

	}

    DashAnSubjectSelect.select2().trigger('change');

}

function getDashAnLoadAsType()
{	
	var DashAnAssSelect 	 = $("#DashAnAssSelect");
	var AssessmentOptions 	 = '';

	var assessmentIdentifier = "";
	var isTheFirsRecord 	 = true;

	var assessments 			= AnGroupedSchoolAssessmentType;
	var assessmentsSize 		= assessments.length;

	if ( assessmentsSize > 0 )
	 {	

	 	AssessmentOptions 	= '<option value="0">All</option>';

	 	for ( var i = 0; i< assessmentsSize ; i++ ) {

	 		var row = [];

			var id 				= assessments[i].id;
			var name 			= assessments[i].name;
			var isExamType 		= assessments[i].isExamType;

			if ( isExamType == assessmentIdentifier ){
			    AssessmentOptions += '<option value="'+id+'" data-isexamtype="'+isExamType+'" >'+ name + '</option>';
			
			}else{

			    assessmentIdentifier = isExamType;

		    	if ( isTheFirsRecord ){

			    	isTheFirsRecord = false; 

		    	}else{
			    	 AssessmentOptions += '</optgroup>';
			    }

			    var labelName = ( isExamType == 1 ) ? "Exam" : "CAT";

		    	AssessmentOptions += '<optgroup label="'+labelName+'">';
		   		AssessmentOptions += '<option value="'+id+'" data-isexamtype="'+isExamType+'" >'+ name + '</option>';
		
			}
				    
		}

	 }else{

	 	AssessmentOptions += '<option value="-1">Empty</option>';
	 }

    DashAnAssSelect.html(AssessmentOptions);
   	DashAnAssSelect.select2().bind('change', getDashAnAssessmentSelected );
}


function getDashAnSubjectSelected()
{
	var DashAnSubjectSelect 	= $("#DashAnSubjectSelect");
	var selected              	= DashAnSubjectSelect.select2('val');
	
	var DashAnAssSelect 		= $("#DashAnAssSelect");

	if ( selected > 0) 
	{	

		var AssessmentOptions 	 = '';

		var assessmentIdentifier = "";
		var isTheFirsRecord 	 = true;

		var assessments 			= AnGroupedAssessment[selected];
		var assessmentsSize 		= assessments.length;

		if ( assessmentsSize > 0 )
		 {	

		 	AssessmentOptions 	= '<option value="0">All</option>';

		 	for ( var i = 0; i< assessmentsSize ; i++ ) {

				if ( assessments[i]['isExamType'] == assessmentIdentifier ){
				    AssessmentOptions += '<option value="'+assessments[i]['assessmentTypeId']+'" data-isexamtype="'+assessments[i]['isExamType']+'" >'+ assessments[i]['assessmentType'] + '</option>';
				
				}else{

				    assessmentIdentifier = assessments[i]['isExamType'];

			    	if ( isTheFirsRecord ){

				    	isTheFirsRecord = false; 

			    	}else{
				    	 AssessmentOptions += '</optgroup>';
				    }

				    var labelName = ( assessments[i]['isExamType'] == 1 ) ? "Exam" : "CAT";

			    	AssessmentOptions += '<optgroup label="'+labelName+'">';
			   		AssessmentOptions += '<option value="'+assessments[i]['assessmentTypeId']+'" data-isexamtype="'+assessments[i]['isExamType']+'" >'+ assessments[i]['assessmentType'] + '</option>';
			
				}
					    
			}

		 }else{

		 	AssessmentOptions += '<option value="-1">Empty</option>';
		 }

	    DashAnAssSelect.html(AssessmentOptions);
	    DashAnAssSelect.select2().trigger('change');

	}else{

		getDashAnLoadAsType();
	}

}

function getDashAnAssessmentSelected()
{
	
	var DashAnPeriodContainer 	= $("#DashAnPeriodContainer");

	var DashAnSubjectSelect 	= $("#DashAnSubjectSelect");
	var DashAnAssSelect 		= $("#DashAnAssSelect");

	var selectSubject 			= DashAnSubjectSelect.select2('val');
	var selectedAsType     		= DashAnAssSelect.select2('val');
	
	//when subject and assessment type are selected and are not "all" 
	if( selectedAsType > 0 )
	{
		var selectedAsTypeIsExam= DashAnAssSelect.select2().find(":selected").data("isexamtype");

		if ( selectedAsTypeIsExam == 0 )
		{
			DashAnPeriodContainer.slideDown();

			if( selectSubject > 0 )
			{
				var SubjectAsTypePeriod 	= AnGroupedAssPeriods[selectSubject][selectedAsType];
				var SubjectAsTypePeriodSize = SubjectAsTypePeriod.length;

				var DashAnPeriodSelect 	= $("#DashAnPeriodSelect");

				if( SubjectAsTypePeriodSize > 0 )
				{

					DashAnPeriodSelect.empty(); 
					DashAnPeriodSelect.append('<option value="0">All</option>');

				    for ( var i=0; i< SubjectAsTypePeriodSize ; i++) {
				      	DashAnPeriodSelect.append('<option value="' + SubjectAsTypePeriod[i] + '">' + SubjectAsTypePeriod[i] + '</option>');
				    }

				}else{

					DashAnPeriodSelect.empty(); 
					DashAnPeriodSelect.append('<option value="-1">Empty</option>');
				}

				DashAnPeriodSelect.select2().trigger('change');

			}else{
				//dashAnPopulateAssessementType();

			}

		}else{
			//dashAnPopulateAssessementType();
			DashAnPeriodContainer.slideUp();
			
		}

	//
	}else{
		//dashAnPopulateAssessementType();
		DashAnPeriodContainer.show();
	}

}

function DashGrNewAssessment()
{	
	var DashTeNewAssessmentLoding 	= $("#DashTeNewAssessmentLoding");
	var dashNewAssSubjectContainer 	= $("#dashNewAssSubjectContainer");
	var dashNewAssPeriodContainer 	= $("#dashNewAssPeriodContainer");

	dashNewAssSubjectContainer.hide();
	dashNewAssPeriodContainer.hide();
	DashTeNewAssessmentLoding.hide();

	$("#dashNewAssTeachingSetSelect").select2();

	$("#dashNewAssClassSelect").select2({ 
            formatResult: newStudentformatResult,
            formatSelection: newStudentformatSelection
    }).bind('change', getNewAssClassSelected);

	$("#dashNewAssTypeSelect").select2().bind('change', DashGrNewAssessmentUpdate);
	$("#dashNewAssPeriodSelect").select2();

	DashGrNewAssessmentValidator();
}

function DashGrEditAssessment()
{	
	var DashTeEditAssessmentLoding 	= $("#DashTeEditAssessmentLoding");
	DashTeEditAssessmentLoding.hide();

	$("#dashEditAssClassSelect").select2({ 
            formatResult: newStudentformatResult,
            formatSelection: newStudentformatSelection
    }).bind('change', getEditAssClassSelected);

	$("#dashEditAssTypeSelect").select2();
	$("#dashEditAssPeriodSelect").select2();
	
	$("#EditAssDate").datepicker({
	        "autoclose": true
	});

	DashGrUpdateAssessmentValidator();
}

function DashGrUpdateAssessmentValidator()
{
	//validation
	var formDashTeEditAssessment = $('#formDashTeEditAssessment');
	formDashTeEditAssessment.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                EditAssClass: {
                    validators: {
                        notEmpty: {
                            message: 'Class is required'
                        }
                    }
                },
                EditAssSubject: {
                    validators: {
                        notEmpty: {
                            message: 'Subject is required'
                        }
                    }
                },
                EditAssType: {
                    validators: {
                        notEmpty: {
                            message: 'Type is required'
                        }
                    }
                },
                EditAssMaximum: {
                    validators: {
                        notEmpty: {
                            message: 'Maximum is required'
                        },
                        integer: {
                            message: 'The value should be numeric',
                            // The default separators
                            thousandsSeparator: '',
                            decimalSeparator: '.'
                        }
                    }
                },
                 EditAssPeriod: {
                    validators: {
                        notEmpty: {
                            message: 'Period is required' 
                        }
                    }
                },
                EditAssMaximum: {
                    validators: {
                        notEmpty: {
                            message: 'Maximum is required'
                        }
                    }
                },
                EditAssDate: {
                    validators: {
                        notEmpty: {
                            message: 'Date is required'
                        }
                    }
                }
            }
        }).find('[name="EditAssDate"]').change(function(e) {

	        formDashTeEditAssessment.formValidation('revalidateField', 'EditAssDate');

	    }).end()
	    .on('success.form.fv', function(e) {

        	e.preventDefault();

        	var EditAssessment   = $("#EditAssessmentID");
        	EditAssessmentID     = EditAssessment.val();

        	var frm = formDashTeEditAssessment;
			var url = base_url+"api/Gr/Assessment/"+EditAssessmentID; 
			
			var DashTeEditAssessmentLoding = $("#DashTeEditAssessmentLoding");
			
			formDashTeEditAssessment.hide();
			DashTeEditAssessmentLoding.slideDown();

		    $.ajax({
		            url: url,
		            type: 'PATCH',
		            data: frm.serialize() ,
		            success: function(data) {

		            	formDashTeEditAssessment.slideDown();
						DashTeEditAssessmentLoding.hide();

		               if ( data.success )
		               {
		               		
		               		$('#Model_EditAssessment').modal('toggle');

		                    $.gritter.add({
		                      title: 'Success',
		                      text: 'Assessment updated.',
		                      class_name: 'success gritter-center',
		                      time: ''
		                    });

		                    onDashLoadResultSection(0 , false , null, null, null );

		               }else{

		                 $.gritter.add({
		                    title: 'Failed',
		                    text: 'Failed to update assessment, Something went wrong it might already exist.',
		                    class_name: 'danger gritter-center',
		                    time: ''
		                  });

		             }
		        },
		        error: function(){

		        	formDashTeEditAssessment.slideDown();
					DashTeEditAssessmentLoding.hide();

				   $.gritter.add({
			            title: 'Failed',
			            text: 'Failed to update, Try again later.',
			            class_name: 'danger gritter-center',
			            time: ''
			          });
				}

		    });

	    }).end();

}
function getEditAssClassSelected()
{	
	var dashEditAssSubjectContainer = $("#dashEditAssSubjectContainer");
	var dashEditAssClassSelect 		= $("#dashEditAssClassSelect");

	var selected              		= dashEditAssClassSelect.select2('val');

	var subjects 			  		= GroupedSubjects[selected];
	var subjectsSize 		  		= subjects.length;

	var dashEditAssSubjectSelect 	= $("#dashEditAssSubjectSelect");

	dashEditAssSubjectSelect.empty(); 
	dashEditAssSubjectSelect.append('<option ></option>');

    for (var i=0; i< subjectsSize ; i++) {
      dashEditAssSubjectSelect.append('<option value="' + subjects[i]['anClsrmSbjtID'] + '">' + subjects[i]['subjectname'] + '</option>');
    }

    dashEditAssSubjectContainer.slideDown();

    dashEditAssSubjectSelect.select2().bind('change', DashGrNewAssessmentUpdate);

}

function getNewAssClassSelected()
{	
	var dashNewAssSubjectContainer 	= $("#dashNewAssSubjectContainer");
	var dashNewAssClassSelect 		= $("#dashNewAssClassSelect");


	var selected              		= dashNewAssClassSelect.select2('val');

	var Class_Level  				= dashNewAssClassSelect.select2().find(":selected").data("level");
	var Class_Year 					= dashNewAssClassSelect.select2().find(":selected").data("year");


	var dashNewAssTypeContainer     = $("#dashNewAssTypeContainer") ;
	var dashNewAssMaximumContainer  = $("#dashNewAssMaximumContainer") ;
	var NurseryLastYearAppreciation = $("#NurseryLastYearAppreciation") ;

	var NurseryLastYear 			= NurseryLastYearAppreciation.val();

	if ( Class_Level == 1 && ( Class_Year<= NurseryLastYear ) )
	 {
	 	dashNewAssTypeContainer.hide();
	 	dashNewAssMaximumContainer.hide();

	 }else{
	 	dashNewAssTypeContainer.show();
	 	dashNewAssMaximumContainer.show();

	 }
	
	var subjects 			  		= GroupedSubjects[selected];
	var subjectsSize 		  		= subjects.length;

	var dashNewAssSubjectSelect 	= $("#dashNewAssSubjectSelect");
	dashNewAssSubjectSelect.empty();

	//*
	var subjectOptions = '';
    subjectOptions +='<option ></option>';

    var subjectType 	= "";
	var isTheFirsRecord = true;

	for (var i = 0; i < subjectsSize; i++) {

		// if (subjectType == subjects[i]['subjectTypeID'])
		// {
			subjectOptions +='<option value="'+subjects[i]['anClsrmSbjtID']+'" >' +subjects[i]['subjectname']+ '</option>';

		// }else{

		// 	subjectType = subjects[i]['subjectTypeID'];

		// 	if (isTheFirsRecord)
		// 	{
		// 		isTheFirsRecord = false;

		// 	} else{
		// 		subjectOptions +='</optgroup>';
		// 	}

		// 	subjectOptions +='<optgroup label="'+subjects[i]['subjectType']+'">';
		// 	subjectOptions +='<option value="'+subjects[i]['anClsrmSbjtID']+'">' +subjects[i]['subjectname']+ '</option>';
		// }

    }

	//
    dashNewAssSubjectSelect.append(subjectOptions);
    dashNewAssSubjectContainer.slideDown();

    dashNewAssSubjectSelect.select2().bind('change', DashGrNewAssessmentUpdate);

}

function DashGrEditAssessmentUpdate()
{
	
	var dashEditAssSubjectSelect 	= $("#dashEditAssSubjectSelect");
	var dashEditAssTypeSelect 		= $("#dashEditAssTypeSelect");

	var selectedClassSubject        = dashEditAssSubjectSelect.select2('val');
	var selectedAsType              = dashEditAssTypeSelect.select2('val');
	var isexamType   				= dashEditAssTypeSelect.select2().find(":selected").data("isexamtype");

	if ( isexamType == 0 )
	 {
	 	if( ( selectedClassSubject > 0)  && ( selectedAsType > 0 ) )
		{	
			var dashEditAssPeriodContainer = $("#dashEditAssPeriodContainer");
			var dashEditAssPeriodSelect    = $("#dashEditAssPeriodSelect");

			dashEditAssPeriodSelect.empty();

			var ClassSubjectAsDone = GroupedClassSubjectPeriodNumber[selectedClassSubject][selectedAsType];

			for (var i = 1; i <= 10 ; i++) {

				if ( $.inArray( i.toString() , ClassSubjectAsDone ) < 0  ) {

		        	dashEditAssPeriodSelect.append('<option value="'+i+'">' +i+ '</option>');

		        }
			}

			dashEditAssPeriodSelect.select2().trigger('change');
			dashEditAssPeriodContainer.slideDown();
		    
		}

	 }else{

	 	$("#dashEditAssPeriodContainer").hide();
	 	$("#dashEditAssPeriodSelect").append('<option value="1" selected>1</option>');

	 }
	
}

function DashGrNewAssessmentUpdate()
{

	var IsTeachingSet 				= $("#IsTeachingSet");

	var dashNewAssTeachingSetSelect = $("#dashNewAssTeachingSetSelect");
	var dashNewAssSubjectSelect 	= $("#dashNewAssSubjectSelect");
	var dashNewAssTypeSelect 		= $("#dashNewAssTypeSelect");

	var IsTeachingSetValue 			= IsTeachingSet.val();

	var selectedStudyGroup          = dashNewAssTeachingSetSelect.select2('val');
	var selectedClassSubject        = dashNewAssSubjectSelect.select2('val');
	var selectedAsType              = dashNewAssTypeSelect.select2('val');
	var isexamType   				= dashNewAssTypeSelect.select2().find(":selected").data("isexamtype");


	if ( isexamType == 0 )
	 {

	 	if( ( (selectedClassSubject > 0)  && (selectedAsType > 0) ) || ( (selectedStudyGroup > 0)  && (selectedAsType > 0) ) )
		{	
			var dashNewAssPeriodContainer = $("#dashNewAssPeriodContainer");
			var dashNewAssPeriodSelect 	  = $("#dashNewAssPeriodSelect");
			var AssessmentSchool 	      = $("#AssessmentSchool");
			var AssessmentSchoolValue     = AssessmentSchool.val();

			dashNewAssPeriodSelect.empty();

			if (AssessmentSchoolValue == 5 )
			 {																
				dashNewAssPeriodSelect.append('<option value="1">1 (CWI)</option>');
		 		dashNewAssPeriodSelect.append('<option value="2">2 (MTM)</option>');
		 		dashNewAssPeriodSelect.append('<option value="3">3 (CWII)</option>');

			 }else{

			 	if ( IsTeachingSetValue == 1 )
			 	{
			 		var ClassSubjectAsDone = GroupedClassSubjectPeriodNumber[selectedStudyGroup][selectedAsType];
			 	}else{

			 		var ClassSubjectAsDone = GroupedClassSubjectPeriodNumber[selectedClassSubject][selectedAsType];
			 	}

				for (var i = 1; i <= 10 ; i++) {

					if ( $.inArray( i.toString() , ClassSubjectAsDone ) < 0  ) {

			        	dashNewAssPeriodSelect.append('<option value="'+i+'">' +i+ '</option>');

			        }
				}
			 }

			dashNewAssPeriodSelect.select2().trigger('change');
			dashNewAssPeriodContainer.slideDown();
		    
		}

	 }else{

	 	$("#dashNewAssPeriodContainer").hide();
	 	$("#dashNewAssPeriodSelect").append('<option value="1" selected>1</option>');

	 }
	
}

function DashGrNewAssessmentValidator()
{
	//validation
	var formDashTeNewAssessment = $('#formDashTeNewAssessment');

	formDashTeNewAssessment.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
            	NewAssTeachingSet: {
                    validators: {
                        notEmpty: {
                            message: 'Teaching Set is required'
                        }
                    }
                },
                NewAssClass: {
                    validators: {
                        notEmpty: {
                            message: 'Class is required'
                        }
                    }
                },
                NewAssSubject: {
                    validators: {
                        notEmpty: {
                            message: 'Subject is required'
                        }
                    }
                },
                NewAssType: {
                    validators: {
                        notEmpty: {
                            message: 'Type is required'
                        }
                    }
                },
                NewAssMaximum: {
                    validators: {
                        notEmpty: {
                            message: 'Maximum is required'
                        },
                        integer: {
                            message: 'The value should be numeric',
                            // The default separators
                            thousandsSeparator: '',
                            decimalSeparator: '.'
                        }
                    }
                },
                // NewAssDate: {
                //     validators: {
                //         notEmpty: {
                //             message: 'Date is required'
                //         }
                //     }
                // },
            }
        }).on('success.form.fv', function(e) {

        	e.preventDefault();

        	var frm = formDashTeNewAssessment;
			var url = base_url+"api/Gr/Assessment"; 
			
			var IsTeachingSetValue 		= $("#IsTeachingSetValue").val() ;

			var DashTeNewAssessmentLoding = $("#DashTeNewAssessmentLoding");
			
			formDashTeNewAssessment.hide();
			DashTeNewAssessmentLoding.slideDown();

			var dashNewAssClassSelect   = $("#dashNewAssClassSelect");
			var dashNewAssSubjectSelect = $("#dashNewAssSubjectSelect");

			var selected              		= dashNewAssClassSelect.select2('val');

			var Class_Level  				= dashNewAssClassSelect.select2().find(":selected").data("level");
			var Class_Year 					= dashNewAssClassSelect.select2().find(":selected").data("year");
			var Class_Name 					= dashNewAssClassSelect.select2('data').text;	 			

			var yearname   					= dashNewAssClassSelect.select2().find(":selected").data("yearname");
			var Subject_Name 				= dashNewAssSubjectSelect.select2('data').text;

			var Selected_ClassID 			= dashNewAssClassSelect.select2('val');
			var Selected_ClassSubjectID 	= dashNewAssSubjectSelect.select2('val');

			var content = '';

		      $.post(url, 
		         frm.serialize(), 
		         function(data, status, xhr){

		         	$('#Model_newALevelOption').modal('toggle');

					DashTeNewAssessmentLoding.hide();
		         	formDashTeNewAssessment.slideDown();

		           if (data.success)
		                {   

		                	$('#Model_NewAssessment').modal('toggle');

		                	var IsAppreciation = data.IsAppreciation ;

		                	if ( IsAppreciation )
		                	 {
		                	 	var Appreciation_Type = data.Appreciation_Type ;
		                	 	var SelectResults     = data.SelectResults ;

		                	 	if ( Appreciation_Type == 1 )
		                	 	 {

		                	 	 	content += '<table class="table table-striped" id="dashDiMetricCondactTable" ><thead><tr class="text-center"><th>Student Name</th><th class="text-center">Reg. Number</th><th class="text-center">TRES BIEN</th><th class="text-center">BIEN</th><th class="text-center">ASSEZ BIEN</th></tr></thead><tbody>';

							            $.each( SelectResults, function(i, item) {

							                content +="<tr class_id="+Selected_ClassID+" class_subject_id="+Selected_ClassSubjectID+" >";
							                  content +="<td>"+item.studentNames+"</td>";
							                  content +="<td class='text-center'>"+item.RegNumber+"</td>";

							                  if ( item.statusID == 1 ) {
							                    content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='1' checked /></td>";
							                  }else{
							                    content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='1' /></td>";
							                  }

							                  if ( item.statusID == 2 ) {
							                     content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='2' checked /></td>";

							                  }else{
							                     content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='2' /></td>";

							                  }

							                   if ( item.statusID == 3 ) {
							                     content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='2' checked /></td>";

							                  }else{
							                     content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='2' /></td>";

							                  }

							                  content +="</td>";
							                content +="</tr>";

							              });
							          
							            content +='</tbody></table>';


		                	 	 }else{

		                	 	 	content += '<table class="table table-striped" id="dashDiMetricCondactTable" ><thead><tr class="text-center"><th>Student Name</th><th class="text-center">Reg. Number</th><th class="text-center">Excellent</th><th class="text-center">Very Good</th><th class="text-center">Good</th><th class="text-center">Fair</th></tr></thead><tbody>';

							            $.each( SelectResults, function(i, item) {

							                content +="<tr class_id="+Selected_ClassID+" class_subject_id="+Selected_ClassSubjectID+" >";
							                  content +="<td>"+item.studentNames+"</td>";
							                  content +="<td class='text-center'>"+item.RegNumber+"</td>";

							                  if ( item.statusID == 4 ) {
							                    content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='4' checked /></td>";
							                  }else{
							                    content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='4' /></td>";
							                  }

							                  if ( item.statusID == 5 ) {
							                     content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='5' checked /></td>";

							                  }else{
							                     content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='5' /></td>";

							                  }

							                  if ( item.statusID == 6 ) {
							                    content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='6' checked /></td>";
							                  }else{
							                    content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='6' /></td>";
							                  }

							                  if ( item.statusID == 7 ) {
							                    content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='7' checked /></td>";
							                  }else{
							                    content +="<td class='text-center'><input type='radio' class='studentRepStatus' name='studentRepStatus["+item.RegNumber+"]' value='7' /></td>";
							                  }
							                  
							                  content +="</td>";
							                content +="</tr>";

							              });
							          
							            content +='</tbody></table>';

		                	 	 }

		                	 	var dashGradebook_contentAnalytics 	= $("#dashGradebook_contentAnalytics")
								var dashGradebook_contentOther 		= $("#dashGradebook_contentOther");

								dashGradebook_contentAnalytics.hide();
								dashGradebook_contentOther.slideDown();

								dashGradebook_contentOther.html('<div class="row"><div class="col-sm-10 col-sm-offset-1 text-center"><h4><b>'+yearname+' : '+Subject_Name+'</b></h4></div></div><div class="row"><div class="col-sm-10 col-sm-offset-1">'+content+'</div></div>');

								$( ".studentRepStatus" ).change(function() {

					                Input_Name  = $(this).attr("name") ;
					                Input_Value = $(this).val() ;

					                var radioButtons = $(this) ;

					                var selectedIndex = radioButtons.index(radioButtons.filter(':checked'));

					                  var url     = base_url+"api/Gr/Appreciation?ClassID="+Selected_ClassID+"&ClassSubjectID="+Selected_ClassSubjectID+"&Name="+Input_Name+"&Value="+Input_Value;
					                  $.ajax({
					                        url: url,
					                        type: 'POST',
					                        success: function(data) {

					                    },
					                    error: function(){
					                    }

					                });

					            });


		                	 }else{

		                	 	$.gritter.add({
									title: 'Success',
									text: 'Assessment added.',
									class_name: 'success gritter-center',
									time: ''
								});

			                    var AssessmentID 		= data.assessmentId;
			                    var IsTeachingSetValue	= data.IsTeachingSet;;
			                    var NewAssStudyGroup	= data.NewAssStudyGroup;
			                    var NewAssClass 		= data.NewAssClass;
			                    var NewAssSubjectID 	= data.NewAssSubject;
			                    var NewAssSubjectID 	= data.NewAssSubject;
			                    var NewAssType 			= data.NewAssType;
			                    var NewAssPeriod 		= data.NewAssPeriod;

			                    var TeacherName 		= data.TeacherName ;
			                    var AssessmentMax   	= data.AssessmentMax ;


								//add assessment number
									if ( IsTeachingSetValue == 1 )
								 	{
								 		GroupedClassSubjectPeriodNumber[NewAssStudyGroup][NewAssType].push( NewAssPeriod );
								 	}else{

								 		GroupedClassSubjectPeriodNumber[NewAssSubjectID][NewAssType].push( NewAssPeriod );
								 	}

								
								DashGrNewAssessmentReset();

								//Load Marks 
								onDashLoadResultSection( AssessmentID , true, AssessmentMax, TeacherName );

								//
								var getResultClassSelect 		= $("#getResultClassSelect");
								var getResultSubjectSelect 		= $("#getResultSubjectSelect");
								var getResultAssessmentSelect 	= $("#getResultAssessmentSelect");

								//
								getResultClassSelect.val( NewAssClass ).trigger('change');
								getResultSubjectSelect.val( NewAssSubjectID ).trigger('change');
								getResultAssessmentSelect.val( AssessmentID ).trigger('change');


		                	 }

		                } else {

		                    $.gritter.add({
								title: 'Already created',
								text: 'Choose it from the left menu.',
								class_name: 'danger gritter-center',
								time: ''
							});

		                }

		         });
				
	        }).end();

}

function DashGrNewAssessmentReset()
{	
	var dashNewAssTeachingSetSelect = $('#dashNewAssTeachingSetSelect');
	var dashNewAssClassSelect 		= $('#dashNewAssClassSelect');
	var dashNewAssSubjectSelect 	= $('#dashNewAssSubjectSelect');
	var dashNewAssTypeSelect 		= $('#dashNewAssTypeSelect');
	var dashNewAssPeriodSelect 		= $('#dashNewAssPeriodSelect');
	var dashNewAssMaximum       	= $('#dashNewAssMaximum');

	dashNewAssTeachingSetSelect.select2('val', '');
	dashNewAssClassSelect.select2('val', '');
	dashNewAssSubjectSelect.select2('val', '');
	dashNewAssTypeSelect.select2('val', '');
	dashNewAssPeriodSelect.select2('val', '');
	dashNewAssMaximum.val('');

	var dashNewAssSubjectContainer 	= $("#dashNewAssSubjectContainer");
	var dashNewAssPeriodContainer 	= $("#dashNewAssPeriodContainer");
	var DashTeNewAssessmentLoding 	= $("#DashTeNewAssessmentLoding");

	dashNewAssSubjectContainer.hide();
	dashNewAssPeriodContainer.hide();
	DashTeNewAssessmentLoding.hide();

}

var dashGrAnTableData = [];

function getResultsAnalytics()
{
	$('#formDashGetAnalytics').on('submit', function(e){

		var container = $("#dashGrStudentPerformanceContainer");
		var dashGrStudentPerformanceLoding = $("#dashGrStudentPerformanceLoding");
		
		container.hide();
		dashGrStudentPerformanceLoding.slideDown();
		$("#dashGrLoadingText").html("Loading...");
		
		var frm = $('#formDashGetAnalytics');
		var url = base_url+"api/Gr/Analytics"; 
		
		var content   = '';
		var DasAnGrStudentListprintArea = '';

	      e.preventDefault();
	      $.post(url, 
	         frm.serialize(), 
	         function( data, status, xhr ){

	         	container.empty();

	         	content +='<div class="row"><div class="col-sm-12">';
	         	content +='</div></div>';

	         	content +='<div class="row"><div class="col-sm-12">';
			   		content +='<table class="table" id="dashGrAnTable" ><thead><tr><th>Student Name</th><th class="text-center">Registration Number</th><th class="text-center">Percentage</th></thead><tbody>';
			 			
		 			$.each(data, function(i, item) {

				        content +="<tr id='"+item.studRegNber+"' >";
				          content +='<td >'+item.studentNames+'</td>';
				          content +='<td class="text-center">'+item.studRegNber+'</td>';
				          content +='<td class="text-right">'+item.score+'%</td>';
				        content +="</tr>";

				    });	

			    	content +='</tbody></table>';
			    content +='</div></div>';

				    DasAnGrStudentListprintArea +="</tbody>";
			    DasAnGrStudentListprintArea +="</table></div></div>";

				container.html(content);

				dashGrStudentPerformanceLoding.hide();
				container.slideDown();

				dashGrAnTableData = $('#dashGrAnTable').dataTable({
				    "aaSorting": [],
					    language: {
				        searchPlaceholder: "Search here... "
				    }
				});

	        });

    });
}

function getDashGrAnMessage()
{
	//validation
	var formDashGrAnMessage = $('#formDashGrAnMessage');

	formDashGrAnMessage.formValidation({
    framework: 'bootstrap',
    icon: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
    },
    exclude: ':disabled',
    fields: {
        subject: {
            validators: {
                notEmpty: {
                    message: 'Subject is required'
                }
            }
        },
        message: {
            validators: {
                notEmpty: {
                    message: 'Message is required'
                }
            }
        }
    }
}).on('success.form.fv', function(e) {

	e.preventDefault();
	
	var dashGrStudentPerformanceContainer   = $("#dashGrStudentPerformanceContainer");
	var dashGrStudentPerformanceLoding 		= $("#dashGrStudentPerformanceLoding");
	var dashGrLoadingText 			  		= $("#dashGrLoadingText");
	var Model_DashGrAnMessage               = $("#Model_DashGrAnMessage");

	dashGrStudentPerformanceContainer.hide();
	dashGrStudentPerformanceLoding.slideDown();
	dashGrLoadingText.html("Sending, please wait...")
	Model_DashGrAnMessage.modal('toggle');

	var AnData 			 = [];

	var columnNames 	 = dashGrAnTableData.api().column([0]).data();
	var columnReg 		 = dashGrAnTableData.api().column([1]).data();
	var columnPercentage = dashGrAnTableData.api().column([2]).data();

	columnReSize 		 = columnReg.length;

	for (var i = 0; i < columnReSize; i++) {

		AnData[i] = new Array();

		var row = [];
		row[0]  = columnNames[i];
		row[1]  = columnReg[i];
		row[2] 	= columnPercentage[i];

		AnData[i].push(row);

	};

	var andata = JSON.stringify(AnData);

	var frm = formDashGrAnMessage;
	var url = base_url+"api/Gr/Analytics/Message?andata="+andata; 

	    $.post(
	     url, 
	  	 frm.serialize(), 
	     function(data, status, xhr){

	       if (data.success)
	        {   	
	        	getSentMessageAfterSubmit(data.threadID,data.Subject);
	        	
	            $.gritter.add({
					title: 'Success',
					text: 'Message Sent.',
					class_name: 'success gritter-center',
					time: ''
				});

	        } else {

	            $.gritter.add({
					title: 'Failed',
					text: 'Failed to send messages.',
					class_name: 'danger gritter-center',
					time: ''
				});

	            dashGrStudentPerformanceContainer.slideDown();
	            dashGrStudentPerformanceLoding.hide();
	            dashGrLoadingText.modal('show');
	        }

	     });


    }).end();

}

function onDashLoadSendResultsSection( loadAllTwoSection ){

	var dashGradebook_contentAnalytics 	= $("#dashGradebook_contentAnalytics")
	var dashGradebook_contentOther 		= $("#dashGradebook_contentOther");

	var Content       = '';
	var LeftContent   = '';
	var RightContent  = '';

	if ( loadAllTwoSection) {

		Content 	+= '<div  class="col-sm-12">';
			Content  	+= '<div  class="col-sm-3" id="GrSendResultsSelectionContainer">'; 
				LeftContent  	+= '<div class="row text-center"><h4>Send results to parents</h4></div>'; 
				LeftContent  	+= '<form id="formDashGrSendResults" class="form-horizontal top-buffer" role="form" >';
					LeftContent  	+= '<input type="hidden" id="GrSendResultsOnlyYears" name="GrSendResultsOnlyYears" value="0" />';
					LeftContent   	+= '<div class="form-group"><label class="col-sm-3 control-label">Send via</label><div class="col-sm-1"></div><div class="col-sm-7"><div class="radio"><label><input type="radio" name="GrSendResultsVia" value="1" checked /> Email</label></div></div></div>';
					LeftContent  	+= '<div class="form-group"><label class="col-sm-3 control-label">Type</label><div class="col-sm-7"><select name="GrSendResultsType" id="selectGrSendResultsType" class="form-control select2-input" ></select></div></div>';
					LeftContent  	+= '<div class="form-group" id="GrSendResultsPeriodContainer"><label class="col-sm-3 control-label">Period</label><div class="col-sm-7"><select name="GrSendResultsPeriod" id="selectGrSendResultsPeriod" class="form-control select2-input" ></select></div></div>';
					LeftContent  	+= '<div class="form-group" id="GrSendResultsYearContainer"><label class="col-sm-3 control-label">Year</label><div class="col-sm-7"><select name="GrSendResultsYear" id="selectSendResultsYear" class="form-control select2-input" ></select></div></div>';
					LeftContent  	+= '<div class="form-group" id="GrSendResultsClassContainer"><label class="col-sm-3 control-label">Class</label><div class="col-sm-7"><select name="GrSendResultsClass" id="selectGrSendResultsClass" class="form-control select2-input" ></select></div></div>';
					LeftContent  	+= '<div class="form-group" id="GrSendResultsStudentsontainer"><label class="col-sm-3 control-label">Students</label><div class="col-sm-7"><select name="GrSendResultsStudents" id="selectGrSendResultsStudents" class="form-control select2-input" ></select></div></div>';
					LeftContent 	+= '<div class="form-group top-buffer" id="GrSendResultsLoadingContainer" ><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-default btn-block" >Sending...</button></div></div>';
					LeftContent 	+= '<div class="form-group top-buffer" id="GrSendResultsSubmitContainer"><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-primary btn-block" type="submit" id="GrSendResultsStudentsSubmit" >Send Result to parents</button></div></div>';
				LeftContent 	+= '</form>';
			Content 	+= '</div>';
			Content 	+= '<div class="col-sm-9" id="dashGrSentResultsSection"><center><span class="top-buffer">Loading Sent Results...</span></center></div>';
		Content 	+= '</div>';

		dashGradebook_contentOther.html(Content);

		dashGradebook_contentAnalytics.hide();
	    dashGradebook_contentOther.slideDown();

    }else{

		$("#dashGrSentResultsSection").html('<center><span class="top-buffer">Loading...</span></center>');

		var GrSendResultsLoadingContainer = $("#GrSendResultsLoadingContainer");
		GrSendResultsLoadingContainer.hide();

		var GrSendResultsSubmitContainer  = $("#GrSendResultsSubmitContainer");
		GrSendResultsSubmitContainer.hide();

	}

	var url = base_url+"api/Gr/SendResults?loadAllTwoSection="+loadAllTwoSection; 

	//get 
	$.getJSON( url, function(data) {

		var ResultsSentToParents 	 = data.ResultsSentToParents ;
		var ResultsSentToParentsSize = ResultsSentToParents.length ;

			RightContent    = '';
			RightContent  	+= '<div class="row text-center">';
				RightContent  	+= '<div class="col-sm-12">';
					RightContent  	+= '<div class="col-sm-9"><h4>Recent Sent Results</h4></div>';
					RightContent  	+= '<div class="col-sm-3"><a onclick="onDashLoadSendResultsSection(false)" href="#"><i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh to the table below</a></div>';
				RightContent  	+= '</div>';
			RightContent  	+= '</div>';
			RightContent  	+= '<div class="row">';
				RightContent  += '<table class="table table-striped" id="tbGrSentResults" ><thead><tr><th class="row text-left">Sent on</th><th class="row text-left">Class</th><th class="row text-left">Student</th><th class="row text-left">Type</th><th class="row text-left">Via</th><th class="row text-right">Total</th><th class="row text-center">Pending</th><th class="row text-center">Sent</th><th class="row text-center">Failed</th><th class="row text-center">Comments</th><th class="row text-center">Modified Reports</th></tr></thead><tbody>';		
				
				//Report Cards 
				for ( var i = 0; i < ResultsSentToParentsSize; i++) {

					var  SendResults_id     		= ResultsSentToParents[i].id ;
					var  SendResults_created_at 	= ResultsSentToParents[i].created_at;
					var  SendResults_className 		= ResultsSentToParents[i].className;
					var  SendResults_studRegNber  	= ResultsSentToParents[i].studRegNber;
					var  SendResults_studentNames   = ResultsSentToParents[i].studentNames;
					var  SendResults_typeName 		= ResultsSentToParents[i].typeName;
					var  SendResults_senderName 	= ResultsSentToParents[i].senderName;
					var  SendResults_via 			= ResultsSentToParents[i].via;

					var  NumberOfOfAllMessages 		= ResultsSentToParents[i].NumberOfOfAllMessages;
					var  NumberOfPending 			= ResultsSentToParents[i].NumberOfPending;
					var  NumberOfSent  				= ResultsSentToParents[i].NumberOfSent;
					var  NumberOfFailed  			= ResultsSentToParents[i].NumberOfFailed;
					var  NumberOfMissingPhoneNumber = ResultsSentToParents[i].NumberOfMissingPhoneNumber;
					var  NumberOfComment    		= ResultsSentToParents[i].NumberOfComment;

					RightContent +="<tr id="+SendResults_id+" >";
			            RightContent +="<td class='row text-left' >"+date_HM_moment(SendResults_created_at)+"</td>";
			            RightContent +="<td class='row text-left'>"+SendResults_className+"</td>";
			            if (SendResults_studRegNber)
			             {
			             	RightContent +="<td class='row text-left'>"+SendResults_studentNames+"</td>";
			             }else{
			             	RightContent +="<td class='row text-center'>All</td>";
			             }
			            RightContent +="<td class='row text-left'>"+SendResults_typeName+"</td>";

			            if ( SendResults_via == 1 ) {
			            	RightContent +="<td class='row text-center'><span class='badge bg-green'> Email</span></td>";

			            }else if( SendResults_via == 0 ){
			            	RightContent +="<td class='row text-center'><span class='badge bg-blue'> SMS</span></td>";

			            }else{
			            	RightContent +="<td class='row text-center'></td>";

			            }

		             	if ( NumberOfOfAllMessages == 0 ) {
			            	RightContent +="<td class='row text-center'>Pending..</td>";

			            }else{
			            	RightContent +="<td class='row text-center text-blue GrSentResultsAllMessage'><a href='#'>"+NumberOfOfAllMessages+" Report(s)</a></td>"; 
			            }
			            
		             	RightContent +="<td class='row text-center'>"+NumberOfPending+"</td>"; 
		             	RightContent +="<td class='row text-center'>"+NumberOfSent+"</td>";
		             	RightContent +="<td class='row text-center'>"+NumberOfFailed+"</td>";
		             	RightContent +="<td class='row text-center text-blue GrSentResultsAllComments'><a href='#'>"+NumberOfComment+" Comment(s)</a></td>";
		             	RightContent +="<td class='row text-center text-green GrSendUpdates'><a href='#'><span class='badge bg-maroon'> Notify Changes</span></a></td>";
			        RightContent +="</tr>";

				}

			RightContent +="</tbody></table>";
		RightContent  += '</div>';

		if ( loadAllTwoSection) {
			$("#GrSendResultsSelectionContainer").html(LeftContent);
		}
		$("#dashGrSentResultsSection").html(RightContent);

		var GrSendResultsLoadingContainer = $("#GrSendResultsLoadingContainer");
		GrSendResultsLoadingContainer.hide();

		var GrSendResultsSubmitContainer  = $("#GrSendResultsSubmitContainer");
		GrSendResultsSubmitContainer.show();

		var GrSendResultsPeriodContainer = $("#GrSendResultsPeriodContainer");
	    var GrSendResultsYearContainer = $("#GrSendResultsYearContainer");
		var GrSendResultsClassContainer= $("#GrSendResultsClassContainer");
		
		if ( loadAllTwoSection) {

			GrSendResultsPeriodContainer.hide();
			GrSendResultsYearContainer.hide();
			GrSendResultsClassContainer.show();

		}

		abTablePagingTable("tbGrSentResults");

			if ( loadAllTwoSection ) {

				var SchoolReportType  	= data.SchoolReportType ;
				var Classrooms  		= data.Classrooms ;
				var Students 			= data.Students ;
				var SchoolPeriods 		= data.SchoolPeriods ;
				var OnlyYears 			= data.OnlyYears ;
				var Years 				= data.Years ;

				var SchoolReportTypeSize= SchoolReportType.length ;
				var ClassroomsSize 		= Classrooms.length ;
				var StudentsSize 		= Students.length ;
				var SchoolPeriodsSize 	= SchoolPeriods.length ;
				var YearsSize 			= Years.length;

				var GrSendResultsOnlyYears = $("#GrSendResultsOnlyYears");
				
				if ( OnlyYears ) {

					GrSendResultsOnlyYears.val(1);

					GrSendResultsYearContainer.show();
					GrSendResultsClassContainer.hide();

				}else{

					GrSendResultsOnlyYears.val(0) ;

					GrSendResultsYearContainer.hide();
					GrSendResultsClassContainer.show();
				}

			//Populate Report Type 
				var selectGrSendResultsType = $("#selectGrSendResultsType");
			    selectGrSendResultsType.empty();
			    var AnReportTypeOptions	= '<option ></option>';

			    for ( var i=0; i< SchoolReportTypeSize ; i++ ) {
			    	AnReportTypeOptions += '<option value="'+SchoolReportType[i].id+'">'+SchoolReportType[i].name+'</option>'; 
			    }

			    selectGrSendResultsType.html( AnReportTypeOptions );
				selectGrSendResultsType.select2().bind('change', getGrSendReportTypeSelected );

			//Populate Student Class
				GroupedStudentsClassArray 		= [[]];

				for ( var i = 0; i < ClassroomsSize; i++) {

					GroupedStudentsClassArray[Classrooms[i].id] = new Array();
				}

				for ( var i = 0; i < StudentsSize; i++) {

					var personID 	= Students[i].personID;
					var regNumber   = Students[i].studRegNber;
					var studentNames= Students[i].studentNames;
					var classId 	= Students[i].classId;	

					var row = [];

					row['personID'] 	= personID;
					row['regNumber'] 	= regNumber;
					row['studentNames'] = studentNames;

					GroupedStudentsClassArray[classId].push(row);

				}

			//populate year  
				var selectSendResultsYear = $("#selectSendResultsYear");
			    selectSendResultsYear.empty();

				var AnYearOptions 	  = '';
					AnYearOptions 	  = '<option ></option>';

				var AnYearidentifier 	   = "";
				var AnisTheFirstYearRecord = true;

				for ( var i=0; i< YearsSize ; i++ ) {

					if ( Years[i].levelID == AnYearidentifier ){
					    AnYearOptions += '<option value="'+Years[i].levelID+'_'+Years[i].year+'" >'+Years[i].year+'</option>';
					
					}else{

					    AnYearidentifier = Years[i].levelID;
				    	if ( AnisTheFirstYearRecord ){

					    	AnisTheFirstYearRecord = false; 

				    	}else{
					    	AnYearOptions += '</optgroup>';
					    }

				    	AnYearOptions += '<optgroup label="'+Years[i].levelName+'">';
				   		AnYearOptions += '<option value="'+Years[i].levelID+'_'+Years[i].year+'"  >'+Years[i].year+'</option>';
				
					}
						    
				}

				selectSendResultsYear.html( AnYearOptions );
				selectSendResultsYear.select2();

			//populate class 
				var selectGrSendResultsClass = $("#selectGrSendResultsClass");
			    selectGrSendResultsClass.empty();

				var AnClassOptions 	  = '';
					AnClassOptions 	  = '<option ></option>';

				var Anclassidentifier = "";
				var AnisTheFirsRecord = true;

				for ( var i=0; i< ClassroomsSize ; i++ ) {

					if ( Classrooms[i].classidentifier == Anclassidentifier ){
					    AnClassOptions += '<option value="'+Classrooms[i].id+'" data-yearname="'+Classrooms[i].YearName+'" data-level="'+Classrooms[i].levelID+'" >'+Classrooms[i].name+'</option>';
					
					}else{

					    Anclassidentifier = Classrooms[i].classidentifier;
				    	if ( AnisTheFirsRecord ){

					    	AnisTheFirsRecord = false; 

				    	}else{
					    	AnClassOptions += '</optgroup>';
					    }

				    	AnClassOptions += '<optgroup label="'+Classrooms[i].YearName+'">';
				   		AnClassOptions += '<option value="'+Classrooms[i].id+'" data-yearname="'+Classrooms[i].YearName+'" data-level="'+Classrooms[i].levelID+'" >'+Classrooms[i].name+'</option>';
				
					}
						    
				}

				selectGrSendResultsClass.html( AnClassOptions );
				selectGrSendResultsClass.select2({ 
			            formatResult: newStudentformatResult,
			            formatSelection: newStudentformatSelection
			    }).bind('change', getGrSendReportClassSelected);
				
			//Populate Period 
				var selectGrSendResultsPeriod = $("#selectGrSendResultsPeriod");
		    	selectGrSendResultsPeriod.empty();

		    	var AnGrReportPeriodOption = "";
		    	AnGrReportPeriodOption    += '<option value="0">All</option>';

		    		for ( var i=0; i< SchoolPeriodsSize ; i++ ) {
		    			AnGrReportPeriodOption += '<option value="'+SchoolPeriods[i].period+'" >'+SchoolPeriods[i].period+'</option>';
					
		    		}
		    	selectGrSendResultsPeriod.html( AnGrReportPeriodOption );
				selectGrSendResultsPeriod.select2();

			//populate Students 
				var selectGrSendResultsStudents = $("#selectGrSendResultsStudents");
			    selectGrSendResultsStudents.empty();

			    var AnStudentsOptions 	= '<option value="0">All</option>';

			    for ( var i = 0; i < StudentsSize; i++) {

					var personID 	= Students[i].personID;
					var regNumber   = Students[i].studRegNber;
					var studentNames= Students[i].studentNames;

					AnStudentsOptions += "<option value="+personID+" data-regnumber="+regNumber+" >"+regNumber+" ("+studentNames+") </option>";

				}

				selectGrSendResultsStudents.html(AnStudentsOptions);
				selectGrSendResultsStudents.select2();

			}else{


			}

			

	    })
	    .done(function() {
	    	
	    	if ( loadAllTwoSection ) {

	    		//validate and submit form 
	    		getGrSendResultsValidate();

	    	}

	    	GrSentResultsClicked();

	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });
    

}

function GrSentResultsClicked()
{
	$( "table#tbGrSentResults" ).delegate( "td.GrSentResultsAllMessage", "click", function(e) {

		e.preventDefault();
		e.stopPropagation();

	  	var id 			= $(this).closest('tr').attr('id');
	  	GrSentResults_LoadAllMessages(id);

	});

	$( "table#tbGrSentResults" ).delegate( "td.GrSentResultsAllComments", "click", function(e) {

		e.preventDefault();
		e.stopPropagation();

	  	var id 			= $(this).closest('tr').attr('id');
	  	GrSentResults_LoadAllComments(id);

	});

	$( "table#tbGrSentResults" ).delegate( "td.GrSendUpdates", "click", function(e) {

		e.preventDefault();
		e.stopPropagation();

	  	var id 			= $(this).closest('tr').attr('id');
	  	GrSentResults_LoadUpdateMessages(id);

	});

}

function GrSentResults_LoadUpdateMessages(id){


	$('#Model_GrSendResultsReport').modal('show');
	
	$('#GrSendResultsReportTitle').html("Notify parents about the changes on reports");

	var Content = '';
		Content += '<div class="col-sm-12">';
			Content += '';
		Content += '</div>';
		Content += '<form class="form-horizontal" id="formReportSendUpdates" role="form">';
			Content += '<div class="form-group">';
				Content += '<div class="col-sm-12">';
					Content += '<textarea name="newMessageContent" id="newMessageContent" class="form-control" placeholder="Write your message here" style="height: 120px;" autofocus></textarea>';
					Content += '<input type="hidden" id="ResultsToParentsThreadID" name="ResultsToParentsThreadID" value="'+id+'" />';
				Content += '</div>';
			Content += '</div>';
			Content += '<div class="form-group">';
				Content += '<div class="col-sm-12">';
					Content += '<button type="submit" id="GrSendUpdatesSubmit" class="btn btn-primary btn-lg col-sm-12">Send Updates</button>';
				Content += '</div>';
			Content += '</div>';
		Content += '</form>';

	$('#GrSendResultsReportBody').html(Content);
	getGrLoadUpdateValidate();

}	

function getGrLoadUpdateValidate()
{
	//validation
	var formReportSendUpdates = $('#formReportSendUpdates');

	formReportSendUpdates.formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        exclude: ':disabled',
        fields: {
        	newMessageContent: {
                validators: {
                    notEmpty: {
                        message: 'The message is required'
                    }
                }
            }
        }
    })
	.on('success.form.fv', function(e , data) {

	e.preventDefault();
	
	onDashLoadSentResultSectionContent();

 	var GrReportStudentsSubmit = $('#GrSendUpdatesSubmit');
		GrReportStudentsSubmit.removeClass('disabled'); 
		GrReportStudentsSubmit.removeAttr('disabled');
    }).end();

}

function onDashLoadSentResultSectionContent()
{	
	  	
	  	var GrSendResultsSubmitContainer  = $("#GrSendResultsSubmitContainer");
	  	var GrSendResultsLoadingContainer = $("#GrSendResultsLoadingContainer");

	  	// var selectGrSendResultsType 	=  $("#selectGrSendResultsType");
	  	// var GrSendResultsOnlyYears 		=  $("#GrSendResultsOnlyYears");
 	  // 	var selectGrSendResultsClass 	=  $("#selectGrSendResultsClass");
 	  // 	var selectSendResultsYear 		=  $("#selectSendResultsYear");

 	  	var frm = $('#formReportSendUpdates');
      	var url = base_url+"api/Gr/SendResults"; 

      	GrSendResultsSubmitContainer.hide();
	  	GrSendResultsLoadingContainer.slideDown();

      	$.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){
	            
	          if ( data.success )
	          {   
	           
	           	onDashLoadSendResultsSection(false);

	           	$.gritter.add({
		            title: 'Success',
		            text:  'The results are being sent to parents, check the status ...',
		            class_name: 'success gritter-center',
		            time: '' 
		        });

		        $('#selectGrSendResultsStudents').val("0").trigger("change");
				$('#selectSendResultsYear').select2('data', null);
				$('#selectGrSendResultsClass').select2('data', null);

	          } else {
	              
	              $.gritter.add({
		            title: 'Failed',
		            text: 'Failed to send the results',
		            class_name: 'danger gritter-center',
		            time: '' 
		          });
	          }
	             
	    },"json")
	      .done(function() {
	        
	    }).fail(function() {

	          $.gritter.add({
	            title: 'Failed',
	            text: 'Something went wrong, Please Try Again',
	            class_name: 'danger gritter-center',
	            time: ''
	          });

	          GrSendResultsLoadingContainer.hide();
		  	  GrSendResultsSubmitContainer.slideDown();

	    }).always(function() { });

}


function GrSentResults_LoadAllMessages(id){

	$('#Model_GrSendResultsReport').modal('show');
	
	$('#GrSendResultsReportTitle').html("Results Sent");
	$('#GrSendResultsReportBody').html("Loading please wait...");

	var url = base_url+"api/Gr/SendResults/AllThreadMessages/"+id ; 

	//get 
		$.getJSON( url, function(data) {

			if ( data.success ) {

				var ThreadMessages = data.ThreadMessages ;

				Content   = "";
			  	Content  += '<table class="table table-hover" id="GrSentResultsAllMessagesTable" ><thead><tr><th class="text-left">Student</th><th class="text-center">Class</th><th class="text-left">Family Member</th><th class="text-center">Relationship</th><th class="text-center">View</th></tr></thead><tbody>';

			   	$.each( ThreadMessages , function(i, item) {

			        Content +="<tr id='"+item.id+"' >";
			          Content +="<td  class='text-left'>"+item.studentNames+"</td>";
			          Content +="<td class='text-center'>"+item.classroomName+"</td>";
			          Content +="<td  class='text-left'>"+item.familyMemberNames+"</td>";
			          Content +="<td class='text-center'>"+item.relationship+"</td>";
			          Content +="<td><a target='_blank' href='StudentReport/View/"+item.urlAppend+"?isParent=0'><i class='fa fa-external-link text-green'></i> Report/comments("+item.numberOfComments+")</a></td>";
			        Content +="</tr>";

			    });

			  	Content +='</tbody></table>';

			  	$('#GrSendResultsReportBody').html(Content);

			}else{

			}

	    }).done(function() {})
	    .fail(function() {
	    })
	    .always(function() {
	    });

}

function GrSentResults_LoadAllComments( id ){

	$('#Model_GrSendResultsReport').modal('show');
	
	$('#GrSendResultsReportTitle').html("Comments");
	$('#GrSendResultsReportBody').html("Loading please wait...");

	var url = base_url+"api/Gr/SendResults/AllThreadComments/"+id ; 

	//get 
		$.getJSON( url, function(data) {

			if ( data.success) {

				ThreadComments = data.ThreadComments ;

				var newCommentContent   = "";
				newCommentContent 		+= '<div class="row message_container" style="padding:20px;" >';

				$.each( ThreadComments , function(i, item) {

					newCommentContent += '<div class="chat-box-timeline"><img class="img-responsive img-circle avatar" src="personimage/50/'+item.personID+'" /><div class="message"><div class="panel panel-shadow panel-white"><div class="panel-body panel-arrow-left"><div class="chat-box-timeline-title"><strong>'+item.commentorNames+'</strong>('+item.relationship+' '+item.roles+')<div class="pull-right text-semi"><i class="fa fa-clock-o"></i> '+moment.unix(item.created_at).fromNow()+'</div><center><span>'+item.studentNames+'</span></center></div><div class="chat-box-timeline-content"><blockquote>'+item.comment+'</blockquote><span class="pull-right"><a target="_blank" href="StudentReport/View/'+item.urlAppend+'?isParent=0"><i class="fa fa-external-link text-green"></i> Reply</a></span></div></div></div></div></div>';
				
				});

				newCommentContent +='</div>';

				$('#GrSendResultsReportBody').html(newCommentContent);
			}

	    }).done(function() {

	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });

}

function getGrSendResultsValidate()
{
	//validation
	var formDashGrSendResults = $('#formDashGrSendResults');

	formDashGrSendResults.formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        exclude: ':disabled',
        fields: {
        	GrSendResultsType: {
                validators: {
                    notEmpty: {
                        message: 'Report Type is required'
                    }
                }
            },
            GrSendResultsClass: {
                validators: {
                    notEmpty: {
                        message: 'Class is required'
                    }
                }
            },
             GrSendResultsYear: {
                validators: {
                    notEmpty: {
                        message: 'Year is required'
                    }
                }
            },
            GrSendResultsStudents: {
                validators: {
                    notEmpty: {
                        message: 'Student is required'
                    }
                }
            }
        }
    })
	.on('success.form.fv', function(e , data) {

	e.preventDefault();
	
	onDashLoadSentResultSectionContent();

 	var GrReportStudentsSubmit = $('#GrSendResultsStudentsSubmit');
		GrReportStudentsSubmit.removeClass('disabled'); 
		GrReportStudentsSubmit.removeAttr('disabled');
    }).end();

}

function onDashLoadSentResultSectionContent()
{	
	  	
	  var GrSendResultsSubmitContainer  = $("#GrSendResultsSubmitContainer");
	  var GrSendResultsLoadingContainer = $("#GrSendResultsLoadingContainer");

	  var selectGrSendResultsType 	=  $("#selectGrSendResultsType");
	  var GrSendResultsOnlyYears 	=  $("#GrSendResultsOnlyYears");
 	  var selectGrSendResultsClass 	=  $("#selectGrSendResultsClass");
 	  var selectSendResultsYear 	=  $("#selectSendResultsYear");

 	  var frm = $('#formDashGrSendResults');
      var url = base_url+"api/Gr/SendResults"; 

      if ( 
      		selectGrSendResultsType.select2('val') > 0 && 
      		( GrSendResultsOnlyYears.val() == 0 && selectGrSendResultsClass.select2('val') > 0 ) 
      		|| 
      		( GrSendResultsOnlyYears.val() == 1 && selectSendResultsYear.select2('val').length > 0 ) 
      	 ) {

      	GrSendResultsSubmitContainer.hide();
	  	GrSendResultsLoadingContainer.slideDown();

      	$.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){
	            
	          if ( data.success )
	          {   
	           
	           	onDashLoadSendResultsSection(false);

	           	$.gritter.add({
		            title: 'Success',
		            text:  'The results are being sent to parents, check the status ...',
		            class_name: 'success gritter-center',
		            time: '' 
		        });

		        $('#selectGrSendResultsStudents').val("0").trigger("change");
				$('#selectSendResultsYear').select2('data', null);
				$('#selectGrSendResultsClass').select2('data', null);

	          } else {
	              
	              $.gritter.add({
		            title: 'Failed',
		            text: 'Failed to send the results',
		            class_name: 'danger gritter-center',
		            time: '' 
		          });
	          }
	             
	    },"json")
	      .done(function() {
	        
	    }).fail(function() {

	          $.gritter.add({
	            title: 'Failed',
	            text: 'Something went wrong, Please Try Again',
	            class_name: 'danger gritter-center',
	            time: ''
	          });

	          GrSendResultsLoadingContainer.hide();
		  	  GrSendResultsSubmitContainer.slideDown();

	    }).always(function() { });     

      }else{

      	frm.formValidation('revalidateField', 'GrSendResultsType');
	  	frm.formValidation('revalidateField', 'GrSendResultsClass');
	  	frm.formValidation('revalidateField', 'GrSendResultsYear');

      }

}

//getDashGrRcTypeSelected
function getGrSendReportTypeSelected()
{
	var selectGrSendResultsType = $("#selectGrSendResultsType");
	var selected           = selectGrSendResultsType.select2('val');

	var GrSendResultsPeriodContainer  = $("#GrSendResultsPeriodContainer");
	var GrSendResultsStudentsontainer = $("#GrSendResultsStudentsontainer");

	if ( selected == 1 ) {
		GrSendResultsPeriodContainer.slideDown();
		GrSendResultsStudentsontainer.slideDown();

	}else if ( selected == 5 ) {
		GrSendResultsPeriodContainer.hide();
		GrSendResultsStudentsontainer.hide();

	}else if ( selected == 6 ) {
		GrSendResultsPeriodContainer.slideDown();
		GrSendResultsStudentsontainer.hide();

	}else{
		GrSendResultsPeriodContainer.hide();
		GrSendResultsStudentsontainer.slideDown();
	}
}

//getDashGrRcClassSelected
function getGrSendReportClassSelected()
{
	var selectGrSendResultsClass 		= $("#selectGrSendResultsClass");
	var selectGrSendResultsStudents 	= $("#selectGrSendResultsStudents");

	var selected            	= selectGrSendResultsClass.select2('val');
	var ClassStudents 			= GroupedStudentsClassArray[selected];
	var StudentsOptions			= '';

	var StudentsOptions 		= '<option value="0">All</option>';

	$.each(ClassStudents, function(i, item) {
		StudentsOptions += "<option value="+item.personID+" data-regnumber="+item.regNumber+" >"+item.regNumber+" ("+item.studentNames+") </option>";

    });	

	selectGrSendResultsStudents.html( StudentsOptions );
	selectGrSendResultsStudents.select2().val('0').trigger('change');
}

function onDashLoadAssessmentsTracking(){

	var dashGradebook_contentAnalytics 	= $("#dashGradebook_contentAnalytics")
	var dashGradebook_contentOther 		= $("#dashGradebook_contentOther");

	var Content       = '';
	var LeftContent   = '';
	var RightContent  = '';

	Content 	+= '<div  class="col-sm-12">';
		Content 	+= '<div class="col-sm-12" id="dashGrAssessmentsTrackSection"><center><span class="top-buffer">Loading Assessments...</span></center></div>';
	Content 	+= '</div>';

	dashGradebook_contentOther.html(Content);

	dashGradebook_contentAnalytics.hide();
    dashGradebook_contentOther.slideDown();

	var url = base_url+"api/Gr/AssessmentTracking"; 

	//get 
	$.getJSON( url, function(data) {

		var Assessments 	 = data.Assessments ;
		var AssessmentsSize  = Assessments.length ;

			RightContent    = '';
			RightContent  	+= '<div class="row text-center">';
				RightContent  	+= '<div class="col-sm-12">';
					RightContent  	+= '<div class="col-sm-12"><h4>Assessments Tracking</h4></div>';
				RightContent  	+= '</div>';
			RightContent  	+= '</div>';
			RightContent  	+= '<div class="row" >';
				RightContent  += '<table class="table table-bordered table-hover table-striped" id="tbGrAssessmentsTracking" ><thead>';
					RightContent  += '<tr>';
						RightContent  += '<th class="row text-left">Class</th>';
						RightContent  += '<th class="row text-left">Subject</th>';
						RightContent  += '<th class="row text-center">Teacher</th>';
						RightContent  += '<th class="row text-center">CW</th>';
						RightContent  += '<th class="row text-center">MTE</th>';
						RightContent  += '<th class="row text-center">EXAM</th>';
						RightContent  += '<th class="row text-center">Other</th>';
						RightContent  += '<th class="row text-center">Empty</th>';
						RightContent  += '<th class="row text-center text-blue" style="background-color:#e5e5ff;" ><span style="writing-mode: vertical-rl; transform: rotate(180deg);">Comments</span></th>';
						RightContent  += '<th class="row text-center" ><span style="writing-mode: vertical-rl; transform: rotate(180deg);">Equipped for Lessons</span></th>';
						RightContent  += '<th class="row text-center" ><span style="writing-mode: vertical-rl; transform: rotate(180deg);">Co-operation with staff</span></th>';
						RightContent  += '<th class="row text-center" ><span style="writing-mode: vertical-rl; transform: rotate(180deg);">Motivation & Independence</span></th>';
						RightContent  += '<th class="row text-center" ><span style="writing-mode: vertical-rl; transform: rotate(180deg);">Behaviour in Lessons</span></th>';
						RightContent  += '<th class="row text-center" ><span style="writing-mode: vertical-rl; transform: rotate(180deg);">Participation in Lessons</span></th>';
						RightContent  += '<th class="row text-center" ><span style="writing-mode: vertical-rl; transform: rotate(180deg);">Concentration in Lessons</span></th>';
						RightContent  += '<th class="row text-center" ><span style="writing-mode: vertical-rl; transform: rotate(180deg);">Presentation & Organisation</span></th>';
						RightContent  += '<th class="row text-center" ><span style="writing-mode: vertical-rl; transform: rotate(180deg);">Meeting Deadlines</span></th>';
					RightContent  += '</tr>';
				RightContent  += '</thead><tbody>';		
				
				//Report Cards 
				for ( var i = 0; i < AssessmentsSize; i++) {

					var  AssessmentsTrack_id     		= Assessments[i].id ;
					var  SendResultsTrack_TeacherName 	= Assessments[i].teacherName;
					var  SendResultsTrack_Class 		= Assessments[i].classname;
					var  SendResultsTrack_Subject 		= Assessments[i].subjectName;
					var  SendResultsTrack_CW  			= Assessments[i].CW ;
					var  SendResultsTrack_MTE  			= Assessments[i].MTE;
					var  SendResultsTrack_Exam  		= Assessments[i].Exam;
					var  SendResultsTrack_Other  		= Assessments[i].Other;
					var  SendResultsTrack_Empty  		= Assessments[i].Empty;

					var  numberOfEnrollment  			= Assessments[i].numberOfEnrollment;
					var  numberOfExcluded  				= Assessments[i].numberOfExcluded;
					var  numberOfComments  				= Assessments[i].numberOfComments;
					var  numberOfBehaviour_13  			= Assessments[i].numberOfBehaviour_13;
					var  numberOfBehaviour_14  			= Assessments[i].numberOfBehaviour_14;
					var  numberOfBehaviour_15  			= Assessments[i].numberOfBehaviour_15;
					var  numberOfBehaviour_16  			= Assessments[i].numberOfBehaviour_16;
					var  numberOfBehaviour_17  			= Assessments[i].numberOfBehaviour_17;
					var  numberOfBehaviour_18  			= Assessments[i].numberOfBehaviour_18;
					var  numberOfBehaviour_19  			= Assessments[i].numberOfBehaviour_19;
					var  numberOfBehaviour_20  			= Assessments[i].numberOfBehaviour_20;

					var TakingSubjectNumberOfStudent = parseFloat(numberOfEnrollment) - parseFloat(numberOfExcluded);

					var Remain_numberOfComments 	=  parseFloat(TakingSubjectNumberOfStudent) - parseFloat(numberOfComments);
					var Remain_numberOfBehaviour_13 =  parseFloat(TakingSubjectNumberOfStudent) - parseFloat(numberOfBehaviour_13);
					var Remain_numberOfBehaviour_14 =  parseFloat(TakingSubjectNumberOfStudent) - parseFloat(numberOfBehaviour_14);
					var Remain_numberOfBehaviour_15 =  parseFloat(TakingSubjectNumberOfStudent) - parseFloat(numberOfBehaviour_15);
					var Remain_numberOfBehaviour_16 =  parseFloat(TakingSubjectNumberOfStudent) - parseFloat(numberOfBehaviour_16);
					var Remain_numberOfBehaviour_17 =  parseFloat(TakingSubjectNumberOfStudent) - parseFloat(numberOfBehaviour_17);
					var Remain_numberOfBehaviour_18 =  parseFloat(TakingSubjectNumberOfStudent) - parseFloat(numberOfBehaviour_18);
					var Remain_numberOfBehaviour_19 =  parseFloat(TakingSubjectNumberOfStudent) - parseFloat(numberOfBehaviour_19);
					var Remain_numberOfBehaviour_20 =  parseFloat(TakingSubjectNumberOfStudent) - parseFloat(numberOfBehaviour_20);
					

					RightContent +="<tr class='AssessmentsTrackingRow' id="+AssessmentsTrack_id+" className="+SendResultsTrack_Class+" subjectName="+SendResultsTrack_Subject+" >";
		             	RightContent +="<td class='row text-left'>"+SendResultsTrack_Class+"</td>"; 
		             	RightContent +="<td class='row text-left'>"+SendResultsTrack_Subject+"</td>";
		             	RightContent +="<td class='row text-left'>"+SendResultsTrack_TeacherName+"</td>";
		             	RightContent +="<td class='row text-center'>";
		             	if( SendResultsTrack_CW > 0 ){ 
		             		RightContent +="<span class='text-green'>"+SendResultsTrack_CW+"</span>";
		             	}else{
		             		RightContent +="<span class='text-red'>"+SendResultsTrack_CW+"</span>";
		             	}
		             	RightContent +="</td>";
		             	RightContent +="<td class='row text-center'>";
		             	if( SendResultsTrack_MTE > 0 ){ 
		             		RightContent +="<span class='text-green'>"+SendResultsTrack_MTE+"</span>";
		             	}else{
		             		RightContent +="<span class='text-red'>"+SendResultsTrack_MTE+"</span>";
		             	}
		             	RightContent +="</td>";
		             	RightContent +="<td class='row text-center'>";
		             	if( SendResultsTrack_Exam > 0 ){ 
		             		RightContent +="<span class='text-green'>"+SendResultsTrack_Exam+"</span>";
		             	}else{
		             		RightContent +="<span class='text-red'>"+SendResultsTrack_Exam+"</span>";
		             	}
		             	RightContent +="</td>";
		             	RightContent +="<td class='row text-center'>";
		             	if( SendResultsTrack_Other > 0 ){ 
		             		RightContent +="<span class='text-green'>"+SendResultsTrack_Other+"</span>";
		             	}else{
		             		RightContent +="";
		             	}
		             	RightContent +="</td>";
		             	RightContent +="<td class='row text-center'>";
		             	if( SendResultsTrack_Empty > 0 ){ 
		             		RightContent +="<span class='text-red'>"+SendResultsTrack_Empty+"</span>";
		             	}else{
		             		RightContent +="";
		             	}
		             	RightContent +="</td>";

		             	if ( Remain_numberOfComments > 0 )
		            	{
		            	 	RightContent +="<td class='text-center text-orange' style='background-color:#e5e5ff;' >"+Remain_numberOfComments+"</td>";
		            	}else{
		            	 	RightContent +="<td class='text-center text-green' style='background-color:#e5e5ff;' ><span><i class='fa fa-thumbs-up'></i></span></td>";
		            	}

		            	if ( Remain_numberOfBehaviour_13 > 0 )
		            	{
		            	 	RightContent +="<td class='text-center text-orange'>"+Remain_numberOfBehaviour_13+"</td>";
		            	}else{
		            	 	RightContent +="<td class='text-center text-green'><span><i class='fa fa-thumbs-up'></i></span></td>";
		            	}

		            	if ( Remain_numberOfBehaviour_14 > 0 )
		            	{
		            	 	RightContent +="<td class='text-center text-orange'>"+Remain_numberOfBehaviour_14+"</td>";
		            	}else{
		            	 	RightContent +="<td class='text-center text-green'><span><i class='fa fa-thumbs-up'></i></span></td>";
		            	}

		            	if ( Remain_numberOfBehaviour_15 > 0 )
		            	{
		            	 	RightContent +="<td class='text-center text-orange'>"+Remain_numberOfBehaviour_15+"</td>";
		            	}else{
		            	 	RightContent +="<td class='text-center text-green'><span><i class='fa fa-thumbs-up'></i></span></td>";
		            	}

		            	if ( Remain_numberOfBehaviour_16 > 0 )
		            	{
		            	 	RightContent +="<td class='text-center text-orange'>"+Remain_numberOfBehaviour_16+"</td>";
		            	}else{
		            	 	RightContent +="<td class='text-center text-green'><span><i class='fa fa-thumbs-up'></i></span></td>";
		            	}

		            	if ( Remain_numberOfBehaviour_17 > 0 )
		            	{
		            	 	RightContent +="<td class='text-center text-orange'>"+Remain_numberOfBehaviour_17+"</td>";
		            	}else{
		            	 	RightContent +="<td class='text-center text-green'><span><i class='fa fa-thumbs-up'></i></span></td>";
		            	}

		            	if ( Remain_numberOfBehaviour_18 > 0 )
		            	{
		            	 	RightContent +="<td class='text-center text-orange'>"+Remain_numberOfBehaviour_18+"</td>";
		            	}else{
		            	 	RightContent +="<td class='text-center text-green'><span><i class='fa fa-thumbs-up'></i></span></td>";
		            	}

		            	if ( Remain_numberOfBehaviour_19 > 0 )
		            	{
		            	 	RightContent +="<td class='text-center text-orange'>"+Remain_numberOfBehaviour_19+"</td>";
		            	}else{
		            	 	RightContent +="<td class='text-center text-green'><span><i class='fa fa-thumbs-up'></i></span></td>";
		            	}

		            	if ( Remain_numberOfBehaviour_20 > 0 )
		            	{
		            	 	RightContent +="<td class='text-center text-orange'>"+Remain_numberOfBehaviour_20+"</td>";
		            	}else{
		            	 	RightContent +="<td class='text-center text-green'><span><i class='fa fa-thumbs-up'></i></span></td>";
		            	}

			        RightContent +="</tr>";

				}

			RightContent +="</tbody></table>";
		RightContent  += '</div>';

		$("#dashGrAssessmentsTrackSection").html(RightContent);
		
			var AssessmentsTrackingTable = $('#tbGrAssessmentsTracking').dataTable({
										        "paging":         false,
											    language: {
											        searchPlaceholder: "Search here, E.g: teacher , class or subject."
											    }
											});

			
	    })
	    .done(function() {
	    	
	    	GrAssessmentTrackingClicked();

	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });
}
	
function GrAssessmentTrackingClicked(){

	$( "table#tbGrAssessmentsTracking" ).delegate( "tr.AssessmentsTrackingRow", "click", function(e) {

		e.preventDefault();
		e.stopPropagation();

	  	var id 			= $(this).closest('tr').attr('id');
	  	var className 	= $(this).closest('tr').attr('className');
	  	var subjectName = $(this).closest('tr').attr('subjectName');

	  	$('#Model_AssessmentTrackSubjects').modal('show');
	  	$('#AssessmentTrackTitle').html(className+":"+subjectName);
	  	
	  	GrGetSubjectAssessments(id);

	});
}

function GrGetSubjectAssessments(subjectID){

	var content  = "";
	var AssessmentTypes = [];
	
	var DashGrAssessmentTrackContent = $('#DashGrAssessmentTrackContent');
	var DashGrAssessmentTrackLoding  = $('#DashGrAssessmentTrackLoding');

	var classIndex = 0 ;

	var url = base_url+"api/Gr/AssessmentTracking/"+subjectID;

  	$.getJSON( url, function(data) {

  	var Assessments 	 	 = data.Assessments ;

  	var Missing_Comments 	 = data.Missing_Comments ;

  	var Missing_Behaviour_13 = data.Missing_Behaviour_13 ;
  	var Missing_Behaviour_14 = data.Missing_Behaviour_14 ;
  	var Missing_Behaviour_15 = data.Missing_Behaviour_15 ;
  	var Missing_Behaviour_16 = data.Missing_Behaviour_16 ;
  	var Missing_Behaviour_17 = data.Missing_Behaviour_17 ;
  	var Missing_Behaviour_18 = data.Missing_Behaviour_18 ;
  	var Missing_Behaviour_19 = data.Missing_Behaviour_19 ;
  	var Missing_Behaviour_20 = data.Missing_Behaviour_20 ;

  	AssessmentTypes     	 = data.AssessmentTypes;

  	var Missing_Comments_Size 	  = Missing_Comments.length ;

  	var Missing_Behaviour_13_Size = Missing_Behaviour_13.length ;
  	var Missing_Behaviour_14_Size = Missing_Behaviour_14.length ;
  	var Missing_Behaviour_15_Size = Missing_Behaviour_15.length ;
  	var Missing_Behaviour_16_Size = Missing_Behaviour_16.length ;
  	var Missing_Behaviour_17_Size = Missing_Behaviour_17.length ;
  	var Missing_Behaviour_18_Size = Missing_Behaviour_18.length ;
  	var Missing_Behaviour_19_Size = Missing_Behaviour_19.length ;
  	var Missing_Behaviour_20_Size = Missing_Behaviour_20.length ;

  	content  += '<div class="row text-center"><h4>Recorded Results</h4></div>';
	content  += '<div class="row"><div class="col-sm-12">';
		content +='<table class="table table-striped abDataTable" id="paGrAssessementTrackRecords" ><thead><tr><th>Assessment</th><th class="text-center">Period</th><th class="text-center">Max</th><th class="text-center">Average</th><th class="text-center">Recorded Marks</th><th class="text-center">Added on</th><th class="text-center">Delete</th></tr></thead><tbody>';
		
		$.each( Assessments, function(i, item) {

          content +="<tr id="+item.id+" tb_IsTeachingSet= 0 classid="+item.annualClassroomID+" anclasssubjid ="+item.anClsrmSbjtID+" asstype="+item.asTypeId+" period="+item.period+" max='"+item.maximumMarks+"' assessmentdate='"+item.assessmentDate+"' teachername='"+item.teachername+"' >";
          	content +="<td ><span class='xeditableAssessmentType' data-type='select2' data-pk='1' data-title='Select Printed Status' data-url='api/Gr/AssessmentTracking/"+subjectID+"?type=1&id="+item.id+"'>"+item.assessment+"</span></td>"; 
         	if ( item.isExamType == 1 ) {
         		content +="<td ></td >";
         	}else{
         		content +="<td ><center><span class='xeditableAssessmentPeriod' data-type='select2' data-pk='1' data-title='Select Printed Status' data-url='api/Gr/AssessmentTracking/"+subjectID+"?type=2&id="+item.id+"'>"+item.period+"</span></center></td>"; 
         	
         	}
         	content +="<td ><center><span class='xeditableAssessmentMax' data-pk='1' data-url='api/Gr/AssessmentTracking/"+subjectID+"?type=3&id="+item.id+"'>"+item.maximumMarks+"</span></center></td>"; 
         	content +="<td class='text-center'>";
            	content += ( item.average > 0 ) ? item.average+"%" : "<span class='text-red'>Not Recorded</span>";
            content +="</td>";
            content +="<td class='text-center'><b>"+item.numberOfRecords+"</b> of <b>"+item.numberOfStudent+"</b></td>";
            content +="<td class='text-center'>"+date_moment(item.addedOn)+"</td>";
            	if ( item.numberOfRecords > 0 ) 
            	{
            		content += "<td class='text-center'></td>";
            	}else{
            		content += "<td class='text-center GrAssessmentTrackDelete'><a href='#'><i class=' fa fa-times text-red'></i></a></td>";
            	}
          content +="</tr>";

	   	});

		content +="</tbody></table>";
	content  	+= '</div></div>';

	if ( Missing_Comments_Size > 0 ) {

		content  += '<div class="row text-center"><h4>Missing Comments</h4></div>';
		content  += '<div class="row"><div class="col-sm-12">';
			content +='<table class="table table-striped abDataTable" id="paGrMissingComments" ><thead><tr><th>Names</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';
			
			$.each( Missing_Comments, function(i, item) {

	          content +="<tr >";
	          	content += "<td class='text-left'>"+item.studentNames+"</td>";
	          	content += "<td class='text-center'>"+item.studRegNber+"</td>";
	          content +="</tr>";

		   	});

			content +="</tbody></table>";
		content  	+= '</div></div>';

	}

	// var Missing_Behaviour_13_Size = Missing_Behaviour_13.length ;
 //  	var Missing_Behaviour_14_Size = Missing_Behaviour_14.length ;
 //  	var Missing_Behaviour_15_Size = Missing_Behaviour_15.length ;
 //  	var Missing_Behaviour_16_Size = Missing_Behaviour_16.length ;
 //  	var Missing_Behaviour_17_Size = Missing_Behaviour_17.length ;
 //  	var Missing_Behaviour_18_Size = Missing_Behaviour_18.length ;
 //  	var Missing_Behaviour_19_Size = Missing_Behaviour_19.length ;
 //  	var Missing_Behaviour_20_Size = Missing_Behaviour_20.length ;

	if ( Missing_Behaviour_13_Size > 0 ) {

		content  += '<div class="row text-center"><h4>Missing Conduct: <b>Equipped for Lessons</b></h4></div>';
		content  += '<div class="row"><div class="col-sm-12">';
			content +='<table class="table table-striped abDataTable" id="paGrMissingConduct_13" ><thead><tr><th>Names</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';
			
			$.each( Missing_Behaviour_13, function(i, item) {

	          content +="<tr >";
	          	content += "<td class='text-left'>"+item.studentNames+"</td>";
	          	content += "<td class='text-center'>"+item.studRegNber+"</td>";
	          content +="</tr>";

		   	});

			content +="</tbody></table>";
		content  	+= '</div></div>';

	}

	if ( Missing_Behaviour_14_Size > 0 ) {

		content  += '<div class="row text-center"><h4>Missing Conduct: <b>Co-operation with Staff</b></h4></div>';
		content  += '<div class="row"><div class="col-sm-12">';
			content +='<table class="table table-striped abDataTable" id="paGrMissingConduct_14" ><thead><tr><th>Names</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';
			
			$.each( Missing_Behaviour_14, function(i, item) {

	          content +="<tr >";
	          	content += "<td class='text-left'>"+item.studentNames+"</td>";
	          	content += "<td class='text-center'>"+item.studRegNber+"</td>";
	          content +="</tr>";

		   	});

			content +="</tbody></table>";
		content  	+= '</div></div>';

	}

	if ( Missing_Behaviour_15_Size > 0 ) {

		content  += '<div class="row text-center"><h4>Missing Conduct: <b>Motivation & Independence</b></h4></div>';
		content  += '<div class="row"><div class="col-sm-12">';
			content +='<table class="table table-striped abDataTable" id="paGrMissingConduct_15" ><thead><tr><th>Names</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';
			
			$.each( Missing_Behaviour_15, function(i, item) {

	          content +="<tr >";
	          	content += "<td class='text-left'>"+item.studentNames+"</td>";
	          	content += "<td class='text-center'>"+item.studRegNber+"</td>";
	          content +="</tr>";

		   	});

			content +="</tbody></table>";
		content  	+= '</div></div>';

	}

	if ( Missing_Behaviour_16_Size > 0 ) {

		content  += '<div class="row text-center"><h4>Missing Conduct: <b>Behaviour in Lessons</b></h4></div>';
		content  += '<div class="row"><div class="col-sm-12">';
			content +='<table class="table table-striped abDataTable" id="paGrMissingConduct_16" ><thead><tr><th>Names</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';
			
			$.each( Missing_Behaviour_16, function(i, item) {

	          content +="<tr >";
	          	content += "<td class='text-left'>"+item.studentNames+"</td>";
	          	content += "<td class='text-center'>"+item.studRegNber+"</td>";
	          content +="</tr>";

		   	});

			content +="</tbody></table>";
		content  	+= '</div></div>';

	}

	if ( Missing_Behaviour_17_Size > 0 ) {

		content  += '<div class="row text-center"><h4>Missing Conduct: <b>Participation in Lessons</b></h4></div>';
		content  += '<div class="row"><div class="col-sm-12">';
			content +='<table class="table table-striped abDataTable" id="paGrMissingConduct_17" ><thead><tr><th>Names</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';
			
			$.each( Missing_Behaviour_17, function(i, item) {

	          content +="<tr >";
	          	content += "<td class='text-left'>"+item.studentNames+"</td>";
	          	content += "<td class='text-center'>"+item.studRegNber+"</td>";
	          content +="</tr>";

		   	});

			content +="</tbody></table>";
		content  	+= '</div></div>';

	}

	if ( Missing_Behaviour_18_Size > 0 ) {

		content  += '<div class="row text-center"><h4>Missing Conduct: <b>Concentration in Lessons</b></h4></div>';
		content  += '<div class="row"><div class="col-sm-12">';
			content +='<table class="table table-striped abDataTable" id="paGrMissingConduct_18" ><thead><tr><th>Names</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';
			
			$.each( Missing_Behaviour_18, function(i, item) {

	          content +="<tr >";
	          	content += "<td class='text-left'>"+item.studentNames+"</td>";
	          	content += "<td class='text-center'>"+item.studRegNber+"</td>";
	          content +="</tr>";

		   	});

			content +="</tbody></table>";
		content  	+= '</div></div>';

	}

	if ( Missing_Behaviour_19_Size > 0 ) {

		content  += '<div class="row text-center"><h4>Missing Conduct: <b>Presentation & Organisation</b></h4></div>';
		content  += '<div class="row"><div class="col-sm-12">';
			content +='<table class="table table-striped abDataTable" id="paGrMissingConduct_19" ><thead><tr><th>Names</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';
			
			$.each( Missing_Behaviour_19, function(i, item) {

	          content +="<tr >";
	          	content += "<td class='text-left'>"+item.studentNames+"</td>";
	          	content += "<td class='text-center'>"+item.studRegNber+"</td>";
	          content +="</tr>";

		   	});

			content +="</tbody></table>";
		content  	+= '</div></div>';

	}

	if ( Missing_Behaviour_20_Size > 0 ) {

		content  += '<div class="row text-center"><h4>Missing Conduct: <b>Presentation & Organisation</b></h4></div>';
		content  += '<div class="row"><div class="col-sm-12">';
			content +='<table class="table table-striped abDataTable" id="paGrMissingConduct_20" ><thead><tr><th>Names</th><th class="text-center">Reg. Number</th></tr></thead><tbody>';
			
			$.each( Missing_Behaviour_20, function(i, item) {

	          content +="<tr >";
	          	content += "<td class='text-left'>"+item.studentNames+"</td>";
	          	content += "<td class='text-center'>"+item.studRegNber+"</td>";
	          content +="</tr>";

		   	});

			content +="</tbody></table>";
		content  	+= '</div></div>';

	}

  }).done(function() {	

  	DashGrAssessmentTrackLoding.hide();
  	DashGrAssessmentTrackContent.html( content );
  	DashGrAssessmentTrackContent.show();

  	$.fn.editable.defaults.ajaxOptions = {type: "PUT" };
  	$.fn.editable.defaults.mode = 'inline';
  	
  	$('.xeditableAssessmentType').editable({
    source: AssessmentTypes ,
        select2: {
           width: 150 ,
           multiple: false
        },
        success: function(response, newValue) {
	        if(response.status == 'error') return response.msg;
	    }
    });

  	$('.xeditableAssessmentPeriod').editable({
        source: [
              {id: '1', text: '1'},
              {id: '2', text: '2'},
              {id: '3', text: '3'},
              {id: '4', text: '4'},
              {id: '5', text: '5'},
              {id: '6', text: '6'},
              {id: '7', text: '7'},
              {id: '8', text: '8'},
              {id: '9', text: '9'},
              {id: '10', text: '10'}
           ],
        select2: {
           width:70,
           multiple: false
        },
        success: function(response, newValue) {
	        if(response.status == 'error') return response.msg;
	    }
    });

    $('.xeditableAssessmentMax').editable({
		emptytext: 'Click to add',
    	success: function(response, newValue) {
	        if(response.status == 'error') return response.msg;
	    }
	});

    dashGrRecordedAssessmentDelete(subjectID);

    //
    abTablePagingTable("paGrAssessementTrackRecords");
    abTablePagingTable("paGrMissingComments");
    abTablePagingTable("paGrMissingConduct_13");
    abTablePagingTable("paGrMissingConduct_14");
    abTablePagingTable("paGrMissingConduct_15");
    abTablePagingTable("paGrMissingConduct_16");
    abTablePagingTable("paGrMissingConduct_17");
    abTablePagingTable("paGrMissingConduct_18");
    abTablePagingTable("paGrMissingConduct_19");
    abTablePagingTable("paGrMissingConduct_20");

  }).fail(function() {
  })
  .always(function() {
  });

}

function dashGrRecordedAssessmentDelete(subjectID)
{	
	$( "table#paGrAssessementTrackRecords" ).delegate( "td.GrAssessmentTrackDelete", "click", function(e) {

		e.preventDefault();
		e.stopPropagation();

	  	var id 			= $(this).closest('tr').attr('id');
	  	GrDeleteAssessmentTrack(id, $(this) , subjectID );

	});
}

function GrDeleteAssessmentTrack( assessmentID , td , subjectID )
{

    var url     = base_url+"api/Gr/Assessment/"+assessmentID;
    td.html("Deleting...");
    
      $.ajax({
            url: url,
            type: 'DELETE',
            success: function(data) {
           
               if ( data.success )
               {
                    $.gritter.add({
                      title: 'Success',
                      text: 'Assessment deleted.',
                      class_name: 'success gritter-center',
                      time: ''
                    });

                    GrGetSubjectAssessments( subjectID );

               }else{

                 $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to delete Assessment, '+ data.error,
                    class_name: 'danger gritter-center',
                    time: ''
                  });

                  td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

             }
        }

    });

}

function onDashLoadReportSection( loadAllTwoSection )
{	

	var dashGradebook_contentAnalytics 	= $("#dashGradebook_contentAnalytics")
	var dashGradebook_contentOther 		= $("#dashGradebook_contentOther");

	var Content       = '';
	var LeftContent   = '';
	var RightContent  = '';

	if ( loadAllTwoSection) {

		Content 	+= '<div  class="col-sm-12">';
			Content  	+= '<div  class="col-sm-4" id="GrReportTypeSelectionContainer" >';
				LeftContent  	+= '<div class="row text-center"><h4>Generate New Report</h4></div>';
				LeftContent  	+= '<form  id="formDashGetSelectedReport" class="form-horizontal top-buffer" role="form" >';
					LeftContent  	+= '<input type="hidden" id="GrReportTypeOnlyYears" name="GrReportTypeOnlyYears" value="0" />';
					LeftContent  	+= '<div class="form-group"><label class="col-sm-3 control-label">Type</label><div class="col-sm-7"><select name="GrReportType" id="selectGrReportType" class="form-control select2-input" ></select></div></div>';
					LeftContent  	+= '<div class="form-group" id="GrReportPeriodContainer"><label class="col-sm-3 control-label">Period</label><div class="col-sm-7"><select name="GrReportPeriod" id="selectGrReportPeriod" class="form-control select2-input" ></select></div></div>';
					LeftContent  	+= '<div class="form-group" id="GrReportYearContainer"><label class="col-sm-3 control-label">Year</label><div class="col-sm-7"><select name="GrReportYear" id="selectGrReportYear" class="form-control select2-input" ></select></div></div>';
					LeftContent  	+= '<div class="form-group" id="GrReportClassContainer"><label class="col-sm-3 control-label">Class</label><div class="col-sm-7"><select name="GrReportClass" id="selectGrReportClass" class="form-control select2-input" ></select></div></div>';
					LeftContent  	+= '<div class="form-group" id="GrReportStudentsontainer"><label class="col-sm-3 control-label">Students</label><div class="col-sm-7"><select name="GrReportStudents" id="selectGrReportStudents" class="form-control select2-input" ></select></div></div>';
					LeftContent  	+= '<div class="form-group"><label class="col-sm-3 control-label">When finish</label><div class="col-sm-1"></div><div class="col-sm-7"><div class="radio"><label><input type="radio" name="whenFinish" id="" value="1" checked="checked" /> Notify me via email with link to download</label></div><div class="radio"><label><input type="radio" name="whenFinish" id="" value="0" /> Don\'t notify</label></div></div></div>';
					LeftContent 	+= '<div class="form-group top-buffer" id="GrReportLoadingContainer" ><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-default btn-block" >Generating...</button></div></div>';
					LeftContent 	+= '<div class="form-group top-buffer" id="GrReportSubmitContainer"><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-primary btn-block" type="submit" id="GrReportStudentsSubmit" >Generate New Report</button></div></div>';
				LeftContent 	+= '</form>';
			Content  += '</div>';
			Content 	+= '<div class="col-sm-8" id="dashGrSelectResultSection"><center><span class="top-buffer">Loading Generated Reports...</span></center></div>';
		Content 	+= '</div>';

		dashGradebook_contentOther.html(Content);

		dashGradebook_contentAnalytics.hide();
		dashGradebook_contentOther.slideDown();

	}else{

		$("#dashGrSelectResultSection").html('<center><span class="top-buffer">Loading...</span></center>');

		var GrReportLoadingContainer = $("#GrReportLoadingContainer");
		GrReportLoadingContainer.hide();

		var GrReportSubmitContainer  = $("#GrReportSubmitContainer");
		GrReportSubmitContainer.hide();

	}

	var url = base_url+"api/Gr/ReportCards?loadAllTwoSection="+loadAllTwoSection; 

	//get 
	$.getJSON( url, function(data) {

			var GenerateReports 	= data.GenerateReports ;
			var GenerateReportsSize = GenerateReports.length ;

			RightContent    = '';
			RightContent  	+= '<div class="row text-center">';
				RightContent  	+= '<div class="col-sm-12">';
					RightContent  	+= '<div class="col-sm-9"><h4>Recent Generated Reports</h4></div>';
					RightContent  	+= '<div class="col-sm-3"><a onclick="onDashLoadReportSection(false)" href="#"><i class="fa fa-refresh"></i>&nbsp;&nbsp;Check finished reports</a></div>';
				RightContent  	+= '</div>';
			RightContent  	+= '</div>';
			RightContent  	+= '<div class="row">';
				RightContent  += '<table class="table table-striped" id="paGrGeneratedReport" ><thead><tr><th class="row text-left">Generated on</th><th class="row text-left">Class</th><th class="row text-left">Student</th><th class="row text-left">Type</th><th class="row text-right">Downloads</th><th class="row text-center">Download Reports</th><th class="row text-center">Delete</th></tr></thead><tbody>';		
				
				//Report Cards 
				for ( var i = 0; i < GenerateReportsSize; i++) {

					var  GenerateReportsID  = GenerateReports[i].id ;
					var  is_Status		    = GenerateReports[i].is_Status ;
					var  type				= GenerateReports[i].typeName ;
					var  numberOfDownload	= GenerateReports[i].numberOfDownload;
					var  className			= GenerateReports[i].className;
					var  studRegNber		= GenerateReports[i].studRegNber;
					var  studentNames		= GenerateReports[i].studentNames;
					var  downloadLink		= GenerateReports[i].downloadLink;
					var  created_at 		= GenerateReports[i].created_at;

					RightContent +="<tr id="+GenerateReportsID+" downloadname='"+downloadLink+"'>";
			            RightContent +="<td class='row text-left' >"+date_HM_moment(created_at)+"</td>";
			            RightContent +="<td class='row text-left'>"+className+"</td>";
			            if (studRegNber)
			             {
			             	RightContent +="<td class='row text-left'>"+studentNames+"</td>";
			             }else{
			             	RightContent +="<td class='row text-center'>All</td>";
			             }
			            RightContent +="<td class='row text-left'>"+type+"</td>";
			            RightContent +="<td class='row text-center'>"+numberOfDownload+"</td>";
			            if ( is_Status == 2 ) 
			            {
			           		RightContent +="<td class='row text-center'><a target='_blank' href='PDF/ReportCard/"+downloadLink+"'><i class=' fa fa-download text-green'></i> Download</a></td>";
			           		RightContent +='<td class="text-center GrGenerateReportDelete"><a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a></td>';

			            }else{
			            	RightContent +="<td class='row text-center'><span class='text-warning'>Pending...</span></td>";
			            	RightContent +="<td class='row text-center'></td>";
			            }
			        RightContent +="</tr>";

				}

			RightContent +="</tbody></table>";
		RightContent  += '</div>';
		
		if ( loadAllTwoSection ) {
			$("#GrReportTypeSelectionContainer").html(LeftContent);
		}

		$("#dashGrSelectResultSection").html(RightContent);

		var GrReportLoadingContainer = $("#GrReportLoadingContainer");
		GrReportLoadingContainer.hide();

		var GrReportSubmitContainer  = $("#GrReportSubmitContainer");
		GrReportSubmitContainer.show();

		var GrReportPeriodContainer = $("#GrReportPeriodContainer");
		var GrReportYearContainer   = $("#GrReportYearContainer");
		var GrReportClassContainer  = $("#GrReportClassContainer");
		var GrReportTypeOnlyYears   = $("#GrReportTypeOnlyYears");

		if ( loadAllTwoSection ) {

			GrReportPeriodContainer.hide();
	   	 	GrReportYearContainer.hide();
			GrReportClassContainer.show();

		}

		abTablePagingTable("paGrGeneratedReport");

		if ( loadAllTwoSection ) {

			var SchoolReportType  	= data.SchoolReportType ;
			var Classrooms  		= data.Classrooms ;
			var Students 			= data.Students ;
			var SchoolPeriods 		= data.SchoolPeriods ;
			var OnlyYears 			= data.OnlyYears ;
			var Years 				= data.Years ;

			var SchoolReportTypeSize= SchoolReportType.length ;
			var ClassroomsSize 		= Classrooms.length ;
			var StudentsSize 		= Students.length ;
			var SchoolPeriodsSize 	= SchoolPeriods.length ;
			var YearsSize 			= Years.length;

			if ( OnlyYears ) {

				GrReportTypeOnlyYears.val(1);

				GrReportYearContainer.show();
				GrReportClassContainer.hide();

			}else{

				GrReportTypeOnlyYears.val(0) ;

				GrReportYearContainer.hide();
				GrReportClassContainer.show();
			}

			//Populate Report Type 
				var selectGrReportType = $("#selectGrReportType");
			    selectGrReportType.empty();
			    var AnReportTypeOptions	= '<option ></option>';

			    for ( var i=0; i< SchoolReportTypeSize ; i++ ) {
			    	AnReportTypeOptions += '<option value="'+SchoolReportType[i].id+'">'+SchoolReportType[i].name+'</option>'; 
			    }

			    selectGrReportType.html( AnReportTypeOptions );
				selectGrReportType.select2().bind('change', getDashGrRcTypeSelected );

			GroupedStudentsClassArray 		= [[]];

			for ( var i = 0; i < ClassroomsSize; i++) {

				GroupedStudentsClassArray[Classrooms[i].id] = new Array();
			}

			for ( var i = 0; i < StudentsSize; i++) {

				var personID 	= Students[i].personID;
				var regNumber   = Students[i].studRegNber;
				var studentNames= Students[i].studentNames;
				var classId 	= Students[i].classId;	

				var row = [];

				row['personID'] 	= personID;
				row['regNumber'] 	= regNumber;
				row['studentNames'] = studentNames;

				GroupedStudentsClassArray[classId].push(row);

			}

			//populate year  
			var selectGrReportYear = $("#selectGrReportYear");
		    selectGrReportYear.empty();

			var AnYearOptions 	  = '';
				AnYearOptions 	  = '<option ></option>';

			var AnYearidentifier 	   = "";
			var AnisTheFirstYearRecord = true;

			for ( var i=0; i< YearsSize ; i++ ) {

				if ( Years[i].levelID == AnYearidentifier ){
				    AnYearOptions += '<option value="'+Years[i].levelID+'_'+Years[i].year+'" >'+Years[i].year+'</option>';
				
				}else{

				    AnYearidentifier = Years[i].levelID;
			    	if ( AnisTheFirstYearRecord ){

				    	AnisTheFirstYearRecord = false; 

			    	}else{
				    	AnYearOptions += '</optgroup>';
				    }

			    	AnYearOptions += '<optgroup label="'+Years[i].levelName+'">';
			   		AnYearOptions += '<option value="'+Years[i].levelID+'_'+Years[i].year+'"  >'+Years[i].year+'</option>';
			
				}
					    
			}

			selectGrReportYear.html( AnYearOptions );
			selectGrReportYear.select2();

			//populate class 
			var selectGrReportClass = $("#selectGrReportClass");
		    selectGrReportClass.empty();

			var AnClassOptions 	  = '';
				AnClassOptions 	  = '<option ></option>';

			var Anclassidentifier = "";
			var AnisTheFirsRecord = true;

			for ( var i=0; i< ClassroomsSize ; i++ ) {

				if ( Classrooms[i].classidentifier == Anclassidentifier ){
				    AnClassOptions += '<option value="'+Classrooms[i].id+'" data-yearname="'+Classrooms[i].YearName+'" data-level="'+Classrooms[i].levelID+'" >'+Classrooms[i].name+'</option>';
				
				}else{

				    Anclassidentifier = Classrooms[i].classidentifier;
			    	if ( AnisTheFirsRecord ){

				    	AnisTheFirsRecord = false; 

			    	}else{
				    	AnClassOptions += '</optgroup>';
				    }

			    	AnClassOptions += '<optgroup label="'+Classrooms[i].YearName+'">';
			   		AnClassOptions += '<option value="'+Classrooms[i].id+'" data-yearname="'+Classrooms[i].YearName+'" data-level="'+Classrooms[i].levelID+'" >'+Classrooms[i].name+'</option>';
			
				}
					    
			}

			selectGrReportClass.html( AnClassOptions );
			selectGrReportClass.select2({ 
		            formatResult: newStudentformatResult,
		            formatSelection: newStudentformatSelection
		    }).bind('change', getDashGrRcClassSelected);
			
			//Populate Period 
			var selectGrReportPeriod = $("#selectGrReportPeriod");
	    	selectGrReportPeriod.empty();

	    	var AnGrReportPeriodOption = "";
	    	AnGrReportPeriodOption    += '<option value="0">All</option>';

	    		for ( var i=0; i< SchoolPeriodsSize ; i++ ) {
	    			AnGrReportPeriodOption += '<option value="'+SchoolPeriods[i].period+'" >'+SchoolPeriods[i].period+'</option>';
				
	    		}
	    	selectGrReportPeriod.html( AnGrReportPeriodOption );
			selectGrReportPeriod.select2();

			//populate Students 
			var selectGrReportStudents = $("#selectGrReportStudents");
		    selectGrReportStudents.empty();

		    var AnStudentsOptions 	= '<option value="0">All</option>';

		    for ( var i = 0; i < StudentsSize; i++) {

				var personID 	= Students[i].personID;
				var regNumber   = Students[i].studRegNber;
				var studentNames= Students[i].studentNames;

				AnStudentsOptions += "<option value="+personID+" data-regnumber="+regNumber+" >"+regNumber+" ("+studentNames+") </option>";

			}

			selectGrReportStudents.html(AnStudentsOptions);
			selectGrReportStudents.select2();

			//Populate Student Type 
				var dashGrSendReportViaEmailStudent = $("#dashGrSendReportViaEmailStudent");
			    dashGrSendReportViaEmailStudent.empty();
			    var GrSendReportViaEmailStudentOptions	= '<option ></option>';

			    for ( var i=0; i< StudentsSize ; i++ ) {
			    	GrSendReportViaEmailStudentOptions += '<option value="'+Students[i].studRegNber+'">'+Students[i].studentNames+' ( '+Students[i].studRegNber+' )</option>'; 
			    }

			    dashGrSendReportViaEmailStudent.html( GrSendReportViaEmailStudentOptions );
			    dashGrSendReportViaEmailStudent.select2();

		}else{

			if ( GrReportTypeOnlyYears.val() == 1 ) {

				GrReportYearContainer.show();
				GrReportClassContainer.hide();

			}else{

				GrReportYearContainer.hide();
				GrReportClassContainer.show();
			}


		}


    })
    .done(function() {

    	$( "table#paGrGeneratedReport" ).delegate( "td.GrGenerateReportDelete", "click", function(e) {

				e.preventDefault();
	    		e.stopPropagation();

			  	var id 			= $(this).closest('tr').attr('id');
			  	var downloadname= $(this).closest('tr').attr('downloadname');

			  	$("#generatedReportID").val(id);
				$("#generatedReportDownloadName").val(downloadname);	

			  	$('#Model_DashGrDeleteGeneratedReport').modal('show');

			});

    	if ( loadAllTwoSection ) {

    		//validate and submit form 
	    	getDashGrRcValidate();
			submitGrSendReportViaEmail();

    	}

    })
    .fail(function() {
    })
    .always(function() {

    	var GrReportSubmitContainer  = $("#GrReportSubmitContainer");
		GrReportSubmitContainer.slideDown();

    });

}


function submitGrSendReportViaEmail()
{
	  var formGrSendReportViaEmail =  $('#formGrSendReportViaEmail');

	  formGrSendReportViaEmail.on('submit', function(e){

	  var frm = formGrSendReportViaEmail;
	  var url = base_url+"api/Gr/SendReportEmail"; 
	  e.preventDefault();

	  $('#Model_GrSendReportViaEmail').modal('toggle');

	  $.post(url, 
	     frm.serialize(), 
         function(data, status, xhr){

         	

	          if ( data.success )
	          {   

	            $.gritter.add({
	                title: 'Success',
	                text: 'The system is now sending the reports to parents.',
	                class_name: 'success gritter-center',
	                time: ''
	            });

	            $('#dashGrSendReportViaEmailStudent').select2('data', null);

	          } else {
	              
	            $.gritter.add({
	               title: 'Failed',
	               text: 'Something went wrong' ,
	               class_name: 'warning gritter-center',
	               time: ''
	            });

	            $('#Model_GrSendReportViaEmail').modal('show');

              }
                 
         },"json")
          .done(function() { 

          })
          .fail(function() {

              $.gritter.add({
                title: 'Failed',
                text: 'Failed to add student, Please Try Again',
                class_name: 'danger gritter-center',
                time: ''
              });

              $('#Model_GrSendReportViaEmail').modal('show');
          })
          .always(function() {
            
          });

	
	});
}


//Deliberation 
function onDashLoadDeliberationSection()
{
	var dashGradebook_contentAnalytics 	= $("#dashGradebook_contentAnalytics")
	var dashGradebook_contentOther 		= $("#dashGradebook_contentOther");

	var content  = '';
	content 	+= '<div  class="col-sm-12">';
		content  	+= '<div class="col-sm-6 col-sm-offset-3">';
			content  	+= '<div class="form-group"><label class="col-sm-3 ">Class Deliberation</label><div class="col-sm-7"><select name="GrDeliberationClass" id="selectGrDeliberationClass" class="form-control select2-input" placeholder="Choose Class Here" ></select></div></div>';
		content 	+= '</div>';
	content 	+= '</div>';
	content 	+= '<div class="col-sm-12 text-center top-buffer" id="GrDeliberationClassName" >';
	content 	+= '</div>';
	content 	+= '<div class="col-sm-12" id="GrDeliberationClassContainer" >';
	content 	+= '</div>';

	dashGradebook_contentOther.html(content);

	dashGradebook_contentAnalytics.hide();
	dashGradebook_contentOther.slideDown();

	var url = base_url+"api/Gr/Deliberation"; 

	//get 
	$.getJSON( url, function(data) {

	//populate class 
		var selectGrDeliberationClass = $("#selectGrDeliberationClass");

	    selectGrDeliberationClass.empty();

		var AnClassOptions 	  	= '<option></option>';

		var Anclassidentifier 	= "";
		var AnisTheFirsRecord 	= true;
		
		var Classrooms  		= data.Classrooms ;

		$.each( Classrooms , function(i, item) {

			if ( item.classidentifier == Anclassidentifier ){
			    AnClassOptions += '<option value="'+item.id+'" data-yearname="'+item.YearName+'" data-level="'+item.levelID+'" >'+item.name+'</option>';
			
			}else{

			    Anclassidentifier = item.classidentifier;
		    	if ( AnisTheFirsRecord ){

			    	AnisTheFirsRecord = false; 

		    	}else{
			    	AnClassOptions += '</optgroup>';
			    }

		    	AnClassOptions += '<optgroup label="'+item.YearName+'">';
		   		AnClassOptions += '<option value="'+item.id+'" data-yearname="'+item.YearName+'" data-level="'+item.levelID+'" >'+item.name+'</option>';
		
			}

		});

		selectGrDeliberationClass.html( AnClassOptions );
		selectGrDeliberationClass.select2({ 
	            formatResult: newStudentformatResult,
	            formatSelection: newStudentformatSelection
	    }).bind('change', getDashDeliberationSelected );
		

    })
    .done(function() {

    })
    .fail(function() {
    })
    .always(function() {
    });

}

var rows_selected = [];
function getDashDeliberationSelected()
{

	var GrDeliberationClassContainer 	= $("#GrDeliberationClassContainer");
	var GrDeliberationClassName 		= $("#GrDeliberationClassName");

	var selectGrDeliberationClass 		= $("#selectGrDeliberationClass");

	var selectedClassID 	 			= selectGrDeliberationClass.select2('val');
	var Class_yearname 					= selectGrDeliberationClass.select2().find(":selected").data("yearname");

	var selected_className 				= Class_yearname+" "+selectGrDeliberationClass.select2('data').text ;

	GrDeliberationClassName.hide();
	GrDeliberationClassName.html("<h4><b>"+selected_className+"</b></h4>");
	GrDeliberationClassName.slideDown();

	var content  = '';
		content 	+= '<form id="formChangeDeliberation" ></form><div class="col-sm-12" >';
		 	content 	+= '<div class="btn-group" id="ChangeDeliberationAction" >';
		 		content 	+= '<div class="btn-group"><a class="btn btn-primary" onClick="GrChangeDeliberationStatus( '+selectedClassID+',1)" ><i class="fa fa-tachometer"></i>&nbsp;&nbsp;Promoted</a></div>';
		 		content 	+= '<div class="btn-group"><a class="btn btn-warning" onClick="GrChangeDeliberationStatus( '+selectedClassID+',3)" ><i class="fa fa-tachometer"></i>&nbsp;&nbsp;Repeated</a></div>';
		 		content 	+= '<div class="btn-group"><a class="btn btn-danger"  onClick="GrChangeDeliberationStatus( '+selectedClassID+',4)" ><i class="fa fa-tachometer"></i>&nbsp;&nbsp;Discontinued</a></div>';
		 	content 	+= '</div>';
		content 	+= '</div>';
		content 	+= '<div id="ChangeDeliberationMessage" >';
		content 	+= '</div>';
		content 	+= '<div id="ChangeDeliberationTableContainer" >';
		content 	+= '</div>';

		GrDeliberationClassContainer.html(content);

	GrDeliberationLoadSelectedClass( selectedClassID );
}

function GrDeliberationLoadSelectedClass( selectedClassID ){

	var ChangeDeliberationTableContainer = $("#ChangeDeliberationTableContainer");
	var url = base_url+"api/Gr/Deliberation/"+selectedClassID ; 

	var content  = '';
	content 	+= '<div class="col-sm-12" ><table class="table table-bordered" id="GrTbDeliberationTable" ><thead><tr><th><input name="select_all" value="1" type="checkbox"></th><th class="text-center">#</th><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Decision</th></tr></thead><tbody>';
	
	//get 
	$.getJSON( url, function(data) {

		var index = 0 ;

		$.each(data, function(i, item) {

			index++;
            content +="<tr >";
              content +="<td ></td>";
	          content +="<td >"+index+"</td>";
	          content +="<td >"+item.studentNames+"</td>";
	          content +="<td >"+item.studRegNber+"</td>";
	          content +="<td class='text-center' >"+GrStudentDeliberationStatus(item.statusID)+"</td>";
	        content +="</tr>";

        });
	
    })
    .done(function() {

    	content     +='</tbody></table>';
    	content 	+= '</div>';

    	ChangeDeliberationTableContainer.html(content);

    	var ChangeDeliberationAction = $("#ChangeDeliberationAction");
    	ChangeDeliberationAction.hide();

    	var ChangeDeliberationMessage = $("#ChangeDeliberationMessage");
    	ChangeDeliberationMessage.hide();

        function updateDataTableSelectAllCtrl(table){
		   
			   var $table             = table.table().node();
			   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
			   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
			   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

			   // If none of the checkboxes are checked
			   if($chkbox_checked.length === 0){
			      chkbox_select_all.checked = false;
			      if('indeterminate' in chkbox_select_all){
			         chkbox_select_all.indeterminate = false;
			      }

			      ChangeDeliberationAction.slideUp();

			   // If all of the checkboxes are checked
			   } else if ($chkbox_checked.length === $chkbox_all.length){
			      chkbox_select_all.checked = true;
			      if('indeterminate' in chkbox_select_all){
			         chkbox_select_all.indeterminate = false;
			      }

			      ChangeDeliberationAction.slideDown();

			   // If some of the checkboxes are checked
			   } else {
			      chkbox_select_all.checked = true;
			      if('indeterminate' in chkbox_select_all){
			         chkbox_select_all.indeterminate = true;
			      }
			      ChangeDeliberationAction.slideDown();
			   }
		}

	   // Array holding selected row IDs
	  	   rows_selected = [];
		   var table = $('#GrTbDeliberationTable').DataTable({
		   		paging: false,
		        'columnDefs': [{
		        'targets': 0,
		         'searchable': false,
		         'orderable': false,
		         'width': '1%',
		         'className': 'dt-body-center',
		         'render': function (data, type, full, meta){
		             return '<input type="checkbox">';
		         }
		      }],
		      'order': [[1, 'asc']],
		      'rowCallback': function(row, data, dataIndex){
		         // Get row ID
		         var rowId = data[3];

		         // If row ID is in the list of selected row IDs
		         if($.inArray(rowId, rows_selected) !== -1){
		            $(row).find('input[type="checkbox"]').prop('checked', true);
		            $(row).addClass('selected');
		         }
		      }
		   });

	   // Handle click on checkbox
		   $('#GrTbDeliberationTable tbody').on('click', 'input[type="checkbox"]', function(e){

		      var $row = $(this).closest('tr');
		      // Get row data
		      var data = table.row($row).data();
		      // Get row ID
		      var rowId = data[3];

		      // Determine whether row ID is in the list of selected row IDs 
		      var index = $.inArray(rowId, rows_selected);

		      // If checkbox is checked and row ID is not in list of selected row IDs
		      if(this.checked && index === -1){
		         rows_selected.push(rowId);

		      // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
		      } else if (!this.checked && index !== -1){
		         rows_selected.splice(index, 1);
		      }

		      if(this.checked){
		         $row.addClass('selected');

		      } else {
		         $row.removeClass('selected');
		      }

		      // Update state of "Select all" control
		      updateDataTableSelectAllCtrl(table);

		      // Prevent click event from propagating to parent
		      e.stopPropagation();
		   });

	   // Handle click on table cells with checkboxes
		   $('#GrTbDeliberationTable').on('click', 'tbody td, thead th:first-child', function(e){
		      $(this).parent().find('input[type="checkbox"]').trigger('click');
		   });

	   // Handle click on "Select all" control
		   $('thead input[name="select_all"]', table.table().container()).on('click', function(e){
		      if(this.checked){
		         $('#GrTbDeliberationTable tbody input[type="checkbox"]:not(:checked)').trigger('click');
		      } else {
		         $('#GrTbDeliberationTable tbody input[type="checkbox"]:checked').trigger('click');
		      }

		    // Prevent click event from propagating to parent
		      e.stopPropagation();

		   });

	   // Handle table draw event
	   	  table.on('draw', function(){
	      	// Update state of "Select all" control
	      		updateDataTableSelectAllCtrl(table);
	  	  });

    })
    .fail(function() {
    })
    .always(function() {

    	var selectGrDeliberationClass 		= $("#selectGrDeliberationClass");
    	selectGrDeliberationClass.select2('data', null);

    });

}

function GrChangeDeliberationStatus( selectedClassID, status )
{
	  var frm = $('#formChangeDeliberation');
      var url = base_url+"api/Gr/Deliberation?class="+selectedClassID+"&status="+status ;

      var ChangeDeliberationAction  = $('#ChangeDeliberationAction');
      var GrTbDeliberationTable  	= $('#GrTbDeliberationTable');
      var ChangeDeliberationMessage = $("#ChangeDeliberationMessage");

      ChangeDeliberationMessage.html("Saving please wait...");

      ChangeDeliberationAction.slideUp();
      GrTbDeliberationTable.slideUp();
      ChangeDeliberationMessage.slideUp();

      $(frm).html("");

      	// Iterate over all selected checkboxes
	      $.each(rows_selected, function(index, rowId){

	    // Create a hidden element 
	         $(frm).append(
	             $('<input>')
	                .attr('type', 'hidden')
	                .attr('name', 'id[]')
	                .val(rowId)
	         );
	      });

	      $.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){

	         	$.gritter.add({
		            title: 'Success',
		            text:  'Successfully Saved',
		            class_name: 'success gritter-center',
		            time: '' 
		        });

	     },"json")
	      .done(function() {
	        
	        GrDeliberationLoadSelectedClass(selectedClassID);

	      })
	      .fail(function() {

	          $.gritter.add({
	            title: 'Failed',
	            text: 'Something went wrong, Please Try Again',
	            class_name: 'danger gritter-center',
	            time: ''
	          });

	      })
	      .always(function() {});
}

function GrStudentDeliberationStatus( status )
{
	var content ='';

	switch(status) {
	    case '1': //Promoted 
	    	content = '<span class="badge bg-blue"> Promoted</span>';
	        break;

	    case '2': //2nd Sitting
	       content = '<span class="badge bg-aqua"> 2nd Sitting</span>';
	        break;

        case '3': //Repeated  
        	content = '<span class="badge bg-yellow"> Repeated</span>';
	        break;

	    case '4': //Discontinued
        	content = '<span class="badge bg-red"> Discontinued</span>';
	        break;

	    default:
	    	content = '<span class="badge"> Not Yet</span>';
	    	break;
	}

	return content ;
}

//Procramation 
function onDashLoadProclamationSection()
{
	var dashGradebook_contentAnalytics 	= $("#dashGradebook_contentAnalytics")
	var dashGradebook_contentOther 		= $("#dashGradebook_contentOther");

	var content  = '';
	content 	+= '<div  class="col-sm-12">';
		content  	+= '<div  class="col-sm-4 ">';
			content  	+= '<div class="row text-center"><h4>Proclamation</h4></div>';
			content  	+= '<form  id="formDashGetSelectedProclamation" class="form-horizontal top-buffer" role="form" >';
				content  	+= '<div class="form-group"><label class="col-sm-3 control-label">Type</label><div class="col-sm-7"><select name="GrProclamationType" id="selectGrProclamationType" class="form-control select2-input" ></select></div></div>';
				content  	+= '<div class="form-group"><label class="col-sm-3 control-label">Class</label><div class="col-sm-7"><select name="GrProclamationClass" id="selectGrProclamationClass" class="form-control select2-input" ></select></div></div>';
				content 	+= '<div class="form-group top-buffer"><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-primary btn-block" type="submit" id="GrProclamationSubmit" >View Class Proclamation</button></div></div>';
			content 	+= '</form>';
		content 	+= '</div>';
		content 	+= '<div class="col-sm-8" id="dashGrSelectProclamationSection" ></div>';
	content 	+= '</div>';

	dashGradebook_contentOther.html(content);

	dashGradebook_contentAnalytics.hide();
	dashGradebook_contentOther.slideDown();

	//Hide GrReportPeriodContainer 
	var GrReportPeriodContainer = $("#GrReportPeriodContainer");
    GrReportPeriodContainer.hide();

    var GrReportStudentsontainer = $("#GrReportStudentsontainer");
    GrReportStudentsontainer.slideDown();
    
	var url = base_url+"api/Gr/Proclamation"; 

	var SendSMS  = true ;

	//get 
	$.getJSON( url, function(data) {

		//populate class 
		var selectGrProclamationType  = $("#selectGrProclamationType");
		var selectGrProclamationClass = $("#selectGrProclamationClass");

	    selectGrProclamationClass.empty();

		var AnClassOptions 	  = '<option></option>';

		var Anclassidentifier = "";
		var AnisTheFirsRecord = true;

		SendSMS 				= data.SendSMS;
		var SchoolReportType  	= data.SchoolReportType ;
		var SchoolReportTypeSize= SchoolReportType.length ;
		
		var Classrooms  		= data.Classrooms ;

		$.each( Classrooms , function(i, item) {

			if ( item.classidentifier == Anclassidentifier ){
			    AnClassOptions += '<option value="'+item.id+'" data-yearname="'+item.YearName+'" data-level="'+item.levelID+'" >'+item.name+'</option>';
			
			}else{

			    Anclassidentifier = item.classidentifier;
		    	if ( AnisTheFirsRecord ){

			    	AnisTheFirsRecord = false; 

		    	}else{
			    	AnClassOptions += '</optgroup>';
			    }

		    	AnClassOptions += '<optgroup label="'+item.YearName+'">';
		   		AnClassOptions += '<option value="'+item.id+'" data-yearname="'+item.YearName+'" data-level="'+item.levelID+'" >'+item.name+'</option>';
		
			}

		});

		var AnReportTypeOptions	= '<option ></option>';

	    for ( var i=0; i< SchoolReportTypeSize ; i++ ) {
	    	AnReportTypeOptions += '<option value="'+SchoolReportType[i].id+'">'+SchoolReportType[i].name+'</option>'; 
	    }

		selectGrProclamationClass.html( AnClassOptions );
		selectGrProclamationClass.select2({ 
	            formatResult: newStudentformatResult,
	            formatSelection: newStudentformatSelection
	    });

		selectGrProclamationType.html( AnReportTypeOptions );
	    selectGrProclamationType.select2();
		

    })
    .done(function() {

    	getDashGrRcProclamationValidate( SendSMS );

    })
    .fail(function() {
    })
    .always(function() {
    });
}

function getDashGrRcProclamationValidate( SendSMS )
{
	//validation
	var formDashGetSelectedProclamation = $('#formDashGetSelectedProclamation');

	formDashGetSelectedProclamation.formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        exclude: ':disabled',
        fields: {
            GrProclamationClass: {
                validators: {
                    notEmpty: {
                        message: 'Class is required'
                    }
                }
            }
        }
    })
	.on('success.form.fv', function(e , data) {

	e.preventDefault();

		onDashLoadProclamationSectionContent( SendSMS );

 	var GrProclamationSubmit = $('#GrProclamationSubmit');
		GrProclamationSubmit.removeClass('disabled'); 
		GrProclamationSubmit.removeAttr('disabled');
    }).end();

}

function onDashLoadProclamationSectionContent( SendSMS )
{	
	  var dashGrSelectProclamationSection = $('#dashGrSelectProclamationSection');

	  var frm = $('#formDashGetSelectedProclamation');
      var url = base_url+"api/Gr/Proclamation"; 

      var TypeID  = $('#selectGrProclamationType').select2('val');
      var ClassID = $('#selectGrProclamationClass').select2('val');

      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){

            var content  = '' ;
            content 	+= '<div class="row">';
            	content 	+= '<div class="col-sm-6 text-right"><h4><strong>Class Performance</strong></h4></div>';

            	if ( SendSMS ) {
            		content 	+= '<div class="col-sm-3"><a class="btn btn-default pull-right" data-id="0" data-toggle="modal" data-target="#Model_DashGrProcMessage" id="DasGrProcStudentSendMessage" ><i class="fa fa-envelope"></i>&nbsp;&nbsp;Notify parents about this</a></div>';
        
            	}
        		
        		content 	+= '<div class="col-sm-3"><a class="btn btn-default pull-right" target="_blank" href="api/Gr/PDF/DownloadProclamation/'+ClassID+'?TypeID='+TypeID+'" ><i class="fa fa-print"></i>&nbsp;&nbsp;Download</a></div></div>'; 
            content 	+= data ;
 
            dashGrSelectProclamationSection.html( content );
             
     },"html")
      .done(function() {
        
        var dashGrProStudentSelect  = $('#dashGrProStudentSelect');

        var GrProcTypeID 			= $('#GrProcTypeID');  
        var GrProcClassID 			= $('#GrProcClassID');

        var ClassStudents 			= GroupedProcStudentsClassArray[ClassID];
		var StudentsOptions			= '';

		var StudentsOptions 		= '<option ></option>';

		$.each(ClassStudents, function(i, item) {
			StudentsOptions += "<option value="+item.personID+" data-regnumber="+item.regNumber+" >"+item.regNumber+" ("+item.studentNames+") </option>";
	    });	

		dashGrProStudentSelect.html( StudentsOptions );
		dashGrProStudentSelect.select2().val('0').trigger('change');

		GrProcTypeID.val( TypeID );
		GrProcClassID.val( ClassID );

      })
      .fail(function() {

          $.gritter.add({
            title: 'Failed',
            text: 'Something went wrong, Please Try Again',
            class_name: 'danger gritter-center',
            time: ''
          });

      })
      .always(function() {});
                
}

function submitDashGrProclamationSendSMS()
{

	var DashGrProcEditAssessmentLoding = $( "#DashGrProcEditAssessmentLoding" );
	var formDashGrProcMessage		   = $( "#formDashGrProcMessage" );

	DashGrProcEditAssessmentLoding.show();
	formDashGrProcMessage.hide();

	var frm = $('#formDashGrProcMessage');
	var url = base_url+"api/Gr/Proclamation/SendSMS"; 

	  $.post(url, 
	     frm.serialize(), 
	     function(data, status, xhr){

	      if ( data.success )
	      {   

	       	$('#Model_DashGrProcMessage').modal('toggle');
	       	$.gritter.add({
	            title: 'Success',
	            text:  'Messages Sent',
	            class_name: 'success gritter-center',
	            time: '' 
	        });

	        formDashGrProcMessage.show();
	      	DashGrProcEditAssessmentLoding.hide();

	      } else {

	      	  formDashGrProcMessage.show();
	      	  DashGrProcEditAssessmentLoding.hide();
			  
	          $.gritter.add({
	            title: 'Failed',
	            text: 'Failed to send Messages',
	            class_name: 'danger gritter-center',
	            time: '' 
	          });
	      }
	         
	 },"json")
	  .done(function() {
	  })
	  .fail(function() {	

	  	  formDashGrProcMessage.show();
	      DashGrProcEditAssessmentLoding.hide();

	      $.gritter.add({
	        title: 'Failed',
	        text: 'Something went wrong, Please Try Again',
	        class_name: 'danger gritter-center',
	        time: ''
	      });

	  })
	  .always(function() {});
}

function getAcStudyGroupAll()
{	
	
	$( "#dashContentainer_Academics").slideDown();
    var container          = $( "#dashAcademics_content" );

    container.empty();

    var url = base_url+"api/Ac/studyGroup";

    leftContent = '';

    $.getJSON( url, function(data) {

        leftContent = '<div class="col-sm-12">';
        leftContent += '<div class="col-sm-6">';
        leftContent += '<div class="row"><div class="col-sm-12 text-right"><h4><strong>Teaching Sets</strong></h4></div></div>'; 
        leftContent += '<div class="row"><table class="table table-hover" id="dashGRStudyGroupTable" ><thead><tr><th>Name</th><th>Teacher</th><th class="text-center">Number Of Students</th><th class="text-center">Delete</th></tr></thead><tbody>';
    
        $.each(data, function(i, item) {

            leftContent +="<tr id='"+item.id+"' groupname="+item.name+" >";
              leftContent +="<td >"+item.name+"</td>";
              leftContent +="<td >"+item.teachername+"</td>";
              leftContent +="<td class='text-center'>"+item.numberOfStudent+"</td>";
              leftContent +="<td class='text-center GrStudyGroupDelete'><a href='#'><i class='fa fa-times text-red'></i></a></td>";
            leftContent +="</tr>";

        });
      
        leftContent +='</tbody></table></div>';
        leftContent +='</div>';
        leftContent += '<div class="col-sm-1"></div>';
        leftContent += '<div class="col-sm-5" id="dashGrSelectedStudyGroup">';
        leftContent +='</div>';
      
    }).done(function() {

        container.append( leftContent );

        dashGrStudyGroupSelected();
        abTablePagingTable('dashGRStudyGroupTable');

        $( "#dashGrAddStudentToGroupBTN" ).click(function() {
            $('#Model_GrAddStudentToGroup').modal('show');
        });

        $( "table#dashGRStudyGroupTable" ).delegate( "td.GrStudyGroupDelete", "click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          var rowid        = $(this).closest('tr').attr('id');
          //GrRemoveStudentInStudyGroup( $(this) , rowid, groupId , groupname );

      });

    })
    .fail(function() {
    })
    .always(function() {
    });

}

function dashGrStudyGroupSelected()
{
    $( "table#dashGRStudyGroupTable" ).delegate( "td:not(.GrStudyGroupDelete)", "click", function() {

        id      	= $(this).closest('tr').attr('id');
        groupname 	= $(this).closest('tr').attr('groupname');

        get_gr_LoadStudyGroupMembers( id , groupname );

    });

}




function get_gr_LoadStudyGroupMembers( groupId , groupname )
{

  	var container          = $( "#dashGrSelectedStudyGroup" );
  	container.empty();

   	var url = base_url+"api/Ac/studyGroup/"+groupId;

   	leftContent = '';

    $.getJSON( url, function(data) {

        leftContent = '<div class="col-sm-12">';
        leftContent += '<div class="row"><div class="col-sm-12 text-center"><h5><strong>'+groupname+'</strong></h5></div></div>'; 
        leftContent += '<div class="row"><table class="table table-hover" id="dashGrStudyGroupMemberTable" ><thead><tr><th>Student Name</th><th>Reg. Number</th><th class="text-center">Remove</th></tr></thead><tbody>';
       
        $.each(data, function(i, item) {

            leftContent +="<tr id='"+item.id+"' >";
              leftContent +="<td>"+item.studentNames+"</td>";
              leftContent +="<td class='text-center'>"+item.studRegNber+"</td>";
              leftContent +="<td class='text-center GrStudyGroupMemberDelete'><a href='#'><i class='fa fa-times text-red'></i></a></td>";
            leftContent +="</tr>";

        });
      
        leftContent +='</tbody></table></div>';
        leftContent +='</div>';
      
    }).done(function() {

      container.append(leftContent);

      abTablePagingTable('dashGrStudyGroupMemberTable');


      $( "table#dashGrStudyGroupMemberTable" ).delegate( "td.GrStudyGroupMemberDelete", "click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          var rowid        = $(this).closest('tr').attr('id');
          GrRemoveStudentInStudyGroup( $(this) , rowid, groupId , groupname );

      });

    })
    .fail(function() {
    })
    .always(function() {
    });

}

function GrRemoveStudyGroup(td, rowid )
{
      var url     = base_url+"api/Ac/studyGroup/"+rowid+"?Type=1";
      td.html("Deleting...");

      $.ajax({
            url: url,
            type: 'DELETE',
            success: function(data) {
           
               if ( data.success )
               {    
                    $.gritter.add({
                      title: 'Success',
                      text: 'Study group deleted',
                      class_name: 'success gritter-center',
                      time: ''
                    });
                    
                    getAcStudyGroupAll();

               }else{

                 $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to remove study group',
                    class_name: 'danger gritter-center',
                    time: ''
                  });

                 td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');
             }
        }

    });
}

function GrRemoveStudentInStudyGroup(td, rowid , groupId, groupname )
{
      var url     = base_url+"api/Ac/studyGroup/"+rowid+"?Type=2";
      td.html("Removing...");

      $.ajax({
            url: url,
            type: 'DELETE',
            success: function(data) {
           
               if ( data.success )
               {    
                    $.gritter.add({
                      title: 'Success',
                      text: 'Student removed '+groupname+'.',
                      class_name: 'success gritter-center',
                      time: ''
                    });
                    
                    get_gr_LoadStudyGroupMembers(groupId, groupname );

               }else{

                 $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to remove on advisor ',
                    class_name: 'danger gritter-center',
                    time: ''
                  });

                 td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');
             }
        }

    });
}


function getHumanResource(){
	
	$("#moduleContainer").load("humanResource/main", function() { get_mn__staff(); } ).fadeIn(1000);
}

function get_mn__staff()
{
	$("#moduleMain").load("humanResource/mn__staff").fadeIn(1000);
}

function get_mn_salaryGrade()
{
	$("#moduleMain").load("humanResource/mn_salaryGrade").fadeIn(1000);
}

function get_mn_staffBankAccounts()
{
	$("#moduleMain").load("humanResource/mn_staffBankAccounts").fadeIn(1000);
}

function get_mn_staffQualifications()
{
	$("#moduleMain").load("humanResource/mn_staffQualifications").fadeIn(1000);
}

function get_new_schoolStaff()
{
  $('#moduleDashHomeContainer > div').hide();
	$("#dashNew_content").load("humanResource/new_schoolStaff" ,function(){ $( "#dashContentainer_new").slideDown();

  }).fadeIn(1000);
}

function get_hr_StudentAdvisors(isSelected, advisorId, advisorName)
{

    $( "#dashContentainer_HumanResource").slideDown();
    var container          = $( "#dashHumanResource_content" );

    container.empty();

    var url = base_url+"api/Hr/StudentAdvisor";

    leftContent = '';

    var AdvisorTypeVerb  = "advised";

    $.getJSON( url, function(data) {

        AdvisorTypeVerb = data.verb ;
        
        var AdvisorTypeName       = data.name ;
        var StudentAdvisor        = data.StudentAdvisor ;
        var StudentWithNoAdvisor  = data.StudentWithNoAdvisor ;

        leftContent = '<div class="col-sm-12">';
        leftContent += '<div class="col-sm-5">';
        leftContent += '<div class="row"><div class="col-sm-6 text-right"><h4><strong>Students\' '+AdvisorTypeName+'</strong></h4></div><div class="col-sm-6"><a class="btn btn-primary pull-right" id="dashHrAddStudentToAdvisor"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Students To '+AdvisorTypeName+'</a></div></div>'; 
        leftContent += '<div class="row"><table class="table table-hover" id="dashHrStudentAdvisorTable" ><thead><tr><th>Name</th><th class="text-center">Number Of Students</th></tr></thead><tbody>';
    
        $.each( StudentAdvisor, function(i, item) {

            leftContent +="<tr id='"+item.personID+"'  advisor='"+item.names+"' >";
              leftContent +="<td>"+item.names+"</td>";
              leftContent +="<td class='text-center'>"+item.numberOfStudents+"</td>";
            leftContent +="</tr>";

        });
      
        leftContent +='</tbody></table></div>';
        leftContent +='</div>';
        leftContent += '<div class="col-sm-1"></div>';
        leftContent += '<div class="col-sm-5" id="dashHrSelectedStudentAdvisor">';

          leftContent += '<div class="col-sm-12">';
            leftContent += '<div class="row"><div class="col-sm-12 text-center"><h5><strong>Students without '+AdvisorTypeName+'s </strong></h5></div></div>'; 
            leftContent += '<div class="row"><table class="table table-hover" id="dashHrStudentsWithoutAdvisorTable" ><thead><tr><th>Name</th><th class="text-center">Class</th></tr></thead><tbody>';
           
            $.each(StudentWithNoAdvisor, function(i, item) {

                leftContent +="<tr  >";
                  leftContent +="<td>"+item.studentNames+"</td>";
                  leftContent +="<td class='text-center '>"+item.classroomName+"</td>";
                leftContent +="</tr>";

            });
          
            leftContent +='</tbody></table></div>';
          leftContent +='</div>';

        leftContent +='</div>';
        
        
      

    }).done(function() {

        container.append( leftContent );
        dashHrStudentAdvisorSelected( AdvisorTypeVerb );
        abTablePagingTable('dashHrStudentAdvisorTable');
        abTablePagingTable('dashHrStudentsWithoutAdvisorTable');
        
        $( "#dashHrAddStudentToAdvisor" ).click(function() {
            $('#Model_HrAddStudentToAdvisor').modal('show');
        });

        if ( isSelected ) {
            get_hr_LoadAdvisorStudents(AdvisorTypeVerb, advisorId, advisorName);
        }

    })
    .fail(function() {
    })
    .always(function() {
    });
}

function formHrAddStudentToAdvisor()
{

  $(" #dashAddStudentToAdvisorAdvisor" ).select2();
  $(" #dashAddStudentToAdvisorStudent" ).select2();

  var frm = $('#formHrAddStudentToAdvisor');

  frm.on('submit', function(e){
    
    $('#Model_HrAddStudentToAdvisor').modal('toggle');

    var url = base_url+"api/Hr/StudentAdvisor"; 

      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
            
          if ( data.success )
          {   

              $.gritter.add({
                title: 'Success',
                text: 'Student added on the advisor',
                class_name: 'success gritter-center',
                time: ''
              });

              get_hr_StudentAdvisors(false,null,null);
              $('#dashAddStudentToAdvisorAdvisor').select2('data', null);
              $('#dashAddStudentToAdvisorStudent').select2('data', null); 

          } else {
              
              $.gritter.add({
                title: 'Failed',
                text: 'Failed to add students on advisor',
                class_name: 'danger gritter-center',
                time: ''
              });

              $('#Model_HrAddStudentToAdvisor').modal('show');

          }
             
     },"json")
      .done(function() { 
      })
      .fail(function() {
      
          $.gritter.add({
            title: 'Failed',
            text: 'Failed to add students on advisor',
            class_name: 'danger gritter-center',
            time: ''
          });

          $('#Model_HrAddStudentToAdvisor').modal('show');

      }).always(function() {});
      
    });
}

function dashHrStudentAdvisorSelected( AdvisorTypeVerb )
{
    $( "table#dashHrStudentAdvisorTable" ).delegate( "td", "click", function() {

        id      = $(this).closest('tr').attr('id');
        advisor = $(this).closest('tr').attr('advisor');
        get_hr_LoadAdvisorStudents( AdvisorTypeVerb, id, advisor);

    });

}

function get_hr_LoadAdvisorStudents(AdvisorTypeVerb, advisorId, advisorName)
{

  var container      = $( "#dashHrSelectedStudentAdvisor" );
  container.empty();

   var url = base_url+"api/Hr/StudentAdvisor/"+advisorId;

   leftContent = '';

    $.getJSON( url, function(data) {

        leftContent = '<div class="col-sm-12">';
        leftContent += '<div class="row"><div class="col-sm-12 text-center"><h5><strong>Student '+AdvisorTypeVerb+' by '+advisorName+'</strong></h5></div></div>'; 
        leftContent += '<div class="row"><table class="table table-hover" id="dashHrAdvisorStudentTable" ><thead><tr><th>Name</th><th class="text-center">Remove</th></tr></thead><tbody>';
       
        $.each(data, function(i, item) {

            leftContent +="<tr id='"+item.id+"' >";
              leftContent +="<td>"+item.names+"</td>";
              leftContent +="<td class='text-center HrAdvisorStudentDelete'><a href='#'><i class='fa fa-times text-red'></i></a></td>";
            leftContent +="</tr>";

        });
      
        leftContent +='</tbody></table></div>';
        leftContent +='</div>';
      
    }).done(function() {

      container.append(leftContent);

      abTablePagingTable('dashHrAdvisorStudentTable');

      $( "table#dashHrAdvisorStudentTable" ).delegate( "td.HrAdvisorStudentDelete", "click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          var rowid        = $(this).closest('tr').attr('id');
          HrRemoveStudentOnAdvisor( $(this) , rowid, advisorId , advisorName );

      });

    })
    .fail(function() {
    })
    .always(function() {
    });

}

function HrRemoveStudentOnAdvisor(td, rowid , advisorId, advisorName)
{
    var url     = base_url+"api/Hr/StudentAdvisor/"+rowid;
    td.html("Removing...");

      $.ajax({
            url: url,
            type: 'DELETE',
            success: function(data) {
           
               if ( data.success )
               {
                    $.gritter.add({
                      title: 'Success',
                      text: 'Student removed on advisor.',
                      class_name: 'success gritter-center',
                      time: ''
                    });

                    get_hr_StudentAdvisors(true, advisorId,  advisorName);

               }else{

                 $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to remove on advisor ',
                    class_name: 'danger gritter-center',
                    time: ''
                  });

                 td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');
             }
        }

    });
}

//submit school staff

function get_profile_staff(id)
{ 
    $("#dashNew_content").hide();
    $("#dashHumanResource_content").load("humanResource/profile_staff/"+id);
}

function view_profile_staff(id)
{
    
    get_profile_staff(id);
    $("#dashContentainer_HumanResource").slideDown();
    
}

function submitSchoolStaffSuccess(staffID)
{
    var successNotif  = '<div class="row"><h4 class="text-green">School Staff successfuly added!</h4></p></div>';
        successNotif += '<div class="row"><center><button type="button" class="btn btn-success" onclick="get_new_schoolStaff()" > New School Staff</button> or <button type="button" class="btn btn-primary" onclick="view_profile_staff('+staffID+')" >View Profile</button></center></div>';

    $("#dashNew_content").html("<center>"+successNotif+"</center>");
}

function submitSchoolStaff()
{
    $('#formNewSchoolStaff').on('submit', function(e){
    
      $("#formNewSchoolStaff").slideUp();
      $("#formNewSchoolStaffLoading").slideDown();

      var imageData = $('.newss-image-editor').cropit('export');
      $('.newss-hidden-image-data').val(imageData);

      var frm = $('#formNewSchoolStaff');
      var url = base_url+"api/Hr/SchoolStaff"; 

      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
            
           if (data.success)
                {   
                  var staffID = data.staff; 
                  submitSchoolStaffSuccess(staffID);

                } else {

                    alert('School staff not saved');

                    $("#formNewSchoolStaffLoading").slideUp();
                    $("#formNewSchoolStaff").slideDown();
                }
                 
         },"json");
    });

}

function selectStaffIdentification(selected)
{
    if (selected) 
    {
        $('#staff_Passport').slideUp();
        $('#staff_NationalID').slideDown();

    }else{

        $('#staff_NationalID').slideUp();
        $('#staff_Passport').slideDown();

    }
}

function submitStaffNewPicture()
{
    $('#staffUpdatePictureForm').on('submit', function(e){
        
      $('#staffChangeProfile').modal('toggle');
      
      $('#staffProfilePictureContainer').hide();
      $('#staffProfileLoadingContainer').slideDown();

      var profileStaffId =  $('#profileStaffId').val();

      var imageData = $('.image-editor').cropit('export');
      $('.hidden-image-data').val(imageData);

      var frm = $('#staffUpdatePictureForm');
      var url = base_url+"api/Hr/Profile_staff/updatePicture"; 

      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
                
                if (data.success)
                {   

                    var photoID = data.photoID;
                    var staffProfilePictureContent = "<img src='images/schoolStaff/200/"+profileStaffId+"/"+photoID+"' class='img-circle thumbnail'>";

                    var staffProfilePictureContainer = $('#staffProfilePictureContainer');

                    staffProfilePictureContainer.html(staffProfilePictureContent);

                    $.gritter.add({
                        title: 'Success',
                        text: 'Picture successfuly updated.',
                        class_name: 'success gritter-center',
                        time: ''
                    });

                    staffProfilePictureContainer.slideDown();

                } else {

                    $.gritter.add({
                        title: 'Failed',
                        text: 'Failed to update the picture.',
                        class_name: 'danger gritter-center',
                        time: ''
                    });
                }
                
                $('#staffProfileLoadingContainer').hide();

         },"json");
    });

}

function newSchoolStaffSelect2()
{

  $("#selectNewStaffNkNumberCountry").select2().bind('change', hrNewStaffNumberCountryChange); 
  $("#selectNewStaffId").select2().bind('change', hrNewStaffIdentificationChange );
}

function hrNewStaffNumberCountryChange()
{
    var selected = $("#selectNewStudentFaNumberCountry").select2('val');
    if (selected == 0)
    {
      $('#nextOfKinMobileNumber').mask('079-999-9999');
    }else{
      $('#nextOfKinMobileNumber').unmask();
    }
}

function hrNewStaffIdentificationChange()
{
    var selected = $("#selectNewStaffId").select2('val');
    if (selected == 0)
    {
      $('#staff_NID').mask('9 9999 9 9999999 9 99');
    }else{
      $('#staff_NID').unmask();
    }
}

function staffProfileEditNames()
{
    $(document).on("click", "#staffNameContent", function () {

        $('#staffNameContainer').hide();
        $('#staffNameEditContainer').slideDown();

    });
}

function hrRemoveStaffPermission( td , permissionID ){

      var staffPersonID =  $('#profileStaffId').val();
      var url     = base_url+"api/Hr/SchoolStaff/"+staffPersonID+"?type=3&permissionID="+permissionID;
      td.html("Deleting...");

        $.ajax({
              url: url,
              type: 'DELETE',
              success: function(data) {
             
                 if ( data.success )
                 {
                      $.gritter.add({
                        title: 'Success',
                        text: 'Permission successfully removed.',
                        class_name: 'success gritter-center',
                        time: ''
                      });

                 }else{

                   $.gritter.add({
                      title: 'Failed',
                      text: 'Failed to remove permission' ,
                      class_name: 'danger gritter-center',
                      time: ''
                    });

                   td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');
               }
          }

      });

}


function getHrNewPermissionValidate()
{
    //validation
        var formStaffNewPermission = $('#formStaffNewPermission');

        formStaffNewPermission.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                'Permission[]': {
                    validators: {
                        notEmpty: {
                            message: 'Permission to add is required'
                        }
                    }
                }
            }
        })
        .on('success.form.fv', function(e , data) {

        e.preventDefault();
        
        onDashTtGenerateTimeTableSubmitted();

        var formDashTtNewTimeSubmit = $('#formDashTtNewTimeSubmit');
            formDashTtNewTimeSubmit.removeClass('disabled'); 
            formDashTtNewTimeSubmit.removeAttr('disabled');
        }).end();

}

function onDashAtNewPermissionSubmitted(){

    var formStaffNewPermission        = $("#formStaffNewPermission") ;
    var formStaffNewPermissionLoading = $("#formStaffNewPermissionLoading") ;

    formStaffNewPermission.hide();
    formStaffNewPermissionLoading.slideDown();

    var frm = formStaffNewPermission;
    var url = base_url+"api/Hr/Permission"; 

    $.post(url, 
       formStaffNewPermission.serialize(), 
       function(data, status, xhr){

            if ( data.success )
            {   
              $.gritter.add({
                  title: 'Success',
                  text: 'Permission added.',
                  class_name: 'success gritter-center',
                  time: ''
              });

              $('#staffAddPermission').modal('toggle');

            } else {
                
                $.gritter.add({
                  title: 'Failed',
                  text: 'Something went wrong' ,
                  class_name: 'warning gritter-center',
                  time: ''
                });

              DashTtNewTimeTableInfo.html('<div class="alert alert-danger">'+data.error+'</div>');

            }
               
       },"json")
        .done(function() { })
        .fail(function() {

            $.gritter.add({
              title: 'Failed',
              text: 'Failed to generate timetable, Please Try Again',
              class_name: 'danger gritter-center',
              time: ''
            });
        })
        .always(function() {
          
          formDashTtNewTimeTable.show();
          formDashTtNewTimeTableLoading.hide();

        });

}

function getLibrary(){
	
	$("#moduleContainer").load("library/main", function() { get_mn_itemsCopies(); } ).fadeIn(1000);
}

function get_mn_itemsCopies()
{
	$("#moduleMain").load("library/mn_itemsCopies").fadeIn(1000);
}

function get_mn_books()
{
	$("#moduleMain").load("library/mn_books").fadeIn(1000);
}

function get_mn_libraryItems()
{
	$("#moduleMain").load("library/mn_libraryItems").fadeIn(1000);
}

function get_mn__borrow()
{
	$("#moduleMain").load("library/mn__borrow").fadeIn(1000);
}

function get_mn_overDue()
{
	$("#moduleMain").load("library/mn_overDue").fadeIn(1000);
}

function DashLiSelectAllLibraryItems()
{
	//
	  var container               		= $( "#dashLibrary_contentOthers" );
	  var dashLibrary_contentAnalytics  = $( "#dashLibrary_contentAnalytics" );

	  dashLibrary_contentAnalytics.hide();
	  container.show();

	  //
	  
	    var content = '';
			content+='<div class="row">';
				content+= '<div class="col-sm-12">';
					content+='<div class="row text-center">';
						content+= '<div class="col-sm-12">';
							content+= '<h4>School Library Items</h4>';
						content+= '</div>';
					content+= '</div>';
					content+= '<div class="row top-buffer">';
						content+= '<div class="row top-buffer" >';
							content+= '<div class="col-sm-10 col-sm-offset-1" id="liLibraryItemsTableContainer">';
							content+= '</div>';
						content+= '</div>';
					content+= '</div>';
				content+= '</div>';
			content+= '</div>';
		container.html( content );
	
	  //
	  var url = base_url+"api/Li/LibraryItems";

	  ItemsCopiesContainer = '';

	  $.getJSON( url, function(data) {

	  	ItemsCopiesContainer    +='<table class="table table-striped abDataTable" id="liLibraryItemsTable" ><thead><tr><th class="text-left">Title</th><th class="text-right">Copies</th><th class="text-right">Available</th><th class="text-right">Borrowed</th><th class="text-right">Lost</th><th class="text-right">Removed</th><th class="text-right">Damaged</th></tr></thead><tbody>';

          $.each(data, function( i, item ) {
          
            ItemsCopiesContainer +="<tr id='"+item.id+"' >";
            	ItemsCopiesContainer +="<td class='text-left'>"+item.title+"</td>";
            	ItemsCopiesContainer +="<td class='text-right'>"+item.Total+"</td>";
                ItemsCopiesContainer +="<td class='text-right'>"+item.TotalAvailable+"</td>";
                ItemsCopiesContainer +="<td class='text-right'>"+item.TotalBorrowed+"</td>";
                ItemsCopiesContainer +="<td class='text-right'>"+item.TotalLost+"</td>";
                ItemsCopiesContainer +="<td class='text-right'>"+item.TotalLost+"</td>";
                ItemsCopiesContainer +="<td class='text-right'>"+item.TotalDamaged+"</td>";
            ItemsCopiesContainer +="</tr>";

        });     
        
      ItemsCopiesContainer +="</tbody></table>";

      var liLibraryItemsTableContainer =  $("#liLibraryItemsTableContainer");
      liLibraryItemsTableContainer.html( ItemsCopiesContainer );

      abTablePagingTable("liLibraryItemsTable");

	  }).done(function() {
	        
	  }).fail(function() {
	  }).always(function() {
	  });
}

function DashAcSelectAllBorrows()
{
	  var container               		= $( "#dashLibrary_contentOthers" );
	  var dashLibrary_contentAnalytics  = $( "#dashLibrary_contentAnalytics" );

	  dashLibrary_contentAnalytics.hide();
	  container.show();

	  //
	    var content = '';
			content+='<div class="row">';
				content+= '<div class="col-sm-12">';
					content+='<div class="row text-center">';
						content+= '<h4>Borrow and return operations</h4>';
					content+= '</div>';
					content+= '<div class="row top-buffer">';
						content+= '<div class="row top-buffer" >';
							content+= '<div class="col-sm-10 col-sm-offset-1" id="liBorrowReturnItemsTableContainer">';
							content+= '</div>';
						content+= '</div>';
					content+= '</div>';
				content+= '</div>';
			content+= '</div>';

		container.html( content );
	
	  //
	  var url = base_url+"api/Li/Borrow";

	  ItemsCopiesContainer = '';

	  $.getJSON( url, function(data) {

	  	ItemsCopiesContainer    +='<table class="table table-striped abDataTable" id="liBorrowReturnTable" ><thead><tr><th class="text-left">Title</th><th class="text-center">Index Number</th><th class="text-center">Status</th><th class="text-left">Borrower</th><th class="text-center">Returned</th></tr></thead><tbody>';

          $.each(data, function( i, item ) {
          
            ItemsCopiesContainer +="<tr id='"+item.id+"' >";
            	ItemsCopiesContainer +="<td class='text-left'>"+item.title+"</td>";
            	ItemsCopiesContainer +="<td class='text-center'>"+item.indexNumber+"</td>";
            	ItemsCopiesContainer +="<td class='text-center'>"+LibraryGetStatus(item.statusID)+"</td>";
            	ItemsCopiesContainer +="<td class='text-left'>"+item.BorrowerNames+"</td>";
            	if (item.returnedDate)
            	 {
            	 	ItemsCopiesContainer +="<td class='text-center'>"+date_moment(item.returnedDate)+"</td>"; 


            	 }else{
            	 	ItemsCopiesContainer +="<td class='text-center'><span class='badge bg-red'> Not Returned</span></td>"; 
            	 }
            ItemsCopiesContainer +="</tr>";

        });     
        
      ItemsCopiesContainer +="</tbody></table>";

      var liBorrowReturnItemsTableContainer =  $("#liBorrowReturnItemsTableContainer");
      liBorrowReturnItemsTableContainer.html( ItemsCopiesContainer );

      abTablePagingTable("liBorrowReturnTable");

	  }).done(function() {
	        
	  }).fail(function() {
	  }).always(function() {
	  });
}

function validateLiNewLibraryItem(){

	var formLiNewLibraryItem = $('#formLiNewLibraryItem');

    formLiNewLibraryItem.formValidation({
      framework: 'bootstrap',
      excluded: [':disabled' , ':hidden' ],
      icon: {
          valid: 'glyphicon glyphicon-ok',
          invalid: 'glyphicon glyphicon-remove',
          validating: 'glyphicon glyphicon-refresh'
      },
      fields: {
        itemType: {
              validators: {
                notEmpty: {
                    message: 'Library item type is required'
                }
              }

        },
        name: {
              validators: {
                notEmpty: {
                    message: 'Name is required'
                }
              }

        },
        publisher: {
            validators: {
                notEmpty: {
                    message: 'Publisher is required'
                }
            }
        },
        language: {
            validators: {
                notEmpty: {
                    message: 'Language is required'
                }
            }
        },
        'tags[]': {
            validators: {
                notEmpty: {
                    message: 'Tag is required'
                }
            }
        },
        price: {
            validators: {
                notEmpty: {
                    message: 'Price is required'
                }
            }
        },
        numberOfCopies: {
            validators: {
                notEmpty: {
                    message: 'Number of copies is required'
                }
            }
        }

      }

    }).on('success.field.fv', function(e, data) {

          if (data.fv.getInvalidFields().length <= 0) {   

              data.fv.disableSubmitButtons(false);
          }

    })
    .on('success.form.fv', function( e ) {

        e.preventDefault();
        submitLiNewLibraryItem();

    });

}

function submitLiNewLibraryItem()
{	
	var LiNewItemType      		= $("#LiNewItemType");
	var LiNewItemName      		= $("#LiNewItemName");
	var LiNewItemEdition 		= $("#LiNewItemEdition");
	var LiNewItemAuthor 		= $("#LiNewItemAuthor");
	//isbnType
	var ISBN10 					= $("#ISBN10");
	var ISBN13 					= $("#ISBN13");
	var LiNewItemPublisher      = $("#LiNewItemPublisher");
	var newBook_selectDay     	= $("#newBook_selectDay");
	var newBook_selectMonth     = $("#newBook_selectMonth");
	var newBook_selectYear      = $("#newBook_selectYear");
	var LiSelectLanguage        = $("#LiSelectLanguage");
	var LiNewItemTags      		= $("#LiNewItemTags");
	var LiNewItemPrice      	= $("#LiNewItemPrice");
	var LiNewItemNumberOfCopies = $("#LiNewItemNumberOfCopies");

	var LiNewLibraryItemLoading = $('#LiNewLibraryItemLoading');
	var frm = $('#formLiNewLibraryItem');

	if ( LiNewItemType.select2('val') == 1 && !(   LiNewItemType.select2('val') > 0 
												&& LiNewItemName.val().length > 0 
												&& LiNewItemPublisher.val().length > 0 
												&& LiSelectLanguage.select2('val') > 0 
												&& LiNewItemTags.val().length > 0 
												&& LiNewItemPrice.val().length > 0 
												&& LiNewItemNumberOfCopies.val().length > 0 
												) 
		) {

		$.gritter.add({
                title: 'Please fill the required inputs with star(*) ',
                text: '',
                class_name: 'warning gritter-center',
                time: ''
              });

		frm.formValidation('revalidateField', 'itemType');
		frm.formValidation('revalidateField', 'name');
		frm.formValidation('revalidateField', 'publisher');
		frm.formValidation('revalidateField', 'language');
		frm.formValidation('revalidateField', 'tags[]');
		frm.formValidation('revalidateField', 'price');
		frm.formValidation('revalidateField', 'numberOfCopies');

	}else if( 
			!(
				LiNewItemType.select2('val') > 0 
				&&  LiNewItemName.val().length > 0 
				&& LiSelectLanguage.select2('val') > 0
				&& LiNewItemTags.val().length > 0 
				&& LiNewItemPrice.val().length > 0 
				&& LiNewItemNumberOfCopies.val().length > 0 
			)
	 ){

		frm.formValidation('revalidateField', 'itemType');
		frm.formValidation('revalidateField', 'name');
		frm.formValidation('revalidateField', 'language');
		frm.formValidation('revalidateField', 'tags[]');
		frm.formValidation('revalidateField', 'price');
		frm.formValidation('revalidateField', 'numberOfCopies');

	}else{

	  frm.hide();
	  LiNewLibraryItemLoading.slideDown();

      var url = base_url+"api/Li/LibraryItems"; 

      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
            if (data.success)
            {   
            	$('#Model_LiNewLibraryItem').modal('toggle');
                
                $.gritter.add({
					title: 'Success',
					text: 'Successfully saved.',
					class_name: 'success gritter-center',
					time: ''
				});
				
				LiNewItemType.select2('data', null);
				LiNewItemName.val('');
				LiNewItemEdition.val('');
				$('.LiNewItemIsbnType').prop('checked', false);
				ISBN10.val('');
				ISBN13.val('');
				LiNewItemPublisher.select2('data', null);
				newBook_selectDay.select2('data', null);
				newBook_selectMonth.select2('data', null);
				newBook_selectYear.select2('data', null);
				LiSelectLanguage.select2('data', null);
				LiNewItemTags.select2('data', null);
				LiNewItemPrice.val('0');
				LiNewItemNumberOfCopies.val('');

				$("#dashLiNewItemBookSection").hide();
				$('#newItemIsbnType10').hide();
				$('#newItemIsbnType13').hide();

				frm.formValidation('revalidateField', 'itemType');
				frm.formValidation('revalidateField', 'name');
				frm.formValidation('revalidateField', 'publisher');
				frm.formValidation('revalidateField', 'language');
				frm.formValidation('revalidateField', 'tags[]');
				frm.formValidation('revalidateField', 'price');
				frm.formValidation('revalidateField', 'numberOfCopies');

				loadLibraryHome();

            } else {

            	var errormessage = data.errormessage;

                $.gritter.add({
					title: 'Failed',
					text: 'Failed to save, '+errormessage ,
					class_name: 'danger gritter-center',
					time: ''
				});

			}
                 
         },"json")
          .done(function() {

          })
          .fail(function() {

              $.gritter.add({
                title: 'Failed',
                text: 'Failed to save, Please Try Again',
                class_name: 'danger gritter-center',
                time: ''
              });

          })
          .always(function() { 
          	
	 		LiNewLibraryItemLoading.hide();
	 		frm.slideDown();

          });

	}
 
}

function submitLiNewItemBorrow()
{	
	var liNewBorrowLoading      = $("#LiNewBorrowLoading");
	var selectnewBorrowStudent  = $('#selectnewBorrowStudent');
	var selectNewMewBorrowStaff = $('#selectNewMewBorrowStaff');
	var selectNewBorrowItems    = $('#selectNewBorrowItems');
	var selectNewBorrowItemCopy = $('#selectNewBorrowItemCopy');

	var frm = $('#formLiNewBorrow');

	if ( (selectnewBorrowStudent.select2('val') > 0 || selectNewMewBorrowStaff.select2('val') > 0 ) && selectNewBorrowItems.select2('val') > 0 && selectNewBorrowItemCopy.select2('val') > 0 ) {

		  frm.hide();
		  liNewBorrowLoading.slideDown();

	      var url = base_url+"api/Li/Borrow"; 

	      $.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){

	            if (data.success)
	            {   
	            	$('#Model_LiBorrowItem').modal('toggle');

	                $.gritter.add({
						title: 'Success',
						text: 'Successfully saved .',
						class_name: 'success gritter-center',
						time: ''
					});
					
					$("#newBorrowTypeStudent").attr('checked', 'checked');
					$('#selectnewBorrowStudent').select2('data', null);
			        $('#selectNewMewBorrowStaff').select2('data', null);
					$('#selectNewBorrowItems').select2('data', null);
					$('#selectNewBorrowItemCopy').select2('data', null);

					$('#NewBorrowItemNewIndexNumber').val('');
					$('#NewBorrowDueDate').val('');

					$('#NewBorrowItemCopyContainer').hide();
					$('#NewBorrowItemNewIndexNumberContainer').hide();

					loadLibraryHome();

	            } else {

	            	var errormessage = data.errormessage;

	                $.gritter.add({
						title: 'Failed',
						text: 'Failed to save, '+errormessage ,
						class_name: 'danger gritter-center',
						time: ''
					});

				}
	                 
	         },"json")
	          .done(function() {

	          })
	          .fail(function() {

	              $.gritter.add({
	                title: 'Failed',
	                text: 'Failed to save Student, Please Try Again',
	                class_name: 'danger gritter-center',
	                time: ''
	              });

	          })
	          .always(function() { 
	          	
		 		liNewBorrowLoading.hide();
		 		frm.slideDown();

	          });

	}else{

		frm.formValidation('revalidateField', 'student');
		frm.formValidation('revalidateField', 'staff');
		frm.formValidation('revalidateField', 'item');
		frm.formValidation('revalidateField', 'itemCopy');
		frm.formValidation('revalidateField', 'NewIndexNumber');
		frm.formValidation('revalidateField', 'returnDueDate');

	}

}

function submitLiNewCopies()
{
	$('#formLiNewItemCopies').on('submit', function(e){
		
	  var frm = $('#formLiNewItemCopies');
	  var url = base_url+"api/Li/ItemsCopies"; 
      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
           if (data.success)
                {   
                	$('#Model_LiNewLibraryItemCopy').modal('toggle');

                    $.gritter.add({
						title: 'Success',
						text: 'Successfully added .',
						class_name: 'success gritter-center',
						time: ''
					});
					
					var sel 			= $("#dashboardLiSelectLibraryItem");
					var selected 		= sel.select2('val');
					var selectedItem 	= sel.select2('data').text;
					var itemtype 		= sel.select2().find(":selected").data("itemtype");
					
					DashLiLoadCopiesOfSelected(selected ,itemtype, selectedItem);

                } else {

                    $.gritter.add({
						title: 'Failed',
						text: 'Failed to add copies.',
						class_name: 'danger gritter-center',
						time: ''
					});

                }
         });
    });

}

function LiReturnLibraryItem(id)
{

	var url = base_url+"api/Li/Borrow/return/"+id;

	$.getJSON( url, function(data) {
	 	
	 	if (data.success)
            {   

                $.gritter.add({
					title: 'Success',
					text: 'Successfully saved .',
					class_name: 'success gritter-center',
					time: ''
				});
				
				getDashboard();

            } else {

                $.gritter.add({
					title: 'Failed',
					text: 'Failed to save.',
					class_name: 'danger gritter-center',
					time: ''
				});

            }

	})
	.done(function() {

	})
	.fail(function() {
	})
	.always(function() {
	});

}

function DashSaAlocateClickedRoom()
{
	$( ".dashSaRoomAlocate" ).delegate("span", "click", function() {

	  	id 					 = $(this).closest('tr').attr('id');

		alert(id);

	});
}

function DashSaAlocateClickedDesk()
{
	$( ".dashSaDeskAlocate" ).delegate("span", "click", function() {

	  	id 					 = $(this).closest('tr').attr('id');
        alert(id);

	});
}

function validateLiNewBorrow()
{
    var formLiNewBorrow = $('#formLiNewBorrow');

    formLiNewBorrow
    	.find('[name="student"]').select2().change(function(e) {
                formLiNewBorrow.formValidation('revalidateField', 'student');
    	}).end().formValidation({
      
      framework: 'bootstrap',
      excluded: [':disabled' , ':hidden' ],
      icon: {
          valid: 'glyphicon glyphicon-ok',
          invalid: 'glyphicon glyphicon-remove',
          validating: 'glyphicon glyphicon-refresh'
      },
      fields: {
        student: {
              validators: {
                notEmpty: {
                    message: 'Student is required'
                }
              }

        },
        staff: {
              validators: {
                notEmpty: {
                    message: 'Staff is required'
                }
              }

        },
        item: {
              validators: {
                notEmpty: {
                    message: 'Library Item is required'
                }
              }

        },
        itemCopy: {
              validators: {
                notEmpty: {
                    message: 'ItemCopy is required'
                }

              }

        },
        NewIndexNumber: {
            validators: {
                stringLength: {
                    min: 10 ,
                    max: 10 ,
                    message: 'The printed index number should be 10 digits.' 
                }
            }
        },
        returnDueDate: {
            validators: {
                notEmpty: {
                    message: 'The Return Due Date is required' 
                },
                date: {
                    format: 'DD/MM/YYYY',
                    message: 'The date is not a valid (Required format is DD/MM/YYYY)'
                }
            }

        }

      }

    }).on('success.field.fv', function(e, data) {

          if (data.fv.getInvalidFields().length <= 0) {   

              data.fv.disableSubmitButtons(false);
          }

    })
    .on('success.form.fv', function( e ) {

        e.preventDefault();
        submitLiNewItemBorrow();

    });
}

function validateLiNewReturn()
{
    var formLiReturnItem = $('#formLiReturnItem');

    formLiReturnItem.formValidation({
      
      framework: 'bootstrap',
      excluded: [':disabled' , ':hidden' ],
      icon: {
          valid: 'glyphicon glyphicon-ok',
          invalid: 'glyphicon glyphicon-remove',
          validating: 'glyphicon glyphicon-refresh'
      },
      fields: {
        student: {
              validators: {
                notEmpty: {
                    message: 'Student is required'
                }
              }

        },
        staff: {
              validators: {
                notEmpty: {
                    message: 'Staff is required'
                }
              }

        },
         status: {
              validators: {
                notEmpty: {
                    message: 'Status is required'
                }
              }

        },
        'items[]': {
              validators: {
                notEmpty: {
                    message: 'Item is required'
                }
              }

        }

      }

    })
    .on('success.field.fv', function(e, data) {

          if (data.fv.getInvalidFields().length <= 0) {   

              data.fv.disableSubmitButtons(false);
          }

    })
    .on('success.form.fv', function( e ) {

        e.preventDefault();
        submitLiReturnItem();

    });
}

function submitLiReturnItem()
{

	var ReturnItemTypeStudent 		  = $('#ReturnItemTypeStudent');
	var selectNewReturnItemStudent 	  = $('#selectNewReturnItemStudent');
	var selectNewReturnItemStaff 	  = $('#selectNewReturnItemStaff');
	var selectReturnItemStatus 		  = $('#selectReturnItemStatus');
	var selectReturnItemBorrowedItems = $('#selectReturnItemBorrowedItems');

	var LiReturnLoading = $('#LiReturnLoading');
	var frm 			= $('#formLiReturnItem');

	if ( (selectNewReturnItemStudent.select2('val') > 0 || selectNewReturnItemStaff.select2('val') > 0 ) && selectReturnItemStatus.select2('val') > 0 && selectReturnItemBorrowedItems.select2('val').length > 0 ) {

		  frm.hide();
		  LiReturnLoading.slideDown();

	      var url = base_url+"api/Li/Borrow/return"; 

	      $.post(url, 
	         frm.serialize(), 
	         function(data, status, xhr){

	            if (data.success)
	            {   
	            	$('#Model_LiReturnItem').modal('toggle');

	                $.gritter.add({
						title: 'Success',
						text: 'Successfully saved.',
						class_name: 'success gritter-center',
						time: ''
					});
					
					ReturnItemTypeStudent.attr('checked', 'checked');
					selectNewReturnItemStudent.select2('data', null);
			        selectNewReturnItemStaff.select2('data', null);
					selectReturnItemStatus.val(1).trigger("change");
					selectReturnItemBorrowedItems.select2('data', null);

					$('#ReturnItemBorrowedItemsContainer').hide();

					loadLibraryHome();

	            } else {

	            	var errormessage = data.errormessage;

	                $.gritter.add({
						title: 'Failed',
						text: 'Failed to save, '+errormessage ,
						class_name: 'danger gritter-center',
						time: ''
					});

				}
	                 
	         },"json")
	          .done(function() {

	          })
	          .fail(function() {

	              $.gritter.add({
	                title: 'Failed',
	                text: 'Failed to save, Please Try Again',
	                class_name: 'danger gritter-center',
	                time: ''
	              });

	          })
	          .always(function() { 
	          	
		 		LiReturnLoading.hide();
		 		frm.slideDown();

	          });

	}else{

		frm.formValidation('revalidateField', 'student');
		frm.formValidation('revalidateField', 'staff');
		frm.formValidation('revalidateField', 'status');
		frm.formValidation('revalidateField', 'items[]');

	}
}


function validateLiNewLibraryItemImport()
{	
	var formLiImportFileLoading = $('#formLiImportFileLoading');
	var formLiImportErrorMessage= $('#formLiImportErrorMessage');
	var formLiImportFile 		= $('#formLiImportFile');

	formLiImportFileLoading.hide();
	formLiImportErrorMessage.empty();
	formLiImportFile.show();

    formLiImportFile.formValidation({
      
      framework: 'bootstrap',
      excluded: [':disabled' , ':hidden' ],
      icon: {
          valid: 'glyphicon glyphicon-ok',
          invalid: 'glyphicon glyphicon-remove',
          validating: 'glyphicon glyphicon-refresh'
      },
      fields: {
        LiImportFile: {
              validators: {
                notEmpty: {
                        message: 'Please select an image'
                },
                file: {
                    extension: 'xls',
                    type: 'application/vnd.ms-excel',
                    maxSize: 2097152,   // 2048 * 1024
                    message: 'The selected file is not valid, it should be in Excel format "<b>.xls</b>" '
                }

              }

        }

      }

    })
    .on('success.field.fv', function(e, data) {

          if (data.fv.getInvalidFields().length <= 0) {   
              data.fv.disableSubmitButtons(false);
          }

    })
    .on('success.form.fv', function( e ) {

        e.preventDefault();
        liNewLibraryItemImportSubmit();

    });
}

function liNewLibraryItemImportSubmit()
{	
	 var formLiImportFileLoading 	= $('#formLiImportFileLoading');
	 var formLiImportErrorMessage	= $('#formLiImportErrorMessage');
	 var formLiImportFile 			= $('#formLiImportFile');

	 formLiImportFile.hide();
	 formLiImportErrorMessage.empty();
	 formLiImportFileLoading.slideDown();

	 var url = base_url+"api/Li/ImportLibraryItems"; 

	$.ajax({
	    url: url ,
	    dataType: 'json',
	    type: 'POST',
	    data :  new FormData( formLiImportFile[0] ) ,
	    processData: false,
	    contentType: false,
	    success: function ( res ) {

	    	if (res.success)
	        {   
	        	$('#Model_ImportLibraryItem').modal('toggle');
	            
	            $.gritter.add({
					title: 'Success',
					text: 'Successfully saved.',
					class_name: 'success gritter-center',
					time: ''
				});
				
				var LiImportFile = $('#LiImportFile');
				LiImportFile.val('');

				loadLibraryHome();

	        } else {

	        	formLiImportErrorMessage.html('<div class="alert alert-danger"><strong>Details!</strong> '+res.Error_message+'</div>'); 

	        	$.gritter.add({
					title: 'Failed',
					text: 'Please check the details below' ,
					class_name: 'danger gritter-center',
					time: ''
				});

			}

	    }

	  }).done(function() {

	}).fail(function() {

		$.gritter.add({
	        title: 'Failed',
	        text: 'Failed to save, Please Try Again',
	        class_name: 'danger gritter-center',
	        time: ''
	      });

	}).always(function() {

		formLiImportFileLoading.hide();
	  	formLiImportFile.show();

	});

}

function getMessage(){
	
	$("#moduleContainer").load("messages/main", function() { get_mn__inbox(); } ).fadeIn(1000);
}

function get_mn__inbox()
{
	$("#moduleMain").load("messages/mn__inbox").fadeIn(1000);
}

function get_mn_sent()
{
	$("#moduleMain").load("messages/mn_sent").fadeIn(1000);
}

function get_mn_templates()
{
	$("#moduleMain").load("messages/mn_templates").fadeIn(1000);
}

function get_messages()
{
	$("#moduleMain").load("messages/messages").fadeIn(1000);
}

function get_newMessage()
{
	$("#moduleMain").load("messages/new_message").fadeIn(1000);
}

function getSentMessageAfterSubmit(threadID,Subject)
{
	$("#moduleContainer").load("messages/main", function() {

		$("#moduleMain").load("messages/mn_sent", function() {

			messageLoadMessages( threadID , 2 , Subject );

		});
	});
}

function submitMessageNewMessage()
{

	var formNewMessageLoading		= $('#formNewMessageLoading');
	var formNewMessage 				= $('#formNewMessage');

	formNewMessageLoading.hide();

	formNewMessage.on('submit', function(e){
			
		var frm = formNewMessage;
		var url = base_url+"api/Me/Messages"; 

		formNewMessage.hide();
		formNewMessageLoading.slideDown();

	    e.preventDefault();

	   	$.ajax({
	      url: base_url+"api/Me/Messages",
	      dataType: 'json',
	      type: 'POST',
	      data: frm.serialize(),
	      success: function ( res ) {

	        if ( res.success )
	        {
	        	$.gritter.add({
					title: 'Success',
					text: 'Messages successfuly sent.',
					class_name: 'success gritter-center',
					time: ''
				});

	            $('#moduleMain').load('messages/mn__inbox', function() {
	           		
	           		 $("#moduleMain").show();
	           		 messageLoadMessages( res.threadID , 1 , res.Subject );

	            });
	              
	        }else{  

	        	$.gritter.add({
					title: 'Failed',
					text: 'Failed to send message.',
					class_name: 'danger gritter-center',
					time: ''
				});

	        }

	      }

	    }).done(function() {


	    }).fail(function() {

	    	$.gritter.add({
					title: 'Failed',
					text: 'Failed to send message. check your internet and try again',
					class_name: 'danger gritter-center',
					time: ''
				});

	    	formNewMessage.slideDown();
			formNewMessageLoading.hide();

	    })
	    .always(function() {
	    
	    });
	    

	 //    var url2 	    = ''; 
	 //    var isFirst     = true;
	 //    var isLast  	= false;
	 //    var hasStarted  = false;

	 //    var TryCounter  = 0;

	 //    var MessageNumber ;
	 //    var ThreadId 	  ; 

	 //    var ThreadOwner	 	  = $('#threadOwner').val();
	 //    var IsLastThreadFound = $('#threadFound').val(); 
	 //    var LastThreadId 	  = $('#threadFoundId').val();

	 //    var progresspump = setInterval(function(){
						    
		//     url2 = base_url+"api/Me/Messages?first=true&ThreadOwner="+ThreadOwner+"&IsLastThreadFound="+IsLastThreadFound+"&LastThreadId="+LastThreadId;

		//     $.get(url2, function(data){

		//     	if( data.isLast )
		//     	{
		//     		isLast = true;
		//     	}

		//     	if ( data.hasStarted ) {

		//     		hasStarted = true;
		//     	}

		//     	if( !hasStarted ){

		//     		TryCounter++;
		//     		console.log("Trying "+TryCounter );

		//     		if (TryCounter == 10 )
		//     		 {
		//     		 	console.log("We have tried "+TryCounter+" times");
		//     		 	clearInterval(progresspump);
		//     		 }

		//     	}else if( data.isMessageReport ) {

		//     	 	clearInterval(progresspump);

		//     	 	console.log( " Last call " );
		//     	 	console.log( data.MessageReport );
		    	 	 
		//     	}else if(  isFirst && isLast ) {

		//     		MessageNumber  = data.messageNumber;
		//     	 	ThreadId 	   = data.threadId;

		//     	 	url2 = base_url+"api/Me/Messages?first=false&report=true&threadId="+ThreadId+"&messageNumber="+MessageNumber;

		//     	 	console.log(" we have one message and let get report ");

		//     	}else if ( isFirst ) {	

		//     	 	MessageNumber  = data.messageNumber;
		//     	 	ThreadId 	   = data.threadId;
		//     	 	hasStarted 	   = data.hasStarted;

		//     	 	if (hasStarted)
		//     	 	 {
		//     	 	 	isFirst = false;
		//     	 	 }

		//     	 	url2 = base_url+"api/Me/Messages?first=false&report=false&threadId="+ThreadId+"&messageNumber="+MessageNumber;

		//     	 	console.log(" The fist call and we continue ");

		//     	}else if( isLast ) {

		//     	 	 url2 = base_url+"api/Me/Messages?first=false&report=true&threadId="+ThreadId+"&messageNumber="+MessageNumber;

		//     	 	 console.log(" This last and let us get report ");

		//     	}else{

		//     	 	url2 = base_url+"api/Me/Messages?first=false&report=false&threadId="+ThreadId+"&messageNumber="+MessageNumber;

		//     	 	console.log(" This normal ... let get the number of sent messages and we continue ");
		//     	}

		//     })

		// }, 1000);

    });

}

function submitMessageNewTemplate()
{
	$('#formMessageNewTemplate').on('submit', function(e){
		
	  var frm = $('#formMessageNewTemplate');
	  var url = base_url+"api/Me/Templates"; 
      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
           if (data.success)
                {   
                	$('#Model_NewTemplate').modal('toggle');
                    $.gritter.add({
						title: 'Success',
						text: 'Template successfuly created.',
						class_name: 'success gritter-center',
						time: ''
					});
					get_mn_templates();
                } else {

                    $.gritter.add({
						title: 'Failed',
						text: 'Failed to add template .',
						class_name: 'danger gritter-center',
						time: ''
					});

                }
         });
    });
}

function onClickMessageTemplate()
{

	$( "table#messageTemplatesTable" ).delegate( "td", "click", function() {

	  	id 			= $(this).closest('tr').attr('id');
	  	subject 	= $(this).closest('tr').attr('subject');
	  	content 	= $(this).closest('tr').attr('content');

	  	var messageSelectedTemplate = $( "#messageSelectedTemplate" );
	  	messageSelectedTemplate.html('<div class="row text-center"><h4><strong>'+subject+'</strong></h4></div><div class="row lead"><p>'+content+'</p></div>');
	  	
	});

}

function onMeNewMessageTemplateSelected()
{

	var selectedOption  = $("#newMessageTemplateSelect");
	var selected      	= selectedOption.select2('val');
	var subject     	= selectedOption.select2('data').text;
	var templateContent = selectedOption.select2().find(":selected").data("templatecontent");

	$("#newMessageSubject").val(subject);
	$("#newMessageContent").val(templateContent);
	
}

function messageNewMessageSelect2()
{
	$("#newMessageTemplateSelect").select2().bind('change', onMeNewMessageTemplateSelected);

	$("#newMessageSelectStudent").select2();
	$("#newMessageSelectExceptStudent").select2();
	
	$("#newMessageSelectClassroom").select2({ 
		    formatResult: classroomformatResult, 
		    formatSelection: classroomformatSelection, 
		    escapeMarkup: function(m) { return m; } 
	});

	$("#newMessageSelectStaff").select2();
	
	$("#newMessageStaffSelectRoles").select2();
	$("#selectNewFeeClassroom").select2();

	var elementOrganization = $("#selectPaymentOrganization");
    var selectedOrganization = [];
    elementOrganization.find("option").each(function(i,e){
        selectedOrganization[selectedOrganization.length]=$(e).attr("value");
    });

    elementOrganization.select2().val(selectedOrganization).trigger('change');

    $("#selectAlumni").select2();

}

function messageNewMessageJS()
{
	   //hide
	    var staffTypeContainer 		= $('#staffTypeContainer');
	    var studentTypeContainer 	= $('#studentTypeContainer');
	    var alumniTypeContainer 	= $('#alumniTypeContainer');

	    $("#newMessageSelectTemplateDiv").hide();

	    staffTypeContainer.hide();
	    alumniTypeContainer.hide();

	    var customStudentContainer 		= $("#customStudentContainer");
	    var chooseClassesContainer 		= $("#chooseClassesContainer");
	    var studentSelectSomeContainer  = $("#studentSelectSomeContainer");
	    var staffSelectRoleContainer 	= $("#staffSelectRoleContainer");
	    var staffSelectSomeContainer 	= $("#staffSelectSomeContainer");
	    var staffSelectAlumnusContainer = $("#staffSelectAlumnusContainer"); 
	    var alumniSelectSomeContainer 	= $("#alumniSelectSomeContainer"); 
	    var selectOrganizationContainer	= $("#selectOrganizationContainer");
	    var studentSelectExceptContainer= $("#studentSelectExceptContainer");

	    customStudentContainer.hide();
	    chooseClassesContainer.hide();
	    studentSelectSomeContainer.hide();
	    staffSelectRoleContainer.hide();
	    staffSelectSomeContainer.hide();
	    staffSelectAlumnusContainer.hide();
	    alumniSelectSomeContainer.hide();
	    studentSelectExceptContainer.hide();

	    $('input[type=radio][name=receiverType]').change(function() {
	        
	        var selected = $(this).val();

	        studentSelectSomeContainer.hide();
		    customStudentContainer.hide();
			staffSelectRoleContainer.hide();
	    	staffSelectSomeContainer.hide();

	    	staffSelectAlumnusContainer.hide();
	    	alumniSelectSomeContainer.hide();
	    	studentSelectExceptContainer.hide();

	    	staffTypeContainer.hide();
	    	studentTypeContainer.hide();
	    	alumniTypeContainer.hide();

	    	$("input[name=studentType][value='1']").prop("checked",false);
	    	$("input[name=studentType][value='2']").prop("checked",false);
	    	$("input[name=studentType][value='3']").prop("checked",false);

	    	$("input[name=staffType][value='1']").prop("checked",true);
	    	$("input[name=alumniType][value='1']").prop("checked",true);

		    if ( selected == 1 ) {

		    	studentTypeContainer.slideDown();

		    }else if( selected == 2 ) {

		    	staffTypeContainer.slideDown();

		    }else if ( selected == 3 ) {

		    	alumniTypeContainer.slideDown();
		    }


	    });

	    $('input[type=radio][name=studentType]').change(function() {
	        
	        var selected = $(this).val();

	        customStudentContainer.hide();
	        chooseClassesContainer.hide();
			studentSelectSomeContainer.hide();
			studentSelectExceptContainer.hide();

		    if ( selected == 1 ) {
		    	
		    	studentSelectExceptContainer.slideDown();

		    }else if( selected == 2 ) {

		    	customStudentContainer.slideDown();
		    	studentSelectExceptContainer.slideDown();

		    }else if( selected == 3 ) {

		    	studentSelectSomeContainer.slideDown();
		    }

	    });

	    $('input[type=radio][name=staffType]').change(function() {
	        
	        var selected = $(this).val();

	        staffSelectRoleContainer.hide();
	        staffSelectSomeContainer.hide();

		    if ( selected == 1 ) {


		    }else if( selected == 2 ) {
				
				staffSelectRoleContainer.slideDown();

		    }else if( selected == 3 ) {

		    	staffSelectSomeContainer.slideDown();
		    }

	    });


	    $('input[type=radio][name=alumniType]').change(function() {
	        
	        var selected = $(this).val();

	        staffSelectAlumnusContainer.hide();
	        alumniSelectSomeContainer.hide();

		    if ( selected == 1 ) {

		    }else if( selected == 2 ) {
				
				staffSelectAlumnusContainer.slideDown();

		    }else if( selected == 3 ) {

		    	alumniSelectSomeContainer.slideDown();
		    }

	    });

	     $('input[type=radio][name=studentClass]').change(function() {
	        
	        var selected = $(this).val();

	        chooseClassesContainer.hide();

		    if ( selected == 0 ) {

		    	chooseClassesContainer.slideDown();

		    }else if( selected == 1 ) {
				
		    }

	    });

	    $('#studentSponsorOrg').change(function() {
	        
	        if ( $(this).is(":checked") )
	         {
	         	selectOrganizationContainer.slideDown();

	         }else{

	         	selectOrganizationContainer.hide();
	         }
	        
	    });

	    var users = [
		  {username: 'IncludeName', fullname: "To include name"}
		];

		$('#newMessageContent').suggest('@', {
		  data: users,
		  map: function(user) {
		    return {
		      value: user.username,
		      text: '<strong>'+user.username+'</strong> <small>'+user.fullname+'</small>'
		    }
		  }
		});

}

function messageInboxTreadSelected()
{
	$( "table#messageInboxTreadMessages" ).delegate( "td", "click", function() {

	  	id 					 = $(this).closest('tr').attr('id');
	  	msgsubject 			 = $(this).closest('tr').attr('msgsubject');

	  	messageLoadMessages( id , 1 , msgsubject);
	});
}

function messageSentTreadSelected()
{
	$( "table#messageInboxTreadMessages" ).delegate( "td", "click", function() {

	  	id 					 = $(this).closest('tr').attr('id');
	  	msgsubject 			 = $(this).closest('tr').attr('msgsubject');

	  	messageLoadMessages( id , 2 , msgsubject);

	});
}


function messageLoadMessages( id , type, msgsubject )
{

  var container     	= $( "#message_content" );
  var message_subject 	= $( "#message_subject" );
  var messagesReport 	= $( "#messagesReport");

  container.empty();
  messagesReport.empty();

  message_subject.html("<h4>"+msgsubject+"</h4>");

  if ( type == 1 )
   {

   	 var url = base_url+"api/Me/Inbox/"+id; 

   }else if(  type == 2 ){

   	 var url = base_url+"api/Me/Sent/"+id; 

   }

   if(! (typeof url === 'undefined') ){
	   
	   	$.getJSON( url, function(data) {

	   		var messages = data.Messages;
	   		var Report 	 = data.Report;

	   		var messagesReportContainer  = '';
	   		messagesReportContainer 	+= '<div class="col-sm-4" ><div class="grid widget"><div class="grid-body text-green"><span class="title">SENT</span><span class="value" id="messagesSent">'+Report.MsgSent+'</span></div></div></div>';
	   		messagesReportContainer 	+= '<div class="col-sm-4" ><div class="grid widget"><div class="grid-body text-red"><span class="title">FAILED</span><span class="value" id="messagesFailed">'+Report.MsgFailed+'</span><span class="title"><a href="#" id="MessageReSend">Re-Send</a></span></div></div></div>';

	   		if ( Report.MsgPending > 0 )
	   		 {
	   		 	messagesReportContainer += '<div class="col-sm-4" ><div class="grid widget"><div class="grid-body"><span class="title">PENDING</span><span class="value" id="messagesPending">'+Report.MsgPending+'</span><span class="title"><a href="#" id="MessageSendNow">Send now</a></span></div></div></div>';
	   		 }
	   		
	   		messagesReport.html(messagesReportContainer);

			$.each(messages, function(i, item) {

		    	var content = '<div class="chat-box-timeline"><img class="img-responsive img-circle avatar" src="personimage/50/'+item.personID+'" /><div class="message"><div class="panel panel-shadow panel-white"><div class="panel-body panel-arrow-left"><div class="chat-box-timeline-title"><strong>'+item.names+'</strong><div class="pull-right text-semi"><i class="fa fa-clock-o"></i> '+moment.unix(item.time).fromNow()+'</div><center><span>'+item.subject+'</span></center></div><div class="chat-box-timeline-content"><blockquote>'+item.content+'</blockquote><span class="pull-right">'+getMessageStatusSpan( item.messageStatus )+'</span></div></div></div></div></div>'
				container.append(content);

		    });
	    
	    })
	    .done(function() {

	    	container.animate({ scrollTop: container[0].scrollHeight}, "slow");

	    	$("#MessageReSend").click(function(){

		       	MsMessageReSend(id,type);

		    });

	    	$("#MessageSendNow").click(function(){

		      	MsMessageSendNow(id,type);

		    });

	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });

   };

}

function MsMessageReSend(ThreadId, MsgType)
{
	var messagesReport 		= $("#messagesReport");
	var resendMsgLoading 	= $("#resendMsgLoading");

	messagesReport.hide();
	resendMsgLoading.slideDown();

	var url = base_url+"api/Me/MessageReSend?Thread="+ThreadId; 

	$.getJSON( url, function(data) {

   		if (data.success)
        {   
			resendMsgLoading.hide();

			 $.gritter.add({
				title: 'Success',
				text: 'Messages successfuly sent.',
				class_name: 'success gritter-center',
				time: ''
			});

            $('#moduleMain').load('messages/mn__inbox', function() {
            	
           		 $("#moduleMain").show();
           		 messageLoadMessages( data.threadID , MsgType , data.Subject );

            });

        } else {

			resendMsgLoading.hide();
        	messagesReport.slideDown();

            $.gritter.add({
				title: 'Failed',
				text: 'Failed to re send message.',
				class_name: 'danger gritter-center',
				time: ''
			});

        }

    })
    .done(function() {

    })
    .fail(function() {
    })
    .always(function() {
    });

}

function MsMessageSendNow(ThreadId, MsgType)
{

	var messagesReport 		= $("#messagesReport");
	var resendMsgLoading 	= $("#resendMsgLoading");

	messagesReport.hide();
	resendMsgLoading.slideDown();

	var url = base_url+"api/Me/MessageSendNow?Thread="+ThreadId; 

	$.getJSON( url, function(data) {

   		if (data.success)
        {   
				resendMsgLoading.hide();

				 $.gritter.add({
					title: 'Success',
					text: 'Messages successfuly sent.',
					class_name: 'success gritter-center',
					time: ''
				});

	            $('#moduleMain').load('messages/mn__inbox', function() {

	           		$("#moduleMain").show();
	           		messageLoadMessages( data.threadID , MsgType , data.Subject );

	            });

	        } else {

	        	resendMsgLoading.hide();
        		messagesReport.slideDown();

	            $.gritter.add({
					title: 'Failed',
					text: 'Failed to send message.',
					class_name: 'danger gritter-center',
					time: ''
				});

	        }

    })
    .done(function() {
	

    })
    .fail(function() {
    })
    .always(function() {
    });

}

function getMessageStatusSpan( status )
{
	var content ='';

	switch(status) {
	    case "1":
	    	content = '<span class="badge"> Pending...</span>';
	        break;

	    case "2":
	       content = '<span class="badge bg-aqua"> Sending...</span>';
	        break;

        case "3":
        	content = '<span class="badge bg-blue "> Sent</span>';
	        break;

	    case "4":
        	content = '<span class="badge  bg-red "> Failed</span>';
	        break;

	    case "5":
	    	content = '<span class="badge  bg-yellow "> Received</span>';
	        break;

	    case "6":
        	content = '<span class="badge bg-green "> Seen</span>';
	        break;

	    case "7":
        	content = '<span class="badge bg-maroon "> Invalid Phone Number</span>';
	        break;
	}

	return content ;
}

function load_ac_Income_SchoolFees_Payments_Assigned(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

    var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
      content+='<div class="row">';
        content+= '<div class="col-sm-12">';
          content+='<div class="row text-center">';
            content+= '<h4>Assigned Payments</h4>';
          content+= '</div>';
          content+= '<div class="row">';
            content+= '<div class="col-sm-12">';
              content+='<div id="pa_Payments_Assigned"></div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div>';
    content+= '</div></div></div></div>';

    dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_PaAn_Payments_Assigned  = '';

  var  pa_Payments_Assigned           = $('#pa_Payments_Assigned');

    $.getJSON( url, function( data ) {
      
      Content_PaAn_Payments_Assigned += '<table class="table table-bordered abDataTable" id="pa_Payments_AssignedTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Fee</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">Source</th><th class="text-center">Date</th></tr></thead>';
      Content_PaAn_Payments_Assigned += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th colspan="3" style="text-align:left"></th></tr></tfoot>'; 
      Content_PaAn_Payments_Assigned += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_PaAn_Payments_Assigned +="<tr id="+item.id+">";
            Content_PaAn_Payments_Assigned +="<td >"+item.studentNames+" </td>";
            Content_PaAn_Payments_Assigned +="<td class='text-center' >"+item.studRegNber+" </td>";
            Content_PaAn_Payments_Assigned +="<td >"+item.className+" </td>";
            Content_PaAn_Payments_Assigned +="<td >"+item.feeName+" </td>";
            Content_PaAn_Payments_Assigned +="<td class='text-right'>"+formatNumber(item.amountPaid)+" </td>";

            if ( item.source == 1 )
             {
                Content_PaAn_Payments_Assigned +="<td class='text-center'>MobiCash</td>";
             }else{
                Content_PaAn_Payments_Assigned +="<td class='text-center'>Bank</td>";
             }
            Content_PaAn_Payments_Assigned +="<td class='text-center' >"+date_moment(item.created_at)+" </td>";
          Content_PaAn_Payments_Assigned +="</tr>";
      
      });

      Content_PaAn_Payments_Assigned +="</tbody></table>";

      pa_Payments_Assigned.html( Content_PaAn_Payments_Assigned );
      

    }).done(function() {

        $('#pa_Payments_AssignedTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 4 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {

    })
    .always(function() {

    });

}

function load_ac_Income_SchoolFees_Payments_Unassigned(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<h4>Unassigned Payments</h4>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Payments_Unassigned"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Unassigned";

  var Content_PaAn_Payments_Unassigned  = '';

  var  pa_Payments_Unassigned           = $('#pa_Payments_Unassigned');

    $.getJSON( url, function( data ) {

      
      Content_PaAn_Payments_Unassigned +='<table class="table table-bordered abDataTable" id="pa_Payments_UnAssignedTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">Source</th><th class="text-center">Date</th></tr></thead>';
      Content_PaAn_Payments_Unassigned += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="3" style="text-align:left"></th></tr></tfoot>'; 
      Content_PaAn_Payments_Unassigned +='<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_PaAn_Payments_Unassigned +="<tr id="+item.id+">";
            Content_PaAn_Payments_Unassigned +="<td >"+item.studentNames+" </td>";
            Content_PaAn_Payments_Unassigned +="<td class='text-center'>"+item.studRegNber+" </td>";
            Content_PaAn_Payments_Unassigned +="<td class='text-center'>"+item.className+" </td>";
            Content_PaAn_Payments_Unassigned +="<td class='text-right'>"+formatNumber(item.amount)+" </td>";

            if ( item.source == 1 )
             {
                Content_PaAn_Payments_Unassigned +="<td class='text-center'>MobiCash</td>";
             }else{
                Content_PaAn_Payments_Unassigned +="<td class='text-center'>Bank</td>";
             }
            Content_PaAn_Payments_Unassigned +="<td class='text-center' >"+date_moment(item.created_at)+" </td>";
          Content_PaAn_Payments_Unassigned +="</tr>";
      
      });

      Content_PaAn_Payments_Unassigned +="</tbody></table>";

      pa_Payments_Unassigned.html( Content_PaAn_Payments_Unassigned );
      
      $('#pa_Payments_UnAssignedTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );


    }).done(function() {

    })
    .fail(function() {
     })
    .always(function() { 
    });

}

function load_ac_Income_SchoolFees_Fees_CurrentInvoices(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Current Invoices (This Term)</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a onclick="get_new_schoolFee()"  data-target="#" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Invoice</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Fees_Current"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Fees_Current";

  var Content_Pa_Fees_Current  = '';
  var pa_Fees_Current          = $('#pa_Fees_Current');

    $.getJSON( url, function( data ) {

      Content_Pa_Fees_Current +='<table class="table table-bordered abDataTable" id="pa_Fees_CurrentTable" ><thead><tr ><th class="text-center">Fee</th><th class="text-center">Description</th><th class="text-center">Priority</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">Total To Pay<br>(FRW)</th><th class="text-center">Total Paid<br>(FRW)</th><th class="text-center">Total Remain<br>(FRW)</th><th class="text-center">View/Add/Remove</th></tr></thead>';
      Content_Pa_Fees_Current += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Fees_Current += '<tbody>';
      
      var CurrentFee_Fees     = data.CurrentFee_Fees ;
      var CurrentFee_Priority = data.CurrentFee_Priority ;

      $.each( CurrentFee_Fees, function(i, item) {

          var TotalToPay    = parseFloat(item.amount) * parseFloat(item.numberOfStudent);
          var Total_Remain  = TotalToPay - parseFloat(item.totalPaid);

          Content_Pa_Fees_Current +="<tr id="+item.id+" fee_name='"+item.feeName+"' fee_description='"+item.description+"' >";
            Content_Pa_Fees_Current +="<td >"+item.feeName+" </td>";
            Content_Pa_Fees_Current +="<td >"+item.description+" </td>";
            Content_Pa_Fees_Current +="<td class='text-center'><span style='width:100%' class='TermlyFeeXeditable' data-type='select2' data-pk='1' data-title='Update Priority' data-url='api/Pa/Fees_Current/"+item.id+"?Priority="+item.feePriority+" ' >"+item.feePriority+"</span></td>"; 
            Content_Pa_Fees_Current +="<td class='text-right'>"+formatNumber(item.amount)+" </td>"; 
            Content_Pa_Fees_Current +="<td class='text-right'>"+formatNumber(TotalToPay)+" </td>";
            Content_Pa_Fees_Current +="<td class='text-right'>"+formatNumber(item.totalPaid)+" </td>";
            Content_Pa_Fees_Current +="<td class='text-right'>"+formatNumber(Total_Remain)+" </td>";
            Content_Pa_Fees_Current +="<td class='text-center PaStudentListLoadStuent'><span class='text-green'>"+item.numberOfStudent+" Students</span></td>";
          Content_Pa_Fees_Current +="</tr>";
      
      });

      Content_Pa_Fees_Current +="</tbody></table>";
      pa_Fees_Current.html( Content_Pa_Fees_Current );
      
      $.fn.editable.defaults.ajaxOptions = {type: "PUT" };
      $.fn.editable.defaults.mode = 'inline';

      $('.TermlyFeeXeditable').editable({
          source:  CurrentFee_Priority ,
          select2: {
             width:200,
             multiple: false
          },
          success: function(response, newValue) {

            if ( response.success ) {

                load_ac_Income_SchoolFees_Fees_CurrentInvoices();

            }

            if(response.status == 'error') return response.msg;
        }
      });


    }).done(function() {

      $( "table#pa_Fees_CurrentTable" ).delegate( "td.PaStudentListLoadStuent", "click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          var id              = $(this).closest('tr').attr('id');
          var feeName         = $(this).closest('tr').attr('fee_name');
          var feeDescription  = $(this).closest('tr').attr('fee_description');

          loadFeeStudentList( id , feeName , feeDescription );

      });

      $('#pa_Fees_CurrentTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total To Pay  
              total_ToPaid = api
                  .column( 4 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-blue">'+formatNumber(total_ToPaid)+' FRW</span>'
              );


              // Total Paid  
                total_Paid = api
                    .column( 5)
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 5 ).footer() ).html(
                    '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
                );

               // Total Remain  
                total_Remain = api
                    .column( 6 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 6 ).footer() ).html(
                    '<span class="text-red">'+formatNumber(total_Remain)+' FRW</span>'
                );


          }
      } );

    })
    .fail(function() {

    })
    .always(function() {

    });

}

function load_ac_Income_SchoolFees_Fees_StudentsWithoutInvoice(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<h4>Students Without Invoices</h4>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Fees_StudentsWithoutFees"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Fees_StudentWithoutFee";

  var Content_Pa_Fees_StudentsWithoutFees  = '';
  var pa_Fees_StudentsWithoutFees          = $('#pa_Fees_StudentsWithoutFees');

    $.getJSON( url, function( data ) {

      Content_Pa_Fees_StudentsWithoutFees +='<table class="table table-bordered abDataTable" id="pa_Fees_StudentsWithoutFeesTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Sponsor</th></tr></thead><tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Fees_StudentsWithoutFees +="<tr id="+item.id+">";
            Content_Pa_Fees_StudentsWithoutFees +="<td >"+item.studentNames+" </td>";
            Content_Pa_Fees_StudentsWithoutFees +="<td class='text-center'>"+item.studRegNber+" </td>";
            Content_Pa_Fees_StudentsWithoutFees +="<td class='text-center'>"+item.className+" </td>";
            Content_Pa_Fees_StudentsWithoutFees +="<td >"+item.sponsor+" </td>";
          Content_Pa_Fees_StudentsWithoutFees +="</tr>";
      
      });

      Content_Pa_Fees_StudentsWithoutFees +="</tbody></table>";

      pa_Fees_StudentsWithoutFees.html( Content_Pa_Fees_StudentsWithoutFees );
      abTablePagingTable("pa_Fees_StudentsWithoutFeesTable");
      

    }).done(function() {

    })
    .fail(function() {

    })
    .always(function() {

    });
}


function load_ac_Income_SchoolFees_Fees_FeeTypes(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Fees Types</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Fee Type</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Fees_Types"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Fees_Types";

  var Content_Pa_Fees_Types   = '';
  var pa_Fees_Types           = $('#pa_Fees_Types');

    $.getJSON( url, function( data ) {

      Content_Pa_Fees_Types +='<table class="table table-bordered abDataTable" id="pa_Fees_TypesTable" ><thead><tr ><th class="text-center">Name</th><th class="text-center">Repetition</th></tr></thead><tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Fees_Types +="<tr id="+item.id+">";
            Content_Pa_Fees_Types +="<td >"+item.name+" </td>";
            Content_Pa_Fees_Types +="<td >"+item.feesRepetition+" </td>";
          Content_Pa_Fees_Types +="</tr>";
      
      });

      Content_Pa_Fees_Types +="</tbody></table>";

      pa_Fees_Types.html( Content_Pa_Fees_Types );
      abTablePagingTable("pa_Fees_TypesTable");
      

    }).done(function() {

    })
    .fail(function() {

    })
    .always(function() {

    });

}

function load_ac_Income_SchoolFees_Deposits_Cash(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<h4>Annual Bank Deposits</h4>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Deposits_OtherDeposits"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Deposits_Others";

  var Content_Pa_Deposits_Other  = '';

  var  pa_Deposits_OtherDeposits = $('#pa_Deposits_OtherDeposits');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Other += '<table class="table table-bordered abDataTable" id="pa_Deposits_OtherTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">Bank Slip Number</th><th class="text-center">Account</th><th class="text-center">Bank</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Other += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Other += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Other +="<tr id="+item.id+">";
            Content_Pa_Deposits_Other +="<td >"+item.studentNames+" </td>";
            Content_Pa_Deposits_Other +="<td class='text-center'>"+item.studRegNber+" </td>"; 
            Content_Pa_Deposits_Other +="<td class='text-center'>"+item.className+" </td>";
            Content_Pa_Deposits_Other +="<td class='text-right'>"+formatNumber(item.amount)+" </td>";
            Content_Pa_Deposits_Other +="<td class='text-center'>"+item.BankSlipNumber+" </td>";
            Content_Pa_Deposits_Other +="<td >"+item.bankAccountNumber+" </td>";
            Content_Pa_Deposits_Other +="<td >"+item.bankName+" </td>";
            Content_Pa_Deposits_Other +="<td class='text-center'>"+date_moment(item.created_at)+"</td>";
          Content_Pa_Deposits_Other +="</tr>";
      

      });

      Content_Pa_Deposits_Other +="</tbody></table>";

      pa_Deposits_OtherDeposits.html( Content_Pa_Deposits_Other );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
}

function load_ac_Income_SchoolFees_Deposits_Bank(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<h4>Annual Bank Deposits</h4>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Deposits_OtherDeposits"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Deposits_Others";

  var Content_Pa_Deposits_Other  = '';

  var  pa_Deposits_OtherDeposits = $('#pa_Deposits_OtherDeposits');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Other += '<table class="table table-bordered abDataTable" id="pa_Deposits_OtherTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">Bank Slip Number</th><th class="text-center">Account</th><th class="text-center">Bank</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Other += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Other += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Other +="<tr id="+item.id+">";
            Content_Pa_Deposits_Other +="<td >"+item.studentNames+" </td>";
            Content_Pa_Deposits_Other +="<td class='text-center'>"+item.studRegNber+" </td>"; 
            Content_Pa_Deposits_Other +="<td class='text-center'>"+item.className+" </td>";
            Content_Pa_Deposits_Other +="<td class='text-right'>"+formatNumber(item.amount)+" </td>";
            Content_Pa_Deposits_Other +="<td class='text-center'>"+item.BankSlipNumber+" </td>";
            Content_Pa_Deposits_Other +="<td >"+item.bankAccountNumber+" </td>";
            Content_Pa_Deposits_Other +="<td >"+item.bankName+" </td>";
            Content_Pa_Deposits_Other +="<td class='text-center'>"+date_moment(item.created_at)+"</td>";
          Content_Pa_Deposits_Other +="</tr>";
      

      });

      Content_Pa_Deposits_Other +="</tbody></table>";

      pa_Deposits_OtherDeposits.html( Content_Pa_Deposits_Other );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

function load_ac_Income_SchoolFees_Deposits_MobiCash(){
    
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<h4>Annual MobiCash Deposits</h4>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Deposits_MobiCashDeposits"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Deposits_MobiCash";

  var Content_Pa_Deposits_MobiCash  = '';

  var  pa_Deposits_MobiCashDeposits = $('#pa_Deposits_MobiCashDeposits');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_MobiCash +='<table class="table table-bordered abDataTable" id="pa_Deposits_MobiCashTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">Paid By<br>Name</th><th class="text-center">Paid By<br>Phone Number</th><th class="text-center">Reference Number</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_MobiCash += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="5" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_MobiCash +='<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_MobiCash +="<tr id="+item.id+">";
            Content_Pa_Deposits_MobiCash +="<td >"+item.studentNames+" </td>";
            Content_Pa_Deposits_MobiCash +="<td class='text-center'>"+item.studRegNber+" </td>";
            Content_Pa_Deposits_MobiCash +="<td class='text-center'>"+item.className+" </td>";
            Content_Pa_Deposits_MobiCash +="<td class='text-right'>"+formatNumber(item.amount)+" </td>";
            Content_Pa_Deposits_MobiCash +="<td class='text-left'>"+item.senderName+" </td>";
            Content_Pa_Deposits_MobiCash +="<td class='text-center'>"+item.senderPhoneNumber+" </td>";
            Content_Pa_Deposits_MobiCash +="<td class='text-center'>"+item.referenceNumber+" </td>";
            Content_Pa_Deposits_MobiCash +="<td class='text-center' >"+date_moment(item.created_at)+" </td>";
          Content_Pa_Deposits_MobiCash +="</tr>";
      
      });

      Content_Pa_Deposits_MobiCash +="</tbody></table>";

      pa_Deposits_MobiCashDeposits.html( Content_Pa_Deposits_MobiCash );
      

    }).done(function() {

      $('#pa_Deposits_MobiCashTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );


    })
    .fail(function() {
    })
    .always(function() {
    });

}

function load_ac_Income_SchoolFees_Debts_UnPaid(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

    var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
      content+='<div class="row">';
        content+= '<div class="col-sm-12">';
          content+='<div class="row text-center">';
            content+= '<div class="col-sm-8">';
              content+= '<h4>UnPaid Debts</h4>';
            content+= '</div>';
            content+= '<div class="col-sm-4">';
              content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Debt</a>';
            content+= '</div>';
          content+= '</div>';
          content+= '<div class="row">';
            content+= '<div class="col-sm-12">';
              content+='<div id="pa_UnPaid_Debts"></div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div>';
    content+= '</div></div></div></div>';

    dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_PaAn_UnPaid_Debts  = '';

  var  pa_UnPaid_Debts           = $('#pa_UnPaid_Debts');

    $.getJSON( url, function( data ) {
      
      Content_PaAn_UnPaid_Debts += '<table class="table table-bordered abDataTable" id="pa_UnPaid_DebtsTable" ><thead><tr><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Paid</th><th class="text-center">Remain<br>(FRW)</th><th class="text-center">Description</th><th class="text-center">Date</th></tr></thead>';
      Content_PaAn_UnPaid_Debts += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th colspan="3" style="text-align:left"></th></tr></tfoot>'; 
      Content_PaAn_UnPaid_Debts += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_PaAn_UnPaid_Debts +="<tr id="+item.id+">";
            Content_PaAn_UnPaid_Debts +="<td >"+item.studentNames+" </td>";
            Content_PaAn_UnPaid_Debts +="<td class='text-center' >"+item.studRegNber+" </td>";
            Content_PaAn_UnPaid_Debts +="<td >"+item.className+" </td>";
            Content_PaAn_UnPaid_Debts +="<td >"+item.feeName+" </td>";
            Content_PaAn_UnPaid_Debts +="<td class='text-right'>"+formatNumber(item.amountPaid)+" </td>";
            Content_PaAn_UnPaid_Debts +="<td class='text-center' >"+date_moment(item.created_at)+" </td>";
            Content_PaAn_UnPaid_Debts +="<td class='text-center' >"+date_moment(item.created_at)+" </td>";
          Content_PaAn_UnPaid_Debts +="</tr>";
      
      });

      Content_PaAn_UnPaid_Debts +="</tbody></table>";

      pa_UnPaid_Debts.html( Content_PaAn_UnPaid_Debts );
      

    }).done(function() {

        $('#pa_Payments_AssignedTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 4 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {

    })
    .always(function() {

    });

}

function load_ac_Income_SchoolFees_Debts_Paid(){
    
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

    var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
      content+='<div class="row">';
        content+= '<div class="col-sm-12">';
          content+='<div class="row text-center">';
            content+= '<h4>Paid Debts</h4>';
          content+= '</div>';
          content+= '<div class="row">';
            content+= '<div class="col-sm-12">';
              content+='<div id="pa_Paid_Debts"></div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div>';
    content+= '</div></div></div></div>';

    dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_PaAn_Paid_Debts  = '';

  var  pa_Paid_Debts           = $('#pa_Paid_Debts');

    $.getJSON( url, function( data ) {
      
      Content_PaAn_Paid_Debts += '<table class="table table-bordered abDataTable" id="pa_UnPaid_DebtsTable" ><thead><tr><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Paid</th><th class="text-center">Description</th><th class="text-center">Date</th></tr></thead>';
      Content_PaAn_Paid_Debts += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th colspan="3" style="text-align:left"></th></tr></tfoot>'; 
      Content_PaAn_Paid_Debts += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_PaAn_Paid_Debts +="<tr id="+item.id+">";
            Content_PaAn_Paid_Debts +="<td >"+item.studentNames+" </td>";
            Content_PaAn_Paid_Debts +="<td class='text-center' >"+item.studRegNber+" </td>";
            Content_PaAn_Paid_Debts +="<td >"+item.className+" </td>";
            Content_PaAn_Paid_Debts +="<td class='text-right'>"+formatNumber(item.amountPaid)+" </td>";
            Content_PaAn_Paid_Debts +="<td class='text-center' >"+date_moment(item.created_at)+" </td>";
            Content_PaAn_Paid_Debts +="<td class='text-center' >"+date_moment(item.created_at)+" </td>";
          Content_PaAn_Paid_Debts +="</tr>";
      
      });

      Content_PaAn_Paid_Debts +="</tbody></table>";

      pa_Paid_Debts.html( Content_PaAn_Paid_Debts );
      

    }).done(function() {

        $('#pa_Payments_AssignedTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 4 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {

    })
    .always(function() {

    });

}

function load_ac_Income_SchoolFees_Organization(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Organization Sponsor</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Organization</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_OrganizationSponsor"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/OrganizationSponsors";

  var Content_Pa_OrganizationSponsor   = '';
  var pa_OrganizationSponsor           = $('#pa_OrganizationSponsor');

    $.getJSON( url, function( data ) {

      Content_Pa_OrganizationSponsor +='<table class="table table-striped abDataTable" id="pa_OrganizationSponsorTable" ><thead><tr ><th class="text-center">Name</th><th class="text-center">Number Of Student</th><th class="text-center">Total To Pay</th><th class="text-center">Total Paid</th><th class="text-center">Total Remain</th></tr></thead><tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_OrganizationSponsor +="<tr id="+item.id+">";
            Content_Pa_OrganizationSponsor +="<td >"+item.CategoryName+" </td>";
            Content_Pa_OrganizationSponsor +="<td >"+item.CategoryName+" </td>";
            Content_Pa_OrganizationSponsor +="<td >"+item.CategoryName+" </td>";
            Content_Pa_OrganizationSponsor +="<td >"+item.CategoryName+" </td>";
            Content_Pa_OrganizationSponsor +="<td >"+item.CategoryName+" </td>";
          Content_Pa_OrganizationSponsor +="</tr>";
      
      });

      Content_Pa_OrganizationSponsor +="</tbody></table>";

      pa_OrganizationSponsor.html( Content_Pa_OrganizationSponsor );
      abTablePagingTable("pa_OrganizationSponsorTable");
      

    }).done(function() {

    })
    .fail(function() {

    })
    .always(function() {

    });


}

function load_ac_Income_SoldAssets(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

    var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
      content+='<div class="row">';
        content+= '<div class="col-sm-12">';
          content+='<div class="row text-center">';
            content+= '<h4>Income from sold fixed assets</h4>';
          content+= '</div>';
          content+= '<div class="row">';
            content+= '<div class="col-sm-12">';
              content+='<div id="pa_OtherSchoolIncome"></div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div>';
    content+= '</div></div></div></div>';

    dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_OtherSchoolIncome  = '';

  var  pa_OtherSchoolIncome           = $('#pa_OtherSchoolIncome');

    $.getJSON( url, function( data ) {
      
      Content_OtherSchoolIncome += '<table class="table table-bordered abDataTable" id="pa_OtherSchoolIncomeTable" ><thead><tr><th class="text-center">Date</th><th class="text-center">Category</th><th class="text-center">Name/Number</th><th class="text-center">Cost</th><th class="text-center">Accumalated<br>Depreciation</th><th class="text-center">Net</th><th class="text-center">Sold at</th><th class="text-center">Profit/Losss</th></tr></thead>';
      Content_OtherSchoolIncome += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th colspan="3" style="text-align:left"></th></tr></tfoot>'; 
      Content_OtherSchoolIncome += '<tbody>';
      
      $.each( data, function(i, item) {
          
          Content_OtherSchoolIncome +="<tr id="+item.id+">";
            Content_OtherSchoolIncome +="<td >"+item.id+"</td>";
            Content_OtherSchoolIncome +="<td >"+item.id+"</td>";
            Content_OtherSchoolIncome +="<td >"+item.id+"</td>";
            Content_OtherSchoolIncome +="<td >"+item.id+"</td>";
            Content_OtherSchoolIncome +="<td >"+item.id+"</td>";
            Content_OtherSchoolIncome +="<td >"+item.id+"</td>";
            Content_OtherSchoolIncome +="<td >"+item.id+"</td>";
            Content_OtherSchoolIncome +="<td >"+item.id+"</td>";
          Content_OtherSchoolIncome +="</tr>";
      
      });

      Content_OtherSchoolIncome +="</tbody></table>";
      pa_OtherSchoolIncome.html( Content_OtherSchoolIncome );
      

    }).done(function() {

        $('#pa_Payments_AssignedTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 4 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {

    })
    .always(function() {

    });

}

function load_ac_Income_OtherSchoolIncome(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

    var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
      content+='<div class="row">';
        content+= '<div class="col-sm-12">';
          content+='<div class="row text-center">';
            content+= '<h4>Other School Income</h4>';
          content+= '</div>';
          content+= '<div class="row">';
            content+= '<div class="col-sm-12">';
              content+='<div id="pa_OtherSchoolIncome"></div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div>';
    content+= '</div></div></div></div>';

    dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_OtherSchoolIncome  = '';

  var  pa_OtherSchoolIncome           = $('#pa_OtherSchoolIncome');

    $.getJSON( url, function( data ) {
      
      Content_OtherSchoolIncome += '<table class="table table-bordered abDataTable" id="pa_OtherSchoolIncomeTable" ><thead><tr><th class="text-center">Source</th><th class="text-center">Channel</th><th class="text-center">Account</th><th class="text-center">Bank Slip Number</th><th class="text-center">Bank Operation<br>Type</th><th class="text-center">Amount</th><th class="text-center">Description</th><th class="text-center">Date</th></tr></thead>';
      Content_OtherSchoolIncome += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th colspan="3" style="text-align:left"></th></tr></tfoot>'; 
      Content_OtherSchoolIncome += '<tbody>';
      
      $.each( data, function(i, item) {
          
          Content_OtherSchoolIncome +="<tr id="+item.id+">";
            Content_OtherSchoolIncome +="<td >"+item.studentNames+" </td>";
            Content_OtherSchoolIncome +="<td class='text-center' >"+item.studRegNber+" </td>";
            Content_OtherSchoolIncome +="<td >"+item.className+" </td>";
            Content_OtherSchoolIncome +="<td >"+item.feeName+" </td>";
            Content_OtherSchoolIncome +="<td >"+item.feeName+" </td>";
            Content_OtherSchoolIncome +="<td class='text-right'>"+formatNumber(item.amountPaid)+" </td>";
            Content_OtherSchoolIncome +="<td class='text-center' >"+date_moment(item.created_at)+" </td>";
            Content_OtherSchoolIncome +="<td class='text-center' >"+date_moment(item.created_at)+" </td>";
          Content_OtherSchoolIncome +="</tr>";
      
      });

      Content_OtherSchoolIncome +="</tbody></table>";

      pa_OtherSchoolIncome.html( Content_OtherSchoolIncome );
      

    }).done(function() {

        $('#pa_Payments_AssignedTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 4 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {

    })
    .always(function() {

    });

}

function load_acc_Expense_Recorded(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<h4>Recorded Expense</h4>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_RecordedExpense"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_RecordedExpense = $('#pa_Expense_RecordedExpense');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Type</th><th class="text-center">Description</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">From</th><th class="text-center">Using</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.id)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+" </td>";
          Content_Pa_Deposits_Others +="</tr>";

      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_RecordedExpense.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

function load_acc_Expense_Types(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Expense Type</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Expense Type</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Type</th><th class="text-center">Description</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">From</th><th class="text-center">Using</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.id)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

function load_acc_Assets_Current_Cash(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Cash</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= 'Starting Balance here';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Particulars</th><th class="text-center">Debit</th><th class="text-center">Credit</th><th class="text-center">Balance</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

function load_acc_Assets_Current_BankAccounts(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Bank Accounts</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Bank Account</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Account Number</th><th class="text-center">Bank</th><th class="text-center">Starting Balance</th><th class="text-center">Debit</th><th class="text-center">Credit</th><th class="text-center">Balance</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

function load_acc_Assets_Current_AccountReceivable(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Accounts Receivable</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Account Receivable</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Type</th><th class="text-center">Description</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">From</th><th class="text-center">Using</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.id)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

function load_acc_Assets_Fixed_All(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>All Fixed Asset</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Fixed Asset</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Type</th><th class="text-center">Name</th><th class="text-center">Cost<br>(FRW)</th><th class="text-center">Depreciation<br>Rate</th><th class="text-center">Accumalated<br>Depreciation</th><th class="text-center">Net</th><th class="text-center">Vendor</th><th class="text-center">Description</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );


          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

function load_acc_Liabilities_Current(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Current Liabilities</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Current Liability</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Type</th><th class="text-center">Description</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">From</th><th class="text-center">Using</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.id)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

function load_acc_Liabilities_Long_Term(){
    
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Long-Term Liabilities</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Long-Term Liability</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Type</th><th class="text-center">Description</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">From</th><th class="text-center">Using</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.id)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
}

function load_acc_Liabilities_Loans(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Loans</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Loan</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Type</th><th class="text-center">Description</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">From</th><th class="text-center">Using</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.id)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
}

function load_acc_Capital(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Capital/Equity</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Capital/Equity</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Type</th><th class="text-center">Description</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">From</th><th class="text-center">Using</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.id)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

function load_acc_Budget_Expense()
{

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Budget: Expense</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Budget Item</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Payments_Assigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Type</th><th class="text-center">Description</th><th class="text-center">Amount<br>(FRW)</th><th class="text-center">From</th><th class="text-center">Using</th><th class="text-center">Date</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.id+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.id)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.id+" </td>";
            Content_Pa_Deposits_Others +="<td >"+item.id+" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_Deposits_OtherTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total Paid 
              total_Paid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
}

function load_acc_Reports(){

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content   += '<div  class="col-sm-12">';
                content   += '<div  class="col-sm-4 ">';
                  content   +='<div class="row">';
                    content   +='<h4>Generating Report</h4>';
                  content   += '</div>';
                  content   +='<div class="row">';
                    content   += '<form  id="formDashGetSelectedProclamation" class="form-horizontal top-buffer" role="form" >';
                      content   += '<div class="form-group"><label class="col-sm-3 control-label">Type</label><div class="col-sm-7"><select name="GrProclamationType" id="selectGrProclamationType" class="form-control select2-input" ></select></div></div>';
                      content   += '<div class="form-group top-buffer"><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-primary btn-block" type="submit" id="GrProclamationSubmit" >Generate New Report</button></div></div>';
                    content   += '</form>';
                  content   += '</div>';
                content   += '</div>';
                content   += '<div class="col-sm-8" >';
                  content   += '<div class="row text-center"><h4>Generated Reports</h4></div>';
                  content   += '<div class="row text-center"></div>';
                content   += '</div>';
              content   += '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

      var reportOptions ='<option ></option>';
      reportOptions    +='<option value="1" >Income Stastement</option>';
      reportOptions    +='<option value="2" >Cash Flow Stastement</option>';
      reportOptions    +='<option value="3" >Balance Sheet</option>';
      reportOptions    +='<option value="4" >Organization Payment Request</option>';
      reportOptions    +='<option value="5" >MobiCash Payment</option>';
      reportOptions    +='<option value="6" >Accounts Receivable</option>';
      reportOptions    +='<option value="7" >Transaction Journal </option>';

      var selectGrProclamationType = $("#selectGrProclamationType");
      selectGrProclamationType.select2().bind('change', getPaReportTypeSelected );
      selectGrProclamationType.html( reportOptions );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();
}

function getPaReportTypeSelected()
{
    var selectGrProclamationType  = $("#selectGrProclamationType");
    var selected                  = selectGrProclamationType.select2('val');

    console.log("selected");
    console.log(selected);
}

function load_pa_Debts()
{
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Debts</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Debt</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Debts"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"api/Pa/Debts";

  var Content_Pa_Debts   = '';
  var pa_Debts           = $('#pa_Debts');

    $.getJSON( url, function( data ) {

      Content_Pa_Debts +='<table class="table table-bordered abDataTable" id="pa_DebtsTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Amount To Pay<br>(FRW)</th><th class="text-center">Paid<br>(FRW)</th><th class="text-center">Remain<br>(FRW)</th><th class="text-center">Description</th></tr></thead>';
      Content_Pa_Debts += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th><th ></th></tr></tfoot>';
      Content_Pa_Debts += '<tbody>';
      
      $.each( data, function(i, item) {
        
          AmountRemain  = parseFloat(item.amount) - parseFloat(item.amountPaid);

          Content_Pa_Debts +="<tr id="+item.id+">";
            Content_Pa_Debts +="<td >"+item.studentNames+" </td>";
            Content_Pa_Debts +="<td class='text-center' >"+item.studRegNber+" </td>";
            Content_Pa_Debts +="<td class='text-right'>"+formatNumber(item.amount)+" </td>";
            Content_Pa_Debts +="<td class='text-right'>"+formatNumber(item.amountPaid)+" </td>";
            Content_Pa_Debts +="<td class='text-right'>"+formatNumber(AmountRemain)+" </td>";
            Content_Pa_Debts +="<td >"+item.description+" </td>";
          Content_Pa_Debts +="</tr>";
      
      });

      Content_Pa_Debts +="</tbody></table>";

      pa_Debts.html( Content_Pa_Debts );
      

    }).done(function() {

      $('#pa_DebtsTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total To Pay  
              total_ToPaid = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 2 ).footer() ).html(
                  '<span class="text-blue">'+formatNumber(total_ToPaid)+' FRW</span>'
              );


              // Total Paid  
                total_Paid = api
                    .column( 3 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 3 ).footer() ).html(
                    '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
                );

               // Total Remain  
                total_Remain = api
                    .column( 4 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 4 ).footer() ).html(
                    '<span class="text-red">'+formatNumber(total_Remain)+' FRW</span>'
                );


          }
      } );

    })
    .fail(function() {

    })
    .always(function() {

    });
  
}


// function loadPaymentAnalyticsHome()
// {
//   var dashPayments_content      = $("#dashPayments_content");
//   var dashContentainer_Payments = $("#dashContentainer_Payments");

//   dashPayments_content.slideUp();

//       var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
//         content+='<div class="row">';
//           content+= '<div class="col-sm-6">';
//             content+='<div class="row text-center">';
//               content+= '<h4>Current Fees (This Term) Payments Status</h4>';
//             content+= '</div>';
//             content+= '<div class="row">';
//               content+= '<div class="col-sm-12">';
//                 content+='<div id="PaymentAnalyticsHomeFeesStatus"></div>';
//               content+= '</div>';
//             content+= '</div>';
//           content+= '</div>';
//           content+= '<div class="col-sm-6">';
//             content+='<div class="row text-center">';
//               content+= '<h4>Recent Deposits via MobiCash</h4>';
//             content+= '</div>';
//             content+= '<div class="row">';
//               content+= '<div class="col-sm-12">';
//                 content+='<div id="PaymentAnalyticsHomeMobiCashStatus"></div>';
//               content+= '</div>';
//             content+= '</div>';
//           content+= '</div>';
//         content+= '</div>';
//       content+= '</div></div></div></div>';

//       dashPayments_content.html( content );

//     dashContentainer_Payments.slideDown();
//     dashPayments_content.slideDown();

//     var url = base_url+"api/Pa/Home";

//     var PaAn_HomeFees       = '';
//     var PaAn_MobiCashStatus = '';

//     var  PaymentAnalyticsHomeFeesStatus     = $('#PaymentAnalyticsHomeFeesStatus');
//     var  PaymentAnalyticsHomeMobiCashStatus = $('#PaymentAnalyticsHomeMobiCashStatus');

//       $.getJSON( url, function( data ) {

//         var currentFeesPaymentsStatus = data.currentFeesPaymentsStatus ;
//         var recentMobiCashDeposit     = data.recentMobiCashDeposit ;

//         
//         PaAn_HomeFees +='<table class="table table-bordered abDataTable" id="paAnalyticsHomeFeesStatusTable" ><thead><tr ><th class="text-center">Fee</th><th class="text-center">Description</th><th class="text-center">Amount<br/>(In FRW)</th><th class="text-center">Total Paid<br/>(In FRW)</th><th class="text-center">Total Remain<br/>(In FRW)</th></tr></thead>';
//         PaAn_HomeFees += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th></tr></tfoot>'; 
//         PaAn_HomeFees +='<tbody>';
        
//         $.each( currentFeesPaymentsStatus, function(i, item) {

//           var FeeTotalToPay  = parseFloat(item.amount) * parseFloat(item.numberOfStudent);
//           var FeeTotalRemain = FeeTotalToPay - parseFloat(item.totalPaid);

//           PaAn_HomeFees +="<tr id="+item.id+">";
//             PaAn_HomeFees +="<td >"+item.feeName+" </td>";
//             PaAn_HomeFees +="<td class='text-left'>"+item.description+" </td>";
//             PaAn_HomeFees +="<td class='text-right'>"+formatNumber(item.amount)+"</td>";
//             PaAn_HomeFees +="<td class='text-right'>"+formatNumber(item.totalPaid)+"</td>";
//             PaAn_HomeFees +="<td class='text-right'>"+formatNumber(FeeTotalRemain)+"</td>";
//           PaAn_HomeFees +="</tr>";

//         });

//         PaAn_HomeFees +="</tbody></table>";

//         PaymentAnalyticsHomeFeesStatus.html(PaAn_HomeFees);
        
//         PaAn_MobiCashStatus +='<table class="table table-bordered abDataTable" id="paAnalyticsMobiCashStatusTable" ><thead><tr ><th class="text-center">Date&Time</th><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Amount<br/>(In FRW)</th></tr></thead><tbody>';
        
//         $.each( recentMobiCashDeposit, function(i, item) {

//             PaAn_MobiCashStatus +="<tr id="+item.id+">";
//               PaAn_MobiCashStatus +="<td >"+date_HM_moment(item.created_at)+"</td>";
//               PaAn_MobiCashStatus +="<td >"+item.studentNames+"</td>";
//               PaAn_MobiCashStatus +="<td class='text-center'>"+item.studRegNber+"</td>";
//               PaAn_MobiCashStatus +="<td >"+item.className+"</td>";
//               PaAn_MobiCashStatus +="<td class='text-right'>"+formatNumber(item.amount)+"</td>";
//             PaAn_MobiCashStatus +="</tr>";

//         });

//         PaAn_MobiCashStatus +="</tbody></table>";

//         PaymentAnalyticsHomeMobiCashStatus.html( PaAn_MobiCashStatus );
//         abTablePagingTable("paAnalyticsMobiCashStatusTable");

//       }).done(function() {

//           $('#paAnalyticsHomeFeesStatusTable').DataTable( {
//           "footerCallback": function ( row, data, start, end, display ) {
//               var api = this.api(), data;
   
//               // Remove the formatting to get integer data for summation
//               var intVal = function ( i ) {
//                   return typeof i === 'string' ?
//                       i.replace(/[\$,]/g, '')*1 :
//                       typeof i === 'number' ?
//                           i : 0;
//               };

//               // Total Paid 
//               total_Paid = api
//                   .column( 3 )
//                   .data()
//                   .reduce( function (a, b) {
//                       return intVal(a) + intVal(b);
//                   }, 0 );
                  
//                // Update footer
//               $( api.column( 3 ).footer() ).html(
//                   '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
//               );

//               // Total Remain 
//               total_Remain = api
//                   .column( 4 )
//                   .data()
//                   .reduce( function (a, b) {
//                       return intVal(a) + intVal(b);
//                   }, 0 );
                  
//                // Update footer
//               $( api.column( 4 ).footer() ).html(
//                   '<span class="text-red">'+formatNumber(total_Remain)+' FRW</span>'
//               );

//           }
//       } );

//       })
//       .fail(function() {
//       })
//       .always(function() {
//       });

// }

// function onDashPaNewDebt()
// {
//   $('#formDashPaNewDebt').formValidation({
      
//         framework: 'bootstrap',
//         excluded: [':disabled'],
//         icon: {
//             valid: 'glyphicon glyphicon-ok',
//             invalid: 'glyphicon glyphicon-remove',
//             validating: 'glyphicon glyphicon-refresh'
//         },
//         fields: {
//           student: {
//                 validators: {
//                     notEmpty: {
//                         message: 'Student is required'
//                     }
//                 }
//             },
//           amount: {
//                 validators: {
//                     notEmpty: {
//                         message: 'Amount is required'
//                     },
//                     integer: {
//                         message: 'The amount should be number'
//                     }

//                 }
//             },
//             description : {
//                 validators: {
//                     notEmpty: {
//                         message: 'BankAccount is required'
//                     }
//                 }
//             }
//         }

//       })
//       .on('success.field.fv', function(e, data) {

//             if (data.fv.getInvalidFields().length <= 0) {   

//                 data.fv.disableSubmitButtons(false);
//             }

//       })
//       .on('success.form.fv', function( e ) {

//           e.preventDefault();
//           submitPaNewStudentDebt();

//       });
// }

// function submitPaNewStudentDebt(){

//    //Hide Your 
//     $('#Model_DashPaNewDebt').modal('toggle');

//     var formDashPaNewDebt  = $("#formDashPaNewDebt");

//     $.ajax({
//       url: base_url+"api/Pa/Debts",
//       dataType: 'json',
//       type: 'POST',
//       data: formDashPaNewDebt.serialize(),
//       success: function ( res ) {

//         if ( res.success )
//         {

//               $.gritter.add({
//                   title: 'Success',
//                   text: 'Debt added.',
//                   class_name: 'success gritter-center',
//                   time: ''
//               });

//               var paNewDebtStudent     = $("#paNewDebtStudent");
//               paNewDebtStudent.select2('data', null);

//               var paNewDebtAmount      = $("#paNewDebtAmount");
//               paNewDebtAmount.val("");

//               var paNewDebtDescription = $("#paNewDebtDescription");
//               paNewDebtDescription.val("");

//               formDashPaNewDebt.formValidation('revalidateField', 'student');
//               formDashPaNewDebt.formValidation('revalidateField', 'amount');
//               formDashPaNewDebt.formValidation('revalidateField', 'description');

//               load_pa_Debts();
              
//         }else{  

//               $.gritter.add({
//                   title: 'Failed',
//                   text: 'Failed to add Debt.',
//                   class_name: 'danger gritter-center',
//                   time: ''
//               });

//               $('#Model_DashPaNewDebt').modal('show');

//         }

//       }

//     }).done(function() {


//     }).fail(function() {

//        $.gritter.add({
//                   title: 'Failed',
//                   text: 'Failed to add Debt.Please try again',
//                   class_name: 'danger gritter-center',
//                   time: ''
//               });

//        $('#Model_DashPaNewDebt').modal('show');

//     })
//     .always(function() {
    
//     });

// }

// function onDashPaNewTransaction()
// {   

//     $('input[type=radio][name=paNewTrans_Type]').change(function() {
          
//           var selected = $(this).val();

//           console.log("selected");
//           console.log(selected);

//         //   studentSelectSomeContainer.hide();
//         // customStudentContainer.hide();
//         // staffSelectRoleContainer.hide();
//         //   staffSelectSomeContainer.hide();

//         //   staffSelectAlumnusContainer.hide();
//         //   alumniSelectSomeContainer.hide();
//         //   studentSelectExceptContainer.hide();

//         //   staffTypeContainer.hide();
//         //   studentTypeContainer.hide();
//         //   alumniTypeContainer.hide();

//         //   $("input[name=studentType][value='1']").prop("checked",false);
//         //   $("input[name=studentType][value='2']").prop("checked",false);
//         //   $("input[name=studentType][value='3']").prop("checked",false);

//         //   $("input[name=staffType][value='1']").prop("checked",true);
//         //   $("input[name=alumniType][value='1']").prop("checked",true);

//         //   if ( selected == 1 ) {

//         //     studentTypeContainer.slideDown();

//         //   }else if( selected == 2 ) {

//         //     staffTypeContainer.slideDown();

//         //   }else if ( selected == 3 ) {

//         //     alumniTypeContainer.slideDown();
//         //   }


//         });

//      //    $("#paNewPaymentStudent").select2();
//      //    $("#paNewPaymentBankAccount").select2();
        
//      // $('#formDashPaNewPayment').formValidation({
      
//      //    framework: 'bootstrap',
//      //    excluded: [':disabled'],
//      //    icon: {
//      //        valid: 'glyphicon glyphicon-ok',
//      //        invalid: 'glyphicon glyphicon-remove',
//      //        validating: 'glyphicon glyphicon-refresh'
//      //    },
//      //    fields: {
//      //      student: {
//      //            validators: {
//      //                notEmpty: {
//      //                    message: 'Student is required'
//      //                }
//      //            }
//      //        },
//      //      amount: {
//      //            validators: {
//      //                notEmpty: {
//      //                    message: 'Amount is required'
//      //                },
//      //                integer: {
//      //                    message: 'The amount should be number'
//      //                }

//      //            }
//      //        },
//      //        BankAccount : {
//      //            validators: {
//      //                notEmpty: {
//      //                    message: 'BankAccount is required'
//      //                }
//      //            }
//      //        }
//      //    }

//      //  })
//      //  .on('success.field.fv', function(e, data) {

//      //        if (data.fv.getInvalidFields().length <= 0) {   

//      //            data.fv.disableSubmitButtons(false);
//      //        }

//      //  })
//      //  .on('success.form.fv', function( e ) {

//      //      e.preventDefault();
//      //      submitPaNewTransaction();

//      //  });

// }

// function submitPaNewTransaction()
// {
//     //Hide Your 
//     $('#Model_DashPaNewPayment').modal('toggle');

//     //

//     var formDashPaNewPayment  = $("#formDashPaNewPayment");

//     $.ajax({
//       url: base_url+"api/Pa/Deposits_Others",
//       dataType: 'json',
//       type: 'POST',
//       data: formDashPaNewPayment.serialize(),
//       success: function ( res ) {

//         if ( res.success )
//         {

//               $.gritter.add({
//                   title: 'Success',
//                   text: 'payments added.',
//                   class_name: 'success gritter-center',
//                   time: ''
//               });

//               var paNewPaymentStudent     = $("#paNewPaymentStudent");
//               var paNewPaymentAmount      = $("#paNewPaymentAmount");
//               var paNewPaymentBankAccount = $("#paNewPaymentBankAccount");
//               var paNewPaSubmit           = $("#paNewPaSubmit");

//               paNewPaymentAmount.val("");
//               paNewPaymentStudent.select2('val', '');
//               paNewPaymentBankAccount.select2('val', '');
              
//               formDashPaNewPayment.formValidation('revalidateField', 'student');
//               formDashPaNewPayment.formValidation('revalidateField', 'amount');


//         }else{

//               $.gritter.add({
//                   title: 'Failed',
//                   text: 'Failed to add payments.',
//                   class_name: 'danger gritter-center',
//                   time: ''
//               });

//               $('#Model_DashPaNewPayment').modal('show');

//         }

//       }

//     }).done(function() {


//     }).fail(function() {

//        $.gritter.add({
//                   title: 'Failed',
//                   text: 'Failed to add payments.Please try again',
//                   class_name: 'danger gritter-center',
//                   time: ''
//               });
//       $('#Model_DashPaNewPayment').modal('show');

//     })
//     .always(function() {
    
//     });

// }

// function onPaViewStudentFeePayments()
// {

//     var paStudentsFeeStudent = $("#paStudentsFeeStudent");
//     var selected             = paStudentsFeeStudent.select2('val');
//     var studentNameRegNumber = paStudentsFeeStudent.select2('data').text;
//     var regnumber            = paStudentsFeeStudent.select2().find(":selected").data("regnumber");
//     var photoid              = paStudentsFeeStudent.select2().find(":selected").data("photoid");

//     DashPaLoadStudentFees( selected, studentNameRegNumber, regnumber, photoid );

// }

// function DashPaLoadStudentFees( selected, studentNameRegNumber, regnumber, photoid )
// {

//     var dashPayments_content      = $("#dashPayments_content");
//     var dashContentainer_Payments = $("#dashContentainer_Payments");

//     dashPayments_content.slideUp();

//     var TotalAmount       = 0;
//     var AmountRemain      = 0;
//     var TotalAmountPaid   = 0;
//     var TotalAmountRemain = 0;


//     var studentPhotoContainer                 = '';

//     var studentPaStContainer_CurrentInvoice   = '';
//     var studentPaStContainer_Debts            = '';
//     var studentPaStContainer_Assigned         = '';
//     var studentPaStContainer_Unassigned       = '';
//     var studentPaStContainer_MobiCash         = '';
//     var studentPaStContainer_Bank             = '';

//     var StudentRemainAmountInput     = '';
//     var StudentRemainAmountValidator = [[]];

//     var currency ;
//     var dueDate  ;

//     var url = base_url+"api/Pa/Students/"+regnumber; 
    
//     var ST_className            =  "" ;
//     var ST_isBoarded            =  "" ;
//     var ST_sponsorshipType      =  "" ;
//     var ST_sponsorshipTypeName  =  "" ;
//     var ST_organization         =  "" ;

//     $.getJSON( url, function(data) {

//       var studentTermEnrollment = data.studentTermEnrollment ;
//       var termlyFees            = data.termlyFees;
//       var debtsOtherTerms       = data.debtsOtherTerms;
//       var debts                 = data.debts;
//       var AssignedPayments      = data.AssignedPayments ;
//       var UnAssignedPayments    = data.UnAssignedPayments ;
//       var MobiCashDeposits      = data.MobiCashDeposits ;
//       var OtherDeposits         = data.OtherDeposits ;

//       ST_className            =  studentTermEnrollment.className ;
//       ST_isBoarded            =  studentTermEnrollment.isBoarded ;
//       ST_sponsorshipType      =  studentTermEnrollment.sponsorshipType ;
//       ST_sponsorshipTypeName  =  studentTermEnrollment.sponsorshipTypeName ;
//       ST_organization         =  studentTermEnrollment.organization ;

//         studentPaStContainer_CurrentInvoice += '<div class="col-md-12"><table class="table table-bordered" id="pa_student_CurrentFee" ><thead><tr><th class="text-center">Fee</th><th class="text-center">Description</th><th class="text-center">Paid Amount<br>(in FRW)</th><th class="text-center">Remain Amount<br>(in FRW)</th><th class="text-center">Due Date</th></tr></thead>';
//         studentPaStContainer_CurrentInvoice += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th style="text-align:right"></th><th style="text-align:right"></th><th></th></tr></tfoot>';  
//         studentPaStContainer_CurrentInvoice += '<tbody>';

//         $.each( termlyFees, function(i, item) {

//             AmountRemain  = parseFloat(item.amount) - parseFloat(item.paidAmount);

//             studentPaStContainer_CurrentInvoice  +='<tr>';
//               studentPaStContainer_CurrentInvoice  +='<td>'+item.feeName+'</strong></td>';
//               studentPaStContainer_CurrentInvoice  +='<td>'+item.description+'</td>';
//               studentPaStContainer_CurrentInvoice  +='<td class="text-right">'+formatNumber(item.paidAmount)+'</td>';
//               studentPaStContainer_CurrentInvoice  +='<td class="text-right">'+formatNumber(AmountRemain)+'</td>';
//               studentPaStContainer_CurrentInvoice  +='<td class="text-right">'+date_moment(item.dueDate)+'</td>';
//             studentPaStContainer_CurrentInvoice  +='</tr>';

//         });

//         studentPaStContainer_CurrentInvoice  +='</tbody></table></div>';

//         studentPaStContainer_Debts += '<div class="col-sm-12"><table class="table table-bordered" id="pa_student_Debts"><thead><tr><th class="text-center">Period</th><th class="text-center">Fee</th><th class="text-center">Description</th><th class="text-center">Remain<br>(in FRW)</th></tr></thead>';
//         studentPaStContainer_Debts += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th style="text-align:right"></th></tr></tfoot>'; 
//         studentPaStContainer_Debts += '<tbody>';

//         $.each( debts, function(i, item) {

//             AmountRemain  = parseFloat(item.amount) - parseFloat(item.paidAmount);

//             studentPaStContainer_Debts  +='<tr>';
//               studentPaStContainer_Debts  +='<td>'+item.period+'</td>';
//               studentPaStContainer_Debts  +='<td>'+item.feeName+'</td>';
//               studentPaStContainer_Debts  +='<td>'+item.description+'</td>';   
//               studentPaStContainer_Debts  +='<td  class="text-right">'+formatNumber(AmountRemain)+'</td>';           
//             studentPaStContainer_Debts  +='</tr>';

//         });

//         $.each( debtsOtherTerms, function(i, item) {

//             AmountRemain  = parseFloat(item.amount) - parseFloat(item.paidAmount);

//             studentPaStContainer_Debts  +='<tr>';
//               studentPaStContainer_Debts  +='<td>'+item.period+'</td>';
//               studentPaStContainer_Debts  +='<td>'+item.feeName+'</td>';
//               studentPaStContainer_Debts  +='<td>'+item.description+'</td>';   
//               studentPaStContainer_Debts  +='<td  class="text-right">'+formatNumber(AmountRemain)+'</td>';           
//             studentPaStContainer_Debts  +='</tr>';

//         });
//         studentPaStContainer_Debts  +='</tbody></table></div>';

//         studentPaStContainer_Assigned += '<div class="col-md-12"><table class="table table-bordered" id="pa_student_AssignedPayments" ><thead><tr><th class="text-center">Fee</th><th class="text-center">Amount<br>(in FRW)</th><th class="text-center">Channel<br>(Deposit)</th><th class="text-center">Date</th></tr></thead>';
//         studentPaStContainer_Assigned += '<tfoot><tr><th colspan="1" style="text-align:right">Total:</th><th colspan="3" style="text-align:left"></th></tr></tfoot>';  
//         studentPaStContainer_Assigned += '<tbody>';

//         $.each( AssignedPayments, function(i, item) {

//             studentPaStContainer_Assigned  +='<tr>';
//               studentPaStContainer_Assigned  +='<td>'+item.feeName+'</strong></td>';
//               studentPaStContainer_Assigned  +='<td class="text-right">'+formatNumber(item.amountPaid)+'</td>';

//               if ( item.source ) {
//                 studentPaStContainer_Assigned  +='<td class="text-center">MobiCash</td>';

//               }else{
//                 studentPaStContainer_Assigned  +='<td class="text-center">Bank</td>';
//               }
      
//               studentPaStContainer_Assigned  +='<td class="text-center">'+date_moment(item.created_at)+'</td>';
//             studentPaStContainer_Assigned  +='</tr>';

//         });

//         studentPaStContainer_Assigned  +='</tbody></table></div>';

//         studentPaStContainer_Unassigned += '<div class="col-md-12"><table class="table table-bordered" id="pa_student_UnAssignedPayments" ><thead><tr><th class="text-center">Date</th><th class="text-center">Amount<br>(in FRW)</th><th class="text-center">Channel<br>(Deposit)</th></tr></thead>';
//         studentPaStContainer_Unassigned += '<tfoot><tr><th colspan="1" style="text-align:right">Total:</th><th colspan="2" style="text-align:left"></th></tr></tfoot>';  
//         studentPaStContainer_Unassigned += '<tbody>';

//         $.each( UnAssignedPayments, function(i, item) {

//             studentPaStContainer_Unassigned  +='<tr>';
//               studentPaStContainer_Unassigned  +='<td class="text-center">'+date_moment(item.created_at)+'</td>';
//               studentPaStContainer_Unassigned  +='<td class="text-right">'+formatNumber(item.amount)+'</td>';

//               if ( item.source ) {
//                 studentPaStContainer_Unassigned  +='<td class="text-center">MobiCash</td>';

//               }else{
//                 studentPaStContainer_Unassigned  +='<td class="text-center">Bank</td>';
//               }

//             studentPaStContainer_Unassigned  +='</tr>';

//         });

//         studentPaStContainer_Unassigned  +='</tbody></table></div>';

//         studentPaStContainer_MobiCash += '<div class="col-md-12"><table class="table table-bordered" id="pa_student_MobiCash" ><thead><tr><th class="text-center">Date</th><th class="text-center">Amount<br>(in FRW)</th><th class="text-center">Sender Name</th><th class="text-center">Sender Phone Number</th><th class="text-center">Reference Number</th></tr></thead>';
//         studentPaStContainer_MobiCash += '<tfoot><tr><th colspan="1" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';  
//         studentPaStContainer_MobiCash += '<tbody>';

//         $.each( MobiCashDeposits, function(i, item) {

//             studentPaStContainer_MobiCash  +='<tr>';
//               studentPaStContainer_MobiCash  +='<td class="text-center">'+date_moment(item.created_at)+'</td>';
//               studentPaStContainer_MobiCash  +='<td class="text-right">'+formatNumber(item.amount)+'</td>';
//               studentPaStContainer_MobiCash  +='<td class="text-left">'+item.senderName+'</td>';
//               studentPaStContainer_MobiCash  +='<td class="text-center">'+item.senderPhoneNumber+'</td>';
//               studentPaStContainer_MobiCash  +='<td class="text-center">'+item.referenceNumber+'</td>';
//             studentPaStContainer_MobiCash  +='</tr>';

//         });

//         studentPaStContainer_MobiCash  +='</tbody></table></div>';

//         studentPaStContainer_Bank += '<div class="col-md-12"><table class="table table-bordered" id="pa_student_DepositBank" ><thead><tr><th class="text-center">Date</th><th class="text-center">Amount<br>(in FRW)</th><th class="text-center">Bank Slip</th><th class="text-center">Bank Account</th><th class="text-center">Bank Name</th></tr></thead>';
//         studentPaStContainer_Bank += '<tfoot><tr><th colspan="1" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';  
//         studentPaStContainer_Bank += '<tbody>';

//         $.each( OtherDeposits, function(i, item) {

//             studentPaStContainer_Bank  +='<tr>';
//               studentPaStContainer_Bank  +='<td class="text-center">'+date_moment(item.created_at)+'</td>';
//               studentPaStContainer_Bank  +='<td class="text-right">'+formatNumber(item.amount)+'</td>';
//               studentPaStContainer_Bank  +='<td class="text-left">'+item.BankSlipNumber+'</td>';
//               studentPaStContainer_Bank  +='<td class="text-center">'+item.bankAccountNumber+'</td>';
//               studentPaStContainer_Bank  +='<td class="text-center">'+item.bankName+'</td>';
//             studentPaStContainer_Bank  +='</tr>';

//         });

//         studentPaStContainer_Bank  +='</tbody></table></div>';

//     })
//     .done(function() {

//       studentPhotoContainer = "<div class='col-sm-12'><div class='row top-buffer '><center><img src='images/student/100/"+selected+"/"+photoid+"' class='img-circle thumbnail' alt=''></center></div><div class='row text-center'><strong>"+studentNameRegNumber+"</strong></div>";
//         studentPhotoContainer +="<div class='row text-center'><div class='text-right col-sm-6'>Class:</div><div class='col-sm-6'><span class='label label-default'>"+ST_className+"</span></div></div>";
        
//         console.log("ST_isBoarded");
//         console.log(ST_isBoarded);

//         if ( ST_isBoarded == 1 ) {
//           studentPhotoContainer +="<div class='row text-center'><div class='text-right col-sm-6'>Boarding Status:</div><div class='col-sm-6'><span class='label label-success'>Boarding</span></div></div>";
//         }else{
//           studentPhotoContainer +="<div class='row text-center'><div class='text-right col-sm-6'>Boarding Status:</div><div class='col-sm-6'><span class='label label-success'>Day</span></div></div>";
//         }

//         if ( ST_sponsorshipType == "2" ) {
//           studentPhotoContainer +="<div class='row text-center'><div class='text-right col-sm-6'>Sponsor:</div><div class='col-sm-6'><span class='label label-primary'>"+ST_organization+"</span></div></div>";

//         }else{
//            studentPhotoContainer +="<div class='row text-center'><div class='text-right col-sm-6'>Sponsor:</div><div class='col-sm-6'><span class='label label-primary'>"+ST_sponsorshipTypeName+"</span></div></div>";
//         }
//       studentPhotoContainer += "</div>";

//       var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
//         content+='<div class="row">';
//           content+= '<div class="col-sm-12">';
//             content+='<div class="row text-center">';
//               content+= '<h4>Student School Fee Payment Status</h4>';
//             content+= '</div>';
//             content+= '<div class="row">';
//               content+= '<div class="col-sm-12">';
//                 content+= '<div class="col-sm-3">';
//                   content+= studentPhotoContainer;
//                 content+= '</div>';
//                 content+= '<div class="col-sm-9">';
//                   content+= '<ul class="nav nav-tabs">';
//                     content+= '<li><a href="#Pa_Student_CurrentInvoice"      data-toggle="tab">Current Invoices</a></li>';
//                     content+= '<li><a href="#pa_Student_Debts"               data-toggle="tab">Debts</a></li>';
//                     content+= '<li><a href="#Pa_Student_AssignedPayments"    data-toggle="tab">Assigned Payments</a></li>';
//                     content+= '<li><a href="#Pa_Student_UnassignedPayments"  data-toggle="tab">Unassigned Payments</a></li>';
//                     content+= '<li><a href="#Pa_Student_Deposits_MobiCash"   data-toggle="tab">MobiCash Deposits</a></li>';
//                     content+= '<li><a href="#Pa_Student_Deposits_Other"      data-toggle="tab">Bank Deposits</a></li>';
//                   content+= '</ul>';
//                   content+= '<div class="tab-content top-buffer">';
//                     content+= '<div class="tab-pane active" id="Pa_Student_CurrentInvoice">';
//                       content+= '<div class="row text-center"><h4><strong>Current Invoices</strong></h4></div>';  
//                       content+= '<div class="row">';  
//                         content+= '<div class="col-md-10 col-md-offset-1">';
//                           content+= '<div class="row">';
//                             content+= studentPaStContainer_CurrentInvoice;
//                           content+= '</div>';
//                         content+= '</div>';
//                       content+= '</div>';
//                       content+= '<br/>';
//                     content+= '</div>';

//                     content+= '<div class="tab-pane" id="pa_Student_Debts">';
//                       content+= '<div class="row text-center"><h4><strong>Debts</strong></h4></div>';
//                       content+= '<div class="row">';
//                         content+= '<div class="col-md-10 col-md-offset-1">';
//                           content+= '<div class="row">';
//                             content+= studentPaStContainer_Debts;
//                           content+= '</div>';
//                         content+= '</div>';
//                       content+= '</div>';
//                       content+= '<br/>';
//                     content+= '</div>';

//                     content+= '<div class="tab-pane" id="Pa_Student_AssignedPayments">';
//                       content+= '<div class="row text-center"><h4><strong>Assigned Payments</strong></h4></div>';
//                       content+= '<div class="row">';
//                         content+= '<div class="col-md-10 col-md-offset-1">';
//                           content+= '<div class="row">';
//                             content+= studentPaStContainer_Assigned;
//                           content+= '</div>';
//                         content+= '</div>';
//                       content+= '</div>';
//                       content+= '<br/>';
//                     content+= '</div>';

//                     content+= '<div class="tab-pane" id="Pa_Student_UnassignedPayments">';
//                       content+= '<div class="row text-center"><h4><strong>Unassigned Payments</strong></h4></div>';
//                       content+= '<div class="row">';
//                         content+= '<div class="col-md-10 col-md-offset-1">';
//                           content+= '<div class="row">';
//                             content+= studentPaStContainer_Unassigned;
//                           content+= '</div>';
//                         content+= '</div>';
//                       content+= '</div>';
//                       content+= '<br/>';
//                     content+= '</div>';

//                     content+= '<div class="tab-pane" id="Pa_Student_Deposits_MobiCash">';
//                       content+= '<div class="row text-center"><h4><strong>MobiCash Deposits</strong></h4></div>';  
//                       content+= '<div class="row">';  
//                         content+= '<div class="col-md-10 col-md-offset-1">';
//                           content+= '<div class="row">';
//                             content+= studentPaStContainer_MobiCash;
//                           content+= '</div>';
//                         content+= '</div>';
//                       content+= '</div>';
//                       content+= '<br/>';
//                     content+= '</div>';

//                     content+= '<div class="tab-pane" id="Pa_Student_Deposits_Other">';
//                       content+= '<div class="row text-center"><h4><strong>Bank Deposits</strong></h4></div>';  
//                       content+= '<div class="row">';  
//                         content+= '<div class="col-md-10 col-md-offset-1">';
//                           content+= '<div class="row">';
//                             content+= studentPaStContainer_Bank;
//                           content+= '</div>';
//                         content+= '</div>';
//                       content+= '</div>';
//                       content+= '<br/>';
//                     content+= '</div>';

//                   content+= '</div>';
//                 content+= '</div>';

//               content+= '</div>';
//             content+= '</div>';
//           content+= '</div>';
//         content+= '</div>';
//       content+= '</div></div></div></div>';

//     dashPayments_content.html( content );

//     dashContentainer_Payments.slideDown();
//     dashPayments_content.slideDown();

//     $('.nav-tabs a[href="#Pa_Student_CurrentInvoice"]').tab('show');

//     $('#pa_student_CurrentFee').DataTable( {
//         "footerCallback": function ( row, data, start, end, display ) {
//             var api = this.api(), data;
 
//             // Remove the formatting to get integer data for summation
//             var intVal = function ( i ) {
//                 return typeof i === 'string' ?
//                     i.replace(/[\$,]/g, '')*1 :
//                     typeof i === 'number' ?
//                         i : 0;
//             };
 
//             // Total Paid
//             total_Paid = api
//                 .column( 2 )
//                 .data()
//                 .reduce( function (a, b) {
//                     return intVal(a) + intVal(b);
//                 }, 0 );

//             // Total Remain 
//             total_Remain = api
//                 .column( 3 )
//                 .data()
//                 .reduce( function (a, b) {
//                     return intVal(a) + intVal(b);
//                 }, 0 );
 
//             // Update footer
//             $( api.column( 2 ).footer() ).html(
//                 '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
//             );

//              // Update footer
//             $( api.column( 3 ).footer() ).html(
//                 '<span class="text-red">'+formatNumber(total_Remain)+' FRW</span>'
//             );

//         }
//     } );

//     $('#pa_student_Debts').DataTable( {
//         "footerCallback": function ( row, data, start, end, display ) {
//             var api = this.api(), data;
 
//             // Remove the formatting to get integer data for summation
//             var intVal = function ( i ) {
//                 return typeof i === 'string' ?
//                     i.replace(/[\$,]/g, '')*1 :
//                     typeof i === 'number' ?
//                         i : 0;
//             };

//             // Total Debts 
//             total_Debts = api
//                 .column( 3 )
//                 .data()
//                 .reduce( function (a, b) {
//                     return intVal(a) + intVal(b);
//                 }, 0 );
                
//              // Update footer
//             $( api.column( 3 ).footer() ).html(
//                 '<span class="text-red">'+formatNumber(total_Debts)+' FRW</span>'
//             );

//         }
//     } );

//     $('#pa_student_AssignedPayments').DataTable( {
//         "footerCallback": function ( row, data, start, end, display ) {
//             var api = this.api(), data;
 
//             // Remove the formatting to get integer data for summation
//             var intVal = function ( i ) {
//                 return typeof i === 'string' ?
//                     i.replace(/[\$,]/g, '')*1 :
//                     typeof i === 'number' ?
//                         i : 0;
//             };

//             // Total Paid 
//             total_Paid = api
//                 .column( 1 )
//                 .data()
//                 .reduce( function (a, b) {
//                     return intVal(a) + intVal(b);
//                 }, 0 );
                
//              // Update footer
//             $( api.column( 1 ).footer() ).html(
//                 '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
//             );

//         }
//     } );

//     $('#pa_student_UnAssignedPayments').DataTable( {
//         "footerCallback": function ( row, data, start, end, display ) {
//             var api = this.api(), data;
 
//             // Remove the formatting to get integer data for summation
//             var intVal = function ( i ) {
//                 return typeof i === 'string' ?
//                     i.replace(/[\$,]/g, '')*1 :
//                     typeof i === 'number' ?
//                         i : 0;
//             };

//             // Total Paid 
//             total_Paid = api
//                 .column( 1 )
//                 .data()
//                 .reduce( function (a, b) {
//                     return intVal(a) + intVal(b);
//                 }, 0 );
                
//              // Update footer
//             $( api.column( 1 ).footer() ).html(
//                 '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
//             );

//         }
//     } );

//     $('#pa_student_MobiCash').DataTable( {
//         "footerCallback": function ( row, data, start, end, display ) {
//             var api = this.api(), data;
 
//             // Remove the formatting to get integer data for summation
//             var intVal = function ( i ) {
//                 return typeof i === 'string' ?
//                     i.replace(/[\$,]/g, '')*1 :
//                     typeof i === 'number' ?
//                         i : 0;
//             };

//             // Total Paid 
//             total_Paid = api
//                 .column( 1 )
//                 .data()
//                 .reduce( function (a, b) {
//                     return intVal(a) + intVal(b);
//                 }, 0 );
                
//              // Update footer
//             $( api.column( 1 ).footer() ).html(
//                 '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
//             );

//         }
//     } );

//     $('#pa_student_DepositBank').DataTable( {
//         "footerCallback": function ( row, data, start, end, display ) {
//             var api = this.api(), data;
 
//             // Remove the formatting to get integer data for summation
//             var intVal = function ( i ) {
//                 return typeof i === 'string' ?
//                     i.replace(/[\$,]/g, '')*1 :
//                     typeof i === 'number' ?
//                         i : 0;
//             };

//             // Total Paid 
//             total_Paid = api
//                 .column( 1 )
//                 .data()
//                 .reduce( function (a, b) {
//                     return intVal(a) + intVal(b);
//                 }, 0 );
                
//              // Update footer
//             $( api.column( 1 ).footer() ).html(
//                 '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
//             );

//         }
//     } );

    


//     })
//     .fail(function() {
//     })
//     .always(function() {
//     });

// }

function loadFeeStudentList( td_id , td_feeName , td_feeDiscription )
  { 

    console.log("loadFeeStudentList in function");

    var Model_PaFeeStudentList  = $('#Model_PaFeeStudentList');
    var paStudentListFeeName    = $('#paStudentListFeeName');

    paStudentListFeeName.html(td_feeName+" ("+td_feeDiscription+") ");

    var formTermlyFeeID      = $('#formTermlyFeeID');
    formTermlyFeeID.val(td_id);

    var PaStudentListStudentContainer   = $('#PaStudentListStudentContainer');
    var PaStudentListStudentLoading     = $('#PaStudentListStudentLoading');
    var PaStudentListStudentLoadingText = $('#PaStudentListStudentLoadingText');
    var PaStudentListList               = $('#PaStudentListList');

    PaStudentListStudentContainer.hide();
    PaStudentListStudentLoading.slideDown();
    PaStudentListList.hide();
    PaStudentListStudentLoadingText.html("Loading list...");

    Model_PaFeeStudentList.modal('show');

    var Content  = "";

    var url = base_url+"api/Pa/Fees_Current/"+td_id+"/edit";


      $.getJSON( url, function(data) {

          var TermlyStudentInvoice = data.TermlyStudentInvoice ;
          var OtherStudents        = data.OtherStudents ;

          var subjectOptions ='<option ></option>';

          $.each( OtherStudents , function(i, item) {

              subjectOptions +='<option value="'+item.studRegNber+'" >' +item.studentNames+ ' ( ' +item.studRegNber+ ' )</option>';
          });

          var PaStudentListAddStudent = $('#PaStudentListAddStudent');
          PaStudentListAddStudent.html( subjectOptions );
          PaStudentListAddStudent.select2();

          Content   = "";
          Content  += '<table class="table table-hover PaStudentListListTable" id="PaStudentListListTable" ><thead><tr><th class="text-center">Name</th><th class="text-center">Class</th><th class="text-center">Remove</th></tr></thead><tbody>';

          $.each( TermlyStudentInvoice , function(i, item) {

              Content +="<tr id='"+item.id+"' studregnber='"+item.studRegNber+"' >";
                Content +="<td>"+item.studentNames+"</td>";
                Content +="<td class='text-center'>"+item.className+"</td>";
                Content +='<td class="text-center PaStudentListListDelete"><a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a></td>';
              Content +="</tr>";

          });

          Content +='</tbody></table>';


      }).done(function() {  

          PaStudentListList.html( Content );

          PaStudentListStudentContainer.slideDown();
          PaStudentListStudentLoading.hide();
          PaStudentListList.slideDown();

          abTablePagingTable('PaStudentListListTable');

          $( "table#PaStudentListListTable" ).delegate( "td.PaStudentListListDelete", "click", function(e) {

                e.preventDefault();
                e.stopPropagation();

                var studRegNber  = $(this).closest('tr').attr('studRegNber');

                PaDeleteStudentList( $(this) , td_id,  studRegNber , td_id , td_feeName , td_feeDiscription );

           });


          submitPaAddStudentList( td_id , td_feeName , td_feeDiscription );

      }).fail(function() {
      })
      .always(function() {
      });

  }


//   function PaDeleteStudentList( td, termly_id, studRegNber , td_id , td_feeName , td_feeDiscription ){

//     var url     = base_url+"api/Pa/Fees_Current/"+termly_id+"?studRegNber="+studRegNber ;
//     td.html("Removing...");

//         $.ajax({
//               url: url,
//               type: 'DELETE',
//               success: function(data) {
              
//               console.log( data );

//                  if ( data.success )
//                  {
//                       $.gritter.add({
//                         title: 'Success',
//                         text: 'Student removed on fee list.',
//                         class_name: 'success gritter-center',
//                         time: ''
//                       });

//                       console.log("loadFeeStudentList in PaDeleteStudentList");
//                       loadFeeStudentList( td_id , td_feeName , td_feeDiscription );

//                  }else{

//                    $.gritter.add({
//                       title: 'Failed',
//                       text: 'Failed to remove student on fee list , '+ data.error,
//                       class_name: 'danger gritter-center',
//                       time: ''
//                     });

//                    td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');
//                }

//           }

//       });

//   }

//   function submitPaAddStudentList( td_id , td_feeName , td_feeDiscription )
//   {

//       console.log("submitPaAddStudentList called");

//       var PaStudentListStudentContainer   = $('#PaStudentListStudentContainer');
//       var PaStudentListStudentLoading     = $('#PaStudentListStudentLoading');
//       var PaStudentListStudentLoadingText = $('#PaStudentListStudentLoadingText');

//       var formPaStudentListAddStudent      =  $('#formPaStudentListAddStudent');

//       formPaStudentListAddStudent.unbind();
//       formPaStudentListAddStudent.on('submit', function(e){

//       PaStudentListStudentContainer.hide();
//       PaStudentListStudentLoading.slideDown();
//       PaStudentListStudentLoadingText.html("Adding students on fee list");

//       var frm = formPaStudentListAddStudent;
//       var url = base_url+"api/Pa/Students"; 
//       e.preventDefault();

//       $.post(url, 
//          frm.serialize(), 
//            function(data, status, xhr){

//                 console.log();

//                 if ( data.success )
//                 {   
//                   $.gritter.add({
//                       title: 'Success',
//                       text: 'Students added on fee list.',
//                       class_name: 'success gritter-center',
//                       time: ''
//                   });

//                   $('#dashboardAcSelectExcludeStudentStudent').select2('data', null);
//                   loadFeeStudentList( td_id , td_feeName , td_feeDiscription );

//                 } else {
                    
//                     $.gritter.add({
//                       title: 'Failed',
//                       text: 'Something went wrong' ,
//                       class_name: 'warning gritter-center',
//                       time: ''
//                     });

//                 }
                   
//            },"json")
//             .done(function() { 

//             })
//             .fail(function() {

//                 $.gritter.add({
//                   title: 'Failed',
//                   text: 'Failed to add student, Please Try Again',
//                   class_name: 'danger gritter-center',
//                   time: ''
//                 });
//             })
//             .always(function() {
              
//               PaStudentListStudentContainer.show();
//               PaStudentListStudentLoading.hide();

//             });

    
//     });

//   }

function get_new_schoolFee()
{

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();
  dashPayments_content.html('<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row"><div class="col-md-12" id="PaNewFeeContainer" ></div></div></div></div></div>');
  
  $("#PaNewFeeContainer").load("payments/new_schoolFee");

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

}

function newFeeJS()
{
  //hide 
    $("#NewSchoolFeeLoding").hide();
    $("#paymentNewFeeTerm").hide();
    $("#newfeeClassroom").hide();

  //fee info 
    $("#selectPaymentFeeType").select2().bind('change');

    var elementTerm = $("#selectPaymentFeeTerm");
    var selectedTerm = [];
    elementTerm.find("option").each(function(i,e){
        selectedTerm[selectedTerm.length]=$(e).attr("value");
    });

    elementTerm.select2().val(selectedTerm).trigger('change');

    $('#newFeedatePicker').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true ,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });

  //who pays 
    var studentTypeContainer        = $("#studentTypeContainer");
    var studentSelectSomeContainer  = $("#studentSelectSomeContainer");
    var customStudentContainer      = $("#customStudentContainer");;

   
    var chooseClassesContainer      = $("#chooseClassesContainer");
    var selectOrganizationContainer = $("#selectOrganizationContainer");

    studentSelectSomeContainer.hide();
    customStudentContainer.hide();

  //student type
  $('input[type=radio][name=studentType]').change(function() {
          
    var selected = $(this).val();

    studentSelectSomeContainer.hide();
    customStudentContainer.hide();

    if ( selected == 1 ) {
      

    }else if( selected == 2 ) {

        customStudentContainer.slideDown();

    }else if ( selected == 3 ) {

        studentSelectSomeContainer.slideDown();
    }


  });

}

function submitPaNewFee()
{   
    var NewSchoolFeeLoding  =  $('#NewSchoolFeeLoding');
    var formNewSchoolFee    =  $('#formNewSchoolFee');


      formNewSchoolFee.on('submit', function(e){
      
      formNewSchoolFee.hide();
      NewSchoolFeeLoding.slideDown();

      var frm = formNewSchoolFee;
      var url = base_url+"api/Pa/Fees_Current"; 

      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){

            if ( data.success )
            {
                  $.gritter.add({
                      title: 'Success',
                      text: 'Fee created.',
                      class_name: 'success gritter-center',
                      time: ''
                  });

                  load_ac_Income_SchoolFees_Fees_CurrentInvoices();

            }else{

                  $.gritter.add({
                      title: 'Failed',
                      text: 'Failed to add fee ',
                      class_name: 'danger gritter-center',
                      time: ''
                  });

            }
                 
         },"json");
    });

}

function newSchoolFeeSelect2( DefaultFeePriority )
{

  $("#selectPaymentFeePriority").select2().val(DefaultFeePriority).trigger('change');

  $("#newFeeSelectStudent").select2();

  $("#newFeeSelectClassroom").select2({ 
        formatResult: classroomformatResult, 
        formatSelection: classroomformatSelection, 
        escapeMarkup: function(m) { return m; } 
  });

    var elementOrganization  = $("#selectPaymentOrganization");
    var selectedOrganization = [];
    elementOrganization.find("option").each(function(i,e){
        selectedOrganization[selectedOrganization.length]=$(e).attr("value");
    });

    elementOrganization.select2().val(selectedOrganization).trigger('change');
}


function PaNewIncomeJS(){

  $("#paNewIncomeSource").select2().bind('change', getPaNewIncomeSource );
  $("#paNewIncomeStudent").select2();
  $("#paNewIncomeAcccountReceivable").select2();
  $("#paNewIncomeBankAccount").select2();
  $("#paNewIncomeBankOperationType").select2().bind('change', getPaNewIncomeBankOperationTypeSelected );
  
  $('#paNewIncomeCheckDate').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true ,
      locale: {
          format: 'DD/MM/YYYY'
      }
  });

  $('#paNewIncomeDate').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true ,
      locale: {
          format: 'DD/MM/YYYY'
      }
  });

  var PaNewIncomeBankContainer = $("#PaNewIncomeBankContainer");
  PaNewIncomeBankContainer.hide();

  $('input[type=radio][name=incomeChannel]').change(function() {
          
      var selected = $(this).val();

      if ( selected == 1 ) {

        PaNewIncomeBankContainer.slideDown();

      }else{

        PaNewIncomeBankContainer.hide();

      }

  });

  var PaNewIncomeSchoolFeesContainer          = $("#PaNewIncomeSchoolFeesContainer");
  var PaNewIncomeAccountsReceivablesContainer = $("#PaNewIncomeAccountsReceivablesContainer");
  var PaNewIncomeOtherContainer               = $("#PaNewIncomeOtherContainer");
  var PaNewIncomeCheckContainer               = $("#PaNewIncomeCheckContainer");
  
  PaNewIncomeSchoolFeesContainer.hide();
  PaNewIncomeAccountsReceivablesContainer.hide();
  PaNewIncomeOtherContainer.hide();
  PaNewIncomeCheckContainer.hide();

}

function PaNewExpenseJS(){

  $("#paNewExpenseBudgetItem").select2().bind('change', getPaNewExpenseBudgetItemChange );
  $("#paNewExpenseLiability").select2();
  $("#paNewExpenseLoanPeriod").select2();
  $("#paNewExpenseAssetType").select2();
  $("#paNewExpenseBankAccount").select2();
  $("#paNewExpenseBankOperationType").select2().bind('change', getPaNewExpenseBankOperationTypeChange );
  
  $('#paNewExpenseDate').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true ,
      locale: {
          format: 'DD/MM/YYYY'
      }
  });


  var newExpenseTypeContainer         = $("#newExpenseTypeContainer");
  var newExpenseBuyingAssetContainer  = $("#newExpenseBuyingAssetContainer");
  var newExpenseLiabilityContainer    = $("#newExpenseLiabilityContainer");
  var newExpenseLoanContainer         = $("#newExpenseLoanContainer");
  var PaNewExpenseBankContainer       = $("#PaNewExpenseBankContainer");
  var PaNewExpenseCheckContainer      = $("#PaNewExpenseCheckContainer");

  var PaNewExpenseAmountContainer     = $("#PaNewExpenseAmountContainer");;
  var PaNewExpenseMemoContainer       = $("#PaNewExpenseMemoContainer");;

  newExpenseTypeContainer.slideDown();
  newExpenseBuyingAssetContainer.hide();
  newExpenseLiabilityContainer.hide();
  newExpenseLoanContainer.hide();
  PaNewExpenseBankContainer.hide();
  PaNewExpenseCheckContainer.hide();

  $('input[type=radio][name=expenseIsRecorded]').change(function() {
          
      var selected = $(this).val();

      if ( selected == 0 ) {

        newExpenseTypeContainer.slideDown();
        newExpenseLiabilityContainer.hide();
        newExpenseLoanContainer.hide();

        PaNewExpenseAmountContainer.slideDown();
        PaNewExpenseMemoContainer.slideDown();

      }else if( selected == 1  ){

        newExpenseTypeContainer.hide();
        newExpenseLiabilityContainer.slideDown();
        newExpenseLoanContainer.hide();

        PaNewExpenseAmountContainer.slideDown();
        PaNewExpenseMemoContainer.slideDown();

      }else if( selected == 2 ){

        newExpenseTypeContainer.hide();
        newExpenseLiabilityContainer.hide();
        newExpenseLoanContainer.slideDown();

        PaNewExpenseAmountContainer.hide();
        PaNewExpenseMemoContainer.hide();

      }

      // else{

      //   newExpenseTypeContainer.hide();
      //   newExpenseLiabilityContainer.slideDown();

      // }

  });

  $('input[type=radio][name=expenseUsing]').change(function() {
          
      var selected = $(this).val();

      if ( selected == 1 ) {

        PaNewExpenseBankContainer.slideDown();

      }else{

        PaNewExpenseBankContainer.hide();

      }

  });
  

}

function getPaNewExpenseBudgetItemChange(){

  var  paNewExpenseBudgetItem   = $("#paNewExpenseBudgetItem");
  var  selected                 = paNewExpenseBudgetItem.select2('val');

  var newExpenseBuyingAssetContainer  = $("#newExpenseBuyingAssetContainer");

  if ( selected == 1 ) {
    newExpenseBuyingAssetContainer.slideDown();

  }else{
    newExpenseBuyingAssetContainer.hide();
  }


}

function getPaNewExpenseBankOperationTypeChange()
{
    var  paNewExpenseBankOperationType  = $("#paNewExpenseBankOperationType");
    var  selected                       = paNewExpenseBankOperationType.select2('val');

    var PaNewExpenseCheckContainer       = $("#PaNewExpenseCheckContainer");

    if ( selected == 3 ) {
      PaNewExpenseCheckContainer.slideDown();

    }else{
      PaNewExpenseCheckContainer.hide();

    }
}

function getPaNewIncomeSource(){

  var  paNewIncomeSource   = $("#paNewIncomeSource");
  var  selected            = paNewIncomeSource.select2('val');

  var PaNewIncomeSchoolFeesContainer          = $("#PaNewIncomeSchoolFeesContainer");
  var PaNewIncomeAccountsReceivablesContainer = $("#PaNewIncomeAccountsReceivablesContainer");
  var PaNewIncomeOtherContainer               = $("#PaNewIncomeOtherContainer");
  

  if ( selected == 1 ) {

    PaNewIncomeSchoolFeesContainer.slideDown();
    PaNewIncomeAccountsReceivablesContainer.hide();
    PaNewIncomeOtherContainer.hide();
    

  }else if( selected == 2 ){
    
    PaNewIncomeSchoolFeesContainer.hide();
    PaNewIncomeAccountsReceivablesContainer.slideDown();
    PaNewIncomeOtherContainer.hide();

  }else{

    PaNewIncomeSchoolFeesContainer.hide();
    PaNewIncomeAccountsReceivablesContainer.hide();
    PaNewIncomeOtherContainer.slideDown();


  }

}

function getPaNewIncomeBankOperationTypeSelected()
{
    var  paNewIncomeBankOperationType   = $("#paNewIncomeBankOperationType");
    var  selected                       = paNewIncomeBankOperationType.select2('val');

    var PaNewIncomeCheckContainer       = $("#PaNewIncomeCheckContainer");

    if ( selected == 3 ) {
      PaNewIncomeCheckContainer.slideDown();

    }else{
      PaNewIncomeCheckContainer.hide();

    }

}



function getStudents(){
	
	$("#moduleContainer").load("students/main",function() { get_mn__students(); } ).fadeIn(1000);
}

function get_mn__students()
{
	$("#moduleMain").load("students/mn__students").fadeIn(1000);
}

function get_mn_parentsGuardians()
{
	$("#moduleMain").load("students/mn_parentsGuardians").fadeIn(1000);
}

function get_profile_parentsGuardian()
{
	$("#moduleContainer").load("students/profile_parentsGuardian").fadeIn(1000);
}

function get_new_student()
{
    $('#moduleDashHomeContainer > div').hide();
  	$("#dashNew_content").load("students/new_student" ,function(){ $( "#dashContentainer_new").slideDown();
    }).fadeIn(1000);
}

function get_st_fromHolidays()
{
    $("#dashContentainer_Students").show();
    $("#dashStudents_content").show();

    $("#dashStudents_contentAnalytics").hide();
    $("#dashStudents_contentLists").hide();

    var Content = '';
    
        Content += '<div class="col-sm-12">';
          Content += '<div class="row">';
            Content += '<div class="col-sm-12">';
              Content += '<div class="col-sm-5 text-left ">';
                Content += '<button type="button" class="btn btn-primary" onclick="get_NewArrivalModal()"><strong><i class="fa fa-plus"></i> New Arrival</strong></button>&nbsp;&nbsp;&nbsp;';
                Content += '<button type="button" class="btn btn-default" onclick="get_St_AllArrivals()"><strong><i class="fa fa-bars"></i> All Arrivals</strong></button>';
              Content += '</div>';
              Content += '<div class="col-sm-7 text-left">';
                Content += '<h4><strong>Students Arrivals From Holidays</strong></h4>';
              Content += '</div>';
            Content += '</div>';
          Content += '</div>';
          Content += '<div id="DashStAnArrivalsStContainer">';
            Content += '<div class="row top-buffer">';
              Content += '<div class="col-sm-12">';
                Content += '<div class="col-sm-5 text-center">';
                  Content += '<h5><strong>Student Arrivals By Date</strong></h5>';
                Content += '</div>';
                Content += '<div class="col-sm-7 text-center">';
                  Content += '<h5><strong>Student Arrivals By Class</strong></h5>';
                Content += '</div>';
              Content += '</div>';
            Content += '</div>';
            Content += '<div class="row ">';
              Content += '<div class="col-sm-12">';
                Content += '<div class="col-sm-5">';
                  Content += '<div class="row">';
                    Content += '<div class="grid widget">';
                      Content += '<div class="grid-body" id="DashStAnArrivalsStTable" >';
                      Content += '</div>';
                    Content += '</div>';
                  Content += '</div>';
                Content += '</div>';
                Content += '<div class="col-sm-7">';
                  Content += '<div class="row">';
                    Content += '<div class="grid widget">';
                      Content += '<div class="grid-body" id="DashStAnClassArrivalsTable" >';
                      Content += '</div>';
                    Content += '</div>';
                  Content += '</div>';
                Content += '</div>';
              Content += '</div>';
            Content += '</div>';
          Content += '</div>';
        Content += '</div>';

    var dashStudents_contentProfile =  $("#dashStudents_contentProfile");
    dashStudents_contentProfile.show();
    dashStudents_contentProfile.html(Content);

    DashStStudentArrivalsLoadAnalytics();
}

function DashStStudentArrivalsLoadAnalytics()
{
  var DashStAnArrivalsStTable = $("#DashStAnArrivalsStTable");
    var TableContent       = '';
    var TableClassContent  = '';

    var url = base_url+"api/St/StudentArrival/create";

    $.getJSON( url, function( data ) {

        TableContent += '<table class="table table-bordered">';
            TableContent += '<thead>';
              TableContent += '<tr>';
                TableContent += '<th  class="text-center">Date</th>';
                TableContent += '<th  class="text-center">Day</th>';
                TableContent += '<th  class="text-center">Boarding</th>';
                TableContent += '<th  class="text-center">Total Arrivals</th>';
              TableContent += '</tr>';
            TableContent += '</thead>';
            TableContent += '<tbody>';

            var ArrivalTotalDay      = 0;
            var ArrivalTotalBoarding = 0;
            var ArrivalTotalTotal    = 0;

            var StudentsArrivals        = data.StudentsArrivals ;
            var StudentsByClassArrivals = data.StudentsByClassArrivals ;
            var DayBoarding             = data.DayBoarding ;

            $.each( StudentsArrivals, function(i, item) {

                ArrivalTotalDay      = ArrivalTotalDay + parseFloat(item.TotalDay) ;
                ArrivalTotalBoarding = ArrivalTotalBoarding + parseFloat(item.TotalBoarding) ;
                ArrivalTotalTotal    = ArrivalTotalTotal + parseFloat(item.numberOfArrivals) ;
                
                TableContent +="<tr >";
                  TableContent +="<td class='text-center'>"+date_moment(item.arrivalDate)+"</td>";
                  TableContent +="<td class='text-center'>"+item.TotalDay+"</td>";
                  TableContent +="<td class='text-center'>"+item.TotalBoarding+"</td>";
                  TableContent +="<td class='text-center'>"+item.numberOfArrivals+"</td>";
                  TableContent +="</td>";
                TableContent +="</tr>";
            });

            TableContent += '<tr>';
              TableContent += '<th  class="text-center">Total</th>';
              TableContent += '<th  class="text-center">'+ArrivalTotalDay+'</th>';
              TableContent += '<th  class="text-center">'+ArrivalTotalBoarding+'</th>';
              TableContent += '<th  class="text-center">'+ArrivalTotalTotal+'</th>';
            TableContent += '</tr>';
          TableContent += '</tbody>';
        TableContent += '</table>';

        //
        TableClassContent += '<table class="table table-bordered">';
            TableClassContent += '<thead>';
              TableClassContent += '<tr>';
                TableClassContent += '<th  rowspan="2" class="text-center">Class</th>';
                TableClassContent += '<th  colspan="2" class="text-center">Day</th>';
                TableClassContent += '<th  colspan="2" class="text-center">Boarding</th>';
                TableClassContent += '<th  colspan="2" class="text-center">Total</th>';
              TableClassContent += '</tr>';
              TableClassContent += '<tr>';
                TableClassContent += '<th  class="text-center">Arrived</th>';
                TableClassContent += '<th  class="text-center">Not Arrived</th>';
                TableClassContent += '<th  class="text-center">Arrived</th>';
                TableClassContent += '<th  class="text-center">Not Arrived</th>';
                TableClassContent += '<th  class="text-center">Arrived</th>';
                TableClassContent += '<th  class="text-center">Not Arrived</th>';
              TableClassContent += '</tr>';
            TableClassContent += '</thead>';
            TableClassContent += '<tbody>';


            $.each( StudentsByClassArrivals, function(i, item) {

                ClassTotalArrivalTotal  = parseFloat(item.ArrivalTotalDay)  + parseFloat(item.ArrivalTotalBoarding) ; 

                NotArrivedDay      = parseFloat(item.TotalDay) - parseFloat(item.ArrivalTotalDay);
                NotArrivedBoarding = parseFloat(item.TotalBoarding) - parseFloat(item.ArrivalTotalBoarding);
                NotArrivedTotal    = parseFloat(item.TotalStudent) - ClassTotalArrivalTotal;

                TableClassContent +="<tr >";  
                  TableClassContent +="<td class='text-center'>"+item.className+"</td>";
                  TableClassContent +="<td class='text-center'>"+item.ArrivalTotalDay+"</td>"; 
                  TableClassContent +="<td class='text-center'>"+NotArrivedDay+"</td>"; 
                  TableClassContent +="<td class='text-center'>"+item.ArrivalTotalBoarding+"</td>";
                  TableClassContent +="<td class='text-center'>"+NotArrivedBoarding+"</td>";
                  TableClassContent +="<td class='text-center'>"+ClassTotalArrivalTotal+"</td>";
                  TableClassContent +="<td class='text-center'>"+NotArrivedTotal+"</td>";
                  TableClassContent +="</td>";
                TableClassContent +="</tr>";
            });

          TableClassContent += '</tbody>';
        TableClassContent += '</table>';

        //
        var st_anlytArrivedDay    = $("#st_anlytArrivedDay");
        var st_anlytNotArrivedDay = $("#st_anlytNotArrivedDay");

        st_anlytArrivedDay.html(ArrivalTotalDay);
        st_anlytNotArrivedDay.html( parseFloat(DayBoarding.TotalDay) - ArrivalTotalDay);

        var st_anlytArrivedBoarding    = $("#st_anlytArrivedBoarding");
        var st_anlytNotArrivedBoarding = $("#st_anlytNotArrivedBoarding");

        st_anlytArrivedBoarding.html(ArrivalTotalBoarding);
        st_anlytNotArrivedBoarding.html( parseFloat(DayBoarding.TotalBoarding) - ArrivalTotalBoarding);

    }).done(function() {

       $("#DashStAnArrivalsStTable").html(TableContent);
       $("#DashStAnClassArrivalsTable").html(TableClassContent);
    })
    .fail(function() {
    })
    .always(function() {
    });

}
function get_NewArrivalModal()
{
  $('#Model_NewArrival').modal('toggle');
}

function get_St_AllArrivals()
{

    var DashStAnArrivalsStContainer =  $("#DashStAnArrivalsStContainer");
    var url = base_url+"api/St/StudentArrival";

    content = '';

    $.getJSON( url, function(data) {

        content += '<div class="col-sm-12">';
          content += '<div class="col-sm-1"></div>';
          content += '<div class="col-sm-10">';
          content += '<div class="row"><table class="table table-striped" id="dashStStudentArrivalsTable" ><thead><tr><th class="text-center">Date</th><th class="text-center">Time</th><th>Name</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">SMS Sent to Parents</th></tr></thead><tbody>';
      
          $.each(data, function(i, item) {

              content +="<tr id='"+item.id+"' >";
                content +="<td class='text-center'>"+date_moment(item.arrivalDate)+"</td>";
                content +="<td class='text-center'>"+item.arrivalTime+"</td>";
                content +="<td>"+item.studentNames+"</td>";
                content +="<td class='text-center'>"+item.studRegNber+"</td>";
                content +="<td class='text-center'>"+item.className+"</td>";
                content +="<td class='text-center'>";
                  if (item.notifyParents == 1 ) 
                  {
                    content +="<span class='label label-primary'>Yes</span>";
                  }else{
                    content +="<span class='label label-danger'>No</span>";
                  }
                content +="</td>";
              content +="</tr>";

          });
        
          content +='</tbody></table></div>';
          content +='</div>';
          content += '<div class="col-sm-1"></div>';
        content +='</div>';

    }).done(function() {

        DashStAnArrivalsStContainer.html(content);
        abTablePagingTable('dashStStudentArrivalsTable');

    })
    .fail(function() {
    })
    .always(function() {
    });
    
}

function get_profile_student(reg_nber)
{   
    
    $("#dashContentainer_Students").show();
    $("#dashStudents_content").show();

    $("#dashStudents_contentAnalytics").hide();
    $("#dashStudents_contentLists").hide();
    
    $("#dashNew_content").hide();

    $("#dashStudents_contentProfile").show();

     $("#dashStudents_contentProfile").load("students/profile_student/"+reg_nber, function() {
       $("#dashboardSeSelectStudent").select2('data', null).trigger("change");
    }); 

}

//form 
function hideAllNewStudent()
{

	$("#newStudent_term").hide("fast");
	$("#newStudent_year").hide("fast");
	$("#newStudent_stream").hide("fast");
  $("#studentNewStudentOrganization").hide("fast");
  $("#studentNewStOrganizationApproved").hide("fast");
  $("#formNewStudentLoading").hide(); 

}

function showNewStudentTerm()
{		
	$("#newStudent_year").hide("fast");
	$("#newStudent_stream").hide("fast");
	$("#newStudent_term").show("slow");
}

function showNewStudentYear()
{		
	$("#newStudent_stream").hide("fast");
	$("#newStudent_year").show("slow");
}

function showNewStudentStream()
{
	$("#newStudent_stream").show("slow");
}

function newStudentformatSelection(state)
{

    if (state.id > 0)
    {
        var originalOption = $(state.element);
        return originalOption.data('yearname') + " " + state.text;
    }
    else{

        return state.text;
    }

}

function newStudentformatResult(state)
{
    return state.text;
}

function onStNewSponsorTypeChange()
{
    var selected = $("#selectNewStudentSponsorType").select2('val');

    if (selected == 2)
    {
        $("#studentNewStudentOrganization").show("slow");

    }else{
        
        $("#studentNewStudentOrganization").hide("fast");
         $("#studentNewStOrganizationApproved").hide("fast");
    }
}

function onStNewSponsorChange()
{
    var selected = $("#selectStudentOrganization").select2('val');

    if (selected > 0)
    {
        $("#studentNewStOrganizationApproved").show("slow");
        var organization = $("#selectStudentOrganization").select2('data').text;
        $("#organizationApproved").text(organization);

    }else{
        
        $("#studentNewStOrganizationApproved").hide("fast");
    }
}

function studentNewStudentSelect2()
{
    $('#selectNewStudentMarital').select2().val('1').trigger('change');
    $('#selectNewStudentStatus').select2().val('4').trigger('change');

    $("#selectStudentNewStudentClass").select2({ 
            closeOnSelect: true,
            formatResult: newStudentformatResult,
            formatSelection: newStudentformatSelection
    });

    $("#selectNewStudentSponsorType").select2().bind('change', onStNewSponsorTypeChange);
    $("#selectStudentOrganization").select2().bind('change', onStNewSponsorChange);

    var element = $("#selectNewStudentTerms");
    var selected = [];
    element.find("option").each(function(i,e){
        selected[selected.length]=$(e).attr("value");
    });
    element.select2().val(selected).trigger('change');

    $("#selectNewStudentFaId").select2().bind('change', studentNewFaIDChange);
    $("#selectNewStudentFaNumberCountry").select2().bind('change', studentNewFaNumberCountryChange); 
     
    $("#selectNewStudentMoId").select2().bind('change', studentNewMoIDChange);
    $("#selectNewStudentMoNumberCountry").select2().bind('change', studentNewMoNumberCountryChange); 

    $("#selectNewStudentGuId").select2().bind('change', studentNewGuIDChange);
    $("#selectNewStudentGuNumberCountry").select2().bind('change', studentNewGuNumberCountryChange); 

    $("#selectNewStudentStId").select2().bind('change', studentNewStIDChange);
}  

function studentNewStIDChange()
{
  var selected = $("#selectNewStudentStId").select2('val');
  if (selected == 0)
  {
    $('#st_NID').mask('9 9999 9 9999999 9 99');
  }else{
    $('#st_NID').unmask();
  }
}

function studentNewFaIDChange()
{
    var selected = $("#selectNewStudentFaId").select2('val');
    if (selected == 0)
    {
      $('#fa_NID').mask('9 9999 9 9999999 9 99');
    }else{
      $('#fa_NID').unmask();
    }
}

function studentNewFaNumberCountryChange()
{
    var selected = $("#selectNewStudentFaNumberCountry").select2('val');
    if (selected == 0)
    {
      $('#fa_PhoneNumber').mask('999-999-9999');
    }else{
      $('#fa_PhoneNumber').unmask();
    }
}

function studentNewMoIDChange()
{
    var selected = $("#selectNewStudentMoId").select2('val');
    if (selected == 0)
    {
      $('#mo_NID').mask('9 9999 9 9999999 9 99');
    }else{
      $('#mo_NID').unmask();
    }
}

function studentNewMoNumberCountryChange()
{
    var selected = $("#selectNewStudentMoNumberCountry").select2('val');
    if (selected == 0)
    {
      $('#mo_PhoneNumber').mask('999-999-9999');
    }else{
      $('#mo_PhoneNumber').unmask();
    }
}

function studentNewGuIDChange()
{
    var selected = $("#selectNewStudentGuId").select2('val');
    if (selected == 0)
    {
      $('#gu_NID').mask('9 9999 9 9999999 9 99');
    }else{
      $('#gu_NID').unmask();
    }
}

function studentNewGuNumberCountryChange()
{
    var selected = $("#selectNewStudentGuNumberCountry").select2('val');
    if (selected == 0)
    {
      $('#gu_PhoneNumber').mask('999-999-9999');
    }else{
      $('#gu_PhoneNumber').unmask();
    }
}

function view_student_profile_student(regnumber)
{
  
  $("#dashNew_content").hide();
  get_profile_student(regnumber);
  $("#dashContentainer_Students").slideDown();

}
function submitNewStudentSuccess(regnumber)
{
     var successNotif  = '<div class="row"><h4 class="text-green">Student successfuly added!</h4></p></div>';
         successNotif += '<div class="row"><center><button type="button" class="btn btn-success" onclick="get_new_student()" >New Student</button> or <button type="button" class="btn btn-primary" onclick="view_student_profile_student('+regnumber+')" >View Profile</button></center></div>';

    $("#dashNew_content").html("<center>"+successNotif+"</center>");
}

function submitNewStudent()
{
    $('#formNewStudent').on('submit', function(e){
    
      $("#formNewStudent").slideUp();
      $("#formNewStudentLoading").slideDown();

      var imageData = $('.newst-image-editor').cropit('export');
      $('.newst-hidden-image-data').val(imageData);
      
      var frm = $('#formNewStudent');
      var url = base_url+"api/St/Students"; 

      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
            
              if ( data.success )
              {   
                var regnumber = data.registrationNumber;
                submitNewStudentSuccess(regnumber);

              } else {
                  
                  var saveStudentError = data.error ;

                  $.gritter.add({
                    title: 'Failed',
                    text: saveStudentError ,
                    class_name: 'warning gritter-center',
                    time: ''
                  });

                  $("#formNewStudentLoading").slideUp();
                  $("#formNewStudent").slideDown();
              }
                 
         },"json")
          .done(function() {
            
          })
          .fail(function() {
           
              $("#formNewStudent").slideDown();
              $("#formNewStudentLoading").slideUp();

              $.gritter.add({
                title: 'Failed',
                text: 'Failed to save Student, Please Try Again',
                class_name: 'danger gritter-center',
                time: ''
              });

          })
          .always(function() {});

    });

}

function onStudentGenderChange()
{

    var boardingGenderID = $('#boardingGenderID').val();
    var boardingID       = $('#boardingID').val();

    $('input[type=radio][name=sex]').change(function() {

        var selectedGender   = $('input[name=sex]:checked', '#formNewStudent').val();

        if ( boardingID == 1 )
        {
            $('#boarding_Boarding').attr("disabled",false);
            $('#boarding_Day').attr("disabled",false);

            if ( ( selectedGender == 0 ) && ( boardingGenderID == 2)  )
            {
                $('#boarding_Boarding').attr("disabled",true);
                $("#boarding_Day").prop("checked", true);
            }
            
            if ( ( selectedGender == 1 ) && ( boardingGenderID == 3) )
            {
                $('#boarding_Boarding').attr("disabled",true);
                $("#boarding_Day").prop("checked", true);
            }

        }
        
    });
}

function studentProfileTabClick(id,reg_nber)
{
    switch(id) {
            case 1:
               
                break;
            case 2:
                //address
                break;
            case 3:
                //payments
                studentProfileLoadPayments(reg_nber);
                break;

            case 4:
                //attendance'
                studentProfileLoadAttendance(reg_nber);
                break;

            case 5:
                //gradebook
                studentProfileLoadGradebook(reg_nber);
                break;
            case 6:
                //enrollment
                studentProfileLoadEnrollment(reg_nber);
                break;
            case 7:
                //discipline
                studentProfileLoadDiscipline(reg_nber);
                break;
            case 8:
                //library
                studentProfileLoadLibrary(reg_nber);
                break;
             case 9:
                //idCard
                studentProfileLoadIdCard(reg_nber);
                break;

             case 10:
               //Health Status
               studentProfileLoadHealthStatus(reg_nber);
                break;
        }
}

function studentProfileLoadPayments(regnumber)
{
        var container            =  $( "#dashboardContent" );

        container.hide();
        container.empty();

        $( "#dashboardContainer" ).hide();
        $( "#donut-dashboardContainer" ).empty();

        var TotalAmount       = 0;
        var AmountRemain      = 0;
        var TotalAmountPaid   = 0;
        var TotalAmountRemain = 0;

        var StudentFeeContainer ;

        var currency ;

        var dueDate  ;

        var url = base_url+"api/St/Profile_student/Payments/"+regnumber; 
         
        $.getJSON( url, function(data) {

          StudentFeeContainer   = '<div class="col-sm-7"><div class="row text-center"><h4><strong>Fees Payments</strong></h4></div><div class="row top-buffer"><table class="table table-hover"><thead><tr><th>Fee Name</th><th>Period</th><th>Paid Amount</th><th>Remain Amount</th><th>Due Date</th></tr></thead><tbody>';

            $.each(data, function(i, item) {

                AmountRemain       = parseFloat(item.amount) - parseFloat(item.paidAmount);
                TotalAmountPaid    = parseFloat(TotalAmountPaid) + parseFloat(item.paidAmount);
                TotalAmountRemain  = parseFloat(TotalAmountRemain) + parseFloat(AmountRemain);

                currency           = item.currency;

                StudentFeeContainer  +='<tr>';
                  StudentFeeContainer  +='<td><strong>'+item.name+'</strong> ('+item.description+') </td>';
                  StudentFeeContainer  +='<td>'+item.duration+'</td>';
                  StudentFeeContainer  +='<td><span class="text-green">'+formatNumber(item.paidAmount)+' '+item.currency+'</span></td>';
                  StudentFeeContainer  +='<td><span class="text-red">'+formatNumber(AmountRemain)+' '+item.currency+'</span></td>';
                  StudentFeeContainer  +='<td>'+ab_moment(item.dueDate)+'</td>';
                StudentFeeContainer  +='</tr>';

            });

            StudentFeeContainer  +='</tbody></table></div>';


        })
        .done(function() {

          if(TotalAmountPaid.toFixed(0) > 0 || TotalAmountRemain.toFixed(0) > 0)
          {
            TotalAmount = parseFloat(TotalAmountPaid.toFixed(0)) + parseFloat(TotalAmountRemain.toFixed(0));

            StudentFeeContainer  +='<div class="row"><div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title">TOTAL</span><span class="value">'+formatNumber(TotalAmount)+' '+ currency+'</span><span >&nbsp;</span></div></div></div><div class="col-sm-4"><div class="grid widget "><div class="grid-body"><span class="title">PAID</span><span class="value">'+formatNumber(TotalAmountPaid.toFixed(0))+' '+ currency+'</span><span >&nbsp;</span></div></div></div><div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title">REMAIN</span><span class="value">'+formatNumber(TotalAmountRemain.toFixed(0))+' '+ currency+'</span><span >&nbsp;</span></div></div></div></div>';

            
            container.append(StudentFeeContainer);

            container.show();
            $( "#dashboardContainer" ).slideDown( "slow" , function()
            {
              ab_load_studentPaFee(TotalAmount,TotalAmountPaid,TotalAmountRemain);

            });

          }else{

               container.append("No fees Added yet");
               container.show();
               $( "#dashboardContainer" ).slideDown( "slow" );
          }

        })
        .fail(function() {
        })
        .always(function() {
        });

}

var NotRecorded   = 0;
var Present       = 0;
var Absent        = 0;
var ExcusedAbsent = 0;
var Late          = 0;
var Sick          = 0;

function studentProfileLoadAttendance( reg_nber )
{   
        NotRecorded   = 0;
        Present       = 0;
        Absent        = 0;
        ExcusedAbsent = 0;
        Late          = 0;
        Sick          = 0;

        var container            =  $( "#studentProfileAtContent" );
        container.empty();   

        var url = base_url+"api/St/Profile_student/Attendance/"+reg_nber; 

        var LeftContainer    = '';
        var RightContainer   = '';

        $.getJSON( url, function(data) {

            var AttendanceStatistics = data.AttendanceStatistics;
            var AttendanceRecord     = data.AttendanceRecord;

            RightContainer   += '<div class="col-sm-6"><div class="row text-center"><h4><strong>Attendance Records</strong></h4></div><div class="row top-buffer"><table class="table abDataTable table-hover"><thead><tr><th class="text-center"> Event</th><th class="text-center"> Status</th><th class="text-center"> Date</th></tr></thead><tbody>';

                $.each(AttendanceRecord, function(i, item) {

                    RightContainer  +='<tr>';
                      RightContainer  +='<td class="text-center">'+item.EventName+'</td>';
                      RightContainer  +='<td class="text-center">'+getAtStatusSpanContentFirst(item.Status)+'</td>';
                      RightContainer  +='<td class="text-center">'+date_moment(item.Date)+'</td>';
                    RightContainer  +='</tr>';
                });
                
                RightContainer  +='</tbody></table></div></div>';

              LeftContainer  += '<div class="col-sm-3">';
                  LeftContainer  += '<div class="row text-center"><h4><strong>Statistics</strong></h4></div>';
                  LeftContainer  += '<div class="row">';
                      LeftContainer  += '<div class="grid widget"><div class="grid-body">';
                        LeftContainer  += '<div class="row top-buffermin"><div class="col-sm-6 text-right"><span class=" text-blue">Present</span></div><div class="col-sm-6" >'+AttendanceStatistics.NberPresent+'</div></div>';
                        LeftContainer  += '<div class="row "><div class="col-sm-6 text-right"><span class=" text-red">Absent</span></div><div class="col-sm-6" >'+AttendanceStatistics.NberAbsent+'</div></div>';
                        LeftContainer  += '<div class="row "><div class="col-sm-6 text-right"><span class=" text-aqua">Excused Absent</span></div><div class="col-sm-6" >'+AttendanceStatistics.NberExcusedAbsent+'</div></div>';
                        LeftContainer  += '<div class="row "><div class="col-sm-6 text-right"><span class=" text-green">Late</span></div><div class="col-sm-6" >'+AttendanceStatistics.NberLate+'</div></div>';
                        LeftContainer  += '<div class="row "><div class="col-sm-6 text-right"><span class=" text-yellow">Sick</span></div><div class="col-sm-6" >'+AttendanceStatistics.NberSick+'</div></div>';
                      LeftContainer  += '</div></div>';
                  LeftContainer  += '</div>';
              LeftContainer  += '</div>';

      })
      .done(function() {

            var AllTogether = '<div class="row"><div class="col-sm-12"><div class="col-sm-1"></div>'+LeftContainer+'<div class="col-sm-1"></div>'+RightContainer+'</div></div>';
            container.append(AllTogether);
            container.show();

            $("#studentProfileAtContainer").slideDown();

            abDataTable();

      })
      .fail(function() {
      })
      .always(function() {
      });

      /*
        var container            =  $( "#studentProfileAtContent" );

        container.hide();
        container.empty();

        $( "#studentProfileAtContainer" ).hide();

        var NotRecorded   = 0;
        var Present       = 0;
        var Absent        = 0;
        var ExcusedAbsent = 0;
        var Late          = 0;
        var Sick          = 0;

        var PeriodAttendanceContainer       = '';
        var DayAttendanceContainer          = '';
        var SchoolEventAttendanceContainer  = '';

        var url = base_url+"api/St/Profile_student/Attendance/"+regnumber; 
         
        $.getJSON( url, function(data) {

        var RecordsPeriod       = data.Period ;
        var RecordsDay          = data.Day ;
        var RecordsSchoolEvent  = data.SchoolEvent ;

          PeriodAttendanceContainer   = '<div class="col-sm-6"><div class="row "><table class="table abDataTable table-hover"><thead><tr><th>Date</th><th>Subject</th><th class="text-center" >Status</th></tr></thead><tbody>'; 

            $.each(RecordsPeriod, function(i, item) {

                PeriodAttendanceContainer  +='<tr>';
                  PeriodAttendanceContainer  +='<td>'+moment.unix(item.date).format("DD-MM-YYYY")+'</td>';
                  PeriodAttendanceContainer  +='<td>'+item.subjectName+'</td>';
                  PeriodAttendanceContainer  +='<td class="text-center">'+getAtStatusSpanContentFirst( item.status )+'</td>';
                PeriodAttendanceContainer  +='</tr>';

            });

          PeriodAttendanceContainer  +='</tbody></table></div></div>';

          DayAttendanceContainer   = '<div class="col-sm-6"><div class="row"><table class="table abDataTable table-hover"><thead><tr><th>Date</th><th class="text-center" >Status</th></tr></thead><tbody>';

            $.each(RecordsDay, function(i, item) {

                DayAttendanceContainer  +='<tr>';
                  DayAttendanceContainer  +='<td>'+moment.unix(item.date).format("DD-MM-YYYY")+'</td>';
                  DayAttendanceContainer  +='<td class="text-center" >'+getAtStatusSpanContentFirst( item.status )+'</td>';
                DayAttendanceContainer  +='</tr>';

            });

          DayAttendanceContainer  +='</tbody></table></div></div>';

            SchoolEventAttendanceContainer   = '<div class="col-sm-6"><div class="row"><table class="table abDataTable table-hover"><thead><tr><th>Date</th><th>Event</th><th class="text-center" >Status</th></tr></thead><tbody>';

            $.each(RecordsSchoolEvent, function(i, item) {

                SchoolEventAttendanceContainer  +='<tr>';
                  SchoolEventAttendanceContainer  +='<td>'+moment.unix(item.date).format("DD-MM-YYYY")+'</td>';
                  SchoolEventAttendanceContainer  +='<td>'+item.eventName+'</td>';
                  SchoolEventAttendanceContainer  +='<td class="text-center" >'+getAtStatusSpanContentFirst( item.status )+'</td>';
                SchoolEventAttendanceContainer  +='</tr>';

            });

            SchoolEventAttendanceContainer  +='</tbody></table></div></div>';


        })
        .done(function() {

           // var contentAll  = '<div class="row"><div class="col-sm-12"><div class="col-sm-1"></div><div class="col-sm-4 text-center"><strong>Subject Class Attendance</strong></div><div class="col-sm-1"></div><div class="col-sm-2 text-center"><string>Day Class Attendance</string></div><div class="col-sm-1"></div><div class="col-sm-4 text-center"><string>Events Attendance</string></div></div></div>';
            
            var contentAll  = '';

            contentAll  += '<div class="tabbable tabs-left"><ul class="nav nav-tabs"><li class="active"><a href="#home-left" data-toggle="tab"><i class="fa fa-home"></i></a></li><li><a href="#PeriodAttendance" data-toggle="tab"> Period Attendance</a></li><li><a href="#ClassDailyAttendance" data-toggle="tab"> Class Daily Attendance</a></li><li><a href="#EventAttendance" data-toggle="tab"></a> Event Attendance</li></ul><div class="tab-content">';

            contentAll     += '<div class="tab-pane" id="EventAttendanceactive">'+PeriodAttendanceContainer+'</div>';
            contentAll     += '<div class="tab-pane" id="ClassDailyAttendance">'+DayAttendanceContainer+'</div>';
            contentAll     += '<div class="tab-pane" id="EventAttendance">'+SchoolEventAttendanceContainer+'</div>';

            contentAll  += '</div></div>';

            container.append(contentAll);
            container.show();

            $( "#studentProfileAtContainer" ).show();

            abDataTable();

        })
        .fail(function() {
        })
        .always(function() {
        });
    */
}

function studentProfileLoadGradebook(reg_nber)
{
    var container            =  $( "#gradebookContentContent" );

    container.empty();   

        var url = base_url+"api/St/Profile_student/Gradebook/"+reg_nber; 
         
        GradebookContainer = '';

        $.getJSON( url, function(data) {

           GradebookContainer   += '<div class="row"><div class="col-sm-12"><div class="col-sm-1"></div><div class="col-sm-10"><div class="row text-center"><h4><strong>Results in this term</strong></h4></div><div class="row"><table class="table abDataTable table-hover"><thead><tr><th> Subject</th><th > Assessment</th><th class="text-right"> Score</th><th class="text-right"> Max</th></tr></thead><tbody>';

              $.each(data, function(i, item) {

                  GradebookContainer  +='<tr>';
                    GradebookContainer  +='<td>'+item.Subject+'</td>';
                    GradebookContainer  +='<td>'+item.Assessment+'</td>';
                    GradebookContainer  +='<td class="text-right" >'+item.score+'</td>';
                    GradebookContainer  +='<td class="text-right" >'+item.maximumMarks+'</td>';
                  GradebookContainer  +='</tr>';

              });

              GradebookContainer  +='</tbody></table></div></div>';
            GradebookContainer  +='</div></div>';

        })
        .done(function() {

            container.html(GradebookContainer);

            container.show();
            $( "#gradebookContentContainer" ).slideDown();

            abDataTable();
        })
        .fail(function() {
        })
        .always(function() {
        });

}

function studentProfileLoadEnrollment(reg_nber)
{
    var container            =  $( "#enrollmentContentContent" );

    container.empty();   

    var url = base_url+"api/St/Profile_student/Enrollment/"+reg_nber; 
     
    EnrollmentContainer = '';

    $.getJSON( url, function(data) {

     EnrollmentContainer   += '<div class="row"><div class="col-sm-12"><div class="col-sm-1"></div><div class="col-sm-9"><div class="row text-center"><h4><strong>Enrollment</strong></h4></div><div class="row"><table class="table table-hover"><thead><tr><th class="text-center"> Year</th><th class="text-center"> Term</th><th class="text-center"> Class</th><th class="text-center">Boarding Status</th><th class="text-center">Status</th></tr></thead><tbody>';

        $.each(data, function(i, item) {

            EnrollmentContainer  +='<tr>';
              EnrollmentContainer  +='<td class="text-center">'+item.year+'</td>';
              EnrollmentContainer  +='<td class="text-center">'+item.number+'</td>';
              EnrollmentContainer  +='<td class="text-center">'+item.classname+'</td>';
              EnrollmentContainer  +='<td class="text-center">'+((item.boarding == 1) ? "<span class='label label-primary'>Yes</span>" : "<span class='label label-danger'>No</span>" )+'</td>';
              EnrollmentContainer  +='<td class="text-center">';
                EnrollmentContainer  +='<span class="xeditableStudentEnrollmentStatus" data-type="select2" data-pk="1" data-title="Select Enrollment status" data-url="api/St/updateProfile?id=0&registrationNumber='+reg_nber+'&editInputType=35&term='+item.id+'">';
                  if ( item.isEnrolled == 1 ) {
                    EnrollmentContainer  +='Enrolled';
                  }else{
                    EnrollmentContainer  +='Un-Enrolled';
                  }
                EnrollmentContainer  +='</span>';
              EnrollmentContainer  +='</td>';
            EnrollmentContainer  +='</tr>';

        });

        EnrollmentContainer  +='</tbody></table></div></div></div></div>';

    })
    .done(function() {

        container.append(EnrollmentContainer);

        container.show();
        $( "#enrollmentContentContainer" ).slideDown();

        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.ajaxOptions = {type: "POST" };

        $('.xeditableStudentEnrollmentStatus').editable({
          source: [
                {id: '1', text: 'Enrolled'},
                {id: '0', text: 'Un-Enrolled'}
             ],
          select2: {
             width:150,
             multiple: false
          },
          success: function(response, newValue) {

            if ( response.success ) {

              if ( response.value == 1 ) {

                var studentEnroll_term = $('#studentEnroll_term');
                studentEnroll_term.val( response.term );

                $('#modal_studentEnrollInClass').modal('toggle');

                var Classrooms        = response.Classrooms;
                var ClassroomsSize    = Classrooms.length ;

                var AnClassOptions    = '';
                AnClassOptions        = '<option></option>';

                var Anclassidentifier = "";
                var AnisTheFirsRecord = true;

                for ( var i=0; i< ClassroomsSize ; i++ ) {

                  if ( Classrooms[i].classidentifier == Anclassidentifier ){
                      AnClassOptions += '<option value="'+Classrooms[i].id+'" data-yearname="'+Classrooms[i].YearName+'" data-level="'+Classrooms[i].levelID+'" >'+Classrooms[i].name+'</option>';
                  
                  }else{

                      Anclassidentifier = Classrooms[i].classidentifier;
                      if ( AnisTheFirsRecord ){

                        AnisTheFirsRecord = false; 

                      }else{
                        AnClassOptions += '</optgroup>';
                      }

                      AnClassOptions += '<optgroup label="'+Classrooms[i].YearName+'">';
                      AnClassOptions += '<option value="'+Classrooms[i].id+'" data-yearname="'+Classrooms[i].YearName+'" data-level="'+Classrooms[i].levelID+'" >'+Classrooms[i].name+'</option>';
                
                  }
                        
                }

               var enroll_class =  $('#enroll_class');
               enroll_class.html(AnClassOptions);

               enroll_class.select2({ 
                  formatResult: newStudentformatResult,
                  formatSelection: newStudentformatSelection
               });

              }else{
                get_profile_student( reg_nber );
                
              }

            }else{

              return response.msg;

            }

        }
      });

    })
    .fail(function() {
    })
    .always(function() {
    });

}

function studentProfileLoadDiscipline(reg_nber)
{
      var container            =  $( "#disciplineContent" );
      container.empty();   

      var url = base_url+"api/St/Profile_student/Discipline/"+reg_nber; 

      var LeftContainer    = '';
      var RightContainer   = '';

      $.getJSON( url, function(data) {

          var Points        = data.points;
          var Deduction     = data.Deduction;

          var TotalDeducted = 0;
          var TotalRewarded = 0;

          RightContainer   += '<div class="col-sm-6"><div class="row text-center"><h4><strong>Discipline Records</strong></h4></div><div class="row top-buffer"><table class="table abDataTable table-hover"><thead><tr><th class="text-center"> Date</th><th class="text-center"> Metric</th><th class="text-center"> Points</th><th class="text-center"> Comments</th></tr></thead><tbody>';

              $.each(Deduction, function(i, item) {

                  RightContainer  +='<tr>';
                    RightContainer  +='<td class="text-center">'+date_moment(item.date)+'</td>';
                    RightContainer  +='<td class="text-center">'+item.faultName+'</td>';
                    RightContainer  +='<td class="text-center">';
                      RightContainer += ( item.faultType == 0 ) ? "<span class='text-red'>- "+item.deductedPoint+"</span>" : "<span class='text-blue'>+ "+item.deductedPoint+"</span>";
                    RightContainer  +='</td>';
                    RightContainer  +='<td class="text-center">'+item.commentNumber+'</td>';
                  RightContainer  +='</tr>';

                  if ( item.faultType == 0 ) {
                    TotalDeducted = parseFloat(TotalDeducted) + parseFloat(item.deductedPoint);

                  }else if( item.faultType == 1 ){
                    TotalRewarded = parseFloat(TotalRewarded) + parseFloat(item.deductedPoint);

                  }

              });
              
              RightContainer  +='</tbody></table></div></div>';

              var currrentDisplinePoints =  parseFloat(Points) - parseFloat(TotalDeducted) + parseFloat(TotalRewarded) ;

           LeftContainer  += '<div class="col-sm-3">';
              LeftContainer  += '<div class="row text-center"><h4><strong>Statistics</strong></h4></div>';
              LeftContainer  += '<div class="row">';
                  LeftContainer  += '<div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title ">Discipline Points</span><span class="value ">'+currrentDisplinePoints+'</span><span >&nbsp;</span></div></div></div>';
                  LeftContainer  += '<div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title text-red">Total Deducted</span><span class="value text-red">'+TotalDeducted+'</span><span >&nbsp;</span></div></div></div>';
                  LeftContainer  += '<div class="col-sm-4"><div class="grid widget"><div class="grid-body"><span class="title text-green">Total Rewarded</span><span class="value text-green">'+TotalRewarded+'</span><span >&nbsp;</span></div></div></div>';
              LeftContainer  += '</div>';
          LeftContainer  += '</div>';

    })
    .done(function() {

          var AllTogether = '<div class="row"><div class="col-sm-12"><div class="col-sm-1"></div>'+LeftContainer+'<div class="col-sm-1"></div>'+RightContainer+'</div></div>';
          container.append(AllTogether);
          container.show();

          $("#disciplineContainer").slideDown();

          abDataTable();

    })
    .fail(function() {
    })
    .always(function() {
    });

}

function studentProfileLoadLibrary(reg_nber)
{
    var container            =  $( "#libraryContent" );

    container.empty();   

    var url = base_url+"api/St/Profile_student/Library/"+reg_nber; 
     
    LibraryContainer = '';

    $.getJSON( url, function(data) {

     LibraryContainer   += '<div class="row"><div class="col-sm-12"><div class="col-sm-1"></div><div class="col-sm-9"><div class="row text-center"><h4><strong>Borrowed Library Items</strong></h4></div><div class="row"><table class="table abDataTable table-hover"><thead><tr><th class="text-center"> Item</th><th class="text-center"> Index Number</th><th class="text-center"> Type</th><th class="text-center"> Taken on</th><th class="text-center"> DueDate</th></tr></thead><tbody>';

        $.each(data, function(i, item) {

            LibraryContainer  +='<tr>';
              LibraryContainer  +='<td class="text-center">'+item.title+'</td>';
              LibraryContainer  +='<td class="text-center">'+item.indexNumber+'</td>';
              LibraryContainer  +='<td class="text-center">'+item.itemType+'</td>';
              LibraryContainer  +='<td class="text-center">'+moment.utc(item.taken, 'X').format('DD-MM-YYYY')+'</td>';
              LibraryContainer  +='<td class="text-center">'+ab_moment(item.returnDueDate)+'</td>';
            LibraryContainer  +='</tr>';

        });

        LibraryContainer  +='</tbody></table></div></div></div></div>';

    })
    .done(function() {

        container.append(LibraryContainer);

        container.show();
        $( "#libraryContainer" ).slideDown();

        abDataTable();
    })
    .fail(function() {
    })
    .always(function() {
    });
}

function studentProfileLoadIdCard(reg_nber)
{
   
}

function studentProfileLoadHealthStatus(reg_nber)
{

    var container    =  $( "#healthStatusContent" );

    container.empty();   

    var url = base_url+"api/St/Profile_student/HealthStatus/"+reg_nber; 
     
    healthStatusContent = '';

    $.getJSON( url, function(data) {

     healthStatusContent   += '<div class="row"><div class="col-sm-12"><div class="col-sm-1"></div><div class="col-sm-9"><div class="row text-center"><h4><strong>Health Status</strong></h4></div><div class="row">';
     healthStatusContent   += '<table class="table abDataTable table-hover"><thead><tr><th class="text-center"> Date</th><th > Sickness</th><th > Comment</th><th > Recorded by</th></tr></thead><tbody>';

        $.each(data, function(i, item) {

            healthStatusContent  +='<tr>';
              healthStatusContent  +='<td class="text-center">'+date_moment(item.date)+'</td>';
              healthStatusContent  +='<td >'+item.sickness+'</td>';
              healthStatusContent  +='<td >'+item.comment+'</td>';
              healthStatusContent  +='<td >'+item.addedBy+'</td>';
            healthStatusContent  +='</tr>';

        });

        healthStatusContent  +='</tbody></table></div></div></div></div>';

    })
    .done(function() {

        container.append(healthStatusContent);
        container.show();

        $( "#healthStatusContainer" ).slideDown();
        abDataTable();
        
    })
    .fail(function() {
    })
    .always(function() {
    });

}

function onStudentCheckAvailability()
{
//
    var fa_NID          = $("#fa_NID");
    var fa_PhoneNumber  = $("#fa_PhoneNumber");
    var fa_InEmail      = $("#fa_InEmail");

    fa_NID.blur(
        function(){
            var fa_NIDValue   = fa_NID.val();
                fa_NIDValue   = fa_NIDValue.replace(/[#_-]/g,'');
            newStudentCheckExisting  (1, 1,'National ID', fa_NIDValue );
        }
    );

    fa_PhoneNumber.blur(
        function(){
            var fa_PhoneNumberValue = fa_PhoneNumber.val();
                fa_PhoneNumberValue = fa_PhoneNumberValue.replace(/[#_-]/g,'');
            console.log("Phone "+fa_PhoneNumberValue);
            newStudentCheckExisting  (1, 3,'Phone number', fa_PhoneNumberValue );
        }
    );

    fa_InEmail.blur(
        function(){
            var fa_emailValue       = fa_InEmail.val();
            newStudentCheckExisting  (1, 4, 'Email' ,fa_emailValue );
        }
    );

//
    var mo_NID          = $("#mo_NID");
    var mo_PhoneNumber  = $("#mo_PhoneNumber");
    var mo_InEmail      = $("#mo_InEmail");

    mo_NID.blur(
        function(){
            var ma_NIDValue = mo_NID.val();
                ma_NIDValue     = ma_NIDValue.replace(/[#_-]/g,'');
            newStudentCheckExisting  (2, 1, 'National ID' , ma_NIDValue );
        }
    );

    mo_PhoneNumber.blur(
        function(){
            var ma_PhoneNumberValue = mo_PhoneNumber.val();
                ma_PhoneNumberValue = ma_PhoneNumberValue.replace(/[#_-]/g,'');
            newStudentCheckExisting  (2, 3,'Phone Number', ma_PhoneNumberValue );
        }
    );

    mo_InEmail.blur(
        function(){
            var ma_emailValue   = mo_InEmail.val();
            newStudentCheckExisting  (2, 4,'Email', ma_emailValue );
        }
    );

//
    var gu_NID          = $("#gu_NID");
    var gu_PhoneNumber  = $("#gu_PhoneNumber");
    var gu_InEmail      = $("#gu_InEmail");


    gu_NID.blur(
        function(){
            var gu_NIDValue = gu_NID.val();
            gu_NIDValue     = gu_NIDValue.replace(/[#_-]/g,'');
            newStudentCheckExisting  (3, 1,'National ID', gu_NIDValue );
        }
    );

    gu_PhoneNumber.blur(
        function(){
            var gu_PhoneNumberValue = gu_PhoneNumber.val();
            gu_PhoneNumberValue     = gu_PhoneNumberValue.replace(/[#_-]/g,'');
            newStudentCheckExisting  (3, 3,'Phone Number', gu_PhoneNumberValue );

        }
    );

    gu_InEmail.blur(
        function(){
            var gu_emailValue = gu_InEmail.val();
            newStudentCheckExisting  (3, 4,'Email', gu_emailValue );
        }
    );

} 



function newStudentCheckExisting  (type, field, FieldName ,ProvidedValue)
{
    ProvidedValue = ProvidedValue.trim();

      if (field == 3 )
      {
        var selectStudentNewPrimarycontact = $("#selectStudentNewPrimarycontact");
        selectStudentNewPrimarycontact.select2('val', '').trigger('change');

        switch(type) {
            case 1:
                var NumberCountry    = $("#selectNewStudentFaNumberCountry").select2('val');
                var PhoneNumberValue = $("#fa_PhoneNumber").val().replace(/[#_-]/g,'');

                if ( PhoneNumberValue.length > 9 )
                 {
                    selectStudentNewPrimarycontact.find('option[value=1]').attr('disabled', false);
                 }else{
                    selectStudentNewPrimarycontact.find('option[value=1]').attr('disabled', true);
                 }
                break;

            case 2:
                var NumberCountry    = $("#selectNewStudentMoNumberCountry").select2('val');
                var PhoneNumberValue = $("#mo_PhoneNumber").val().replace(/[#_-]/g,'');
                
                if ( PhoneNumberValue.length > 9 )
                 {
                  selectStudentNewPrimarycontact.find('option[value=2]').attr('disabled', false);
                 }else{
                  selectStudentNewPrimarycontact.find('option[value=2]').attr('disabled', true);
                 }
                break;

            case 3:
                var NumberCountry    = $("#selectNewStudentGuNumberCountry").select2('val');
                var PhoneNumberValue = $("#gu_PhoneNumber").val().replace(/[#_-]/g,'');
                
                if ( PhoneNumberValue.length > 9 )
                {
                  selectStudentNewPrimarycontact.find('option[value=3]').attr('disabled', false);
                }else{
                  selectStudentNewPrimarycontact.find('option[value=3]').attr('disabled', true);
                }
                break;
        }

      }else{

        var NumberCountry = '';

      }


    if ( ProvidedValue.length > 0 )
     {
        var url = base_url+"api/St/Students/checkExisting"; 
        
        $.getJSON( url, { DataType: type , DataField: field , DataValue: ProvidedValue , NumberCountry: NumberCountry  ,PhoneNumberValue: PhoneNumberValue }, function(data) {

            var Found         = data.Found;
            var PersonInfo    = data.Person;

            if (Found) {

                var container = $('#newStudentFoundSection');
                container.html("<div class=' top-buffer'><center><div class='row'><img src='personimage/100/"+PersonInfo.id+"' class='img-circle thumbnail' alt=''></div></center></div>");
                
                var newStudentFoundPersonName = $('#newStudentFoundPersonName');
                newStudentFoundPersonName.html(PersonInfo.firstname+' '+PersonInfo.middleName+' '+PersonInfo.lastName);

                var newStudentFoundFieldValue = $('#newStudentFoundFieldValue');
                newStudentFoundFieldValue.html(ProvidedValue);

                var newStudentFoundFieldName  = $('#newStudentFoundFieldName');
                newStudentFoundFieldName.html(FieldName);

                var newStudentCheckExistingValue = $('#newStudentCheckExistingValue');

                newStudentCheckExistingValue.attr('Persontype',type);
                newStudentCheckExistingValue.attr('PersonFieldType',field);

                newStudentCheckExistingValue.attr('PersonID',PersonInfo.id);
                newStudentCheckExistingValue.attr('PersonFirstName',PersonInfo.firstname);
                newStudentCheckExistingValue.attr('PersonMiddlename',PersonInfo.middleName);
                newStudentCheckExistingValue.attr('PersonLastName',PersonInfo.lastName);

                var PersonInfoPhoneNumber = PersonInfo.PhoneNumber;

                var newStudentFoundPersonTypePhone   = $('#newStudentFoundPersonTypePhone').val(type);
                var newStudentFoundPersonNumberPhone = $('#newStudentFoundPersonNumberPhone').val(type);

                newStudentFoundPersonTypePhone.val('');
                newStudentFoundPersonNumberPhone.val('');

                if (!PersonInfoPhoneNumber)
                {
                    // show enter phone number 

                }else{

                    newStudentFoundPersonTypePhone.val(type);
                    newStudentFoundPersonNumberPhone.val("+"+PersonInfoPhoneNumber);
                }

                var options = {
                    "backdrop" : "static",
                    "keyboard" : false
                }

                $('#Model_NewStudentFoundPerson').modal(options);

            }

        })
        .done(function() {

        })
        .fail(function() {
        })
        .always(function() {
        });

     }
}

function studentProfileCheckExistingConfirm()
{     
      $('#formStudentProfileFound').on('submit', function(e){

        var studentProfileFaContainer         = $("#studentProfileFaContainer");
        var studentProfileFaLoadingContainer  = $("#studentProfileFaLoadingContainer");

        var studentProfileMoContainer         = $("#studentProfileMoContainer");
        var studentProfileMoLoadingContainer  = $("#studentProfileMoLoadingContainer");

        var studentProfileGuContainer         = $("#studentProfileGuContainer");
        var studentProfileGuLoadingContainer  = $("#studentProfileGuLoadingContainer");

      $('#Model_StudentProfileFoundPerson').modal('hide');

      var ParentType       = $('#studentProfileFoundPersonStudentPersonType').val();
      var StudentPersonID  = $('#studentProfileFoundPersonStudentPersonID').val();
      var StudentPersonReg = $('#studentProfileFoundPersonStudentReg').val();

      switch( ParentType )
      {
        case 1: 
            studentProfileFaContainer.hide();
            studentProfileFaLoadingContainer.slideDown();
          break;

        case 2:
            studentProfileMoContainer.hide();
            studentProfileMoLoadingContainer.slideDown();
          break;

        case 3:
            studentProfileGuContainer.hide();
            studentProfileGuLoadingContainer.slideDown();
          break;
      }

      var frm = $('#formStudentProfileFound');
      var url = base_url+"api/St/updateProfile?id="+StudentPersonID+"&registrationNumber="+StudentPersonReg+"&editInputType=30"; 

      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){

              if ( data.success )
              {
                
                //Reload The Student Profile 
                get_profile_student( StudentPersonReg );

                $.gritter.add({
                    title: 'Success',
                    text: 'Successfuly updated.',
                    class_name: 'success gritter-center',
                    time: ''
                });

              } else{


                  $.gritter.add({
                    title: data.errormsg ,
                    text: 'Failed To Update ',
                    class_name: 'danger gritter-center',
                    time: ''
                  });

              }  
                 
         },"json")
          .done(function() {
            
          })
          .fail(function() {
           
          })
          .always(function() {

                switch( ParentType )
                {
                    case 1: 

                        studentProfileFaLoadingContainer.hide();
                        studentProfileFaContainer.slideDown();
                      break;

                    case 2:
                        studentProfileMoContainer.hide();
                        studentProfileMoLoadingContainer.slideDown();
                      break;

                    case 3:
                        studentProfileGuContainer.hide();
                        studentProfileGuLoadingContainer.slideDown();
                      break;
                }
          });

    });



      //   var FoundNumberPersontype  = $('#newStudentFoundPersonTypePhone').val();
      //   var FoundNumberPersonPhone = $('#newStudentFoundPersonNumberPhone').val();

      //   console.log("TypePhone: "+FoundNumberPersontype);

      //   $('#Model_NewStudentFoundPerson').modal('hide');

      //   var newStudentCheckExistingValue = $('#newStudentCheckExistingValue');

      //   var Persontype       = newStudentCheckExistingValue.attr('Persontype');
      //   var PersonFieldType  = newStudentCheckExistingValue.attr('PersonFieldType');

      //   var PersonID         = newStudentCheckExistingValue.attr('PersonID');
      //   var PersonFirstName  = newStudentCheckExistingValue.attr('PersonFirstName');
      //   var PersonMiddlename = newStudentCheckExistingValue.attr('PersonMiddlename');
      //   var PersonLastName   = newStudentCheckExistingValue.attr('PersonLastName');
        
      //   switch(Persontype) {
      //       case "1":
      //               $('.newStudentFaContainer').hide();
                    
      //               $('#fa_foundPerson').val(PersonID);
      //               $('#fa_foundInputType').val(PersonFieldType);

      //               $('#fa_firstname').val(PersonFirstName);
      //               $('#fa_middleName').val(PersonMiddlename);
      //               $('#fa_lastName').val(PersonLastName);

      //               if (FoundNumberPersontype)
      //               {
      //                   $("#fa_phone_type").select2("val", "0");
      //                   $('#fa_PhoneNumber').val(FoundNumberPersonPhone);
      //               }

      //               var newStudentFaFoundContainer = $('#newStudentFaFoundContainer');
      //               var content = "<div class=' top-buffer'><center><div class='row'><img src='personimage/100/"+PersonID+"' class='img-circle thumbnail' alt=''></div><div class='row'><h4>"+PersonFirstName+" "+PersonMiddlename+" "+PersonLastName+"</h4></div><div class='row'><p class='info'><a href='#' onclick='newStudentPersonFoundFaChange()' >Change this person</a></p></div></center></div>";
      //               newStudentFaFoundContainer.html(content);
      //               newStudentFaFoundContainer.slideDown();

      //           break;

      //       case "2":
      //               $('.newStudentMoContainer').hide();
                    
      //               $('#ma_foundPerson').val(PersonID);
      //               $('#ma_foundInputType').val(PersonFieldType);

      //               $('#ma_firstname').val(PersonFirstName);
      //               $('#ma_middlename').val(PersonMiddlename);
      //               $('#ma_lastname').val(PersonLastName);

      //               if (FoundNumberPersontype)
      //               {
      //                   $("#mo_phone_type").select2("val", "0");
      //                   $('#mo_PhoneNumber').val(FoundNumberPersonPhone);
      //               }

      //               var newStudentMaFoundContainer = $('#newStudentMaFoundContainer');
      //               var content = "<div class=' top-buffer'><center><div class='row'><img src='personimage/100/"+PersonID+"' class='img-circle thumbnail' alt=''></div><div class='row'><h4>"+PersonFirstName+" "+PersonMiddlename+" "+PersonLastName+"</h4></div><div class='row'><p class='info'><a href='#' onclick='newStudentPersonFoundMaChange()' >Change this person</a></p></div></center></div>";
      //               newStudentMaFoundContainer.html(content);
      //               newStudentMaFoundContainer.slideDown();

      //           break;

      //       case "3":
      //               $('.newStudentGuContainer').hide();
                    
      //               $('#gu_foundPerson').val(PersonID);
      //               $('#gu_foundInputType').val(PersonFieldType);

      //               $('#gu_firstname').val(PersonFirstName);
      //               $('#gu_middleName').val(PersonMiddlename);
      //               $('#gu_lastName').val(PersonLastName);

      //               if (FoundNumberPersontype)
      //               {
      //                   $("#gu_phone_type").select2("val", "0");
      //                   $('#gu_PhoneNumber').val(FoundNumberPersonPhone);
      //               }

      //               var newStudentGuFoundContainer = $('#newStudentGuFoundContainer');
      //               var content = "<div class=' top-buffer'><center><div class='row'><img src='personimage/100/"+PersonID+"' class='img-circle thumbnail' alt=''></div><div class='row'><h4>"+PersonFirstName+" "+PersonMiddlename+" "+PersonLastName+"</h4></div><div class='row'><p class='info'><a href='#' onclick='newStudentPersonFoundGuChange()' >Change this person</a></p></div></center></div>";
      //               newStudentGuFoundContainer.html(content);
      //               newStudentGuFoundContainer.slideDown();

      //           break;
      //   }

      // if (!FoundNumberPersontype)
      // {
      //     studentNewStudentRemovePrimaryContact(FoundNumberPersontype);
      // }else{
      //     studentNewStudentAddPrimaryContact(FoundNumberPersontype);
      // }

    
}


function newStudentCheckExistingConfirm()
{
    $( "#newStudentCheckExistingConfirm" ).click(function() {
          
        var FoundNumberPersontype  = $('#newStudentFoundPersonTypePhone').val();
        var FoundNumberPersonPhone = $('#newStudentFoundPersonNumberPhone').val();

        $('#Model_NewStudentFoundPerson').modal('hide');

        var newStudentCheckExistingValue = $('#newStudentCheckExistingValue');

            var Persontype       = newStudentCheckExistingValue.attr('Persontype');
            var PersonFieldType  = newStudentCheckExistingValue.attr('PersonFieldType');

            var PersonID         = newStudentCheckExistingValue.attr('PersonID');
            var PersonFirstName  = newStudentCheckExistingValue.attr('PersonFirstName');
            var PersonMiddlename = newStudentCheckExistingValue.attr('PersonMiddlename');
            var PersonLastName   = newStudentCheckExistingValue.attr('PersonLastName');
        
        switch(Persontype) {
            case "1":
                    $('.newStudentFaContainer').hide();
                    
                    $('#fa_foundPerson').val(PersonID);
                    $('#fa_foundInputType').val(PersonFieldType);

                    $('#fa_firstname').val(PersonFirstName);
                    $('#fa_middleName').val(PersonMiddlename);
                    $('#fa_lastName').val(PersonLastName);

                    if (FoundNumberPersontype)
                    {
                        $("#fa_phone_type").select2("val", "0");
                        $('#fa_PhoneNumber').val(FoundNumberPersonPhone);
                    }

                    var newStudentFaFoundContainer = $('#newStudentFaFoundContainer');
                    var content = "<div class=' top-buffer'><center><div class='row'><img src='personimage/100/"+PersonID+"' class='img-circle thumbnail' alt=''></div><div class='row'><h4>"+PersonFirstName+" "+PersonMiddlename+" "+PersonLastName+"</h4></div><div class='row'><p class='info'><a href='#' onclick='newStudentPersonFoundFaChange()' >Change this person</a></p></div></center></div>";
                    newStudentFaFoundContainer.html(content);
                    newStudentFaFoundContainer.slideDown();

                break;

            case "2":
                    $('.newStudentMoContainer').hide();
                    
                    $('#ma_foundPerson').val(PersonID);
                    $('#ma_foundInputType').val(PersonFieldType);

                    $('#ma_firstname').val(PersonFirstName);
                    $('#ma_middlename').val(PersonMiddlename);
                    $('#ma_lastname').val(PersonLastName);

                    if (FoundNumberPersontype)
                    {
                        $("#mo_phone_type").select2("val", "0");
                        $('#mo_PhoneNumber').val(FoundNumberPersonPhone);
                    }

                    var newStudentMaFoundContainer = $('#newStudentMaFoundContainer');
                    var content = "<div class=' top-buffer'><center><div class='row'><img src='personimage/100/"+PersonID+"' class='img-circle thumbnail' alt=''></div><div class='row'><h4>"+PersonFirstName+" "+PersonMiddlename+" "+PersonLastName+"</h4></div><div class='row'><p class='info'><a href='#' onclick='newStudentPersonFoundMaChange()' >Change this person</a></p></div></center></div>";
                    newStudentMaFoundContainer.html(content);
                    newStudentMaFoundContainer.slideDown();

                break;

            case "3":
                    $('.newStudentGuContainer').hide();
                    
                    $('#gu_foundPerson').val(PersonID);
                    $('#gu_foundInputType').val(PersonFieldType);

                    $('#gu_firstname').val(PersonFirstName);
                    $('#gu_middleName').val(PersonMiddlename);
                    $('#gu_lastName').val(PersonLastName);

                    if (FoundNumberPersontype)
                    {
                        $("#gu_phone_type").select2("val", "0");
                        $('#gu_PhoneNumber').val(FoundNumberPersonPhone);
                    }

                    var newStudentGuFoundContainer = $('#newStudentGuFoundContainer');
                    var content = "<div class=' top-buffer'><center><div class='row'><img src='personimage/100/"+PersonID+"' class='img-circle thumbnail' alt=''></div><div class='row'><h4>"+PersonFirstName+" "+PersonMiddlename+" "+PersonLastName+"</h4></div><div class='row'><p class='info'><a href='#' onclick='newStudentPersonFoundGuChange()' >Change this person</a></p></div></center></div>";
                    newStudentGuFoundContainer.html(content);
                    newStudentGuFoundContainer.slideDown();

                break;
        }

      if (!FoundNumberPersontype)
      {
          studentNewStudentRemovePrimaryContact(FoundNumberPersontype);
      }else{
          studentNewStudentAddPrimaryContact(FoundNumberPersontype);
      }

    });
}

function studentNewStudentAddPrimaryContact(Persontype)
{     
       
      var selectStudentNewPrimarycontact = $("#selectStudentNewPrimarycontact");
      switch(Number(Persontype))
      {
          case 1: 
                selectStudentNewPrimarycontact.select2('val', '').trigger('change');
                selectStudentNewPrimarycontact.find('option[value=1]').attr('disabled', false);
            break;

          case 2: //ma
                selectStudentNewPrimarycontact.select2('val', '').trigger('change');
                selectStudentNewPrimarycontact.find('option[value=2]').attr('disabled', false);
            break;

          case 3: //gu
                selectStudentNewPrimarycontact.select2('val', '').trigger('change');
                selectStudentNewPrimarycontact.find('option[value=3]').attr('disabled', false);
            break;

      }

}

function studentNewStudentRemovePrimaryContact(Persontype)
{     
       
      var selectStudentNewPrimarycontact = $("#selectStudentNewPrimarycontact");
      switch(Persontype)
      {
          case 1: //fa
                $("#fa_PhoneNumber").val('');
                selectStudentNewPrimarycontact.select2('val', '').trigger('change');
                selectStudentNewPrimarycontact.find('option[value=1]').attr('disabled', true);
            break;

          case 2: //ma
                $("#mo_PhoneNumber").val('');
                selectStudentNewPrimarycontact.select2('val', '').trigger('change');
                selectStudentNewPrimarycontact.find('option[value=2]').attr('disabled', true);
            break;

          case 3: //gu
                $("#gu_PhoneNumber").val('');
                selectStudentNewPrimarycontact.select2('val', '').trigger('change');
                selectStudentNewPrimarycontact.find('option[value=3]').attr('disabled', true);
            break;

      }

}

function newStudentCheckExistingClose()
{
    $(".newStudentFoundSectionClose").on('click', function() {

        var selectStudentNewPrimarycontact = $("#selectStudentNewPrimarycontact");

        var newStudentCheckExistingValue = $('#newStudentCheckExistingValue');

        var Persontype      = newStudentCheckExistingValue.attr('Persontype');
        var PersonFieldType = newStudentCheckExistingValue.attr('PersonFieldType');

        switch(Persontype)
        {
            case "1": //fa
                switch(PersonFieldType) {
                    case "1":
                        $("#fa_NID").val('');
                    break;

                    case "3":
                        $("#fa_PhoneNumber").val('');
                        selectStudentNewPrimarycontact.select2('val', '').trigger('change');
                        selectStudentNewPrimarycontact.find('option[value=1]').attr('disabled', true);
                    break;

                    case "4":
                        $("#fa_InEmail").val('');
                    break;
                }
            break;

            case "2": //ma
                switch(PersonFieldType) {
                    case "1":
                        $("#mo_NID").val('');
                    break;

                    case "3":
                        $("#mo_PhoneNumber").val('');
                        selectStudentNewPrimarycontact.select2('val', '').trigger('change');
                        selectStudentNewPrimarycontact.find('option[value=2]').attr('disabled', true);
                    break;

                    case "4":
                        $("#mo_InEmail").val('');
                    break;
                 }       
            break;

            case "3": //gu
                switch(PersonFieldType) {
                    case "1":
                        $("#gu_NID").val('');
                    break;

                    case "3":
                        $("#gu_PhoneNumber").val('');
                        selectStudentNewPrimarycontact.select2('val', '').trigger('change');
                        selectStudentNewPrimarycontact.find('option[value=3]').attr('disabled', true);
                    break;

                    case "4":
                        $("#gu_InEmail").val('');
                    break;
                }
            break;

        }
    });
}

function newStudentPersonFoundFaChange()
{
    studentNewStudentRemovePrimaryContact(1);
    var tempScrollTop = $(window).scrollTop();

    $("#newStudentFaFoundContainer").hide('fast');
    $(".newStudentFaContainer").show('slow');

    $(window).scrollTop(tempScrollTop);

    $("#fa_foundPerson").val('');
    $("#fa_PhoneNumber").val('');

    var fa_foundInputType         = $("#fa_foundInputType");
    var fa_foundInputTypeValue    = fa_foundInputType.val();
    

    fa_foundInputType.val('');

    switch(fa_foundInputTypeValue) {
        case "1":
            $("#fa_NID").val('');
        break;
        case "3":
            var selectStudentNewPrimarycontact = $("#selectStudentNewPrimarycontact");
            selectStudentNewPrimarycontact.select2('val', '').trigger('change');
            selectStudentNewPrimarycontact.find('option[value=1]').attr('disabled', true);
        break;

        case "4":
            $("#fa_InEmail").val('');
        break;
    }

}

function newStudentPersonFoundMaChange()
{
    studentNewStudentRemovePrimaryContact(2);
    var tempScrollTop = $(window).scrollTop();

    $("#newStudentMaFoundContainer").hide('fast');
    $(".newStudentMoContainer").show('slow');

    $(window).scrollTop(tempScrollTop);

    $("#ma_foundPerson").val('');
    $("#mo_PhoneNumber").val('');

    var ma_foundInputType         = $("#ma_foundInputType");
    var ma_foundInputTypeValue    = ma_foundInputType.val();

    ma_foundInputType.val('');

    switch(ma_foundInputTypeValue) {
        case "1":
            $("#mo_NID").val('');
        break;

        case "3":
            $("#mo_PhoneNumber").val('');
             var selectStudentNewPrimarycontact = $("#selectStudentNewPrimarycontact");
             selectStudentNewPrimarycontact.select2('val', '').trigger('change');
             selectStudentNewPrimarycontact.find('option[value=2]').attr('disabled', true);
        break;

        case "4":
            $("#mo_InEmail").val('');
        break;
    }
}

function newStudentPersonFoundGuChange()
{   
    studentNewStudentRemovePrimaryContact(3);
    var tempScrollTop = $(window).scrollTop();

    $("#newStudentGuFoundContainer").hide('fast');
    $(".newStudentGuContainer").show('slow');

    $(window).scrollTop(tempScrollTop);

    $("#gu_foundPerson").val('');
    $("#gu_PhoneNumber").val('');

    var gu_foundInputType         = $("#gu_foundInputType");
    var gu_foundInputTypeValue    = gu_foundInputType.val();

    gu_foundInputType.val('');

    switch(gu_foundInputTypeValue) {
        case "1":
            $("#gu_NID").val('');
        break;

        case "3":
            $("#gu_PhoneNumber").val('');
             var selectStudentNewPrimarycontact = $("#selectStudentNewPrimarycontact");
             selectStudentNewPrimarycontact.select2('val', '').trigger('change');
             selectStudentNewPrimarycontact.find('option[value=3]').attr('disabled', true);
        break;

        case "4":
            $("#gu_InEmail").val('');
        break;
    }
    
}

function submitStudentNewPicture()
{
    $('#studentUpdatePictureForm').on('submit', function(e){
        
      $('#studentChangeProfile').modal('toggle');
      
      $('#studentProfilePictureContainer').hide();
      $('#studentProfileLoadingContainer').slideDown();

      var profileStudentId =  $('#profileStudentId').val();

      var imageData = $('.image-editor').cropit('export');
      $('.hidden-image-data').val(imageData);

      var frm = $('#studentUpdatePictureForm');
      var url = base_url+"api/St/Profile_student/updatePicture"; 

      e.preventDefault();
      $.post(url, 
         frm.serialize(), 
         function(data, status, xhr){
                
                if (data.success)
                {   
                    var photoID                       = data.photoID ; 
                    var studentProfilePictureContent  = "<img src='images/student/200/"+profileStudentId+"/"+photoID+"' class='img-circle thumbnail'>";

                    var studentProfilePictureContainer= $('#studentProfilePictureContainer');

                    studentProfilePictureContainer.html(studentProfilePictureContent);

                    $.gritter.add({
                        title: 'Success',
                        text: 'Picture successfuly updated.',
                        class_name: 'success gritter-center',
                        time: ''
                    });

                    studentProfilePictureContainer.slideDown();

                } else {

                    $.gritter.add({
                        title: 'Failed',
                        text: 'Failed to update the picture.',
                        class_name: 'danger gritter-center',
                        time: ''
                    });
                }
                
                $('#studentProfileLoadingContainer').hide();

         },"json");
    });

}

function submitStudentNewGuardian()
{     

    $("#st_newGuardianFormContent").hide();
    $("#st_newGuardianLoding").show();

    var frm = $('#newGurdianStudentForm');
    var url = base_url+"api/St/Students/newGuardian"; 

    $('#studentAddGuadian').modal('toggle');

    $.post(url, 
       frm.serialize(), 
       function(data, status, xhr){

          if (data.success)
          {   
              $.gritter.add({
                  title: 'Success',
                  text: 'Guardian successfuly added.',
                  class_name: 'success gritter-center',
                  time: ''
              });

              var registrationNumber = data.registrationNumber;

              get_profile_student(registrationNumber);

          } else {

              $.gritter.add({
                  title: 'Failed',
                  text: 'Failed to add guardian.',
                  class_name: 'danger gritter-center',
                  time: ''
              });
          }

    },"json");

}

function studentProfileEditNames()
{
    $(document).on("click", "#studentNameContent", function () {

        $('#studentNameContainer').hide();
        $('#studentNameEditContainer').slideDown();

    });
}

function studentProfileEditFatherNames()
{
    $(document).on("click", "#studentFatherNameContent", function () {

        $('#studentFatherNameContainer').hide();
        $('#studentFatherNameEditContainer').slideDown();

    });
}

function studentProfileEditMotherNames()
{
    $(document).on("click", "#studentMotherNameContent", function () {

        $('#studentMotherNameContainer').hide();
        $('#studentMotherNameEditContainer').slideDown();

    });
}

function studentProfileEditGuardianNames()
{
    $(document).on("click", "#studentGuardianNameContent", function () {

        $('#studentGuardianNameContainer').hide();
        $('#studentGuardianNameEditContainer').slideDown();

    });
}

function onStudentGuCheckAvailability()
{
    var gu_NID          = $("#gu_NID");
    var gu_PhoneNumber  = $("#gu_PhoneNumber");
    var gu_InEmail      = $("#gu_InEmail");


    gu_NID.blur(
        function(){
            var gu_NIDValue = gu_NID.val();
            gu_NIDValue     = gu_NIDValue.replace(/[#_-]/g,'');
            newStudentGuCheckExisting  ( 1,'National ID', gu_NIDValue );
        }
    );

    gu_PhoneNumber.blur(
        function(){
            var gu_PhoneNumberValue = gu_PhoneNumber.val();
            gu_PhoneNumberValue     = gu_PhoneNumberValue.replace(/[#_-]/g,'');
            newStudentGuCheckExisting  ( 3,'Phone Number', gu_PhoneNumberValue );

        }
    );

    gu_InEmail.blur(
        function(){
            var gu_emailValue = gu_InEmail.val();
            newStudentGuCheckExisting  ( 4,'Email', gu_emailValue );
        }
    );
}

function newStudentGuCheckExistingConfirm()
{
    $( "#studentNewGuCheckExistingConfirm" ).click(function() {

        $( ".studentNewGuContainer" ).show();
        $( "#studentNewGuOtherField" ).hide();

        $( ".studentNewGuRemoveConfirm" ).hide();

        $('#newGuardianSubmitBtn').removeAttr('disabled');

        $('#studentNewGuChangePerson').html("<div class='row'><center><p class='info'><a href='#' onclick='studentNewGuFoundChange()' >Change this person</a></p></center></div>")

    });

}

function studentNewGuFoundChange()
{
    $("#studentNewGuFoundContainer").hide();
    $(".studentNewGuContainer").show();
    
    var gu_foundInputType = $('#gu_foundInputType').val();
    $('#gu_foundPerson').val('');

    switch(gu_foundInputType) {
          case '1':
              $('#gu_NID').val('');
              break;
          case '3':
              $('#gu_PhoneNumber').val('');
              break;
          case '4':
              $('#gu_InEmail').val('');
              break;
    }
}

function newStudentGuCheckExistingClose()
{
    $( "#studentNewGuFoundSectionClose" ).click(function() {

      $('#newGuardianSubmitBtn').removeAttr('disabled');
      $("#studentNewGuFoundContainer").hide();
      $(".studentNewGuContainer").show();
      
      var gu_foundInputType = $('#gu_foundInputType').val();
      $('#gu_foundPerson').val('');

      console.log(typeof gu_foundInputType );

      switch(gu_foundInputType) {
            case '1':
                $('#gu_NID').val('');
                break;
            case '3':
                $('#gu_PhoneNumber').val('');
                break;
            case '4':
                $('#gu_InEmail').val('');
                break;
      }

    });
}

function newStudentGuCheckExisting( field, FieldName ,ProvidedValue)
{

  var newGuardianSubmitBtn =  $('#newGuardianSubmitBtn'); 
  newGuardianSubmitBtn.attr('disabled', 'disabled');

  ProvidedValue = ProvidedValue.trim();

      if (field == 3 )
      {
          var NumberCountry    = $("#selectNewStudentGuNumberCountry").select2('val');
          var PhoneNumberValue = $("#gu_PhoneNumber").val().replace(/[#_-]/g,'');

      }else{

          var NumberCountry    = '';
          var PhoneNumberValue = '';
      }


    if ( ProvidedValue.length > 0 )
     {
        var url = base_url+"api/St/Students/checkExisting"; 
        
        var container = $('#studentNewGuFoundContainer');
        var content   = '';

        $.getJSON( url, { DataType: 3 , DataField: field , DataValue: ProvidedValue , NumberCountry: NumberCountry  ,PhoneNumberValue: PhoneNumberValue }, function(data) {

            var Found         = data.Found;
            var PersonInfo    = data.Person;

            if ( Found ) {

                $('#gu_foundInputType').val(field);
                $('#gu_foundPerson').val(PersonInfo.id);

                content +="<h4 class='text-center'> <span class='studentNewGuRemoveConfirm'>Do you mean </span> <strong>"+PersonInfo.firstname+" "+PersonInfo.middleName+" "+PersonInfo.lastName+"</strong> <span class='studentNewGuRemoveConfirm'>?</span></h4>";
                content +="<div class='top-buffer'><center><div class='row'><img src='personimage/100/"+PersonInfo.id+"' class='img-circle thumbnail' alt=''></div></center></div>";
                content +="<div class='row studentNewGuRemoveConfirm' ><center><strong><p >"+ProvidedValue+"</p></strong></center></div>";
                content +="<div class='row' id='studentNewGuChangePerson'></div>";
                content +="<div class='row studentNewGuRemoveConfirm' ><center><button type='button' class='btn btn-default' id='studentNewGuFoundSectionClose' >No thanks</button>&nbsp;&nbsp;<button type='button' id='studentNewGuCheckExistingConfirm' class='btn btn-warning'>Yes</button></center></div>";

                container.html( content );

                newStudentGuCheckExistingConfirm();
                newStudentGuCheckExistingClose();

                $("#studentNewGuFoundContainer").show();
                $(".studentNewGuContainer").hide();

            }else{
                console.log("enable submit");
            }

        })
        .done(function() {

        })
        .fail(function() {
        })
        .always(function() {
          newGuardianSubmitBtn.removeAttr('disabled');

        });

     }

}

function submitStSaveNewArrival()
{
    //validation
    var formDashTeNewArrival = $('#formDashTeNewArrival');
    formDashTeNewArrival.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                NewArrivalStudent: {
                    validators: {
                        notEmpty: {
                            message: 'Student is required'
                        }
                    }
                },
                NewArrivalDate: {
                    validators: {
                        notEmpty: {
                            message: 'Arrival Date is required'
                        }
                    }
                },
                NewArrivalTimeHour: {
                    validators: {
                        notEmpty: {
                            message: 'Arrival Hour is required'
                        }
                    }
                },
                NewArrivalTimeMin: {
                    validators: {
                        notEmpty: {
                            message: 'Arrival Minutes is required'
                        }
                    }
                }
            }
        })
    .on('success.form.fv', function(e , data) {

        e.preventDefault();

        dashStSubmitValidatedNewArrival();

     }).find('[name="NewArrivalDate"]').change(function(e) {
         formDashTeNewArrival.formValidation('revalidateField', 'NewArrivalDate');
                                                           
     }).end();

}

function dashStSubmitValidatedNewArrival()
{

    var DashStNewArrivalLoding = $( "#DashStNewArrivalLoding" );
    var formDashTeNewArrival   = $( "#formDashTeNewArrival" );

    DashStNewArrivalLoding.show();
    formDashTeNewArrival.hide();

    var frm = formDashTeNewArrival;
    var url = base_url+"api/St/StudentArrival"; 

    $.post(url, 
       frm.serialize(), 
       function(data, status, xhr){

        if ( data.success )
        {   

          $('#Model_NewArrival').modal('toggle');
          $.gritter.add({
              title: 'Success',
              text:  'Arrival saved',
              class_name: 'success gritter-center',
              time: '' 
          });

          formDashTeNewArrival.show();
          DashStNewArrivalLoding.hide();

          DashStStudentArrivalsLoadAnalytics();

          $('#NewArrivalStudentSelect').select2('data', null);
          $('#NewArrivalDate').val(null);
          $('#NewArrivalTimeHour').val(null).trigger("change");
          $('#NewArrivalTimeMin').val(null).trigger("change");
          $("#NewArrivalSendSMSYes").prop("checked", true)
          
        } else {

            formDashTeNewArrival.show();
            DashStNewArrivalLoding.hide();
        
            $.gritter.add({
              title: 'Failed',
              text: 'Failed to add student Arrival',
              class_name: 'danger gritter-center',
              time: '' 
            });
        }
             
     },"json")
      .done(function() {
      })
      .fail(function() {  

          formDashTeNewArrival.show();
          DashStNewArrivalLoding.hide();

          $.gritter.add({
            title: 'Failed',
            text: 'Something went wrong, Please Try Again',
            class_name: 'danger gritter-center',
            time: ''
          });

      })
      .always(function() {});
}

function st_List(MinAge, MaxAge)
{ 


      var LowestNumber = MinAge - ( MinAge%5 ) ;
      var UpperNumber  = MaxAge + ( MaxAge%5 ) - 1 ;

      var AgeScale = [] ;

      var i = LowestNumber 

      while( i <= UpperNumber  )
      {

          if ( i == LowestNumber ) 
          {
            AgeScale.push(i);

          }else if( i == UpperNumber ){

            AgeScale.push('|');
            AgeScale.push( i );

          }else{
             AgeScale.push('|');
             AgeScale.push( i );
          }

          i = i+5 ;
      }

      var AgeScale2 = "[0, '|', 10, '|', 20, '|', 30, '|', 40 , '|', '50', '|', 60, '|', 70, '|', 80 , '|', 90, '|', 100 ]";

    $("#STList_Year").hide();
    $("#STList_Combination").hide();
    $("#STList_Class").hide();

    $("#STList_province").hide();
    $("#STList_district").hide();

    $("#DashStListTypeSelect").select2();
    $("#DashStListLevelSelect").select2().bind('change', OnStListSchoolLevelChange );
    $("#DashStListYearSelect").select2().bind('change', OnStListYearChange );
    
    $("#DashStListCombinationSelect").select2().bind('change', OnStListCombinationChange );

    $("#DashStListClassSelect").select2({ 
            closeOnSelect: false,
            formatResult: newStudentformatResult,
            formatSelection: newStudentformatSelection
    });

    $("#STList_SelectCountry").select2().bind('change', function() { populateProvince('STList_'); }).trigger('change');
    $("#STList_SelectProvince").select2().bind('change', function() { populateDistrict('STList_'); }).trigger('change');
    $("#STList_SelectDistrict").select2();

    jQuery("#DashStListAgeRange").slider({   
      from: LowestNumber  ,
      to: UpperNumber ,
      step: 1 ,
      scale: AgeScale ,
      dimension: '&nbsp;years',
      skin: "blue"
    });
}

function OnStListSchoolLevelChange()
{
      var selected              = $("#DashStListLevelSelect").select2('val');
      var LevelFirstYear        = $("#DashStListLevelSelect").select2().find(":selected").data("firstyear");
      var LevelLastYear         = $("#DashStListLevelSelect").select2().find(":selected").data("lastyear");
      var Yearname              = $("#DashStListLevelSelect").select2().find(":selected").data("yearname");

      var DashStListYearName    = $("#DashStListYearName") ;

      var STList_Year           = $("#STList_Year") ;
      var DashStListYearSelect  = $("#DashStListYearSelect") ;

      var STList_Class          = $("#STList_Class");
      var STList_Combination    = $("#STList_Combination");

      STList_Class.slideUp();
      STList_Combination.slideUp();

      if ( selected > 0 ) 
      {
        STList_Year.slideDown();

        DashStListYearName.html(Yearname);

         //Add Year Options 
        var LevelYearOptions = '';
        LevelYearOptions    += '<option ></option>';

        for ( var i = LevelFirstYear ; i <= LevelLastYear ; i++ ) {
          
            LevelYearOptions += '<option value='+i+'>'+i+'</option>';

        }

        STList_Year.slideDown();
        DashStListYearSelect.empty();
        DashStListYearSelect.html( LevelYearOptions );

      }else{
        STList_Year.slideUp();
        DashStListYearName.html("Year");
      }

   

}

function OnStListYearChange()
{

      //
        var selectedYear          = $("#DashStListYearSelect").select2('val');
        var selectedLevel         = $("#DashStListLevelSelect").select2('val');

        var STList_Class          = $("#STList_Class");
        var DashStListClassSelect = $("#DashStListClassSelect");

        DashStListClassSelect.empty();

      //
        var STList_Combination    = $("#STList_Combination");
        if ( selectedLevel == 3 && selectedYear >= 4 )
         {
           STList_Combination.slideDown();
           STList_Class.slideUp();

         }else{

            STList_Combination.slideUp();
            STList_Class.slideDown();

        //Add Class Options 
          var SchoolLevelClasses        = GroupedStListClasses[selectedLevel][selectedYear][0];

          var SchoolLevelClassesLenght  = SchoolLevelClasses.length;

          var ClassOptions = '';
          ClassOptions +='<option ></option>';

          var classidentifier = "";
          var isTheFirsRecord = true ;

          for ( var i = 0; i < SchoolLevelClassesLenght ; i++) {
              
                var ClassID           = SchoolLevelClasses[i]['ClassID'];
                var YearName          = SchoolLevelClasses[i]['YearName'];
                var ClassidentifierID = SchoolLevelClasses[i]['ClassidentifierID'];
                var ClassName         = SchoolLevelClasses[i]['ClassName'];
                var ClassAcScLevelID  = SchoolLevelClasses[i]['ClassAcScLevelID'];
                var ClassLevelID      = SchoolLevelClasses[i]['ClassLevelID'];

                if ( ClassidentifierID == classidentifier  ) {

                  ClassOptions += '<option value="'+ClassID+'" data-yearname="'+YearName+'" data-level="'+ClassLevelID+'" >'+ClassName+'</option>';

                }else{

                  classidentifier = ClassidentifierID;

                  if ( isTheFirsRecord ) {
                    isTheFirsRecord = false;

                  }else{
                    ClassOptions += '</optgroup>';

                  }

                  ClassOptions += '<optgroup label="'+YearName+'">';
                  ClassOptions += '<option value="'+ClassID+'" data-yearname="'+YearName+'" data-level="'+ClassLevelID+'" >'+ClassName+'</option>';
                
                }
                  
        }
          
        DashStListClassSelect.html(ClassOptions);

    }
}

function OnStListCombinationChange()
{

  var selectedLevel         = $("#DashStListLevelSelect").select2('val');
  var selectedYear          = $("#DashStListYearSelect").select2('val');
  var selectedALevelOption  = $("#DashStListCombinationSelect").select2('val');

  var STList_Class          = $("#STList_Class");

  var DashStListClassSelect = $("#DashStListClassSelect");

  DashStListClassSelect.empty();

  if ( selectedALevelOption > 0  ) 
  {

  //
    var STList_Combination    = $("#STList_Combination");
    if ( selectedLevel == 3 && selectedYear >= 4 )
    {

    //Add Class Options 
    var SchoolLevelClasses        = GroupedStListClasses[selectedLevel][selectedYear][selectedALevelOption];
    var SchoolLevelClassesLenght  = SchoolLevelClasses.length;

    var ClassOptions = '';
    ClassOptions +='<option ></option>';

    var classidentifier = "";
    var isTheFirsRecord = true ;

    for ( var i = 0; i < SchoolLevelClassesLenght ; i++) {
        
          var ClassID           = SchoolLevelClasses[i]['ClassID'];
          var YearName          = SchoolLevelClasses[i]['YearName'];
          var ClassidentifierID = SchoolLevelClasses[i]['ClassidentifierID'];
          var ClassName         = SchoolLevelClasses[i]['ClassName'];
          var ClassAcScLevelID  = SchoolLevelClasses[i]['ClassAcScLevelID'];
          var ClassLevelID      = SchoolLevelClasses[i]['ClassLevelID'];

          if ( ClassidentifierID == classidentifier  ) {

            ClassOptions += '<option value="'+ClassID+'" data-yearname="'+YearName+'" data-level="'+ClassLevelID+'" >'+ClassName+'</option>';

          }else{

            classidentifier = ClassidentifierID;

            if ( isTheFirsRecord ) {
              isTheFirsRecord = false;

            }else{
              ClassOptions += '</optgroup>';

            }

            ClassOptions += '<optgroup label="'+YearName+'">';
            ClassOptions += '<option value="'+ClassID+'" data-yearname="'+YearName+'" data-level="'+ClassLevelID+'" >'+ClassName+'</option>';
          
          }
                      
        }
            
      DashStListClassSelect.html(ClassOptions);

     }

     STList_Class.slideDown();

  }else{

    STList_Class.slideUp();

  }

}

function get_st_studentsList()
{

    $("#dashContentainer_Students").show();
    $("#dashStudents_content").show();

    $("#dashStudents_contentAnalytics").hide();
    $("#dashStudents_contentProfile").hide();
    $("#dashNew_content").hide();
    $("#dashStListLoding").hide();

    $("#dashStudents_contentLists").show();
}
  
function submit_st_studentList()
{
  $('#formDashStLists').on('submit', function(e){

    var container        = $("#dashStListContainer");
    var dashStListLoding = $("#dashStListLoding");
    
    container.hide();
    dashStListLoding.slideDown();

    $("#dashStListLoadingText").html("Loading...");
    
    var frm = $('#formDashStLists');
    var url = base_url+"api/St/Students/StudentList"; 
    
    var content   = '';

        e.preventDefault();
        $.post(url, 
           frm.serialize(), 
           function( data, status, xhr ){

            container.empty();

        //     content +='<div class="row"><div class="col-sm-12">';
        //       content +='<a class="btn btn-default pull-right" id="DasAnGrStudentListPrint"><i class="fa fa-print"></i>&nbsp;&nbsp;Print</a>&nbsp;&nbsp;&nbsp;';
        //       content +='<a class="btn btn-default pull-right" data-id="0" data-toggle="modal" data-target="#Model_DashGrAnMessage" id="DasAnGrStudentSendMessage" ><i class="fa fa-envelope"></i>&nbsp;&nbsp;Notify parents about this</a>';
        //     content +='</div></div>';

        //     content +='<div class="row"><div class="col-sm-12">';
        //     content +='<table class="table" id="dashGrAnTable" ><thead><tr><th>Student Name</th><th class="text-center">Registration Number</th><th class="text-center">Percentage</th></thead><tbody>';
            
        //   $.each(data, function(i, item) {

        //         content +="<tr id='"+item.studRegNber+"' >";
        //           content +='<td >'+item.studentNames+'</td>';
        //           content +='<td class="text-center">'+item.studRegNber+'</td>';
        //           content +='<td class="text-right">'+item.score+'%</td>';
        //         content +="</tr>";

        //     }); 

        //     content +='</tbody></table>';
        //   content +='</div></div>';

        // container.html(content);

        // dashStListLoding.hide();
        // container.slideDown();
        // dashGrAnTableData = $('#dashGrAnTable').dataTable({
        //     "aaSorting": [],
        //       language: {
        //         searchPlaceholder: "Search here... "
        //     }
        // });
        // $( "#Model_DashGrAnMessage" ).on('shown.bs.modal', function (e) {
        //   $( "#Model_DashGrAnMessageInfo" ).html(dashGrAnTableData.fnSettings().fnRecordsTotal());
        //   var users = [
        //     {username: 'StudentName', fullname: "Replace this with student's name in each message"},
        //     {username: 'StudentPercentage', fullname: "Replace this with student's Percentage in each message"}
        //   ];
        //   $('#DashGrAnContent').suggest('@', {
        //     data: users,
        //     map: function(user) {
        //       return {
        //         value: user.username,
        //         text: '<strong>'+user.username+'</strong> <small>'+user.fullname+'</small>'
        //       }
        //     }
        //   });
        // });

      });

    });
}

function studentResetPassword( StudentPersonID, registrationNumber ){

      var studentResetPasswordText = $("#studentResetPasswordText");
      studentResetPasswordText.html("Resetting student's password...");

      var url = base_url+"api/St/updateProfile?id="+StudentPersonID+"&registrationNumber="+registrationNumber+"&editInputType=34$value=1"; 

      $.post(url, 
         function(data, status, xhr){
              
              if ( data.success )
              {   
                $.gritter.add({
                    title: 'Success',
                    text: 'Student\'s password successfuly reset.',
                    class_name: 'success gritter-center',
                    time: ''
                });

                studentResetPasswordText.html('<span class="label label-success"> Was Reset</span>');

              } else {
                  
                  $.gritter.add({
                    title: 'Failed',
                    text: 'Something went wrong' ,
                    class_name: 'warning gritter-center',
                    time: ''
                  });

                  studentResetPasswordText.html('<span class="label label-success" > Reset Student\'s Password</span>');
              }
                 
         },"json")
          .done(function() { 

          })
          .fail(function() {

              $.gritter.add({
                title: 'Failed',
                text: 'Failed to reset student password, Please Try Again',
                class_name: 'danger gritter-center',
                time: ''
              });

              studentResetPasswordText.html('<span class="label label-success" > Reset Student\'s Password</span>');
          })
          .always(function() {
            
          });

    }


   function submitDeleteStudent( reg_nber )
  {

    var formDeleteStudent                = $("#formDeleteStudent");

    var deleteStudentLoadingContainer    = $("#deleteStudentLoadingContainer");
    var deleteStudentMessageContainer    = $("#deleteStudentMessageContainer");

    deleteStudentLoadingContainer.hide();
    deleteStudentMessageContainer.show();

    formDeleteStudent.on('submit', function(e){


      deleteStudentLoadingContainer.slideDown();
      deleteStudentMessageContainer.hide();
      
      var frm = formDeleteStudent;
      var url = base_url+"api/St/Students/"+reg_nber ; 
      e.preventDefault();

      $.ajax({
            url: url,
            type: 'DELETE',
            success: function(data) {
              
              deleteStudentLoadingContainer.hide();
              deleteStudentMessageContainer.show();
              
              $('#model_DeleteStudent').modal('toggle');

               if ( data.success )
               {
                    $.gritter.add({
                      title: 'Success',
                      text: 'Student deleted.',
                      class_name: 'success gritter-center',
                      time: ''
                    });

                   dashOnTabSelectedStudents();

               }else{

                 $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to delete student, There might some critical data associated with student.',
                    class_name: 'danger gritter-center',
                    time: ''
                  });

             }
        },
        error: function(){
       
            deleteStudentLoadingContainer.hide();
            deleteStudentMessageContainer.show();

            $.gritter.add({
              title: 'Failed',
              text: 'Failed to delete student .',
              class_name: 'danger gritter-center',
              time: ''
            });
        }

      });
    
    });

  }

function onStStudentProfileEnroll()
{
  $('#newEnrollStudentForm').formValidation({
      
        framework: 'bootstrap',
        excluded: [':disabled'],
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
          enroll_class: {
                validators: {
                    notEmpty: {
                        message: 'Class is required'
                    }
                }
            },
            boarding : {
                validators: {
                    notEmpty: {
                        message: 'Boarding Status is required'
                    }
                }
            }
        }

      })
      .on('success.field.fv', function(e, data) {

            if (data.fv.getInvalidFields().length <= 0) {   
                data.fv.disableSubmitButtons(false);
            }

      })
      .on('success.form.fv', function( e ) {

          e.preventDefault();
          submitStStudentProfileEnroll();

      });
}

function submitStStudentProfileEnroll(){

   //Hide Your 
    $('#modal_studentEnrollInClass').modal('toggle');

    var newEnrollStudentForm  = $("#newEnrollStudentForm");

    $.ajax({
      url: base_url+"api/St/updateProfile?id=0&editInputType=36&value=0" ,
      dataType: 'json',
      type: 'POST',
      data: newEnrollStudentForm.serialize(),
      success: function ( res ) {

        console.log(res);

        if ( res.success )
        {

              $.gritter.add({
                  title: 'Success',
                  text: 'Student Enrolled.',
                  class_name: 'success gritter-center',
                  time: ''
              });
              
              var studentEnroll_regNumber = $("#studentEnroll_regNumber");
              get_profile_student( studentEnroll_regNumber.val() );

        }else{  

              $.gritter.add({
                  title: 'Failed',
                  text: 'Failed to enroll a student.',
                  class_name: 'danger gritter-center',
                  time: ''
              });

              var studentEnroll_regNumber = $("#studentEnroll_regNumber");
              get_profile_student( studentEnroll_regNumber.val() );

        }

      }

    }).done(function() {


    }).fail(function() {

      $.gritter.add({
                  title: 'Failed',
                  text: 'Failed to enroll a student.Please try again',
                  class_name: 'danger gritter-center',
                  time: ''
              });

      var studentEnroll_regNumber = $("#studentEnroll_regNumber");
      get_profile_student( studentEnroll_regNumber.val() );

    })
    .always(function() {
    
    });

}

function DashStudPerAllPermission()
{
	console.log("DashStudPerAllPermission");
	
	$( "#dashContentainer_Displine").slideDown();
    var container = $( "#dashDispline_content" );

    container.empty();

    var url = base_url+"api/StudPer/StudentPermission";
    content = '';

    $.getJSON( url, function(data) {

        content = '<div class="col-sm-12">';
        content += '<div class="row"><div class="col-sm-12 text-center"><h4><strong>All Given Student Permissions</strong></h4></div></div>'; 
        content += '<div class="row"><table class="table table-striped" id="dashStudPerTable" ><thead><tr><th class="text-center" style="width: 10%;">Date</th><th style="width: 25%;">Name</th><th style="width: 10%;" class="text-center">Reg. Number</th><th style="width: 10%;" class="text-center">From</th><th style="width: 10%;" class="text-center">To</th><th style="width: 10%;" class="text-center">Where</th><th style="width: 15%;" class="text-center">Reason</th></tr></thead><tbody>'; //<th style="width: 10%;" class="text-center">Action</th>
    
        $.each(data, function(i, item) {

            content +="<tr id='"+item.id+"'>"; 
              content +="<td style='width: 10%;' class='text-center'>"+date_HM_moment(item.created_at)+"</td>";
              content +="<td style='width: 25%;' class='text-left'>"+item.studentNames+"</td>";
              content +="<td style='width: 10%;' class='text-center'>"+item.studRegNber+"</td>";
              content +="<td style='width: 10%;' class='text-center'>"+date_HM_moment(item.FromDateTime)+"</td>";
              content +="<td style='width: 10%;' class='text-center'>"+date_HM_moment(item.ToDateTime)+"</td>";
              content +="<td style='width: 10%;' class='text-center'>"+item.ToWhere+"</td>";
              content +="<td style='width: 15%;' class='text-left'>"+item.Reason+"</td>";
              //content +="<td style='width: 10%;' class='row text-center'><a target='_blank' href='PDF/StudentPermission/'><i class='fa fa-print text-green'></i> Print</a></td>";
            content +="</tr>";

        });
      
         content +='</tbody></table></div>';
        content +='</div>';
      
    }).done(function() {

        container.append( content );
        abTablePagingTable('dashStudPerTable');

        //DiDeductionTableClicked();

    })
    .fail(function() {
    })
    .always(function() {
    });

} 

function submitStSaveNewStudentPermission()
{
    //validation
    var formNewStudentPermission = $('#formNewStudentPermission');
    formNewStudentPermission.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                NewStudPerSelectStudent: {
                    validators: {
                        notEmpty: {
                            message: 'Student is required'
                        }
                    }
                },
                NewStudPerWhere: {
                    validators: {
                        notEmpty: {
                            message: 'Where the student is going is required'
                        }
                    }
                },
                NewStudPerReason: {
                    validators: {
                        notEmpty: {
                            message: 'The reason is required'
                        }
                    }
                },
                NewStudPerValidPeriod: {
                    validators: {
                        notEmpty: {
                            message: 'When the permission is given is required'
                        }
                    }
                }
            }
        })
    .on('success.form.fv', function(e , data) {

        e.preventDefault();
        submitValidatedNewStudentPermission();

     }).find('[name="NewStudPerValidPeriod"]').change(function(e) {
         formNewStudentPermission.formValidation('revalidateField', 'NewStudPerValidPeriod');
                                                           
     }).end();

}

function submitValidatedNewStudentPermission()
{
    console.log("dashStSubmitValidatedNewArrival"); 

    var NewStudentPermissionLoading = $( "#NewStudentPermissionLoading" );
    var formNewStudentPermission    = $( "#formNewStudentPermission" );

    NewStudentPermissionLoading.show();
    formNewStudentPermission.hide();

    var frm = formNewStudentPermission;
    var url = base_url+"api/StudPer/StudentPermission"; 

    $.post(url, 
       frm.serialize(), 
       function(data, status, xhr){

        if ( data.success )
        {   

          $('#Model_StudPerNewPermission').modal('toggle');
          $.gritter.add({
              title: 'Success',
              text:  'Students permission added',
              class_name: 'success gritter-center',
              time: '' 
          });

          formNewStudentPermission.show();
          NewStudentPermissionLoading.hide();

          dashOnTableSelectedStudentPermission();

          $("#NewStudPerSelectStudent").each(function () { //added a each loop here
  	        $(this).select2('val', '');
  	      });

          //$('#NewStudPerSelectStudent').empty().trigger('change');
          $('#NewStudPerWhere').val(null);
          $('#NewStudPerReason').val(null);
          $('#NewStudPerValidPeriod').val(null);
          $('#NewStudPerValidPeriod').val(null);
          $("#NewStudPerSendSMSYes").prop("checked", true);

          // $('#NewArrivalTimeHour').val(null).trigger("change");
          // $('#NewArrivalTimeMin').val(null).trigger("change");
          
        } else {

            formNewStudentPermission.show();
            NewStudentPermissionLoading.hide();
        
            $.gritter.add({
              title: 'Failed',
              text: 'Failed to add student permission',
              class_name: 'danger gritter-center',
              time: '' 
            });
        }
             
     },"json")
      .done(function() {
      })
      .fail(function() {  

          formNewStudentPermission.show();
          NewStudentPermissionLoading.hide();

          // $.gritter.add({
          //   title: 'Failed',
          //   text: 'Something went wrong, Please Try Again',
          //   class_name: 'danger gritter-center',
          //   time: ''
          // });

      })
      .always(function() {});
}

var rd = null;

function loadTtTimetableDash()
{

    var container          = $( "#dashTimetable_content" ); 
    container.empty();

     var url = base_url+"api/Tt/schoolTimetable";

      $.getJSON( url, function(data) {

            var content = '<div class="col-sm-12"><div class="col-sm-12">';
                content += '<div class="row"><div class="col-sm-12 text-center"><h4><strong>Generated timetable versions</strong></h4></div></div>'; 
                
                content += '<div class="row"><div class="col-sm-8 col-sm-offset-2"><table class="table table-striped" id="dashTtTimetableVerions" ><thead><tr><th>Generated on</th><th>Version Name</th><th class="text-center">Download Timetable</th></tr></thead><tbody>';
              
                $.each(data, function(i, item) {

                    content +="<tr id="+item.id+" >";
                        content +="<td class='row text-left'>"+date_HM_moment(item.created_at)+"</td>";
                        content +="<td class='row text-left'>"+item.name+"</td>";
                        content +="<td class='row text-center'><a target='_blank' href='api/Tt/classTimeTable/"+item.id+"?class_id=0'><i class=' fa fa-download text-green'></i> All Classes</a>&nbsp;&nbsp;<a target='_blank' href='api/Tt/teacherTimeTable/"+item.id+"'><i class=' fa fa-download text-green'></i> All Teachers</a></td>";
                    content +="</tr>";

                });
                
                content +='</tbody></table></div></div>';
            content +='</div>';

        container.html(content); 
        

      }).done(function() {
        
        $( "#dashTimetable_content" ).slideDown();
        abTableNoPaging('dashTtTimetableVerions');

      })
      .fail(function() {
      })
      .always(function() {
      });
}

function  onTTViewClassTimeTable( ) {

    var dashboardTTSelectClass  = $("#dashboardTTSelectClass");
    var selected                = dashboardTTSelectClass.select2('val');

    $( "#dashContentainer_Timetable").slideDown();
    var container = $( "#dashTimetable_content" );

    container.html('<center><img src="../packages/assets/plugins/img/loading.gif" alt="Loading students..." > Loading Class Timetable...</center>');

    var url = base_url+"api/Tt/schoolTimetable/"+selected+"?TimetableViewType=1";

     /* enable strict mode */
        "use strict";

        // create redips container
        var redips = {},        // redips container
            rd = REDIPS.drag,   // reference to the REDIPS.drag library
            counter = 0,        // counter for cloned DIV elements
            clonedDIV = false,  // cloned flag set in event.moved
            lastCell;


    // REDIPS.drag initialization
   

     $.get( url , function( data ) {
        
        container.html( data );

        redips.init = function () {
          REDIPS.drag.init();
        };

        var rd = REDIPS.drag, msg = $( "#timetable_message" );

        rd.init();
        rd.dropMode = 'switch';

        rd.event.clicked = function () {
            //msg.append('</br>Clicked');
        };
        rd.event.dblClicked = function () {
            //msg.append('</br> Dblclicked');
        };
        rd.event.moved  = function () {
            msg.html('<div class="alert alert-warning"><strong>Moving...</strong></div>');
        };
        rd.event.notMoved = function () {
            //msg.append('</br> Not moved');
        };
        rd.event.dropped = function () {
            var pos = rd.getPosition();
            // msg.append('</br> '+pos+'');
            // msg.append('</br> Dropped');

            msg.html('<div class="alert alert-info">You can drag <b>subject</b> to any period you want and the system will check for you if the <b>subject teacher is available</b>!</div>');
            
        };
        rd.event.droppedBefore = function () {

            var pos = rd.getPosition();
            // msg.append('</br> --------------'+pos+'---------------');
            // msg.append('</br> droppedBefore');

            msg.html('<div class="alert alert-warning"><strong>Checking for any conflict in timetable then save...</strong></div>');

            parameter_array = pos.toString().split(',');

            var to_row    = TimeTableRowPeriod[parameter_array[1]] ;
            var to_col    = parameter_array[2] ;

            var from_row  = TimeTableRowPeriod[parameter_array[4]] ;
            var from_col  = parameter_array[5] ;

            var url = base_url+"api/Tt/schoolTimetable/"+ClassID+"?scheduleVersion="+Latest_ScheduleVersion+"&from_row="+from_row+"&from_col="+from_col+"&to_row="+to_row+"&to_col="+to_col ;
            var cancel = false;

            var error_message = '';

            $.ajax({
              url: url,
              async: false,
              dataType: 'json',
              type: 'PUT', 
              data: null,
              success: function ( res ) {

                if ( res.success) {
                    cancel = false;

                }else{
                    cancel = true;
                    error_message = res.error_message;
                }
               

              }

            }).done(function() { 

            }).fail(function() {
                error_message  = "Please check your internet and try again!";

            }).always(function() {
                
            });

            if ( cancel) {
                 
                $('#TtUpdateTimetableErrorErrorMessage').html(error_message);
                $('#Model_DashTtUpdateTimetableError').modal('show');

                return false;

            }else{

                $.gritter.add({
                    title: 'Success',
                    text: 'Successfully moved.',
                    class_name: 'success gritter-center',
                    time: '2000'
                });

            }
            
        };
        

        rd.event.switched = function () {
            //msg.append('</br> Switched');
            // REDIPS.drag.relocate("id_Mathematics", "id_Biology", "animation");
        };
        rd.event.clonedEnd1 = function () {
            //msg.append('</br> Cloned end1');
        };
        rd.event.clonedEnd2 = function () {
           //msg.append('</br> Cloned end2');
        };
        rd.event.notCloned = function () {
           //msg.append('</br> Not cloned');
        };
        rd.event.deleted = function (cloned) {
            // if cloned element is directly moved to the trash
            if (cloned) {
                // set id of original element (read from redips property)
                // var id_original = rd.obj.redips.id_original;
                //msg.append('</br> Deleted (c)');
            }
            else {
                //msg.append('</br> Deleted');
            }
        };
        rd.event.undeleted = function () {
            //msg.append('</br> Undeleted');
        };
        rd.event.cloned = function () {
            // display message
            //msg.append('</br> Cloned');
            // append 'd' to the element text (Clone -> Cloned)
            rd.obj.innerHTML += 'd';
        };
        rd.event.changed = function () {
            // get target and source position (method returns positions as array)
            var pos = rd.getPosition();
            // display current row and current cell
            //msg.append('</br> Changed: ' + pos[1] + ' ' + pos[2]);
        };


    }).done(function() {

    }).fail(function() {

    })
    .always(function() {
       $("#dashboardTTSelectClass").select2('data', null);
    });

}

function onTTViewTeacherTimeTable()
{
    var dashboardTTSelectTeacher  = $("#dashboardTTSelectTeacher"); 
    var selected                  = dashboardTTSelectTeacher.select2('val');

    console.log("onTTViewTeacherTimeTable selected: "+selected);
}

function get_tt_SubjectGroups()
{

    $( "#dashContentainer_Timetable").slideDown();
    var container = $( "#dashTimetable_content" );

    container.html('<center><img src="../packages/assets/plugins/img/loading.gif" alt="Loading students..." > Loading subject groups...</center>');

    var NewSubjectGroup_ClassSubjects   = null;
    var EditStudyGroup_Teachers         = null;

    var url = base_url+"api/Tt/studyGroups";

    $.get( url , function( data ) {
        
        var content = '';
         content += '<div class="row"><div class="col-sm-3 text-center"><button type="button" class="btn btn-primary" onclick="get_TtMergeSubjects()"><strong><i class="fa fa-link"></i> Group Subjects</strong></button></div><div class="col-sm-9 text-center"><h4> Subject Groups</h4></div></div>';
         //content += '<div class="row"><div class="col-sm-12 text-center"><h4> Subject Groups</h4></div></div>';
            content += '<div class="row">';
                content += '<div class="col-sm-10 col-sm-offset-1">';
                content += '<table class="table table-striped abDataTable" id="dashTtStudyGroupTable" >';
                content += '<thead><tr><th>Subject</th><th>Teacher</th><th class="text-center" >Total periods</th><th class="text-center" >Total periods per week</th><th class="text-center">Double periods</th><th>Show on timetable</th></thead>';
                content += '<tfoot><tr><th colspan="3" style="text-align:right">Total :</th><th style="text-align:center"></th><th colspan="2" style="text-align:right"></th></tr></tfoot>'; 
                content += '<tbody>';

                var StudyGroups = data.StudyGroups;

                $.each( StudyGroups , function(i, item) {

                    content +='<tr id='+item.id+'>';
                      content +='<td class="TtStudyGroupManageList" ><a href="#">&nbsp;'+item.classsSubjects+'</a></td>';
                      content +='<td class="text-left" ><span class="xeditableStudyGroupTeacher" data-type="select2" data-pk="'+item.id+'" data-title="Select Teacher" data-url="'+base_url+'api/Tt/studyGroups/'+item.id+'?UpdateType=4" >'+item.teacherNames+'</span></td>';
                      content +='<td class="text-center">'+item.numberOfPeriods+'</td>';
                      content +='<td class="text-center"><span class="xeditableStudyGroupPeriodsPerWeek" data-type="text" data-pk="'+item.id+'" data-title="Enter Subject Maximum" data-url="'+base_url+'api/Tt/studyGroups/'+item.id+'?UpdateType=1" >'+item.numberOfPeriods+'</span></td>';
                      content +='<td class="text-center"><span class="xeditableStudyGroupPairPeriods" data-type="text" data-pk="'+item.id+'" data-title="Enter Subject Maximum" data-url="'+base_url+'api/Tt/studyGroups/'+item.id+'?UpdateType=2" >'+item.numberOfTwoConsectiveHours+'</span></td>';
                      if ( item.isOnTimeTable == 1 ) {
                        content +='<td class="text-center text-green"><span class="xeditableStudyGroupIsOn" data-type="select2" data-pk="'+item.id+'" data-title="Select Teacher" data-url="'+base_url+'api/Tt/studyGroups/'+item.id+'?UpdateType=3" >Yes</span></td>';
                      }else{
                        content +='<td class="text-center text-red"><span class="xeditableStudyGroupIsOn" data-type="select2" data-pk="'+item.id+'" data-title="Select Teacher" data-url="'+base_url+'api/Tt/studyGroups/'+item.id+'?UpdateType=3" >No</span></td>';
                      }
                      
                    content +="</tr>";

                });

                content +='</tbody></table></div>';
            content +='</div>';

        container.html( content );

        //
            $.fn.editable.defaults.mode = 'inline';
            $.fn.editable.defaults.ajaxOptions = {type: "PUT" };

            $('.xeditableStudyGroupTeacher').editable({
                source: EditStudyGroup_Teachers ,
                select2: {
                   width: 150,
                   multiple: false
                },
                success: function(response, newValue) {
                    if(response.status == 'error') return response.msg;
                }
            });

            $('.xeditableStudyGroupPeriodsPerWeek').editable({
                emptytext: 'Click to add',
                success: function(response, newValue) {
                    if(response.status == 'error') return response.msg;
                }
            });

            $('.xeditableStudyGroupPairPeriods').editable({
                emptytext: 'Click to add',
                success: function(response, newValue) {
                    if(response.status == 'error') return response.msg;
                }
            });

            $('.xeditableStudyGroupIsOn').editable({
                source: [
                      {id: '1', text: 'Yes'},
                      {id: '0', text: 'No'}
                   ],
                select2: {
                   width:70,
                   multiple: false
                },
                success: function(response, newValue) {
                    if(response.status == 'error') return response.msg;
                }
            });

            //TtStudyGroupClicked();
        //

        EditStudyGroup_Teachers        = data.Teachers ;
        NewSubjectGroup_ClassSubjects  = data.ClassSubjects;

        var classSubjectOptions = '<option></option>';

        $.each( NewSubjectGroup_ClassSubjects , function(i, item) {

            classSubjectOptions +='<option value="'+item.id+'" data-teacher="'+item.teacherID+'" data-doubleperiod="'+item.numberOfTwoConsectiveHours+'" data-numberofperiod="'+item.numberOfPeriods+'" >'+item.classSubject+'</option>';

        });

        var dashTtGroupSubject_Subjects = $( "#dashTtGroupSubject_Subjects");
        dashTtGroupSubject_Subjects.html(classSubjectOptions);
        dashTtGroupSubject_Subjects.select2().bind('change', onTtGroupSubjectClassSubjectChange);

        var dashTtGroupSubject_Teacher  = $( "#dashTtGroupSubject_Teacher"); 
        dashTtGroupSubject_Teacher.select2();

        $("#subjectSubject").show("slow");
        
    }).done(function() {

        $('#dashTtStudyGroupTable').DataTable( {
          paging: false ,
          "aaSorting": [] ,
          language: {
                searchPlaceholder: "Search teacher here... "
          },
          "columnDefs": [
                {
                    "targets": [ 2 ],
                    "visible": false,
                    "searchable": false
                }
            ],
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
                total = api
                    .column( 2 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
 
            // Total over this page
                pageTotal = api
                    .column( 2, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
 
            // Update footer
                $( api.column( 3 ).footer() ).html(
                    ' '+pageTotal +' periods'
                );

          }
      } );

    }).fail(function() {

    }).always(function() {

    });

}

function onTtGroupSubjectClassSubjectChange()
{
    var dashTtGroupSubject_Subjects     = $("#dashTtGroupSubject_Subjects");
    var selected                        = dashTtGroupSubject_Subjects.select2('val');
    
    var selectedSubject_teacher         = dashTtGroupSubject_Subjects.select2().find(":selected").data("teacher");
    var selectedSubject_doubleperiod    = dashTtGroupSubject_Subjects.select2().find(":selected").data("doubleperiod");
    var selectedSubject_numberofperiod  = dashTtGroupSubject_Subjects.select2().find(":selected").data("numberofperiod");

    if ( selectedSubject_teacher > 0 ) {
        var dashTtGroupSubject_Teacher      = $("#dashTtGroupSubject_Teacher");
        dashTtGroupSubject_Teacher.val(selectedSubject_teacher).trigger("change");
    }

    if ( selectedSubject_numberofperiod > 0 ) {
        var dashTtGroupSubject_TotalPeriod  = $("#dashTtGroupSubject_TotalPeriod");
        dashTtGroupSubject_TotalPeriod.val(selectedSubject_numberofperiod);
    }
    
    if ( selectedSubject_doubleperiod > 0 ) {
        var dashTtGroupSubject_DoublePeriods  = $("#dashTtGroupSubject_DoublePeriods");
        dashTtGroupSubject_DoublePeriods.val(selectedSubject_doubleperiod);
    }

}

function get_TtMergeSubjects()
{   
    //
    $("#dashTtGroupSubject_Subjects").select2('data', null);
    $("#dashTtGroupSubject_Teacher").select2('data', null);
    $("#dashTtGroupSubject_Subjects").val('0');
    $("#dashTtGroupSubject_DoublePeriods").val('0');

    $('#modal_TtStudyGroupGroup').modal('show');
}

function TtStudyGroupClicked()
{
    $( "table#dashTtStudyGroupTable" ).delegate( "td.TtStudyGroupManageList", "click", function(e) {

        e.preventDefault();
        e.stopPropagation();

        var id          = $(this).closest('tr').attr('id');
        TtLoadStudyGroup(id);

    });

}

function TtLoadStudyGroup(id)
{

    $('#modal_TtStudyGroup').modal('show');

    $( "#dashContentainer_Timetable").slideDown();
    var container = $( "#TtStudyGroup_Subjects" );

    container.html('<center><img src="../packages/assets/plugins/img/loading.gif" alt="Loading students..." > Loading Subjects...</center>');

    var url = base_url+"api/Tt/studyGroups/"+id ;

    $.get( url, function(data) {

        var classsSubjectIndex = 1;

        var content   = "";
        content  += '<table class="table table-hover" id="dashTtStudyGroupSubjectsTable" ><thead><tr><th class="text-left">#</th><th class="text-center">Subjects</th><th class="text-center">Remove</th></tr></thead><tbody>';

        $.each( data , function(i, item) {

            content +="<tr id='"+item.id+"' study_group_id='"+id+"'>";
              content +="<td>"+classsSubjectIndex+"</td>";
              content +="<td>"+item.classsSubjects+"</td>";
              content +='<td class="text-center TtStudyGroup_Ungroup"><a href="#">&nbsp;<i class="fa fa-chain-broken text-red"></i>&nbsp;Ungroup</a></td>';
            content +="</tr>";
        
            classsSubjectIndex++;

        });

        content +='</tbody></table>';

        container.html(content);


    }).done(function() { 

        TtStudyGroup_Ungroup();
    })
    .fail(function() {
    })
    .always(function() {
    });

}

function TtStudyGroup_Ungroup()
{
    $( "table#dashTtStudyGroupSubjectsTable" ).delegate( "td.TtStudyGroup_Ungroup", "click", function(e) {

        e.preventDefault();
        e.stopPropagation();

        var id              = $(this).closest('tr').attr('id');
        var study_group_id  = $(this).closest('tr').attr('study_group_id');

        UngroupStudyGroupSubjects(id, study_group_id, $(this) );

    });
}   

function UngroupStudyGroupSubjects( id, study_group_id, td )
{
    var url     = base_url+"api/Tt/studyGroups/"+id+"?study_group_id="+study_group_id ;
    td.html("Ungrouping...");

      $.ajax({
            url: url,
            type: 'DELETE',
            success: function(data) {
           
               if ( data.success )
               {
                    $.gritter.add({
                      title: 'Success',
                      text: 'Subject Ungrouped.',
                      class_name: 'success gritter-center',
                      time: ''
                    });

                    $('#modal_TtStudyGroup').modal('toggle');
                    get_tt_SubjectGroups();

               }else{

                 $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to Ungroup the subject',
                    class_name: 'danger gritter-center',
                    time: ''
                  });

                 td.html('<a href="#">&nbsp;<i class="fa fa-chain-broken text-red"></i>&nbsp;Ungroup</a>');
             }
        }

    });

}

function get_tt_Settings()
{
    var break_number = 0;

    $( "#dashContentainer_Timetable").slideDown();
    var container = $( "#dashTimetable_content" );

    container.html('<center><img src="../packages/assets/plugins/img/loading.gif" > Loading Settings...</center>');

    var url = base_url+"api/Tt/schoolSettings";

    $.get( url, function(data) {

        container.html(data);

    }).done(function() { 

        $( "#TtSchoolSettingsNonStudyingPeriods").select2();
        $( "#TtSchoolSettingsStartTime").select2();

        // $( "#TtSchoolSettingsNewBreak" ).click(function() {
        //
        //     break_number++;
        //
        //     var content = '';
        //     content +='<div class="form-group" id="TtSchoolSettings_Break_'+break_number+'">';
        //         content +='<label class="col-sm-4 control-label"></label>';
        //             content +='<div class="col-sm-2">';
        //                 content +='<input type="text" class="form-control" placeholder="Break Name">';
        //             content +='</div>';
        //             content +='<div class="col-sm-2">';
        //                 content +='<input type="text" class="form-control" placeholder="From">';
        //             content +='</div>';
        //             content +='<div class="col-sm-2">';
        //                 content +='<input type="text" class="form-control" placeholder="To">';
        //             content +='</div>';
        //             content +='<label class="col-sm-2 text-left" onclick="get_tt_removeBreak('+break_number+')" ><a href="javascript:void(0)" class="text-red" ><i class="fa fa-times fa-1x"></i> Remove</a></label>';
        //         content +='</div>';
        //     $( "#TtSchoolSettingsBreakContainer").append(content);
        // });

    })
    .fail(function() {
    })
    .always(function() {
    });

}

function get_tt_removeBreak( selected_break_number ){

    $( "#TtSchoolSettings_Break_"+selected_break_number ).hide("slow", function(){ $(this).remove(); });

}

function get_tt_NewTimeTable()
{
    $('#Model_DashTtNewTimeTable').modal('show');

}

function getTtGenerateTimeTableValidate()
{
    //validation
        var formDashTtNewTimeTable = $('#formDashTtNewTimeTable');

        formDashTtNewTimeTable.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                TimeTableVersionName: {
                    validators: {
                        notEmpty: {
                            message: 'Timetable version name is required'
                        }
                    }
                }
            }
        })
        .on('success.form.fv', function(e , data) {

        e.preventDefault();
        
        onDashTtGenerateTimeTableSubmitted();

        var formDashTtNewTimeSubmit = $('#formDashTtNewTimeSubmit');
            formDashTtNewTimeSubmit.removeClass('disabled'); 
            formDashTtNewTimeSubmit.removeAttr('disabled');
        }).end();

}


function onDashTtGenerateTimeTableSubmitted()
{

  var DashTtNewTimeTableInfo        = $('#DashTtNewTimeTableInfo');
  var formDashTtNewTimeTableLoading = $("#formDashTtNewTimeTableLoading") ;
  var formDashTtNewTimeTable        = $("#formDashTtNewTimeTable") ;

  DashTtNewTimeTableInfo.html('<i class="fa fa-info-circle"></i> The school timetable settings will be used'); 

  formDashTtNewTimeTable.hide();
  formDashTtNewTimeTableLoading.slideDown();

  var frm = formAcClassroomSubjectExcludeStudent;
  var url = base_url+"api/Tt/schoolTimetable"; 

  $.post(url, 
     formDashTtNewTimeTable.serialize(), 
     function(data, status, xhr){

          if ( data.success )
          {   
            $.gritter.add({
                title: 'Success',
                text: 'Timetable generated.',
                class_name: 'success gritter-center',
                time: ''
            });

            $('#Model_DashTtNewTimeTable').modal('toggle');

          } else {
              
              $.gritter.add({
                title: 'Failed',
                text: 'Something went wrong' ,
                class_name: 'warning gritter-center',
                time: ''
              });

            DashTtNewTimeTableInfo.html('<div class="alert alert-danger">'+data.error+'</div>');

          }
             
     },"json")
      .done(function() { })
      .fail(function() {

          $.gritter.add({
            title: 'Failed',
            text: 'Failed to generate timetable, Please Try Again',
            class_name: 'danger gritter-center',
            time: ''
          });
      })
      .always(function() {
        
        formDashTtNewTimeTable.show();
        formDashTtNewTimeTableLoading.hide();

      });

}

function getTtNewSubjectGroupValidate()
{
    //validation
        var formTtStudyGroupGroup = $('#formTtStudyGroupGroup');

        formTtStudyGroupGroup.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                 'subjects[]': {
                    validators: {
                        notEmpty: {
                            message: 'Subject is required'
                        }
                    }
                },
                teacher: {
                    validators: {
                        notEmpty: {
                            message: 'Teacher is required'
                        }
                    }
                },
                totalPeriod: {
                    validators: {
                        notEmpty: {
                            message: 'Total period is required'
                        }
                    }
                },
                doublePeriods: {
                    validators: {
                        notEmpty: {
                            message: 'Double period is required'
                        }
                    }
                }
            }
        })
        .on('success.form.fv', function(e , data) {

        e.preventDefault();
        
        onDashTtNewSubjectGroupSubmitted();

        var formTtStudyGroupGroup = $('#formTtStudyGroupGroup');
            formTtStudyGroupGroup.removeClass('disabled'); 
            formTtStudyGroupGroup.removeAttr('disabled');
        }).end();

}

function onDashTtNewSubjectGroupSubmitted()
{
      var dashTtGroupSubject_Subjects       = $("#dashTtGroupSubject_Subjects") ;
      var dashTtGroupSubject_Teacher        = $("#dashTtGroupSubject_Teacher") ;
      var dashTtGroupSubject_TotalPeriod    = $("#dashTtGroupSubject_TotalPeriod") ;
      var dashTtGroupSubject_DoublePeriods  = $("#dashTtGroupSubject_DoublePeriods") ;

      if ( dashTtGroupSubject_Subjects.select2('val').length && dashTtGroupSubject_Teacher.select2('val') > 0 && dashTtGroupSubject_TotalPeriod.val() == 0 && dashTtGroupSubject_DoublePeriods.val() == 0) {
          
          var formDashTtNewGroupLoading = $("#formDashTtNewGroupLoading") ;
          var formTtStudyGroupGroup     = $("#formTtStudyGroupGroup") ;

          formTtStudyGroupGroup.hide();
          formDashTtNewGroupLoading.slideDown();

          var frm = formTtStudyGroupGroup;
          var url = base_url+"api/Tt/studyGroups"; 

          $.post(url, 
             frm.serialize(), 
             function(data, status, xhr){

                  if ( data.success )
                  {   
                    $.gritter.add({
                        title: 'Success',
                        text: 'Subject grouped added.',
                        class_name: 'success gritter-center',
                        time: ''
                    });

                    $('#modal_TtStudyGroupGroup').modal('toggle');
                    get_tt_SubjectGroups();
                    
                  } else {
                      
                      $.gritter.add({
                        title: 'Failed',
                        text: 'Something went wrong' ,
                        class_name: 'warning gritter-center',
                        time: ''
                      });

                  }
                     
             },"json")
              .done(function() { })
              .fail(function() {

                  $.gritter.add({
                    title: 'Failed',
                    text: 'Failed to add subject group, Please Try Again',
                    class_name: 'danger gritter-center',
                    time: ''
                  });
              })
              .always(function() {
                
                formTtStudyGroupGroup.show();
                formDashTtNewGroupLoading.hide();

              });

      }else{
          
          $('#formTtStudyGroupGroup').formValidation('revalidateField', 'subjects[]');
          $('#formTtStudyGroupGroup').formValidation('revalidateField', 'teacher');
          $('#formTtStudyGroupGroup').formValidation('revalidateField', 'totalPeriod');
          $('#formTtStudyGroupGroup').formValidation('revalidateField', 'doublePeriods');

      } 

}























