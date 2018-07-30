<?php 

require_once("config.php");

set_time_limit(300);

do {
    $tweets = query("SELECT id, textOriginal FROM semantic_tweets_alcolic WHERE possuiURL = -1 LIMIT 200");
    
    $sim = array();
    $nao = array();
    $ind = 0;
    $rows = getNumRows($tweets);
    foreach (getRows($tweets) as $key => $value) {
        try {
            if (preg_match('~(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)~', $value["textOriginal"])) {
                $sim[] = $value["id"];
            } else {
                $nao[] = $value["id"];
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
        if ($ind > 10) {
            //break;
        }
        $ind++;
    }

    if (count($sim)) {
        query("UPDATE semantic_tweets_alcolic SET possuiURL = 1 WHERE id IN (" . implode(",", $sim) . ")");
    }
    if (count($nao)) {
        query("UPDATE semantic_tweets_alcolic SET possuiURL = 0 WHERE id IN (" . implode(",", $nao) . ")");
    }

} while ($rows > 0);
?>