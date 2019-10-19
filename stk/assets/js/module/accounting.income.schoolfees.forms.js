
function NewIncomeForm(studentoptions, dataoptionstaff, activebank, activeclient){
  //get address where to load new content now
  var dashPayments_content = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  //now hide the form a short
  dashPayments_content.slideUp();

  //adjust the content to fit the new design now
  var content = '<div class="col-md-12" id="myContainner"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>New Income</h4>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">\
					<div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
						<div class="modal-wrapper">\
							<div class="modal-dialog">\
								<div class="modal-content">\
									<div class="modal-header">\
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
										<h4 class="modal-title text-left" id="myModalLabel22">New Invoice Information 1</h4>\
									</div>\
									<div id="ModalDataLoadingNow">\
									\
									</div>\
								</div>\
							</div>\
						</div>\
					</div>\
					<div class="modal fade" id="Model_DashPaFeeStudentCurrent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
						<div class="modal-wrapper">\
							<div class="modal-dialog">\
								<div class="modal-content">\
									<div class="modal-header">\
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
										<h4 class="modal-title text-left" id="myModalLabel22">Add New Invoice to be paid</h4>\
									</div>\
									<div id="ModalDataLoadingNowCurrent">\
									\
									</div>\
								</div>\
							</div>\
						</div>\
					</div>';

              content+='<div id="pa_Expense_ExpenseType">\
            		  <div id="UplaodSchoolFeesPaymentLoding">\
						<center>\
							<div class="row">]\
								<img src="../packages/assets/img/loading.gif" alt="Loading..." height="35">\
							</div>\
							<div class="row">\
								<p>Saving...</p>\
							</div>\
						</center>\
					  </div>\
                      <form id="formDashPaNewIncomeData" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
                          <div class="form-group" > \
                            <div class="col-sm-3 text-right">Income Type</div>\
                            <div class="col-sm-7">\
                              <select name="incomeSource" class="form-control" id="paNewIncomeSource" placeholder="Choose Income Source" >\
                                <option></option>\
                                <option value="1" > School Fees</option>\
                                <option value="2" > Invoice</option>\
                              </select>\
                            </div >\
                          </div>\
                          <div id="PaNewIncomeSchoolFeesContainer" >\
                              	<div class="form-group">\
	                              	<div class="col-sm-3 text-right">Student</div>\
		                            <div class="col-sm-7">\
		                                <select name="incomeStudent" class="form-control" id="paNewIncomeStudent" placeholder="Choose student" >\
		                                ' + studentoptions + '\
		                                </select>\
		                            </div >\
	                           	</div>\
	                           	<div class="form-group" >\
									<div class="col-sm-12 text-center"><label>Notify parents with the new balance&nbsp;&nbsp;<input type="checkbox" name="incomeNotifyParents" value="1" ></label></div>\
								</div>\
                            </div>\
                            <div id="PaNewIncomeAccountsReceivablesContainer" >\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Client</div>\
									<div class="col-sm-7">\
										<select name="incomeInvoiceId" class="form-control" id="paNewIncomeClient" placeholder="Choose a Client invoice" >\
											' + activeclient + '\
										</select>\
									</div >\
								</div>\
                            </div>\
                            <div class="form-group" > \
								<div class="col-sm-3 text-right">Amount</div>\
								<div class="col-sm-7">\
									<input name="incomeAmountData" type="text" id="paNewIncomeAmount" onkeyup="DistributeAmount()" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right"><div style="display: inline-block; vertical-align: middle; float: none;" >Students\'s Fees</div></div>\
								<div class="col-sm-7" id="studentUnpaidInvoiContainner">\
								</div >\
							</div>\
                          	<div class="form-group" >\
                                <label class="col-sm-3 control-label">Channel</label>\
                                <div class="col-sm-1"></div>\
                                <div class="col-sm-4">\
                                    <div class="radio">\
                                        <label>\
                                            <input type="radio" name="incomeChannel" id="paNewIncomeChannel" value="1" checked /> Bank Deposit\
                                        </label>\
                                    </div>\
                                </div>\
                                <div class="col-sm-4">\
                                  <div class="radio">\
                                        <label>\
                                            <input type="radio" name="incomeChannel" id="paNewIncomeChannel" value="0" /> Cash\
                                        </label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div id="PaNewIncomeBankContainer">\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Account</div>\
									<div class="col-sm-7">\
										<select name="incomeBankAccount" class="form-control" id="paNewIncomeBankAccount" placeholder="Choose Bank Account here" >\
											' + activebank + '\
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
										<select name="incomeCashAccount" class="form-control" id="AccountingStaffAccountID" placeholder="Choose Cash on hand Account" >\
										' + dataoptionstaff + '</select>\
									</div >\
								</div>\
							</div>\
                          <div class="form-group" > \
                            <div class="col-sm-3 text-right">Date</div>\
                            <div class="col-sm-7">\
                              <input name="incomeDate" type="text" id="paNewIncomeDate" class="form-control" >\
                            </div >\
                          </div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Income</button>\
                          </div>\
                        </div>\
					</form>\
                </div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';
      //add the content to the container
      dashPayments_content.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();
  $("#UplaodSchoolFeesPaymentLoding").hide();
  
  //Enable to Modal to be shown programatically.
  $('#Model_DashPaErrorLogs').modal({ show: false});
	/* Activate button for Incomes Payments */
  validateNewIncomeForm();
}

