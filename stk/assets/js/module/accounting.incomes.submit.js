/* submit the budget item form */
function submitUpdateTransaction(){

    var accUpdateTransactionLoading      = $("#accUpdateTransactionLoading");
    var accUpdateTransactionErrorMessage = $("#accUpdateTransactionErrorMessage");
    var accUpdateTransactionForm         = $("#accUpdateTransactionForm");

    accUpdateTransactionForm.hide();
    accUpdateTransactionErrorMessage.empty();
    accUpdateTransactionLoading.slideDown();

    var transactionAmount = $("#transactionAmount").val();
    var Source            = $("#transactionType").val();
    var TransactionId     = $("#transactionId").val();

     $.ajax({
       url: base_url+"accounting/transactions/"+TransactionId ,
       dataType: 'json',
       type: 'PUT',
       data: accUpdateTransactionForm.serialize(),
       success: function ( data ) {
         if ( data.success )
         {

             $.gritter.add({
                 title: 'Success',
                 text: 'Transaction successfuly updated',
                 class_name: 'success gritter-center',
                 time: ''
             });

             $("#accUpdateTransactionForm").formValidation('revalidateField', 'transactionAmount');
             $('#Model_UpdateTransaction').modal('toggle');

             var transactionRegNumber = $("#transactionRegNumber").val();
             var transactionVia       = $("#transactionVia").val();
             
             if ( transactionVia == 1 ) {
                load_Accounting_Income_SchoolFees();

             }else if( transactionVia == 2 ){
                 $("#accSelectStudent").select2().select2('val',transactionRegNumber).trigger('change');

             }else if( transactionVia == 3 ){

             }else{
               
             }

         }else{

           $.gritter.add({
             title: 'Failed',
             text: "Failed to update, Please see the error details!" ,
             class_name: 'danger gritter-center',
             time: ''
           });

           $("#accUpdateTransactionErrorMessage").html("<span class='col-sm-12 alert alert-danger text-center' ><b>Error Details:</b>"+data.error_msg+"</span>"); 
           currentTd.html('<a href="#">Try again<i class="fa fa-pencil"></i></a>');

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
           title: 'Failed',
           text: 'Failed to update transaction, check your internet and try again',
           class_name: 'danger gritter-center',
           time: ''
         });
         currentTd.html('<a href="#">Try again<i class="fa fa-pencil"></i></a>');

     })
     .always(function() {

        accUpdateTransactionLoading.hide();
        accUpdateTransactionForm.show();
    });

}


/* submit the budget item form */
function submitPaNewFeeType(){

     var PaNewSchoolFeeTypeLoading = $("#PaNewSchoolFeeTypeLoading");
     var formDashPaNewSchoolFeeType= $("#formDashPaNewSchoolFeeType");

     formDashPaNewSchoolFeeType.hide();
     PaNewSchoolFeeTypeLoading.slideDown();

     $.ajax({
       url: base_url+"accounting/schoolfees/feetype",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewSchoolFeeType.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

           $.gritter.add({
               title: 'Success',
               text: 'School fee type successfuly added',
               class_name: 'success gritter-center',
               time: ''
           });

         //load the bill list now
            load_Accounting_School_Fees_Fee_Type();
              
         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });

         }

       }

     }).done(function() {

     }).fail(function() {

        $.gritter.add({
           title: 'Failed',
           text: 'Failed to add a Bill',
           class_name: 'danger gritter-center',
           time: ''
        });

     })
     .always(function() {
        
        PaNewSchoolFeeTypeLoading.hide();
        formDashPaNewSchoolFeeType.slideDown();

    });

}

function AccDeleteFeeType( currentFeeId, td )
{
    td.html("Deleting...");

    $.ajax({
     url: base_url+"accounting/schoolfees/feetype/"+currentFeeId,
     dataType: 'json',
     type: 'DELETE',
     success: function ( res ) {
       if ( res.success )
       {

           $.gritter.add({
               title: 'Success',
               text: 'Fee type successfuly removed',
               class_name: 'success gritter-center',
               time: ''
           });
            
           load_Accounting_School_Fees_Fee_Type();
           
       }else{  

         $.gritter.add({
             title: 'Failed',
             text: 'Failed to remove fee : '+res.error_msg,
             class_name: 'danger gritter-center',
             time: ''
         });

         td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

       }

     }

   }).done(function() {

   }).fail(function() {

      $.gritter.add({
         title: 'Failed',
         text: 'Failed to remove fee, try again later',
         class_name: 'danger gritter-center',
         time: ''
      });

      td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

   })
   .always(function() {
  
  });

}

/* submit the budget item form */
function submitPaNewReminder(){

     var dashClientContainer    = $("#pa_AccountingSchoolFeesContainer");

     var formDashAcNewReminderLoading  = $("#formDashAcNewReminderLoading");
     var formDashAcNewReminder         = $("#formDashAcNewReminder");

     formDashAcNewReminder.hide();
     formDashAcNewReminderLoading.slideDown();

     $.ajax({
       url: base_url+"accounting/schoolfees/paymentReminder",
       dataType: 'json',
       type: 'POST',
       data: formDashAcNewReminder.serialize(),
       success: function ( data ) {
         if ( data.success )
         {

           $.gritter.add({
               title: 'Success',
               text: 'Reminders successfuly sent',
               class_name: 'success gritter-center',
               time: ''
           });

            var content  = '<div class="row text-center" id="message_subject"><h4><b>'+data.Messages_number+'</b> Reminder(s)</h4></div>';

            var messages = data.Messages;
            $.each(messages, function(i, item) {

              content += '<div class="chat-box-timeline"><img class="img-responsive img-circle avatar" src="personimage/50/'+item.personID+'" /><div class="message"><div class="panel panel-shadow panel-white"><div class="panel-body panel-arrow-left"><div class="chat-box-timeline-title"><strong>'+item.names+'</strong><div class="pull-right text-semi"><i class="fa fa-clock-o"></i> '+moment.unix(item.time).fromNow()+'</div><center><span>'+item.subject+'</span></center></div><div class="chat-box-timeline-content"><blockquote>'+item.content+'</blockquote><span class="pull-right">'+getMessageStatusSpan( item.messageStatus )+'</span></div></div></div></div></div>'
              
            });

            formDashAcNewReminder.html(content);

         }else{

           $.gritter.add({
             title: 'Failed',
             text: 'Failed to send Reminders, check your internet and try again',
             class_name: 'danger gritter-center',
             time: ''
           });
           
            formDashAcNewReminderLoading.hide();
            formDashAcNewReminder.slideDown();

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
           title: 'Failed',
           text: 'Failed to send Reminders, check your internet and try again',
           class_name: 'danger gritter-center',
           time: ''
         });

     })
     .always(function() {

        formDashAcNewReminderLoading.hide();
        formDashAcNewReminder.slideDown();
    });

}

