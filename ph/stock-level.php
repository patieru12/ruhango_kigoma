<?php
	session_start();
	
	require_once "../lib/db_function.php";
	//var_dump($_GET);
	?>
	<style>
		.list{
			width:100%;
		}
		.list tr:hover{
			background-color:#ddd;
			
		}
		.list tr.underLow{
			background-color: #dd8;
		}
		.list tr.underCritical{
			background-color: #da8;
		}
	</style>
	<?php
	if(@$_GET['filter'] && trim($_GET['keyword'])){
		$d = mysql_query("SELECT Date FROM md_price WHERE Date <= '".(date("Y-m-d",time()))."' ORDER BY Date DESC LIMIT 0,1");
		$active_date = mysql_fetch_assoc($d)['Date'];
		
		$diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT md_name.* FROM md_name, md_price WHERE md_price.MedecineNameID = md_name.MedecineNameID && md_price.Date='{$active_date}' && md_name.MedecineName LIKE('%".PDB($_GET['keyword'],true,$con)."%') ORDER BY MedecineName ASC",$con),$multirows=true,$con);
		//echo $sql;
		if($diagnostic){
			echo "<table class=list><tr><th>#</th><th>Medicine</th><th>Stock</th><th></th></tr>";
			for($i=0; $i<count($diagnostic); $i++){
				$stock = formatResultSet($rslt=returnResultSet($sql="SELECT md_stock.* FROM md_stock WHERE md_stock.MedicineNameID ='".PDB($diagnostic[$i]['MedecineNameID'],true,$con)."' ORDER BY Quantity ASC",$con),$multirows=false,$con);
				$ommit = false;
				$display = ommitStringPart($str=$diagnostic[$i]['MedecineName'],$char_to_display=30,$ommit);
				echo "<tr onclick='$(\".medicine_field\").html(\"{$diagnostic[$i]['MedecineName']}\");$(\"#medicine\").val(\"{$diagnostic[$i]['MedecineNameID']}\");' style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">
				<td>".($i+1)."</td>
				<td>".$display."</td><td>{$stock['Quantity']}</td>
				<td><a href='#' onclick='regulateStock(\"{$stock['MedicineStockID']}\",{$i}); return false;' style='color:blue; text-decoration:none;'>Adjust</a></td>
				</tr>";
			}
			echo "</table>";
		} else{
			echo "<span class=error-text >No Medicine in the Stock</span>";
		}
		return;
	}
	//select all active diagnostic now
	$diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT md_name.*, md_stock.Quantity, md_stock.MedicineStockID FROM md_name, md_stock WHERE md_stock.MedicineNameID = md_name.MedecineNameID ORDER BY md_stock.Quantity ASC",$con),$multirows=true,$con);
	if($diagnostic){
		echo "<table class=list>";
		for($i=0; $i<count($diagnostic); $i++){
			$ommit = false;
			$display = ommitStringPart($str=$diagnostic[$i]['MedecineName'],$char_to_display=30,$ommit);
			echo "<tr class='".($diagnostic[$i]['Quantity'] <= $diagnostic[$i]['criticalLevel']?"underCritical":($diagnostic[$i]['Quantity'] <= $diagnostic[$i]['lowLevel']?"underLow":"normalStock"))."' onclick='$(\".medicine_field\").html(\"{$diagnostic[$i]['MedecineName']}\");$(\"#medicine\").val(\"{$diagnostic[$i]['MedecineNameID']}\");' style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">
				<td>".($i+1)."</td>
				<td>".$display."</td>
				<td align=right id='nn{$i}'>{$diagnostic[$i]['Quantity']}</td>
				<td> <a href='#' onclick='regulateStock(\"{$diagnostic[$i]['MedicineStockID']}\",{$i}); return false;' style='color:blue; text-decoration:none;'>Adjust</a></td>
				</tr>";
			//echo "<div style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">".$display." {$diagnostic[$i]['Quantity']}</div>";
		}
		echo "</table>";
	} else{
		echo "<span class=error-text >No Medicine in the Stock</span>";
	}
?>