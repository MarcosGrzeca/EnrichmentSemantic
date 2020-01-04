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

$sql = "SELECT id, q3 as resposta, textEmbedding as texto
			FROM tweets t
			WHERE textparser <> ''
			AND id <> 462478714693890048
			AND q3 IS NOT NULL
			ORDER by id";

// 697 - 1 (139 testes) 558
// 631	-	0 (126 testes) 505

$tweets = query($sql);

foreach (getRows($tweets) as $tweet) {
	$texto = clear($tweet["texto"]);
	if ($tweet["resposta"] == 1) {
		if (count($trainPos) < 558) {
			$trainPos[] = $texto;
		} else {
			$testePos[] = $texto;
		}
	} else if ($tweet["resposta"] == 0) {
		if (count($trainNeg) < 505) {
			$trainNeg[] = $texto;
		} else {
			$testeNeg[] = $texto;
		}
	}
}


$myfile = fopen("berttrainq3.tsv", "w") or die("Unable to open file!");
foreach ($trainPos as $tweet) {
	fputcsv($myfile, ["tweet", 1, "", $tweet], "\t");
}

foreach ($trainNeg as $tweet) {
	fputcsv($myfile, ["tweet", 0, "", $tweet], "\t");
}
fclose($myfile);

$myfile = fopen("berttestq3.tsv", "w") or die("Unable to open file!");
foreach ($testeNeg as $tweet) {
	fputcsv($myfile, ["tweet", 0, "", $tweet], "\t");
}

foreach ($testePos as $tweet) {
	fputcsv($myfile, ["tweet", 1, "", $tweet], "\t");
}
fclose($myfile);