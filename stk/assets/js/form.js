

function getSelect2()
{
	$("select.select2-input").select2();
		
}

function pictureUpload()
{
	$('.image-editor').cropit();
}

//year 
function populateYearOption(inputSelectYear)
{
    var sel = $("#"+inputSelectYear);
    sel.empty();
    sel.append('<option></option><option value="2015">2015</option><option value="2014">2014</option><option value="2013">2013</option><option value="2012">2012</option><option value="2011">2011</option><option value="2010">2010</option><option value="2009">2009</option><option value="2008">2008</option><option value="2007">2007</option><option value="2006">2006</option><option value="2005">2005</option><option value="2004">2004</option><option value="2003">2003</option><option value="2002">2002</option><option value="2001">2001</option><option value="2000">2000</option><option value="1999">1999</option><option value="1998">1998</option><option value="1997">1997</option><option value="1996">1996</option><option value="1995">1995</option><option value="1994">1994</option><option value="1993">1993</option><option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option><option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option><option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option><option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option><option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option><option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option><option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option><option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option><option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option><option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option><option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option><option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option><option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option><option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option><option value="1950">1950</option><option value="1949">1949</option><option value="1948">1948</option><option value="1947">1947</option><option value="1946">1946</option><option value="1945">1945</option><option value="1944">1944</option><option value="1943">1943</option><option value="1942">1942</option><option value="1941">1941</option><option value="1940">1940</option><option value="1939">1939</option><option value="1938">1938</option><option value="1937">1937</option><option value="1936">1936</option><option value="1935">1935</option><option value="1934">1934</option><option value="1933">1933</option><option value="1932">1932</option><option value="1931">1931</option><option value="1930">1930</option><option value="1929">1929</option><option value="1928">1928</option><option value="1927">1927</option><option value="1926">1926</option><option value="1925">1925</option><option value="1924">1924</option><option value="1923">1923</option><option value="1922">1922</option><option value="1921">1921</option><option value="1920">1920</option><option value="1919">1919</option><option value="1918">1918</option><option value="1917">1917</option><option value="1916">1916</option><option value="1915">1915</option><option value="1914">1914</option><option value="1913">1913</option><option value="1912">1912</option><option value="1911">1911</option><option value="1910">1910</option><option value="1909">1909</option><option value="1908">1908</option><option value="1907">1907</option><option value="1906">1906</option><option value="1905">1905</option>');

}

//month
function populateMonthOption(inputSelectMonth)
{
	var sel = $("#"+inputSelectMonth);
	sel.empty();
    sel.append('<option></option><option value="1">Jan</option><option value="2">Feb</option><option value="3">Mar</option><option value="4">Apr</option><option value="5">May</option><option value="6">Jun</option><option value="7">Jul</option><option value="8">Aug</option><option value="9">Sep</option><option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option>');

}

//day
function populateDayOption(inputSelectDay)
{
	var sel = $("#"+inputSelectDay);
	sel.empty();
    sel.append('<option></option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>');

}

function populateAlphabet()
{
	var sel = $("#selectAlphabet");
	sel.empty();
	sel.append('<option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option><option value="E">E</option><option value="F">F</option><option value="G">G</option><option value="H">H</option><option value="I">I</option><option value="J">J</option><option value="K">K</option><option value="L">L</option><option value="M">M</option><option value="N">N</option><option value="O">O</option><option value="P">P</option><option value="Q">Q</option><option value="R">R</option><option value="S">S</option><option value="T">T</option><option value="U">U</option><option value="V">V</option><option value="W">W</option><option value="X">X</option><option value="Y">Y</option><option value="Z">Z</option>');

}

