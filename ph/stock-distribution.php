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
	$filterCondition = "";
	if(@$_GET['filter'] && trim($_GET['keyword'])){
		$filterCondition .= " && a.MedecineName LIKE('%".PDB($_GET['keyword'],true,$con)."%')";
		/*$diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT md_name.* FROM md_name WHERE md_name.MedecineName LIKE('%".PDB($_GET['keyword'],true,$con)."%') && CategoryCode != '' ORDER BY MedecineName ASC",$con),$multirows=true,$con);
		//echo $sql;
		if($diagnostic){
			echo "<table class=list>";
			for($i=0; $i<count($diagnostic); $i++){
				$stock = formatResultSet($rslt=returnResultSet($sql="SELECT md_stock.* FROM md_stock WHERE md_stock.MedicineNameID ='".PDB($diagnostic[$i]['MedecineNameID'],true,$con)."' ORDER BY Quantity ASC",$con),$multirows=false,$con);
				$ommit = false;
				$display = ommitStringPart($str=$diagnostic[$i]['MedecineName'],$char_to_display=30,$ommit);
				echo "<tr onclick='$(\".medicine_field\").html(\"{$diagnostic[$i]['MedecineName']}\");$(\"#medicine\").val(\"{$diagnostic[$i]['MedecineNameID']}\");' style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">
				<td>".($i+1)."</td>
				<td>".$display."</td><td>{$stock['Quantity']}</td>
				<td>Ajust</td>
				</tr>";
			}
			echo "</table>";
		} else{
			echo "<span class=error-text >No Medicine in the Stock</span>";
		}
		return;*/
	}
	//select all active diagnostic now
	$condition1 = @$_GET['start_date']?"&& md_stock_records.Date >= ".mktime(0,0,0,explode("-",$_GET['start_date'])[1],explode("-",$_GET['start_date'])[2],explode("-",$_GET['start_date'])[0]):"";
	$condition2 = @$_GET['end_date']?"&& md_stock_records.Date <= ".mktime(23,59,59,explode("-",$_GET['end_date'])[1],explode("-",$_GET['end_date'])[2],explode("-",$_GET['end_date'])[0]):"";
	$diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.MedecineName AS MedecineName,
																		c.Date AS Date,
																		f.Name AS Name,
																		c.Quantity AS Quantity,
																		a.MedecineNameID AS MedecineNameID
																		FROM md_name AS a
																		INNER JOIN md_price AS b
																		ON a.MedecineNameID = b.MedecineNameID
																		INNER JOIN md_records AS c
																		ON b.MedecinePriceID = c.MedecinePriceID
																		INNER JOIN md_stock AS d
																		ON a.MedecineNameID = d.MedicineNameID
																		INNER JOIN md_stock_records AS e
																		ON d.MedicineStockID = e.MedicineStockID
																		INNER JOIN sy_users AS f
																		ON c.PharmacistID = f.UserID
																		WHERE e.Operation='DISTRIBUTION' &&
																			  c.Date >= '{$_GET['start_date']}' &&
																			  c.Date <= '{$_GET['end_date']}'
																			  {$filterCondition}
																		ORDER BY e.Date ASC
																	",$con),$multirows=true,$con);

	// echo $sql;
	if($diagnostic){
		echo "<table class=list>
		<tr><th>#</th><th>Medicine</th><th>Qty</th><th>Date</th><th>User</th></tr>";
		for($i=0; $i<count($diagnostic); $i++){
			$ommit = false;
			$display = ommitStringPart($str=$diagnostic[$i]['MedecineName'],$char_to_display=30,$ommit);
			echo "<tr onclick='$(\".medicine_field\").html(\"{$diagnostic[$i]['MedecineName']}\");$(\"#medicine\").val(\"{$diagnostic[$i]['MedecineNameID']}\");' style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">
				<td>".($i+1)."</td>
				<td>".$display."</td>
				<td align=right>{$diagnostic[$i]['Quantity']}</td>
				<td>".$diagnostic[$i]['Date']."</td>
				<td>{$diagnostic[$i]['Name']}</td>
				</tr>";
			//echo "<div style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">".$display." {$diagnostic[$i]['Quantity']}</div>";
		}
		echo "</table>";
	} else{
		echo "<span class=error-text >No Medicine in the Adjust Report</span>";
	}
?>