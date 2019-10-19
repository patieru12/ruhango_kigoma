var previous_count;
var current_count;
var debt_count;

function validateNewFeesType(){
	$("#newCurrentFeeType").select2();
	$('#formDashPaNewSchoolFeeType').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          feeTypeName: {
                 validators: {
                     notEmpty: {
                         message: 'Fee Type Required'
                     }
                 }
             }, 
          feeTypeDescription: {
                 validators: {
                     notEmpty: {
                         message: 'Description Required'
                     }

                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

		if (data.fv.getInvalidFields().length <= 0) {
			data.fv.disableSubmitButtons(false);
		}

    }).on('success.form.fv', function( e ) {
		e.preventDefault();
		submitPaNewFeeType();
    });
}

/* Load the Invoice List function */
function load_Accounting_School_Fees_New_Fee_Type(){
	//try to change the style sheet and check
	$(".left_menu").removeClass('active');
	$("#FeeType").addClass('active');
	var url = base_url+"accounting/frequence";
	$.getJSON( url, function( data ) {
		/* load the form by passing received data the function */
		NewFeeTypeForm(data);
	}).done(function(){
		
	});
	
}

/* Load the Invoice List function */
function load_Accounting_School_Fees_New_Current_Fee(){

	//try to change the style sheet and check
		$(".left_menu").removeClass('active');
		$("#currentFees").addClass('active');

		var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");

		var url = base_url+"accounting/schoolfees/currentfee/create";
	    content = '';

	    $.getJSON( url, function(data) {

	    	var FeeType 			= data.FeeType;
	    	var Students 			= data.Students;
	    	var classrooms 			= data.classrooms;
	    	var boarding 			= data.boarding;
	    	var SchoolOrganizations = data.SchoolOrganizations;

	    	content += '<div class="row"><label class="col-sm-12 text-center"><h5>New '+Cust_TermName+' Fee</h5></div></div>';
	    	content += '<div class="row">';
	    		content += '<div class="form-group" id="PaNewCurrentFeeLoading"><center><div class="row"><img src="../packages/assets/img/loading.gif" alt="Loading..." height="35"></div><div class="row"><p>Saving...</p></div></center></div>';
	    		content += '<form role="form" id="formDashPaNewCurrentFee" >';
		    		content += '<div class="form-horizontal">';
			    		content += '<div class="form-group" >';
							content += '<label class="col-sm-3 control-label">Fee Type</label>';
							content += '<div class="col-sm-7">';
								content += '<select name="newTermlyFee_FeeType" class="form-control" id="newTermlyFee_FeeType" >';
									content += '<option></option>';
									$.each( FeeType , function(i, item){
										content += '<option value="'+item.id+'">'+item.name+'</option>';
									});	
								content += '</select>';
							content +='</div>';
						content += '</div>';
						content += '<div class="form-group" >';
							content += '<label class="col-sm-3 control-label">Description</label>';
							content += '<div class="col-sm-7">';
								content += '<input name="newTermlyFee_description" type="text" class="form-control"  placeholder="description">';
							content += '</div>';
						content += '</div>';
						content += '<div class="form-group" >';
							content += '<label class="col-sm-3 control-label">Amount</label>';
							content += '<div class="col-sm-7">';
								content += '<input name="newTermlyFee_amount" type="text" class="form-control"  placeholder="amout">';
							content += '</div>';
						content += '</div>';
						content += '<div class="form-group" >';
							content += '<label class="col-sm-3 control-label">Due Date</label>';
							content += '<div class="col-sm-7">';
								content += '<input name="newTermlyFee_dueDate" type="text" class="form-control"  placeholder="due date" id="newTermlyFee_dueDate">';
							content += '</div>';
						content += '</div>';
						content += '<div class="form-group" >';
							content += '<label class="col-sm-3 control-label">Students to pay</label>';
							content += '<div class="col-sm-2"><label><input type="radio" name="newTermlyFee_studentType" value="1"  checked> All Students</label></div>';
							content += '<div class="col-sm-2"><label><input type="radio" name="newTermlyFee_studentType" value="2"  > Custom Students</label></div>';
							content += '<div class="col-sm-2"><label><input type="radio" name="newTermlyFee_studentType" value="3"  > Choose Students</label></div>';
						content += '</div>';
						content += '<div class="form-group" id="ac_SchoolFee_NewTermlyFee_ExceptContainer">';
							content += '<label class="col-sm-3 control-label"> Except</label>';
							content += '<div class="col-sm-7">';
								content += '<select name="newTermlyFee_StudentsExcept[]" class="form-control" id="newTermlyFee_StudentsExcept" multiple>';
									content += '<option></option>';
									$.each( Students , function(i, item){
										content += '<option value="'+item.personID+'">'+item.studentNames+' ('+item.studRegNber+')</option>';
									});	
								content += '</select>';
							content +='</div>';
						content += '</div>';
						content += '<div class="form-group" id="ac_SchoolFee_NewTermlyFee_BoardingContainer" >';
							content += '<label class="col-sm-3 control-label"> Boarding</label>';
							if ( boarding.boardingID == 1 ) {
								content += '<div class="col-sm-3"><label><input type="checkbox" name="newTermlyFee_studentBoarding[]" value="0" checked > Day</label></div>';
								content += '<div class="col-sm-3"><label><input type="checkbox" name="newTermlyFee_studentBoarding[]" value="1" checked > Boarding</label></div>';

							}else if( boarding.boardingID == 2 ){
								content += '<div class="col-sm-6"><label><input type="checkbox" name="newTermlyFee_studentBoarding[]" value="0" checked desabled > Day</label></div>';

							}else if( boarding.boardingID == 3 ){
								content += '<div class="col-sm-6"><label><input type="checkbox" name="newTermlyFee_studentBoarding[]" value="1" checked desabled > Boarding</label></div>';
							}
						content += '</div>';
						content += '<div class="form-group" id="ac_SchoolFee_NewTermlyFee_ClassContainer" >';
							content += '<label class="col-sm-3 control-label"> Class</label>';
							content += '<div class="col-sm-3"><label><input type="radio" name="newTermlyFee_studentClass" value="1" checked > All Class</label></div>';
							content += '<div class="col-sm-3"><label><input type="radio" name="newTermlyFee_studentClass" value="0" > Some Class</label></div>';
						content += '</div>';
						content += '<div class="form-group" id="ac_SchoolFee_NewTermlyFee_SomeClassesContainer">';
							content += '<label class="col-sm-3 control-label"> Some Classes</label>';
							content += '<div class="col-sm-7">';
								content += '<select name="newTermlyFee_SomeClasses[]" class="form-control" id="newTermlyFee_SomeClasses" multiple>';
									content += '<option></option>';

									var classidentifier = "";
									var isTheFirsRecord = true;

									$.each( classrooms , function(i, classroom){

										if ( classroom.classidentifier == classidentifier ) {
											content += '<option value="'+classroom.id+'" data-yearname="'+classroom.YearName+'" data-level="'+classroom.levelID+'" >'+classroom.name+'</option>';

										}else{

											classidentifier = classroom.classidentifier;
											if ( isTheFirsRecord ) {
												isTheFirsRecord = false;

											}else{
												content += '</optgroup>';
											}

											content += '<optgroup label="'+classroom.YearName+'">';
											content += '<option value="'+classroom.id+'" data-yearname="'+classroom.YearName+'" data-level="'+classroom.levelID+'" >'+classroom.name+'</option>';
										}
									});	
								content += '</select>';
							content += '</div>';
						content += '</div>';
						content += '<div class="form-group" id="ac_SchoolFee_NewTermlyFee_SponsorContainer">';
							content += '<label class="col-sm-3 control-label"> Sponsor</label>';
							content += '<div class="col-sm-2"><label><input type="checkbox" name="newTermlyFee_studentSponsor[]" value="1" checked > Parents/Guardian</label></div>';
							content += '<div class="col-sm-2"><label><input type="checkbox" name="newTermlyFee_studentSponsor[]" value="3" checked > School</label></div>';
							content += '<div class="col-sm-1"><label><input type="checkbox" name="newTermlyFee_studentSponsor[]" value="4" checked > Self</label></div>';
							content += '<div class="col-sm-2"><label><input type="checkbox" name="newTermlyFee_studentSponsor[]" value="2" checked id="newTermlyFee_studentSponsorOrg"> Organization</label></div>';
						content += '</div>';
						content += '<div class="form-group" id="ac_SchoolFee_NewTermlyFee_OrganizationContainer">';
							content += '<label class="col-sm-3 control-label"> Organization</label>';
							content += '<div class="col-sm-7">';
								content += '<select name="newTermlyFee_Organization[]" class="form-control" id="newTermlyFee_Organization" multiple >';
									content += '<option></option>';
									$.each( SchoolOrganizations , function(i, item){
										content += '<option value="'+item.id+'" selected="selected">'+item.name+'</option>';
									});	
								content += '</select>';
							content += '</div>';
						content += '</div>';
						content += '<div class="form-group" id="ac_SchoolFee_NewTermlyFee_SomeStudentsContainer">';
							content += '<label class="col-sm-3 control-label"> Some Students</label>';
							content += '<div class="col-sm-7">';
								content += '<select name="newTermlyFee_StudentsSome[]" class="form-control" id="newTermlyFee_StudentsSome" multiple>';
									content += '<option></option>';
									$.each( Students , function(i, item){
										content += '<option value="'+item.personID+'">'+item.studentNames+' ('+item.studRegNber+')</option>';
									});	
								content += '</select>';
							content +='</div>';
						content += '</div>';
						content += '<div class="form-group" >';
							content += '<div class="col-sm-12 text-center"><label>Automaticaly use unassigned payments to pay this fee &nbsp;&nbsp;<input type="checkbox" name="newTermlyFee_autoAssign" value="1" checked ></label></div>';
						content += '</div>';
						content += '<div class="form-group" >';
							content += '<label class="col-sm-3 control-label"></label>';
							content += '<div class="col-sm-7 text-right">';
								content += '<button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save '+Cust_TermName+' Fee</button>';
							content += '</div>';
						content += '</div>';
					content += '</div>';
				content += '</form>';
	    	content += '</div>';

	    }).done(function() {

	    	dashClientContainer.html(content);
	    	$("#PaNewCurrentFeeLoading").hide();

	    	ac_SchoolFee_NewTermlyFeeForm();

	    }).fail(function() {

			$.gritter.add({
	            title: 'Result not saved',
	            text: 'Check your internet and try again',
	            class_name: 'danger gritter-center',
	            time: ''
	        });

	    })
	    .always(function() {

	    });

}

function ac_SchoolFee_NewTermlyFeeForm(){

	$("#newTermlyFee_FeeType").select2();
	$("#newTermlyFee_StudentsExcept").select2({ 
            closeOnSelect: false
    });
	$("#newTermlyFee_SomeClasses").select2({ 
            closeOnSelect: false,
            formatResult: newStudentformatResult,
            formatSelection: newStudentformatSelection
    });
	$("#newTermlyFee_Organization").select2({ 
            closeOnSelect: false
    });
	$("#newTermlyFee_StudentsSome").select2({ 
            closeOnSelect: false
    });

	$('#newTermlyFee_dueDate').datepicker().datepicker({ dateFormat: 'yy-mm-dd' });

	var ac_SchoolFee_NewTermlyFee_ExceptContainer 		= $("#ac_SchoolFee_NewTermlyFee_ExceptContainer");
	var ac_SchoolFee_NewTermlyFee_BoardingContainer 	= $("#ac_SchoolFee_NewTermlyFee_BoardingContainer");
	var ac_SchoolFee_NewTermlyFee_ClassContainer  		= $("#ac_SchoolFee_NewTermlyFee_ClassContainer");
	var ac_SchoolFee_NewTermlyFee_SomeClassesContainer	= $("#ac_SchoolFee_NewTermlyFee_SomeClassesContainer");
	var ac_SchoolFee_NewTermlyFee_SponsorContainer		= $("#ac_SchoolFee_NewTermlyFee_SponsorContainer")
	var ac_SchoolFee_NewTermlyFee_OrganizationContainer = $("#ac_SchoolFee_NewTermlyFee_OrganizationContainer");
	var ac_SchoolFee_NewTermlyFee_SomeStudentsContainer = $("#ac_SchoolFee_NewTermlyFee_SomeStudentsContainer");

	ac_SchoolFee_NewTermlyFee_BoardingContainer.hide();
	ac_SchoolFee_NewTermlyFee_ClassContainer.hide();
	ac_SchoolFee_NewTermlyFee_SomeClassesContainer.hide();
	ac_SchoolFee_NewTermlyFee_SponsorContainer.hide();
	ac_SchoolFee_NewTermlyFee_OrganizationContainer.hide();
	ac_SchoolFee_NewTermlyFee_SomeStudentsContainer.hide();

	$('input[type=radio][name=newTermlyFee_studentType]').change(function() {
	        
	    var selected = $(this).val();

	    ac_SchoolFee_NewTermlyFee_ExceptContainer.hide();
	    ac_SchoolFee_NewTermlyFee_BoardingContainer.hide();
	    ac_SchoolFee_NewTermlyFee_ClassContainer.hide();
	    ac_SchoolFee_NewTermlyFee_SomeClassesContainer.hide();
	    ac_SchoolFee_NewTermlyFee_SponsorContainer.hide();
		ac_SchoolFee_NewTermlyFee_OrganizationContainer.hide();
		ac_SchoolFee_NewTermlyFee_SomeStudentsContainer.hide();

		switch(selected) {
		    case "1":
		    		ac_SchoolFee_NewTermlyFee_ExceptContainer.slideDown();
		        break;

		    case "2":
		    		ac_SchoolFee_NewTermlyFee_BoardingContainer.slideDown();
		    		ac_SchoolFee_NewTermlyFee_ClassContainer.slideDown();
					ac_SchoolFee_NewTermlyFee_SponsorContainer.slideDown();
					ac_SchoolFee_NewTermlyFee_OrganizationContainer.slideDown();
		       break;

	        case "3":
	        		ac_SchoolFee_NewTermlyFee_SomeStudentsContainer.slideDown();
		        break;

		} 

	});

	$('input[type=radio][name=newTermlyFee_studentClass]').change(function() {
	        
	    var selected = $(this).val();

	    ac_SchoolFee_NewTermlyFee_SomeClassesContainer.hide();

		switch(selected) {
		    case "1":
		    		ac_SchoolFee_NewTermlyFee_SomeClassesContainer.hide();
		        break;

		    case "0":
		    		ac_SchoolFee_NewTermlyFee_SomeClassesContainer.slideDown();
		       break;

		} 

	});

	$('#newTermlyFee_studentSponsorOrg').change(function() {
        
        if ( $(this).is(":checked") )
         {
         	ac_SchoolFee_NewTermlyFee_OrganizationContainer.slideDown();

         }else{

         	ac_SchoolFee_NewTermlyFee_OrganizationContainer.hide();
         }
        
    });

    // validate the for her now
    // console.log("calling Validator!");
    validateNewCurrentFee();

}	

