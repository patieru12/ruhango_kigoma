
/* Function to edit feetype */
function load_Accounting_School_Fees_Edit_Fee_Type(feetypeid){
	//try to change the style sheet and check
	$(".left_menu").removeClass('active');
	$("#FeeType").addClass('active');
	
	/* get the fee type information for the currently saved */
	var url2 = base_url+"accounting/schoolfees/feetype/" + feetypeid;
	$.getJSON( url2, function( existing ) {
		var url = base_url+"accounting/frequence";
		$.getJSON( url, function( data ) {
			/* load the form by passing received data the function */
			EditFeeTypeForm(data, existing);
		});
	});
	
}
