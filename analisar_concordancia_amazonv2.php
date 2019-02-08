<?php

require_once("config.php");

$avaliacoes = [];

// if (($handle = fopen("planilhas/Batch_3515675_batch_results.csv", "r")) !== FALSE) {
// if (($handle = fopen("planilhas/Batch_3521895_batch_results.csv", "r")) !== FALSE) {
// if (($handle = fopen("planilhas/Batch_3521951_batch_results.csv", "r")) !== FALSE) {
// if (($handle = fopen("planilhas/Batch_3521993_batch_results.csv", "r")) !== FALSE) {
// if (($handle = fopen("planilhas/Batch_3523241_batch_results.csv", "r")) !== FALSE) {
if (($handle = fopen("planilhas/Batch_3524454_batch_results.csv", "r")) !== FALSE) {
	$cont = 1;

	$header = fgetcsv($handle, 2000, ",");
	$contador = 0;
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
    	// if (!isset($avaliacoes[$data[27]])) {

    	// }

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
    	// continue;
    }
}

// $fp = fopen('kappa_amazon.csv', 'w');
foreach ($avaliacoes as $key => $linha) {
	$tweets = query("SELECT id, classificado FROM semantic_tweets_alcolic WHERE situacao = 1 AND link = '" . $key . "';");
	foreach (getRows($tweets) as $tweet) {
        update("semantic_tweets_alcolic", $tweet["id"], array("classificado" => 1, "classificador" => "Amazon", "a1" => $linha[0], "a2" => $linha[1], "a3" => $linha[2]));
	}
    // fputcsv($fp, $linha);
}
// fclose($fp);
?>