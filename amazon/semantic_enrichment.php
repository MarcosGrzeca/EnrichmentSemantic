<?php 

require_once("../config.php");

set_time_limit(290);

#estaAtivo("enriquecer");

// $sqlIni = "SELECT id, textParser FROM chat_tweets WHERE processado = 1 AND enriquecido = 'N' ";
$sqlIni = "SELECT id, textSemPalavrasControle as textParser FROM chat_tweets WHERE processado = 1 AND enriquecido = 'N' ";

if (isset($_REQUEST["order"]) && $_REQUEST["order"] == "DESC") {
	$sqlIni .= "ORDER BY id desc ";
} else {
	$sqlIni .= "ORDER BY id ";
}

$sqlIni .= "LIMIT 200";

$tweets = query($sqlIni);

$ind = 0;
foreach (getRows($tweets) as $key => $value) {
    $language = "";

    try {
    	try {
    		$calais = calais($value["textParser"]);
    		$calaisJSON = json_decode($calais);
    	} catch (Exception $e) {
    		if ($e->getCode() == 99) {
    			var_dump("EXCEDEU LIMITE Calais");
    			die;
    		}
    	}

		try {
    		$alchemy = alchemy($value["textParser"], "en");
    		$alchemyJSON = json_decode($alchemy);
    		$language = $alchemyJSON->language;
    	} catch (Exception $e) {
    		if ($e->getCode() == 27) {
    			debug("Idioma invalido");
    			$language = $e->getMessage();
    		} else if ($e->getCode() == 99) {
    			var_dump("EXCEDEU LIMITE Alchemy");
    			die;
    		} else {
    			var_dump("Alchemy");
    			var_dump($value["textParser"] . " -- " . $value["id"]);
    			var_dump($e->getMessage());
    		}
    	}

    	$fields = array("idTweet", "origem", "tipo", "palavra", "type");

    	foreach ($calaisJSON as $keyC => $valueC) {
    		if (isset($valueC->_typeGroup)) {
				$typeGroup = $valueC->_typeGroup;
				if ($valueC->_typeGroup == "entities") {
					insert("chat_tweets_nlp", $fields, array($value["id"], "C", "E", $valueC->name, $valueC->_type));
				} else if (!empty($typeGroup) && !empty($valueC->{$typeGroup})) {
					insert("chat_tweets_nlp", $fields, array($value["id"], "C", $valueC->_typeGroup, $valueC->{$typeGroup}, NULL));
				}
    		}
    	}

    	if ($language == "en") {
    		foreach ($alchemyJSON as $keyC => $valueC) {
    			$tipo = "";
    			switch ($keyC) {
    				case 'categories':
    				$tipo = "C";
    				break;
    				case 'concepts':
    				$tipo = "CO";
    				break;
    				case 'entities':
    				$tipo = "E";
    				break;
    				case 'keywords':
    				$tipo = "K";
    				break;
    				default:
    			}

    			if ($tipo == "") {
    				continue;
    			}

    			foreach ($valueC as $keyTwo => $valueTwo) {
    				if ($tipo == "C") {
    					insert("chat_tweets_nlp", $fields, array($value["id"], "A", $tipo, $valueTwo->label, NULL));
    				} else if ($tipo == "CO") {
    					insert("chat_tweets_nlp", $fields, array($value["id"], "A", $tipo, $valueTwo->text, NULL));
    				} else if ($tipo == "E") {
    					insert("chat_tweets_nlp", $fields, array($value["id"], "A", $tipo, $valueTwo->type, $valueTwo->type));
    				} else if ($tipo == "K") {
    					insert("chat_tweets_nlp", $fields, array($value["id"], "A", $tipo, $valueTwo->text, NULL));
    				}
    			}
    		}
    	}
		// update("chat_tweets", $value["id"], array("enriquecido" => "S", "language" => $language, "calaisResponse" => $calais, "alchemyResponse" => $alchemy));
		update("chat_tweets", $value["id"], array("enriquecido" => "S", "calaisResponse" => $calais, "alchemyResponse" => $alchemy));
    } catch (Exception $e) {
    	var_dump($e->getMessage()); 
    }
    if ($ind >= 1) {
    	//break;
    }
    $ind++;
}

