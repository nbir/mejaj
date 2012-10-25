<?php
/* FUNCTIONS */

/* CONSTANTS */
define("MAX_ALLOWED_PERCENTAGE", 0.00001);

/* FUNCTION LISTING */
function featureSelector($wordbank_file)
{
	global $wordBank, $positive, $negative, $total;
	global $max_positive, $max_negative, $max_total;
	
	readWordBankFromJSON($wordbank_file);
	
	sortPositiveNegative();
	$max_positive=max($positive);
	$max_negative=max($negative);
	$max_total=max($total);
	
	print("\n".$max_positive."\t".$max_negative."\t".$max_total."\n");
	print(count($wordBank)." => ");
	
	removeLowFreqWords();
	normalizePositiveLowFreqWords();
	normalizeNegativeLowFreqWords();
	
	writeWordBankToJSON($wordbank_file);
	
	print(count($wordBank)."\n");
}

function sortPositiveNegative()
{
	global $wordBank, $positive, $negative, $total;
	
	foreach($wordBank as $word=>$word_freq)
	{
		$positive[$word]=$word_freq["positive"];
		$negative[$word]=$word_freq["negative"];
		$total[$word]=$word_freq["positive"]+$word_freq["negative"];
	}
	
	//asort($positive);
	//asort($negative);
	//asort($total);
}

function removeLowFreqWords()
{
	global $wordBank, $max_total;
	
	$min_count_allowed = floor(constant("MAX_ALLOWED_PERCENTAGE")*$max_total);
	
	foreach($wordBank as $word=>$word_freq)
	{
		if(($word_freq["positive"] + $word_freq["negative"]) < $min_count_allowed)
		{
			unset($wordBank[$word]);
			//print(".");
		}
	}
}

// #1
///*
function normalizePositiveLowFreqWords()
{
	global $wordBank, $max_positive;
	
	$min_count_allowed = floor(constant("MAX_ALLOWED_PERCENTAGE")*$max_positive);
	
	foreach($wordBank as $word=>$word_freq)
	{
		if($word_freq["positive"] < $min_count_allowed)
		{
			if($word_freq["positive"]==0)
				$word_freq["positive"]=1;
			
			$factor=$min_count_allowed/$word_freq["positive"];
			$wordBank[$word]["positive"]=$min_count_allowed;
			$wordBank[$word]["negative"]=floor($factor*$word_freq["negative"]);
			//print(".");
		}
	}
}

function normalizeNegativeLowFreqWords()
{
	global $wordBank, $max_negative;
	
	$min_count_allowed = floor(constant("MAX_ALLOWED_PERCENTAGE")*$max_negative);
	
	foreach($wordBank as $word=>$word_freq)
	{
		if($word_freq["negative"] < $min_count_allowed)
		{
			if($word_freq["negative"]==0)
				$word_freq["negative"]=1;
			
			$factor=$min_count_allowed/$word_freq["negative"];
			$wordBank[$word]["negative"]=$min_count_allowed;
			$wordBank[$word]["positive"]=floor($factor*$word_freq["positive"]);
			//print(".");
		}
	}
}
//*/

// #2
/*
function normalizePositiveLowFreqWords()
{
	global $wordBank, $max_positive;
	
	$min_count_allowed = floor(constant("MAX_ALLOWED_PERCENTAGE")*$max_positive);
	
	foreach($wordBank as $word=>$word_freq)
	{
		if($word_freq["positive"] < $min_count_allowed)
		{
			$wordBank[$word]["negative"]+=$wordBank[$word]["positive"];
			$wordBank[$word]["positive"]=0;
			//print(".");
		}
	}
}

function normalizeNegativeLowFreqWords()
{
	global $wordBank, $max_negative;
	
	$min_count_allowed = floor(constant("MAX_ALLOWED_PERCENTAGE")*$max_negative);
	
	foreach($wordBank as $word=>$word_freq)
	{
		if($word_freq["negative"] < $min_count_allowed)
		{
			$wordBank[$word]["positive"]+=$wordBank[$word]["negative"];
			$wordBank[$word]["negative"]=0;
			//print(".");
		}
	}
}
*/

function readWordBankFromJSON($file)
{
	global $wordBank;
	
	$wordBank=(array) json_decode(file_get_contents($file.".json", true));
	
	foreach($wordBank as $word_key=>$word_value)
		$wordBank[$word_key]=(array) $wordBank[$word_key];
}

function writeWordBankToJSON($file)
{
	global $wordBank;
	
	file_put_contents($file.".".((string) constant("MAX_ALLOWED_PERCENTAGE")).".json", json_encode($wordBank));
}
?>
