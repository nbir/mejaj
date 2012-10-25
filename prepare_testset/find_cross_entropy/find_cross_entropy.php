<?php
/* FUNCTIONS */
/*		findCrossEntropy($dataset_cf_file, $testset_cf_file)
 * This function finds the Cross Entropy of two tweet collections.
 * The character frequencies of the collections are stored in 
 * $dataset_cf_file and $testset_cf_file */


/* FUNCTION LISTING */
function findCrossEntropy($dataset_cf_file, $testset_cf_file)
{
	global $dataset_cf, $testset_cf;
	global $ds_total, $ts_total;
	
	loadCharFreqs($dataset_cf_file, $testset_cf_file);
	//print_r($dataset_cf);
	//print_r($testset_cf);
	
	$entropy=0;
	foreach($dataset_cf as $char=>$ds_count)
	{
		if($ds_count!=0 && $testset_cf[$char] !=0)
			$entropy+=(($ds_count/$ds_total)*log(($testset_cf[$char]/$ts_total), 2));
	}
	$entropy*=(-1);
	
	print("Cross Entropy : ".$entropy."\n");
}

function loadCharFreqs($dataset_cf_file, $testset_cf_file)
{
	global $dataset_cf, $testset_cf;
	global $ds_total, $ts_total;
	
	$dataset_cf_temp=(array) json_decode(file_get_contents($dataset_cf_file.".json", true));
	foreach($dataset_cf_temp as $char=>$count)
		$dataset_cf[ord($char)]=$count;
	$ds_total=array_sum($dataset_cf);
	
	$testset_cf_temp=(array) json_decode(file_get_contents($testset_cf_file.".json", true));
	foreach($testset_cf_temp as $char=>$count)
		$testset_cf[ord($char)]=$count;
	$ts_total=array_sum($testset_cf);
}
?>
