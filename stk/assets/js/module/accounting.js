var currentTd = null;

//payement 
  function dashboardAccounting(){

    console.log("Dashboard Accounting Loading.....");

    dashboardPayment();
    InitializeAccountingModule();
    validateImportAccountingData();
    validateUpdateTransaction();


    validateAccountingStockReceive();
    validateAccountingStockDistribute();

    $('#Model_UpdateTransaction').on('hidden.bs.modal', function () {
       currentTd.html("<a href='#'><i class='fa fa-pencil text-green'></i></a>");
       $("#transactionIncomeContainer").empty();
       $("#transactionAmount").val("");
    
    });

  }

  function dashOnTabSelectedPayments()
  {

    // PaNewIncomeJS();
    // PaNewExpenseJS();

    // InitializeAccountingModule();
    //dashAccLoadAnalytics();
    AccStockCurrentStatus();
  }


  function dashAccLoadAnalytics()
  {

      //get the instance of container division 
        var dashClientContainer = $("#dashPayments_content");
      
      //get the instance of the content division
        var dashContentainer_Payments = $("#dashContentainer_Payments");
        
      //hide the container first
        dashClientContainer.slideUp();
        
        //prepare the content to be printed in the containner
          
        var content = '';
          content += '<div class="row">';
            content += '<div class="col-md-12"><div class="grid"><div class="grid-body">';
               content += '<div class="row">';
                content += '<label class="col-sm-2 text-right"> Students Payment Status</label>';
                content += '<div class="col-sm-4" ><select name="" class="form-control" id="accSelectStudent" placeholder="Choose student here" ><option></option></select></div>';
                content += '<div class="col-md-2 ">Total Enrolled: <span class="badge" id="accAnalyticsTotalEnrolled" ></span></div>';
                content += '<div class="col-md-2 ">Boarding: <span class="badge bg-blue" id="accAnalyticsBoarding" >12</span></div>';
                content += '<div class="col-md-2 ">Day: <span class="badge bg-green" id="accAnalyticsDay">12</span></div>';
              content += '</div>';
            content += '</div></div></div></div>';
          content += '</div>';
          content += '<div class="row" >';
            content += '<div class="col-md-12"><div class="grid email"><div class="grid-body" id="accAnalyticsContainer">';
              
              content += '<div class="row">';
                content += '<div class="col-md-6">';
                  content += '<div class="grid-body full">';
                    content += '<canvas id="dashAcc_CurrentFees" width="500" height="250" ></canvas>';
                  content += '</div>';
                content += '</div>';
                content += '<div class="col-md-6">';
                  content += '<div class="grid-body full">';
                    content += '<canvas id="dashAcc_ProfiteLoss" width="500" height="250" ></canvas>';
                  content += '</div>';
                content += '</div>';
              content += '</div>';

              content += '<div class="row top-buffer">';
                content += '<div class="col-md-6">';
                  content += '<div class="grid-body full">';
                    content += '<canvas id="dashAcc_RECEIVABLE" width="800" height="500" ></canvas>';
                  content += '</div>';
                content += '</div>';
                content += '<div class="col-md-6">';
                  content += '<div class="grid-body full">';
                    content += '<canvas id="dashAcc_PAYABLE" width="800" height="500" ></canvas>';
                  content += '</div>';
                content += '</div>';
              content += '</div>';

            content += '</div></div></div></div>';
          content += '</div>';

        //send the prepared content to the screen for user
        dashClientContainer.html( '<div class="row"><div class="col-md-12"><div class="grid"><div class="grid-body"><center>Loading...</center></div></div></div></div>' );

        //load the contetn now
        dashContentainer_Payments.slideDown();

        dashClientContainer.slideDown();
        // return;
          var url = base_url+"analytics.php";

          $.getJSON( url, function( data ) {

            dashClientContainer.html( content );

            var students              = data.students; 

            var TotalEnrollment       = data.TotalEnrollment;
            var Total_Boarded         = data.Total_Boarded;
            var Total_Day             = data.Total_Day;  

            var TermlyFee_Names       = data.TermlyFee_Names;
            var TermlyFee_Paid        = data.TermlyFee_Paid;
            var TermlyFee_UnPaid      = data.TermlyFee_UnPaid;  

            var IncomeExpense_Period  = data.IncomeExpense_Period;
            var IncomeExpense_Income  = data.IncomeExpense_Income;
            var IncomeExpense_Expense = data.IncomeExpense_Expense;  

            var Receivable_Client     = data.Receivable_Client;
            var Receivable_Amount     = data.Receivable_Amount;
            var Receivable_Color      = data.Receivable_Color;  
            var Total_Receivable      = data.Total_Receivable;

            var Payable_Vendor        = data.Payable_Vendor;
            var Payable_Amount        = data.Payable_Amount;
            var Payable_Color         = data.Payable_Color;  
            var Total_Payable         = data.Total_Payable;

          //Student Option 
            var studentoption         = PopulateStudentInClassOption(students);
            var accSelectStudent      = $("#accSelectStudent");
            accSelectStudent.html(studentoption);
            accSelectStudent.select2({
            }).bind('change', onAcViewStudentPaymentProfile);

          //
            $("#accAnalyticsTotalEnrolled").html(TotalEnrollment);
            $("#accAnalyticsBoarding").html(Total_Boarded);
            $("#accAnalyticsDay").html(Total_Day);

          //
            var barChartData = {
            labels: TermlyFee_Names ,
                datasets: [{
                    label: 'Paid Amount',
                    backgroundColor: '#6666ff',
                    data: TermlyFee_Paid
                }, {
                    label: 'Un-Paid Amount',
                    backgroundColor: '#d69999',
                    data: TermlyFee_UnPaid
                }]

            };

            var ctx = document.getElementById("dashAcc_CurrentFees").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    title:{
                        display:true,
                        text:"Current Term Fees Payment Status"
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false ,
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return schoolCurrency+" " + Number(tooltipItem.yLabel).toFixed(0).replace(/./g, function(c, i, a) {
                                    return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
                                });
                            }
                        }
                    },
                    responsive: true,
                    scales: {
                        xAxes: [{
                            stacked: true,
                        }],
                        yAxes: [{
                            stacked: true
                        }]
                    }
                }
            });

          //

            new Chart(document.getElementById("dashAcc_ProfiteLoss"), {
                type: 'line',
                data: {
                  labels: IncomeExpense_Period,
                  datasets: [{ 
                      data: IncomeExpense_Income,
                      label: "Income",
                      borderColor: "#3e95cd",
                      fill: false 
                    }, {
                      data: IncomeExpense_Expense,
                      label: "Expenses",
                      borderColor: "#c45850",
                      fill: false 
                    }
                  ]
                },
                options: {
                  title: {
                    display: true,
                    text: 'Monthly Income VS Expenses'
                  },
                  tooltips: {
                      mode: 'index',
                      intersect: false ,
                      callbacks: {
                          label: function(tooltipItem, data) {
                              return schoolCurrency+" " + Number(tooltipItem.yLabel).toFixed(0).replace(/./g, function(c, i, a) {
                                  return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
                              });
                          }
                      }
                  }
                }
              });

            //
              new Chart(document.getElementById("dashAcc_RECEIVABLE"), {
              type: 'horizontalBar',
                data: {
                  labels: Receivable_Client,
                  datasets: [
                    {
                      label: "Amount ("+schoolCurrency+")",
                      backgroundColor: Receivable_Color,
                      data: Receivable_Amount
                    }
                  ]
                },
                options: {
                  legend: { display: false },
                  title: {
                    display: true,
                    text: 'Receivables - Total: '+formatNumber(Total_Receivable)+' '+schoolCurrency
                  },
                  tooltips: {
                      callbacks: {
                          label: function(tooltipItem, data) {
                              return schoolCurrency+" " + formatNumber(tooltipItem.xLabel);
                          }
                      }
                  }
                }
            });

          //
            new Chart(document.getElementById("dashAcc_PAYABLE"), {
                type: 'polarArea',
                data: {
                  labels: Payable_Vendor,
                  datasets: [
                    {
                      label: "Amount ("+schoolCurrency+")",
                      backgroundColor: Payable_Color,
                      data: Payable_Amount
                    }
                  ]
                },
                options: {
                  title: {
                    display: true,
                    text: 'Payables - Total: '+formatNumber(Total_Payable)+' '+schoolCurrency
                  },
                  tooltips: {
                      mode: 'index',
                      intersect: false ,
                      callbacks: {
                          label: function(tooltipItem, data) {
                              return schoolCurrency+" " + formatNumber(tooltipItem.yLabel);
                          }
                      }
                  }
                }
            });

          }).done(function() {

          })
          .fail(function() {

          })
          .always(function() {

          });
  }

  function onAcViewStudentPaymentProfile()
  {

    var accSelectStudent        = $("#accSelectStudent");
    var regnumber               = accSelectStudent.select2('val');
    var studentNameRegNumber    = accSelectStudent.select2('data').text;

    $("#transactionRegNumber").val(regnumber);
    $("#accAnalyticsContainer").html("<center>Loading "+studentNameRegNumber+" 's payment status...</center>");

    var container       = $( "#accAnalyticsContainer" );
    container.empty();   

    var url = base_url+"accounting/studentPaymentStatus/"+regnumber; 

    var content    = '';

    $.getJSON( url, function(data) {

      var StudentInfo     = data.StudentInfo;
      var UnPaidAc_Debts  = data.UnPaidAc_Debts ;
      var Term_Other      = data.Term_Other ;
      var Term_Current    = data.Term_Current ;
      var Transaction_Data= data.Transaction_Data ;
      var Total_Unassigned= data.Total_Unassigned ;

      var StudentSponsors = data.StudentSponsors;

      content  += '<div class="row">';
        content  += '<div class="col-sm-3">';
          content  += '<div class="row"><div class="top-buffer"><center><img src="images/student/200/0/0" class="img-circle thumbnail" alt=""></center></div></div>';
          content  += '<div class="row text-center"><h4><strong>'+studentNameRegNumber+'</strong></h4></div>';
          content  += '<div class="row top-buffermin"><label class="col-sm-5 text-right">un-assigned: </label><label class="col-sm-7 text-left">'+schoolCurrency+' '+formatNumber(Total_Unassigned.Total_Unassigned)+'</label></div>';
          content  += '<div class="row top-buffermin"><label class="col-sm-5 text-right">Sponsor : </label><label class="col-sm-7 text-left" id="acStudentSponsor"></label></div>';
          content  += '<div class="row"><label class="col-sm-5 text-right">Boarding : </label><label class="col-sm-7 text-left" id="acStudentBoarding" ></label></div>';
          content  += '<div class="row"><label class="col-sm-5 text-right">Contact : </label><label class="col-sm-7 text-left" id="acStudentContact" ></label></div>';
        content  += '</div>';
        content  += '<div class="col-sm-9">';
          content  += '<div class="row text-center"><h5><strong>Payment Status</strong></h5></div>';
          content  += '<div class="row">';
            content  += '<table class="table abDataTable table-bordered" id="acStudentPaymentsStatus" ><thead><tr><th class="text-center">Term</th><th class="text-center">Fee</th><th class="text-center">Description</th><th class="text-center">To Pay</th><th class="text-center">Paid</th><th class="text-center">Remain</th></tr></thead>';
            content  += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th style="text-align:right"></th><th style="text-align:right"></th><th style="text-align:right"></th></tr></tfoot>';
            content  += '<tbody>';
              
              $.each( UnPaidAc_Debts, function(i, item) {

                  var Total_Remain   = parseFloat(item.amount_ToPay) - parseFloat(item.amount_Paid);

                  content  +='<tr id="'+item.id+'" >';
                    content  +='<td style="background-color:#ffcccc;" class="text-left"></td>';
                    content  +='<td style="background-color:#ffcccc;" class="text-left">Debt</td>';
                    content  +='<td style="background-color:#ffcccc;" class="text-left">'+item.description+'</td>';
                    content  +='<td style="background-color:#ffcccc;" class="text-right">'+formatNumber(item.amount_ToPay)+'</td>';
                    content  +='<td style="background-color:#ffcccc;" class="text-right">'+formatNumber(item.amount_Paid)+'</td>';
                    content  +='<td style="background-color:#ffcccc;" class="text-right">'+formatNumber(Total_Remain)+'</td>';
                  content  +='</tr>';
                  
              });

              $.each( Term_Other, function(i, item) {

                  var Total_Remain   = parseFloat(item.amount_ToPay) - parseFloat(item.amount_Paid);

                  content  +='<tr id="'+item.id+'" >';
                    content  +='<td style="background-color:#ffe5e5;" class="text-left">'+item.term+'</td>';
                    content  +='<td style="background-color:#ffe5e5;" class="text-left">'+item.name+'</td>';
                    content  +='<td style="background-color:#ffe5e5;" class="text-left">'+item.description+'</td>';
                    content  +='<td style="background-color:#ffe5e5;" class="text-right">'+formatNumber(item.amount_ToPay)+'</td>';
                    content  +='<td style="background-color:#ffe5e5;" class="text-right">'+formatNumber(item.amount_Paid)+'</td>';
                    content  +='<td style="background-color:#ffe5e5;" class="text-right">'+formatNumber(Total_Remain)+'</td>';
                  content  +='</tr>';
                  
              });

              $.each( Term_Current , function(i, item) {

                  var Total_Remain   = parseFloat(item.amount_ToPay) - parseFloat(item.amount_Paid);

                  content  +='<tr id="'+item.id+'" >';
                    content  +='<td style="background-color:#e5e5ff;" class="text-left">'+item.term+'</td>';
                    content  +='<td style="background-color:#e5e5ff;" class="text-left">'+item.name+'</td>';
                    content  +='<td style="background-color:#e5e5ff;" class="text-left">'+item.description+'</td>';
                    content  +='<td style="background-color:#e5e5ff;" class="text-right">'+formatNumber(item.amount_ToPay)+'</td>';
                    content  +='<td style="background-color:#e5e5ff;" class="text-right">'+formatNumber(item.amount_Paid)+'</td>';
                    content  +='<td style="background-color:#e5e5ff;" class="text-right">'+formatNumber(Total_Remain)+'</td>';
                  content  +='</tr>';
                  
              });

            content  += '</table>';
          content  += '</div>';

          content  += '<div class="row text-center"><h5><strong>Payment Transactions</strong></h5></div>';
            content  += '<div class="row">';
            content += '<table class="table table-bordered abDataTable" id="acStudentTransactions" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Amount</th><th class="text-center">Deposit in</th><th class="text-center">Fees Paid</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead>';
              content += '<tfoot><tr><th colspan="1" style="text-align:right">Total:</th><th colspan="5" style="text-align:left"></th></tr></tfoot>';
              content += '<tbody>';

              var TransIdentifier   = null;
              var isTheFirsRecord   = true;

              var gray_color        = "#d3d3d3";
              var white_color       = "#ffffff";
              var current_color     = white_color;

              var TotalAmount_Trans = 0;
              var TotalAmount_Dist  = 0;

              $.each( Transaction_Data , function(i, item) {

                    content +="<tr id="+item.id +" studRegNber= "+regnumber+">";
                      content +="<td class='text-center'>"+ date_moment(item.date) +" </td>";
                      content +="<td class='text-right'>"+ formatNumber(item.paidAmount) + " </td>"; 
                      content +="<td class='text-center'>"+ item.deposedIn +" </td>";
                      content +="<td class='text-left'>"+ item.distribution +" </td>";
                      content +="<td class='text-center acIncomeTransEdit'><a href='#'><i class='fa fa-pencil text-green'></i></a></td>";
                      content +="<td class='text-center AccIncomesSchoolFeesDelete'><a href='#'><i class='fa fa-times text-red'></i></a></td>"; 
                    content +="</tr>";
                  
              });

            content  += '</table>';
          content  += '</div>';
        content  += '</div>';
      content  += '</div>';

      container.html(content);

      $("#acStudentSponsor").html('<span class="xeditableAccStudentSponsor" data-type="select2" data-pk="1" data-title="Select Student Sponsor" data-url="'+base_url+'accounting/students/'+regnumber+'?UpdateType=3"><span class="badge bg-maroon">'+StudentInfo.sponsor+'</span></span>');

      if ( StudentInfo.Boarding == "Boarding") {
        $("#acStudentBoarding").html('<span class="xeditableAccStudentBoarding" data-type="select2" data-pk="1" data-title="Select Boarding status" data-url="'+base_url+'accounting/students/'+regnumber+'?UpdateType=4"><span class="badge bg-green">'+StudentInfo.Boarding+'</span></span>');

      }else if ( StudentInfo.Boarding == "Day" ) {
        $("#acStudentBoarding").html('<span class="xeditableAccStudentBoarding" data-type="select2" data-pk="1" data-title="Select Boarding status" data-url="'+base_url+'accounting/students/'+regnumber+'?UpdateType=4"><span class="badge bg-blue">'+StudentInfo.Boarding+'</span></span>');

      }else{
        $("#acStudentBoarding").html('<span class="xeditableAccStudentBoarding" data-type="select2" data-pk="1" data-title="Select Boarding status" data-url="'+base_url+'accounting/students/'+regnumber+'?UpdateType=4"><span class="badge">'+StudentInfo.Boarding+'</span></span>');
      }

      $("#acStudentContact").html(StudentInfo.parentContact);

      $("#acStudentTotalAmount_Trans").html('<span class="text-blue">'+schoolCurrency+' '+formatNumber(TotalAmount_Trans)+'</span>'); 
      $("#acStudentTotalAmount_Dist").html('<span class="text-green">'+schoolCurrency+' '+formatNumber(TotalAmount_Dist)+'</span>'); 

      $.fn.editable.defaults.ajaxOptions = {type: "PUT" };
      $.fn.editable.defaults.mode = 'popup'; 
      
      $('.xeditableAccStudentBoarding').editable({
          source: [
                {id: '1', text: 'Boarding'},
                {id: '0', text: 'Day'}
             ],
          select2: {
             width:150 ,
             multiple: false
          },
          success: function(response, newValue) {
            if(response.status == 'error') return response.msg;
        }
      });

      $('.xeditableAccStudentSponsor').editable({
          source: StudentSponsors,
          select2: {
             width:200,
             multiple: false
          },
          success: function(response, newValue) {
            if(response.status == 'error') return response.msg;
        }
      });

      $( "table#acStudentTransactions" ).delegate( "td.AccIncomesSchoolFeesDelete", "click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          console.log(transaction_id);
          console.log(studRegNber);

          var transaction_id  = $(this).closest('tr').attr('id');
          var studRegNber     = $(this).closest('tr').attr('studRegNber');

          loadTransactionDelete( 2, 1, studRegNber, transaction_id, $(this) );

      });

      $( "table#acStudentTransactions" ).delegate( "td.acIncomeTransEdit", "click", function(e) {

        e.preventDefault();
        e.stopPropagation();

        var transaction_id  = $(this).closest('tr').attr('id');
        var studRegNber     = $(this).closest('tr').attr('studRegNber');

        loadTransactionEdit( 2, 1, studRegNber, transaction_id, $(this) );

      });

    })
    .done(function() {

          $('#acStudentPaymentsStatus').DataTable( {
              "paging":   false,
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
                      .column( 3, {search:'applied'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                      
                   // Update footer
                  $( api.column( 3 ).footer() ).html(
                      '<span class="text-blue">'+schoolCurrency+' '+formatNumber(total_ToPaid)+'</span>'
                  );


                  // Total Paid  
                    total_Paid = api
                        .column( 4, {search:'applied'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                        
                     // Update footer
                    $( api.column( 4 ).footer() ).html(
                        '<span class="text-green">'+schoolCurrency+' '+formatNumber(total_Paid)+'</span>'
                    );

                   // Total Remain  
                    total_Remain = api
                        .column( 5, {search:'applied'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                        
                     // Update footer
                    $( api.column( 5 ).footer() ).html(
                        '<span class="text-red">'+schoolCurrency+' '+formatNumber(total_Remain)+'</span>'
                    );


              }
          } );

           $('#acStudentTransactions').removeAttr('width').DataTable( {
              paging:         false ,
              columnDefs: [
                  { width: "17%", targets: 0 },
                  { width: "17%", targets: 0 },
                  { width: "20%", targets: 0 },
                  { width: "30%", targets: 0 },
                  { width: "8%", targets: 0 },
                  { width: "8%", targets: 0 }
              ],
              fixedColumns: true ,
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
                      .column( 1, {search:'applied'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                      
                   // Update footer
                  $( api.column( 1 ).footer() ).html(
                      '<span class="text-green">'+schoolCurrency+' '+formatNumber(total_Paid)+'</span>'
                  );

              }
          } );

    })
    .fail(function() {
    })
    .always(function() {

      $("#accSelectStudent").select2('destroy').val("").select2();

    });

  }


  function loadTransactionEdit( Via, UpdateType, RegNumber, transaction_id, td )
  { 

    currentTd = td;

    td.html('<img src="../packages/assets/plugins/img/loading.gif" alt="Loading..." ></div>');

    var url = base_url+"accounting/transactions/"+transaction_id+"/edit?UpdateType="+UpdateType+"&RegNumber="+RegNumber;

      $.getJSON( url, function(data) {

          var Transaction_Info  = data.Transaction_Info;
          var Transaction_Data  = data.Transaction_Data;

          $('#Model_UpdateTransaction').modal('show');
          $("#transactionAmount").val(Transaction_Info.amount);

          var content = '';
          $.each( Transaction_Data, function(i, item){

            content += '<div class="form-group">';
              content += '<label class="col-sm-3 control-label">'+item.Name+'</label>';
              content += '<div class="col-sm-7">';
                content += '<input name="income['+item.incomeId+']" type="text" class="form-control accIncomeDistrAmount" value="'+item.Amount+'">';
              content += '</div>';
            content += '</div>';

          });

          $("#transactionIncomeContainer").html(content);

          $("#transactionVia").val(Via);
          $("#transactionType").val(UpdateType);
          $("#transactionId").val(transaction_id);
          $("#transactionRegNumber").val(RegNumber);

          $("#transactionAmount").val(Transaction_Info.amount);
            
          $( ".accIncomeDistrAmount" ).change(function() {
            $("#accUpdateTransactionForm").formValidation('revalidateField', 'transactionAmount');
          });

      }).done(function() {
      })
      .fail(function() {
      })
      .always(function() {
      });

  }

  function loadTransactionDelete( Via, UpdateType, RegNumber, transaction_id, td )
  { 

    $('#accDeleteTransactionVia').val(Via);
    $('#accDeleteTransactionType').val(UpdateType);
    $('#accDeleteTransactionId').val(transaction_id);
    $('#accDeleteTransactionRegNumber').val(RegNumber);

    $('#Model_DashAccDeleteTrans').modal('show');
    $("#deleteTransactionLoading").hide();
    
    var accDeleteTransactionConfirm = $("#accDeleteTransactionConfirm");
    accDeleteTransactionConfirm.click(function(){
        
        var accDeleteTransactionId     = $("#accDeleteTransactionId").val();

        var deleteTransactionLoading  = $("#deleteTransactionLoading");
        var deleteTransactionContent  = $("#deleteTransactionContent");

        deleteTransactionContent.hide();
        deleteTransactionLoading.slideDown();

        var url     = base_url+"accounting/transactions/"+accDeleteTransactionId; 

          $.ajax({
                url: url,
                type: 'DELETE',
                success: function(data) {
                  
                  deleteTransactionLoading.hide();
                  deleteTransactionContent.slideDown();

                   if ( data.success )
                   {
                        $.gritter.add({
                          title: 'Success',
                          text: 'Transaction deleted.',
                          class_name: 'success gritter-center',
                          time: ''
                        });

                        var transactionRegNumber = $("#accDeleteTransactionRegNumber").val();
                        var transactionVia       = $("#accDeleteTransactionVia").val();

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
                        text: 'Failed to delete transaction, Something went wrong.',
                        class_name: 'danger gritter-center',
                        time: ''
                      });

                 }
            },
            error: function(){
           
            deleteTransactionLoading.hide();
            deleteTransactionContent.slideDown();

            $('#Model_DashAccDeleteTrans').modal('toggle');
            
              $.gritter.add({
                title: 'Failed',
                text: 'Failed to delete transaction.',
                class_name: 'danger gritter-center',
                time: ''
              });
        }

        });


    });

  }

/* Public variable required for accounting module to run correctly */

/* End Public variable */
function InitializeAccountingModule(){
  /* console.log("Accounting Module Loading");
  //activate the expense part of the system
  //InitializeExpenseModule();
  console.log("Accounting Module Loaded Correctly."); */
  
  /* Load the School Bank Account Validator Function */
  validateNewBankItem();
  /* Acticatet the new Budget Item Now */
  validateNewBudgetItem();
  /* Activate the new Cash Account Now */
  validateNewCashItem();
  /* Activate the money movement now */
  validateNewCashMovement();
  /* Activate the new fixed form */
  validateNewFixedAsset();
  /* Activate the new prepaid rent form */
  validateNewPrepaidRentAsset();

  /* Validate the expense category form */
  validateNewExpenseCategory();
}

function NewBillForm( budgetitems, vendors, product_list, taxs){

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
                content+= '<h4>New Bill</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewBillList class="btn btn-primary pull-right"><i class="fa fa-bars"></i>&nbsp;&nbsp;Bill List</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">\
                          <div id="pa_Expense_ExpenseType">\
                            <form id="formDashPaNewBill" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                              <div class="modal-body" >\
                                <div class="form-group" > \
                                  <div class="col-sm-2 text-right">Expense Account</div>\
                                    <div class="col-sm-8">\
                                      <select name="budgetitem" class="form-control" id="paNewBillBudgetItem" placeholder="Choose Budget Item" >';
                                          var category = "";
                                          $.each(budgetitems, function(i, item){
                                            if(category != item.categoryId){
                                              content += "<optgroup label='" + item.categoryName + "'>";
                                              category = item.categoryId;
                                            }
                                            content+= "<option value='" + item.id + "'>" + item.name + "</option>";
                                          });
                            content+= '</select>\
                                    </div >\
                                  </div>\
                                  <div class="form-group" > \
                                    <div class="col-sm-2 text-right">Vendor</div>\
                                      <div class="col-sm-8">\
                                        <select name="billVendor" class="form-control" id="paNewBillVendor" placeholder="Choose Vendor Name" >';
                                          $.each(vendors, function(i, item){
                                            content+= "<option value='" + item.id + "'>" + item.name + "</option>";
                                          });
                              content+= '</select>\
                                      </div >\
                                    </div>\
                                  <div class="form-group" > \
                                    <div class="col-sm-2 text-right">Bill Nr.</div>\
                                    <div class="col-sm-8">\
                                      <input name="billNumber" type="text" id="paNewBillNumber" class="form-control" >\
                                    </div >\
                                  </div>\
                                <div class="form-group" > \
                                  <div class="col-sm-2 text-right">Bill Date</div>\
                                  <div class="col-sm-8">\
                                    <input name="billDate" type="text" id="paNewBillDate" class="form-control" >\
                                  </div >\
                                </div>\
                                <div class="form-group" > \
                                  <div class="col-sm-2 text-right">Payment Date</div>\
                                  <div class="col-sm-8">\
                                    <input name="paymentDate" type="text" id="paNewBillPaymentDate" class="form-control" >\
                                  </div >\
                                </div>\
                              </div>\
                              <div class="form-group" > \
                                <div class="col-sm-2 text-right"></div>\
                                <div class="col-sm-8">\
                                  <div class="form-group" id="InvoiceProductItems" > \
                                    <div class="col-sm-3">\
                                      Product\
                                    </div >\
                                    <div class="col-sm-3">\
                                      Amount\
                                    </div >\
                                    <div class="col-sm-3">\
                                      Tax\
                                    </div >\
                                    <div class="col-sm-3">\
                                      Final Amount\
                                    </div >\
                                  </div>\
                                  <div id="AccountingNewBillItems"> \
                                  </div>\
                                  <div class="form-group" id="InvoiceProductItems" >\
                                    <div class="col-sm-3">\
                                      <i class="fa fa-plus icon-4x" id="paAccountNewBillItemAdd" style="color:green; cursor:pointer;" aria-hidden="true" title="Add New Product"></i>\
                                    </div>\
                                    <div class="col-sm-3">\
                                    </div>\
                                    <div class="col-sm-3">\
                                      Total\
                                    </div>\
                                    <div class="col-sm-3">\
                                      <input name="billAmount" readonly type="text" id="paNewBillPaymentTotalAmount" class="form-control" >\
                                      <input name="totalItemsOnBill" type="hidden"  id="totalItemsOnBill" class="form-control" value="1" >\
                                    </div>\
                                  </div>\
                                </div>\
                              </div>\
                            <div class="modal-footer" >\
                            <div class="pull-right btn-group">\
                              <!--<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>-->\
                              <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Bill</button>\
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

  AccountingNewBillJS(product_list, taxs);

}

/* Active button of new bill view */
function AccountingNewBillJS(product_list, taxs){
  
  //active select 2 for the client name name
  //activate the drop down menu to allow to load new client upon drop
  $("#paNewBillBudgetItem").select2();
  $("#paNewBillVendor").select2();

  var counter = 1;  
  
  //active the add new button 
    $("#paAccountNewBillItemAdd").click(function(){
      //add a new row to the item table now
      var data = '\
        <div class="form-group" id="billProductItems" > \
          <div class="col-sm-3">\
            <select name="billItemProductID_' + counter + '" class="form-control paNewBillItemProduct' + counter + '" counter="'+ counter +'" id="paNewBillItem' + counter + '" placeholder="Choose Product" >\
              <option></option>';
              $.each(product_list, function(i, item){
                data += "<option for='" + item.amount + "' data-amount="+ item.amount +" value='" + item.id + "'>" + item.name + "</option>";
              });
            data += '</select>\
          </div >\
          <div class="col-sm-3">\
            <input name="billItemProductAmount_' + counter + '" type="text" id="paNewBillPrice' + counter + '" class="form-control _paNewBillItem' + counter + ' _paNewBillItemTax' + counter + ' pa_NewBillItemTax' + counter + '" >\
          </div >\
          <div class="col-sm-3">\
            <select name="billItemTax_' + counter + '" class="form-control paNewBillItemTax' + counter + ' taxpaNewBillItem' + counter + '" counter="'+ counter +'" id="pa_NewBillItemTax' + counter + '" placeholder="Choose Tax" >\
              <option value="0"> None</option>';
        $.each(taxs, function(i, item){
          data += "<option value='" + item.id + "' data-percentage='"+ item.percentage +"'>" + item.name + " " + item.percentage + "%</option>";
        });
        data += '</select>\
          </div >\
          <div class="col-sm-3">\
            <input name="billItemProductFinalAmount_' + counter + '" type="text" id="paNewBillFinalAmount' + counter + '" class="form-control finalpaNewBillItem' + counter + ' finalpaNewBillItemTax' + counter + ' finalpaNewBillPrice' + counter + ' finalpa_NewBillItemTax' + counter + '" >\
          </div >\
        </div>';
                    
      $("#AccountingNewBillItems").append(data);

      $("#paNewBillPrice" + counter + "").blur(counter, function(e){
          
          RecalculateTotal( e.data ,'Bill');

      });

      $("#paNewBillFinalAmount" + counter + "").blur(counter, function(e){
            
          RecalculateTotalReverse( e.data ,'Bill');

      });
    
      var paNewBillItemProduct = $(".paNewBillItemProduct" + counter );

      paNewBillItemProduct.select2({
        templateResult: formatInvoiceItemsList
      }).bind('change', function(){

          var clicked         = $(this);

          var paNewBillItemProduct    = $("#paNewBillItem"+clicked.attr("counter"));
          var selectedProduct         = paNewBillItemProduct.select2('val');
          var selectedProductAmount   = paNewBillItemProduct.select2().find(":selected").data("amount");

          $("._" + clicked.attr("id")).val(selectedProductAmount);

          var tax     = $(".tax" + clicked.attr("id")).val();
          var finalamount = $("._" + clicked.attr("id")).val() * (tax > 0?tax:1);
          var totalamount = ($("#paNewBillPaymentTotalAmount").val()*1) - $(".final" + clicked.attr('id')).val();
          
          $(".final" + clicked.attr("id")).val(finalamount);
          $("#paNewBillPaymentTotalAmount").val( ($(".final" + clicked.attr("id")).val()*1) + totalamount);
          $("#formDashPaNewBill").formValidation('revalidateField', 'billAmount');

      });

      var paNewBillItemTax = $(".paNewBillItemTax" + counter );

      paNewBillItemTax.select2({
        templateResult: formatInvoiceItemsList
      }).bind('change', function(){

          var clicked         = $(this);

          var paNewBillItemTax      = $("#pa_NewBillItemTax"+clicked.attr("counter"));
          var selectedTax           = paNewBillItemTax.select2('val');
          var selectedTaxPercentage = paNewBillItemTax.select2().find(":selected").data("percentage");

          RecalculateTotal(clicked.attr("counter"), "Bill");

      });

      $("#totalItemsOnBill").val(counter);

      counter += 1;

    });

    $("#paAccountNewBillItemAdd").click();

  
  $('#paNewBillDate').datepicker().datepicker("setDate", new Date());
  $('#paNewBillPaymentDate').datepicker().datepicker("setDate", new Date());

  //activate the list bill button
  $("#AccountingNewBillList").bind("click",load_Accounting_List_Bills);
  //validate the visibale form now
  validateNewBill();
  //allow a modal to open programaticaly
  $('#Model_Dash_PaNewVendor').modal({ show: false})
}

/* validate the New Item Budget Item Form */
function validateNewLoan(){
  
  //validate the form now
  $('#formDashPaNewLoanToPay').formValidation({
      
         framework: 'bootstrap',
         excluded: [':disabled'],
         icon: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
         },
         fields: {
          loanName: {
                 validators: {
                     notEmpty: {
                         message: 'Name is required'
                     }
                 }
             },
          provider: {
                 validators: {
                     notEmpty: {
                         message: 'Provider is required'
                     }
                 }
             },
          description: {
                 validators: {
                     notEmpty: {
                         message: 'Description is required'
                     }
                 }
             },
          totalAmount: {
                 validators: {
                     notEmpty: {
                         message: 'Amount is required'
                     },
                     integer: {
                         message: 'The amount should be number'
                     }

                 }
             },
          installmentAmount: {
                 validators: {
                     notEmpty: {
                         message: 'Installment is required'
                     },
                     integer: {
                         message: 'The Installment amount should be number'
                     }

                 }
             },
          loanInterest: {
                 validators: {
                     notEmpty: {
                         message: 'Intereset Rate is required'
                     },
                     double: {
                         message: 'The Intereset Rate should be number'
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
           submitPaNewLoanInfo();

       });
}

/* submit the budget item form */
function submitPaNewBillInfo(){
     var formDashPaNewDebt  = $("#formDashPaNewBill");

     $.ajax({
       url: base_url+"accounting/bills",
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
               load_Accounting_List_Bills();
              
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
    
    });

}

/* submit the budget item form */
function submitPaNewLoanInfo(){
     var formDashPaNewDebt  = $("#formDashPaNewLoanToPay");

     $.ajax({
       url: base_url+"accounting/loans",
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
               load_acc_loan_to_pay();
              
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
    
    });

}


//payement 


    //var studentFeesLoaded = false; 
    function dashboardPayment() {

        $('#acc_Stock').click(function(e) 
        { 
          AccStockCurrentStatus();
        });

/* Start Accounting Modules button listners */
    //Activate the new expense link now
    $("#AccountingNewExpense").click(function(e){
      load_acc_New_Expense_Form();
    });

    $('#AccountingNewIncome').click(function(e) { 
      load_acc_New_Income_Form();
    });
    
    $('#LoadNationalResultREB').click(function(e) { 
      LoadNationalResultREBRes();
    });
    
    //activate the new vendor link
    $("#AccountingNewVendor").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_New_Vendor();
    });
    
    /* Start Income Menu Activation */
    
    //activate the recorded Link link
    $("#AccountingIncomeSchoolFees").click(function(e){
      //now the new client button is clicked and the load the new client form
        load_Accounting_Income_SchoolFees();
    });

    $("#AccountingIncomeInvoice").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_Income_Invoice();
    });

    //activate the new invoice link
    $("#AccountingSchoolFees").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_School_Fees();
      load_Accounting_School_Fees_Current_Fees()
    });
    //activate the new invoice link
    $("#AccountingTransactionMobiCash").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_Transaction_MobiCash();
    });
    //activate the new invoice link
    $("#AccountingNewInvoice").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_New_Invoice();
    });
    //activate the list of invoice link
    $("#AccountingListInvoice").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_List_Invoice();
    });
    //activate the new client link
    $("#AccountingNewClient").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_New_Client();
    });
    //activate the client List link
    $("#AccountingListClient").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_List_Client();
    });

    //activate the list of invoice link
    $("#AccountingListProduct_WeSell").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_List_Product(1);

    });

    // //activate the new Product link
    $("#AccountingNewProduct1").click(function(e){
      //now the new product button is clicked and the load the new client form
      load_Accounting_New_Product(1);
    });

    //activate the Organization sponsor List link
    $("#AccountingSponsorList").click(function(e){
      load_Accounting_List_Organization();
    });
    //activate the Organization sponsor List link
    $("#AccountingCapitalEquityList").click(function(e){
      load_Accounting_List_Capital_Equity();
    });
    /* End Income Menu Activation */
    
    /* Start of Expense Menu */
    //activate the recorded Expense Menu
    $("#AccountingExpenseList").click(function(e){
      load_Accounting_Expense_List();
    })
    
    //activate the vednors Menu
    $("#AccountingExpenseVendorsList").click(function(e){
      load_Accounting_List_Vendor();
    })
    
    //activate the list of invoice link
    $("#AccountingListProduct_WeBuy").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_List_Product(2);

    });

    // //activate the new Product link
    $("#AccountingNewProduct2").click(function(e){
      //now the new product button is clicked and the load the new client form
      load_Accounting_New_Product(2);
    });

    //activate the list of invoice link
    $("#AccountingExpenseBillsList").click(function(e){
      //now the new client button is clicked and the load the new client form
      load_Accounting_List_Bills();
    });
    /* End of Expense Menu */
    //activate the new bill link
    // $("#AccountingNewBill").click(function(e){
    //   //now the new client button is clicked and the load the new client form
    //   load_Accounting_New_Bill();

    // });
    
    
  /* End of Accounting Modules */
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
                  .column( 4, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
                  .column( 3, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
                  .column( 4, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-blue">'+formatNumber(total_ToPaid)+' '+schoolCurrency+'</span>'
              );


              // Total Paid  
                total_Paid = api
                    .column( 5, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 5 ).footer() ).html(
                    '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
                );

               // Total Remain  
                total_Remain = api
                    .column( 6, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 6 ).footer() ).html(
                    '<span class="text-red">'+formatNumber(total_Remain)+' '+schoolCurrency+'</span>'
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
                content+= '<a data-target="#" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Fee</a>';
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
                  .column( 3, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
                  .column( 3, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
                  .column( 3, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
                  .column( 4, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
                  .column( 4, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
                  .column( 4, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
                  .column( 4, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
                  .column( 3, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
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
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>\
      <div class="modal fade" id="Model_DashPaExpenseDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
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
      </div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"accounting/expenses";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered table-striped abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Category</th><th class="text-center">Item</th><th>Details</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+item.categoryName+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+item.ExpenseNumber + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-center' style='cursor:pointer' onclick='ExpenseDetails(" + item.id + ");'> <a href='#' onclick='return false;'><i class='fa fa-list'></i>&nbsp;</a> </td>";
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
                  .column( 3, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  //to allow modal to be opened programaticaly
  $('#Model_DashPaExpenseDetails').modal({ show: false});
  AccountingListExpenseTypeJS();

}

function load_acc_loan_to_pay(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>Loans Payment</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                //content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Loan</a>';
                content+= '<a class="btn btn-primary pull-right" id="AccountingExpenseNewLoan" ><i class="fa fa-plus"></i>&nbsp;&nbsp;New Loan</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>\
      <div class="modal fade" id="Model_DashPaExpenseDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
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
      </div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();

  var url = base_url+"accounting/loans";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Name</th><th class="text-center">Description</th><th class="text-center">Duration</th><th>Payment Mode</th><th>Total</th><th>Paid Amount</th><th>Remaining</th><th>action</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.name+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.description+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.date+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.paymentModeId+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+formatNumber(item.amount)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+formatNumber(item.PaidAmount)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+formatNumber(item.amount - item.PaidAmount)+" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center' style='cursor:pointer' onclick='LoanPaymentDetails(" + item.id + ");'> Details </td>";
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
                  .column( 3, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  //to allow modal to be opened programaticaly
  $('#Model_DashPaExpenseDetails').modal({ show: false});
  AccountingListLoanToPayJS();

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
                content+= '<h4>School Cash</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewCashAccount" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Cash On Hand account</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>\
      <div class="modal fade" id="Model_DashPaExpenseDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
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
      </div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();
  //1 in the url parameters represante the cash on hand in database
  var url = base_url+"accounting/assets?type=1";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="AccountingCashOnHandTable" ><thead><tr ><th class="text-center">Account</th><th class="text-center">Starting Amount</th><th class="text-center">Current Amount</th><th class="text-center" >Operations</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th style="text-align:right"></th><th style="text-align:right"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //console.log(data);
      $.each( data, function(i, item) {
        
          var StartingAmount = parseFloat(item.startingAmount); 
          var TotalCredit    = parseFloat(item.TotalCredit); 
          var TotalDebt      = parseFloat(item.TotalDebt); 
          var CurrentAmount  = StartingAmount + TotalCredit + TotalDebt ;

          Content_Pa_Deposits_Others +="<tr id=" + item.id + ">";
            Content_Pa_Deposits_Others +="<td >" + item.accountName + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(StartingAmount) + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(CurrentAmount) + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.numberOperations + "</td>";
            // Content_Pa_Deposits_Others +="<td style='cursor:pointer' onclick='AccountDetails(\"" + item.id + "\",\"incomes\")' >Incomes</td>";
            // Content_Pa_Deposits_Others +="<td style='cursor:pointer' onclick='AccountDetails(\"" + item.id + "\",\"expenses\")' >Expenses</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#AccountingCashOnHandTable').DataTable( {
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
                total_amount = api
                    .column( 2, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 2 ).footer() ).html(
                    '<span class="text-green">'+formatNumber(total_amount)+' '+schoolCurrency+'</span>'
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
                content+= '<a data-target="#Model_DashPaNewBankAccount" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Bank Account</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>\
      <div class="modal fade" id="Model_DashPaExpenseDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
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
      </div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();
  //2 represent the Banks Accounts
  var url = base_url+"accounting/assets?type=2";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="AccountingBankAccountTable" ><thead><tr ><th class="text-center">Bank</th><th class="text-center">Account Number</th><th class="text-center">Starting Amount</th><th class="text-center">Current Amount</th><th class="text-center">Internal</br>Description</th><th class="text-center">Public</br>Description</th><th class="text-center">Operations</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th style="text-align:right"></th><th colspan="3"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //console.log(data);
      $.each( data, function(i, item) {
        
        var StartingAmount = parseFloat(item.startingAmount); 
        var TotalCredit    = parseFloat(item.TotalCredit); 
        var TotalDebt      = parseFloat(item.TotalDebt); 
        var CurrentAmount  = StartingAmount + TotalCredit + TotalDebt ;

          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others += "<td><span style='width:100%' class='AccountingBankXeditable' data-type='text' data-pk='1' data-title='Enter Fault' data-url='accounting/assetsBank/"+item.id+"?UpdateType=1' >"+item.bankName+"</span></td>"
            //Content_Pa_Deposits_Others +="<td >"+item.bankName+"</td>";
            Content_Pa_Deposits_Others += "<td><span style='width:100%' class='AccountingBankXeditable' data-type='text' data-pk='1' data-title='Enter Fault' data-url='accounting/assetsBank/"+item.id+"?UpdateType=2' >"+item.bankAccountNumber+"</span></td>"
            
            //Content_Pa_Deposits_Others +="<td >"+item.bankAccountNumber+"</td>";
            Content_Pa_Deposits_Others += "<td class='text-right'><span style='width:100%' class='AccountingBankXeditable' data-type='text' data-pk='1' data-title='Enter Fault' data-url='accounting/assetsBank/"+item.id+"?UpdateType=3' >"+formatNumber(StartingAmount)+"</span></td>"

            //Content_Pa_Deposits_Others +="<td >" + formatNumber(StartingAmount) + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(CurrentAmount) + "</td>";

            Content_Pa_Deposits_Others += "<td class='text-left'><span style='width:100%' class='AccountingBankXeditable' data-type='text' data-pk='1' data-title='Enter Fault' data-url='accounting/assetsBank/"+item.id+"?UpdateType=4' >"+item.internalDescription+"</span></td>"
            Content_Pa_Deposits_Others += "<td class='text-left'><span style='width:100%' class='AccountingBankXeditable' data-type='text' data-pk='1' data-title='Enter Fault' data-url='accounting/assetsBank/"+item.id+"?UpdateType=5' >"+item.publicDescription+"</span></td>"


            // Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.internalDescription + "</td>";
            // Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.publicDescription + "</td>";
            
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.numberOperations + "</td>";

            // Content_Pa_Deposits_Others +="<td style='cursor:pointer' onclick='AccountDetails(\"" + item.id + "\",\"incomes\",\"bank\")' >Incomes</td>";
            // Content_Pa_Deposits_Others +="<td style='cursor:pointer' onclick='AccountDetails(\"" + item.id + "\",\"expenses\",\"bank\")' >Expenses</td>";
          
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#AccountingBankAccountTable').DataTable( {
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
                        .column( 3, {search:'applied'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                        
                     // Update footer
                    $( api.column( 3 ).footer() ).html(
                        '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
                    );

          }
        } );

        $.fn.editable.defaults.ajaxOptions  = {type: "PUT" };
        $.fn.editable.defaults.mode         = 'inline';

        $('.AccountingBankXeditable').editable({
          emptytext: 'Click to add',
              success: function(response, newValue) {
                  if(response.status == 'error') return response.msg;
              }
        });



    })
    .fail(function() {
    })
    .always(function() {

    });
  //activate the found button now
  //add function to validate the form and manage the submission
  //validateNewBankItem();
}
function load_acc_Parent_Profile(){
  var studentId = 1;
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="grid-body">\
                      <div class="row">\
                        <div class="row">\
                          <div class="col-sm-12">\
                            <div class="row text-center">\
                              <div class="col-sm-8">\
                                <h4>Payent History for Students </h4>\
                              </div>\
                              <div class="col-sm-4">\
                                <a data-target="#Model_DashPaNewMoneyTransfer" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Movement</a>\
                              </div>\
                            </div>\
                            <div class="row">\
                              <div class="col-sm-12">\
                                <div id="pa_Expense_ExpenseType">\
                                </div>\
                              </div>\
                            </div>\
                          </div>\
                        </div>\
                      </div>\
                    </div>\
                        \
      ';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();
  //4 all money movement between school accounts
  var url = base_url+"accounting/sumary/" + studentId;

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="AccountingCashOnHandTable" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Debitor Account</th><th class="text-center">Creditor Account</th><th class="text-center">Amount</th><th coslpan=2>Details</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th style="text-align:right">Total:</th><th colspan="" style="text-align:right"></th><th colspan="" style="text-align:right"></th><th colspan=2></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //console.log(data);
     /* $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.date + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.Debitor + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.Creditor + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.amount) + "</td>";
            Content_Pa_Deposits_Others +="<td style='cursor:pointer' onclick='UpdateMovement(\"" + item.id + "\",\"edit\")' >Edit</td>";
            Content_Pa_Deposits_Others +="<td style='cursor:pointer' onclick='DeleteMovement(\"" + item.id + "\",\"remove\")' >Remove</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });*/

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#AccountingCashOnHandTableID').DataTable( {
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
                  .column( 1 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 1 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

      }
    } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  //autorise the modal to be triggered programaticalyy
  $('#Model_DashPaUpdateMovement').modal({ show: false});
  //activate the form submission
  
}

function load_acc_Assets_Money_Transfer(){
  
  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-8">';
                content+= '<h4>School Money Movement</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a data-target="#Model_DashPaNewMoneyTransfer" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Movement</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType"></div>';
              content+= '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>\
      <div class="modal fade" id="Model_DashPaUpdateMovement" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
        <div class="modal-wrapper">\
          <div class="modal-dialog">\
            <div class="modal-content">\
              <div class="modal-header">\
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                <h4 class="modal-title text-left" id="myModalLabel22">New Expense</h4>\
              </div>\
              <form id="formDashPaUpdateMoneyMovement" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                <input type="hidden" name="_method" value="PUT" /> \
                <div class="modal-body" id="ModalDataLoadingNow">\
                  \
                </div>\
                <div class="modal-footer" >\
                  <div class="pull-right btn-group">\
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                    <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Update Movement</button>\
                  </div>\
                </div>\
              </form>\
            </div>\
          </div>\
        </div>\
      </div>';

      dashPayments_content.html( content );

  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();
  //4 all money movement between school accounts
  var url = base_url+"accounting/assets/4";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="AccountingCashOnHandTableID" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Debitor Account</th><th class="text-center">Creditor Account</th><th class="text-center">Amount</th><th>Memo</th><th colspan=2>Details</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th style="text-align:right">Total:</th><th colspan="" style="text-align:right"></th><th colspan="" style="text-align:right"></th><th colspan=2></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //console.log(data);
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.date + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.Debitor + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.Creditor + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.amount) + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.memo + "</td>";
            Content_Pa_Deposits_Others +="<td style='cursor:pointer' onclick='UpdateMovement(\"" + item.id + "\",\"edit\")' ><i class='fa fa-pencil text-blue'></i></td>";
            Content_Pa_Deposits_Others +="<td style='cursor:pointer' onclick='DeleteMovement(\"" + item.id + "\",\"remove\")' ><i class='fa fa-remove text-red'></i></td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#AccountingCashOnHandTableID').DataTable( {
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
                  .column( 1 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 1 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

      }
    } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  //autorise the modal to be triggered programaticalyy
  $('#Model_DashPaUpdateMovement').modal({ show: false});
  //activate the form submission
  
  
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
                content+= '<a data-target="#Model_DashPaNewFixedAsset" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Fixed Asset</a>';
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

  var url = base_url+"accounting/assets/3";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="AccountingAssetFixedAssetListTable" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Asset</th><th class="text-center">Amount</th><th class="text-center">Deprec[%]</th><th class="text-center">Depreciation</th><th class="text-center">Total Deprec.</th><th class="text-center">Net</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="6" style="text-align:right">Total:</th><th style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.AssetID+">";
            Content_Pa_Deposits_Others +="<td >"+item.FirstValueDate+"</td>";
            Content_Pa_Deposits_Others +="<td >"+item.AssetName+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.StartingAmount)+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.AnnualDepreciationRate+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.DepreciationAmount)+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.TotalDepreciationAmount)+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.StartingAmount - item.TotalDepreciationAmount)+"</td>";
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
                  .column( 6 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 6 ).footer() ).html(
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
                //content+= '<a data-target="#Model_DashPaNewDebt" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Capital/Equity</a>';
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

/* Load the client form function */
function load_Accounting_New_Vendor(){
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
                content+= '<h4>New Vendor</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewVendorList class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;Vendor List</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                \
                      <form id="formDashPaNewVendor" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <input type=hidden name=_method value=POST />\
            <div class="modal-body" >\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Name</div>\
                <div class="col-sm-7">\
                  <input name="vendorName" type="text" id="paNewVendorName" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Phone</div>\
                <div class="col-sm-7">\
                  <input name="vendorPhone" type="text" id="paNewVendorPhone" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Email</div>\
                <div class="col-sm-7">\
                  <input name="vendorEmail" type="text" id="paNewVendorEmail" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Contact Person</div>\
                <div class="col-sm-7">\
                  <input name="vendorContactPerson" placeholder="Contact Person Name" type="text" id="paNewVendorContact" class="form-control" >\
                </div >\
              </div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Vendor</button>\
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
  ///AccountingNewVendorJS();
  
  //validate the new Vendor Form
  validateNewVendor();
}
function load_Accounting_New_Student_On_Fees(){
  var table = $("#pa_AccountingStudentListTable tbody");
  
  //add the row to this table now
  table.append("<tr><td>Student Name</td><td>Class</td><td>Paid</td><td>Remain</td><td>Due</td><td><img src='packages/assets/img/tick_2.png' style='width:15px;' alt='save' /></td></tr>");
}
function saveNewStudentToFeePayment(){
  
  //alert("Add New Student fired!");
  //var table = $("#pa_AccountingStudentListTable tbody");
  
  //add the row to this table now
  //table.append("<tr><td>Student Name</td><td>Class</td><td>Paid</td><td>Remain</td><td>Due</td><td><img src='packages/assets/img/tick_2.png' style='width:15px;' alt='save' /></td></tr>");
}

/* Load the client form function */
function load_Accounting_Expense_New_Type(){
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
                content+= '<h4>Expense Type</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingExpenseListType class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">\
        <div id="pa_Expense_ExpenseType">\
                \
                      <form id="formDashPaNewIncome" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
              <div class="form-group" > \
                            <div class="col-sm-3 text-right">Client</div>\
                            <div class="col-sm-7">\
                              <select name="invoiceClient" class="form-control" id="paNewInvoiceClient" placeholder="Choose Client Name" >\
                                <option></option>\
                                <option value="Add">New Client</option>\
                                <option value="1" > Client 1</option>\
                                <option value="2" > Client 2</option>\
                              </select>\
                            </div >\
                          </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Invoice Nr.</div>\
                <div class="col-sm-7">\
                  <input name="invoiceNumber" type="text" id="paNewInvoiceNumber" class="form-control" >\
                </div >\
              </div>\
              <div class="form-group" > \
                <div class="col-sm-3 text-right">Invoice Date</div>\
                <div class="col-sm-7">\
                  <input name="invoiceDate" type="text" id="paNewInvoiceDate" class="form-control" >\
                </div >\
              </div>\
              <div class="form-group" > \
                <div class="col-sm-3 text-right">Payment Date</div>\
                <div class="col-sm-7">\
                  <input name="paymenteDate" type="text" id="paNewInvoicePaymentDate" class="form-control" >\
                </div >\
              </div>\
                        </div>\
              <div class="form-group" id="InvoiceProductItems" > \
                <div class="col-sm-3">\
                  Product\
                </div >\
                <div class="col-sm-3">\
                  Amount\
                </div >\
                <div class="col-sm-3">\
                  Tax\
                </div >\
                <div class="col-sm-3">\
                  Final Amount\
                </div >\
              </div>\
              <div id="AccountingNewInvoiceItems"> \
                <div class="form-group" id="InvoiceProductItems" > \
                  <div class="col-sm-3">\
                    <select name="invoiceItemName" class="form-control paNewInvoiceItemProduct" id="paNewInvoiceItemName" placeholder="Choose Product" >\
                      <option></option>\
                      <option data-toggle="modal" data-target="#Model_DashPaNewExpense">Add new</option>\
                      <option value="1" > Product 1</option>\
                      <option value="2" > Product 2</option>\
                    </select>\
                  </div >\
                  <div class="col-sm-3">\
                    <input name="product1" type="text" id="paNewInvoicePaymentDate" class="form-control" >\
                  </div >\
                  <div class="col-sm-3">\
                    <select name="invoiceItemTax" class="form-control paNewInvoiceItemTax" id="paNewInvoiceItemTax" placeholder="Choose Taxt" >\
                      <option></option>\
                      <option data-toggle="modal" data-target="#Model_DashPaNewExpense">New Tax</option>\
                      <option value="1" > VAT 18%</option>\
                    </select>\
                  </div >\
                  <div class="col-sm-3">\
                    <input name="product1" type="text" id="paNewInvoicePaymentDate" class="form-control" >\
                  </div >\
                </div>\
              </div>\
              <img id="paAccountNewInvoiceItemAdd" alt="New Item" style="float:left; margin-left:5px; cursor:pointer" src="packages/assets/img/add.png" />\
            \
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
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
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  
  /* Activate the new invoice buttons */
  AccountingNewInvoiceJS();
}

/* Load the client form function */
function load_Accounting_Expense_New_Loan(){
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
                content+= '<h4>New Loan Received</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingExpenseListLoan class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">\
        <div id="pa_Expense_ExpenseType">\
                \
                      <form id="formDashPaNewLoanToPay" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Loan Name</div>\
                <div class="col-sm-7">\
                  <input name="loanName" type="text" id="paNewInvoiceNumber" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Institution Provider</div>\
                <div class="col-sm-7">\
                  <input name="provider" type="text" id="paNewInvoiceNumber" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Description</div>\
                <div class="col-sm-7">\
                  <input name="description" type="text" id="paNewInvoiceNumber" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Amount</div>\
                <div class="col-sm-7">\
                  <input name="totalAmount" type="text" id="paNewInvoiceNumber" class="form-control" >\
                </div >\
              </div>\
              <div class="form-group" > \
                            <div class="col-sm-3 text-right">Payment Mode</div>\
                            <div class="col-sm-7">\
                              <select name="loanPaymentMode" class="form-control" id="AccountingLoanPaymentMode" placeholder="Choose Payment Mode" >\
                                <option></option>\
                                <option value="1" > Monthly</option>\
                                <option value="2" > 3 Month</option>\
                                <option value="3" > 6 Month</option>\
                                <option value="4" > Yearly</option>\
                              </select>\
                            </div >\
                          </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Installment</div>\
                <div class="col-sm-7">\
                  <input name="installmentAmount" type="text" id="paNewInvoiceNumber" class="form-control" >\
                </div >\
              </div>\
              <div class="form-group" > \
                <div class="col-sm-3 text-right">Interest rate[%]</div>\
                <div class="col-sm-7">\
                  <input name="loanInterest" type="text" id="AccountingLoanInterest" class="form-control" >\
                </div >\
              </div>\
              <div class="form-group" > \
                <div class="col-sm-3 text-right">First Payment Date</div>\
                <div class="col-sm-7">\
                  <input name="firstPaymentDate" type="text" id="AccountingFirstPaymentDate" class="form-control" >\
                </div >\
              </div>\
              <div class="form-group" > \
                <div class="col-sm-3 text-right">Last Payment Date</div>\
                <div class="col-sm-7">\
                  <input name="lastPaymentDate" type="text" id="AccountingLastPaymentDate" class="form-control" >\
                </div >\
              </div>\
                        </div>\
            <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Loan</button>\
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
  
  /* Activate the new invoice buttons */
  AccountingNewLoanJS();
}

/* Load the Invoice List function */
function load_Accounting_Income_Invoice(){

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
                content+= '<h4>Invoice Payments</h4>';
              content+= '</div><!--';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewInvoice class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;New Invoice&nbsp;</a>';
              content+= '</div>-->';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12 text-center">\
                <div id="pa_Expense_ExpenseType">\
                Invoice payements loading...\
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
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  var url = base_url + "accounting/transactions?type=incomesInvoice";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingTransactionIncomesInvoice" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Client</th><th class="text-center">Invoice Number</th><th class="text-center">Amount</th><th class="text-center">Invoice Date</th><th class="text-center">Deposit in</th><th class="text-center">Action</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      $.each( data, function(i, item) {

           Content_Pa_Deposits_Others +="<tr id="+item.id +" >";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ date_moment(item.date) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.clientName + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.invoiceNumber + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.paidAmount) + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ date_moment(item.invoiceDate) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.deposedIn +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center AccIncomesInvoiceDelete'><a href='#'><i class='fa fa-times text-red'></i></a></td>"; 
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      
      $( "table#pa_AccountingTransactionIncomesInvoice" ).delegate( "td.AccIncomesInvoiceDelete", "click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          var id          = $(this).closest('tr').attr('id'); 
          console.log(id);

          // loadClassroomDelete(id , classname, $(this) );

      });

      // $( "table#pa_AccountingTransactionIncomesInvoice" ).delegate( "td.acIncomeTransEdit", "click", function(e) {

      //   e.preventDefault();
      //   e.stopPropagation();

      //   var transaction_id  = $(this).closest('tr').attr('id');
      //   var type_id         = $(this).closest('tr').attr('type');
      //   var clientId        = $(this).closest('tr').attr('clientId');

      //   loadTransactionEdit( 2, type_id, clientId, transaction_id, $(this) );

      // });


    }).done(function() {

        $('#pa_AccountingTransactionIncomesInvoice').DataTable( {
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
                  .column( 3, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+schoolCurrency+' '+formatNumber(total_Paid)+'</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}

/* Load the Invoice List function */
function load_Accounting_Income_SchoolFees(){

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
                content+= '<h4>School Payments</h4>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12 text-center">\
                <div id="pa_Expense_ExpenseType">\
                School Fees Payments loading...\
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
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  var url = base_url + "accounting/transactions?type=incomesSchoolFees";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingTransactionIncomesSchoolFees" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Amount</th><th class="text-center">Deposit in</th><th class="text-center">Fees Paid</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th colspan="5" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      $.each( data, function(i, item) {

           Content_Pa_Deposits_Others +="<tr id="+item.id +" studRegNber= "+item.studRegNber+">";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ date_moment(item.date) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.studentNames + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.studRegNber + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.ClassName + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.paidAmount) + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.deposedIn +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.distribution +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center acIncomeTransEdit'><a href='#'><i class='fa fa-pencil text-green'></i></a></td>";
            Content_Pa_Deposits_Others +="<td class='text-center AccIncomesSchoolFeesDelete'><a href='#'><i class='fa fa-times text-red'></i></a></td>"; 
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      
      $( "table#pa_AccountingTransactionIncomesSchoolFees" ).delegate( "td.AccIncomesSchoolFeesDelete", "click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          // var id = $(this).closest('tr').attr('id'); 
          var transaction_id  = $(this).closest('tr').attr('id');
          var studRegNber     = $(this).closest('tr').attr('studRegNber');

          console.log("Deleting " + transaction_id + " Paid by " + studRegNber);

      });

      $( "table#pa_AccountingTransactionIncomesSchoolFees" ).delegate( "td.acIncomeTransEdit", "click", function(e) {

        e.preventDefault();
        e.stopPropagation();

        var transaction_id  = $(this).closest('tr').attr('id');
        var studRegNber     = $(this).closest('tr').attr('studRegNber');

        loadTransactionEdit( 1, 1, studRegNber, transaction_id, $(this) );

      });


    }).done(function() {

        $('#pa_AccountingTransactionIncomesSchoolFees').DataTable( {
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
                  .column( 4, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+schoolCurrency+' '+formatNumber(total_Paid)+'</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });

}



/* Load the Invoice List function */
function load_Accounting_School_Fees(){
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
            content+= '<div class="col-sm-3" style="display:table; height:350px;">';
              content+='<div class="row text-center" style="border:0px solid #000; display:table-cell; vertical-align:middle; text-align:left; ">';
                content+= '<div>\
                  <ul class="nav nav-pills nav-stacked">\
                    <li class="FeesCurrentFees">School Fees</li>\
                    <li id="CurrentFees" class="active left_menu">\
                      <a href="#" id="AccountingSchoolFeesCurrentFees"><i class="fa fa-money"></i> Current term fees </a>\
                    </li>\
                    <li id="StudentWithoutFees" class="left_menu">\
                      <a href="#" id="AccountingStudentWithoutFees"><i class="fa fa-exclamation-triangle"></i>Students without any Fee this term</a>\
                    </li>\
                    <li id="FeeType" class="left_menu">\
                      <a href="#" id="AccountingSchoolFeesFeeType"><i class="fa fa-list-ol"></i>Fee Types</a>\
                    </li>\
                    <li id="FeesDebts" class="left_menu">\
                      <a href="#" id="AccountingSchoolFeesDebts"><i class="fa fa-archive"></i>Debt</a>\
                    </li>\
                    <li id="OverDuePayment" class="left_menu">\
                      <a href="#" id="AccountingSchoolFeesOverDuePayment"><i class="fa fa-clock-o"></i>Overdue Payments</a>\
                    </li>\
                    <li id="FeesUnAssigned" class="left_menu">\
                      <a href="#" id="AccountingSchoolFeesUnAssigned"><i class="fa fa-plus-square"></i>Un-assigned</a>\
                    </li>\
                    <li id="PaymentReminder" class="left_menu">\
                      <a href="#" id="AccountingSchoolPaymentReminder"><i class="fa fa-paper-plane"></i>Send Payments Reminder</a>\
                    </li>\
                  </ul>\
                </div>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="col-sm-9" id="pa_AccountingSchoolFeesContainer">';
              content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
                content+= '<h4>Current Term Fees (This term)</h4>';
                content+= '<div class="row" >';
                  content+= ' \
                        <div id="pa_Expense_ExpenseType">\
                          School Fees\
                        </div>\
                      </div>';
                  content+= '</div>';
                content+= '</div>';
            content+= '</div>';
            
          content+= '</div>\
        </div>\
      </div></div></div></div>';
    
  
  //send the prepared content to the screen for user
  dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  
  /* Activate the new invoice buttons */
  AccountingSchoolFeesJS();
}

/* Load the Invoice List function */
function load_Accounting_Transaction_MobiCash(){
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
                content+= '<h4>Mobi Cash Transactions</h4>';
              content+= '</div><!--';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewInvoice class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;New Invoice&nbsp;</a>';
              content+= '</div>-->';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">\
                <div id="pa_Expense_ExpenseType">\
                  Invoice List Here\
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
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  var url = base_url + "accounting/transactions?type=mobicash";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingTransactionMobiCash" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Student</th><th class="text-center">Reg Number</th><th class="text-center">Class</th><th class="text-center">Amount</th><th class="text-center">Paid By</th><th class="text-center">Phone</th><th class="text-center">Reference</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {

          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ date_HM_moment(item.date) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.studentNames + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.studRegNber + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.className + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.amount) + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.senderName + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.senderMobilePhoneNumber + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.referenceNumber +" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_AccountingTransactionMobiCash').DataTable( {
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
                  .column( 4, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of the ajax request here */
  /* Activate the new invoice buttons */
  AccountingListInvoiceJS();
}

/* This allow the update to movement */
function UpdateMovement(movementId){
  //modify the content before display the modal with data from the database
  var modal_container = $("#ModalDataLoadingNow");//.html("Data Modified And Every Thing is fine!");
  var content = '<div id="pa_Fee_Student_List">\
        Movement Update Form Here\
      </div>';
  /* Send Ajax request to return the list of student who are involved in paying */
  //ajust the content now
  modal_container.html(content);
  var url = base_url + "accounting/moneymovement/" + movementId + "/edit";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<div id="">';
      //$.each(data.FeeType, function(i, item){
    $("#myModalLabel22").html("Update the Movement Information");
   // });
   //console.log(data.Students);
     // $.each( data.Students, function(i, item) {
    /* This precess the result after running the command */
    
    /* End of the form to allow movement information editing */
     // });
    //now complete the form information form the database
    var formContent = '<div class="form-group" > \
                <div class="col-sm-3 text-right">From</div>\
                <div class="col-sm-7">\
                  <select name="debitorId" class="form-control" id="AccountingUpdateMoneyTransferFrom" placeholder="Choose Debitor Account" >\
                    <optgroup label="School Staff">';
                    /* load the of staff account here to be allow selected */
                    $.each(data.staffaccounts, function(i, item){
                      formContent += "<option value='" + item.schoolMoneyAccountId + "' " + (item.schoolMoneyAccountId == data.currentContent.fromId?"selected":"") + ">" + item.firstname + " " + item.middleName + " " + item.lastName + "</option>";
                    });
                    /* End bank account display option */
            formContent += '</optgroup>\
                    <optgroup label="Bank Accounts">';
                    /* load the of bank account here to be allow selected */
                    $.each(data.bankaccounts, function(i, item){
                      formContent += "<option value='" + item.schoolMoneyAccountId + "' " + (item.schoolMoneyAccountId == data.currentContent.fromId?"selected":"") + ">" + item.accountNumber + " " + item.BankName + "</option>";
                    });
                    /* End bank account display option */
            formContent += '</optgroup>\
                  </select>\
                </div >\
              </div>\
              <div class="form-group" > \
                <div class="col-sm-3 text-right">To</div>\
                <div class="col-sm-7">\
                  <select name="creditorId" class="form-control" id="AccountingUpdateMoneyTransferTo" placeholder="Choose Creditor Account" >\
                    <optgroup label="School Staff">';
                    /* load the of staff account here to be allow selected */
                    $.each(data.staffaccounts, function(i, item){
                      formContent += "<option value='" + item.schoolMoneyAccountId + "' " + (item.schoolMoneyAccountId == data.currentContent.toId?"selected":"") + ">" + item.firstname + " " + item.middleName + " " + item.lastName + "</option>";
                    });
                    /* End bank account display option */
             formContent += '</optgroup>\
                    <optgroup label="Bank Accounts">';
                    /* load the of bank account here to be allow selected */
                    $.each(data.bankaccounts, function(i, item){
                      formContent += "<option value='" + item.schoolMoneyAccountId + "' " + (item.schoolMoneyAccountId == data.currentContent.toId?"selected":"") + ">" + item.accountNumber + " " + item.BankName + "</option>";
                    });
                    /* End bank account display option */
             formContent += '</optgroup>\
                  </select>\
                </div >\
              </div>\
              <div class="form-group" > \
                <div class="col-sm-3 text-right">Amount</div>\
                <div class="col-sm-7">\
                  <input name="movedAmount" type="text" id="paUpdateMovedMoney" class="form-control" value="' + data.currentContent.amount + '" >\
                </div >\
              </div>\
              <div class="form-group" > \
                <div class="col-sm-3 text-right">Date</div>\
                <div class="col-sm-7">\
                  <input name="movementDate" type="text" id="AccountingUpdateMoneyMovementDate" class="form-control" value="' + data.currentContent.date + '" >\
                </div >\
              </div>\
              <div class="form-group" > \
                <div class="col-sm-3 text-right">Memo</div>\
                <div class="col-sm-7">\
                  <input name="movementMemo" type="text" id="AccountingUpdateMoneyMovementMemo" class="form-control" value="' + data.currentContent.memo + '" >\
                </div >\
              </div>';
      Content_Pa_Deposits_Others += formContent + "</div>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {
    /* This will be executed if the fetch operation ends on the server side */
    validateUpdateCashMovement(movementId);
    //alert("Received!");
    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of Ajax Request */
  $('#Model_DashPaUpdateMovement').modal({ show: true});
  /* Validate the form to allow form submission and storage update */
  //AccountingStudentOnFeeJS();
  
  //alert($("#paAccountingNewStudentOnFee").attr('src'));
}

/* Current Fee Student List should appear as modal */
function ExpenseDetails(ExpenseID){
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
  var url = base_url + "accounting/expenses/" + ExpenseID;

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<div id="">\
                  <table class="table table-bordered abDataTable" id="pa_AccountingStudentListTable" ><thead><tr ><!--<th class="text-center">Date</th>--><th class="text-center">Item</th><!--<th class="text-center">Memo</th>--><th class="text-center">Amount</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th style="text-align:left">&nbsp;<!--<img id="paAccountingNewStudentOnFee" src="packages/assets/img/add.png" alt="new" />-->Total</th><th class="text-right"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //$.each(data.FeeType, function(i, item){
   // });
   //console.log(data.Students);
   $.each( data, function(i, item) {
    $("#myModalLabel22").html("Budget for " + item.categoryName);
      
      $.each(item.ExpenseData, function(k, dt){
        Content_Pa_Deposits_Others +="<tr>";
        //Content_Pa_Deposits_Others +="<td class='text-center'> </td>"; 
        Content_Pa_Deposits_Others +="<td class='text-left'>" + dt.name +"</td>";
        //Content_Pa_Deposits_Others +="<td class='text-right'> </td>";
        Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(dt.amount) + "</td>";
        Content_Pa_Deposits_Others +="</tr>";
      });
          
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
                  .column( 1 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 1 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of Ajax Request */
  $('#Model_DashPaExpenseDetails').modal({ show: true});
  /* Activate Button that are the appered modal */
  AccountingStudentOnFeeJS();
  //alert($("#paAccountingNewStudentOnFee").attr('src'));
}

/* Current Fee Student List should appear as modal */
function LoanPaymentDetails(LoanID){
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
  var url = base_url + "accounting/loans/" + LoanID + "/edit";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<div id="">\
                  <table class="table table-bordered abDataTable" id="pa_AccountingStudentListTable" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Period</th><th class="text-center">Amount</th><th class="text-center">Interest</th><th class="text-center">Penality</th><th class="text-center">Total</th><th class="text-center">Memo</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="2" style="text-align:left">&nbsp;Total</th><th class="text-right"></th><th class="text-right"></th><th class="text-right"></th><th class="text-center" colspan=2></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //$.each(data.FeeType, function(i, item){
    $("#myModalLabel22").html("Loan:" + data.ExpenseType.name + "<br />Description:" + data.ExpenseType.description + "<br />Amount:<span style='color:green;'>" + formatNumber(data.ExpenseType.amount) + "</span><br />Remain:<span style='color:red;'>" + formatNumber(data.ExpenseType.paid) + "</span>");
   // });
   //console.log(data.Students);
   $.each( data.Expenses, function(i, item) {
      
    months = ["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"];
    timestamp = "1374267600";
    var jsDate = new Date(item.Date * 1000);


          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ jsDate.toDateString() + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ item.period +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.amount) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.interest) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.penality) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber( (1*item.amount) + (1*item.interest) + (1*item.penality)) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + item.memo +" </td>";
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
                  .column( 5 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
              total_amount = api
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
              total_interest = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
              total_penalities = api
                  .column( 4 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 5 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' RWF</span>'
              );
              $( api.column( 2 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_amount)+'</span>'
              );
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_interest)+'</span>'
              );
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_penalities)+' </span>'
              );
      }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of Ajax Request */
  $('#Model_DashPaExpenseDetails').modal({ show: true});
  /* Activate Button that are the appered modal */
  AccountingStudentOnFeeJS();
  //alert($("#paAccountingNewStudentOnFee").attr('src'));
}

/* Current Fee Student List should appear as modal */
function AccountDetails(AccountID, request, accountType='cash'){
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
  var url = base_url + "accounting/assets/" + accountType + "/" + AccountID + "/" + request;

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Fee_Student_List');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<div id="">\
                  <table class="table table-bordered abDataTable" id="pa_AccountingStudentListTable" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Description</th><th class="text-center">Amount</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="2" style="text-align:left">&nbsp;Total</th><th style="text-align:right"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //$.each(data.FeeType, function(i, item){
    $("#myModalLabel22").html("Account:" + data.AccountID.AccountName + "<br />Starting Amount:" + formatNumber(data.AccountID.StartingAmount) + "RWF<!--<br />Current Amount:" + formatNumber(data.AccountID.CurrentAmount) + "RWF--><br />" + data.AccountID.ResultFor);
   // });
   //console.log(data.Students);
   $.each( data.Datails, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.OperationDate + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.Description +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.Amount) +" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });
      

      Content_Pa_Deposits_Others +="</tbody></table></div>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_AccountingStudentListTable').DataTable( {
      "order": [[ 0, "desc" ]],
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
                  .column( 2 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 2 ).footer() ).html(
                  '<span class="text-green">'+formatNumber(total_Paid)+' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of Ajax Request */
  $('#Model_DashPaExpenseDetails').modal({ show: true});
  /* Activate Button that are the appered modal */
  AccountingStudentOnFeeJS();
  //alert($("#paAccountingNewStudentOnFee").attr('src'));
}

/* Load the Invoice List function */
function load_Accounting_School_Fees_Current_Fees(){
  //try to change the style sheet and check
  $(".left_menu").removeClass('active');
  $("#CurrentFees").addClass('active');
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
            content+= '<div class="col-sm-6">';
              content+= '<h4>Current Term Fees</h4>';
            content+= '</div>';
            content+= '<div class="col-sm-6">';
              content+= '<a id=AccountingSchoolFeesNewCurrentFeesPayment class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Term Fee</a>';
              content+='<a href="'+ base_url + 'accounting/schoolfees/downloads/0" title="Download Current Status" target="_blank" class="btn btn-default pull-right"><i class="fa fa-file-excel-o text-blue"></i>&nbsp;&nbsp;All Current Term Fees in Excel</a>&nbsp;&nbsp;';
            content+= '</div>';
          content+= '</div>';
          content+= '<div class="row" ><div class="col-sm-12" >\
                  <div id="pa_Expense_ExpenseType">\
                    Loading...\
                  </div>\
                </div></div>\
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
  
  var url = base_url + "accounting/schoolfees/currentfee";

  var Content  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content += '<table class="table table-bordered abDataTable" id="Acc_AccountingCurrentFeeListTable" ><thead><tr ><th class="text-center">Fee Type</th><th class="text-center">Description</th><th class="text-center">Amount</th><th class="text-center">Student</th><th class="text-center">To Pay</th><th class="text-center">Paid</th><th class="text-center">Remain</th><th class="text-center">Remove</th></tr></thead>';
      Content += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th></tr></tfoot>';
      Content += '<tbody>';

      $.each( data, function(i, item) {

          Content +="<tr id="+item.id +">";
            Content +="<td class='text-left'>" + item.name + "</td>"; 
            Content +="<td class='text-left'><span style='width:100%' class='AccCurrentFeeXeditable' data-type='text' data-pk='1' data-title='Enter description' data-url='accounting/schoolfees/currentfee/"+item.id+"?type=2' >"+item.description+"</span></td>";
            Content +="<td class='text-right'><span style='width:100%' class='AccCurrentFeeXeditable' data-type='text' data-pk='1' data-title='Enter amount' data-url='accounting/schoolfees/currentfee/"+item.id+"?type=3' >"+formatNumber(item.amount)+"</span></td>";
            Content +="<td class='text-right'><a href='#' onclick = 'LoadCurrentDetailsInformation(\"" + item.id + "\",\"" + item.name + "\",\"" + item.description + "\",\"" + item.amount + "\",\"" + item.dueDate + "\")' >" + item.numberOfStudent +"&nbsp;<i class='fa fa-align-right text-green'></i>&nbsp;&nbsp;<a href='" + base_url + "accounting/schoolfees/downloads/" + item.id + "' title='Download Current Status' target='_blank'><i class='fa fa-file-excel-o text-blue'></i></a></td>";
            Content +="<td class='text-right'>"+ formatNumber((total = (item.amount * item.numberOfStudent))) +"</td>";
            Content +="<td class='text-right'>"+ formatNumber(item.amount_paid) +" </td>";
            Content +="<td class='text-right'>"+ formatNumber((total - item.amount_paid)) +" </td>";
            if ( item.amount_paid > 0 ) {
               Content +='<td></td>';

            }else{
              Content +='<td class="text-center AccCurrentfeeListListDelete"><a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a></td>';
            
            }
          Content +="</tr>";
          
      });

      Content +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content );
      
      $.fn.editable.defaults.ajaxOptions = {type: "PUT" };
      $.fn.editable.defaults.mode = 'inline';

      $('.AccCurrentFeeXeditable').editable({
        emptytext: 'Click to add',
            success: function(response, newValue) {

              if ( response.success ) {

                  load_Accounting_School_Fees_Current_Fees();

              }
              if(response.status == 'error') return response.msg;
              
          }
      });


    }).done(function() {

        $('#Acc_AccountingCurrentFeeListTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

            //To pay 
              // Total 
                total_Topay = api
                    .column( 4, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 4 ).footer() ).html(
                    '<span class="text-black">'+formatNumber(total_Topay)+' '+schoolCurrency+'</span>'
                );

            //Paid 
              // Total 
                total_Paid = api
                    .column( 5, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 5 ).footer() ).html(
                    '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
                );

            //Remain 
              // Total 
                total_Remain = api
                    .column( 6, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                // Update footer
                $( api.column( 6 ).footer() ).html(
                    '<span class="text-red">'+formatNumber(total_Remain)+' '+schoolCurrency+'</span>'
                );


          }
      } );

      $( "table#Acc_AccountingCurrentFeeListTable" ).delegate( "td.AccCurrentfeeListListDelete", "click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          var currentFeeId  = $(this).closest('tr').attr('id');
          AccDeleteCurrentFee( $(this) , currentFeeId );

     });

    })
    .fail(function() {
    })
    .always(function() {

    });

  /* End of the ajax request here */
  /* Activate the new invoice buttons */
  AccountingListSchoolFeesCurrentJS();
  $('#Model_DashPaFeeStudentList').modal({ show: false});

}

