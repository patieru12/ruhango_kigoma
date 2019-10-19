
function LoadIncomeStatementReport(data){
	/* Add the report header to the report now */
	var report = "\
	<div class='row'>\
		<div class='col-md-8'>\
			<h4 class='text-left'>\
				<!--" + data.header.name + "<br />\
				" + data.header.schoolAddress + "<br />-->\
				" + data.header.reportTitle + "<br />\
				" + data.header.reportTime + "<br />\
			</h4>\
		</div>\
		<div class='col-md-4'>\
			<img src='/SchoolLogo/" + data.header.schoolLogo + "' style='width:50px;' alt='Logo Here' />\
		</div>\
	</div>\
	";
	/* Add the report content now. */
	report += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" >\
				<tbody>';
	/* Now check the data and try to load them in the correct format */
	/*var total_revenues = 0;
	var total_expenses = 0;
	 (total_revenues += (data.data.revenues.SchoolFees*1));
	 (total_revenues += (data.data.revenues.InvoicePayment*1));*/
	var revenuesTotal_previous = 0;
	var revenuesTotal_current = 0;
	revenuesTotal_previous	+= data.data.revenues.Sales.previousSales * 1;
	revenuesTotal_current	+= data.data.revenues.Sales.currentSales * 1;
	report += '<tr class="bg-blue"><th colspan=2 class="text-left">Revenues</th><th class="text-center">' + data.header.reportPreviousEnd + '</th><th class="text-center">' + data.header.reportCurrentEnd + '</th></tr>';
	report += '<tr><td>&nbsp;</td><td class="text-left">Sales</td><td class="text-right">' + formatNumber(data.data.revenues.Sales.previousSales) + '</td><td class="text-right">' + formatNumber(data.data.revenues.Sales.currentSales) + '</td></tr>';
	revenuesTotal_previous	+= data.data.revenues.SchoolFees.previousSchoolFees * 1;
	revenuesTotal_current	+= data.data.revenues.SchoolFees.currentSchoolFees * 1;
	report += '<tr><td>&nbsp;</td><td class="text-left">School Fees</td><td class="text-right">' + formatNumber(data.data.revenues.SchoolFees.previousSchoolFees) + '</td><td class="text-right">' + formatNumber(data.data.revenues.SchoolFees.currentSchoolFees) + '</td></tr>';
	report += '<tr class="bg-success"><td colspan=2 class="text-left">Total Revenues</td><th class="text-right">' + formatNumber(revenuesTotal_previous) + '</th><th class="text-right">' + formatNumber(revenuesTotal_current) + '</th></tr>';
	report += '<tr class="bg-blue"><td colspan=4 class="text-left">Cost Of Goods Sold</td></tr>';
	var costOfGoodsSoldTotal_previous = 0;
	var costOfGoodsSoldTotal_current = 0;

	$.each(data.data.expenses.CostOfGoodsSold, function(i, item){
		// console.log(item);
		costOfGoodsSoldTotal_previous 	+= item.previousYearData *1;
		costOfGoodsSoldTotal_current 	+= item.currentYearData *1;
		report += '<tr><td>&nbsp;</td><td class="text-left">' + item.displayName + '</td><td class="text-right">' + formatNumber(item.previousYearData) + '</td><td class="text-right">' + formatNumber(item.currentYearData) + '</td></tr>';
	});
	
	report += '<tr class="bg-success"><td colspan=2 class="text-left">Total Cost of Good Sold</td><th class="text-right">' + formatNumber(costOfGoodsSoldTotal_previous) + '</th><th class="text-right">' + formatNumber(costOfGoodsSoldTotal_current) + '</th></tr>';
	var grossProfitPrevious = revenuesTotal_previous - costOfGoodsSoldTotal_previous;
	var grossProfitCurrent = revenuesTotal_current - costOfGoodsSoldTotal_current;
	report += '<tr class="bg-warning"><td colspan=2 class="text-left">Gross Profit</td><th class="text-right">' + formatNumber(grossProfitPrevious) + '</th><th class="text-right">' + formatNumber(grossProfitCurrent) + '</th></tr>';
	var grossMarginPrevious = (grossProfitPrevious*1) / (revenuesTotal_previous*1)*100;
	var grossMarginCurrent = ( grossProfitCurrent*1) / (revenuesTotal_current*1)*100;
	report += '<tr class="bg-warning"><td colspan=2 class="text-left">Gross Margin</td><th class="text-right">' + formatNumber(grossMarginPrevious.toFixed(1)) + '%</th><th class="text-right">' + formatNumber(grossMarginCurrent.toFixed(1)) + '%</th></tr>';
	report += '<tr class="bg-blue"><td colspan=4 class="text-left">Expenses</td></tr>';
	var expensesTotal_previous = 0;
	var expensesTotal_current = 0;
	$.each(data.data.expenses.Expenses, function(i, item){
		// console.log(item);
		expensesTotal_previous 	+= item.previousYearData * 1;
		expensesTotal_current 	+= item.currentYearData * 1;
		report += '<tr><td>&nbsp;</td><td class="text-left">' + item.displayName + '</td><td class="text-right">' + formatNumber(item.previousYearData) + '</td><td class="text-right">' + formatNumber(item.currentYearData) + '</td></tr>';
	});
	report += '<tr class="bg-success"><td colspan=2 class="text-left">Total Expenses</td><th class="text-right">' + formatNumber(expensesTotal_previous) + '</th><th class="text-right">' + formatNumber(expensesTotal_current) + '</th></tr>';
	var EBIT_previous = grossProfitPrevious - expensesTotal_previous;
	var EBIT_current = grossProfitCurrent - expensesTotal_current;
	report += '<tr class="bg-warning"><td colspan=2 class="text-left">EBIT</td><th class="text-right">' + formatNumber(EBIT_previous) + '</th><th class="text-right">' + formatNumber(EBIT_current) + '</th></tr>';
	var Interest_previous = data.data.expenses.Interest.previousInterest * 1;
	var Interest_current = data.data.expenses.Interest.currentInterest * 1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Interest</td><td class="text-right">' + formatNumber(data.data.expenses.Interest.previousInterest) + '</td><td class="text-right">' + formatNumber(data.data.expenses.Interest.currentInterest) + '</td></tr>';
	var Tax_previous = EBIT_previous * 0.18;
	var Tax_current = EBIT_current * 0.18;
	report += '<tr><td>&nbsp;</td><td class="text-left">Tax (18%)</td><td class="text-right">' + formatNumber(Tax_previous.toFixed(1)) + '</td><td class="text-right">' + formatNumber(Tax_current.toFixed(1)) + '</td></tr>';
	var NetIncome_previous = (EBIT_previous * 1) - (Tax_previous * 1) - Interest_previous;
	var NetIncome_current = (EBIT_current * 1) - (Tax_current * 1) - Interest_current;
	report += '<tr class="bg-green"><td colspan=2 class="text-left">Net Income</td><td class="text-right">' + formatNumber(NetIncome_previous.toFixed(1)) + '</td><td class="text-right">' + formatNumber(NetIncome_current.toFixed(1)) + '</td></tr>';
	report += '</tbody>';
	
	report += '</table>';
	return report;
}