//adrress
//hide
	function hideAddress(addressType){

		$("#"+addressType+"province").hide("fast");
		$("#"+addressType+"district").hide("fast");
		$("#"+addressType+"sector").hide("fast");
		$("#"+addressType+"cell").hide("fast");
		$("#"+addressType+"city").hide("fast");

	}	

	//show
		function nationalitySelected2()
		{
			$("#SelectNationality").select2().bind('change', populateCountry);
		}

		function birthAddressSelect2()
		{
			 $("#BP_SelectCountry").select2().bind('change', function() { populateProvince('BP_'); }).trigger('change');
			 $("#BP_SelectProvince").select2().bind('change', function() { populateDistrict('BP_'); }).trigger('change');
			 $("#BP_SelectDistrict").select2().bind('change', function() { populateSector('BP_'); }).trigger('change');
			 $("#BP_SelectSector").select2().bind('change', function() { populateCell('BP_'); }).trigger('change');
			 $("#BP_SelectCell").select2();
		}

		function ResidenceAddressSelect2()
		{
			$("#RA_SelectCountry").select2().bind('change', function() { populateProvince('RA_'); }).trigger('change');
			$("#RA_SelectProvince").select2().bind('change', function() { populateDistrict('RA_'); }).trigger('change');
			$("#RA_SelectDistrict").select2().bind('change', function() { populateSector('RA_'); }).trigger('change');
			$("#RA_SelectSector").select2().bind('change', function() { populateCell('RA_'); }).trigger('change');
			$("#RA_SelectCell").select2();
		}

		function NKResidenceAddressSelect2()
		{
			$("#NK_SelectCountry").select2().bind('change', function() { populateProvince('NK_'); }).trigger('change');
			$("#NK_SelectProvince").select2().bind('change', function() { populateDistrict('NK_'); }).trigger('change');
			$("#NK_SelectDistrict").select2().bind('change', function() { populateSector('NK_'); }).trigger('change');
			$("#NK_SelectSector").select2().bind('change', function() { populateCell('NK_'); }).trigger('change');
			$("#NK_SelectCell").select2();
		}

		function populateCountry()
		{
			var id = $("#SelectNationality").select2('val');

			$("#BP_province").hide("fast");
			$("#BP_district").hide("fast");
			$("#BP_sector").hide("fast");
			$("#BP_cell").hide("fast");
			$("#BP_city").hide("fast");

			$("#BP_SelectProvince").select2("val", "").trigger('change');
			$("#BP_SelectDistrict").select2("val", "").trigger('change');
			$("#BP_SelectSector").select2("val", "").trigger('change');
			$("#BP_SelectCell").select2("val", "").trigger('change');

	        $("#BP_SelectCountry").val(id).trigger('change');
            
		}		
				

		//province
		function populateProvince(addressType)
		{	

			var id = $("#"+addressType+"SelectCountry").select2('val');

			$("#"+addressType+"province").hide("fast");
			$("#"+addressType+"district").hide("fast");
			$("#"+addressType+"sector").hide("fast");
			$("#"+addressType+"cell").hide("fast");
			$("#"+addressType+"city").hide("fast");

			$("#"+addressType+"SelectProvince").select2("val", "").trigger('change');
			$("#"+addressType+"SelectDistrict").select2("val", "").trigger('change');
			$("#"+addressType+"SelectSector").select2("val", "").trigger('change');
			$("#"+addressType+"SelectCell").select2("val", "").trigger('change');

			if ( id == 1 || id == 2 ) { 

				console.log("Ajax one");

				var url = base_url+"api/Pd/Country/Province/"+id;
				$.ajax({
		                url: url,
		                type: 'GET',
		                dataType: "json",
		                success: function(data) {
		                	
		                	var sel = $("#"+addressType+"SelectProvince");

						    sel.empty();
							sel.append('<option ></option>');

						    for (var i=0; i<data.length; i++) {
						      sel.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
						    }
		                }

		        }).done(function( data ) {
		        	
				    $("#"+addressType+"province").show("slow");

			    });

				// 

		  //   	$.getJSON( url, function(data) {
				//   var sel = $("#"+addressType+"SelectProvince");

				//     sel.empty();
				// 	sel.append('<option ></option>');

				//     for (var i=0; i<data.length; i++) {
				//       sel.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
				//     }
				// })
				//   .done(function() {

				//   	$("#"+addressType+"province").show("slow");

				//   })
				//   .fail(function() {
				//   })
				//   .always(function() {
				//   });

				

			} else {

			   $("#"+addressType+"city").show("slow");
			}

		}

		//district
		function populateDistrict(addressType)
		{	

			var id = $("#"+addressType+"SelectProvince").select2('val');

			$("#"+addressType+"sector").hide("fast");
			$("#"+addressType+"cell").hide("fast");
			$("#"+addressType+"city").hide("fast");

			$("#"+addressType+"SelectDistrict").select2("val", "").trigger('change');
			$("#"+addressType+"SelectSector").select2("val", "").trigger('change');
			$("#"+addressType+"SelectCell").select2("val", "").trigger('change');

			if ( id > 0 ) { 

				console.log("Ajax one");

				var url = base_url+"api/Pd/Province/District/"+id;
				$.ajax({
		                url: url,
		                type: 'GET',
		                dataType: "json",
		                success: function(data) {
		                	
		                	var sel = $("#"+addressType+"SelectDistrict");

						    sel.empty();
						    sel.append('<option ></option>');

						    for (var i=0; i<data.length; i++) {
						      sel.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
						    }

		                }

		        }).done(function( data ) {
		        	
				  	$("#"+addressType+"district").show("slow");

			    });

				

				// $.getJSON( url, function(data) {
				//   var sel = $("#"+addressType+"SelectDistrict");

				//     sel.empty();
				//     sel.append('<option ></option>');

				//     for (var i=0; i<data.length; i++) {
				//       sel.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
				//     }
				// })
				//   .done(function() {

				//   	$("#"+addressType+"district").show("slow");

				//   })
				//   .fail(function() {
				//   })
				//   .always(function() {
				//   });

				
			}
		}

		//sector
		function populateSector( addressType )
		{
			var id = $("#"+addressType+"SelectDistrict").select2('val');

			$("#"+addressType+"cell").hide("fast");
			$("#"+addressType+"city").hide("fast");

			$("#"+addressType+"SelectSector").select2("val", "").trigger('change');
			$("#"+addressType+"SelectCell").val('').trigger('change');

			if ( id > 0 ) { 

				console.log("Ajax one");

				var url = base_url+"api/Pd/District/Sector/"+id;
				$.ajax({
		                url: url,
		                type: 'GET',
		                dataType: "json",
		                success: function(data) {
		                	
		                	var sel = $("#"+addressType+"SelectSector");

						    sel.empty();
						    sel.append('<option ></option>');

						    for (var i=0; i<data.length; i++) {
						      sel.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
						    }

		                }

		        }).done(function( data ) {
		        	
				  	$("#"+addressType+"sector").show("slow");

			    });

				// var url = base_url+"api/Pd/District/Sector/"+id;

				// $.getJSON( url, function(data) {
				//   var sel = $("#"+addressType+"SelectSector");

				//     sel.empty();
				//     sel.append('<option ></option>');

				//     for (var i=0; i<data.length; i++) {
				//       sel.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
				//     }
				// })
				//   .done(function() {

				//   	$("#"+addressType+"sector").show("slow");

				//   })
				//   .fail(function() {
				//   })
				//   .always(function() {
				//   });

				

			}
		}

		//cell
		function populateCell(addressType)
		{
			var id = $("#"+addressType+"SelectSector").select2('val');
			$("#"+addressType+"SelectCell").val('').trigger('change');

			if ( id > 0 ) { 

				console.log("Ajax one");

				var url = base_url+"api/Pd/Sector/Cell/"+id;
				$.ajax({
		                url: url,
		                type: 'GET',
		                dataType: "json",
		                success: function(data) {
		                	
		                	var sel = $("#"+addressType+"SelectCell");

						    sel.empty();
						    sel.append('<option ></option>');

						    for (var i=0; i<data.length; i++) {
						      sel.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
						    }

		                }

		        }).done(function( data ) {
		        	
				  	$("#"+addressType+"city").hide("fast");
					$("#"+addressType+"cell").show("slow");

			    });

				// var url = base_url+"api/Pd/Sector/Cell/"+id;

				// $.getJSON( url, function(data) {
				//   var sel = $("#"+addressType+"SelectCell");

				//     sel.empty();
				//     sel.append('<option ></option>');

				//     for (var i=0; i<data.length; i++) {
				//       sel.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
				//     }
				// })
				//   .done(function() {

				// 	  	$("#"+addressType+"city").hide("fast");
				// 		$("#"+addressType+"cell").show("slow");

				//   })
				//   .fail(function() {
				//   })
				//   .always(function() {
				//   });

			}
			
		}


