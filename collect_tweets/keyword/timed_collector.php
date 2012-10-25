#!/usr/bin/env php
<?php

require("tweet_collector.php");
timedCollector();


function timedCollector()
{
	//for($intervals=0;;sleep(2700), $intervals++) //15 minutes
	for(;;sleep(2700)) //45 minutes
	{
		print("Collecting... ");
		
		//Keyword search
		/*
		collectTweets("happy", 1, 5);
		collectTweets("sad", 1, 5);
		collectTweets("angry", 1, 5);
		collectTweets("afraid", 1, 5);
		*/
		//Hashtag search
		//if($intervals%3==0) //Every 45 minutes
		//{
			collectTweets("#happy", 1, 1);
			collectTweets("#sad", 1, 1);
			collectTweets("#angry", 1, 1);
			collectTweets("#afraid", 1, 1);
		//}
		
		print("\nLast collected at ".date("Y-m-d H:i:s")."\n\n");
	}
}

function collectTweets($query, $from_pg, $to_pg)
{
	writeToCSV(tweetCollector($query, $from_pg, $to_pg), $query);
}
?>
