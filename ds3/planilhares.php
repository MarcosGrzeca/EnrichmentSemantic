<?php

require_once("../config.php");

$fp = fopen('/var/www/html/marcos/EnrichmentSemantic/ds3/comparativo.csv', 'w') or die("Unable to open file!");

$f1Baseline = 82.85397;
$precisionBaseline = 75.154139;
$recallBaseline = 92.349;

$resultados = query("SELECT * FROM resultado WHERE enriquecimento = 1 ORDER BY redeId, epocas, metricaEarly");

$resultadosParaPlanilha = [];

foreach ($resultados as $key => $value) {
     $res = [];
    $res["redeId"] = $value["redeId"];
    $res["algoritmo"] = $value["tipoRede"];
    $res["embedding"] = $value["embedding"];
    $res["epocas"] = $value["epocas"];
    $res["metricaEarly"] = $value["metricaEarly"];
    $res["f1Baseline"] = $value["f1"] - $f1Baseline;
    $res["precisionBaseline"] = $value["precision"] - $precisionBaseline;
    $res["recallBaseline"] = $value["recall"] - $recallBaseline;

	$resultadao = query("SELECT * FROM resultado WHERE enriquecimento = 0 AND redeId = '" . $value["redeId"] . "' AND metricaEarly = '" . $value["metricaEarly"] . "' AND epocas = '" . $value["epocas"] . "';");
	foreach ($resultadao as $comparacao) {
		$res["f1BaselineEnriquecimento"] = $value["f1"] - $comparacao["f1"];
	    $res["precisionBaselineEnriquecimento"] = $value["precision"] - $comparacao["precision"];
	    $res["recallBaselineEnriquecimento"] = $value["recall"] - $comparacao["recall"];
	}

	fputcsv($fp, $res, ";");
    // $resultadosParaPlanilha[] = $res;
}

// echo "<pre>";
// var_dump($resultadosParaPlanilha);
// echo "</pre>";

fclose($fp);


?>