/* Load the Invoice List function */
function load_Accounting_School_Fees_Debts(){
  $(".left_menu").removeClass('active');
  $("#FeesDebts").addClass('active');
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
            content += '<div class="col-sm-6">';
              content+= '<h4>Current Un paid Debt</h4>';
            content+= '</div>';
            content+= '<div class="col-sm-6">';
              content+= '<a id=AccountingSchoolFeesNewDebt class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New</a>';
            content+= '</div>';
          content+= '</div>'
          content+= '<div class="row" ><div class="col-sm-12" >';
            content+= ' \
                  <div id="pa_Expense_ExpenseType">\
                    Loading Debt...\
                  </div>\
                </div></div>\
                 ';
        content+= '</div><div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
                <div class="modal-wrapper">\
                  <div class="modal-dialog">\
                    <form id="formPayDebtNow" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                      <div class="modal-content">\
                        <div class="modal-header">\
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                          <h4 class="modal-title text-left" id="myModalLabel22">Debt Payment</h4>\
                        </div>\
                        <div id="ModalDataLoadingNow">\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Pay Debt</button>\
                          </div>\
                        </div>\
                      </div>\
                    </form>\
                  </div>\
                </div>\
              </div>';
    
  
  //send the prepared content to the screen for user
  dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  var url = base_url + "accounting/schoolfees/debt";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="AccStudentDebtsTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Class</th><th class="text-center">Description</th><th class="text-center">To Pay</th><th class="text-center">Paid</th><th class="text-center">Remain</th><th class="text-center">Delete</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      //console.log(data);
      $.each( data, function(i, item) {
        
          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.name + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.className + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'><span style='width:100%' class='AccStudentDebtsXeditable' data-type='text' data-pk='1' data-title='Enter description' data-url='accounting/schoolfees/debt/"+item.id+"?type=1' >"+item.description+"</span></td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'><span style='width:100%' class='AccStudentDebtsXeditable' data-type='text' data-pk='1' data-title='Enter description' data-url='accounting/schoolfees/debt/"+item.id+"?type=2' >"+formatNumber(item.amount)+"</span></td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.paid) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber((item.remains)) +" </td>";
            Content_Pa_Deposits_Others +='<td class="text-center AccStudentDebtsDelete"><a href="#">&nbsp;<i class="fa fa-times text-red"></i>&nbsp;</a></td>';
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";
      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      
      $.fn.editable.defaults.ajaxOptions = {type: "PUT" };
      $.fn.editable.defaults.mode = 'inline';

      $('.AccStudentDebtsXeditable').editable({
        emptytext: 'Click to add',
            success: function(response, newValue) {

              if ( response.success ) {
                load_Accounting_School_Fees_Debts();

              }
              if(response.status == 'error') return response.msg;
              
          }
      }); 

      $( "table#AccStudentDebtsTable" ).delegate( "td.AccStudentDebtsDelete", "click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          var currentFeeId  = $(this).closest('tr').attr('id');
          AccDeleteStudentDebt( $(this) , currentFeeId );

      });


    }).done(function() {

        $('#AccStudentDebtsTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // //To pay 
              // // Total 
              //   total_Topay = api
              //       .column( 3, {search:'applied'} )
              //       .data()
              //       .reduce( function (a, b) {
              //           return intVal(a) + intVal(b);
              //       }, 0 );
                    
              //    // Update footer
              //   $( api.column( 3 ).footer() ).html(
              //       '<span class="text-black">'+formatNumber(total_Topay)+' '+schoolCurrency+'</span>'
              //   );

            //Paid 
              // Total 
                total_Paid = api
                    .column( 4, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 4 ).footer() ).html(
                    '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
                );

            //Remain 
              // Total 
                total_Remain = api
                    .column( 5, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                // Update footer
                $( api.column( 5 ).footer() ).html(
                    '<span class="text-red">'+formatNumber(total_Remain)+' '+schoolCurrency+'</span>'
                );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of the ajax request here */
  /* Activate the new invoice buttons */
  AccountingSchoolFeesDebtsJS();
}

/* Load the Invoice List function */
function load_Accounting_School_Fees_Over_Due_Payment(){
  $(".left_menu").removeClass('active');
  $("#OverDuePayment").addClass('active');
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
              content+= '<h4>Overdue Payments</h4>';
            content+= '</div>';
            content+= '<div class="col-sm-4">';
              //content+= '<a id=AccountingSchoolFeesNewOverDue class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New</a>';
            content+= '</div>';
          content+= '</div>'
          
          content+= '<div class="row" ><div class="col-sm-12" >';
            content+= ' \
                  <div id="pa_Expense_ExpenseType">\
                    Loading overdue payments...\
                  </div>\
                </div></div>\
                 ';

        content+= '</div><div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
                <div class="modal-wrapper">\
                  <div class="modal-dialog">\
                    <form id="formPayOverDueNow" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                      <div class="modal-content">\
                        <div class="modal-header">\
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                          <h4 class="modal-title text-left" id="myModalLabel22">Over Due Payment</h4>\
                        </div>\
                        <div id="ModalDataLoadingNow">\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Pay Invoice</button>\
                          </div>\
                        </div>\
                      </div>\
                    </form>\
                  </div>\
                </div>\
              </div>';
    
  
  //send the prepared content to the screen for user
  dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  var url = base_url + "accounting/schoolfees/overdue";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" ><thead><tr ><th class="text-center">Student</th><th class="text-center">Class</th><th class="text-center">Fee</th><th class="text-center">Amount</th><th class="text-center">Paid</th><th class="text-center">Remain</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.name + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.StudentClass + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.FeeType + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.amount) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.Paid) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.amount - item.Paid) +" </td>";
            // Content_Pa_Deposits_Others +="<td class='text-right'> <a href='#' onclick = 'PayOverDue(\""+ item.id +"\")' >Pay</a></td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

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

              //To pay 
              // Total 
                total_Topay = api
                    .column( 3, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 3 ).footer() ).html(
                    '<span class="text-black">'+formatNumber(total_Topay)+' '+schoolCurrency+'</span>'
                );

            //Paid 
              // Total 
                total_Paid = api
                    .column( 4, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 4 ).footer() ).html(
                    '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
                );

            //Remain 
              // Total 
                total_Remain = api
                    .column( 5, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                // Update footer
                $( api.column( 5 ).footer() ).html(
                    '<span class="text-red">'+formatNumber(total_Remain)+' '+schoolCurrency+'</span>'
                );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of the ajax request here */
  /* Activate the new invoice buttons */
  AccountingSchoolFeesOverDuePaymentJS();
}

/* Load the Invoice List function */
function load_Accounting_School_Fees_Un_Assigned(){
  $(".left_menu").removeClass('active');
  $("#FeesUnAssigned").addClass('active');

  //get the instance of container division 
  var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
  //get the instance of the content division
  var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
  
  //hide the container first
  dashClientContainer.slideUp();
  
  //prepare the content to be printed in the containner
  
  var content = '';
        content+='<div class="row text-center" id="pa_AccountingSchoolFeesData">';
          content+= '<h4>Un-assigned Students\' Payments</h4>';
      
        content+= '<div class="row" ><div class="col-sm-12" >';
            content+= ' \
                  <div id="pa_Expense_ExpenseType">\
                    Loading unassigned payments...\
                  </div>\
                </div></div>\
                 ';

  
  //send the prepared content to the screen for user
  dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  var url = base_url + "accounting/schoolfees/unassigned";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" ><thead><tr ><th class="text-center">Added on</th><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Amount</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th style="text-align:right"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.studRegNber +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ date_moment(item.created_at) + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.name + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.studRegNber + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.className + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.amount) +" </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

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

              //To pay 
                // Total 
                  total_Topay = api
                      .column( 4, {search:'applied'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                      
                // Update footer
                  $( api.column( 4 ).footer() ).html(
                      '<span class="text-black">'+schoolCurrency+' '+formatNumber(total_Topay)+'</span>'
                  );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of the ajax request here */
  /* Activate the new invoice buttons */
  AccountingListInvoiceJS();
}

/* Load the Invoice List function */
function load_Accounting_School_PaymentReminder(){

  //try to change the style sheet and check
    $(".left_menu").removeClass('active');
    $("#PaymentReminder").addClass('active');

    var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");

    var url = base_url+"accounting/schoolfees/paymentReminder/create";
      content = '';

      $.getJSON( url, function(data) {

        var Students            = data.Students;
        var classrooms          = data.classrooms;
        var boarding            = data.boarding;
        var SchoolOrganizations = data.SchoolOrganizations;
        var ToSendMessageContent= data.ToSendMessageContent;

        content += '<div class="row"><label class="col-sm-12 text-center"><h4>Remind parents to pay remaining amounts via SMS!</h4></div></div>';
        content += '<div class="row top-buffer">';
          content +='<div class="row" id="formDashAcNewReminderLoading">';
            content +='<div class="grid text-center">';
              content +='<img src="../packages/assets/plugins/img/loading.gif" alt=""> Loading, Please wait ...';
                content +='';
              content +='</div>';
            content +='</div>';
          content +='</div>';
          content += '<form role="form" id="formDashAcNewReminder" >';
            content += '<div class="form-horizontal">';
            content += '<div class="form-group" >';
              content += '<label class="col-sm-3 control-label">Students to pay</label>';
              content += '<div class="col-sm-2"><label><input type="radio" name="newReminder_studentType" value="1"  checked> All Students</label></div>';
              content += '<div class="col-sm-2"><label><input type="radio" name="newReminder_studentType" value="2"  > Custom Students</label></div>';
              content += '<div class="col-sm-2"><label><input type="radio" name="newReminder_studentType" value="3"  > Choose Students</label></div>';
            content += '</div>';
            content += '<div class="form-group" id="ac_SchoolFee_NewReminder_ExceptContainer">';
              content += '<label class="col-sm-3 control-label"> Except</label>';
              content += '<div class="col-sm-7">';
                content += '<select name="newReminder_StudentsExcept[]" class="form-control" id="newReminder_StudentsExcept" multiple>';
                  content += '<option></option>';
                  $.each( Students , function(i, item){
                    content += '<option value="'+item.personID+'">'+item.studentNames+' ('+item.studRegNber+')</option>';
                  }); 
                content += '</select>';
              content +='</div>';
            content += '</div>';
            content += '<div class="form-group" id="ac_SchoolFee_NewReminder_BoardingContainer" >';
              content += '<label class="col-sm-3 control-label"> Boarding</label>';
              if ( boarding.boardingID == 1 ) {
                content += '<div class="col-sm-3"><label><input type="checkbox" name="newReminder_studentBoarding[]" value="0" checked > Day</label></div>';
                content += '<div class="col-sm-3"><label><input type="checkbox" name="newReminder_studentBoarding[]" value="1" checked > Boarding</label></div>';

              }else if( boarding.boardingID == 2 ){
                content += '<div class="col-sm-6"><label><input type="checkbox" name="newReminder_studentBoarding[]" value="0" checked desabled > Day</label></div>';

              }else if( boarding.boardingID == 3 ){
                content += '<div class="col-sm-6"><label><input type="checkbox" name="newReminder_studentBoarding[]" value="1" checked desabled > Boarding</label></div>';
              }
            content += '</div>';
            content += '<div class="form-group" id="ac_SchoolFee_NewReminder_ClassContainer" >';
              content += '<label class="col-sm-3 control-label"> Class</label>';
              content += '<div class="col-sm-3"><label><input type="radio" name="newReminder_studentClass" value="1" checked > All Class</label></div>';
              content += '<div class="col-sm-3"><label><input type="radio" name="newReminder_studentClass" value="0" > Some Class</label></div>';
            content += '</div>';
            content += '<div class="form-group" id="ac_SchoolFee_NewReminder_SomeClassesContainer">';
              content += '<label class="col-sm-3 control-label"> Some Classes</label>';
              content += '<div class="col-sm-7">';
                content += '<select name="newReminder_SomeClasses[]" class="form-control" id="newReminder_SomeClasses" multiple>';
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
            content += '<div class="form-group" id="ac_SchoolFee_NewReminder_SponsorContainer">';
              content += '<label class="col-sm-3 control-label"> Sponsor</label>';
              content += '<div class="col-sm-2"><label><input type="checkbox" name="newReminder_studentSponsor[]" value="1" checked > Parents/Guardian</label></div>';
              content += '<div class="col-sm-2"><label><input type="checkbox" name="newReminder_studentSponsor[]" value="3" checked > School</label></div>';
              content += '<div class="col-sm-1"><label><input type="checkbox" name="newReminder_studentSponsor[]" value="4" checked > Self</label></div>';
              content += '<div class="col-sm-2"><label><input type="checkbox" name="newReminder_studentSponsor[]" value="2" checked id="newReminder_studentSponsorOrg"> Organization</label></div>';
            content += '</div>';
            content += '<div class="form-group" id="ac_SchoolFee_NewReminder_OrganizationContainer">';
              content += '<label class="col-sm-3 control-label"> Organization</label>';
              content += '<div class="col-sm-7">';
                content += '<select name="newReminder_Organization[]" class="form-control" id="newReminder_Organization" multiple >';
                  content += '<option></option>';
                  $.each( SchoolOrganizations , function(i, item){
                    content += '<option value="'+item.id+'" selected="selected">'+item.name+'</option>';
                  }); 
                content += '</select>';
              content += '</div>';
            content += '</div>';
            content += '<div class="form-group" id="ac_SchoolFee_NewReminder_SomeStudentsContainer">';
              content += '<label class="col-sm-3 control-label"> Some Students</label>';
              content += '<div class="col-sm-7">';
                content += '<select name="newReminder_StudentsSome[]" class="form-control" id="newReminder_StudentsSome" multiple>';
                  content += '<option></option>';
                  $.each( Students , function(i, item){
                    content += '<option value="'+item.personID+'">'+item.studentNames+' ('+item.studRegNber+')</option>';
                  }); 
                content += '</select>';
              content +='</div>';
            content += '</div>';
            content += '<div class="form-group" >';
              content += '<label class="col-sm-3 control-label">Parent to pay before(time) </label>';
              content += '<div class="col-sm-7">';
                content += '<input name="newReminder_dueDate" type="text" class="form-control" placeholder="Add due date" id="newReminder_dueDate">';
              content += '</div>';
            content += '</div>'
            content += '<div class="form-group">';
              content += '<div class="col-sm-3 text-right">Reminder Message</div>';
              content += '<div class="col-sm-7">';
                content += '<textarea name="message" id="newReminderContent" class="form-control" placeholder="write your message here" style="height: 120px;">'+ToSendMessageContent+'</textarea>';
              content += '</div>';
            content += '</div>';
            content += '<div class="form-group" >';
              content += '<label class="col-sm-3 control-label"></label>';
              content += '<div class="col-sm-7 text-right">';
                content += '<button type="submit" class="btn btn-success" id="paNewPaSubmit" >Send SMS Reminder</button>';
              content += '</div>';
            content += '</div>';
          content += '</div>';
        content += '</form>';
        content += '</div>';

      }).done(function() {

        dashClientContainer.html(content);
        $("#formDashAcNewReminderLoading").hide();
        
        ac_SchoolFee_NewPaymentReminderForm();

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

/* Load the Invoice List function */
function load_Accounting_StudentWithoutFees(){

  //try to change the style sheet and check
    $(".left_menu").removeClass('active');
    $("#AccountingStudentWithoutFees").addClass('active');

    //get the instance of container division 
    var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");
    //get the instance of the content division
    var dashContentainer_Payments = $("#pa_AccountingSchoolFeesData");
    
    //hide the container first
    dashClientContainer.slideUp();
    
    //prepare the content to be printed in the containner
    
    var content = '';
          content+='<div class="row text-center" >';
            content +='<div class="row text-center">';
              content+= '<div class="col-sm-12">';
                content+= '<h4>Students Without Fees This Term</h4>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row" ><div class="col-sm-12" >\
                    <div id="pa_Expense_ExpenseType">\
                      Loading Students List...\
                    </div>\
                  </div></div>\
                   ';
      
    
    //send the prepared content to the screen for user
    dashClientContainer.html( content );
    //load the contetn now
    dashContentainer_Payments.slideDown();
    dashClientContainer.slideDown();
    /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
    
    var url = base_url + "accounting/schoolfees/studentsWithoutFees";

    var Content_Pa_Deposits_Others  = '';

    var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

      $.getJSON( url, function( data ) {

        Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingStudentsWithoutFeesTable" ><thead><tr ><th class="text-center">#</th><th class="text-center">Student</th><th class="text-center">Reg. Number</th><th class="text-center">Class</th><th class="text-center">Sponsor</th></tr></thead>';
        Content_Pa_Deposits_Others += '<tbody>';

        var studentIndexNumber      = 1;
        $.each( data, function(i, item) {

            Content_Pa_Deposits_Others +="<tr id="+item.id +">";
              Content_Pa_Deposits_Others +="<td class='text-left'>" + studentIndexNumber + "</td>"; 
              Content_Pa_Deposits_Others +="<td class='text-left'>" + item.studentNames + "</td>";
              Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.studRegNber+" </td>";
              Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.className+" </td>";
              Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.sponsor+" </td>";
            Content_Pa_Deposits_Others +="</tr>";

            studentIndexNumber++;
            
        });

        Content_Pa_Deposits_Others +="</tbody></table>";

        pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
        

      }).done(function() {

          $('#pa_AccountingStudentsWithoutFeesTable').DataTable();

      })
      .fail(function() {
      })
      .always(function() {

      });

  // //try to change the style sheet and check
  //   $(".left_menu").removeClass('active');
  //   $("#AccountingStudentWithoutFees").addClass('active');

  //   var dashClientContainer = $("#pa_AccountingSchoolFeesContainer");

  //   var url = base_url+"accounting/schoolfees/studentsWithoutFees";
  //     content = '';

  //     $.getJSON( url, function(data) {

  //       content += '<div class="row"><label class="col-sm-12 text-center"><h4>Remind parents to pay remaining amounts via SMS!</h4></div></div>';
  //       content += '<div class="row top-buffer">';
  //         content +='<div class="row" id="formDashAcNewReminderLoading">';
  //           content +='<div class="grid text-center">';
  //             content +='<img src="../packages/assets/plugins/img/loading.gif" alt=""> Loading, Please wait ...';
  //               content +='';
  //             content +='</div>';
  //           content +='</div>';
  //         content +='</div>';
  //         content += '<form role="form" id="formDashAcNewReminder" >';
  //           content += '<div class="form-horizontal">';
  //           content += '<div class="form-group" >';
  //             content += '<label class="col-sm-3 control-label">Students to pay</label>';
  //             content += '<div class="col-sm-2"><label><input type="radio" name="newReminder_studentType" value="1"  checked> All Students</label></div>';
  //             content += '<div class="col-sm-2"><label><input type="radio" name="newReminder_studentType" value="2"  > Custom Students</label></div>';
  //             content += '<div class="col-sm-2"><label><input type="radio" name="newReminder_studentType" value="3"  > Choose Students</label></div>';
  //           content += '</div>';
  //           content += '<div class="form-group" id="ac_SchoolFee_NewReminder_ExceptContainer">';
  //             content += '<label class="col-sm-3 control-label"> Except</label>';
  //             content += '<div class="col-sm-7">';
  //               content += '<select name="newReminder_StudentsExcept[]" class="form-control" id="newReminder_StudentsExcept" multiple>';
  //                 content += '<option></option>';
  //                 $.each( Students , function(i, item){
  //                   content += '<option value="'+item.personID+'">'+item.studentNames+' ('+item.studRegNber+')</option>';
  //                 }); 
  //               content += '</select>';
  //             content +='</div>';
  //           content += '</div>';
  //           content += '<div class="form-group" id="ac_SchoolFee_NewReminder_BoardingContainer" >';
  //             content += '<label class="col-sm-3 control-label"> Boarding</label>';
  //             if ( boarding.boardingID == 1 ) {
  //               content += '<div class="col-sm-3"><label><input type="checkbox" name="newReminder_studentBoarding[]" value="0" checked > Day</label></div>';
  //               content += '<div class="col-sm-3"><label><input type="checkbox" name="newReminder_studentBoarding[]" value="1" checked > Boarding</label></div>';

  //             }else if( boarding.boardingID == 2 ){
  //               content += '<div class="col-sm-6"><label><input type="checkbox" name="newReminder_studentBoarding[]" value="0" checked desabled > Day</label></div>';

  //             }else if( boarding.boardingID == 3 ){
  //               content += '<div class="col-sm-6"><label><input type="checkbox" name="newReminder_studentBoarding[]" value="1" checked desabled > Boarding</label></div>';
  //             }
  //           content += '</div>';
  //           content += '<div class="form-group" id="ac_SchoolFee_NewReminder_ClassContainer" >';
  //             content += '<label class="col-sm-3 control-label"> Class</label>';
  //             content += '<div class="col-sm-3"><label><input type="radio" name="newReminder_studentClass" value="1" checked > All Class</label></div>';
  //             content += '<div class="col-sm-3"><label><input type="radio" name="newReminder_studentClass" value="0" > Some Class</label></div>';
  //           content += '</div>';
  //           content += '<div class="form-group" id="ac_SchoolFee_NewReminder_SomeClassesContainer">';
  //             content += '<label class="col-sm-3 control-label"> Some Classes</label>';
  //             content += '<div class="col-sm-7">';
  //               content += '<select name="newReminder_SomeClasses[]" class="form-control" id="newReminder_SomeClasses" multiple>';
  //                 content += '<option></option>';

  //                 var classidentifier = "";
  //                 var isTheFirsRecord = true;

  //                 $.each( classrooms , function(i, classroom){

  //                   if ( classroom.classidentifier == classidentifier ) {
  //                     content += '<option value="'+classroom.id+'" data-yearname="'+classroom.YearName+'" data-level="'+classroom.levelID+'" >'+classroom.name+'</option>';

  //                   }else{

  //                     classidentifier = classroom.classidentifier;
  //                     if ( isTheFirsRecord ) {
  //                       isTheFirsRecord = false;

  //                     }else{
  //                       content += '</optgroup>';
  //                     }

  //                     content += '<optgroup label="'+classroom.YearName+'">';
  //                     content += '<option value="'+classroom.id+'" data-yearname="'+classroom.YearName+'" data-level="'+classroom.levelID+'" >'+classroom.name+'</option>';
  //                   }

  //                 }); 

  //               content += '</select>';
  //             content += '</div>';
  //           content += '</div>';
  //           content += '<div class="form-group" id="ac_SchoolFee_NewReminder_SponsorContainer">';
  //             content += '<label class="col-sm-3 control-label"> Sponsor</label>';
  //             content += '<div class="col-sm-2"><label><input type="checkbox" name="newReminder_studentSponsor[]" value="1" checked > Parents/Guardian</label></div>';
  //             content += '<div class="col-sm-2"><label><input type="checkbox" name="newReminder_studentSponsor[]" value="3" checked > School</label></div>';
  //             content += '<div class="col-sm-1"><label><input type="checkbox" name="newReminder_studentSponsor[]" value="4" checked > Self</label></div>';
  //             content += '<div class="col-sm-2"><label><input type="checkbox" name="newReminder_studentSponsor[]" value="2" checked id="newReminder_studentSponsorOrg"> Organization</label></div>';
  //           content += '</div>';
  //           content += '<div class="form-group" id="ac_SchoolFee_NewReminder_OrganizationContainer">';
  //             content += '<label class="col-sm-3 control-label"> Organization</label>';
  //             content += '<div class="col-sm-7">';
  //               content += '<select name="newReminder_Organization[]" class="form-control" id="newReminder_Organization" multiple >';
  //                 content += '<option></option>';
  //                 $.each( SchoolOrganizations , function(i, item){
  //                   content += '<option value="'+item.id+'" selected="selected">'+item.name+'</option>';
  //                 }); 
  //               content += '</select>';
  //             content += '</div>';
  //           content += '</div>';
  //           content += '<div class="form-group" id="ac_SchoolFee_NewReminder_SomeStudentsContainer">';
  //             content += '<label class="col-sm-3 control-label"> Some Students</label>';
  //             content += '<div class="col-sm-7">';
  //               content += '<select name="newReminder_StudentsSome[]" class="form-control" id="newReminder_StudentsSome" multiple>';
  //                 content += '<option></option>';
  //                 $.each( Students , function(i, item){
  //                   content += '<option value="'+item.personID+'">'+item.studentNames+' ('+item.studRegNber+')</option>';
  //                 }); 
  //               content += '</select>';
  //             content +='</div>';
  //           content += '</div>';
  //           content += '<div class="form-group" >';
  //             content += '<label class="col-sm-3 control-label">Parent to pay before(time) </label>';
  //             content += '<div class="col-sm-7">';
  //               content += '<input name="newReminder_dueDate" type="text" class="form-control" placeholder="Add due date" id="newReminder_dueDate">';
  //             content += '</div>';
  //           content += '</div>'
  //           content += '<div class="form-group">';
  //             content += '<div class="col-sm-3 text-right">Reminder Message</div>';
  //             content += '<div class="col-sm-7">';
  //               content += '<textarea name="message" id="newReminderContent" class="form-control" placeholder="write your message here" style="height: 120px;">'+ToSendMessageContent+'</textarea>';
  //             content += '</div>';
  //           content += '</div>';
  //           content += '<div class="form-group" >';
  //             content += '<label class="col-sm-3 control-label"></label>';
  //             content += '<div class="col-sm-7 text-right">';
  //               content += '<button type="submit" class="btn btn-success" id="paNewPaSubmit" >Send SMS Reminder</button>';
  //             content += '</div>';
  //           content += '</div>';
  //         content += '</div>';
  //       content += '</form>';
  //       content += '</div>';

  //     }).done(function() {

  //       dashClientContainer.html(content);
  //       $("#formDashAcNewReminderLoading").hide();
        
  //       ac_SchoolFee_NewPaymentReminderForm();

  //     }).fail(function() {

  //         $.gritter.add({
  //             title: 'Result not saved',
  //             text: 'Check your internet and try again',
  //             class_name: 'danger gritter-center',
  //             time: ''
  //         });

  //     })
  //     .always(function() {

  //     });

}

function ac_SchoolFee_NewPaymentReminderForm(){

 
  $("#newReminder_StudentsExcept").select2({ 
            closeOnSelect: false
    });
  $("#newReminder_SomeClasses").select2({ 
            closeOnSelect: false,
            formatResult: newStudentformatResult,
            formatSelection: newStudentformatSelection
    });
  $("#newReminder_Organization").select2({ 
            closeOnSelect: false
    });
  $("#newReminder_StudentsSome").select2({ 
            closeOnSelect: false
    });

  $('#newReminder_dueDate').blur(function() {

      var dueDate            = $(this).val();

      var newReminderContent  = $("#newReminderContent");
      var ReminderMessage     = newReminderContent.val();
      //ReminderMessage         = ReminderMessage.replace(/()(.+?)(?= ##)/, "$1 "+dueDate+"");

      var startIndex = ReminderMessage.indexOf("<<");
      var endIndex   = ReminderMessage.indexOf(">>");

      String.prototype.replaceBetween = function(start, end, what) {
          return this.substring(0, start) + what + this.substring(end);
      };

      ReminderMessage = ReminderMessage.replaceBetween( startIndex+2, endIndex, dueDate );
      newReminderContent.val(ReminderMessage);

  });

  // $('#newReminder_dueDate').datepicker();

  var ac_SchoolFee_NewReminder_ExceptContainer       = $("#ac_SchoolFee_NewReminder_ExceptContainer");
  var ac_SchoolFee_NewReminder_BoardingContainer     = $("#ac_SchoolFee_NewReminder_BoardingContainer");
  var ac_SchoolFee_NewReminder_ClassContainer        = $("#ac_SchoolFee_NewReminder_ClassContainer");
  var ac_SchoolFee_NewReminder_SomeClassesContainer  = $("#ac_SchoolFee_NewReminder_SomeClassesContainer");
  var ac_SchoolFee_NewReminder_SponsorContainer      = $("#ac_SchoolFee_NewReminder_SponsorContainer")
  var ac_SchoolFee_NewReminder_OrganizationContainer = $("#ac_SchoolFee_NewReminder_OrganizationContainer");
  var ac_SchoolFee_NewReminder_SomeStudentsContainer = $("#ac_SchoolFee_NewReminder_SomeStudentsContainer");

  ac_SchoolFee_NewReminder_BoardingContainer.hide();
  ac_SchoolFee_NewReminder_ClassContainer.hide();
  ac_SchoolFee_NewReminder_SomeClassesContainer.hide();
  ac_SchoolFee_NewReminder_SponsorContainer.hide();
  ac_SchoolFee_NewReminder_OrganizationContainer.hide();
  ac_SchoolFee_NewReminder_SomeStudentsContainer.hide();

  $('input[type=radio][name=newReminder_studentType]').change(function() {
          
    var selected = $(this).val();

      ac_SchoolFee_NewReminder_ExceptContainer.hide();
      ac_SchoolFee_NewReminder_BoardingContainer.hide();
      ac_SchoolFee_NewReminder_ClassContainer.hide();
      ac_SchoolFee_NewReminder_SomeClassesContainer.hide();
      ac_SchoolFee_NewReminder_SponsorContainer.hide();
      ac_SchoolFee_NewReminder_OrganizationContainer.hide();
      ac_SchoolFee_NewReminder_SomeStudentsContainer.hide();

    switch(selected) {
        case "1":
            ac_SchoolFee_NewReminder_ExceptContainer.slideDown();
            break;

        case "2":
            ac_SchoolFee_NewReminder_BoardingContainer.slideDown();
            ac_SchoolFee_NewReminder_ClassContainer.slideDown();
            ac_SchoolFee_NewReminder_SponsorContainer.slideDown();
            ac_SchoolFee_NewReminder_OrganizationContainer.slideDown();
           break;

          case "3":
              ac_SchoolFee_NewReminder_SomeStudentsContainer.slideDown();
            break;

    } 

  });

  $('input[type=radio][name=newReminder_studentClass]').change(function() {
          
    var selected = $(this).val();

      ac_SchoolFee_NewReminder_SomeClassesContainer.hide();

    switch(selected) {
        case "1":
            ac_SchoolFee_NewReminder_SomeClassesContainer.hide();
            break;

        case "0":
            ac_SchoolFee_NewReminder_SomeClassesContainer.slideDown();
           break;

    } 

  });

  $('#newReminder_studentSponsorOrg').change(function() {
        
        if ( $(this).is(":checked") )
         {
          ac_SchoolFee_NewReminder_OrganizationContainer.slideDown();

         }else{

          ac_SchoolFee_NewReminder_OrganizationContainer.hide();
         }
        
  });

    // validate
    validateNewReminder();

}

/* Load the Invoice List function */
function load_Accounting_List_Invoice(){
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
                content+= '<h4>Registered Invoices</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingInvoiceSettings class="btn btn-success pull-right"><i class="fa fa-gear"></i>&nbsp;&nbsp;Invoice Settings</a>';
                content+= '<a id=AccountingNewInvoice class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Invoice</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12 text-center">\
        <div id="pa_Expense_ExpenseType">\
                Loading invoices...\
                </div>\
            </div>\
            </div>\
          </div>\
        </div>\
      </div></div></div></div>\
    <div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
      <div class="modal-wrapper">\
        <div class="modal-dialog">\
          <form id="formPayInvoiceNow" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
            <div class="modal-content">\
              <div class="modal-header">\
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                <h4 class="modal-title text-left" id="myModalLabel22">Invoice Payment</h4>\
              </div>\
              <div id="ModalDataLoadingNow">\
              </div>\
              <div class="modal-footer" >\
                <div class="pull-right btn-group">\
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                  <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Pay Invoice</button>\
                </div>\
              </div>\
            </div>\
          </form>\
        </div>\
      </div>\
    </div>';
    
  
  //send the prepared content to the screen for user
  dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  var url = base_url + "accounting/invoices";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Client</th><th class="text-center">Number</th><th class="text-center">Amount</th><th class="text-center">Paid</th><th class="text-center">Remain</th><th class="text-center">Due</th><th class="text-center">&nbsp;Items</th><th class="text-center">&nbsp;</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th><th colspan="4" style="text-align:right"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + date_moment(item.date) + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.clientName + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.number + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.totalToPay) + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.totalPaid) + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.totalToPay - item.totalPaid) + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + date_moment(item.dueDate) + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.numberOfItems +"</td>";
            //Content_Pa_Deposits_Others +="<td class='text-right'> <a href='#' onclick = 'PayInvoice(\"" + item.id + "\")' >Paid</a></td>";
            Content_Pa_Deposits_Others +="<td class='text-right;' id='print" + item.id + "'> <a href='#' onclick = 'PrintInvoice(\"" + item.id + "\")' ><i class='fa fa-print'></i>Print</a><span id='printLog" + item.id + "'></span></td>";
            // Content_Pa_Deposits_Others +="<td class='text-right;' id='send" + item.id + "'> <a href='#' onclick = 'SendInvoice(\"" + item.id + "\")' ><i class='fa fa-paper-plane'></i>Send</a></td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

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

              //To pay 
                // Total 
                  total_Topay = api
                      .column( 3, {search:'applied'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                      
                   // Update footer
                  $( api.column( 3 ).footer() ).html(
                      '<span class="text-black">'+formatNumber(round_(total_Topay,2)) +' '+schoolCurrency+'</span>'
                  );

              //Paid 
                // Total 
                  total_Paid = api
                      .column( 4, {search:'applied'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                      
                   // Update footer
                  $( api.column( 4 ).footer() ).html(
                      '<span class="text-green">'+formatNumber(round_(total_Paid,2))+' '+schoolCurrency+'</span>'
                  );

              //Remain 
                // Total 
                  total_Remain = api
                      .column( 5, {search:'applied'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                      
                  // Update footer
                  $( api.column( 5 ).footer() ).html(
                      '<span class="text-red">'+formatNumber(round_(total_Remain,2))+' '+schoolCurrency+'</span>'
                  );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of the ajax request here */
  /* Activate the new invoice buttons */
  AccountingListInvoiceJS();
}
function round_(number, precision) {
    var pair = (number + 'e').split('e')
    var value = Math.round(pair[0] + 'e' + (+pair[1] + precision))
    pair = (value + 'e').split('e')
    return +(pair[0] + 'e' + (+pair[1] - precision))
}
/* Load the Invoice List function */
function load_Accounting_List_Bills(){
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
                content+= '<h4>Registered Bills</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewBill class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Bill</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">\
                <div id="pa_Expense_ExpenseType">\
                  Bills List Here\
                </div>\
            </div>\
            </div>\
          </div>\
        </div>\
      </div></div></div></div>\
    <div class="modal fade" id="Model_DashPaFeeStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
      <div class="modal-wrapper">\
        <div class="modal-dialog">\
          <form id="formPayInvoiceNow" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
            <div class="modal-content">\
              <div class="modal-header">\
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                <h4 class="modal-title text-left" id="myModalLabel22">Bill Payment</h4>\
              </div>\
              <div id="ModalDataLoadingNow">\
              </div>\
              <div class="modal-footer" >\
                <div class="pull-right btn-group">\
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                  <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Pay Bill</button>\
                </div>\
              </div>\
            </div>\
          </form>\
        </div>\
      </div>\
    </div>';
    
  
  //send the prepared content to the screen for user
  dashClientContainer.html( content );
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  var url = base_url + "accounting/bills";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" ><thead><tr ><th class="text-center">Bill</th><th class="text-center">Vendor</th><th class="text-center">Account</th><th class="text-center">Amount</th><th class="text-center">Paid</th><th class="text-center">Remain</th><th class="text-center">Date</th><th class="text-center">Due</th><th class="text-center">Items</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th><th colspan="3" style="text-align:right"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.BillID +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.BillNumber + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.VendorName + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.BudgetItem + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.totalToPay) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.totalPaid) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.totalToPay - item.totalPaid) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ date_moment(item.date) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ date_moment(item.dueDate) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.numberOfItems +" </td>";
            // Content_Pa_Deposits_Others +="<td class='text-right'> <a href='#' onclick = 'PayBill(\"" + item.BillID + "\")' >Pay</a></td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

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

               // Total To Pay  
              total_ToPaid = api
                  .column( 3 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 3 ).footer() ).html(
                  '<span class="text-blue">'+formatNumber(total_ToPaid)+' FRW</span>'
              );

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

               // Total Remain  
                total_Remain = api
                    .column( 5 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 5 ).footer() ).html(
                    '<span class="text-red">'+formatNumber(total_Remain)+' FRW</span>'
                );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of the ajax request here */
  /* Activate the new invoice buttons */
  AccountingListBillJS();
}
/* Load the client form function */
function load_Accounting_New_Bill(){

  var budgetitems = [];
  var clients     = [];
  var products    = [];
  var countryTax  = [];

  var url = base_url+"accounting/bills/create";

    $.getJSON( url, function( data ) {

      budgetitems = data.budgetitems;
      vendors     = data.vendors;
      products    = data.products;
      countryTax  = data.countryTax;

    }).done(function() {
    
      NewBillForm( budgetitems, vendors, products, countryTax );
    
    }).fail(function() {
      
      $.gritter.add({
             title: 'Failed',
             text: 'Failed to load new bill, try again',
             class_name: 'danger gritter-center',
             time: ''
         });

    }).always(function() {

    });

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
                \
                      <form id="formDashPaNewIncome" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
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
                  <input name="clientEmail" type="text" id="paNewClientEmail" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Contact Person</div>\
                <div class="col-sm-7">\
                  <input name="clientContactFname" placeholder="First Name" type="text" id="paNewClientEmail" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">&nbsp;</div>\
                <div class="col-sm-7">\
                  <input name="clientContactLname" placeholder="Larst Name" type="text" id="paNewClientEmail" class="form-control" >\
                </div >\
              </div>\
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
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
  //load the contetn now
  dashContentainer_Payments.slideDown();
  dashClientContainer.slideDown();
  
  /* Activate the new Client buttons */
  AccountingNewClientJS();
}

/* Load the client form function */
function load_Accounting_New_Capital_Equity(){
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
                content+= '<h4>New Capital/Equity</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewCapitalEquityList class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                \
                      <form id="formDashPaNewIncome" class="form-horizontal" data-fv-framework="bootstrap" data-fv-icon-valid="glyphicon glyphicon-ok" data-fv-icon-invalid="glyphicon glyphicon-remove" data-fv-icon-validating="glyphicon glyphicon-refresh">\
                        <div class="modal-body" >\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Source</div>\
                <div class="col-sm-7">\
                  <input name="accountName" type="text" id="paNewAccountName" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Amount</div>\
                <div class="col-sm-7">\
                  <input name="accountAmount" type="text" id="paNewAccountAmount" class="form-control" >\
                </div >\
              </div>\
                            <div class="form-group" > \
                <div class="col-sm-3 text-right">Date</div>\
                <div class="col-sm-7">\
                  <input name="accountDate" type="text" id="paNewAccountDate" class="form-control" >\
                </div >\
              </div>\
                            \
                        </div>\
                        <div class="modal-footer" >\
                          <div class="pull-right btn-group">\
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                            <button type="submit" class="btn btn-primary" id="paNewPaSubmit" >Save Capital</button>\
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
  AccountingNewCapitalEquityJS();
}

/* Load the client form function */
function load_Accounting_List_Client(){
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
                content+= '<h4>Available Clients</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewClient class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Client</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12 text-center">';
                content+='<div id="pa_Expense_ExpenseType">\
                Loading the clients...\
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
  
  //Send Ajax request to check client list in the  database
  /* Start of Client Ajaxt Request */

  var url = base_url + "accounting/clients";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" ><thead><tr ><th class="text-center">Name</th><th class="text-center">Contact Person</th><th class="text-center">Phone</th><th class="text-center">Email</th><th class="text-center">To Pay</th><th class="text-center">Paid</th><th class="text-center">Remain</th><th class="text-center">Open Invoices</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:center"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
          
          AmountRemain  = parseFloat(item.totalToPay) - parseFloat(item.totalPaid); 

          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+item.name+"</td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+item.contactPerson+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.phone+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+item.email+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.totalToPay)+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(item.totalPaid)+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+formatNumber(AmountRemain)+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+formatNumber(item.numberOfOpenInvoice)+"</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

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

              // Total To Pay  
              total_ToPaid = api
                  .column( 4, {search:'applied'} )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );
                  
               // Update footer
              $( api.column( 4 ).footer() ).html(
                  '<span class="text-blue">'+formatNumber(total_ToPaid)+' '+schoolCurrency+'</span>'
              );


              // Total Paid  
                total_Paid = api
                    .column( 5, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 5 ).footer() ).html(
                    '<span class="text-green">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
                );

               // Total Remain  
                total_Remain = api
                    .column( 6, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 6 ).footer() ).html(
                    '<span class="text-red">'+formatNumber(total_Remain)+' '+schoolCurrency+'</span>'
                );

            //  Total Open Invoice  
                total_OpenInvoice = api
                    .column( 7, {search:'applied'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                 // Update footer
                $( api.column( 7 ).footer() ).html(
                    '<span class="text-black">'+formatNumber(total_OpenInvoice)+'</span>'
                );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of Client Ajaxt Request */
  
  /* Activate the new Client buttons */
  AccountingListClientJS();
}

/* Load the vendor form function */
function load_Accounting_List_Vendor(){
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
                content+= '<h4>Available Vendors</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewVendor class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Vendor</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                Load the Vendord List Here Now\
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
  
  //Send Ajax request to check client list in the  database
  /* Start of Client Ajaxt Request */

  var url = base_url + "accounting/vendors";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingVendorsTable" ><thead><tr ><th class="text-center">Name</th><th class="text-center">Contact Person</th><th class="text-center">Phone</th><th class="text-center">Email</th><th class="text-center">To Pay</th><th class="text-center">Paid</th><th class="text-center">Remain</th><th class="text-center">Open Bills</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th><th style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
          
          var AmountRemain  = parseFloat(item.totalToPay) - parseFloat(item.totalPaid);

          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            //Content_Pa_Deposits_Others +="<td >"+item.ClientID+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+item.name+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>"+item.contactPerson+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.phone + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.email + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.totalToPay) +"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.totalPaid) +"</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(AmountRemain) + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ item.numberOfOpenInvoice + "</td>";
            //Content_Pa_Deposits_Others +="<td class='text-right' style='cursor:pointer' onclick='VendorDetails(\"" + item.id + "\")'>View</td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_AccountingVendorsTable').DataTable( {
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
                    .column( 5 )
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
  /* End of Client Ajaxt Request */
  
  /* Activate the new Client buttons */
  AccountingListVendorJS();
}

/* Load the vendor form function */
function VendorDetails(vendorId){
  
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
                content+= '<h4>Bills Status</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewVendor class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Vendor</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                Load the Vendord List Here Now\
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
  
  //Send Ajax request to check client list in the  database
  /* Start of Client Ajaxt Request */

  var url = base_url + "accounting/vendors/" + vendorId;

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {
    Content_Pa_Deposits_Others += "Vendor: " + data.vendor.name + "<br />";
    Content_Pa_Deposits_Others += "Phone: " + data.vendor.phone + "<br />";
    Content_Pa_Deposits_Others += "Email: " + data.vendor.email + "<br />";
    Content_Pa_Deposits_Others += "Open Bills: " + data.vendor.Bills + "#";
    Content_Pa_Deposits_Others += data.vendor.amount + " RWF<br />";
      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" ><thead><tr ><th class="text-center">Number</th><th class="text-center">Date</th><th class="text-center">Due</th><th class="text-center">Amount</th><th class="text-center">Paid</th><th class="text-center">Remain</th><!--<th class="text-center">Action</th>--></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data.bills, function(i, item) {
      var pay = item.amount - item.paid;
          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            //Content_Pa_Deposits_Others +="<td >"+item.ClientID+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.number+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.date + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.duedate + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.amount) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>"+ formatNumber(item.paid) +" </td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.amount - item.paid) + "</td><!--";
            if(pay > 0)
        Content_Pa_Deposits_Others +="<td class='text-right' style='cursor:pointer'>Pay</td>";
            else
        Content_Pa_Deposits_Others +="<td class='text-right' style='cursor:pointer'></td>";
      Content_Pa_Deposits_Others +="<td class='text-right' style='cursor:pointer'>Details</td>-->";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

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
                  '<span class="text-green">' + formatNumber(total_Paid) + ' FRW</span>'
              );

          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of Client Ajaxt Request */
  
  /* Activate the new Client buttons */
  AccountingListVendorJS();
}

/* Load the product List form function */
function load_Accounting_List_Product(type){

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
                content+= '<h4>Available Products</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                content+= '<a id="pa_AccountingNewProduct" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Product</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                Loading product List...\
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
  
  //Send Ajax request to check client list in the  database
  /* Start of Client Ajaxt Request */

  var url = base_url + "accounting/products?type="+type;

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingProductListTable" ><thead><tr ><th class="text-center">Product</th><th class="text-center">Amount</th><th class="text-center">Type</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+item.name+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.amount) + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>" + item.type + " </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      
    }).done(function() {

        $('#pa_AccountingProductListTable').DataTable( {
          "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;
   
              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };


          }
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of Client Ajaxt Request */
  
  /* Activate the new Client buttons */
   AccountingListProductJS(type);
}
/* End of client form function */

/* Load the client form function */
function load_Accounting_List_Organization(){
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
              content+= '<div class="col-sm-6 text-right">';
                content+= '<h4>Organization Sponsors</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-6">';
               content+= '<a id=AccountingNewSponsor class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Sponsor</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                Sponsors List Loading....\
                </div>\
            </div>\
            </div>\
          </div>\
        </div>\
      </div></div></div></div>\
          <div class="modal fade" id="Model_DashPaSponsorStudentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
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
  
  //Send Ajax request to check client list in the  database
  /* Start of Client Ajaxt Request */

  var url = base_url + "accounting/sponsors";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingSponsorTable" ><thead><tr ><th class="text-center">Name</th><th class="text-center">Sponsored Students<br>Payments Status</th><th class="text-center">Contact Person</th><th class="text-center">Phone</th><th class="text-center">Email</th><th class="text-center">P.O Box</th><th class="text-center">Description</th></tr></thead>';
      
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.id+">";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.name + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'><a href='"+ base_url + "accounting/sponsors/"+item.id+"' title='Download Current Status' target='_blank' ><i style='color:green;' class='fa fa-align-right'></i>" + item.numberOfStudent + "</a></td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.contactPerson + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.phoneNumber + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.email + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.pobox + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.description + " </td>"; 
            //Content_Pa_Deposits_Others +="<td class='text-center'><a href='#' onclick = 'if(true){return false;} LoadSponsorStudentInfo(\"" + item.id + "\")' >"+ item.numberOfStudent +"</a> <a href='#' onclick = 'LoadSponsorStudentInformation(\"" + item.id + "\")' ><i style='color:green;' class='fa fa-align-right'></i></a></td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

      $('#pa_AccountingSponsorTable').DataTable();

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of Client Ajaxt Request */
  
  /* Activate the new Client buttons */
  AccountingListSponsorJS();
}

/* Load the client form function */
function load_Accounting_List_Capital_Equity(){
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
                content+= '<h4>Capital/Equity</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-4">';
                //content+= '<a id=AccountingNewCapitalEquity class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New</a>';
              content+= '</div>';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">';
                content+='<div id="pa_Expense_ExpenseType">\
                Capital/Equity Loading....\
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
  
  //Send Ajax request to check client list in the  database
  /* Start of Client Ajaxt Request */

  var url = base_url + "accounting/capital";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" ><thead><tr ><th class="text-center">Source</th><th class="text-right">Amount</th><th class="text-right">Date</th><th>Transaction</th></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id="+item.AccountID +">";
            //Content_Pa_Deposits_Others +="<td >"+item.ClientID+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.AccountName+" </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.Amount) + " </td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.CapitalDate + " </td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'> View </td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

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
      } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of Client Ajaxt Request */
  
  /* Activate the new Client buttons */
  AccountingListCapitalEquityJS();
}

/* Load the Invoice List function */
function load_Accounting_Expense_List(){
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
                content+= '<h4>Recorded Expenses</h4>';
              content+= '</div><!--';
              content+= '<div class="col-sm-4">';
                content+= '<a id=AccountingNewInvoice class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;New Invoice&nbsp;</a>';
              content+= '</div>-->';
            content+= '</div>';
            content+= '<div class="row">';
              content+= '<div class="col-sm-12">\
        <div id="pa_Expense_ExpenseType">\
                Invoice List Here\
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
  /* Prepare and Submit the ajax query to retrieve the list of registered invoice */
  
  var url = base_url + "accounting/transactions?type=expenses";

  var Content_Pa_Deposits_Others  = '<style>\
  tbody tr.odd {\
    background-color: #f9f9f9;\
  }\
  </style>';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_AccountingTransactionMobiCash" ><thead><tr ><th class="text-center">Date</th><th class="text-center">Expense Type</th><th class="text-center">Account</th><th class="text-center">Memo</th><th class="text-center">Amount</th><th class="text-center">Money Used</th><th class="text-center">Details</th></tr></thead>';
      Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="4" style="text-align:right">Total:</th><th colspan="3" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
      var rowscheck = 0;
      $.each( data, function(i, item) {

            var expenseKindContent = '';
            switch(item.expenseType) {
                case 1:
                  expenseKindContent = '<span class="badge"> ' + item.expensecategoryName + '</span>';
                  break;
                case 2:
                  expenseKindContent = '<span class="badge bg-blue"> ' + item.expensecategoryName + '</span>';
                  break;
                  case 3:
                  expenseKindContent = '<span class="badge bg-aqua"> ' + item.expensecategoryName + '</span>';
                  break;
                case 4:
                  expenseKindContent = '<span class="badge bg-green"> ' + item.expensecategoryName + '</span>';
                  break;
            }

          Content_Pa_Deposits_Others +="<tr id="+item.id +">";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ date_moment(item.date) + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-center'>"+ expenseKindContent + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>"+ item.name + "</td>"; 
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.memo + "</td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.amount) + "</td>"; 
            Content_Pa_Deposits_Others +="<td class='text-center'>"+item.moneyUsed+"</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'></td>";
          Content_Pa_Deposits_Others +="</tr>";
          
      });

      Content_Pa_Deposits_Others +="</tbody></table>";

      pa_Expense_ExpenseType.html( Content_Pa_Deposits_Others );
      

    }).done(function() {

        $('#pa_AccountingTransactionMobiCash').DataTable( {
          "order": [[ 0, "desc" ]],
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
                      .column( 4, {search:'applied'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                      
                   // Update footer
                  $( api.column( 4 ).footer() ).html(
                      '<span class="text-red">'+formatNumber(total_Paid)+' '+schoolCurrency+'</span>'
                  );

              }
          } );

    })
    .fail(function() {
    })
    .always(function() {

    });
  /* End of the ajax request here */
  /* Activate the new invoice buttons */
  AccountingSchoolFeesJS();
}

/* Submit the Expense Category Form */
function submitPaNewExpenseCategory(){
  //Hide Your 
     $('#Model_DashPaNewExpenseCategory').modal('toggle');

     var formDashPaNewExpenseCategory  = $("#formDashPaNewExpenseCategory");

     $.ajax({
       url: base_url+"accounting/expensecategory",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewExpenseCategory.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: 'New School Expense Category Added.',
                   class_name: 'success gritter-center',
                   time: ''
               });

              // var paNewDebtStudent     = $("#paNewDebtStudent");
               //paNewDebtStudent.select2('data', null);

               var categoryname      = $("#categoryname");
               categoryname.val("");
              
         }else{  

               $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to add New Expense Category.' + res.message,
                   class_name: 'danger gritter-center',
                   time: ''
               });

               $('#Model_DashPaNewExpenseCategory').modal('show');

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
                   title: 'Failed',
                   text: 'Unable to save Expense Category Name. Please try again',
                   class_name: 'danger gritter-center',
                   time: ''
               });

        $('#Model_DashPaNewExpenseCategory').modal('show');

     })
     .always(function() {
    
    });
}

