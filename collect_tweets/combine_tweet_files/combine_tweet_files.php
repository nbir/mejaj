<?php
/* FUNCTIONS */
/*		combineTweetFiles()
 * This function accumulates tweets from the various files 
 * listed in the file list. It requires the setTweetFileName() 
 * function to be executed first. */
 
/*		setTweetFileName($name)
 * This function sets the name for the Tweet filename. If the 
 * parameter $name is not specified, it automatically 
 * sets the current date and time as filename. */
 
 
/* CONSTANTS */
/* Filename for the file (JSON)) that stores the 
 * names of tweet files (CSV) collected so far. */
//define("LIST_TWEET_FILENAMES", "ListTweetFile");

/* The Directory which contains the tweet files (CSV)
 * to form a dataset of. */
define("DIR_TWEET_FILES", "DirTweetFiles");


/* GLOBAL VARIABLES */
/* Global array storing filenames */
$FileNames=array();

/* FUNCTION LISTING */
function combineTweetFiles()
{
	global $FileNames;

	loadFileNames();
	
	foreach($FileNames as $file_name)
		collectTweetsFrom($file_name);
}

function setTweetFileName($name=null)
{
	global $tweet_file_name;
	
	if($name)
		$tweet_file_name=$name;
	else
		$tweet_file_name="tf.".date("Y-m-d_H-i-s");
}

function collectTweetsFrom($file_name)
{
	global $tweet_file_name;
	
	if(file_exists(constant("DIR_TWEET_FILES")."/".$file_name.".csv"))
	{
		$fp=fopen(constant("DIR_TWEET_FILES")."/".$file_name.".csv", "r");
		$fp_tf=fopen($tweet_file_name.".csv", "a");

		while(!feof($fp))
		{
			$new_tweet=fgetcsv($fp, 256);
			
			if($new_tweet)
				fputcsv($fp_tf, $new_tweet);
		}

		fclose($fp);
		fclose($fp_tf);
	}
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
?>
