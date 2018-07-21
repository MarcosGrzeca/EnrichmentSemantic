<?php

require_once("config.php");


//pre_validacao_q2

$paises = array();

$avaliacoes = [];

if (($handle = fopen("planilhas/resultados/f1279922.csv", "r")) !== FALSE) {
	$cont = 1;

	$header = fgetcsv($handle, 2000, ",");
	//debug($header);

	$contador = 0;
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
    	//debug($data);

    	$date = $data[1];

    	if (strtotime($date) < strtotime("07/02/2018 12:00:00")) {
//    		continue;
    	}
		
    	$q1 = $data[14];
    	$q2 = $data[15];
    	$q3 = $data[16];
    	$link = $data[21];

    	//debug(array($q1, $q2, $q3, $link));

    	//"CAN", "USA", "IND", "PHL"
    	if (!in_array($data[10], array("CAN", "USA", "IND"))) {
    		//continue;
    	}

    	if (!isset($paises[$data[10]])) {
    		$paises[$data[10]] = 0;
    	}
    	$paises[$data[10]]++;

    	if (!isset($avaliacoes[$link])) {
			$avaliacoes[$link] = array("q1" => [], "q2" => [], "q3" => []);
    	}
    	$avaliacoes[$link]["q1"][] = $q1;
    	$avaliacoes[$link]["q2"][] = $q2;
    	$avaliacoes[$link]["q3"][] = $q3;

    	$contador++;

    	if ($contador > 200) {
    		//break;
    	}
    }
    //debug($contador);
}

echo "<pre>";
arsort($paises);
var_dump($paises);

$totalQ1Igual = 0;
$totalQ1Diferente = 0;
$totalQ2Igual = 0;
$totalQ2Diferente = 0;
$totalQ3Igual = 0;
$totalQ3Diferente = 0;

$apenasCalcular = false;
debug("Total " . count($avaliacoes));

if ($apenasCalcular) {
	foreach ($avaliacoes as $keyAvaliacao => $avaliacao) {

		foreach ($avaliacao["q1"] as $key => $value) {
			if (trim($value) == "") {
				//$value = "no";
			}
			if ($key == 0) {
				$qAnterior = $value;
			} else {
				if ($qAnterior == $value) {
					if ($key == 2) {
						$totalQ1Igual++;
					}
				} else {
					$totalQ1Diferente++;
					break;
				}
			}
		}
		foreach ($avaliacao["q2"] as $key => $value) {
			if (trim($value) == "") {
				//$value = "no";
			}
			if ($key == 0) {
				$qAnterior = $value;
			} else {
				if ($qAnterior == $value) {
					if ($key == 2) {
						$totalQ2Igual++;
					}
				} else {
					$totalQ2Diferente++;
					break;
				}
			}
		}
		foreach ($avaliacao["q3"] as $key => $value) {
			if (trim($value) == "") {
				//$value = "no";
			}
			if ($key == 0) {
				$qAnterior = $value;
			} else {
				if ($qAnterior == $value) {
					if ($key == 2) {
						$totalQ3Igual++;
					}
				} else {
					$totalQ3Diferente++;
					break;
				}
			}
		}
	}

	$percentualQ1 = $totalQ1Igual / ($totalQ1Diferente + $totalQ1Igual);
	$percentualQ2 = $totalQ2Igual / ($totalQ2Diferente + $totalQ2Igual);
	$percentualQ3 = $totalQ3Igual / ($totalQ3Diferente + $totalQ3Igual);

	debug(array($totalQ1Igual, $totalQ2Igual, $totalQ3Igual));
	debug(array($percentualQ1, $percentualQ2, $percentualQ3));
} else {
	foreach ($avaliacoes as $keyAvaliacao => $avaliacao) {
		$q1 = false;
		if ($avaliacao["q1"][0] == $avaliacao["q1"][1]) {
			$q1 = $avaliacao["q1"][1];
		} else if ($avaliacao["q1"][0] == $avaliacao["q1"][2]) {
			$q1 = $avaliacao["q1"][2];
		} else if ($avaliacao["q1"][1] == $avaliacao["q1"][2]) {
			$q1 = $avaliacao["q1"][2];
		}

		$q2 = false;
		if ($avaliacao["q2"][0] == $avaliacao["q2"][1]) {
			$q2 = $avaliacao["q2"][1];
		} else if ($avaliacao["q2"][0] == $avaliacao["q2"][2]) {
			$q2 = $avaliacao["q2"][2];
		} else if ($avaliacao["q2"][1] == $avaliacao["q2"][2]) {
			$q2 = $avaliacao["q2"][2];
		}

		$q3 = false;
		if ($avaliacao["q3"][0] == $avaliacao["q3"][1]) {
			$q3 = $avaliacao["q3"][1];
		} else if ($avaliacao["q3"][0] == $avaliacao["q3"][2]) {
			$q3 = $avaliacao["q3"][2];
		} else if ($avaliacao["q3"][1] == $avaliacao["q3"][2]) {
			$q3 = $avaliacao["q3"][2];
		}

		$tweetSearch = query("SELECT * FROM semantic_tweets WHERE link = '" . escape($keyAvaliacao) . "'");
		if (getNumRows($tweetSearch) == 1) {
			$aTweet = getRows($tweetSearch)[0];
		} else {
			if (in_array($keyAvaliacao, array("9752621192926453", "https://twitter.com/mattman1624/status/9752621192926453"))) {
				continue;
			}
			throw new Exception("Tweet não encontrado " . escape($keyAvaliacao), 1);
		}
		
		if (trim($q1) != "") {
			$fields = array("q1" => ($q1 == "yes" ? 1 : 0));
			if (trim($q2) != "") {
				$fields["q2"] = $q2 == "yes" ? 1 : 0;
				if (trim($q3) != "") {
					$fields["q3"] = $q3 == "yes" ? 1 : 0;
				}
			}
			update("semantic_tweets", $aTweet["id"], $fields);
		}
	}
}
?>