/* submit the budget item form */
function submitPaNewStudentDebt(){
    //Hide Your 
     $('#Model_DashPaNewBudgetItem').modal('toggle');

     var formDashPaNewDebt  = $("#formDashPaNewBudgetItem");

     $.ajax({
       url: base_url+"accounting/budgetitems",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: 'New Budget Item added.',
                   class_name: 'success gritter-center',
                   time: ''
               });
               var paNewBudgetItemName      = $("#paNewBudgetItemName");
               paNewBudgetItemName.val("");

               formDashPaNewDebt.formValidation('revalidateField', 'itemname');

               load_acc_Budget_Expense();
              
         }else{  

               $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to add Debt.',
                   class_name: 'danger gritter-center',
                   time: ''
               });

               $('#Model_DashPaNewDebt').modal('show');

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to add Item Budget.Please try again',
                   class_name: 'danger gritter-center',
                   time: ''
               });

        $('#Model_DashPaNewBudgetItem').modal('show');

     })
     .always(function() {
    
    });

}

/* submit the budget item form */
function submitPaNewVendor(){

     var formDashPaNewDebt  = $("#formDashPaNewVendor");

     $.ajax({
       url: base_url+"accounting/vendors",
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

               var paNewFixedName     = $("#paNewVendorName");
               paNewFixedName.val("");
               var paNewFixedAssetAmount  = $("#paNewVendorPhone");
               paNewFixedAssetAmount.val("");

               var paNewFicedAssetDepreciation = $("#paNewVendorEmail");
               paNewFicedAssetDepreciation.val("");
               var paNewVendorContact = $("#paNewVendorContact");
               paNewVendorContact.val("");

               formDashPaNewDebt.formValidation('revalidateField', 'vendorName');
               formDashPaNewDebt.formValidation('revalidateField', 'vendorPhone');
               formDashPaNewDebt.formValidation('revalidateField', 'vendorEmail');
               formDashPaNewDebt.formValidation('revalidateField', 'vendorContactPerson');

              load_Accounting_List_Vendor();
              
         } else {

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

               //$('#Model_DashPaNewFixedAsset').modal('show');

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

       // $('#Model_DashPaNewFixedAsset').modal('show');

     })
     .always(function() {
    
    });

}

