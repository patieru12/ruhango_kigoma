
var tropho_sent = false;
var ascaris_sent = false;
var kehist_sent = false;
var trichomnas_sent = false;
var levure_sent = false;
var gb_sent = false;
var ankylostome_sent = false;
var gr_sent = false;

var pst_sent = false;
var sent_pst_value = 0;

var acc_sent = false;
var perf_sent = false;
var insurance = "";
var enabled = false;
//variable to Emballage position in the System
var emballage_position = 0;
function mdWrite(counter, data, locat=0){
	if(counter >= data.md.length)
		return;
	//locat = counter;
	//alert(counter);
	/* if($("#medecinename" + locat).val() != ""){
		$("#another").click();
	} */
	setTimeout(function(){
		//locat = $("#med_counter").val();
		//console.log("Write Posotion: " + locat);
		//check if the current field to be written on has any other data
		if($("#medecinename" + locat).val() != ""){
			//check if the current content is equal to the one to be written
			//and if the current content was from automatic prescription
			if($("#medecinename" + locat).val() != data.md[counter]['name']){
				//increment for new write without changing the current prescription
				if(locat <= $("#med_counter").val()){
					mdWrite(counter, data, (locat + 1));
				} else{
					$("#another").click();
					mdWrite(counter, data, (locat + 1));
				}
			}
		} else {
			$("#medecinename" + locat).val(data.md[counter]['name']);
			$("#medecinequantity" + locat).val(data.md[counter]['Qty']);
			
			$("#another").click();
			mdWrite(++counter, data);
		}
		
	}, 800);
}
function AutoPrescription(fl,value,age,weight){
	$.ajax({
		type: "POST",
		url: "./auto-presc/" + fl + ".php",
		data: "data=" + value + "&age=" + age + "&weight=" + weight + "&url=ajax",
		cache: false,
		dataType: "json",
		success: function(result){
			//text = text + result;
			console.log(result);
			//try to write found exams
			if(result.la){
				var t=0;
				for(t=0; t<=la.length; t++){
					alert(la[t]['ExamName']);
				}
			}
			if(result.md){
				var t=0;
				var location = 1;
				//alert(result.md);
				mdWrite(t,result);
				/* var array = [];
				for(t=0; t < result.md.length; t++){
					//alert( result.md[t]['name'] + result.md[t]['Qty']);
					location = $("#med_counter").val();
					if($("#medecinename" + location).val() != ""){
						$("#another").click();
					}
					//setTimeout(function(){
						location = $("#med_counter").val();
						$("#medecinename" + location).val(result.md[t]['name']);
						$("#medecinequantity" + location).val(result.md[t]['Qty']);
					//}, 800);
					$("#another").click();
				} */
			}
		},
		error:function(er){
			console.log(er.responseText);
		}
	});
}
function perfusion(med_value){
	var pattern1 = /glucose isotonique 500 ml/; //code 9000
	var pattern2 = /ringer lactate 500 ml/; //9020
	var pattern3 = /chlorure de sodium normal saline 500 ml/; //9020
	console.log(med_value + $("#medecinename" + med_value).val().toLowerCase());
	if(perf_sent == false){
		console.log("Perfusion n'est pas envoyer deja");
		if(pattern1.test($("#medecinename" + med_value).val().toLowerCase()) || pattern2.test($("#medecinename" + med_value).val().toLowerCase()) || pattern3.test($("#medecinename" + med_value).val().toLowerCase()) ){
			console.log("Pattern1 Verified" + perf_sent);
			//sent the act request
			$(".act" + $("#ac_counter").val()).load("./adds-on/acts.php?number=" + $("#ac_counter").val() + "&code=9000");
				
			//add consummable related the simple wound dressing
			$(".consumable" + $("#cons_counter").val()).load("./adds-on/consumable.php?number=" + $("#cons_counter").val() + "&code=9000");
			
			perf_sent = true;
			
			//force the quantity to be 1
			$("#medecinequantity" + med_value).val("1");
		}
		
		//
	}
	
}
function pansement(value, weight=0, acc=false){
	var low_value_act = $("#ac_counter").val();
	var low_value_cons = $("#cons_counter").val();
	var low_value_med = $("#med_counter").val();
	
	console.log(low_value_act + ", " + low_value_cons);
	
	var pst_s_pattern = /pansement simple/; //code 440
	var pst_c_pattern = /pansement comp/; //code 450
	var str_s_pattern = /suture simple/; //code 500
	var str_c_pattern = /suture comp/; //code 5001
	if(acc == false && pst_sent == false){
		if(pst_s_pattern.test(value.toLowerCase())){
			pst_s_sent = true;
			//sent the http request now
			$(".act" + $("#ac_counter").val()).load("./adds-on/acts.php?number=" + $("#ac_counter").val() + "&code=400");
			
			//add consummable related the simple wound dressing
			$(".consumable" + $("#cons_counter").val()).load("./adds-on/consumable.php?number=" + $("#cons_counter").val() + "&code=440");
		}
		if(pst_c_pattern.test(value.toLowerCase())){
			pst_s_sent = true;
			//sent the http request now
			$(".act" + $("#ac_counter").val()).load("./adds-on/acts.php?number=" + $("#ac_counter").val() + "&code=401");
			
			//add consummable related the simple wound dressing
			$(".consumable" + $("#cons_counter").val()).load("./adds-on/consumable.php?number=" + $("#cons_counter").val() + "&code=440");
		}
		//alert(value);
		if(str_s_pattern.test(value.toLowerCase())){
			pst_s_sent = true;
			//sent the http request now
			$(".act" + $("#ac_counter").val()).load("./adds-on/acts.php?number=" + $("#ac_counter").val() + "&code=500");
			
			//add consummable related the simple wound dressing
			$(".consumable" + $("#cons_counter").val()).load("./adds-on/consumable.php?number=" + $("#cons_counter").val() + "&code=550");
			//add medicine
			$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&wght=" + weight +"&code=5000");
		}
		if(str_c_pattern.test(value.toLowerCase())){
			pst_s_sent = true;
			//sent the http request now
			$(".act" + $("#ac_counter").val()).load("./adds-on/acts.php?number=" + $("#ac_counter").val() + "&code=501");
			
			//add consummable related the simple wound dressing
			$(".consumable" + $("#cons_counter").val()).load("./adds-on/consumable.php?number=" + $("#cons_counter").val() + "&code=550");
			//add medicine
			$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&wght=" + weight +"&code=5000");
		}
		
		
		pst_sent = true;
		return;
	}
	
	var pst_s_pattern = /travail/; //code 6000
	var pst_c_pattern = /acc. eutoc. sans episio/; //code 7000
	var str_s_pattern = /acc. eutoc. avec episio/; //code 7500
	var str_c_pattern = /acc. dystocique/; //code 8000
	if(acc_sent == false){
		if(pst_s_pattern.test(value.toLowerCase())){
			
			//sent the http request now
			//$(".act" + $("#ac_counter").val()).load("./adds-on/acts.php?number=" + $("#ac_counter").val() + "&code=400");
			
			//add consummable related the simple wound dressing
			$(".consumable" + $("#cons_counter").val()).load("./adds-on/consumable.php?number=" + $("#cons_counter").val() + "&code=6000");
		}
		if(pst_c_pattern.test(value.toLowerCase())){
			/* accouchement eutocique sans episio */
			pst_s_sent = true;
			//sent the http request now
			$(".act" + $("#ac_counter").val()).load("./adds-on/acts.php?number=" + $("#ac_counter").val() + "&code=7000");
			
			//add consummable related the simple wound dressing
			$(".consumable" + $("#cons_counter").val()).load("./adds-on/consumable.php?number=" + $("#cons_counter").val() + "&code=7000");
			//add medicine
			$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&wght=" + weight +"&code=7000");
			
		}
		//alert(value);
		if(str_s_pattern.test(value.toLowerCase())){
			pst_s_sent = true;
			//sent the http request now
			$(".act" + $("#ac_counter").val()).load("./adds-on/acts.php?number=" + $("#ac_counter").val() + "&code=7500");
			
			//add consummable related the simple wound dressing
			$(".consumable" + $("#cons_counter").val()).load("./adds-on/consumable.php?number=" + $("#cons_counter").val() + "&code=7500");
			//add medicine
			$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&wght=" + weight +"&code=7500");
		}
		if(str_c_pattern.test(value.toLowerCase())){
			pst_s_sent = true;
			//sent the http request now
			$(".act" + $("#ac_counter").val()).load("./adds-on/acts.php?number=" + $("#ac_counter").val() + "&code=8000");
			
			//add consummable related the simple wound dressing
			$(".consumable" + $("#cons_counter").val()).load("./adds-on/consumable.php?number=" + $("#cons_counter").val() + "&code=7000");
			//add medicine
			$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&wght=" + weight +"&code=7000");
			
		}
		acc_sent = true;
	}
}

