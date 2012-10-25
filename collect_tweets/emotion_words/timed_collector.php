#!/usr/bin/env php
<?php
$keywords=array(
"annoyed", "depressed", "displeased", "embarrassed", "gloomy", "hurt", "lonely", "mad", "shocked", "unhappy", "furious", "ashamed", "defeated", "awful", "disappointed", "discouraged", "greedy", "guilty", "miserable", "upset",
"amused", "cheerful", "delighted", "elated", "excited", "festive", "hilarious", "joyful", "pleased", "overjoyed", "attracted", "thrilled", "pleasure", "passion", "amazed", "funny", "wonderful", "lively", "pleasant", "pleasant", "loving"
);

require("tweet_collector.php");
timedCollector();

function timedCollector()
{
	global $keywords;
	
	for(;;sleep(1800)) //30 minutes
	{
		print("Collecting... ");
		
		//Keyword search
		foreach($keywords as $query)
			collectTweets($query, 1, 1);
		
		print("\nLast collected at ".date("Y-m-d H:i:s")."\n\n");
	}
}

function collectTweets($query, $from_pg, $to_pg)
{
	writeToCSV(tweetCollector($query, $from_pg, $to_pg), $query);
}
?>
