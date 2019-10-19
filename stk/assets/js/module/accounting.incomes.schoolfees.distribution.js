var sentCheck = false; // Check the buffering process
/* Here are function related to the school fees distribution */
function Debt_Loading_Function(data, counter, selectedvalue){

	//<input type=button id='Accounting_New_Debt_Invoice_Item' class='btn btn-success' value='New Debt'  />

	var studentid = selectedvalue; 

	//<a href="#" onclick="NewStudentInvoiceDistribution('+studentid+')"><i class="fa fa-plus text-green"></i> Add new debt</a>

	Content_PaAn_student_invoice ='\
	<div id="SchoolFeesDebtDataContainner">\
	<input type="hidden" id="incomesDebtFieldsData" name="incomeDebtFieldsData" value="done" />\
	<div class="row">\
		<div class="col-sm-12"><h5>Debt&nbsp;&nbsp;</h5></div>\
	</div>\
	<table class="table table-bordered abDataTable" id="pa_Payments_Student_invoice_Debt" ><thead><tr ><th class="text-center">Item</th><th class="text-center">Amount</th><th class="text-center">Paid</th></tr></thead>';
      //Content_PaAn_student_invoice += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th colspan="1" style="text-align:left"></th></tr></tfoot>'; 
      Content_PaAn_student_invoice +='<tbody>';
     
	  count_data = 1;
      $.each( data, function(i, item) {
			if(studentid == ""){
				studentid = item.regNumber;
			}
          Content_PaAn_student_invoice +="<tr id="+(counter++)+">";
          Content_PaAn_student_invoice +="<td >"+item.description+" </td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=hidden id='debtDataOn_" + count_data + "' value='" + item.amount + "' />"+formatNumber(item.amount)+" </td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=text name='debt[" + item.id + "]' id='debtPaidDataOn_" + count_data + "' onkeyup='DistributeAmountCustom(\"debtPaidDataOn_" + count_data + "\")' class='form-control' placeholder='Paid Amount' /> </td>";
          Content_PaAn_student_invoice +="</tr>";
		  debt_count = count_data++;
      
      });
	if(count_data == 1){
		debt_count = "";
	}
      Content_PaAn_student_invoice +="</tbody></table></div>";

      
	return Content_PaAn_student_invoice;
}

function Debt_Loading_Function_Edit(data, special, counter, selectedvalue){
	
	var studentid = selectedvalue; 

	Content_PaAn_student_invoice ='\
	<div id="SchoolFeesDebtDataContainner">\
	<input type="hidden" id="incomesDebtFieldsData" name="incomeDebtFieldsData" value="done" />\
	<div class="row">\
		<div class="col-sm-12"><h5>Debt&nbsp;&nbsp;<a href="#" onclick="NewStudentInvoiceDistribution('+studentid+')"><i class="fa fa-plus text-green"></i> Add new debt</a></h5></div>\
	</div>\
	<table class="table table-bordered abDataTable" id="pa_Payments_Student_invoice_Debt" ><thead><tr ><th class="text-center">Item</th><th class="text-center">Amount</th><th class="text-center">Paid</th></tr></thead>';
      //Content_PaAn_student_invoice += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th colspan="1" style="text-align:left"></th></tr></tfoot>'; 
      Content_PaAn_student_invoice +='<tbody>';
      
	  count_data = 1;
      $.each( special, function(i, item) {
			if(studentid == ""){
				studentid = item.regNumber;
			}
          Content_PaAn_student_invoice +="<tr id="+(counter++)+">";
          Content_PaAn_student_invoice +="<td >"+item.description+" </td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=hidden id='debtDataOn_" + count_data + "' value='" + item.amount + "' />"+formatNumber(item.amount)+" </td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=hidden name='existing_debt_" + item.id + "' value='" + item.paymentId + "' /><input type=text name='debt_" + item.id + "' id='debtPaidDataOn_" + count_data + "' value='" + item.paymentAmount + "' onkeyup='DistributeAmountCustom(\"debtPaidDataOn_" + count_data + "\")' class='form-control' placeholder='Paid Amount' /> </td>";
          Content_PaAn_student_invoice +="</tr>";
		  debt_count = count_data++;
      
      });
      $.each( data, function(i, item) {
			if(studentid == ""){
				studentid = item.regNumber;
			}
          Content_PaAn_student_invoice +="<tr id="+(counter++)+">";
          Content_PaAn_student_invoice +="<td >"+item.description+" </td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=hidden id='debtDataOn_" + count_data + "' value='" + item.amount + "' />"+formatNumber(item.amount)+" </td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=text name='debt_" + item.id + "' id='debtPaidDataOn_" + count_data + "' onkeyup='DistributeAmountCustom(\"debtPaidDataOn_" + count_data + "\")' class='form-control' placeholder='Paid Amount' /> </td>";
          Content_PaAn_student_invoice +="</tr>";
		  debt_count = count_data++;
      
      });
	if(count_data == 1){
		debt_count = "";
	}
      Content_PaAn_student_invoice +="</tbody></table><input type=button id='Accounting_New_Debt_Invoice_Item' class='btn btn-success' value='New Debt' onclick='NewStudentInvoiceDistribution(\""+ studentid +"\")' /></div>";
	return Content_PaAn_student_invoice;
}

