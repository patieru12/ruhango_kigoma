function validateUpdateTransaction(){
    
    var accUpdateTransactionLoading      = $("#accUpdateTransactionLoading");
    var accUpdateTransactionErrorMessage = $("#accUpdateTransactionErrorMessage");
    var accUpdateTransactionForm         = $("#accUpdateTransactionForm");

    accUpdateTransactionLoading.hide();
    accUpdateTransactionErrorMessage.empty();
    accUpdateTransactionForm.slideDown();

    //transactionAmount

//validation
    accUpdateTransactionForm.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
              transactionAmount: {
                    validators: {
                        callback: {
                            message: 'The sum of distributions should be equal to transaction amount',
                            callback: function(value, validator, $field) {

                                var transactionAmount = $("#transactionAmount").val();
                                var total_transaction = 0;

                                $('[name^="income["]').each(function() {
                                    total_transaction += parseFloat($(this).val());
                                });

                                if ( transactionAmount == total_transaction) {
                                    return true;

                                }else{
                                    return false;
                                }

                            }
                        }
                    }
                 }

            }
        })
    .on('success.form.fv', function(e , data) {

        e.preventDefault();
        submitUpdateTransaction();

    });

}


function validateNewReminder(){
    
    //validation
        var formDashAcNewReminder = $('#formDashAcNewReminder');
        formDashAcNewReminder.formValidation({
                framework: 'bootstrap',
                icon: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                excluded: [':disabled', ':hidden', ':not(:visible)'],
                fields: {
                  newReminder_studentType: {
                         validators: {
                             notEmpty: {
                                 message: 'Student is Required'
                             }
                         }
                     },
                  message: {
                         validators: {
                             notEmpty: {
                                 message: 'Message to parents is Required'
                             }
                         }
                     },
                  newReminder_dueDate: {
                         validators: {
                             notEmpty: {
                                 message: 'Due date is Required'
                             }
                         }
                     },
                  'newReminder_Organization[]': {
                         validators: {
                             notEmpty: {
                                 message: 'Organization is Required'
                             }
                         }
                     },
                  'newReminder_SomeClasses[]': {
                         validators: {
                             notEmpty: {
                                 message: 'Class is Required'
                             }
                         }
                     },
                  'newReminder_StudentsSome[]': {
                         validators: {
                             notEmpty: {
                                 message: 'Student is Required'
                             }
                         }
                     }

                }
            })
        .on('success.form.fv', function(e , data) {

            e.preventDefault();
            submitPaNewReminder();

         });

}

function validateNewCurrentFee(){
    
     //validation
    var formDashPaNewCurrentFee = $('#formDashPaNewCurrentFee');
    formDashPaNewCurrentFee.formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
              newTermlyFee_FeeType: {
                     validators: {
                         notEmpty: {
                             message: 'Fee type is Required'
                         }
                     }
                 },
              newTermlyFee_description: {
                     validators: {
                         notEmpty: {
                             message: 'Description is Required'
                         }
                     }
                 },
              newTermlyFee_amount: {
                     validators: {
                         notEmpty: {
                             message: 'Amount is Required'
                         },
                         numeric: {
                                message: 'The amount is not a number',
                                thousandsSeparator: ',',
                                decimalSeparator: '.'
                            }
                     }
                 },
              newTermlyFee_dueDate: {
                     validators: {
                         notEmpty: {
                             message: 'Due date is Required'
                         }
                     }
                 },
              'newTermlyFee_Organization[]': {
                     validators: {
                         notEmpty: {
                             message: 'Organization is Required'
                         }
                     }
                 },
              'newTermlyFee_SomeClasses[]': {
                     validators: {
                         notEmpty: {
                             message: 'Class is Required'
                         }
                     }
                 },
              'newTermlyFee_StudentsSome[]': {
                     validators: {
                         notEmpty: {
                             message: 'Student is Required'
                         }
                     }
                 }

            }
        })
    .on('success.form.fv', function(e , data) {

        e.preventDefault();
        submitPaNewCurrentFee();

     }).find('[name="newTermlyFee_dueDate"]').change(function(e) {
         formDashPaNewCurrentFee.formValidation('revalidateField', 'newTermlyFee_dueDate');
                                                           
     }).end();

}

