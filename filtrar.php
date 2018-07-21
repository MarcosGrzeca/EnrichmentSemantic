<?php

require_once("config.php");


$paises = array();

$avaliacoes = [];

$fp = fopen('planilhas/amazonpartecompleto2_filtrado.csv', 'w');

if (($handle = fopen("planilhas/amazonparte2.csv", "r")) !== FALSE) {
	$cont = 1;

	$header = fgetcsv($handle, 2000, ",");

	$contador = 0;
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
    	$tweetSearch = query("SELECT * FROM semantic_tweets WHERE link = '" . escape($data[0]) . "'");
		if (getNumRows($tweetSearch) == 1) {
			$aTweet = getRows($tweetSearch)[0];
			if ($aTweet["pre_validacao_q2"] == 1) {
				fputcsv($fp, $data, ";");
			}
		} else {
			throw new Exception("Tweet não encontrado " . escape($data[0]), 1);
		}
    }
}
fclose($fp);
?>