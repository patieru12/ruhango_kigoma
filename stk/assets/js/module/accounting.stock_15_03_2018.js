var nextUrl = "";
var SupplierInitialized  = false;
var ReceiverInitialized  = false;
var StockItemInitialized = false;
function DeleteStockOperation(stockID){
  var tdDelete = $("#delete" + stockID)
  var tdHTML = tdDelete.html();
  tdDelete.html("...");
  //here sendthe delete commend
  var formData = "_method=DELETE";
  $.ajax({
    url: base_url+"accounting/stockDistribute/" + stockID,
    dataType: 'json',
    type: 'POST',
    data: formData,
    success: function ( res ) {
      if ( res.success ){
        load_Accounting_StockRecord();
        $.gritter.add({
          title: 'Success',
          text: res.success,
          class_name: 'success gritter-center',
          time: ''
        });
      } else if(res.error){
        tdDelete.html(tdHTML);
        $.gritter.add({
          title: 'Deleting Error',
          text: res.error,
          class_name: 'danger gritter-center',
          time: ''
        });
      }
    }
  }).done(function() {
  }).fail(function() {
    tdDelete.html(tdHTML);
    $.gritter.add({
      title: 'Delete Stock Operation Fail',
      text: 'Fail to delete Selected Operation Please Try Again!',
      class_name: 'danger gritter-center',
      time: ''
    });
  }).always(function() {
  });
}

function optionizeSchoolItem(){
  var url = base_url + "accounting/stockReceive";
  var itemOptions = "<option value='All'>All</option>";
  $.getJSON( url, function( data ) {
    $.each( data, function(i, item) {
      itemOptions += "<option value='" + item.id + "'>" + item.name + "</option>";
    });
  }).done(function() {
    $("#accountingStockRecordItem").html(itemOptions);
  }).fail(function() {
    
  }).always(function() {
  });
}

function UpdateStockOperationReceive(stockID){
  //console.log("Distribute Operation Being Updated!" + stockID);
  var updateModalReceiveContent = $("#updateModalReceiveContent");
  var url = base_url + "accounting/stockReceive"; //Load the complete list of stock Items
  $.getJSON( url, function( itemTypes ) {
    var url2 = base_url + "accounting/stockReceive/0"; //Load the List of Receivers
    $.getJSON( url2, function( receiversList ) {
      var url3 = base_url + "accounting/stock/" + stockID + "/edit"; //Load the Existing operation
      $.getJSON( url3, function( operationData ) {
        var itemListOptions = "";
        $.each(itemTypes, function(i, item){
          itemListOptions += "<option value='" + item.id +"'" + (item.id == operationData.itemTypeID?"selected":"") + ">" + item.name + "</option>";
        });
        var operatorListOptions = "";
        $.each(receiversList, function(i, item){
          operatorListOptions += "<option value='" + item.id +"' " + (item.id == operationData.operatorID?"selected":"") + ">" + item.name + "</option>";
        });
        var formContentHtml = '\
                          <div class="modal-header">\
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                            <h4 class="modal-title text-left" id="myModalLabel22">Update Received Item</h4>\
                          </div>\
                          <div class="form-group" id="formAccountingStockReceiveUpdateLoading" style="display: none;">\
                            <center>\
                              <div class="row">\
                                <img src="../packages/assets/img/loading.gif" alt="Loading..." height="35">\
                              </div>\
                              <div class="row" >\
                                <p>saving...</p>\
                              </div>\
                            </center>\
                          </div>\
                          <form class="form-horizontal" role="form" id="formAccountingStockUpdateReceive" >\
                            <div class="modal-body" id="accountingStockOperationReceiveUpdateForm">\
                              <input type=hidden name=_method value="PUT" />\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Item Type</label>\
                                <div class="col-sm-7">\
                                  <select name="receiveItemTypeUpdate" class="form-control" id="receiveItemTypeUpdate" placeholder="Select Received Item">\
                                    <option></option>\
                                    ' + itemListOptions + '\
                                  </select>\
                                </div>\
                              </div>\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Quantity</label>\
                                <div class="col-sm-7">\
                                  <input type="text" name="receiveItemQuantityUpdate" class="form-control" id="receiveItemQuantityUpdate" value="' + operationData.quantity + '" placeholder="Enter Distributed Quantity" />\
                                </div>\
                              </div>\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Date</label>\
                                <div class="col-sm-7">\
                                  <input type="text" name="receiveItemDateUpdate" class="form-control" id="receiveItemDateUpdate" value="' + operationData.date + '" placeholder="Enter Action Date" />\
                                </div>\
                              </div>\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Supplier</label>\
                                <div class="col-sm-7">\
                                  <select name="receiveItemSupplierUpdate" class="form-control" id="receiveItemSupplierUpdate" placeholder="Select item Supplier">\
                                    <option></option>\
                                    ' + operatorListOptions + '\
                                  </select>\
                                </div>\
                              </div>\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Comment</label>\
                                <div class="col-sm-7">\
                                  <input type="text" name="receiveItemCommentUpdate" class="form-control" id="receiveItemCommentUpdate" placeholder="Provide comment if any" />\
                                </div>\
                              </div>\
                            </div>\
                            <div class="modal-footer">\
                              <div class="btn-group">\
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                                <button type="submit" class="btn btn-primary" >Update</button>\
                              </div>\
                            </div>\
                          </form>';
        updateModalReceiveContent.html(formContentHtml);
        $('#Model_AccountingStockUpdateReceiveOperation').modal({ show: true});
      }).done(function() {
        // activate datatable on the summary table please
        validateAccountingStockUpdateReceive(stockID);
      });
    });
  });
}

