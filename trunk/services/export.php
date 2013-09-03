<?php
try {
	set_time_limit(1000);
	 
	require_once( "../application/configs/config.php" );
	$application->bootstrap();
	
	header("Content-Disposition: attachment; filename=\"dico.csv\"; ");
	header("Content-Type: text/csv");
	if(isset($_GET['idDico'])){
		$dbDico = new Model_DbTable_Gen_dicos();
		$arr = $dbDico->exporter($_GET['idDico']);
		$csv_data = array_to_scv($arr);
		print_r($csv_data);
 	}else
		echo "ERREUR : aucun dictionnaire n'est spécifié.";
	
}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}

/**
* Generatting CSV formatted string from an array.
* By Sergey Gurevich.
*/
function array_to_scv($array, $header_row = true, $col_sep = ",", $row_sep = "\n", $qut = '"')
{
	if (!is_array($array) or !is_array($array[0])) return false;
	
	//Header row.
	if ($header_row)
	{
		foreach ($array[0] as $key => $val)
		{
			//Escaping quotes.
			$key = str_replace($qut, "$qut$qut", $key);
			$output .= "$col_sep$qut$key$qut";
		}
		$output = substr($output, 1)."\n";
	}
	//Data rows.
	foreach ($array as $key => $val)
	{
		$tmp = '';
		foreach ($val as $cell_key => $cell_val)
		{
			//Escaping quotes.
			$cell_val = str_replace($qut, "$qut$qut", $cell_val);
			$tmp .= "$col_sep$qut$cell_val$qut";
		}
		$output .= substr($tmp, 1).$row_sep;
	}
	
	return $output;
}