function availableFeeType(){
	var url = base_url+"accounting/schoolfees/feetype";
	$.getJSON( url, function( data ) {
		return data; 
	});
}
function availableTerms(){
	var url3 = base_url + 'accounting/terms';
	$.getJSON(url3, function(terms){
		return terms;
	});
}

// function Previous_Loading_Function(data, counter, selectedvalue){
	
// 	var studentid = selectedvalue;

// 	Content_PaAn_student_invoice ='\
// 	<div id="SchoolFeesPreviousDataContainner">\
// 	<input type="hidden" id="incomesPreviousFieldsData" name="incomePreviousFields" value="done" />\
// 	<h5>Previous Term</h5><table class="table table-bordered abDataTable" id="pa_Payments_Student_invoice_Previous" ><thead><tr ><th class="text-center">Fee Type</th><th class="text-center">Amount</th><th class="text-center">Paid</th></tr></thead>';
//       //Content_PaAn_student_invoice += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th colspan="1" style="text-align:left"></th></tr></tfoot>'; 
//       Content_PaAn_student_invoice +='<tbody>';
      
// 	  var count_data = 1;
//       $.each( data, function(i, item) {
// 			if(studentid == ""){
// 				studentid = item.regNumber;
// 			}
//           Content_PaAn_student_invoice +="<tr id=" + (counter++) + ">";
//           Content_PaAn_student_invoice +="<td >"+item.description+" </td>";
//           Content_PaAn_student_invoice +="<td class='text-center'>" + formatNumber(item.amount) + " <input type=hidden id='previousDataOn_" + count_data + "' value='" + item.amount + "' /></td>";
//           Content_PaAn_student_invoice +="<td class='text-center'><input type=text name='previous_"+item.id+"' class='form-control' id='previousPaidDataOn_" + count_data + "' onkeyup='DistributeAmountCustom(\"previousPaidDataOn_" + count_data + "\")' placeholder='Paid Amount' /> </td>";
//           Content_PaAn_student_invoice +="</tr>";
// 		  previous_count = count_data++;
//       });
// 	if(count_data == 1){
// 		previous_count = "";
// 	}
//     Content_PaAn_student_invoice +="</tbody></table></div>";
// 	// Content_PaAn_student_invoice +="</tbody></table><input type=button id='Accounting_New_Previous_Invoice_Item' class='btn btn-success' value='New Invoice' onclick='NewStudentInvoiceDistributionPrevious(\""+ studentid +"\")' /></div>";
// 	return Content_PaAn_student_invoice;
// }

// function Previous_Loading_Function_Edit(data, special, counter, selectedvalue){
	
// 	var studentid = selectedvalue;

// 	Content_PaAn_student_invoice ='\
// 	<div id="SchoolFeesPreviousDataContainner">\
// 	<input type="hidden" id="incomesPreviousFieldsData" name="incomePreviousFields" value="done" />\
// 	<h5>Previous Term</h5><table class="table table-bordered abDataTable" id="pa_Payments_Student_invoice_Previous" ><thead><tr ><th class="text-center">Fee Type</th><th class="text-center">Amount</th><th class="text-center">Paid</th></tr></thead>';
//       //Content_PaAn_student_invoice += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th colspan="1" style="text-align:left"></th></tr></tfoot>'; 
//       Content_PaAn_student_invoice +='<tbody>';
      