/* submit the school cash item form */
function submitPaNewSchoolCash(){

    //Hide Your 
     $('#Model_DashPaNewCashAccount').modal('toggle');

     var formDashPaNewDebt  = $("#formDashPaNewSchoolCash");

     $.ajax({
       url: base_url+"accounting/schoolcash",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: 'New School Cash Account Created.',
                   class_name: 'success gritter-center',
                   time: ''
               });

              // var paNewDebtStudent     = $("#paNewDebtStudent");
               //paNewDebtStudent.select2('data', null);

               
               var paNewBudgetItemAmount      = $("#paNewBudgetItemAmount");
               paNewBudgetItemAmount.val("");
         
               formDashPaNewDebt.formValidation('revalidateField', 'staffname');
               formDashPaNewDebt.formValidation('revalidateField', 'startingAmount');
               
               load_acc_Assets_Current_Cash();
              
         } else {  

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

               $('#Model_DashPaNewCashAccount').modal('show');

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to add school cash Account.Please try again',
                   class_name: 'danger gritter-center',
                   time: ''
               });

        $('#Model_DashPaNewCashAccount').modal('show');

     })
     .always(function() {
    
    });

}

/* submit the school Bank item form */
function submitPaNewSchoolBank(){

    //Hide Your 
     $('#Model_DashPaNewBankAccount').modal('toggle');

     var formDashPaNewDebt  = $("#formDashPaNewSchoolBank");

     $.ajax({
       url: base_url+"accounting/schoolbank",
       dataType: 'json',
       type: 'POST',
       data: formDashPaNewDebt.serialize(),
       success: function ( res ) {
         if ( res.success )
         {

               $.gritter.add({
                   title: 'Success',
                   text: 'New School Bank Account Created.',
                   class_name: 'success gritter-center',
                   time: ''
               });

              // var paNewDebtStudent     = $("#paNewDebtStudent");
               //paNewDebtStudent.select2('data', null);

               
               var paNewAccountNumber = $("#paNewAccountNumber");
               paNewAccountNumber.val("");
               var paNewBankStartingAmount = $("#paNewBankStartingAmount");
               paNewBankStartingAmount.val("");
               var paNewBankAccountPublicDescription = $("#paNewBankAccountPublicDescription");
               paNewBankAccountPublicDescription.val("");
               var paNewBankAccountPrivateDescription = $("#paNewBankAccountPrivateDescription");
               paNewBankAccountPrivateDescription.val("");
         /* revalidate all fields that are required */
               formDashPaNewDebt.formValidation('revalidateField', 'accountnumber');
               formDashPaNewDebt.formValidation('revalidateField', 'startingAmount');
               formDashPaNewDebt.formValidation('revalidateField', 'publicDescription');
               formDashPaNewDebt.formValidation('revalidateField', 'privateDescription');
               
               load_acc_Assets_Current_BankAccounts();
              
         } else {  

               $.gritter.add({
                   title: 'Failed',
                   text: res.error,
                   class_name: 'danger gritter-center',
                   time: ''
               });

               $('#Model_DashPaNewBankAccount').modal('show');

         }

       }

     }).done(function() {


     }).fail(function() {

        $.gritter.add({
                   title: 'Failed',
                   text: 'Failed to add school Bank Account.Please try again',
                   class_name: 'danger gritter-center',
                   time: ''
               });

        $('#Model_DashPaNewBankAccount').modal('show');

     })
     .always(function() {
    
    });

}