function alchemy($texto, $idioma = "") {
	/*{
	  "url": "https://gateway.watsonplatform.net/natural-language-understanding/api",
	  "username": "a6564515-0ab7-4748-881e-280a6506c1e1",
	  "password": "AgSElOZigYhL"
	}*/

	//$tokens = array("ZjQ5MmVlY2ItYjZkOC00NzY0LWIyNDctYzkzNzZkMzA0ZjRkOmN6anhVYWNHUE1YeA==", "ZTBjYTdhMzctMmE1OC00ZDI2LTlmNzUtMGUwN2EwYTFhMmRmOk9aVFJ0YUM2MklqOA==", "ZjVjYTgwMjgtMDk3ZC00MmEzLThiM2ItODc4MjJjZWM3Njk5OlJyYTdGMmREVFNIeg==", "YTY1NjQ1MTUtMGFiNy00NzQ4LTg4MWUtMjgwYTY1MDZjMWUxOkFnU0VsT1ppZ1loTA==", "YTg5ZDNmNmMtNmZhZi00NTBiLTg0ZWQtMjViNjk3ZmJlNDZjOnZRUUJSMnd5RFN0Rg==");
	// $tokens = array("ZTBjYTdhMzctMmE1OC00ZDI2LTlmNzUtMGUwN2EwYTFhMmRmOk9aVFJ0YUM2MklqOA==", "ZjVjYTgwMjgtMDk3ZC00MmEzLThiM2ItODc4MjJjZWM3Njk5OlJyYTdGMmREVFNIeg==", "YTY1NjQ1MTUtMGFiNy00NzQ4LTg4MWUtMjgwYTY1MDZjMWUxOkFnU0VsT1ppZ1loTA==", "YTg5ZDNmNmMtNmZhZi00NTBiLTg0ZWQtMjViNjk3ZmJlNDZjOnZRUUJSMnd5RFN0Rg==");
	$tokens = array("ZDMyYTQxNzgtODdlMC00MjBkLThiMDEtZGQ4YmI3NDgxYWE2OlpBWXNSWTdJUzJjdg==");
	$tokens = array("YXBpa2V5OnBOMnBzMjlqQUd4Q3lYZ0wxM3lSbkRiQVljem9JUndNbXltOTdCaTdGNmZr");
	$token = $tokens[rand(0,count($tokens) - 1)];

	debug($token);

	$parametros = array("text" => $texto, "features" => array());
	if ($idioma != "") {
		$parametros["language"] = $idioma;
	}
	$parametros["features"]["entities"] = array("emotion" => true, "sentiment" => true, "limit" => 10);
	$parametros["features"]["keywords"] = array("emotion" => true, "sentiment" => true, "limit" => 5);
	$parametros["features"]["categories"] = array("emotion" => true, "sentiment" => true, "limit" => 5);
	$parametros["features"]["concepts"] = array("emotion" => true, "sentiment" => true, "limit" => 5);
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://gateway.watsonplatform.net/natural-language-understanding/api/v1/analyze?version=2017-02-27",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POSTFIELDS => json_encode($parametros),
		CURLOPT_HTTPHEADER => array(
			//"authorization: Basic YTk4NTdjYTUtMWUyMC00M2M2LWJiODctZjMzZDM1YjYwYzQ0OkpTUnp2WWNUOFRZVg==",
			"authorization: Basic " . $token,
			"cache-control: no-cache",
			"content-type: application/json"
		),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	/*
	debug($httpcode);
	debug($response);
	debug(json_decode($response)->error);
	*/

	curl_close($curl);
	if ($err) {
		echo "cURL Error #:" . $err;
		throw new Exception($err, 1);
	} else {
      //echo $response;
		if (trim($response) == "You exceeded the concurrent request limit for your license key. Please try again later or contact support to upgrade your license.") {
			throw new Exception($response, 99);
			echo "Excedeu limite<br/>";
		} else {
			if ($httpcode == 200) {
				return $response;
			} else if ($httpcode == 401) {
				throw new Exception($response, 99);		
			} else if ($httpcode == 400 && isset(json_decode($response)->language) && (json_decode($response)->language != "en")) {
				throw new Exception($response->language, 27);
			} else if ($httpcode == 400 && isset(json_decode($response)->error) && (json_decode($response)->error == "not enough text provided for language detection") && $idioma == "") {
				return alchemy($texto, "en");
			} else if ($httpcode == 400 && isset(json_decode($response)->error) && (json_decode($response)->error == "unknown language detected") && $idioma == "") {
				return alchemy($texto, "en");
			} else {
				throw new Exception($response, 1);
			}
		}
	}
}

function calais($texto) {
	$curl = curl_init();

	$tokens = array("bpftqGYLoMICrD2GvuuaSyKgvsSTsjgb", "5k2EFOFxFOIAUl5e9AJXDuJVM7x03nxd", "lsE903VAyy6QXLPozCUFaPIJKteHsIog");
	$token = $tokens[rand(0,count($tokens)-1)];

	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.thomsonreuters.com/permid/calais",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $texto,
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: text/raw",
			"outputformat: application/json",
			"x-ag-access-token: " . $token
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
      if (trim($response) == "You exceeded the concurrent request limit for your license key. Please try again later or contact support to upgrade your license." || $httpcode == 401 || $httpcode == 403) {
      	     echo "Excedeu limite<br/>";
      		throw new Exception($response, 99);
      } else {
      	if ($httpcode == 200) {
      		return $response;
      	} else {
      		throw new Exception($response);
      	}
      }
  }
}
?>