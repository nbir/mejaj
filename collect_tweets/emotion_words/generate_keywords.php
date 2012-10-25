#!/usr/bin/env php
<?php
$negative=array(
"annoyed", "depressed", "displeased", "embarrassed", "gloomy", "hurt", "lonely", "mad", "shocked", "unhappy", "furious", "ashamed", "defeated", "awful", "disappointed", "discouraged", "greedy", "guilty", "miserable", "upset");
$positive=array(
"amused", "cheerful", "delighted", "elated", "excited", "festive", "hilarious", "joyful", "pleased", "overjoyed", "attracted", "thrilled", "pleasure", "passion", "amazed", "funny", "wonderful", "lively", "pleasant", "pleasant", "loving", "amazement",
);

generateKeywords();

function generateKeywords()
{
	global $positive, $negative;
	

	foreach($negative as $word)
		$keywords[$word]="negative";

	foreach($positive as $word)
		$keywords[$word]="positive";

	file_put_contents("ListKeyword.json", json_encode($keywords));
}
?>
