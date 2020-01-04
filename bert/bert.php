<?php

require_once("../config.php");

function clear($texto) {
	$string = trim(preg_replace('/\s+/', ' ', $texto));
	$string = preg_replace("#[[:punct:]]#", "", $string);
	return mb_strtolower($string, 'UTF-8');
}

$trainPos = [];
$trainNeg = [];
$testePos = [];
$testeNeg = [];

$sql = "SELECT id, q2 as resposta, textEmbedding as texto
			FROM tweets t
			WHERE textparser <> ''
			AND id <> 462478714693890048
			AND q2 IS NOT NULL
			ORDER by id";

// 1465 - 1 (293 testes)
// 565	-	0 (113 testes)

$tweets = query($sql);

foreach (getRows($tweets) as $tweet) {
	$texto = clear($tweet["texto"]);
	if ($tweet["resposta"] == 1) {
		if (count($trainPos) < 1172) {
			$trainPos[] = $texto;
		} else {
			$testePos[] = $texto;
		}
	} else if ($tweet["resposta"] == 0) {
		if (count($trainNeg) < 452) {
			$trainNeg[] = $texto;
		} else {
			$testeNeg[] = $texto;
		}
	}
}


$myfile = fopen("berttrain.tsv", "w") or die("Unable to open file!");
foreach ($trainPos as $tweet) {
	fputcsv($myfile, ["tweet", 1, "", $tweet], "\t");
}

foreach ($trainNeg as $tweet) {
	fputcsv($myfile, ["tweet", 0, "", $tweet], "\t");
}
fclose($myfile);

$myfile = fopen("berttest.tsv", "w") or die("Unable to open file!");
foreach ($testeNeg as $tweet) {
	fputcsv($myfile, ["tweet", 0, "", $tweet], "\t");
}

foreach ($testePos as $tweet) {
	fputcsv($myfile, ["tweet", 1, "", $tweet], "\t");
}
fclose($myfile);