// 	  var count_data = 1;
//       $.each( special, function(i, item) {
// 			if(studentid == ""){
// 				studentid = item.regNumber;
// 			}
//           Content_PaAn_student_invoice +="<tr id=" + (counter++) + ">";
//           Content_PaAn_student_invoice +="<td >"+item.description+" </td>";
//           Content_PaAn_student_invoice +="<td class='text-center'>" + formatNumber(item.amount) + " <input type=hidden id='previousDataOn_" + count_data + "' value='" + item.amount + "' /></td>";
//           Content_PaAn_student_invoice +="<td class='text-center'><input type=hidden name='existing_previous_" + item.id + "' value='" + item.paymentId + "' /><input type=text name='previous_"+item.id+"' class='form-control' id='previousPaidDataOn_" + count_data + "' value='" + item.paymentAmount + "' onkeyup='DistributeAmountCustom(\"previousPaidDataOn_" + count_data + "\")' placeholder='Paid Amount' /> </td>";
//           Content_PaAn_student_invoice +="</tr>";
// 		  previous_count = count_data++;
//       });
//       $.each( data, function(i, item) {
// 			if(studentid == ""){
// 				studentid = item.regNumber;
// 			}
//           Content_PaAn_student_invoice +="<tr id=" + (counter++) + ">";
//           Content_PaAn_student_invoice +="<td >"+item.description+" </td>";
//           Content_PaAn_student_invoice +="<td class='text-center'>" + formatNumber(item.amount) + " <input type=hidden id='previousDataOn_" + count_data + "' value='" + item.amount + "' /></td>";
//           Content_PaAn_student_invoice +="<td class='text-center'><input type=text name='previous_"+item.id+"' class='form-control' id='previousPaidDataOn_" + count_data + "' onkeyup='DistributeAmountCustom(\"previousPaidDataOn_" + count_data + "\")' placeholder='Paid Amount' /> </td>";
//           Content_PaAn_student_invoice +="</tr>";
// 		  previous_count = count_data++;
//       });
// 	if(count_data == 1){
// 		previous_count = "";
// 	}
//       Content_PaAn_student_invoice +="</tbody></table></div>";
// 	return Content_PaAn_student_invoice;
// }

function Current_Loading_Function(data, counter, selectedvalue){
	
	var studentid = selectedvalue;

	//<a href="#" onclick="NewStudentInvoiceDistributionCurrent('+studentid+')"><i class="fa fa-plus text-green"></i> Add new fee</a>
	
	Content_PaAn_student_invoice ='\
	<div id="SchoolFeesCurrentDataContainner">\
	<input type="hidden" id="incomesCurrentFieldsData" name="incomeCurrentFieldsData" value="" />\
	<div class="row">\
		<div class="col-sm-12"><h5>Unpaid Fees&nbsp;&nbsp;</h5></div>\
	</div>\
	<table class="table table-bordered abDataTable" id="pa_Payments_Student_invoice_Current" ><thead><tr ><th class="text-center">Fee Type</th><th class="text-center">Amount</th><th class="text-center">Paid</th></tr></thead>';
      //Content_PaAn_student_invoice += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th colspan="1" style="text-align:left"></th></tr></tfoot>'; 
      Content_PaAn_student_invoice +='<tbody>';
      
	  var count_data = 1;
      $.each( data, function(i, item) {
			if(studentid == ""){
				studentid = item.regNumber;
			}
          Content_PaAn_student_invoice +="<tr id="+(counter++)+">";
          Content_PaAn_student_invoice +="<td >"+item.description+" </td>";
          Content_PaAn_student_invoice +="<td class='text-center'>" + formatNumber(item.amount) + " <input type=hidden id='currentDataOn_" + count_data + "' value='" + item.amount + "' /></td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=text name='current[" + item.id + "]' id='currentPaidDataOn_" + count_data + "' onkeyup='DistributeAmountCustom(\"currentPaidDataOn_" + count_data + "\")' class='form-control' placeholder='Paid Amount' /> </td>";
          Content_PaAn_student_invoice +="</tr>";
		  current_count = count_data++;
      
      });
	if(count_data == 1){
		current_count = "";
	}
      Content_PaAn_student_invoice +="</tbody></table></div>";
	return Content_PaAn_student_invoice;
}

