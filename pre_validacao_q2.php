<?php

require_once("config.php");


$paises = array();

$avaliacoes = [];

if (($handle = fopen("planilhas/pred_union.csv", "r")) !== FALSE) {
	$cont = 1;

	$header = fgetcsv($handle, 2000, ",");

	$contador = 0;
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
    	$tweetSearch = query("SELECT * FROM semantic_tweets WHERE id = '" . escape($data[0]) . "'");
		if (getNumRows($tweetSearch) == 1) {
			$aTweet = getRows($tweetSearch)[0];
			update("semantic_tweets", $aTweet["id"], array("pre_validacao_q2" => $data[1]));
		} else {
			$idTmp = substr($data[0], 0, 15);
			$tweetSearch = query("SELECT * FROM semantic_tweets WHERE id LIKE '" . escape($idTmp) . "%'");
			if (getNumRows($tweetSearch) == 1) {
				$aTweet = getRows($tweetSearch)[0];
				update("semantic_tweets", $aTweet["id"], array("pre_validacao_q2" => $data[1]));			
			} else {
				debug($data[0]);
				if (in_array($keyAvaliacao, array("9752621192926453", "https://twitter.com/mattman1624/status/9752621192926453"))) {
					//continue;
				}
				throw new Exception("Tweet não encontrado " . escape($data[0]), 1);
			}
		}
    }
}
?>