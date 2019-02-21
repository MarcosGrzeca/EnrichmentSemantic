<?php

require_once("config.php");

echo "<pre>";
$files = array("planilhas/resultados/f1279922.csv");

$contador = 0;
$uteis = 0;
foreach ($files as $file) {
    $avaliacoes = [];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $header = fgetcsv($handle, 2000, ",");
        while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
            $q1 = $data[14];
            $q2 = $data[15];
            $q3 = $data[16];
            $link = $data[21];

            if (empty($q2)) {
                continue;
            }

            if (!in_array($data[10], array("CAN", "USA", "IND"))) {
                continue;
            }

            if (!isset($avaliacoes[$link])) {
                $avaliacoes[$link] = array("q1" => [], "q2" => [], "q3" => []);
            } else {
                continue;
            }
            $avaliacoes[$link]["q1"][] = $q1;
            $avaliacoes[$link]["q2"][] = $q2;
            $avaliacoes[$link]["q3"][] = $q3;

            $tweets = query("SELECT id, classificado FROM semantic_tweets_alcolic WHERE situacao = 1 AND classificado = 0 AND link = '" . $link . "';");
            // foreach (getRows($tweets) as $tweet) {

            // }

            if (getNumRows($tweetSearch) > 0) {
                $uteis++;
            }
            $contador++;

            if ($contador > 100) {
                break;
            }
        }
    }
}

debug($uteis);

echo "</pre>";
?>