/* End of Expense menu */

function load_acc_Budget_Expense()
{

  var dashPayments_content      = $("#dashPayments_content");
  var dashContentainer_Payments = $("#dashContentainer_Payments");

  dashPayments_content.slideUp();

      var content = '<div class="col-md-12"><div class="grid email"><div class="grid-body"><div class="row">';
        content+='<div class="row">';
          content+= '<div class="col-sm-12">';
            content+='<div class="row text-center">';
              content+= '<div class="col-sm-6">';
                content+= '<h4>Active Expense Accounts</h4>';
              content+= '</div>';
              content+= '<div class="col-sm-6">';
                content+= '<a data-target="#Model_DashPaNewBudgetItem" data-toggle="modal" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Expense Account</a>';
                content+= '<a data-target="#Model_DashPaNewExpenseCategory" data-toggle="modal" class="btn btn-success pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Expense Category</a>';
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

  var url = base_url+"accounting/budgetitems/";

  var Content_Pa_Deposits_Others  = '';

  var  pa_Expense_ExpenseType = $('#pa_Expense_ExpenseType');

    $.getJSON( url, function( data ) {

      Content_Pa_Deposits_Others += '<table class="table table-bordered abDataTable" id="pa_RecordedExpenseTable" ><thead><tr ><th class="text-center">Category</th><th class="text-center">Account Name</th><th class="text-center">Code</th><th class="text-center">Amount Spent</th><!--<th class="text-center">Balance</th>--></tr></thead>';
      //Content_Pa_Deposits_Others += '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th colspan="4" style="text-align:left"></th></tr></tfoot>';
      Content_Pa_Deposits_Others += '<tbody>';
    //console.log(data);
      
      $.each( data, function(i, item) {
      
          Content_Pa_Deposits_Others +="<tr id=1>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.Category + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-left'>" + item.Item + "</td>"; 
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.accountCode) + "</td>";
            Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.Used) + "</td>";
            //Content_Pa_Deposits_Others +="<td class='text-right'>" + formatNumber(item.Amount - item.Used ) + "</td>";
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
                    content   += '<form  id="formDashGetAccountingReport" class="form-horizontal top-buffer" role="form" >';
                      content   += '<div class="form-group">\
                    <label class="col-sm-3 control-label">Type</label>\
                    <div class="col-sm-7">\
                      <select name="reportType" id="reportTypeNumber" class="form-control select2-input" ></select>\
                    </div>\
                  </div>\
                  <!--<div class="form-group">\
                    <label class="col-sm-3 control-label">Start Date</label>\
                    <div class="col-sm-7">\
                      <input type="text" name="reportStartingDate" class="form-control" id="AccountingReportStartDate" />\
                    </div>\
                  </div>-->\
                  <div class="form-group">\
                    <label class="col-sm-3 control-label">End Date</label>\
                    <div class="col-sm-7">\
                      <input type="text" name="reportEndingDate" class="form-control" id="AccountingReportEndDate" />\
                    </div>\
                  </div>\
                  <!--<div class="form-group">\
                    <div class="col-sm-10" style="border:0px solid #000;">\
                      <label title="If Checked the system will recalculate the previous Calculated Data"><input type="checkbox" name="forceReflesh" class="form-control" id="" />Force Reflesh</label>\
                    </div>\
                  </div>-->';
                      content   += '<div class="form-group top-buffer"><label class="col-sm-3 control-label"></label><div class="col-sm-7 text-center"><button class="btn btn-lg btn-primary btn-block" type="submit" id="GrProclamationSubmit" >Generate</button></div></div>';
                    content   += '</form>';
                  content   += '</div>';
                content   += '</div>';
                content   += '<div class="col-sm-8" >';
                  content   += '<div class="row text-center"><h4>Generated Reports</h4></div>';
                  content   += '<div class="row text-center">\
          <div id="AccountingReportPartID"></div>\
          </div>';
                content   += '</div>';
              content   += '</div>';
            content+= '</div>';
          content+= '</div>';
        content+= '</div>';
      content+= '</div></div></div></div>';

      dashPayments_content.html( content );

      var reportOptions ='<option ></option>';
      reportOptions    +='<option value="3" >Income Statement</option>';
      reportOptions    +='<option value="2" >Cash Flow Statement</option>';
      reportOptions    +='<option value="1" >Balance Sheet</option>';
      reportOptions    +='<option value="4" >Organization Payment Request</option>';
      reportOptions    +='<option value="5" >MobiCash Payment</option>';
      reportOptions    +='<option value="6" >Accounts Receivable</option>';
      reportOptions    +='<option value="7" >Transaction Journal </option>';

      var selectGrProclamationType = $("#reportTypeNumber");
      //selectGrProclamationType.select2();//.bind('change', getPaReportTypeSelected );
      selectGrProclamationType.html( reportOptions );
  
  dashContentainer_Payments.slideDown();
  dashPayments_content.slideDown();
  
  validateReportSubmit();
}

function getPaReportTypeSelected()
{
    var selectGrProclamationType  = $("#selectGrProclamationType");
    var selected                  = selectGrProclamationType.select2('val');

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


  function loadFeeStudentList( td_id , td_feeName , td_feeDiscription )
  { 

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

/* Active button of new client view */
function AccountingNewClientJS(){
  alert("New Client Button Fired!");
  //load the client list when a button is clicked
  $("#AccountingNewClientList").bind("click", load_Accounting_List_Client);
  //active select 2 for the client name name
  //activate the drop down menu to allow to load new client upon drop
  $("#paAccountingNewClientName").select2().bind("dropdown", getAccountingClientList);
}
/* Active button of new client view */
function AccountingNewVendorJS(){
  //load the client list when a button is clicked
  $("#AccountingNewVendorList").bind("click", load_Accounting_List_Vendor);
  //active select 2 for the client name name
  //activate the drop down menu to allow to load new client upon drop
  $("#paAccountingNewClientName").select2().bind("dropdown", getAccountingClientList);
}
/* Active button of new client view */
function AccountingNewProductJS(){
  //load the client list when a button is clicked
  $("#AccountingNewProductList").bind("click", load_Accounting_List_Product);
  //active select 2 for the client name name
  //activate the drop down menu to allow to load new client upon drop
  $("#paAccountingNewClientName").select2().bind("dropdown", getAccountingClientList);
}

/* Active button of new client view */
function AccountingNewSponsorJS(){
  //load the client list when a button is clicked
  //$("#AccountingNewSponsorList").bind("click", load_Accounting_List_Organization);
  //active select 2 for the client name name
  //activate the drop down menu to allow to load new client upon drop
  //$("#paAccountingNewClientName").select2().bind("dropdown", getAccountingClientList);
}
/* Active button of new client view */
function AccountingNewCapitalEquityJS(){
  //load the client list when a button is clicked
  $("#AccountingNewCapitalEquityList").bind("click", load_Accounting_List_Capital_Equity);
  //active select 2 for the client name name
  //activate the drop down menu to allow to load new client upon drop
  //$("#paAccountingNewClientName").select2().bind("dropdown", getAccountingClientList);
  //activate the date plugin to this form
  $("#paNewAccountDate").daterangepicker({
      singleDatePicker: true,
      showDropdowns: true ,
      locale: {
        format: 'DD/MM/YYYY'
      }
  });
}
/* Active button of list client view */
function AccountingListClientJS(){
  //active select 2 for the client name name
  //activate the drop down menu to allow to load new client upon drop
  $("#AccountingNewClient").bind("click", load_Accounting_New_Client);
}
/* Active button of list vendor view */
function AccountingListVendorJS(){
  //active select 2 for the client name name
  //activate the drop down menu to allow to load new client upon drop
  $("#AccountingNewVendor").bind("click", load_Accounting_New_Vendor);
}
/* Active button of list client view */
function AccountingListProductJS(type){
  //activate the new product button which is in main content
  if ( type == 1 ) {
      $("#pa_AccountingNewProduct").bind("click", load_Accounting_New_Product_WeSell );

  }else if( type == 2 ){
    $("#pa_AccountingNewProduct").bind("click", load_Accounting_New_Product_WeBuy );

  }else{
    console.log("Some else");

  }
  

  //activate the drop down menu to allow to load new client upon drop
  //$("#AccountingNewProduct").bind("click", load_Accounting_New_Client);
}

/* Active button of list client view */
function AccountingListSponsorJS(){
  //active select 2 for the client name name
  
  //activate the drop down menu to allow to load new client upon drop
  $("#AccountingNewSponsor").bind("click", load_Accounting_New_Sponsor);
}

/* Active button of list client view */
function AccountingListCapitalEquityJS(){
  //active select 2 for the client name name
  
  //activate the drop down menu to allow to load new client upon drop
  $("#AccountingNewCapitalEquity").bind("click", load_Accounting_New_Capital_Equity);
}
function formatInvoiceItemsList(state){
  if (!state.id) { return state.text; }
    var $state = $(
    '<span><img src="packages/assets/img/add.png" class="img-flag" /> ' + state.text + '</span>'
    );
    return $state;
  
}

/* Active button of new invoice view */
function AccountingNewLoanJS(){
  //active select 2 for the client name name
  //activate the drop down menu to allow to load new client upon drop
  //$("#AccountingLoanPaymentMode").select2().bind('change', paCreateNewClient);
  $("#AccountingLoanPaymentMode").select2({
    templateResult: formatInvoiceItemsList
  });
  
  //activate the calendar to come up when use click date field
  $('#AccountingFirstPaymentDate').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true ,
      locale: {
        format: 'YYYY-MM-DD'
      }
  });

  $('#AccountingFirstPaymentDate').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true ,
      locale: {
        format: 'YYYY-MM-DD'
      }
  });

  $('#AccountingLastPaymentDate').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true ,
      locale: {
        format: 'YYYY-MM-DD'
      }
  });
  
  //allow a modal to open programaticaly
  $('#Model_Dash_PaNewClient').modal({ show: false})
  
  //activate the invoice list button now
  $("#AccountingExpenseListLoan").click(load_acc_loan_to_pay);
  
  //acivate the form please for the current diplayed form
  validateNewLoan();
  
}