/* submit the budget item form */
function submitPaNewCurrentFee(){

    var PaNewCurrentFeeLoading    = $("#PaNewCurrentFeeLoading");
    var formDashPaNewCurrentFee   = $("#formDashPaNewCurrentFee");

    formDashPaNewCurrentFee.hide();
    PaNewCurrentFeeLoading.slideDown();

     $.ajax({
       url: base_url+"accounting/schoolfees/currentfee",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewCurrentFee.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: 'Current fee successfuly added',
                   class_name: 'success gritter-center',
                   time: ''
               });

            //load the bill list now
              load_Accounting_School_Fees_Current_Fees();
              
         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error_msg,
             class_name: 'danger gritter-center',
             time: ''
           });
           
         }

       }

     }).done(function() {


     }).fail(function() {

       $.gritter.add({
         title: 'Failed',
         text: 'Failed to add a Bill',
         class_name: 'danger gritter-center',
         time: ''
       });

     })
     .always(function() {
        
        PaNewCurrentFeeLoading.hide();
        formDashPaNewCurrentFee.slideDown();
    });

}

/* submit the budget item form */
function submitPaAddStudentToFee(FeeID){

    console.log("FeeID");
    console.log(FeeID);

    $('#Model_DashPaFeeStudentList').modal('toggle');
     
     var formDashPaNewDebt  = $("#formAddStudentToCurrentFee");

     $.ajax({
       url: base_url+"accounting/schoolfees/currentfee/" + FeeID +"?type=1",
       dataType: 'json',
       type: 'PUT',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

           $.gritter.add({
               title: 'Success',
               text: res.success,
               class_name: 'success gritter-center',
               time: ''
           });

         //first the modal
         
         } else {

         $.gritter.add({
           title: 'Failed',
           text: res.error,
           class_name: 'danger gritter-center',
           time: ''
         });

       //$('#Model_DashPaFeeStudentList').modal('show');
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
         title: 'Failed',
         text: 'Failed to add a student to termly fee',
         class_name: 'danger gritter-center',
         time: ''
       });

     //$('#Model_DashPaFeeStudentList').modal('show');
     })
     .always(function() {
    
    });

}

// /* submit the budget item form */
// function submitPaAddStudentToSponsor(FeeID){
//   $('#Model_DashPaSponsorStudentList').modal('toggle');
//      var formDashPaNewDebt  = $("#formAddStudentToOrganization");

//      $.ajax({
//        url: base_url+"accounting/sponsors/" + FeeID,
//        dataType: 'json',
//        type: 'POST',
//        data: formDashPaNewDebt.serialize(),
//        success: function ( res ) {
//          if ( res.success )
//          {

//                $.gritter.add({
//                    title: 'Success',
//                    text: res.success,
//                    class_name: 'success gritter-center',
//                    time: ''
//                });
//          //first the modal
         
//          } else {

//        $.gritter.add({
//          title: 'Failed',
//          text: res.error,
//          class_name: 'danger gritter-center',
//          time: ''
//        });
//        //$('#Model_DashPaFeeStudentList').modal('show');
//          }

//        }

//      }).done(function() {


//      }).fail(function() {

//         $.gritter.add({
//        title: 'Failed',
//        text: 'Failed to add a student to organization sponsor',
//        class_name: 'danger gritter-center',
//        time: ''
//      });
//      //$('#Model_DashPaFeeStudentList').modal('show');
//      })
//      .always(function() {
    
//     });

// }

//  submit the budget item form 
// function submitPaAddStudentToSponsorInfo(FeeID, termId){
//   $('#Model_DashPaSponsorStudentList').modal('toggle');
//      var formDashPaNewDebt  = $("#formAddStudentToOrganizationInfo");

//      $.ajax({
//        url: base_url+"accounting/sponsors/" + FeeID + "_:" + termId,
//        dataType: 'json',
//        type: 'POST',
//        data: formDashPaNewDebt.serialize(),
//        success: function ( res ) {
//          if ( res.success )
//          {

//                $.gritter.add({
//                    title: 'Success',
//                    text: res.success,
//                    class_name: 'success gritter-center',
//                    time: ''
//                });
//          //first the modal
         
//          } else {

//        $.gritter.add({
//          title: 'Failed',
//          text: res.error,
//          class_name: 'danger gritter-center',
//          time: ''
//        });
//        //$('#Model_DashPaFeeStudentList').modal('show');
//          }

//        }

//      }).done(function() {


//      }).fail(function() {

//         $.gritter.add({
//        title: 'Failed',
//        text: 'Unable to add student payment',
//        class_name: 'danger gritter-center',
//        time: ''
//      });
//      //$('#Model_DashPaFeeStudentList').modal('show');
//      })
//      .always(function() {
    
//     });

// }


