<?php
/* FUNCTIONS */
/*		prepareTestset($dataset_name)
 * This function randomly collects TESTSET_SIZE number
 * of tweets from the $dataset_name file. */

 
/* CONSTANTS */
/* Directory where the testset will be stored */
define("TESTSET_LOCATION", "../_testsets/");


/* FUNCTION LISTING */
function labelTestset($testset_name)
{
	$fp_from=fopen(constant("TESTSET_LOCATION").$testset_name.".csv", "r");
	$fp_to=fopen("_".$testset_name.".csv", "a");	
	
	while(!feof($fp_from))
	{
		$new_tweet=fgetcsv($fp_from, 256);
		
		system("clear");
		print("Tweet:\n".$new_tweet[1]."\n");
		print("Catagory:\t".$new_tweet[0]."\n");
		print("1  OK\t2  SWAP\t3  SKIP\nSelect an option... ");
		
		fputcsv($fp_to, $new_tweet);
	}

	fclose($fp_from);
	fclose($fp_to);
}
?>
