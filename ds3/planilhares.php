<?php

require_once("../config.php");

$filePath = realpath('.') . '\parte3.csv';
$fp = fopen($filePath, 'w') or die("Unable to open file!");

$resultados = query("SELECT * FROM resultado WHERE enriquecimento = 1 ORDER BY redeId, epocas, metricaEarly, dataset");

$resultadosParaPlanilha = [];

foreach ($resultados as $key => $value) {
	if ($value["dataset"] == "DS3") {
		$f1Baseline = 82.85397;
		$precisionBaseline = 75.154139;
		$recallBaseline = 92.349;
	} else if ($value["dataset"] == "DS1-Q1") {
		$f1Baseline = 89.834;
		$precisionBaseline = 92.151;
		$recallBaseline = 87.517;
	} else if ($value["dataset"] == "DS1-Q2") {
		$f1Baseline = 89.057;
		$precisionBaseline = 81.398;
		$recallBaseline = 96.715;
	} else if ($value["dataset"] == "DS1-Q3") {
		$f1Baseline = 88.037;
		$precisionBaseline = 80.892;
		$recallBaseline = 95.182;
	}

    $res = [];
    $res["redeId"] = $value["redeId"];
    $pieces = explode("-DS1", $res["redeId"]);

    if ($pieces !== false) {
    	$res["redeId"] = $pieces[0];
    }
    $res["dataset"] = $value["dataset"];
    $res["algoritmo"] = $value["tipoRede"];
    $res["embedding"] = $value["embedding"];
    $res["epocas"] = $value["epocas"];
    $res["metricaEarly"] = $value["metricaEarly"];
    $res["f1Baseline"] = $value["f1"] - $f1Baseline;
    $res["precisionBaseline"] = $value["precision"] - $precisionBaseline;
    $res["recallBaseline"] = $value["recall"] - $recallBaseline;

	$resultadao = query("SELECT * FROM resultado WHERE enriquecimento = 0 AND redeId = '" . $value["redeId"] . "' AND metricaEarly = '" . $value["metricaEarly"] . "' AND epocas = '" . $value["epocas"] . "' AND dataset = '" . $value["dataset"] . "';");


	if (getNumRows($resultadao) == 0) {
		debug($value);
		continue;
	}
	foreach ($resultadao as $comparacao) {
		$res["f1BaselineEnriquecimento"] = $value["f1"] - $comparacao["f1"];
	    $res["precisionBaselineEnriquecimento"] = $value["precision"] - $comparacao["precision"];
	    $res["recallBaselineEnriquecimento"] = $value["recall"] - $comparacao["recall"];
	}
	fputcsv($fp, $res, ";");
}

fclose($fp);
?>