<?php 

require_once("../config.php");

set_time_limit(280);

$ind = 0;
do {
    $drunkTwo = array();
    $sober = array();

    $tweets = query("SELECT id, textoOriginal FROM chat_tweets WHERE contabilizar = 1 AND textEmbedding IS NULL LIMIT 100");
    $num = getNumRows($tweets);
    foreach (getRows($tweets) as $key => $value) {
        $textoRetornado = removerExpressao($value["textoOriginal"]);
        update("chat_tweets", $value["id"], array("textEmbedding" => $textoRetornado));
    }
    $ind++;
    sleep(2);
} while ($num > 0);

function removerExpressao($text) {
    $expressoes = getPatterns();
    foreach ($expressoes as $pattern) {
        $text = preg_replace($pattern, "", $text);
    }
    return $text;
}

function getPatterns() {
    return array("/\bdrunk\b/i", "/\b#drunk\b/i", "/\balcohol\b/i", "/\b#alcohol\b/i", "/\bbeer\b/i", "/\b#beer\b/i", "/\bliquor\b/i", "/\b#liquor\b/i", "/\bvodka\b/i", "/\b#vodka\b/i", "/\bhangover\b/i", "/\b#hangover\b/i", "/\bbeers\b/i", "/\bliquors\b/i");
}