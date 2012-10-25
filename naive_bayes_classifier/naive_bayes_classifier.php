<?php
/* FUNCTIONS */
/* 		countWordFreq($dataset_file, $wordbank_file)
 * This function analyzes the $dataset_file file and 
 * stores the word frequencies in the $wordbank_file 
 * file. The word bank may already exist.
 * It Requires the readWordBankFromJSON() function 
 * to be called previously. */

/* 		readWordBankFromJSON()
 * This function loads the WordBank file specified
 * in the FILE_WORDBANK constant. */


/* GLOBAL VARIABLES */
/* This array associates the various keywords in the 
 * dataset to their respective catagory name. */
$moodArray=array("positive"=>0, "negative"=>0);

/* CONSTANTS */
/* The file (JSON) which stores the word bank. */
//define("FILE_WORDBANK", "../_wb.processed/"."w.hp+sd+an.p.cpd.0.125.0.00001");
define("FILE_WORDBANK", "../_wb.processed/"."emoWrd2/". "w.emoWrd.p.cpd.0.25.0.0001");

/* EXTERNAL REQUIREMENTS */
/* Requires the function featureExtractor($tweet) which 
 * returns the array of features/attributes in the tweet. */
//require_once("../feature_extractor/feature_extractor.php");
require_once("../feature_extractor/feature_extractor.porter.php");

/* FUNCTION LISTING */
function classify($tweet)
{
	//readWordBankFromJSON();
	
	$tweet_words=featureExtractor($tweet);
	
	$tweetWord_freq=getTweetWordFreq($tweet_words);
	
	$result["positive"]=1;
	$result["negative"]=1;
	
	foreach($tweetWord_freq as $word=>$word_freq)
	{
		//if($word_freq["positive"]!=0)
			$result["positive"]*=($word_freq["positive"]/$word_freq["total"]);
		
		//if($word_freq["negative"]!=0)
			$result["negative"]*=($word_freq["negative"]/$word_freq["total"]);
		
		//$result["positive"]*=10;
		//$result["negative"]*=10;
		
		//print($result["positive"]." : ".$result["negative"].", ");
	}
	//print("\n");
	return $result;
}

function getTweetWordFreq($tweet_words)
{
	global $wordBank;
	$result=array();
	
	foreach($tweet_words as $word)
	{
		$word_freq=getWordFreq($word);
		if($word_freq["total"]!=0)
			$result[$word]=$word_freq;
	}
	
	return $result;
}

function getWordFreq($word)
{
	global $wordBank;
	
	$word_freq["positive"]=0;
	$word_freq["negative"]=0;
	$word_freq["total"]=0;
	
	if(isset($wordBank[$word]))
	{
		$word_freq["positive"]+=$wordBank[$word]["positive"];
		$word_freq["negative"]+=$wordBank[$word]["negative"];
		$word_freq["total"]+=($word_freq["positive"]+$word_freq["negative"]);
	}
	
	return $word_freq;
}

function readWordBankFromJSON()
{
	global $wordBank;
	
	$wordBank=(array) json_decode(file_get_contents(constant("FILE_WORDBANK").".json", true));
	
	foreach($wordBank as $word_key=>$word_value)
		$wordBank[$word_key]=(array) $wordBank[$word_key];
	
	foreach($wordBank as $word_key=>$word_value)
	{
		if($word_value["positive"]==0 || $word_value["negative"]==0)
			print($word_key." = ".$word_value["positive"]." / ".$word_value["negative"]."\n");
	}
}
?>
