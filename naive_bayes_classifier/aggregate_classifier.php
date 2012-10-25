<?php
/* FUNCTIONS */

/* GLOBAL VARIABLES */

/* CONSTANTS */

/* EXTERNAL REQUIREMENTS */
require_once("tweet_collector.php");
require_once("naive_bayes_classifier.php");
readWordBankFromJSON();


/* FUNCTION LISTING */
function findAggregateResults($query, $from_pg, $to_pg)
{
	global $positive, $negative;
	
	$tweets=tweetCollector($query, $from_pg, $to_pg);
	
	foreach($tweets as $tweet_id=>$tweet_details)
	{
		findCatagory($tweet_id, $tweet_details["text"]);
	}
	
	arsort($positive);
	asort($negative);
	
	return $tweets;
}

function findCatagory($tweet_id, $tweet)
{
	global $positive, $negative;
	
	$result=classify($tweet);
	//print_r($result);
	
	//if($result["negative"]==0)
		//print($result["positive"]." / ".$result["negative"]);
	
	$score=$result["positive"]/$result["negative"];
	
	if($score>1)
		$positive[$tweet_id]=$score;
	else
		$negative[$tweet_id]=$score;
}
?>
