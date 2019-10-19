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
		$d = mysql_query("SELECT Date FROM md_price WHERE Date <= '".(date("Y-m-d",time()))."' ORDER BY Date DESC LIMIT 0,1");
		$active_date = mysql_fetch_assoc($d)['Date'];
		$keyword = PDB($_GET['keyword'],true,$con);



		$diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT 	d.*,
																			a.Quantity AS mainStockQuantity
																			FROM md_main_stock AS a
																			INNER JOIN md_stock_batch AS b
																			ON a.batchId = b.id
																			INNER JOIN md_name AS c
																			ON b.medicineNameId = c.MedecineNameID
																			INNER JOIN (
																				SELECT 	a.MedecineName, 
																						COALESCE(b.batchNumber, a.CategoryCode) AS batchNumber,
																						a.MedecineNameID,
																						c.expirationDate,
																						c.Quantity,
																						c.MedicineStockID 
																						FROM md_name AS a
																						LEFT JOIN md_stock_batch AS b
																						ON a.MedecineNameID = b.medicineNameId
																						INNER JOIN md_stock_v2 AS c
																						ON b.id = c.batchId
																						WHERE a.MedecineName LIKE('%{$keyword}%')
																						ORDER BY c.Quantity ASC
																			) AS d
																			ON c.MedecineNameID = d.MedecineNameID
																			WHERE a.Quantity > 0
																			",$con),$multirows=true,$con);

		/*$diagnostic = formatResultSet(returnResultSet("SELECT 	a.MedecineName, 
																COALESCE(b.batchNumber, a.CategoryCode) AS batchNumber,
																a.MedecineNameID,
																c.expirationDate,
																c.Quantity,
																c.MedicineStockID,
																'' AS mainStockQuantity
																FROM md_name AS a
																LEFT JOIN md_stock_batch AS b
																ON a.MedecineNameID = b.medicineNameId
																INNER JOIN md_stock_v2 AS c
																ON b.id = c.batchId
																WHERE a.MedecineName LIKE('%{$keyword}%')
																ORDER BY c.Quantity ASC",$con), true, $con);*/
		
		// $diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT md_name.* FROM md_name, md_price WHERE md_price.MedecineNameID = md_name.MedecineNameID && md_price.Date='{$active_date}' && md_name.MedecineName LIKE('%".."%') ORDER BY MedecineName ASC",$con),$multirows=true,$con);
		//echo $sql;
		// echo "<pre>"; var_dump($diagnostic);
		if($diagnostic){
			echo "<table class=list><tr><th>#</th><th>Batch</th><th>Medicine</th><th>Expiration</th><th>Main Stock Level</th><th>Stock Level</th><th></th></tr>";
			for($i=0; $i<count($diagnostic); $i++){
				$stock = $diagnostic[$i]; //formatResultSet($rslt=returnResultSet($sql="SELECT md_stock.* FROM md_stock WHERE md_stock.MedicineNameID ='".PDB($diagnostic[$i]['MedecineNameID'],true,$con)."' ORDER BY Quantity ASC",$con),$multirows=false,$con);
				$ommit = false;
				$display = ommitStringPart($str=$diagnostic[$i]['MedecineName'],$char_to_display=30,$ommit);
				echo "<tr onclick='$(\".medicine_field\").html(\"{$diagnostic[$i]['MedecineName']}\");$(\"#medicine\").val(\"{$diagnostic[$i]['MedecineNameID']}\");' style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">
				<td>".($i+1)."</td>
				<td>{$stock['batchNumber']}</td>
				<td>".$display."</td>
				<td>{$stock['expirationDate']}</td>
				<td>{$stock['mainStockQuantity']}</td>
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
	//select all active diagnostic now
	$diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.MedecineName, 
																		GROUP_CONCAT(b.batchNumber) AS batchNumber, 
																		MIN(c.expirationDate) AS expirationDate,
																		SUM(c.Quantity) AS Quantity,
																		c.MedicineStockID,
																		a.MedecineNameID 
																		FROM md_name AS a
																		INNER JOIN md_stock_batch AS b
																		ON a.MedecineNameID = b.medicineNameId
																		INNER JOIN md_stock_v2 AS c
																		ON b.id = c.batchId
																		WHERE c.Quantity > 0
																		GROUP BY MedecineNameID
																		ORDER BY c.Quantity ASC",$con),$multirows=true,$con);
	if($diagnostic){
		echo "<table class=list><tr><th>#</th><th>Batch</th><th>Medicine</th><th>Expiration</th><th>Stock</th><!--<th>Adjust</th>--></tr>";
		for($i=0; $i<count($diagnostic); $i++){
			$md = $diagnostic[$i];
			$ommit = false;
			$display = ommitStringPart($str=$diagnostic[$i]['MedecineName'],$char_to_display=30,$ommit);
			echo "<tr onclick='$(\".medicine_field\").html(\"{$diagnostic[$i]['MedecineName']}\");$(\"#medicine\").val(\"{$diagnostic[$i]['MedecineNameID']}\");' style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">
				<td>".($i+1)."</td>
				<td>{$md['batchNumber']}</td>
				<td>".$display."</td>
				<td>{$md['expirationDate']}</td>
				<td align=right id='nn{$i}'>{$diagnostic[$i]['Quantity']}</td>
				<!--<td> <a href='#' onclick='return false;' style='color:blue; text-decoration:none;'>Adjust</a></td>-->
				</tr>";
			//echo "<div style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">".$display." {$diagnostic[$i]['Quantity']}</div>";
		}
		echo "</table>";
	} else{
		echo "<span class=error-text >Details Stock is empty.<br />Please type in the medicine you want to make a request or adjust<br />&nbsp;<br />If you have physical stock</span>";
	}
?>