function PayDebtForm(debtId){
	var url2 = base_url+"accounting/schoolfees/debt/" + debtId;
	/* load the student list now */
	$.getJSON( url2, function( student ) {
		/* load the form by passing received data the function */
		DebtPaymentForm(debtId, student);
	});
}

function PayOverDue(invoiceId){
	var url2 = base_url+"accounting/schoolfees/overdue/" + invoiceId;
	/* load the student list now */
	$.getJSON( url2, function( student ) {
		/* load the form by passing received data the function */
		OverDuePaymentForm(invoiceId, student);
	});
}

function PayInvoice(invoiceId){
	var url2 = base_url+"accounting/invoices/" + invoiceId;
	/* load the student list now */
	$.getJSON( url2, function( student ) {
		/* load the form by passing received data the function */
		InvoicePaymentForm(invoiceId, student);
	});
}

function PayBill(billId){
	var url2 = base_url+"accounting/bills/" + billId;
	/* load the bill information list now */
	$.getJSON( url2, function( bills ) {
		/* load the form by passing received data the function */
		BillPaymentForm(billId, bills);
	});
}

function PrintInvoice(invoiceId){
	var oldData = $("#print" + invoiceId).html();
	//change the text value to be printed
	$("#print" + invoiceId).html("printing...");
	//console.log("Printing the " + invoiceId);
	var url2 = base_url+"accounting/invoices/" + invoiceId + "/edit?option=print";
	/* load the student list now */
	$.getJSON( url2, function( invoiceData ) {
		/* load the form by passing received data the function */
		//InvoicePaymentForm(invoiceId, student);
		console.log(invoiceData);

		/* The generated pdf file is with the invoice id as name for never gerating many file for single invoice
		 * and the file can serve for printing operation or email notification 
		 */
		if(invoiceData.status == "error"){
		 	$.gritter.add({
               title: 'Failed',
               text: invoiceData.message,
               class_name: 'danger gritter-center',
               time: ''
           });
		 	$("#print" + invoiceId).html(oldData);
		 	$("#printLog" + invoiceId).html("&nbsp;<span class='text-red'>" + invoiceData.content + "</span>");
		} else if(invoiceData.status == "success"){
		 	$.gritter.add({
               title: 'Success',
               text: invoiceData.message,
               class_name: 'success gritter-center',
               time: ''
           });
		 	$("#print" + invoiceId).html(oldData);
		 	$("#printLog" + invoiceId).html("&nbsp;<span class='text-orange'>" + invoiceData.content + "</span>");
		} else if(invoiceData.status == "download"){
		 	$.gritter.add({
               title: 'Success',
               text: invoiceData.message,
               class_name: 'success gritter-center',
               time: ''
           	});
		 	$("#print" + invoiceId).html(oldData);
		 	$("#printLog" + invoiceId).html("&nbsp;<a href='/accounting/invoices/PDF/" + invoiceData.content + "/print' target='_blank' class='text-red'><i class='fa fa-file-pdf-o'></i></span>");
		}

		
	}).done(function(){
		//$("#print" + invoiceId).html("Printed! Thanks");
		// $("#linkprint" + invoiceId)[0].click();
	}).fail(function(err) {
		console.log(err.responseText);
        $.gritter.add({
                   title: 'Failed',
                   text: 'Invoice Generation command Fails. <br />Please Check the internet connection and try again<br />' + err.responseText,
                   class_name: 'danger gritter-center',
                   time: ''
        });
        $("#print" + invoiceId).html(oldData);
        $("#printLog" + invoiceId).html("&nbsp;<span class='text-red'>Retry</span>");

 	});
}

function SendInvoice(invoiceId){
	// console.log("Sending by email the " + invoiceId);
	var url2 = b.Textase_url+"accounting/invoices/" + invoiceId + "/edit?option=send";
	/* load the student list now */
	$.getJSON( url2, function( invoiceData ) {
		/* load the form by passing received data the function */
		//InvoicePaymentForm(invoiceId, student);
		// console.log(invoiceData);
		/* The generated pdf file is with the invoice id as name for never gerating many file for single invoice
		 * and the file can serve for printing operation or email notification 
		 */
	});
}

