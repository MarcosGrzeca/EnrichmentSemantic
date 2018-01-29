<?php 

require_once("config.php");

estaAtivo("erros");

$tweets = query("SELECT * FROM semantic_tweets WHERE situacao = 1 AND preProcessado = 'S' AND (language = 'en' OR language = '') AND erros = -1 LIMIT 300");
//$tweets = query("SELECT * FROM semantic_tweets WHERE id = 911002453834850304;");


$ind = 0;
foreach (getRows($tweets) as $key => $value) {
	$totalErros = 0;
	$texto = clear($value["textParser"]);
	try {
		$spell = spell($texto);
		$spellJSON = json_decode($spell);
		if (count($spellJSON->corrections)) {
			$totalErros = 0;
			foreach ($spellJSON->corrections as $palavraErro => $palavrasSimilares) {
				if (ehErro($palavraErro, $palavrasSimilares)) {
					$totalErros++;
					//debug(array("erro" => true, "palavra" => $palavraErro, "similiares" => $palavrasSimilares));
				} else {
					//$totalErros--;
					//debug(array("erro" => false, "palavra" => $palavraErro, "similiares" => $palavrasSimilares));
				}
			}
		} else {
			//debug("TEXTO CORRETO");
		}
		update("semantic_tweets", $value["id"], array("erros" => $totalErros, "jsonErros" => $spell));
	} catch (Exception $e) {
		debug($e->getMessage());
	}
	if ($ind > 10) {
        //break;
	}
	$ind++;
}

function spell($text) {
    /*$curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://www.webspellchecker.net/spellcheck3/script/ssrv.cgi?cmd=check_spelling&customerid=1:r23Az2-kjwhU3-tDJn21-PKJhH-uJmzy1-xAo9o1-OhAyz3-0GjRk3-xSYqy4-WL3r54-o7NJ61-Kx8&version=1.0&out_type=words&slang=en_US&callback=cc&format=json&text=" . urlencode($text),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Cache-Control: no-cache",
        "Postman-Token: 079315ca-13b5-a4e7-a321-682276dcb0d6"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    debug($httpcode);
    debug($response);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
  }*/

  $token = "eb539e21-22e8-13cb-9b04-8fbea23b2bdb";
  if (rand(0, 1)) {
  	$token = "9v2CvSfHZAmshXDNOhNV3qHyQeaap1Ggt0hjsneNotKCh7n7Ja";
  }

  $curl = curl_init();
  curl_setopt_array($curl, array(
  	CURLOPT_URL => "https://montanaflynn-spellcheck.p.mashape.com/check/?text=" . urlencode($text),
  	CURLOPT_SSL_VERIFYHOST => 0,
  	CURLOPT_SSL_VERIFYPEER => 0,
  	CURLOPT_RETURNTRANSFER => true,
  	CURLOPT_ENCODING => "",
  	CURLOPT_MAXREDIRS => 10,
  	CURLOPT_TIMEOUT => 30,
      //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  	CURLOPT_CUSTOMREQUEST => "GET",
  	CURLOPT_HTTPHEADER => array(
  		"Cache-Control: no-cache",
  		"Postman-Token: " . $token,
  		"X-Mashape-Key: lGazgCQaIgmshqsTCM14e16ZFWoXp1eRDWOjsnvwvYEM3SUdn1"
  	),
  ));
    
  $response = curl_exec($curl);
  $err = curl_error($curl);
  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

  curl_close($curl);

  if ($err) {
  	throw new Exception($err, 1);
  } else {
  	if ($httpcode == 200) {
  		return $response;
  	}
  	throw new Exception($response, 1);
  }
}

function replaceRisos($texto) {
	$texto = " " . $texto . " ";
	$textAnt = "";
	while ($textAnt != $texto) {
		$textAnt = $texto;
		$texto = preg_replace("/[^\w][hakeHAKE]{3,}[^\w]/", ' ', $texto);
	}
	return trim($texto);
}

function clear($texto) {
	$texto = strtolower($texto);
	$texto = str_ireplace("#mention", "", $texto);
	$texto = str_ireplace("#url", "", $texto);
	$texto = str_ireplace("#media", "", $texto);
	$texto = str_ireplace("\n", "", $texto);
	$texto = replaceRisos($texto);

  //Remover urls
	$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@";
	$texto = preg_replace($regex, '', $texto);

	return trim($texto);
}

function ehErro($palavraComErro, $sugestoes = array()) {
	$girias = array("LOL", "OMG", "ILY", "LMAO", "WTF", "PPL", "IDK", "TBH", "BTW", "THX", "SMH", "FFS", "AMA", "FML", "TBT", "JK", "IMO", "YOLO", "ROFL", "MCM", "IKR", "FYI", "BRB", "GG", "IDC", "TGIF", "NSFW", "ICYMI", "STFU", "WCW", "IRL", "BFF", "OOTD", "FTW", "Txt", "HMU", "HBD", "TMI", "NM", "GTFO", "NVM", "DGAF", "FBF", "DTF", "FOMO", "SMFH", "OMW", "POTD", "LMS", "GTG", "ROFLMAO", "TTYL", "AFAIK", "LMK", "PTFO", "SFW", "HMB", "TTYS", "FBO", "TTYN");
	$redesSociais = array("facebook", "youtube", "whatsapp", "snapchat", "twitter", "instagram", "snapchats");

	$errosConhecidos = array("crossfit", "mardigras", "mardi", "gras");

	if (strlen($palavraComErro) <= 2) {
		return false;
	}

	if (in_array(strtoupper($palavraComErro), $girias)) {
		return false;
	}
	if (in_array(strtolower($palavraComErro), $redesSociais)) {
		return false;
	}
	if (in_array(strtolower($palavraComErro), $errosConhecidos)) {
		return false;
	}

	$palavraComErro = strtolower($palavraComErro);		

	foreach ($sugestoes as $keySugestao => $sugestao) {
		$sugestao = strtolower($sugestao);
		if (strlen($palavraComErro) >= strlen($sugestao)) {
			if (levenshtein($palavraComErro, $sugestao, 1, 2, 1) == 1) {
				$keyWordTwo = 0;
				$sucesso = true;

				for ($keyW = 0; $keyW < strlen($palavraComErro); $keyW++) {
					if (isset($sugestao[$keyWordTwo]) && $palavraComErro[$keyW] == $sugestao[$keyWordTwo]) {
						$keyWordTwo++;
					} else if ($keyWordTwo > 0 && $palavraComErro[$keyW] == $sugestao[($keyWordTwo - 1)]) {
					} else {
						$sucesso = false;
					}
				}
				if ($sucesso) {
//					debug("CASE levenshtein(str1, str2)");
					return false;
				}
			}
		}
	}

	return true;
}