function LoadCurrentDetailsInformation(FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate ){

	var url = base_url+"accounting/schoolfees/currentfee/" + FeeId;

	$.getJSON( url, function( data ) {
	  
	  var Student_WithThisFee = data.Student_WithThisFee;
	  var Student_WithoutFee  = data.Student_WithoutFee;

	  CurrentFeeStudentList(FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate, Student_WithThisFee, Student_WithoutFee );

  	}).done(function() {

  	})
  	.fail(function() {

  	})
  	.always(function() {

 	});
}


function LoadSponsorStudentInformation(orgId){
	var url2 = base_url+"accounting/students/" + orgId;
	/* load the student list now */
	$.getJSON( url2, function( student ) {
		/* load the form by passing received data the function */
		//NewCurrentFeeForm(data, student, terms);
		SponsorStudentList(orgId, student);
	});
}

function AddOrganizationContribution(orgId, termid){
	var url2 = base_url+"accounting/students/" + orgId + "_:" + termid;
	/* load the student list now */
	$.getJSON( url2, function( student ) {
		/* load the form by passing received data the function */
		//NewCurrentFeeForm(data, student, terms);
		ChangeStudentSponsorshipData(orgId, student, termid);
	});
}

function LoadSponsorStudentInfo(orgId){
	SponsorStudentListInfo(orgId);
}


/* Load the Invoice List function */
function load_Accounting_New_Fee_Payment(){
	//try to change the style sheet and check
	$(".left_menu").removeClass('active');
	$("#currentFees").addClass('active');
	
	//NewFeePaymentForm();
}

function NewFeeTypeForm(frequence){
	//get the instance of container division 
	var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
	//get the instance of the content division
	var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
	
	//hide the container first
	dashClientContainer.slideUp();
	
	//prepare the content to be printed in the containner
	
	var content = '';
				content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
					content +='<div class="row text-center">';
						content+= '<div class="col-sm-8">';
							content+= '<h4> New Fee Type</h4>';
						content+= '</div>';
						content+= '<div class="col-sm-4">';
							content+= '<a id=AccountingSchoolFeesMoreFeeType class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a>';
						content+= '</div>';
					content+= '</div>';
					content+= '<div class="row" >\
									<div id="pa_Expense_ExpenseType">\
										\
					  <div class="form-group" id="PaNewSchoolFeeTypeLoading"><center><div class="row"><img src="../packages/assets/img/loading.gif" alt="Loading..." height="35"></div><div class="row"><p>Saving...</p></div></center></div>\
                      <form id="formDashPaNewSchoolFeeType" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Fee Type</div>\
								<div class="col-sm-7">\
									<input name="feeTypeName" type="text" id="paNewInvoiceDate" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Frequence</div>\
								<div class="col-sm-7">\
									<select name="feeTypeFrequence" class="form-control" id="newCurrentFeeType" placeholder="Choose Client Frequence" >\
										';
						 $.each(frequence, function(i, item){
							 content+= '<option value="' + item.id + '">' + item.name + '</option>'
						 });
						 content+= '</select>\
									</div >\
							</div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Fee</button>\
                          </div>\
                        </div>\
					</form>\
									</div>\
								</div>\
							   ';
				content+= '</div>\
							<div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
								<div class="modal-wrapper">\
									<div class="modal-dialog">\
										<div class="modal-content">\
											<div class="modal-header">\
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
												<h4 class="modal-title text-left" id="myModalLabel22">New Expense</h4>\
											</div>\
											<div id="ModalDataLoadingNow">\
											</div>\
											<div class="modal-footer" >\
												<div class="pull-right btn-group">\
													<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
													\
												</div>\
											</div>\
										</div>\
									</div>\
								</div>\
							</div>\
											';
	  
	
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
	$("#PaNewSchoolFeeTypeLoading").hide();

  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  /* Activate the new invoice buttons */
  //AccountingSchoolFeesNewCurrentCurrentJS();
  AccountingSchoolFeesListFeesType();
  
  $('#Model_DashPaFeeStudentList').modal({ show: false});
  
  validateNewFeesType();
}


/* Load the Invoice List function */
function NewFeePaymentForm(){
	//get the instance of container division 
	var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
	//get the instance of the content division
	var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
	
	//hide the container first
	dashClientContainer.slideUp();
	
	//prepare the content to be printed in the containner
	
	var content = '';
				content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
					content +='<div class="row text-center">';
						content+= '<div class="col-sm-8">';
							content+= '<h4> New Current Fees To Pay</h4>';
						content+= '</div>';
						content+= '<div class="col-sm-4">';
							content+= '<a id=AccountingSchoolFeesListCurrentFeesPayment class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a>';
						content+= '</div>';
					content+= '</div>';
					content+= '<div class="row" >\
									<div id="pa_Expense_ExpenseType">\
										\
                      <form id="formDashPaNewIncome" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
							<div class="form-group" > \
                            <div class="col-sm-3 text-right">Fee Type</div>\
                            <div class="col-sm-7">\
                              <select name="newCurrentFeeType" class="form-control" id="newCurrentFeeType" placeholder="Choose Client Name" >\
                                <option></option>\
                                <option value="Add">New Fee Type</option>\
                                <option value="1" > School Fees</option>\
                                <option value="2" > Uniform</option>\
                              </select>\
                            </div >\
                          </div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Student</div>\
								<div class="col-sm-7">\
									<select name="newCurrentFeeStudent[]" class="form-control" id="newCurrentFeeStudent" placeholder="Choose student here" multiple >\
									<option>New Student</option>\
									<option>Cyusa Emmanuel</option>\
									<option>Abizeye Aime</option>\
									<option>Akumutima Elyse</option>\
									</select>\
								</div>\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Amount</div>\
								<div class="col-sm-7">\
									<input name="invoiceDate" type="text" id="paNewInvoiceDate" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Due Date</div>\
								<div class="col-sm-7">\
									<input name="newCurrentFeeDueDate" type="text" id="newCurrentFeeDueDate" class="form-control" >\
								</div >\
							</div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Fee</button>\
                          </div>\
                        </div>\
					</form>\
									</div>\
								</div>\
							   ';
				content+= '</div>\
							<div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
								<div class="modal-wrapper">\
									<div class="modal-dialog">\
										<div class="modal-content">\
											<div class="modal-header">\
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
												<h4 class="modal-title text-left" id="myModalLabel22">New Expense</h4>\
											</div>\
											<div id="ModalDataLoadingNow">\
											</div>\
											<div class="modal-footer" >\
												<div class="pull-right btn-group">\
													<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
													\
												</div>\
											</div>\
										</div>\
									</div>\
								</div>\
							</div>\
											';
	  
	
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  /* Activate the new invoice buttons */
  AccountingSchoolFeesNewCurrentCurrentJS();
  
  $('#Model_DashPaFeeStudentList').modal({ show: false});
}

// function NewCurrentFeeForm(feetype, students, terms){
// 	//get the instance of container division 
// 	var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
// 	//get the instance of the content division
// 	var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
	
// 	//hide the container first
// 	dashClientContainer.slideUp();
	
// 	//prepare the content to be printed in the containner
	
// 	var content = '';
// 				content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
// 					content +='<div class="row text-center">';
// 						content+= '<div class="col-sm-8">';
// 							content+= '<h4> New Current Fees To Pay</h4>';
// 						content+= '</div>';
// 						content+= '<div class="col-sm-4">';
// 							content+= '<a id=AccountingSchoolFeesListCurrentFeesPayment class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a>';
// 						content+= '</div>';
// 					content+= '</div>';
// 					content+= '<div class="row" >\
// 									<div id="pa_Expense_ExpenseType">\
// 										\
// 					  <div class="form-group" id="PaNewCurrentFeeLoading"><center><div class="row"><img src="../packages/assets/img/loading.gif" alt="Loading..." height="35"></div><div class="row"><p>Saving...</p></div></center></div>\
//                       <form id="formDashPaNewCurrentFee" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
//                         <div class="modal-body" >\
// 							<div class="form-group" > \
// 								<div class="col-sm-3 text-right">Term</div>\
// 								<div class="col-sm-7">\
// 								  <select name="newCurrentTermId" class="form-control" id="newCurrentTermId" placeholder="Choose Client Name" >\
// 									';
// 						$.each(terms, function(i, item){
// 							content+= '<option value="' + item.id + '">' + item.name + ' term ' + item.number + '</option>';
// 						});
// 					   content+= '</select>\
// 								</div >\
// 							</div>\
// 							<div class="form-group" > \
// 								<div class="col-sm-3 text-right">Fee Type</div>\
// 								<div class="col-sm-7">\
// 									<select name="newCurrentFeeType" class="form-control" id="newCurrentFeeType" placeholder="Choose Client Name" >\
// 									';
// 						$.each(feetype, function(i, item){
// 							content+= '<option value="' + item.id + '">' + item.name + ' #' + item.description + '</option>';
// 						});
// 						content+= '</select>\
// 								</div >\
// 							</div>\
//                             <div class="form-group" > \
// 								<div class="col-sm-10">\
// 									<div class="col-sm-2">\
// 										<div class="row text-left">\
// 											<label>\
// 												<input type="radio" checked name="student_selection" id="paNewIncomeChannel" value="0" /> All\
// 											</label>\
// 										</div>\
// 										<div class="row text-left">\
// 											<label>\
// 												<input type="radio" name="student_selection" id="paNewIncomeChannel" value="1" /> Custom\
// 											</label>\
// 										</div>\
// 									</div>\
// 									<div class="col-sm-3">\
// 										<div id="custom_container" >\
// 											<div class="row text-left">\
// 												<label>\
// 													<input type="radio" name="custom_selection" id="paNewIncomeChannel" value="0" /> class\
// 												</label>\
// 											</div>\
// 											<div class="row text-left">\
// 												<label>\
// 													<input type="radio" name="custom_selection" id="paNewIncomeChannel" value="1" /> sponsorship\
// 												</label>\
// 											</div>\
// 											<div class="row text-left">\
// 												<label>\
// 													<input type="radio" name="custom_selection" id="paNewIncomeChannel" value="2" /> Custom\
// 												</label>\
// 											</div>\
// 										</div>\
// 									</div>\
// 									<div class="col-sm-7">\
// 										<div id="classes_container" class="row text-left">\
// 											<select id="custom_student_selection_classes" class="form-control" name="classes">\
// 											</select>\
// 										</div>\
// 										<div id="sponsorship_container" class="row text-left">\
// 											<select id="custom_student_selection_sponsorship" class="form-control" name="classes">\
// 											</select>\
// 										</div>\
// 										<div id="custom_student_selection_container" class="row text-left">\
// 											<select id="custom_student_selection_data" class="form-control" name="classes">\
// 											</select>\
// 										</div>\
// 									</div>\
// 								</div>\
// 							</div>\
//                             <div class="form-group" > \
// 								<div class="col-sm-3 text-right">Student</div>\
// 								<div class="col-sm-7">\
// 									<select name="newCurrentFeeStudent[]" class="form-control" id="newDebtStudentForm" placeholder="Choose student here" multiple >\
// 									';
// 						var class_name = ""; var clos = false; var started = false;
// 						$.each(students, function(i, item){
// 							if(class_name != item.ClassName){
// 								if(started){
// 									content += '</optgroup>';
// 								}
// 								content += '<optgroup label="' + item.ClassName + '">';
// 								class_name = item.ClassName;
// 								started = true;
// 								clos = true;
// 							}
// 							content += '<option value=' + item.registrationNumber + '>' + item.registrationNumber + "(" + item.name + ')</option>';
// 							//content += '<option value=' + item.id + '>' + item.registrationNumber + "(" + item.name + ')</option>';
							
// 						});
// 						if(clos){
// 							content += '</optgroup>';
// 						}
// 					   content+= '</select>\
// 								</div>\
// 							</div>\
// 							<div class="form-group" > \
// 								<div class="col-sm-3 text-right">Amount</div>\
// 								<div class="col-sm-7">\
// 									<input name="amount" type="text" id="paNewInvoiceDate" class="form-control" >\
// 								</div >\
// 							</div>\
// 							<div class="form-group" > \
// 								<div class="col-sm-3 text-right">Due Date</div>\
// 								<div class="col-sm-7">\
// 									<input name="newCurrentFeeDueDate" type="text" id="newCurrentFeeDueDate" class="form-control" >\
// 								</div >\
// 							</div>\
// 							<div class="form-group" > \
// 								<div class="col-sm-10">\
// 									<label>If Student Has Unassigned will be distributed automaticaly <input type=checkbox name=distributeUnassigned /></label>\
// 								</div >\
// 							</div>\
//                         </div>\
//                         <div class="modal-footer" >\
//                           <div class="pull-right btn-group">\
//                             <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
//                             <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Fee</button>\
//                           </div>\
//                         </div>\
// 					</form>\
// 									</div>\
// 								</div>\
// 							   ';
// 				content+= '</div>\
// 							<div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
// 								<div class="modal-wrapper">\
// 									<div class="modal-dialog">\
// 										<div class="modal-content">\
// 											<div class="modal-header">\
// 												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
// 												<h4 class="modal-title text-left" id="myModalLabel22">New Expense</h4>\
// 											</div>\
// 											<div id="ModalDataLoadingNow">\
// 											</div>\
// 											<div class="modal-footer" >\
// 												<div class="pull-right btn-group">\
// 													<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
// 													\
// 												</div>\
// 											</div>\
// 										</div>\
// 									</div>\
// 								</div>\
// 							</div>\
// 											';
	  
	
// //send the prepared content to the screen for user
// 	dashClientContainer.html( content );
// 	$("#PaNewCurrentFeeLoading").hide();