function UpdateIncomeForm(studentoptions, dataoptionstaff, activebank, activeclient, transactionId, tr_exist){
  //get address where to load new content now
  var dashPayments_content = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  //now hide the form a short
  dashPayments_content.slideUp();

  //adjust the content to fit the new design now
  var content = '<div class="col-md-12" id="myContainner"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Review The Transaction Now</h4>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">\
					<div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
						<div class="modal-wrapper">\
							<div class="modal-dialog">\
								<div class="modal-content">\
									<div class="modal-header">\
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
										<h4 class="modal-title text-left" id="myModalLabel22">New Invoice Information 2</h4>\
									</div>\
									<div id="ModalDataLoadingNow">\
									\
									</div>\
								</div>\
							</div>\
						</div>\
					</div>\
					<div class="modal fade" id="Model_DashPaFeeStudentCurrent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
						<div class="modal-wrapper">\
							<div class="modal-dialog">\
								<div class="modal-content">\
									<div class="modal-header">\
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
										<h4 class="modal-title text-left" id="myModalLabel22">Add New Invoice to be paid</h4>\
									</div>\
									<div id="ModalDataLoadingNowCurrent">\
									\
									</div>\
								</div>\
							</div>\
						</div>\
					</div>';
										
				//console.log(tr_exist[0].isBank);
                content+='<div id="pa_Expense_ExpenseType">\
					\
                      <form id="formDashPaUpdateIncomeData" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
                          <div class="form-group" > \
                            <div class="col-sm-3 text-right">Income Type</div>\
                            <div class="col-sm-7">\
								<input type=hidden name=transactionIdOld value="' + transactionId + '" />\
								<input type=hidden name="_method" value="PUT" />\
                              <select name="incomeSource" class="form-control" id="paNewIncomeSource" placeholder="Choose Income Source" >\
                                <option></option>\
                                <option value="1" '+ (tr_exist[0].incomeSource == 1?'selected':'') +' > School Fees</option>\
                                <option value="2" '+ (tr_exist[0].incomeSource == 2?'selected':'') +' > Invoice</option>\
                              </select>\
                            </div >\
                          </div>\
                          <div id="PaNewIncomeSchoolFeesContainer" >\
                              <div class="form-group">\
                              <div class="col-sm-3 text-right">Student</div>\
                              <div class="col-sm-7">\
                                \
                                <select name="incomeStudent" class="form-control" id="paNewIncomeStudent" placeholder="Choose student" >\
                                  <option></option>' + studentoptions + '\
                                </select>\
                              </div >\
                            </div>\
                            </div>\
                            <div id="PaNewIncomeAccountsReceivablesContainer" >\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Client</div>\
									<div class="col-sm-7">\
										<select name="incomeInvoiceId" class="form-control" id="paNewIncomeClient" placeholder="Choose a Client invoice" >\
											<option></option>' + activeclient + '\
										</select>\
									</div >\
								</div>\
                            </div>\
                            <div class="form-group" > \
	                            <div class="col-sm-3 text-right">Amount</div>\
	                            <div class="col-sm-7">\
								' + (tr_exist[0].incomeSource == 2?'<input type=hidden name="existing_invoice" value="' + tr_exist[0].regNumber + '" />':'') + '\
	                              <input name="incomeAmountData" type="text" id="paNewIncomeAmount" onkeyup="DistributeAmount()" value="' + tr_exist[0].amount + '" class="form-control" >\
	                            </div >\
                         	</div>\
                         	<div class="form-group" > \
	                            <div class="col-sm-3 text-right">Student\'s fees</div>\
	                            <div class="col-sm-7" id=studentUnpaidInvoiContainner">\
								</div >\
                         	</div>\
                          <div class="form-group" >\
                                <label class="col-sm-3 control-label">Channel</label>\
                                <div class="col-sm-1"></div>\
                                <div class="col-sm-4">\
                                    <div class="radio">\
                                        <label>\
                                            <input type="radio" ' + (tr_exist[0].isBank == 0?"checked":"") + ' name="incomeChannel" id="paNewIncomeChannel" value="0" /> Cash\
                                        </label>\
                                    </div>\
                                </div>\
                                <div class="col-sm-4">\
                                  <div class="radio">\
                                        <label>\
                                            <input type="radio" ' + (tr_exist[0].isBank == 1?"checked":"") + ' name="incomeChannel" id="paNewIncomeChannel" value="1" /> Bank Deposit\
                                        </label>\
                                    </div>\
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
                                <input name="bankSlipNumber" value="' + tr_exist[0].bankSlipNumber + '" type="text" id="paNewPaymentBankSlipNumber" class="form-control" >\
                              </div >\
                            </div>\
                            <div class="form-group" > \
                              <div class="col-sm-3 text-right"> Bank Operation Type</div>\
                              <div class="col-sm-7">\
                                <select name="incomeBankOperationType" class="form-control" id="paNewIncomeBankOperationType" placeholder="Choose Income Bank Operation Type" >\
                                  <option></option>\
                                  <option value="1" ' + (tr_exist[0].operationType == 1?"selected":"") + ' > Deposit</option>\
                                  <option value="2" ' + (tr_exist[0].operationType == 2?"selected":"") + ' > Transfer</option>\
                                  <option value="3" ' + (tr_exist[0].operationType == 3?"selected":"") + ' > Check</option>\
                                </select>\
                              </div >\
                            </div>\
                            </div>\
							<div  id="PaNewIncomeCashContainer">\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Staff</div>\
									<div class="col-sm-7">\
										<select name="incomeCashAccount" class="form-control" id="AccountingStaffAccountID" placeholder="Choose Bank Account here" >\
										' + dataoptionstaff + '</select>\
									</div >\
								</div>\
							</div>\
                          <div class="form-group" > \
                            <div class="col-sm-3 text-right">Date</div>\
                            <div class="col-sm-7">\
                              <input name="incomeDate" type="text" id="paNewIncomeDate" value="' + tr_exist[0].date + '" class="form-control" >\
                            </div >\
                          </div>\
                        </div>\
                        <div class="modal-footer" >\
							<label style="padding:0 5px; "><input type=checkbox id="delete_operation" onclick="if($(\'#delete_operation\').prop(\'checked\')){return confirm(\'This Will delete All Information on this form\');}" name=canceltransaction /> Cancel This Transaction</label>\
                            \
                          <div class="pull-right btn-group">\
							<button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Update Transaction</button>\
                          </div>\
                        </div>\
					</form>\
                </div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';
      //add the content to the container
      dashPayments_content.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();
	/* Activate button for Incomes Payments */
  validateUpdateIncomeForm(tr_exist);
}


