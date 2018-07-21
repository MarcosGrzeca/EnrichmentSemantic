<?php

require_once("config.php");

$avaliacoes = [];

if (($handle = fopen("planilhas/resultados/parte1/Requester_A199PTXKQW8EG0_Results.csv", "r")) !== FALSE) {
	$cont = 1;

	$header = fgetcsv($handle, 2000, ";");
	$contador = 0;
    while (($data = fgetcsv($handle, 2000, ";")) !== FALSE) {
    	$q1 = ""; $q2 = ""; $q3 = "";
    	$link = json_decode($data[6])->tweet_url;
    	$posQ1 = strpos($data[4], "Q1Answer");
    	if ($posQ1 !== false) {
    		$q1 = substr($data[4], ($posQ1 + 8), 1);
    	}
    	$posQ2 = strpos($data[4], "Q2Answer");
    	if ($posQ2 !== false) {
    		$q2 = substr($data[4], ($posQ2 + 8), 1);
    	}
    	$posQ3 = strpos($data[4], "Q3Answer");
    	if ($posQ3 !== false) {
    		$q3 = substr($data[4], ($posQ3 + 8), 1);
    	}

    	if (!isset($avaliacoes[$link])) {
			$avaliacoes[$link] = array("q1" => [], "q2" => [], "q3" => []);
    	}
    	$avaliacoes[$link]["q1"][] = $q1;
    	$avaliacoes[$link]["q2"][] = $q2;
    	$avaliacoes[$link]["q3"][] = $q3;

    	$contador++;

    	if ($contador > 20) {
    		//break;
    	}
    }
}

debug(count($avaliacoes));

$totalQ1Igual = 0;
$totalQ1Diferente = 0;
$totalQ2Igual = 0;
$totalQ2Diferente = 0;
$totalQ3Igual = 0;
$totalQ3Diferente = 0;

$apenasCalcular = false;

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
				////$value = "no";
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
				////$value = "no";
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

	$contadores = array("q1" => 0, "q2" => 0, "q3" => 0);
	debug(count($avaliacoes));
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

		//debug(array($q1, $q2, $q3));

		if (trim($q1) != "") {
			$fields = array("q1" => ($q1 == "y" ? 1 : 0));
			$contadores["q1"] += $fields["q1"];
			if (trim($q2) != "") {
				$fields["q2"] = ($q2 == "y" ? 1 : 0);
				$contadores["q2"] += $fields["q2"];
				if (trim($q3) != "") {
					$fields["q3"] = $q3 == "y" ? 1 : 0;
					$contadores["q3"] += $fields["q3"];
				}
			}
			//update("semantic_tweets", $aTweet["id"], $fields);

			//debug($fields);
		}
	}

	debug($contadores);
}
?>