/* validate adding student to school fees payment form */
function validateNewDebt(){
    
    //$("#termlyInvoiceStudents").select2();
    $("#AccountingSchoolFeesNewDebtStudent").select2({
            closeOnSelect: false
    });

    $("#AccountingSchoolFeesMoreDebt").click(load_Accounting_School_Fees_Debts);
    
    $('#formDashPaNewSchoolDebt').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          debtDescription: {
                 validators: {
                     notEmpty: {
                         message: 'Description is Required'
                     }
                 }
             },
          debtAmount: {
                 validators: {
                     notEmpty: {
                         message: 'Amount is Required'
                     },
                     integer: {
                         message: 'The amount should be number'
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
        submitPaNewDebt();
    });
    //alert('Ends');
}

/* validate adding student to school fees payment form */
function validateAddStudent(FeeID){
    
    $("#termlyInvoiceStudents").select2({
        closeOnSelect: false
    });
    
    $('#formAddStudentToCurrentFee').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          termlyInvoiceStudents: {
                 validators: {
                     notEmpty: {
                         message: 'Select Student'
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
        submitPaAddStudentToFee(FeeID);
    });
}

/* validate adding student to school fees payment form */
function validateAddStudentSponsor(FeeID){
    
    $("#organizationStudentsList").select2();
    
    $('#formAddStudentToOrganization').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          termlyInvoiceStudents: {
                 validators: {
                     notEmpty: {
                         message: 'Select Student'
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
        //console.log('Well Filled Form');
        submitPaAddStudentToSponsor(FeeID);
    });
}

/* validate adding student to school fees payment form */
function validateAddStudentSponsorInfo(FeeID, termId){
    
    $("#organizationStudentsList").select2();
    
    $('#formAddStudentToOrganizationInfo').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          termlyInvoiceStudents: {
                 validators: {
                     notEmpty: {
                         message: 'Select Student'
                     }
                 }
             },
          studentGroupPayment: {
                 validators: {
                     notEmpty: {
                         message: 'Enter Amount'
                     },
                     double:{
                         message: 'Amount is Number'
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
        //console.log('Well Filled Form');
        submitPaAddStudentToSponsorInfo(FeeID, termId);
    });
}

/* validate adding student to school fees payment form */
function validateNewClient(){
    
    //$("#termlyInvoiceStudents").select2();
    $("#AccountingSchoolFeesNewDebtStudent").select2();
    $("#AccountingNewClientList").click(load_Accounting_List_Client);
    
    $('#formDashPaNewClient').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          clientName: {
                 validators: {
                     notEmpty: {
                         message: 'Name is Required'
                     }
                 }
             },
          clientContactFname: {
                 validators: {
                     notEmpty: {
                         message: 'Contact Person is Required'
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
        submitPaNewClient();
    });
    
}

/* validate adding student to school fees payment form */
function validateNewProduct(type){
    
    $("#paNewProductType").select2();
    //$("#termlyInvoiceStudents").select2();
    $("#AccountingSchoolFeesNewDebtStudent").select2();
    $("#AccountingNewProductList").click(load_Accounting_List_Product);
    
    $('#formDashPaNewProduct').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          productName: {
                 validators: {
                     notEmpty: {
                         message: 'Product is Required'
                     }
                 }
             },
          productAmount: {
                 validators: {
                     notEmpty: {
                         message: 'Phone is Required'
                     }, 
                     number: {
                         message: 'Phone Should be a number'
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
        submitPaNewProduct(type);
    });
    //alert('Ends');
}

/* validate adding student to school fees payment form */
function validateAddStudent(FeeID){
    
    $("#termlyInvoiceStudents").select2({
        closeOnSelect: false
    });
    
    $('#formAddStudentToCurrentFee').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          termlyInvoiceStudents: {
                 validators: {
                     notEmpty: {
                         message: 'Select Student'
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
        submitPaAddStudentToFee(FeeID);
    });
}

/* validate the New Item Budget Item Form */
function validateNewBill(){
    
    // //validate the form now
  //     $('#formDashPaNewInvoice').formValidation({
          
  //            framework: 'bootstrap',
  //            excluded: [':disabled'],
  //            icon: {
  //                valid: 'glyphicon glyphicon-ok',
  //                invalid: 'glyphicon glyphicon-remove',
  //                validating: 'glyphicon glyphicon-refresh'
  //            },
  //            fields: {
  //             billAmount: {
  //                    validators: {
  //                        notEmpty: {
  //                            message: 'Bill Amount is required'
  //                        },
  //                        double: {
  //                            message: 'The amount should be number'
  //                        }

  //                    }
  //                }
  //            }

  //          })
  //         .on('success.field.fv', function(e, data) {

  //                if (data.fv.getInvalidFields().length <= 0) {   

  //                    data.fv.disableSubmitButtons(false);
  //                }

  //          })
  //          .on('success.form.fv', function( e ) {

  //              e.preventDefault();
  //              submitPaNewBillInfo();

  //          });

    var formDashPaNewBill = $('#formDashPaNewBill');

    //validate the form now
       formDashPaNewBill.formValidation({
          
             framework: 'bootstrap',
             excluded: [':disabled'],
             icon: {
                 valid: 'glyphicon glyphicon-ok',
                 invalid: 'glyphicon glyphicon-remove',
                 validating: 'glyphicon glyphicon-refresh'
             },
             fields: {
              budgetitem: {
                     validators: {
                         notEmpty: {
                             message: 'Budget Item is Required'
                         }
                     }
              },
              billVendor: {
                     validators: {
                         notEmpty: {
                             message: 'Vendor is Required'
                         }
                     }
              },
              billDate: {
                 validators: {
                     notEmpty: {
                         message: 'Bill date is Required'
                     }
                 }
              },
              paymentDate: {
                 validators: {
                     notEmpty: {
                         message: 'Due date is Required'
                     }
                 }
              },
              billAmount: {
                     validators: {
                         notEmpty: {
                             message: 'Bill Amount is required'
                         },
                        numeric: {
                            message: 'The amount is not a number',
                            thousandsSeparator: ',',
                            decimalSeparator: '.'
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
               submitPaNewBillInfo();

           }).find('[name="billDate"]').change(function(e) {
             formDashPaNewBill.formValidation('revalidateField', 'billDate');
                                                               
           }).end()
           .find('[name="paymentDate"]').change(function(e) {
             formDashPaNewBill.formValidation('revalidateField', 'paymentDate');
                                                               
           }).end();

}

  
  


/* validate the New Item Budget Item Form */
function validateNewInvoice(){
    
    var formDashPaNewInvoice = $('#formDashPaNewInvoice');

    //validate the form now
       formDashPaNewInvoice.formValidation({
          
             framework: 'bootstrap',
             excluded: [':disabled'],
             icon: {
                 valid: 'glyphicon glyphicon-ok',
                 invalid: 'glyphicon glyphicon-remove',
                 validating: 'glyphicon glyphicon-refresh'
             },
             fields: {
              invoiceClient: {
                     validators: {
                         notEmpty: {
                             message: 'Client is Required'
                         }
                     }
              },
              invoiceDate: {
                 validators: {
                     notEmpty: {
                         message: 'Invoice date is Required'
                     }
                 }
              },
              paymentDate: {
                 validators: {
                     notEmpty: {
                         message: 'Due date is Required'
                     }
                 }
              },
              invoiceAmount: {
                     validators: {
                         notEmpty: {
                             message: 'Invoice Amount is required'
                         },
                        numeric: {
                            message: 'The amount is not a number',
                            thousandsSeparator: ',',
                            decimalSeparator: '.'
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
               submitPaNewInvoiceInfo();

           }).find('[name="invoiceDate"]').change(function(e) {
             formDashPaNewInvoice.formValidation('revalidateField', 'invoiceDate');
                                                               
           }).end()
           .find('[name="paymentDate"]').change(function(e) {
             formDashPaNewInvoice.formValidation('revalidateField', 'paymentDate');
                                                               
           }).end();
}

/* validate adding student to school fees payment form */
function validateAddSponsor(){
    
    $("#AccountingNewSponsorList").bind("click", load_Accounting_List_Organization);
    $("#paNewCountry").select2();
    $('#formDashPaNewSponsor').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          sponsorName: {
                 validators: {
                     notEmpty: {
                         message: 'Name is Required'
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
        submitPaNewSponsorInfo();
    });
    //alert('Ends');
}

/* validate New Expense Form */
function validateNewExpenseForm(){
    var PaNewIncomeBankContainer = $("#PaNewIncomeBankContainer");
    PaNewIncomeBankContainer.hide();
    var PaNewIncomeCashContainer = $("#PaNewIncomeCashContainer");
    PaNewIncomeCashContainer.hide();

    var PaNewExpenseCheckContainer = $("#PaNewExpensesCheckContainer");
    PaNewExpenseCheckContainer.hide();

    
    //define setting for for asset information
    var AccountingAssetInfoContainer = $("#AccountingExpenseAssetInfoContainer");
    //hide it by default
    AccountingAssetInfoContainer.hide();
    //define setting for for asset information
    var AccountingGeneralInfoContainer = $("#AccountingExpenseGeneralInfoContainer");
    //hide it by default
    AccountingGeneralInfoContainer.hide();
    //define setting for for asset information
    var AccountingBillInfoContainer = $("#AccountingExpenseBillInfoContainer");
    //hide it by default
    AccountingBillInfoContainer.hide();
    //define setting for for asset information
    var AccountingLoanInfoContainer = $("#AccountingExpenseLoanInfoContainer");
    //hide it by default
    AccountingLoanInfoContainer.hide();
    
    //activate select 2 function for all drop down fields
    var AccountingStaff                = $("#AccountingStaffAccountID");
    var AccountingSchoolBankAccount    = $("#AccountingSchoolBankAccount_A");
    var AccountingBankOperationType    = $("#AccountingBankOperationType_A");
    var AccountingBudgetItem           = $("#AccountingBudgetItem");
    var AccountingExpenseBill          = $("#AccountingExpenseBill");
    var AccountingExpenseVendor        = $("#AccountingExpenseVendor");
    var AccountingExpenseAssetVendorID = $("#AccountingExpenseAssetVendorID");
    var AccountingAssetBudgetItem      = $("#AccountingAssetBudgetItem");
    var AccountingLoanID               = $("#AccountingLoanID");
  
    //activation of selec2
    AccountingStaff.select2();
    AccountingSchoolBankAccount.select2();
    AccountingBankOperationType.select2().bind('change', function(){
        if($(this).val() == 3){
            //console.log("Check Input Found Please Add Required Fields");
            PaNewExpenseCheckContainer.slideDown();
        } else{
            //console.log("Remove Unnecessary Fields");
            PaNewExpenseCheckContainer.slideUp();
        }
    });
    AccountingBudgetItem.select2();
    AccountingExpenseBill.select2().bind('change', function(){

        var billRemainAmount  = AccountingExpenseBill.select2().find(":selected").data("remainamount");
        $("#paNewExpenseAllAmount").val( billRemainAmount );

    });
    AccountingExpenseVendor.select2();
    AccountingExpenseAssetVendorID.select2();
    AccountingAssetBudgetItem.select2()
    AccountingLoanID.select2().bind('change', function(){
        
        // var url = base_url+"accounting/loans/" + $(this).val();
        // var activeinvoice = ""
        // $.getJSON( url, function( data ) {
        //     //get the amount of the invoice only
        //     var amount = $("#paNewExpenseLoanAmountHere").val() * data.interest / 100;
        //     $("#paNewExpenseLoanInterestHere").val( amount );
        //     $("#paNewExpenseLoanInterestRateHere").val( data.interest );
            
        //     var tt = 0;
        //     tt = ($("#paNewExpenseLoanAmountHere").val() * 1) + ($("#paNewExpenseLoanAmountHere").val() * $("#paNewExpenseLoanInterestRateHere").val() / 100) + ($("#paNewExpenseLoanPenalityHere").val() * 1);
        //     $("#paNewExpenseAllAmount").val( tt );
        // });
    });
    
    // $("#paNewExpenseLoanAmountHere, #paNewExpenseLoanInterestHere, #paNewExpenseLoanPenalityHere").keyup(function(){
    //     var tt = 0;
    //     /* Calculate the Interest First */
    //     $("#paNewExpenseLoanInterestHere").val(( $("#paNewExpenseLoanAmountHere").val() * $("#paNewExpenseLoanInterestRateHere").val() / 100 )) ;
    //     tt = ($("#paNewExpenseLoanAmountHere").val() * 1) + ($("#paNewExpenseLoanAmountHere").val() * $("#paNewExpenseLoanInterestRateHere").val() / 100) + ($("#paNewExpenseLoanPenalityHere").val() * 1);
    //     $("#paNewExpenseAllAmount").val( tt );
    // });

    //activate the expense type selection buttons
    $("input[type=radio][name=expenseIsRecorded]").change(function(){
        var currentValue = $(this).val();
        //console.log(currentValue);
        //if the general tab is selected hide any other visible container
        if(currentValue == 0) {
            AccountingLoanInfoContainer.hide();
            AccountingAssetInfoContainer.hide();
            AccountingBillInfoContainer.hide();
            AccountingGeneralInfoContainer.slideDown();
        } else if(currentValue == 1) {
            AccountingLoanInfoContainer.hide();
            AccountingAssetInfoContainer.hide();
            AccountingBillInfoContainer.slideDown();
            AccountingGeneralInfoContainer.hide();
        } else if(currentValue == 2) {
            AccountingLoanInfoContainer.hide();
            AccountingAssetInfoContainer.slideDown();
            AccountingBillInfoContainer.hide();
            AccountingGeneralInfoContainer.hide();
        } else if(currentValue == 3) {
            AccountingLoanInfoContainer.slideDown();
            AccountingAssetInfoContainer.hide();
            AccountingBillInfoContainer.hide();
            AccountingGeneralInfoContainer.hide();
        }
    });
    $('input[type=radio][name=expenseChannel]').change(function() {
        var selected = $(this).val();
        if ( selected == 1 ) {
            PaNewIncomeBankContainer.slideDown();
            PaNewIncomeCashContainer.hide();
        } else if ( selected == 0 ) {
            PaNewIncomeBankContainer.hide();
            PaNewIncomeCashContainer.slideDown();
        }

    });

    
  var PaNewIncomeSchoolFeesContainer          = $("#PaNewIncomeSchoolFeesContainer");
  var PaNewIncomeAccountsReceivablesContainer = $("#PaNewIncomeAccountsReceivablesContainer");
  var PaNewIncomeOtherContainer               = $("#PaNewIncomeOtherContainer");
  
  PaNewIncomeSchoolFeesContainer.hide();
  PaNewIncomeAccountsReceivablesContainer.hide();
  PaNewIncomeOtherContainer.hide();
  
  $('#AccountingExpenseDate').datepicker().datepicker("setDate", new Date());

  AccountingGeneralInfoContainer.slideDown();
    
    //validation
        var formDashPaNewExpenseAll = $('#formDashPaNewExpenseAll');
        formDashPaNewExpenseAll.formValidation({
                framework: 'bootstrap',
                icon: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                excluded: [':disabled', ':hidden', ':not(:visible)'],
                fields: {
                  expenseIsRecorded: {
                         validators: {
                             notEmpty: {
                                 message: 'Expense type is Required'
                             }
                         }
                     },
                  expenseBudgetItemGeneral: {
                         validators: {
                             notEmpty: {
                                 message: 'Budget item is Required'
                             }
                         }
                     },
                  expenseBudgetItemBills: {
                         validators: {
                             notEmpty: {
                                 message: 'Bill is Required'
                             }
                         }
                     },
                  expenseAssetNameData: {
                         validators: {
                             notEmpty: {
                                 message: 'Asset name is Required'
                             }
                         }
                     },
                  expenseAssetNameData: {
                         validators: {
                             notEmpty: {
                                 message: 'Asset name is Required'
                             }
                         }
                     },
                  expenseAssetDepreciationRate: {
                         validators: {
                             notEmpty: {
                                 message: 'Depreciation rate is Required'
                             },
                             numeric: {
                                    message: 'Depreciation rate should be a number',
                                    thousandsSeparator: ',',
                                    decimalSeparator: '.'
                                }
                         }
                     },
                  expenseAssetVendorId: {
                         validators: {
                             notEmpty: {
                                 message: 'Vendor is Required'
                             }
                         }
                     },
                  expenseLoanId: {
                         validators: {
                             notEmpty: {
                                 message: 'Loan is Required'
                             }
                         }
                     },
                  expenseLoanPeriod: {
                         validators: {
                             notEmpty: {
                                 message: 'Loan payment period is Required'
                             }
                         }
                     },
                  expenseLoanInterest: {
                         validators: {
                             notEmpty: {
                                 message: 'Interest on paid amount is Required'
                             },
                             numeric: {
                                    message: 'The amount should be a number',
                                    thousandsSeparator: ',',
                                    decimalSeparator: '.'
                                }
                         }
                     },
                  expenseLoanPenality: {
                         validators: {
                             notEmpty: {
                                 message: 'Penality on paid amount is Required'
                             },
                             numeric: {
                                    message: 'The amount should be a number',
                                    thousandsSeparator: ',',
                                    decimalSeparator: '.'
                                }
                         }
                    },
                  expenseAllAmount: {
                         validators: {
                             notEmpty: {
                                 message: 'Amount is Required'
                             },
                             numeric: {
                                    message: 'The amount is not a number',
                                    thousandsSeparator: ',',
                                    decimalSeparator: '.'
                                }
                         }
                     },
                  expenseCashAccount: {
                         validators: {
                             notEmpty: {
                                 message: 'Cash account is Required'
                             }
                         }
                     },
                  expenseBankAccount: {
                         validators: {
                             notEmpty: {
                                 message: 'Bank account is Required'
                             }
                         }
                     }, 
                  expenseDate: {
                         validators: {
                             notEmpty: {
                                 message: 'Due date is Required'
                             }
                         }
                     }

                }
            })
        .on('success.form.fv', function(e , data) {

            e.preventDefault();
            submitPaNewExpenseInfo();

         }).find('[name="expenseDate"]').change(function(e) {
            formDashPaNewExpenseAll.formValidation('revalidateField', 'expenseDate');
                                                               
         }).end();
    
}

/* validate New Expense Form */
function validateUpdateExpenseForm(tr_exist){
    var PaNewIncomeBankContainer = $("#PaNewIncomeBankContainer");
  PaNewIncomeBankContainer.hide();
  var PaNewIncomeCashContainer = $("#PaNewIncomeCashContainer");
  PaNewIncomeCashContainer.hide();
    
    //define setting for for asset information
    var AccountingAssetInfoContainer = $("#AccountingExpenseAssetInfoContainer");
    //hide it by default
    AccountingAssetInfoContainer.hide();
    //define setting for for asset information
    var AccountingGeneralInfoContainer = $("#AccountingExpenseGeneralInfoContainer");
    //hide it by default
    AccountingGeneralInfoContainer.hide();
    //define setting for for asset information
    var AccountingBillInfoContainer = $("#AccountingExpenseBillInfoContainer");
    //hide it by default
    AccountingBillInfoContainer.hide();
    //define setting for for asset information
    var AccountingLoanInfoContainer = $("#AccountingExpenseLoanInfoContainer");
    //hide it by default
    AccountingLoanInfoContainer.hide();
    //alert(tr_exist.expenseKind);
    //check the currently used expense Kind
    if(tr_exist.expenseKind == 0){
        AccountingGeneralInfoContainer.show();
    } else if(tr_exist.expenseKind == 1){
        AccountingBillInfoContainer.show();
    } else if(tr_exist.expenseKind == 2){
        AccountingAssetInfoContainer.show();
    } else if(tr_exist.expenseKind == 3){
        AccountingLoanInfoContainer.show();
    }
    //activate select 2 function for all drop down fields
    var AccountingStaff = $("#AccountingStaffAccountID");
    var AccountingSchoolBankAccount = $("#AccountingSchoolBankAccount_");
    var AccountingBankOperationType = $("#AccountingBankOperationType_");
    var AccountingBudgetItem = $("#AccountingBudgetItem");
    var AccountingExpenseBillID = $("#AccountingExpenseBillID");
    var AccountingExpenseVendorID = $("#AccountingExpenseVendorID");
    var AccountingExpenseAssetVendorID = $("#AccountingExpenseAssetVendorID");
    var AccountingAssetBudgetItem = $("#AccountingAssetBudgetItem");
    var AccountingLoanID = $("#AccountingLoanID");
    
    //activation of selec2
    AccountingStaff.select2();
    AccountingSchoolBankAccount.select2();
    AccountingBankOperationType.select2();
    AccountingBudgetItem.select2();
    AccountingExpenseVendorID.select2().bind('change', function(){
        var url = base_url+"accounting/bills/" + $(this).val();
        var activeinvoice = ""
        $.getJSON( url, function( data ) {
            //get the amount of the invoice only
            $("#paNewExpenseAllAmount").val( data.amount );
        });
    });
    AccountingExpenseBillID.select2();
    AccountingExpenseAssetVendorID.select2();
    AccountingAssetBudgetItem.select2()
    AccountingLoanID.select2().bind('change', function(){
        // var url = base_url+"accounting/loans/" + $(this).val();
        // var activeinvoice = ""
        // $.getJSON( url, function( data ) {
        //     //get the amount of the invoice only
        //     var amount = $("#paNewExpenseLoanAmountHere").val() * data.interest / 100;
        //     $("#paNewExpenseLoanInterestHere").val( amount );
        //     $("#paNewExpenseLoanInterestRateHere").val( data.interest );
            
        //     var tt = 0;
        //     tt = ($("#paNewExpenseLoanAmountHere").val() * 1) + ($("#paNewExpenseLoanAmountHere").val() * $("#paNewExpenseLoanInterestRateHere").val() / 100) + ($("#paNewExpenseLoanPenalityHere").val() * 1);
        //     $("#paNewExpenseAllAmount").val( tt );
        // });
    });
    
    // $("#paNewExpenseLoanAmountHere, #paNewExpenseLoanInterestHere, #paNewExpenseLoanPenalityHere").keyup(function(){
    //     var tt = 0;
    //     /* Calculate the Interest First */
    //     $("#paNewExpenseLoanInterestHere").val(( $("#paNewExpenseLoanAmountHere").val() * $("#paNewExpenseLoanInterestRateHere").val() / 100 )) ;
    //     tt = ($("#paNewExpenseLoanAmountHere").val() * 1) + ($("#paNewExpenseLoanAmountHere").val() * $("#paNewExpenseLoanInterestRateHere").val() / 100) + ($("#paNewExpenseLoanPenalityHere").val() * 1);
    //     $("#paNewExpenseAllAmount").val( tt );
    // });

    //activate the expense type selection buttons
    $("input[type=radio][name=expenseIsRecorded]").change(function(){
        var currentValue = $(this).val();
        //console.log(currentValue);
        //if the general tab is selected hide any other visible container
        if(currentValue == 0) {
            AccountingLoanInfoContainer.hide();
            AccountingAssetInfoContainer.hide();
            AccountingBillInfoContainer.hide();
            AccountingGeneralInfoContainer.slideDown();
        } else if(currentValue == 1) {
            AccountingLoanInfoContainer.hide();
            AccountingAssetInfoContainer.hide();
            AccountingBillInfoContainer.slideDown();
            AccountingGeneralInfoContainer.hide();
        } else if(currentValue == 2) {
            AccountingLoanInfoContainer.hide();
            AccountingAssetInfoContainer.slideDown();
            AccountingBillInfoContainer.hide();
            AccountingGeneralInfoContainer.hide();
        } else if(currentValue == 3) {
            AccountingLoanInfoContainer.slideDown();
            AccountingAssetInfoContainer.hide();
            AccountingBillInfoContainer.hide();
            AccountingGeneralInfoContainer.hide();
        }
    });
    $('input[type=radio][name=expenseChannel]').change(function() {
        var selected = $(this).val();
        if ( selected == 1 ) {
            PaNewIncomeBankContainer.slideDown();
            PaNewIncomeCashContainer.hide();
        } else if ( selected == 0 ) {
            PaNewIncomeBankContainer.hide();
            PaNewIncomeCashContainer.slideDown();
        }

    });

    if(tr_exist.isBank == 0){
        PaNewIncomeBankContainer.hide();
        PaNewIncomeCashContainer.slideDown();
    } else if(tr_exist.isBank == 1){
        PaNewIncomeBankContainer.slideDown();
        PaNewIncomeCashContainer.hide();
    }
    
  var PaNewIncomeSchoolFeesContainer          = $("#PaNewIncomeSchoolFeesContainer");
  var PaNewIncomeAccountsReceivablesContainer = $("#PaNewIncomeAccountsReceivablesContainer");
  var PaNewIncomeOtherContainer               = $("#PaNewIncomeOtherContainer");
  var PaNewIncomeCheckContainer               = $("#PaNewIncomeCheckContainer");
  
  PaNewIncomeSchoolFeesContainer.hide();
  PaNewIncomeAccountsReceivablesContainer.hide();
  PaNewIncomeOtherContainer.hide();
  PaNewIncomeCheckContainer.hide();
  
  //active the datePicker
  $('#AccountingExpenseDate').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true ,
      locale: {
          format: 'YYYY-MM-DD'
      }
  });
 
    $('#formDashPaUpdateExpenseAll').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          expenseAllAmount: {
                 validators: {
                     notEmpty: {
                         message: 'The Expense Amount'
                     }, 
                     double: {
                         message: 'Amount Should be a number'
                     }
                 }
             },
          expenseChannel: {
                 validators: {
                     notEmpty: {
                         message: 'Please Select a channel'
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
        submitPaUpdateExpenseInfo(tr_exist);
    });
    
}

/* validate adding student to school fees payment form */
function validateAddStudentDebt(StudentID){
    //$("#termlyInvoiceStudents").select2();
    
    $('#formAddNewStudentDebtNow').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          studentDebtDescription: {
                 validators: {
                     notEmpty: {
                        message: 'Description Required'
                     }
                 }
             },
          studentDebtAmount: {
                 validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     double: {
                        message: 'Amount Should be a Number'
                     }
                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        submitPaAddStudentDebtNow(StudentID);
    });
}


/* validate adding student to school fees payment form */
function validateAddStudentPrevious(StudentID){
    $("#paNewStudentPreviousDueDate").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true ,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
    
    $("#newCurrentTermIdData").select2();
    $("#newCurrentFeeTypeData").select2();
    
    $('#formAddNewStudentPreviousNow').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          studentPreviousDescription: {
                 validators: {
                     notEmpty: {
                        message: 'Description Required'
                     }
                 }
             },
          studentPreviousAmount: {
                 validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     double: {
                        message: 'Amount Should be a Number'
                     }
                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        submitPaAddStudentPreviousNow(StudentID);
    });
}


/* validate adding student to school fees payment form */
function validateAddStudentCurrent(StudentID){
    $("#paNewStudentCurrentDueDate").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true ,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
    
    $("#newCurrentTermIdDataCurrent").select2();
    $("#newCurrentFeeTypeDataCurrent").select2();
    
    $('#formAddNewStudentCurrentNow').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          studentCurrentDescription: {
                 validators: {
                     notEmpty: {
                        message: 'Description Required'
                     }
                 }
             },
          studentCurrentAmount: {
                 validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     double: {
                        message: 'Amount Should be a Number'
                     }
                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        submitPaAddStudentCurrentNow(StudentID);
    });
}

function validateNewIncomeForm(){

  $("#paNewIncomeSource").select2().bind('change', getPaNewIncomeSource );
  $("#paNewIncomeStudent").select2().bind('change', getPaNewIncomeStudentSelected );
  
  $("#paNewIncomeAcccountReceivable").select2();
  $("#paNewIncomeClient").select2().bind('change', function(){
            
        var invoiceAmount  = $("#paNewIncomeClient").select2().find(":selected").data("amount");
        $("#paNewIncomeAmount").val( invoiceAmount );
  });

  $("#paNewIncomeBankAccount").select2();
  $("#AccountingStaffAccountID").select2();
  $("#paNewIncomeBankOperationType").select2().bind('change', getPaNewIncomeBankOperationTypeSelected );
  //add the listner for the Accounting Special module fields
  
  $('#paNewIncomeDate').datepicker().datepicker("setDate", new Date());

  var PaNewIncomeBankContainer = $("#PaNewIncomeBankContainer");
  PaNewIncomeBankContainer.show();

  var PaNewIncomeCashContainer = $("#PaNewIncomeCashContainer");
  PaNewIncomeCashContainer.hide();

  $('input[type=radio][name=incomeChannel]').change(function() {
          
      var selected = $(this).val();

      if ( selected == 1 ) {

        PaNewIncomeCashContainer.slideUp();
        PaNewIncomeBankContainer.slideDown();

      } else if ( selected == 0 ){

        PaNewIncomeBankContainer.slideUp();
        PaNewIncomeCashContainer.slideDown();
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

  /* Start validating the form content  */
  
    $('#formDashPaNewIncomeData').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          incomeAmountData: {
                validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     integer: {
                        message: 'Amount Should be a Number'
                     }
                }
             },
            incomeAmountData: {
                validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     integer: {
                        message: 'Amount Should be a Number'
                     }
                }
             },
              
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        submitPaNewIncomeNow();
    });
  
}

function validateDebtUploadForm(){
    /* Validate the Upload Form*/
  
    $('#formUplaodSchoolFeesPayment').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          payment_list: {
                 validators: {
                     notEmpty: {
                        message: 'Select File'
                     },
                     file: {
                        extension: 'xls,xlsx',
                        type: 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        message: 'Only xls and xlsx accepted'
                     }
                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        //console.log("Now Sending The File.");
        submitPaNewSchoolFeesDebtListInfo();
    });
}

function validatePayDebt(debtId){
    //alert("now validating the payment debt !");
    $("#paNewIncomeBankAccount").select2();
    $("#AccountingStaffAccountID").select2();
    $("#paNewIncomeBankOperationType").select2().bind('change', getPaNewIncomeBankOperationTypeSelected );
     //add the listner for the Accounting Special module fields

    $('#paNewIncomeDateDebtP').daterangepicker({
        singleDatePicker: true,
            showDropdowns: true ,
            locale: {
                format: 'YYYY-MM-DD'
            }
    });
    
    var PaNewIncomeBankContainer = $("#PaNewIncomeBankContainer");
  
    PaNewIncomeBankContainer.hide();
    var PaNewIncomeCashContainer = $("#PaNewIncomeCashContainer");
    PaNewIncomeCashContainer.show();
    
    
    $('input[type=radio][name=incomeChannel]').change(function() {
          
        var selected = $(this).val();

        if ( selected == 1 ) {

            PaNewIncomeCashContainer.hide();
            PaNewIncomeBankContainer.slideDown();

        } else if ( selected == 0 ){

            PaNewIncomeBankContainer.hide();
            PaNewIncomeCashContainer.slideDown();
        }

    });
    
    $('#formPayDebtNow').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          incomeAmountData: {
                 validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     double: {
                        message: 'Amount Should be a Number'
                     }
                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        submitPaPayDebtNow(debtId);
    });
}

function validatePayOverDue(invoiceId){
    //alert("now validating the payment debt !");
    $("#paNewIncomeBankAccount").select2();
    $("#AccountingStaffAccountID").select2();
    $("#paNewIncomeBankOperationType").select2().bind('change', getPaNewIncomeBankOperationTypeSelected );
     //add the listner for the Accounting Special module fields

    $('#paNewIncomeDateDebtP').daterangepicker({
        singleDatePicker: true,
            showDropdowns: true ,
            locale: {
                format: 'YYYY-MM-DD'
            }
    });
    
    var PaNewIncomeBankContainer = $("#PaNewIncomeBankContainer");
  
    PaNewIncomeBankContainer.hide();
    var PaNewIncomeCashContainer = $("#PaNewIncomeCashContainer");
    PaNewIncomeCashContainer.show();
    
    
    $('input[type=radio][name=incomeChannel]').change(function() {
          
        var selected = $(this).val();

        if ( selected == 1 ) {

            PaNewIncomeCashContainer.hide();
            PaNewIncomeBankContainer.slideDown();

        } else if ( selected == 0 ){

            PaNewIncomeBankContainer.hide();
            PaNewIncomeCashContainer.slideDown();
        }

    });
    
    $('#formPayOverDueNow').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          incomeAmountData: {
                 validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     double: {
                        message: 'Amount Should be a Number'
                     }
                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        submitPaPayOverDueNow(invoiceId);
    });
}

function validatePayInvoice(invoiceId){
    //alert("now validating the payment debt !");
    $("#paNewIncomeBankAccount").select2();
    $("#AccountingStaffAccountID").select2();
    $("#paNewIncomeBankOperationType").select2().bind('change', getPaNewIncomeBankOperationTypeSelected );
     //add the listner for the Accounting Special module fields

    $('#paNewIncomeDateDebtP').daterangepicker({
        singleDatePicker: true,
            showDropdowns: true ,
            locale: {
                format: 'YYYY-MM-DD'
            }
    });
    
    var PaNewIncomeBankContainer = $("#PaNewIncomeBankContainer");
  
    PaNewIncomeBankContainer.hide();
    var PaNewIncomeCashContainer = $("#PaNewIncomeCashContainer");
    PaNewIncomeCashContainer.show();
    
    
    $('input[type=radio][name=incomeChannel]').change(function() {
          
        var selected = $(this).val();

        if ( selected == 1 ) {

            PaNewIncomeCashContainer.hide();
            PaNewIncomeBankContainer.slideDown();

        } else if ( selected == 0 ){

            PaNewIncomeBankContainer.hide();
            PaNewIncomeCashContainer.slideDown();
        }

    });
    
    $('#formPayInvoiceNow').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          incomeAmountData: {
                 validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     double: {
                        message: 'Amount Should be a Number'
                     }
                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        submitPaPayInvoiceNow(invoiceId);
    });
}

function validatePayBill(billId){
    //alert("now validating the payment debt !");
    $("#paNewIncomeBankAccount").select2();
    $("#AccountingStaffAccountID").select2();
    $("#paNewIncomeBankOperationType").select2().bind('change', getPaNewIncomeBankOperationTypeSelected );
     //add the listner for the Accounting Special module fields

    $('#paNewIncomeDateDebtP').daterangepicker({
        singleDatePicker: true,
            showDropdowns: true ,
            locale: {
                format: 'YYYY-MM-DD'
            }
    });
    
    var PaNewIncomeBankContainer = $("#PaNewIncomeBankContainer");
  
    PaNewIncomeBankContainer.hide();
    var PaNewIncomeCashContainer = $("#PaNewIncomeCashContainer");
    PaNewIncomeCashContainer.show();
    
    
    $('input[type=radio][name=expenseChannel]').change(function() {
          
        var selected = $(this).val();

        if ( selected == 1 ) {

            PaNewIncomeCashContainer.hide();
            PaNewIncomeBankContainer.slideDown();

        } else if ( selected == 0 ){

            PaNewIncomeBankContainer.hide();
            PaNewIncomeCashContainer.slideDown();
        }

    });
    
    $('#formPayInvoiceNow').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          incomeAmountData: {
                 validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     double: {
                        message: 'Amount Should be a Number'
                     }
                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        submitPaPayBillNow(billId);
    });
}

function validateUpdateIncomeForm(tr_exist){
    //console.log(tr_exist);
  $("#paNewIncomeSource").select2().bind('change', getPaUpdateIncomeSource );
  $("#paNewIncomeStudent").select2().bind('change', getPaUpdateIncomeStudentSelected );
  if(tr_exist[0].incomeSource == 1)
    getPaUpdateIncomeStudentSelected(tr_exist);
  
  $("#paNewIncomeAcccountReceivable").select2();
  $("#paNewIncomeClient").select2().bind('change', function(){
        //$("#paNewIncomeAmount").val( );
        var url = base_url+"accounting/invoices/amount_" + $(this).val();
        var activeinvoice = ""
        $.getJSON( url, function( data ) {
            //get the amount of the invoice only
            $("#paNewIncomeAmount").val( data.amount );
        });
  });
  $("#paNewIncomeBankAccount").select2();
  $("#AccountingStaffAccountID").select2();
  $("#paNewIncomeBankOperationType").select2().bind('change', getPaNewIncomeBankOperationTypeSelected );
  //add the listner for the Accounting Special module fields

  
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
          format: 'YYYY-MM-DD'
      }
  });

  var PaNewIncomeBankContainer = $("#PaNewIncomeBankContainer");
  
  PaNewIncomeBankContainer.hide();
  var PaNewIncomeCashContainer = $("#PaNewIncomeCashContainer");
  PaNewIncomeCashContainer.show();

  $('input[type=radio][name=incomeChannel]').change(function() {
          
      var selected = $(this).val();

      if ( selected == 1 ) {

        PaNewIncomeCashContainer.slideUp();
        PaNewIncomeBankContainer.slideDown();

      } else if ( selected == 0 ){

        PaNewIncomeBankContainer.slideUp();
        PaNewIncomeCashContainer.slideDown();
      }

  });

  var PaNewIncomeSchoolFeesContainer          = $("#PaNewIncomeSchoolFeesContainer");
  var PaNewIncomeAccountsReceivablesContainer = $("#PaNewIncomeAccountsReceivablesContainer");
  
  PaNewIncomeSchoolFeesContainer.hide();
  PaNewIncomeAccountsReceivablesContainer.hide();
  
  /* check the income source */
  if(tr_exist[0].incomeSource == 1){
      PaNewIncomeSchoolFeesContainer.slideDown();
  } else if(tr_exist[0].incomeSource == 2){
      PaNewIncomeAccountsReceivablesContainer.slideDown();
  }
  /* check the payment Mode*/
  if(tr_exist[0].isBank == 1){
      PaNewIncomeCashContainer.hide();
        PaNewIncomeBankContainer.slideDown();
  } else if(tr_exist[0].incomeSource == 0){
      PaNewIncomeBankContainer.hide();
        PaNewIncomeCashContainer.slideDown();
  }
  /* Start validating the form content  */
  
    $('#formDashPaUpdateIncomeData').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          incomeAmountData: {
                 validators: {
                     notEmpty: {
                        message: 'Amount Required'
                     }, 
                     double: {
                        message: 'Amount Should be a Number'
                     }
                 }
             }
         }

    }).on('success.field.fv', function(e, data) {

        if (data.fv.getInvalidFields().length <= 0) {
            data.fv.disableSubmitButtons(false);
        }

    }).on('success.form.fv', function( e ) {
        //alert('prevent the form default submit');
        e.preventDefault();
        submitPaUpdateIncomeNow(tr_exist);
    });
}

function switchDateInputs(){
    var selected = $("#reportTypeNumber").select2('val');
    if(selected == 1){
        //here only stay with ending dat only
        // $("#AccountingReportStartDate").attr('desabled', 'true');
    }
}

function validateReportSubmit(){
    $("#reportTypeNumber").select2().bind('change', switchDateInputs);
    // $('#AccountingReportStartDate').datepicker().trigger("change")
    /*{
          singleDatePicker: true,
          showDropdowns: true ,
          locale: {
              format: 'YYYY-MM-DD'
          }
      });*/
    $('#AccountingReportEndDate').datepicker().trigger("change")
    /*$('#AccountingReportEndDate').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true ,
          locale: {
              format: 'YYYY-MM-DD'
          }
      });*/
      
    $('#formDashGetAccountingReport').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          reportType: {
                 validators: {
                     notEmpty: {
                         message: 'Please Select the report'
                     }
                 }
             },
          reportEndingDate: {
                validators: {
                    notEmpty: {
                        message: 'Select the Ending Date'
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
        submitReportGenerator();
    });
}

/* Validate the expense form  */

/* validate the New Item Budget Item Form */
function validateNewExpenseCategory(){
    
    $("#AccountingExpenseCategory").select2();
    
    //validate the form now
    $('#formDashPaNewExpenseCategory').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
           itemcategory: {
                 validators: {
                     notEmpty: {
                         message: 'Expense Type Required!'
                     }
                 }
             },
          categoryname: {
                 validators: {
                     notEmpty: {
                         message: 'Expense category Name required'
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
           submitPaNewExpenseCategory();

       });
}

/* validate the New Item Budget Item Form */
function validateNewBudgetItem(){
    
    $("#AccountingBudgetNewItemCategory").select2();
    $("#AccountingBudgetNewItemAcademicYear").select2();
    
    //activate the date function on our date field
    /*$("#paNewBudgetItemDate").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true ,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });*/
    //validate the form now
    $('#formDashPaNewBudgetItem').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
           itemname: {
                 validators: {
                     notEmpty: {
                         message: 'Item Name Required!'
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
           submitPaNewStudentDebt();

       });
}

/* validate the New Item Budget Item Form */
function validateNewFixedAsset(){
    
    $("#AccountingNewFixedAssetBudgetItem").select2();
    $("#AccountingNewFixedAssetVendor").select2();
    $("#AccountingAssetMoneyAccountId").select2();
    
    //activate the date function on our date field
    $('#AccountingAssetNewFixedValueDate').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true ,
          locale: {
              format: 'YYYY-MM-DD'
          }
    });
    
    //validate the form now
    $('#formDashPaNewFixedAsset').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
           assetName: {
                 validators: {
                     notEmpty: {
                         message: 'Item Name Required!'
                     }
                 }
             },
          assetAmount: {
                 validators: {
                     notEmpty: {
                         message: 'Asset Value is required'
                     },
                     double: {
                         message: 'The Value should be number'
                     }

                 }
             },
          assetDepreciation: {
                 validators: {
                     notEmpty: {
                         message: 'Deprecition Ratio is required'
                     },
                     double: {
                         message: 'The amount should be number'
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
           submitPaNewFixedAsset();
           //alert("Submit Your Data Now");

       });
}

/* validate the New Item Budget Item Form */
function validateNewPrepaidRentAsset(){
    /* hide account by default display bank by default */
    var PaNewIncomeCashContainer =  $("#PaNewExpensePrepaidRentCashContainer");
    var PaNewIncomeBankContainer =  $("#PaNewExpensePrepaidRentBankContainer");
    
    // hide the cash account
    PaNewIncomeCashContainer.hide();
    $("#AccountingPrepaidRentDepreciationDate").select2();
    $("#AccountingNewPrepaidRentBudgetItem").select2();
    $("#AccountingPrepaidRentMoneyAccountId").select2();
    $("#AccountingSchoolBankAccount").select2();
    $("#AccountingBankOperationType").select2();
    $("#PrepaidRentAccountingStaffAccountID").select2();
    // paNewPrepaidRentAmount
    /* try to validate the amount field according to the monthly payments requires */
    $("#paNewFixedPrepaidRentDepreciation").keyup(function(){
        var newValue = $("#paNewPrepaidRentAmount").val() / $("#paNewFixedPrepaidRentDepreciation").val();
        
        // write the answer into an other fields
        $("#paNewPrepaidAvailableMonths").val( parseInt( newValue ) );
    });
    
    /* Check if the expenseChannel is updated */
    $('input[type=radio][name=expenseChannel]').change(function() {
        var selected = $(this).val();
        if ( selected == 1 ) {
            PaNewIncomeBankContainer.slideDown();
            PaNewIncomeCashContainer.hide();
        } else if ( selected == 0 ) {
            PaNewIncomeBankContainer.hide();
            PaNewIncomeCashContainer.slideDown();
        }

    });
    
    //activate the date function on our date field
    $('#AccountingAssetNewPrepaidRentValueDate').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true ,
          locale: {
              format: 'YYYY-MM-DD'
          }
    });
    
    //validate the form now
    $('#formDashPaNewPrepaidRentAsset').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
           prepaidRentAmount: {
                 validators: {
                     notEmpty: {
                         message: 'Amount Required'
                     },
                     double: {
                         message: 'Amount should be a number'
                     }
                 }
             },
          prepaidRentDescription: {
                 validators: {
                     notEmpty: {
                         message: 'Description is required'
                     }

                 }
             },
          prepaidRentDepreciation: {
                 validators: {
                     notEmpty: {
                         message: 'Monthly Value required'
                     },
                     double: {
                         message: 'The amount should be number'
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
           submitPaNewPrepaidRent();
           //alert("Submit Your Data Now");

       });
}

/* validate the New Item Budget Item Form */
function validateNewCashItem(){
    
    $("#paNewCashStaff").select2();
    //$("#AccountingBudgetNewItemAcademicYear").select2();
    
    //activate the date function on our date field
    $("#paNewCashDate").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true ,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
    //validate the form now
    $('#formDashPaNewSchoolCash').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
           staffname: {
                 validators: {
                     notEmpty: {
                         message: 'Staff Name Required!'
                     }
                 }
             },
          startingAmount: {
                 validators: {
                     notEmpty: {
                         message: 'Amount is required'
                     },
                     integer: {
                         message: 'The amount should be number'
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
           submitPaNewSchoolCash();

       });
}

/* validate the New Vendor Item Form */
function validateNewVendor(){
    
    //$("#AccountingNewBankId").select2();
    //$("#AccountingNewBankAccountRole").select2();
    //$("#AccountingBudgetNewItemAcademicYear").select2();
    
    //activate the date function on our date field
    /* $("#paNewCashDate").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true ,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }); */
    //validate the form now
    $("#AccountingNewVendorList").bind("click", load_Accounting_List_Vendor);
    
    //alert($('#formDashPaNewSchoolBank'));
    $('#formDashPaNewVendor').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
           vendorName: {
                 validators: {
                     notEmpty: {
                         message: 'Vendor Name Required!'
                     }
                 }
             },
          vendorPhone: {
                 validators: {
                     notEmpty: {
                         message: 'Phone Number Required'
                     }
                 }
             },
          vendorEmail: {
                 validators: {
                     notEmpty: {
                         message: 'Email Required'
                     },
                    emailAddress: {
                        message: 'Enter a valid email address'
                    }

                 }
             },
          vendorContactPerson: {
                 validators: {
                     notEmpty: {
                         message: 'Contact Person Required'
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
           submitPaNewVendor();
           //console.log("Submit Form Now!");
          // alert("Please Save all data!");

       });
}

/* validate the New Item Budget Item Form */
function validateNewBankItem(){
    
    // $("#AccountingNewBankId").select2();
    // console.log(banksOptions);
    select2GenerateNewOption("AccountingNewBankId", banksOptions);
    $("#AccountingNewBankAccountRole").select2();
    //$("#AccountingBudgetNewItemAcademicYear").select2();
    
    //activate the date function on our date field
    /* $("#paNewCashDate").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true ,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }); */
    //validate the form now
    //alert($('#formDashPaNewSchoolBank'));
    $('#formDashPaNewSchoolBank').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
           accountnumber: {
                 validators: {
                     notEmpty: {
                         message: 'Account Number is required!'
                     }
                 }
             },
          startingAmount: {
                 validators: {
                     notEmpty: {
                         message: 'Amount is required'
                     },
                     integer: {
                         message: 'The amount should be number'
                     }

                 }
             },
          publicDescription: {
                 validators: {
                     notEmpty: {
                         message: 'Public Description Required'
                     }

                 }
             },
          privateDescription: {
                 validators: {
                     notEmpty: {
                         message: 'Private Description Required'
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
           submitPaNewSchoolBank();
          // alert("Please Save all data!");

       });
}

/* validate the New Item Budget Item Form */
function validateNewCashMovement(){
    //alert('Form Validator Triggered!');
    $("#AccountingAssetMoneyTransferFrom").select2();
    $("#AccountingAssetMoneyTransferTo").select2();
    
    $('#AccountingAssetMoneyTransferDate').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true ,
          locale: {
              format: 'YYYY-MM-DD'
          }
    });
    /* 
    $("#AccountingNewBankId").select2();
    $("#AccountingNewBankAccountRole").select2();
    //$("#AccountingBudgetNewItemAcademicYear").select2();
    
    //activate the date function on our date field
    $("#paNewCashDate").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true ,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }); */
    //validate the form now
    //alert($('#formDashPaNewSchoolBank'));
    $('#formDashPaNewMoneyMovement').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
            movedAmount: {
                validators: {
                    notEmpty: {
                        message: 'Amount is required'
                    },
                    integer: {
                        message: 'The amount should be number'
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
           //console.log("Save The Form Content!");
           submitPaNewSchoolMoneyMovement();
          // alert("Please Save all data!");

       });
}

/* validate the New Item Budget Item Form */
function validateUpdateCashMovement(movementId){
    //alert("Activating Button Now");
    $("#AccountingUpdateMoneyTransferFrom").select2();
    $("#AccountingUpdateMoneyTransferTo").select2();
    
    $('#AccountingUpdateMoneyMovementDate').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true ,
          locale: {
              format: 'YYYY-MM-DD'
          }
    });
    /* 
    $("#AccountingNewBankId").select2();
    $("#AccountingNewBankAccountRole").select2();
    //$("#AccountingBudgetNewItemAcademicYear").select2();
    
    //activate the date function on our date field
    $("#paNewCashDate").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true ,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }); */
    //validate the form now
    //alert($('#formDashPaNewSchoolBank'));
    $('#formDashPaUpdateMoneyMovement').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
            movedAmount: {
                validators: {
                    notEmpty: {
                        message: 'Amount is required'
                    },
                    integer: {
                        message: 'The amount should be number'
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
           submitPaUpdateSchoolMoneyMovement(movementId);
          // alert("Please Save all data!");

       });
}


    function validateImportAccountingData()
    {
      var formAccountingImportFileLoading   = $('#formAccountingImportFileLoading');
      var formAccountingImportErrorMessage  = $('#formAccountingImportErrorMessage');
      var formAccountingImportFile          = $('#formAccountingImportFile');

      formAccountingImportFileLoading.hide();
      formAccountingImportErrorMessage.empty();
      formAccountingImportFile.show();
      
        formAccountingImportFile.formValidation({
          
          framework: 'bootstrap',
          excluded: [':disabled' , ':hidden' ],
          icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            AccImportFile: {
                  validators: {
                    notEmpty: {
                            message: 'Please select the file'
                    },
                    file: {
                        extension: 'xls,xlsx',
                        type: 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        maxSize: 2097152,   // 2048 * 1024
                        message: 'Only xls and xlsx accepted'
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
            ImportAccountingDataSubmit();

        });
  }

    function validateAccountingStockReceive()
    {
        console.log("Stock Receive Activated!");
      var formAccountingStockReceiveLoading   = $('#formAccountingStockReceiveLoading');
      var formAccountingStockReceive          = $('#formAccountingStockReceive');

      formAccountingStockReceiveLoading.hide();
      formAccountingStockReceive.show();
        $('#receiveItemDate').datepicker();

        // $("#receiveItemSupplier").select2();
        // $("#receiveItemType").select2();
      
        formAccountingStockReceive.formValidation({
          
            framework: 'bootstrap',
            excluded: [':disabled' , ':hidden' ],
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                receiveItemType: {
                    validators: {
                        notEmpty: {
                            message: 'Please select the item'
                        }
                    }
                },
                receiveItemQuantity: {
                    validators: {
                        notEmpty: {
                            message: 'Received Quantity is required'
                        },
                        numeric: {
                            message: 'The amount is not a number',
                            thousandsSeparator: ',',
                            decimalSeparator: '.'
                        }
                    }
                },
                receiveItemDate: {
                    validators: {
                        notEmpty: {
                            message: 'Action Date is required'
                        }
                    }
                },
                receiveItemSupplier: {
                    validators: {
                        notEmpty: {
                            message: 'Received Quantity is required'
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
            AccountingStockReceiveSubmit();
            //alert("Form Validated!");

        });
    }


    function onItemChange(){
        var selected = $("#distributeItemType").select2().select2().find(":selected").data("quantity")
        $("#distributeAvailableQuantity").html("<h5>Max: " + selected + "</h5>");
    }

    function validateAccountingStockDistribute()
    {
      var formAccountingStockDistributeLoading   = $('#formAccountingStockDistributeLoading');
      var formAccountingStockDistribute          = $('#formAccountingStockDistribute');

      formAccountingStockDistributeLoading.hide();
      formAccountingStockDistribute.show();
        $('#distributeItemDate').datepicker();

        // $("#distributeItemReceiver").select2();
        $("#distributeItemType").select2().bind("change", onItemChange);
      
        formAccountingStockDistribute.formValidation({
          
            framework: 'bootstrap',
            excluded: [':disabled' , ':hidden' ],
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                distributeItemType: {
                    validators: {
                        notEmpty: {
                            message: 'Please select the item'
                        }
                    }
                },
                distributeItemQuantity: {
                    validators: {
                        notEmpty: {
                            message: 'Received Quantity is required'
                        },
                        numeric: {
                            message: 'The amount is not a number',
                            thousandsSeparator: ',',
                            decimalSeparator: '.'
                        }
                    }
                },
                distributeItemDate: {
                    validators: {
                        notEmpty: {
                            message: 'Action Date is required'
                        }
                    }
                },
                distributeItemReceiver: {
                    validators: {
                        notEmpty: {
                            message: 'Received Quantity is required'
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
            AccountingStockDistributeSubmit();
            //ImportAccountingDataSubmit();
            //alert("Form Validated!");

        });
    }
    function validateAccountingStockUpdateReceive(stockID){
        var formAccountingStockReceiveUpdateLoading   = $('#formAccountingStockReceiveUpdateLoading');
        var formAccountingStockUpdateReceive          = $('#formAccountingStockUpdateReceive');

      formAccountingStockReceiveUpdateLoading.hide();
      formAccountingStockUpdateReceive.show();
        $('#receiveItemDateUpdate').datepicker().trigger("change");

        $("#receiveItemSupplierUpdate").select2();
        $("#receiveItemTypeUpdate").select2();

        var TestData = null;
      
        formAccountingStockUpdateReceive.formValidation({
          
            framework: 'bootstrap',
            excluded: [':disabled' , ':hidden' ],
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                receiveItemTypeUpdate: {
                    validators: {
                        notEmpty: {
                            message: 'Please select the item'
                        }
                    }
                },
                receiveItemQuantityUpdate: {
                    validators: {
                        notEmpty: {
                            message: 'Received Quantity is required'
                        },
                        numeric: {
                            message: 'The amount is not a number',
                            thousandsSeparator: ',',
                            decimalSeparator: '.'
                        }
                    }
                },
                receiveItemDateUpdate: {
                    validators: {
                        notEmpty: {
                            message: 'Action Date is required'
                        }
                    }
                },
                receiveItemSupplierUpdate: {
                    validators: {
                        notEmpty: {
                            message: 'Received Quantity is required'
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
            
            AccountingStockUpdateReceiveSubmit(stockID);
            //ImportAccountingDataSubmit();
            //alert("Form Validated!");

        });
    }
    function validateAccountingStockUpdateDistribute(stockID)
    {
      var formAccountingStockDistributeUpdateLoading   = $('#formAccountingStockDistributeUpdateLoading');
      var formAccountingStockUpdateDistribute          = $('#formAccountingStockUpdateDistribute');

      formAccountingStockDistributeUpdateLoading.hide();
      formAccountingStockUpdateDistribute.show();
        $('#distributeItemDateUpdate').datepicker().trigger("change");

        $("#distributeItemReceiverUpdate").select2();
        $("#distributeItemTypeUpdate").select2();

        var TestData = null;
      
        formAccountingStockUpdateDistribute.formValidation({
          
            framework: 'bootstrap',
            excluded: [':disabled' , ':hidden' ],
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                distributeItemTypeUpdate: {
                    validators: {
                        notEmpty: {
                            message: 'Please select the item'
                        }
                    }
                },
                distributeItemQuantityUpdate: {
                    validators: {
                        notEmpty: {
                            message: 'Received Quantity is required'
                        },
                        numeric: {
                            message: 'The amount is not a number',
                            thousandsSeparator: ',',
                            decimalSeparator: '.'
                        }
                    }
                },
                distributeItemDateUpdate: {
                    validators: {
                        notEmpty: {
                            message: 'Action Date is required'
                        }
                    }
                },
                distributeItemReceiverUpdate: {
                    validators: {
                        notEmpty: {
                            message: 'Received Quantity is required'
                        }
                    }
                }
            }
        })
        .on('success.field.fv', function(e, data) {
            TestData = data.fv;
            if (data.fv.getInvalidFields().length <= 0) {   
                data.fv.disableSubmitButtons(false);
            }

        })
        .on('success.form.fv', function( e ) {
            e.preventDefault();
            if(TestData != null){
                TestData.disableSubmitButtons(false);
            }
            AccountingStockUpdateDistributeSubmit(stockID);
            //ImportAccountingDataSubmit();
            //alert("Form Validated!");

        });
    }

function validateStockRecordReport(){
    $("#accountingStockRecordItem").select2();
    $('#accountingStockRecordMonth').datepicker();
      
    $('#formDashGetAccountingStockReport').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          accStockOperationType: {
                 validators: {
                     notEmpty: {
                         message: 'Please Select on operation'
                     }
                 }
             }, 
          accountingStockRecordMonth: {
                validators: {
                    notEmpty: {
                        message: 'Select the desired month'
                    }

                }
            }, 
          accountingStockRecordItem: {
                validators: {
                    notEmpty: {
                        message: 'Select Item Type you want to view records'
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
        submitSchoolStockRecordGenerator();
    });
}
