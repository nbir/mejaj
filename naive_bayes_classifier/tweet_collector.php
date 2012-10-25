<?php
/* FUNCTIONS */
/* 		tweetCollector($query)
 * Returns a mixed array of tweets collected from
 * the Twitter Search API, matching the query */
 
/*	 	writeToCSV($tweets, $keyword) 
 * Writes the tweets to a file (CSV) and adds 
 * the filename to the filename list */


/* CONSTANTS */
/* URL for twitter search API */
define("URL_TWITTER_SEARCH", "http://search.twitter.com/search.json");
/* Add any other search parameters to the search query
 * to retrive tweets using the Twitter Search API */
define("OTHER_SEARCH_PARAMETERS", "&lang=en&rpp=100");

/* Filename for the file (JSON)) that stores the 
 * names of tweet files (CSV) collected so far. */
define("LIST_TWEET_FILE_LIST", "TweetFileList");


/* FUNCTION LISTING */
function tweetCollector($query, $from_pg, $to_pg)
{
	$tweets=searchResult($query, $from_pg, $to_pg);
	$tweets=filterReTweets($tweets);
	$tweets=keepRequiredFields($tweets);
	$tweets=removeUnwantedChars($tweets);
	$tweets=convertTime($tweets);
	$tweets=restructureTweetSets($tweets);
	
	return $tweets;
}

function searchResult($query, $from_pg, $to_pg)
{
	$tweets=array();
	
	//print($query." : ");
	
	for(;$to_pg>=$from_pg;$to_pg--)
	{
		/* PROXY SETTINGS */

		$opts = array('http' => array('method' => 'GET', 'proxy' => 'tcp://192.168.56.1:8080', 'request_fulluri' => true));
		$context = stream_context_create($opts);
		
		$new_tweet_set=json_decode(file_get_contents(constant("URL_TWITTER_SEARCH")."?q=".urlencode($query).constant("OTHER_SEARCH_PARAMETERS")."&page=".$to_pg, false, $context), true);
		
		/* PROXY SETTINGS END */
		//$new_tweet_set=json_decode(file_get_contents(constant("URL_TWITTER_SEARCH")."?q=".urlencode($query).constant("OTHER_SEARCH_PARAMETERS")."&page=".$to_pg), true);
		
		//print($to_pg."(".count($new_tweet_set["results"])."), ");

		if($new_tweet_set)
			$tweets=array_merge($tweets, $new_tweet_set["results"]);
	}
	
	return $tweets;
}

function isReTweet($tweet)
{
	if(preg_match("/([.,\s]+RT\W+)|(^RT\W+)/i", $tweet)!=0)
		return true;
	return false;
};

function filterReTweets($tweets)
{
	foreach($tweets as $tweet_key=>$tweet)
	{
		if(isReTweet($tweet["text"]))
			unset($tweets[$tweet_key]);
	}
	
	return $tweets;
}

function removeUnwantedChars($tweets)
{
	foreach($tweets as $tweet_key=>$tweet)
		$tweets[$tweet_key]["text"]=preg_replace('/[^\x00-\x7F]/', "", $tweets[$tweet_key]["text"]);
	
	return $tweets;
}

function keepRequiredFields($tweets)
{
	foreach($tweets as $tweet_key=>$tweet)
	{
		unset($tweets[$tweet_key]["from_user_id_str"]);
		//unset($tweets[$tweet_key]["id_str"]);
		unset($tweets[$tweet_key]["metadata"]);
		unset($tweets[$tweet_key]["to_user_id"]);
		unset($tweets[$tweet_key]["from_user_id"]);
		unset($tweets[$tweet_key]["to_user"]);
		unset($tweets[$tweet_key]["geo"]);
		unset($tweets[$tweet_key]["iso_language_code"]);
		unset($tweets[$tweet_key]["to_user_id_str"]);
		unset($tweets[$tweet_key]["source"]);
	}
	
	return $tweets;
}

function convertTime($tweets)
{
	foreach($tweets as $tweet_key=>$tweet)
		$tweets[$tweet_key]["created_at"]=strtotime($tweets[$tweet_key]["created_at"]);

	return $tweets;
}

function restructureTweetSets($tweets)
{
	foreach($tweets as $key=>$tweet_details)
		$tweets_temp[$tweet_details["id_str"]]=$tweet_details;
	
	return $tweets_temp;
}
?>
