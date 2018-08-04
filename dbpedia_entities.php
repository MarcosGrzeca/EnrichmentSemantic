<?php 

require_once("config.php");

estaAtivo("entidades");

set_time_limit(200);

$tweets = query("SELECT DISTINCT(palavra) as palavra FROM semantic_tweets_nlp WHERE NOT EXISTS (SELECT id FROM semantic_conceito WHERE semantic_conceito.palavra = semantic_tweets_nlp.palavra) AND tipo = 'E' ");

if (isset($_REQUEST["order"]) && $_REQUEST["order"] == "DESC") {
	$sqlIni .= "ORDER by id desc ";
} else {
	$sqlIni .= "ORDER by id ";
}

$sqlIni .= "LIMIT 100";

$ind = 0;
foreach (getRows($tweets) as $key => $value) {
	try {
		$dbpedia = dbpedia($value["palavra"]);
		$dbpediaJSON = json_decode($dbpedia, true);
		if (isset($dbpediaJSON["Resources"])) {
			debug($dbpediaJSON);
			foreach ($dbpediaJSON["Resources"] as $keyR => $valueR) {
				insert("semantic_conceito", array("palavra", "resource", "json", "sucesso", "similarityScore"), array($value["palavra"], $valueR["@URI"], $dbpedia, 1, $valueR["@similarityScore"]));
			}
		} else {
			insert("semantic_conceito", array("palavra", "resource", "json", "sucesso"), array($value["palavra"], NULL, $dbpedia, 0));
		}
    } catch (Exception $e) {
    	var_dump($e->getMessage()); 
    }

    if ($ind >= 1) {
    	//break;
    }
    $ind++;
}

function dbpedia($palavra) {
	try {
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "http://model.dbpedia-spotlight.org/en/annotate",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "text=" . $palavra . "&confidence=0.35",
			CURLOPT_HTTPHEADER => array(
				"accept: application/json",
				"cache-control: no-cache",
				"content-type: application/x-www-form-urlencoded",
				"postman-token: 5a0e0b03-9587-f575-3ae4-8f8fa233b677"
				),
			));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
			throw new Exception($err, 1);
		} else {
			if ($httpcode == 200) {
				return $response;
			} else {
				throw new Exception($response, 1);
			}
		}
	} catch (Exception $e) {
		debug("ERRO");
		debug($e->getMessage());
	}
}
?>