<?php
/* FUNCTIONS */
/*		prepareDataset()
 * This function accumulates tweets from the various files 
 * listed in the file list. It requires the setDatasetName() 
 * function to be executed first. */
 
/*		setDatasetName($name)
 * This function sets the name for the dataset. If the 
 * parameter $name is not specified, it automatically 
 * sets the current date and time as filename. */
 
 
/* CONSTANTS */
/* Filename for the file (JSON)) that stores the 
 * names of tweet files (CSV) collected so far. */
//define("LIST_TWEET_FILENAMES", "ListTweetFile");

/* The Directory which contains the tweet files (CSV)
 * to form a dataset of. */
define("DIR_TWEET_FILES", "DirTweetFiles");
/* Filename for the file (JSON)) that stores the 
 * list of tweet IDs added to the dataset so far. */
define("LIST_TWEET_IDS", "ListTweetID");
/* Filename for the file (JSON)) that stores the list of 
 * keywords and their corresponding catagory names.. */
define("LIST_KEYWORDS", "ListKeyword");


/* GLOBAL VARIABLES */
/* Global arrays storing filenames, tweet IDs and keywords */
$FileNames=array();
$TweetIDs=array();
$KeyWords=array();


/* FUNCTION LISTING */
function prepareDataset()
{
	global $FileNames;

	loadFileNames();
	loadTweetIDs();
	loadKeywords();
	
	foreach($FileNames as $file_name)
		collectTweetsFrom($file_name);
	
	storeTweetIDs();
}


function setDatasetName($name=null)
{
	global $dataset_file_name;
	
	if($name)
		$dataset_file_name=$name;
	else
		$dataset_file_name="dataset.".date("Y-m-d_H-i-s");
}

function collectTweetsFrom($file_name)
{
	global $TweetIDs, $KeyWords, $dataset_file_name;
	
	if(file_exists(constant("DIR_TWEET_FILES")."/".$file_name.".csv"))
	{
		$fp=fopen(constant("DIR_TWEET_FILES")."/".$file_name.".csv", "r");
		$fp_dataset=fopen($dataset_file_name.".csv", "a");

		while(!feof($fp))
		{
			$new_tweet=fgetcsv($fp, 256);
			
			//if($new_tweet && (!in_array((string) $new_tweet[2], $TweetIDs)))
			if($new_tweet && (!isset($TweetIDs[$new_tweet[2]])))
			{
				//array_push($TweetIDs, (string) $new_tweet[2]);
				$TweetIDs[$new_tweet[2]] = 1;
				
				$new_tweet[1]=replaceKeyword($new_tweet[1], $new_tweet[0]);
				$new_tweet[0]=$KeyWords[$new_tweet[0]];
				unset($new_tweet[2]);
				
				fputcsv($fp_dataset, $new_tweet);
			}
		}

		fclose($fp);
		fclose($fp_dataset);
	}
}

function replaceKeyword($tweet_text, $keyword)
{
	global $KeyWords;
	
	$tweet_text=preg_replace("/$keyword/i", "", $tweet_text);
	
	return $tweet_text;
}
/*
function loadFileNames()
{
	global $FileNames;
	
	$FileNames=json_decode(file_get_contents(constant("LIST_TWEET_FILENAMES").".json", true));
}
*/
function loadFileNames()
{
	global $FileNames;

	$dh=opendir(constant("DIR_TWEET_FILES"));
	
	while($new_file_name=readdir($dh))
	{
		if($new_file_name!="." && $new_file_name!="..")
			array_push($FileNames, preg_replace("/\.csv/", "", $new_file_name));
	}
}

function loadTweetIDs()
{
	global $TweetIDs;
	
	if(file_exists(constant("LIST_TWEET_IDS").".json"))
	{
		$TweetIDs_temp=json_decode(file_get_contents(constant("LIST_TWEET_IDS").".json", true));
		
		foreach($TweetIDs_temp as $id)
			$TweetIDs[$id] = 1;
	}
}

function storeTweetIDs()
{
	global $TweetIDs;
	
	$TweetIDs_temp=array_keys($TweetIDs);
	
	file_put_contents(constant("LIST_TWEET_IDS").".json", json_encode($TweetIDs_temp));
}

function loadKeywords()
{
	global $KeyWords;
	
	$KeyWords=(array) json_decode(file_get_contents(constant("LIST_KEYWORDS").".json", true));
}
?>
