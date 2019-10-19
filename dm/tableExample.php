<?php
$tbl_content = array(
					array(
						'size'=>300,
						'content'=>array(
										array('size'=>($table_width * 0.8),'content'=>'E) Nouveaux cas de maladies (Consultation pour enfants < 5 ans voir PECIME)','font'=>$header_small,'format'=>$withBorder, 'colspan'=>$cellColSpanInner3, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'5 - 19 ans','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'≥ 20 ans','font'=>null,'format'=>$withBorder, 'colspan'=>$cellColSpanInner2, 'rowspan'=>null)
										)
						)
					);

//var_dump($new_case);
$sex = array("Female"=>"F","Male"=>"M");
$row_size = 300;
$tbl_content[] = array(
						'size'=>$row_size,
						'content'=>array(
										array('size'=>($table_width * 0.05),'content'=>'#','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.65),'content'=>'Diagnostique','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.1),'content'=>'ICD-10','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'M','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
										array('size'=>($table_width * 0.05),'content'=>'F','font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
										)
						);
for($i=0;$i<count($new_case);$i++){
	$tbl_content[] = array(
							'size'=>$row_size,
							'content'=>array(
											array('size'=>($table_width * 0.05),'content'=>($i+1),'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.65),'content'=>$new_case[$i]['DiagnosticName'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.1),'content'=>$new_case[$i]['Code'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['M5_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['F5_19'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['M20'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null),
											array('size'=>($table_width * 0.05),'content'=>$new_case[$i]['F20'],'font'=>null,'format'=>$withBorder, 'colspan'=>null, 'rowspan'=>null)
										)
							);
}

$table = $section->addTable("Process Table");
foreach($tbl_content as $row_prop){
	$table->addRow($row_prop['size'],array('exactHeight'=>true));
	foreach($row_prop['content'] as $cell_prop){
		if($cell_prop['rowspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['rowspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font']);
		else if($cell_prop['colspan'] != null)
			$table->addCell($cell_prop['size'],$cell_prop['colspan'],@$cell_prop['format'])->addText(htmlspecialchars(@$cell_prop['content'],ENT_COMPAT,'UTF-8'),@$cell_prop['font']);
		else
			$table->addCell($cell_prop['size'],$cell_prop['format'])->addText(htmlspecialchars($cell_prop['content'],ENT_COMPAT,'UTF-8'));
	}
}

$section->addTextBreak(1);
?>