/* Start of Expense menu */
function displayNewExpenseForm( budgetItems, budgetItemAsset, schoolAccountCashStaff, schoolAccountBank, activeVendorBills, activeVendor, activeLoans ){
	//get address where to load new content now
	  var dashPayments_content = $("#dashPayments_content");
	  var dashContentainer_Payments = $("#dashContentainer_Payments");

  //now hide the form a short
	  dashPayments_content.slideUp();
		var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
	        content+='<div class="row">';
	          content+= '<div class="col-sm-12">';
	            content+='<div class="row text-center">';
	              content+= '<div class="col-sm-8">';
	                content+= '<h4>New Expense</h4>';
	              content+= '</div>';
	            content+= '</div>';
	            content+= '<div class="row">';
	              content+= '<div class="col-sm-12">';
	                content+=' \
					<form enctype="form/multipart" id="formUplaofMonthlyExpense" style="display:none;" class="form-horizontal">\
						<div class="form-group" >\
							<div class="col-sm-5"><input type=file name=expense_list /></div>\
							<div class="col-sm-3"><input type=submit name=upload value=import /></div>\
						</div>\
					</form>\
					<form id="formDashPaNewExpenseAll" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
	                    <div id="pa_Expense_ExpenseType">\
	                       <div class="modal-body" >\
							  <div class="form-group" >\
								<label class="col-sm-3 control-label"></label>\
								<div class="col-sm-1"></div>\
								<div class="col-sm-2">\
									<div class="radio">\
										<label>\
											<input type="radio" name="expenseIsRecorded" id="paNewExpenseUsing" value="0" checked /> General\
										</label>\
									</div>\
								</div>\
								<div class="col-sm-2">\
									<div class="radio">\
										<label>\
											<input type="radio" name="expenseIsRecorded" id="paNewExpenseUsing" value="1" />  Paying Bills\
										</label>\
									</div>\
								</div>\
								<div class="col-sm-2">\
									<div class="radio">\
										<label>\
											<input type="radio" name="expenseIsRecorded" id="paNewExpenseUsing" value="2" />  Buying Asset\
										</label>\
									</div>\
								</div>\
								 <div class="col-sm-2">\
									<div class="radio">\
										<label>\
											<input type="radio" name="expenseIsRecorded" id="paNewExpenseUsing" value="3" /> Paying Loan\
										</label>\
									</div>\
								</div>\
							  </div>\
							  <div id="AccountingExpenseGeneralInfoContainer">\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Budget Item</div>\
									<div class="col-sm-7">\
										<select name="expenseBudgetItemGeneral" class="form-control" id="AccountingBudgetItem" placeholder="Choose Expense Type" >';
			   content+= budgetItems + '</select>\
									</div >\
								</div>\
							  </div>\
							  <div id="AccountingExpenseBillInfoContainer">\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Bill</div>\
									<div class="col-sm-7">\
										<select name="expenseBudgetItemBills" class="form-control" id="AccountingExpenseBill" placeholder="Select Bill to Pay" >\
											' + activeVendorBills +'\
										</select>\
									</div >\
								</div>\
							  </div>\
							  <div id="AccountingExpenseAssetInfoContainer" >\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Budget Item</div>\
									<div class="col-sm-7">\
										<select name="expenseBudgetItemAsset" class="form-control" id="AccountingAssetBudgetItem" placeholder="Choose Expense Type" >\
										' + budgetItemAsset + '</select>\
									</div >\
								</div>\
								<div class="form-group"  > \
									<div class="col-sm-3 text-right">Asset Name</div>\
									<div class="col-sm-7">\
										<input name="expenseAssetNameData" type="text" id="paNewExpenseAssetDepreciation" class="form-control" >\
									</div >\
								</div>\
								<div id="newExpenseBuyingAssetContainer" >\
									<div class="form-group"  > \
										<div class="col-sm-3 text-right">Annual Depr. Rate[%]</div>\
										<div class="col-sm-7">\
											<input name="expenseAssetDepreciationRate" type="text" id="paNewExpenseDepreciationRate" class="form-control" >\
										</div >\
									</div>\
									<div class="form-group" > \
									<div class="col-sm-3 text-right">Vendor</div>\
									<div class="col-sm-7">\
										<select name="expenseAssetVendorId" class="form-control" id="AccountingExpenseVendor" placeholder="Choose Vendor ID" >\
											' + activeVendor + '\
										</select>\
									</div >\
								</div>\
								</div>\
							  </div>\
								<div id="AccountingExpenseLoanInfoContainer" >\
									<div class="form-group"  > \
										<div class="col-sm-3 text-right">Loan</div>\
										<div class="col-sm-7">\
											<select name="expenseLoanId" class="form-control" id="AccountingLoanID" placeholder="Select Loan to Pay" >\
												' + activeLoans + '\
											</select>\
										</div >\
									</div>\
									<div class="form-group"  > \
										<div class="col-sm-3 text-right">Period</div>\
										<div class="col-sm-7">\
											<input name="expenseLoanPeriod" type="text" id="paNewExpenseDepreciationRate" class="form-control" >\
										</div >\
									</div>\
									<div class="form-group"  > \
										<div class="col-sm-3 text-right">Interest on amount paid</div>\
										<div class="col-sm-7">\
											<input name="expenseLoanInterest" type="text" id="paNewExpenseLoanInterestHere" class="form-control" value="0">\
											<input name="expenseLoanInterestRate" type="hidden" id="paNewExpenseLoanInterestRateHere" >\
										</div >\
									</div>\
									<div class="form-group"  > \
										<div class="col-sm-3 text-right">Penality on amount paid</div>\
										<div class="col-sm-7">\
											<input name="expenseLoanPenality" type="text" id="paNewExpenseLoanPenalityHere" class="form-control" value="0">\
										</div >\
									</div>\
								</div>\
								<div class="form-group"  > \
									<div class="col-sm-3 text-right">Amount</div>\
									<div class="col-sm-7">\
										<input name="expenseAllAmount" type="text" id="paNewExpenseAllAmount" class="form-control" >\
									</div >\
								</div>\
								<div class="form-group" >\
	                                <label class="col-sm-3 control-label">Paid Using</label>\
	                                <div class="col-sm-1"></div>\
	                                <div class="col-sm-4">\
	                                    <div class="radio">\
	                                        <label>\
	                                            <input type="radio" name="expenseChannel" id="paNewIncomeChannel" value="0" /> Cash\
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
	                              <div class="col-sm-3 text-right">Account</div>\
	                              <div class="col-sm-7">\
	                                <select name="expenseBankAccount" class="form-control" id="AccountingSchoolBankAccount_A" placeholder="Choose Bank Account here" >\
	                                	' + schoolAccountBank + '\
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
	                                <select name="expensesBankOperationType" class="form-control" id="AccountingBankOperationType_A" placeholder="Choose Income Bank Operation Type" >\
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
											<select name="expenseCashAccount" class="form-control" id="AccountingStaffAccountID" placeholder="Choose Bank Account here" >\
											' + schoolAccountCashStaff + '</select>\
										</div >\
									</div>\
								</div>\
								<div  id="PaNewExpensesCheckContainer">\
									<div class="form-group" > \
										<div class="col-sm-3 text-right">Check Number</div>\
										<div class="col-sm-7">\
											<input name="checkNumber" type="text" id="paNewPaymentCheckNumber" class="form-control" >\
										</div >\
									</div>\
									<div class="form-group" > \
										<div class="col-sm-3 text-right">Receiver</div>\
										<div class="col-sm-7">\
											<input name="underName" type="text" id="paNewPaymentCheckReceiver" class="form-control" >\
										</div >\
									</div>\
								</div>\
	                          <div class="form-group" > \
	                            <div class="col-sm-3 text-right">Memo</div>\
	                            <div class="col-sm-7">\
	                              <input name="expenseMemo" type="text" id="AccountingExpenseMemo" class="form-control" >\
	                            </div >\
	                          </div>\
	                          <div class="form-group" > \
	                            <div class="col-sm-3 text-right">Date</div>\
	                            <div class="col-sm-7">\
	                              <input name="expenseDate" type="text" id="AccountingExpenseDate" class="form-control" >\
	                            </div >\
	                          </div>\
	                        </div>\
	                        <div class="modal-footer" >\
	                          <div class="pull-right btn-group"><!--\
	                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>-->\
	                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Expense</button>\
	                          </div>\
	                        </div>\
						</form>\
	                </div>';
	              content+= '</div>';
	            content+= '</div>';
	          content+= '</div>';
	        content+= '</div>';
	      content+= '</div></div></div></div>';
	      //add the content to the container
	      dashPayments_content.html( content );
	  //load the contetn now
	  dashContentainer_Payments.slideDown();
	  dashPayments_content.slideDown();
		/* Activate button for Expense Payments */
	  validateNewExpenseForm();
  
}

