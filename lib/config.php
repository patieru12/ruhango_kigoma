<?php
class DBConnector{
	public $dbname;
	public $payroll_user;
	function __construct($db='care_full_v1',$server="127.0.0.1",$user="root",$password=''){
		$password = '';
		$this->dbname = $db;
		$this->payroll_user = array("hr"=>"Human Resource","nrm"=>"Call Center","fnc"=>"Finance");
		$con = mysql_connect($server,$user,$password)or die("COULD NOT CONNECT!");
		return mysql_select_db($db,$con);//or ("DATABASE NOT FOUND!");
	}
	public function select1cell($tbl,$field,$condition=null,$return_data=true){
		$sql = "SELECT `".$field."` FROM `".$tbl."`";
		if($condition !=null && count($condition) >0){
			$sql .= " WHERE";
			foreach($condition as $key=>$value){
				if(is_array($value)){
					$sql .= " && `".$key."`".$value['sign']."'".$value['value']."'";
				} else{
					if(preg_match("/LIKE/",$value))
						$sql .= " && `".$key."` ".$value;
					else
						$sql .= " && `".$key."` = '".$value."'";
				}
			}
		}
		$sql = preg_replace("/WHERE &&/","WHERE ",$sql);
		#echo $sql."<br>";
		$result = mysql_query($sql)or die(mysql_error());			
		if($result){
			$res = mysql_fetch_array($result,MYSQL_ASSOC);
			if($return_data === true)return $res[$field];
			else return $res;
		}
		return null;
	}
	public function selectFields($tbl,$field,$condition=null,$limit=null,$order="",$indexed=true,$sign='=',$multiplereference=false,$distinct=false,$comp="&&"){
		if(count($field) < 1) return null;
 		$sql = "SELECT ";
		if($distinct) $sql .= "DISTINCT "; $count=0;
		foreach($field as $value){
			if($count != 0) $sql .= ", ";
			if($value == '*') $sql .= " * ";
			else $sql .= " `".$value."`";
			$count++;
		}
		$sql .= " FROM `".$tbl."`";
		if($condition != null){
			$sql .= " WHERE";# var_dump($condition);
			$counter = 0;
			foreach($condition as $key=>$value){
				#var_dump($value);
				#var_dump($multiplereference);
				#echo $counter;
				if($multiplereference && is_array($value)){
					#echo "OK";
					foreach($value as $value2){
						if($counter != 0) $sql .= $comp;
						if(!preg_match("/^LIKE/",$value2) && !preg_match("/^NOT LIKE/",$value2)) $sql .= " `".$key."` ".$sign." \"".$value2."\"";
						else $sql .= " `".$key."` ".$value2;
						$counter++;
					}
				} else{
					#echo "OK"; var_dump($value);
					if($counter != 0) $sql .= $comp;
					if(is_array($value)){
						$sql .= " `".$key."`".$value['sign']."\"".$value['value']."\"";
					} else{
						if(!preg_match("/^LIKE/",$value) && !preg_match("/^NOT LIKE/",$value)) $sql .= " `".$key."` ".$sign." \"".$value."\"";
						else $sql .= " `".$key."` ".$value;
					}
					#echo $counter;
				}
				$counter++;
			}
		}
		$sql = preg_replace(array("/WHERE &&/","/WHERE&&/","/SELECT ,/"),array("WHERE ","WHERE ","SELECT "),$sql);
		if($order !="") $sql .= " ".$order." ";
		if($limit != null && count($limit) == 2) $sql .= " LIMIT ".$limit[0].", ".$limit[1];
		#echo $sql."<br>";# return;
		$result = mysql_query($sql)or die(mysql_error());			
		if($result){
			$res; $rtn=array(); $count=0;
			if($indexed == true){
				while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
					$rtn[$count] = $row;
					$count++;
				}
			}
			if($indexed == false){
				while($row = mysql_fetch_array($result,MYSQL_NUM)){
					$rtn[$count] = $row;
					$count++;
				}
			}
			return $rtn;
		}
		return null;
	}
	public function selectInMoreTable($lbl,$multirows=false,$indexed=false, $order="", $dist=false){
		$sql = "SELECT ".($dist?"DISTINCT ":""); $tbl = $lbl['tbl']; $condition = $lbl['condition']; $field = $lbl['fld'];
		#try to extract all fields from first table
		for($i=0;$i<count($tbl);$i++){
			$fld = $field[$tbl[$i]];
			foreach($fld as $value){
				if($value == "*") $sql .= "`".$tbl[$i]."`.".$value.", "; 
				else $sql .= "`".$tbl[$i]."`.`".$value."`, "; 
			}
		}
		$sql .= "FROM ";
		foreach($tbl as $value) $sql .= "`".$value."`, ";
		$sql .= "WHERE ";
		#echo "<pre>";#var_dump($condition); #return;
		foreach($condition as $key=>$value){
			if(is_array($value)){
				#$sql .= "&& `".$key."`".$value['sign']."'".$value['value']."'";
				#var_dump($value[0]);
				if(isset($value[0])){
					$kkk=0;
					#var_dump($value);
					foreach($value as $vvv){
						if(!preg_match('/`/',$vvv['value'])) 
							$sql .= "&& `".$key."`".$vvv['sign']."'".$vvv['value']."' ";
						else 
							$sql .= "&& `".$key."`".$vvv['sign']."`".$vvv['value']."` ";
						
					}
				} else{
					if(!preg_match('/`/',$value['value'])) 
						$sql .= "&& `".$key."`".$value['sign']."'".$value['value']."' ";
					else 
						$sql .= "&& `".$key."`".$value['sign']."`".$value['value']."` ";
				}
			} else{
				if(!preg_match('/`/',$value)) $sql .= "&& `".$key."`='".$value."' ";
				else $sql .= "&& `".$key."`=`".$value."` ";
			}
		}
		$look_for = array("/, FROM/","/, WHERE/","/WHERE &&/");
		$replace_with = array(" FROM"," WHERE","WHERE ");
		$sql .= $order;
		$sql = preg_replace($look_for,$replace_with,$sql);
		#echo $sql."<br>";
		$result = mysql_query($sql)or die("Invalid data provided ".mysql_error());
		#start out put
		if($multirows == false && $indexed == false) return mysql_fetch_array($result,MYSQL_NUM);
		if($multirows == false && $indexed == true) return mysql_fetch_array($result,MYSQL_ASSOC);
		if($multirows == true){
			$dt = array(); $count=0;
			if($indexed == false){
				while($row = mysql_fetch_array($result,MYSQL_NUM)){
					$dt[$count] = $row;
					$count++;
				}
			}
			if($indexed == true){
				while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
					$dt[$count] = $row;
					$count++;
				}
			}
			return $dt;
		}
	}
	public function InsertIfNotExist($tbl,$data,$condition,$auto_increment=true){
		/*check if to inset or not*/
		$insert = true;
		if($condition != NULL && count($condition)>0){
			$check = "SELECT * FROM `".$tbl."` WHERE ";
			foreach($condition as $key=>$value){
				if(preg_match("/LIKE/",$value))
					$check .= "&& `".$key."` ".$value." ";
				else {
					if($value == 'NOW()')
						$check .= "&& `".$key."`=".$value." ";
					else 
						$check .= "&& `".$key."`='".$value."' ";
				}
			}
			$check = preg_replace("/WHERE &&/","WHERE",$check);
			//echo $check;
			$res = mysql_query($check)or die(mysql_error()." ".$tbl);
			#var_dump(mysql_num_rows($res)); echo "<br><br>";
			if(mysql_num_rows($res)>0) $insert = false;
		}
		if($insert == true){
			$sql = "INSERT INTO `".$tbl."` SET ";
			if($auto_increment) $sql .= " ID=NULL";
			foreach($data as $key=>$value){
				if($value == "NOW()") $sql .= ", `".$key."`=".$value."";
				else $sql .= ", `".$key."`='".$value."'";
			}
			$sql = preg_replace('/SET ,/','SET',$sql);
			#echo $sql;
			if(mysql_query($sql)or die(mysql_error()." ".$tbl)){
				return mysql_insert_id();
			} else 
				return false;
		}
	}
	public function InsertOrUpdate($tbl,$data,$id_increment=true,$condition=null,$referencefield="ErrorCount",$replace=false){
		$check = 1; $sql="";
		if($condition != null){
			$sql = "SELECT * FROM `".$tbl."` WHERE ";
			foreach($condition as $a=>$b){
				$sql .= "&& `".$a."`='".$b."'";
			}
			$sql = preg_replace("/WHERE &&/","WHERE",$sql);
			#echo $sql;
			$result = mysql_query($sql)or die(mysql_error());
			if($result &&  mysql_num_rows($result) > 0 ) $check = 2;
		}
		#echo $check;
		if($check == 1){
			$sql = "INSERT INTO `".$tbl."` SET ";
			if($id_increment == true) $sql .="`ID`=NULL";
			foreach($data as $key=>$value){
				if($value == "NOW()") $sql .= ", `".$key."`=".$value."";
				else $sql .= ", `".$key."`='".$value."'";
			}
			$sql = preg_replace('/SET ,/','SET ',$sql);
		}
		elseif($check == 2){
			$sql = "UPDATE `".$tbl."` SET ";
			foreach($data as $key=>$value) {
				if($key == $referencefield && !$replace){
					if(is_numeric($value))$sql .= ", `".$key."`=".$key."+".$value."";
					else{
						$ext = DBConnector::select1cell($tbl,$key,$condition,true,false);
						#var_dump($ext);
						$sql .= ", `".$key."`=\"".trim($ext." ".$value)."\"";
					}
				}
				else{
					if($value == "NOW()") $sql .= ", `".$key."`=".$value."";
					else $sql .= ", `".$key."`='".$value."'";
				}
			}
			$sql .= " WHERE ";
			foreach($condition as $key=>$value) $sql .= "&& `".$key."`='".$value."'";
			$look_for = array("/SET ,/","/WHERE &&/");
			$replace_with = array("SET ","WHERE");
			$sql = preg_replace($look_for,$replace_with,$sql);
		}
		#echo $sql;#return;
		if(mysql_query($sql)or die(mysql_error())) return true;
		else return false;
	}
	public function createTable($tbl,$fields){
		$sql = "CREATE TABLE IF NOT EXISTS `".$tbl."` (";
		for($i=0; $i<count($fields)-1; $i++){
			$field = $fields[$i];
			$sql .= "`".$field['NAME']."` ".$field['TYPE'];
			if(preg_match("/TEXT/",$field['TYPE'])) $sql .= " CHARACTER SET ".$field['CHARACTER_SET']." COLLATE ".$field['COLLATE'];
			if($field['LENGTH'] != null) $sql .= "(".$field['LENGTH'].")";
			if($field['NOT_NULL'] == true) $sql .= " NOT NULL ";
			else $sql .= " NULL ";
			if($field['AUTO_INCREMENT'] == true) $sql .= "AUTO_INCREMENT ";
			if($field['PRIMARY_KEY'] == true) $sql .= "PRIMARY KEY ";
			if($i < count($fields)-2) $sql .= ", ";
		}
		if($fields['UNIQUE'] != null){
			$sql .= ", ";
			$unique = $fields['UNIQUE'];
			if(count($unique)>0) $sql .= "UNIQUE(";
			foreach($unique as $key=>$value){
				if($key>0)$sql .= ", `".$value."` ";
				if($key == 0)$sql .= "`".$value."`";
			}
			if(count($unique)>0) $sql .= ")";
		}
		$sql .= ")";
		#echo $sql; return;
		mysql_query($sql)or die(mysql_error());
	}
	public function selectOneRowFromTable($tbl,$condition,$indexed=false){
		$sql = "SELECT*FROM `".$tbl."` WHERE ";
		foreach($condition as $key=>$value){
			if(preg_match("/LIKE/",$value))
					
				$sql .= "&& `".$key."` ".$value;
			else	
				$sql .= "&& `".$key."`='".$value."'";
		}
		$sql = preg_replace("/WHERE &&/","WHERE ",$sql);
		//echo $sql;
		$res = mysql_query($sql)or die(mysql_error());
		if($res && mysql_num_rows($res) == 1){
			if($indexed === false) return mysql_fetch_array($res,MYSQL_NUM);
			if($indexed === true) return mysql_fetch_array($res,MYSQL_ASSOC);
		}
		return null;
	}
	public function DropColomn($table,$colomn){
		if(!$table || !$colomn) return false;
		$sql = "ALTER TABLE `".$table."`";
		if(is_array($colomn)){
			for($i=0;$i<count($colomn);$i++){
				if($i>0) $sql .= ", ";
				$sql .= " DROP `".$colomn[$i]."`";
			}
		} else $sql .= " DROP `".$colomn."`";
		mysql_query( $sql);
	}
	public function AddColomn($table,$data){
		if(!$table || !$data) return false;
		$sql = "ALTER TABLE `".$table."`";
		for($i=0;$i<count($data);$i++){
			if($i>0) $sql .= ",";
			$sql .= " ADD ";
			$sql .= "`".$data[$i]['NAME']."` ".$data[$i]['TYPE'];
			if(preg_match("/TEXT/",$data[$i]['TYPE'])) $sql .= " CHARACTER SET ".$data[$i]['CHARACTER_SET']." COLLATE ".$data[$i]['COLLATE'];
			if($data[$i]['LENGTH'] != null) $sql .= "(".$data[$i]['LENGTH'].")";
			if($data[$i]['NOT_NULL'] == true) $sql .= " NOT NULL ";
			if(@$data[$i]['DEFAULT'] == true) $sql .= " DEFAULT '".$data[$i]['DEFAULT']."'";
			if(@$data[$i]['AUTO_INCREMENT'] == true) $sql .= "AUTO_INCREMENT ";
			if(@$data[$i]['PRIMARY_KEY'] == true) $sql .= "PRIMARY KEY ";
		}
		#echo $sql;
		#var_dump($data);
		if(mysql_query($sql)) return true;
		else return false;
	}
	
	public function InsertData($tbl,$data,$id_increment=true){
		$sql = "INSERT INTO `".$tbl."` SET ";
		if($id_increment == true) $sql .="`ID`=NULL";
		foreach($data as $key=>$value){
			if($value == 'NOW()') $sql .= ", `".$key."`=".$value ; 
			else $sql .= ", `".$key."`=\"".$value."\"";
		}
		$sql = preg_replace('/SET ,/','SET ',$sql);
		#echo $sql;
		if(mysql_query($sql)/* or die(mysql_error()) */) 
			return mysql_insert_id();
		return false;
	}
	public function delete1row($tbl=null,$condition=null){
		if($tbl == null) return null;
		$sql = "DELETE FROM `".$tbl."`";
		if(count($condition) > 0){
			$sql .= " WHERE ";
			foreach($condition as $field=>$value) $sql .= "&& `".$field."`='".$value."'";
			$sql = preg_replace("/WHERE &&/","WHERE",$sql);
		}
		#echo $sql;
		if(mysql_query($sql)or die(mysql_error()))
			return true;
		return false;
	}
	public function updateCells($data=null,$tbl="",$condition=null){
		if($tbl == "" || $data == null || count($data) <1) return null;
		$sql = "UPDATE `".$tbl."` SET";
		foreach($data as $fld=>$value){
			#var_dump($value);
			if($value == null) $sql .= " ,`".$fld."`= NULL ";
			else{
				if($value === 'NOW()')
					$sql .= " ,`".$fld."`=".$value." ";
				else
					$sql .= " ,`".$fld."`='".$value."' ";
			}
		}
		if($condition != null){
			$sql .= "WHERE "; $i=0;
			foreach($condition as $field=>$value){
				if($i >0 ) $sql .= "&& ";
				$sql .= "`".$field."`='".$value."' ";
				$i++;
			}
		}
		$sql = preg_replace(array('/SET ,/','/WHERE ,/'),array('SET ','WHERE '),$sql);
		#echo $sql;
		//return;
		if(mysql_query($sql)or die(mysql_error())) return true;
		else return false;
	}
	public function emptyTables($tbl){
		mysql_query("TRUNCATE TABLE `".$tbl."`");
	}
	public function selectAllInTable($tbl,$indexed=false,$condition=null ,$order=""){
		if($tbl == "" || $tbl == null) return null;
		$sql = "SELECT * FROM `".$tbl."` ";
		if($condition != null && count($condition)>0) {
			$sql .= "WHERE ";
			$i=0;
			foreach($condition as $key=>$value){
				$sql .= ($i++)>0?"&&":"";
				if(isset($value[0]) && is_array($value[0])){
					$k = 0;
					foreach($value as $value_condition){
						$sql .= ($k++)>0?"&&":"";
						$sql .= "`{$key}`{$value_condition['sign']}'{$value_condition['value']}'";
					}
				}
				else if(is_array($value))
					$sql .= "`{$key}`{$value['sign']}'{$value['value']}'";
				else
					$sql .= "`".$key."`='".$value."' ";
			}#$sql .= "&& `".$key."`='".$value."' ";
		}
		if($order != "") $sql .= $order;
		$sql = preg_replace('/WHERE &&/','WHERE',$sql);
		#echo $sql;
		$rs = mysql_query($sql)or die(mysql_error());
		$res = array();
		if($rs && mysql_num_rows($rs) >0){
			$i=0;
			if($indexed == false){
				while($row = mysql_fetch_array($rs,MYSQL_NUM)){
					$res[$i] = $row;
					$i++;
				}
			}
			if($indexed == true){
				while($row = mysql_fetch_array($rs,MYSQL_ASSOC)){
					$res[$i] = $row;
					$i++;
				}
			}
			return $res;
		}
		return null;
	}
	public function selectMax($tbl,$fld,$rtn=false, $condition=null,$multiplereference=false){
		$sql = "SELECT MAX(`".$fld."`) FROM `".$tbl."` ";
		if($condition != null){
			$sql .= "WHERE";
			if($multiplereference){
				foreach($condition as $key=>$value ){
					foreach($value as $value3){
						if(preg_match('/^LIKE/',$value3) || preg_match('/^NOT LIKE/',$value3)) $sql .= " && `".$key."` ".$value3."";
						else $sql .= " && `".$key."`='".$value3."'";
					}
				}
			} else{
				foreach($condition as $key=>$value ){
					if(preg_match('/^LIKE/',$value)) $sql .= " && `".$key."` ".$value."";
					else $sql .= " && `".$key."`='".$value."'";
				}
			}
		}
		$sql = preg_replace("/WHERE &&/","WHERE",trim($sql));
		#echo $sql;# return;
		$result = mysql_query($sql);
		$res = mysql_fetch_array($result,MYSQL_NUM);
		if($rtn == true) return $res;
		if($rtn == false) return $res[0];
		return null;
	}
	public function checkTable($tbl,&$con){
		$check = $con->RunSqlQuery("SHOW TABLES;",true);
		#var_dump($check);
		#echo $tbl;
		#echo $con->dbname;
		$tables = array();
		while($table = mysql_fetch_array($check,MYSQL_NUM)){
			$tables[] = $table[0];
		}
		#echo "<pre>";var_dump($tables); echo "<br>";
		if(in_array($tbl,$tables)) return true;
		else return false;
	}
	public function CheckDB($dbname){
		$sql ="SHOW DATABASES";
		$rslst = $this->RunSqlQuery($sql,true);
		#var_dump($rslst);
		//format the found result
		$db_array = $this->formatResultSet($rslst,$indexed=1,$multipleRows=1);
		#echo "<pre>";var_dump($db_array);
		//loop all found database and compare the given one
		
		for($i=0;$i<count($db_array);$i++){
			if($dbname == $db_array[$i]["Database"])
				return true;
		}
		return false;
	}
	public function checkField($tbl,$field){
		$check = mysql_query("DESCRIBE `{$tbl}`;")or die(mysql_error());
		#var_dump($check);
		$fields = array();
		while($fld = mysql_fetch_array($check,MYSQL_NUM)){
			$fields[] = $fld[0];
		}
		#echo "<pre>";var_dump($fields);
		#var_dump($tables); echo "<br>";
		if(in_array($field,$fields)){
			#echo $field."<br />";
			return true;
		}
		else return false;
	}
	public function selectMin($tbl,$fld,$rtn=false,$condition=null){
		$sql = "SELECT MIN(`".$fld."`) FROM `".$tbl."`";
		if($condition != null){
			$sql .= " WHERE "; $i=0;
			foreach($condition as $field=>$data){
				if($i!=0) $sql .= "&& ";
				$sql .= " `".$field."`='".$data."'";
				$i++;
			}
		}
		#echo $sql;
		$result = mysql_query($sql);
		$res = mysql_fetch_array($result,MYSQL_NUM);
		if($rtn == true) return $res;
		if($rtn == false) return $res[0];
		return null;
	}
	public function DropTable($tbl){
		if($tbl == null || $tbl='') return null;
		mysql_query("DROP TABLE `{$tbl}`");
	}
	function selectSum($tbl,$fld,$condition=null){
		if($tbl == null || $tbl=='' || $fld == null || $fld =='') return null;
		$sql = "SELECT SUM(`".$fld."`) FROM `".$tbl."`";
		if($condition != null){
			$sql .= " WHERE "; $i=0;
			foreach($condition as $field=>$data){
				if($i!=0) $sql .= "&& ";
				if(preg_match("/^LIKE/",$data) || preg_match("/^NOT LIKE/",$data))
					$sql .= " `".$field."` ".$data;
				else
					$sql .= " `".$field."`='".$data."'";
				$i++;
			}
		}
		#echo $sql;
		$result = mysql_query($sql)or die(mysql_error());
		$res = mysql_fetch_array($result,MYSQL_NUM);
		return $res[0];
		#return null;
	}
	function selectCount($tbl,$fld,$condition=null,$comp="&&",$multipleReference=false,$sign=false){
		#echo $comp;
		if($tbl == null || $tbl=='' || $fld == null || $fld =='') return null;
		if($sign == true) $multipleReference=false;
		$sql = "SELECT COUNT(`".$fld."`) FROM `".$tbl."`";
		if($condition != null){
			$sql .= " WHERE "; $i=0;
			foreach($condition as $field=>$data){
				#echo $i;
				if($multipleReference && is_array($data)){
					foreach($data as $v){
						if($i!=0) $sql .= $comp." ";
						$sql .= " `".$field."`='".$v."'";
						$i++;
					}
				} else{
					if($sign && is_array($data)){
						#var_dump($data);
						#foreach($data as $v){
							if($i!=0) $sql .= $comp." ";
							$sql .= " `".$field."`".$data['sign']."'".$data['value']."'";
							$i++;
						#}
					} else{
						if($i!=0) $sql .= $comp." ";
						$sql .= " `".$field."`='".$data."'";
						$i++;
					}
				}
			}
		}
		#echo $sql;
		$result = mysql_query($sql)or die(mysql_error());
		$res = mysql_fetch_array($result,MYSQL_NUM);
		return $res[0];
		return null;
	}
	function fieldList($table){
		if($table == null || $table == "") return null;
		if($this->checkTable($table,$this)){
			$sql = "DESCRIBE `{$table}`";
			#echo $sql;
			$query = mysql_query($sql);
			if($query){
				$returnString = array();
				while($row = mysql_fetch_assoc($query) ) $returnString[] = $row['Field'];
				return $returnString;
			}
		}
		return null;
	}
	
	function formatResultSet($resultSet,$indexed=0,$multipleRows=0){
		if($resultSet){
			$rtn = array();
			if($multipleRows){
				while($row = mysql_fetch_array($resultSet,($indexed?MYSQL_ASSOC:MYSQL_NUM))){
					$rtn[] = $row;
				}
				return $rtn;
			} else{
				return mysql_fetch_array($resultSet,($indexed?MYSQL_ASSOC:MYSQL_NUM));
			}
		}
	}
	function RunSqlQuery($queryString,$rst){
		#echo $queryString; return;
		
		if($result = mysql_query($queryString)or die(mysql_error())){
			if($rst)
				return  $result;
			else
				return true;
			return false;
		}else
			return false;
	}
	
	function PDB($str, $trim=true){
		return mysql_real_escape_string($trim?trim($str):$str);
	}
}