function Current_Loading_Function_Edit(data, special, counter, selectedvalue){
	
	var studentid = selectedvalue;

	Content_PaAn_student_invoice ='\
	<div id="SchoolFeesCurrentDataContainner">\
	<input type="hidden" id="incomesCurrentFieldsData" name="incomeCurrentFieldsData" value="" />\
	<div class="row">\
		<div class="col-sm-12"><h5>Unpaid Fees&nbsp;&nbsp;<a href="#" onclick="NewStudentInvoiceDistributionCurrent('+studentid+')"><i class="fa fa-plus text-green"></i> Add new fee</a> </h5></div>\
	</div>\
	<table class="table table-bordered abDataTable" id="pa_Payments_Student_invoice_Current" ><thead><tr ><th class="text-center">Fee Type</th><th class="text-center">Amount</th><th class="text-center">Paid</th></tr></thead>';
      //Content_PaAn_student_invoice += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th colspan="1" style="text-align:left"></th></tr></tfoot>'; 
      Content_PaAn_student_invoice +='<tbody>';
      
	  var count_data = 1;
      $.each( special, function(i, item) {
			if(studentid == ""){
				studentid = item.regNumber;
			}
          Content_PaAn_student_invoice +="<tr id="+(counter++)+">";
          Content_PaAn_student_invoice +="<td >"+item.description+" </td>";
          Content_PaAn_student_invoice +="<td class='text-center'>" + formatNumber(item.amount) + " <input type=hidden id='currentDataOn_" + count_data + "' value='" + item.amount + "' /></td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=hidden name='existing_current_" + item.id + "' value='" + item.paymentId + "' /><input type=text name='current_" + item.id + "' id='currentPaidDataOn_" + count_data + "' onkeyup='DistributeAmountCustom(\"currentPaidDataOn_" + count_data + "\")' value='" + item.paymentAmount + "' class='form-control' placeholder='Paid Amount' /> </td>";
          Content_PaAn_student_invoice +="</tr>";
		  current_count = count_data++;
      
      });
      $.each( data, function(i, item) {
			if(studentid == ""){
				studentid = item.regNumber;
			}
          Content_PaAn_student_invoice +="<tr id="+(counter++)+">";
          Content_PaAn_student_invoice +="<td >"+item.description+" </td>";
          Content_PaAn_student_invoice +="<td class='text-center'>" + formatNumber(item.amount) + " <input type=hidden id='currentDataOn_" + count_data + "' value='" + item.amount + "' /></td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=text name='current_" + item.id + "' id='currentPaidDataOn_" + count_data + "' onkeyup='DistributeAmountCustom(\"currentPaidDataOn_" + count_data + "\")' class='form-control' placeholder='Paid Amount' /> </td>";
          Content_PaAn_student_invoice +="</tr>";
		  current_count = count_data++;
      
      });
	if(count_data == 1){
		current_count = "";
	}
      Content_PaAn_student_invoice +="</tbody></table></div>";
	return Content_PaAn_student_invoice;
}

function Unsigned_Loading_Function(data, counter, selectedvalue){
	Content_PaAn_student_invoice ='\
	<div id="SchoolFeesCurrentDataContainner">\
	<input type="hidden" id="incomesUnassignedFieldsData" name="incomeCurrentFieldsData" />\
	<h5>Unassigned</h5><table class="table table-bordered abDataTable" id="pa_Payments_Student_invoice_Current" ><thead><tr ><th class="text-center">Unassigned</th><th class="text-center">Amount</th><th class="text-center">Paid</th></tr></thead>';
      //Content_PaAn_student_invoice += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th colspan="1" style="text-align:left"></th></tr></tfoot>'; 
      /* Content_PaAn_student_invoice +='<tbody>';
      var studentid = selectedvalue;
	  var count_data = 1;
      $.each( data, function(i, item) {
			if(studentid == ""){
				studentid = item.regNumber;
			} */
          Content_PaAn_student_invoice +="<tr id="+(counter++)+">";
          Content_PaAn_student_invoice +="<td> Unassigned </td>";
          Content_PaAn_student_invoice +="<td class='text-center'>&nbsp;</td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=text name='unassignedAmount' id='unassignedAmount' readonly class='form-control' placeholder='Paid Amount' /> </td>";
          Content_PaAn_student_invoice +="</tr>";
      
      // });
      //Content_PaAn_student_invoice +="</tbody></table><input type=button id='Accounting_New_Current_Invoice_Item' class='btn btn-success' value='New Invoice' onclick='NewStudentInvoiceDistributionCurrent(\""+ studentid +"\")' /></div>";
	return Content_PaAn_student_invoice;
}

function Unsigned_Loading_Function_Edit(data, counter, selectedvalue){
	Content_PaAn_student_invoice ='\
	<div id="SchoolFeesCurrentDataContainner">\
	<input type="hidden" id="incomesUnassignedFieldsData" name="incomeCurrentFieldsData" />\
	<h5>Unassigned</h5><table class="table table-bordered abDataTable" id="pa_Payments_Student_invoice_Current" ><thead><tr ><th class="text-center">Unassigned</th><th class="text-center">Amount</th><th class="text-center">Paid</th></tr></thead>';
      //Content_PaAn_student_invoice += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th colspan="1" style="text-align:left"></th></tr></tfoot>'; 
      /* Content_PaAn_student_invoice +='<tbody>';
      var studentid = selectedvalue; */
	  var count_data = 1;
      $.each( data, function(i, item) {
			
          Content_PaAn_student_invoice +="<tr id="+(counter++)+">";
          Content_PaAn_student_invoice +="<td> Unassigned </td>";
          Content_PaAn_student_invoice +="<td class='text-center'>&nbsp;</td>";
          Content_PaAn_student_invoice +="<td class='text-center'><input type=hidden name='existing_unassignedAmount' value='" + item.id + "' /><input type=text name='unassignedAmount' id='unassignedAmount' value='" + item.amount + "' readonly class='form-control' placeholder='Paid Amount' /> </td>";
          Content_PaAn_student_invoice +="</tr>";
      
      });
      //Content_PaAn_student_invoice +="</tbody></table><input type=button id='Accounting_New_Current_Invoice_Item' class='btn btn-success' value='New Invoice' onclick='NewStudentInvoiceDistributionCurrent(\""+ studentid +"\")' /></div>";
	return Content_PaAn_student_invoice;
}