function UpdateStockOperationDistribute(stockID){
  //console.log("Distribute Operation Being Updated!" + stockID);
  var updateModalDistributeContent = $("#updateModalDistributeContent");
  var url = base_url + "accounting/stockReceive"; //Load the complete list of stock Items
  $.getJSON( url, function( itemTypes ) {
    var url2 = base_url + "accounting/stockReceive/1"; //Load the List of Receivers
    $.getJSON( url2, function( receiversList ) {
      var url3 = base_url + "accounting/stock/" + stockID + "/edit"; //Load the Existing operation
      $.getJSON( url3, function( operationData ) {
        var itemListOptions = "";
        $.each(itemTypes, function(i, item){
          itemListOptions += "<option value='" + item.id +"'" + (item.id == operationData.itemTypeID?"selected":"") + ">" + item.name + "</option>";
        });
        var operatorListOptions = "";
        $.each(receiversList, function(i, item){
          operatorListOptions += "<option value='" + item.id +"' " + (item.id == operationData.operatorID?"selected":"") + ">" + item.name + "</option>";
        });
        var formContentHtml = '\
                          <div class="modal-header">\
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                            <h4 class="modal-title text-left" id="myModalLabel22">Update Item Distribution</h4>\
                          </div>\
                          <div class="form-group" id="formAccountingStockDistributeUpdateLoading" style="display: none;">\
                            <center>\
                              <div class="row">\
                                <img src="../packages/assets/img/loading.gif" alt="Loading..." height="35">\
                              </div>\
                              <div class="row" >\
                                <p>saving...</p>\
                              </div>\
                            </center>\
                          </div>\
                          <form class="form-horizontal" role="form" id="formAccountingStockUpdateDistribute" >\
                            <div class="modal-body" id="accountingStockOperationDistributeUpdateForm">\
                              <input type=hidden name=_method value="PUT" />\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Item Type</label>\
                                <div class="col-sm-7">\
                                  <select name="distributeItemTypeUpdate" class="form-control" id="distributeItemTypeUpdate" placeholder="Select Distributed Item">\
                                    <option></option>\
                                    ' + itemListOptions + '\
                                  </select>\
                                </div>\
                              </div>\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Quantity</label>\
                                <div class="col-sm-7">\
                                  <input type="text" name="distributeItemQuantityUpdate" class="form-control" id="distributeItemQuantityUpdate" value="' + operationData.quantity + '" placeholder="Enter Distributed Quantity" />\
                                </div>\
                              </div>\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Date</label>\
                                <div class="col-sm-7">\
                                  <input type="text" name="distributeItemDateUpdate" class="form-control" id="distributeItemDateUpdate" value="' + operationData.date + '" placeholder="Enter Action Date" />\
                                </div>\
                              </div>\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Receiver</label>\
                                <div class="col-sm-7">\
                                  <select name="distributeItemReceiverUpdate" class="form-control" id="distributeItemReceiverUpdate" placeholder="Select item Receiver">\
                                    <option></option>\
                                    ' + operatorListOptions + '\
                                  </select>\
                                </div>\
                              </div>\
                              <div class="form-group">\
                                <label class="col-sm-3 control-label">Comment</label>\
                                <div class="col-sm-7">\
                                  <input type="text" name="distributeItemCommentUpdate" class="form-control" id="distributeItemCommentUpdate" placeholder="Provide comment if any" />\
                                </div>\
                              </div>\
                            </div>\
                            <div class="modal-footer">\
                              <div class="btn-group">\
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                                <button type="submit" class="btn btn-primary" >Update</button>\
                              </div>\
                            </div>\
                          </form>';
        updateModalDistributeContent.html(formContentHtml);
        $('#Model_AccountingStockUpdateDistributeOperation').modal({ show: true});
      }).done(function() {
        // activate datatable on the summary table please
        validateAccountingStockUpdateDistribute(stockID);
      });
    });
  });
  
}

