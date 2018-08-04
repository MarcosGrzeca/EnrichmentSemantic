<?php 

require_once("config.php");

set_time_limit(0);

estaAtivo("enriquecer");

$proxId = 0;

do {
	$sql = 	"SELECT id, calaisResponse " .
			"FROM semantic_tweets_alcolic " .
			"WHERE calaisResponse IS NOT NULL " .
			"AND id > " . $proxId . " " .
			"ORDER by id " .
			"LIMIT 100 ";

	debug($sql);
	$tweets = query($sql);
	$rows = getNumRows($tweets);
	
	foreach (getRows($tweets) as $key => $value) {
		$proxId = $value["id"];
	    $language = "";

	    try {
	    	try {
	    		$calais = $value["calaisResponse"];
	    		$calaisJSON = json_decode($calais);
	    	} catch (Exception $e) {
	    		if ($e->getCode() == 99) {
	    			var_dump("EXCEDEU LIMITE Calais");
	    			die;
	    		}
	    	}

			
	    	$fields = array("idTweet", "origem", "tipo", "palavra", "type");
	    	foreach ($calaisJSON as $keyC => $valueC) {
	    		if (isset($valueC->_typeGroup)) {
					$typeGroup = $valueC->_typeGroup;
					if ($valueC->_typeGroup == "entities") {
						insert("semantic_tweets_nlp", $fields, array($value["id"], "C", "E", $valueC->name, $valueC->_type));
					}
	    		}
	    	}
	    } catch (Exception $e) {
	    	var_dump($e->getMessage()); 
	    }
	    if ($ind >= 1) {
	    	//break;
	    }
	    $ind++;
	}
} while ($rows > 0);
?>