/* Current Fee Student List should appear as modal */
function NewStudentInvoiceDistribution(StudentID){
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
							<form id="formAddNewStudentDebtNow" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
							<div class="modal-body">\
								<input type="hidden" name="studentWithDebt[]" value="' + StudentID + '" />\
								<div class="form-group" >\
									<div class="col-sm-4 text-right">Description</div>\
									<div class="col-sm-8">\
										<input name="debtDescription" type="text" class="form-control" >\
									</div >\
								</div>\
								<div class="form-group" >\
									<div class="col-sm-4 text-right">Amount</div>\
									<div class="col-sm-8">\
										<input name="debtAmount" type="text" class="form-control" >\
									</div >\
								</div>\
							</div>\
							<div class="modal-footer" >\
								<div class="pull-right btn-group">\
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
									<button type="submit" class="btn btn-primary" id="paAccountingAddStudentToFeePaymentSubmit" >Add to List</button>\
								</div>\
							</div>\
						</form>\
								<div id=studentnfocontainner></div>';
	pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
	/* End of Ajax Request */
	$('#Model_DashPaFeeStudentList').modal({ show: true});
	$('#Model_DashPaFeeStudentList').on('hidden.bs.modal', function () {
		OnlyStudentDebt(StudentID);
	});
	/* Validate the form fields */
	validateAddStudentDebt(StudentID);
	
}

/* Current Fee Student List should appear as modal */
// function NewStudentInvoiceDistributionPrevious(StudentID){
// 	//select terms from database
// 	var url3 = base_url + 'accounting/terms';
// 	$.getJSON(url3, function(terms){
// 		//check for fee type
// 		var url = base_url+"accounting/schoolfees/feetype/"+StudentID;
// 		$.getJSON( url, function( data ) {
// 			NSIDPF(StudentID, terms, data);
// 		});
// 	});
// }


function NSIDPF(StudentID, terms, feetypes){
	//modify the content before display the modal with data from the database
	var modal_container = $("#ModalDataLoadingNowPrevious");//.html("Data Modified And Every Thing is fine!");
	var content = '\
		<div id="pa_Fee_Student_List_Previous">\
			Student List Here\
		</div>\
	';
	/* Send Ajax request to return the list of student who are involved in paying */
	//ajust the content now
	modal_container.html(content);
	
	var Content_Pa_Deposits_Others  = '';
	var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List_Previous');
	Content_Pa_Deposits_Others += '\
							<form id="formAddNewStudentPreviousNow" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
							<div class="modal-body">\
								<input type="hidden" name="studentId" id="studentId" value="' + StudentID + '" />\
								<input type="hidden" name="_method" id="termlyFeeId" value="PUT" />\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Term</div>\
									<div class="col-sm-8">\
										<select name="newCurrentTermIdData" class="form-control" id="newCurrentTermIdData" placeholder="Choose Client Name" >\
										';
			//get the list of available terms
			$.each(terms, function(i, item){
				Content_Pa_Deposits_Others += '<option value="' + item.id + '">' + item.name + ' Term ' + item.number + '</option>';
			});
		Content_Pa_Deposits_Others += '</select>\
									</div >\
								</div>\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Fee Type</div>\
									<div class="col-sm-8">\
										<select name="newCurrentFeeTypeData" class="form-control" id="newCurrentFeeTypeData" placeholder="Choose Client Name" >\
										';
			//get the list of available terms
			$.each(feetypes, function(i, item){
				Content_Pa_Deposits_Others += '<option value="' + item.id + '">' + item.name + ' #' + item.description + '</option>';
			});
		Content_Pa_Deposits_Others += '</select>\
									</div >\
								</div>\
								<div class="form-group" >\
									<div class="col-sm-3 text-right">Description</div>\
									<div class="col-sm-8">\
										<input name="studentPreviousDescription" type="text" id="paNewBudgetItemAmount" class="form-control" >\
									</div >\
								</div>\
								<div class="form-group" >\
									<div class="col-sm-3 text-right">Amount</div>\
									<div class="col-sm-8">\
										<input name="studentPreviousAmount" type="text" id="paNewBudgetItemAmount" class="form-control" >\
									</div >\
								</div>\
								<div class="form-group" >\
									<div class="col-sm-3 text-right">Due Date</div>\
									<div class="col-sm-8">\
										<input name="studentPreviousDueDate" type="text" id="paNewStudentPreviousDueDate" class="form-control" >\
									</div >\
								</div>\
							</div>\
							<div class="modal-footer" >\
								<div class="pull-right btn-group">\
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
									<button type="submit" class="btn btn-primary" id="paAccountingAddStudentToFeePaymentSubmit" >Add to List</button>\
								</div>\
							</div>\
						</form>\
								<div id=studentnfocontainner></div>';
	pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
	/* End of Ajax Request */
	// $('#Model_DashPaFeeStudentPrevious').modal({ show: true});
	// $('#Model_DashPaFeeStudentPrevious').on('hidden.bs.modal', function () {
	// 	OnlyStudentPrevious(StudentID);
	// });
	/* Validate the form fields */
	validateAddStudentPrevious(StudentID);
	
}
/* Current Fee Student List should appear as modal */
function NewStudentInvoiceDistributionCurrent(StudentID){
	//console.log(StudentID);
	//select terms from database
	// var url3 = base_url + 'accounting/terms';
	// $.getJSON(url3, function(terms){
	// 	//check for fee type
		
	// });

	var url = base_url+"accounting/schoolfees/currentfee/"+StudentID;
	$.getJSON( url, function( data ) {
		NSIDCF(StudentID, data);
	});
}