function load_Accounting_StockRecord(){
  var stockSummaryContainer = $("#stockSummaryContainer");
  stockSummaryContainer.slideUp();

  // Send the request toget the currentpossible records
  var url = base_url+"stockRecord.php?accountingStockRecordItem=All&accStockOperationType=All&accountingStockRecordMonth=now";
  if (nextUrl == ""){
    nextUrl = url;
  }
  url = nextUrl;
  var printUrl = url.replace("stockRecord.php?","printStockRecord.php?");

  content ='<div class="row">';
    content+= '<div class="col-sm-12">';
      content+= '<div class="col-sm-4">';
        content+= '<div class="row">';
          content+= '<h4>Review Records</h4>';
        content+= '</div>';
        content+= '<div class="row">';
          content+= '<form  id="formDashGetAccountingStockReport" class="form-horizontal top-buffer" role="form" >';
            content+= '\
                      <div class="form-group">\
                        <label class="col-sm-1"></label>\
                        <label class="col-sm-2"><input type="radio" name="accStockOperationType" value="All" checked> All</label>\
                        <label class="col-sm-4"><input type="radio" name="accStockOperationType" value="0"> Received</label>\
                        <label class="col-sm-4"><input type="radio" name="accStockOperationType" value="1"> Distributed</label>\
                        <label class="col-sm-1"></label>\
                      </div>\
                      <div class="form-group">\
                        <label class="col-sm-3 control-label">Month</label>\
                        <div class="col-sm-7">\
                          <input type="text" name="accountingStockRecordMonth" class="form-control" id="accountingStockRecordMonth" />\
                        </div>\
                      </div>\
                      <div class="form-group">\
                        <label class="col-sm-3 control-label">Item</label>\
                        <div class="col-sm-7">\
                          <select name="accountingStockRecordItem" id="accountingStockRecordItem" class="form-control select2-input" >\
                            <option value="All">All</option>\
                          </select>\
                        </div>\
                      </div>\
                      <div class="form-group top-buffer">\
                        <label class="col-sm-3 control-label"></label>\
                        <div class="col-sm-7 text-center">\
                          <button class="btn btn-lg btn-primary" type="submit" id="AccReviewRecordsSubmit" >View Records</button>\
                        </div>\
                      </div>';
          content+= '</form>';
        content+= '</div>';
      content+= '</div>';
      content+= '<div class="col-sm-8" id="pa_AccountingSchoolStockRecordContainer">';
        content+='<div class="row text-center" id="pa_AccountingSchoolStockRecordData">';
          content+= '<h4  class="col-sm-10">Stock Record this month</h4>';
          content+= '<div class="col-sm-2">\
                      <a href="' + printUrl + '" target="_blank" class="btn btn-success" id="accountingStockDownloadReport"> <span class="fa fa-download"></span> Download</a>\
                     </div>';
          content+= '<div class="row" >';
            content+= '<div id="pa_StockRecord">\
                        Stock Records\
                      </div>\
                    </div>';
        content+= '</div>';
      content+= '</div>';
    content+= '</div>';
  content+= '</div>\
      <div class="modal fade" id="Model_AccountingStockUpdateReceiveOperation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
        <div class="modal-wrapper">\
          <div class="modal-dialog">\
            <div class="modal-content" id="updateModalReceiveContent">\
            </div>\
          </div>\
        </div>\
      </div>\
      <div class="modal fade" id="Model_AccountingStockUpdateDistributeOperation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">\
        <div class="modal-wrapper">\
          <div class="modal-dialog">\
            <div class="modal-content" id="updateModalDistributeContent">\
            </div>\
          </div>\
        </div>\
      </div>';

  stockSummaryContainer.html( content );

  
  // console.log("Requestable URL:" + url);
  $.getJSON( url, function( data ) {
          // dashClientContainer.html( content );
          var tableContent = '<table class="table abDataTable table-bordered" id="acStockSummaryStatus" ><thead><tr><th class="text-center">Date</th><th class="text-center">Commited By</th><th class="text-center">Item</th><th class="text-center">Quantity</th><th class="text-center">Action</th><th class="text-center"></th></tr></thead>';
              //tableContent += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th style="text-align:right"></th><th style="text-align:right"></th><th style="text-align:right"></th></tr></tfoot>';
              tableContent += '<tbody>';
              var itemCounter = 1;
          $.each( data, function(i, item) {
            tableContent  +='<tr id="'+item.id+'" >';
              tableContent += '<td class="text-left">' + (item.operationDate) + '</td>';
              tableContent += '<td class="text-left">' + (item.operationType == 0?'<span class="label label-primary" title="Supplier">':'<span class="label label-success" title="receiver:">') + item.operatorName + '</label></td>';
              tableContent += '<td class="text-left">' + item.itemName + '</td>';
              tableContent += '<td class="text-right">' + item.operationQuantity + '</td>';
              tableContent += '<td class="text-center"><a href="#" onclick="' + (item.operationType == 0?'UpdateStockOperationReceive(' + item.id + ')':'UpdateStockOperationDistribute(' + item.id + ')') + '; return false;" class="text-blue"><span class="fa fa-pencil"></span></a></td>';
              tableContent += '<td id="delete' + item.id + '" class="text-center"><a href="#" onclick="DeleteStockOperation(' + item.id + '); return false;" class="text-red"><span class="fa fa-times"></a></td>';
            tableContent  +='</tr>';
          });
      tableContent += '</table>';
          var pa_StockRecord = $("#pa_StockRecord");
          pa_StockRecord.html(tableContent);
        }).done(function() {
          // activate datatable on the summary table please
          $("#acStockSummaryStatus").DataTable( {
              "paging":   true
            });
          // dashClientContainer.slideDown();
        }).fail(function() {

        }).always(function() {

        });
  //send the prepared content to the screen for user
  stockSummaryContainer.slideDown();

  validateStockRecordReport();
}

