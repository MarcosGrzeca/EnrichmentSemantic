<?php

require_once("config.php");


$paises = array();

$avaliacoes = [];

//if (($handle = fopen("planilhas/pred_union.csv", "r")) !== FALSE) {
if (($handle = fopen("planilhas/pred_union_v3.csv", "r")) !== FALSE) {
	$cont = 1;

	$header = fgetcsv($handle, 2000, ",");

	$contador = 0;
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
    	$tweetSearch = query("SELECT * FROM semantic_tweets WHERE id = '" . escape($data[0]) . "'");
		if (getNumRows($tweetSearch) == 1) {
			$aTweet = getRows($tweetSearch)[0];
			update("semantic_tweets", $aTweet["id"], array("pre_validacao_q2_v2" => $data[1]));
		} else {
			$idTmp = substr($data[0], 0, 15);
			$tweetSearch = query("SELECT * FROM semantic_tweets WHERE id LIKE '" . escape($idTmp) . "%'");
			if (getNumRows($tweetSearch) == 1) {
				$aTweet = getRows($tweetSearch)[0];
				update("semantic_tweets", $aTweet["id"], array("pre_validacao_q2_v2" => $data[1]));			
			} else {
				throw new Exception("Tweet não encontrado " . escape($data[0]), 1);
			}
		}
    }
}
?>