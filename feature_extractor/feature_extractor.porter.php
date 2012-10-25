<?php
require_once("porter_stemming.php");


function featureExtractor($tweet)
{	
	$tweet=removeUrlAndUserNames($tweet);	
	$tweet=replaceEmoticons($tweet);	
	$tweet=changeCase($tweet);
	$tweet=separateWords($tweet);
	$tweet=stripSpecialChar($tweet);
	$tweet=removeEmptyWords($tweet);
	$tweet=removeStopWords($tweet);
	$tweet=replaceMultiLetterSpelling($tweet);
	$tweet=applyPorterStemming($tweet);
	
	return $tweet;
}


define("FILE_EMOTICON", "../feature_extractor/emoticon.json");
define("FILE_STOPWORDS", "../feature_extractor/stopwords.default.json");

function removeUrlAndUserNames($tweet)
{
	//return preg_replace("/(@\w+)/", "", $tweet);
	//return preg_replace("/(http|https|ftp)\:\/\/[a-zA-Z0-9\.\-]+(\/[a-zA-Z0-9\.\-]+)*/", "", $tweet);
	
	//check the URL part
	return preg_replace("/(@\w+)|((http|https|ftp)\:\/\/[a-zA-Z0-9\.\-]+(\/[a-zA-Z0-9\.\-]+)*)/", "", $tweet);
}

function changeCase($tweet)
{
	return strtolower($tweet);
}

function replaceEmoticons($tweet)
{
	$emoticons=json_decode(file_get_contents(constant("FILE_EMOTICON")), true);
	
	foreach($emoticons as $smiley=>$word)
		$tweet=str_replace($smiley, $word, $tweet);

	return $tweet;
}

function separateWords($tweet)
{
	return preg_split("/[.,\s]+/", $tweet, null, PREG_SPLIT_NO_EMPTY);
}

function stripSpecialChar($tweet)
{
	foreach($tweet as $index=>$word)
		$tweet[$index]=preg_replace("/[^a-zA-Z\s]/", "", $word);
	
	return $tweet;
}

function removeEmptyWords($tweet)
{
	foreach($tweet as $tweet_key=>$tweet_word)
	{
		if(strlen($tweet_word)==0)
			unset($tweet[$tweet_key]);
	}
	
	return array_values($tweet);
}

function removeStopWords($tweet)
{
	$stopwords=json_decode(file_get_contents(constant("FILE_STOPWORDS")), true);
	
	foreach($stopwords as $word)
	{
		foreach($tweet as $tweet_key=>$tweet_word)
		{
			if($tweet_word == $word)
				unset($tweet[$tweet_key]);
		}
	}
	
	return array_values($tweet);
}

function replaceMultiLetterSpelling($tweet)
{
	$j=count($tweet);
	for($k=0;$k<$j;$k++)
	{
		$tweet_word=$tweet[$k];
		$misspelt=false;
		$k1=$k2=-1;
		$new2=$new1=array();

		for($i=0;$i<strlen($tweet_word);$i++)
		{
			$current_letter=$tweet_word[$i];
			
			if((isset($tweet_word[$i+1]) && $tweet_word[$i+1]==$current_letter) && (isset($tweet_word[$i+2]) && $tweet_word[$i+2]==$current_letter))
			{
				$misspelt=true;
				
				$new1[++$k1]=$current_letter;
				$new1[++$k1]=$current_letter;
				$new2[++$k2]=$current_letter;
				
				for(;isset($tweet_word[$i]) && $tweet_word[$i]==$current_letter;$i++);
				
				for(;$i<strlen($tweet_word);$i++)
				{
					$new1[++$k1]=$tweet_word[$i];
					$new2[++$k2]=$tweet_word[$i];
				}
				break;
			}
			else
			{
				$new1[++$k1]=$tweet_word[$i];
				$new2[++$k2]=$tweet_word[$i];
			}
		}
		
		if($misspelt==true)
		{
			$tweet[$k--]=implode($new1);
			$tweet[$j++]=implode($new2);
		}
	}
	
	return $tweet;
}

function applyPorterStemming($tweet)
{
	foreach($tweet as $index=>$word)
		$tweet[$index] = PorterStemmer::Stem($word);

	return $tweet;
}
?>