function load_Accounting_StockRecordTable(){
  var url = base_url+"accounting/stockDistribute?accountingStockRecordItem=All&accStockOperationType=All&accountingStockRecordMonth=now";
  if (nextUrl == ""){
    nextUrl = url;
  }
  url = nextUrl;
  //console.log("Requestable URL:" + url);
  $.getJSON( url, function( data ) {
          // dashClientContainer.html( content );
          var tableContent = '<table class="table abDataTable table-bordered" id="acStockSummaryStatus" ><thead><tr><th class="text-center">Date</th><th class="text-center">Commited By</th><th class="text-center">Item</th><th class="text-center">Quantity</th><th class="text-center">Action</th><th class="text-center"></th></tr></thead>';
              //tableContent += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th style="text-align:right"></th><th style="text-align:right"></th><th style="text-align:right"></th></tr></tfoot>';
              tableContent += '<tbody>';
              var itemCounter = 1;
          $.each( data, function(i, item) {
            tableContent  +='<tr id="'+item.id+'" >';
              tableContent += '<td class="text-left">' + (item.operationDate) + '</td>';
              tableContent += '<td class="text-left">' + (item.operationType == 0?'<span class="label label-primary" title="Supplier">':'<span class="label label-success" title="receiver:">') + item.operatorName + '</label></td>';
              tableContent += '<td class="text-left">' + item.itemName + '</td>';
              tableContent += '<td class="text-right">' + item.operationQuantity + '</td>';
              tableContent += '<td class="text-center"><a href="#" onclick="' + (item.operationType == 0?'UpdateStockOperationReceive(' + item.id + ')':'UpdateStockOperationDistribute(' + item.id + ')') + '; return false;" class="text-blue"><span class="fa fa-pencil"></span></a></td>';
              tableContent += '<td id="delete' + item.id + '" class="text-center"><a href="#" onclick="DeleteStockOperation(' + item.id + '); return false;" class="text-red"><span class="fa fa-times"></a></td>';
            tableContent  +='</tr>';
          });
      tableContent += '</table>';
          var pa_StockRecord = $("#pa_StockRecord");
          pa_StockRecord.html(tableContent);
        }).done(function() {
          // activate datatable on the summary table please
          $("#acStockSummaryStatus").DataTable( {
              "paging":   true
            });
          // dashClientContainer.slideDown();
        }).fail(function() {

        }).always(function() {

        });
}

