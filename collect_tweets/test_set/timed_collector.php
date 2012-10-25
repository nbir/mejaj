#!/usr/bin/env php
<?php
//$keywords=array("Burger King", "Google", "Nikon", "Transformers", "Kung Fu Panda", "Justin Bieiber", "Megan Fox", "Osama bin Laden", "Barack Obama", "Lokpal", "Lebanon", "Hawaii", "Honeymoon", "Blackjack", "Beijing Olympics");

$keywords=array("Justin Beiber");

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
