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

foreach ($avaliacoes as $keyAvaliacao => $avaliacao) {

	foreach ($avaliacao["q1"] as $key => $value) {
		if (trim($value) == "") {
			$value = "no";
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
			$value = "no";
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
			$value = "no";
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

debug(array($percentualQ1, $percentualQ2, $percentualQ3));
?>