#!/usr/bin/env php
<?php

require_once("naive_bayes_classifier.php");
readWordBankFromJSON();

//findEffeciency("testdata");
findAccuracy("mejaj.testset");
//sleep(18000);

function findAccuracy($file_name)
{
	$correct=0;
	$incorrect=0;
	
	$fp=fopen($file_name.".csv", "r");
	
	while(!feof($fp))
	{
		$new_tweet=fgetcsv($fp);
		
		if($new_tweet)
		{
			//print(findCatagory($new_tweet[1])."\n");
			
			if(findCatagory($new_tweet[1]) == $new_tweet[0])
				$correct++;
			else
				$incorrect++;
			
		}
	}

	fclose($fp);
	
	print("Correct:\t".$correct."\n");
	print("Incorrect:\t".$incorrect."\n");
	print("Accuracy:\t".($correct/($correct+$incorrect))."\n");
}

function findCatagory($tweet)
{
	$result=classify($tweet);
	//print_r($result);
	
	if($result["positive"]>$result["negative"])
		return "positive";
	else
		return "negative";
}
?>