/* Active button on List of invoice view */
function AccountingListInvoiceJS(){
  
  //active the add new button 
  $("#AccountingNewInvoice").click(load_Accounting_New_Invoice);
  
}

/* Active button on List of invoice view */
function AccountingListExpenseTypeJS(){
  
  //active the add new button 
  $("#AccountingExpenseNewType").click(load_Accounting_Expense_New_Type);
  
}
/* Active button on List of invoice view */
function AccountingListLoanToPayJS(){
  
  //active the add new button 
  $("#AccountingExpenseNewLoan").click(load_Accounting_Expense_New_Loan);
  
}
/* Active button on List of invoice view */
function AccountingListSchoolFeesCurrentJS(){
  
  //active the add new button 
  $("#AccountingSchoolFeesNewCurrentFeesPayment").click(load_Accounting_School_Fees_New_Current_Fee);
  
}
/* Active button on List of invoice view */
function AccountingSchoolFeesNewCurrentCurrentJS(){
  
  //activate select two functions
  $("#newCurrentFeeType").select2();
  $("#newCurrentFeeStudent").select2();
  
  //active the add new button 
  $("#AccountingSchoolFeesListCurrentFeesPayment").click(load_Accounting_School_Fees_Current_Fees);
  
}
/* Active button on List of invoice view */
function AccountingSchoolFeesNewDebtJS(){
  
  //activate select two functions
  $("#AccountingSchoolFeesNewDebtStudent").select2();
  
  //active the add new button 
  
  
}
/* Active button on List of invoice view */
function AccountingSchoolFeesNewOverDueJS(){
  
  //activate select two functions
  $("#AccountingSchoolFeesNewDebtStudent").select2();
  $("#newCurrentFeeStudent").select2();
  //active the add new button 
  $("#AccountingSchoolFeesMoreOverDue").click(load_Accounting_School_Fees_Over_Due_Payment);
  
  
  $('#paNewOverDueDate').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true ,
      locale: {
        format: 'DD/MM/YYYY'
      }
  });
  
}
/* Active button on List of invoice view */
function AccountingSchoolFeesListFeesType(){
  
  //active the add new button 
  $("#AccountingSchoolFeesMoreFeeType").click(load_Accounting_School_Fees_Fee_Type);
  
}
/* Active button on List of invoice view */
function AccountingSchoolFeesFeeTypeJS(){
  
  //active the add new button 
  $("#AccountingSchoolFeesNewFeeType").click(load_Accounting_School_Fees_New_Fee_Type);
  
}
/* Active button on List of invoice view */
function AccountingSchoolFeesDebtsJS(){
  
  //active the add new button 
  $("#AccountingSchoolFeesNewDebt").click(load_Accounting_School_Fees_New_Debt);
  $("#AccountingSchoolFeesNewDebtUpload").click(load_Accounting_School_Fees_New_Debt_Upload);
  
}
/* Active button on List of invoice view */
function AccountingSchoolFeesOverDuePaymentJS(){
  
  //active the add new button 
  $("#AccountingSchoolFeesNewOverDue").click(load_Accounting_School_Fees_New_Over_Due_Payment);
  
}
/* Active button on List of invoice view */
function AccountingStudentOnFeeJS(){
  /* 
  //wait for madal to complete loading and activate the loaded buttons
  setTimeout(function(e){
    //activate the new Student button
    $("#paAccountingNewStudentOnFee").bind("click", load_Accounting_New_Student_On_Fees);
    $("#dashboardAccountingAddNewStudentOnFeeType").select2();//.bind("click", load_Accounting_New_Student_On_Fees);
    //activate the submit button
    $("#paAccountingAddStudentToFeePaymentSubmit").bind("click", saveNewStudentToFeePayment);
    //alert($("#paAccountingNewStudentOnFee").attr("src"));//.bind("click", load_Accounting_New_Student_On_Fees);
  }, 1000);
  // */
  
}

