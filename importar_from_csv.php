<?php 

require_once("config.php");

if (($handle = fopen("planilhas/amazonpartecompleto.csv", "r")) !== FALSE) {
	$cont = 1;

    while (($data = fgetcsv($handle, 2000, ";")) !== FALSE) {
        if ($cont != 0) {
            if ($data[8] > 0) {
	    	  insert("semantic_tweets", array("id", "tweet", "link"), array($data[8], $data[4], $data[9]));
            } else {
                var_dump($data);
                //insert("semantic_tweets", array("id", "tweet", "link"), array($data[9], $data[4] . ";" . $data[5], $data[10]));
            }
	    }
    	$cont++;
    	/*if ($cont > 50) {
    		break;
    	}*/
    }
}


?>