function LoadCashFlowReport(data){
	/* Add the report header to the report now */
	var report = "\
	<h4 class='text-left'>\
		" + data.header.name + "<br />\
		" + data.header.schoolAddress + "<br />\
		" + data.header.reportTitle + "<br />\
		" + data.header.reportTime + "<br />\
	</h4>\
	";
	/* Add the report content now. */
	report += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" >\
				<tbody>';
	/* Now check the data and try to load them in the correct format */
	var total_revenues = 0;
	var total_expenses = 0;
	report += '<tr><th colspan=5 class="text-left">Cash Flows from Operating Activities</th></tr>';
	//report += '<tr><td>&nbsp;</td><td colspan=2 class="text-left">Operating Incomes</td><td class="text-right"></td><td>&nbsp;</td></tr>';
	$.each(data.data.operating, function(i, item){
		report += '<tr><td>&nbsp;</td><td colspan=2 class="text-left">' + item.name + '</td><td class="text-right">' + formatNumber(item.amount) + '</td><td>&nbsp;</td></tr>';
	});
	//report += '<tr><td>&nbsp;</td><td class="text-left">Invoice Payment</td><td class="text-right">' + formatNumber( (total_revenues += (data.data.revenues.InvoicePayment*1)) ) + '</td><td>&nbsp;</td></tr>';
	report += '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="text-left" colspan=2>Net Cash Flow from Operating Activities</td><td class="text-right"></td></tr>';
	report += '<tr><td colspan=5 class="text-left">Cash Flows from Investing Activities</td></tr>';
	$.each(data.data.investing, function(i, item){
		report += '<tr><td>&nbsp;</td><td colspan=2 class="text-left">' + item.name + '</td><td class="text-right">' + formatNumber(item.amount) + '</td><td>&nbsp;</td></tr>';
	});
	report += '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="text-left" colspan=2>Net Cash Flow from Investing Activities</td><td class="text-right"></td></tr>';
	report += '<tr><td colspan=5 class="text-left">Cash Flows from Financing Activities</td></tr>';
	$.each(data.data.financing, function(i, item){
		report += '<tr><td>&nbsp;</td><td colspan=2 class="text-left">' + item.name + '</td><td class="text-right">' + formatNumber(item.amount) + '</td><td>&nbsp;</td></tr>';
	});
	report += '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="text-left" colspan=2>Net Cash Flow from Financing Activities</td><td class="text-right"></td></tr>';
	report += '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="text-left">Net change in Cash</td><td class="text-right" colspan=2></td></tr>';
	report += '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="text-left">Beginning Cash Balance</td><td class="text-right" colspan=2></td></tr>';
	report += '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="text-left">Ending Cash Balance</td><td class="text-right" colspan=2></td></tr>';
	report += '</tbody>\
				';
	
	report += '</table>';
	return report;
}