/* Start of Expense menu */
function UpdateExpenseForm(dataoption, dataoption2, dataoptionstaff, activebank, activevendor, activevendoronly, activeloans, tr_exist){
	
	//console.log(tr_exist);
	//get address where to load new content now
  var dashPayments_content = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  //now hide the form a short
  dashPayments_content.slideUp();
	var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Review the transaction</h4>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='\
				<form id="formDashPaUpdateExpenseAll" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                    <input type=hidden name=transactionIdOld value="' + tr_exist.id + '" />\
					<input type=hidden name="_method" value="PUT" />\
                    <div id="pa_Expense_ExpenseType">\
                      <div class="modal-body" >\
						  <div class="form-group" >\
							<label class="col-sm-3 control-label"></label>\
							<div class="col-sm-1"></div>\
							<div class="col-sm-2">\
								<div class="radio">\
									<label>\
										<input type="radio" name="expenseIsRecorded" id="paNewExpenseUsing" value="0" checked /> General\
									</label>\
								</div>\
							</div>\
							<div class="col-sm-2">\
								<div class="radio">\
									<label>\
										<input type="radio" name="expenseIsRecorded" id="paNewExpenseUsing" value="1" />  Bills\
									</label>\
								</div>\
							</div>\
							<div class="col-sm-2">\
								<div class="radio">\
									<label>\
										<input type="radio" name="expenseIsRecorded" id="paNewExpenseUsing" value="2" />  Asset\
									</label>\
								</div>\
							</div>\
							 <div class="col-sm-2">\
								<div class="radio">\
									<label>\
										<input type="radio" name="expenseIsRecorded" id="paNewExpenseUsing" value="3" /> Loan\
									</label>\
								</div>\
							</div>\
						  </div>\
						  <div id="AccountingExpenseGeneralInfoContainer">\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Budget Item</div>\
								<div class="col-sm-7">\
									<select name="expenseBudgetItemGeneral" class="form-control" id="AccountingBudgetItem" placeholder="Choose Expense Type" >';
				content+= dataoption + '</select>\
								</div >\
							</div>\
						  </div>\
						  <div id="AccountingExpenseBillInfoContainer">\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Vendor</div>\
								<div class="col-sm-7">\
									<select name="expenseBudgetItemBills" class="form-control" id="AccountingExpenseVendorID" placeholder="Choose Vendor ID" >\
										<option></option>' + activevendor +'\
									</select>\
								</div >\
							</div>\
						  </div>\
						  <div id="AccountingExpenseAssetInfoContainer" >\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Budget Item</div>\
								<div class="col-sm-7">\
									<select name="expenseBudgetItemAsset" class="form-control" id="AccountingAssetBudgetItem" placeholder="Choose Expense Type" >\
									' + dataoption2 + '</select>\
								</div >\
							</div>\
							<div class="form-group"  > \
								<div class="col-sm-3 text-right">Asset Name</div>\
								<div class="col-sm-7">\
									<input name="expenseAssetNameData" type="text" value="' + tr_exist.assetName + '" id="paNewExpenseAssetDepreciation" class="form-control" >\
								</div >\
							</div>\
							<div id="newExpenseBuyingAssetContainer" >\
								<div class="form-group"  > \
									<div class="col-sm-3 text-right">Depreciation Annual Rate</div>\
									<div class="col-sm-7">\
										<input name="expenseAssetDepreciationRate" type="text" value="' + tr_exist.assetDepreciation + '" id="paNewExpenseDepreciationRate" class="form-control" >\
									</div >\
								</div>\
								<div class="form-group" > \
								<div class="col-sm-3 text-right">Vendor</div>\
								<div class="col-sm-7">\
									<select name="expenseAssetVendorId" class="form-control" id="AccountingExpenseAssetVendorID" placeholder="Choose Vendor ID" >\
										' + activevendoronly + '\
									</select>\
								</div >\
							</div>\
							</div>\
						  </div>\
							<div id="AccountingExpenseLoanInfoContainer" >\
								<div class="form-group"  > \
									<div class="col-sm-3 text-right">Loan</div>\
									<div class="col-sm-7">\
										<select name="expenseLoanId" class="form-control" id="AccountingLoanID" placeholder="Choose Expense Type" >\
											<option></option>' + activeloans + '\
										</select>\
									</div >\
								</div>\
								<div class="form-group"  > \
									<div class="col-sm-3 text-right">Period</div>\
									<div class="col-sm-7">\
										<input name="expenseLoanPeriod" type="text" value="' + tr_exist.loanPeriod + '" id="paNewExpenseDepreciationRate" class="form-control" >\
									</div >\
								</div>\
								<div class="form-group"  > \
									<div class="col-sm-3 text-right">Amount</div>\
									<div class="col-sm-7">\
										<input name="expenseLoanAmount" type="text" value="' + tr_exist.loanAmount + '" id="paNewExpenseLoanAmountHere" class="form-control" >\
									</div >\
								</div>\
								<div class="form-group"  > \
									<div class="col-sm-3 text-right">Interest</div>\
									<div class="col-sm-7">\
										<input name="expenseLoanInterest" type="text" value="' + tr_exist.loanInterest + '" id="paNewExpenseLoanInterestHere" class="form-control" >\
										<input name="expenseLoanInterestRate" type="hidden" value="' + tr_exist.loanInterestRate + '" id="paNewExpenseLoanInterestRateHere" >\
									</div >\
								</div>\
								<div class="form-group"  > \
									<div class="col-sm-3 text-right">Penality</div>\
									<div class="col-sm-7">\
										<input name="expenseLoanPenality" type="text" value="' + tr_exist.loanPenality + '" id="paNewExpenseLoanPenalityHere" class="form-control" >\
									</div >\
								</div>\
							</div>\
							<div class="form-group"  > \
								<div class="col-sm-3 text-right">Amount</div>\
								<div class="col-sm-7">\
									<input name="expenseAllAmount" type="text" id="paNewExpenseAllAmount" value="' + tr_exist.amount + '" class="form-control" >\
								</div >\
							</div>\
							<div class="form-group" >\
                                <label class="col-sm-3 control-label">Paid Using</label>\
                                <div class="col-sm-1"></div>\
                                <div class="col-sm-4">\
                                    <div class="radio">\
                                        <label>\
                                            <input type="radio" ' + (tr_exist.isBank == 0?"checked":"") + ' name="expenseChannel" id="paNewIncomeChannel" value="0" /> Cash\
                                        </label>\
                                    </div>\
                                </div>\
                                <div class="col-sm-4">\
                                  <div class="radio">\
                                        <label>\
                                            <input type="radio" ' + (tr_exist.isBank == 1?"checked":"") + '  name="expenseChannel" id="paNewIncomeChannel" value="1" /> Bank Deposit\
                                        </label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div  id="PaNewIncomeBankContainer">\
                              <div class="form-group" > \
                              <div class="col-sm-3 text-right">Account</div>\
                              <div class="col-sm-7">\
                                <select name="expenseBankAccount" class="form-control" id="AccountingSchoolBankAccount_" placeholder="Choose Bank Account here" >\
                                  <option></option>\
                                    ' + activebank + '\
                                </select>\
                              </div >\
                            </div>\
                            <div class="form-group" > \
                              <div class="col-sm-3 text-right">Bank Slip Number</div>\
                              <div class="col-sm-7">\
                                <input name="bankSlipNumber" value="' + tr_exist.bankSlipNumber + '" type="text" id="paNewPaymentBankSlipNumber" class="form-control" >\
                              </div >\
                            </div>\
                            <div class="form-group" > \
                              <div class="col-sm-3 text-right"> Bank Operation Type</div>\
                              <div class="col-sm-7">\
                                <select name="expensesBankOperationType" class="form-control" id="AccountingBankOperationType" placeholder="Choose Income Bank Operation Type" >\
                                  <option></option>\
                                  <option value="1" ' + (tr_exist.operationType == 1?"selected":"") + ' > Deposit</option>\
                                  <option value="2" ' + (tr_exist.operationType == 2?"selected":"") + ' > Transfer</option>\
                                  <option value="3" ' + (tr_exist.operationType == 3?"selected":"") + ' > Check</option>\
                                </select>\
                              </div >\
                            </div>\
                            </div>\
							<div  id="PaNewIncomeCashContainer">\
								<div class="form-group" > \
									<div class="col-sm-3 text-right">Staff</div>\
									<div class="col-sm-7">\
										<select name="expenseCashAccount" class="form-control" id="AccountingStaffAccountID" placeholder="Choose Bank Account here" >\
										' + dataoptionstaff + '</select>\
									</div >\
								</div>\
							</div>\
                          <div class="form-group" > \
                            <div class="col-sm-3 text-right">Memo</div>\
                            <div class="col-sm-7">\
                              <input name="expenseMemo" type="text" id="AccountingExpenseMemo" value="' + tr_exist.memo + '" class="form-control" >\
                            </div >\
                          </div>\
                          <div class="form-group" > \
                            <div class="col-sm-3 text-right">Date</div>\
                            <div class="col-sm-7">\
                              <input name="expenseDate" type="text" id="AccountingExpenseDate" value="' + tr_exist.date + '"  class="form-control" >\
                            </div >\
                          </div>\
                        </div>\
                        <div class="modal-footer" >\
                          <label style="padding:0 5px; "><input type=checkbox id="delete_operation" onclick="if($(\'#delete_operation\').prop(\'checked\')){return confirm(\'This Will delete All Information on this form\');}" name=canceltransaction /> Cancel This Transaction</label>\
                            <div class="pull-right btn-group"><!--\
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>-->\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Expense</button>\
                          </div>\
                        </div>\
					</form>\
                </div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';
      //add the content to the container
      dashPayments_content.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();
	/* Activate button for Expense Payments */
  validateUpdateExpenseForm(tr_exist);
  
}