/* submit the budget item form */
function submitPaNewDebt(){

  //$('#Model_DashPaFeeStudentList').modal('toggle');
     var formDashPaNewSchoolDebtLoading = $("#formDashPaNewSchoolDebtLoading");
     var formDashPaNewDebt              = $("#formDashPaNewSchoolDebt");

     formDashPaNewDebt.hide();
     formDashPaNewSchoolDebtLoading.slideDown();

     $.ajax({
       url: base_url+"accounting/schoolfees/debt",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

           $.gritter.add({
               title: 'Success',
               text: 'Successfully added debts',
               class_name: 'success gritter-center',
               time: ''
           });

         //first the modal
          load_Accounting_School_Fees_Debts();

         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
         title: 'Failed',
         text: 'Failed to add a debt',
         class_name: 'danger gritter-center',
         time: ''
       });

     })
     .always(function() {
      
      formDashPaNewSchoolDebtLoading.hide();
      formDashPaNewDebt.slideDown();
     
    });

}

/* submit the budget item form */
function submitPaNewClient(){

      var PaNewClientLoading  = $("#PaNewClientLoading");
      var formDashPaNewClient = $("#formDashPaNewClient");

      formDashPaNewClient.hide();
      PaNewClientLoading.slideDown();

     $.ajax({
       url: base_url+"accounting/clients",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewClient.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

           $.gritter.add({
               title: 'Success',
               text: 'Successfully added the client',
               class_name: 'success gritter-center',
               time: ''
           });
         
          load_Accounting_List_Client();

         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });
       
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
         title: 'Failed',
         text: 'Failed to add a client',
         class_name: 'danger gritter-center',
         time: ''
       });
   
     })
     .always(function() {
        
        PaNewClientLoading.hide();
        formDashPaNewClient.slideDown();
      
    });

}

/* submit the budget item form */
function submitPaNewProduct(type){

     var PaNewProductLoading  = $("#PaNewProductLoading")
     var formDashPaNewProduct = $("#formDashPaNewProduct");

     formDashPaNewProduct.hide();
     PaNewProductLoading.slideDown();

     $.ajax({
       url: base_url+"accounting/products",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewProduct.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: 'Successfully added the product',
                   class_name: 'success gritter-center',
                   time: ''
               });

         //first the modal
         load_Accounting_List_Product(type);
         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });
           
             //$('#Model_DashPaFeeStudentList').modal('show');
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
         title: 'Failed',
         text: 'Failed to add a product',
         class_name: 'danger gritter-center',
         time: ''
       });

     })
     .always(function() {
      
      PaNewProductLoading.hide();
      formDashPaNewProduct.slideDown();

    });

}

function AccCurrentFeeRemoveStudentClicked( FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate ){

  $( "table#pa_AccountingStudentListTable" ).delegate( "td.deleteStudentFromTermlyInvoice", "click", function(e) {

      e.preventDefault();
      e.stopPropagation();

      var id    = $(this).closest('tr').attr('id');
      var feeID = $(this).closest('tr').attr('feeID');

      DeleteStudentFromTermlyInvoice(id, $(this), FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate );

  });

}


function DeleteStudentFromTermlyInvoice(invoiceID, td, FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate ){

  var td_content = td;
  td.html("Deleting...");

  $.ajax({
       url: base_url+"accounting/schoolfees/currentfee/" + invoiceID+"?Type=Student",
       dataType: 'json',
       type: 'DELETE',
       success: function ( res ) {
         if ( res.success )
         {

             $.gritter.add({
                 title: 'Success',
                 text: res.success,
                 class_name: 'success gritter-center',
                 time: ''
             });

              LoadCurrentDetailsInformation(FeeId, FeeName, FeeDescription, FeeAmount, FeeDueDate );
              
         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });

           td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
           title: 'Failed',
           text: 'Failed to remove a student to termly fee',
           class_name: 'danger gritter-center',
           time: ''
        });

        td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

     })
     .always(function() {
    
    });
}

function AccDeleteCurrentFee( td, currentFeeId )
{
    td.html("Deleting...");

    $.ajax({
     url: base_url+"accounting/schoolfees/currentfee/"+currentFeeId+"?Type=Fee",
     dataType: 'json',
     type: 'DELETE',
     success: function ( res ) {
       if ( res.success )
       {

           $.gritter.add({
               title: 'Success',
               text: 'Fee successfuly removed',
               class_name: 'success gritter-center',
               time: ''
           });
            
           load_Accounting_School_Fees_Current_Fees();
           
       }else{  

         $.gritter.add({
             title: 'Failed',
             text: 'Failed to remove fee : '+res.error_msg,
             class_name: 'danger gritter-center',
             time: ''
         });

         td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

       }

     }

   }).done(function() {


   }).fail(function() {

      $.gritter.add({
         title: 'Failed',
         text: 'Failed to remove fee, try again later',
         class_name: 'danger gritter-center',
         time: ''
      });

      td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

   })
   .always(function() {
  
  });

}

function AccDeleteStudentDebt( td, debtId )
{
    td.html("Deleting...");

    $.ajax({
     url: base_url+"accounting/schoolfees/debt/"+debtId,
     dataType: 'json',
     type: 'DELETE',
     success: function ( res ) {
       if ( res.success )
       {

           $.gritter.add({
               title: 'Success',
               text: 'Debt successfuly removed',
               class_name: 'success gritter-center',
               time: ''
           });
            
           load_Accounting_School_Fees_Debts();
           
       }else{  

         $.gritter.add({
             title: 'Failed',
             text: 'Failed to remove Debt : '+res.error_msg,
             class_name: 'danger gritter-center',
             time: ''
         });

         td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

       }

     }

   }).done(function() {


   }).fail(function() {

      $.gritter.add({
         title: 'Failed',
         text: 'Failed to remove Debts, try again later',
         class_name: 'danger gritter-center',
         time: ''
      });

      td.html('<a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a>');

   })
   .always(function() {
  
  });

}

function DeleteStudentFromOrganizationSponsor(invoiceID, FeeID){
  $.ajax({
       url: base_url+"accounting/sponsors/dl_:" + invoiceID,
       dataType: 'json',
       type: 'POST',
       data: '_method=DELETE',
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //first the modal
         
         //load the bill list now
               DetailsTableDataForSponsorship(FeeID);
         
              
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
       //$('#Model_DashPaFeeStudentList').modal('show');
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'Failed to remove a student to termly fee',
       class_name: 'danger gritter-center',
       time: ''
     });
     //$('#Model_DashPaFeeStudentList').modal('show');
     })
     .always(function() {
    
    });
}


