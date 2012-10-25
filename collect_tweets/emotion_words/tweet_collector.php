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
	
	return $tweets;
}

function searchResult($query, $from_pg, $to_pg)
{
	$tweets=array();
	
	print($query." : ");
	
	for(;$to_pg>=$from_pg;$to_pg--)
	{
		/* PROXY SETTINGS */

		$auth = base64_encode('nbora11:nibir123');

		$opts = array('http' => array('method' => 'GET', 'proxy' => 'tcp://202.141.80.30:3128', 'request_fulluri' => true, 'header' => "Proxy-Authorization: Basic $auth"));
		$context = stream_context_create($opts);
		
		$new_tweet_set=json_decode(file_get_contents(constant("URL_TWITTER_SEARCH")."?q=".urlencode($query).constant("OTHER_SEARCH_PARAMETERS")."&page=".$to_pg, false, $context), true);
		
		/* PROXY SETTINGS END */
		
		//$new_tweet_set=json_decode(file_get_contents(constant("URL_TWITTER_SEARCH")."?q=".urlencode($query).constant("OTHER_SEARCH_PARAMETERS")."&page=".$to_pg), true);
		
		print($to_pg."(".count($new_tweet_set["results"])."), ");

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

function writeToCSV($tweets, $keyword)
{
	if($tweets)
	{
		$dir_name="Datewise/".date("Y-m-d")."/";
		if(!file_exists($dir_name))
			mkdir($dir_name);
		
		$file_name=$keyword.".".date("Y-m-d_H-i-s");
		$fp=fopen($dir_name.$file_name.".csv", "w");
	
		foreach($tweets as $tweet_key=>$tweet)
		{
			$new_tweet=array();
		
			array_push($new_tweet, $keyword);
			array_push($new_tweet, $tweets[$tweet_key]["text"]);
			array_push($new_tweet, $tweets[$tweet_key]["id_str"]);
		
			fputcsv($fp, $new_tweet);
		}
	
		fclose($fp);
	
		//addFileName($file_name);
	}
}

function addFileName($file_name)
{	
	if(file_exists(constant("LIST_TWEET_FILE_LIST").".json"))
		$fileNames = json_decode(file_get_contents(constant("LIST_TWEET_FILE_LIST").".json", true));
	else
		$fileNames=array();
	
	array_push($fileNames, $file_name);
	
	file_put_contents(constant("LIST_TWEET_FILE_LIST").".json", json_encode($fileNames));
}
?>