function NSIDCF(StudentID, feetypes){
	//modify the content before display the modal with data from the database
	var modal_container = $("#ModalDataLoadingNowCurrent");//.html("Data Modified And Every Thing is fine!");
	var content = '\
		<div id="pa_Fee_Student_List_Previous">\
			Student List Here\
		</div>\
	';
	console.log("1. "+StudentID);
	/* Send Ajax request to return the list of student who are involved in paying */
	//ajust the content now
	modal_container.html(content);
	
	var Content_Pa_Deposits_Others  = '';
	var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List_Previous');
	Content_Pa_Deposits_Others += '\
							<form id="formAddNewStudentCurrentNow" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
							<div class="modal-body">\
								<input type="hidden" name="studentId" id="studentId" value="' + StudentID + '" />\
								<input type="hidden" name="_method" id="termlyFeeId" value="PUT" />\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Fee Type</div>\
									<div class="col-sm-8">\
										<select name="newCurrentFeeTypeDataCurrent" class="form-control" id="newCurrentFeeTypeDataCurrent" placeholder="Choose Client Name" >\
										';
			//get the list of available terms
			$.each(feetypes, function(i, item){
				Content_Pa_Deposits_Others += '<option value="' + item.id + '">' + item.name + ' #' + item.description + '</option>';
			});
		Content_Pa_Deposits_Others += '</select>\
									</div >\
								</div>\
								<div class="form-group" >\
									<div class="col-sm-3 text-right">Description</div>\
									<div class="col-sm-8">\
										<input name="studentCurrentDescription" type="text" id="paNewBudgetItemAmount" class="form-control" >\
									</div >\
								</div>\
								<div class="form-group" >\
									<div class="col-sm-3 text-right">Amount</div>\
									<div class="col-sm-8">\
										<input name="studentCurrentAmount" type="text" id="paNewBudgetItemAmount" class="form-control" >\
									</div >\
								</div>\
								<div class="form-group" >\
									<div class="col-sm-3 text-right">Due Date</div>\
									<div class="col-sm-8">\
										<input name="studentCurrentDueDate" type="text" id="paNewStudentCurrentDueDate" class="form-control" >\
									</div >\
								</div>\
							</div>\
							<div class="modal-footer" >\
								<div class="pull-right btn-group">\
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
									<button type="submit" class="btn btn-primary" id="paAccountingAddStudentToFeePaymentSubmit" >Add to List</button>\
								</div>\
							</div>\
						</form>\
								<div id=studentnfocontainner></div>';
	pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
	/* End of Ajax Request */
	console.log("2. "+StudentID);
	$('#Model_DashPaFeeStudentCurrent').modal({ show: true});
	$('#Model_DashPaFeeStudentCurrent').on('hidden.bs.modal', function () {
		console.log("unloading. "+StudentID);
		OnlyStudentCurrent(StudentID);
	});
	console.log("3. "+StudentID);
	/* Validate the form fields */
	validateAddStudentCurrent(StudentID);
	console.log("Function ends. "+StudentID);
}

