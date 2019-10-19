$(document).ready(function(){
	/************* HIDE THE CALENDAR AUTOMATICALLY  ********/
	var c_clicked = false;
	
	$(".date_on").focus(function(){
		c_clicked = false;
		console.log("Date Started!");
	});
	$(".date_on").blur(function(){
		console.log("Date Field Loast Focus Test Calendar Status");
		setTimeout(function(){
			if(c_clicked == false){
				console.log("Now Close the Calendar NOW");
				ds_hi();
			}
		},300);
	});
	$(".ds_box").click(function(){
		c_clicked = true;
		console.log("Calendar Clicked! Dont Close");
	});
	/************* HIDE THE CALENDAR AUTOMATICALLY  ********/
});