/* submit the budget item form */
function submitPaNewInvoiceInfo(){

     var PaNewInvoiceLoading     = $("#PaNewInvoiceLoading");
     var formDashPaNewInvoice    = $("#formDashPaNewInvoice");

     formDashPaNewInvoice.hide();
     PaNewInvoiceLoading.slideDown();

     $.ajax({
       url: base_url+"accounting/invoices",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewInvoice.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

              $.gritter.add({
                   title: 'Success',
                   text: 'Successfully added the invoice',
                   class_name: 'success gritter-center',
                   time: ''
              });
          
          //load the bill list now
              load_Accounting_List_Invoice();
              
         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
         title: 'Failed',
         text: 'Failed to add a invoice',
         class_name: 'danger gritter-center',
         time: ''
       });
     })
     .always(function() {
        
        PaNewInvoiceLoading.hide();
        formDashPaNewInvoice.slideDown();

    });

}

function submitPaNewSponsorInfo(){
  
     var formDashPaNewDebt  = $("#formDashPaNewSponsor");
     
     $.ajax({
       url: base_url+"accounting/sponsors",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

           $.gritter.add({
               title: 'Success',
               text: 'Expense successfuly added!',
               class_name: 'success gritter-center',
               time: ''
           });

            //load the bill list now
              load_Accounting_List_Organization();
              
         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
           title: 'Failed',
           text: 'Failed to add a sponsor',
           class_name: 'danger gritter-center',
           time: ''
        });
        
     })
     .always(function() {
    
    });

}

function submitPaNewExpenseInfo(){

     var formDashPaNewExpenseAll  = $("#formDashPaNewExpenseAll");

     $.ajax({
       url: base_url+"accounting/expenses",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewExpenseAll.serialize(),
       success: function ( res ) {

          console.log(res);

         if ( res.success )
         {
               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the bill list now
               load_Accounting_Expense_List();

         } else {
           $.gritter.add({
             title: 'Failed to add expense,'+res.error_msg ,
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
           title: 'Failed',
           text: 'Failed to add expense, check your internet and try again',
           class_name: 'danger gritter-center',
           time: ''
         });

     })
     .always(function() {

    });

}

function submitPaNewExpenseListInfo(){
     var formDashPaNewDebt  = $("#formUplaofMonthlyExpense");
     $.ajax({
       url: base_url+"accounting/imports",
       dataType: 'json',
       type: 'POST',
       data: new FormData( formDashPaNewDebt[0] ),
     processData: false,
     contentType: false,
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the bill list now
               load_Accounting_Expense_List();
         } else {
       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'System Fails to Capture Uploaded Data',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}

function submitPaNewSchoolFeesListInfo(){
     var formDashPaNewDebt  = $("#formUplaodSchoolFeesPayment");
     $.ajax({
       url: base_url+"accounting/imports_data/schoolfees",
       dataType: 'json',
       type: 'POST',
       data: new FormData( formDashPaNewDebt[0] ),
     processData: false,
     contentType: false,
       success: function ( res ) {
      if ( res.success ){
        $.gritter.add({
          title: 'Success',
          text: res.success,
          class_name: 'success gritter-center',
          time: ''
        });
           //load the bill list now
           load_Accounting_Expense_List();
      } else {
        if(res.data){
          /* Here try to send the Ajax Request For Downloading this in Excel */
          //console.log(res.data.data);
          // Here Iterate in all fields and print
          var html_data = '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" >';
          html_data +="<thead><tr >";
          $.each(res.data.data[0], function(i, item){
            html_data +="<th>" + i + "</th>";
          });
          html_data +="</tr></thead><tbody>"; 
          var count_row = 1;
          $.each(res.data.data, function(i, item){
            html_data +="<tr id='" + (count_row++) + "'>";
            //console.log(item);
            $.each(item, function(l, ha){
              html_data +="<td class='text-left'>"+ (ha == null?"":ha) + " </td>"; 
              
            });
            html_data +="</tr>";
          });
          
          
          html_data += "</tbody></table>";
          
          $("#ModalDataLoadingNowErrors").html(html_data);
          
          $('#pa_AccountingClientListTable').DataTable( {
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
          });
          
          $('#Model_DashPaErrorLogs').modal({ show: true});
        }
         $.gritter.add({
           title: 'Failed',
           text: res.error,
           class_name: 'danger gritter-center',
           time: ''
         });
      }

    }

     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'System Fails to Capture Uploaded Data',
       class_name: 'danger gritter-center',
       time: ''
     });
     });
}

function submitPaAddStudentDebtNow(StudentID){
  
  /* Need to hide Modal */
    
    console.log("submitPaAddStudentDebtNow");

     var formDashPaNewDebt  = $("#formAddNewStudentDebtNow");

     $.ajax({
       url: base_url+"accounting/schoolfees/debt",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               // $.gritter.add({
               //     title: 'Success',
               //     text: res.success,
               //     class_name: 'success gritter-center',
               //     time: ''
               // });

         //load the bill list now
             $('#Model_DashPaFeeStudentList').modal("toggle");

         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
           title: 'Failed',
           text: 'Failed to add new debt',
           class_name: 'danger gritter-center',
           time: ''
         });
     })
     .always(function() {
    
    });

}