//alive form

function showAlive(parentType)
{	
	switch(parentType) {
        case 1:
            $("#fa_alivePeronInfo").slideDown();
        break;

        case 2:
            $("#mo_alivePeronInfo").slideDown();
        break;
    }
}

function hideAlive(parentType)
{	
	var selectStudentNewPrimarycontact = $("#selectStudentNewPrimarycontact");
	selectStudentNewPrimarycontact.select2('val', '').trigger('change');

	switch(parentType) {
        case 1:
            selectStudentNewPrimarycontact.find('option[value=1]').attr('disabled', true);
            $("#fa_alivePeronInfo").slideUp();
        break;

        case 2:
            selectStudentNewPrimarycontact.find('option[value=2]').attr('disabled', true);
            $("#mo_alivePeronInfo").slideUp();
        break;
    }

}


//validation 
	var degreeNumber = 0;

	var optionsLevel        = [];
	var optionsInstitution  = [];
	var optionsField 		= [];

	function pullDegreeLevel(){

		var url  = base_url+'api/Pd/DegreeLevel/';

		$.getJSON( url, function(data) {

		    $.each(data, function(i, item) {
	 			var row = [];

	   			row['id'] 	= item.id;
		    	row['text'] = item.name;

		    	optionsLevel.splice(i,0,row);
		    });

	    })
	    .done(function() {

	     select2GenerateNewOption('level_0',optionsLevel);

	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });

	}

	function pullInstitution(){

		var url  = base_url+'api/Pd/DegreeInstitution/';

		$.getJSON( url, function(data) {

		    $.each(data, function(i, item) {
	 			var row = [];

	   			row['id'] 	= item.id;
		    	row['text'] = item.name;

		    	optionsInstitution.splice(i,0,row);
		    });

	    })
	    .done(function() {

	     select2GenerateNewOption('institution_0',optionsInstitution);

	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });

	}

	function pullField(){

		var url  = base_url+'api/Pd/DegreeField/';

		$.getJSON( url, function(data) {

		    $.each(data, function(i, item) {
	 			var row = [];

	   			row['id'] 	= item.id;
		    	row['text'] = item.name;

		    	optionsField.splice(i,0,row);
		    });

	    })
	    .done(function() {
	    	
	     select2GenerateNewOption('field_0',optionsField);

	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });

	}

	function firstDegree()
	{

		$.when(pullDegreeLevel(), pullInstitution(), pullField()).done(function(a1, a2, a3){

		});

	}

	function initDegree()
	{
		firstDegree();
	}


	function appendNewDegree()
	{
		degreeNumber++;

		var DegreeContainer  = '<div class="col-sm-12" class="row" id="row_'+degreeNumber+'"  >';
				DegreeContainer += '<div class="col-sm-3"><input name="level_'+degreeNumber+'" type="hidden" id="level_'+degreeNumber+'" class="form-control" /></div>';
				DegreeContainer += '<div class="col-sm-4"><input name="institution_'+degreeNumber+'" type="hidden" id="institution_'+degreeNumber+'" class="form-control" /></div>';
				DegreeContainer += '<div class="col-sm-3"><input name="field_'+degreeNumber+'" type="hidden" id="field_'+degreeNumber+'" class="form-control" /></div>';
				DegreeContainer += '<div class="col-sm-2">';
					DegreeContainer += '<div class="col-sm-6"><label><input type="radio" name="status_'+degreeNumber+'" value="1" checked> Yes</label></div>';
					DegreeContainer += '<div class="col-sm-6"><label><input type="radio" name="status_'+degreeNumber+'" value="0" > No</label></div>';
				DegreeContainer += '</div>';
			DegreeContainer += '</div>';

		$(DegreeContainer).appendTo('#staffDegrees').hide().fadeIn(1000);

		select2GenerateNewOption('level_'+degreeNumber,optionsLevel);
		select2GenerateNewOption('institution_'+degreeNumber,optionsInstitution);
		select2GenerateNewOption('field_'+degreeNumber,optionsField);

		$("#numberOfDegree").val(degreeNumber);

	}
	
	function classroomformatSelection(state)
	{
		var originalOption = $(state.element);

	    if (state.id > 0)
	    {
	        return originalOption.data('yearname') + " : " + state.text;
	    }
	    else{

	        return state.text;
	    }

	}

	function classroomformatResult(state) 
	{
	    return state.text;
	}

	function subjectformatSelection(state)
	{

	    if (state.id > 0)
	    {
	        var originalOption = $(state.element);
	        return originalOption.data('yearname') + " :  " + state.text;
	    }
	    else{

	        return state.text;
	    }

	}

	function subjectformatResult(state) 
	{
	    return state.text;
	}

	function classroomSubjectformatSelection(state)
	{
        var originalOption = $(state.element);
        return originalOption.data('classname') + " :  " + state.text;
	  
	}

	function classroomSubjectformatResult(state) 
	{
	    return state.text;
	}