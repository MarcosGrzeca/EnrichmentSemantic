<?php

require_once("../config.php");

$sql = "SELECT textEmbedding as texto
		FROM tweets t
		WHERE textparser <> ''
		UNION ALL
		SELECT textOriginal as texto
		FROM semantic_tweets_alcolic t
		UNION ALL
		SELECT textoOriginal as texto
		FROM chat_tweets t";

		
$myfile = fopen("embedao.txt", "w") or die("Unable to open file!");

$tweets = query($sql);

echo getNumRows($tweets);

foreach (getRows($tweets) as $tweet) {
	// $tweet["textEmbedding"] = preg_replace('/[\n|\r|\n\r|\r\n]{2,}/',' ', $tweet["textEmbedding"]);
	// preg_replace( "/\r|\n/", "", $tweet["textEmbedding"]);
	$string = trim(preg_replace('/\s+/', ' ', $tweet["texto"]));
	fwrite($myfile, $string . "\n");
}

fclose($myfile);