function LoadBalanceSheetReport(data){
	/* Add the report header to the report now */
	var report = "\
	<div class='row'>\
		<div class='col-md-8'>\
			<h4 class='text-left'>\
				<!--" + data.header.name + "<br />\
				" + data.header.schoolAddress + "<br />-->\
				" + data.header.reportTitle + "<br />\
				" + data.header.reportTime + "<br />\
			</h4>\
		</div>\
		<div class='col-md-4'>\
			<img src='/SchoolLogo/" + data.header.schoolLogo + "' style='width:50px;' alt='Logo Here' />\
		</div>\
	</div>\
	";
	/* Add the report content now. */
	report += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" >\
				<tbody>';
	/* Now check the data and try to load them in the correct format */
	var total_current_asset_previous= 0;
	var total_current_asset_current	= 0;
	report += '<tr class="bg-blue"><th colspan=2 class="text-left">Asset</th><th class="text-center">' + data.header.reportPreviousEnd + '</th><th class="text-center">' + data.header.reportCurrentEnd + '</th></tr>';
	report += '<tr class="bg-aqua"><td colspan=4 class="text-left">Current Asset</td></tr>';
	total_current_asset_previous 	+= data.data.asset.Cash.previousCash * 1;
	total_current_asset_current 	+= data.data.asset.Cash.currentCash * 1;
	if(data.data.asset.Cash.previousCash != 0 || data.data.asset.Cash.currentCash != 0){
		report += '<tr><td>&nbsp;</td><td class="text-left">Cash</td><td class="text-right">' + formatNumber(data.data.asset.Cash.previousCash) + '</td><td class="text-right">' + formatNumber(data.data.asset.Cash.currentCash) + '</td></tr>';
	}
	total_current_asset_previous 	+= data.data.asset.Bank.previousBank * 1;
	total_current_asset_current 	+= data.data.asset.Bank.currentBank * 1;
	if(data.data.asset.Bank.previousBank != 0 || data.data.asset.Bank.currentBank != 0){
		report += '<tr><td>&nbsp;</td><td class="text-left">Bank</td><td class="text-right">' + formatNumber(data.data.asset.Bank.previousBank) + '</td><td class="text-right">' + formatNumber(data.data.asset.Bank.currentBank) + '</td></tr>';
	}
	total_current_asset_previous 	+= data.data.asset.Receivables.previousReceivables * 1;
	total_current_asset_current 	+= data.data.asset.Receivables.currentReceivables * 1;
	if(data.data.asset.Receivables.previousReceivables != 0 || data.data.asset.Receivables.currentReceivables != 0){
		report += '<tr><td>&nbsp;</td><td class="text-left">Account Receivables</td><td class="text-right">' + formatNumber(data.data.asset.Receivables.previousReceivables) + '</td><td class="text-right">' + formatNumber(data.data.asset.Receivables.currentReceivables) + '</td></tr>';
	}
	total_current_asset_previous 	+= data.data.asset.Invetory.previousInvetory * 1;
	total_current_asset_current 	+= data.data.asset.Invetory.currentInvetory * 1;
	if(data.data.asset.Invetory.previousInvetory != 0 || data.data.asset.Invetory.currentInvetory != 0 ){
		report += '<tr><td>&nbsp;</td><td class="text-left">Invetory</td><td class="text-right">' + formatNumber(data.data.asset.Invetory.previousInvetory) + '</td><td class="text-right">' + formatNumber(data.data.asset.Invetory.currentInvetory) + '</td></tr>';
	}
	total_current_asset_previous 	+= data.data.asset.PrepaidRent.previousPrepaidRent * 1;
	total_current_asset_current 	+= data.data.asset.PrepaidRent.currentPrepaidRent * 1;
	if(data.data.asset.PrepaidRent.previousPrepaidRent != 0 || data.data.asset.PrepaidRent.currentPrepaidRent != 0){
		report += '<tr><td>&nbsp;</td><td class="text-left">Pre-paid Expenses</td><td class="text-right">' + formatNumber(data.data.asset.PrepaidRent.previousPrepaidRent) + '</td><td class="text-right">' + formatNumber(data.data.asset.PrepaidRent.currentPrepaidRent) + '</td></tr>';
	}
	total_current_asset_previous 	+= data.data.asset.BadDept.previousBadDept * 1;
	total_current_asset_current 	+= data.data.asset.BadDept.currentBadDept * 1;
	if(data.data.asset.BadDept.previousBadDept != 0 || data.data.asset.BadDept.currentBadDept != 0){
		report += '<tr><td>&nbsp;</td><td class="text-left">Bad Dept</td><td class="text-right">' + formatNumber(data.data.asset.BadDept.previousBadDept) + '</td><td class="text-right">' + formatNumber(data.data.asset.BadDept.currentBadDept) + '</td></tr>';
	}
	var total_asset_previous = 0;
	var total_asset_current = 0;
	total_asset_previous 	+= total_current_asset_previous*1;
	total_asset_current 	+= total_current_asset_current*1;

	if(total_current_asset_previous != 0 || total_current_asset_current != 0){
		report += '<tr class="bg-success"><td colspan=2 class="text-left">Total Current Assets</td><td class="text-right">' + formatNumber(total_current_asset_previous) + '</td><td class="text-right">' + formatNumber(total_current_asset_current) + '</td></tr>';
	}
	
	var total_fixed_asset_previous	= 0;
	var total_fixed_asset_current	= 0;
	report += '<tr class="bg-aqua"><td colspan=4 class="text-left">Fixed Assets</td></tr>';
	total_fixed_asset_previous 	+= data.data.asset.Supplies.previousSupplies*1;
	total_fixed_asset_current 	+= data.data.asset.Supplies.currentSupplies*1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Supplies</td><td class="text-right">' + formatNumber(data.data.asset.Supplies.previousSupplies) + '</td><td class="text-right">' + formatNumber(data.data.asset.Supplies.currentSupplies) + '</td></tr>';
	total_fixed_asset_previous 	+= data.data.asset.PropertyEquipement.previousPropertyEquipement*1;
	total_fixed_asset_current 	+= data.data.asset.PropertyEquipement.currentPropertyEquipement*1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Property, Plant and Equipment</td><td class="text-right">' + formatNumber(data.data.asset.PropertyEquipement.previousPropertyEquipement) + '</td><td class="text-right">' + formatNumber(data.data.asset.PropertyEquipement.currentPropertyEquipement) + '</td></tr>';
	total_fixed_asset_previous 	+= data.data.asset.LessAccumulatedDeprec.previousLessAccumulatedDeprec*1;
	total_fixed_asset_current 	+= data.data.asset.LessAccumulatedDeprec.currentLessAccumulatedDeprec*1;
	report += '<tr><td>&nbsp;</td><td class="text-left" style="padding-left: 20px;">Less depreciated accumulation</td><td class="text-right">' + formatNumber(data.data.asset.LessAccumulatedDeprec.previousLessAccumulatedDeprec) + '</td><td class="text-right">' + formatNumber(data.data.asset.LessAccumulatedDeprec.currentLessAccumulatedDeprec) + '</td></tr>';
	total_asset_previous 	+= total_fixed_asset_previous*1;
	total_asset_current 	+= total_fixed_asset_current*1;
	report += '<tr class="bg-success"><td colspan=2 class="text-left">Total Fixed Assets</td><td class="text-right">' + formatNumber(total_fixed_asset_previous) + '</td><td class="text-right">' + formatNumber(total_fixed_asset_current) + '</td></tr>';

	report += '<tr class="bg-green"><td colspan=2 class="text-left">Total Assets</td><td class="text-right">' + formatNumber(total_asset_previous) + '</td><td class="text-right">' + formatNumber(total_asset_current) + '</td></tr>';
	report += '<tr class="bg-blue"><th colspan=4 class="text-left">Liabilities and Owner\'s Equity</th></tr>';
	report += '<tr class="bg-aqua"><td colspan=4 class="text-left">Current Liabilities</td></tr>';
	var total_current_liabilities_previous = 0;
	var total_current_liabilities_current = 0;
	total_current_liabilities_previous 	+= data.data.liabilities.Payables.previousPayables * 1;
	total_current_liabilities_current 	+= data.data.liabilities.Payables.currentPayables * 1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Account Payables</td><td class="text-right">' + formatNumber(data.data.liabilities.Payables.previousPayables) + '</td><td class="text-right">' + formatNumber(data.data.liabilities.Payables.currentPayables) + '</td></tr>';
	total_current_liabilities_previous 	+= data.data.liabilities.ShortTermLoans.previousShortTermLoans * 1;
	total_current_liabilities_current 	+= data.data.liabilities.ShortTermLoans.currentShortTermLoans * 1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Short Term Loans</td><td class="text-right">' + formatNumber(data.data.liabilities.ShortTermLoans.previousShortTermLoans) + '</td><td class="text-right">' + formatNumber(data.data.liabilities.ShortTermLoans.currentShortTermLoans) + '</td></tr>';
	total_current_liabilities_previous 	+= data.data.liabilities.UnearnedRevenues.previousUnearnedRevenues * 1;
	total_current_liabilities_current 	+= data.data.liabilities.UnearnedRevenues.currentUnearnedRevenues * 1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Unearned Revenues</td><td class="text-right">' + formatNumber(data.data.liabilities.UnearnedRevenues.previousUnearnedRevenues) + '</td><td class="text-right">' + formatNumber(data.data.liabilities.UnearnedRevenues.currentUnearnedRevenues) + '</td></tr>';
	report += '<tr><td colspan=2 class="text-left">Total Current Liabilities</td><td class="text-right">' + formatNumber(total_current_liabilities_previous) + '</td><td class="text-right">' + formatNumber(total_current_liabilities_current) + '</td></tr>';
	report += '<tr class="bg-aqua"><td colspan=4 class="text-left">Long-term Liabilities</td></tr>';
	var total_longterm_liabities_previous = 0;
	var total_longterm_liabities_current  = 0;
	total_longterm_liabities_previous 	+= data.data.liabilities.LongTermLoans.previousLongTermLoans * 1;
	total_longterm_liabities_current  	+= data.data.liabilities.LongTermLoans.currentLongTermLoans * 1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Long Term Loans</td><td class="text-right">' + formatNumber(data.data.liabilities.LongTermLoans.previousLongTermLoans) + '</td><td class="text-right">' + formatNumber(data.data.liabilities.LongTermLoans.currentLongTermLoans) + '</td></tr>';
	total_longterm_liabities_previous 	+= data.data.liabilities.Others.previousOthers * 1;
	total_longterm_liabities_current  	+= data.data.liabilities.Others.currentOthers * 1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Others</td><td class="text-right">' + formatNumber(data.data.liabilities.Others.previousOthers) + '</td><td class="text-right">' + formatNumber(data.data.liabilities.Others.currentOthers) + '</td></tr>';
	report += '<tr><td colspan=2 class="text-left">Total Long-term Liabilities</td><td class="text-right">' + formatNumber(total_longterm_liabities_previous) + '</td><td class="text-right">' + formatNumber(total_longterm_liabities_current) + '</td></tr>';
	var liabilities_total_previous = (total_current_liabilities_previous * 1) + (total_longterm_liabities_previous * 1);
	var liabilities_total_current  = (total_current_liabilities_current * 1) + (total_longterm_liabities_current * 1);
	var liabilities_main_total_previous= liabilities_total_previous * 1;
	var liabilities_main_total_current = liabilities_total_current * 1;
	report += '<tr class="bg-success"><td colspan=2 class="text-left">Total Liabilities</td><td class="text-right">' + formatNumber(liabilities_total_previous) + '</td><td class="text-right">' + formatNumber(liabilities_total_current) + '</td></tr>';
	// report += '<tr><td colspan=3 class="text-left">Total Liabilities</td><th class="text-right">' + formatNumber(liabilities_total) + '</th></tr>';
	var total_owners_equity_previous= 0;
	var total_owners_equity_current	= 0;
	report += '<tr class="bg-aqua"><td colspan=4 class="text-left">Owner\'s Equity</td></tr>';
	total_owners_equity_previous	+= data.data.ownersequity.OwnersInvestiment.previousOwnersInvestiment * 1;
	total_owners_equity_current		+= data.data.ownersequity.OwnersInvestiment.currentOwnersInvestiment * 1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Owner\'s Investiment</td><td class="text-right">' + formatNumber(data.data.ownersequity.OwnersInvestiment.previousOwnersInvestiment) + '</td><td class="text-right">' + formatNumber(data.data.ownersequity.OwnersInvestiment.currentOwnersInvestiment) + '</td></tr>';
	total_owners_equity_previous	+= data.data.ownersequity.RetainedEarnings.previousRetainedEarnings * 1;
	total_owners_equity_current		+= data.data.ownersequity.RetainedEarnings.currentRetainedEarnings * 1;
	report += '<tr><td>&nbsp;</td><td class="text-left">Retained Earnings</td><td class="text-right">' + formatNumber(data.data.ownersequity.RetainedEarnings.previousRetainedEarnings) + '</td><td class="text-right">' + formatNumber(data.data.ownersequity.RetainedEarnings.currentRetainedEarnings) + '</td></tr>';
	liabilities_main_total_previous += total_owners_equity_previous * 1;
	liabilities_main_total_current += total_owners_equity_current * 1;
	report += '<tr class="bg-success"><td colspan=2 class="text-left">Total Owner\'s Equity</td><td class="text-right">' + formatNumber(total_owners_equity_previous) + '</td><td class="text-right">' + formatNumber(total_owners_equity_current) + '</td></tr>';
	// liabilities_total += equity_total;

	report += '<tr class="bg-green"><td colspan=2 class="text-left">Total Liabilities and Owner\'s Equity</td><th class="text-right">' + formatNumber(liabilities_main_total_previous) + '</th><th class="text-right">' + formatNumber(liabilities_main_total_current) + '</th></tr>';
	report += '</tbody>\
				';
	
	report += '</table>';
	//<a target="_blank" href="accounting/reports/1?startDate=' + $("#AccountingReportStartDate").val() + '&endDate=' + $("#AccountingReportEndDate").val() + '"><img src="packages/assets/img/excel.png" style="width:40px;" title="Export Balance Sheet to Excel" /></a>';
	
	return report;
}