function EditFeeTypeForm(frequence, existing){
	//get the instance of container division 
	var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
	//get the instance of the content division
	var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
	
	//hide the container first
	dashClientContainer.slideUp();
	console.log(existing);
	//prepare the content to be printed in the containner
	
	var content = '';
				content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
					content +='<div class="row text-center">';
						content+= '<div class="col-sm-8">';
							content+= '<h4> Edit Fee Type</h4>';
						content+= '</div>';
						content+= '<div class="col-sm-4">';
							content+= '<a id=AccountingSchoolFeesMoreFeeType class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a>';
						content+= '</div>';
					content+= '</div>';
					content+= '<div class="row" >\
									<div id="pa_Expense_ExpenseType">\
										\
                      <form id="formDashPaEditSchoolFeeType" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <input type=hidden name=feetypeid value="' + existing.id + '" />\
						<input type=hidden name=_method value=PUT />\
						<div class="modal-body" >\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Fee Type</div>\
								<div class="col-sm-7">\
									<input name="feeTypeName" type="text" id="paNewInvoiceDate" class="form-control" value="' + existing.name + '" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Description</div>\
								<div class="col-sm-7">\
									<input name="feeTypeDescription" type="text" id="paNewInvoiceDate" class="form-control" value="' + existing.description + '" >\
								</div >\
							</div>\
							<div class="form-group" > \
								<div class="col-sm-3 text-right">Frequence</div>\
								<div class="col-sm-7">\
									<select name="feeTypeFrequence" class="form-control" id="newCurrentFeeType" placeholder="Choose Client Frequence" >\
										';
						 $.each(frequence, function(i, item){
							 content+= '<option ' + (item.id == existing.frequenceId?'selected':'') + ' value="' + item.id + '">' + item.name + '</option>'
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
				content+= '</div>';
	  
	
	//send the prepared content to the screen for user
	dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  /* Activate the new invoice buttons */
  //AccountingSchoolFeesNewCurrentCurrentJS();
  //AccountingSchoolFeesListFeesType();
  
}


function load_acc_Assets_Prepaid_Rent(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Prepaid Rent</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewPrepaidRent" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Prepaid Rent</a>';
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

  var url = base_url+"accounting/rents";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="AccountingAssetFixedAssetListTable" >\
		<thead><tr ><th class="text-center">Date</th><th class="text-center">Rent</th><th class="text-center">Amount</th><th class="text-center">Monthly Payment</th><th class="text-center">Completed Stages</th><th class="text-center">Balance</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="5" style="text-align:right">Total:</th><th style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td >"+item.date+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.description+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.amount)+"</td>";
            Content_Pa_Deposits_Others +="<td >"+formatNumber(item.depreciation)+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+formatNumber(item.completed)+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.amount - item.completed)+"</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#AccountingAssetFixedAssetListTable').DataTable( {
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
                  .column( 5 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 5 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+'</span>'
              );


          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
	
	//validate the new asset for now
	
}

