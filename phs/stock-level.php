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
	</style>
	<?php
	if(@$_GET['filter'] && trim($_GET['keyword'])){
		$keyword = PDB($_GET['keyword'], true, $con);
		$sql = "SELECT 	a.*,
						c.Quantity AS Quantity,
						c.id AS MedicineStockID,
						COALESCE(b.batchNumber, a.CategoryCode, '') AS batchNumber
						FROM md_name AS a
						LEFT JOIN md_stock_batch AS b
						ON a.MedecineNameID = b.medicineNameId
						LEFT JOIN md_main_stock AS c
						ON b.id = c.batchId
						WHERE a.MedecineName LIKE('%{$keyword}%') ||
							  b.batchNumber = '{$keyword}'
						ORDER BY a.MedecineName ASC
						";
		// echo $sql;
		// $d = mysql_query("SELECT Date FROM md_price WHERE Date <= '".(date("Y-m-d",time()))."' ORDER BY Date DESC LIMIT 0,1");
		// $active_date = mysql_fetch_assoc($d)['Date'];
		
		$diagnostic = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
		// $diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT md_name.* FROM md_name, md_price WHERE md_price.MedecineNameID = md_name.MedecineNameID && md_price.Date='{$active_date}' && md_name.MedecineName LIKE('%".PDB($_GET['keyword'],true,$con)."%') ORDER BY MedecineName ASC",$con),$multirows=true,$con);
		//echo $sql;
		if($diagnostic){
			echo "<table class=list><tr><th>#</th><th>Batch</th><th>Medicine</th><th>Stock</th><th></th></tr>";
			for($i=0; $i<count($diagnostic); $i++){
				$stock = $diagnostic[$i];
				// $stock = formatResultSet($rslt=returnResultSet($sql="SELECT md_stock.* FROM md_stock WHERE md_stock.MedicineNameID ='".PDB($diagnostic[$i]['MedecineNameID'],true,$con)."' ORDER BY Quantity ASC",$con),$multirows=false,$con);
				$ommit = false;
				$display = ommitStringPart($str=$diagnostic[$i]['MedecineName'],$char_to_display=30,$ommit);
				echo "<tr onclick='$(\".medicine_field\").html(\"{$diagnostic[$i]['MedecineName']}\");$(\"#medicine\").val(\"{$diagnostic[$i]['MedecineNameID']}\");' style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">
				<td>".($i+1)."</td>
				<td>".$stock['batchNumber']."</td>
				<td>".$display."</td>
				<td id='nn{$i}'>{$stock['Quantity']}</td>
				<td><a href='#' onclick='regulateStock(\"{$stock['MedicineStockID']}\",{$i}, \"{$stock['batchNumber']}\"); return false;' style='color:blue; text-decoration:none;'>Adjust</a></td>
				</tr>";
			}
			echo "</table>";
		} else{
			echo "<span class=error-text >No Medicine in the Stock</span>";
		}
		return;
	}
	//select all active medicines now
	$sql = "SELECT 	a.*,
					SUM(c.Quantity) AS Quantity,
					c.id AS MedicineStockID,
					GROUP_CONCAT(b.batchNumber) AS batchNumber
					FROM md_name AS a
					INNER JOIN md_stock_batch AS b
					ON a.MedecineNameID = b.medicineNameId
					INNER JOIN md_main_stock AS c
					ON b.id = c.batchId
					WHERE c.quantity > 0
					GROUP BY a.MedecineNameID
					ORDER BY c.quantity ASC
					";
	$diagnostic = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	// $diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT md_name.*, md_stock.Quantity, md_stock.MedicineStockID FROM md_name, md_stock WHERE md_stock.MedicineNameID = md_name.MedecineNameID ORDER BY md_stock.Quantity ASC",$con),$multirows=true,$con);
	if($diagnostic){
		echo "<table class=list>";
		echo "<table class=list><tr><th>#</th><th>Batch</th><th>Medicine</th><th>Stock</th><th></th></tr>";
		for($i=0; $i<count($diagnostic); $i++){
			$ommit = false;
			$display = ommitStringPart($str=$diagnostic[$i]['MedecineName'],$char_to_display=30,$ommit);
			echo "<tr onclick='return false; $(\".medicine_field\").html(\"{$diagnostic[$i]['MedecineName']}\");$(\"#medicine\").val(\"{$diagnostic[$i]['MedecineNameID']}\");' style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">
				<td>".($i+1)."</td>
				<td>".$diagnostic[$i]['batchNumber']."</td>
				<td>".$display."</td>
				<td align=right id='nn{$i}'>{$diagnostic[$i]['Quantity']}</td>
				<td> <a href='#' onclick='return false; regulateStock(\"{$diagnostic[$i]['MedicineStockID']}\",{$i}, \"{$diagnostic[$i]['batchNumber']}\"); return false;' style='color:blue; text-decoration:none;'>Adjust</a></td>
				</tr>";
			//echo "<div style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">".$display." {$diagnostic[$i]['Quantity']}</div>";
		}
		echo "</table>";
	} else{
		echo "<span class=error-text >No Medicine in the Stock</span>";
	}
?>