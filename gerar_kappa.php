<?php

require_once("config.php");

$fp = fopen('kappa_amazon.csv', 'w');
$tweets = query("SELECT a1, a2, a3 FROM semantic_tweets_alcolic WHERE classificado = 1 AND a1 IS NOT NULL AND a2 IS NOT NULL AND a3 IS NOT NULL");

foreach (getRows($tweets) as $tweet) {
	fputcsv($fp, [$tweet["a1"], $tweet["a2"], $tweet["a3"]]);
}
fclose($fp);