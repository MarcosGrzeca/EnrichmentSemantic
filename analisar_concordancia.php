<?php

require_once("config.php");


$paises = array();

$avaliacoes = [];

if (($handle = fopen("planilhas/resultados/parte4/f1279922.csv", "r")) !== FALSE) {
	$cont = 1;

	$header = fgetcsv($handle, 2000, ",");
	debug($header);

	$contador = 0;
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
    	//debug($data);
    	$q1 = $data[14];
    	$q2 = $data[15];
    	$q3 = $data[16];
    	$link = $data[21];

    	//debug(array($q1, $q2, $q3, $link));

    	if ($data[10] != "VEN") {
    		continue;
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

    	if ($contador > 20) {
    		//break;
    	}
    }
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