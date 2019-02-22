<?php

require_once("config.php");

$totalNulos = 0;
$tweets = query("SELECT id, a1, a2, a3, classificado FROM semantic_tweets_alcolic WHERE situacao = 1 AND classificado = 1 AND q2 IS NULL");
foreach (getRows($tweets) as $tweet) {
    if (is_null($tweet["a1"]) || is_null($tweet["a2"]) || is_null($tweet["a3"])) {
        $totalNulos++;
        continue;
    }
    $classificacaoFinal = null;
    if ($tweet["a1"] == $tweet["a2"]) {
        $classificacaoFinal = $tweet["a1"];
    } else if ($tweet["a1"] == $tweet["a3"]) {
        $classificacaoFinal = $tweet["a1"];
    } else if ($tweet["a2"] == $tweet["a3"]) {
        $classificacaoFinal = $tweet["a2"];
    }
    if (!is_null($classificacaoFinal)) {
        $aUpdate["q2"] = $classificacaoFinal;
        update("semantic_tweets_alcolic", $tweet["id"], $aUpdate);
    }
}
print_r($totalNulos);
?>