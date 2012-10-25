<?php
/* FUNCTIONS */
/*		countCharFreq($dir, $file_name)
 * This function counts the frequency of each character 
 * specified in the character set in the tweet collection 
 * given by $file_name in $dir directory. */

 
/* CONSTANTS */
/* The file name of the file containing the list 
 * of characters to be counted.. */
define("FILE_CHARACTER_SET", "CharacterSet");

/* GLOBAL VARIABLES */
/* Global arrays storing characters and their frequencies */
$Characters=array();


/* FUNCTION LISTING */
function countCharFreq($dir, $file_name)
{
	global $Characters;	
	
	loadCharacterSet();

	$fp=fopen($dir.$file_name.".csv", "r");
	
	while(!feof($fp))
	{		
		$new_tweet=fgetcsv($fp, 256);
		
		accumulateChars($new_tweet[1]);
	}

	fclose($fp);
	
	file_put_contents("cf.".$file_name.".json", json_encode($Characters));
}

function accumulateChars($tweet)
{
	global $Characters;
	
	$tweet_chars=str_split($tweet);
	
	foreach($tweet_chars as $char)
	{
		
		if(isset($Characters[$char]))
			$Characters[$char]++;
	}
}

function loadCharacterSet()
{
	global $Characters;
	
	$char_set=(array) json_decode(file_get_contents(constant("FILE_CHARACTER_SET").".json", true));
	
	foreach($char_set as $char)
		$Characters[$char]=0;
}
?>
