/*
 * Template name: Kertas - Responsive Bootstrap 3 Admin Template
 * Version: 1.0.0
 * Author: VergoLabs
 */

/* DATATABLES */

function abDataTable()
{
	$('table.abDataTable').dataTable();
}

function abDataTableNoSection()
{
	$('table.abDataTable').dataTable({
	    "aaSorting": []
	  });
}

function abTablePagingTable(tableId)
{
	$('#'+tableId).dataTable({
	    "aaSorting": []
	});
}

function abTableNoPaging(tableId)
{
	$('#'+tableId).dataTable({
	    paging: false,
	    "aaSorting": []

	});
}

function abTableNoPaging(tableId)
{
	$('#'+tableId).dataTable({
	    paging: false,
	    "aaSorting": []

	});
}

function abTableCostumArg(tableId, sSearch, sRecord)
{
	$('#'+tableId).dataTable({
	    paging: false,
	    "aaSorting": [],

	});

	
}