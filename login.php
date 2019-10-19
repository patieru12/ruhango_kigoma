<?php
session_start();
//connect to the database now;
require_once"./lib/config.php";
$db = new DBConnector();
$url = ""; //echo sha1("123");
#var_dump($_POST);
//check if there is a password update
if(@$_POST['pwd_update'] && is_numeric($_POST['userid'])){
	//now update the password and continue to the normal login to change credentials immediately 
	//var_dump($_POST);
	require_once "./lib/db_function.php";
	//check all field
	if(empty($_POST['password'])){
		echo "<span class='error-text'>No Password Found</span>";
		return;
	}
	if(empty($_POST['oldpwd'])){
		echo "<span class='error-text'>No Password Found</span>";
		return;
	}
	if(empty($_POST['username'])){
		echo "<span class='error-text'>Invalid Username Found</span>";
		return;
	}
	if(sha1($_POST['password']) != sha1($_POST['password2'])){
		echo "<span class='error-text'>Password Mismatch</span>";
		return;
	}
	if(!$user_id = returnSingleField("SELECT UserID FROM sy_users WHERE Phone='{$_POST['username']}' && Password='".sha1($_POST['oldpwd'])."'","UserID",true,$con)){
		echo "<span class='error-text'>Invalid Old Password Found</span>";
		return;
	}
	
	if($_POST['userid'] != $user_id){
		echo "<span class='error-text'>Unmatched Condition Found</span>";
		return;
	}
	
	if(saveData("UPDATE sy_users SET Password='".sha1($_POST['password'])."' WHERE UserID='".PDB($_POST['userid'],true,$con)."' && UserID='{$user_id}' && Password='".sha1($_POST['oldpwd'])."' && Phone='".PDB($_POST['username'],true,$con)."'",$con)){
		$url = "profile.php?msg=success";
	}else{
		$url = "profile.php?msg=fail";
	}
	//die;
}
//check where data are from
if($_POST['url'] == "ajax"){
	//login process start by here now
	//check if all input are there
	if(trim($_POST['username'])){
		if(trim($_POST['password'])){
			//all data are there then login now
			if($data = $db->selectOneRowFromTable($tbl="sy_users",$condition=array("Phone"=>$db->PDB($_POST['username']),"Password"=>sha1($_POST['password']), "Status"=>1),$indexed=true)){
				#var_dump($data);
				$system = "";
				$cstsystem = "";
				

				$post = $db->select1cell("sy_post","PostCode",array("PostID"=>$data['PostID']),true);
				/* $currentHour = date("H", time());
				if(strtolower($post) =='rcp'){
					if(!$url){
						$currentHour = date("H", time());

						$weekend_days= array("Sat", "Sun");
						$thisDayPrefix = date("D", time());

						$closedDays = $db->selectAllInTable($tbl="sy_conge",$indexed=false,$condition=array("Date"=>array('sign'=>"=", 'value'=>date("Y-m-d"))) ,$order="");
						// var_dump($closedDays); die();
						if( count($closedDays) > 0) {
							$system = " closed-day-";
						} else if(in_array($thisDayPrefix, $weekend_days)){
							$system = "weekend-";
						}
					}
				} */

				
				//now make the next URL for the user
				$url = $url?$url:($system.$db->select1cell("sy_post","PostCode",array("PostID"=>$data['PostID']),true)."/");
				//register necessary sessions now
				
				//InsertIfNotExist(,,$auto_increment=false);
				$_SESSION['user'] = $data;
				if(strtolower($post) =='cst'){
					$_SESSION['user']['ServiceID'] = NULL;
					// die();
				}

				$_SESSION['mode'] = $cstsystem?$cstsystem:$system;
				//echo $url; die;
				//print success message and redirect the page
				echo "<span class=success>Login Success...</span><br />";
				echo "<script type='text/javaScript'>setTimeout('window.location=\"{$url}\"',400);</script>";
					
			} else{
				echo "<span class='error-text'>Invalid Input Found</span>";
			}
		} else{
			echo "<span class='error-text'>No Password Found</span>";
		}
	} else{
		echo "<span class='error-text'>No User name Found</span>";
	}
} else{
	echo "<span class='error-text'>No Data Found</span>";
}
?>