function queryMedecine(level, weight, value){
	
	console.log("Query Received: and weight=" +  weight);
	var tropho_pattern = /tropho/; //code=2
	var ascaris_pattern = /ascaris/; //code=300
	var kehist_pattern = /kehist/; //code=301
	var trichomnas_pattern = /trichomnas/; //code=302
	var levure_pattern = /levure/; //code=303
	var gb_pattern = /gb/; //code=304
	var ankylostome_pattern = /ankylostome/; //code=305
	var gr_pattern = /gr/; //code=306
	//check if the current result contain tropho
	if(tropho_pattern.test($("#examresult" + level).val().toLowerCase())){
		//now check if coartem is already submitted and send the query
		if(tropho_sent == false){
			tropho_sent = true;
			//alert($("#examresult" + level).val().toLowerCase());
			//sent the http request now
			$(".medecine" + $("#med_counter").val()).load("../rcp/adds-on/medecine.php?number=" + $("#med_counter").val() + "&code=2&wght=" + weight);
		}
	} else {
		if( ascaris_pattern.test( $("#examresult" + level).val().toLowerCase() ) ){
			//now check if coartem is already submitted and send the query
			//alert(ascaris_sent);
			if(ascaris_sent == false){
				ascaris_sent = true;
				//sent the http request now
				$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&code=300&wght=" + weight);
				//alert($("#medecine1").html());
			}
		}
		if( kehist_pattern.test( $("#examresult" + level).val().toLowerCase() ) ){
		//now check if coartem is already submitted and send the query
		//alert(ascaris_sent);
			if(kehist_sent == false){
				kehist_sent = true;
				//sent the http request now
				$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&code=301&wght=" + weight);
				//alert($("#medecine1").html());
			}
		}
		if( trichomnas_pattern.test( $("#examresult" + level).val().toLowerCase() ) ){
		//now check if coartem is already submitted and send the query
		//alert(ascaris_sent);
			if(trichomnas_sent == false){
				trichomnas_sent = true;
				//sent the http request now
				$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&code=302&wght=" + weight);
				//alert($("#medecine1").html());
			}
		}
		if( levure_pattern.test( $("#examresult" + level).val().toLowerCase() ) ){
		//now check if coartem is already submitted and send the query
		//alert(ascaris_sent);
			if(levure_sent == false){
				levure_sent = true;
				//sent the http request now
				$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&code=303&wght=" + weight);
				//alert($("#medecine1").html());
			}
		}
		if( gb_pattern.test( $("#examresult" + level).val().toLowerCase() ) ){
		//now check if coartem is already submitted and send the query
		//alert(ascaris_sent);
			if(gb_sent == false){
				gb_sent = true;
				//sent the http request now
				$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&code=304&wght=" + weight);
				//alert($("#medecine1").html());
			}
		}
		if( ankylostome_pattern.test( $("#examresult" + level).val().toLowerCase() ) ){
		//now check if coartem is already submitted and send the query
		//alert(ascaris_sent);
			if(ankylostome_sent == false){
				ankylostome_sent = true;
				//sent the http request now
				$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&code=305&wght=" + weight);
				//alert($("#medecine1").html());
			}
		}
		if( gr_pattern.test( $("#examresult" + level).val().toLowerCase() ) ){
		//now check if coartem is already submitted and send the query
		//alert(ascaris_sent);
			if(gr_sent == false){
				gr_sent = true;
				//sent the http request now
				$(".medecine" + $("#med_counter").val()).load("./adds-on/medecine.php?number=" + $("#med_counter").val() + "&code=306&wght=" + weight);
				//alert($("#medecine1").html());
			}
		}
	}
}
console.log(emballage_position);
function checkSachets(){
	var data_value = "";
	var last = $("#med_counter").val();
	while(last > 0){
		if(($("#medecinequantity" + last).val() - 0) > 0 )
			data_value += "&mdname" + last + "="+ $("#medecinename" + last).val() +"&mddate" + last + "="+ $("#medecinedate" + last).val();
		last--;
	}
	//alert(data);
	emballage = 0;
	$.ajax({
        type: "POST",
        url: "./check_amballage_2.php",
		data: "check=" + $("#med_counter").val() + data_value,
        dataType: "json",
        cache: false,
        success: function(data) {
			$("#consumablequantity0").val(data['Emballage']);
			console.log(data['Emballage']);
        },
		error:function(dataerror){
			console.log(dataerror.responseText);
		}
    });
	setTimeout(function(){
		return emballage;
	},500);
}
function Emballage(md_sent){
	$("#consumablequantity0").val(checkSachets());
	return;
	//console.log("NOW CHECKING EMBALAGE");
	//if the emballage position is zero track the last valid
	//emballage_position = emballage_position == 0?$("#cons_counter").val():emballage_position;
	//console.log(emballage_position);
	//verify if the found position contain Emballage
	var pattern_emb = /sachets/;
	emb_value = 0;
	console.log(emballage_position);
	/* if(pattern_emb.test($("#consumablename" + emballage_position).val().toLowerCase())){
		//get the current value now
		emb_value = $("#consumablequantity" + emballage_position).val();
		emb_value /= 1;
		
	} */
	console.log("consumablename" + emballage_position);
	//if the desired position contain other consumable don't delete it
	/* if($("#consumablename" + emballage_position).val().trim() != "" && !pattern_emb.test($("#consumablename" + emballage_position).val().toLowerCase())){
		//click the another conumable button and take new position now
		$("#another_consumable").click();
		setTimeout(function(e){
			//change the Emballage Position
			emballage_position = $("#cons_counter").val();
		}, 100);
	} */
	console.log("consumablename" + emballage_position);
	//send Amballage check request now
	emballage_value = 0;
	//loop all medicine to check amballage configuration
	var last = $("#cons_counter").val();
	var i =1;
	while(i<= last){
		$.post(
			"./check_amballage.php",
			{
				"date": $("#medecinedate" + md_sent).val(),
				"mdname" : $("#medecinename" + md_sent).val()
			},
			function(data){
				//change data format integer now
				console.log(data);
				data = data/1;
				emb_value += data;
				
				//log the value to verify if correct
				//console.log(emb_value);
				
				//now write the current
				/* setTimeout(function(e){
					$("#consumablename" + emballage_position).val("sachets");
					$("#consumablequantity" + emballage_position).val(emb_value);
				}, 200); */
			}
		);
		i++;
		console.log(i);
	}
	console.log(emb_value);
}
function isNumberKey(evt){
	var charCode = (evt.which) ? evt.which : evt.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		//document.getElementById('styles').innerHTML = charCode;
		if(charCode == 46)
		return true;
		return false;
	}
	//document.getElementById('styles').innerHTML = charCode;
	return true;
}
function isEnterKey(evt, id){
	var charCode = (evt.which) ? evt.which : evt.keyCode
	
	if (charCode == 13){
		$("#" + id).focus();
	}
	//document.getElementById('styles').innerHTML = charCode;
	//return false;
}