function LoadAccountReceivablesReport(data){
	/* Add the report header to the report now */
	var report = "\
	<h4 class='text-left'>\
		" + data.header.name + "<br />\
		" + data.header.schoolAddress + "<br />\
		" + data.header.reportTitle + "<br />\
		" + data.header.reportTime + "<br />\
	</h4>\
	";
	/* Add the report content now. */
	report += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" >\
				<thead><tr ><th class="text-center">Date</th><th class="text-center">Invoice Number</th><th class="text-center">Customer</th><th class="text-center">Amount</th><th class="text-center">Due Date</th><th class="text-center">Balance</th>';
	for(k=1; k<=data.data.accounts.maxPayment; k++){
		report += '<th class="text-center">Payment ' + k + '</th>';
	}
	report += '</tr></thead>\
				<tbody>';
	/* Now check the data and try to load them in the correct format */
	var total_amount = 0;
	var total_paid = 0;
	$.each(data.data.accounts.data, function(i, item){
		total_amount += (item.amount*1);
		total_paid += (item.balance*1);
		report += '<tr><td>' + item.date + '</td><td>' + item.number + '</td><td>' + item.client + '</td><td>' + formatNumber(item.amount) + '</td><td>' + item.dueDate + '</td><td>' + formatNumber(item.balance) + '</td>';
		var payment_made = 0;
		$.each(item.payment, function(countamount, dt){
			report += '<td>' + formatNumber(dt.amount) + '</td>';
			payment_made++;
		});
		if(data.data.accounts.maxPayment > payment_made){
			report += "<td colspan='" + (data.data.accounts.maxPayment - payment_made) + "'>&nbsp;</td>";
		}
		report += '</tr>';
	});
	report += '<tr>\
			<td colspan=3>&nbsp;</td>\
			<td>' + formatNumber(total_amount) + '</td>\
			<td>&nbsp;</td>\
			<td>' + formatNumber(total_paid) + '</td>\
			<td colspan="' + data.data.accounts.maxPayment + '"></td>\
	</tr>';
	report += '</table>';
	return report;
}