function submitPaAddStudentPreviousNow(StudentID){
  
  /* Need to hide Modal */
  
     var formDashPaNewDebt  = $("#formAddNewStudentPreviousNow");

     $.ajax({
       url: base_url+"accounting/schoolfees/overdue/" + StudentID,
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the bill list now
               $('#Model_DashPaFeeStudentList').modal("toggle");
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'Failed to add new delayed payment',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}

function submitPaAddStudentCurrentNow(StudentID){
  
  /* Need to hide Modal */
  
     var formDashPaNewDebt  = $("#formAddNewStudentCurrentNow");

     $.ajax({
       url: base_url+"accounting/schoolfees/overdue/" + StudentID,
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the bill list now
               $('#Model_DashPaFeeStudentCurrent').modal("toggle");
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'Failed to add new payment',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}

function getExcelData(reportType, startDate, endDate){
  $.ajax({
    url: base_url+"accounting/reports/" + reportType,
    type: 'GET',
    data: 'print=excel&startDate=' + startDate + "&endDate=" + endDate,
    success: function ( res ) {
      console.log("Printed!");
    }
    }).done(function() {
    
    }).fail(function() {

        $.gritter.add({
       title: 'Report Loading Fails',
       text: 'Fail to load the the selected report Please Try Again!',
       class_name: 'danger gritter-center',
       time: ''
     });
    }).always(function() {
    
    });
}

function submitReportGenerator(){
  
  /* Need to hide Modal */
  
     var formDashPaNewDebt  = $("#formDashGetAccountingReport");

     $.ajax({
       url: base_url+"accounting/reports",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the all recorded incomes now
         //load_Accounting_Income_List();
         /* Load the generated report now and format according to the selected report */
         if($("#reportTypeNumber").val() == 1){
           /* Load the returned as balance sheet now */
           $("#AccountingReportPartID").slideUp();
           $("#AccountingReportPartID").html(LoadBalanceSheetReport(res.data));
           $("#AccountingReportPartID").slideDown();
         } else if($("#reportTypeNumber").val() == 2){
           /* Load the returned as balance sheet now */
           $("#AccountingReportPartID").slideUp();
           $("#AccountingReportPartID").html(LoadCashFlowReport(res.data));
           $("#AccountingReportPartID").slideDown();
         } else if($("#reportTypeNumber").val() == 3){
           /* Load the returned as balance sheet now */
           $("#AccountingReportPartID").slideUp();
           $("#AccountingReportPartID").html(LoadIncomeStatementReport(res.data));
           $("#AccountingReportPartID").slideDown();
         } else if($("#reportTypeNumber").val() == 4){
           console.log("Here The report is type 4 not Income Statement!");
           /* Load the returned as balance sheet now */
           /*$("#AccountingReportPartID").slideUp();
           $("#AccountingReportPartID").html(LoadIncomeStatementReport(res.data));
           $("#AccountingReportPartID").slideDown();*/
         } else if($("#reportTypeNumber").val() == 5){
           /* Load the returned as balance sheet now */
           /*$("#AccountingReportPartID").slideUp();
           $("#AccountingReportPartID").html(LoadIncomeStatementReport(res.data));
           $("#AccountingReportPartID").slideDown();*/
         } else if($("#reportTypeNumber").val() == 6){
           /* Load the returned as balance sheet now */
           $("#AccountingReportPartID").slideUp();
           $("#AccountingReportPartID").html(LoadAccountReceivablesReport(res.data));
           $("#AccountingReportPartID").slideDown();
         } else if($("#reportTypeNumber").val() == 7){
           /* Load the returned as balance sheet now */
           $("#AccountingReportPartID").slideUp();
           $("#AccountingReportPartID").html(LoadTransactionJournalReport(res.data));
           $("#AccountingReportPartID").slideDown();
         }
               //$('#Model_DashPaFeeStudentCurrent').modal("toggle");
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Report Loading Fails',
       text: 'Fail to load the the selected report Please Try Again!',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}

function submitPaNewIncomeNow(){
  
  /* Need to hide Modal */
    
    var UplaodSchoolFeesPaymentLoding = $("#UplaodSchoolFeesPaymentLoding");
    var formDashPaNewIncomeData       = $("#formDashPaNewIncomeData");

    formDashPaNewIncomeData.hide();
    UplaodSchoolFeesPaymentLoding.slideDown();

     $.ajax({
       url: base_url+"accounting/incomes",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewIncomeData.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

           $.gritter.add({
               title: 'Success',
               text: 'Income successfuly added',
               class_name: 'success gritter-center',
               time: ''
           });

           dashAccLoadAnalytics();

         } else {

           $.gritter.add({
             title: 'Failed',
             text: res.error,
             class_name: 'danger gritter-center',
             time: ''
           });

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
         title: 'Failed',
         text: 'Failed to add new income',
         class_name: 'danger gritter-center',
         time: ''
       });

     }).always(function() {
        
        UplaodSchoolFeesPaymentLoding.hide();
        formDashPaNewIncomeData.slideDown();

     });

}

function submitPaUpdateIncomeNow(tr_exist){
  
  /* Need to hide Modal */
  
     var formDashPaNewDebt  = $("#formDashPaUpdateIncomeData");

     $.ajax({
       url: base_url+"accounting/incomes/" + tr_exist[0].id,
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the all recorded incomes now
         load_Accounting_Income_List();
               //$('#Model_DashPaFeeStudentCurrent').modal("toggle");
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'Failed to add new income',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}

function submitPaUpdateExpenseInfo(tr_exist){
  
  /* Need to hide Modal */
  
     var formDashPaNewDebt  = $("#formDashPaUpdateExpenseAll");

     $.ajax({
       url: base_url+"accounting/expenses/" + tr_exist.id,
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the all recorded incomes now
         load_Accounting_Expense_List();
               //$('#Model_DashPaFeeStudentCurrent').modal("toggle");
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'Failed to update Expenses Record',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}

function submitPaPayDebtNow(debtId){
    
    console.log("submitPaPayDebtNow");

  /* Need to hide Modal */
  
     var formDashPaNewDebt  = $("#formPayDebtNow");

     $.ajax({
       url: base_url+"accounting/schoolfees/debt/" + debtId,
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the all recorded incomes now
               $('#Model_DashPaFeeStudentList').modal("toggle");
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'Failed to pay a debt',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}

function submitPaPayOverDueNow(invoiceId){
  
  /* Need to hide Modal */
  
     var formDashPaNewDebt  = $("#formPayOverDueNow");

     $.ajax({
       url: base_url+"accounting/schoolfees/overdue/" + invoiceId,
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the all recorded incomes now
               $('#Model_DashPaFeeStudentList').modal("toggle");
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'Failed to pay a debt',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}

function submitPaPayInvoiceNow(invoiceId){
  
  /* Need to hide Modal */
  
     var formDashPaNewDebt  = $("#formPayInvoiceNow");

     $.ajax({
       url: base_url+"accounting/invoices/" + invoiceId,
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the all recorded incomes now
               $('#Model_DashPaFeeStudentList').modal("toggle");
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'Failed to pay a debt',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}


function submitPaPayBillNow(billId){
  
  /* Need to hide Modal */
  
     var formDashPaNewDebt  = $("#formPayInvoiceNow");

     $.ajax({
       url: base_url+"accounting/bills/" + billId,
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //load the all recorded incomes now
               $('#Model_DashPaFeeStudentList').modal("toggle");
         } else {

       $.gritter.add({
         title: 'Failed',
         text: res.error,
         class_name: 'danger gritter-center',
         time: ''
       });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
       title: 'Failed',
       text: 'Failed to pay a debt',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}


/* submit the school Bank item form */
function submitPaNewSchoolMoneyMovement(){
  //console.log("Saving The Form Content!");
    //Hide Your 
     $('#Model_DashPaNewMoneyTransfer').modal('toggle');

     var formDashPaNewDebt  = $("#formDashPaNewMoneyMovement");

     $.ajax({
       url: base_url+"accounting/moneymovement",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });

              // var paNewDebtStudent     = $("#paNewDebtStudent");
               //paNewDebtStudent.select2('data', null);

               
               var paNewAccountNumber = $("#paNewMovedMoney");
               paNewAccountNumber.val("");
               
         /* revalidate all fields that are required */
               formDashPaNewDebt.formValidation('revalidateField', 'movedAmount');
        setTimeout(function(e){
          load_acc_Assets_Money_Transfer();
        }, 500);
          
         } else {  

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

               $('#Model_DashPaNewMoneyTransfer').modal('show');

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
                   title: 'Failed',
                   text: 'Money Movement Failed',
                   class_name: 'danger gritter-center',
                   time: ''
               });

        $('#Model_DashPaNewMoneyTransfer').modal('show');

     })
     .always(function() {
    
    });

}

/* submit the school Bank item form */
function submitPaUpdateSchoolMoneyMovement(movementId){

    //Hide Your 
    $('#Model_DashPaUpdateMovement').modal('toggle');

     var formDashPaNewDebt  = $("#formDashPaUpdateMoneyMovement");

     $.ajax({
       url: base_url+"accounting/moneymovement/" + movementId,
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });

              // var paNewDebtStudent     = $("#paNewDebtStudent");
               //paNewDebtStudent.select2('data', null);

               
               var paNewAccountNumber = $("#paNewMovedMoney");
               paNewAccountNumber.val("");
               
         /* revalidate all fields that are required */
               formDashPaNewDebt.formValidation('revalidateField', 'movedAmount');
               
               load_acc_Assets_Money_Transfer();
      //$('#Model_DashPaUpdateMovement').modal('toggle');
         } else {  

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

               $('#Model_DashPaUpdateMovement').modal('show');

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
                   title: 'Failed',
                   text: 'Money Movement Failed',
                   class_name: 'danger gritter-center',
                   time: ''
               });

        $('#Model_DashPaUpdateMovement').modal('show');

     })
     .always(function() {
    
    });

}

/* submit the school Bank item form */
function DeleteMovement(transactionId){
  if(!confirm('Do you realy want to delete this movemt')){
    return false;
  }
    //Hide Your 
    
     $.ajax({
       url: base_url+"accounting/moneymovement/" + transactionId,
       dataType: 'json',
       type: 'POST',
       data: '_method=DELETE',
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         //reload the table for reflesshin the content
               load_acc_Assets_Money_Transfer();
              
         } else {  

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });
         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
                   title: 'Failed',
                   text: 'Fail to remove the selected movement',
                   class_name: 'danger gritter-center',
                   time: ''
        });
     })
     .always(function() {
    
    });

}


/* submit the budget item form */
function submitPaNewFixedAsset(){

    //Hide Your 
     $('#Model_DashPaNewFixedAsset').modal('toggle');

     var formDashPaNewDebt  = $("#formDashPaNewFixedAsset");

     $.ajax({
       url: base_url+"accounting/assets",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });

              // var paNewDebtStudent     = $("#paNewDebtStudent");
               //paNewDebtStudent.select2('data', null);

               var paNewFixedName     = $("#paNewFixedName");
               paNewFixedName.val("");
               var paNewFixedAssetAmount  = $("#paNewFixedAssetAmount");
               paNewFixedAssetAmount.val("");

               var paNewFicedAssetDepreciation = $("#paNewFicedAssetDepreciation");
               paNewFicedAssetDepreciation.val("");

               formDashPaNewDebt.formValidation('revalidateField', 'assetName');
               formDashPaNewDebt.formValidation('revalidateField', 'assetAmount');
               formDashPaNewDebt.formValidation('revalidateField', 'assetDepreciation');
               //formDashPaNewDebt.formValidation('revalidateField', 'registrationdate');

               load_acc_Assets_Fixed_All();
              
         } else {

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

               $('#Model_DashPaNewFixedAsset').modal('show');

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to add Asset',
                   class_name: 'danger gritter-center',
                   time: ''
               });

        $('#Model_DashPaNewFixedAsset').modal('show');

     })
     .always(function() {
    
    });

}


