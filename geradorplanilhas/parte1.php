<?php

require_once("../config.php");

$tweets = query("SELECT id, link, a1, a2, a3
                    FROM semantic_tweets_alcolic
                    WHERE situacao = 1
                    AND classificado = 1
                    AND a1 IS NOT NULL
                    AND a2 IS NOT NULL
                    AND a3 IS NULL
                    ");

$file = fopen("../planilhas/envio/parte2/faltando1.csv","w");

foreach (getRows($tweets) as $tweet) {
    fputcsv($file, [$tweet["link"]]);
}
?>