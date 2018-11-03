<?php 
require_once("../config.php");

if (($handle = fopen("planilhas/alc5000.csv", "r")) !== FALSE) {
	$cont = 1;
	$header = fgetcsv($handle, 4000, ",");
	$contador = 0;

	$fields = array("drunk", "id", "retweetid", "link", "textoOriginal", "user_name");

    while (($data = fgetcsv($handle, 4000, ",")) !== FALSE) {
    	try {
    		$tweet = ["drunk" => "S"];
    		$tweet["id"] = $data[7];
    		$tweet["retweetid"] = $data[5];
    		$tweet["link"] = $data[4];
    		$tweet["textoOriginal"] = $data[6];
    		$tweet["user_name"] = $data[1];
    		insert("chat_tweets", $fields, $tweet);	
    	} catch (Exception $e) {
    		debug($e->getMessage());
    	}
    }
 }
?>