/* submit the budget item form */
function submitPaNewPrepaidRent(){

    //Hide Your Modal
     $('#Model_DashPaNewPrepaidRent').modal('toggle');

     var formDashPaNewDebt  = $("#formDashPaNewPrepaidRentAsset");

     $.ajax({
       url: base_url+"accounting/rents",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         
               var prepaidRentAmount      = $("#paNewPrepaidRentAmount");
               prepaidRentAmount.val("");
               var prepaidRentDescription = $("#paNewFixedName");
               prepaidRentDescription.val("");

               var prepaidRentDepreciation = $("#paNewFixedPrepaidRentDepreciation");
               prepaidRentDepreciation.val("");

               formDashPaNewDebt.formValidation('revalidateField', 'prepaidRentAmount');
               formDashPaNewDebt.formValidation('revalidateField', 'prepaidRentDescription');
               formDashPaNewDebt.formValidation('revalidateField', 'prepaidRentDepreciation');

               load_acc_Assets_Prepaid_Rent();
              
         } else {

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

               $('#Model_DashPaNewPrepaidRent').modal('show');

         }

       }

     }).done(function() {


     }).fail(function(err) {
    
        $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to add prepaid rent<br />System Configuration not ready for this',
                   class_name: 'danger gritter-center',
                   time: ''
               });

        $('#Model_DashPaNewPrepaidRent').modal('show');

     })
     .always(function() {
    
    });

}