//   //load the contetn now
//   dashContentainer_Payments.slideDown();
//   dashClientContainer.slideDown();
//   /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
//   /* Activate the new invoice buttons */
//   AccountingSchoolFeesNewCurrentCurrentJS();
  
//   $('#Model_DashPaFeeStudentList').modal({ show: false});
	
//   //validate the form form submition
//   validateNewCurrentFee();
// }

function DetailsTableData( FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate, FeeStudent_WithThisFee ){

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#studentnfocontainner');
	pa_Expense_ExpenseType.slideUp();
	  
	  var Content_Pa_Deposits_Others  = '';
      	  Content_Pa_Deposits_Others += '<div class="row top-buffer"><div class="col-sm-2 text-right">Amount: </div><div class="col-sm-4"><b>'+formatNumber(FeeAmount)+'</b></div>';
      	  Content_Pa_Deposits_Others += '<div class="col-sm-2 text-right">Due Date:</div><div class="col-sm-4"><b>'+date_moment(FeeDueDate)+'</b></div></div>';

		Content_Pa_Deposits_Others += '<div class="row top-buffer"><div class="col-sm-12">\
									<table class="table table-bordered abDataTable" id="pa_AccountingStudentListTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Class</th><th class="text-center">Paid</th><th class="text-center">Remain</th><th class="text-center">Due Date</th><th>Manage</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="2" style="text-align:left"></th><th></th><th></th><th colspan=2></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
 
	  $("#myModalLabel22").html( FeeName +' '+FeeDescription );

      $.each( FeeStudent_WithThisFee , function(i, item) {	

      	AmountRemain  = parseFloat(FeeAmount) - parseFloat(item.amount_paid); 
      
          Content_Pa_Deposits_Others +="<tr id="+ item.id +" feeID="+FeeId+">";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.studentNames + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.className +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.amount_paid) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(AmountRemain) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + date_moment(item.dueDate) +" </td>";
            /*  Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.InvoiceDate +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.InvoiceDue +" </td>";
             */
			Content_Pa_Deposits_Others +="<td class='text-center deleteStudentFromTermlyInvoice' style='cursor:pointer;' ><i class='fa fa-times text-red'></i></td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table></div></div>";
      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );

      AccCurrentFeeRemoveStudentClicked( FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate );
	  pa_Expense_ExpenseType.slideDown();

	   $('#pa_AccountingStudentListTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

            //Paid 
              // Total 
                total_Paid = api
                    .column( 2 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 2 ).footer() ).html(
                    '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
                );

            //Remain 
              // Total 
                total_Remain = api
                    .column( 3 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                // Update footer
                $( api.column( 3 ).footer() ).html(
                    '<span class="text-red">'+formatNumber(total_Remain)+' '+schoolCurrency+'</span>'
                );


          }
      } );

}