function DistributeAmount(){
	//alert($("#incomesDebtFieldsData").val());
	var debtsEnds = $("#incomesDebtFieldsData").val();
	var previousEnds = $("#incomesPreviousFieldsData").val();
	var currentEnds = $("#incomesCurrentFieldsData").val();
	
	var PaidTotalAmount = $("#paNewIncomeAmount").val() * 1 ;
	for(i=1; i<= debtsEnds; i++){
		//console.log(i + ". "+debtsEnds);
		//compare 2 value for a single row
		var availableAmount = 0;
		//if( $("#debtDataOn_" + i).val() > $("#debtPaidDataOn_" + i).val() ){
		
		//console.log(PaidTotalAmount + " > " + $("#debtDataOn_" + i).val()+ " ==>" + (PaidTotalAmount > $("#debtDataOn_" + i).val() ) );
		availableAmount = (PaidTotalAmount > ($("#debtDataOn_" + i).val()*1) )? $("#debtDataOn_" + i).val():PaidTotalAmount;
		$("#debtPaidDataOn_" + i).val( availableAmount );
		//}
		//console.log("To be written in the form: " + availableAmount);
		PaidTotalAmount -= availableAmount
		//console.log("Remaining: " + PaidTotalAmount);
		
	}
	/* Now distribute for previous request */
	for(i=1; i<= previousEnds; i++){
		//console.log(i + ". "+debtsEnds);
		//compare 2 value for a single row
		var availableAmount = 0;
		//if( $("#debtDataOn_" + i).val() > $("#debtPaidDataOn_" + i).val() ){
		
		//console.log(PaidTotalAmount + " > " + $("#debtDataOn_" + i).val()+ " ==>" + (PaidTotalAmount > $("#debtDataOn_" + i).val() ) );
		availableAmount = (PaidTotalAmount > ($("#previousDataOn_" + i).val()*1) )? $("#previousDataOn_" + i).val():PaidTotalAmount;
		$("#previousPaidDataOn_" + i).val( availableAmount );
		//}
		//console.log("To be written in the form: " + availableAmount);
		PaidTotalAmount -= availableAmount
		//console.log("Remaining: " + PaidTotalAmount);
		
		
	}
	/* Now distribute for current request */
	for(i=1; i<= currentEnds; i++){
		//console.log(i + ". "+debtsEnds);
		//compare 2 value for a single row
		var availableAmount = 0;
		//if( $("#debtDataOn_" + i).val() > $("#debtPaidDataOn_" + i).val() ){
		
		//console.log(i + ". "+ PaidTotalAmount + " > " + $("#debtDataOn_" + i).val()+ " ==>" + (PaidTotalAmount > $("#debtDataOn_" + i).val() ) );
		availableAmount = (PaidTotalAmount > ($("#currentDataOn_" + i).val()*1) )? $("#currentDataOn_" + i).val():PaidTotalAmount;
		$("#currentPaidDataOn_" + i).val( availableAmount );
		//}
		//console.log("To be written in the form: " + availableAmount);
		PaidTotalAmount -= availableAmount
		//console.log("Remaining: " + PaidTotalAmount);
	}
	
	/* Add Un-assigned value if remains */
	$("#unassignedAmount").val(PaidTotalAmount);
}

function DistributeAmountCustom(affected_id){
	//alert($("#incomesDebtFieldsData").val());
	var debtsEnds = $("#incomesDebtFieldsData").val();
	var previousEnds = $("#incomesPreviousFieldsData").val();
	var currentEnds = $("#incomesCurrentFieldsData").val();
	
	var PaidTotalAmount = $("#paNewIncomeAmount").val() * 1;
	
	for(i=1; i<= debtsEnds; i++){
		//console.log(i + ". "+debtsEnds);
		//compare 2 value for a single row
		var availableAmount = 0;
		//if( $("#debtDataOn_" + i).val() > $("#debtPaidDataOn_" + i).val() ){
		
		//console.log(PaidTotalAmount + " > " + $("#debtDataOn_" + i).val()+ " ==>" + (PaidTotalAmount > $("#debtDataOn_" + i).val() ) );
		availableAmount = $("#debtPaidDataOn_" + i).val();
		//$("#debtPaidDataOn_" + i).val( availableAmount );
		//}
		//console.log("To be written in the form: " + availableAmount);
		PaidTotalAmount -= availableAmount
		//console.log("Remaining: " + PaidTotalAmount);
		
	}
	/* Now distribute for previous request */
	for(i=1; i<= previousEnds; i++){
		//console.log(i + ". "+debtsEnds);
		//compare 2 value for a single row
		var availableAmount = 0;
		//if( $("#debtDataOn_" + i).val() > $("#debtPaidDataOn_" + i).val() ){
		
		//console.log(PaidTotalAmount + " > " + $("#debtDataOn_" + i).val()+ " ==>" + (PaidTotalAmount > $("#debtDataOn_" + i).val() ) );
		availableAmount = $("#previousPaidDataOn_" + i).val();
		//$("#previousPaidDataOn_" + i).val( availableAmount );
		//}
		//console.log("To be written in the form: " + availableAmount);
		PaidTotalAmount -= availableAmount
		//console.log("Remaining: " + PaidTotalAmount);
		
		
	}
	
	/* Now distribute for current request */
	for(i=1; i<= currentEnds; i++){
		//console.log(i + ". "+debtsEnds);
		//compare 2 value for a single row
		var availableAmount = 0;
		//if( $("#debtDataOn_" + i).val() > $("#debtPaidDataOn_" + i).val() ){
		
		//console.log(i + ". "+ PaidTotalAmount + " > " + $("#debtDataOn_" + i).val()+ " ==>" + (PaidTotalAmount > $("#debtDataOn_" + i).val() ) );
		availableAmount = $("#currentPaidDataOn_" + i).val();
		//$("#currentPaidDataOn_" + i).val( availableAmount );
		//}
		//console.log("To be written in the form: " + availableAmount);
		PaidTotalAmount -= availableAmount
		//console.log("Remaining: " + PaidTotalAmount);
	}
	
	/* Add Un-assigned value if remains */
	$("#unassignedAmount").val(PaidTotalAmount);
}

