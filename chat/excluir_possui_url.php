<?php 

require_once("../config.php");

set_time_limit(240);

do {
    $tweets = query("SELECT id, textoOriginal FROM chat_tweets WHERE drunk = 'A' AND possuiURL = -1 LIMIT 200");
    
    $sim = array();
    $nao = array();
    $ind = 0;
    $rows = getNumRows($tweets);
    foreach (getRows($tweets) as $key => $value) {
        try {
            if (preg_match('~(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)~', $value["textoOriginal"])) {
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
       query("DELETE FROM chat_tweets WHERE id IN (" . implode(",", $sim) . ")");
    }
    if (count($nao)) {
        query("UPDATE chat_tweets SET possuiURL = 0 WHERE id IN (" . implode(",", $nao) . ")");
    }
    sleep(10);
} while ($rows > 0);
?>