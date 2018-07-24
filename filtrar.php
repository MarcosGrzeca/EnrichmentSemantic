<?php

require_once("config.php");


$paises = array();

$avaliacoes = [];

$fp = fopen('planilhas/nao_classificados_filtro_v2.csv', 'w');

$jaClassificados = [];

if (($handle = fopen("planilhas/resultados/f1279922.csv", "r")) !== FALSE) {
	$header = fgetcsv($handle, 2000, ",");

    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
    	if (!in_array($data[21], $jaClassificados)) {
			$jaClassificados[] = $data[21];
		}
    }
}

if (($handle = fopen("planilhas/nao_classificados_filtro.csv", "r")) !== FALSE) {
	$cont = 1;

	$header = fgetcsv($handle, 2000, ",");

	$contador = 0;
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
    	if (in_array($data[0], $jaClassificados)) {
    		continue;
    	}
    	$tweetSearch = query("SELECT * FROM semantic_tweets WHERE link = '" . escape($data[0]) . "'");
		if (getNumRows($tweetSearch) == 1) {
			$aTweet = getRows($tweetSearch)[0];
			if ($aTweet["pre_validacao_q2_v2"] == 1) {
				fputcsv($fp, $data, ";");
			}
		} else {
			throw new Exception("Tweet não encontrado " . escape($data[0]), 1);
		}
    }
}
fclose($fp);
?>