function dateReportChange(){
  var dashClientContainer = $("#dashPayments_content");
  dashClientContainer.slideUp();

  var date = $("#accountingStockMonthlyReportMonth").val();

  var url = base_url+"monthlyReport.php?id=" + date.replace(/\//g,"_");
        $.getJSON( url, function( data ) {
          // dashClientContainer.html( content );
          var tableContent = '<table class="table abDataTable table-bordered" id="acStockSummaryStatus" ><thead><tr><th class="text-center">#</th><th class="text-center">Item</th><th class="text-center">Carried Forward</th><th class="text-center">Received</th><th class="text-center">Total Consumption</th><th class="text-center">Current Stock</th></tr></thead>';
              //tableContent += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th style="text-align:right"></th><th style="text-align:right"></th><th style="text-align:right"></th></tr></tfoot>';
              tableContent += '<tbody>';
              var itemCounter = 1;
          $.each( data, function(i, item) {
            tableContent  +='<tr id="'+item.id+'" >';
              tableContent += '<td class="text-left">' + (itemCounter++) + '</td>';
              tableContent += '<td class="text-left">' + item.itemName + '</td>';
              tableContent += '<td class="text-left">' + item.carriedForward + '</td>';
              tableContent += '<td class="text-left">' + item.receivedQuantity + '</td>';
              tableContent += '<td class="text-left">' + item.distributedQuantity + '</td>';
              tableContent += '<td class="text-left">' + item.currentStock + '</td>';
            tableContent  +='</tr>';
          });
      tableContent += '</table>';
          var stockSummaryContainer = $("#stockSummaryContainer");
          var reportHeader = '<div class="row text-center">';
          reportHeader += '<h4  class="col-sm-10">Monthly Stock Status</h4>';
          reportHeader += '<div class="col-sm-2">\
                            <a href="./printMonthlyReport.php?id=' + date.replace(/\//g,"_") + '" target="_blank" class="btn btn-success" id="accountingStockDownloadReport"> <span class="fa fa-download"></span> Download</a>\
                           </div>\
                          </div>';
          stockSummaryContainer.html(reportHeader + tableContent);
        }).done(function() {
          // activate datatable on the summary table please
          $("#acStockSummaryStatus").DataTable( {
              "paging":   true
            });
          dashClientContainer.slideDown();
        }).fail(function() {

        }).always(function() {

        });
}

function AccStockCurrentStatus(){
	//get the instance of container division 
    var dashClientContainer = $("#dashPayments_content");
  
  	//get the instance of the content division
    var dashContentainer_Payments = $("#dashContentainer_Payments");
    
  	//hide the container first
    dashClientContainer.slideUp();

    var content = '';
      	content += '<div class="row">';
        	content += '<div class="col-md-12"><div class="grid"><div class="grid-body">';
           		content += '<div class="row">';
	            	content += '<div class="col-sm-4" >\
	            					<div class="btn-group">\
	            						<div class="btn-group">\
	            							<button type="button" class="btn btn-success" data-toggle="modal" data-target="#Model_AccountingStockReceive" id="AccountingNewStockReceive" ><strong><i class="fa fa-plus"></i> Receive</strong></button>\
	            						</div>\
	            						<div class="btn-group">\
	            							<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#Model_AccountingStockDistribute" id="AccountingNewStockDistribute" ><strong><i class="fa fa-plus"></i> Distribute</strong></button>\
	            						</div>\
	            					</div>\
	            				</div>';
	            	content += '<label class="col-sm-2 text-right">Monthly Report</label>\
	            				<div class="col-md-2 ">\
	            					<input type="text" name="month" id="accountingStockMonthlyReportMonth" class="form-control" placeholder="Enter Month" />\
	            				</div>';
	            	content += '<div class="col-md-4">\
            						<div class="btn-group pull-right">\
            							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="" id="AccountingStockRecord" ><strong><i class="fa fa-bar-chart"></i> Records</strong></button>\
            						</div>\
	            				</div>';
	            	// content += '<div class="col-md-2 ">Day: <span class="badge bg-green" id="accAnalyticsDay">12</span></div>';
	          	content += '</div>';
        content += '</div></div></div></div>';
      	content += '</div>';
      	content += '<div class="row" >';
            content += '<div class="col-md-12"><div class="grid email"><div class="grid-body" id="accAnalyticsContainer">';
              
              content += '<div class="row">';
                content += '<div class="col-md-12" id="stockSummaryContainer">';
                content += '</div>';
              content += '</div>';

            content += '</div></div></div></div>';
          content += '</div>';
        //send the prepared content to the screen for user
        dashClientContainer.html( '<div class="row"><div class="col-md-12"><div class="grid"><div class="grid-body"><center>Loading...</center></div></div></div></div>' );

        //load the contetn now
        dashContentainer_Payments.slideDown();
        //

        dashClientContainer.slideDown();

        if(!SupplierInitialized){

          //load the List item to allow new option
          var itemsUrl = base_url + "stockReceive.php?id=0";
          var optionsItemTypes  = [];
          $.getJSON(itemsUrl, function(data){
            // Write the new data to hidden input
            $.each(data, function(i, item){
              var row = [];
              row['id'] = item.id;
              row['text'] = item.name;
              optionsItemTypes.splice(i,0,row);
            });
          }).done(function(){
            //HereHandle action tobe perfomed at the end
            select2GenerateNewOption("receiveItemSupplier", optionsItemTypes);
            SupplierInitialized = true;
          });
        }

        if(!ReceiverInitialized){

          //load the List item to allow new option
          var itemsUrl = base_url + "stockReceive.php?id=1";
          var optionsReceivers  = [];
          $.getJSON(itemsUrl, function(data){
            // Write the new data to hidden input
            $.each(data, function(i, item){
              var row = [];
              row['id'] = item.id;
              row['text'] = item.name;
              optionsReceivers.splice(i,0,row);
            });
          }).done(function(){
            //HereHandle action tobe perfomed at the end
            select2GenerateNewOption("distributeItemReceiver", optionsReceivers);
            ReceiverInitialized = true;
          });
        }

        if(!StockItemInitialized){

          //load the List item to allow new option
          var itemsUrl = base_url + "stockItemType.php";
          var optionsStockItemTypes  = [];
          $.getJSON(itemsUrl, function(data){
            // Write the new data to hidden input
            $.each(data, function(i, item){
              var row = [];
              row['id'] = item.id;
              row['text'] = item.name;
              optionsStockItemTypes.splice(i,0,row);
            });
          }).done(function(){
            //HereHandle action tobe perfomed at the end
            select2GenerateNewOption("receiveItemType", optionsStockItemTypes);
            StockItemInitialized = true;
          });
        }

        //get the current stock status and activate all button for the new request
        var url = base_url+"stock.php";
        $.getJSON( url, function( data ) {
        	dashClientContainer.html( content );
        	var tableContent = '<table class="table abDataTable table-bordered" id="acStockSummaryStatus" ><thead><tr><th class="text-center">#</th><th class="text-center">Item</th><th class="text-center">Carried Forward</th><th class="text-center">Received</th><th class="text-center">Total Consumption</th><th class="text-center">Current Stock</th></tr></thead>';
			        //tableContent += '<tfoot><tr><th colspan="2" style="text-align:right">Total:</th><th style="text-align:right"></th><th style="text-align:right"></th><th style="text-align:right"></th></tr></tfoot>';
			        tableContent += '<tbody>';
			        var itemCounter = 1;
					$.each( data, function(i, item) {
						tableContent  +='<tr id="'+item.id+'" >';
							tableContent += '<td class="text-left">' + (itemCounter++) + '</td>';
							tableContent += '<td class="text-left">' + item.itemName + '</td>';
							tableContent += '<td class="text-left">' + item.carriedForward + '</td>';
							tableContent += '<td class="text-left">' + item.receivedQuantity + '</td>';
							tableContent += '<td class="text-left">' + item.distributedQuantity + '</td>';
							tableContent += '<td class="text-left">' + item.currentStock + '</td>';
						tableContent  +='</tr>';
					});
			tableContent += '</table>';
        	var stockSummaryContainer = $("#stockSummaryContainer");
        	stockSummaryContainer.html(tableContent);
        }).done(function() {
        	// activate datatable on the summary table please
        	$("#acStockSummaryStatus").DataTable( {
              "paging":   true
          	});
          $("#accountingStockMonthlyReportMonth").datepicker().bind("change", dateReportChange);
          $("#AccountingStockRecord").click(function(e){
            nextUrl = "";
            load_Accounting_StockRecord();
          });
      	}).fail(function() {

      	}).always(function() {

      	});
}

function liNewStaffListImportSubmit()
{
    var formStaffImportListFileLoading  = $('#formStaffImportListFileLoading');
    var formStaffImportListErrorMessage = $('#formStaffImportListErrorMessage');
    var formStaffImportListFile         = $('#formStaffImportListFile');

   formStaffImportListFile.hide();
   formStaffImportListErrorMessage.empty();
   formStaffImportListFileLoading.slideDown();

   var url = base_url+"api/Hr/ImportSchoolStaff"; 

  $.ajax({
      url: url ,
      dataType: 'json',
      type: 'POST',
      data :  new FormData( formStaffImportListFile[0] ) ,
      processData: false,
      contentType: false,
      success: function ( res ) {
        if (res.success)
          {   
            $('#Model_StaffImportList').modal('toggle');
              
              $.gritter.add({
                title: 'Success',
                text: 'Staff List Successfully saved.',
                class_name: 'success gritter-center',
                time: ''
              });
        
            var StaffImportListFile = $('#StaffImportListFile');
            StaffImportListFile.val('');
            // alert("Please Check Why Registered Person Don't Appear on the list!");
            // console.log("dashboardHumanResource .....");
            dashModuleTabClick(5);
            // console.log("......dashboardHumanResource");

          } else {

            formStaffImportListErrorMessage.html('<div class="alert alert-danger"><strong>Details!</strong> '+res.Error_message+'</div>'); 

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

    formStaffImportListFileLoading.hide();
    formStaffImportListFile.show();

  });

}

function validateStaffImportListImport(){
    var formStaffImportListFileLoading  = $('#formStaffImportListFileLoading');
    var formStaffImportListErrorMessage = $('#formStaffImportListErrorMessage');
    var formStaffImportListFile         = $('#formStaffImportListFile');

    formStaffImportListFileLoading.hide();
    formStaffImportListErrorMessage.empty();
    formStaffImportListFile.show();
    
      formStaffImportListFile.formValidation({
        
        framework: 'bootstrap',
        excluded: [':disabled' , ':hidden' ],
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
          StaffImportListFile: {
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
          liNewStaffListImportSubmit();

      });
}