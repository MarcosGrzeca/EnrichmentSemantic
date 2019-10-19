<?php

// train_source = {'train-neg.txt':'TRAIN_NEG',
// 				'train-pos.txt':'TRAIN_POS',
// 				'test-neg.txt':'TEST_NEG',
// 				'test-pos.txt':'TEST_POS',
// 				'meu-train-unsup.txt':'TRAIN_UNS'}

require_once("../config.php");

$sql = "SELECT textOriginal as texto
		FROM semantic_tweets_alcolic t
		UNION ALL
		SELECT textoOriginal as texto
		FROM chat_tweets t
		UNION ALL
		SELECT textEmbedding as texto
		FROM tweets t
		WHERE textparser <> ''
		AND id <> 462478714693890048
		AND q2 IS NULL";

		
$myfile = fopen("meu-train-unsup.txt", "w") or die("Unable to open file!");

$tweets = query($sql);

function clear($texto) {
	$string = trim(preg_replace('/\s+/', ' ', $texto));
	return mb_strtolower($string, 'UTF-8');
}

foreach (getRows($tweets) as $tweet) {
	fwrite($myfile, clear($tweet["texto"]) . "\n");
}
fclose($myfile);

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


$myfile = fopen("meu-train-neg.txt", "w") or die("Unable to open file!");
foreach ($trainNeg as $tweet) {
	fwrite($myfile, $tweet . "\n");
}
fclose($myfile);

$myfile = fopen("meu-train-pos.txt", "w") or die("Unable to open file!");
foreach ($trainPos as $tweet) {
	fwrite($myfile, $tweet . "\n");
}
fclose($myfile);

$myfile = fopen("meu-test-pos.txt", "w") or die("Unable to open file!");
foreach ($testePos as $tweet) {
	fwrite($myfile, $tweet . "\n");
}
fclose($myfile);

$myfile = fopen("meu-test-neg.txt", "w") or die("Unable to open file!");
foreach ($testeNeg as $tweet) {
	fwrite($myfile, $tweet . "\n");
}
fclose($myfile);