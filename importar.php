<?php 

require_once("config.php");

if (($handle = fopen("planilhas/output_got.csv", "r")) !== FALSE) {
	$cont = 0;

    while (($data = fgetcsv($handle, 2000, ";")) !== FALSE) {
    	if ($cont != 0) {
	    	insert("semantic_tweets", array("id", "tweet", "link"), array($data[8], $data[4], $data[9]));
	    }
    	$cont++;
    	/*if ($cont > 50) {
    		break;
    	}*/
    }
}


?>