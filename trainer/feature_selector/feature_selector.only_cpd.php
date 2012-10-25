<?php
/* FUNCTIONS */

/* CONSTANTS */
define("CPD_THRESHOLD", 0);
define("MAX_ALLOWED_PERCENTAGE", 0.0001);

/* FUNCTION LISTING */
function featureSelector($wordbank_file)
{
	global $wordBank, $pd, $total, $positive, $negative;
	global $max_pd, $min_pd, $max_total, $max_positive, $max_negative;
	
	readWordBankFromJSON($wordbank_file);
	
	findProportionalDiff();
	$max_pd=max($pd);
	$min_pd=min($pd);
	$max_total=max($total);
	
	$max_positive=max($positive);
	$max_negative=max($negative);
	
	print($max_pd." / ".$min_pd."\t".$max_total."\n");
	print(count($wordBank)." => ");
	
	//removeLowFreqWords();
	removeLowCPDWords();
	normalizePositiveLowFreqWords();
	normalizeNegativeLowFreqWords();
	
	writeWordBankToJSON($wordbank_file);
	
	print(count($wordBank)."\n");
}

function findProportionalDiff()
{
	global $wordBank, $pd, $total, $positive, $negative;
	
	foreach($wordBank as $word=>$word_freq)
	{
		$pd[$word]=abs($word_freq["positive"]-$word_freq["negative"])/($word_freq["positive"]+$word_freq["negative"]);
		$total[$word]=$word_freq["positive"]+$word_freq["negative"];
		
		$positive[$word]=$word_freq["positive"];
		$negative[$word]=$word_freq["negative"];
	}
	
	//asort($pd);
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

function removeLowCPDWords()
{
	global $wordBank, $pd;
	
	foreach($pd as $word=>$pd_value)
	{
		if($pd_value<constant("CPD_THRESHOLD"))
			unset($wordBank[$word]);
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
	
	file_put_contents($file.".cpd.".constant("CPD_THRESHOLD").".json", json_encode($wordBank));
}
?>
