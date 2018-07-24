<?php 

require_once("config.php");


$files = array("planilhas/stpatricksday/result1.csv", "planilhas/stpatricksday/result2.csv", "planilhas/stpatricksday/result3.csv", "planilhas/stpatricksday/result4.csv", "planilhas/stpatricksday/result5.csv", "planilhas/stpatricksday/result6.csv", "planilhas/stpatricksday/result7.csv", "planilhas/stpatricksday/result8.csv", "planilhas/stpatricksday/result9.csv", "planilhas/stpatricksday/result10.csv", "planilhas/stpatricksday/result11.csv", "planilhas/stpatricksday/result12.csv", "planilhas/stpatricksday/result13.csv", "planilhas/stpatricksday/result14.csv");

$resultadoFinal = array();
$resultadoNaoFoi = array();

$contador = 0;
$contadorNaoEsta = 0;
echo "<pre>";
foreach ($files as $key => $file) {
    try {
        $dados = read_file_csv($file, ";");
        $header = true;
        foreach ($dados as $keyTweet => $tweet) {
            if ($header) {
                $header = false;
                continue;
            }
            if (possuiExpressao($tweet[4])) {
                if (!isset($resultadoFinal[$tweet[8]])) {
                    $resultadoFinal[$tweet[8]] = $tweet;
                }
            } else {
                if (!isset($resultadoNaoFoi[$tweet[8]])) {
                    $resultadoNaoFoi[$tweet[8]] = $tweet;
                    $contadorNaoEsta++;
                }
            }
        }
    } catch (Exception $e) {
        
    }
}
print_r(count($resultadoFinal));
print_r("<br/><br/>");
print_r("Não foi " . $contadorNaoEsta);

$fp = fopen('planilhas/amazonpartecompleto2.csv', 'w');

//tweet_url

$i = 0;
foreach ($resultadoFinal as $key => $value) {
    if ($i < 100) {
        $i++;
        //continue;
    }
    fputcsv($fp, $value, ";");
}
fclose($fp);

//$resultadoFinal

function possuiExpressao($text) {
    $expressoes = getPatterns();
    foreach ($expressoes as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            return true;
        }
    }
    return false;
}

function getPatterns() {
    return array("/\bshit\s*faced\b/i", "/\bkeg\s*beer\b/i", "/\bturn\s*up\b/i", "/\bturnt\s*up\b/i", "/\blit\s*up\b/i", "/\bpoo\s*pooed\b/i", "/\bpoo-?pooed\b/i", "/\bbar\s*hop\b/i", "/\bbeer\s*goggles\b/i", "/\btoes?\s*up\b/i", "/\bboot\s*and\s*rally\b/i", "/\bbeer\s*pong\b/i", "/\bbeer\s*belly\b/i", "/\bflip\s*cup\b/i", "/\bbud\s*light\b/i", "/\bnight\s*club\b/i", "/\bdrinking\s*games?\b/i", "/\bshit-?faced\b/i", "/\bfucked\s+up\b/i", "/\bdrunk\b/i", "/\balcohol\b/i", "/\bparty\b/i", "/\bbooze\b/i", "/\bliquor\b/i", "/\bvodka\b/i", "/\bhangover\b/i", "/\bwasted\b/i", "/\btequila\b/i", "/\bcocktail\b/i", "/\bwhiske?y\b/i", "/\bscotch\b/i", "/\brum\b/i", "/\bplastered\b/i", "/\bsloshed\b/i", "/\bhammered\b/i", "/\btrashed\b/i", "/\btipsy\b/i", "/\bbuzzed\b/i", "/\bbeer\b/i", "/\bshot\b/i", "/\bbrew\b/i", "/\bwine\b/i", "/\bbar\b/i", "/\bchampagne\b/i", "/\blager\b/i", "/\bclub\b/i", "/\bpub\b/i", "/\balcoholic\b/i", "/\bbottles?\b/i", "/\bcrown\b/i", "/\bbinge\b/i", "/\bboozy\b/i", "/\blean\b/i", "/\bhennessy\b/i", "/\bHenee\b/i", "/\bkegger\b/i", "/\bciroc\b/i", "/\bcognac\b/i", "/\byac\b/i", "/\byak\b/i", "/\bhammed\b/i");
}

echo "</pre>";