date_default_timezone_set('Africa/Kigali');
define("SCHOOL_MANAGER","SCHOOL MANAGER");

//function to return formatted time
function _time($sec){
	if($sec<0)
		return "#inv";
		
	$min = intval($sec/60,10);
	$sc = ($sec%60);
	$sc = $sc<10?"0".$sc:$sc;
	$min = $min <10?"0".$min:$min;
	if($min>59){
		$hour = intval($min/60,10);
		$hour = $hour<10?"0".$hour:$hour;
		$min %= 60;
		$min = $min <10?"0".$min:$min;
		
		return $hour.":".$min.":".$sc;
	}
	return "00:".$min.":".$sc;
}
function formatDateDifference($date1,$date2,$strict, &$total_secs=0){
	//return d;
	return _time(dateDifferentInSec($date1,$date2,$strict,$total_secs));
}
function HoursFromSecs($secs){
	//echo $secs;
	if($secs<0)
		return 0;
	return intval($secs/(60*60));

}
function dateDifferentInSec($date1,$date2,$strict=false, &$total_secs=0){
	if($strict && $date1>$date2){
		return "#inv";
	}
	//echo "DONE!";
	//echo $date1;
	if($date1>$date2){
		$date3 = $date1;
		$date1 = $date2;
		$date2 = $date3;
	}
	//echo $date2.":";
	//echo $date1;
	$year = 0;$month = 0;$days = 0;$hour = 0;$min = 0;$secs = 0;
	splitDate($date1,$year,$month,$days,$hour,$min,$secs);
	//echo $secs."--<br />";
	$time1 = mktime($hour, $min, $secs, $month,$days,$year);
	//return $min.":".$time1;//."==>".$date2;
	splitDate($date2,$year,$month,$days,$hour,$min,$secs);
	$time2 = mktime($hour, $min,$secs,$month,$days,$year);
	//return $time2." ".$time1;
	$total_secs = ($time2 - $time1);
	return ($time2 - $time1);
}
function splitDate($date,&$y=0,&$m=0,&$d=0,&$h=0,&$i=0,&$s=0){
	$dt = "";
	$hour = "";
	if(preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/",$date)){
		$k = explode(" ",$date);
		$dt = $k[0];
		$hour = $k[1];
	} elseif(preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$date)){
		$dt = $date;
	}
	//manipulate date
	$dtt = explode("-",$dt);
	$y = $dtt[0];
	$m = $dtt[1];
	$d = $dtt[2];
	if($hour){
		$htt = explode(":",$hour);
		$h = $htt[0];
		$i = $htt[1];
		$s = $htt[2];
	}
}
?>					