function ImportAccountingDataSubmit(){

    var formAccountingImportFileLoading     = $('#formAccountingImportFileLoading');
    var formAccountingImportErrorMessage    = $('#formAccountingImportErrorMessage');
    var formAccountingImportFile            = $('#formAccountingImportFile');

   formAccountingImportFile.hide();
   formAccountingImportErrorMessage.empty();
   formAccountingImportFileLoading.slideDown();

   var importDataType = $("input[name='importDataType']:checked").val();
   var url = base_url+"accounting/imports?importDataType="+importDataType ; 
   
   $.ajax({
      url: url ,
      dataType: 'json',
      type: 'POST',
      data :  new FormData( formAccountingImportFile[0] ) ,
      processData: false,
      contentType: false,
      success: function ( res ) {

        if (res.success)
          {   
            $('#Model_AccountingImport').modal('toggle');
              
              $.gritter.add({
                title: 'Success',
                text: 'Data Successfully Uploaded.',
                class_name: 'success gritter-center',
                time: ''
              });
        
            var ImportAccDataFile = $('#ImportAccDataFile');
            ImportAccDataFile.val('');

            dashOnTabSelectedPayments();

          } else {

            formAccountingImportErrorMessage.html('<div class="alert alert-danger"><strong>Details!</strong> '+res.Error_message+'</div>'); 

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

    formAccountingImportFileLoading.hide();
    formAccountingImportFile.show();

  });
}

/* Submitting the newItem Receiption */
function AccountingStockDistributeSubmit(){

    //Hide Your Modal
     // $('#Model_AccountingStockReceive').modal('toggle');
    var formAccountingStockDistributeLoading     = $('#formAccountingStockDistributeLoading');
    var formAccountingStockDistribute  = $("#formAccountingStockDistribute");

    formAccountingStockDistributeLoading.slideDown();
    formAccountingStockDistribute.slideUp();

     $.ajax({
       url: base_url+"distributeItem.php",
       dataType: 'json',
       type: 'POST',
       data: formAccountingStockDistribute.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         
               $("#distributeItemQuantity").val("");
               $("#distributeItemDate").val("");

               formAccountingStockDistribute.formValidation('revalidateField', 'distributeItemQuantity');
               formAccountingStockDistribute.formValidation('revalidateField', 'distributeItemDate');
               // validateAccountingStockReceive();
               // reload the current status data
                formAccountingStockDistributeLoading.slideUp();
                formAccountingStockDistribute.slideDown();
                $('#Model_AccountingStockDistribute').modal('toggle');
                AccStockCurrentStatus();
              
         } else {

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

              formAccountingStockDistributeLoading.slideUp();
              formAccountingStockDistribute.slideDown();

         }

       }

     }).done(function() {


     }).fail(function(err) {
    
        $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to receive new Item<br />Please Check the internet connection and try again.',
                   class_name: 'danger gritter-center',
                   time: ''
               });

              formAccountingStockDistributeLoading.slideUp();
              formAccountingStockDistribute.slideDown();

     })
     .always(function() {
    
    });

}

function AccountingStockUpdateReceiveSubmit(stockID){
  // $('#Model_AccountingStockReceive').modal('toggle');
    var formAccountingStockReceiveUpdateLoading  = $('#formAccountingStockReceiveUpdateLoading');
    var formAccountingStockUpdateReceive         = $("#formAccountingStockUpdateReceive");

    formAccountingStockReceiveUpdateLoading.slideDown();
    formAccountingStockUpdateReceive.slideUp();

     $.ajax({
       url: base_url+"accounting/stockReceive/"+stockID,
       dataType: 'json',
       type: 'POST',
       data: formAccountingStockUpdateReceive.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         
               $("#receiveItemQuantityUpdate").val("");
               $("#receiveItemDateUpdate").val("");

               formAccountingStockUpdateReceive.formValidation('revalidateField', 'receiveItemQuantityUpdate');
               formAccountingStockUpdateReceive.formValidation('revalidateField', 'receiveItemDateUpdate');
               // validateAccountingStockReceive();
               // reload the current status data
                formAccountingStockReceiveUpdateLoading.slideUp();
                formAccountingStockUpdateReceive.slideDown();
                $('#Model_AccountingStockUpdateReceiveOperation').modal('toggle');
                load_Accounting_StockRecordTable();
              
         } else {

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

              formAccountingStockReceiveUpdateLoading.slideUp();
              formAccountingStockUpdateReceive.slideDown();

         }

       }

     }).done(function() {


     }).fail(function(err) {
    
        $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to receive new Item<br />Please Check the internet connection and try again.',
                   class_name: 'danger gritter-center',
                   time: ''
               });

              formAccountingStockReceiveUpdateLoading.slideUp();
              formAccountingStockUpdateReceive.slideDown();

     })
     .always(function() {
    
    });
}

