z<?php
require_once("config.php");

$girias = array("LOL", "OMG", "ILY", "LMAO", "WTF", "PPL", "IDK", "TBH", "BTW", "THX", "SMH", "FFS", "AMA", "FML", "TBT", "JK", "IMO", "YOLO", "ROFL", "MCM", "IKR", "FYI", "BRB", "GG", "IDC", "TGIF", "NSFW", "ICYMI", "STFU", "WCW", "IRL", "BFF", "OOTD", "FTW", "Txt", "HMU", "HBD", "TMI", "NM", "GTFO", "NVM", "DGAF", "FBF", "DTF", "FOMO", "SMFH", "OMW", "POTD", "LMS", "GTG", "ROFLMAO", "TTYL", "AFAIK", "LMK", "PTFO", "SFW", "HMB", "TTYS", "FBO", "TTYN");
$redesSociais = array("facebook", "youtube", "whatsapp", "snapchat", "twitter", "instagram", "snapchats");

$errosConhecidos = array("ve", "crossfit");

function replaceAll($texto) {
	$texto = " " . $texto . " ";
	$textAnt = "";
	while ($textAnt != $texto) {
		$textAnt = $texto;
		$texto = preg_replace("/[^\w][hakeHAKE]{3,}[^\w]/", ' ', $texto);
	}
	return trim($texto);
}

echo "<pre>";
chdir('C:\Program Files\R\R-3.4.1patched\bin\\');

$tweets = query("SELECT * FROM tweets");
foreach (getRows($tweets) as $keyTweet => $tweet) {
	$texto = replaceAll($tweet["textParser"]);
	$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@";
	$texto = preg_replace($regex, '', $texto);

	$texto = str_ireplace(array("I've", "dont't", "Dont't", "couldn't", "Couldn't", "I’ve", "didn't", "isn't", "weren't", "aren't", "wasn't", "hasn't", "ain't", "shouldn't", "wouldn't", "doesn't", "won't", "haven't"), "", $texto);

	if (isset($output)) {
		unset($output);
	}

	exec("Rscript.exe C:\Users\Marcos\Documents\GitHub\drunk\processadores\spelling2.R \"$texto\" 2>&1",  $output, $return_var);

	/*var_export($texto . "<br/>");
	var_export($output);
	var_export("<br/>");*/
	
	$correcoes = $words = array();
	if (count($output) == 1 && $output[0] == "NULL") {
		continue;
	} else {
		//var_export("TEXTO <br/>" . $texto);
		//var_export($output);
		$words = array_values(array_filter(explode("\"", $output[1])));
		unset($words[0]);
		foreach ($words as $key => $value) {
			if (trim($value) == "") {
				unset($words[$key]);
			}
		}
		$words = array_values($words);
		$totalErros = count($words);
		//debug($words);
		//debug($totalErros);

		$correcoes = array();
		$indWord = 0;
		foreach ($output as $key => $value) {
			if ($key <= 2) {
				continue;
			}

			if (trim($value) == "") {
				$indWord++;
			} else {
				if (!isset($correcoes[$indWord])) {
					$correcoes[$indWord] = array();
				}
				$wordsCorrecoes = array_values(array_filter(explode("\"", $value)));
				unset($wordsCorrecoes[0]);
				foreach ($wordsCorrecoes as $key => $value) {
					if (trim($value) == "") {
						unset($wordsCorrecoes[$key]);
					}
				}
				$correcoes[$indWord] = array_merge($correcoes[$indWord], $wordsCorrecoes);
			}
		}

		foreach ($words as $key => $value) {
			if (in_array(strtolower($value), $correcoes[$key])) {
				unset($words[$key]);
				unset($correcoes[$key]);
			}
			if (in_array(strtoupper($value), $girias)) {
				unset($words[$key]);
				unset($correcoes[$key]);
			}
			if (in_array(strtolower($value), $redesSociais)) {
				unset($words[$key]);
				unset($correcoes[$key]);
			}
			if (in_array(strtolower($value), $errosConhecidos)) {
				unset($words[$key]);
				unset($correcoes[$key]);
			}
			if (strlen($value) <= 2) {
				unset($words[$key]);
				unset($correcoes[$key]);
			}

			if (isset($words[$key]) && isset($correcoes[$key])) {
				foreach ($correcoes[$key] as $keyCorrecao => $valueCorrecao) {
					if (strlen($value) >= strlen($valueCorrecao)) {
						if (levenshtein(strtolower($value), strtolower($valueCorrecao), 1, 2, 1) == 1) {
							$keyWordTwo = 0;
							$sucesso = true;

							$palavra = $words[$key];
							for ($keyW = 0; $keyW < strlen($palavra); $keyW++) {
								if (isset($valueCorrecao[$keyWordTwo]) && $palavra[$keyW] == $valueCorrecao[$keyWordTwo]) {
									$keyWordTwo++;
								} else if ($keyWordTwo > 0 && $palavra[$keyW] == $valueCorrecao[($keyWordTwo - 1)]) {
								} else {
									$sucesso = false;
								}
							}
							if ($sucesso) {
								//debug("MATCH " . $words[$key] . "  ----- " . $valueCorrecao);
								unset($words[$key]);
								unset($correcoes[$key]);
							}
						}
					}
					if ($keyCorrecao > 2) {
						break;
					}
				}
			}
		}

		//var_export("<br/>Final Erros ====>>>> " . $texto . "<br/>");
		//var_export($words);

		$sql = "UPDATE tweets SET `erroParseado` = " . count($words) . " WHERE id = " . $tweet["id"];
		query($sql);

		foreach ($words as $wordErro) {
			$sql = "INSERT INTO tweets_erro_new (idTweetInterno, palavra, versao) VALUES ('" . $tweet["idInterno"] . "', '" . escape(utf8_encode($wordErro)) . "', 3);";
			query($sql);
		}
	}
}

echo "<br/>";

//var_export($return_var);
?>