/* Active button on List of invoice view */
function AccountingListBillJS(){
  
  //active the add new button 
  $("#AccountingNewBill").click( function(){

    load_Accounting_New_Bill();
    
  });
  
}

/* Active button on List of invoice view */
function AccountingSchoolFeesJS(){
  
  //active the Current School Fees Payment Button
  $("#AccountingSchoolFeesCurrentFees").click(load_Accounting_School_Fees_Current_Fees);
  
  //active the Fee Types Button
  $("#AccountingSchoolFeesFeeType").click(load_Accounting_School_Fees_Fee_Type);
  
  //active the Debt Button
  $("#AccountingSchoolFeesDebts").click(load_Accounting_School_Fees_Debts);
  
  //active the Debt Button
  $("#AccountingSchoolFeesOverDuePayment").click(load_Accounting_School_Fees_Over_Due_Payment);
  
  //active the unassigned button
  $("#AccountingSchoolFeesUnAssigned").click(load_Accounting_School_Fees_Un_Assigned);
  
  //active the downloads button
  $("#AccountingSchoolPaymentReminder").click(load_Accounting_School_PaymentReminder);
  
  //active the downloads button
  $("#AccountingStudentWithoutFees").click(load_Accounting_StudentWithoutFees);  
  
}


function PaNewIncomeJS(){

}

