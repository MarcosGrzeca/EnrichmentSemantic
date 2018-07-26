<?php 

require_once("config.php");

estaAtivo("preprocessar");

$lacos = 0;

do {

    $tweets = query("SELECT id, content FROM semantic_tweets_alcolic WHERE situacao = 1 AND textOriginal IS NOT NULL AND preProcessado = 'S' AND language IS NULL AND drunk = 'A' LIMIT 5000");

    $exclude = [];
    $update = [];
    $ind = 0;

    $contador = getNumRows($tweets);
    foreach (getRows($tweets) as $key => $value) {
        #Substituições
        #HashTags
        #Emotions
        #Hora
        #Remover alongamentos
        try {
            $tweet = json_decode($value["content"]);
            if ($tweet->lang == "en") {
                //update("semantic_tweets_alcolic", $value["id"], array("language" => $tweet->lang));
                $update[] = $value["id"];
            } else {
                //query("DELETE FROM semantic_tweets_alcolic WHERE id = " . $value["id"] . " ");
                $exclude[] = $value["id"];
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

        if ($ind > 10) {
            //break;
        }
        $ind++;
    }

    if (count($update)) {
        query("UPDATE semantic_tweets_alcolic SET language = 'en' WHERE id IN (" . implode(",", $update) . ");");
    }

    if (count($exclude)) {
        query("DELETE FROM semantic_tweets_alcolic WHERE id IN (" . implode(",", $exclude) . ");");
    }

    $lacos++;
    if ($lacos > 100) {
        break;
    }
} while ($contador > 0);
?>