function DetailsTableDataForSponsorship(orgId){
	var url = base_url + "accounting/sponsors/" + orgId;

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#studentnfocontainner');
	pa_Expense_ExpenseType.slideUp();
    $.getJSON( url, function( data ) {
		
		Content_Pa_Deposits_Others += '<div id="">\
									<table class="table table-bordered abDataTable" id="pa_AccountingStudentListTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Class</th><th class="text-center">Reg Number</th><th class="text-center">action</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:left">&nbsp;<!--<img id="paAccountingNewStudentOnFee" src="packages/assets/img/add.png" alt="new" />--></th><th></th><th colspan=2></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //$.each(data.FeeType, function(i, item){
	  $("#myModalLabel22").html("Type:" + data.FeeType.name + "<br />Total Student Supported: " + formatNumber(data.FeeType.numberOfStudent) + "<br />From:" + data.FeeType.created_at);
	 // });
	 //console.log(data.Students);
      $.each( data.Students, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+ item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.name + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.ClassName +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.regNumber +" </td>";
            
			Content_Pa_Deposits_Others +="<td class='text-right' style='cursor:pointer;' onclick='DeleteStudentFromOrganizationSponsor(" + item.id + ", " + orgId + ")'> Remove</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table></div>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_AccountingStudentListTable').DataTable( {
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
	  /* Display again */
	  pa_Expense_ExpenseType.slideDown();
	  
    })
    .fail(function() {
    })
    .always(function() {

    });
}

function DetailsTableDataForSponsorshipData(orgId, termId){
	var url = base_url + "accounting/sponsors/" + orgId + "_:" + termId;
	//var term = termId;
  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#studentnfocontainner');
	pa_Expense_ExpenseType.slideUp();
    $.getJSON( url, function( data ) {
		
		Content_Pa_Deposits_Others += '<div id="">\
									<table class="table table-bordered abDataTable" id="pa_AccountingStudentListTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Class</th><th class="text-center">Reg Number</th><th class="text-center">Amount</th><th class="text-center">action</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:left">&nbsp;<!--<img id="paAccountingNewStudentOnFee" src="packages/assets/img/add.png" alt="new" />--></th><th></th><th colspan=2></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //$.each(data.FeeType, function(i, item){
	  $("#myModalLabel22").html("Type:" + data.FeeType.name + "<br />Total Student Supported: " + formatNumber(data.FeeType.numberOfStudent) + "<br />From:" + data.FeeType.created_at + "<br />"+Cust_TermName+":<b>" + termId + "</b>");
	 // });
	 //console.log(data.Students);
      $.each( data.Students, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+ item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.name + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.ClassName +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.regNumber +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.amount + " </td>";
            
			Content_Pa_Deposits_Others +="<td class='text-right' style='cursor:pointer;' onclick='DeleteStudentFromOrganizationSponsor(" + item.id + ", " + orgId + ")'> Remove</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table></div>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_AccountingStudentListTable').DataTable( {
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
	  /* Display again */
	  pa_Expense_ExpenseType.slideDown();
	  
    })
    .fail(function() {
    })
    .always(function() {

    });
}

function DetailsTableDataForSponsorshipInfo(orgId){
	var url = base_url + "accounting/sponsors/" + orgId;

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#studentnfocontainner');
	pa_Expense_ExpenseType.slideUp();
    $.getJSON( url, function( data ) {
		
		Content_Pa_Deposits_Others += '<div id="">\
									<table class="table table-bordered abDataTable" id="pa_AccountingStudentListTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Class</th><th class="text-center">Reg Number</th><th class="text-center">action</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:left">&nbsp;<!--<img id="paAccountingNewStudentOnFee" src="packages/assets/img/add.png" alt="new" />--></th><th></th><th colspan=2></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //$.each(data.FeeType, function(i, item){
	  $("#myModalLabel22").html("Type:" + data.FeeType.name + "<br />Total Student Supported: " + formatNumber(data.FeeType.numberOfStudent) + "<br />From:" + data.FeeType.created_at);
	 // });
	 //console.log(data.Students);
      $.each( data.Students, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+ item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.name + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.ClassName +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.regNumber +" </td>";
            
			Content_Pa_Deposits_Others +="<td class='text-right' style='cursor:pointer;' onclick='DeleteStudentFromOrganizationSponsor(" + item.id + ", " + orgId + ")'> Remove</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table></div>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_AccountingStudentListTable').DataTable( {
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
	  /* Display again */
	  pa_Expense_ExpenseType.slideDown();
	  
    })
    .fail(function() {
    })
    .always(function() {

    });
}

/* Current Fee Student List should appear as modal */
function CurrentFeeStudentList(FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate, Student_WithThisFee, Student_WithoutFee ){
	//modify the content before display the modal with data from the database
	var modal_container = $("#ModalDataLoadingNow");//.html("Data Modified And Every Thing is fine!");
	var content = '\
		<div class="col-xs-12" >\
			<div id="pa_Fee_Student_List">\
				Loading student list...\
			</div>\
		</div>\
	';

	/* Send Ajax request to return the list of student who are involved in paying */
	//ajust the content now
	modal_container.html(content);
	var Content_Pa_Deposits_Others  = '';
	var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');
	Content_Pa_Deposits_Others += '<form id="formAddStudentToCurrentFee" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
								<div class="row top-buffer">\
									<div class="col-sm-9">\
										<input type="hidden" name="termlyFeeId" id="termlyFeeId" value="' + FeeId +'" />\
										<input type="hidden" name="_method" id="termlyFeeId" value="PUT" />\
										<select name="termlyInvoiceStudents[]" class="form-control" id="termlyInvoiceStudents" placeholder="Choose student here" multiple >\
											';
						var class_id = ""; var clos = false; var started = false;
						$.each(Student_WithoutFee, function(i, item){
							if(class_id != item.annualClassroomID){
								if(started){
									Content_Pa_Deposits_Others += '</optgroup>';
								}
								Content_Pa_Deposits_Others += '<optgroup label="' + item.className + '">';
								class_id = item.annualClassroomID;
								started = true;
								clos = true;
							}
							Content_Pa_Deposits_Others += '<option value=' + item.studRegNber + '>' + item.studentNames + " (" + item.studRegNber + ')</option>';
							
						});
						if(clos){
							Content_Pa_Deposits_Others += '</optgroup>';
						}
		 Content_Pa_Deposits_Others += '</select>\
									</div>\
									<div class="col-sm-3">\
										<button type="submit" class="btn btn-primary" id="paAccountingAddStudentToFeePaymentSubmit" >Add to List</button>\
									</div>\
								</div>\
								</form><div id=studentnfocontainner></div>';

	pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
	/* End of Ajax Request */
	$('#Model_DashPaFeeStudentList').modal({ show: true});
	$('#Model_DashPaFeeStudentList').on('hidden.bs.modal', function () {
		load_Accounting_School_Fees_Current_Fees();
	});
	/* Activate Button that are the appered modal */
	AccountingStudentOnFeeJS();
	DetailsTableData( FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate, Student_WithThisFee );

	//alert($("#paAccountingNewStudentOnFee").attr('src'));
	validateAddStudent(FeeId);
	
}

/* Current Fee Student List should appear as modal */
function DebtPaymentForm(debtId, debtData){
	//Here try to load the school cash account now
	var url = base_url+"accounting/staff/";
	var cashAccount = ""
	$.getJSON( url, function( data ) {
		$.each( data, function(i, item) {
			cashAccount += "<option value='" + item.id + "'>" + item.staffName + "</option>";
		});
	}).done(function() {
		// here find the list of active bank account
		var url = base_url+"accounting/assets/2";
		var activebank = ""
		$.getJSON( url, function( banks ) {
			var lastprint = "";
			$.each( banks, function(i, item) {
				
				activebank += "<option value='" + item.id + "'>" + item.AccountName + "</option>";
				
			});
		}).done(function() {
			//modify the content before display the modal with data from the database
			var modal_container = $("#ModalDataLoadingNow");//.html("Data Modified And Every Thing is fine!");
			var content = '\
					<div id="pa_Fee_Student_List">\
						Student List Here\
					</div>\
			';
			/* Send Ajax request to return the list of student who are involved in paying */
			//ajust the content now
			modal_container.html(content);
			var Content_Pa_Deposits_Others  = '';
			var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');
			Content_Pa_Deposits_Others += '\
										<!-- ';
				/* $.each(sts, function(i, kitem){
					Content_Pa_Deposits_Others += '<optgroup label="' + i + '">';
					$.each(kitem, function (k, item){
						Content_Pa_Deposits_Others += '<option>' + item.registrationNumber + '</option>';
					});
					Content_Pa_Deposits_Others += '</optgroup>';
				}); */
				 Content_Pa_Deposits_Others += '\
											-->\
										<div class="form-group" style="margin-top:5px;" > \
											<div class="col-sm-3 text-right">Amount</div>\
											<div class="col-sm-7">\
												<input type=hidden name=_method value="PUT" />\
												<input type=hidden name=debtIdNow value="' + debtId + '" />\
												<input type=hidden name=update_for_payment value="Pay_NOW" />\
												<input name="incomeAmountData" type="text" id="paNewIncomeAmount" value="' + debtData.amount + '" class="form-control" >\
											</div >\
										</div>\
										<div class="form-group" >\
										<label class="col-sm-3 control-label">Channel</label>\
										<div class="col-sm-1"></div>\
										<div class="col-sm-4">\
											<div class="radio">\
												<label>\
													<input type="radio" checked name="incomeChannel" id="paNewIncomeChannel" value="0" /> Cash\
												</label>\
											</div>\
										</div>\
										<div class="col-sm-4">\
										  <div class="radio">\
												<label>\
													<input type="radio" name="incomeChannel" id="paNewIncomeChannel" value="1" /> Bank Deposit\
												</label>\
											</div>\
										</div>\
										<div  id="PaNewIncomeBankContainer">\
											<div class="form-group" > \
												<div class="col-sm-3 text-right">Account</div>\
												<div class="col-sm-7">\
													<select name="incomeBankAccount" class="form-control" id="paNewIncomeBankAccount" placeholder="Choose Bank Account here" >\
														<option></option>' + activebank + '\
													</select>\
												</div >\
											</div>\
											<div class="form-group" > \
												<div class="col-sm-3 text-right">Bank Slip Number</div>\
												<div class="col-sm-7">\
													<input name="bankSlipNumber" type="text" id="paNewPaymentBankSlipNumber" class="form-control" >\
												</div >\
											</div>\
											<div class="form-group" > \
												<div class="col-sm-3 text-right"> Bank Operation Type</div>\
												<div class="col-sm-7">\
													<select name="incomeBankOperationType" class="form-control" id="paNewIncomeBankOperationType" placeholder="Choose Income Bank Operation Type" >\
														<option></option>\
														<option value="1" > Deposit</option>\
														<option value="2" > Transfer</option>\
														<option value="3" > Check</option>\
													</select>\
												</div >\
											</div>\
										</div>\
										<div  id="PaNewIncomeCashContainer">\
											<div class="form-group" > \
												<div class="col-sm-3 text-right">Staff</div>\
												<div class="col-sm-7">\
													<select name="incomeCashAccount" class="form-control" id="AccountingStaffAccountID" placeholder="Choose Bank Account here" >\
													' + cashAccount + '</select>\
												</div >\
											</div>\
										</div>\
										<div class="form-group" > \
											<div class="col-sm-3 text-right">Date</div>\
											<div class="col-sm-7">\
											  <input name="incomeDate" type="text" id="paNewIncomeDateDebtP" class="form-control" >\
											</div >\
										</div>\
									</div>\
								<div id=studentnfocontainner></div>';
			pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
			/* End of Ajax Request */
			$('#Model_DashPaFeeStudentList').modal({ show: true});
			$('#Model_DashPaFeeStudentList').on('hidden.bs.modal', function () {
				load_Accounting_School_Fees_Debts();
			});
			/* Activate Button that are the appered modal */
			//AccountingStudentOnFeeJS();
			//DetailsTableData(FeeId);
			//alert($("#paAccountingNewStudentOnFee").attr('src'));
			validatePayDebt(debtId);
			
		});
	});
	
}

/* Current Fee Student List should appear as modal */
function OverDuePaymentForm(invoiceId, invoiceData){
	//Here try to load the school cash account now
	var url = base_url+"accounting/staff/";
	var cashAccount = ""
	$.getJSON( url, function( data ) {
		$.each( data, function(i, item) {
			cashAccount += "<option value='" + item.id + "'>" + item.staffName + "</option>";
		});
	}).done(function() {
		// here find the list of active bank account
		var url = base_url+"accounting/assets/2";
		var activebank = ""
		$.getJSON( url, function( banks ) {
			var lastprint = "";
			$.each( banks, function(i, item) {
				
				activebank += "<option value='" + item.id + "'>" + item.AccountName + "</option>";
				
			});
		}).done(function() {
			//modify the content before display the modal with data from the database
			var modal_container = $("#ModalDataLoadingNow");//.html("Data Modified And Every Thing is fine!");
			var content = '\
					<div id="pa_Fee_Student_List">\
						Student List Here\
					</div>\
			';
			/* Send Ajax request to return the list of student who are involved in paying */
			//ajust the content now
			modal_container.html(content);
			var Content_Pa_Deposits_Others  = '';
			var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');
			Content_Pa_Deposits_Others += '\
										<!-- ';
				/* $.each(sts, function(i, kitem){
					Content_Pa_Deposits_Others += '<optgroup label="' + i + '">';
					$.each(kitem, function (k, item){
						Content_Pa_Deposits_Others += '<option>' + item.registrationNumber + '</option>';
					});
					Content_Pa_Deposits_Others += '</optgroup>';
				}); */
				 Content_Pa_Deposits_Others += '\
											-->\
										<div class="form-group" style="margin-top:5px;" > \
											<div class="col-sm-3 text-right">Amount</div>\
											<div class="col-sm-7">\
												<input type=hidden name=_method value="PUT" />\
												<input type=hidden name=invoiceIdNow value="' + invoiceId + '" />\
												<input type=hidden name=update_for_payment value="Pay_NOW" />\
												<input name="incomeAmountData" type="text" id="paNewIncomeAmount" value="' + invoiceData.amount + '" class="form-control" >\
											</div >\
										</div>\
										<div class="form-group" >\
										<label class="col-sm-3 control-label">Channel</label>\
										<div class="col-sm-1"></div>\
										<div class="col-sm-4">\
											<div class="radio">\
												<label>\
													<input type="radio" checked name="incomeChannel" id="paNewIncomeChannel" value="0" /> Cash\
												</label>\
											</div>\
										</div>\
										<div class="col-sm-4">\
										  <div class="radio">\
												<label>\
													<input type="radio" name="incomeChannel" id="paNewIncomeChannel" value="1" /> Bank Deposit\
												</label>\
											</div>\
										</div>\
										<div  id="PaNewIncomeBankContainer">\
											<div class="form-group" > \
												<div class="col-sm-3 text-right">Account</div>\
												<div class="col-sm-7">\
													<select name="incomeBankAccount" class="form-control" id="paNewIncomeBankAccount" placeholder="Choose Bank Account here" >\
														<option></option>' + activebank + '\
													</select>\
												</div >\
											</div>\
											<div class="form-group" > \
												<div class="col-sm-3 text-right">Bank Slip Number</div>\
												<div class="col-sm-7">\
													<input name="bankSlipNumber" type="text" id="paNewPaymentBankSlipNumber" class="form-control" >\
												</div >\
											</div>\
											<div class="form-group" > \
												<div class="col-sm-3 text-right"> Bank Operation Type</div>\
												<div class="col-sm-7">\
													<select name="incomeBankOperationType" class="form-control" id="paNewIncomeBankOperationType" placeholder="Choose Income Bank Operation Type" >\
														<option></option>\
														<option value="1" > Deposit</option>\
														<option value="2" > Transfer</option>\
														<option value="3" > Check</option>\
													</select>\
												</div >\
											</div>\
										</div>\
										<div  id="PaNewIncomeCashContainer">\
											<div class="form-group" > \
												<div class="col-sm-3 text-right">Staff</div>\
												<div class="col-sm-7">\
													<select name="incomeCashAccount" class="form-control" id="AccountingStaffAccountID" placeholder="Choose Bank Account here" >\
													' + cashAccount + '</select>\
												</div >\
											</div>\
										</div>\
										<div class="form-group" > \
											<div class="col-sm-3 text-right">Date</div>\
											<div class="col-sm-7">\
											  <input name="incomeDate" type="text" id="paNewIncomeDateDebtP" class="form-control" >\
											</div >\
										</div>\
									</div>\
								<div id=studentnfocontainner></div>';
			pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
			/* End of Ajax Request */
			$('#Model_DashPaFeeStudentList').modal({ show: true});
			$('#Model_DashPaFeeStudentList').on('hidden.bs.modal', function () {
				load_Accounting_School_Fees_Over_Due_Payment();
			});
			/* Activate Button that are the appered modal */
			//AccountingStudentOnFeeJS();
			//DetailsTableData(FeeId);
			//alert($("#paAccountingNewStudentOnFee").attr('src'));
			validatePayOverDue(invoiceId);
			
		});
	});
	
}

/* Current Fee Student List should appear as modal */
function InvoicePaymentForm(invoiceId, invoiceData){
	//Here try to load the school cash account now
	var url = base_url+"accounting/staff/";
	var cashAccount = ""
	$.getJSON( url, function( data ) {
		$.each( data, function(i, item) {
			cashAccount += "<option value='" + item.id + "'>" + item.staffName + "</option>";
		});
	}).done(function() {
		// here find the list of active bank account
		var url = base_url+"accounting/assets/2";
		var activebank = ""
		$.getJSON( url, function( banks ) {
			var lastprint = "";
			$.each( banks, function(i, item) {
				
				activebank += "<option value='" + item.id + "'>" + item.AccountName + "</option>";
				
			});
		}).done(function() {
			//modify the content before display the modal with data from the database
			var modal_container = $("#ModalDataLoadingNow");//.html("Data Modified And Every Thing is fine!");
			var content = '\
					<div id="pa_Fee_Student_List">\
						Student List Here\
					</div>\
			';
			/* Send Ajax request to return the list of student who are involved in paying */
			//ajust the content now
			modal_container.html(content);
			var Content_Pa_Deposits_Others  = '';
			var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');
			Content_Pa_Deposits_Others += '\
										<!-- ';
				/* $.each(sts, function(i, kitem){
					Content_Pa_Deposits_Others += '<optgroup label="' + i + '">';
					$.each(kitem, function (k, item){
						Content_Pa_Deposits_Others += '<option>' + item.registrationNumber + '</option>';
					});
					Content_Pa_Deposits_Others += '</optgroup>';
				}); */
				 Content_Pa_Deposits_Others += '\
											-->\
										<div class="form-group" style="margin-top:5px;" > \
											<div class="col-sm-3 text-right">Amount</div>\
											<div class="col-sm-7">\
												<input type=hidden name=_method value="PUT" />\
												<input type=hidden name=invoiceIdNow value="' + invoiceId + '" />\
												<input type=hidden name=update_for_payment value="Pay_NOW" />\
												<input name="incomeAmountData" type="text" id="paNewIncomeAmount" value="' + invoiceData.amount + '" class="form-control" >\
											</div >\
										</div>\
										<div class="form-group" >\
										<label class="col-sm-3 control-label">Channel</label>\
										<div class="col-sm-1"></div>\
										<div class="col-sm-4">\
											<div class="radio">\
												<label>\
													<input type="radio" checked name="incomeChannel" id="paNewIncomeChannel" value="0" /> Cash\
												</label>\
											</div>\
										</div>\
										<div class="col-sm-4">\
										  <div class="radio">\
												<label>\
													<input type="radio" name="incomeChannel" id="paNewIncomeChannel" value="1" /> Bank Deposit\
												</label>\
											</div>\
										</div>\
										<div  id="PaNewIncomeBankContainer">\
											<div class="form-group" > \
												<div class="col-sm-3 text-right">Account</div>\
												<div class="col-sm-7">\
													<select name="incomeBankAccount" class="form-control" id="paNewIncomeBankAccount" placeholder="Choose Bank Account here" >\
														<option></option>' + activebank + '\
													</select>\
												</div >\
											</div>\
											<div class="form-group" > \
												<div class="col-sm-3 text-right">Bank Slip Number</div>\
												<div class="col-sm-7">\
													<input name="bankSlipNumber" type="text" id="paNewPaymentBankSlipNumber" class="form-control" >\
												</div >\
											</div>\
											<div class="form-group" > \
												<div class="col-sm-3 text-right"> Bank Operation Type</div>\
												<div class="col-sm-7">\
													<select name="incomeBankOperationType" class="form-control" id="paNewIncomeBankOperationType" placeholder="Choose Income Bank Operation Type" >\
														<option></option>\
														<option value="1" > Deposit</option>\
														<option value="2" > Transfer</option>\
														<option value="3" > Check</option>\
													</select>\
												</div >\
											</div>\
										</div>\
										<div  id="PaNewIncomeCashContainer">\
											<div class="form-group" > \
												<div class="col-sm-3 text-right">Staff</div>\
												<div class="col-sm-7">\
													<select name="incomeCashAccount" class="form-control" id="AccountingStaffAccountID" placeholder="Choose Bank Account here" >\
													' + cashAccount + '</select>\
												</div >\
											</div>\
										</div>\
										<div class="form-group" > \
											<div class="col-sm-3 text-right">Date</div>\
											<div class="col-sm-7">\
											  <input name="incomeDate" type="text" id="paNewIncomeDateDebtP" class="form-control" >\
											</div >\
										</div>\
									</div>\
								<div id=studentnfocontainner></div>';
			pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
			/* End of Ajax Request */
			$('#Model_DashPaFeeStudentList').modal({ show: true});
			$('#Model_DashPaFeeStudentList').on('hidden.bs.modal', function () {
				load_Accounting_List_Invoice();
			});
			/* Activate Button that are the appered modal */
			//AccountingStudentOnFeeJS();
			//DetailsTableData(FeeId);
			//alert($("#paAccountingNewStudentOnFee").attr('src'));
			validatePayInvoice(invoiceId);
			
		});
	});
	
}

/* Current Fee Student List should appear as modal */
function BillPaymentForm(billId, billData){
	//Here try to load the school cash account now
	var url = base_url+"accounting/staff/";
	var cashAccount = ""
	$.getJSON( url, function( data ) {
		$.each( data, function(i, item) {
			cashAccount += "<option value='" + item.id + "'>" + item.staffName + "</option>";
		});
	}).done(function() {
		// here find the list of active bank account
		var url = base_url+"accounting/assets/2";
		var activebank = ""
		$.getJSON( url, function( banks ) {
			var lastprint = "";
			$.each( banks, function(i, item) {
				
				activebank += "<option value='" + item.id + "'>" + item.AccountName + "</option>";
				
			});
		}).done(function() {
			//modify the content before display the modal with data from the database
			var modal_container = $("#ModalDataLoadingNow");//.html("Data Modified And Every Thing is fine!");
			var content = '\
					<div id="pa_Fee_Student_List">\
						Student List Here\
					</div>\
			';
			/* Send Ajax request to return the list of student who are involved in paying */
			//ajust the content now
			modal_container.html(content);
			var Content_Pa_Deposits_Others  = '';
			var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');
			Content_Pa_Deposits_Others += '\
										<!-- ';
				/* $.each(sts, function(i, kitem){
					Content_Pa_Deposits_Others += '<optgroup label="' + i + '">';
					$.each(kitem, function (k, item){
						Content_Pa_Deposits_Others += '<option>' + item.registrationNumber + '</option>';
					});
					Content_Pa_Deposits_Others += '</optgroup>';
				}); */
				 Content_Pa_Deposits_Others += '\
											-->\
										<div class="form-group" style="margin-top:5px;" > \
											<div class="col-sm-4 text-right">Amount</div>\
											<div class="col-sm-7">\
												<input type=hidden name=_method value="PUT" />\
												<input type=hidden name=billIdNow value="' + billId + '" />\
												<input type=hidden name=update_for_payment value="Pay_NOW" />\
												<input name="expenseAmountData" type="text" id="paNewIncomeAmount" value="' + billData.amount + '" class="form-control" >\
											</div >\
										</div>\
										<div class="form-group" >\
											<label class="col-sm-4 control-label">Channel</label>\
											<div class="col-sm-4 text-center">\
												<div class="radio">\
													<label>\
														<input type="radio" checked name="expenseChannel" id="paNewIncomeChannel" value="0" /> Cash\
													</label>\
												</div>\
											</div>\
											<div class="col-sm-4">\
											  <div class="radio">\
													<label>\
														<input type="radio" name="expenseChannel" id="paNewIncomeChannel" value="1" /> Bank Deposit\
													</label>\
												</div>\
											</div>\
										</div>\
										<div  id="PaNewIncomeBankContainer">\
											<div class="form-group" > \
												<div class="col-sm-4 text-right">Account</div>\
												<div class="col-sm-7">\
													<select name="expenseBankAccount" class="form-control" id="paNewIncomeBankAccount" placeholder="Choose Bank Account here" >\
														<option></option>' + activebank + '\
													</select>\
												</div >\
											</div>\
											<div class="form-group" > \
												<div class="col-sm-4 text-right">Bank Slip Number</div>\
												<div class="col-sm-7">\
													<input name="bankSlipNumber" type="text" id="paNewPaymentBankSlipNumber" class="form-control" >\
												</div >\
											</div>\
											<div class="form-group" > \
												<div class="col-sm-4 text-right"> Bank Operation Type</div>\
												<div class="col-sm-7">\
													<select name="expenseBankOperationType" class="form-control" id="paNewIncomeBankOperationType" placeholder="Choose Income Bank Operation Type" >\
														<option></option>\
														<option value="1" > Deposit</option>\
														<option value="2" > Transfer</option>\
														<option value="3" > Check</option>\
													</select>\
												</div >\
											</div>\
										</div>\
										<div  id="PaNewIncomeCashContainer">\
											<div class="form-group" > \
												<div class="col-sm-4 text-right">Staff</div>\
												<div class="col-sm-7">\
													<select name="expenseCashAccount" class="form-control" id="AccountingStaffAccountID" placeholder="Choose Bank Account here" >\
													' + cashAccount + '</select>\
												</div >\
											</div>\
										</div>\
										<div class="form-group" > \
											<div class="col-sm-4 text-right">Memo</div>\
											<div class="col-sm-7">\
											  <input name="expenseMemo" type="text" id="paNewExpenseMemo" class="form-control" >\
											</div >\
										</div>\
										<div class="form-group" > \
											<div class="col-sm-4 text-right">Date</div>\
											<div class="col-sm-7">\
											  <input name="expenseDate" type="text" id="paNewIncomeDateDebtP" class="form-control" >\
											</div >\
										</div>\
									</div>\
								<div id=studentnfocontainner></div>';
			pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
			/* End of Ajax Request */
			$('#Model_DashPaFeeStudentList').modal({ show: true});
			$('#Model_DashPaFeeStudentList').on('hidden.bs.modal', function () {
				load_Accounting_List_Bills();
			});
			/* Activate Button that are the appered modal */
			//AccountingStudentOnFeeJS();
			//DetailsTableData(FeeId);
			//alert($("#paAccountingNewStudentOnFee").attr('src'));
			validatePayBill(billId);
			
		});
	});
	
}

/* Current Fee Student List should appear as modal */
function SponsorStudentList(orgID, sts){
	//modify the content before display the modal with data from the database
	var modal_container = $("#ModalDataLoadingNow");//.html("Data Modified And Every Thing is fine!");
	var content = '\
		<div class="col-xs-12" >\
			<div id="pa_Fee_Student_List">\
				Student List Here\
			</div>\
		</div>\
	';
	/* Send Ajax request to return the list of student who are involved in paying */
	//ajust the content now
	modal_container.html(content);
	var Content_Pa_Deposits_Others  = '';
	var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');
	Content_Pa_Deposits_Others += '<form id="formAddStudentToOrganization" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
								<div class=row>\
									<div class="col-sm-6">\
										<input type="hidden" name="organizationId" id="organizationId" value="' + orgID +'" />\
										<input type="hidden" name="_method" id="termlyFeeId" value="PUT" />\
										<select name="organizationStudents[]" class="form-control" id="organizationStudentsList" placeholder="Choose student here" multiple >\
											';
		//console.log(sts);
		//console.log(sts);
		var class_name = ""; var clos = false; var started = false;
		$.each(sts, function(i, item){
			if(class_name != item.ClassName){
				if(started){
					Content_Pa_Deposits_Others += '</optgroup>';
				}
				Content_Pa_Deposits_Others += '<optgroup label="' + item.ClassName + '">';
				class_name = item.ClassName;
				started = true;
				clos = true;
			}
			Content_Pa_Deposits_Others += '<option value=' + item.registrationNumber + '>' + item.registrationNumber + "(" + item.name + ')</option>';
			//Content_Pa_Deposits_Others += '<option value=' + item.id + '>' + item.registrationNumber + "(" + item.name + ')</option>';
			
		});
		if(clos){
			Content_Pa_Deposits_Others += '</optgroup>';
		}
		 Content_Pa_Deposits_Others += '</select>\
									</div>\
									<div class="col-sm-6">\
										<button type="submit" class="btn btn-primary" id="paAccountingAddStudentToFeePaymentSubmit" >Add to List</button>\
									</div>\
								</div>\
								</form><div id=studentnfocontainner></div>';
	pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
	/* End of Ajax Request */
	$('#Model_DashPaSponsorStudentList').modal({ show: true});
	$('#Model_DashPaSponsorStudentList').on('hidden.bs.modal', function () {
		load_Accounting_List_Organization();
	});
	/* Activate Button that are the appered modal */
	DetailsTableDataForSponsorship(orgID);
	//alert($("#paAccountingNewStudentOnFee").attr('src'));
	validateAddStudentSponsor(orgID);
	
}

/* Current Fee Student List should appear as modal */
function ChangeStudentSponsorshipData(orgID, sts, termid){
	//modify the content before display the modal with data from the database
	var modal_container = $("#ModalDataLoadingNow");//.html("Data Modified And Every Thing is fine!");
	var content = '\
		<div class="col-xs-12" >\
			<div id="pa_Fee_Student_List">\
				Student List Here\
			</div>\
		</div>\
	';
	/* Send Ajax request to return the list of student who are involved in paying */
	//ajust the content now
	modal_container.html(content);
	var Content_Pa_Deposits_Others  = '';
	var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');
	Content_Pa_Deposits_Others += '<form id="formAddStudentToOrganizationInfo" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
								<div class=row>\
									<div class="col-sm-6">\
										<input type="hidden" name="organizationId" id="organizationId" value="' + orgID +'" />\
										<input type="hidden" name="termId" id="termId" value="' + termid +'" />\
										<input type="hidden" name="_method" id="termlyFeeId" value="PUT" />\
										Enrolled Sponsored Students<br />\
										<select name="organizationStudentsData[]" class="form-control" id="organizationStudentsList" placeholder="Choose student here" multiple >\
											';
		//console.log(sts);
		var class_name = ""; var clos = false; var started = false;
		$.each(sts, function(i, item){
			if(class_name != item.ClassName){
				if(started){
					Content_Pa_Deposits_Others += '</optgroup>';
				}
				Content_Pa_Deposits_Others += '<optgroup label="' + item.ClassName + '">';
				class_name = item.ClassName;
				started = true;
				clos = true;
			}
			Content_Pa_Deposits_Others += '<option value=' + item.id + '>' + item.registrationNumber + "(" + item.name + ')</option>';
			//Content_Pa_Deposits_Others += '<option value=' + item.id + '>' + item.registrationNumber + "(" + item.name + ')</option>';
			
		});
		if(clos){
			Content_Pa_Deposits_Others += '</optgroup>';
		 Content_Pa_Deposits_Others += '</select>\
									</div>\
									<div class="col-sm-3">\
										Amount<br />\
										<input type="text" class="form-control" id="organizationPayableAmount" name="studentGroupPayment" />\
									</div>\
									<div class="col-sm-3">\
										&nbsp;<br />\
										<button type="submit" class="btn btn-primary" id="paAccountingAddStudentToFeePaymentSubmit" >Save Money</button>\
									</div>\
								</div>\
								</form><div style="margin-top:5px;" id=studentnfocontainner></div>';
		}
	pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
	/* End of Ajax Request */
	$('#Model_DashPaSponsorStudentList').modal({ show: true});
	$('#Model_DashPaSponsorStudentList').on('hidden.bs.modal', function () {
		load_Accounting_List_Organization();
	});
	/* Activate Button that are the appered modal */
	DetailsTableDataForSponsorshipData(orgID, termid);
	//alert($("#paAccountingNewStudentOnFee").attr('src'));
	validateAddStudentSponsorInfo(orgID, termid);
	
}

/* Load the Uplaod Form the Debt Tab Now */
function load_Accounting_School_Fees_New_Debt_Upload(){
	$(".left_menu").removeClass('active');
	$("#debtss").addClass('active');

	//get the instance of container division 
	var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
	//get the instance of the content division
	var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
	
	//hide the container first
	dashClientContainer.slideUp();
	
	//prepare the content to be printed in the containner
	
	var content = '';
				content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
					content +='<div class="row text-center">';
						content+= '<div class="col-sm-12 text-center">';
							content+= '<h4>Upload Debt Information</h4>';
						content+= '</div>';
						content+= '<div class="col-sm-4">';
							//content+= '<a id=AccountingSchoolFeesMoreDebt class="btn btn-primary pull-right"><i class="fa fa-bars"></i>&nbsp;&nbsp;More</a>';
						content+= '</div>';
					content+= '</div>';
					content+= '<div class="row top-buffer" >\
									<div id="pa_Expense_ExpenseType">\
										<form enctype="form/multipart" id="formUplaodSchoolFeesPayment" class="form-horizontal" style="">\
											<div class="form-group" >\
												<div class="col-sm-3"></div>\
												<div class="col-sm-3"><input data-fv-file="true" type=file name=payment_list /></div>\
												<div class="col-sm-2"><input type=submit class="btn btn-primary pull-right" name=upload value="Import Student Debts" /></div>\
											</div>\
										</form>\
									</div>\
								</div>\
							   ';
				content+= '</div>\
							';
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  /* Activate the new invoice buttons */
  //AccountingSchoolFeesNewCurrentCurrentJS();
  //AccountingSchoolFeesNewDebtJS();
  //validate the form 
  
  validateDebtUploadForm();
  $('#Model_DashPaFeeStudentList').modal({ show: false});
}


/* Current Fee Student List should appear as modal */
function SponsorStudentListInfo(orgId){
	//get the instance of container division 
	var dashClientContainer = $("#dashPayments_content");
	//get the instance of the content division
	var dashContentainer_Payments = $("#dashContentainer_Payments");
	
	//hide the container first
	dashClientContainer.slideUp();
	
	//modify the content before display the modal with data from the database
	var modal_container = $("#ModalDataLoadingNow");//.html("Data Modified And Every Thing is fine!");
	var content = '\
	<div class="col-md-12">\
		<div class="grid email">\
			<div class="grid-body">\
				<div class="row">\
					<div class="col-sm-12">\
						<div class="row text-center">\
							<div class="col-sm-8">\
								<h4>Currently Supported Student</h4>\
							</div>\
							<div class="col-sm-4">\
								<a id=AccountingNewSponsorList class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;Sponsor List</a>\
							</div>\
						</div>\
						<div class="row">\
							<div class="col-sm-12">\
								<div id="pa_Expense_ExpenseType">\
									Student List Now....\
								</div>\
							</div>\
						</div>\
					</div>\
				</div>\
			</div>\
		</div>\
	</div>';
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
    //load the contetn now
    dashContentainer_Payments.slideDown();
    dashClientContainer.slideDown();
	
	var url = base_url + "accounting/sponsors/-" + orgId;

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');
	pa_Expense_ExpenseType.slideUp();
    $.getJSON( url, function( data ) {
		
		Content_Pa_Deposits_Others += '<div id="">\
		<table class="table table-bordered abDataTable" id="pa_AccountingStudentListTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Class</th><th class="text-center">Reg Number</th><th class="text-center">action</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:left">&nbsp;<!--<img id="paAccountingNewStudentOnFee" src="packages/assets/img/add.png" alt="new" />--></th><th></th><th colspan=2></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //$.each(data.FeeType, function(i, item){
	  $("#myModalLabel22").html("Type:" + data.FeeType.name + "<br />Total Student Supported: " + formatNumber(data.FeeType.numberOfStudent) + "<br />From:" + data.FeeType.created_at);
	 // });
	 //console.log(data.Students);
      $.each( data.Students, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+ item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.name + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.ClassName +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.regNumber +" </td>";
            
			Content_Pa_Deposits_Others +="<td class='text-right' style='cursor:pointer;' onclick='DeleteStudentFromOrganizationSponsor(" + item.id + ", " + orgId + ")'> Remove</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table></div>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_AccountingStudentListTable').DataTable( {
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
	  /* Display again */
	  pa_Expense_ExpenseType.slideDown();
	  
    })
    .fail(function() {
    })
    .always(function() {

    });
	/* Activate Button that are the appered modal */
	//DetailsTableDataForSponsorshipInfo(orgID);
	//alert($("#paAccountingNewStudentOnFee").attr('src'));
	//validateAddStudentSponsor(orgID);
	validateAddSponsor();
	
}

/* Load the Invoice List function */
function load_Accounting_School_Fees_New_Debt(){
	//try to change the style sheet and check
	$(".left_menu").removeClass('active');
	$("#debtss").addClass('active');
	
	var url2 = base_url+"accounting/students/";
	/* load the student list now */
	$.getJSON( url2, function( student ) {
		/* load the form by passing received data the function */
		//NewCurrentFeeForm(data, student, terms);
		NewDebtForm(student);
	});
}


/* Load the Invoice List function */
function load_Accounting_School_Fees_New_Over_Due_Payment(){
	//try to change the style sheet and check
	$(".left_menu").removeClass('active');
	$("#overdue").addClass('active');
	
	var url2 = base_url+"accounting/students/";
	/* load the student list now */
	$.getJSON( url2, function( student ) {
		/* load the form by passing received data the function */
		//NewCurrentFeeForm(data, student, terms);
		overDueNewForm(student);
	});
}



function NewDebtForm(sts){
	//get the instance of container division 
	var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
	//get the instance of the content division
	var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
	
	//hide the container first
	dashClientContainer.slideUp();
	
	//prepare the content to be printed in the containner
	
	var content = '';
				content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
					content +='<div class="row text-center">';
						content+= '<div class="col-sm-8">';
							content+= '<h4> New Fee Debt</h4>';
						content+= '</div>';
						content+= '<div class="col-sm-4">';
							//content+= '<a id=AccountingSchoolFeesMoreDebt class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a>';
						content+= '</div>';
					content+= '</div>';
					content+= '<div class="row" >\
									<div id="pa_Expense_ExpenseType">\
										\
										<div class="row" id="formDashPaNewSchoolDebtLoading"><div class="col-sm-12 text-center">Saving...</div></div >\
					                      <form id="formDashPaNewSchoolDebt" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
					                        <div class="modal-body" >\
												<div class="form-group" > \
													<div class="col-sm-3 text-right">Student</div>\
													<div class="col-sm-7">\
														<select name="studentWithDebt[]" class="form-control" id="AccountingSchoolFeesNewDebtStudent" placeholder="Choose student here" multiple >\
															';
											content += PopulateStudentInClassOption(sts);
											content+= '</select>\
													</div >\
												</div>\
												<div class="form-group" > \
													<div class="col-sm-3 text-right">Description</div>\
													<div class="col-sm-7">\
														<input name="debtDescription" type="text" id="debtDescription" class="form-control" >\
													</div >\
												</div>\
												<div class="form-group" > \
													<div class="col-sm-3 text-right">Amount</div>\
													<div class="col-sm-7">\
														<input name="debtAmount" type="text" id="debtAmount" class="form-control" >\
													</div >\
												</div>\
					                        </div>\
					                        <div class="modal-footer" >\
					                          <div class="pull-right btn-group">\
					                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Debt</button>\
					                          </div>\
					                        </div>\
										</form>\
									</div>\
								</div>\
							   ';
				content+= '</div>\
							<div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
								<div class="modal-wrapper">\
									<div class="modal-dialog">\
										<div class="modal-content">\
											<div class="modal-header">\
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
												<h4 class="modal-title text-left" id="myModalLabel22">New Expense</h4>\
											</div>\
											<div id="ModalDataLoadingNow">\
											</div>\
											<div class="modal-footer" >\
												<div class="pull-right btn-group">\
													<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
													\
												</div>\
											</div>\
										</div>\
									</div>\
								</div>\
							</div>\
							';
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
	$("#formDashPaNewSchoolDebtLoading").hide();

  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  /* Activate the new invoice buttons */
  //AccountingSchoolFeesNewCurrentCurrentJS();
  //AccountingSchoolFeesNewDebtJS();
  //validate the form 
  
  validateNewDebt();
  $('#Model_DashPaFeeStudentList').modal({ show: false});
}

function overDueNewForm(sts){
	//get the instance of container division 
	var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
	//get the instance of the content division
	var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
	
	//hide the container first
	dashClientContainer.slideUp();
	
	//prepare the content to be printed in the containner
	
	var content = '';
				content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
					content +='<div class="row text-center">';
						content+= '<div class="col-sm-8">';
							content+= '<h4> New Over Due Payment</h4>';
						content+= '</div>';
						content+= '<div class="col-sm-4">';
							content+= '<a id=AccountingSchoolFeesMoreOverDue class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a>';
						content+= '</div>';
					content+= '</div>';
					content+= '<div class="row" >\
									<div id="pa_Expense_ExpenseType">\
										\
                      <form id="formDashPaNewIncome" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Student</div>\
								<div class="col-sm-7">\
									<select name="dashboardAcSelectExcludeStudentStudent[]" class="form-control" id="AccountingSchoolFeesNewDebtStudent" placeholder="Choose student here" multiple >\
									';
						$.each(sts, function (i, std){
							content += '<optgroup label="' + i + '">';
							$.each(std, function (k, item){
								content += '<option value="' + item.registrationNumber + '">' + item.registrationNumber + '</option>';
							});
							content += '</optgroup>';
						});
						content+= '</select>\
									</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Description</div>\
								<div class="col-sm-7">\
									<input name="invoiceDate" type="text" id="paNewInvoiceDate" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Amount</div>\
								<div class="col-sm-7">\
									<input name="invoiceDate" type="text" id="paNewInvoiceDate" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Due Date</div>\
								<div class="col-sm-7">\
									<input name="invoiceDate" type="text" id="paNewOverDueDate" class="form-control" >\
								</div >\
							</div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save</button>\
                          </div>\
                        </div>\
					</form>\
									</div>\
								</div>\
							   ';
				content+= '</div>\
							<div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
								<div class="modal-wrapper">\
									<div class="modal-dialog">\
										<div class="modal-content">\
											<div class="modal-header">\
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
												<h4 class="modal-title text-left" id="myModalLabel22">New Expense</h4>\
											</div>\
											<div id="ModalDataLoadingNow">\
											</div>\
											<div class="modal-footer" >\
												<div class="pull-right btn-group">\
													<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
													\
												</div>\
											</div>\
										</div>\
									</div>\
								</div>\
							</div>\
							';
	
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  /* Activate the new invoice buttons */
  //AccountingSchoolFeesNewCurrentCurrentJS();
  AccountingSchoolFeesNewOverDueJS();
  
  $('#Model_DashPaFeeStudentList').modal({ show: false});
}

/* Load the client form function */
function load_Accounting_New_Client(){
	//get the instance of container division 
	var dashClientContainer = $("#dashPayments_content");
	//get the instance of the content division
	var dashContentainer_Payments = $("#dashContentainer_Payments");
	
	//hide the container first
	dashClientContainer.slideUp();
	
	//prepare the content to be printed in the containner
	
	var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>New Client</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
              	content+= '<a id=AccountingNewClientList class="btn btn-default pull-right"><i class="fa fa-align-right"></i>&nbsp;&nbsp;Clients List</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                	  <div class="form-group" id="PaNewClientLoading"><center><div class="row"><img src="../packages/assets/img/loading.gif" alt="Loading..." height="35"></div><div class="row"><p>Saving...</p></div></center></div>\
                      <form id="formDashPaNewClient" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Name</div>\
								<div class="col-sm-7">\
									<input name="clientName" type="text" id="paNewClientName" class="form-control" >\
								</div >\
							</div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Phone</div>\
								<div class="col-sm-7">\
									<input name="clientPhone" type="text" id="paNewClientPhone" class="form-control" >\
								</div >\
							</div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Email</div>\
								<div class="col-sm-7">\
									<input name="clientEmail" type="email" id="paNewClientEmail" class="form-control" >\
								</div >\
							</div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Contact Person</div>\
								<div class="col-sm-7">\
									<input name="clientContactFname" placeholder="Contact Person" type="text" id="paNewClientContactPerson" class="form-control" >\
								</div >\
							</div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Client</button>\
                          </div>\
                        </div>\
					</form>\
                </div>\
              </div>\
            </div>\
          </div>\
        </div>\
      </div></div></div></div>';
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
	$("#PaNewClientLoading").hide();

  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  
  /* Activate the new Client buttons */
  validateNewClient();
}

function load_Accounting_New_Product_WeSell(){
	load_Accounting_New_Product(1);
}

function load_Accounting_New_Product_WeBuy(){
	load_Accounting_New_Product(2);
}

/* Load the product form function */
function load_Accounting_New_Product(type){
	//get the instance of container division 
	var dashClientContainer = $("#dashPayments_content");
	//get the instance of the content division
	var dashContentainer_Payments = $("#dashContentainer_Payments");
	
	//hide the container first
	//dashClientContainer.slideUp();
	
	//prepare the content to be printed in the containner
	
	var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>New Product</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewProductList class="btn btn-default pull-right"><i class="fa fa-bars"></i>&nbsp;&nbsp;Product List</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                	  <div class="form-group" id="PaNewProductLoading"><center><div class="row"><img src="../packages/assets/img/loading.gif" alt="Loading..." height="35"></div><div class="row"><p>Saving...</p></div></center></div>\
                      <form id="formDashPaNewProduct" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Product</div>\
								<div class="col-sm-7">\
									<input name="productName" type="text" id="paNewProductName" class="form-control" >\
								</div >\
							</div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Amount</div>\
								<div class="col-sm-7">\
									<input name="productAmount" type="text" id="paNewProductAmount" class="form-control" >\
								</div >\
							</div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Type</div>\
								<div class="col-sm-7">\
									<select name="productType" id="paNewProductType" class="form-control" >';

										if ( type == 1 ) {
											content+='<option value="0" selected>We Sell</option>';
											content+='<option value=1>We Buy</option>';
										}else if( type == 2 ){
											content+='<option value="0">We Sell</option>';
											content+='<option value="1" selected>We Buy</option>';
										}

									content+='</select>\
								</div >\
							</div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Product</button>\
                          </div>\
                        </div>\
					</form>\
                </div>\
            </div>\
            </div>\
          </div>\
        </div>\
      </div></div></div></div>';
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
	$("#PaNewProductLoading").hide();

  //load the contetn now
  dashContentainer_Payments.slideDown();
  //dashClientContainer.slideDown();
  
  /* Activate the new Client buttons */
  validateNewProduct(type);
}


/* Load the client form function */
function load_Accounting_New_Invoice(){
	//get the list of vendor before colling this new bill function

	var clients 	= [];
	var products 	= [];
	var countryTax 	= [];

	var url = base_url+"accounting/invoices/create";
    $.getJSON( url, function( data ) {

    	clients 	= data.clients;
    	products 	= data.products;
    	countryTax 	= data.countryTax;
		
		NewInvoiceForm(clients, products, countryTax);

    }).done(function() {

    }).fail(function() {
		
    }).always(function() {

    });

}

function NewInvoiceForm(data, products, taxs){
	//get the instance of container division 
	var dashClientContainer = $("#dashPayments_content");
	//get the instance of the content division
	var dashContentainer_Payments = $("#dashContentainer_Payments");
	
	//hide the container first
	dashClientContainer.slideUp();

	//prepare the content to be printed in the containner
	
	var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>New Invoice</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewInvoiceList class="btn btn-primary pull-right"><i class="fa fa-bars"></i>&nbsp;&nbsp;Invoice List</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">\
				<div id="pa_Expense_ExpenseType">\
					  <div class="form-group" id="PaNewInvoiceLoading"><center><div class="row"><img src="../packages/assets/img/loading.gif" alt="Loading..." height="35"></div><div class="row"><p>Saving...</p></div></center></div>\
                      <form id="formDashPaNewInvoice" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
							<div class="form-group" > \
                            	<div class="col-sm-2 text-right">Client</div>\
                            	<div class="col-sm-10">\
                              		<select name="invoiceClient" class="form-control" id="paNewInvoiceClient" placeholder="Choose Client Name" >';
                              		content+= "<option></option>";
									$.each(data, function(i, item){
										content+= "<option value='" + item.id + "'>" + item.name + "</option>";
									});
												
				          content+= '</select>\
                            	</div >\
                          	</div>\
                            <div class="form-group" > \
								<div class="col-sm-2 text-right">Invoice Nr.</div>\
								<div class="col-sm-10">\
									<input name="invoiceNumber" type="text" id="paNewInvoiceNumber" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-2 text-right">Invoice Date</div>\
								<div class="col-sm-10">\
									<input name="invoiceDate" type="text" id="paNewInvoiceDate" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-2 text-right">Payment Date</div>\
								<div class="col-sm-10">\
									<input name="paymentDate" type="text" id="paNewInvoicePaymentDate" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-12">\
									<div class="form-group" id="InvoiceProductItems" > \
										<div class="col-sm-3">\
											Product\
										</div >\
										<div class="col-sm-2">\
											Unit\
										</div >\
										<div class="col-sm-2">\
											Amount\
										</div >\
										<div class="col-sm-3">\
											Tax\
										</div >\
										<div class="col-sm-2">\
											Final Amount\
										</div >\
									</div>\
									<div id="AccountingNewInvoiceItems"> \
									</div>\
									<div class="form-group" id="InvoiceProductItems" >\
										<div class="col-sm-4">\
											<i class="fa fa-plus icon-4x" id="paAccountNewInvoiceItemAdd" style="color:green; cursor:pointer;" aria-hidden="true" title="Add New Product"></i>\
										</div>\
										<div class="col-sm-2">\
										</div>\
										<div class="col-sm-2">\
											Total\
										</div>\
										<div class="col-sm-4">\
											<input name="invoiceAmount" readonly type="text" id="paNewInvoicePaymentTotalAmount" class="form-control" >\
											<input name="totalItemsOnInvoice" type="hidden"  id="totalItemsOnInvoice" class="form-control" value="1" >\
										</div>\
									</div>\
								</div>\
							</div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Invoice</button>\
                          </div>\
                        </div>\
					</form>\
                </div>\
            </div>\
            </div>\
          </div>\
        </div>\
      </div></div></div></div>';
	//send the prepared content to the screen for user

	dashClientContainer.html( content );
	$("#PaNewInvoiceLoading").hide();

  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();

  AccountingNewInvoiceJS(products, taxs);
  
}

/* Active button of new invoice view */
function AccountingNewInvoiceJS(product_list, taxs){

	$("#paNewInvoiceClient").select2();

	var counter = 1;	
	
	//active the add new button 
	$("#paAccountNewInvoiceItemAdd").click(function(){
		//add a new row to the item table now
		var data = '\
			<div class="form-group" id="InvoiceProductItems" > \
				<div class="col-sm-3">\
					<select name="invoiceItemProductID_' + counter + '" class="form-control paNewInvoiceItemProduct' + counter + '" counter="'+ counter +'" id="paNewInvoiceItem' + counter + '" placeholder="Choose Product" >\
						<option></option>';
						$.each(product_list, function(i, item){
							data += "<option for='" + item.amount + "' data-amount="+ item.amount +" value='" + item.id + "'>" + item.name + "</option>";
						});
					data += '</select>\
				</div >\
				<div class="col-sm-2">\
					<input name="invoiceItemUnit_' + counter + '" type="text" id="paNewInvoiceUnit' + counter + '" class="form-control _paNewInvoiceUnit' + counter + ' _paNewInvoiceUnitTax' + counter + ' pa_NewInvoiceUnitTax' + counter + '" value="1" >\
				</div >\
				<div class="col-sm-2">\
					<input name="invoiceItemProductAmount_' + counter + '" type="text" id="paNewInvoicePrice' + counter + '" class="form-control _paNewInvoiceItem' + counter + ' _paNewInvoiceItemTax' + counter + ' pa_NewInvoiceItemTax' + counter + '" >\
				</div >\
				<div class="col-sm-3">\
					<select name="invoiceItemTax_' + counter + '" class="form-control paNewInvoiceItemTax' + counter + ' taxpaNewInvoiceItem' + counter + '" counter="'+ counter +'" id="pa_NewInvoiceItemTax' + counter + '" placeholder="Choose Tax" >\
						<option value="0"> None</option>';
			$.each(taxs, function(i, item){
				data += "<option value='" + item.id + "' data-percentage='"+ item.percentage +"'>" + item.name + " " + item.percentage + "%</option>";
			});
			data += '</select>\
				</div >\
				<div class="col-sm-2">\
					<input name="invoiceItemProductFinalAmount_' + counter + '" type="text" id="paNewInvoiceFinalAmount' + counter + '" class="form-control finalpaNewInvoiceItem' + counter + ' finalpaNewInvoiceItemTax' + counter + ' finalpaNewInvoicePrice' + counter + ' finalpa_NewInvoiceItemTax' + counter + '" >\
				</div >\
			</div>';
									
		$("#AccountingNewInvoiceItems").append(data);

		
		$("#paNewInvoicePrice" + counter + "").blur(counter, function(e){
		    	
		   	RecalculateTotal( e.data ,'Invoice');

		});
		$("#paNewInvoiceUnit" + counter + "").blur(counter, function(e){
		    	
		   	RecalculateTotal( e.data ,'Invoice');

		});

		$("#paNewInvoiceFinalAmount" + counter + "").blur(counter, function(e){
		    	
		   	RecalculateTotalReverse( e.data ,'Invoice');

		});

		var paNewInvoiceItemProduct = $(".paNewInvoiceItemProduct" + counter );

		paNewInvoiceItemProduct.select2({
			templateResult: formatInvoiceItemsList
		}).bind('change', function(){

				var clicked 				= $(this);

				var paNewInvoiceItemProduct = $("#paNewInvoiceItem"+clicked.attr("counter"));
				var selectedProduct         = paNewInvoiceItemProduct.select2('val');
				var selectedProductAmount 	= paNewInvoiceItemProduct.select2().find(":selected").data("amount");

				$("._" + clicked.attr("id")).val(selectedProductAmount);

				var tax 		= $(".tax" + clicked.attr("id")).val();
				var finalamount = $("._" + clicked.attr("id")).val() * (tax > 0?tax:1);
				var totalamount = ($("#paNewInvoicePaymentTotalAmount").val()*1) - $(".final" + clicked.attr('id')).val();
				
				$(".final" + clicked.attr("id")).val(finalamount);
				$("#paNewInvoicePaymentTotalAmount").val( ($(".final" + clicked.attr("id")).val()*1) + totalamount);
				$("#formDashPaNewInvoice").formValidation('revalidateField', 'invoiceAmount');

		});

		var paNewInvoiceItemTax = $(".paNewInvoiceItemTax" + counter );

		paNewInvoiceItemTax.select2({
			templateResult: formatInvoiceItemsList
		}).bind('change', function(){

				var clicked 				= $(this);

				var paNewInvoiceItemTax 	= $("#pa_NewInvoiceItemTax"+clicked.attr("counter"));
				var selectedTax        		= paNewInvoiceItemTax.select2('val');
				var selectedTaxPercentage 	= paNewInvoiceItemTax.select2().find(":selected").data("percentage");

				RecalculateTotal(clicked.attr("counter"), "Invoice");

		});

		$("#totalItemsOnInvoice").val(counter);
		counter += 1;
	});

	$("#paAccountNewInvoiceItemAdd").click();

	//activate the calendar to come up when use click date field
	$('#paNewInvoiceDate').datepicker().datepicker("setDate", new Date());
	$('#paNewInvoicePaymentDate').datepicker().datepicker("setDate", new Date());

	//allow a modal to open programaticaly
	$('#Model_Dash_PaNewClient').modal({ show: false})
	
	//activate the invoice list button now
	$("#AccountingNewInvoiceList").click(load_Accounting_List_Invoice);
	validateNewInvoice();
}

/* Load the client form function */
function load_Accounting_New_Sponsor(){

    NewSponsorForm();
}


/* Load the client form function */
function NewSponsorForm(){
	//get the instance of container division 
	var dashClientContainer = $("#dashPayments_content");
	//get the instance of the content division
	var dashContentainer_Payments = $("#dashContentainer_Payments");
	
	//hide the container first
	dashClientContainer.slideUp();
	
	//prepare the content to be printed in the containner
	
	var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>New Organization Sponsor</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewSponsorList class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;Sponsor List</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                \
                      <form id="formDashPaNewSponsor" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Name</div>\
								<div class="col-sm-7">\
									<input name="sponsorName" type="text" class="form-control" >\
								</div >\
							</div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Phone</div>\
								<div class="col-sm-7">\
									<input name="sponsorPhone" type="text" class="form-control" >\
								</div >\
							</div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Email</div>\
								<div class="col-sm-7">\
									<input name="sponsorEmail" type="email" class="form-control" >\
								</div >\
							</div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Contact Person</div>\
								<div class="col-sm-7">\
									<input name="sponsorContactFname" placeholder="Contact Person" type="text" class="form-control" >\
								</div >\
							</div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">P.O Box</div>\
								<div class="col-sm-7">\
									<input name="sponsorPobox" placeholder="PO Box" type="text" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Description</div>\
								<div class="col-sm-7">\
									<input name="sponsorDescription" placeholder="Description" type="text" class="form-control" >\
								</div >\
							</div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Sponsor</button>\
                          </div>\
                        </div>\
					</form>\
                </div>\
            </div>\
            </div>\
          </div>\
        </div>\
      </div></div></div></div>';
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  
  /* Activate the new Client buttons */
  AccountingNewSponsorJS();
  validateAddSponsor();
}
function LoadNationalResultREBRes(){
	var url = base_url + "accounting/rebResults";
	$.getJSON(url, function(results){
		console.log(results);
	});
}
function load_acc_New_Income_Form(){
	var url = base_url + "accounting/incomes/create";
	$.getJSON(url, function(allData){

		studentoption = PopulateStudentInClassOption(allData.students);

		/* Add school cash account on the request */
		var dataoption3 = "<option></option>";
		// console.log(allData);
		$.each( allData.cashAccount, function(i, item) {
			dataoption3 += "<option value='" + item.id + "'>" + item.staffName + "</option>";
		});
		/* Add the school bank account on the request */
		var activebank = "<option></option>";
		$.each( allData.bankAccount, function(i, item) {
			activebank += "<option value='" + item.id + "'>" + item.accountName + "</option>";
		});
		/* Add active invoices on the current request */
		var lastprint 	  = ""; 
		var clos 		  = false;
		var activeinvoice = "<option></option>";

		$.each( allData.openInvoice, function(i, item) {
			if(item.clientID != lastprint){
				if(clos){
					activeinvoice += "</optgroup>";
					clos = false;
				}
				activeinvoice += "<optgroup label='" + item.clientName + "'>";
				lastprint = item.clientID;
				clos = true;
			}

			activeinvoice += "<option value='" + item.id +"' data-amount='" + item.balance + "' >Nr." + item.number + " Due Date:" + date_moment(item.dueDate) + "</option>";
			
		});

		if(clos){
			activeinvoice += "</optgroup>";
			clos = false;
		}

		NewIncomeForm(studentoption, dataoption3, activebank, activeinvoice);
	});
}

function load_acc_Update_Income_Form(transactionId, clientName, incomeType, amount ){

	console.log("load_acc_Update_Income_Form");

	$('#Model_DashPaAccountingTable').modal('toggle');
	$('#DashPaAccountingTableTitle').html(amount+": "+amount+ " "+ schoolCurrency );

	var url = base_url+"accounting/incomes/"+transactionId;

	$.getJSON( url, function( data ) {
			
		console.log(data);
		
	}).done(function() {
		
	})
	.fail(function() {
		
	})
	.always(function() {

	});
	
}

function load_acc_Update_Expenses_Form(transactionId){
	/* Here Process all information related to the selected transaction now */
	var url_t = base_url+"accounting/details/transaction/" + transactionId + "/expenses";
	$.getJSON( url_t, function (trans){
		var url = base_url+"accounting/budgetitems/1";
		var dataoption = "";
		//console.log(trans);
		$.getJSON( url, function( data ) {
			var lastprint = "";
			var label1found = false;
			var somedataprinted = false;
			$.each( data, function(i, item) {
				if(lastprint != item.Category){
					if(label1found){
						dataoption += "</optgroup>";
					}
					dataoption += "<optgroup label='" + item.Category + "'>";
				}
				dataoption += "<option " + (item.id == trans[0].budgetItemId?"selected":"") + " value='" + item.id + "'>" + item.Item + "</option>";
				somedataprinted = true;
				labelledby = true;
				lastprint = item.Category;
			});
			if(somedataprinted){
				dataoption += "</optgroup>";
			}
		}).done(function() {
			//console.log(dataoption);
			//adjust the content to fit the new design now
			var url = base_url+"accounting/budgetitems/2";
			var dataoption2 = ""
			$.getJSON( url, function( data ) {
				var lastprint = "";
				var label1found = false;
				var somedataprinted = false;
				$.each( data, function(i, item) {
					if(lastprint != item.Category){
						if(label1found){
							dataoption2 += "</optgroup>";
						}
						dataoption2 += "<optgroup label='" + item.Category + "'>";
					}
					dataoption2 += "<option " + (item.id == trans[0].budgetItemId?"selected":"") + "  value='" + item.id + "'>" + item.Item + "</option>";
					somedataprinted = true;
					labelledby = true;
					lastprint = item.Category;
				});
				if(somedataprinted){
					dataoption2 += "</optgroup>";
				}
			}).done(function() {
				var url = base_url+"accounting/staff/";
				var dataoption3 = ""
				$.getJSON( url, function( data ) {
					var lastprint = "";
					var label1found = false;
					var somedataprinted = false;
					$.each( data, function(i, item) {
						
						dataoption3 += "<option " + (trans[0].schoolMoneyAccountId == item.id?"selected":"") + " value='" + item.id + "'>" + item.staffName + "</option>";
						
					});
				}).done(function() {
					//console.log(dataoption3);
					//select bills and add them to the form now
					var url = base_url+"accounting/assets/2";
					var activebank = ""
					$.getJSON( url, function( banks ) {
						var lastprint = "";
						$.each( banks, function(i, item) {
							
							activebank += "<option " + (trans[0].schoolMoneyAccountId == item.id?"selected":"") + "  value='" + item.id + "'>" + item.AccountName + "</option>";
							
						});
					}).done(function() {
						//select and try to include the already existing
						var url = base_url+"accounting/vendors/for_form:" + trans[0].billId;
						var activevendor = ""
						$.getJSON( url, function( vendors ) {
							
							var lastprint = ""; var clos = false;
							$.each( vendors, function(i, item) {
								if(lastprint != item.name ){
									if(clos){
										activevendor += "</optgroup>";
										clos = false;
									}
									activevendor += "<optgroup label='" + item.name + "'>";
									lastprint = item.name;
								}
								//add the ;optgroup for the current vendor
								activevendor += "<option " + (trans[0].billId == item.id?"selected":"") + " value='" + item.id + "'>" + item.number + "#" + formatNumber(item.amount) + " RWF</option>";
								
							});
							if(clos){
								activevendor += "</optgroup>";
								clos = false;
							}
						}).done(function() {
							//select vendors
							var url = base_url+"accounting/vendors";
							var activevendoronly = ""
							$.getJSON( url, function( vendorso ) {
								var lastprint = "";
								//console.log(vendorso);
								$.each( vendorso, function(i, item) {
									//add the ;optgroup for the current vendor
									activevendoronly += "<option " + (trans[0].vendorId == item.id?"selected":"") + " value='" + item.id + "'>" + item.name + "</option>";
									
								});
							}).done(function() {
								//select vendors
								var url = base_url+"accounting/loans";
								var activeloans = ""
								$.getJSON( url, function( loans ) {
									var lastprint = "";
									$.each( loans, function(i, item) {
										//add the ;optgroup for the current vendor
										activeloans += "<option " + (trans[0].loanId == item.id?"selected":"") + " value='" + item.id + "'>" + item.name + " #" + item.description + "</option>";
									});
								}).done(function() {
									//adjust the content to fit the new design now
									UpdateExpenseForm(dataoption, dataoption2, dataoption3, activebank, activevendor, activevendoronly, activeloans, trans[0]);
								});
							});
						});
					});
				});
			});
		});
	});
	
}

function load_acc_New_Expense_Form(){
  
  //send http request to find list of budgeted items
  	var url = base_url + "accounting/expenses/create";

  	$.getJSON(url, function(data){

  		var budgetItems 			= data.budgetItems ;
  		var budgetItemAsset 		= data.budgetItemAsset ;
  		var schoolAccountCashStaff 	= data.schoolAccountCashStaff ;
  		var schoolAccountBank 		= data.schoolAccountBank ;
  		var activeVendorBills 		= data.activeVendorBills ;
  		var activeVendor 			= data.activeVendor ;
  		var activeLoans 			= data.activeLoans ;

  		var option_budgetItems  			= "";
		var option_budgetItemAsset 			= "";
		var option_schoolAccountCashStaff 	= "";
		var option_schoolAccountBank  		= "";
		var option_activeVendorBills 		= "";
		var option_activeVendor 			= "";
		var option_activeLoans 				= "";

	//
		var option_budgetItems 	= '<option></option>';
		var optgroup_identifier = "";
		var isTheFirsRecord 	= true;

		$.each( budgetItems , function(i, item) {

			if ( item.categoryId == optgroup_identifier ){
			    option_budgetItems += '<option value="'+item.id+'" >'+item.expenseType+'</option>';
			
			}else{

			    optgroup_identifier = item.categoryId;
		    	if ( isTheFirsRecord ){

			    	isTheFirsRecord = false; 

		    	}else{
			    	 option_budgetItems += '</optgroup>';
			    }

		    	option_budgetItems += '<optgroup label="'+item.categoryName+'">';
		   		option_budgetItems += '<option value="'+item.id+'" >'+item.expenseType+'</option>';
			}
				    
		});

	//
		var option_budgetItemAsset  = '<option></option>';
		var optgroup_identifier 	= "";
		var isTheFirsRecord 		= true;

		$.each( budgetItemAsset , function(i, item) {

			if ( item.categoryId == optgroup_identifier ){
			    option_budgetItemAsset += '<option value="'+item.id+'" >'+item.expenseType+'</option>';
			
			}else{

			    optgroup_identifier = item.categoryId;
		    	if ( isTheFirsRecord ){

			    	isTheFirsRecord = false; 

		    	}else{
			    	 option_budgetItemAsset += '</optgroup>';
			    }

		    	option_budgetItemAsset += '<optgroup label="'+item.categoryName+'">';
		   		option_budgetItemAsset += '<option value="'+item.id+'" >'+item.expenseType+'</option>';
			}
				    
		});

	//	
		option_schoolAccountCashStaff  = '<option></option>';
		$.each( schoolAccountCashStaff , function(i, item) {
		   	
		   	option_schoolAccountCashStaff += '<option value="'+item.id+'" >'+item.accountName+'</option>';
		    
		});

	//	
		$option_schoolAccountBank = '<option></option>';
		$.each( schoolAccountBank , function(i, item) {
		   	
		   	option_schoolAccountBank += '<option value="'+item.id+'" >'+item.accountName+'</option>';
		    
		});

	//
		var option_activeVendorBills 	= '<option></option>';
		var optgroup_identifier 		= "";
		var isTheFirsRecord 			= true;

		$.each( activeVendorBills , function(i, item) {

			if ( item.vendor_id == optgroup_identifier ){
			    option_activeVendorBills += '<option value="'+item.bill_id+'" data-remainamount="'+(parseFloat(item.totalToPay)-parseFloat(item.totalPaid))+'" >'+item.bill_number+'</option>';
			
			}else{

			    optgroup_identifier = item.vendor_id;
		    	if ( isTheFirsRecord ){

			    	isTheFirsRecord = false; 

		    	}else{
			    	 option_activeVendorBills += '</optgroup>';
			    }

		    	option_activeVendorBills += '<optgroup label="'+item.vendor_name+'">';
		   		option_activeVendorBills += '<option value="'+item.bill_id+'" data-remainamount="'+(parseFloat(item.totalToPay)-parseFloat(item.totalPaid))+'" >'+item.bill_number+'</option>';
			}
				    
		});

	//
		$option_activeVendor = '<option></option>';
		$.each( activeVendor , function(i, item) {
		   	
		   	option_activeVendor += '<option value="'+item.id+'" >'+item.name+'</option>';
		    
		});

	// //
		$option_activeLoans = '<option></option>';
		$.each( activeLoans , function(i, item) {
		   	
		   	option_activeLoans += '<option value="'+item.id+'" >'+item.name+'</option>';
		    
		});

		displayNewExpenseForm( option_budgetItems, option_budgetItemAsset, option_schoolAccountCashStaff, option_schoolAccountBank, option_activeVendorBills, option_activeVendor, option_activeLoans );

  	});
}

function getPaNewIncomeStudentSelected()
{
   
   var  paNewIncomeStudent   = $("#paNewIncomeStudent");
   var  selectedvalue        = paNewIncomeStudent.select2('val');

   //console.log(selectedvalue);
    //load all student unpaid invoice in the container an show it now
   // $("#studentUnpaidInvoice").load("accounting/studentUnpaidInvoice/" + selectedvalue );

    var dashPayments_content      = $("#studentUnpaidInvoiContainner");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

  	  var content = '';

      // var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
      //   content+='<div class="row">';
      //     content+= '<div class="col-sm-12">';
      //       content+='<div class="row text-center">';
      //         content+= '<h4>Student\'s Invoice</h4>';
      //       content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_student_invoice"></div>';
              content+= '</div>';
            content+= '</div>';
      //     content+= '</div>';
      //   content+= '</div>';
      // content+= '</div></div></div></div>';

      dashPayments_content.html( content );

    dashPayments_content.slideDown();

  var url = base_url+"accounting/studentUnpaidInvoice/" + selectedvalue;

  var Content_PaAn_student_invoice  = '';

  var  pa_student_invoice           = $('#pa_student_invoice');

  //send the ajax command to receive tne jso reposne now
  var counter =1;
  $.getJSON( url, function( data ) {
	  
      Content_PaAn_student_invoice += Debt_Loading_Function(data.debt, counter, selectedvalue);
      //alert(previous_count);
	  Content_PaAn_student_invoice += Current_Loading_Function(data.current, counter, selectedvalue);
      
	  /* Add the Unassigen Money Here */
	  Content_PaAn_student_invoice +=  Unsigned_Loading_Function(data.current, counter, selectedvalue);
      pa_student_invoice.html( Content_PaAn_student_invoice );
      
  }).done(function(){
	//write the counter into the form
	$("#incomesPreviousFieldsData").val(previous_count);
	$("#incomesCurrentFieldsData").val(current_count);
	$("#incomesDebtFieldsData").val(debt_count);
  });



}

function getPaUpdateIncomeStudentSelected(tr_exist) {
   
   var  paNewIncomeStudent   = $("#paNewIncomeStudent");
   var  selectedvalue             = paNewIncomeStudent.select2('val');

   //console.log(selectedvalue);
    //load all student unpaid invoice in the container an show it now
   // $("#studentUnpaidInvoice").load("accounting/studentUnpaidInvoice/" + selectedvalue );

    var dashPayments_content      = $("#studentUnpaidInvoiContainner");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

  var content = '';
      // var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
      //   content +='<div class="row">';
      //     content += '<div class="col-sm-12">';
      //       content +='<div class="row text-center">';
      //         content += '<h4>Student\'s Invoice</h4>';
      //       content += '</div>';
     	  content += '<div class="row">';
              content += '<div class="col-sm-12">';
               	content +='<div id="pa_student_invoice"></div>';
              content += '</div>';
            content += '</div>';
          content += '</div>';
      //   content += '</div>';
      // content += '</div></div></div></div>';

      dashPayments_content.html( content );

    dashPayments_content.slideDown();
    //console.log(tr_exist);
  var url = base_url+"accounting/studentUnpaidInvoice/" + tr_exist[0].regNumber + "/old/" + tr_exist[0].id;

  var Content_PaAn_student_invoice  = '';

  var pa_student_invoice           = $('#pa_student_invoice');
  
  //send the ajax command to receive the json reponse now
  var counter =1;
  $.getJSON( url, function( data ) {
      Content_PaAn_student_invoice += Debt_Loading_Function_Edit(data.debt, data.debt_special, counter, tr_exist[0].regNumber);
      //alert(previous_count);
	  Content_PaAn_student_invoice += Current_Loading_Function_Edit(data.current, data.current_special, counter, tr_exist[0].regNumber);
      
	  /* Add the Unassigen Money Here */
	  Content_PaAn_student_invoice +=  Unsigned_Loading_Function_Edit(data.unassigned_data, counter, tr_exist[0].regNumber);
      pa_student_invoice.html( Content_PaAn_student_invoice );
      
  }).done(function(){
	//write the counter into the form
	$("#incomesPreviousFieldsData").val(previous_count);
	$("#incomesCurrentFieldsData").val(current_count);
	$("#incomesDebtFieldsData").val(debt_count);
  }).always(function(){
  	// console.log(tr_exist);
  });



}

function OnlyStudentDebt(studentId){
	$("#SchoolFeesDebtDataContainner").slideUp();
	var url = base_url+"accounting/studentUnpaidInvoice/" + studentId + "/debt";
	var content = studentId;
	var datatodisplay = "";
	$.getJSON( url, function( debts ) {
		datatodisplay += Debt_Loading_Function(debts, 1);
	}).done(function(){
		$("#SchoolFeesDebtDataContainner").html(datatodisplay);
		/* Send the ajax commad to have all */
		$("#SchoolFeesDebtDataContainner").slideDown();
		
		$("#incomesDebtFieldsData").val(debt_count);
	});
	
}

// function OnlyStudentPrevious(studentId){
// 	$("#SchoolFeesPreviousDataContainner").slideUp();
// 	var url = base_url+"accounting/studentUnpaidInvoice/" + studentId + "/previous";
// 	var content = studentId;
// 	var datatodisplay = "";
// 	$.getJSON( url, function( previous ) {
// 		datatodisplay += Previous_Loading_Function(previous, 1);
// 	}).done(function(){
// 		$("#SchoolFeesPreviousDataContainner").html(datatodisplay);
// 		/* Send the ajax commad to have all */
// 		$("#SchoolFeesPreviousDataContainner").slideDown();
		
// 		$("#incomesPreviousFieldsData").val(previous_count);
// 	});
	
// }

function OnlyStudentCurrent(studentId){
	$("#SchoolFeesCurrentDataContainner").slideUp();
	var url = base_url+"accounting/studentUnpaidInvoice/" + studentId + "/current";
	var content = studentId;
	var datatodisplay = "";
	$.getJSON( url, function( current ) {
		datatodisplay += Current_Loading_Function(current, 1, studentId);
	}).done(function(){
		$("#SchoolFeesPreviousDataContainner").html(datatodisplay);
		/* Send the ajax commad to have all */
		$("#SchoolFeesCurrentDataContainner").slideDown();
		
		$("#incomesCurrentFieldsData").val(current_count);
	});
	
}


/* Here School fees sub menu */

/* Load the Fee Type List function */
function load_Accounting_School_Fees_Fee_Type(){

	$(".left_menu").removeClass('active');
	$("#FeeType").addClass('active');
	
	//get the instance of container division 
	var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
	//get the instance of the content division
	var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
	
	//hide the container first
	dashClientContainer.slideUp();
	
	//prepare the content to be printed in the containner
	
	var content = '';
				content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
					content += '<div class="row text-center">';
						content += '<div class="col-sm-8">';
							content += '<h4>Current Fee Types</h4>';
						content += '</div>';
						content += '<div class="col-sm-4">';
							content += '<a id=AccountingSchoolFeesNewFeeType class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New</a>';
						content += '</div>';
					content += '</div>'; 
					content += '<div class="row" ><div class="col-sm-12" >';
					  content += ' \
							<div id="pa_SchoolFees_FeesType">\
								Fee Type Loading...\
							</div>\
						</div></div>\
					   ';
				content+= '</div>';
	  
	
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  var url = base_url + "accounting/schoolfees/feetype";

  var content  = '';

  var  pa_SchoolFees_FeesType = $('#pa_SchoolFees_FeesType');

    $.getJSON( url, function( data ) {

      content += '<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
					<div class="modal-wrapper">\
						<div class="modal-dialog">\
							<div class="modal-content">\
								<div class="modal-header">\
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
									<h4>Delete Fee Type</h4>\
								</div>\
								<div class="modal-body">\
									The Selected Fee type will and all related termly invoice\
								</div>\
								<div class="modal-footer">\
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
									<a class="btn btn-danger btn-ok">Delete</a>\
								</div>\
							</div>\
						</div>\
					</div>\
				</div><table class="table table-bordered abDataTable" id="pa_AccountingSchoolFeeTypeTable" ><thead><tr ><th class="text-center">Fee Type</th><th class="text-center">Frequence</th><th class="text-center">Delete</th></tr></thead>';

      content += '<tbody>';
      
      $.each( data, function(i, item) {

          content +="<tr id="+item.id+">";
            content +="<td class='text-left'><span style='width:100%' class='AccFeeTypeXeditable' data-type='text' data-pk='1' data-title='Enter fee type' data-url='accounting/schoolfees/feetype/"+item.id+"' >"+item.name+"</span></td>";
            content +="<td class='text-center'>"+ item.frequenceName +" </td>";
            if ( item.numberOfTermlyFee > 0 ) {
            	content +="<td class='text-center'></td>"; 
            }else{
            	content +="<td class='text-center AccFeeTypeDelete'><a href='#'><i class='fa fa-times text-red'></i></a></td>"; 
            }
		  content +="</tr>";
          
      });

      content +="</tbody></table>";
      pa_SchoolFees_FeesType.html( content );
      
	    $( "table#pa_AccountingSchoolFeeTypeTable" ).delegate( "td.AccFeeTypeDelete", "click", function(e) {

	          e.preventDefault();
	          e.stopPropagation();

	          var id = $(this).closest('tr').attr('id'); 
	          AccDeleteFeeType( id, $(this) );

	    });

      	$.fn.editable.defaults.ajaxOptions  = {type: "PUT" };
	    $.fn.editable.defaults.mode         = 'inline';

	  	$('.AccFeeTypeXeditable').editable({
	    	emptytext: 'Click to add',
	        	success: function(response, newValue) {
	            if(response.status == 'error') return response.msg;
	        }
	  	});


    }).done(function() {

        $('#pa_AccountingSchoolFeeTypeTable').DataTable();

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of the ajax request here */
  
  /* Activate the new invoice buttons */
  AccountingSchoolFeesFeeTypeJS();
}

function EditFeeTypeInfo(feetypeid){
	load_Accounting_School_Fees_Edit_Fee_Type(feetypeid);
}

function DisplayDetailsOfIncomes(transactionID, clientName, incomeType, amount ){

	console.log("DisplayDetailsOfIncomes");
	
	/* Load the form with editable information for the income */
	load_acc_Update_Income_Form(transactionID, clientName, incomeType, amount );
}
function DisplayDetailsOfExpenses(transactionID){
	/* Load the form with editable information for the income */
	load_acc_Update_Expenses_Form(transactionID);
}

function PopulateStudentInClassOption(sts, selectedId=null){
	var class_name = ""; var clos = false; var started = false;
	var content = "<option></option>";
	$.each(sts, function(i, item){
		if(class_name != item.ClassName){
			if(started){
				content += '</optgroup>';
			}
			content += '<optgroup label="' + item.ClassName + '">';
			class_name = item.ClassName;
			started = true;
			clos = true;
		}
		content += '<option ' + (selectedId != null && selectedId == item.registrationNumber?'selected':'') + ' value=' + item.registrationNumber + '>'+item.name+ ' ('+ item.registrationNumber +')</option>';
		//Content_Pa_Deposits_Others += '<option value=' + item.id + '>' + item.registrationNumber + "(" + item.name + ')</option>';
		
	});
	if(clos){
		content += '</optgroup>';
	}

	return content;
}