function PaNewExpenseJS(){

  
}

function paCreateNewVendor(){
  //get the instance of the trigger
  var sender = $("#paNewBillVendor");
  var selectedValue = sender.select2('val');
  
  if(selectedValue == 'Add'){
    //open the new 
    $('#Model_Dash_PaNewVendor').modal('show');
  }
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

function getAccountingClientList(){
  alert("Received");
}

function getPaNewIncomeSource(){

  var  paNewIncomeSource   = $("#paNewIncomeSource");
  var  selected            = paNewIncomeSource.select2('val');

  $("#paNewIncomeAmount").val("");

  //console.log(selected);
  var PaNewIncomeSchoolFeesContainer          = $("#PaNewIncomeSchoolFeesContainer");
  var PaNewIncomeAccountsReceivablesContainer = $("#PaNewIncomeAccountsReceivablesContainer");
  var PaNewIncomeOtherContainer               = $("#PaNewIncomeOtherContainer");
  

  if ( selected == 1 ) {
  //send the request to check the student list here and return json string
  
  //populate the returned student list in the select2 box
    PaNewIncomeSchoolFeesContainer.slideDown();
    PaNewIncomeAccountsReceivablesContainer.hide();
    PaNewIncomeOtherContainer.hide();
    

  }else if( selected == 2 ){
    
    PaNewIncomeSchoolFeesContainer.hide();
    PaNewIncomeAccountsReceivablesContainer.slideDown();
    PaNewIncomeOtherContainer.hide();
    $("#studentUnpaidInvoiContainner").hide();

  }else{

    PaNewIncomeSchoolFeesContainer.hide();
    PaNewIncomeAccountsReceivablesContainer.hide();
    PaNewIncomeOtherContainer.slideDown();


  }

}

function getPaUpdateIncomeSource(){

  var  paNewIncomeSource   = $("#paNewIncomeSource");
  var  selected            = paNewIncomeSource.select2('val');

  //console.log(selected);
  var PaNewIncomeSchoolFeesContainer          = $("#PaNewIncomeSchoolFeesContainer");
  var PaNewIncomeAccountsReceivablesContainer = $("#PaNewIncomeAccountsReceivablesContainer");
  var PaNewIncomeOtherContainer               = $("#PaNewIncomeOtherContainer");
  

  if ( selected == 1 ) {
  //send the request to check the student list here and return json string
  
  //populate the returned student list in the select2 box
    PaNewIncomeSchoolFeesContainer.slideDown();
    PaNewIncomeAccountsReceivablesContainer.hide();
    PaNewIncomeOtherContainer.hide();
    

  }else if( selected == 2 ){
    
    PaNewIncomeSchoolFeesContainer.hide();
    PaNewIncomeAccountsReceivablesContainer.slideDown();
    PaNewIncomeOtherContainer.hide();
    $("#studentUnpaidInvoiContainner").hide();

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