/* Submitting the newItem Receiption */
function AccountingStockUpdateDistributeSubmit(stockID){

    //Hide Your Modal
     // $('#Model_AccountingStockReceive').modal('toggle');
    var formAccountingStockDistributeUpdateLoading  = $('#formAccountingStockDistributeUpdateLoading');
    var formAccountingStockUpdateDistribute         = $("#formAccountingStockUpdateDistribute");

    formAccountingStockDistributeUpdateLoading.slideDown();
    formAccountingStockUpdateDistribute.slideUp();

     $.ajax({
       url: base_url+"accounting/stockDistribute/"+stockID,
       dataType: 'json',
       type: 'POST',
       data: formAccountingStockUpdateDistribute.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         
               $("#distributeItemQuantityUpdate").val("");
               $("#distributeItemDateUpdate").val("");

               formAccountingStockUpdateDistribute.formValidation('revalidateField', 'distributeItemQuantityUpdate');
               formAccountingStockUpdateDistribute.formValidation('revalidateField', 'distributeItemDateUpdate');
               // validateAccountingStockReceive();
               // reload the current status data
                formAccountingStockDistributeUpdateLoading.slideUp();
                formAccountingStockUpdateDistribute.slideDown();
                $('#Model_AccountingStockUpdateDistributeOperation').modal('toggle');
                load_Accounting_StockRecordTable();
              
         } else {

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

              formAccountingStockDistributeUpdateLoading.slideUp();
              formAccountingStockUpdateDistribute.slideDown();

         }

       }

     }).done(function() {


     }).fail(function(err) {
    
        $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to receive new Item<br />Please Check the internet connection and try again.',
                   class_name: 'danger gritter-center',
                   time: ''
               });

              formAccountingStockDistributeUpdateLoading.slideUp();
              formAccountingStockUpdateDistribute.slideDown();

     })
     .always(function() {
    
    });

}

/* Submitting the newItem Receiption */
function AccountingStockReceiveSubmit(){

    //Hide Your Modal
     // $('#Model_AccountingStockReceive').modal('toggle');
    var formAccountingStockReceiveLoading     = $('#formAccountingStockReceiveLoading');
    var formAccountingStockReceive  = $("#formAccountingStockReceive");
    formAccountingStockReceiveLoading.slideDown();
    formAccountingStockReceive.slideUp();

     $.ajax({
       url: base_url+"receiveNewItem.php",
       dataType: 'json',
       type: 'POST',
       data: formAccountingStockReceive.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: res.success,
                   class_name: 'success gritter-center',
                   time: ''
               });
         
               $("#receiveItemQuantity").val("");
               $("#receiveItemDate").val("");

               formAccountingStockReceive.formValidation('revalidateField', 'receiveItemQuantity');
               formAccountingStockReceive.formValidation('revalidateField', 'receiveItemDate');
               // validateAccountingStockReceive();
               // reload the current status data
                formAccountingStockReceiveLoading.slideUp();
                formAccountingStockReceive.slideDown();
                $('#Model_AccountingStockReceive').modal('toggle');
                AccStockCurrentStatus();
              
         } else {

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

               formAccountingStockReceiveLoading.slideUp();
                formAccountingStockReceive.slideDown();

         }

       }

     }).done(function() {


     }).fail(function(err) {
    
        $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to receive new Item<br />Please Check the internet connection and try again.',
                   class_name: 'danger gritter-center',
                   time: ''
               });

        formAccountingStockReceiveLoading.slideUp();
        formAccountingStockReceive.slideDown();

     })
     .always(function() {
    
    });

}


function submitSchoolStockRecordGenerator(){
  
  /* Need to hide Modal */
    
     var formDashGetAccountingStockReport  = $("#formDashGetAccountingStockReport");
     nextUrl = base_url+"stockRecord.php?" + formDashGetAccountingStockReport.serialize();
     printUrl = base_url+"printStockRecord.php?" + formDashGetAccountingStockReport.serialize();
     // console.log(nextUrl);
     $("#accountingStockDownloadReport").prop("href", printUrl);
     $.ajax({
       url: base_url+"stockRecord.php",
       dataType: 'json',
       type: 'GET',
       data: formDashGetAccountingStockReport.serialize(),
       success: function ( res ) {
        // if ( res.success ){
          var tableContent = '<table class="table abDataTable table-bordered" id="acStockRecords" ><thead><tr><th class="text-center">Date</th><th class="text-center">Commited By</th><th class="text-center">Item</th><th class="text-center">Quantity</th><th class="text-center">Action</th><th class="text-center"></th></tr></thead>';
                  //tableContent += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th style="text-align:right"></th><th style="text-align:right"></th><th style="text-align:right"></th></tr></tfoot>';
                  tableContent += '<tbody>';
                  var itemCounter = 1;
              $.each( res, function(i, item) {
                tableContent  +='<tr id="'+item.id+'" >';
                  tableContent += '<td class="text-left">' + (item.operationDate) + '</td>';
                  tableContent += '<td class="text-left">' + (item.operationType == 0?'<span class="label label-primary" title="Supplie">':'<span class="label label-success" title="receiver:">') + item.operatorName + '</label></td>';
                  tableContent += '<td class="text-left">' + item.itemName + '</td>';
                  tableContent += '<td class="text-right">' + item.operationQuantity + '</td>';
                  tableContent += '<td class="text-center"><a href="#" class="text-blue"><span class="fa fa-pencil"></span></a></td>';
                  tableContent += '<td id="delete' + item.id + '" class="text-center"><a href="#" onclick="DeleteStockOperation(' + item.id + '); return false;" class="text-red"><span class="fa fa-times"></a></td>';
                tableContent  +='</tr>';
              });
          tableContent += '</table>';

          var pa_StockRecord = $("#pa_StockRecord");
          pa_StockRecord.html(tableContent);
       // }
     }

     }).done(function() {
      $("#acStockRecords").DataTable( {
        "paging":   true
      });
     }).fail(function() {

        $.gritter.add({
       title: 'Report Loading Fails',
       text: 'Fail to load the the selected report Please Try Again!',
       class_name: 'danger gritter-center',
       time: ''
     });
     })
     .always(function() {
    
    });

}