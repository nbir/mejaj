#!/usr/bin/env php
<?php
$q=array("Burger King", "Google", "Nikon", "Transformers", "Kung Fu Panda", "Justin Bieiber", "Megan Fox", "Osama bin Laden", "Barack Obama", "Lokpal", "Lebanon", "Hawaii", "Honeymoon", "Blackjack", "Beijing Olympics");

generateKeywords();

function generateKeywords()
{
	global $q;
	
	foreach($q as $word)
		$keywords[$word]=$word;

	file_put_contents("ListKeyword.json", json_encode($keywords));
}
?>