function LoadTransactionJournalReport(data){
	/* Add the report header to the report now */
	var report = "\
	<h4 class='text-left'>\
		" + data.header.name + "<br />\
		" + data.header.schoolAddress + "<br />\
		" + data.header.reportTitle + "<br />\
		" + data.header.reportTime + "<br />\
	</h4>\
	";
	/* Add the report content now. */
	report += '<table class="table table-bordered abDataTable" id="pa_AccountingClientListTable" >\
				<thead><tr ><th class="text-center">Date</th><th class="text-center" colspan=2>Account</th><th class="text-center">Debit</th><th class="text-center">Credit</th></tr></thead>\
				<tbody>';
	/* Now check the data and try to load them in the correct format */
	var total_revenues = 0;
	var total_expenses = 0;
	//report += '<tr><td>&nbsp;</td><td colspan=2 class="text-left">Operating Incomes</td><td class="text-right"></td><td>&nbsp;</td></tr>';
	$.each(data.data.journal, function(i, item){
		var date_ = item.date;
		$.each(item.data, function(countrecords, records){
			report += '<tr><td>' + date_ + '</td><td colspan=2 class="text-left">' + records.account +'</td><td class="text-right">' + (records.type == 'debit'?records.amount:'') + '</td><td>' + (records.type == 'credit'?records.amount:'') + '</td></tr>';
			date_ = "";
		});
	});
	report += '</table>';
	return report;
}