/* this allow the new invoice to have new price after editing the registered number */
function RecalculateTotal(counter, type){

	var selectedTaxId = $("#pa_New"+type+"ItemTax" + counter).val();
	var Productamount = ($("#paNew"+type+"Price" + counter).val()*1) * ($("#paNew"+type+"Unit" + counter).val()*1);
	var FinalAmount   = $("#paNew"+type+"FinalAmount" + counter).val()*1;
	var TotalAmount   = $("#paNew"+type+"PaymentTotalAmount").val()*1;
	
	if ( selectedTaxId > 0 ) {

		var selectedTaxPerentage = $("#pa_New"+type+"ItemTax" + counter).select2().find(":selected").data("percentage");
		var New_FinalAmount 	 = parseFloat(Productamount) +  Math.ceil( ((Productamount* parseFloat(selectedTaxPerentage))/100) * 100) / 100;

	}else{
		var New_FinalAmount = Productamount;
	}

	var New_TotalAmount     = TotalAmount - parseFloat(FinalAmount) + parseFloat(New_FinalAmount);

	$("#paNew"+type+"FinalAmount" + counter).val(New_FinalAmount);
	$("#paNew"+type+"PaymentTotalAmount").val(New_TotalAmount);
	
}

/* this allow the new invoice to have new price after editing the registered number */
function RecalculateTotalReverse(counter,type){


	var selectedTaxId = $("#pa_New"+type+"ItemTax" + counter).val();
	//console.log("selectedTaxId: " + selectedTaxId);
	var ProductUnit   = $("#paNew"+type+"Unit" + counter).val()*1;
	//console.log("ProductUnit: " + ProductUnit);
	var Productamount = $("#paNew"+type+"Price" + counter).val()*1*ProductUnit; //<<<<<====================================================================================================
	//console.log("Productamount: " + Productamount);
	var FinalAmount   = $("#paNew"+type+"FinalAmount" + counter).val()*1;
	//console.log("FinalAmount: " + FinalAmount);
	var TotalAmount   = $("#paNew"+type+"PaymentTotalAmount").val()*1;
	//console.log("TotalAmount: " + TotalAmount);

	if ( selectedTaxId > 0 ) {

		var selectedTaxPerentage = $("#pa_New"+type+"ItemTax" + counter).select2().find(":selected").data("percentage");
		//console.log("selectedTaxPerentage_>0: " + selectedTaxPerentage);
		var New_Productamount 	 = Math.floor( ((100*parseFloat(FinalAmount)) / (100 + parseFloat(selectedTaxPerentage))) * 100) / 100 //
		//console.log("New_Productamount_>0: " + New_Productamount);
		//var New_ProductAmountss	= New_Productamount/ProductUnit;

		var OLD_FinalAmount 	 = parseFloat(Productamount) + Math.ceil( ((Productamount* parseFloat(selectedTaxPerentage))/100) * 100) / 100;
		//console.log("OLD_FinalAmount_>0: " + OLD_FinalAmount);

	}else{
		var New_Productamount = FinalAmount;
		//console.log("New_Productamount: " + New_Productamount);
		var OLD_FinalAmount   = parseFloat(Productamount);
		//console.log("OLD_FinalAmount: " + OLD_FinalAmount);
	}

	var New_TotalAmount     = TotalAmount - parseFloat(OLD_FinalAmount) + parseFloat(FinalAmount);
	//console.log("New_TotalAmount: " + New_TotalAmount);
	var New_ProductAmounts	= New_Productamount/ProductUnit;
	//console.log("New_ProductAmounts: " + New_ProductAmounts);
	$("#paNew"+type+"Price" + counter).val( (New_ProductAmounts));
	$("#paNew"+type+"PaymentTotalAmount").val(New_TotalAmount);

}