function runSimulation(){
	if(!enabled)
		return;
			//console.log("Executed!");
			$(".progress").html("<img src='../images/ajax_clock_small.gif' />");
			//start with the consultation record
			$(".consultation_simulator").load("./sim/cons.php?date=" + $("#consultation_date").val() + "&cons=" + $("#consultation_data").val());
			$(".hospitalization_simulator").load("./sim/hosp.php?date=" + $("#consultation_date").val() + "&days=" + $("#hospitalizationdays").val() + "&type=" + $("#hospitalizationtype").val() + "&insurance=" + $("#insurance_category_id").val() );
			$(".decision_simulator").html("Transfer: <span class=success>" + $("#decision_data").val() + "</span>" );
			//simulate the exam submission
			//loop from 1 to the latest exam record found 
			var i=1; var text = ""; var small_wait=0;
			//reset the malaria case form
			$("#lock_malaria").val("0");
			$("#lock_malaria_anti").val("0");
			while(i<= $("#exam_counter").val()){
				
				//send the AJAX query to get the response text for concatenation
				if($("#exam_date" + i).val().trim() == "" || $("#examname" + i).val().trim() == "" || $("#examid" + i).val().trim() == "" || $("#examresult" + i).val().trim() == ""){
					i++;
					continue;
				}
				//console.log("No Skipped!");
				$.ajax({
					type: "POST",
					url: "./sim/exam.php",
					data: "examdate=" + $("#exam_date" + i).val() + "&examname=" + $("#examname" + i).val() + "&examid=" + $("#examid" + i).val() + "&result=" + $("#examresult" + i).val() + "&existing_id=" + $("#examexistbefore" + i).val() + "&insurance=" + $("#insurance_category_id").val() + "&url=ajax",
					cache: false,
					success: function(result){
						text = text + result;
						//console.log(result);
					}
				});
				small_wait += 500;
				i++;
				//if all information are available now send the request
			}
			//wait a bit for exam query to finish();
			setTimeout(function(){
				console.log("Exam time End now!");
				$(".exam_simulator").html(text);
				//wait for exam to displayed
				setTimeout(function(){
					console.log("Start calculating Medicines!");
					//loop from 1 to the latest medicine record found 
					var i=1; var text = ""; var small_wait=0;
					while(i<= $("#med_counter").val()){
						
						//send the AJAX query to get the response text for concatenation
						if($("#medecinedate" + i).val().trim() == "" || $("#medecinename" + i).val().trim() == "" || $("#medecinequantity" + i).val().trim() == "" ){
							i++;
							continue;
						}
						//console.log("No Skipped!");
						$.ajax({
							type: "GET",
							url: "./sim/medicine.php",
							data: "medecinedate=" + $("#medecinedate" + i).val() + "&medecinename=" + $("#medecinename" + i).val() + "&medecinequantity=" + $("#medecinequantity" + i).val() + "&url=ajax",
							cache: false,
							success: function(result){
								text = text + result;
								//console.log(result);
							}
						});
						small_wait += 500;
						i++;
						//if all information are available now send the request
					}
					//wait a bit for medicine query to finish();
					setTimeout(function(){
						$(".medicine_simulator").html(text);
								
						//wait for exam to displayed
						setTimeout(function(){
							console.log("Start calculating Medicines!");
							//loop from 1 to the latest consumable record found 
							var i=0; var text = ""; var small_wait=0;
							while(i<= $("#cons_counter").val()){
								
								//send the AJAX query to get the response text for concatenation
								if($("#consumabledate" + i).val().trim() == "" || $("#consumablename" + i).val().trim() == "" || $("#consumablequantity" + i).val().trim() == "" ){
									i++;
									continue;
								}
								//console.log("No Skipped!");
								$.ajax({
									type: "GET",
									url: "./sim/consumable.php",
									data: "medecinedate=" + $("#consumabledate" + i).val() + "&medecinename=" + $("#consumablename" + i).val() + "&medecinequantity=" + $("#consumablequantity" + i).val() + "&url=ajax",
									cache: false,
									success: function(result){
										text = text + result;
										//console.log(result);
									}
								});
								small_wait += 500;
								i++;
								//if all information are available now send the request
							}
							//wait a bit for consumable query to finish();
							setTimeout(function(){
								$(".consumable_simulator").html(text);
									
								//wait for consumable to displayed
								setTimeout(function(){
									console.log("Start calculating Acts!");
									//loop from 1 to the latest consumable record found 
									var i=1; var text = ""; var small_wait=0;
									while(i<= $("#ac_counter").val()){
										
										//send the AJAX query to get the response text for concatenation
										if($("#actdate" + i).val().trim() == "" || $("#actname" + i).val().trim() == "" || $("#actquantity" + i).val().trim() == "" ){
											i++;
											continue;
										}
										//console.log("No Skipped!");
										$.ajax({
											type: "GET",
											url: "./sim/acts.php",
											data: "actdate=" + $("#actdate" + i).val() + "&actname=" + $("#actname" + i).val() + "&actquantity=" + $("#actquantity" + i).val() + "&insurance=" + $("#insurance_category_id").val() + "&url=ajax",
											cache: false,
											success: function(result){
												text = text + result;
												//console.log(result);
											}
										});
										small_wait += 500;
										i++;
										//if all information are available now send the request
									}
									//wait a bit for acts query to finish();
									setTimeout(function(){
										$(".act_simulator").html(text);
										
										$(".progress").html("");
										
										if($("#lock_malaria").val() != $("#lock_malaria_anti").val()){
											alert("Malaria Case Without Treatment\n OR \nTreatment Without Malaria Case.");
											return preventDefault();
										}
										
										$("#disable_submit").val("0");
										//send acknowledgement to the user
										$("#save_data").css("background-color","#6bb642");
										$("#save_data").css("color","#f3faef");
										setTimeout(function(){
											$("#save_data").click();
										}, 1000);
									}, small_wait);
								}, 100);
								//$(".progress").html("");
								
							}, small_wait);
						}, 100);
						
						//$(".progress").html("");
						
					}, small_wait);
				}, 100);
				
			}, small_wait);
			
			
		}
