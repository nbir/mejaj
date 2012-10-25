<?php
/* FUNCTIONS */
/* 		countWordFreq($dataset_file, $wordbank_file)
 * This function analyzes the $dataset_file file and 
 * stores the word frequencies in the $wordbank_file 
 * file. The word bank may already exist. */


/* GLOBAL VARIABLES */
/* This array associates the various keywords in the 
 * dataset to their respective catagory name. */
$moodArray=array("positive"=>0, "negative"=>0);

/* EXTERNAL REQUIREMENTS */
/* Requires the function featureExtractor($tweet) which 
 * returns the array of features/ttributes in the tweet. */
//require_once("../feature_extractor/feature_extractor.php");
require_once("../feature_extractor/feature_extractor.porter.php");

/* FUNCTION LISTING */
function countWordFreq($dataset_file, $wordbank_file=null)
{
	if(!$wordbank_file)
		$wordbank_file="w.".$dataset_file;
	
	readWordBankFromJSON($wordbank_file);
	
	$fp=fopen($dataset_file.".csv", "r");
	
	while(!feof($fp))
	{
		$tweet=fgetcsv($fp, 256);
		
		if($tweet)
		{
			$tweet_words=featureExtractor($tweet[1]);
			
			if($tweet_words)
				updatewordBank($tweet_words, $tweet[0]);
		}
	}
	
	writeWordBankToJSON($wordbank_file);
}

function updateWordBank($tweet_words, $mood)
{
	global $wordBank, $moodArray;
	
	foreach($tweet_words as $word)
	{
		if(!isset($wordBank[$word]))
			$wordBank[$word]=$moodArray;
		
		$wordBank[$word][$mood]=$wordBank[$word][$mood]+1;
	}
}

function readWordBankFromJSON($file)
{
	global $wordBank;
	
	if(file_exists($file.".json"))
		$wordBank=(array) json_decode(file_get_contents($file.".json", true));
	else
		$wordBank=array();
		
	foreach($wordBank as $word_key=>$word_value)
		$wordBank[$word_key]=(array) $wordBank[$word_key];
}

function writeWordBankToJSON($file)
{
	global $wordBank;
	
	file_put_contents($file.".json", json_encode($wordBank));
}
?>
