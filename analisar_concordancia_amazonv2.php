<?php

require_once("config.php");

// $files = array("planilhas/Batch_3523119_batch_results.csv", "planilhas/Batch_3515675_batch_results.csv", "planilhas/Batch_3521895_batch_results.csv", "planilhas/Batch_3521951_batch_results.csv", "planilhas/Batch_3521993_batch_results.csv", "planilhas/Batch_3523241_batch_results.csv", "planilhas/Batch_3524454_batch_results.csv", "planilhas/Batch_3526694_batch_results.csv", "planilhas/Batch_3526743_batch_results.csv", "planilhas/Batch_3527050_batch_results.csv", "planilhas/Batch_3527093_batch_results.csv", "planilhas/Batch_3527559_batch_results.csv", "planilhas/Batch_3527584_batch_results.csv", "planilhas/Batch_3527886_batch_results.csv", "planilhas/Batch_3531458_batch_results.csv", "planilhas/Batch_3534492_batch_results.csv");
// $files = array("planilhas/Batch_3535981_batch_results.csv"); //Faltando 1
// $files = array("planilhas/Batch_3536013_batch_results.csv", "planilhas/Batch_3536041_batch_results.csv"); //Faltando 2
// $files = array("planilhas/Batch_3537406_batch_results.csv");
$files = array("planilhas/Batch_3538973_batch_results.csv");

foreach ($files as $file) {
    $avaliacoes = [];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $cont = 1;

        $header = fgetcsv($handle, 2000, ",");
        $contador = 0;
        while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
            if ($data[28] === "y") {
                $data[28] = 1;
            }

            if ($data[28] === "s") {
                $data[28] = -1;
            }

            if ($data[28] === "n") {
                $data[28] = 0;
            }
            $avaliacoes[$data[27]][] = $data[28];
        }
    }

    foreach ($avaliacoes as $key => $linha) {
        $tweets = query("SELECT id, classificado FROM semantic_tweets_alcolic WHERE situacao = 1 AND link = '" . $key . "';");
        foreach (getRows($tweets) as $tweet) {
            $aUpdate = array("classificado" => 1, "classificador" => "Amazon");
            if (isset($linha[0])) {
                $aUpdate["a1"] = $linha[0];
            }
            if (isset($linha[1])) {
                $aUpdate["a2"] = $linha[1];
            }
            if (isset($linha[2])) {
                $aUpdate["a3"] = $linha[2];
            }
            // if (isset($linha[0])) {
            //     $aUpdate["a2"] = $linha[0];
            // }
            // if (isset($linha[1])) {
            //     $aUpdate["a3"] = $linha[1];
            // }
            update("semantic_tweets_alcolic", $tweet["id"], $aUpdate);
        }
    }
}
?>