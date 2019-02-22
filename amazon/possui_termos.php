<?php 

require_once("../config.php");

set_time_limit(290);

$ind = 0;
do {
    $drunkTwo = array();
    $sober = array();

    $tweets = query("SELECT id, textoOriginal, link FROM chat_tweets WHERE drunk = 'A' LIMIT 100");
    foreach (getRows($tweets) as $key => $value) {
        if (possuiExpressao($value["textoOriginal"])) {
            // debug("Drunk");
            debug($value["textoOriginal"]);
            $drunkTwo[] = $value["id"];
        } else {
            // debug("Sober");
            $sober[] = $value["id"];
        }
    }

    if (count($drunkTwo)) {
        query("UPDATE chat_tweets SET drunk = 'Y' WHERE id IN (" . implode(",", $drunkTwo) . ");");
    }

    if (count($sober)) {
        query("UPDATE chat_tweets SET drunk = 'N' WHERE id IN (" . implode(",", $sober) . ");");
    }
    $ind++;
} while ($ind < 100);

function possuiExpressao($text) {
    $expressoes = getPatterns();
    foreach ($expressoes as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            return true;
        }
    }
    return false;
}

function getPatterns() {
    // return array("/\bdrunk\b/i", "/\b#drunk\b/i", "/\balcohol\b/i", "/\b#alcohol\b/i", "/\bbeer\b/i", "/\b#beer\b/i", "/\bliquor\b/i", "/\b#liquor\b/i", "/\bvodka\b/i", "/\b#vodka\b/i", "/\bhangover\b/i", "/\b#hangover\b/i");
    return array("/\bdrunk\b/i", "/\b#drunk\b/i", "/\balcohol\b/i", "/\b#alcohol\b/i", "/\bbeer\b/i", "/\b#beer\b/i", "/\bliquor\b/i", "/\b#liquor\b/i", "/\bvodka\b/i", "/\b#vodka\b/i", "/\bhangover\b/i", "/\b#hangover\b/i", "/\bbeers\b/i", "/\bliquors\b/i");
}