<?php
/* FUNCTIONS */
/*		prepareTestset($dataset_name)
 * This function randomly collects TESTSET_SIZE number
 * of tweets from the $dataset_name file. */

 
/* CONSTANTS */
/* Filename for the file (JSON)) that stores the 
 * names of tweet files (CSV) collected so far. */
//define("LIST_TWEET_FILENAMES", "ListTweetFile");

/* Number of twets to be selected for the testset. */
define("TESTSET_SIZE", 500);
/* Random value range = size of dataset / NUMBER_OF_SLICE.
 * It defines the maximum number of tweets to consider
 * while selecting the next random tweet. */
define("NUMBER_OF_SLICE", 20);
/* Filename for the file (JSON)) that stores the list of 
 * keywords and their corresponding catagory names.. */
define("LIST_KEYWORDS", "ListKeyword");
/* Directory where the testset will be stored */
define("TESTSET_LOCATION", "../_testsets/");


/* GLOBAL VARIABLES */
/* Global arrays storing filenames, tweet IDs and keywords */
$KeyWords=array();


/* FUNCTION LISTING */
function prepareTestset($dataset_dir, $dataset_name)
{
	global $KeyWords, $KeyWords_left, $testset_size;
	
	loadKeywords();
	$dataset_size=countDataset($dataset_dir.$dataset_name);
	$max_random=$dataset_size/constant("NUMBER_OF_SLICE");
	
	$testset_name="test.".$dataset_name.".".date("mdHi");	
	$fp_dataset=fopen($dataset_dir.$dataset_name.".csv", "r");
	$fp_testset=fopen(constant("TESTSET_LOCATION").$testset_name.".csv", "a");
	
	$no_in_ts=0;
	$selected=array();
	
	while($no_in_ts<$testset_size)
	{
		//$jump=mt_rand(0, 10000);
		$jump=mt_rand(0, $max_random);
		
		for(;$jump>=0;$jump--)
		{
			if(feof($fp_dataset))
				$fp_dataset=fopen($dataset_dir.$dataset_name.".csv", "r");
			
			fgetcsv($fp_dataset, 256);
		}
		
		if(feof($fp_dataset))
			$fp_dataset=fopen($dataset_dir.$dataset_name.".csv", "r");
		
		$new_tweet=fgetcsv($fp_dataset, 256);
		if(!in_array($new_tweet[2],$selected) && $KeyWords_left[$new_tweet[0]]>0)
		{
			array_push($selected, $new_tweet[2]);
			$KeyWords_left[$new_tweet[0]]--;
			$new_tweet[0]=$KeyWords[$new_tweet[0]];
			unset($new_tweet[2]);
			
			fputcsv($fp_testset, $new_tweet);
			
			$no_in_ts++;
			print($no_in_ts.", ");
		}
	}

	fclose($fp_dataset);
	fclose($fp_testset);
}

function loadKeywords()
{
	global $KeyWords, $KeyWords_left, $testset_size;
	
	$KeyWords=(array) json_decode(file_get_contents(constant("LIST_KEYWORDS").".json", true));
	
	$limit=ceil(constant("TESTSET_SIZE")/count($KeyWords));
	
	foreach($KeyWords as $key=>$value)
		$KeyWords_left[$key]=$limit;
	
	$testset_size=count($KeyWords)*$limit;
}

function countDataset($file_name)
{
	$fp=fopen($file_name.".csv", "r");

	$no_of_tweets=0;
	while(!feof($fp))
	{
		$new_tweet=fgetcsv($fp, 256);
		
		if($new_tweet)
			$no_of_tweets++;
	}

	fclose($fp);
	
	return $no_of_tweets;
}
?>
